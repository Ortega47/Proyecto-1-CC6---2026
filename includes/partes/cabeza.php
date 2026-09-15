<?php
// Cabeza HTML común a todo el sitio.
// Variables: $titulo (obligatoria), $hoja ('publico' o 'panel').
$hoja = $hoja ?? 'publico';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($titulo) ?> · <?= h(NOMBRE_COURIER) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= h(url('assets/img/favicon.svg')) ?>">
<link rel="preload" href="<?= h(url('assets/fonts/barlow-latin-400-normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= h(url('assets/fonts/barlow-condensed-latin-700-normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= h(url('assets/css/base.css')) ?>">
<link rel="stylesheet" href="<?= h(url('assets/css/' . $hoja . '.css')) ?>">
</head>
<body class="<?= h($hoja) ?>">
<a class="salto" href="#contenido">Ir al contenido</a>
