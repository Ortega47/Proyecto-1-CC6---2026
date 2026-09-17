<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }

$result = pg_query($conn, 'SELECT ID_usuario, Nombre, Usuario FROM Usuario ORDER BY ID_usuario');
$titulo = 'Usuarios';
$mensaje = '';

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


<a class="button" href="agregar.php">Agregar usuario</a>
<div class="tabla"><table>
<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr>
<?php while ($fila=pg_fetch_assoc($result)): ?>
<tr><td><?= htmlspecialchars(trim((string) ($fila['id_usuario']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['nombre']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['usuario']))) ?></td>
<td><?= (int)$fila['id_usuario']===0 ? 'Administrador' : 'Operador' ?></td>
<td><a href="editar.php?id=<?= htmlspecialchars(trim((string) ($fila['id_usuario']))) ?>">Editar</a>
<?php if ((int)$fila['id_usuario']!==0): ?><a href="eliminar.php?id=<?= htmlspecialchars(trim((string) ($fila['id_usuario']))) ?>">Eliminar</a><?php endif; ?></td></tr>
<?php endwhile; ?></table></div>
<div class="enlaces"><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
