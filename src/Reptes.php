<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Reptes ràpids i repàs espaciat.
 *
 * Les preguntes dels reptes són a content/reptes/*.php. Cada partida és una «sessió» desada a la base de dades:
 * el servidor tria les preguntes, corregeix cada resposta i calcula la puntuació (el navegador només mostra).
 *
 * Referències de pregunta: "repte:<id>" (banc de reptes) o "lesson:<lliçó>#<pregunta>" (preguntes de les lliçons, al repàs).
 */
final class Reptes
{
    /** Configuració de cada mode. */
    public const MODES = [
        'rapid'   => ['titol' => 'Contrarellotge',   'icona' => 'rellotge', 'preguntes' => 10, 'temps' => 150,  'xp' => 3, 'tipus' => null],
        'error'   => ['titol' => 'Troba l\'error',   'icona' => 'bitxo', 'preguntes' => 6,  'temps' => null, 'xp' => 2, 'tipus' => 'error'],
        'sortida' => ['titol' => 'Què imprimeix?',   'icona' => 'impressora', 'preguntes' => 8,  'temps' => null, 'xp' => 2, 'tipus' => 'sortida'],
        'ordenar' => ['titol' => 'Ordena el codi',   'icona' => 'ordena', 'preguntes' => 5,  'temps' => null, 'xp' => 3, 'tipus' => 'ordenar'],
        'repas'   => ['titol' => 'Repàs espaciat',   'icona' => 'repas', 'preguntes' => 15, 'temps' => null, 'xp' => 3, 'tipus' => null],
    ];

    /** XP màxim que es pot guanyar cada dia amb els reptes (el repàs no hi compta). */
    public const DAILY_XP_CAP = 80;

    /** Marge (segons) per a les respostes que arriben just quan s'acaba el temps. */
    private const GRACE_S = 3;

    // ------------------------------------------------------------------ Banc de preguntes

    /** @return array<string, array> id => pregunta (amb la clau 'tema') */
    public static function bank(): array
    {
        static $bank = null;
        if ($bank === null) {
            $bank = [];
            foreach (glob(APP_ROOT . '/content/reptes/*.php') as $file) {
                $data = require $file;
                foreach ($data['items'] as $item) {
                    $bank[$item['id']] = $item + ['tema' => $data['tema']];
                }
            }
        }
        return $bank;
    }

    public static function temes(): array
    {
        $temes = array_values(array_unique(array_column(self::bank(), 'tema')));
        sort($temes);
        return $temes;
    }

    /** Recupera una pregunta a partir de la seva referència (banc de reptes o lliçó). */
    public static function item(string $ref): ?array
    {
        if (str_starts_with($ref, 'repte:')) {
            $item = self::bank()[substr($ref, 6)] ?? null;
            return $item ? $item + ['ref' => $ref, 'origen_tipus' => 'repte'] : null;
        }
        if (preg_match('/^lesson:([a-z0-9\-]+)#(\w+)$/', $ref, $m)) {
            $lesson = Lessons::get($m[1]);
            $q = $lesson ? (Lessons::questions($lesson)[$m[2]] ?? null) : null;
            return $q ? $q + ['ref' => $ref, 'origen_tipus' => 'lesson', 'tema' => $lesson['tema'], 'leccio' => $lesson['titol']] : null;
        }
        return null;
    }

    // ------------------------------------------------------------------ Sessions

    /** Comença una partida i retorna les preguntes (sense les respostes). */
    public static function start(string $mode, ?string $tema): array
    {
        $cfg = self::MODES[$mode] ?? throw new \InvalidArgumentException('Mode desconegut');

        if ($mode === 'repas') {
            $refs = array_values(array_filter(Review::due($cfg['preguntes']), fn($r) => self::item($r) !== null));
            if ($refs === []) {
                throw new \InvalidArgumentException('Avui no tens res per repassar. Torna-hi demà!');
            }
        } else {
            $pool = array_filter(self::bank(), fn($i) => ($cfg['tipus'] === null || $i['tipus'] === $cfg['tipus'])
                && ($tema === null || $tema === '' || $i['tema'] === $tema));
            if ($pool === []) {
                throw new \InvalidArgumentException('No hi ha preguntes d\'aquest tipus per a aquest tema.');
            }
            $ids = array_keys($pool);
            shuffle($ids);
            $refs = array_map(fn($id) => "repte:$id", array_slice($ids, 0, $cfg['preguntes']));
        }

        Db::run('INSERT INTO repte_sessions (mode, tema, items, started_at, limit_s) VALUES (?, ?, ?, ?, ?)', [
            $mode, $tema ?: null, json_encode($refs), time(), $cfg['temps'],
        ]);
        $id = (int) Db::pdo()->lastInsertId();

        return [
            'session' => $id,
            'mode' => $mode,
            'titol' => $cfg['titol'],
            'temps' => $cfg['temps'],
            'items' => array_map(fn($ref) => self::publicItem(self::item($ref)), $refs),
        ];
    }

    /** El que veu el navegador d'una pregunta: res que en reveli la resposta. */
    private static function publicItem(array $item): array
    {
        $pub = [
            'ref' => $item['ref'],
            'tipus' => $item['tipus'],
            'pregunta' => $item['pregunta'] ?? null,
            'codi' => $item['codi'] ?? null,
            'tema' => $item['tema'],
            'leccio' => $item['leccio'] ?? null,
            'errata' => $item['errata'] ?? null,
            'args' => $item['args'] ?? null,
        ];
        if ($item['tipus'] === 'test') {
            $pub['opcions'] = $item['opcions'];
        }
        if ($item['tipus'] === 'ordenar') {
            $lines = $item['linies'] ?? $item['elements'];
            $order = array_keys($lines);
            // Barrejat, però mai en l'ordre correcte
            do {
                shuffle($order);
            } while (count($order) > 1 && $order === array_keys($lines));
            $pub['linies'] = array_map(fn($k) => ['idx' => Lessons::ordreToken($item, $k), 'text' => $lines[$k]], $order);
            $pub['codi_ordenar'] = isset($item['linies'], $item['sortida']);   // codi (monoespai) o text
        }
        return $pub;
    }

    /** Corregeix una resposta d'una partida en curs. */
    public static function answer(int $sessionId, string $ref, string $answer): array
    {
        $s = Db::one('SELECT * FROM repte_sessions WHERE id = ?', [$sessionId]) ?? throw new \InvalidArgumentException('Partida inexistent');
        if ($s['finished_at'] !== null) {
            throw new \InvalidArgumentException('Aquesta partida ja ha acabat.');
        }
        $refs = json_decode($s['items'], true);
        $answers = json_decode($s['answers'], true) ?: [];
        if (!in_array($ref, $refs, true)) {
            throw new \InvalidArgumentException('Aquesta pregunta no és d\'aquesta partida.');
        }
        if (isset($answers[$ref])) {
            throw new \InvalidArgumentException('Ja has respost aquesta pregunta.');
        }
        $late = $s['limit_s'] !== null && time() > (int) $s['started_at'] + (int) $s['limit_s'] + self::GRACE_S;
        if ($late) {
            throw new \InvalidArgumentException('S\'ha acabat el temps!');
        }

        $item = self::item($ref);
        $correct = self::check($item, $answer);
        $answers[$ref] = ['ok' => $correct, 't' => time() - (int) $s['started_at']];
        Db::run('UPDATE repte_sessions SET answers = ?, encerts = encerts + ? WHERE id = ?', [json_encode($answers), $correct ? 1 : 0, $sessionId]);

        if ($item['origen_tipus'] === 'repte') {
            Db::run('INSERT INTO repte_stats (item_id, attempts, correct) VALUES (?, 1, ?)
                     ON CONFLICT(item_id) DO UPDATE SET attempts = attempts + 1, correct = correct + excluded.correct',
                [substr($ref, 6), $correct ? 1 : 0]);
        }
        foreach ($item['conceptes'] ?? [] as $c) {
            Progress::concept($c, $correct ? 'successes' : 'fails');
        }
        // Repàs espaciat: el que falles torna a sortir; al repàs, l'encert fa pujar de caixa
        if (!$correct) {
            Review::fail($ref);
        } elseif ($s['mode'] === 'repas') {
            Review::success($ref);
        }

        return ['correcte' => $correct, 'explicacio' => $item['explicacio'] ?? ''] + self::solution($item);
    }

    /** Acaba la partida, calcula la puntuació i dona l'XP. */
    public static function finish(int $sessionId): array
    {
        $s = Db::one('SELECT * FROM repte_sessions WHERE id = ?', [$sessionId]) ?? throw new \InvalidArgumentException('Partida inexistent');
        $cfg = self::MODES[$s['mode']];
        $total = count(json_decode($s['items'], true));
        $encerts = (int) $s['encerts'];

        $answers = json_decode($s['answers'], true) ?: [];
        ['punts' => $punts, 'max' => $comboMax] = self::combo($answers);

        if ($s['finished_at'] === null) {
            $score = $punts;
            if ($cfg['temps'] !== null) {
                // Bonus de velocitat: 1 punt per cada 5 segons que sobren (només si s'han respost totes)
                $used = min((int) $cfg['temps'], time() - (int) $s['started_at']);
                if (count($answers) === $total) {
                    $score += intdiv((int) $cfg['temps'] - $used, 5);
                }
            }
            $xp = $encerts * $cfg['xp'];
            if ($s['mode'] !== 'repas') {
                $xp = min($xp, max(0, self::DAILY_XP_CAP - self::xpToday()));
            }
            Db::run('UPDATE repte_sessions SET finished_at = ?, score = ?, xp = ? WHERE id = ?', [time(), $score, $xp, $sessionId]);
            Progress::award('repte', "session#$sessionId", $xp);
            $s = Db::one('SELECT * FROM repte_sessions WHERE id = ?', [$sessionId]);
        }

        $best = (int) Db::value('SELECT MAX(score) FROM repte_sessions WHERE mode = ? AND COALESCE(tema, \'\') = COALESCE(?, \'\') AND finished_at IS NOT NULL AND id <> ?',
            [$s['mode'], $s['tema'], $sessionId]);
        return [
            'encerts' => $encerts,
            'total' => $total,
            'score' => (int) $s['score'],
            'xp' => (int) $s['xp'],
            'record' => $s['mode'] !== 'repas' && (int) $s['score'] > $best && $encerts > 0,
            'millor' => max($best, (int) $s['score']),
            'limit_xp' => $s['mode'] !== 'repas' && self::xpToday() >= self::DAILY_XP_CAP,
            'repas_pendent' => Review::countDue(),
            'combo_max' => $comboMax,
            'segons' => (int) $s['finished_at'] - (int) $s['started_at'],
        ];
    }

    /**
     * Punts dels encerts amb combo: cada encert seguit val més (10, 15, 20 i, a partir del quart, 25).
     * Les respostes es desen en l'ordre en què s'han contestat.
     * @return array{punts:int, max:int} punts i el combo més llarg
     */
    public static function combo(array $answers): array
    {
        $punts = $seguits = $max = 0;
        foreach ($answers as $a) {
            if (!empty($a['ok'])) {
                $seguits++;
                $max = max($max, $seguits);
                $punts += 10 + 5 * min($seguits - 1, 3);
            } else {
                $seguits = 0;
            }
        }
        return ['punts' => $punts, 'max' => $max];
    }

    /** XP guanyat avui amb reptes (sense el repàs). */
    public static function xpToday(): int
    {
        return (int) Db::value("SELECT COALESCE(SUM(xp), 0) FROM repte_sessions WHERE mode <> 'repas'
                                AND date(finished_at, 'unixepoch', 'localtime') = date('now','localtime')");
    }

    public static function bestScores(): array
    {
        $out = [];
        foreach (Db::all("SELECT mode, MAX(score) best, COUNT(*) n FROM repte_sessions WHERE finished_at IS NOT NULL GROUP BY mode") as $r) {
            $out[$r['mode']] = ['millor' => (int) $r['best'], 'partides' => (int) $r['n']];
        }
        return $out;
    }

    // ------------------------------------------------------------------ Correcció

    public static function check(array $item, string $answer): bool
    {
        if ($item['origen_tipus'] === 'lesson') {
            return Lessons::isCorrect($item, $answer);
        }
        return match ($item['tipus']) {
            'test'    => (int) $answer === (int) $item['correcta'],
            'sortida' => Labs::normalize($answer) === Labs::normalize($item['resposta']),
            'error'   => (int) $answer === (int) $item['linia'],
            'ordenar' => self::checkOrder($item, $answer),
            default   => false,
        };
    }

    /**
     * Ordenar: és correcte si l'ordre coincideix amb el de la solució o, si és codi,
     * si el programa ordenat així funciona i treu el mateix resultat.
     */
    private static function checkOrder(array $item, string $answer): bool
    {
        $lines = $item['linies'] ?? $item['elements'];
        $order = Lessons::ordreIndexos($item, count($lines), $answer) ?? [];
        $sorted = $order;
        sort($sorted);
        if ($sorted !== array_keys($lines)) {
            return false;   // falten o sobren línies
        }
        $user = array_map(fn($k) => rtrim($lines[$k]), $order);
        if ($user === array_map('rtrim', $lines)) {
            return true;
        }
        if (!isset($item['linies'], $item['sortida'])) {
            return false;   // text (no codi): només val l'ordre exacte
        }
        $r = Sandbox::run(implode("\n", $user) . "\n", [], '', 'ordena.php', null, $item['fitxers'] ?? []);
        if ($r['busy']) {
            // No és culpa de l'alumne: no es pot comptar com a resposta fallada (seria definitiva i aniria al repàs)
            throw new \InvalidArgumentException('Hi ha massa programes executant-se ara mateix. Torna a prémer Comprova d\'aquí a un moment.');
        }
        return trim($r['stderr']) === '' && !$r['timeout'] && Labs::normalize($r['stdout']) === Labs::normalize($item['sortida']);
    }

    /** La resposta correcta, per mostrar-la després de respondre. */
    private static function solution(array $item): array
    {
        return match ($item['tipus']) {
            'test'     => ['correcta' => (int) $item['correcta']],
            'sortida'  => ['resposta' => $item['resposta']],
            'completa' => ['resposta' => $item['respostes'][0]],
            'error'    => ['linia' => (int) $item['linia'], 'corregit' => $item['corregit'] ?? null],
            'ordenar'  => ['ordre' => array_values($item['linies'] ?? $item['elements'])],
            default    => [],
        };
    }
}
