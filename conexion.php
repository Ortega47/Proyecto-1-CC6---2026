<?php
require __DIR__.'/auth.php';
if (!$_SESSION['admin']) { header('Location: index.php'); exit; }

$probar_conexion = true;
require __DIR__.'/postsql.php';
$mensaje = '';
$titulo = 'Conexión a PostgreSQL';
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


<p>Escribe el servidor, puerto, base, usuario y contraseña en <b>postsql.php</b>.</p>
<?php if (!function_exists('pg_connect')): ?>
<p class="mensaje">Activa extension=pgsql en php.ini y reinicia PHP o Apache.</p>
<?php elseif (!$conn): ?>
<p class="mensaje">No se pudo conectar. Comprueba los datos de postsql.php y que PostgreSQL esté iniciado.</p>
<?php else: ?>
<p class="exito">Conexión realizada correctamente.</p>
<?php if (isset($_SESSION['id']) || in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'],true)): ?>
<p>Servidor: <?= htmlspecialchars(trim((string) ($host))) ?> · Puerto: <?= htmlspecialchars(trim((string) ($port))) ?><br>Base de datos: <?= htmlspecialchars(trim((string) ($dbname))) ?></p>
<?php endif; ?>
<?php endif; ?>
<form method="get"><button>Probar conexión de nuevo</button></form>
<p><a href="login.php">Iniciar sesión</a></p>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
