<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id = $_GET["id"];

    $query = "SELECT * FROM Destino WHERE id_destino=$1";
    $result = @pg_query_params($conn, $query, [$id]);
    if (!$result || pg_num_rows($result) == 0) {
        http_response_code(404);
        exit("El registro no existe.");
    }
    $fila = pg_fetch_assoc($result);
    $id_destino = $fila["id_destino"];
    $ciudad = trim((string)$fila["ciudad"]);
    $cobertura = trim((string)$fila["cobertura"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ciudad = trim($_POST["ciudad"]);
        $cobertura = trim($_POST["cobertura"]);

        if ($ciudad == "" || ($cobertura != "SI" && $cobertura != "NO")) {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "UPDATE Destino SET ciudad=$1, cobertura=$2 WHERE id_destino=$3";
            $result = @pg_query_params($conn, $query, [$ciudad, $cobertura, $id]);
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
    <title>Editar destino - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Editar destino</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_destino">ID del destino</label>';
    echo '    <input id="id_destino" name="id_destino" type="number" min="1" max="2147483647" step="1" readonly required value="' . $id_destino . '">';

    echo '    <label for="ciudad">Ciudad</label>';
    echo '    <input id="ciudad" name="ciudad" type="text" maxlength="100" required value="' . $ciudad . '">';

    echo '    <label for="cobertura">Cobertura</label>';
    echo '    <select id="cobertura" name="cobertura" required>';
    echo '        <option value="SI" ';
    if (in_array(mb_strtoupper($cobertura),['SI','SÍ','TRUE','1'])) {
        echo 'selected';
    }
    echo '>Sí</option>';
    echo '        <option value="NO" ';
    if (in_array(mb_strtoupper($cobertura),['NO','FALSE','0'])) {
        echo 'selected';
    }
    echo '>No</option>';
    echo '    </select>';

    echo '    <button>Guardar</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
