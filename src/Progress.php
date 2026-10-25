<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * XP, nivells i ratxa diària.
 * Cada XP es guarda com un "esdeveniment" amb una clau única (kind + ref):
 * així no es pot guanyar dues vegades el mateix XP.
 */
final class Progress
{
    /** Afegeix XP si encara no s'havia donat per aquest element. Retorna l'XP guanyat (0 si ja estava). */
    public static function award(string $kind, string $ref, int $amount): int
    {
        if ($amount <= 0) {
            return 0;
        }
        $stmt = Db::run('INSERT OR IGNORE INTO xp_events (kind, ref, amount) VALUES (?, ?, ?)', [$kind, $ref, $amount]);
        return $stmt->rowCount() > 0 ? $amount : 0;
    }

    public static function totalXp(): int
    {
        return (int) Db::value('SELECT COALESCE(SUM(amount), 0) FROM xp_events');
    }

    /**
     * Nivell: cada nivell costa una mica més que l'anterior.
     * Nivell 1: 0 XP, nivell 2: 100, nivell 3: 250, nivell 4: 450... (+50 cada vegada)
     */
    public static function level(int $xp): array
    {
        $level = 1;
        $needed = 100;
        $floor = 0;
        while ($xp >= $floor + $needed) {
            $floor += $needed;
            $needed += 50;
            $level++;
        }
        return [
            'level' => $level,
            'xp' => $xp,
            'into' => $xp - $floor,   // XP dins del nivell actual
            'needed' => $needed,      // XP que calen per passar de nivell
            'name' => self::levelName($level),
        ];
    }

    private static function levelName(int $level): string
    {
        $names = [1 => 'Hola Món', 2 => 'Aprenent de variables', 3 => 'Domador de bucles', 4 => 'Artesà de funcions',
                  5 => 'Mestre dels arrays', 6 => 'Guardià del servidor', 7 => 'Arquitecte web', 8 => 'Sysadmin PHP'];
        return $names[min($level, 8)];
    }

    /** Dies seguits (fins avui o ahir) amb alguna activitat que ha donat XP. */
    public static function streak(): int
    {
        $days = array_column(Db::all("SELECT DISTINCT date(created_at) d FROM xp_events ORDER BY d DESC LIMIT 400"), 'd');
        if ($days === []) {
            return 0;
        }
        $day = new \DateTimeImmutable('today');
        if ($days[0] !== $day->format('Y-m-d')) {
            $day = $day->modify('-1 day'); // si avui encara no has fet res, la ratxa continua des d'ahir
        }
        $streak = 0;
        foreach ($days as $d) {
            if ($d !== $day->format('Y-m-d')) {
                break;
            }
            $streak++;
            $day = $day->modify('-1 day');
        }
        return $streak;
    }

    public static function summary(): array
    {
        return self::level(self::totalXp()) + ['streak' => self::streak()];
    }

    public static function concept(string $concept, string $field): void
    {
        if (!in_array($field, ['fails', 'hints', 'successes'], true)) {
            return;
        }
        Db::run("INSERT INTO concept_stats (concept, $field) VALUES (?, 1)
                 ON CONFLICT(concept) DO UPDATE SET $field = $field + 1", [$concept]);
    }
}
