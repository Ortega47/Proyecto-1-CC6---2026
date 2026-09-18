<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $id_destino = "";
    $ciudad = "";
    $cobertura = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_destino = trim($_POST["id_destino"]);
        $ciudad = trim($_POST["ciudad"]);
        $cobertura = trim($_POST["cobertura"]);

        if ($id_destino < 1 || $ciudad == "" || ($cobertura != "SI" && $cobertura != "NO")) {
            $mensaje = "Revisa los campos del formulario.";
        } else {
            $query = "INSERT INTO Destino (id_destino, ciudad, cobertura) VALUES ($1, $2, $3)";
            $result = @pg_query_params($conn, $query, [$id_destino, $ciudad, $cobertura]);

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
    <title>Agregar destino - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Agregar destino</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="id_destino">ID del destino</label>';
    echo '    <input id="id_destino" name="id_destino" type="number" min="1" max="2147483647" step="1" required value="' . $id_destino . '">';

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
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
