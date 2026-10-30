/* PHPLab — reptes: contrarellotge, troba l'error, què imprimeix, ordena el codi i repàs espaciat. */
'use strict';

(() => {
  const root = document.getElementById('repte');
  if (!root) return;
  const { api, esc, toast, updateProgress } = PHPLab;

  const card = document.getElementById('r-card');
  const st = { session: null, items: [], idx: 0, encerts: 0, combo: 0, temps: null, deadline: null, timer: null, finished: false };
  const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /** Compte enrere «3, 2, 1, Som-hi!» abans de començar (no al repàs, que és tranquil). */
  function countdown() {
    if (calm || root.dataset.mode === 'repas') return Promise.resolve();
    return new Promise((resolve) => {
      const passos = ['3', '2', '1', 'Som-hi!'];
      let i = 0;
      const pas = () => {
        if (i >= passos.length) { resolve(); return; }
        card.innerHTML = '<div class="countdown" aria-live="assertive"><span class="' + (i === 3 ? 'go' : '') + '">' + passos[i] + '</span></div>';
        i++;
        setTimeout(pas, i === 4 ? 500 : 650);
      };
      pas();
    });
  }

  // ---------- Inici de la partida ----------
  async function start() {
    // El compte enrere va abans de crear la partida: així no es menja temps del rellotge
    await countdown();
    try {
      const r = await api('repte_start', { mode: root.dataset.mode, tema: root.dataset.tema || null });
      Object.assign(st, { session: r.session, items: r.items, temps: r.temps });
      if (r.temps) {
        st.deadline = Date.now() + r.temps * 1000;
        st.timer = setInterval(tick, 250);
        tick();
      }
      show();
    } catch (e) {
      card.innerHTML = '<p>' + esc(e.message) + '</p><a class="btn" href="index.php?p=reptes">Tornar als reptes</a>';
    }
  }

  function tick() {
    const left = Math.max(0, st.deadline - Date.now());
    document.getElementById('r-timer').textContent = '⏱ ' + Math.ceil(left / 1000) + ' s';
    document.getElementById('r-timebar').style.width = (left / (st.temps * 1000)) * 100 + '%';
    document.getElementById('r-timer').classList.toggle('urgent', left < 20000);
    if (left <= 0 && !st.finished) {
      toast('S\'ha acabat el temps!');
      finish();
    }
  }

  function updateHeader() {
    document.getElementById('r-progress').textContent = Math.min(st.idx + 1, st.items.length) + ' / ' + st.items.length;
    document.getElementById('r-encerts').textContent = st.encerts + ' encert' + (st.encerts === 1 ? '' : 's');
  }

  /** Comptador de combo: apareix a partir de 2 encerts seguits i «salta» a cada encert. */
  function updateCombo() {
    const el = document.getElementById('r-combo');
    el.hidden = st.combo < 2;
    if (st.combo < 2) return;
    el.textContent = '🔥 Combo ×' + st.combo;
    el.classList.toggle('calent', st.combo >= 4);
    el.classList.remove('salta'); void el.offsetWidth; el.classList.add('salta');
  }

  // ---------- Pintar una pregunta ----------
  const codeBlock = (code) => (code ? '<pre class="code">' + esc(code) + '</pre>' : '');

  function show() {
    delete card.dataset.answered;   // la targeta es reaprofita per a cada pregunta
    updateHeader();
    const it = st.items[st.idx];
    let html = '<div class="item-meta small muted">' + esc(it.tema) + (it.leccio ? ' · ' + esc(it.leccio) : '') +
      (it.errata ? ' <span class="pill err">Errata real: ' + esc(it.errata) + '</span>' : '') + '</div>';
    const pregunta = it.pregunta || (it.tipus === 'error' ? 'On és l\'error? Fes clic a la línia.' : '');
    html += '<p class="quiz-q">' + pregunta + '</p>';   // text de confiança (escrit al contingut de l'app)
    if (it.args) html += '<p class="small muted">S\'executa amb: <code>php main.php ' + esc(it.args) + '</code></p>';

    switch (it.tipus) {
      case 'test':
        html += codeBlock(it.codi) + '<div class="options">' +
          it.opcions.map((o, k) => '<button type="button" class="option" data-value="' + k + '">' + esc(o) + '</button>').join('') + '</div>';
        break;
      case 'sortida':
        html += codeBlock(it.codi) + '<div class="answer-row"><textarea class="answer" rows="2" placeholder="Escriu exactament el que sortiria" spellcheck="false"></textarea>' +
          '<button type="button" class="btn primary send">Comprova</button></div>';
        break;
      case 'completa':
        html += codeBlock(it.codi) + '<div class="answer-row"><input class="answer" placeholder="Què hi va a ___ ?" spellcheck="false" autocomplete="off">' +
          '<button type="button" class="btn primary send">Comprova</button></div>';
        break;
      case 'error':
        html += '<ol class="code-lines">' + it.codi.split('\n').map((l, k) =>
          '<li data-line="' + (k + 1) + '"><span class="ln">' + (k + 1) + '</span><code>' + (esc(l) || ' ') + '</code></li>').join('') + '</ol>' +
          '<button type="button" class="btn primary send" disabled>Aquesta és la línia</button>';
        break;
      case 'ordenar':
        html += '<ol class="sortable' + (it.codi_ordenar ? ' code-sort' : '') + '">' + it.linies.map((l) =>
          '<li data-idx="' + l.idx + '"><span class="sort-text">' + esc(l.text) + '</span>' +
          '<span class="sort-buttons"><button type="button" class="btn small up" aria-label="Amunt">↑</button>' +
          '<button type="button" class="btn small down" aria-label="Avall">↓</button></span></li>').join('') + '</ol>' +
          '<button type="button" class="btn primary send">Comprova l\'ordre</button>';
        break;
    }
    html += '<div class="feedback" hidden></div><div class="next-row" hidden><button type="button" class="btn primary next">Següent →</button></div>';
    card.innerHTML = html;
    bind(it);
  }

  function bind(it) {
    const send = card.querySelector('.send');
    if (it.tipus === 'test') {
      card.querySelectorAll('.option').forEach((b) => b.addEventListener('click', () => submit(it, b.dataset.value)));
    }
    if (it.tipus === 'sortida' || it.tipus === 'completa') {
      const input = card.querySelector('.answer');
      input.focus();
      send.addEventListener('click', () => input.value.trim() !== '' && submit(it, input.value));
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && (it.tipus === 'completa' || e.ctrlKey)) { e.preventDefault(); send.click(); }
      });
    }
    if (it.tipus === 'error') {
      let chosen = null;
      card.querySelectorAll('.code-lines li').forEach((li) => li.addEventListener('click', () => {
        if (card.dataset.answered) return;
        card.querySelectorAll('.code-lines li').forEach((x) => x.classList.remove('chosen'));
        li.classList.add('chosen');
        chosen = li.dataset.line;
        send.disabled = false;
      }));
      send.addEventListener('click', () => chosen && submit(it, chosen));
    }
    if (it.tipus === 'ordenar') {
      const list = card.querySelector('.sortable');
      list.addEventListener('click', (e) => {
        const li = e.target.closest('li');
        if (!li || card.dataset.answered) return;
        if (e.target.classList.contains('up') && li.previousElementSibling) list.insertBefore(li, li.previousElementSibling);
        if (e.target.classList.contains('down') && li.nextElementSibling) list.insertBefore(li.nextElementSibling, li);
      });
      send.addEventListener('click', () => submit(it, [...list.children].map((li) => li.dataset.idx).join(',')));
    }
  }

  // ---------- Respondre ----------
  async function submit(it, answer) {
    if (card.dataset.answered || st.finished) return;
    card.dataset.answered = '1';
    card.querySelectorAll('button.send, .option').forEach((b) => (b.disabled = true));
    try {
      const r = await api('repte_answer', { session: st.session, item: it.ref, answer });
      if (r.correcte) st.encerts++;
      st.combo = r.correcte ? st.combo + 1 : 0;
      updateHeader();
      updateCombo();
      showFeedback(it, answer, r);
    } catch (e) {
      toast(e.message);
      if (/temps/.test(e.message)) finish();
    }
  }

  function showFeedback(it, answer, r) {
    const fb = card.querySelector('.feedback');
    let html = (r.correcte ? '✔ Correcte! ' : '✖ No és correcte. ') + esc(r.explicacio);
    if (it.tipus === 'test') {
      card.querySelectorAll('.option').forEach((b) => {
        if (+b.dataset.value === r.correcta) b.classList.add('right');
        else if (b.dataset.value === String(answer)) b.classList.add('wrong');
      });
    }
    if ((it.tipus === 'sortida' || it.tipus === 'completa') && !r.correcte) {
      html += '<div class="small">Resposta correcta:</div><pre class="code">' + esc(r.resposta) + '</pre>';
    }
    if (it.tipus === 'error') {
      card.querySelectorAll('.code-lines li').forEach((li) => {
        if (+li.dataset.line === r.linia) li.classList.add('right');
        else if (li.dataset.line === String(answer)) li.classList.add('wrong');
      });
      if (r.corregit) html += '<div class="small">Codi corregit:</div><pre class="code">' + esc(r.corregit) + '</pre>';
    }
    if (it.tipus === 'ordenar' && !r.correcte) {
      html += '<div class="small">Un ordre correcte:</div><pre class="code">' + esc(r.ordre.join('\n')) + '</pre>';
    }
    if (!r.correcte) html += '<div class="small muted">🔁 Aquesta pregunta tornarà a sortir al repàs.</div>';
    fb.innerHTML = html;
    fb.className = 'feedback ' + (r.correcte ? 'right' : 'wrong');
    fb.hidden = false;
    if (r.correcte) PHPLab.celebrate(card.querySelector('.option.right, .code-lines li.right') || fb, 18);
    else PHPLab.shake(card);

    const nextRow = card.querySelector('.next-row');
    nextRow.hidden = false;
    const next = nextRow.querySelector('.next');
    if (st.idx + 1 >= st.items.length) next.textContent = 'Veure el resultat';
    next.focus();
    next.addEventListener('click', () => {
      st.idx++;
      st.idx >= st.items.length ? finish() : show();
    });
  }

  // ---------- Final ----------
  async function finish() {
    if (st.finished) return;
    st.finished = true;
    clearInterval(st.timer);
    try {
      const r = await api('repte_finish', { session: st.session });
      const pct = r.total ? Math.round((r.encerts * 100) / r.total) : 0;
      const tema = root.dataset.tema ? '&tema=' + encodeURIComponent(root.dataset.tema) : '';
      // Carta de resultat: rang segons el percentatge d'encerts
      const rang = pct === 100 ? 'S' : pct >= 80 ? 'A' : pct >= 60 ? 'B' : pct >= 40 ? 'C' : 'D';
      const temps = Math.floor(r.segons / 60) + ':' + String(r.segons % 60).padStart(2, '0');
      const stat = (valor, etiqueta) => '<div><b>' + valor + '</b><span>' + etiqueta + '</span></div>';
      card.innerHTML = '<div class="result">' +
        '<div class="result-card rang-' + rang + '">' +
        '<div class="rc-top"><span class="rc-mode">' + esc(root.dataset.titol) + '</span><span class="rc-rang" title="Rang">' + rang + '</span></div>' +
        '<div class="result-score">' + r.score + '<small> punts</small></div>' +
        (r.record ? '<div class="pill ok">🏆 Nou rècord!</div>' : '<div class="small muted">Millor puntuació: ' + r.millor + '</div>') +
        '<div class="rc-stats">' + stat(r.encerts + '/' + r.total, 'encerts') + stat('×' + r.combo_max, 'millor combo') +
        stat(temps, 'temps') + stat('+' + r.xp, 'XP') + '</div></div>' +
        (r.limit_xp ? '<p class="small muted">Has arribat al límit d\'XP diari dels reptes. Pots continuar practicant, però sense XP fins demà.</p>' : '') +
        (r.repas_pendent ? '<p>🔁 Tens <strong>' + r.repas_pendent + '</strong> ' + (r.repas_pendent > 1 ? 'preguntes' : 'pregunta') + ' per repassar avui.</p>' : '') +
        '<div class="result-actions">' +
        (root.dataset.mode === 'repas' ? '' : '<a class="btn primary" href="index.php?p=repte&mode=' + root.dataset.mode + tema + '">Torna-hi</a>') +
        (r.repas_pendent && root.dataset.mode !== 'repas' ? '<a class="btn" href="index.php?p=repte&mode=repas">Repassar</a>' : '') +
        '<a class="btn" href="index.php?p=reptes">Tornar als reptes</a></div></div>';
      document.getElementById('r-combo').hidden = true;
      document.getElementById('r-progress').textContent = 'Acabat';
      // La puntuació compta des de 0 i, si és rècord o partida perfecta, confeti
      const score = card.querySelector('.result-score');
      const target = r.score;
      score.firstChild.textContent = '0';
      const t0 = performance.now();
      const tick = (t) => {
        const k = Math.min(1, (t - t0) / 900);
        score.firstChild.textContent = Math.round(target * (1 - Math.pow(1 - k, 3)));
        if (k < 1) requestAnimationFrame(tick);
        else if (r.record || (r.total && r.encerts === r.total)) PHPLab.celebrate(score, 50);
      };
      requestAnimationFrame(tick);
      updateProgress(r.progress, r.xp, score);
    } catch (e) {
      card.innerHTML = '<p>' + esc(e.message) + '</p>';
    }
  }

  start();
})();
