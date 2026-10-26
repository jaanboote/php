<?php
/**
 * Arrencada comuna de totes les peticions (index.php i api.php).
 *
 * 1. Carrega les classes (autoloader propi + el de Composer si hi és).
 * 2. Llegeix config.php.
 * 3. Comprova que la IP del visitant és de la xarxa permesa.
 * 4. Inicia la sessió amb cookies segures.
 */

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

// Autoloader senzill: PhpLab\Sandbox -> src/Sandbox.php
spl_autoload_register(function (string $class): void {
    $prefix = 'PhpLab\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

require __DIR__ . '/helpers.php';

// Les llibreries de Composer són opcionals: sense elles no hi ha PDF ni IA, però la resta funciona.
if (is_file(APP_ROOT . '/vendor/autoload.php')) {
    require APP_ROOT . '/vendor/autoload.php';
}

use PhpLab\Config;
use PhpLab\Auth;

if (!is_file(APP_ROOT . '/config.php')) {
    http_response_code(500);
    exit('Falta config.php. Copia config.example.php com a config.php i revisa-la (mira docs/GUIA.md).');
}
Config::load(require APP_ROOT . '/config.php');

// Errors: es registren al log, però no es mostren mai al navegador.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Defensa en profunditat: encara que el .htaccess falli, només deixem entrar la xarxa de casa.
// Des del mateix PC sempre es pot entrar, encara que sigui per la seva IP de la xarxa (canvia si passes d'Ethernet a Wi-Fi)
$mateixPc = ($_SERVER['REMOTE_ADDR'] ?? '') !== '' && ($_SERVER['REMOTE_ADDR'] ?? '') === ($_SERVER['SERVER_ADDR'] ?? null);
if (PHP_SAPI !== 'cli' && !$mateixPc && !Auth::ipAllowed($_SERVER['REMOTE_ADDR'] ?? '')) {
    http_response_code(403);
    exit('Accés denegat: només es pot accedir des de la xarxa local.');
}

// Capçaleres de seguretat bàsiques.
if (PHP_SAPI !== 'cli') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
    // web.php (el navegador dels laboratoris web) no fa servir la sessió de la app
    if (!defined('PHPLAB_NO_SESSION')) {
        Auth::startSession();
    }
}
