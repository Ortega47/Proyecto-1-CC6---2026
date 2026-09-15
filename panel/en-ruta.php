<?php
declare(strict_types=1);
// Pantalla de estado 4 · En ruta. La lógica común está en includes/pantalla_estado.php.
$pantalla = [
    'estado'      => 4,
    'titulo'      => 'En ruta',
    'descripcion' => 'Paquetes que ya salieron de bodega con el piloto. Al entregarlos, se confirma la entrega.',
];
require dirname(__DIR__) . '/includes/pantalla_estado.php';
