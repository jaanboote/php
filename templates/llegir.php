<?php
/** @var string $tema @var array $files */
?>
<div class="crumbs"><a href="index.php">Inici</a> › <a href="index.php?p=temari">Temari</a> › Llegir</div>
<h1><?= e($tema) ?></h1>
<p class="muted">El text dels fitxers d'aquest tema, tal com l'ha extret l'indexador. Als PDF es perd la indentació del codi.</p>

<?php if (!$files): ?>
  <section class="card"><p class="muted">No hi ha cap fitxer llegible d'aquest tema. Puja'l des de la pàgina <a href="index.php?p=temari">Temari</a>.</p></section>
<?php endif; ?>

<?php foreach ($files as $i => $f): ?>
  <section class="card">
    <details class="reading-file" <?= $i === 0 ? 'open' : '' ?>>
      <summary><?= icona('document') ?> <?= e(basename($f['path'])) ?> <span class="pill"><?= e($f['kind']) ?></span></summary>
      <?php // Les marques [[Pàgina N]] que posa l'extractor de PDF es mostren com a separadors ?>
      <div class="reading"><?= preg_replace('/\[\[Pàgina (\d+)\]\]/u', '<span class="page-mark">— Pàgina $1 —</span>', e($f['text'])) ?></div>
    </details>
  </section>
<?php endforeach; ?>
