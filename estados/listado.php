<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
$result = pg_query($conn, 'SELECT id_estado, orden, nombre FROM Estado ORDER BY id_estado');
$titulo = 'Estados';
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


<?php if ($_SESSION['admin']): ?><a class="button" href="agregar.php">Agregar estado</a><?php endif; ?>
<div class="tabla"><table>
<tr><th scope="col">ID del estado (1 a 5)</th><th scope="col">Orden (igual al ID)</th><th scope="col">Nombre</th><?php if ($_SESSION['admin']): ?><th>Acciones</th><?php endif; ?></tr>
<?php while ($fila = pg_fetch_assoc($result)): ?>
<tr>
<td><?= htmlspecialchars(trim((string) ($fila['id_estado']))) ?></td>
<td><?= htmlspecialchars(trim((string) ($fila['orden']))) ?></td>
<td><?= htmlspecialchars(trim((string) ($fila['nombre']))) ?></td>
<?php if ($_SESSION['admin']): ?><td>
<a href="editar.php?id=<?= htmlspecialchars(trim((string) ($fila['id_estado']))) ?>">Editar</a>
</td><?php endif; ?>
</tr>
<?php endwhile; ?>
</table></div>
<?php if (pg_num_rows($result) === 0): ?><p>No hay registros.</p><?php endif; ?>
<p class="ayuda">Estos cinco estados corresponden al enunciado. Se pueden editar sus nombres; se conserva el orden del recorrido.</p>
<div class="enlaces"><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
