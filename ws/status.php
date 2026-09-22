<?php
   
    ini_set("display_errors", "0");
    date_default_timezone_set("America/Guatemala");
    require __DIR__ . "/config.php";
    require __DIR__ . "/formato.php";

    $formato = "XML";
    if (isset($_GET["formato"]) && is_string($_GET["formato"]) && strtoupper(trim($_GET["formato"])) == "JSON") {
        $formato = "JSON";
    }

    $orden = "";
    if (isset($_GET["orden"]) && is_string($_GET["orden"])) {
        $orden = trim($_GET["orden"]);
    }

    $tienda = "";
    if (isset($_GET["tienda"]) && is_string($_GET["tienda"])) {
        $tienda = trim($_GET["tienda"]);
    }

    $id_tienda = -1;
    if ($tienda != "" && ctype_digit($tienda)) {
        $id_tienda = (int) $tienda;
    }

    $status = "NO ENCONTRADO";
    $codigo_http = 200;

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        $codigo_http = 405;
        $status = "ERROR";
    } else if ($orden != "" && $id_tienda >= 0) {
      
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
            $status = "ERROR";
        } else {
            pg_set_client_encoding($conn, "UTF8");
            pg_query($conn, "SET TIME ZONE 'America/Guatemala'");

            $query = "SELECT s.Nombre
                      FROM Envio e
                      JOIN Estado s ON e.ID_estado = s.ID_estado
                      WHERE e.ID_tienda = $1 AND e.No_orden = $2";
            $result = @pg_query_params($conn, $query, [$id_tienda, $orden]);

            if (!$result) {
                $codigo_http = 500;
                $status = "ERROR";
            } else {
                $fila = pg_fetch_assoc($result);
                if ($fila) {
                    $status = trim($fila["nombre"]);
                }
            }
            pg_close($conn);
        }
    }

    http_response_code($codigo_http);
    $respuesta = [
        "courrier" => CODIGO_COURIER,
        "orden" => $orden,
        "status" => $status
    ];
    if ($formato == "JSON") {
        responder_json("orden", $respuesta);
    } else {
        responder_xml("orden", $respuesta);
    }
?>
