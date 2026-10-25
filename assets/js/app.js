/* PHPLab — funcions comunes: crides a l'API, editor CodeMirror, sortida i XP. */
'use strict';

const PHPLab = (() => {
  const csrf = document.querySelector('meta[name="csrf"]')?.content || '';

  /** Crida api.php?a=accio amb JSON i el token CSRF. Llança un Error amb el missatge del servidor. */
  async function api(action, data = {}) {
    const res = await fetch('api.php?a=' + encodeURIComponent(action), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify(data),
      credentials: 'same-origin',
    });
    let json;
    try { json = await res.json(); } catch { throw new Error('Resposta no vàlida del servidor (' + res.status + ')'); }
    if (!res.ok || json.error) {
      if (res.status === 401) { location.href = 'index.php?p=login'; }
      throw new Error(json.error || 'Error ' + res.status);
    }
    if (json.insignies?.length) showBadges(json.insignies);
    return json;
  }

  /** Avís d'insígnia nova (després del de l'XP, una darrere l'altra). */
  function showBadges(list) {
    const box = document.getElementById('badge-pop');
    if (!box) return;
    // La primera vegada se'n poden aconseguir moltes de cop (feina feta abans de les insígnies): no les ensenyem totes
    if (list.length > 3) list = [...list.slice(0, 2), { icona: '🏆', titol: 'I ' + (list.length - 2) + ' insígnies més', descripcio: 'Mira-les totes a la pàgina Progrés.' }];
    list.forEach((b, i) => setTimeout(() => {
      box.innerHTML = '<span class="badge-pop-icon">' + esc(b.icona) + '</span><div><small>Insígnia nova!</small>' +
        '<strong>' + esc(b.titol) + '</strong><span>' + esc(b.descripcio) + '</span></div>';
      box.classList.remove('show');
      void box.offsetWidth;   // reinicia l'animació
      box.classList.add('show');
      clearTimeout(box._timer);
      box._timer = setTimeout(() => box.classList.remove('show'), 4200);
    }, 1800 + i * 4600));
  }

  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  function toast(text) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = text;
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 2600);
  }

  /** Actualitza la barra d'XP de la capçalera. */
  function updateProgress(p, gained = 0) {
    if (!p) return;
    const oldLevel = +document.getElementById('p-level').textContent;
    document.getElementById('p-level').textContent = p.level;
    document.getElementById('p-xp').textContent = p.xp;
    document.getElementById('p-streak').textContent = p.streak;
    document.getElementById('p-bar').style.width = Math.round((p.into * 100) / p.needed) + '%';
    if (gained > 0) toast('+' + gained + ' XP');
    if (p.level > oldLevel) {
      document.getElementById('player').classList.add('bump');
      setTimeout(() => toast('🎉 Has pujat al nivell ' + p.level + ': ' + p.name + '!'), 900);
    }
  }

  // Paraules per a l'autocompletat: funcions i paraules clau que surten al temari
  const WORDS = ('echo print print_r var_dump printf sprintf strlen strtolower strtoupper ucfirst ucwords str_repeat str_pad ' +
    'str_replace str_contains str_starts_with str_ends_with substr strpos strrev trim ltrim rtrim explode implode str_split ' +
    'mb_strlen mb_strtoupper mb_strtolower mb_substr count array_push array_pop array_shift array_unshift in_array array_search ' +
    'array_keys array_values array_merge array_slice array_sum array_map array_filter array_reduce array_key_exists sort rsort ' +
    'asort arsort ksort krsort usort range is_numeric is_int is_float is_string is_array is_bool isset empty unset intval floatval ' +
    'intdiv round floor ceil abs max min pow sqrt rand random_int number_format date time mktime checkdate readline fopen fgets ' +
    'fwrite fclose feof file file_exists file_get_contents file_put_contents json_encode json_decode define ctype_digit ' +
    'ctype_alpha filter_var htmlspecialchars function return foreach for while do if else elseif switch case break continue ' +
    'default match exit const global static true false null array PHP_EOL PHP_INT_MAX $argv $argc $_GET $_POST try catch finally throw').split(' ');

  /** Autocompletat: paraules de la llista + variables que ja has escrit al codi. */
  function hint(cm) {
    const cur = cm.getCursor();
    const line = cm.getLine(cur.line);
    let start = cur.ch;
    while (start > 0 && /[\w$]/.test(line.charAt(start - 1))) start--;
    const word = line.slice(start, cur.ch);
    if (word.length < 1) return null;
    const vars = new Set(cm.getValue().match(/\$[A-Za-z_]\w*/g) || []);
    const all = [...new Set([...WORDS, ...vars])];
    const list = all.filter((w) => w.toLowerCase().startsWith(word.toLowerCase()) && w !== word).sort();
    return list.length ? { list, from: CodeMirror.Pos(cur.line, start), to: CodeMirror.Pos(cur.line, cur.ch) } : null;
  }

  /** Converteix un <textarea> en un editor de PHP. */
  function makeEditor(textarea, extraKeys = {}) {
    if (typeof CodeMirror === 'undefined') return null; // sense Internet: queda el textarea normal
    const cm = CodeMirror.fromTextArea(textarea, {
      mode: 'application/x-httpd-php',
      lineNumbers: true,
      indentUnit: 4,
      tabSize: 4,
      indentWithTabs: false,
      matchBrackets: true,
      autoCloseBrackets: true,
      viewportMargin: Infinity,
      extraKeys: Object.assign({
        'Ctrl-Space': (c) => c.showHint({ hint, completeSingle: false }),
        Tab: (c) => (c.somethingSelected() ? c.indentSelection('add') : c.replaceSelection('    ')),
      }, extraKeys),
    });
    cm.on('inputRead', (c, change) => {
      if (/[\w$]/.test(change.text[0]) && !c.state.completionActive) {
        const cur = c.getCursor();
        const before = c.getLine(cur.line).slice(0, cur.ch);
        if (/[\w$]{2,}$/.test(before)) c.showHint({ hint, completeSingle: false });
      }
    });
    return cm;
  }

  /** Llista de fitxers (nom, mida i contingut) que ha deixat el programa. */
  function filesHtml(files, title = '📁 Fitxers del programa') {
    if (!files || !files.length) return '';
    let html = '<details class="files"><summary>' + esc(title) + ' (' + files.length + ')</summary>';
    for (const f of files) {
      html += '<div class="file"><div class="file-name">' + esc(f.nom) + (f.nom.endsWith('/') ? '' : ' <span class="muted">· ' + f.size + ' bytes</span>') + '</div>';
      if (f.contingut !== null && !f.nom.endsWith('/')) {
        html += '<pre>' + (f.contingut === '' ? '<span class="out-empty">(buit)</span>' : esc(f.contingut)) + '</pre>';
      } else if (!f.nom.endsWith('/')) {
        html += '<div class="muted small">(fitxer binari)</div>';
      }
      html += '</div>';
    }
    return html + '</details>';
  }

  /** Pinta el resultat d'una execució: sortida + errors explicats en català (+ fitxers, si showFiles). */
  function renderOutput(box, r, cm, showFiles = true) {
    box.hidden = false;
    const meta = [];
    if (r.timeout) meta.push('⏱ aturat per temps');
    else if (r.exit_code !== null && r.exit_code !== undefined) meta.push('codi de sortida ' + r.exit_code);
    if (r.ms) meta.push(r.ms + ' ms');
    let html = '<div class="out-meta">' + meta.map(esc).join(' · ') + '</div>';
    html += r.stdout ? '<pre>' + esc(r.stdout) + '</pre>' : '<div class="out-empty">(el programa no ha escrit res)</div>';
    for (const ex of r.explicacions || []) {
      html += '<div class="explain ' + esc(ex.tipus) + '"><strong>' + (ex.linia ? 'Línia ' + ex.linia + ' · ' : '') + esc(ex.titol) +
        '</strong><br>' + esc(ex.explicacio) + (ex.original ? '<div class="orig">' + esc(ex.original) + '</div>' : '') + '</div>';
    }
    if (!r.explicacions?.length && r.stderr) html += '<div class="explain"><pre>' + esc(r.stderr) + '</pre></div>';
    if (showFiles) html += filesHtml(r.files);
    if (r.avis_fitxers) html += '<div class="explain avis">' + esc(r.avis_fitxers) + '</div>';
    box.innerHTML = html;
    if (cm) markErrorLines(cm, r.explicacions || []);
  }

  function markErrorLines(cm, explicacions) {
    (cm._errLines || []).forEach((l) => cm.removeLineClass(l, 'background', 'cm-error-line'));
    cm._errLines = [];
    for (const ex of explicacions) {
      if (ex.linia && ex.linia <= cm.lineCount()) {
        cm._errLines.push(cm.addLineClass(ex.linia - 1, 'background', 'cm-error-line'));
      }
    }
  }

  /** Mostra com quedaria $argv amb els arguments escrits. */
  function argvPreview(fitxer, argsText) {
    const args = [];
    let cur = '', q = null, inArg = false;
    for (const c of argsText) {
      if (q) { if (c === q) q = null; else cur += c; }
      else if (c === '"' || c === "'") { q = c; inArg = true; }
      else if (/\s/.test(c)) { if (inArg) { args.push(cur); cur = ''; inArg = false; } }
      else { cur += c; inArg = true; }
    }
    if (inArg) args.push(cur);
    const all = [fitxer, ...args];
    return '$argv = [' + all.map((a) => JSON.stringify(a)).join(', ') + ']   $argc = ' + all.length;
  }

  // Arrossegar fitxers a la zona de pujada del temari
  document.querySelectorAll('.drop').forEach((d) => {
    ['dragenter', 'dragover'].forEach((ev) => d.addEventListener(ev, (e) => { e.preventDefault(); d.classList.add('over'); }));
    ['dragleave', 'drop'].forEach((ev) => d.addEventListener(ev, () => d.classList.remove('over')));
    d.addEventListener('drop', (e) => { e.preventDefault(); d.querySelector('input').files = e.dataTransfer.files; });
  });
  document.getElementById('upload-form')?.addEventListener('submit', (e) => {
    e.submitter?.classList.add('loading');
    if (e.submitter) e.submitter.textContent = 'Pujant i indexant…';
  });

  return { api, esc, toast, updateProgress, makeEditor, renderOutput, markErrorLines, argvPreview, filesHtml };
})();
