<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Repàs espaciat amb el sistema Leitner.
 *
 * Cada pregunta fallada entra a la caixa 1 i torna a sortir l'endemà. Cada vegada que l'encertes al repàs,
 * puja de caixa i s'espera més dies; si la falles, torna a la caixa 1. Quan l'encertes a l'última caixa, ja la domines.
 */
final class Review
{
    /** Dies d'espera de cada caixa. */
    public const INTERVALS = [1 => 1, 2 => 3, 3 => 7, 4 => 14, 5 => 30];

    public static function fail(string $ref): void
    {
        Db::run(
            "INSERT INTO review_items (ref, box, due, fails) VALUES (?, 1, date('now','localtime','+1 day'), 1)
             ON CONFLICT(ref) DO UPDATE SET box = 1, due = date('now','localtime','+1 day'), fails = fails + 1,
             updated_at = datetime('now','localtime')",
            [$ref]
        );
    }

    /** Encert al repàs: puja de caixa (o surt del repàs si ja era a l'última). */
    public static function success(string $ref): void
    {
        $row = Db::one('SELECT * FROM review_items WHERE ref = ?', [$ref]);
        if ($row === null) {
            return;
        }
        $box = (int) $row['box'] + 1;
        if ($box > count(self::INTERVALS)) {
            Db::run('DELETE FROM review_items WHERE ref = ?', [$ref]);
            return;
        }
        Db::run(
            "UPDATE review_items SET box = ?, due = date('now','localtime', ?), successes = successes + 1,
             updated_at = datetime('now','localtime') WHERE ref = ?",
            [$box, '+' . self::INTERVALS[$box] . ' days', $ref]
        );
    }

    /** Referències que toca repassar avui (les més endarrerides primer). */
    public static function due(int $limit = 15): array
    {
        return array_column(Db::all(
            "SELECT ref FROM review_items WHERE due <= date('now','localtime') ORDER BY due, box LIMIT ?",
            [$limit]
        ), 'ref');
    }

    public static function countDue(): int
    {
        return (int) Db::value("SELECT COUNT(*) FROM review_items WHERE due <= date('now','localtime')");
    }

    /** Quantes preguntes hi ha a cada caixa (per a les estadístiques). */
    public static function boxes(): array
    {
        $out = array_fill_keys(array_keys(self::INTERVALS), 0);
        foreach (Db::all('SELECT box, COUNT(*) n FROM review_items GROUP BY box') as $r) {
            $out[(int) $r['box']] = (int) $r['n'];
        }
        return $out;
    }

    /** Data del proper repàs (si avui no en toca cap). */
    public static function nextDue(): ?string
    {
        return Db::value('SELECT MIN(due) FROM review_items');
    }
}
