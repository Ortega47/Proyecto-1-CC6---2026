<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/consultas.php';

$guia_escrita  = trim((string) ($_GET['guia'] ?? ''));
$rastreo_valor = $guia_escrita;
$rastreo_error = null;
$envio         = null;
$no_encontrada = false;

if ($guia_escrita === '') {
    $rastreo_error = 'Escriba el número de guía para rastrear su paquete.';
} elseif (mb_strlen($guia_escrita) !== 15) {
    $rastreo_error = sprintf(
        'El número de guía tiene 15 caracteres y el que escribió tiene %d. Revíselo e intente de nuevo.',
        mb_strlen($guia_escrita)
    );
} else {
    $envio = envio_por_guia(mb_strtoupper($guia_escrita)); // TODO(bd): consulta por no_guia
    if ($envio === null) {
        $no_encontrada = true;
        http_response_code(404);
    }
}

if ($envio !== null) {
    $titulo = 'Guía ' . $envio['no_guia'];
} elseif ($no_encontrada) {
    $titulo = 'Guía no encontrada';
} else {
    $titulo = 'Rastree su paquete';
}
require __DIR__ . '/includes/partes/cabecera_publico.php';
?>
<?php if ($envio !== null): ?>
  <?php
  $historial = seguimiento_de($envio['no_guia']);
  $fechas    = fechas_por_estado($envio['no_guia']);
  ?>
  <div class="resultado">
    <div class="resultado__cabecera">
      <h1><?= h(frase_estado_publico((int) $envio['id_estado'])) ?></h1>
      <p>Última actualización: <?= h(fecha_hora_ui($fechas[$envio['id_estado']]['fecha'], $fechas[$envio['id_estado']]['hora'])) ?></p>
    </div>

    <?php
    $etiqueta_envio = $envio;
    $etiqueta_modo  = 'publico';
    require __DIR__ . '/includes/partes/etiqueta_guia.php';
    ?>

    <section aria-labelledby="titulo-etapas">
      <h2 id="titulo-etapas" class="visualmente-oculto">Etapas del envío</h2>
      <?php
      $ruta_actual  = (int) $envio['id_estado'];
      $ruta_fechas  = $fechas;
      $ruta_animada = true;
      require __DIR__ . '/includes/partes/ruta_estados.php';
      ?>
    </section>

    <section aria-labelledby="titulo-historial">
      <h2 id="titulo-historial">Historial</h2>
      <ol class="historial" reversed>
        <?php foreach (array_reverse($historial) as $registro): ?>
          <li class="historial__item">
            <span class="historial__cuando"><?= h(fecha_hora_ui($registro['fecha'], $registro['hora'])) ?></span>
            <span class="historial__estado"><?= h(etiqueta_estado((int) $registro['id_estado'])) ?></span>
            <?php if ($registro['observacion'] !== null && $registro['observacion'] !== ''): ?>
              <span class="historial__nota"><?= h($registro['observacion']) ?></span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ol>
    </section>

    <section class="resultado__otra" aria-labelledby="titulo-otra">
      <h2 id="titulo-otra">Rastrear otra guía</h2>
      <?php $rastreo_valor = ''; require __DIR__ . '/includes/partes/formulario_rastreo.php'; ?>
    </section>
  </div>

<?php elseif ($no_encontrada): ?>
  <div class="no-encontrada">
    <h1>No encontramos la guía <?= h(mb_strtoupper($guia_escrita)) ?></h1>
    <p>Puede ser que el número tenga un carácter distinto o que la tienda todavía no nos haya enviado la orden.</p>
    <ul>
      <li>Revise que los 15 caracteres sean iguales a los de su confirmación de compra.</li>
      <li>Si acaba de comprar, espere unos minutos y vuelva a intentar.</li>
      <li>Si sigue sin aparecer, pida el número de guía a la tienda donde compró.</li>
    </ul>
    <?php require __DIR__ . '/includes/partes/formulario_rastreo.php'; ?>
  </div>

<?php else: ?>
  <section class="portada">
    <h1>Rastree su paquete</h1>
    <p class="portada__intro">Escriba el número de guía que le dio la tienda y vea en qué etapa va su envío.</p>
    <?php require __DIR__ . '/includes/partes/formulario_rastreo.php'; ?>
  </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/partes/pie_publico.php'; ?>
