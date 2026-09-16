<?php
// Exige sesión de administrador. Quien no lo sea vuelve al menú con un aviso.
require_once __DIR__ . '/auth.php';
if (empty($_SESSION['es_admin'])) {
    $_SESSION['aviso'] = 'Esta sección es solo para administradores';
    header('Location: ' . $raiz . 'menu.php');
    exit;
}
