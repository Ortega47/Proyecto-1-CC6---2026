<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $guia = $_GET["guia"];
    $mensaje = "";
    $observacion = "";

    $query = "SELECT e.*, s.Nombre AS estado FROM Envio e
              JOIN Estado s ON e.ID_estado=s.ID_estado WHERE e.No_guia=$1";
    $result = pg_query_params($conn, $query, [$guia]);
    $envio = pg_fetch_assoc($result);
    if (!$envio) {
        http_response_code(404);
        exit("Envío no encontrado.");
    }

    $actual = $envio["id_estado"];

    $query = "SELECT * FROM Estado WHERE ID_estado=$1";
    $result = pg_query_params($conn, $query, [$actual + 1]);
    $siguiente = pg_fetch_assoc($result);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $observacion = $_POST["observacion"];

        if (!$siguiente || $actual >= 5 || $actual != $_POST["estado_actual"]) {
            $mensaje = "No se puede avanzar. Revisa el estado actual del envío.";
        } else {
            pg_query($conn, "BEGIN");
            $query = "UPDATE Envio SET ID_estado=$1,
                      Fecha_entrega=CASE WHEN $1::int=5 THEN CURRENT_DATE ELSE NULL END
                      WHERE No_guia=$2 AND ID_estado=$3";
            $result = @pg_query_params($conn, $query, [$actual + 1, $guia, $actual]);

            if ($result && pg_affected_rows($result) == 1) {
                pg_query($conn, "LOCK TABLE Seguimiento IN EXCLUSIVE MODE");
                $query = "INSERT INTO Seguimiento
                          (ID_seguimiento, Observacion, Hora, Fecha, No_guia, ID_estado, ID_usuario)
                          SELECT COALESCE(MAX(ID_seguimiento),0)+1, $1, LOCALTIME, CURRENT_DATE, $2, $3, $4
                          FROM Seguimiento";
                $result = @pg_query_params($conn, $query, [$observacion, $guia, $actual + 1, $_SESSION["id"]]);
            } else {
                $result = false;
            }

            if ($result) {
                pg_query($conn, "COMMIT");
                header("Location: ../envios/detalle.php?guia=" . rawurlencode($guia));
                exit;
            } else {
                pg_query($conn, "ROLLBACK");
                $mensaje = "No se pudo guardar el seguimiento.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar estado - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Cambiar estado</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>Guía: <b>' . $guia . '</b><br>Destinatario: ' . $envio['destinatario'] . '<br>Estado actual: ' . $envio['estado'] . '</p>';
    if ($siguiente && $actual<5) {
        echo '<form method="post">';
        echo '    <input type="hidden" name="estado_actual" value="' . $actual . '">';

        echo '    <label for="observacion">Observación (opcional)</label>';
        echo '    <textarea id="observacion" name="observacion" maxlength="200" rows="3">' . $observacion . '</textarea>';
        echo '    <p>¿Pasar a <b>' . $siguiente['nombre'] . '</b>?</p>';
        echo '    <div class="acciones"><button name="decision" value="si">Sí, cambiar estado</button><a href="../envios/listado.php" class="secundario">Cancelar</a></div>';
        echo '</form>';
    } else {
        echo '<p>El envío está entregado o no tiene un siguiente estado configurado.</p>';
    }
    echo '<a href="../envios/detalle.php?guia=' . rawurlencode($guia) . '">Volver al envío</a>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
