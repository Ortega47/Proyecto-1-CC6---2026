<?php
declare(strict_types=1);
// Página no encontrada. También se incluye desde el panel cuando un parámetro
// no existe; en ese caso la página que incluye puede definir $regreso = [ruta, texto].
require_once __DIR__ . '/includes/consultas.php';

http_response_code(404);
$regreso = $regreso ?? ['index.php', 'Volver al inicio'];
$titulo  = 'Página no encontrada';
require __DIR__ . '/includes/partes/cabecera_publico.php';
?>
<div class="no-encontrado">
  <h1>No encontramos esta página</h1>
  <p>La dirección puede estar mal escrita o el registro que busca ya no existe.</p>
  <p><a class="boton boton--primario" href="<?= h(url($regreso[0])) ?>"><?= h($regreso[1]) ?></a></p>
</div>
<?php require __DIR__ . '/includes/partes/pie_publico.php'; ?>
