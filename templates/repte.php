<?php
/** @var string $mode @var string $tema @var bool $buit @var ?string $proper */
use PhpLab\Reptes;
$cfg = Reptes::MODES[$mode];
?>
<div class="crumbs"><a href="index.php">Inici</a> › <a href="index.php?p=reptes">Reptes</a> › <?= e($cfg['titol']) ?></div>

<?php if ($buit): ?>
<section class="card estat-buit">
  <?php $ratxa = 0; $humorForcat = 'dorm'; $fraseForcada = 'Res per repassar. Puc dormir una mica més?'; $mascotaClasse = 'mascota-gran';
        require __DIR__ . '/_mascota.php'; ?>
  <h1>Avui no tens res per repassar</h1>
  <p class="muted"><?= $proper ? 'El proper repàs és el ' . e(date('d/m/Y', strtotime($proper))) . '. ' : '' ?>Les preguntes que falles a les lliçons i als reptes tornaran aquí.</p>
  <div class="error-accions">
    <a class="btn primary" href="index.php?p=repte&amp;mode=rapid">⏱ Fes una contrarellotge</a>
    <a class="btn" href="index.php?p=reptes">Tornar als reptes</a>
  </div>
</section>
<?php else: ?>
<div class="repte" id="repte" data-mode="<?= e($mode) ?>" data-tema="<?= e($tema) ?>" data-titol="<?= e($cfg['icona'] . ' ' . $cfg['titol']) ?>">
  <header class="repte-head">
    <h1><?= $cfg['icona'] ?> <?= e($cfg['titol']) ?><?= $tema !== '' ? ' <small class="muted">· ' . e($tema) . '</small>' : '' ?></h1>
    <div class="repte-stats">
      <span id="r-progress" class="pill">–</span>
      <span id="r-encerts" class="pill ok">0 encerts</span>
      <span id="r-combo" class="pill combo" hidden>🔥 Combo ×2</span>
      <?php if ($cfg['temps']): ?><span id="r-timer" class="pill timer">⏱ <?= e($cfg['temps']) ?> s</span><?php endif; ?>
    </div>
  </header>
  <div class="timebar" <?= $cfg['temps'] ? '' : 'hidden' ?>><div class="timebar-fill" id="r-timebar"></div></div>
  <ol class="repte-passos" id="r-passos" aria-hidden="true"></ol>

  <section class="card repte-card" id="r-card">
    <p class="muted">Carregant les preguntes…</p>
  </section>
</div>
<?php endif; ?>
