<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id = $_GET["id"];

    $query = "SELECT * FROM Estado WHERE id_estado=$1";
    $result = @pg_query_params($conn, $query, [$id]);
    if (!$result || pg_num_rows($result) == 0) {
        http_response_code(404);
        exit("El registro no existe.");
    }
    $fila = pg_fetch_assoc($result);
    $id_estado = $fila["id_estado"];
    $orden = trim((string)$fila["orden"]);
    $nombre = trim((string)$fila["nombre"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $orden = trim($_POST["orden"]);
        $nombre = trim($_POST["nombre"]);

        if ($id < 1 || $id > 5 || $orden != $id || $nombre == "") {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "UPDATE Estado SET orden=$1, nombre=$2 WHERE id_estado=$3";
            $result = @pg_query_params($conn, $query, [$orden, $nombre, $id]);
            if ($result) {
                $_SESSION["mensaje"] = "Registro guardado correctamente.";
                header('Location: listado.php');
                exit;
            } else {
                $mensaje = "No se pudo guardar. Revisa los datos ingresados.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar estado - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Editar estado</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_estado">ID del estado (1 a 5)</label>';
    echo '    <input id="id_estado" name="id_estado" type="number" min="1" max="2147483647" step="1" readonly required value="' . $id_estado . '">';

    echo '    <label for="orden">Orden (igual al ID)</label>';
    echo '    <input id="orden" name="orden" type="number" min="1" max="2147483647" step="1" required value="' . $orden . '">';

    echo '    <label for="nombre">Nombre</label>';
    echo '    <input id="nombre" name="nombre" type="text" maxlength="60" required value="' . $nombre . '">';

    echo '    <button>Guardar</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
