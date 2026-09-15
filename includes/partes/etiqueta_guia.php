<?php
// Etiqueta de guía (la que va pegada en la caja). Única en el sitio.
// Variables: $etiqueta_envio (fila de envio), $etiqueta_modo ('publico' | 'interno').
// En modo público no se muestra la dirección ni el nombre completo del destinatario.
$etiqueta_modo = $etiqueta_modo ?? 'publico';
$destino = destino_por_id($etiqueta_envio['id_destino']);
$tienda  = tienda_por_id($etiqueta_envio['id_tienda']);
?>
<article class="guia" aria-label="Etiqueta de la guía <?= h($etiqueta_envio['no_guia']) ?>">
  <div class="guia__franja" aria-hidden="true"></div>
  <div class="guia__cuerpo">
    <p class="guia__rotulo">Guía</p>
    <p class="guia__numero"><?= h($etiqueta_envio['no_guia']) ?></p>
    <div class="guia__ruteo">
      <p class="guia__destino">
        <span class="guia__codigo"><span class="visualmente-oculto">Código de destino </span><?= h($etiqueta_envio['id_destino']) ?></span>
        <span class="guia__ciudad"><?= h($destino['ciudad'] ?? 'Destino desconocido') ?></span>
      </p>
      <dl class="guia__datos">
        <?php if ($etiqueta_modo === 'interno'): ?>
          <dt>Destinatario</dt>
          <dd><?= h($etiqueta_envio['destinatario']) ?></dd>
          <dt>Dirección</dt>
          <dd><?= h($etiqueta_envio['direccion']) ?></dd>
          <dt>Tienda</dt>
          <dd><?= h($tienda['nombre'] ?? $etiqueta_envio['id_tienda']) ?></dd>
          <dt>Orden</dt>
          <dd><?= h($etiqueta_envio['no_orden']) ?></dd>
          <dt>Recibido</dt>
          <dd><?= h(fecha_hora_ui($etiqueta_envio['fecha'], $etiqueta_envio['hora'])) ?></dd>
          <dt>Costo</dt>
          <dd><?= h(moneda($etiqueta_envio['costo_total'])) ?></dd>
        <?php else: ?>
          <dt>Para</dt>
          <dd><?= h(destinatario_abreviado($etiqueta_envio['destinatario'])) ?></dd>
          <dt>Tienda</dt>
          <dd><?= h($tienda['nombre'] ?? $etiqueta_envio['id_tienda']) ?></dd>
          <dt>Fecha</dt>
          <dd><?= h(fecha_ui($etiqueta_envio['fecha'])) ?></dd>
        <?php endif; ?>
      </dl>
    </div>
    <div class="guia__barras" aria-hidden="true"></div>
  </div>
</article>
<?php
unset($etiqueta_envio, $etiqueta_modo, $destino, $tienda);
