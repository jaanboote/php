<?php
/** @var array $files @var ?array $flash */
use PhpLab\{Auth, TextExtractor};
$maxUpload = ini_get('upload_max_filesize');
$maxPost = ini_get('post_max_size');
?>
<h1>Temari</h1>
<p class="muted">El material de classe es guarda fora del webroot (no es pot obrir des del navegador) i s'indexa per temes: el pots llegir des d'aquí i el tutor el fa servir per respondre't.</p>

<?php if ($flash && $flash['type'] === 'error'): ?>
  <p class="alert error"><?= e($flash['msg']) ?></p>
<?php elseif ($flash): $up = $flash['msg']['pujada'] ?? null; $ix = $flash['msg']['index']; ?>
  <div class="alert ok">
    <?php if ($up): ?>
      <p><strong>Pujada:</strong> <?= count($up['guardats']) ?> guardat(s), <?= count($up['duplicats']) ?> duplicat(s) ignorat(s), <?= count($up['rebutjats']) ?> rebutjat(s).</p>
      <?php if ($up['rebutjats']): ?><ul class="small"><?php foreach ($up['rebutjats'] as $r): ?><li><?= e($r['nom']) ?>: <?= e($r['motiu']) ?></li><?php endforeach; ?></ul><?php endif; ?>
      <?php if ($up['duplicats']): ?><p class="small muted">Ja existien (mateix contingut): <?= e(implode(', ', $up['duplicats'])) ?></p><?php endif; ?>
    <?php endif; ?>
    <p><strong>Índex:</strong> <?= count($ix['nous']) ?> nou(s), <?= count($ix['modificats']) ?> modificat(s), <?= count($ix['esborrats']) ?> esborrat(s), <?= e($ix['sense_canvis']) ?> sense canvis.</p>
    <?php if ($ix['temes_nous']): ?><p>🆕 <strong>Temes nous:</strong> <?= e(implode(' · ', $ix['temes_nous'])) ?></p><?php endif; ?>
    <?php if ($ix['errors']): ?><ul class="small"><?php foreach ($ix['errors'] as $er): ?><li>⚠ <?= e($er['path']) ?>: <?= e($er['message']) ?></li><?php endforeach; ?></ul><?php endif; ?>
  </div>
<?php endif; ?>

<div class="grid-2">
  <section class="card">
    <h2>Pujar temari</h2>
    <form method="post" enctype="multipart/form-data" class="form" id="upload-form">
      <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
      <input type="hidden" name="accio" value="pujar">
      <label class="drop">
        <input type="file" name="fitxers[]" multiple required accept=".<?= e(implode(',.', array_merge(TextExtractor::EXTENSIONS, ['zip']))) ?>">
        <svg class="drop-icona" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18a5 5 0 0 1-.5-9.97A6 6 0 0 1 18 9a4 4 0 0 1 0 8"/><path d="M12 12v8M8.5 15.5 12 12l3.5 3.5"/></svg>
        <span>Tria fitxers o arrossega'ls aquí<br><small class="muted">PDF, ZIP, DOCX, PHP, HTML, TXT, MD · màxim <?= e($maxUpload) ?> per fitxer i <?= e($maxPost) ?> en total</small></span>
      </label>
      <button class="btn primary" type="submit">⬆ Pujar temari</button>
    </form>
    <p class="small muted">Els ZIP s'extreuen (amb protecció contra «zip slip»). Si un fitxer ja existeix amb el mateix contingut, no es duplica.</p>
  </section>

  <section class="card">
    <h2>Actualitzar l'índex</h2>
    <p class="muted">Torna a llegir la carpeta del temari: només processa el que és nou o ha canviat (es compara el hash SHA-256 de cada fitxer).</p>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
      <input type="hidden" name="accio" value="reindexar">
      <button class="btn" type="submit">🔄 Actualitzar temari</button>
    </form>
    <p class="small muted">Carpeta: <code><?= e(PhpLab\Indexer::temariDir()) ?></code></p>
  </section>
</div>

<section class="card">
  <h2>Fitxers indexats (<?= count($files) ?>)</h2>
  <?php if (!$files): ?>
    <div class="estat-buit petit">
      <?php $ratxa = 0; $humorForcat = 'dorm'; $fraseForcada = 'Encara no tinc res per llegir…'; $mascotaClasse = 'mascota-petita';
            require __DIR__ . '/_mascota.php'; ?>
      <p class="muted">Encara no hi ha res. Puja els PDF o el ZIP de classe amb el formulari de dalt.</p>
    </div>
  <?php else: ?>
    <?php
    // Agrupats per tema, en el mateix ordre que la consulta (per número de tema)
    $grups = [];
    foreach ($files as $f) {
        $grups[$f['topic_name']][] = $f;
    }
    ?>
    <?php foreach ($grups as $tema => $llista): ?>
      <div class="temari-grup <?= e(tema_class($tema)) ?>">
        <h3 class="group-title"><?= tema_icona($tema, 18) ?><?= e($tema) ?>
          <a class="btn small" href="index.php?p=llegir&amp;tema=<?= e(urlencode($tema)) ?>">📖 Llegir</a></h3>
        <div class="fitxers-grid">
          <?php foreach ($llista as $f): $dir = dirname(str_replace('\\', '/', $f['path'])); ?>
            <article class="fitxer-card <?= $f['status'] === 'ok' ? '' : 'amb-error' ?>">
              <?= fitxer_icona((string) $f['ext']) ?>
              <div class="fitxer-info">
                <strong title="<?= e($f['path']) ?>"><?= e(basename(str_replace('\\', '/', $f['path']))) ?></strong>
                <?php if ($dir !== '.'): ?><span class="fitxer-carpeta">📁 <?= e($dir) ?></span><?php endif; ?>
                <span class="fitxer-meta"><?= e($f['kind']) ?> · <?= number_format((int) $f['chars'], 0, ',', '.') ?> caràcters</span>
                <?php if ($f['status'] !== 'ok'): ?>
                  <span class="pill err" title="<?= e($f['message']) ?>">⚠ <?= e($f['status']) ?></span>
                  <span class="small muted"><?= e($f['message']) ?></span>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>
