/* PHPLab — simulacres d'examen: el navegador només recull les respostes; la correcció es fa al servidor en entregar. */
'use strict';

(() => {
  const intro = document.getElementById('examen-intro');
  const root = document.getElementById('examen');
  if (!intro || !root) return;
  const { api, esc, ic, toast, updateProgress, makeEditor, renderOutput } = PHPLab;
  const examenId = root.dataset.examen;
  const st = { intent: null, respostes: {}, fi: 0, timer: null, desaTimer: null, entregant: false, preguntes: 0 };

  document.getElementById('examen-comenca').addEventListener('click', async (e) => {
    const btn = e.currentTarget;
    btn.disabled = true;
    try {
      const r = await api('examen_start', { examen: examenId });
      st.intent = r.intent;
      st.respostes = Array.isArray(r.respostes) ? {} : r.respostes;
      st.fi = Date.now() + r.restant * 1000;
      st.preguntes = r.teoria.length;
      intro.hidden = true;
      document.querySelector('.examen-historial')?.closest('.card')?.setAttribute('hidden', '');
      root.hidden = false;
      pintaTeoria(r.teoria);
      pintaPractica(r.practica);
      compta();
      st.timer = setInterval(rellotge, 1000);
      rellotge();
      window.scrollTo({ top: 0 });
    } catch (err) {
      toast(err.message);
      btn.disabled = false;
    }
  });

  // ---------- Teoria ----------
  const codeBlock = (code) => (code ? '<pre class="code">' + esc(code) + '</pre>' : '');

  function pintaTeoria(items) {
    const box = document.getElementById('ex-teoria');
    box.innerHTML = items.map((it, n) => {
      let html = '<section class="block ex-pregunta" data-ref="' + esc(it.ref) + '" data-tipus="' + esc(it.tipus) + '">' +
        '<p class="correccio-cap"><span class="correccio-num">' + (n + 1) + '</span><span class="small muted">' +
        String(it.punts).replace('.', ',') + ' punts</span></p>' +
        '<p class="quiz-q">' + (it.pregunta || 'On és l\'error? Fes clic a la línia.') + '</p>';   // text de confiança (contingut de l'app)
      if (it.args) html += '<p class="small muted">S\'executa amb: <code>php main.php ' + esc(it.args) + '</code></p>';
      switch (it.tipus) {
        case 'test':
          html += codeBlock(it.codi) + '<div class="options">' +
            it.opcions.map((o, k) => '<button type="button" class="option" data-value="' + k + '">' + esc(o) + '</button>').join('') + '</div>';
          break;
        case 'sortida':
          html += codeBlock(it.codi) + '<textarea class="answer" rows="2" placeholder="Escriu exactament el que sortiria" spellcheck="false"></textarea>';
          break;
        case 'completa':
          html += codeBlock(it.codi) + '<input class="answer" placeholder="Què hi va a ___ ?" spellcheck="false" autocomplete="off">';
          break;
        case 'error':
          html += '<ol class="code-lines">' + it.codi.split('\n').map((l, k) =>
            '<li data-line="' + (k + 1) + '"><span class="ln">' + (k + 1) + '</span><code>' + (esc(l) || ' ') + '</code></li>').join('') + '</ol>';
          break;
        case 'ordenar':
          html += '<ol class="sortable' + (it.codi_ordenar ? ' code-sort' : '') + '">' + it.linies.map((l) =>
            '<li data-idx="' + esc(l.idx) + '"><span class="sort-text">' + esc(l.text) + '</span>' +
            '<span class="sort-buttons"><button type="button" class="btn small up" aria-label="Amunt">↑</button>' +
            '<button type="button" class="btn small down" aria-label="Avall">↓</button></span></li>').join('') + '</ol>';
          break;
      }
      return html + '</section>';
    }).join('');
    box.querySelectorAll('pre.code').forEach(PHPLab.pinta);
    box.querySelectorAll('.ex-pregunta').forEach(lligaPregunta);
  }

  function lligaPregunta(sec) {
    const ref = sec.dataset.ref;
    const desada = st.respostes[ref];
    const respon = (valor) => {
      st.respostes[ref] = valor;
      sec.classList.toggle('respost', valor !== '');
      compta();
      programaDesa();
    };
    switch (sec.dataset.tipus) {
      case 'test': {
        const opcions = sec.querySelectorAll('.option');
        const tria = (b) => { opcions.forEach((x) => x.classList.toggle('chosen', x === b)); };
        opcions.forEach((b) => b.addEventListener('click', () => { tria(b); respon(b.dataset.value); }));
        if (desada !== undefined) tria(sec.querySelector('.option[data-value="' + desada + '"]'));
        break;
      }
      case 'sortida':
      case 'completa': {
        const input = sec.querySelector('.answer');
        if (desada !== undefined) input.value = desada;
        input.addEventListener('input', () => respon(input.value.trim() === '' ? '' : input.value));
        break;
      }
      case 'error': {
        const linies = sec.querySelectorAll('.code-lines li');
        const tria = (li) => linies.forEach((x) => x.classList.toggle('chosen', x === li));
        linies.forEach((li) => li.addEventListener('click', () => { tria(li); respon(li.dataset.line); }));
        if (desada !== undefined) tria(sec.querySelector('li[data-line="' + desada + '"]'));
        break;
      }
      case 'ordenar': {
        const list = sec.querySelector('.sortable');
        // Si ja s'havia ordenat abans de tancar la pestanya, es recupera aquell ordre
        if (desada) desada.split(',').forEach((tok) => { const li = list.querySelector('li[data-idx="' + CSS.escape(tok) + '"]'); if (li) list.appendChild(li); });
        list.addEventListener('click', (e) => {
          const li = e.target.closest('li');
          if (!li) return;
          if (e.target.classList.contains('up') && li.previousElementSibling) list.insertBefore(li, li.previousElementSibling);
          else if (e.target.classList.contains('down') && li.nextElementSibling) list.insertBefore(li.nextElementSibling, li);
          else return;
          respon([...list.children].map((x) => x.dataset.idx).join(','));
        });
        break;
      }
    }
    if (desada !== undefined && desada !== '') sec.classList.add('respost');
  }

  // ---------- Pràctica ----------
  function pintaPractica(programes) {
    const box = document.getElementById('ex-practica');
    box.innerHTML = programes.map((p, n) =>
      '<section class="block ex-programa" data-ref="' + esc(p.ref) + '" data-id="' + esc(p.id) + '" data-fitxer="' + esc(p.fitxer) + '">' +
      '<p class="correccio-cap"><strong>Programa ' + (n + 1) + ': ' + esc(p.titol) + '</strong><span class="small muted">' +
      String(p.punts).replace('.', ',') + ' punts</span></p>' +
      '<div class="ex-enunciat">' + p.enunciat + '</div>' +   // HTML de confiança (contingut de l'app)
      (p.fitxers.length ? '<p class="small muted">' + ic('carpeta') + ' Fitxers que hi ha a la carpeta: ' + p.fitxers.map(esc).join(', ') + '</p>' : '') +
      '<div class="file-tabs"><span class="file-tab active">' + ic('document') + ' ' + esc(p.fitxer) + '</span></div>' +
      '<textarea class="code-src"></textarea>' +
      '<div class="run-bar"><label class="inline">$ php ' + esc(p.fitxer) +
      ' <input class="args" placeholder="arguments" spellcheck="false" autocomplete="off"></label>' +
      '<button class="btn run" type="button">Executa ▶</button></div>' +
      '<details class="stdin-box"><summary>Entrada per a readline() (opcional)</summary><textarea class="stdin" rows="2" spellcheck="false"></textarea></details>' +
      '<div class="output" hidden></div></section>').join('');

    box.querySelectorAll('.ex-programa').forEach((sec, n) => {
      const ref = sec.dataset.ref;
      const ta = sec.querySelector('.code-src');
      ta.value = st.respostes[ref] ?? programes[n].codi_inicial;
      const cm = makeEditor(ta, { 'Ctrl-Enter': () => executa() });
      const valor = () => (cm ? cm.getValue() : ta.value);
      const canvi = () => { st.respostes[ref] = valor(); programaDesa(); };
      if (cm) cm.on('change', canvi); else ta.addEventListener('input', canvi);
      sec.querySelectorAll('.ex-enunciat pre.code').forEach(PHPLab.pinta);

      async function executa() {
        const btn = sec.querySelector('.run');
        btn.classList.add('loading');
        try {
          const r = await api('run', {
            code: valor(), args: sec.querySelector('.args').value, stdin: sec.querySelector('.stdin').value,
            fitxer: sec.dataset.fitxer, examen: examenId, programa: sec.dataset.id,
          });
          renderOutput(sec.querySelector('.output'), r, cm);
        } catch (e) {
          toast(e.message);
        } finally {
          btn.classList.remove('loading');
        }
      }
      sec.querySelector('.run').addEventListener('click', executa);
    });
  }

  // ---------- Desat, rellotge i entrega ----------
  function compta() {
    const fetes = document.querySelectorAll('.ex-pregunta.respost').length;
    document.getElementById('ex-respostes').textContent = fetes + ' / ' + st.preguntes + ' respostes';
  }

  function programaDesa() {
    document.getElementById('ex-desat').textContent = 'Canvis sense desar…';
    clearTimeout(st.desaTimer);
    st.desaTimer = setTimeout(desa, 1500);
  }

  async function desa() {
    if (st.entregant) return;
    try {
      await api('examen_desa', { intent: st.intent, respostes: st.respostes });
      document.getElementById('ex-desat').textContent = 'Desat';
    } catch (e) {
      document.getElementById('ex-desat').textContent = 'No s\'ha pogut desar';
    }
  }

  function rellotge() {
    const restant = Math.max(0, Math.round((st.fi - Date.now()) / 1000));
    const el = document.getElementById('ex-temps');
    el.querySelector('span').textContent = String(Math.floor(restant / 60)).padStart(2, '0') + ':' + String(restant % 60).padStart(2, '0');
    el.classList.toggle('urgent', restant <= 300);
    if (restant === 0) {
      clearInterval(st.timer);
      toast('S\'ha acabat el temps: s\'entrega l\'examen.');
      entrega(true);
    }
  }

  async function entrega(perTemps = false) {
    if (st.entregant) return;
    const falten = st.preguntes - document.querySelectorAll('.ex-pregunta.respost').length;
    if (!perTemps && !confirm(falten > 0
      ? 'Tens ' + falten + (falten === 1 ? ' pregunta' : ' preguntes') + ' sense respondre. Vols entregar l\'examen igualment?'
      : 'Segur que vols entregar l\'examen? Ja no podràs canviar res.')) return;
    st.entregant = true;
    clearTimeout(st.desaTimer);
    document.querySelectorAll('#ex-entrega, #ex-entrega-2').forEach((b) => { b.disabled = true; b.classList.add('loading'); });
    try {
      const r = await api('examen_entrega', { intent: st.intent, respostes: st.respostes });
      clearInterval(st.timer);
      updateProgress(r.progress, r.xp);
      location.href = 'index.php?p=examen&id=' + encodeURIComponent(examenId) + '&intent=' + r.intent;
    } catch (e) {
      toast(e.message);
      st.entregant = false;
      document.querySelectorAll('#ex-entrega, #ex-entrega-2').forEach((b) => { b.disabled = false; b.classList.remove('loading'); });
    }
  }
  document.querySelectorAll('#ex-entrega, #ex-entrega-2').forEach((b) => b.addEventListener('click', () => entrega()));

  // Avisa abans de sortir de la pàgina amb l'examen en curs (les respostes ja es desen, però el rellotge segueix)
  addEventListener('beforeunload', (e) => {
    if (st.intent && !st.entregant) e.preventDefault();
  });
})();
