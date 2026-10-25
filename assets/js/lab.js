/* PHPLab — laboratori: editor, executar, comprovar el pas, pistes, solució i tutor. */
'use strict';

(() => {
  const root = document.getElementById('lab');
  if (!root) return;
  const { api, esc, toast, updateProgress, makeEditor, renderOutput, argvPreview, filesHtml } = PHPLab;

  const labId = root.dataset.lab;
  const step = +root.dataset.step;
  const fitxer = root.dataset.fitxer;
  const need = +root.dataset.need;
  let fails = +root.dataset.fails;
  let hintsUsed = +root.dataset.hints;

  const $ = (id) => document.getElementById(id);
  const argsInput = $('args');
  const stdinInput = $('stdin');
  let lastRun = { stdout: '', stderr: '', args: '' };
  let lastTests = [];

  // ---------- Editor i desat automàtic ----------
  const textarea = $('code');
  const cm = makeEditor(textarea, { 'Ctrl-Enter': () => run() });
  const getCode = () => (cm ? cm.getValue() : textarea.value);
  let saveTimer = null;
  const scheduleSave = () => {
    $('save-state').textContent = 'canvis sense desar…';
    clearTimeout(saveTimer);
    saveTimer = setTimeout(async () => {
      try {
        await api('lab_save', { lab: labId, code: getCode() });
        $('save-state').textContent = 'desat ✓';
      } catch {
        $('save-state').textContent = 'no s\'ha pogut desar';
      }
    }, 1200);
  };
  cm ? cm.on('change', scheduleSave) : textarea.addEventListener('input', scheduleSave);

  const updateArgv = () => ($('argv-preview').textContent = argvPreview(fitxer, argsInput.value));
  argsInput.addEventListener('input', updateArgv);
  argsInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') run(); });
  updateArgv();

  function busy(btn, on) { btn.classList.toggle('loading', on); }

  // ---------- Executar ----------
  async function run() {
    const btn = $('btn-run');
    busy(btn, true);
    try {
      const r = await api('run', { code: getCode(), args: argsInput.value, stdin: stdinInput.value, fitxer, lab: labId });
      lastRun = { stdout: r.stdout, stderr: r.stderr, args: argsInput.value };
      renderOutput($('output'), r, cm, false);
      renderFiles(r.files);
    } catch (e) {
      toast(e.message);
    } finally {
      busy(btn, false);
    }
  }
  $('btn-run').addEventListener('click', run);

  // ---------- Fitxers del laboratori (es conserven entre execucions) ----------
  function renderFiles(files) {
    const box = $('files');
    if (!files || !files.length) {
      box.hidden = true;
      box.innerHTML = '';
      return;
    }
    box.hidden = false;
    box.innerHTML = filesHtml(files, '📁 Carpeta del programa') +
      '<button class="btn small" type="button" id="btn-clear-files">🗑 Buidar la carpeta</button>';
    box.querySelector('details').open = true;
    $('btn-clear-files').addEventListener('click', async () => {
      if (!confirm('Esborrar tots els fitxers que ha creat el programa?')) return;
      const r = await api('lab_files', { lab: labId, buidar: true });
      renderFiles(r.files);
      toast('Carpeta buidada');
    });
  }
  api('lab_files', { lab: labId }).then((r) => renderFiles(r.files)).catch(() => {});

  // ---------- Comprovar el pas ----------
  $('btn-check').addEventListener('click', async () => {
    const btn = $('btn-check');
    busy(btn, true);
    try {
      const r = await api('lab_check', { lab: labId, step, code: getCode() });
      lastTests = r.proves.map((t) => ({ nom: t.nom, ordre: t.ordre, ok: t.ok, motiu: t.motiu }));
      renderResults(r);
      updateProgress(r.progress, r.xp, btn);
      if (r.superat) {
        PHPLab.celebrate($('results').querySelector('.banner'), r.acabat ? 60 : 32);
        document.querySelector('.step.current')?.classList.add('passed', 'just-passed');
      } else {
        PHPLab.shake($('results').querySelector('.banner'));
      }
      if (!r.superat) {
        fails++;
        if (fails >= need) $('btn-solution').hidden = false;
      }
    } catch (e) {
      toast(e.message);
    } finally {
      busy(btn, false);
    }
  });

  function renderResults(r) {
    const box = $('results');
    box.hidden = false;
    let html = '';
    if (r.superat) {
      html += '<div class="banner ok">✔ Pas superat!' + (r.xp ? ' +' + r.xp + ' XP' : '') +
        (r.acabat
          ? ' <button class="btn primary small" onclick="location.reload()">Veure el resum 🎉</button>'
          : ' <button class="btn primary small" onclick="location.reload()">Següent pas →</button>') + '</div>';
    } else {
      const n = r.proves.filter((t) => !t.ok).length + r.codi.filter((c) => !c.ok).length;
      html += '<div class="banner ko">Encara no: ' + (n > 1 ? n + ' comprovacions fallen' : '1 comprovació falla') +
        '. Mira el detall i, si et cal, pregunta al tutor.</div>';
    }
    for (const c of r.codi) {
      html += '<div class="code-check">' + (c.ok ? '✔' : '✖') + ' ' + esc(c.missatge) + '</div>';
    }
    html += '<h3>Proves</h3>';
    r.proves.forEach((t, i) => {
      html += '<details class="test ' + (t.ok ? 'ok' : 'ko') + '" style="--i:' + i + '"' + (t.ok ? '' : ' open') + '><summary>' + (t.ok ? '✔' : '✖') + ' ' +
        esc(t.nom) + '<span class="test-cmd">$ ' + esc(t.ordre) + '</span></summary><div class="test-body">';
      if (!t.ok) html += '<p><strong>' + esc(t.motiu) + '</strong></p>';
      if (t.esperat !== null && !t.ok) {
        html += '<div class="compare"><div>Esperat<pre>' + esc(t.esperat) + '</pre></div><div>Has mostrat<pre>' + esc(t.obtingut || '(res)') + '</pre></div></div>';
      } else {
        html += '<div>Sortida<pre class="code">' + esc(t.obtingut || '(res)') + '</pre></div>';
      }
      if (t.fitxers_inicials && t.fitxers_inicials.length) {
        html += '<p class="small muted">Fitxers preparats abans d\'executar: ' + t.fitxers_inicials.map(esc).join(', ') + '</p>';
      }
      html += filesHtml(t.fitxers, '📁 Fitxers després de l\'execució');
      for (const ex of t.errors || []) {
        html += '<div class="explain ' + esc(ex.tipus) + '"><strong>' + (ex.linia ? 'Línia ' + ex.linia + ' · ' : '') + esc(ex.titol) + '</strong><br>' + esc(ex.explicacio) + '</div>';
      }
      html += '<button class="btn small try" type="button" data-args="' + esc(t.ordre.replace(/^php \S+\s?/, '')) + '">Prova-ho amb aquests arguments</button>';
      html += '</div></details>';
    });
    box.innerHTML = html;
    box.querySelectorAll('.try').forEach((b) => b.addEventListener('click', () => {
      argsInput.value = b.dataset.args;
      updateArgv();
      run();
      argsInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }));
  }

  // ---------- Pistes ----------
  $('btn-hint')?.addEventListener('click', async () => {
    try {
      const r = await api('lab_hint', { lab: labId, step, level: hintsUsed + 1 });
      hintsUsed = r.nivell;
      const div = document.createElement('div');
      div.className = 'hint';
      div.innerHTML = '<strong>Pista ' + r.nivell + ':</strong> <span class="hint-text">' + esc(r.text).replace(/\n/g, '<br>') + '</span>';
      $('hints').appendChild(div);
      $('hint-level').textContent = hintsUsed + 1;
      if (hintsUsed >= 3) $('btn-hint').hidden = true;
      toast('Aquest pas ara val ' + r.xp_pas + ' XP');
    } catch (e) {
      toast(e.message);
    }
  });

  // ---------- Solució ----------
  $('btn-solution')?.addEventListener('click', async () => {
    if (!confirm('Segur? Si veus la solució, aquest pas només donarà 2 XP. Abans pots demanar una pista o preguntar al tutor.')) return;
    try {
      const r = await api('lab_solution', { lab: labId, step });
      const box = $('solution');
      box.hidden = false;
      box.innerHTML = '<h3>Solució del pas</h3><p class="small">' + esc(r.explicacio) + '</p><pre class="code">' + esc(r.codi) +
        '</pre><button class="btn small" type="button" id="copy-sol">Posa-la a l\'editor</button>';
      $('copy-sol').addEventListener('click', () => {
        if (confirm('Substituir el teu codi per la solució? Llegeix-la bé abans per entendre-la!')) {
          cm ? cm.setValue(r.codi) : (textarea.value = r.codi);
        }
      });
    } catch (e) {
      toast(e.message);
    }
  });

  // ---------- Tutor ----------
  const chat = $('chat');
  const input = $('chat-input');
  chat.scrollTop = chat.scrollHeight;

  function addMsg(cls, html) {
    const d = document.createElement('div');
    d.className = 'msg ' + cls;
    d.innerHTML = html;
    chat.appendChild(d);
    chat.scrollTop = chat.scrollHeight;
    return d;
  }

  async function ask(text) {
    addMsg('user', esc(text || '(sense text) Ajuda\'m amb aquest pas').replace(/\n/g, '<br>'));
    const typing = addMsg('assistant typing', 'El tutor està mirant el teu codi…');
    try {
      const r = await api('tutor', {
        lab: labId, step, message: text, code: getCode(),
        args: lastRun.args, stdout: lastRun.stdout, stderr: lastRun.stderr, proves: lastTests,
      });
      typing.remove();
      if (r.avis) addMsg('notice', esc(r.avis));
      addMsg('assistant', r.html);
    } catch (e) {
      typing.remove();
      addMsg('notice', esc(e.message));
    }
  }

  $('chat-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const text = input.value.trim();
    input.value = '';
    ask(text);
  });
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      $('chat-form').requestSubmit();
    }
  });
  document.querySelectorAll('[data-quick]').forEach((b) => b.addEventListener('click', () => ask(b.dataset.quick)));
})();
