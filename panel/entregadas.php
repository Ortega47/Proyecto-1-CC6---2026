<?php
declare(strict_types=1);
// Pantalla de estado 5 · Entregada. Solo consulta: es la última etapa.
$pantalla = [
    'estado'      => 5,
    'titulo'      => 'Entregadas',
    'descripcion' => 'Envíos que ya llegaron a su destino. Solo consulta: la entrega es la última etapa.',
];
require dirname(__DIR__) . '/includes/pantalla_estado.php';
