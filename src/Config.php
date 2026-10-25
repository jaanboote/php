<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Accés a la configuració amb notació de punts: Config::get('sandbox.timeout').
 */
final class Config
{
    private static array $data = [];

    public static function load(array $data): void
    {
        self::$data = $data;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    /** Ruta dins de la carpeta de dades (la crea si no existeix). */
    public static function dataPath(string $sub = ''): string
    {
        $base = rtrim((string) self::get('data_dir'), '/\\');
        $path = $sub === '' ? $base : $base . DIRECTORY_SEPARATOR . $sub;
        if (!is_dir($path)) {
            mkdir($path, 0770, true);
        }
        return $path;
    }
}
