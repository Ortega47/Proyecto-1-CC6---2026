<?php
    require __DIR__ . "/../auth.php";
    require __DIR__ . "/../postsql.php";

    if (!$_SESSION["admin"]) {
        header("Location: ../rastreo.php");
        exit;
    }

    $mensaje = "";
    $guia = "";
    $fecha = date("Y-m-d");
    $hora = date("H:i");
    $destinatario = "";
    $cabecera = "";
    $tienda = "";
    $costo = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $guia = trim($_POST["no_guia"]);
        $fecha = $_POST["fecha"];
        $hora = $_POST["hora"];
        $destinatario = trim($_POST["destinatario"]);
        $cabecera = $_POST["id_cabecera"];
        $tienda = $_POST["id_tienda"];
        $costo = $_POST["costo_total"];

        $query = "SELECT c.costo_envio+c.costo_manejo AS total
                  FROM Cabeceras c
                  JOIN Origen o ON c.ID_origen=o.ID_origen
                  JOIN Destino d ON c.ID_destino=d.ID_destino
                  WHERE c.ID_cabecera=$1
                  AND UPPER(TRIM(o.Cobertura)) IN ('SI','SÍ','TRUE','1')
                  AND UPPER(TRIM(d.Cobertura)) IN ('SI','SÍ','TRUE','1')";
        $result = @pg_query_params($conn, $query, [$cabecera]);
        if (!$result || pg_num_rows($result) == 0) {
            $mensaje = "Selecciona una ruta existente con cobertura.";
        } else {
            $ruta = pg_fetch_assoc($result);
            if ($costo == "") {
                $costo = $ruta["total"];
            }
            if ($guia == "" || $destinatario == "" || !is_numeric($costo) || $costo < 0) {
                $mensaje = "Revisa el destinatario y el costo.";
            } else if (strtotime($fecha . " " . $hora) > time()) {
                $mensaje = "La fecha y hora no pueden ser futuras.";
            } else {
                pg_query($conn, "BEGIN");
                $query = "INSERT INTO Envio
                          (No_guia, Fecha, Hora, Costo_total, Destinatario, ID_estado, ID_cabecera, ID_tienda)
                          VALUES ($1,$2,$3,$4,$5,1,$6,$7)";
                $result = @pg_query_params($conn, $query, [$guia, $fecha, $hora, $costo, $destinatario, $cabecera, $tienda]);

                if ($result) {
                    pg_query($conn, "LOCK TABLE Seguimiento IN EXCLUSIVE MODE");
                    $query = "INSERT INTO Seguimiento
                              (ID_seguimiento, Observacion, Hora, Fecha, No_guia, ID_estado, ID_usuario)
                              SELECT COALESCE(MAX(ID_seguimiento),0)+1,'Orden registrada',$1,$2,$3,1,$4
                              FROM Seguimiento";
                    $result = @pg_query_params($conn, $query, [$hora, $fecha, $guia, $_SESSION["id"]]);
                }

                if ($result) {
                    pg_query($conn, "COMMIT");
                    $_SESSION["mensaje"] = "Envío guardado.";
                    header("Location: listado.php");
                    exit;
                } else {
                    pg_query($conn, "ROLLBACK");
                    $mensaje = "No se pudo guardar. Revisa la guía y los datos ingresados.";
                }
            }
        }
    }

    $query = "SELECT c.*, o.Ciudad AS origen, d.Ciudad AS destino
              FROM Cabeceras c
              JOIN Origen o ON c.ID_origen=o.ID_origen
              JOIN Destino d ON c.ID_destino=d.ID_destino
              ORDER BY c.ID_cabecera";
    $rutas = pg_query($conn, $query);

    $query = "SELECT ID_tienda, Nombre FROM Tienda ORDER BY Nombre";
    $tiendas = pg_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar envío - Courier</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <a href="../index.php">Menú principal</a>
    <a href="../rastreo.php">Rastrear paquete</a>
    <a href="../logout.php">Cerrar sesión</a>
</header>
<main class="formulario">
<h1>Agregar envío</h1>
<?php
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="no_guia">Número de guía</label>';
    echo '    <input type="text" id="no_guia" name="no_guia" maxlength="20" value="' . $guia . '"  required>';

    echo '    <label for="fecha">Fecha</label>';
    echo '    <input type="date" id="fecha" name="fecha" value="' . $fecha . '" required>';

    echo '    <label for="hora">Hora</label>';
    echo '    <input type="time" id="hora" name="hora" value="' . $hora . '" required>';

    echo '    <label for="destinatario">Destinatario</label>';
    echo '    <input type="text" id="destinatario" name="destinatario" maxlength="150" value="' . $destinatario . '" required>';

    echo '    <label for="id_tienda">Tienda</label>';
    echo '    <select id="id_tienda" name="id_tienda" required><option value="">Seleccionar...</option>';
    while ($t=pg_fetch_assoc($tiendas)) {
        echo '        <option value="' . $t['id_tienda'] . '" ';
        if ($tienda===$t['id_tienda']) {
            echo 'selected';
        }
        echo '>' . $t['nombre'] . '</option>';
    }
    echo '</select>';

    echo '    <label for="id_cabecera">Ruta y tarifa</label>';
    echo '    <select id="id_cabecera" name="id_cabecera" required><option value="">Seleccionar...</option>';
    while ($c=pg_fetch_assoc($rutas)) {
        echo '        <option value="' . $c['id_cabecera'] . '" ';
        if ($cabecera===$c['id_cabecera']) {
            echo 'selected';
        }
        echo '>' . trim($c['origen']).' → '.trim($c['destino']).' · Q'.number_format($c['costo_envio']+$c['costo_manejo'],2) . '</option>';
    }
    echo '</select>';

    echo '    <label for="costo_total">Costo total (Q)</label>';
    echo '    <input type="number" id="costo_total" name="costo_total" min="0" max="99999999.99" step="0.01" value="' . $costo . '">';
    echo '    <p class="ayuda">Vacío: se usa el costo de envío más manejo de la ruta. El envío inicia en Orden nueva.</p>';

    echo '    <button>Guardar envío</button>';
    echo '</form>';

    echo '<div class="enlaces"><a href="listado.php">Listado de envíos</a><a href="../index.php">Menú principal</a></div>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
