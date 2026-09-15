<?php
declare(strict_types=1);
// Pantalla de estado 3 · Empacándose. La lógica común está en includes/pantalla_estado.php.
$pantalla = [
    'estado'      => 3,
    'titulo'      => 'Empacándose',
    'descripcion' => 'Aquí se cierra la caja, se pega la guía y el paquete queda listo para salir. Al despacharlo, pasa a En ruta.',
];
require dirname(__DIR__) . '/includes/pantalla_estado.php';
