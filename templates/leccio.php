<?php
/** @var array $lesson @var array $answers @var array $progres */
?>
<div class="crumbs"><a href="index.php">Inici</a> › Aprendre › <?= e($lesson['tema']) ?></div>
<article class="lesson" data-lesson="<?= e($lesson['id']) ?>">
  <header class="lesson-head">
    <h1><?= e($lesson['titol']) ?></h1>
    <p class="muted"><?= e($lesson['resum']) ?></p>
    <p class="source">📄 Font: <?= e($lesson['origen']) ?> · <?= e($lesson['apartat']) ?></p>
    <div class="lesson-progress"><span id="lesson-count"><?= e($progres['encertades']) ?></span>/<?= e($progres['total']) ?> preguntes encertades</div>
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
        <div class="playground" data-fitxer="<?= e($b['fitxer'] ?? 'main.php') ?>">
          <textarea class="code-src"><?= e($b['codi']) ?></textarea>
          <div class="run-bar">
            <label class="inline">$ php <?= e($b['fitxer'] ?? 'main.php') ?>
              <input class="args" value="<?= e($b['args'] ?? '') ?>" placeholder="arguments" spellcheck="false"></label>
            <button class="btn primary run" type="button">Executa ▶</button>
            <button class="btn small reset" type="button" title="Torna al codi original">↺</button>
          </div>
          <?php if (isset($b['stdin'])): ?>
            <label class="stdin-label">Entrada (respostes a readline, una per línia)
              <textarea class="stdin" rows="2"><?= e($b['stdin']) ?></textarea></label>
          <?php endif; ?>
          <div class="output" hidden></div>
        </div>

      <?php else: /* preguntes */ ?>
        <?php $ans = $answers[$b['id']] ?? null; $done = $ans && $ans['correct']; ?>
        <div class="quiz <?= $done ? 'done' : '' ?>" data-q="<?= e($b['id']) ?>" data-tipus="<?= e($b['tipus']) ?>">
          <div class="quiz-kind"><?= ['test' => '❓ Pregunta', 'completa' => '🧩 Completa el codi', 'sortida' => '🖨️ Què imprimeix?'][$b['tipus']] ?></div>
          <p class="quiz-q"><?= $b['pregunta'] ?></p>
          <?php if (isset($b['codi'])): ?><pre class="code"><?= e($b['codi']) ?></pre><?php endif; ?>

          <?php if ($b['tipus'] === 'test'): ?>
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
