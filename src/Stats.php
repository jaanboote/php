<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Estadístiques i mapa de temes (pàgina «Progrés»).
 * Tot es calcula a partir de les taules que ja omplen lliçons, laboratoris i reptes.
 */
final class Stats
{
    /** Nivell de domini d'un concepte: cap (sense dades), fluix, practicant o dominat. */
    public static function mastery(array $c): array
    {
        $s = (int) $c['successes'];
        $total = $s + (int) $c['fails'] + (int) $c['hints'];
        if ($total === 0) {
            return ['nivell' => 'cap', 'pct' => null, 'text' => 'Encara no l\'has practicat'];
        }
        $pct = (int) round($s * 100 / $total);
        $nivell = match (true) {
            $pct >= 80 && $s >= 3 => 'dominat',
            $pct >= 50 => 'practicant',
            default => 'fluix',
        };
        return ['nivell' => $nivell, 'pct' => $pct, 'text' => "$s encerts · {$c['fails']} errors · {$c['hints']} pistes"];
    }

    /** concepte => fila de concept_stats */
    public static function concepts(): array
    {
        $out = [];
        foreach (Db::all('SELECT * FROM concept_stats') as $r) {
            $out[$r['concept']] = $r;
        }
        return $out;
    }

    /** Els conceptes que més costen (nivell «fluix»), els que tenen més errors primer. */
    public static function weak(int $limit): array
    {
        $out = [];
        foreach (self::concepts() as $c) {
            $m = self::mastery($c);
            if ($m['nivell'] === 'fluix') {
                $out[] = $c + $m;
            }
        }
        usort($out, fn($a, $b) => ($b['fails'] + $b['hints']) <=> ($a['fails'] + $a['hints']));
        return array_slice($out, 0, $limit);
    }

    /** Lliçons acabades: [id => true] */
    private static function lessonsDone(): array
    {
        return array_fill_keys(array_column(Db::all('SELECT lesson_id FROM lesson_state WHERE completed_at IS NOT NULL'), 'lesson_id'), true);
    }

    /** Estat dels laboratoris: [id => 'fet' | 'fent'] (els que no s'han començat no hi surten) */
    private static function labsState(): array
    {
        $out = [];
        foreach (Db::all('SELECT lab_id, current_step, completed_at FROM lab_state') as $r) {
            if ($r['completed_at'] !== null) {
                $out[$r['lab_id']] = 'fet';
            } elseif ((int) $r['current_step'] > 0) {
                $out[$r['lab_id']] = 'fent';
            }
        }
        return $out;
    }

    /**
     * Mapa de temes: per a cada tema, les lliçons i laboratoris (en ordre) amb el seu estat,
     * l'encert als reptes i el domini de cada concepte que hi surt.
     */
    public static function temaMap(): array
    {
        $temes = [];
        $concepts = self::concepts();
        $lessonsDone = self::lessonsDone();
        $labsState = self::labsState();
        $add = function (string $tema) use (&$temes): void {
            $temes[$tema] ??= ['tema' => $tema, 'num' => preg_match('/Tema\s+(\d+)/u', $tema, $m) ? (int) $m[1] : 99,
                               'passos' => [], 'conceptes' => [], 'reptes' => ['intents' => 0, 'encerts' => 0, 'preguntes' => 0]];
        };

        foreach (Lessons::all() as $l) {
            $add($l['tema']);
            $answered = (int) Db::value('SELECT COUNT(*) FROM lesson_answers WHERE lesson_id = ?', [$l['id']]);
            $estat = isset($lessonsDone[$l['id']]) ? 'fet' : ($answered > 0 ? 'fent' : 'pendent');
            $temes[$l['tema']]['passos'][] = ['tipus' => 'leccio', 'id' => $l['id'], 'titol' => $l['titol'], 'estat' => $estat, 'ordre' => $l['ordre'] ?? 99];
            foreach (Lessons::questions($l) as $q) {
                foreach ($q['conceptes'] ?? [] as $c) {
                    $temes[$l['tema']]['conceptes'][$c] = true;
                }
            }
        }
        foreach (Labs::all() as $lab) {
            $add($lab['tema']);
            $temes[$lab['tema']]['passos'][] = ['tipus' => 'lab', 'id' => $lab['id'], 'titol' => $lab['titol'],
                                                'estat' => $labsState[$lab['id']] ?? 'pendent', 'ordre' => ($lab['ordre'] ?? 99) + 0.5];
            foreach ($lab['passos'] as $p) {
                foreach ($p['conceptes'] ?? [] as $c) {
                    $temes[$lab['tema']]['conceptes'][$c] = true;
                }
            }
        }
        $stats = [];
        foreach (Db::all('SELECT * FROM repte_stats') as $r) {
            $stats[$r['item_id']] = $r;
        }
        foreach (Reptes::bank() as $id => $item) {
            $add($item['tema']);
            $t = &$temes[$item['tema']]['reptes'];
            $t['preguntes']++;
            $t['intents'] += (int) ($stats[$id]['attempts'] ?? 0);
            $t['encerts'] += (int) ($stats[$id]['correct'] ?? 0);
            unset($t);
            foreach ($item['conceptes'] ?? [] as $c) {
                $temes[$item['tema']]['conceptes'][$c] = true;
            }
        }

        foreach ($temes as &$t) {
            // Lliçons i laboratoris barrejats segons l'ordre del temari (el laboratori just després de la seva lliçó)
            usort($t['passos'], fn($a, $b) => $a['ordre'] <=> $b['ordre']);
            $fets = count(array_filter($t['passos'], fn($p) => $p['estat'] === 'fet'));
            $t['fets'] = $fets;
            $t['pct'] = $t['passos'] ? (int) round($fets * 100 / count($t['passos'])) : 0;
            $t['complet'] = $t['passos'] !== [] && $fets === count($t['passos']);
            $noms = array_keys($t['conceptes']);
            sort($noms);
            $t['conceptes'] = array_map(fn($c) => ['nom' => $c] + self::mastery($concepts[$c] ?? ['successes' => 0, 'fails' => 0, 'hints' => 0]), $noms);
        }
        unset($t);
        uasort($temes, fn($a, $b) => [$a['num'], $a['tema']] <=> [$b['num'], $b['tema']]);
        return $temes;
    }

    /** XP de cada dia dels últims $days dies (inclòs avui): [['dia' => 'AAAA-MM-DD', 'xp' => n], ...] */
    public static function xpByDay(int $days = 30): array
    {
        $rows = [];
        foreach (Db::all("SELECT date(created_at) d, SUM(amount) xp FROM xp_events
                          WHERE date(created_at) > date('now','localtime', ?) GROUP BY d", ['-' . $days . ' days']) as $r) {
            $rows[$r['d']] = (int) $r['xp'];
        }
        $out = [];
        $day = new \DateTimeImmutable('today');
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = $day->modify("-$i days")->format('Y-m-d');
            $out[] = ['dia' => $d, 'xp' => $rows[$d] ?? 0];
        }
        return $out;
    }

    /**
     * Calendari de pràctica (com el de GitHub): les últimes $setmanes setmanes, de dilluns a diumenge,
     * acabant la setmana d'avui. Els dies que encara han de venir no hi surten.
     * @return array<int, array{dia:string, xp:int}> del més antic a avui
     */
    public static function calendari(int $setmanes = 18): array
    {
        $diaSetmana = (int) date('N');   // 1 = dilluns ... 7 = diumenge
        return self::xpByDay(($setmanes - 1) * 7 + $diaSetmana);
    }

    /** D'on ve l'XP: lliçons, laboratoris i reptes. */
    public static function xpBySource(): array
    {
        $out = ['Lliçons' => 0, 'Laboratoris' => 0, 'Reptes' => 0, 'Exàmens' => 0];
        foreach (Db::all('SELECT kind, SUM(amount) xp FROM xp_events GROUP BY kind') as $r) {
            $key = match (true) {
                str_starts_with($r['kind'], 'lesson') => 'Lliçons',
                str_starts_with($r['kind'], 'lab') => 'Laboratoris',
                $r['kind'] === 'examen' => 'Exàmens',
                default => 'Reptes',
            };
            $out[$key] += (int) $r['xp'];
        }
        return $out;
    }

    /** Partides i percentatge d'encert per mode de repte. */
    public static function reptesByMode(): array
    {
        $out = [];
        foreach (Db::all('SELECT mode, items, encerts, score FROM repte_sessions WHERE finished_at IS NOT NULL') as $s) {
            $m = &$out[$s['mode']];
            $m ??= ['partides' => 0, 'encerts' => 0, 'preguntes' => 0, 'millor' => 0];
            $m['partides']++;
            $m['encerts'] += (int) $s['encerts'];
            $m['preguntes'] += count(json_decode($s['items'], true) ?: []);
            $m['millor'] = max($m['millor'], (int) $s['score']);
            unset($m);
        }
        return $out;
    }

    /** Dies diferents amb activitat i la ratxa més llarga de dies seguits. */
    public static function days(): array
    {
        $days = array_column(Db::all('SELECT DISTINCT date(created_at) d FROM xp_events ORDER BY d'), 'd');
        $best = $run = 0;
        $prev = null;
        foreach ($days as $d) {
            $run = ($prev !== null && date('Y-m-d', strtotime("$prev +1 day")) === $d) ? $run + 1 : 1;
            $best = max($best, $run);
            $prev = $d;
        }
        return ['actius' => count($days), 'millor_ratxa' => $best];
    }

    /** Dades per decidir les insígnies (mira Badges::DEFINITIONS). */
    public static function snapshot(): array
    {
        $bank = Reptes::bank();
        $errataOk = $errorHits = 0;
        foreach (Db::all('SELECT * FROM repte_stats WHERE correct > 0') as $r) {
            $item = $bank[$r['item_id']] ?? null;
            if ($item && !empty($item['errata'])) {
                $errataOk++;
            }
            if ($item && $item['tipus'] === 'error') {
                $errorHits += (int) $r['correct'];
            }
        }
        $perfect = $fastPerfect = false;
        foreach (Db::all("SELECT mode, items, encerts, started_at, finished_at FROM repte_sessions WHERE finished_at IS NOT NULL AND mode <> 'repas'") as $s) {
            $n = count(json_decode($s['items'], true) ?: []);
            if ($n > 0 && (int) $s['encerts'] === $n) {
                $perfect = true;
                if ($s['mode'] === 'rapid' && (int) $s['finished_at'] - (int) $s['started_at'] <= 90) {
                    $fastPerfect = true;
                }
            }
        }
        $temes = self::temaMap();
        $progress = Progress::summary();
        return [
            'encerts_leccio' => (int) Db::value("SELECT COUNT(*) FROM xp_events WHERE kind = 'lesson_q'"),
            'lliçons' => count(self::lessonsDone()),
            'lliçons_total' => count(Lessons::all()),
            'labs' => count(array_filter(self::labsState(), fn($s) => $s === 'fet')),
            'labs_total' => count(Labs::all()),
            'labs_nets' => (int) Db::value('SELECT COUNT(*) FROM lab_state s WHERE completed_at IS NOT NULL AND NOT EXISTS
                                            (SELECT 1 FROM lab_steps p WHERE p.lab_id = s.lab_id AND (p.hints_used > 0 OR p.solution_seen > 0))'),
            'errates' => $errataOk,
            'errates_total' => count(array_filter($bank, fn($i) => !empty($i['errata']))),
            'errors_trobats' => $errorHits,
            'partida_perfecta' => $perfect,
            'llamp' => $fastPerfect,
            'partides' => (int) Db::value("SELECT COUNT(*) FROM repte_sessions WHERE finished_at IS NOT NULL AND mode <> 'repas'"),
            'encerts_repas' => (int) Db::value("SELECT COALESCE(SUM(encerts), 0) FROM repte_sessions WHERE mode = 'repas'"),
            'caixa5' => (bool) Db::value('SELECT 1 FROM review_items WHERE box >= 5 LIMIT 1'),
            'ratxa' => max($progress['streak'], self::days()['millor_ratxa']),
            'nivell' => $progress['level'],
            'temes_complets' => array_values(array_map(fn($t) => $t['num'], array_filter($temes, fn($t) => $t['complet']))),
            'temes' => array_values(array_map(fn($t) => ['num' => $t['num'], 'tema' => $t['tema']], array_filter($temes, fn($t) => $t['num'] !== 99))),
            'conceptes_dominats' => count(array_filter(self::concepts(), fn($c) => self::mastery($c)['nivell'] === 'dominat')),
        ];
    }
}
