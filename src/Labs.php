<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Motor dels laboratoris: carrega les definicions de content/labs/*.php,
 * valida cada pas, gestiona pistes, solucions i XP.
 */
final class Labs
{
    /** Intents fallits mínims abans de poder demanar la solució d'un pas. */
    public const ATTEMPTS_FOR_SOLUTION = 3;

    public static function all(): array
    {
        $labs = [];
        foreach (glob(APP_ROOT . '/content/labs/*.php') as $file) {
            $lab = require $file;
            $labs[$lab['id']] = $lab;
        }
        uasort($labs, fn($a, $b) => ($a['ordre'] ?? 99) <=> ($b['ordre'] ?? 99));
        return $labs;
    }

    public static function get(string $id): ?array
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $id)) {
            return null;
        }
        $file = APP_ROOT . "/content/labs/$id.php";
        return is_file($file) ? require $file : null;
    }

    /** Estat de l'alumne en aquest laboratori (el crea si no existeix). */
    public static function state(array $lab): array
    {
        $state = Db::one('SELECT * FROM lab_state WHERE lab_id = ?', [$lab['id']]);
        if ($state === null) {
            Db::run('INSERT INTO lab_state (lab_id, current_step, code) VALUES (?, 0, ?)', [$lab['id'], $lab['codi_inicial']]);
            $state = Db::one('SELECT * FROM lab_state WHERE lab_id = ?', [$lab['id']]);
        }
        $steps = [];
        foreach (Db::all('SELECT * FROM lab_steps WHERE lab_id = ?', [$lab['id']]) as $row) {
            $steps[(int) $row['step']] = $row;
        }
        $state['steps'] = $steps;
        return $state;
    }

    public static function stepRow(string $labId, int $step): array
    {
        Db::run('INSERT OR IGNORE INTO lab_steps (lab_id, step) VALUES (?, ?)', [$labId, $step]);
        return Db::one('SELECT * FROM lab_steps WHERE lab_id = ? AND step = ?', [$labId, $step]);
    }

    public static function saveCode(string $labId, string $code): void
    {
        Db::run('UPDATE lab_state SET code = ? WHERE lab_id = ?', [$code, $labId]);
    }

    /**
     * Comprova el pas actual amb el codi de l'alumne.
     * @return array{superat:bool, codi:array, proves:array, xp:int, seguent:?int, acabat:bool}
     */
    public static function check(array $lab, int $stepIdx, string $code): array
    {
        $state = self::state($lab);
        if ($stepIdx !== (int) $state['current_step'] && !isset($state['steps'][$stepIdx]['passed_at'])) {
            throw new \InvalidArgumentException('Aquest pas encara està bloquejat.');
        }
        $step = $lab['passos'][$stepIdx] ?? throw new \InvalidArgumentException('Pas inexistent');
        self::saveCode($lab['id'], $code);

        ['codi' => $codeChecks, 'proves' => $tests, 'superat' => $passed] = self::evaluate($lab, $stepIdx, $code);

        $row = self::stepRow($lab['id'], $stepIdx);
        Db::run('UPDATE lab_steps SET attempts = attempts + 1, fails = fails + ? WHERE lab_id = ? AND step = ?', [$passed ? 0 : 1, $lab['id'], $stepIdx]);

        $xp = 0;
        $next = null;
        $finished = false;
        if ($passed) {
            $first = $row['passed_at'] === null;
            if ($first) {
                Db::run("UPDATE lab_steps SET passed_at = datetime('now','localtime') WHERE lab_id = ? AND step = ?", [$lab['id'], $stepIdx]);
                $xp = Progress::award('lab_step', "{$lab['id']}#$stepIdx", self::stepXp($step, $row));
                foreach ($step['conceptes'] ?? [] as $c) {
                    Progress::concept($c, 'successes');
                }
            }
            if ($stepIdx + 1 < count($lab['passos'])) {
                $next = $stepIdx + 1;
                if ((int) $state['current_step'] <= $stepIdx) {
                    Db::run('UPDATE lab_state SET current_step = ? WHERE lab_id = ?', [$next, $lab['id']]);
                }
            } else {
                $finished = true;
                if ($state['completed_at'] === null) {
                    Db::run("UPDATE lab_state SET completed_at = datetime('now','localtime') WHERE lab_id = ?", [$lab['id']]);
                }
                $xp += Progress::award('lab_done', $lab['id'], (int) ($lab['xp_final'] ?? 50));
            }
        } else {
            foreach ($step['conceptes'] ?? [] as $c) {
                Progress::concept($c, 'fails');
            }
        }

        return ['superat' => $passed, 'codi' => $codeChecks, 'proves' => $tests, 'xp' => $xp, 'seguent' => $next, 'acabat' => $finished];
    }

    /**
     * Avalua el codi contra un pas sense desar res: comprovacions sobre el codi i proves executant-lo.
     * També el fa servir eines/verifica-contingut.php per comprovar les solucions de referència.
     */
    public static function evaluate(array $lab, int $stepIdx, string $code): array
    {
        $step = $lab['passos'][$stepIdx];

        // 1) Comprovacions sobre el codi (sense executar-lo)
        $codeChecks = [];
        $clean = self::stripComments($code);
        foreach ($step['codi'] ?? [] as $chk) {
            $ok = isset($chk['regex']) ? (bool) preg_match($chk['regex'], $clean) : !preg_match($chk['no_regex'], $clean);
            $codeChecks[] = ['ok' => $ok, 'missatge' => $chk['missatge']];
        }

        // 2) Proves executant el programa
        $tests = [];
        foreach ($step['proves'] as $t) {
            $tests[] = self::runTest($lab, $t, $code);
        }

        $passed = !in_array(false, array_column($codeChecks, 'ok'), true) && !in_array(false, array_column($tests, 'ok'), true);
        return ['codi' => $codeChecks, 'proves' => $tests, 'superat' => $passed];
    }

    /** XP d'un pas: el valor base menys 5 per cada nivell de pista. Amb la solució vista, només 2. */
    public static function stepXp(array $step, array $row): int
    {
        if ((int) $row['solution_seen']) {
            return 2;
        }
        return max(5, (int) ($step['xp'] ?? 20) - 5 * (int) $row['hints_used']);
    }

    private static function runTest(array $lab, array $t, string $code): array
    {
        $args = $t['args'] ?? [];
        $r = Sandbox::run($code, $args, $t['stdin'] ?? '', $lab['fitxer'], $t['append'] ?? null);
        $out = self::normalize($r['stdout']);
        $res = [
            'nom' => $t['nom'],
            'ordre' => 'php ' . $lab['fitxer'] . ($args ? ' ' . implode(' ', array_map([self::class, 'shellQuote'], $args)) : ''),
            'ok' => true,
            'motiu' => '',
            'esperat' => $t['exacte'] ?? null,
            'obtingut' => mb_substr($r['stdout'], 0, 3000),
            'errors' => ErrorExplainer::explain($r['stderr'], $r['timeout'], $r['truncated']),
        ];

        if ($r['busy']) {
            return ['ok' => false, 'motiu' => $r['stderr']] + $res;
        }
        if ($r['timeout'] || $r['truncated']) {
            return ['ok' => false, 'motiu' => 'El programa no ha acabat (possible bucle infinit).'] + $res;
        }
        if (trim($r['stderr']) !== '' && empty($t['permet_avisos'])) {
            return ['ok' => false, 'motiu' => 'PHP ha mostrat un error o un avís. El programa no ha de mostrar mai avisos de PHP.'] + $res;
        }
        // Codi de sortida: exit(1), exit(2)... (0 = tot bé)
        if (isset($t['codi_sortida']) && $r['exit_code'] !== $t['codi_sortida']) {
            return ['ok' => false, 'motiu' => "El programa ha acabat amb el codi de sortida {$r['exit_code']} i s'esperava exit({$t['codi_sortida']})."] + $res;
        }
        if (isset($t['exacte']) && $out !== self::normalize($t['exacte'])) {
            return ['ok' => false, 'motiu' => 'La sortida no coincideix amb l\'esperada.'] + $res;
        }
        foreach ($t['conte'] ?? [] as $needle) {
            if (!str_contains($out, self::normalize($needle))) {
                return ['ok' => false, 'motiu' => "La sortida hauria de contenir: «{$needle}»"] + $res;
            }
        }
        foreach ($t['no_conte'] ?? [] as $needle) {
            if (str_contains($out, self::normalize($needle))) {
                return ['ok' => false, 'motiu' => "La sortida NO hauria de contenir: «{$needle}»"] + $res;
            }
        }
        return $res;
    }

    /**
     * Normalitza una sortida per comparar-la sense ser massa estrictes:
     * - els espais repetits compten com un de sol (l'alineació no importa)
     * - "->" equival a "→" i l'apòstrof tipogràfic ’ equival a '
     * - s'ignoren les línies buides del final
     */
    public static function normalize(string $s): string
    {
        $s = str_replace(["\r\n", "\r", '->', '’', '‘', '“', '”'], ["\n", "\n", '→', "'", "'", '"', '"'], $s);
        $lines = array_map(fn($l) => trim(preg_replace('/[ \t]+/', ' ', $l)), explode("\n", $s));
        while ($lines !== [] && end($lines) === '') {
            array_pop($lines);
        }
        return implode("\n", $lines);
    }

    public static function shellQuote(string $a): string
    {
        return ($a === '' || preg_match('/\s/', $a)) ? '"' . $a . '"' : $a;
    }

    /** Treu els comentaris perquè una pista escrita en un comentari no compti com a codi. */
    public static function stripComments(string $code): string
    {
        $out = '';
        foreach (@token_get_all($code) as $t) {
            if (is_array($t) && in_array($t[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            $out .= is_array($t) ? $t[1] : $t;
        }
        return $out;
    }

    /** Retorna una pista (nivell 1, 2 o 3). S'han de demanar en ordre. */
    public static function hint(array $lab, int $stepIdx, int $level): array
    {
        $step = $lab['passos'][$stepIdx] ?? throw new \InvalidArgumentException('Pas inexistent');
        $row = self::stepRow($lab['id'], $stepIdx);
        $used = (int) $row['hints_used'];
        if ($level < 1 || $level > count($step['pistes']) || $level > $used + 1) {
            throw new \InvalidArgumentException('Has de demanar les pistes en ordre.');
        }
        if ($level > $used) {
            Db::run('UPDATE lab_steps SET hints_used = ? WHERE lab_id = ? AND step = ?', [$level, $lab['id'], $stepIdx]);
            foreach ($step['conceptes'] ?? [] as $c) {
                Progress::concept($c, 'hints');
            }
        }
        $row['hints_used'] = max($used, $level);
        return ['nivell' => $level, 'text' => $step['pistes'][$level - 1], 'xp_pas' => self::stepXp($step, $row)];
    }

    public static function canSeeSolution(array $row): bool
    {
        return (int) $row['fails'] >= self::ATTEMPTS_FOR_SOLUTION;
    }

    public static function solution(array $lab, int $stepIdx): array
    {
        $step = $lab['passos'][$stepIdx] ?? throw new \InvalidArgumentException('Pas inexistent');
        $row = self::stepRow($lab['id'], $stepIdx);
        if (!self::canSeeSolution($row)) {
            $falten = self::ATTEMPTS_FOR_SOLUTION - (int) $row['fails'];
            throw new \InvalidArgumentException("Encara no. Prova-ho $falten vegada/es més (o demana una pista o pregunta al tutor).");
        }
        Db::run('UPDATE lab_steps SET solution_seen = 1 WHERE lab_id = ? AND step = ?', [$lab['id'], $stepIdx]);
        return ['codi' => $step['solucio'], 'explicacio' => $step['solucio_explicacio'] ?? ''];
    }

    /** Resum final: què has après i què t'ha costat més. */
    public static function summary(array $lab): array
    {
        $state = self::state($lab);
        $rows = $state['steps'];
        $costos = [];
        $totalXp = 0;
        foreach ($lab['passos'] as $i => $step) {
            $r = $rows[$i] ?? ['fails' => 0, 'hints_used' => 0, 'solution_seen' => 0, 'attempts' => 0];
            $cost = (int) $r['fails'] + 2 * (int) $r['hints_used'] + 5 * (int) $r['solution_seen'];
            foreach ($step['conceptes'] ?? [] as $c) {
                $costos[$c] = ($costos[$c] ?? 0) + $cost;
            }
            $totalXp += (int) Db::value('SELECT amount FROM xp_events WHERE kind = ? AND ref = ?', ['lab_step', "{$lab['id']}#$i"]);
        }
        $totalXp += (int) Db::value('SELECT amount FROM xp_events WHERE kind = ? AND ref = ?', ['lab_done', $lab['id']]);
        arsort($costos);
        $dificils = array_keys(array_filter($costos, fn($v) => $v > 0));

        $temps = null;
        if ($state['completed_at']) {
            $temps = (int) round((strtotime($state['completed_at']) - strtotime($state['started_at'])) / 60);
        }
        return [
            'apres' => $lab['resum_apres'] ?? [],
            'dificils' => array_slice($dificils, 0, 4),
            'xp' => $totalXp,
            'minuts' => $temps,
            'intents' => array_sum(array_map(fn($r) => (int) $r['attempts'], $rows)),
            'pistes' => array_sum(array_map(fn($r) => (int) $r['hints_used'], $rows)),
        ];
    }

    /** Torna a començar el laboratori des de zero (no treu l'XP ja guanyat). */
    public static function reset(array $lab): void
    {
        Db::run('DELETE FROM lab_state WHERE lab_id = ?', [$lab['id']]);
        Db::run('DELETE FROM lab_steps WHERE lab_id = ?', [$lab['id']]);
        Db::run('DELETE FROM tutor_messages WHERE lab_id = ?', [$lab['id']]);
    }
}
