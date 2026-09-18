<?php
session_start();
date_default_timezone_set('America/Guatemala');
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(24));
}

require __DIR__.'/postsql.php';
if (isset($_SESSION['id'])) { header('Location: index.php'); exit; }
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $nombre = (is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '');
    $usuario = (is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '');
    $clave = is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '';
    $confirmar = is_string($_POST['confirmar'] ?? null) ? $_POST['confirmar'] : '';
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario.';
    }
    elseif (
        ($nombre === '' ||
        mb_strlen($nombre) > 50) ||
        ($usuario === '' ||
        mb_strlen($usuario) > 50) ||
        strlen($clave)<8 ||
        strlen($clave)>72 || str_contains($clave, "\0")
    ) {
        $mensaje='Completa nombre, usuario y una contraseña de 8 a 72 bytes.';
    }
    elseif ($clave !== $confirmar) {
        $mensaje='Las contraseñas no coinciden.';
    }
    else {
        pg_query($conn,'BEGIN');
        pg_query($conn,'LOCK TABLE Usuario IN EXCLUSIVE MODE');
        $repetido=pg_query_params($conn,'SELECT ID_usuario FROM Usuario WHERE TRIM(Usuario)=$1',[$usuario]);
        if (pg_num_rows($repetido)>0) {
            pg_query($conn,'ROLLBACK');
            $mensaje='Ese nombre de usuario ya existe.';
        } else {
            // El registro público siempre crea clientes; el ID se asigna aquí.
            $result=@pg_query_params($conn,'INSERT INTO Usuario (ID_usuario,Nombre,Usuario,Contraseña) SELECT GREATEST(COALESCE(MAX(ID_usuario),0),0)::bigint+1,$1,$2,$3 FROM Usuario',[$nombre,$usuario,password_hash($clave,PASSWORD_DEFAULT)]);
            if ($result && pg_query($conn,'COMMIT')) {
                $_SESSION['mensaje']='Cuenta creada. Ya puedes iniciar sesión.';
                header('Location: login.php'); exit;
            }
            pg_query($conn,'ROLLBACK');
            $mensaje='No se pudo crear la cuenta. Inténtalo de nuevo más tarde.';
        }
    }
}
$titulo='Crear cuenta';
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


<p>Regístrate para acceder al rastreo de tus paquetes con su número de guía.</p>
<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <label for="nombre">Nombre</label><input id="nombre" name="nombre" type="text" maxlength="50" value="<?= htmlspecialchars(trim((string) ((is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '')))) ?>" required>
    <label for="usuario">Usuario</label><input id="usuario" name="usuario" type="text" maxlength="50" value="<?= htmlspecialchars(trim((string) ((is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '')))) ?>" required>
    <label for="contrasena">Contraseña</label><input id="contrasena" name="contrasena" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
    <label for="confirmar">Confirmar contraseña</label><input id="confirmar" name="confirmar" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
    <button>Crear cuenta</button>
</form>
<p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
