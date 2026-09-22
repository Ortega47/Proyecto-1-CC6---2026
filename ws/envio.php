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
    $destinatario = "";
    $destino = "";
    $direccion = "";
    $tienda = "";
    if (isset($_GET["orden"]) && is_string($_GET["orden"])) {
        $orden = trim($_GET["orden"]);
    }
    if (isset($_GET["destinatario"]) && is_string($_GET["destinatario"])) {
        $destinatario = trim($_GET["destinatario"]);
    }
    if (isset($_GET["destino"]) && is_string($_GET["destino"])) {
        $destino = trim($_GET["destino"]);
    }
    if (isset($_GET["direccion"]) && is_string($_GET["direccion"])) {
        $direccion = trim($_GET["direccion"]);
    }
    if (isset($_GET["tienda"]) && is_string($_GET["tienda"])) {
        $tienda = trim($_GET["tienda"]);
    }

    $id_destino = -1;
    $codigo_destino = $destino;
    if ($destino != "" && ctype_digit($destino)) {
        $id_destino = (int) $destino;
        $codigo_destino = str_pad((string) $id_destino, 5, "0", STR_PAD_LEFT);
    }
    $id_tienda = -1;
    $codigo_tienda = $tienda;
    if ($tienda != "" && ctype_digit($tienda)) {
        $id_tienda = (int) $tienda;
        $codigo_tienda = str_pad((string) $id_tienda, 15, "0", STR_PAD_LEFT);
    }

    $status = "RECHAZADO";
    $mensaje = "";
    $guia = "";
    $costo = "0.00";
    $fecha = "";
    $hora = "";
    $codigo_http = 200;

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        $codigo_http = 405;
        $mensaje = "Solo se acepta el método GET.";
    } else if ($orden == "" || $destinatario == "" || $destino == "" || $direccion == "" || $tienda == "") {
        $mensaje = "Faltan datos: orden, destinatario, destino, direccion y tienda son obligatorios.";
    } else if (strlen($orden) > 20) {
        $mensaje = "El número de orden tiene más de 20 caracteres.";
    } else if (strlen($direccion) > 255) {
        $mensaje = "La dirección tiene más de 255 caracteres.";
    } else if ($id_tienda < 0) {
        $mensaje = "La tienda no existe.";
    } else if ($id_destino < 0) {
        $mensaje = "El destino no existe.";
    } else {

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
            $mensaje = "No se pudo conectar con la base de datos.";
        } else {
            pg_set_client_encoding($conn, "UTF8");
            pg_query($conn, "SET TIME ZONE 'America/Guatemala'");
e
            $query = "SELECT ID_tienda FROM Tienda WHERE ID_tienda = $1";
            $result = @pg_query_params($conn, $query, [$id_tienda]);
            $cabecera = false;

            if (!$result) {
                $codigo_http = 500;
                $mensaje = "No se pudo consultar la base de datos.";
            } else if (pg_num_rows($result) == 0) {
                $mensaje = "La tienda no existe.";
            } else {
                $query = "SELECT CASE WHEN UPPER(TRIM(d.Cobertura)) IN ('SI','SÍ','TRUE','1') THEN 1 ELSE 0 END AS cobertura_destino,
                                 (SELECT CASE WHEN UPPER(TRIM(o.Cobertura)) IN ('SI','SÍ','TRUE','1') THEN 1 ELSE 0 END
                                  FROM Origen o WHERE o.ID_origen = $2) AS cobertura_origen
                          FROM Destino d
                          WHERE d.ID_destino = $1";
                $result = @pg_query_params($conn, $query, [$id_destino, ID_ORIGEN]);
                $fila = false;
                if ($result) {
                    $fila = pg_fetch_assoc($result);
                }

                if (!$result) {
                    $codigo_http = 500;
                    $mensaje = "No se pudo consultar la base de datos.";
                } else if (!$fila) {
                    $mensaje = "El destino no existe.";
                } else if ($fila["cobertura_destino"] != 1) {
                    $mensaje = "El destino no tiene cobertura.";
                } else if ($fila["cobertura_origen"] != 1) {
                    $mensaje = "El origen del courier no tiene cobertura.";
                } else {
                    $query = "SELECT ID_cabecera, costo_envio + costo_manejo AS costo
                              FROM Cabeceras
                              WHERE ID_origen = $1 AND ID_destino = $2
                              ORDER BY ID_cabecera
                              LIMIT 1";
                    $result = @pg_query_params($conn, $query, [ID_ORIGEN, $id_destino]);
                    if ($result) {
                        $cabecera = pg_fetch_assoc($result);
                    }

                    if (!$result) {
                        $codigo_http = 500;
                        $mensaje = "No se pudo consultar la base de datos.";
                    } else if (!$cabecera) {
                        $mensaje = "No hay ruta con tarifa hacia ese destino.";
                    } else {
                        $query = "SELECT No_guia FROM Envio WHERE ID_tienda = $1 AND No_orden = $2";
                        $result = @pg_query_params($conn, $query, [$id_tienda, $orden]);

                        if (!$result) {
                            $codigo_http = 500;
                            $mensaje = "No se pudo consultar la base de datos.";
                        } else if (pg_num_rows($result) > 0) {
                            $mensaje = "La tienda ya envió la orden " . $orden . ".";
                        }
                    }
                }
            }

            if ($mensaje == "" && $cabecera) {
                pg_query($conn, "BEGIN");
                pg_query($conn, "LOCK TABLE Envio IN EXCLUSIVE MODE");

                
                $query = "SELECT COALESCE(MAX(CAST(SUBSTRING(TRIM(No_guia) FROM $2::int) AS BIGINT)), 0) + 1 AS siguiente
                          FROM Envio
                          WHERE TRIM(No_guia) ~ $1";
                $result = @pg_query_params($conn, $query, ["^" . PREFIJO_GUIA . "[0-9]+$", strlen(PREFIJO_GUIA) + 1]);

                if ($result) {
                    $fila = pg_fetch_assoc($result);
                    $guia = PREFIJO_GUIA . str_pad($fila["siguiente"], 15 - strlen(PREFIJO_GUIA), "0", STR_PAD_LEFT);

                    $query = "INSERT INTO Envio
                              (No_guia, Fecha, Hora, Costo_total, Destinatario, ID_estado, ID_cabecera, ID_tienda, Direccion, No_orden)
                              VALUES ($1, CURRENT_DATE, LOCALTIME, $2, $3, 1, $4, $5, $6, $7)
                              RETURNING Fecha, Hora";
                    $result = @pg_query_params($conn, $query, [$guia, $cabecera["costo"], $destinatario, $cabecera["id_cabecera"], $id_tienda, $direccion, $orden]);
                }

                if ($result) {
                    $fila = pg_fetch_assoc($result);
                    pg_query($conn, "LOCK TABLE Seguimiento IN EXCLUSIVE MODE");
                    $query = "INSERT INTO Seguimiento
                              (ID_seguimiento, Observacion, Hora, Fecha, No_guia, ID_estado, ID_usuario)
                              SELECT COALESCE(MAX(ID_seguimiento),0)+1, 'Orden recibida de la tienda', LOCALTIME, CURRENT_DATE, $1, 1, 0
                              FROM Seguimiento";
                    $result = @pg_query_params($conn, $query, [$guia]);
                }

                if ($result) {
                    pg_query($conn, "COMMIT");
                    $status = "ACEPTADO";
                    $costo = number_format((float) $cabecera["costo"], 2, ".", "");
                    $fecha = date("Ymd", strtotime($fila["fecha"]));
                    $hora = substr($fila["hora"], 0, 5);
                } else {
                    pg_query($conn, "ROLLBACK");
                    $codigo_http = 500;
                    $guia = "";
                    $mensaje = "No se pudo registrar el envío. Intente de nuevo.";
                }
            }
            pg_close($conn);
        }
    }

    http_response_code($codigo_http);
    $respuesta = [
        "courrier" => CODIGO_COURIER,
        "orden" => $orden,
        "tienda" => $codigo_tienda,
        "destino" => $codigo_destino,
        "guia" => $guia,
        "status" => $status,
        "costo" => $costo,
        "fecha" => $fecha,
        "hora" => $hora
    ];
    if ($status == "RECHAZADO") {
        $respuesta["mensaje"] = $mensaje;
    }
    if ($formato == "JSON") {
        responder_json("envio", $respuesta);
    } else {
        responder_xml("envio", $respuesta);
    }
?>
