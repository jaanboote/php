<?php
/** @var array $examen @var array $historial @var ?array $resultat */
$temaText = isset($examen['temes']) ? implode(' · ', array_map(fn($t) => preg_replace('/ – .*/u', '', $t), $examen['temes'])) : $examen['tema'];
$nota = fn(float $n) => number_format($n, 2, ',', '.');
$puntsPregunta = round($examen['punts_teoria'] / count($examen['teoria']), 2);
?>
<div class="crumbs"><a href="index.php">Inici</a> › <a href="index.php?p=examens">Exàmens</a> › <?= e($examen['titol']) ?></div>

<?php if ($resultat): ?>
  <?php $r = $resultat; ?>
  <section class="card examen-resultat <?= $r['aprovat'] ? 'aprovat' : 'suspes' ?>">
    <div class="nota-gran" aria-label="<?= e('Nota: ' . $nota($r['nota']) . ' sobre 10') ?>">
      <span class="nota-xifra"><?= e($nota($r['nota'])) ?></span><span class="nota-sobre">/ 10</span>
    </div>
    <div class="examen-resultat-text">
      <p class="examen-veredicte"><?= $r['aprovat'] ? icona('check') . ' Aprovat' : icona('creu') . ' Suspès' ?></p>
      <h1><?= e($examen['titol']) ?></h1>
      <ul class="examen-xifres">
        <li><?= icona('pregunta') ?> Teoria: <strong><?= e($nota($r['punts_teoria'])) ?></strong> / <?= e($nota($r['max_teoria'])) ?> (<?= e($r['encerts']) ?> de <?= e(count($r['teoria'])) ?>)</li>
        <li><?= icona('terminal') ?> Pràctica: <strong><?= e($nota($r['punts_practica'])) ?></strong> / <?= e($nota($r['max_practica'])) ?></li>
        <li><?= icona('rellotge') ?> <?= e(intdiv($r['segons'], 60)) ?> min <?= e($r['segons'] % 60) ?> s</li>
        <?php if ($r['xp'] > 0): ?><li><?= icona('estrelles') ?> +<?= e($r['xp']) ?> XP</li><?php endif; ?>
      </ul>
      <div class="examen-barres" aria-hidden="true">
        <span class="barra-teoria" style="--pct: <?= e(round($r['punts_teoria'] * 10)) ?>%"></span><span class="barra-practica" style="--pct: <?= e(round($r['punts_practica'] * 10)) ?>%"></span>
      </div>
      <div class="banner-actions">
        <a class="btn primary" href="index.php?p=examen&amp;id=<?= e($examen['id']) ?>"><?= icona('tornar') ?> Torna-hi</a>
        <a class="btn" href="index.php?p=examens">Tots els exàmens</a>
      </div>
    </div>
  </section>

  <h2 class="section-title"><?= icona('pregunta', 'h-ic') ?> Correcció de la teoria</h2>
  <?php foreach ($r['teoria'] as $t): ?>
    <section class="block correccio <?= $t['ok'] ? 'ok' : 'ko' ?>">
      <p class="correccio-cap"><span class="correccio-num"><?= e($t['num']) ?></span>
        <span class="pill <?= $t['ok'] ? 'ok' : 'err' ?>"><?= $t['ok'] ? icona('check') . ' +' . e($nota($puntsPregunta)) : icona('creu') . ' 0' ?></span></p>
      <p class="quiz-q"><?= $t['pregunta'] /* HTML de confiança: escrit per nosaltres a content/ */ ?></p>
      <?php if ($t['codi'] !== null && $t['tipus'] !== 'ordenar'): ?><pre class="code"><?= e($t['codi']) ?></pre><?php endif; ?>
      <div class="correccio-respostes">
        <div><p class="small muted">La teva resposta</p><pre class="resposta <?= $t['ok'] ? 'ok' : 'ko' ?>"><?= e($t['resposta']) ?></pre></div>
        <?php if (!$t['ok']): ?><div><p class="small muted">Resposta correcta</p><pre class="resposta ok"><?= e($t['solucio']) ?></pre></div><?php endif; ?>
      </div>
      <p class="small"><?= $t['explicacio'] ?></p>
    </section>
  <?php endforeach; ?>

  <h2 class="section-title"><?= icona('terminal', 'h-ic') ?> Correcció de la pràctica</h2>
  <?php foreach ($r['practica'] as $p): ?>
    <section class="block correccio <?= $p['punts'] >= $p['max'] ? 'ok' : ($p['punts'] > 0 ? 'mig' : 'ko') ?>">
      <p class="correccio-cap"><strong><?= e($p['titol']) ?></strong>
        <span class="pill <?= $p['punts'] >= $p['max'] ? 'ok' : ($p['punts'] > 0 ? 'doing' : 'err') ?>"><?= e($nota($p['punts'])) ?> / <?= e($nota($p['max'])) ?></span></p>
      <ul class="correccio-proves">
        <?php foreach ($p['proves'] as $t): ?>
          <li class="<?= $t['ok'] ? 'ok' : 'ko' ?>"><?= icona($t['ok'] ? 'check' : 'creu') ?> <span><?= e($t['nom']) ?>
            <?php if ($t['ordre'] !== ''): ?><code class="small"><?= e($t['ordre']) ?></code><?php endif; ?>
            <?php if (!$t['ok'] && $t['motiu'] !== ''): ?><span class="small muted correccio-motiu"><?= e($t['motiu']) ?></span><?php endif; ?></span></li>
        <?php endforeach; ?>
      </ul>
      <details><summary>El teu codi</summary><pre class="code"><?= e($p['codi']) ?></pre></details>
      <details><summary>Una solució</summary><pre class="code"><?= e($p['solucio']) ?></pre>
        <?php if ($p['solucio_explicacio'] !== ''): ?><p class="small"><?= e($p['solucio_explicacio']) ?></p><?php endif; ?></details>
    </section>
  <?php endforeach; ?>

<?php else: ?>
  <section class="card examen-intro <?= e(isset($examen['temes']) ? '' : tema_class($examen['tema'])) ?>" id="examen-intro">
    <p class="lesson-tema"><?= e($temaText) ?></p>
    <h1><?= e($examen['titol']) ?></h1>
    <p><?= e($examen['resum']) ?></p>
    <ul class="examen-xifres">
      <li><?= icona('rellotge') ?> <?= e($examen['minuts']) ?> minuts</li>
      <li><?= icona('pregunta') ?> Teoria: <?= e(count($examen['teoria'])) ?> preguntes · <?= e($nota((float) $examen['punts_teoria'])) ?> punts</li>
      <li><?= icona('terminal') ?> Pràctica: <?= e(count($examen['practica'])) ?> <?= count($examen['practica']) === 1 ? 'programa' : 'programes' ?> · <?= e($nota((float) array_sum(array_column($examen['practica'], 'punts')))) ?> punts</li>
    </ul>
    <div class="examen-normes">
      <h2>Com funciona</h2>
      <ul>
        <li>No veuràs si encertes fins que l'entreguis. Cada pregunta de teoria val <?= e($nota($puntsPregunta)) ?> punts i les errades no resten.</li>
        <li>A la part pràctica pots executar els teus programes tantes vegades com vulguis. La nota de cada programa depèn de quantes proves supera.</li>
        <li>Les respostes es desen soles: si tanques la pestanya, en tornar continues on eres (el rellotge no s'atura).</li>
        <li>Quan s'acaba el temps, l'examen s'entrega sol.</li>
      </ul>
    </div>
    <button class="btn primary" id="examen-comenca" type="button" data-examen="<?= e($examen['id']) ?>"><?= icona('fletxa') ?> Comença l'examen</button>
  </section>

  <?php if ($historial): ?>
    <section class="card">
      <h2><?= icona('historial', 'h-ic') ?> Intents anteriors</h2>
      <table class="taula examen-historial">
        <tr><th>Data</th><th>Nota</th><th></th></tr>
        <?php foreach ($historial as $h): ?>
          <tr><td><?= e(date('d/m/Y H:i', (int) $h['finished_at'])) ?></td>
            <td><span class="nota-pill <?= $h['nota'] >= 5 ? 'ok' : 'ko' ?>"><?= e($nota((float) $h['nota'])) ?></span></td>
            <td><a href="index.php?p=examen&amp;id=<?= e($examen['id']) ?>&amp;intent=<?= e($h['id']) ?>">Veure la correcció</a></td></tr>
        <?php endforeach; ?>
      </table>
    </section>
  <?php endif; ?>

  <div class="examen" id="examen" data-examen="<?= e($examen['id']) ?>" hidden>
    <div class="examen-barra">
      <strong class="examen-barra-titol"><?= e($examen['titol']) ?></strong>
      <span class="pill" id="ex-respostes">0 / 0</span>
      <span class="pill timer" id="ex-temps" role="timer" aria-live="off"><?= icona('rellotge') ?> <span>--:--</span></span>
      <span class="small muted" id="ex-desat" aria-live="polite"></span>
      <button class="btn primary" id="ex-entrega" type="button"><?= icona('check') ?> Entrega l'examen</button>
    </div>
    <h2 class="section-title"><?= icona('pregunta', 'h-ic') ?> Part 1 · Teoria</h2>
    <div id="ex-teoria"></div>
    <h2 class="section-title"><?= icona('terminal', 'h-ic') ?> Part 2 · Pràctica</h2>
    <div id="ex-practica"></div>
    <div class="examen-final-entrega">
      <button class="btn primary" id="ex-entrega-2" type="button"><?= icona('check') ?> Entrega l'examen</button>
    </div>
  </div>
<?php endif; ?>
