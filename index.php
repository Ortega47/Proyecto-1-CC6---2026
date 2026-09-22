<?php
    require __DIR__ . '/auth.php';

    if (!$_SESSION["admin"]) {
        header("Location: rastreo.php");
        exit;
    }

    $mensaje = '';
    if ($mensaje === '' && isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
    }
    unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú del courier - 🐳 Winni Express</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="index.php">Menú principal</a>
    <a href="rastreo.php">Rastrear paquete</a>
    <a href="logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Menú del courier</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>Bienvenido, <b>' . $_SESSION['nombre'] . '</b>.</p>';
    echo '<p>Administrador</p>';
    echo '<div class="menu">';
    echo '    <a class="button" href="envios/listado.php">Envíos</a>';
    echo '    <a class="button" href="seguimiento/listado.php">Seguimiento</a>';
    echo '    <a class="button" href="estados/orden_nueva.php">Orden nueva</a>';
    echo '    <a class="button" href="estados/surtiendose.php">Surtiéndose</a>';
    echo '    <a class="button" href="estados/empacandose.php">Empacándose</a>';
    echo '    <a class="button" href="estados/en_ruta.php">En ruta</a>';
    echo '    <a class="button" href="estados/entregadas.php">Entregadas</a>';
    echo '    <h2>Catálogos</h2>';
    echo '    <a class="button" href="origenes/listado.php">Orígenes</a>';
    echo '    <a class="button" href="destinos/listado.php">Destinos</a>';
    echo '    <a class="button" href="cabeceras/listado.php">Rutas y tarifas</a>';
    echo '    <a class="button" href="tiendas/listado.php">Tiendas</a>';
    echo '    <a class="button" href="estados/listado.php">Catálogo de estados</a>';
    echo '    <a class="button secundario" href="rastreo.php">Rastrear un paquete</a>';
    echo '</div>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
