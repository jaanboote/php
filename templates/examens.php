<?php
/** @var array $examens @var array $resum */

// Els simulacres per tema i, a part, els que engloben diversos temes (l'examen final)
$perTema = [];
$finals = [];
foreach ($examens as $ex) {
    if (isset($ex['temes'])) {
        $finals[] = $ex;
    } else {
        $perTema[$ex['tema']][] = $ex;
    }
}

/** La targeta d'un examen: durada, parts i la millor nota si ja s'ha fet. */
$targeta = function (array $ex, string $classe = '') use ($resum): void {
    $r = $resum[$ex['id']] ?? null;
    $preguntes = count($ex['teoria']);
    $programes = count($ex['practica']);
    ?>
    <a class="card examen-card <?= e($classe) ?>" href="index.php?p=examen&amp;id=<?= e($ex['id']) ?>">
      <div class="examen-card-cap">
        <h3><?= e($ex['titol']) ?></h3>
        <?php if ($r): ?>
          <span class="nota-pill <?= $r['millor'] >= 5 ? 'ok' : 'ko' ?>" title="Millor nota"><?= e(number_format($r['millor'], 2, ',', '.')) ?></span>
        <?php endif; ?>
      </div>
      <p class="muted small"><?= e($ex['resum']) ?></p>
      <ul class="examen-xifres small">
        <li><?= icona('rellotge') ?> <?= e($ex['minuts']) ?> min</li>
        <li><?= icona('pregunta') ?> <?= e($preguntes) ?> preguntes</li>
        <li><?= icona('terminal') ?> <?= e($programes) ?> <?= $programes === 1 ? 'programa' : 'programes' ?></li>
      </ul>
      <p class="examen-estat small"><?= $r ? e($r['intents'] . ' ' . ($r['intents'] === 1 ? 'intent' : 'intents')) : 'Encara no l\'has fet' ?></p>
    </a>
    <?php
};
?>
<header class="pagina-cap">
  <h1>Exàmens</h1>
  <p class="pagina-entradeta">Simulacres amb rellotge per preparar l'examen de M0376: preguntes de teoria i programes que es corregeixen
    amb proves. No veuràs si encertes fins que l'entreguis, com en un examen de veritat.</p>
</header>

<?php foreach ($finals as $ex): ?>
  <section class="examen-final">
    <div class="examen-final-text">
      <p class="examen-final-etiqueta"><?= icona('trofeu') ?> Examen final</p>
      <h2><?= e($ex['titol']) ?></h2>
      <p><?= e($ex['resum']) ?></p>
      <ul class="examen-xifres">
        <li><?= icona('rellotge') ?> <?= e($ex['minuts']) ?> min</li>
        <li><?= icona('pregunta') ?> <?= e(count($ex['teoria'])) ?> preguntes</li>
        <li><?= icona('terminal') ?> <?= e(count($ex['practica'])) ?> programes</li>
        <?php if (isset($resum[$ex['id']])): ?><li><?= icona('trofeu') ?> Millor nota: <strong><?= e(number_format($resum[$ex['id']]['millor'], 2, ',', '.')) ?></strong></li><?php endif; ?>
      </ul>
    </div>
    <a class="btn primary" href="index.php?p=examen&amp;id=<?= e($ex['id']) ?>"><?= icona('fletxa') ?> Anar a l'examen final</a>
  </section>
<?php endforeach; ?>

<?php foreach ($perTema as $tema => $llista): ?>
  <section class="examen-tema <?= e(tema_class($tema)) ?>">
    <h2 class="group-title"><?= tema_icona($tema, 18) ?><span><?= e($tema) ?></span></h2>
    <div class="examen-graella">
      <?php foreach ($llista as $ex) { $targeta($ex); } ?>
    </div>
  </section>
<?php endforeach; ?>
