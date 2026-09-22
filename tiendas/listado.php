<?php
    require __DIR__ . '/../auth.php';
    require __DIR__ . '/../postsql.php';

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $query = 'SELECT id_tienda, no_orden, nombre, host FROM Tienda ORDER BY id_tienda';
    $result = pg_query($conn, $query);
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
    <title>Tiendas - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="contenido">
<h1>Tiendas</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<a class="button" href="agregar.php">Agregar tienda</a>';
    echo '<div class="tabla"><table>';
    echo '<tr><th scope="col">ID de tienda</th>';
    echo '    <th scope="col">Número de orden</th>';
    echo '    <th scope="col">Nombre</th>';
    echo '    <th scope="col">Host de la tienda</th>';
    echo '    <th>Acciones</th></tr>';
    while ($fila = pg_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $fila['id_tienda'] . '</td>';
        echo '<td>' . $fila['no_orden'] . '</td>';
        echo '<td>' . $fila['nombre'] . '</td>';
        echo '<td>' . $fila['host'] . '</td>';
        echo '<td>';
        echo '<a href="editar.php?id=' . $fila['id_tienda'] . '">Editar</a>';
        echo '<a href="eliminar.php?id=' . $fila['id_tienda'] . '">Eliminar</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table></div>';
    if (pg_num_rows($result) === 0) {
        echo '<p>No hay registros.</p>';
    }

    echo '<div class="enlaces"><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
