<?php
/** @var array $files @var ?array $flash */
use PhpLab\{Auth, TextExtractor};
$maxUpload = ini_get('upload_max_filesize');
$maxPost = ini_get('post_max_size');
?>
<h1>Temari</h1>
<p class="muted">El material de classe es guarda fora del webroot (no es pot obrir des del navegador) i s'indexa per fer-lo servir al tutor i, més endavant, per generar contingut nou.</p>

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
    <p class="muted">Encara no hi ha res.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="files">
        <thead><tr><th>Tema</th><th>Fitxer</th><th>Tipus</th><th>Text</th><th>Estat</th></tr></thead>
        <tbody>
          <?php foreach ($files as $f): ?>
            <tr>
              <td><a href="index.php?p=llegir&amp;tema=<?= e(urlencode($f['topic_name'])) ?>"><?= e($f['topic_name']) ?></a></td>
              <td class="path"><?= e($f['path']) ?></td>
              <td><?= e($f['kind']) ?></td>
              <td class="small"><?= number_format((int) $f['chars'], 0, ',', '.') ?> car.</td>
              <td><?php if ($f['status'] === 'ok'): ?><span class="pill ok">ok</span><?php else: ?>
                <span class="pill err" title="<?= e($f['message']) ?>"><?= e($f['status']) ?></span>
                <div class="small muted"><?= e($f['message']) ?></div><?php endif; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
