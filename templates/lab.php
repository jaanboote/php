<?php
/** @var array $lab @var array $state @var ?array $summary @var array $history @var bool $ai */
use PhpLab\{Auth, Labs, WebLab};

$web = WebLab::isWeb($lab);

$current = (int) $state['current_step'];
$finished = $state['completed_at'] !== null;
$total = count($lab['passos']);
$row = $state['steps'][$current] ?? ['fails' => 0, 'hints_used' => 0, 'solution_seen' => 0, 'attempts' => 0];
$step = $lab['passos'][$current];
?>
<div class="crumbs"><a href="index.php">Inici</a> › Laboratori › <?= e($lab['tema']) ?></div>

<div class="lab <?= $web ? 'web' : '' ?> <?= e(tema_class($lab['tema'])) ?>" id="lab"
     data-lab="<?= e($lab['id']) ?>" data-step="<?= $current ?>" data-total="<?= $total ?>" data-web="<?= $web ? '1' : '0' ?>"
     data-fitxer="<?= e($lab['fitxer'] ?? '') ?>" data-finished="<?= $finished ? '1' : '0' ?>"
     data-hints="<?= (int) $row['hints_used'] ?>" data-fails="<?= (int) $row['fails'] ?>"
     data-need="<?= Labs::ATTEMPTS_FOR_SOLUTION ?>">

  <!-- ============ Columna esquerra: enunciat i passos ============ -->
  <aside class="lab-steps">
    <h1><?= e($lab['titol']) ?></h1>
    <p class="source small">📄 <?= e($lab['origen']['descripcio']) ?></p>
    <details class="context" <?= $current === 0 && !$finished ? 'open' : '' ?>>
      <summary>De què va?</summary>
      <?= $lab['context'] ?>
    </details>

    <?php if ($summary): ?>
      <section class="summary">
        <h2>🎉 Laboratori acabat!</h2>
        <p><strong><?= e($summary['xp']) ?> XP</strong> guanyats · <?= e($summary['intents']) ?> comprovacions · <?= e($summary['pistes']) ?> pistes
          <?= $summary['minuts'] !== null ? ' · ' . e($summary['minuts']) . ' min' : '' ?></p>
        <h3>Què has après</h3>
        <ul><?php foreach ($summary['apres'] as $a): ?><li><?= e($a) ?></li><?php endforeach; ?></ul>
        <?php if ($summary['dificils']): ?>
          <h3>El que t'ha costat més</h3>
          <p><?php foreach ($summary['dificils'] as $d): ?><span class="pill doing"><?= e($d) ?></span> <?php endforeach; ?></p>
          <p class="muted small">Ho tindràs en compte al repàs (fase 3).</p>
        <?php endif; ?>
        <form method="post" onsubmit="return confirm('Començar de zero? L\'XP que ja tens no es perd.')">
          <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
          <input type="hidden" name="accio" value="reiniciar_lab">
          <input type="hidden" name="lab" value="<?= e($lab['id']) ?>">
          <button class="btn small" type="submit">↺ Tornar a fer-lo</button>
        </form>
      </section>
    <?php endif; ?>

    <ol class="steps">
      <?php foreach ($lab['passos'] as $i => $s):
          $passed = isset($state['steps'][$i]['passed_at']);
          $isCurrent = $i === $current && !$finished;
          $cls = $passed ? 'passed' : ($isCurrent ? 'current' : 'locked'); ?>
        <li class="step <?= $cls ?>">
          <div class="step-head">
            <span class="step-num"><?= $passed ? '✔' : ($cls === 'locked' ? '🔒' : $i + 1) ?></span>
            <span class="step-title"><?= e($s['titol']) ?></span>
            <span class="step-xp muted small"><?= e($s['xp']) ?> XP</span>
          </div>
          <?php if ($isCurrent): ?>
            <div class="step-body">
              <?= $s['explicacio'] ?>
              <div class="hints" id="hints">
                <?php for ($h = 1; $h <= (int) $row['hints_used']; $h++): ?>
                  <div class="hint"><strong>Pista <?= $h ?>:</strong> <span class="hint-text"><?= nl2br(e($s['pistes'][$h - 1])) ?></span></div>
                <?php endfor; ?>
              </div>
              <div class="step-actions">
                <button class="btn small" id="btn-hint" type="button" <?= (int) $row['hints_used'] >= count($s['pistes']) ? 'hidden' : '' ?>>
                  💡 Pista <span id="hint-level"><?= (int) $row['hints_used'] + 1 ?></span> (−5 XP)</button>
                <button class="btn small warn" id="btn-solution" type="button" <?= Labs::canSeeSolution($row) ? '' : 'hidden' ?>>Veure la solució</button>
              </div>
              <div id="solution" hidden></div>
            </div>
          <?php elseif ($passed): ?>
            <details class="step-body done"><summary class="small muted">Tornar a llegir</summary><?= $s['explicacio'] ?></details>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
    <?php if ($web): ?>
      <details class="tutor-inline">
        <summary>🧑‍🏫 Pregunta al tutor</summary>
  <aside class="lab-tutor">
    <div class="tutor-head">
      <h2>🧑‍🏫 Tutor</h2>
      <span class="pill <?= $ai ? 'ok' : '' ?>" title="<?= $ai ? 'Respostes amb la IA de Claude' : 'Sense clau d\'API: respostes predefinides' ?>"><?= $ai ? 'IA' : 'Sense connexió' ?></span>
    </div>
    <div class="chat" id="chat">
      <div class="msg assistant">Hola! Soc el teu tutor. No et donaré la solució, però t'ajudaré a trobar-la. Quan tinguis un dubte o un error, explica'm què passa. Jo veig el teu codi i l'última sortida.</div>
      <?php foreach ($history as $m): ?>
        <div class="msg <?= e($m['role']) ?>"><?= $m['role'] === 'assistant' ? mini_markdown($m['content']) : nl2br(e($m['content'])) ?></div>
      <?php endforeach; ?>
    </div>
    <form class="chat-form" id="chat-form">
      <textarea id="chat-input" rows="2" placeholder="Pregunta al tutor… (Enter per enviar)"></textarea>
      <div class="chat-buttons">
        <button class="btn small" type="button" data-quick="No entenc l'error que surt. Què vol dir?">No entenc l'error</button>
        <button class="btn small" type="button" data-quick="No sé per on començar aquest pas.">Per on començo?</button>
        <button class="btn primary small" type="submit">Envia</button>
      </div>
    </form>
  </aside>
      </details>
    <?php endif; ?>
  </aside>

  <!-- ============ Columna central: editor ============ -->
  <section class="lab-editor">
    <?php if ($web): $sources = WebLab::sources($lab, $state['code'] ?? null); ?>
    <div class="editor-head">
      <div class="file-tabs" role="tablist" aria-label="Fitxers de la web">
        <?php foreach (array_keys($sources) as $k => $name): ?>
          <button class="file-tab <?= $k === 0 ? 'active' : '' ?>" type="button" role="tab" data-file="<?= e($name) ?>"><?= e($name) ?></button>
        <?php endforeach; ?>
      </div>
      <span class="muted small" id="save-state"></span>
    </div>
    <textarea id="code" class="web-sources" hidden><?= e(json_encode($sources, JSON_UNESCAPED_UNICODE)) ?></textarea>
    <textarea id="code-editor"></textarea>
    <div class="status-bar" aria-live="off"><span id="sb-pos">Ln 1, Col 1</span><span id="sb-lines"></span><span class="sb-right">PHP 8 · UTF-8</span></div>
    <div class="run-bar">
      <button class="btn" id="btn-open" type="button" title="Ctrl+Enter">▶ Desa i obre al navegador</button>
      <button class="btn primary" id="btn-check" type="button" <?= $finished ? 'disabled' : '' ?>>✔ Comprova el pas</button>
    </div>
    <?php else: ?>
    <div class="editor-head">
      <span class="filename">📄 <?= e($lab['fitxer']) ?></span>
      <span class="muted small" id="save-state"></span>
    </div>
    <textarea id="code"><?= e($state['code'] ?? $lab['codi_inicial']) ?></textarea>
    <div class="status-bar" aria-live="off"><span id="sb-pos">Ln 1, Col 1</span><span id="sb-lines"></span><span class="sb-right">PHP 8 · UTF-8</span></div>
    <div class="run-bar">
      <label class="inline">$ php <?= e($lab['fitxer']) ?>
        <input id="args" placeholder="arguments (p. ex. 754)" spellcheck="false" autocomplete="off"></label>
      <button class="btn" id="btn-run" type="button" title="Ctrl+Enter">Executa ▶</button>
      <button class="btn primary" id="btn-check" type="button" <?= $finished ? 'disabled' : '' ?>>✔ Comprova el pas</button>
    </div>
    <details class="stdin-box">
      <summary class="small muted">Entrada per a readline() (opcional)</summary>
      <textarea id="stdin" rows="2" placeholder="Una resposta per línia"></textarea>
    </details>
    <div id="argv-preview" class="argv muted small"></div>
    <div id="output" class="output" hidden></div>
    <div id="files" class="files-panel" hidden></div>
    <?php endif; ?>
    <div id="results" class="results" hidden></div>
  </section>

  <?php if ($web): ?>
  <!-- ============ Columna dreta: navegador i inspector ============ -->
  <section class="lab-browser">
    <div class="browser">
      <div class="browser-bar">
        <span class="browser-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <button class="browser-btn" id="br-home" type="button" title="Torna a index.php" aria-label="Torna a index.php">⌂</button>
        <button class="browser-btn" id="br-reload" type="button" title="Torna a carregar (GET)" aria-label="Torna a carregar">⟳</button>
        <form class="browser-address" id="br-form">
          <span class="browser-host">localhost/</span>
          <input id="br-url" value="index.php" spellcheck="false" autocomplete="off" aria-label="Adreça">
        </form>
      </div>
      <iframe id="browser" class="browser-frame" title="La teva web" sandbox="allow-forms allow-scripts allow-modals"></iframe>
    </div>
    <div class="inspector" id="inspector">
      <p class="muted small">Prem «Desa i obre» per veure la teva web. Aquí veuràs què rep PHP a cada petició.</p>
    </div>
  </section>
  <?php else: ?>
  <!-- ============ Columna dreta: tutor ============ -->
  <aside class="lab-tutor">
    <div class="tutor-head">
      <h2>🧑‍🏫 Tutor</h2>
      <span class="pill <?= $ai ? 'ok' : '' ?>" title="<?= $ai ? 'Respostes amb la IA de Claude' : 'Sense clau d\'API: respostes predefinides' ?>"><?= $ai ? 'IA' : 'Sense connexió' ?></span>
    </div>
    <div class="chat" id="chat">
      <div class="msg assistant">Hola! Soc el teu tutor. No et donaré la solució, però t'ajudaré a trobar-la. Quan tinguis un dubte o un error, explica'm què passa. Jo veig el teu codi i l'última sortida.</div>
      <?php foreach ($history as $m): ?>
        <div class="msg <?= e($m['role']) ?>"><?= $m['role'] === 'assistant' ? mini_markdown($m['content']) : nl2br(e($m['content'])) ?></div>
      <?php endforeach; ?>
    </div>
    <form class="chat-form" id="chat-form">
      <textarea id="chat-input" rows="2" placeholder="Pregunta al tutor… (Enter per enviar)"></textarea>
      <div class="chat-buttons">
        <button class="btn small" type="button" data-quick="No entenc l'error que surt. Què vol dir?">No entenc l'error</button>
        <button class="btn small" type="button" data-quick="No sé per on començar aquest pas.">Per on començo?</button>
        <button class="btn primary small" type="submit">Envia</button>
      </div>
    </form>
  </aside>
  <?php endif; ?>
</div>
