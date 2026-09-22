<?php
    require __DIR__ . '/../auth.php';
    require __DIR__ . '/../postsql.php';

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $query = 'SELECT c.*, o.ciudad AS origen, d.ciudad AS destino FROM Cabeceras c JOIN Origen o ON c.id_origen=o.id_origen JOIN Destino d ON c.id_destino=d.id_destino ORDER BY c.id_cabecera';
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
    <title>Rutas y tarifas - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="contenido">
<h1>Rutas y tarifas</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<a class="button" href="agregar.php">Agregar ruta</a>';
    echo '<div class="tabla"><table>';
    echo '<tr><th scope="col">ID de cabecera</th>';
    echo '    <th scope="col">Costo de envío (Q)</th>';
    echo '    <th scope="col">Costo de manejo (Q)</th>';
    echo '    <th scope="col">Origen</th>';
    echo '    <th scope="col">Destino</th>';
    echo '    <th>Acciones</th></tr>';
    while ($fila = pg_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $fila['id_cabecera'] . '</td>';
        echo '<td>' . 'Q'.number_format((float)$fila['costo_envio'],2) . '</td>';
        echo '<td>' . 'Q'.number_format((float)$fila['costo_manejo'],2) . '</td>';
        echo '<td>' . $fila['origen'] . '</td>';
        echo '<td>' . $fila['destino'] . '</td>';
        echo '<td>';
        echo '<a href="editar.php?id=' . $fila['id_cabecera'] . '">Editar</a>';
        echo '<a href="eliminar.php?id=' . $fila['id_cabecera'] . '">Eliminar</a>';
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
