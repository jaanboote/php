<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Plantilles PHP molt senzilles: templates/nom.php rep les variables i es posa dins de layout.php.
 */
final class View
{
    public static function render(string $template, array $vars = [], bool $withLayout = true): void
    {
        $content = self::capture($template, $vars);
        if (!$withLayout) {
            echo $content;
            return;
        }
        echo self::capture('layout', $vars + ['content' => $content]);
    }

    private static function capture(string $template, array $vars): string
    {
        extract($vars, EXTR_SKIP);
        ob_start();
        require APP_ROOT . "/templates/$template.php";
        return (string) ob_get_clean();
    }

    /** Missatges d'un sol ús entre redireccions (p. ex. "Temari actualitzat"). */
    public static function flash(?string $type = null, mixed $msg = null): ?array
    {
        if ($type !== null) {
            $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
            return null;
        }
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $f;
    }
}
