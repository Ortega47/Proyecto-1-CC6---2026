<?php
    require __DIR__ . '/../auth.php';
    require __DIR__ . '/../postsql.php';

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $guia = '';
    if (isset($_GET['guia']) && is_string($_GET['guia'])) {
        $guia = trim($_GET['guia']);
    }

    $query = 'SELECT s.*, e.Nombre AS estado, u.Nombre AS usuario FROM Seguimiento s JOIN Estado e ON s.ID_estado=e.ID_estado JOIN Usuario u ON s.ID_usuario=u.ID_usuario WHERE ($1::text=\'\' OR TRIM(s.No_guia)=$1) ORDER BY s.Fecha DESC,s.Hora DESC,s.ID_seguimiento DESC';
    $result = pg_query_params($conn, $query, [$guia]);
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
    <title>Seguimiento - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="contenido">
<h1>Seguimiento</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="get" class="filtros"><div><label for="guia">Número de guía</label>';
    echo '    <input type="text" name="guia" id="guia" maxlength="20" value="' . $guia . '"></div><button>Consultar</button></form>';
    echo '<p>Para registrar el siguiente estado, abre el envío y selecciona Cambiar estado.</p>';
    echo '<div class="tabla"><table><tr><th>ID</th>';
    echo '    <th>Guía</th>';
    echo '    <th>Fecha</th>';
    echo '    <th>Hora</th>';
    echo '    <th>Estado</th>';
    echo '    <th>Usuario</th>';
    echo '    <th>Observación</th>';
    echo '    <th>Acciones</th></tr>';
    while ($fila=pg_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $fila['id_seguimiento'] . '</td>';
        echo '    <td><a href="../envios/detalle.php?guia=' . rawurlencode(trim($fila['no_guia'])) . '">' . $fila['no_guia'] . '</a></td>';
        echo '    <td>' . $fila['fecha'] . '</td>';
        echo '    <td>' . substr($fila['hora'],0,5) . '</td>';
        echo '    <td>' . $fila['estado'] . '</td>';
        echo '    <td>' . $fila['usuario'] . '</td>';
        echo '    <td>' . $fila['observacion'] . '</td>';
        echo '<td><a href="editar.php?id=' . $fila['id_seguimiento'] . '">Editar observación</a><a href="eliminar.php?id=' . $fila['id_seguimiento'] . '">Eliminar</a></td></tr>';
    }
    echo '</table></div>';
    if (pg_num_rows($result)===0) {
        echo '<p>No hay registros de seguimiento.</p>';
    }

    echo '<div class="enlaces"><a href="../envios/listado.php">Ver envíos</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
