<?php
/** @var array $lesson @var array $answers @var array $progres @var ?array $seguent @var ?array $labLligat */
?>
<div class="crumbs"><a href="index.php">Inici</a> › Aprendre › <?= e($lesson['tema']) ?></div>
<article class="lesson <?= e(tema_class($lesson['tema'])) ?>" data-lesson="<?= e($lesson['id']) ?>"
         data-seguent-id="<?= e($seguent['id'] ?? '') ?>" data-seguent-titol="<?= e($seguent['titol'] ?? '') ?>"
         data-lab-id="<?= e($labLligat['id'] ?? '') ?>" data-lab-titol="<?= e($labLligat['titol'] ?? '') ?>">
  <div class="read-progress" id="read-progress" aria-hidden="true"></div>
  <header class="lesson-head">
    <div class="lesson-title">
      <span class="tema-badge gran"><?= tema_icona($lesson['tema'], 30) ?></span>
      <div>
        <p class="lesson-tema"><?= e($lesson['tema']) ?></p>
        <h1><?= e($lesson['titol']) ?></h1>
      </div>
    </div>
    <p class="lesson-resum"><?= e($lesson['resum']) ?></p>
    <div class="lesson-meta">
      <span class="meta-chip">⏱ <?= e($lesson['minuts']) ?> min</span>
      <span class="meta-chip" title="<?= e($lesson['origen']) ?>">📄 <?= e($lesson['apartat']) ?></span>
      <span class="lesson-progress"><span id="lesson-count"><?= e($progres['encertades']) ?></span>/<?= e($progres['total']) ?> preguntes encertades</span>
    </div>
  </header>

  <?php foreach ($lesson['blocs'] as $i => $b): ?>
    <?php $amp = !empty($b['ampliacio']); ?>
    <section class="block block-<?= e($b['tipus']) ?><?= $amp ? ' ampliacio' : '' ?>">
      <?php if ($amp): ?><span class="badge-amp" title="Aquest contingut no surt del temari de classe">Ampliació</span><?php endif; ?>

      <?php if ($b['tipus'] === 'text'): ?>
        <h2><?= e($b['titol']) ?></h2>
        <?= $b['html'] /* HTML de confiança: escrit per nosaltres a content/ */ ?>

      <?php elseif ($b['tipus'] === 'codi'): ?>
        <h3>▶ <?= e($b['titol']) ?></h3>
        <?= $b['html'] ?? '' ?>
        <div class="playground" data-fitxer="<?= e($b['fitxer'] ?? 'main.php') ?>" data-bloc="<?= (int) $i ?>">
          <textarea class="code-src"><?= e($b['codi']) ?></textarea>
          <div class="run-bar">
            <label class="inline">$ php <?= e($b['fitxer'] ?? 'main.php') ?>
              <input class="args" value="<?= e($b['args'] ?? '') ?>" placeholder="arguments" spellcheck="false"></label>
            <button class="btn primary run" type="button">Executa ▶</button>
            <button class="btn small reset" type="button" title="Torna al codi original" aria-label="Torna al codi original">↺</button>
          </div>
          <?php if (isset($b['stdin'])): ?>
            <label class="stdin-label">Entrada (respostes a readline, una per línia)
              <textarea class="stdin" rows="2"><?= e($b['stdin']) ?></textarea></label>
          <?php endif; ?>
          <?php if (!empty($b['fitxers'])): ?>
            <p class="small muted">📁 Fitxers que hi ha a la carpeta abans d'executar: <?= e(implode(', ', array_keys($b['fitxers']))) ?></p>
          <?php endif; ?>
          <div class="output" hidden></div>
        </div>

      <?php else: /* preguntes */ ?>
        <?php $ans = $answers[$b['id']] ?? null; $done = $ans && $ans['correct']; ?>
        <div class="quiz <?= $done ? 'done' : '' ?>" data-q="<?= e($b['id']) ?>" data-tipus="<?= e($b['tipus']) ?>">
          <div class="quiz-kind"><?= ['test' => '❓ Pregunta', 'completa' => '🧩 Completa el codi', 'sortida' => '🖨️ Què imprimeix?', 'ordenar' => '↕️ Ordena-ho'][$b['tipus']] ?></div>
          <p class="quiz-q"><?= $b['pregunta'] ?></p>
          <?php if (isset($b['codi'])): ?><pre class="code"><?= e($b['codi']) ?></pre><?php endif; ?>

          <?php if ($b['tipus'] === 'ordenar'): ?>
            <ol class="sortable">
              <?php foreach ($done ? array_keys($b['elements']) : PhpLab\Lessons::shuffled($b) as $k): /* ja encertada: en l'ordre correcte */ ?>
                <li data-idx="<?= (int) $k ?>"><span class="sort-text"><?= e($b['elements'][$k]) ?></span>
                  <span class="sort-buttons"><button type="button" class="btn small up" aria-label="Amunt">↑</button><button type="button" class="btn small down" aria-label="Avall">↓</button></span></li>
              <?php endforeach; ?>
            </ol>
            <button type="button" class="btn primary check-order">Comprova l'ordre</button>
          <?php elseif ($b['tipus'] === 'test'): ?>
            <div class="options">
              <?php foreach ($b['opcions'] as $k => $op): ?>
                <button type="button" class="option" data-value="<?= e($k) ?>"><?= e($op) ?></button>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="answer-row">
              <?php if ($b['tipus'] === 'sortida'): ?>
                <textarea class="answer" rows="2" placeholder="Escriu exactament el que sortiria per pantalla" spellcheck="false"></textarea>
              <?php else: ?>
                <input class="answer" placeholder="Què hi va a ___ ?" spellcheck="false" autocomplete="off">
              <?php endif; ?>
              <button type="button" class="btn primary check">Comprova</button>
            </div>
          <?php endif; ?>
          <div class="feedback" <?= $done ? '' : 'hidden' ?>><?= $done ? '✔ Ja l\'has encertat. ' . e($b['explicacio']) : '' ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($b['origen'])): ?><p class="source small">📄 <?= e($b['origen']) ?></p><?php endif; ?>
    </section>
  <?php endforeach; ?>

  <section class="block next-step">
    <h2>I ara, a practicar!</h2>
    <p>Al laboratori faràs un programa complet pas a pas, amb el tutor al costat.</p>
    <a class="btn primary" href="index.php?p=lab&amp;id=<?= e($lesson['lab']) ?>">Anar al laboratori →</a>
  </section>
</article>
