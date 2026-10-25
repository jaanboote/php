<?php
/** @var array $topics @var array $lessons @var array $labs @var array $concepts @var array $progress */
?>
<?php
// Salutació segons l'hora, com al laboratori «Pàgina dinàmica» del Tema 1
$hora = (int) date('G');
$salutacio = $hora >= 6 && $hora < 14 ? 'Bon dia' : ($hora >= 14 && $hora < 21 ? 'Bona tarda' : 'Bona nit');
$usuari = (string) PhpLab\Auth::user();

// On continuar: la primera lliçó o laboratori començat i no acabat; si no n'hi ha, la primera lliçó pendent
$continua = null;
foreach ($lessons as $l) {
    if (!$l['progres']['acabada'] && $l['progres']['encertades'] > 0) { $continua = ['Lliçó', $l['titol'], "index.php?p=leccio&id={$l['id']}"]; break; }
}
foreach ($labs as $lab) {
    if ($continua === null && !$lab['estat']['completed_at'] && (int) $lab['estat']['current_step'] > 0) { $continua = ['Laboratori', $lab['titol'], "index.php?p=lab&id={$lab['id']}"]; }
}
foreach ($lessons as $l) {
    if ($continua === null && !$l['progres']['acabada']) { $continua = ['Lliçó', $l['titol'], "index.php?p=leccio&id={$l['id']}"]; }
}
$pctNivell = $progress['needed'] > 0 ? $progress['into'] / $progress['needed'] : 0;
$circ = 2 * M_PI * 64;   // perímetre de l'anell (radi 64)
?>
<section class="hero">
  <div class="term-window">
    <div class="term-bar" aria-hidden="true"><i></i><i></i><i></i><span>hola.php — PHPLab</span></div>
    <div class="term-body" id="term">
      <p class="prompt" data-type><b><?= e($usuari) ?>@phplab</b>:~$ php hola.php <?= e($usuari) ?></p>
      <p class="hello" data-type><?= e("$salutacio, $usuari!") ?></p>
      <p class="out" data-type>Nivell <?= e($progress['level']) ?>: <?= e($progress['name']) ?>. Et falten <b><?= e($progress['needed'] - $progress['into']) ?> XP</b> per al nivell <?= e($progress['level'] + 1) ?>.</p>
      <?php if ($pendents > 0): ?>
        <p class="out" data-type>Tens <b><?= e($pendents) ?> pregunta<?= $pendents > 1 ? 'es' : '' ?></b> per repassar avui.</p>
      <?php endif; ?>
      <p class="prompt caret" data-type><b><?= e($usuari) ?>@phplab</b>:~$ </p>
      <div class="term-actions">
        <?php if ($continua): ?>
          <a class="btn primary" href="<?= e($continua[2]) ?>">Continua: <?= e($continua[1]) ?></a>
        <?php endif; ?>
        <?php if ($pendents > 0): ?><a class="btn" href="index.php?p=repte&amp;mode=repas">🔁 Repassar</a><?php endif; ?>
        <a class="btn" href="index.php?p=reptes">🎯 Reptes</a>
      </div>
    </div>
  </div>

  <div class="card level-card">
    <div class="ring" role="img" aria-label="<?= e("Nivell {$progress['level']}: {$progress['into']} de {$progress['needed']} XP") ?>">
      <svg viewBox="0 0 150 150" aria-hidden="true">
        <circle class="ring-bg" cx="75" cy="75" r="64"/>
        <circle class="ring-fg" cx="75" cy="75" r="64" stroke-dasharray="<?= round($circ, 1) ?>"
                stroke-dashoffset="<?= round($circ, 1) ?>" data-offset="<?= round($circ * (1 - max($pctNivell, 0.015)), 1) ?>"/>
      </svg>
      <div class="ring-center"><span class="ring-label">nivell</span><span class="ring-level"><?= e($progress['level']) ?></span></div>
    </div>
    <p class="level-name"><?= e($progress['name']) ?></p>
    <p class="muted"><?= e($progress['into']) ?> / <?= e($progress['needed']) ?> XP</p>
    <?php if ($progress['streak'] > 0): ?>
      <span class="streak-big"><span class="flame" aria-hidden="true">🔥</span> <?= e($progress['streak']) ?> dia<?= $progress['streak'] > 1 ? 'es' : '' ?> seguit<?= $progress['streak'] > 1 ? 's' : '' ?></span>
    <?php endif; ?>
  </div>
</section>

<div class="grid-2">
  <section class="card">
    <h2>📖 Lliçons</h2>
    <?php $temaActual = null; foreach ($lessons as $l): $p = $l['progres']; ?>
      <?php if ($l['tema'] !== $temaActual): $temaActual = $l['tema']; ?><h3 class="group-title <?= e(tema_class($temaActual)) ?>"><?= e($temaActual) ?></h3><?php endif; ?>
      <a class="item <?= e(tema_class($l['tema'])) ?>" href="index.php?p=leccio&amp;id=<?= e($l['id']) ?>">
        <div>
          <div class="item-title"><?= e($l['titol']) ?></div>
          <div class="muted small"><?= e($l['apartat']) ?> · <?= e($l['minuts']) ?> min</div>
        </div>
        <span class="pill <?= $p['acabada'] ? 'ok' : ($p['encertades'] ? 'doing' : '') ?>">
          <?= $p['acabada'] ? '✔ Feta' : e($p['encertades'] . '/' . $p['total']) ?>
        </span>
      </a>
    <?php endforeach; ?>
  </section>

  <section class="card">
    <h2>🧪 Laboratoris</h2>
    <?php $temaActual = null; foreach ($labs as $lab): $st = $lab['estat']; $n = count($lab['passos']); $done = $st['completed_at'] ? $n : (int) $st['current_step']; ?>
      <?php if ($lab['tema'] !== $temaActual): $temaActual = $lab['tema']; ?><h3 class="group-title <?= e(tema_class($temaActual)) ?>"><?= e($temaActual) ?></h3><?php endif; ?>
      <a class="item <?= e(tema_class($lab['tema'])) ?>" href="index.php?p=lab&amp;id=<?= e($lab['id']) ?>">
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
            <a class="topic-name" href="index.php?p=llegir&amp;tema=<?= e(urlencode($t['topic_name'])) ?>"><?= e($t['topic_name']) ?></a>
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
  <p class="muted">Més exercicis d'Exercicis 01 · Laboratoris web amb formularis (Activitat de Nadal).</p>
</section>
