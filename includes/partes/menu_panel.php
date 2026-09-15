<?php
// Menú del panel. Se incluye dos veces (barra lateral y <details> móvil), por eso
// no lleva ids. Variables: $pagina_actual (nombre del archivo, p. ej. 'envios.php').
$conteos = conteo_por_estado();
$usuario = usuario_actual();
$actual = fn(string $archivo): string => ($pagina_actual ?? '') === $archivo ? ' aria-current="page"' : '';
?>
<nav class="menu" aria-label="Secciones del panel">
  <ul class="menu__lista">
    <li><a href="<?= h(url('panel/index.php')) ?>"<?= $actual('index.php') ?>>Tablero</a></li>
    <li><a href="<?= h(url('panel/envios.php')) ?>"<?= $actual('envios.php') ?>>Envíos</a></li>
  </ul>
  <p class="menu__grupo" aria-hidden="true">Etapas</p>
  <ul class="menu__lista" aria-label="Etapas">
    <?php foreach ([1 => 'Órdenes nuevas', 2 => 'Surtiéndose', 3 => 'Empacándose', 4 => 'En ruta', 5 => 'Entregadas'] as $id => $nombre): ?>
      <?php $archivo = pantalla_de_estado($id); ?>
      <li>
        <a href="<?= h(url('panel/' . $archivo)) ?>"<?= $actual($archivo) ?>>
          <span class="menu__numero" aria-hidden="true"><?= $id ?></span>
          <span class="menu__texto"><?= h($nombre) ?></span>
          <span class="menu__conteo"><?= $conteos[$id] ?><span class="visualmente-oculto"> envíos</span></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <p class="menu__grupo" aria-hidden="true">Catálogos</p>
  <ul class="menu__lista" aria-label="Catálogos">
    <li><a href="<?= h(url('panel/destinos.php')) ?>"<?= $actual('destinos.php') ?>>Destinos</a></li>
    <li><a href="<?= h(url('panel/tiendas.php')) ?>"<?= $actual('tiendas.php') ?>>Tiendas</a></li>
  </ul>
  <div class="menu__usuario">
    <span class="menu__nombre"><?= h($usuario['nombre']) ?></span>
    <a href="<?= h(url('panel/salir.php')) ?>">Salir</a>
  </div>
</nav>
