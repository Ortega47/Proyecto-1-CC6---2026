<?php
session_start();
$raiz = '';
require __DIR__ . '/funciones.php';
require __DIR__ . '/datos_demo.php';

if (isset($_SESSION['id_usuario'])) {
    header('Location: menu.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario    = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    // TODO(bd): $r = pg_query_params($conn,
    //   'SELECT id_usuario, nombre, contrasena, es_admin FROM usuario WHERE usuario = $1', [$usuario]);
    //   $fila = pg_fetch_assoc($r);
    $fila = buscar('usuario', 'usuario', $usuario);

    if ($fila !== null && password_verify($contrasena, $fila['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['nombre']     = $fila['nombre'];
        $_SESSION['es_admin']   = (bool) $fila['es_admin'];
        header('Location: menu.php');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos';
}

$titulo = 'Acceso del personal';
$sin_acceso = true;
require __DIR__ . '/encabezado.php';
?>
<div class="tarjeta">
    <h1>Acceso del personal</h1>
    <?php if ($error !== ''): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" required autocomplete="username" autocapitalize="none" spellcheck="false" value="<?= h($_POST['usuario'] ?? '') ?>">

        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password">

        <button type="submit">Iniciar sesión</button>
    </form>
    <p><a href="index.php">Rastrear un paquete</a></p>
</div>
<?php require __DIR__ . '/pie.php'; ?>
