<?php
    require __DIR__ . '/../auth.php';
    require __DIR__ . '/../postsql.php';

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $query = 'SELECT id_origen, ciudad, cobertura FROM Origen ORDER BY id_origen';
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
    <title>Orígenes - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="contenido">
<h1>Orígenes</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<a class="button" href="agregar.php">Agregar origen</a>';
    echo '<div class="tabla"><table>';
    echo '<tr><th scope="col">ID del origen</th>';
    echo '    <th scope="col">Ciudad</th>';
    echo '    <th scope="col">Cobertura</th>';
    echo '    <th>Acciones</th></tr>';
    while ($fila = pg_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $fila['id_origen'] . '</td>';
        echo '<td>' . $fila['ciudad'] . '</td>';
        echo '<td>' . $fila['cobertura'] . '</td>';
        echo '<td>';
        echo '<a href="editar.php?id=' . $fila['id_origen'] . '">Editar</a>';
        echo '<a href="eliminar.php?id=' . $fila['id_origen'] . '">Eliminar</a>';
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
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
