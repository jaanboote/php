<?php use PhpLab\Auth; require __DIR__ . '/_auth_head.php'; ?>
<main class="auth-card">
  <div class="brand big"><span class="brand-mark">&lt;?</span>PHPLab</div>
  <h1>Entrar</h1>
  <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" action="index.php?p=login" class="form">
    <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
    <label>Usuari <input name="usuari" required autocomplete="username" autofocus></label>
    <label>Contrasenya <input type="password" name="contrasenya" required autocomplete="current-password"></label>
    <button class="btn primary" type="submit">Entrar</button>
  </form>
</main>
</body>
</html>
