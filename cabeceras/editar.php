<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id = $_GET["id"];

    $query = "SELECT * FROM Cabeceras WHERE id_cabecera=$1";
    $result = @pg_query_params($conn, $query, [$id]);
    if (!$result || pg_num_rows($result) == 0) {
        http_response_code(404);
        exit("El registro no existe.");
    }
    $fila = pg_fetch_assoc($result);
    $id_cabecera = $fila["id_cabecera"];
    $costo_envio = trim((string)$fila["costo_envio"]);
    $costo_manejo = trim((string)$fila["costo_manejo"]);
    $id_origen = trim((string)$fila["id_origen"]);
    $id_destino = trim((string)$fila["id_destino"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $costo_envio = trim($_POST["costo_envio"]);
        $costo_manejo = trim($_POST["costo_manejo"]);
        $id_origen = trim($_POST["id_origen"]);
        $id_destino = trim($_POST["id_destino"]);

        if (!is_numeric($costo_envio) || !is_numeric($costo_manejo) || $costo_envio < 0 || $costo_manejo < 0) {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "UPDATE Cabeceras SET costo_envio=$1, costo_manejo=$2, id_origen=$3, id_destino=$4 WHERE id_cabecera=$5";
            $result = @pg_query_params($conn, $query, [$costo_envio, $costo_manejo, $id_origen, $id_destino, $id]);
            if ($result) {
                $_SESSION["mensaje"] = "Registro guardado correctamente.";
                header('Location: listado.php');
                exit;
            } else {
                $mensaje = "No se pudo guardar. Revisa los datos ingresados.";
            }
        }
    }

    $query = "SELECT id_origen, ciudad FROM Origen ORDER BY ciudad";
    $opciones_id_origen = pg_query($conn, $query);

    $query = "SELECT id_destino, ciudad FROM Destino ORDER BY ciudad";
    $opciones_id_destino = pg_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar ruta - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Editar ruta</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_cabecera">ID de cabecera</label>';
    echo '    <input id="id_cabecera" name="id_cabecera" type="number" min="1" max="2147483647" step="1" readonly required value="' . $id_cabecera . '">';

    echo '    <label for="costo_envio">Costo de envío (Q)</label>';
    echo '    <input id="costo_envio" name="costo_envio" type="number" min="0" max="99999999.99" step="0.01" required value="' . $costo_envio . '">';

    echo '    <label for="costo_manejo">Costo de manejo (Q)</label>';
    echo '    <input id="costo_manejo" name="costo_manejo" type="number" min="0" max="99999999.99" step="0.01" required value="' . $costo_manejo . '">';

    echo '    <label for="id_origen">Origen</label>';
    echo '    <select id="id_origen" name="id_origen" required>';
    echo '        <option value="">Seleccionar...</option>';
    while ($opcion = pg_fetch_assoc($opciones_id_origen)) {
        echo '        <option value="' . $opcion['id_origen'] . '" ';
        if ($id_origen == $opcion['id_origen']) {
            echo 'selected';
        }
        echo '>' . $opcion['id_origen'].' - '.trim($opcion['ciudad']) . '</option>';
    }
    echo '    </select>';

    echo '    <label for="id_destino">Destino</label>';
    echo '    <select id="id_destino" name="id_destino" required>';
    echo '        <option value="">Seleccionar...</option>';
    while ($opcion = pg_fetch_assoc($opciones_id_destino)) {
        echo '        <option value="' . $opcion['id_destino'] . '" ';
        if ($id_destino == $opcion['id_destino']) {
            echo 'selected';
        }
        echo '>' . $opcion['id_destino'].' - '.trim($opcion['ciudad']) . '</option>';
    }
    echo '    </select>';

    echo '    <button>Guardar</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
