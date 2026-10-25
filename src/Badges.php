<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Insígnies: fites que s'aconsegueixen fent servir la app.
 *
 * No cal cridar res especial quan passa alguna cosa: check() mira l'estat actual (Stats::snapshot)
 * i desa les insígnies noves. L'API el crida després de cada resposta i el navegador mostra l'avís.
 * Una insígnia aconseguida no es perd mai (encara que després es reiniciï un laboratori).
 */
final class Badges
{
    /**
     * Totes les insígnies: id => [icona, títol, com s'aconsegueix, condició(snapshot), progrés(snapshot) opcional]
     * El progrés és [fet, cal] i serveix per mostrar «3/5» a les que encara no tens.
     */
    public static function definitions(): array
    {
        $defs = [
            'primer-encert' => ['🌱', 'Hola, món!', 'Encerta la primera pregunta d\'una lliçó.',
                fn($s) => $s['encerts_leccio'] >= 1, null],
            'lector' => ['📖', 'Lector', 'Acaba 5 lliçons.',
                fn($s) => $s['lliçons'] >= 5, fn($s) => [$s['lliçons'], 5]],
            'biblioteca' => ['📚', 'Tot el temari', 'Acaba totes les lliçons.',
                fn($s) => $s['lliçons'] >= $s['lliçons_total'], fn($s) => [$s['lliçons'], $s['lliçons_total']]],
            'primer-lab' => ['🧪', 'Primer laboratori', 'Acaba un laboratori.',
                fn($s) => $s['labs'] >= 1, null],
            'net' => ['🧼', 'Sense xuleta', 'Acaba un laboratori sense demanar cap pista ni mirar la solució.',
                fn($s) => $s['labs_nets'] >= 1, null],
            'laborant' => ['⚗️', 'Laborant', 'Acaba 5 laboratoris.',
                fn($s) => $s['labs'] >= 5, fn($s) => [$s['labs'], 5]],
            'cientific' => ['🔬', 'Científic boig', 'Acaba tots els laboratoris.',
                fn($s) => $s['labs'] >= $s['labs_total'], fn($s) => [$s['labs'], $s['labs_total']]],
            'errates' => ['🕵️', 'Caçador d\'errates', 'Troba totes les errates reals del temari a «Troba l\'error».',
                fn($s) => $s['errates_total'] > 0 && $s['errates'] >= $s['errates_total'], fn($s) => [$s['errates'], $s['errates_total']]],
            'cacabugs' => ['🐞', 'Caçabugs', 'Troba 15 errors a «Troba l\'error».',
                fn($s) => $s['errors_trobats'] >= 15, fn($s) => [$s['errors_trobats'], 15]],
            'perfecte' => ['💯', 'Ple', 'Acaba una partida de reptes sense cap error.',
                fn($s) => $s['partida_perfecta'], null],
            'llamp' => ['⚡', 'Llamp', 'Fes una contrarellotge perfecta en menys de 90 segons.',
                fn($s) => $s['llamp'], null],
            'jugador' => ['🎯', 'Habitual', 'Juga 20 partides de reptes.',
                fn($s) => $s['partides'] >= 20, fn($s) => [$s['partides'], 20]],
            'repassador' => ['🔁', 'Constància', 'Encerta 30 preguntes al repàs espaciat.',
                fn($s) => $s['encerts_repas'] >= 30, fn($s) => [$s['encerts_repas'], 30]],
            'elefant' => ['🐘', 'Memòria d\'elefant', 'Fes arribar una pregunta a la caixa 5 del repàs.',
                fn($s) => $s['caixa5'], null],
            'ratxa-3' => ['🔥', 'Escalfant', 'Practica 3 dies seguits.',
                fn($s) => $s['ratxa'] >= 3, fn($s) => [min($s['ratxa'], 3), 3]],
            'ratxa-7' => ['☄️', 'Imparable', 'Practica 7 dies seguits.',
                fn($s) => $s['ratxa'] >= 7, fn($s) => [min($s['ratxa'], 7), 7]],
            'conceptes' => ['🧠', 'Ho tinc clar', 'Domina 10 conceptes (80 % d\'encerts).',
                fn($s) => $s['conceptes_dominats'] >= 10, fn($s) => [$s['conceptes_dominats'], 10]],
            'nivell-5' => ['🏅', 'Mestre dels arrays', 'Arriba al nivell 5.',
                fn($s) => $s['nivell'] >= 5, fn($s) => [min($s['nivell'], 5), 5]],
        ];
        // Una insígnia per tema: totes les lliçons i laboratoris acabats
        foreach (self::temes() as $num => $tema) {
            $defs["tema-$num"] = ['🗺️', "Tema $num superat", "Acaba totes les lliçons i laboratoris de: $tema.",
                fn($s) => in_array($num, $s['temes_complets'], true), null];
        }
        return $defs;
    }

    /** Temes amb número, a partir del contingut (no cal la base de dades). */
    private static function temes(): array
    {
        $temes = [];
        foreach ([...Lessons::all(), ...Labs::all()] as $x) {
            if (preg_match('/Tema\s+(\d+)/u', $x['tema'], $m)) {
                $temes[(int) $m[1]] = $x['tema'];
            }
        }
        ksort($temes);
        return $temes;
    }

    /** Insígnies aconseguides: id => data */
    public static function unlocked(): array
    {
        return array_column(Db::all('SELECT id, unlocked_at FROM badges'), 'unlocked_at', 'id');
    }

    /** Desa les insígnies que s'acaben d'aconseguir i les retorna (per mostrar l'avís). */
    public static function check(): array
    {
        $have = self::unlocked();
        $snapshot = null;
        $new = [];
        foreach (self::definitions() as $id => [$icona, $titol, $desc, $cond]) {
            if (isset($have[$id])) {
                continue;
            }
            $snapshot ??= Stats::snapshot();
            if ($cond($snapshot) && Db::run('INSERT OR IGNORE INTO badges (id) VALUES (?)', [$id])->rowCount() > 0) {
                $new[] = ['id' => $id, 'icona' => $icona, 'titol' => $titol, 'descripcio' => $desc];
            }
        }
        return $new;
    }

    /** Llista per a la pàgina de progrés: aconseguides primer (les més recents davant), després les que falten. */
    public static function all(): array
    {
        $have = self::unlocked();
        $snapshot = Stats::snapshot();
        $list = [];
        foreach (self::definitions() as $id => [$icona, $titol, $desc, $cond, $prog]) {
            $list[] = [
                'id' => $id, 'icona' => $icona, 'titol' => $titol, 'descripcio' => $desc,
                'data' => $have[$id] ?? null,
                'progres' => !isset($have[$id]) && $prog ? $prog($snapshot) : null,
            ];
        }
        usort($list, fn($a, $b) => [$b['data'] !== null, $b['data'] ?? ''] <=> [$a['data'] !== null, $a['data'] ?? '']);
        return $list;
    }
}
