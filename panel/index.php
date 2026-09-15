<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

$conteos  = conteo_por_estado();
$total    = array_sum($conteos);
$activos  = $total - $conteos[5];
$titulo   = 'Tablero';
$pagina_actual = 'index.php';
require dirname(__DIR__) . '/includes/partes/cabecera_panel.php';
?>
<div class="panel-cabecera">
  <div>
    <h1>Tablero</h1>
    <p>Cómo va la bodega hoy. Cada parada abre la pantalla de esa etapa.</p>
  </div>
  <p class="panel-cabecera__cifra"><?= $activos ?> envíos en proceso · <?= $conteos[5] ?> entregados</p>
</div>

<section class="tablero-ruta superficie" aria-labelledby="titulo-ruta">
  <h2 id="titulo-ruta" class="visualmente-oculto">Envíos por etapa</h2>
  <?php
  $ruta_conteos = $conteos;
  $ruta_enlaces = true;
  require dirname(__DIR__) . '/includes/partes/ruta_estados.php';
  ?>
</section>

<section aria-labelledby="titulo-recientes">
  <div class="encabezado">
    <h2 id="titulo-recientes">Últimos envíos</h2>
    <p>Los ocho más recientes. <a href="<?= h(url('panel/envios.php')) ?>">Ver todos los envíos</a></p>
  </div>
  <?php
  $tabla_envios = envios_recientes(8);
  $tabla_vacio  = 'Todavía no hay envíos registrados.';
  require dirname(__DIR__) . '/includes/partes/tabla_envios.php';
  ?>
</section>
<?php require dirname(__DIR__) . '/includes/partes/pie_panel.php'; ?>
