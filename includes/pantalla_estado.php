<?php
declare(strict_types=1);
// Plantilla común de las cinco pantallas de estado (panel/ordenes-nuevas.php,
// surtiendose.php, empacandose.php, en-ruta.php y entregadas.php).
// La página que la incluye define:
//   $pantalla = ['estado' => int, 'titulo' => string, 'descripcion' => string]
require_once __DIR__ . '/consultas.php';

$id_estado  = (int) $pantalla['estado'];
$transicion = transicion_estado($id_estado); // null en Entregada
$archivo    = pantalla_de_estado($id_estado);
$envios     = envios_por_estado($id_estado);
$error      = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $transicion !== null) {
    $observacion = trim((string) ($_POST['observacion'] ?? ''));
    $guia_fila   = trim((string) ($_POST['guia'] ?? ''));

    if ($guia_fila !== '') {
        // Botón de una fila: se mueve solo esa guía, aunque haya otras marcadas.
        $seleccion = [$guia_fila];
    } else {
        $seleccion = array_map('strval', (array) ($_POST['guias'] ?? []));
    }
    // Solo cuentan las guías que de verdad están en esta etapa.
    $validas = array_values(array_intersect($seleccion, array_column($envios, 'no_guia')));

    if ($validas === []) {
        $error = $guia_fila !== ''
            ? 'Ese envío ya no está en esta etapa. Revise la lista actualizada.'
            : 'Marque al menos un envío de la lista, o use el botón de su fila para mover uno solo.';
    } else {
        // TODO(bd): en una sola transacción, por cada guía de $validas:
        //   UPDATE envio SET id_estado = :siguiente WHERE no_guia = :guia AND id_estado = :actual;
        //   INSERT INTO seguimiento (no_guia, id_estado, id_usuario, observacion)
        //     VALUES (:guia, :siguiente, :id_usuario, NULLIF(:observacion, ''));
        redirigir('panel/' . $archivo . '?ok=demo&n=' . count($validas));
    }
}

$titulo        = $pantalla['titulo'];
$pagina_actual = $archivo;
require __DIR__ . '/partes/cabecera_panel.php';
?>
<div class="panel-cabecera">
  <div>
    <h1><?= h($pantalla['titulo']) ?></h1>
    <p><?= h($pantalla['descripcion']) ?></p>
  </div>
  <p class="panel-cabecera__cifra"><?= count($envios) ?> <?= count($envios) === 1 ? 'envío' : 'envíos' ?> en esta etapa</p>
</div>

<?php if ($error !== null): ?>
  <?php
  $mensaje_tipo  = 'error';
  $mensaje_texto = 'No se movió ningún envío';
  $mensaje_detalle = $error;
  require __DIR__ . '/partes/mensaje.php';
  ?>
<?php elseif (($_GET['ok'] ?? '') === 'demo' && $transicion !== null): ?>
  <?php
  $n = max(1, (int) ($_GET['n'] ?? 1));
  $mensaje_tipo  = 'vista';
  $mensaje_texto = 'Vista previa: ' . plural($transicion['demo'], $n) . ' cuando conectemos la base de datos.';
  $mensaje_detalle = 'Por ahora la lista no cambia.';
  // TODO(bd): cuando el POST guarde de verdad, mostrar plural($transicion['final'], $n) con tipo 'ok'.
  require __DIR__ . '/partes/mensaje.php';
  ?>
<?php endif; ?>

<?php if ($envios === []): ?>
  <div class="tabla-contenedor tabla-contenedor--ancha">
    <div class="vacio">
      <p>No hay envíos en esta etapa</p>
      <p><a href="<?= h(url('panel/envios.php')) ?>">Ver todos los envíos</a></p>
    </div>
  </div>
<?php else: ?>
  <?php if ($transicion !== null): ?>
  <form method="post" action="<?= h(url('panel/' . $archivo)) ?>">
    <div class="acciones-masivas superficie">
      <div class="campo">
        <label for="observacion">Observación (opcional)</label>
        <textarea id="observacion" name="observacion" rows="2" maxlength="200"></textarea>
        <span class="campo__ayuda">Se guarda junto con el cambio de etapa.</span>
      </div>
      <button class="boton boton--primario" type="submit"><?= h($transicion['boton']) ?> los seleccionados</button>
      <p class="acciones-masivas__nota">Marque varios envíos y use este botón, o use el botón de cada fila para mover uno solo.</p>
    </div>
  <?php endif; ?>

    <div class="tabla-contenedor tabla-contenedor--ancha">
      <table class="tabla tabla--apilable" role="table">
        <thead>
          <tr role="row">
            <?php if ($transicion !== null): ?>
              <th scope="col" role="columnheader" class="seleccion"><span class="visualmente-oculto">Seleccionar</span></th>
            <?php endif; ?>
            <th scope="col" role="columnheader">Guía</th>
            <th scope="col" role="columnheader">Orden</th>
            <th scope="col" role="columnheader">Tienda</th>
            <th scope="col" role="columnheader">Destino</th>
            <th scope="col" role="columnheader">Destinatario</th>
            <th scope="col" role="columnheader"><?= $id_estado === 5 ? 'Entregado' : 'En esta etapa desde' ?></th>
            <?php if ($transicion !== null): ?>
              <th scope="col" role="columnheader" class="accion"><span class="visualmente-oculto">Acción</span></th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($envios as $envio): ?>
            <?php
            $tienda  = tienda_por_id($envio['id_tienda']);
            $destino = destino_por_id($envio['id_destino']);
            $desde   = fechas_por_estado($envio['no_guia'])[$id_estado] ?? null;
            $id_html = 'sel-' . preg_replace('/[^A-Za-z0-9]/', '', $envio['no_guia']);
            ?>
            <tr role="row">
              <?php if ($transicion !== null): ?>
                <td role="cell" data-etiqueta="Seleccionar" class="seleccion">
                  <input type="checkbox" name="guias[]" value="<?= h($envio['no_guia']) ?>" id="<?= h($id_html) ?>" aria-label="Seleccionar <?= h($envio['no_guia']) ?>">
                </td>
              <?php endif; ?>
              <td role="cell" data-etiqueta="Guía" class="codigo"><a href="<?= h(url('panel/detalle-envio.php?guia=' . rawurlencode($envio['no_guia']))) ?>"><?= h($envio['no_guia']) ?></a></td>
              <td role="cell" data-etiqueta="Orden" class="sin-salto"><?= h($envio['no_orden']) ?></td>
              <td role="cell" data-etiqueta="Tienda"><?= h($tienda['nombre'] ?? $envio['id_tienda']) ?></td>
              <td role="cell" data-etiqueta="Destino"><?= h($envio['id_destino']) ?> · <?= h($destino['ciudad'] ?? '') ?></td>
              <td role="cell" data-etiqueta="Destinatario" class="destinatario"><?= h($envio['destinatario']) ?></td>
              <td role="cell" data-etiqueta="<?= $id_estado === 5 ? 'Entregado' : 'Desde' ?>"><?= $desde !== null ? h(fecha_hora_ui($desde['fecha'], $desde['hora'])) : '' ?></td>
              <?php if ($transicion !== null): ?>
                <td role="cell" data-etiqueta="Acción" class="accion">
                  <button class="boton boton--secundario boton--pequeno" type="submit" name="guia" value="<?= h($envio['no_guia']) ?>"><?= h($transicion['boton']) ?><span class="visualmente-oculto"> <?= h($envio['no_guia']) ?></span></button>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php if ($transicion !== null): ?>
  </form>
  <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/partes/pie_panel.php'; ?>
