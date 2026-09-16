<?php
// Cabecera común. La página define antes $titulo y $raiz.
// En las páginas públicas puede no haber sesión: se lee sin dar aviso.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sesion_nombre = $_SESSION['nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($titulo) ?> - Courier</title>
    <link rel="icon" type="image/svg+xml" href="<?= h($raiz) ?>img/favicon.svg">
    <link rel="stylesheet" href="<?= h($raiz) ?>style.css">
</head>
<body>
<header class="barra">
    <div>
        <a class="marca" href="<?= h($raiz) ?>index.php">
            <img src="<?= h($raiz) ?>img/logo.svg" alt="">
            Courier
        </a>
        <nav>
            <?php if ($sesion_nombre !== ''): ?>
                <span><?= h($sesion_nombre) ?></span>
                <a href="<?= h($raiz) ?>menu.php">Menú</a>
                <a href="<?= h($raiz) ?>logout.php">Cerrar sesión</a>
            <?php elseif (empty($sin_acceso)): ?>
                <a class="acceso" href="<?= h($raiz) ?>login.php">Acceso del personal</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
