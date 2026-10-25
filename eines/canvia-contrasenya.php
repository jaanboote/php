<?php
/**
 * Eina de terminal per canviar la contrasenya (per si l'oblides).
 * Ús (des de la carpeta del projecte):
 *     C:\xampp\php\php.exe eines\canvia-contrasenya.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Només es pot executar des del terminal.');
}
require __DIR__ . '/../src/bootstrap.php';

use PhpLab\Db;

$user = Db::one('SELECT * FROM users ORDER BY id LIMIT 1');
if ($user === null) {
    exit("Encara no hi ha cap usuari. Obre http://localhost/phplab per crear-lo.\n");
}
echo "Usuari: {$user['username']}\n";
$pass = readline('Nova contrasenya (mínim 10 caràcters): ');
if ($pass === false || mb_strlen($pass) < 10) {
    exit("ERROR: la contrasenya ha de tenir com a mínim 10 caràcters.\n");
}
Db::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $user['id']]);
Db::run('DELETE FROM login_attempts');
echo "Contrasenya canviada. També s'han esborrat els bloquejos per intents fallits.\n";
