<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/consultas.php';

$titulo = 'Rastree su paquete';
require __DIR__ . '/includes/partes/cabecera_publico.php';
?>
<section class="portada" aria-labelledby="titulo-rastreo">
  <h1 id="titulo-rastreo">Rastree su paquete</h1>
  <p class="portada__intro">Escriba el número de guía que le dio la tienda y vea en qué etapa va su envío.</p>
  <?php require __DIR__ . '/includes/partes/formulario_rastreo.php'; ?>
</section>

<section class="seccion etapas" aria-labelledby="titulo-etapas">
  <div class="encabezado">
    <h2 id="titulo-etapas">Cómo avanza un paquete</h2>
    <p>Todo envío pasa por estas cinco etapas, en este orden.</p>
  </div>
  <?php
  $ruta_descripciones = [
      1 => 'La tienda nos avisó de su compra.',
      2 => 'Reunimos los artículos de su orden.',
      3 => 'Cerramos la caja y pegamos la guía.',
      4 => 'El paquete salió hacia su dirección.',
      5 => 'El paquete llegó a su destino.',
  ];
  require __DIR__ . '/includes/partes/ruta_estados.php';
  ?>
</section>

<section class="seccion" aria-labelledby="titulo-destinos">
  <div class="encabezado">
    <h2 id="titulo-destinos">Destinos que cubrimos</h2>
    <p>El costo incluye el envío y el manejo del paquete.</p>
  </div>
  <ul class="destinos">
    <?php foreach (destinos() as $destino): ?>
      <li>
        <span class="destinos__ciudad"><?= h($destino['ciudad']) ?></span>
        <?php if ($destino['cobertura']): ?>
          <span class="destinos__costo"><?= h(moneda($destino['costo_envio'] + $destino['costo_manejo'])) ?></span>
        <?php else: ?>
          <span class="destinos__sin">Sin cobertura por ahora</span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
  <p class="destinos__nota">El envío se contrata desde la tienda donde compra; el costo se calcula con el destino que usted indique.</p>
</section>
<?php require __DIR__ . '/includes/partes/pie_publico.php'; ?>
