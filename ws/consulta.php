<?php
    // WebService: GET /consulta?destino=____&formato=____
    // Responde cobertura y costo (envío + manejo) desde el origen fijo hacia el destino.
    // Es público: sin sesión y sin redirecciones. Siempre responde en XML o JSON.
    ini_set("display_errors", "0");
    date_default_timezone_set("America/Guatemala");
    require __DIR__ . "/config.php";
    require __DIR__ . "/formato.php";

    $formato = "XML";
    if (isset($_GET["formato"]) && is_string($_GET["formato"]) && strtoupper(trim($_GET["formato"])) == "JSON") {
        $formato = "JSON";
    }

    $destino = "";
    if (isset($_GET["destino"]) && is_string($_GET["destino"])) {
        $destino = trim($_GET["destino"]);
    }

    // El código público tiene 5 caracteres con ceros a la izquierda; en la base es entero.
    $id_destino = -1;
    $codigo_destino = $destino;
    if ($destino != "" && ctype_digit($destino)) {
        $id_destino = (int) $destino;
        $codigo_destino = str_pad((string) $id_destino, 5, "0", STR_PAD_LEFT);
    }

    $cobertura = "FALSE";
    $costo = "0.00";
    $codigo_http = 200;

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        $codigo_http = 405;
    } else if ($id_destino >= 0) {
        // Misma conexión que postsql.php, pero sin die(): el servicio debe responder
        // en el formato pedido aunque la base no esté disponible.
        $host = getenv("DB_HOST");
        $port = getenv("DB_PORT");
        if (!$port) {
            $port = "5432";
        }
        $dbname = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $password = getenv("DB_PASSWORD");
        $password = str_replace(["\\", "'"], ["\\\\", "\\'"], (string) $password);
        $conn = @pg_connect("host=$host port=$port dbname=$dbname user=$user password='$password' connect_timeout=5");

        if (!$conn) {
            $codigo_http = 500;
        } else {
            pg_set_client_encoding($conn, "UTF8");
            pg_query($conn, "SET TIME ZONE 'America/Guatemala'");

            // Cabecera del origen fijo hacia el destino pedido. Si hay varias, la de menor ID.
            // Cobertura es texto ('SI' / 'NO'); se traduce a 1 / 0 igual que en envios/agregar.php.
            $query = "SELECT CASE WHEN UPPER(TRIM(d.Cobertura)) IN ('SI','SÍ','TRUE','1') THEN 1 ELSE 0 END AS cobertura_destino,
                             CASE WHEN UPPER(TRIM(o.Cobertura)) IN ('SI','SÍ','TRUE','1') THEN 1 ELSE 0 END AS cobertura_origen,
                             c.costo_envio + c.costo_manejo AS costo
                      FROM Destino d
                      JOIN Origen o ON o.ID_origen = $2
                      LEFT JOIN Cabeceras c ON c.ID_destino = d.ID_destino AND c.ID_origen = o.ID_origen
                      WHERE d.ID_destino = $1
                      ORDER BY c.ID_cabecera
                      LIMIT 1";
            $result = @pg_query_params($conn, $query, [$id_destino, ID_ORIGEN]);

            if (!$result) {
                $codigo_http = 500;
            } else {
                $fila = pg_fetch_assoc($result);
                // Hay cobertura solo si el destino y el origen la tienen y existe la ruta con tarifa.
                if ($fila && $fila["cobertura_destino"] == 1 && $fila["cobertura_origen"] == 1 && $fila["costo"] !== null) {
                    $cobertura = "TRUE";
                    $costo = number_format((float) $fila["costo"], 2, ".", "");
                }
            }
            pg_close($conn);
        }
    }

    http_response_code($codigo_http);
    $respuesta = [
        "courrier" => CODIGO_COURIER,
        "destino" => $codigo_destino,
        "cobertura" => $cobertura,
        "costo" => $costo
    ];
    if ($formato == "JSON") {
        responder_json("consultaprecio", $respuesta);
    } else {
        responder_xml("consultaprecio", $respuesta);
    }
?>
