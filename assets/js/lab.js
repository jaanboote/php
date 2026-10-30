/* PHPLab — laboratori: editor, executar, comprovar el pas, pistes, solució i tutor. */
'use strict';

(() => {
  const root = document.getElementById('lab');
  if (!root) return;
  const { api, esc, ic, toast, updateProgress, makeEditor, renderOutput, argvPreview, filesHtml } = PHPLab;

  const labId = root.dataset.lab;
  const web = root.dataset.web === '1';   // laboratori web: diversos fitxers i navegador integrat
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
  let cm, getCode, setCode;
  if (!web) {
    cm = makeEditor(textarea, { 'Ctrl-Enter': () => run() });
    getCode = () => (cm ? cm.getValue() : textarea.value);
    setCode = (c) => (cm ? cm.setValue(c) : (textarea.value = c));
  } else {
    // Un document de CodeMirror per fitxer; les pestanyes canvien el document que es veu
    const files = JSON.parse(textarea.value);
    const modeFor = (n) => (/\.css$/.test(n) ? 'css' : /\.js$/.test(n) ? 'javascript' : /\.html?$/.test(n) ? 'htmlmixed' : 'application/x-httpd-php');
    const names = Object.keys(files);
    const edTa = $('code-editor');
    edTa.value = files[names[0]];
    cm = makeEditor(edTa, { 'Ctrl-Enter': () => openWeb() });
    const docs = {};
    if (cm) {
      names.forEach((n, i) => { docs[n] = i === 0 ? cm.getDoc() : CodeMirror.Doc(files[n], modeFor(n)); });
      cm.setOption('mode', modeFor(names[0]));
    }
    let currentFile = names[0];
    document.querySelectorAll('.file-tab').forEach((tab) => tab.addEventListener('click', () => {
      document.querySelectorAll('.file-tab').forEach((t) => t.classList.toggle('active', t === tab));
      if (cm) {
        cm.swapDoc(docs[tab.dataset.file]);
        cm.focus();
      } else {
        files[currentFile] = edTa.value;
        edTa.value = files[tab.dataset.file];
      }
      currentFile = tab.dataset.file;
    }));
    getCode = () => {
      const out = {};
      names.forEach((n) => { out[n] = cm ? docs[n].getValue() : (n === currentFile ? edTa.value : files[n]); });
      return JSON.stringify(out);
    };
    setCode = (c) => {
      const obj = typeof c === 'string' ? JSON.parse(c) : c;
      names.forEach((n) => { if (n in obj) cm ? docs[n].setValue(obj[n]) : (files[n] = obj[n]); });
      if (!cm) edTa.value = files[currentFile];
    };
  }
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
  // Barra d'estat de l'editor: línia i columna del cursor, i quantes línies té el fitxer
  const updateStatus = () => {
    if (!cm || !$('sb-pos')) return;
    const c = cm.getCursor();
    $('sb-pos').textContent = 'Ln ' + (c.line + 1) + ', Col ' + (c.ch + 1);
    $('sb-lines').textContent = cm.lineCount() + ' línies';
  };
  if (cm) {
    cm.on('cursorActivity', updateStatus);
    cm.on('swapDoc', updateStatus);
    updateStatus();
  }
  cm ? cm.on('changes', scheduleSave) : (web ? $('code-editor') : textarea).addEventListener('input', scheduleSave);

  const updateArgv = () => { if (argsInput) $('argv-preview').textContent = argvPreview(fitxer, argsInput.value); };
  argsInput?.addEventListener('input', updateArgv);
  argsInput?.addEventListener('keydown', (e) => { if (e.key === 'Enter') run(); });
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
  $('btn-run')?.addEventListener('click', run);

  // ---------- Navegador integrat (laboratoris web) ----------
  const frame = $('browser');
  let lastInspector = null;
  async function openWeb(path) {
    const btn = $('btn-open');
    path = (path ?? $('br-url').value).trim().replace(/^\/+/, '') || 'index.php';
    busy(btn, true);
    try {
      clearTimeout(saveTimer);
      const r = await api('web_open', { lab: labId, code: getCode(), path });
      $('save-state').textContent = 'desat ✓';
      frame.src = r.url;
    } catch (e) {
      toast(e.message);
    } finally {
      busy(btn, false);
    }
  }
  if (web) {
    $('btn-open').addEventListener('click', () => openWeb());
    $('br-form').addEventListener('submit', (e) => { e.preventDefault(); openWeb(); });
    $('br-home').addEventListener('click', () => { $('br-url').value = 'index.php'; openWeb('index.php'); });
    $('br-reload').addEventListener('click', () => openWeb());
    // Cada cop que el navegador carrega una pàgina (formulari enviat, enllaç, redirecció), actualitzem l'inspector
    frame.addEventListener('load', () => { if (frame.src) refreshInspector(); });
    openWeb('index.php');
  }

  async function refreshInspector() {
    try {
      const r = await api('web_inspector', { lab: labId });
      lastInspector = r;
      if (r.historial.length) $('br-url').value = r.historial[0].path;
      renderInspector(r);
    } catch (e) {
      toast(e.message);
    }
  }

  const table = (rows, empty) => rows.length
    ? '<table class="kv">' + rows.map(([k, v]) => '<tr><th>' + esc(k) + '</th><td>' + v + '</td></tr>').join('') + '</table>'
    : '<p class="muted small">' + empty + '</p>';

  function renderInspector(r) {
    const u = r.ultima;
    let html = '';
    if (u) {
      const st = u.status >= 400 ? 'err' : u.status >= 300 ? 'doing' : 'ok';
      html += '<div class="req-line"><span class="method">' + esc(u.method) + '</span> <code>' + esc(u.path + (u.query ? '?' + u.query : '')) +
        '</code> <span class="pill ' + st + '">' + u.status + '</span>' + (u.location ? ' <span class="small muted">→ ' + esc(u.location) + '</span>' : '') +
        ' <span class="small muted">' + u.ms + ' ms</span></div>';
      for (const ex of u.explicacions || []) {
        html += '<div class="explain' + (/Warning|Notice|Deprecated/.test(ex.original) ? ' avis' : '') + '"><strong>' + esc(ex.titol) + '</strong>' +
          (ex.linia ? ' (línia ' + ex.linia + ')' : '') + '<div>' + esc(ex.explicacio) + '</div><div class="orig">' + esc(ex.original) + '</div></div>';
      }
      html += '<div class="insp-grid">';
      html += '<section><h4>$_GET</h4>' + table(u.get.map(([k, v]) => [k, esc(v)]), 'Buit (no hi ha res a la URL després de «?»)') + '</section>';
      if (u.method === 'POST') {
        html += '<section><h4>$_POST</h4>' + table(u.post.map(([k, v]) => [k, esc(v)]), 'Buit') + '</section>';
        html += '<section><h4>$_FILES</h4>' + table(u.files.map((f) => [f.camp, f.nom ? esc(f.nom) + ' <span class="muted small">' + esc(f.tipus) + ' · ' + Math.ceil(f.mida / 1024) + ' KB</span>' : '<span class="muted">(cap fitxer)</span>']), 'Cap fitxer pujat') + '</section>';
      }
      html += '</div>';
    }
    const cookies = Object.entries(r.galetes || {});
    html += '<details class="insp-more"' + (cookies.length ? ' open' : '') + '><summary>' + ic('galeta') + ' Galetes i sessió</summary>' +
      table(cookies.map(([k, v]) => [k, '<code>' + esc(v) + '</code>']), 'El navegador no té cap galeta.') +
      (r.sessio !== null ? '<div class="small muted">Dades de la sessió al servidor ($_SESSION):</div><pre class="code">' + esc(r.sessio || '(buida)') + '</pre>' : '') +
      '</details>';
    html += '<details class="insp-more"' + (r.fitxers.length ? ' open' : '') + '><summary>' + ic('servidor') + ' Fitxers del servidor (' + r.fitxers.length + ')</summary>' +
      (r.fitxers.length ? '<ul class="server-files">' + r.fitxers.map((f) => '<li><code>' + esc(f.nom) + '</code>' + (f.size ? ' <span class="muted small">' + Math.ceil(f.size / 1024) + ' KB</span>' : '') + '</li>').join('') + '</ul>' : '<p class="muted small">Encara no hi ha cap fitxer (p. ex. les imatges pujades a uploads/).</p>') +
      '<button class="btn small" type="button" id="br-reset">' + ic('paperera') + ' Reinicia el servidor i les galetes</button></details>';
    if (r.historial.length > 1) {
      html += '<details class="insp-more"><summary>' + ic('historial') + ' Peticions anteriors</summary><ol class="history">' +
        r.historial.slice(1).map((h) => '<li><span class="small muted">' + esc(h.hora) + '</span> ' + esc(h.method) + ' <code>' + esc(h.path) + '</code> ' + h.status + '</li>').join('') + '</ol></details>';
    }
    $('inspector').innerHTML = html;
    $('br-reset')?.addEventListener('click', async () => {
      if (!confirm('Esborrar els fitxers pujats, les sessions i les galetes?')) return;
      await api('web_reset', { lab: labId });
      openWeb('index.php');
    });
  }

  // ---------- Fitxers del laboratori (es conserven entre execucions) ----------
  function renderFiles(files) {
    const box = $('files');
    if (!files || !files.length) {
      box.hidden = true;
      box.innerHTML = '';
      return;
    }
    box.hidden = false;
    box.innerHTML = filesHtml(files, 'Carpeta del programa') +
      '<button class="btn small" type="button" id="btn-clear-files">' + ic('paperera') + ' Buidar la carpeta</button>';
    box.querySelector('details').open = true;
    $('btn-clear-files').addEventListener('click', async () => {
      if (!confirm('Esborrar tots els fitxers que ha creat el programa?')) return;
      const r = await api('lab_files', { lab: labId, buidar: true });
      renderFiles(r.files);
      toast('Carpeta buidada');
    });
  }
  if (!web) api('lab_files', { lab: labId }).then((r) => renderFiles(r.files)).catch(() => {});

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
      html += '<div class="banner ok">' + ic('check') + ' Pas superat!' + (r.xp ? ' +' + r.xp + ' XP' : '') +
        (r.acabat
          ? ' <button class="btn primary small" onclick="location.reload()">' + ic('estrelles') + ' Veure el resum</button>'
          : ' <button class="btn primary small" onclick="location.reload()">Següent pas →</button>') + '</div>';
    } else {
      const n = r.proves.filter((t) => !t.ok).length + r.codi.filter((c) => !c.ok).length;
      html += '<div class="banner ko">Encara no: ' + (n > 1 ? n + ' comprovacions fallen' : '1 comprovació falla') +
        '. Mira el detall i, si et cal, pregunta al tutor.</div>';
    }
    for (const c of r.codi) {
      html += '<div class="code-check ' + (c.ok ? 'ok' : 'ko') + '">' + ic(c.ok ? 'check' : 'creu') + ' ' + esc(c.missatge) + '</div>';
    }
    html += '<h3>Proves</h3>';
    r.proves.forEach((t, i) => {
      html += '<details class="test ' + (t.ok ? 'ok' : 'ko') + '" style="--i:' + i + '"' + (t.ok ? '' : ' open') + '><summary>' + ic(t.ok ? 'check' : 'creu') + ' ' +
        esc(t.nom) + (web ? '' : '<span class="test-cmd">$ ' + esc(t.ordre) + '</span>') + '</summary><div class="test-body">' +
        (web ? '<p class="test-steps small">' + ic('llista') + ' ' + esc(t.ordre) + '</p>' : '');
      if (!t.ok) html += '<p><strong>' + esc(t.motiu) + '</strong></p>';
      if (t.esperat !== null && !t.ok) {
        html += '<div class="compare"><div>Esperat<pre>' + esc(t.esperat) + '</pre></div><div>Has mostrat<pre>' + esc(t.obtingut || '(res)') + '</pre></div></div>';
      } else if (web) {
        html += '<div>Text que es veu a la pàgina<pre class="code">' + esc(t.text || '(res)') + '</pre></div>' +
          '<details class="small"><summary>Codi HTML de la resposta</summary><pre class="code">' + esc(t.obtingut || '(res)') + '</pre></details>';
      } else {
        html += '<div>Sortida<pre class="code">' + esc(t.obtingut || '(res)') + '</pre></div>';
      }
      if (t.fitxers_inicials && t.fitxers_inicials.length) {
        html += '<p class="small muted">Fitxers preparats abans d\'executar: ' + t.fitxers_inicials.map(esc).join(', ') + '</p>';
      }
      html += filesHtml(t.fitxers, web ? 'Fitxers del servidor després de la prova' : 'Fitxers després de l\'execució', web ? 'servidor' : 'carpeta');
      for (const ex of t.errors || []) {
        html += '<div class="explain ' + esc(ex.tipus) + '"><strong>' + (ex.linia ? 'Línia ' + ex.linia + ' · ' : '') + esc(ex.titol) + '</strong><br>' + esc(ex.explicacio) + '</div>';
      }
      if (!web) html += '<button class="btn small try" type="button" data-args="' + esc(t.ordre.replace(/^php \S+\s?/, '')) + '">Prova-ho amb aquests arguments</button>';
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
      const blocks = typeof r.codi === 'string' ? '<pre class="code">' + esc(r.codi) + '</pre>'
        : Object.entries(r.codi).map(([n, c]) => '<div class="file-name">' + ic('document') + ' ' + esc(n) + '</div><pre class="code">' + esc(c) + '</pre>').join('');
      box.innerHTML = '<h3>Solució del pas</h3><p class="small">' + esc(r.explicacio) + '</p>' + blocks +
        '<button class="btn small" type="button" id="copy-sol">Posa-la a l\'editor</button>';
      $('copy-sol').addEventListener('click', () => {
        if (confirm('Substituir el teu codi per la solució? Llegeix-la bé abans per entendre-la!')) setCode(r.codi);
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
        args: lastRun.args, stdout: lastRun.stdout, proves: lastTests,
        stderr: web ? (lastInspector?.ultima?.errors || '') : lastRun.stderr,
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
