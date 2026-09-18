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
    $usuario = (is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '');
    $clave = is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '';
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario e inténtalo de nuevo.';
    }
    else {
        $result = pg_query_params($conn, 'SELECT * FROM Usuario WHERE TRIM(Usuario)=$1', [$usuario]);
        $fila = pg_fetch_assoc($result);
        $guardada = $fila ? rtrim($fila['contraseña']) : '';
        $valida = $fila && password_verify($clave, $guardada);
        // Acepta las claves antiguas de las tablas originales y las cambia a hash.
        if (!$valida && $fila && $guardada !== '' && password_get_info($guardada)['algoName'] === 'unknown' && hash_equals($guardada, $clave)) {
            $cambio = @pg_query_params($conn, 'UPDATE Usuario SET Contraseña=$1 WHERE ID_usuario=$2', [password_hash($clave,PASSWORD_DEFAULT),$fila['id_usuario']]);
            $valida = (bool)$cambio;
            if (!$cambio) {
                $mensaje = 'Amplía la columna Contraseña a varchar(255) en pgAdmin; consulta README.md.';
            }
        }
        if ($valida) {
            session_regenerate_id(true);
            $_SESSION['id'] = (int)$fila['id_usuario'];
            $_SESSION['nombre'] = trim($fila['nombre']);
            $_SESSION['cliente'] = $_SESSION['id'] !== 0;
            $_SESSION['admin'] = $_SESSION['id'] === 0 && !$_SESSION['cliente'];
            header('Location: '.($_SESSION['cliente'] ? 'rastreo.php' : 'index.php')); exit;
        }
        if ($mensaje === '') {
            $mensaje = 'Usuario o contraseña incorrectos.';
        }
    }
}
$titulo = 'Iniciar sesión';
$formulario = true;

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


<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <label for="usuario">Usuario</label>
    <input id="usuario" name="usuario" type="text" required maxlength="50" autocomplete="username" value="<?= htmlspecialchars(trim((string) ((is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '')))) ?>">
    <label for="contrasena">Contraseña</label>
    <input id="contrasena" name="contrasena" type="password" required autocomplete="current-password">
    <button>Iniciar sesión</button>
</form>
<p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
