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

    $query = 'SELECT e.*,s.Nombre AS estado,t.Nombre AS tienda,t.No_orden,o.Ciudad AS origen,d.Ciudad AS destino FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado JOIN Tienda t ON e.ID_tienda=t.ID_tienda JOIN Cabeceras c ON e.ID_cabecera=c.ID_cabecera JOIN Origen o ON c.ID_origen=o.ID_origen JOIN Destino d ON c.ID_destino=d.ID_destino WHERE e.No_guia=$1';
    $result = pg_query_params($conn, $query, [$guia]);
    $envio = pg_fetch_assoc($result);
    if (!$envio) {
        http_response_code(404);
        exit('Envío no encontrado.');
    }

    $query = 'SELECT s.*,e.Nombre AS estado,u.Nombre AS usuario FROM Seguimiento s JOIN Estado e ON s.ID_estado=e.ID_estado JOIN Usuario u ON s.ID_usuario=u.ID_usuario WHERE s.No_guia=$1 ORDER BY s.Fecha,s.Hora,s.ID_seguimiento';
    $historial = pg_query_params($conn, $query, [$guia]);
    $mensaje = '';
    if ($mensaje === '' && isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
    }
    unset($_SESSION['mensaje']);
    $fecha_entrega = 'Pendiente';
    if ($envio && !empty($envio['fecha_entrega'])) {
        $fecha_entrega = $envio['fecha_entrega'];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del envío - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="contenido">
<h1>Detalle del envío</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<dl>';
    echo '<dt>Guía</dt><dd>' . $envio['no_guia'] . '</dd>';
    echo '<dt>Destinatario</dt><dd>' . $envio['destinatario'] . '</dd>';
    echo '<dt>Tienda / orden</dt><dd>' . $envio['tienda'] . ' / ' . $envio['no_orden'] . '</dd>';
    echo '<dt>Ruta</dt><dd>' . $envio['origen'] . ' → ' . $envio['destino'] . '</dd>';
    echo '<dt>Recibido</dt><dd>' . $envio['fecha'] . ' ' . substr($envio['hora'],0,5) . '</dd>';
    echo '<dt>Estado</dt><dd>' . $envio['estado'] . '</dd>';
    echo '<dt>Fecha de entrega</dt><dd>' . $fecha_entrega . '</dd>';
    echo '<dt>Total</dt><dd>Q' . number_format((float)$envio['costo_total'],2) . '</dd>';
    echo '</dl>';
    if ((int)$envio['id_estado']<5) {
        echo '<a class="button" href="../seguimiento/agregar.php?guia=' . rawurlencode($guia) . '">Cambiar estado</a>';
    }
    echo '<h2>Historial</h2>';
    echo '<div class="tabla"><table><tr><th>Fecha</th>';
    echo '    <th>Hora</th>';
    echo '    <th>Estado</th>';
    echo '    <th>Usuario</th>';
    echo '    <th>Observación</th></tr>';
    while ($s=pg_fetch_assoc($historial)) {
        echo '<tr><td>' . $s['fecha'] . '</td>';
        echo '    <td>' . substr($s['hora'],0,5) . '</td>';
        echo '    <td>' . $s['estado'] . '</td>';
        echo '    <td>' . $s['usuario'] . '</td>';
        echo '    <td>' . $s['observacion'] . '</td></tr>';
    }
    echo '</table></div>';

    echo '<div class="enlaces"><a href="listado.php">Listado de envíos</a><a href="../seguimiento/listado.php?guia=' . rawurlencode($guia) . '">Administrar seguimiento</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
