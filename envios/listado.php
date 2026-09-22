<?php
    require __DIR__ . '/../auth.php';
    require __DIR__ . '/../postsql.php';

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $estado = '';
    if (isset($_GET['estado'])) {
        $estado = $_GET['estado'];
    }
    if (isset($estado_fijo)) {
        $estado = (string)$estado_fijo;
    }
    if (!is_string($estado)
        || ($estado !== '' && (filter_var($estado, FILTER_VALIDATE_INT) === false
        || $estado < 1
        || $estado > 2147483647))) {
        $estado = '';
    }
    $guia = '';
    if (isset($_GET['guia']) && is_string($_GET['guia'])) {
        $guia = trim($_GET['guia']);
    }

    $query = 'SELECT e.*, s.nombre AS estado, t.nombre AS tienda, d.ciudad AS destino
          FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado
          JOIN Tienda t ON e.ID_tienda=t.ID_tienda
          JOIN Cabeceras c ON e.ID_cabecera=c.ID_cabecera
          JOIN Destino d ON c.ID_destino=d.ID_destino
          WHERE ($1::int IS NULL OR e.ID_estado=$1) AND ($2::text=\'\' OR TRIM(e.No_guia) ILIKE $3)
          ORDER BY e.Fecha DESC,e.Hora DESC,e.No_guia';
    $filtro_estado = $estado;
    if ($estado === '') {
        $filtro_estado = null;
    }
    $result = pg_query_params($conn, $query, [$filtro_estado, $guia, '%'.$guia.'%']);

    $query = 'SELECT * FROM Estado ORDER BY Orden';
    $estados = pg_query($conn, $query);
    if (!isset($titulo_estado)) {
        $titulo_estado = 'Envíos';
    }
    $titulo = $titulo_estado;
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
    <title><?php
    echo $titulo . ' - Courier</title>';
    echo '    <link rel="stylesheet" href="../style.css">';
    echo '</head>';
    echo '<body>';
    echo '<header>';
    echo '    <a href="../index.php">Menú principal</a>';
    echo '    <a href="../rastreo.php">Rastrear paquete</a>';
    echo '    <a href="../logout.php">Cerrar sesión</a>';
    echo '</header>';
    echo '<main class="contenido">';
    echo '<h1>' . $titulo . '</h1>';
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<a class="button" href="../envios/agregar.php">Agregar envío</a>';
    echo '<form method="get" class="filtros">';
    if (!isset($estado_fijo)) {
        echo '<div><label for="estado">Estado</label>';
        echo '    <select id="estado" name="estado"><option value="">Todos</option>';
        while ($fila=pg_fetch_assoc($estados)) {
            echo '        <option value="' . $fila['id_estado'] . '" ';
            if ($estado===$fila['id_estado']) {
                echo 'selected';
            }
            echo '>' . $fila['nombre'] . '</option>';
        }
        echo '    </select></div>';
    }
    echo '    <div><label for="guia">Guía</label>';
    echo '    <input type="text" id="guia" name="guia" maxlength="20" value="' . $guia . '"></div>';

    echo '    <button>Filtrar</button>';
    echo '</form>';
    echo '<div class="tabla"><table>';
    echo '<tr><th>Guía</th>';
    echo '    <th>Fecha</th>';
    echo '    <th>Tienda</th>';
    echo '    <th>Destino</th>';
    echo '    <th>Destinatario</th>';
    echo '    <th>Estado</th>';
    echo '    <th>Total</th>';
    echo '    <th>Acciones</th></tr>';
    while ($fila=pg_fetch_assoc($result)) {
        $g=rawurlencode(trim($fila['no_guia']));
        echo '<tr><td>' . $fila['no_guia'] . '</td>';
        echo '    <td>' . $fila['fecha'] . '</td>';
        echo '    <td>' . $fila['tienda'] . '</td>';
        echo '    <td>' . $fila['destino'] . '</td>';
        echo '    <td>' . $fila['destinatario'] . '</td>';
        echo '    <td>' . $fila['estado'] . '</td>';
        echo '    <td>Q' . number_format((float)$fila['costo_total'],2) . '</td>';
        echo '<td><a href="../envios/detalle.php?guia=' . $g . '">Detalle</a>';
        if ((int)$fila['id_estado']<5) {
            echo '<a href="../seguimiento/agregar.php?guia=' . $g . '">Cambiar estado</a>';
        }
        echo '<a href="../envios/editar.php?guia=' . $g . '">Editar</a><a href="../envios/eliminar.php?guia=' . $g . '">Eliminar</a></td></tr>';
    }
    echo '</table></div>';
    if (pg_num_rows($result)===0) {
        echo '<p>No hay envíos para estos filtros.</p>';
    }

    echo '<div class="enlaces"><a href="../envios/listado.php">Todos los envíos</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
