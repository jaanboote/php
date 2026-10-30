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
<?php
$errors = count(array_filter($checks, fn($c) => !$c[1] && empty($c[3])));
?>
<h1>Diagnòstic</h1>
<section class="card panell <?= $errors ? 'amb-errors' : 'tot-ok' ?>">
  <div class="panell-estat">
    <span class="led gran <?= $errors ? 'ko' : 'ok' ?>" aria-hidden="true"></span>
    <div>
      <h2><?= $errors ? e($errors . ($errors > 1 ? ' coses per arreglar' : ' cosa per arreglar')) : 'Tot en ordre' ?></h2>
      <p class="muted"><?= $errors ? 'Mira els llums vermells: a sota de cada un hi ha com arreglar-ho.' : 'PHPLab té tot el que necessita per funcionar.' ?></p>
    </div>
  </div>
  <div class="panell-specs">
    <span class="meta-chip">🐘 Sandbox: PHP <?= e($test['stdout'] ?: '—') ?></span>
    <span class="meta-chip">⏱ <?= e(Config::get('sandbox.timeout')) ?> s per execució</span>
    <span class="meta-chip">🧠 <?= e(Config::get('sandbox.memory_limit')) ?> de memòria</span>
    <span class="meta-chip">🌐 La teva IP: <?= e($_SERVER['REMOTE_ADDR'] ?? '') ?></span>
  </div>
</section>

<div class="checks-grid">
  <?php foreach ($checks as $c): [$label, $ok, $help] = $c; $optional = $c[3] ?? false; $estat = $ok ? 'ok' : ($optional ? 'opt' : 'ko'); ?>
    <div class="check-tile <?= $estat ?>">
      <span class="led <?= $estat ?>" aria-hidden="true"></span>
      <div>
        <strong><?= e($label) ?></strong>
        <span class="small muted"><?= $ok ? 'Correcte' : e($help) ?></span>
      </div>
      <span class="sr-only"><?= $ok ? 'correcte' : ($optional ? 'opcional, no configurat' : 'error') ?></span>
    </div>
  <?php endforeach; ?>
</div>
<p class="small muted">PHP del sandbox: <code><?= e($phpCli) ?></code> · Accés permès des de: <?= e(implode(', ', (array) Config::get('allowed_networks'))) ?></p>
