<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id_tienda = "";
    $no_orden = "";
    $nombre = "";
    $host = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_tienda = trim($_POST["id_tienda"]);
        $no_orden = trim($_POST["no_orden"]);
        $nombre = trim($_POST["nombre"]);
        $host = trim($_POST["host"]);

        if ($id_tienda < 1 || $no_orden < 1 || $nombre == "" || $host == "") {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "INSERT INTO Tienda (id_tienda, no_orden, nombre, host) VALUES ($1, $2, $3, $4)";
            $result = @pg_query_params($conn, $query, [$id_tienda, $no_orden, $nombre, $host]);

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
    <title>Agregar tienda - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Agregar tienda</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_tienda">ID de tienda</label>';
    echo '    <input id="id_tienda" name="id_tienda" type="number" min="1" max="2147483647" step="1" required value="' . $id_tienda . '">';

    echo '    <label for="no_orden">Número de orden</label>';
    echo '    <input id="no_orden" name="no_orden" type="number" min="1" max="2147483647" step="1" required value="' . $no_orden . '">';

    echo '    <label for="nombre">Nombre</label>';
    echo '    <input id="nombre" name="nombre" type="text" maxlength="150" required value="' . $nombre . '">';

    echo '    <label for="host">Host de la tienda</label>';
    echo '    <input id="host" name="host" type="text" maxlength="255" required value="' . $host . '">';

    echo '    <button>Guardar</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
