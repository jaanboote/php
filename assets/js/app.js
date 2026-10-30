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

  const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /**
   * Actualitza la barra d'XP de la capçalera.
   * Si es passa l'element que ha fet guanyar l'XP (from), el «+N XP» surt d'allà i vola fins a la barra.
   */
  function updateProgress(p, gained = 0, from = null) {
    if (!p) return;
    const oldLevel = +document.getElementById('p-level').textContent;
    const apply = () => {
      document.getElementById('p-level').textContent = p.level;
      countTo(document.getElementById('p-xp'), p.xp);
      document.getElementById('p-streak').textContent = p.streak;
      document.getElementById('p-bar').style.width = Math.round((p.into * 100) / p.needed) + '%';
      const player = document.getElementById('player');
      player.classList.remove('gain'); void player.offsetWidth; player.classList.add('gain');
    };
    if (gained > 0 && from && !calm) flyXp(from, gained, apply);
    else { apply(); if (gained > 0) toast('+' + gained + ' XP'); }
    if (p.level > oldLevel) {
      document.getElementById('player').classList.add('bump');
      setTimeout(() => levelUp(p), 1100);
    }
  }

  /** Fa pujar un número de mica en mica fins al valor nou. */
  function countTo(el, to) {
    const from = +el.textContent || 0;
    if (calm || from === to) { el.textContent = to; return; }
    const t0 = performance.now(), dur = 700;
    const step = (t) => {
      const k = Math.min(1, (t - t0) / dur);
      el.textContent = Math.round(from + (to - from) * (1 - Math.pow(1 - k, 3)));
      if (k < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  /** «+N XP» que surt de l'element i vola fins al comptador d'XP de la capçalera. */
  function flyXp(from, amount, done) {
    const a = from.getBoundingClientRect();
    const b = document.getElementById('p-xp').getBoundingClientRect();
    const chip = document.createElement('div');
    chip.className = 'xp-fly';
    chip.textContent = '+' + amount + ' XP';
    document.body.appendChild(chip);
    const x0 = a.left + a.width / 2 - chip.offsetWidth / 2, y0 = a.top - chip.offsetHeight - 6;
    chip.style.transform = `translate(${x0}px, ${y0 + 12}px) scale(.7)`;
    chip.style.opacity = '0';
    requestAnimationFrame(() => {
      chip.style.transition = 'transform .35s cubic-bezier(.34,1.56,.64,1), opacity .2s';
      chip.style.transform = `translate(${x0}px, ${y0}px) scale(1.1)`;
      chip.style.opacity = '1';
      setTimeout(() => {
        chip.style.transition = '';
        chip.style.transform = `translate(${b.left + b.width / 2 - chip.offsetWidth / 2}px, ${b.top}px) scale(.5)`;
        chip.style.opacity = '0';
      }, 650);
    });
    setTimeout(() => { chip.remove(); done(); }, 1500);
  }

  /** Confeti que esclata des d'un element (encerts, passos superats, rècords). */
  function celebrate(el, n = 28) {
    if (calm || !el) return;
    const r = el.getBoundingClientRect();
    const css = getComputedStyle(document.documentElement);
    const colors = ['--accent', '--spark', '--ok', '--t1', '--t4'].map((v) => css.getPropertyValue(v).trim());
    for (let i = 0; i < n; i++) {
      const c = document.createElement('i');
      c.className = 'confetti';
      const ang = (Math.PI * 2 * i) / n + Math.random() * .5;
      const dist = 70 + Math.random() * 110;
      c.style.setProperty('--x0', r.left + r.width / 2 + 'px');
      c.style.setProperty('--y0', r.top + Math.min(r.height / 2, 40) + 'px');
      c.style.setProperty('--dx', Math.cos(ang) * dist + 'px');
      c.style.setProperty('--dy', Math.sin(ang) * dist * .7 + 60 + Math.random() * 60 + 'px');
      c.style.setProperty('--rot', (Math.random() * 720 - 360) + 'deg');
      c.style.background = colors[i % colors.length];
      if (i % 3 === 0) c.style.borderRadius = '50%';
      document.body.appendChild(c);
      setTimeout(() => c.remove(), 1200);
    }
  }

  /** Sacseja un element (resposta incorrecta). */
  function shake(el) {
    if (!el) return;
    el.classList.remove('shake'); void el.offsetWidth; el.classList.add('shake');
    setTimeout(() => el.classList.remove('shake'), 500);
  }

  /** Pantalla de «Has pujat de nivell!». Es tanca amb un clic, Escape o sola al cap d'uns segons. */
  function levelUp(p) {
    const box = document.createElement('div');
    box.className = 'level-up';
    box.setAttribute('role', 'alertdialog');
    box.setAttribute('aria-label', 'Has pujat al nivell ' + p.level);
    box.innerHTML = '<div class="level-up-card"><small>Has pujat de nivell!</small><div class="level-up-num">' + esc(p.level) + '</div>' +
      '<div class="level-up-name">' + esc(p.name) + '</div><button class="btn primary" type="button">Som-hi!</button></div>';
    const tpl = document.getElementById('tpl-mascota');
    if (tpl) box.querySelector('.level-up-card').prepend(tpl.content.cloneNode(true));
    document.body.appendChild(box);
    const btn = box.querySelector('button');
    btn.focus();
    setTimeout(() => celebrate(box.querySelector('.level-up-num'), 44), 250);
    const close = () => { box.classList.add('out'); setTimeout(() => box.remove(), 300); document.removeEventListener('keydown', onKey); };
    const onKey = (e) => { if (e.key === 'Escape' || e.key === 'Enter') close(); };
    box.addEventListener('click', close);
    document.addEventListener('keydown', onKey);
    setTimeout(() => box.isConnected && close(), 6000);
  }

  /** Inici: el terminal «escriu» la salutació línia a línia i l'anell d'XP s'omple. */
  function introHome() {
    const ring = document.querySelector('.ring-fg[data-offset]');
    if (ring) setTimeout(() => { ring.style.strokeDashoffset = ring.dataset.offset; }, calm ? 0 : 500);
    const term = document.getElementById('term');
    if (!term || calm) return;
    const lines = [...term.querySelectorAll('[data-type]')];
    const texts = lines.map((line) => {
      const nodes = [];
      const walker = document.createTreeWalker(line, NodeFilter.SHOW_TEXT);
      while (walker.nextNode()) nodes.push({ node: walker.currentNode, text: walker.currentNode.textContent });
      nodes.forEach((n) => { n.node.textContent = ''; });
      return nodes;
    });
    term.classList.add('typing');
    let li = 0;
    const nextLine = () => {
      if (li >= lines.length) { term.classList.remove('typing'); return; }
      const line = lines[li], nodes = texts[li];
      line.classList.add('typed');
      const isCommand = line.classList.contains('prompt');
      let ni = 0, ci = 0;
      const tick = () => {
        if (ni >= nodes.length) { li++; setTimeout(nextLine, isCommand ? 320 : 140); return; }
        const n = nodes[ni];
        // L'usuari@phplab surt de cop (és el «prompt»); l'ordre i les respostes s'escriuen
        if (isCommand && n.node.parentElement.tagName === 'B') { n.node.textContent = n.text; ni++; tick(); return; }
        ci += isCommand ? 1 : 3;
        n.node.textContent = n.text.slice(0, ci);
        if (ci >= n.text.length) { ni++; ci = 0; }
        setTimeout(tick, isCommand ? 30 + Math.random() * 30 : 12);
      };
      tick();
    };
    setTimeout(nextLine, 350);
  }
  introHome();

  // ---------- Efectes de la interfície ----------

  // Llum que segueix el ratolí per sobre de les targetes (només hi posa la posició; el dibuix és CSS)
  const LIT = '.card, .block, .stat-tile, .mode, .inspector, .lab-steps, .lab-editor, .lab-tutor, .badge';
  if (!calm && window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll(LIT).forEach((el) => el.classList.add('lit'));
    document.addEventListener('pointermove', (e) => {
      const el = e.target.closest?.('.lit');
      if (!el) return;
      const r = el.getBoundingClientRect();
      el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      el.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  }

  // Transició entre pàgines: la targeta on fas clic «creix» fins a la capçalera de la pàgina nova
  // (View Transitions entre documents; els navegadors que no en saben simplement canvien de pàgina).
  document.addEventListener('click', (e) => {
    const a = e.target.closest?.('a.item, a.mode, .cami-node a');
    if (a && !e.ctrlKey && !e.metaKey && !e.shiftKey) a.style.viewTransitionName = 'cap-pagina';
  });
  window.addEventListener('pageshow', () => {
    document.querySelectorAll('[style*="view-transition-name"]').forEach((el) => { el.style.viewTransitionName = ''; });
  });

  // Barra de progrés de lectura (lliçons)
  const readBar = document.getElementById('read-progress');
  if (readBar) {
    const upd = () => {
      const max = document.documentElement.scrollHeight - innerHeight;
      readBar.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, scrollY / max) : 1) + ')';
    };
    addEventListener('scroll', upd, { passive: true });
    upd();
  }

  // Botó per copiar els blocs de codi de les explicacions
  document.querySelectorAll('.lesson pre.code, .lesson pre.term, .step-body pre').forEach((pre) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'copy-btn';
    btn.textContent = 'Copia';
    btn.setAttribute('aria-label', 'Copia el codi');
    btn.addEventListener('click', async () => {
      const text = [...pre.childNodes].filter((n) => n !== btn).map((n) => n.textContent).join('');
      try {
        await navigator.clipboard.writeText(text);
      } catch {
        // Fora de localhost (http per la IP de la xarxa) no hi ha API del porta-retalls: mètode antic
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
      }
      btn.textContent = 'Copiat ✓';
      setTimeout(() => { btn.textContent = 'Copia'; }, 1500);
    });
    pre.classList.add('has-copy');
    pre.appendChild(btn);
  });

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
    const input = d.querySelector('input');
    // Mostra quants fitxers hi ha triats (el camp natiu està amagat)
    const mostra = () => {
      const n = input.files.length;
      let info = d.querySelector('.drop-triats');
      if (!info) { info = document.createElement('span'); info.className = 'drop-triats'; d.appendChild(info); }
      info.textContent = n ? (n === 1 ? '📄 ' + input.files[0].name : '📄 ' + n + ' fitxers triats') : '';
    };
    input.addEventListener('change', mostra);
    d.addEventListener('drop', (e) => { e.preventDefault(); input.files = e.dataTransfer.files; mostra(); });
  });
  document.getElementById('upload-form')?.addEventListener('submit', (e) => {
    e.submitter?.classList.add('loading', 'keep-text');
    if (e.submitter) e.submitter.textContent = 'Pujant i indexant…';
  });

  return { api, esc, toast, updateProgress, celebrate, shake, makeEditor, renderOutput, markErrorLines, argvPreview, filesHtml };
})();
