<?php
declare(strict_types=1);
// Pantalla de estado 2 · Surtiéndose. La lógica común está en includes/pantalla_estado.php.
$pantalla = [
    'estado'      => 2,
    'titulo'      => 'Surtiéndose',
    'descripcion' => 'Se están reuniendo los artículos de cada orden. Cuando la orden está completa, pasa a empaque.',
];
require dirname(__DIR__) . '/includes/pantalla_estado.php';
