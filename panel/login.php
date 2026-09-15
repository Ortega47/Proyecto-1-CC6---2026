<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO(bd): buscar el usuario por `usuario`, comprobar con password_verify()
    // contra `contrasena`, abrir la sesión y, si falla, redirigir a login.php?error=1.
    redirigir('panel/index.php');
}

$hoja   = 'panel';
$titulo = 'Acceso del personal';
$error  = ($_GET['error'] ?? '') === '1';
require dirname(__DIR__) . '/includes/partes/cabeza.php';
?>
<main id="contenido" class="acceso">
  <div class="acceso__tarjeta superficie">
    <a class="marca acceso__marca" href="<?= h(url('index.php')) ?>">
      <img src="<?= h(url('assets/img/logo.svg')) ?>" alt="" width="36" height="36">
      <span class="marca__nombre"><?= h(NOMBRE_COURIER) ?></span>
    </a>
    <h1>Acceso del personal</h1>
    <?php if ($error): ?>
      <?php
      $mensaje_tipo  = 'error';
      $mensaje_texto = 'Usuario o contraseña incorrectos';
      $mensaje_detalle = 'Revise ambos datos e intente de nuevo.';
      require dirname(__DIR__) . '/includes/partes/mensaje.php';
      ?>
    <?php endif; ?>
    <form method="post" action="<?= h(url('panel/login.php')) ?>">
      <div class="campo">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" required autocomplete="username" autocapitalize="none" spellcheck="false"<?= $error ? ' aria-invalid="true"' : '' ?>>
      </div>
      <div class="campo">
        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password"<?= $error ? ' aria-invalid="true"' : '' ?>>
      </div>
      <button class="boton boton--primario boton--ancho" type="submit">Entrar al panel</button>
    </form>
    <p class="acceso__pie"><a href="<?= h(url('index.php')) ?>">Ir al rastreo público</a></p>
  </div>
</main>
</body>
</html>
