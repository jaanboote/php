<?php
/** @var array $temes @var array $millors @var int $pendents @var ?string $proper @var array $caixes @var int $xpAvui */
use PhpLab\{Reptes, Review};

$descripcions = [
    'rapid'   => '10 preguntes barrejades contra el rellotge. Com més ràpid, més punts.',
    'error'   => 'Codi amb un error: fes clic a la línia culpable. Hi ha errates reals del temari!',
    'sortida' => 'Llegeix el codi i escriu exactament el que mostraria per pantalla.',
    'ordenar' => 'Les línies estan barrejades: ordena-les perquè el programa funcioni.',
];
?>
<h1>Reptes</h1>
<p class="muted">Practica ràpid el que has après. Cada encert seguit suma més punts (combo). Les preguntes que falles tornen al repàs espaciat.
  Avui has guanyat <strong><?= e($xpAvui) ?></strong> de <?= Reptes::DAILY_XP_CAP ?> XP possibles amb reptes (el repàs no té límit).</p>

<section class="card repas-card">
  <div class="repas-info">
    <h2>🔁 Repàs espaciat</h2>
    <?php if ($pendents > 0): ?>
      <p>Avui toca repassar <strong><?= e($pendents) ?> <?= $pendents > 1 ? 'preguntes' : 'pregunta' ?></strong>.</p>
    <?php elseif ($proper): ?>
      <p class="muted">Avui no tens res per repassar. El proper repàs és el <?= e(date('d/m/Y', strtotime($proper))) ?>.</p>
    <?php else: ?>
      <p class="muted">Encara no has fallat cap pregunta. Quan en fallis una, tornarà a sortir aquí l'endemà.</p>
    <?php endif; ?>
    <p class="small muted">Sistema Leitner: cada encert fa pujar la pregunta de caixa i s'espera més dies
      (<?= e(implode(', ', array_map(fn($d) => "$d d", Review::INTERVALS))) ?>). Si la falles, torna a la caixa 1.</p>
    <div class="boxes" aria-label="Preguntes per caixa">
      <?php foreach ($caixes as $box => $n): ?>
        <div class="box"><span class="box-n"><?= e($n) ?></span><span class="box-label">caixa <?= e($box) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php if ($pendents > 0): ?>
    <a class="btn primary" href="index.php?p=repte&amp;mode=repas">Començar el repàs</a>
  <?php endif; ?>
</section>

<form class="tema-filter" method="get">
  <input type="hidden" name="p" value="reptes">
  <label>Tema dels reptes:
    <select name="tema" onchange="this.form.submit()">
      <option value="">Tots els temes</option>
      <?php foreach ($temes as $t): ?>
        <option value="<?= e($t) ?>" <?= ($_GET['tema'] ?? '') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
</form>

<div class="modes">
  <?php foreach ($descripcions as $mode => $desc): $cfg = Reptes::MODES[$mode]; $m = $millors[$mode] ?? null; ?>
    <a class="mode card" href="index.php?p=repte&amp;mode=<?= e($mode) ?>&amp;tema=<?= e(urlencode($_GET['tema'] ?? '')) ?>">
      <div class="mode-icon"><?= $cfg['icona'] ?></div>
      <h2><?= e($cfg['titol']) ?></h2>
      <p class="muted small"><?= e($desc) ?></p>
      <p class="small"><?= e($cfg['preguntes']) ?> preguntes · <?= $cfg['temps'] ? e($cfg['temps']) . ' s' : 'sense límit de temps' ?> · <?= e($cfg['xp']) ?> XP per encert</p>
      <?php if ($m): ?><p class="small muted">🏆 Millor puntuació: <?= e($m['millor']) ?> · <?= e($m['partides']) ?> <?= $m['partides'] > 1 ? 'partides' : 'partida' ?></p><?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>
