<?php
/**
 * Comprova que l'entorn està ben configurat (extensions, rutes, permisos...).
 */
use PhpLab\{Config, Sandbox, Tutor};

$phpCli = (string) Config::get('sandbox.php_cli');
$test = Sandbox::run('<?php echo PHP_VERSION;');
$cgi = Sandbox::phpCgi();
$webTest = is_file($cgi) ? Sandbox::runWeb(['index.php' => '<?php echo "ok-" . PHP_SAPI;'], ['path' => 'index.php'], Config::dataPath('sandbox') . DIRECTORY_SEPARATOR . 'diagnostic-web') : null;
$checks = [
    ['PHP ' . PHP_VERSION . ' (Apache)', version_compare(PHP_VERSION, '8.1', '>='), 'Cal PHP 8.1 o superior.'],
    ['Extensió pdo_sqlite', extension_loaded('pdo_sqlite'), 'Activa extension=pdo_sqlite al php.ini.'],
    ['Extensió mbstring', extension_loaded('mbstring'), 'Activa extension=mbstring al php.ini.'],
    ['Extensió zip (ZIP i DOCX)', class_exists(ZipArchive::class), 'Treu el ; de ;extension=zip a C:\xampp\php\php.ini i reinicia Apache.'],
    ['Extensió curl (tutor IA)', extension_loaded('curl'), 'Activa extension=curl al php.ini.'],
    ['Dependències de Composer (PDF i IA)', is_file(APP_ROOT . '/vendor/autoload.php'), 'Executa composer install a la carpeta del projecte.'],
    ['PHP CLI per al sandbox', $test['stdout'] !== '', "No s'ha pogut executar $phpCli. " . $test['stderr']],
    ['php-cgi per als laboratoris web', $webTest !== null && str_starts_with($webTest['body'], 'ok-cgi'),
     $webTest === null ? "No s'ha trobat $cgi. Indica la ruta a 'sandbox.php_cgi' de config.php (a XAMPP: C:\\xampp\\php\\php-cgi.exe)." : 'php-cgi no ha respost bé: ' . mb_substr(trim($webTest['body'] . ' ' . $webTest['errors']), 0, 200)],
    ['Carpeta de dades fora del webroot', !str_starts_with(str_replace('\\', '/', realpath(Config::dataPath())), str_replace('\\', '/', realpath(APP_ROOT))), 'data_dir no pot ser dins de la carpeta de l\'aplicació.'],
    ['Carpeta de dades amb permís d\'escriptura', is_writable(Config::dataPath()), 'Apache ha de poder escriure a ' . Config::dataPath()],
    ['Clau de l\'API de Claude', Tutor::aiAvailable(), 'Opcional: sense clau, el tutor funciona en mode sense connexió.', true],
];
?>
<h1>Diagnòstic</h1>
<section class="card">
  <ul class="checks">
    <?php foreach ($checks as $c): [$label, $ok, $help] = $c; $optional = $c[3] ?? false; ?>
      <li class="<?= $ok ? 'ok' : ($optional ? 'opt' : 'ko') ?>"><span class="check-icon"><?= $ok ? '✔' : ($optional ? '○' : '✖') ?></span>
        <span><?= e($label) ?><?php if (!$ok): ?><br><small class="muted"><?= e($help) ?></small><?php endif; ?></span></li>
    <?php endforeach; ?>
  </ul>
  <p class="small muted">PHP del sandbox: <?= e($test['stdout'] ?: '—') ?> · <code><?= e($phpCli) ?></code> · temps límit <?= e(Config::get('sandbox.timeout')) ?> s · memòria <?= e(Config::get('sandbox.memory_limit')) ?></p>
  <p class="small muted">Accés permès des de: <?= e(implode(', ', (array) Config::get('allowed_networks'))) ?> · La teva IP: <?= e($_SERVER['REMOTE_ADDR'] ?? '') ?></p>
</section>
