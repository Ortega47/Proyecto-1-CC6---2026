<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id = $_GET["id"];

    $query = "SELECT * FROM Seguimiento WHERE id_seguimiento=$1";
    $result = @pg_query_params($conn, $query, [$id]);
    if (!$result || pg_num_rows($result) == 0) {
        http_response_code(404);
        exit("El registro no existe.");
    }
    $fila = pg_fetch_assoc($result);
    $id_seguimiento = $fila["id_seguimiento"];
    $observacion = trim((string)$fila["observacion"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $observacion = trim($_POST["observacion"]);

        $query = "UPDATE Seguimiento SET observacion=$1 WHERE id_seguimiento=$2";
        $result = @pg_query_params($conn, $query, [$observacion, $id]);
        if ($result) {
            $_SESSION["mensaje"] = "Registro guardado correctamente.";
            header('Location: listado.php?guia='.rawurlencode(trim($fila['no_guia'])));
            exit;
        } else {
            $mensaje = "No se pudo guardar. Revisa los datos ingresados.";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar observación - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Editar observación</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>Guía: ' . $fila['no_guia'] . ' · ' . $fila['fecha'] . ' ' . substr($fila['hora'],0,5) . '</p>';
    echo '<p class="ayuda">El estado, la fecha y el usuario conservan los datos del avance registrado.</p>';
    echo '<form method="post">';

    echo '<label for="observacion">Observación</label>';
    echo '    <textarea id="observacion" name="observacion" rows="4" maxlength="200">' . $observacion . '</textarea><button>Guardar</button></form>';

    echo '<div class="enlaces"><a href="listado.php">Seguimiento</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
