<?php
declare(strict_types=1);
// Pantalla de estado 1 · Orden nueva. La lógica común está en includes/pantalla_estado.php.
$pantalla = [
    'estado'      => 1,
    'titulo'      => 'Órdenes nuevas',
    'descripcion' => 'Órdenes que la tienda acaba de mandar. Al empezar a surtir, el envío pasa a Surtiéndose.',
];
require dirname(__DIR__) . '/includes/pantalla_estado.php';
