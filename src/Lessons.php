<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Lliçons: blocs de text, exemples executables ("prova-ho") i micro-preguntes.
 * Les definicions són a content/lessons/*.php.
 */
final class Lessons
{
    public static function all(): array
    {
        $list = [];
        foreach (glob(APP_ROOT . '/content/lessons/*.php') as $file) {
            $l = require $file;
            $list[$l['id']] = $l;
        }
        uasort($list, fn($a, $b) => ($a['ordre'] ?? 99) <=> ($b['ordre'] ?? 99));
        return $list;
    }

    public static function get(string $id): ?array
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $id)) {
            return null;
        }
        $file = APP_ROOT . "/content/lessons/$id.php";
        return is_file($file) ? require $file : null;
    }

    public static function questions(array $lesson): array
    {
        $q = [];
        foreach ($lesson['blocs'] as $b) {
            if (in_array($b['tipus'], ['test', 'completa', 'sortida', 'ordenar'], true)) {
                $q[$b['id']] = $b;
            }
        }
        return $q;
    }

    /** Respostes de l'alumne guardades: [question_id => row]. */
    public static function answers(string $lessonId): array
    {
        $out = [];
        foreach (Db::all('SELECT * FROM lesson_answers WHERE lesson_id = ?', [$lessonId]) as $r) {
            $out[$r['question_id']] = $r;
        }
        return $out;
    }

    /**
     * Corregeix una resposta al servidor (així les respostes correctes no són al codi de la pàgina).
     * XP: 5 si l'encertes al primer intent, 2 si l'encertes després.
     */
    public static function answer(array $lesson, string $qid, string $answer): array
    {
        $q = self::questions($lesson)[$qid] ?? throw new \InvalidArgumentException('Pregunta inexistent');
        $correct = self::isCorrect($q, $answer);

        Db::run('INSERT OR IGNORE INTO lesson_answers (lesson_id, question_id) VALUES (?, ?)', [$lesson['id'], $qid]);
        $row = Db::one('SELECT * FROM lesson_answers WHERE lesson_id = ? AND question_id = ?', [$lesson['id'], $qid]);
        $firstTry = (int) $row['attempts'] === 0;
        Db::run(
            "UPDATE lesson_answers SET attempts = attempts + 1, correct = MAX(correct, ?),
             last_wrong_at = CASE WHEN ? THEN last_wrong_at ELSE datetime('now','localtime') END
             WHERE lesson_id = ? AND question_id = ?",
            [$correct ? 1 : 0, $correct ? 1 : 0, $lesson['id'], $qid]
        );

        $xp = 0;
        if (!$correct) {
            Review::fail("lesson:{$lesson['id']}#$qid");   // tornarà a sortir al repàs
        }
        if ($correct) {
            $xp = Progress::award('lesson_q', "{$lesson['id']}#$qid", $firstTry ? 5 : 2);
            foreach ($q['conceptes'] ?? [] as $c) {
                Progress::concept($c, 'successes');
            }
        } else {
            foreach ($q['conceptes'] ?? [] as $c) {
                Progress::concept($c, 'fails');
            }
        }

        $done = self::checkCompleted($lesson);
        if ($done['nova']) {
            $xp += $done['xp'];
        }
        return [
            'correcte' => $correct,
            'explicacio' => $q['explicacio'] ?? '',
            'xp' => $xp,
            'leccio_acabada' => $done['acabada'],
            'leccio_nova' => $done['nova'],   // s'acaba d'acabar ara mateix: el navegador ho celebra
            // XP total de la lliçó (preguntes + premi final), per mostrar-lo a la celebració
            'xp_leccio' => $done['nova'] ? (int) Db::value(
                "SELECT COALESCE(SUM(amount), 0) FROM xp_events WHERE (kind = 'lesson_q' AND ref LIKE ?) OR (kind = 'lesson_done' AND ref = ?)",
                [$lesson['id'] . '#%', $lesson['id']]
            ) : 0,
        ];
    }

    /** Corregeix una resposta (també el fa servir el repàs espaciat). */
    public static function isCorrect(array $q, string $answer): bool
    {
        return match ($q['tipus']) {
            'test'     => (int) $answer === (int) $q['correcta'],
            'completa' => in_array(self::squash($answer), array_map([self::class, 'squash'], $q['respostes']), true),
            'sortida'  => Labs::normalize($answer) === Labs::normalize($q['resposta']),
            // ordenar: el navegador envia els índexs originals en l'ordre triat ("2,0,1"); correcte = 0, 1, 2...
            'ordenar'  => array_map('intval', explode(',', $answer)) === range(0, count($q['elements']) - 1),
            default    => false,
        };
    }

    /** La lliçó està acabada quan totes les preguntes s'han encertat. */
    private static function checkCompleted(array $lesson): array
    {
        $total = count(self::questions($lesson));
        $ok = (int) Db::value('SELECT COUNT(*) FROM lesson_answers WHERE lesson_id = ? AND correct = 1', [$lesson['id']]);
        if ($ok < $total) {
            return ['acabada' => false, 'nova' => false, 'xp' => 0];
        }
        $already = Db::value('SELECT completed_at FROM lesson_state WHERE lesson_id = ?', [$lesson['id']]);
        if ($already) {
            return ['acabada' => true, 'nova' => false, 'xp' => 0];
        }
        Db::run("INSERT OR REPLACE INTO lesson_state (lesson_id, completed_at) VALUES (?, datetime('now','localtime'))", [$lesson['id']]);
        return ['acabada' => true, 'nova' => true, 'xp' => Progress::award('lesson_done', $lesson['id'], (int) ($lesson['xp_final'] ?? 20))];
    }

    public static function progress(array $lesson): array
    {
        $total = count(self::questions($lesson));
        $ok = (int) Db::value('SELECT COUNT(*) FROM lesson_answers WHERE lesson_id = ? AND correct = 1', [$lesson['id']]);
        return ['total' => $total, 'encertades' => $ok, 'acabada' => (bool) Db::value('SELECT completed_at FROM lesson_state WHERE lesson_id = ?', [$lesson['id']])];
    }

    /**
     * Ordre en què es mostren els elements d'una pregunta «ordenar»: barrejat, però sempre igual
     * per a la mateixa pregunta (i mai en l'ordre correcte).
     */
    public static function shuffled(array $q): array
    {
        $idx = array_keys($q['elements']);
        usort($idx, fn($a, $b) => strcmp(md5($q['id'] . $a), md5($q['id'] . $b)));
        if ($idx === array_keys($q['elements'])) {
            $idx = array_reverse($idx);
        }
        return $idx;
    }

    /** Per comparar codi curt sense tenir en compte els espais ("!= " == "!="). Les majúscules sí que compten (N no és n). */
    private static function squash(string $s): string
    {
        return preg_replace('/\s+/', '', $s);
    }
}
