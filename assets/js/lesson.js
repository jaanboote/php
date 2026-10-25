/* PHPLab — lliçons: exemples "prova-ho" i micro-preguntes. */
'use strict';

(() => {
  const lessonEl = document.querySelector('.lesson');
  if (!lessonEl) return;
  const lessonId = lessonEl.dataset.lesson;

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
        fb.textContent = '✔ Correcte! ' + r.explicacio;
        quiz.classList.add('done');
        optionBtn?.classList.add('right');
        const c = document.getElementById('lesson-count');
        c.textContent = +c.textContent + 1;
      } else {
        fb.textContent = '✖ No és correcte. Torna-ho a provar (pots fer servir els exemples de dalt per comprovar-ho).';
        if (optionBtn) {
          optionBtn.classList.add('wrong');
          setTimeout(() => optionBtn.classList.remove('wrong'), 600);
        }
      }
      PHPLab.updateProgress(r.progress, r.xp);
      if (r.leccio_acabada && r.xp >= 20) setTimeout(() => PHPLab.toast('🏁 Lliçó completada!'), 1200);
    } catch (e) {
      PHPLab.toast(e.message);
    }
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
      });
      quiz.querySelector('.check-order').addEventListener('click', () => {
        answer(quiz, [...list.children].map((li) => li.dataset.idx).join(','));
      });
    }
    if (input?.tagName === 'INPUT') input.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
  });
})();
