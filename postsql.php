<?php
    $host = getenv("DB_HOST");
    $port = getenv("DB_PORT");
    if (!$port) {
        $port = "5432";
    }
    $dbname = getenv("DB_NAME");
    $user = getenv("DB_USER");
    $password = getenv("DB_PASSWORD");
    $password = str_replace(["\\", "'"], ["\\\\", "\\'"], (string)$password);

    $conn = @pg_connect("host=$host port=$port dbname=$dbname user=$user password='$password' connect_timeout=5")
        or die("No se pudo conectar con la base de datos.");

    date_default_timezone_set("America/Guatemala");
    pg_set_client_encoding($conn, "UTF8");
    pg_query($conn, "SET TIME ZONE 'America/Guatemala'");
?>
