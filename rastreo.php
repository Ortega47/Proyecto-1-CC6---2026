<?php
    session_start();
    date_default_timezone_set('America/Guatemala');
    require __DIR__ . '/postsql.php';
    $guia = '';
    if (isset($_GET['guia']) && is_string($_GET['guia'])) {
        $guia = trim($_GET['guia']);
    }
    $envio = false;
    $historial = false;
    $mensaje = '';
    if ($guia !== '') {
        $query = 'SELECT e.No_guia,e.Fecha,e.Fecha_entrega,s.Nombre AS estado,o.Ciudad AS origen,d.Ciudad AS destino FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado JOIN Cabeceras c ON e.ID_cabecera=c.ID_cabecera JOIN Origen o ON c.ID_origen=o.ID_origen JOIN Destino d ON c.ID_destino=d.ID_destino WHERE e.No_guia=$1';
        $r = pg_query_params($conn, $query, [$guia]);
        $envio = pg_fetch_assoc($r);
        if (!$envio) {
            $mensaje = 'No se encontró un envío con esa guía.';
        } else $historial = pg_query_params($conn, 'SELECT s.Fecha,s.Hora,e.Nombre AS estado FROM Seguimiento s JOIN Estado e ON s.ID_estado=e.ID_estado WHERE s.No_guia=$1 ORDER BY s.Fecha,s.Hora,s.ID_seguimiento', [$guia]);
    }
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
    <title>Rastrear un paquete - Courier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="index.php">Winni Express</a>
    <nav><a href="rastreo.php">Rastrear paquete</a>
    <?php
    if (isset($_SESSION['id'])) {
        if (empty($_SESSION['cliente'])) {
            echo '<a href="index.php">Menú</a>';
        }
        echo '        <a href="logout.php">Cerrar sesión</a>';
    } else {
        echo '<a href="login.php">Iniciar sesión</a>';
    }
    echo '    </nav>';
    echo '</header>';
    echo '<main class="formulario">';
    echo '<h1>Rastrear un paquete</h1>';
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="get"><label for="guia">Número de guía</label>';
    echo '    <input id="guia" name="guia" type="text" maxlength="20" value="' . $guia . '" required><button>Consultar</button></form>';
    if ($envio) {
        echo '<h2>' . $envio['estado'] . '</h2>';
        echo '<p>Guía: ' . $envio['no_guia'] . '<br>' . $envio['origen'] . ' → ' . $envio['destino'] . '<br>Fecha de entrega: ' . $fecha_entrega . '</p>';
        echo '<div class="tabla"><table><tr><th>Fecha</th>';
        echo '    <th>Hora</th>';
        echo '    <th>Estado</th></tr>';
        while ($fila=pg_fetch_assoc($historial)) {
            echo '<tr><td>' . $fila['fecha'] . '</td>';
            echo '    <td>' . substr($fila['hora'],0,5) . '</td>';
            echo '    <td>' . $fila['estado'] . '</td></tr>';
        }
        echo '</table></div>';
    }
    if (!isset($_SESSION['id'])) {
        echo '<p><a href="login.php">Iniciar sesión</a> · <a href="register.php">Crear cuenta</a></p>';
    }
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
