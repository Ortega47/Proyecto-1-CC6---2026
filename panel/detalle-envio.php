<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

$guia  = trim((string) ($_GET['guia'] ?? ''));
$envio = $guia !== '' ? envio_por_guia($guia) : null; // TODO(bd): consulta por no_guia

if ($envio === null) {
    http_response_code(404);
    $titulo        = 'Guía no encontrada';
    $pagina_actual = 'envios.php';
    require dirname(__DIR__) . '/includes/partes/cabecera_panel.php';
    ?>
    <div class="panel-cabecera">
      <div><h1>Guía no encontrada</h1></div>
    </div>
    <?php
    $mensaje_tipo    = 'error';
    $mensaje_texto   = $guia === '' ? 'Falta el número de guía en la dirección.' : 'No existe ningún envío con la guía ' . $guia . '.';
    $mensaje_detalle = 'Revise el número o búsquelo en la lista de envíos.';
    $mensaje_enlace  = ['panel/envios.php', 'Ir a envíos'];
    require dirname(__DIR__) . '/includes/partes/mensaje.php';
    require dirname(__DIR__) . '/includes/partes/pie_panel.php';
    exit;
}

$id_estado  = (int) $envio['id_estado'];
$transicion = transicion_estado($id_estado);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $transicion !== null) {
    $observacion = trim((string) ($_POST['observacion'] ?? ''));
    // TODO(bd): en una transacción: UPDATE envio SET id_estado = :siguiente WHERE no_guia = :guia AND id_estado = :actual;
    //           INSERT INTO seguimiento (no_guia, id_estado, id_usuario, observacion) VALUES (...)
    redirigir('panel/detalle-envio.php?guia=' . rawurlencode($envio['no_guia']) . '&ok=demo');
}

$historial = seguimiento_de($envio['no_guia']);
$fechas    = fechas_por_estado($envio['no_guia']);
$titulo    = 'Guía ' . $envio['no_guia'];
$pagina_actual = pantalla_de_estado($id_estado);
require dirname(__DIR__) . '/includes/partes/cabecera_panel.php';
?>
<div class="panel-cabecera">
  <div>
    <h1>Guía <?= h($envio['no_guia']) ?></h1>
    <p>Orden <?= h($envio['no_orden']) ?> de <?= h(tienda_por_id($envio['id_tienda'])['nombre'] ?? $envio['id_tienda']) ?>. <a href="<?= h(url('panel/envios.php')) ?>">Volver a envíos</a></p>
  </div>
  <?php $insignia_estado = $id_estado; require dirname(__DIR__) . '/includes/partes/insignia_estado.php'; ?>
</div>

<?php if (($_GET['ok'] ?? '') === 'demo' && $transicion !== null): ?>
  <?php
  $mensaje_tipo    = 'vista';
  $mensaje_texto   = 'Vista previa: ' . plural($transicion['demo'], 1) . ' cuando conectemos la base de datos.';
  $mensaje_detalle = 'Por ahora el envío sigue en ' . etiqueta_estado($id_estado) . '.';
  // TODO(bd): cuando el POST guarde de verdad, mostrar plural($transicion['final'], 1) con tipo 'ok'.
  require dirname(__DIR__) . '/includes/partes/mensaje.php';
  ?>
<?php endif; ?>

<div class="detalle">
  <?php
  $etiqueta_envio = $envio;
  $etiqueta_modo  = 'interno';
  require dirname(__DIR__) . '/includes/partes/etiqueta_guia.php';
  ?>

  <section class="detalle__bloque superficie" aria-labelledby="titulo-ruta">
    <h2 id="titulo-ruta" class="visualmente-oculto">Etapas del envío</h2>
    <?php
    $ruta_actual = $id_estado;
    $ruta_fechas = $fechas;
    require dirname(__DIR__) . '/includes/partes/ruta_estados.php';
    ?>
  </section>

  <div class="detalle__columnas">
    <section aria-labelledby="titulo-historial">
      <h2 id="titulo-historial">Historial</h2>
      <ol class="historial" reversed>
        <?php foreach (array_reverse($historial) as $registro): ?>
          <?php $quien = usuario_por_id($registro['id_usuario'] === null ? null : (int) $registro['id_usuario']); ?>
          <li class="historial__item">
            <span class="historial__cuando"><?= h(fecha_hora_ui($registro['fecha'], $registro['hora'])) ?></span>
            <span class="historial__estado"><?= h(etiqueta_estado((int) $registro['id_estado'])) ?></span>
            <?php if ($registro['observacion'] !== null && $registro['observacion'] !== ''): ?>
              <span class="historial__nota"><?= h($registro['observacion']) ?></span>
            <?php endif; ?>
            <span class="historial__quien"><?= $quien === null ? 'Registrado por la tienda' : 'Por ' . h($quien['nombre']) ?></span>
          </li>
        <?php endforeach; ?>
      </ol>
    </section>

    <section class="detalle__bloque superficie formulario-avance" aria-labelledby="titulo-avance">
      <?php if ($transicion !== null): ?>
        <h2 id="titulo-avance">Pasar a la siguiente etapa</h2>
        <p>Este envío está en <?= h(etiqueta_estado($id_estado)) ?>. El siguiente paso es <?= h(etiqueta_estado($transicion['siguiente'])) ?>.</p>
        <form method="post" action="<?= h(url('panel/detalle-envio.php?guia=' . rawurlencode($envio['no_guia']))) ?>">
          <div class="campo">
            <label for="observacion">Observación (opcional)</label>
            <textarea id="observacion" name="observacion" rows="3" maxlength="200"></textarea>
          </div>
          <button class="boton boton--primario" type="submit"><?= h($transicion['boton']) ?></button>
        </form>
      <?php else: ?>
        <h2 id="titulo-avance">Entrega confirmada</h2>
        <p>Este envío ya fue entregado. No hay más etapas.</p>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/partes/pie_panel.php'; ?>
