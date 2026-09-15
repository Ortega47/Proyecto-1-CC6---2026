<?php
// Cabecera del sitio público. Variables: $titulo.
$hoja = 'publico';
require __DIR__ . '/cabeza.php';
?>
<header class="cabecera-publica">
  <div class="contenedor cabecera-publica__interior">
    <a class="marca" href="<?= h(url('index.php')) ?>">
      <img src="<?= h(url('assets/img/logo.svg')) ?>" alt="" width="36" height="36">
      <span class="marca__nombre"><?= h(NOMBRE_COURIER) ?></span>
    </a>
    <a class="cabecera-publica__personal" href="<?= h(url('panel/login.php')) ?>">Acceso del personal</a>
  </div>
</header>
<main id="contenido" class="contenedor contenido-publico">
