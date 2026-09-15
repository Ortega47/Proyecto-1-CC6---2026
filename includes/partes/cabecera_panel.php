<?php
// Cabecera del panel interno. Variables: $titulo, $pagina_actual.
$hoja = 'panel';
require __DIR__ . '/cabeza.php';
?>
<div class="panel-envoltura">
  <aside class="panel-lateral">
    <a class="marca marca--claro" href="<?= h(url('panel/index.php')) ?>">
      <img src="<?= h(url('assets/img/logo.svg')) ?>" alt="" width="36" height="36">
      <span class="marca__nombre"><?= h(NOMBRE_COURIER) ?></span>
    </a>
    <?php require __DIR__ . '/menu_panel.php'; ?>
  </aside>
  <div class="panel-principal">
    <header class="panel-superior">
      <a class="marca marca--claro" href="<?= h(url('panel/index.php')) ?>">
        <img src="<?= h(url('assets/img/logo.svg')) ?>" alt="" width="32" height="32">
        <span class="marca__nombre"><?= h(NOMBRE_COURIER) ?></span>
      </a>
      <details class="menu-movil">
        <summary>Menú</summary>
        <?php require __DIR__ . '/menu_panel.php'; ?>
      </details>
    </header>
    <main id="contenido" class="panel-contenido">
