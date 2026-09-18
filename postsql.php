<?php
// Cambia estos datos por los de tu PostgreSQL, igual que en el proyecto de CC5.
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'courier';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: ''; // Tu contraseña de PostgreSQL.

$conn = false;
if (function_exists('pg_connect')) {
    // Las comillas permiten contraseñas que contienen espacios o caracteres especiales.
    $valores = [$host, $port, $dbname, $user, $password];
    foreach ($valores as &$valor) $valor = "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $valor) . "'";
    unset($valor);
    $conn = @pg_connect("host=$valores[0] port=$valores[1] dbname=$valores[2] user=$valores[3] password=$valores[4] connect_timeout=5");
}
if ($conn) {
    pg_set_client_encoding($conn, 'UTF8');
    pg_query($conn, "SET TIME ZONE 'America/Guatemala'");
} elseif (empty($probar_conexion)) {
    http_response_code(503);
    exit('El servicio no está disponible en este momento. Inténtalo de nuevo más tarde.');
}
