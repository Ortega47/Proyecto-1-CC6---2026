<?php
// Exige sesión iniciada. La página que lo incluye define antes $raiz
// ('' en la raíz, '../' en las subcarpetas) para el redirect.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ' . $raiz . 'login.php');
    exit;
}
