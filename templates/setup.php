<?php use PhpLab\Auth; require __DIR__ . '/_auth_head.php'; ?>
<main class="auth-card">
  <div class="brand big"><span class="brand-mark">&lt;?</span>PHPLab</div>
  <h1>Configuració inicial</h1>
  <p class="muted">Crea el teu usuari. Aquesta pàgina només surt la primera vegada i només des del PC servidor (localhost).</p>
  <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="form">
    <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
    <label>Usuari <input name="usuari" required autocomplete="username" autocapitalize="none" spellcheck="false" pattern="[a-zA-Z0-9_.\-]{3,30}" value="<?= e($_POST['usuari'] ?? '') ?>"></label>
    <label>Contrasenya (mínim 10 caràcters) <input type="password" name="contrasenya" required minlength="10" autocomplete="new-password"></label>
    <label>Repeteix la contrasenya <input type="password" name="contrasenya2" required minlength="10" autocomplete="new-password"></label>
    <button class="btn primary" type="submit">Crear usuari i entrar</button>
  </form>
</main>
</body>
</html>
