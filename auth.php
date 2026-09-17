<?php
session_start();
date_default_timezone_set('America/Guatemala');
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(24));
}

$raiz = $raiz ?? '';
if (!isset($_SESSION['id'])) {
    header('Location: '.$raiz.'login.php'); exit;
}
require_once __DIR__ . '/postsql.php';
$result = pg_query_params($conn, 'SELECT nombre FROM Usuario WHERE ID_usuario=$1', [$_SESSION['id']]);
$usuario_actual = $result ? pg_fetch_assoc($result) : false;
if (!$usuario_actual) {
    session_unset(); header('Location: '.$raiz.'login.php'); exit;
}
$_SESSION['nombre'] = trim($usuario_actual['nombre']);
// Igual que en CC5: el usuario con ID 0 es el administrador.
$_SESSION['admin'] = (int)$_SESSION['id'] === 0;
