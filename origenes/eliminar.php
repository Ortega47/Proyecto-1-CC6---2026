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
        exit('Identificador no válido.');
    }

    $query = 'SELECT id_origen FROM Origen WHERE id_origen=$1';
    $result = pg_query_params($conn, $query, [$id]);
    if (pg_num_rows($result) === 0) {
        http_response_code(404);
        exit('El registro no existe.');
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
            $query = 'DELETE FROM Origen WHERE id_origen=$1';
            $result = @pg_query_params($conn, $query, [$id]);
            if ($result) {
                $_SESSION['mensaje'] = 'Registro eliminado.';
                header('Location: listado.php');
                exit;
            }
            $mensaje = 'No se puede eliminar porque otros registros lo utilizan.';
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
    <title>Eliminar origen - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Eliminar origen</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>¿Eliminar el registro con ID <b>' . $id . '</b>?</p>';
    echo '<form method="post">';
    echo '    <div class="acciones"><button name="decision" value="si">Sí, eliminar</button><button name="decision" value="no" class="secundario">No</button></div>';
    echo '</form>';
    echo '<a href="listado.php">Volver al listado</a>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
