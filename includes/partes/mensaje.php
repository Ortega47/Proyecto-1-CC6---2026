<?php
// Mensaje de resultado. Variables: $mensaje_tipo ('ok' | 'error' | 'vista'),
// $mensaje_texto, y opcionales $mensaje_detalle y $mensaje_enlace = [ruta, texto].
$mensaje_detalle = $mensaje_detalle ?? null;
$mensaje_enlace  = $mensaje_enlace ?? null;
?>
<div class="mensaje mensaje--<?= h($mensaje_tipo) ?>" role="<?= $mensaje_tipo === 'error' ? 'alert' : 'status' ?>">
  <p class="mensaje__titulo"><?= h($mensaje_texto) ?></p>
  <?php if ($mensaje_detalle !== null): ?>
    <p><?= h($mensaje_detalle) ?></p>
  <?php endif; ?>
  <?php if ($mensaje_enlace !== null): ?>
    <p><a href="<?= h(url($mensaje_enlace[0])) ?>"><?= h($mensaje_enlace[1]) ?></a></p>
  <?php endif; ?>
</div>
<?php
unset($mensaje_tipo, $mensaje_texto, $mensaje_detalle, $mensaje_enlace);
