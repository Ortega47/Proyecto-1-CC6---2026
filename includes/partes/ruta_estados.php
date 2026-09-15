<?php
// Ruta de los 5 estados. Se reutiliza en el rastreo, el detalle, el tablero y el inicio.
// Variables:
//   $ruta_actual        int|null  estado actual (null = ninguna parada resaltada)
//   $ruta_fechas        array     [id_estado => ['fecha' => ..., 'hora' => ...]] (opcional)
//   $ruta_conteos       array     [id_estado => n] para el tablero (opcional)
//   $ruta_enlaces       bool      cada parada enlaza a su pantalla del panel
//   $ruta_animada       bool      anima el avance hasta la parada actual (solo rastreo)
//   $ruta_descripciones array     [id_estado => frase] para el inicio (opcional)
$ruta_actual        = $ruta_actual ?? null;
$ruta_fechas        = $ruta_fechas ?? [];
$ruta_conteos       = $ruta_conteos ?? null;
$ruta_enlaces       = $ruta_enlaces ?? false;
$ruta_animada       = $ruta_animada ?? false;
$ruta_descripciones = $ruta_descripciones ?? [];
?>
<ol class="ruta<?= $ruta_animada ? ' ruta--animada' : '' ?>" aria-label="Etapas del envío">
  <?php foreach (estados() as $estado): ?>
    <?php
    $id = (int) $estado['id_estado'];
    if ($ruta_actual === null) {
        $clase = 'neutra';
    } elseif ($id < $ruta_actual) {
        $clase = 'hecha';
    } elseif ($id === $ruta_actual) {
        $clase = 'actual';
    } else {
        $clase = 'futura';
    }
    $indice = $id - 1;
    ?>
    <li class="ruta__parada ruta__parada--<?= $clase ?> ruta__parada--e<?= $id ?><?= $ruta_enlaces ? ' ruta__parada--enlazada' : '' ?>"
        style="--i: <?= $indice ?>"<?= $clase === 'actual' ? ' aria-current="step"' : '' ?>>
      <?php if ($ruta_enlaces): ?><a class="ruta__enlace" href="<?= h(url('panel/' . pantalla_de_estado($id))) ?>"><?php endif; ?>
        <span class="ruta__marca" aria-hidden="true"><?= $id ?></span>
        <span class="ruta__texto">
          <span class="ruta__nombre"><span class="visualmente-oculto"><?= $id ?>. </span><?= h(etiqueta_estado($id)) ?></span>
          <?php if ($ruta_conteos !== null): ?>
            <span class="ruta__conteo"><?= (int) ($ruta_conteos[$id] ?? 0) ?><span class="visualmente-oculto"> envíos</span></span>
          <?php elseif (isset($ruta_fechas[$id])): ?>
            <span class="ruta__detalle"><?= h(fecha_hora_ui($ruta_fechas[$id]['fecha'], $ruta_fechas[$id]['hora'])) ?></span>
          <?php elseif (isset($ruta_descripciones[$id])): ?>
            <span class="ruta__detalle"><?= h($ruta_descripciones[$id]) ?></span>
          <?php endif; ?>
        </span>
      <?php if ($ruta_enlaces): ?></a><?php endif; ?>
    </li>
  <?php endforeach; ?>
</ol>
<?php
unset($ruta_actual, $ruta_fechas, $ruta_conteos, $ruta_enlaces, $ruta_animada, $ruta_descripciones);
