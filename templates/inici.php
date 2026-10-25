<?php
/** @var array $topics @var array $lessons @var array $labs @var array $concepts @var array $progress */
?>
<section class="hero">
  <div>
    <h1>Hola, <?= e(PhpLab\Auth::user()) ?> 👋</h1>
    <p class="muted">Nivell <?= e($progress['level']) ?> · <strong><?= e($progress['name']) ?></strong>
      · et falten <?= e($progress['needed'] - $progress['into']) ?> XP per al nivell següent.</p>
  </div>
  <?php if ($progress['streak'] > 0): ?>
    <div class="streak-big">🔥 <?= e($progress['streak']) ?> <small>dia<?= $progress['streak'] > 1 ? 'es' : '' ?> seguit<?= $progress['streak'] > 1 ? 's' : '' ?></small></div>
  <?php endif; ?>
</section>

<div class="grid-2">
  <section class="card">
    <h2><span class="tag">Aprendre</span> Lliçons</h2>
    <?php foreach ($lessons as $l): $p = $l['progres']; ?>
      <a class="item" href="index.php?p=leccio&amp;id=<?= e($l['id']) ?>">
        <div>
          <div class="item-title"><?= e($l['titol']) ?></div>
          <div class="muted small"><?= e($l['tema']) ?> · <?= e($l['minuts']) ?> min</div>
        </div>
        <span class="pill <?= $p['acabada'] ? 'ok' : ($p['encertades'] ? 'doing' : '') ?>">
          <?= $p['acabada'] ? '✔ Feta' : e($p['encertades'] . '/' . $p['total']) ?>
        </span>
      </a>
    <?php endforeach; ?>
  </section>

  <section class="card">
    <h2><span class="tag tag-lab">Laboratori</span> Exercicis guiats</h2>
    <?php foreach ($labs as $lab): $st = $lab['estat']; $n = count($lab['passos']); $done = $st['completed_at'] ? $n : (int) $st['current_step']; ?>
      <a class="item" href="index.php?p=lab&amp;id=<?= e($lab['id']) ?>">
        <div>
          <div class="item-title"><?= e($lab['titol']) ?></div>
          <div class="muted small"><?= e($lab['resum']) ?></div>
          <div class="steps-mini" aria-label="<?= e("$done de $n passos") ?>">
            <?php for ($i = 0; $i < $n; $i++): ?><span class="<?= $i < $done ? 'on' : '' ?>"></span><?php endfor; ?>
          </div>
        </div>
        <span class="pill <?= $st['completed_at'] ? 'ok' : ($done ? 'doing' : '') ?>"><?= $st['completed_at'] ? '✔ Acabat' : e("$done/$n") ?></span>
      </a>
    <?php endforeach; ?>
  </section>
</div>

<div class="grid-2">
  <section class="card">
    <h2>El teu temari</h2>
    <?php if (!$topics): ?>
      <p class="muted">Encara no has pujat cap fitxer. <a href="index.php?p=temari">Puja el temari</a> (PDF, ZIP, DOCX...) i la app el farà servir per al tutor i, a la fase 2, per generar lliçons noves.</p>
    <?php else: ?>
      <ul class="topics">
        <?php foreach ($topics as $t): ?>
          <li><span class="topic-num"><?= $t['topic_num'] !== null ? e($t['topic_num']) : '·' ?></span>
            <span class="topic-name"><?= e($t['topic_name']) ?></span>
            <span class="muted small"><?= e($t['n']) ?> fitxer<?= $t['n'] > 1 ? 's' : '' ?></span></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn small" href="index.php?p=temari">Gestionar temari</a>
    <?php endif; ?>
  </section>

  <section class="card">
    <h2>Conceptes per reforçar</h2>
    <?php $weak = array_filter($concepts, fn($c) => $c['fails'] + $c['hints'] > 0); ?>
    <?php if (!$weak): ?>
      <p class="muted">Quan facis lliçons i laboratoris, aquí sortiran els conceptes que et costen més.</p>
    <?php else: ?>
      <ul class="concepts">
        <?php foreach ($weak as $c): ?>
          <li><span><?= e($c['concept']) ?></span>
            <span class="muted small"><?= e($c['fails']) ?> errors · <?= e($c['hints']) ?> pistes · <?= e($c['successes']) ?> encerts</span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>

<section class="card soon">
  <h2>Properament</h2>
  <p class="muted">Fase 2: lliçons i laboratoris generats a partir del teu temari · Fase 3: reptes contrarellotge, «Troba l'error» i repàs espaciat · Fase 4: mapa de temes, insígnies i estadístiques · Fase 5: laboratoris web amb formularis.</p>
</section>
