<?php
    require __DIR__ . '/../auth.php';
    require __DIR__ . '/../postsql.php';

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $id = '';
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }
    if (!is_string($id) || (filter_var($id, FILTER_VALIDATE_INT) === false || $id < 1 || $id > 2147483647)) {
        http_response_code(404);
        exit('ID no válido.');
    }

    $query = 'SELECT * FROM Seguimiento WHERE ID_seguimiento=$1';
    $result = pg_query_params($conn, $query, [$id]);
    $fila = pg_fetch_assoc($result);
    if (!$fila) {
        http_response_code(404);
        exit('Seguimiento no encontrado.');
    }
    $guia = trim($fila['no_guia']);
    $mensaje = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['decision'])) {
            http_response_code(400);
            exit('Recarga el formulario e inténtalo de nuevo.');
        }
        if (trim($_POST['decision']) === 'no') {
            header('Location: listado.php');
            exit;
        } else if (trim($_POST['decision']) === 'si') {
            pg_query($conn, 'BEGIN');
            pg_query_params($conn, 'SELECT No_guia FROM Envio WHERE No_guia=$1 FOR UPDATE', [$guia]);
            $query = 'SELECT * FROM Seguimiento WHERE No_guia=$1 ORDER BY Fecha DESC,Hora DESC,ID_seguimiento DESC LIMIT 2';
            $r = pg_query_params($conn, $query, [$guia]);
            $ultimo = pg_fetch_assoc($r);
            $anterior = pg_fetch_assoc($r);
            $ok = false;
            if ($ultimo && $anterior && $ultimo['id_seguimiento'] === $id) {
                $query = 'DELETE FROM Seguimiento WHERE ID_seguimiento=$1';
                $borrar = @pg_query_params($conn, $query, [$id]);
                $cambiar = false;
                if ($borrar) {
                    $cambiar = @pg_query_params($conn, 'UPDATE Envio SET ID_estado=$1,Fecha_entrega=CASE WHEN $1::int=5 THEN $2::date ELSE NULL END WHERE No_guia=$3', [$anterior['id_estado'], $anterior['fecha'], $guia]);
                }
                $ok = (bool)$cambiar;
            }
            if ($ok) {
                pg_query($conn, 'COMMIT');
            } else {
                pg_query($conn, 'ROLLBACK');
            }
            if ($ok) {
                $_SESSION['mensaje'] = 'Último avance eliminado; el envío volvió al estado anterior.';
                header('Location: listado.php?guia='.rawurlencode($guia));
                exit;
            }
            $mensaje = 'Solo se puede eliminar el último avance. La orden inicial debe conservarse.';
        }
    }
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
    <title>Eliminar último seguimiento - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Eliminar último seguimiento</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>¿Eliminar el registro ' . $id . ' de la guía ' . $guia . '?</p>';
    echo '<p class="mensaje">Solo se permite eliminar el último avance. El envío regresará al estado anterior.</p>';
    echo '<form method="post">';
    echo '<div class="acciones"><button name="decision" value="si">Sí, eliminar</button><button name="decision" value="no" class="secundario">No</button></div></form>';
    echo '<a href="listado.php">Volver al seguimiento</a>';
?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
