<?php
/**
 * Pàgina de progrés: mapa de temes, insígnies i estadístiques.
 * @var array $temes @var array $insignies @var array $xpDies @var array $xpFonts @var array $reptes @var array $dies @var array $fluixos
 */
use PhpLab\Reptes;

$aconseguides = count(array_filter($insignies, fn($b) => $b['data'] !== null));
$estatText = ['fet' => 'Fet', 'fent' => 'En curs', 'pendent' => 'Per començar'];
$nivellText = ['dominat' => 'Dominat', 'practicant' => 'Practicant', 'fluix' => 'Per reforçar', 'cap' => 'Sense practicar'];

// Gràfic d'XP per dia (SVG fet al servidor): barres amb la punta arrodonida i la base recta
$w = 640; $h = 150; $padL = 30; $padB = 22; $padT = 10;
$maxXp = max(10, ...array_column($xpDies, 'xp'));
$maxXp = (int) (ceil($maxXp / 10) * 10);
$slot = ($w - $padL) / count($xpDies);
$barW = max(4, $slot - 4);
$base = $h - $padB;
$y = fn(int $v) => $base - ($v / $maxXp) * ($base - $padT);
$totalMes = array_sum(array_column($xpDies, 'xp'));
$totalFonts = max(1, array_sum($xpFonts));
?>
<h1>El teu progrés</h1>

<section class="stat-tiles">
  <div class="stat-tile"><span class="stat-label">Nivell</span><span class="stat-value"><?= e($progress['level']) ?></span><span class="stat-sub"><?= e($progress['name']) ?></span></div>
  <div class="stat-tile"><span class="stat-label">XP total</span><span class="stat-value"><?= e($progress['xp']) ?></span><span class="stat-sub"><?= e($totalMes) ?> els últims 30 dies</span></div>
  <div class="stat-tile"><span class="stat-label">Ratxa</span><span class="stat-value">🔥 <?= e($progress['streak']) ?></span><span class="stat-sub">la millor: <?= e($dies['millor_ratxa']) ?> dia<?= $dies['millor_ratxa'] === 1 ? '' : 'es' ?></span></div>
  <div class="stat-tile"><span class="stat-label">Dies practicant</span><span class="stat-value"><?= e($dies['actius']) ?></span><span class="stat-sub">en total</span></div>
  <div class="stat-tile"><span class="stat-label">Insígnies</span><span class="stat-value"><?= e($aconseguides) ?><small>/<?= count($insignies) ?></small></span><span class="stat-sub"><a href="#insignies">veure-les</a></span></div>
</section>

<h2 class="section-title">🗺️ Mapa de temes</h2>
<div class="tema-map">
  <?php foreach ($temes as $t): $r = $t['reptes']; ?>
    <section class="card tema-card <?= e(tema_class($t['tema'])) ?> <?= $t['complet'] ? 'complet' : '' ?>">
      <header class="tema-head">
        <h3><?= e($t['tema']) ?></h3>
        <span class="pill <?= $t['complet'] ? 'ok' : ($t['fets'] ? 'doing' : '') ?>"><?= $t['complet'] ? '✔ Superat' : e($t['pct'] . ' %') ?></span>
      </header>
      <div class="meter" role="img" aria-label="<?= e("{$t['fets']} de " . count($t['passos']) . ' fets') ?>"><div class="meter-fill" style="width: <?= e($t['pct']) ?>%"></div></div>

      <ol class="tema-path">
        <?php foreach ($t['passos'] as $p): ?>
          <li class="node <?= e($p['estat']) ?>">
            <a href="index.php?p=<?= $p['tipus'] === 'lab' ? 'lab' : 'leccio' ?>&amp;id=<?= e($p['id']) ?>" title="<?= e($estatText[$p['estat']]) ?>">
              <span class="node-dot"><?= $p['estat'] === 'fet' ? '✔' : ($p['tipus'] === 'lab' ? '🧪' : '📖') ?></span>
              <span class="node-text"><?= e($p['titol']) ?><small><?= $p['tipus'] === 'lab' ? 'Laboratori' : 'Lliçó' ?> · <?= e($estatText[$p['estat']]) ?></small></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ol>

      <p class="small tema-reptes">
        🎯 <?php if ($r['intents'] > 0): ?>Reptes: <strong><?= e((int) round($r['encerts'] * 100 / $r['intents'])) ?> %</strong> d'encerts (<?= e($r['intents']) ?> <?= $r['intents'] === 1 ? 'resposta' : 'respostes' ?>)
        <?php else: ?><span class="muted">Encara no has fet reptes d'aquest tema (<?= e($r['preguntes']) ?> preguntes).</span><?php endif; ?>
        <a href="index.php?p=reptes&amp;tema=<?= e(urlencode($t['tema'])) ?>">Practicar</a>
      </p>

      <?php if ($t['conceptes']): ?>
        <details class="conceptes-tema">
          <summary>Conceptes (<?= count(array_filter($t['conceptes'], fn($c) => $c['nivell'] === 'dominat')) ?> de <?= count($t['conceptes']) ?> dominats)</summary>
          <ul class="chips">
            <?php foreach ($t['conceptes'] as $c): ?>
              <li class="chip m-<?= e($c['nivell']) ?>" title="<?= e($nivellText[$c['nivell']] . ' · ' . $c['text']) ?>">
                <span class="chip-mark" aria-hidden="true"><?= ['dominat' => '●', 'practicant' => '◐', 'fluix' => '◔', 'cap' => '○'][$c['nivell']] ?></span><?= e($c['nom']) ?></li>
            <?php endforeach; ?>
          </ul>
        </details>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>
</div>
<p class="small muted legend-mastery">Conceptes: ● dominat (80 % d'encerts o més) · ◐ practicant · ◔ per reforçar · ○ sense practicar</p>

<h2 class="section-title" id="insignies">🏅 Insígnies <small class="muted"><?= e($aconseguides) ?> de <?= count($insignies) ?></small></h2>
<div class="badges">
  <?php foreach ($insignies as $b): $on = $b['data'] !== null; ?>
    <div class="badge <?= $on ? 'on' : 'off' ?>">
      <span class="badge-icon" aria-hidden="true"><?= $b['icona'] ?></span>
      <div>
        <strong><?= e($b['titol']) ?></strong>
        <span class="small muted"><?= e($b['descripcio']) ?></span>
        <?php if ($on): ?>
          <span class="small badge-date">✔ <?= e(date('d/m/Y', strtotime($b['data']))) ?></span>
        <?php elseif ($b['progres']): [$fet, $cal] = $b['progres']; ?>
          <span class="meter mini"><span class="meter-fill" style="width: <?= e($cal > 0 ? min(100, (int) round($fet * 100 / $cal)) : 0) ?>%"></span></span>
          <span class="small muted"><?= e("$fet/$cal") ?></span>
        <?php else: ?>
          <span class="small muted">🔒 Bloquejada</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<h2 class="section-title">📊 Estadístiques</h2>
<div class="grid-2">
  <section class="card">
    <h3 class="chart-title">XP de cada dia <small class="muted">· últims 30 dies</small></h3>
    <svg class="chart" viewBox="0 0 <?= $w ?> <?= $h ?>" role="img" aria-label="<?= e("XP guanyat cada dia els últims 30 dies: $totalMes XP en total") ?>">
      <line class="grid" x1="<?= $padL ?>" x2="<?= $w ?>" y1="<?= $padT ?>" y2="<?= $padT ?>"/>
      <line class="grid" x1="<?= $padL ?>" x2="<?= $w ?>" y1="<?= $y((int) ($maxXp / 2)) ?>" y2="<?= $y((int) ($maxXp / 2)) ?>"/>
      <line class="axis" x1="<?= $padL ?>" x2="<?= $w ?>" y1="<?= $base ?>" y2="<?= $base ?>"/>
      <text class="tick" x="<?= $padL - 6 ?>" y="<?= $padT + 4 ?>" text-anchor="end"><?= $maxXp ?></text>
      <text class="tick" x="<?= $padL - 6 ?>" y="<?= $y((int) ($maxXp / 2)) + 4 ?>" text-anchor="end"><?= (int) ($maxXp / 2) ?></text>
      <text class="tick" x="<?= $padL - 6 ?>" y="<?= $base + 4 ?>" text-anchor="end">0</text>
      <?php foreach ($xpDies as $i => $d):
          $x = $padL + $i * $slot + ($slot - $barW) / 2;
          $top = $y($d['xp']);
          $rad = min(4, $barW / 2, ($base - $top));
          $label = date('d/m', strtotime($d['dia'])); ?>
        <g class="bar-g">
          <title><?= e($label . ': ' . $d['xp'] . ' XP') ?></title>
          <rect class="hit" x="<?= $padL + $i * $slot ?>" y="0" width="<?= $slot ?>" height="<?= $base ?>"/>
          <?php if ($d['xp'] > 0): ?>
            <path class="bar" d="M<?= round($x, 1) ?>,<?= $base ?> V<?= round($top + $rad, 1) ?> Q<?= round($x, 1) ?>,<?= round($top, 1) ?> <?= round($x + $rad, 1) ?>,<?= round($top, 1) ?> H<?= round($x + $barW - $rad, 1) ?> Q<?= round($x + $barW, 1) ?>,<?= round($top, 1) ?> <?= round($x + $barW, 1) ?>,<?= round($top + $rad, 1) ?> V<?= $base ?> Z"/>
          <?php endif; ?>
          <?php if ($i % 7 === 2 || $i === count($xpDies) - 1): ?>
            <text class="tick" x="<?= round($x + $barW / 2, 1) ?>" y="<?= $h - 6 ?>" text-anchor="middle"><?= $i === count($xpDies) - 1 ? 'avui' : e($label) ?></text>
          <?php endif; ?>
        </g>
      <?php endforeach; ?>
    </svg>
    <details class="small">
      <summary>Veure les dades en una taula</summary>
      <table class="table">
        <thead><tr><th>Dia</th><th>XP</th></tr></thead>
        <tbody><?php foreach (array_reverse($xpDies) as $d): if ($d['xp'] === 0) continue; ?>
          <tr><td><?= e(date('d/m/Y', strtotime($d['dia']))) ?></td><td><?= e($d['xp']) ?></td></tr>
        <?php endforeach; ?></tbody>
      </table>
    </details>
  </section>

  <section class="card">
    <h3 class="chart-title">D'on ve el teu XP</h3>
    <ul class="hbars">
      <?php foreach ($xpFonts as $font => $xp): ?>
        <li><span class="hbar-label"><?= e($font) ?></span>
          <span class="meter"><span class="meter-fill" style="width: <?= e((int) round($xp * 100 / $totalFonts)) ?>%"></span></span>
          <span class="hbar-value"><?= e($xp) ?> XP</span></li>
      <?php endforeach; ?>
    </ul>

    <h3 class="chart-title">Reptes</h3>
    <?php if (!$reptes): ?>
      <p class="muted small">Encara no has jugat cap partida. <a href="index.php?p=reptes">Prova un repte</a>.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Mode</th><th>Partides</th><th>Encert</th><th>Rècord</th></tr></thead>
        <tbody>
          <?php foreach (Reptes::MODES as $mode => $cfg): if (!isset($reptes[$mode])) continue; $m = $reptes[$mode]; ?>
            <tr><td><?= $cfg['icona'] ?> <?= e($cfg['titol']) ?></td><td><?= e($m['partides']) ?></td>
              <td><?= e($m['preguntes'] ? (int) round($m['encerts'] * 100 / $m['preguntes']) : 0) ?> %</td>
              <td><?= $mode === 'repas' ? '—' : e($m['millor']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</div>

<section class="card">
  <h3 class="chart-title">Conceptes per reforçar</h3>
  <?php if (!$fluixos): ?>
    <p class="muted small">Cap concepte per sota del 50 % d'encerts. Bona feina!</p>
  <?php else: ?>
    <ul class="concepts">
      <?php foreach ($fluixos as $c): ?>
        <li><span><?= e($c['concept']) ?></span><span class="muted small"><?= e($c['pct']) ?> % d'encerts · <?= e($c['text']) ?></span></li>
      <?php endforeach; ?>
    </ul>
    <p class="small muted">Els reptes d'aquests conceptes que has fallat ja són al <a href="index.php?p=reptes">repàs espaciat</a>.</p>
  <?php endif; ?>
</section>
