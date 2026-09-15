<?php
// Tabla de envíos (tablero y pantalla de envíos).
// Variables: $tabla_envios (array), $tabla_vacio (texto), $tabla_vacio_enlace = [ruta, texto] (opcional).
$tabla_vacio        = $tabla_vacio ?? 'No hay envíos.';
$tabla_vacio_enlace = $tabla_vacio_enlace ?? null;
?>
<div class="tabla-contenedor tabla-contenedor--ancha">
  <?php if ($tabla_envios === []): ?>
    <div class="vacio">
      <p><?= h($tabla_vacio) ?></p>
      <?php if ($tabla_vacio_enlace !== null): ?>
        <p><a href="<?= h(url($tabla_vacio_enlace[0])) ?>"><?= h($tabla_vacio_enlace[1]) ?></a></p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <table class="tabla tabla--apilable" role="table">
      <thead>
        <tr role="row">
          <th scope="col" role="columnheader">Guía</th>
          <th scope="col" role="columnheader">Orden</th>
          <th scope="col" role="columnheader">Tienda</th>
          <th scope="col" role="columnheader">Destino</th>
          <th scope="col" role="columnheader">Fecha y hora</th>
          <th scope="col" role="columnheader" class="num">Costo</th>
          <th scope="col" role="columnheader">Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tabla_envios as $envio): ?>
          <?php $tienda = tienda_por_id($envio['id_tienda']); $destino = destino_por_id($envio['id_destino']); ?>
          <tr role="row">
            <td role="cell" data-etiqueta="Guía" class="codigo"><a href="<?= h(url('panel/detalle-envio.php?guia=' . rawurlencode($envio['no_guia']))) ?>"><?= h($envio['no_guia']) ?></a></td>
            <td role="cell" data-etiqueta="Orden" class="sin-salto"><?= h($envio['no_orden']) ?></td>
            <td role="cell" data-etiqueta="Tienda"><?= h($tienda['nombre'] ?? $envio['id_tienda']) ?></td>
            <td role="cell" data-etiqueta="Destino"><?= h($envio['id_destino']) ?> · <?= h($destino['ciudad'] ?? '') ?></td>
            <td role="cell" data-etiqueta="Fecha y hora"><?= h(fecha_hora_ui($envio['fecha'], $envio['hora'])) ?></td>
            <td role="cell" data-etiqueta="Costo" class="num"><?= h(moneda($envio['costo_total'])) ?></td>
            <td role="cell" data-etiqueta="Estado"><?php $insignia_estado = $envio['id_estado']; require __DIR__ . '/insignia_estado.php'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
unset($tabla_envios, $tabla_vacio, $tabla_vacio_enlace);
