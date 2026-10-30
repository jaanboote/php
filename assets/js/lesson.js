/* PHPLab — lliçons: exemples "prova-ho" i micro-preguntes. */
'use strict';

(() => {
  const lessonEl = document.querySelector('.lesson');
  if (!lessonEl) return;
  const lessonId = lessonEl.dataset.lesson;

  // ---------- Índex lateral ----------
  const toc = document.querySelector('.toc');
  /** Torna a comptar les preguntes encertades d'una secció i la marca com a feta si ja les té totes. */
  function actualitzaIndex(sec) {
    const li = sec && toc?.querySelector('li[data-sec="' + sec + '"]');
    if (!li) return;
    const total = document.querySelectorAll('[data-sec="' + sec + '"] .quiz').length;
    const fetes = document.querySelectorAll('[data-sec="' + sec + '"] .quiz.done').length;
    const compte = li.querySelector('.toc-compte');
    if (compte) compte.textContent = fetes + '/' + total;
    li.classList.toggle('fet', total > 0 && fetes === total);
  }
  if (toc && 'IntersectionObserver' in window) {
    // La secció actual és la darrera explicació que ha passat per la franja superior de la pantalla
    const enllacos = new Map([...toc.querySelectorAll('li')].map((li) => [li.dataset.sec, li]));
    const visibles = new Set();
    const obs = new IntersectionObserver((entrades) => {
      entrades.forEach((en) => (en.isIntersecting ? visibles.add(en.target.id) : visibles.delete(en.target.id)));
      const ordre = [...enllacos.keys()];
      const actual = ordre.filter((id) => visibles.has(id)).shift();
      if (!actual) return;
      enllacos.forEach((li, id) => li.classList.toggle('actiu', id === actual));
    }, { rootMargin: '-80px 0px -55% 0px' });
    enllacos.forEach((li, id) => { const el = document.getElementById(id); if (el) obs.observe(el); });
  }

  // ---------- Exemples executables ----------
  document.querySelectorAll('.playground').forEach((pg) => {
    const ta = pg.querySelector('.code-src');
    const original = ta.value;
    const run = async () => {
      const btn = pg.querySelector('.run');
      btn.classList.add('loading');
      try {
        const r = await PHPLab.api('run', {
          code: cm ? cm.getValue() : ta.value,
          args: pg.querySelector('.args').value,
          stdin: pg.querySelector('.stdin')?.value || '',
          fitxer: pg.dataset.fitxer,
          lesson: lessonId,
          bloc: +pg.dataset.bloc,
        });
        PHPLab.renderOutput(pg.querySelector('.output'), r, cm);
      } catch (e) {
        PHPLab.toast(e.message);
      } finally {
        btn.classList.remove('loading');
      }
    };
    const cm = PHPLab.makeEditor(ta, { 'Ctrl-Enter': run });
    pg.querySelector('.run').addEventListener('click', run);
    pg.querySelector('.args').addEventListener('keydown', (e) => { if (e.key === 'Enter') run(); });
    pg.querySelector('.reset').addEventListener('click', () => (cm ? cm.setValue(original) : (ta.value = original)));
  });

  // ---------- Preguntes ----------
  async function answer(quiz, value, optionBtn) {
    if (quiz.classList.contains('done')) return;
    try {
      const r = await PHPLab.api('lesson_answer', { lesson: lessonId, q: quiz.dataset.q, answer: value });
      const fb = quiz.querySelector('.feedback');
      fb.hidden = false;
      fb.className = 'feedback ' + (r.correcte ? 'right' : 'wrong');
      if (r.correcte) {
        fb.innerHTML = PHPLab.ic('check') + ' Correcte! ' + PHPLab.esc(r.explicacio);
        quiz.classList.add('done');
        optionBtn?.classList.add('right');
        const c = document.getElementById('lesson-count');
        c.textContent = +c.textContent + 1;
        c.parentElement.classList.remove('bump'); void c.offsetWidth; c.parentElement.classList.add('bump');
        actualitzaIndex(quiz.closest('[data-sec]')?.dataset.sec);
        PHPLab.celebrate(optionBtn || fb, 22);
      } else {
        fb.innerHTML = PHPLab.ic('creu') + ' No és correcte. Torna-ho a provar (pots fer servir els exemples de dalt per comprovar-ho).';
        if (optionBtn) {
          optionBtn.classList.add('wrong');
          setTimeout(() => optionBtn.classList.remove('wrong'), 600);
        } else {
          PHPLab.shake(quiz);
        }
      }
      PHPLab.updateProgress(r.progress, r.xp, optionBtn || fb);
      if (r.leccio_nova) setTimeout(() => celebraLeccio(r.xp_leccio), 1400);
    } catch (e) {
      PHPLab.toast(e.message);
    }
  }

  /**
   * Pantalla de «Lliçó completada!» amb la mascota, l'XP de la lliçó i cap on anar ara
   * (el laboratori per practicar-ho i la lliçó següent). Si s'està mostrant la de pujar de nivell, espera.
   */
  function celebraLeccio(xp) {
    if (document.querySelector('.level-up')) {
      setTimeout(() => celebraLeccio(xp), 700);
      return;
    }
    const art = document.querySelector('.lesson');
    const { esc, ic } = PHPLab;
    let accions = '';
    if (art.dataset.labId) {
      accions += '<a class="btn primary" href="index.php?p=lab&amp;id=' + encodeURIComponent(art.dataset.labId) + '">' + ic('matras') + ' Practica-ho: ' + esc(art.dataset.labTitol) + '</a>';
    }
    if (art.dataset.seguentId) {
      accions += '<a class="btn' + (art.dataset.labId ? '' : ' primary') + '" href="index.php?p=leccio&amp;id=' + encodeURIComponent(art.dataset.seguentId) + '">' + ic('llibre') + ' Següent: ' + esc(art.dataset.seguentTitol) + '</a>';
    }
    accions += '<button class="btn" type="button" data-tanca>Quedar-me aquí</button>';
    const box = document.createElement('div');
    box.className = 'level-up leccio-feta';
    box.setAttribute('role', 'alertdialog');
    box.setAttribute('aria-label', 'Lliçó completada');
    box.innerHTML = '<div class="level-up-card"><small>Lliçó completada!</small>' +
      '<div class="level-up-name">' + esc(art.querySelector('h1').textContent) + '</div>' +
      '<div class="leccio-xp">+' + esc(xp) + ' XP</div><div class="leccio-accions">' + accions + '</div></div>';
    const tpl = document.getElementById('tpl-mascota');
    if (tpl) box.querySelector('.level-up-card').prepend(tpl.content.cloneNode(true));
    document.body.appendChild(box);
    PHPLab.celebrate(box.querySelector('.leccio-xp'), 50);
    box.querySelector('.leccio-accions .btn').focus();
    const tanca = () => { box.classList.add('out'); setTimeout(() => box.remove(), 300); document.removeEventListener('keydown', onKey); };
    const onKey = (e) => { if (e.key === 'Escape') tanca(); };
    box.querySelector('[data-tanca]').addEventListener('click', tanca);
    box.addEventListener('click', (e) => { if (e.target === box) tanca(); });
    document.addEventListener('keydown', onKey);
  }

  document.querySelectorAll('.quiz').forEach((quiz) => {
    quiz.querySelectorAll('.option').forEach((btn) => btn.addEventListener('click', () => answer(quiz, btn.dataset.value, btn)));
    const input = quiz.querySelector('.answer');
    const send = () => input.value.trim() !== '' && answer(quiz, input.value);
    quiz.querySelector('.check')?.addEventListener('click', send);

    // Ordenar: botons ↑ ↓ per moure cada element
    const list = quiz.querySelector('.sortable');
    if (list) {
      list.addEventListener('click', (e) => {
        const li = e.target.closest('li');
        if (!li || quiz.classList.contains('done')) return;
        if (e.target.classList.contains('up') && li.previousElementSibling) list.insertBefore(li, li.previousElementSibling);
        if (e.target.classList.contains('down') && li.nextElementSibling) list.insertBefore(li.nextElementSibling, li);
        li.classList.remove('moved'); void li.offsetWidth; li.classList.add('moved');
      });
      quiz.querySelector('.check-order').addEventListener('click', () => {
        answer(quiz, [...list.children].map((li) => li.dataset.idx).join(','));
      });
    }
    if (input?.tagName === 'INPUT') input.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
  });
})();
