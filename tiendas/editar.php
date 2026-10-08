<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id = $_GET["id"];

    $query = "SELECT * FROM Tienda WHERE id_tienda=$1";
    $result = @pg_query_params($conn, $query, [$id]);
    if (!$result || pg_num_rows($result) == 0) {
        http_response_code(404);
        exit("El registro no existe.");
    }
    $fila = pg_fetch_assoc($result);
    $id_tienda = $fila["id_tienda"];
    $nombre = trim((string)$fila["nombre"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = trim($_POST["nombre"]);

        if ($nombre == "") {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "UPDATE Tienda SET nombre=$1 WHERE id_tienda=$2";
            $result = @pg_query_params($conn, $query, [$nombre, $id]);
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
    <title>Editar tienda - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Editar tienda</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_tienda">ID de tienda</label>';
    echo '    <input id="id_tienda" name="id_tienda" type="number" min="1" max="2147483647" step="1" readonly required value="' . $id_tienda . '">';

    echo '    <label for="nombre">Nombre</label>';
    echo '    <input id="nombre" name="nombre" type="text" maxlength="150" required value="' . $nombre . '">';

    echo '    <button>Guardar</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
