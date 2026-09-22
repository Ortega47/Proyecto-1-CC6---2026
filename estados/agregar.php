<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id_estado = "";
    $orden = "";
    $nombre = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_estado = trim($_POST["id_estado"]);
        $orden = trim($_POST["orden"]);
        $nombre = trim($_POST["nombre"]);

        if ($id_estado < 1 || $id_estado > 5 || $orden != $id_estado || $nombre == "") {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "INSERT INTO Estado (id_estado, orden, nombre) VALUES ($1, $2, $3)";
            $result = @pg_query_params($conn, $query, [$id_estado, $orden, $nombre]);

            if ($result) {
                $_SESSION["mensaje"] = "Registro guardado correctamente.";
                header("Location: listado.php");
                exit;
            } else {
                $mensaje = "No se pudo guardar. Revisa el ID y los datos ingresados.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar estado - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Agregar estado</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_estado">ID del estado (1 a 5)</label>';
    echo '    <input id="id_estado" name="id_estado" type="number" min="1" max="2147483647" step="1" required value="' . $id_estado . '">';

    echo '    <label for="orden">Orden (igual al ID)</label>';
    echo '    <input id="orden" name="orden" type="number" min="1" max="2147483647" step="1" required value="' . $orden . '">';

    echo '    <label for="nombre">Nombre</label>';
    echo '    <input id="nombre" name="nombre" type="text" maxlength="60" required value="' . $nombre . '">';

    echo '    <button>Guardar</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
