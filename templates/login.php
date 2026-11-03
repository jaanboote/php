<?php
use PhpLab\{Auth, Lessons, Labs, Reptes, Examens};
require __DIR__ . '/_auth_head.php';

// Les xifres surten del contingut real: si s'hi afegeixen lliçons o exàmens, la portada es posa al dia sola
$xifres = [
    'lliçons' => count(Lessons::all()),
    'laboratoris' => count(Labs::all()),
    'reptes' => count(Reptes::bank()),
    'exàmens' => count(Examens::all()),
];
?>
<?= icones_sprite() ?>
<div class="portada">
  <section class="portada-text" aria-labelledby="portada-titol">
    <div class="brand big"><span class="brand-mark">&lt;?</span>PHPLab</div>
    <h1 id="portada-titol">Aprèn PHP fent&#8209;lo funcionar.</h1>
    <p class="portada-entradeta">Lliçons, laboratoris i exàmens fets a partir del temari de M0376 (ASIX2). El codi que escrius
      s'executa de veritat en aquest ordinador i es corregeix al moment.</p>
  </section>
  <ul class="beneficis" aria-label="Què hi trobaràs">
      <li><?= icona('llibre') ?><div><strong><?= e($xifres['lliçons']) ?> lliçons</strong>
        <span>Exemples que pots modificar i executar, i preguntes que es corregeixen en respondre.</span></div></li>
      <li><?= icona('matras') ?><div><strong><?= e($xifres['laboratoris']) ?> laboratoris</strong>
        <span>Programes pas a pas amb proves, pistes i un tutor que t'ajuda sense donar-te la solució.</span></div></li>
      <li><?= icona('diana') ?><div><strong><?= e($xifres['reptes']) ?> reptes</strong>
        <span>Partides curtes, i un repàs que et torna a preguntar el que has fallat.</span></div></li>
      <li><?= icona('document') ?><div><strong><?= e($xifres['exàmens']) ?> exàmens</strong>
        <span>Simulacres amb rellotge, teoria i pràctica, i nota sobre 10.</span></div></li>
  </ul>

  <main class="auth-card login">
    <!-- Un exemple real del producte: el laboratori «Codis d'estat HTTP» del Tema 1 i les seves proves.
         Les línies apareixen una darrere l'altra (només CSS); el formulari ja es pot fer servir des del principi -->
    <figure class="boot-finestra">
      <div class="term-bar" aria-hidden="true"><i></i><i></i><i></i><span>codi_http.php · laboratori del Tema 1</span></div>
      <div class="boot" aria-hidden="true">
        <p>$ php codi_http.php 404</p>
        <p>Codi: 404</p>
        <p>Família: 4xx Error del client</p>
        <p>Nom: Not Found</p>
        <p class="boot-prompt"><b>✓</b> 7 de 7 proves superades · +10 XP<span class="cursor"></span></p>
      </div>
      <figcaption class="sr-only">Exemple d'un laboratori: el programa codi_http.php supera les 7 proves del pas.</figcaption>
    </figure>
    <h2>Inicia la sessió</h2>
    <?php if ($error): ?><p class="alert error" role="alert"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="index.php?p=login" class="form">
      <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
      <label>Usuari <input name="usuari" required autocomplete="username" autocapitalize="none" spellcheck="false" autofocus></label>
      <label>Contrasenya <input type="password" name="contrasenya" required autocomplete="current-password"></label>
      <button class="btn primary" type="submit">Entra a PHPLab</button>
    </form>
    <p class="auth-nota">Només s'hi pot entrar des de la xarxa local. Si has oblidat la contrasenya, canvia-la des del PC on hi ha
      la app amb <code>eines/canvia-contrasenya.php</code>.</p>
  </main>
</div>
<footer class="peu peu-auth">
  <span>PHPLab · M0376 Implantació d'aplicacions web</span>
  <span>Funciona sense Internet</span>
</footer>
</body>
</html>
