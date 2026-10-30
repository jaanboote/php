<?php
/** @var array $temes @var array $millors @var int $pendents @var ?string $proper @var array $caixes @var int $xpAvui */
use PhpLab\{Reptes, Review};

// Il·lustració de cada mode (SVG propi; s'anima en passar el ratolí per sobre de la targeta)
$dibuixos = [
    'rapid' => '<svg viewBox="0 0 200 110" aria-hidden="true"><circle cx="100" cy="60" r="38" class="il-fons"/><circle cx="100" cy="60" r="38" class="il-linia"/>'
        . '<rect x="92" y="10" width="16" height="10" rx="3" class="il-ple"/><path d="M128 28l8-8" class="il-linia"/>'
        . '<path d="M100 60V34" class="il-agulla"/><circle cx="100" cy="60" r="4" class="il-ple"/>'
        . '<path d="M30 46h28M22 60h30M30 74h24" class="il-velocitat"/></svg>',
    'error' => '<svg viewBox="0 0 200 110" aria-hidden="true"><rect x="40" y="14" width="120" height="82" rx="10" class="il-fons"/>'
        . '<path d="M54 32h60M54 46h80M54 74h50M54 88h70" class="il-codi"/><rect x="48" y="53" width="104" height="14" rx="4" class="il-marca"/>'
        . '<path d="M54 60h64" class="il-codi fort"/><g class="il-bitxo"><ellipse cx="160" cy="62" rx="11" ry="14" class="il-ple"/>'
        . '<path d="M149 54l-8-5M149 64h-9M150 73l-7 5M171 54l8-5M171 64h9M170 73l7 5" class="il-linia"/><circle cx="160" cy="46" r="6" class="il-ple"/></g></svg>',
    'sortida' => '<svg viewBox="0 0 200 110" aria-hidden="true"><rect x="36" y="14" width="128" height="82" rx="10" class="il-fons"/>'
        . '<path d="M36 30h128" class="il-linia"/><circle cx="48" cy="22" r="3" class="il-ple"/><circle cx="58" cy="22" r="3" class="il-ple"/>'
        . '<path d="M50 46l8 6-8 6" class="il-linia"/><path d="M66 58h40" class="il-codi fort"/><path d="M50 74h70M50 86h44" class="il-codi"/>'
        . '<rect x="98" y="80" width="8" height="12" class="il-cursor"/></svg>',
    'ordenar' => '<svg viewBox="0 0 200 110" aria-hidden="true"><rect x="56" y="14" width="88" height="20" rx="6" class="il-fons"/>'
        . '<rect x="56" y="45" width="88" height="20" rx="6" class="il-fons il-bloc-mou"/><rect x="56" y="76" width="88" height="20" rx="6" class="il-fons"/>'
        . '<path d="M66 24h40M66 86h52" class="il-codi"/><path d="M66 55h60" class="il-codi fort il-bloc-mou"/>'
        . '<path d="M160 36v38M154 42l6-6 6 6M154 68l6 6 6-6" class="il-linia"/></svg>',
];

$descripcions = [
    'rapid'   => '10 preguntes barrejades contra el rellotge. Com més ràpid, més punts.',
    'error'   => 'Codi amb un error: fes clic a la línia culpable. Hi ha errates reals del temari!',
    'sortida' => 'Llegeix el codi i escriu exactament el que mostraria per pantalla.',
    'ordenar' => 'Les línies estan barrejades: ordena-les perquè el programa funcioni.',
];
?>
<header class="pagina-cap">
  <h1>Reptes</h1>
  <p class="pagina-entradeta">Partides curtes per practicar el que has après. Cada encert seguit suma més punts, i el que falles torna al repàs.</p>
  <p class="pagina-dada"><?= icona('estrelles') ?> Avui: <strong><?= e($xpAvui) ?></strong> de <?= Reptes::DAILY_XP_CAP ?> XP amb reptes · el repàs no té límit</p>
</header>

<section class="card repas-card">
  <div class="repas-info">
    <h2><?= icona('repas', 'h-ic') ?> Repàs espaciat</h2>
    <?php if ($pendents > 0): ?>
      <p>Avui toca repassar <strong><?= e($pendents) ?> <?= $pendents > 1 ? 'preguntes' : 'pregunta' ?></strong>.</p>
    <?php elseif ($proper): ?>
      <p class="muted">Avui no tens res per repassar. El proper repàs és el <?= e(date('d/m/Y', strtotime($proper))) ?>.</p>
    <?php else: ?>
      <p class="muted">Encara no has fallat cap pregunta. Quan en fallis una, tornarà a sortir aquí l'endemà.</p>
    <?php endif; ?>
    <p class="small muted">Sistema Leitner: cada encert fa pujar la pregunta de caixa i s'espera més dies
      (<?= e(implode(', ', array_map(fn($d) => "$d d", Review::INTERVALS))) ?>). Si la falles, torna a la caixa 1.</p>
    <div class="caixes" aria-label="Preguntes per caixa">
      <?php foreach ($caixes as $box => $n): ?>
        <div class="caixa" title="<?= e("Caixa $box: $n " . ($n === 1 ? 'pregunta' : 'preguntes') . ', es repassen cada ' . Review::INTERVALS[$box] . ' dies') ?>">
          <div class="caixa-cartes" aria-hidden="true">
            <?php for ($k = 0; $k < min($n, 6); $k++): ?><span style="--k: <?= $k ?>"></span><?php endfor; ?>
          </div>
          <div class="caixa-front"><span class="caixa-n"><?= e($n) ?></span><span class="caixa-dies"><?= e(Review::INTERVALS[$box]) ?> d</span></div>
          <span class="caixa-label">caixa <?= e($box) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php if ($pendents > 0): ?>
    <a class="btn primary" href="index.php?p=repte&amp;mode=repas">Començar el repàs</a>
  <?php else: ?>
    <?php $ratxa = 0; $humorForcat = 'dorm'; $fraseForcada = 'Res per repassar avui!'; $mascotaClasse = 'mascota-petita';
          require __DIR__ . '/_mascota.php'; ?>
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
    <a class="mode card mode-<?= e($mode) ?>" href="index.php?p=repte&amp;mode=<?= e($mode) ?>&amp;tema=<?= e(urlencode($_GET['tema'] ?? '')) ?>">
      <div class="mode-il">
        <?= $dibuixos[$mode] ?>
      </div>
      <h2><?= icona($cfg['icona'], 'h-ic') ?> <?= e($cfg['titol']) ?></h2>
      <p class="muted small"><?= e($desc) ?></p>
      <p class="small"><?= e($cfg['preguntes']) ?> preguntes · <?= $cfg['temps'] ? e($cfg['temps']) . ' s' : 'sense límit de temps' ?> · <?= e($cfg['xp']) ?> XP per encert</p>
      <?php if ($m): ?><p class="small muted"><?= icona('trofeu') ?> Millor puntuació: <?= e($m['millor']) ?> · <?= e($m['partides']) ?> <?= $m['partides'] > 1 ? 'partides' : 'partida' ?></p><?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>
