<?php
/** @var string $content  @var array $progress  @var string $title  @var string $page */
use PhpLab\Auth;
$pct = $progress['needed'] > 0 ? (int) round($progress['into'] * 100 / $progress['needed']) : 0;
?>
<!doctype html>
<html lang="ca" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf" content="<?= e(Auth::csrfToken()) ?>">
  <title><?= e($title) ?> · PHPLab</title>
  <link rel="icon" href="assets/img/icon.svg" type="image/svg+xml">
  <!-- CodeMirror 5.65.16 (editor de codi), copiat a assets/vendor perquè funcioni sense Internet -->
  <link rel="stylesheet" href="assets/vendor/codemirror/codemirror.css">
  <link rel="stylesheet" href="assets/vendor/codemirror/addon/hint/show-hint.css">
  <link rel="preload" href="assets/vendor/fonts/bricolage-grotesque-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="assets/vendor/fonts/lexend-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="assets/css/app.css?v=6">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php"><span class="brand-mark">&lt;?</span>PHPLab</a>
  <nav class="nav">
    <a href="index.php" class="<?= $page === 'inici' ? 'active' : '' ?>">Inici</a>
    <a href="index.php?p=reptes" class="<?= in_array($page, ['reptes', 'repte'], true) ? 'active' : '' ?>">Reptes</a>
    <a href="index.php?p=progres" class="<?= $page === 'progres' ? 'active' : '' ?>">Progrés</a>
    <a href="index.php?p=temari" class="<?= $page === 'temari' ? 'active' : '' ?>">Temari</a>
    <a href="index.php?p=diagnostic" class="<?= $page === 'diagnostic' ? 'active' : '' ?>">Diagnòstic</a>
  </nav>
  <div class="player" id="player" title="<?= e($progress['xp']) ?> XP en total">
    <span class="level-badge">Nv <span id="p-level"><?= e($progress['level']) ?></span></span>
    <div class="xpbar"><div class="xpbar-fill" id="p-bar" style="width: <?= $pct ?>%"></div></div>
    <span class="xp-text"><span id="p-xp"><?= e($progress['xp']) ?></span> XP</span>
    <span class="streak" title="Dies seguits practicant">🔥 <span id="p-streak"><?= e($progress['streak']) ?></span></span>
  </div>
  <form method="post" action="index.php?p=sortir" class="logout">
    <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
    <button type="submit" class="btn-link" title="Tancar la sessió">Sortir</button>
  </form>
</header>
<main class="main">
<?= $content ?>
</main>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<a id="badge-pop" class="badge-pop" href="index.php?p=progres#insignies" role="status" aria-live="polite"></a>
<script src="assets/vendor/codemirror/codemirror.js"></script>
<script src="assets/vendor/codemirror/mode/xml/xml.js"></script>
<script src="assets/vendor/codemirror/mode/javascript/javascript.js"></script>
<script src="assets/vendor/codemirror/mode/css/css.js"></script>
<script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="assets/vendor/codemirror/mode/clike/clike.js"></script>
<script src="assets/vendor/codemirror/mode/php/php.js"></script>
<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js"></script>
<script src="assets/vendor/codemirror/addon/edit/closebrackets.js"></script>
<script src="assets/vendor/codemirror/addon/hint/show-hint.js"></script>
<script src="assets/js/app.js?v=5"></script>
<?php if ($page === 'leccio'): ?><script src="assets/js/lesson.js?v=5"></script><?php endif; ?>
<?php if ($page === 'lab'): ?><script src="assets/js/lab.js?v=5"></script><?php endif; ?>
<?php if ($page === 'repte'): ?><script src="assets/js/repte.js?v=4"></script><?php endif; ?>
</body>
</html>
