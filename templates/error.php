<?php
/**
 * Pàgina d'error (p. ex. 404) amb la mascota confosa.
 * @var string $titol @var string $missatge
 */
?>
<section class="pagina-error">
  <?php $ratxa = 0; $humorForcat = 'confos'; $fraseForcada = null; $mascotaClasse = 'mascota-gran';
        require __DIR__ . '/_mascota.php'; ?>
  <p class="error-codi">404</p>
  <h1><?= e($titol) ?></h1>
  <p class="muted"><?= e($missatge) ?></p>
  <div class="error-accions">
    <a class="btn primary" href="index.php">Torna a l'inici</a>
    <a class="btn" href="index.php?p=reptes">🎯 Fes un repte</a>
  </div>
</section>
