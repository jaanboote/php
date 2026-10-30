<?php
/** @var string $mode @var string $tema */
use PhpLab\Reptes;
$cfg = Reptes::MODES[$mode];
?>
<div class="crumbs"><a href="index.php">Inici</a> › <a href="index.php?p=reptes">Reptes</a> › <?= e($cfg['titol']) ?></div>

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

  <section class="card repte-card" id="r-card">
    <p class="muted">Carregant les preguntes…</p>
  </section>
</div>
