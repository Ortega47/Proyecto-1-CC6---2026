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

    $query = 'SELECT No_guia,Destinatario FROM Envio WHERE No_guia=$1';
    $result = pg_query_params($conn, $query, [$guia]);
    $envio = pg_fetch_assoc($result);
    if (!$envio) {
        http_response_code(404);
        exit('Envío no encontrado.');
    }
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
            $query = 'DELETE FROM Envio WHERE No_guia=$1';
            $result = @pg_query_params($conn, $query, [$guia]);
            if ($result) {
                $_SESSION['mensaje'] = 'Envío y seguimiento eliminados.';
                header('Location: listado.php');
                exit;
            }
            $mensaje = 'No se pudo eliminar el envío.';
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
    <title>Eliminar envío - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Eliminar envío</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>¿Eliminar la guía <b>' . $guia . '</b> de ' . $envio['destinatario'] . '?</p>';
    echo '<p class="mensaje">También se eliminará todo su historial de seguimiento.</p>';
    echo '<form method="post">';
    echo '<div class="acciones"><button name="decision" value="si">Sí, eliminar</button><button name="decision" value="no" class="secundario">No</button></div></form>';
    echo '<a href="listado.php">Volver al listado</a>';
?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
