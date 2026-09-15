<?php
declare(strict_types=1);

// Ruta base del sitio, sin barra final.
//   php -S localhost:8000 desde la raíz  ->  ''
//   XAMPP en htdocs/courier              ->  '/courier'
define('BASE_URL', '');

// TODO: nombre comercial del courier, pendiente de definir con el grupo.
define('NOMBRE_COURIER', 'Courier');

// TODO: código de 15 caracteres del courier (identificador que viaja en las respuestas
// del WebService), pendiente de acordar con los grupos de Tienda Virtual.
define('CODIGO_COURIER', 'COURIER-GT-0001');

// Zona horaria para fecha_ui() y hora_ui().
date_default_timezone_set('America/Guatemala');
