<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Simulacres d'examen.
 *
 * Cada examen és a content/examens/<id>.php: una part teòrica (preguntes amb el format dels reptes) i una part pràctica
 * (programes que es corregeixen amb proves, com els passos dels laboratoris). Durant l'examen el navegador no rep cap
 * resposta ni cap resultat de les proves: tot es corregeix al servidor en entregar-lo.
 *
 * Referències de pregunta: "<examen>#<pregunta>" (p. ex. "tema2-a#q3"); dels programes, "<examen>#<programa>".
 */
final class Examens
{
    /** Segons de marge després del temps límit perquè l'entrega arribi encara que la xarxa vagi lenta. */
    private const GRACE_S = 60;
    /** Mida màxima de les respostes desades d'un intent (sobretot el codi dels programes). */
    private const MAX_RESPOSTES = 200_000;

    public static function all(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $list = [];
        foreach (glob(APP_ROOT . '/content/examens/*.php') as $file) {
            $e = require $file;
            $list[$e['id']] = $e;
        }
        uasort($list, fn($a, $b) => ($a['ordre'] ?? 99) <=> ($b['ordre'] ?? 99));
        return $cache = $list;
    }

    public static function get(string $id): ?array
    {
        return preg_match('/^[a-z0-9\-]+$/', $id) ? (self::all()[$id] ?? null) : null;
    }

    /** Una pregunta de teoria amb tot el que necessiten Reptes::check(), publicItem() i solution(). */
    private static function item(array $examen, array $q): array
    {
        $ref = $examen['id'] . '#' . $q['id'];
        // L'id també és la llavor dels testimonis de les preguntes d'ordenar: ha de ser únic entre tots els exàmens
        return ['id' => $ref, 'ref' => $ref, 'origen_tipus' => 'repte', 'tema' => $q['tema'] ?? $examen['tema'] ?? ''] + $q;
    }

    /** El temps de l'examen, en segons. */
    private static function limit(array $examen): int
    {
        return (int) $examen['minuts'] * 60;
    }

    /** Fitxers de partida d'un programa de la part pràctica (per a l'acció run, com fan els exemples de les lliçons). */
    public static function fitxersPrograma(array $examen, string $programa): array
    {
        foreach ($examen['practica'] as $p) {
            if ($p['id'] === $programa) {
                return $p['fitxers'] ?? [];
            }
        }
        return [];
    }

    // ------------------------------------------------------------------ Intents

    /**
     * Comença un intent (o reprèn el que hi ha en curs) i retorna l'enunciat sense cap resposta.
     * Un intent que s'ha quedat sense temps sense entregar es corregeix amb el que hi havia desat.
     */
    public static function start(string $id): array
    {
        $examen = self::get($id) ?? throw new \InvalidArgumentException('Examen inexistent');
        $obert = Db::one('SELECT * FROM exam_attempts WHERE examen_id = ? AND finished_at IS NULL ORDER BY id DESC LIMIT 1', [$id]);
        if ($obert !== null && time() > (int) $obert['started_at'] + (int) $obert['limit_s'] + self::GRACE_S) {
            self::corregeix($examen, $obert);
            $obert = null;
        }
        if ($obert === null) {
            Db::run('INSERT INTO exam_attempts (examen_id, started_at, limit_s) VALUES (?, ?, ?)', [$id, time(), self::limit($examen)]);
            $obert = Db::one('SELECT * FROM exam_attempts WHERE id = ?', [(int) Db::pdo()->lastInsertId()]);
        }

        return [
            'intent' => (int) $obert['id'],
            'restant' => max(0, (int) $obert['started_at'] + (int) $obert['limit_s'] - time()),
            'respostes' => json_decode($obert['respostes'], true) ?: new \stdClass(),
            'teoria' => array_map(fn($q) => Reptes::publicItem(self::item($examen, $q)) + ['punts' => self::puntsPregunta($examen)], $examen['teoria']),
            'practica' => array_map(fn($p) => [
                'ref' => $examen['id'] . '#' . $p['id'],
                'id' => $p['id'],
                'titol' => $p['titol'],
                'enunciat' => $p['enunciat'],
                'fitxer' => $p['fitxer'],
                'codi_inicial' => $p['codi_inicial'],
                'punts' => $p['punts'],
                'fitxers' => array_keys($p['fitxers'] ?? []),
            ], $examen['practica']),
        ];
    }

    /** Desa les respostes mentre es fa l'examen (perquè no es perdin si es tanca la pestanya). */
    public static function desa(int $intent, array $respostes): array
    {
        $a = self::intentObert($intent);
        if (time() > (int) $a['started_at'] + (int) $a['limit_s'] + self::GRACE_S) {
            throw new \InvalidArgumentException('S\'ha acabat el temps: ja no es poden canviar les respostes.');
        }
        self::guardaRespostes($intent, $respostes);
        return ['desat' => true];
    }

    /** Entrega l'examen: desa les últimes respostes (si encara és a temps), el corregeix i en retorna la correcció. */
    public static function entrega(int $intent, array $respostes): array
    {
        $a = self::intentObert($intent);
        $examen = self::get($a['examen_id']) ?? throw new \InvalidArgumentException('Examen inexistent');
        // Fora de temps no s'accepten respostes noves: es corregeix el que hi havia desat
        if (time() <= (int) $a['started_at'] + (int) $a['limit_s'] + self::GRACE_S) {
            self::guardaRespostes($intent, $respostes);
            $a = Db::one('SELECT * FROM exam_attempts WHERE id = ?', [$intent]);
        }
        self::corregeix($examen, $a);
        return self::resultat($intent);
    }

    /** La correcció d'un intent ja entregat. */
    public static function resultat(int $intent): array
    {
        $a = Db::one('SELECT * FROM exam_attempts WHERE id = ? AND finished_at IS NOT NULL', [$intent])
            ?? throw new \InvalidArgumentException('Aquest intent encara no s\'ha entregat.');
        $examen = self::get($a['examen_id']);
        return [
            'intent' => (int) $a['id'],
            'examen' => $a['examen_id'],
            'titol' => $examen['titol'] ?? $a['examen_id'],
            'nota' => (float) $a['nota'],
            'aprovat' => (float) $a['nota'] >= 5,
            'segons' => (int) $a['finished_at'] - (int) $a['started_at'],
            'millor' => (float) Db::value('SELECT MAX(nota) FROM exam_attempts WHERE examen_id = ? AND finished_at IS NOT NULL', [$a['examen_id']]),
        ] + (json_decode((string) $a['detall'], true) ?: []);
    }

    /** Intents entregats d'un examen, del més nou al més antic. */
    public static function historial(string $id): array
    {
        return Db::all('SELECT id, nota, started_at, finished_at FROM exam_attempts WHERE examen_id = ? AND finished_at IS NOT NULL ORDER BY id DESC LIMIT 10', [$id]);
    }

    /** Millor nota i nombre d'intents de cada examen. */
    public static function resum(): array
    {
        $out = [];
        foreach (Db::all('SELECT examen_id, MAX(nota) millor, COUNT(*) intents FROM exam_attempts WHERE finished_at IS NOT NULL GROUP BY examen_id') as $r) {
            $out[$r['examen_id']] = ['millor' => (float) $r['millor'], 'intents' => (int) $r['intents']];
        }
        return $out;
    }

    private static function intentObert(int $intent): array
    {
        $a = Db::one('SELECT * FROM exam_attempts WHERE id = ?', [$intent]) ?? throw new \InvalidArgumentException('Intent inexistent');
        if ($a['finished_at'] !== null) {
            throw new \InvalidArgumentException('Aquest examen ja s\'ha entregat.');
        }
        return $a;
    }

    private static function guardaRespostes(int $intent, array $respostes): void
    {
        // Només textos: el navegador no pot desar res més que una resposta per referència
        $net = [];
        foreach ($respostes as $ref => $valor) {
            if (is_string($ref) && is_scalar($valor)) {
                $net[mb_substr($ref, 0, 80)] = (string) $valor;
            }
        }
        $json = json_encode($net, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if (strlen($json) > self::MAX_RESPOSTES) {
            throw new \InvalidArgumentException('Les respostes ocupen massa (el codi és massa llarg).');
        }
        Db::run('UPDATE exam_attempts SET respostes = ?, saved_at = ? WHERE id = ?', [$json, time(), $intent]);
    }

    // ------------------------------------------------------------------ Correcció

    private static function puntsPregunta(array $examen): float
    {
        return round((float) $examen['punts_teoria'] / count($examen['teoria']), 3);
    }

    /** Corregeix un intent, desa la nota i la correcció i, el primer cop que s'aprova, dona XP. */
    private static function corregeix(array $examen, array $a): void
    {
        $respostes = json_decode($a['respostes'], true) ?: [];
        $val = self::puntsPregunta($examen);

        $teoria = [];
        $puntsTeoria = 0.0;
        foreach ($examen['teoria'] as $n => $q) {
            $item = self::item($examen, $q);
            $resposta = $respostes[$item['ref']] ?? '';
            $ok = $resposta !== '' && ($item['tipus'] === 'completa' ? Lessons::isCorrect($item, $resposta) : Reptes::check($item, $resposta));
            $puntsTeoria += $ok ? $val : 0;
            $teoria[] = [
                'num' => $n + 1,
                'tipus' => $item['tipus'],
                'pregunta' => $item['pregunta'],
                'codi' => $item['codi'] ?? null,
                'ok' => $ok,
                'resposta' => self::llegible($item, $resposta),
                'solucio' => self::solucioLlegible($item),
                'explicacio' => $item['explicacio'],
            ];
        }

        $practica = [];
        $puntsPractica = 0.0;
        foreach ($examen['practica'] as $p) {
            $codi = $respostes[$examen['id'] . '#' . $p['id']] ?? $p['codi_inicial'];
            $lab = ['id' => 'examen-' . $examen['id'], 'fitxer' => $p['fitxer'], 'passos' => [['codi' => $p['codi'] ?? [], 'proves' => $p['proves']]]];
            // Sense cap canvi no cal executar res: les proves fallarien totes
            $r = trim($codi) === trim($p['codi_inicial'])
                ? ['codi' => [], 'proves' => array_map(fn($t) => ['nom' => $t['nom'], 'ok' => false, 'motiu' => 'No has escrit cap programa.', 'ordre' => ''], $p['proves'])]
                : Labs::evaluate($lab, 0, $codi);
            $passades = count(array_filter($r['proves'], fn($t) => $t['ok']));
            $punts = round($p['punts'] * $passades / count($p['proves']), 2);
            $puntsPractica += $punts;
            $practica[] = [
                'titol' => $p['titol'],
                'punts' => $punts,
                'max' => $p['punts'],
                'proves' => array_map(fn($t) => ['nom' => $t['nom'], 'ok' => $t['ok'], 'motiu' => $t['motiu'], 'ordre' => $t['ordre'] ?? ''], $r['proves']),
                'codi_checks' => $r['codi'],
                'codi' => $codi,
                'solucio' => $p['solucio'],
                'solucio_explicacio' => $p['solucio_explicacio'] ?? '',
            ];
        }

        $nota = round(min(10, $puntsTeoria + $puntsPractica), 2);
        // L'XP, només el primer cop que s'aprova cada examen (award() no repeteix la mateixa referència)
        $xp = $nota >= 5 ? Progress::award('examen', 'examen:' . $examen['id'], (int) ($examen['xp'] ?? 50)) : 0;
        $detall = [
            'teoria' => $teoria,
            'practica' => $practica,
            'punts_teoria' => round($puntsTeoria, 2),
            'max_teoria' => (float) $examen['punts_teoria'],
            'punts_practica' => round($puntsPractica, 2),
            'max_practica' => (float) array_sum(array_column($examen['practica'], 'punts')),
            'encerts' => count(array_filter($teoria, fn($t) => $t['ok'])),
            'xp' => $xp,
        ];
        Db::run('UPDATE exam_attempts SET finished_at = ?, nota = ?, detall = ? WHERE id = ? AND finished_at IS NULL',
            [time(), $nota, json_encode($detall, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), (int) $a['id']]);
    }

    /** La resposta de l'alumne en text: l'opció triada, la línia, l'ordre de les línies... */
    private static function llegible(array $item, string $resposta): string
    {
        if ($resposta === '') {
            return '(sense resposta)';
        }
        return match ($item['tipus']) {
            'test' => (string) ($item['opcions'][(int) $resposta] ?? $resposta),
            'error' => 'Línia ' . (int) $resposta,
            'ordenar' => implode("\n", array_map(
                fn($k) => ($item['linies'] ?? $item['elements'])[$k] ?? '?',
                Lessons::ordreIndexos($item, count($item['linies'] ?? $item['elements']), $resposta) ?? []
            )),
            default => $resposta,
        };
    }

    private static function solucioLlegible(array $item): string
    {
        $s = Reptes::solution($item);
        return match ($item['tipus']) {
            'test' => (string) $item['opcions'][$s['correcta']],
            'error' => 'Línia ' . $s['linia'] . ($s['corregit'] ? "\n\n" . $s['corregit'] : ''),
            'ordenar' => implode("\n", $s['ordre']),
            default => (string) $s['resposta'],
        };
    }
}
