<?php
    require __DIR__ . '/auth.php';
    require __DIR__ . '/postsql.php';

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
    <title>Conexión a PostgreSQL - Courier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="index.php">Menú principal</a>
    <a href="rastreo.php">Rastrear paquete</a>
    <a href="logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Conexión a PostgreSQL</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>La conexión usa las variables de entorno configuradas para el sitio.</p>';
    if (!function_exists('pg_connect')) {
        echo '<p class="mensaje">Activa extension=pgsql en php.ini y reinicia PHP o Apache.</p>';
    } else if (!$conn) {
        echo '<p class="mensaje">No se pudo conectar. Comprueba los datos de postsql.php y que PostgreSQL esté iniciado.</p>';
    } else {
        echo '<p class="exito">Conexión realizada correctamente.</p>';
        echo '<p>Servidor: ' . $host . ' · Puerto: ' . $port . '<br>Base de datos: ' . $dbname . '</p>';
    }
    echo '<form method="get"><button>Probar conexión de nuevo</button></form>';
    echo '<p><a href="login.php">Iniciar sesión</a></p>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
