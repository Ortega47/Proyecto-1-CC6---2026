<?php
session_start();
date_default_timezone_set('America/Guatemala');
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(24));
}

require __DIR__.'/postsql.php';
$cantidad = pg_fetch_result(pg_query($conn,'SELECT COUNT(*) FROM Usuario'),0,0);
if ((int)$cantidad !== 0) { header('Location: login.php'); exit; }
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'],true)) {
    http_response_code(403); exit('Crea el primer administrador desde localhost en el servidor.');
}
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $nombre = (is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '');
    $usuario = (is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '');
    $clave = is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '';
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario.';
    }
    elseif (
        ($nombre === '' ||
        mb_strlen($nombre) > 50) ||
        ($usuario === '' ||
        mb_strlen($usuario) > 50) ||
        strlen($clave)<8 ||
        strlen($clave)>72
    ) {
        $mensaje='Completa nombre, usuario y una contraseña de 8 a 72 bytes.';
    }
    else {
        pg_query($conn,'BEGIN');
        pg_query($conn,'LOCK TABLE Usuario IN EXCLUSIVE MODE');
        $cantidad=pg_fetch_result(pg_query($conn,'SELECT COUNT(*) FROM Usuario'),0,0);
        $result=false;
        if ((int)$cantidad === 0) $result=@pg_query_params($conn,'INSERT INTO Usuario (ID_usuario,Nombre,Usuario,Contraseña) VALUES (0,$1,$2,$3)',[$nombre,$usuario,password_hash($clave,PASSWORD_DEFAULT)]);
        if ($result) {
            pg_query($conn,'COMMIT');
            $_SESSION['mensaje']='Administrador creado. Inicia sesión.';
            header('Location: login.php'); exit;
        }
        pg_query($conn,'ROLLBACK');
        $mensaje='No se pudo crear el administrador. Revisa el tamaño de Contraseña en pgAdmin o si ya se registró otro usuario.';
    }
}
$titulo='Crear primer administrador';
$formulario=true;

$raiz = $raiz ?? '';
if ($mensaje === '' && isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(trim((string) ($titulo))) ?> - Courier</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(trim((string) ($raiz))) ?>style.css">
</head>
<body>
<header>
    <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>index.php">Courier</a>
    <nav><a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>rastreo.php">Rastrear paquete</a>
    <?php if (isset($_SESSION['id'])): ?>
        <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>index.php">Menú</a>
        <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>logout.php">Cerrar sesión</a>
    <?php else: ?><a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>login.php">Iniciar sesión</a><?php endif; ?>
    </nav>
</header>
<main class="<?= !empty($formulario) ? 'formulario' : 'contenido' ?>">
<h1><?= htmlspecialchars(trim((string) ($titulo))) ?></h1>
<?php if ($mensaje !== ''): ?><p class="mensaje" role="status"><?= htmlspecialchars(trim((string) ($mensaje))) ?></p><?php endif; ?>


<p>El administrador tendrá ID 0, igual que en el proyecto de CC5.</p>
<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <label for="nombre">Nombre</label><input id="nombre" name="nombre" type="text" maxlength="50" value="<?= htmlspecialchars(trim((string) ((is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '')))) ?>" required>
    <label for="usuario">Usuario</label><input id="usuario" name="usuario" type="text" maxlength="50" value="<?= htmlspecialchars(trim((string) ((is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '')))) ?>" required>
    <label for="contrasena">Contraseña</label><input id="contrasena" name="contrasena" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
    <button>Crear administrador</button>
</form>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
