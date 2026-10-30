<?php use PhpLab\Auth; require __DIR__ . '/_auth_head.php'; ?>
<main class="auth-card login">
  <div class="brand big"><span class="brand-mark">&lt;?</span>PHPLab</div>
  <!-- Arrencada: les línies apareixen una darrere l'altra (només CSS) i després el formulari -->
  <div class="boot" aria-hidden="true">
    <p>PHPLab · M0376 · ASIX2</p>
    <p><b>[ OK ]</b> Servidor web en marxa</p>
    <p><b>[ OK ]</b> Intèrpret de PHP carregat</p>
    <p><b>[ OK ]</b> Sandbox preparat</p>
    <p><b>[ OK ]</b> Base de dades connectada</p>
    <p class="boot-prompt">&gt; Identifica't per continuar<span class="cursor"></span></p>
  </div>
  <h1>Inicia la sessió</h1>
  <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" action="index.php?p=login" class="form">
    <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
    <label>Usuari <input name="usuari" required autocomplete="username" autocapitalize="none" spellcheck="false" autofocus></label>
    <label>Contrasenya <input type="password" name="contrasenya" required autocomplete="current-password"></label>
    <button class="btn primary" type="submit">Entrar</button>
  </form>
</main>
</body>
</html>
