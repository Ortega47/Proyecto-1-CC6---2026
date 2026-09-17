<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';

$guia=is_string($_GET['guia'] ?? null)?trim($_GET['guia']):'';
$result=pg_query_params($conn,'SELECT s.*, e.Nombre AS estado, u.Nombre AS usuario FROM Seguimiento s JOIN Estado e ON s.ID_estado=e.ID_estado JOIN Usuario u ON s.ID_usuario=u.ID_usuario WHERE ($1::text=\'\' OR TRIM(s.No_guia)=$1) ORDER BY s.Fecha DESC,s.Hora DESC,s.ID_seguimiento DESC',[$guia]);
$titulo='Seguimiento';
$mensaje='';

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


<form method="get" class="filtros"><div><label for="guia">Número de guía</label><input type="text" name="guia" id="guia" maxlength="20" value="<?= htmlspecialchars(trim((string) ($guia))) ?>"></div><button>Consultar</button></form>
<p>Para registrar el siguiente estado, abre el envío y selecciona Cambiar estado.</p>
<div class="tabla"><table><tr><th>ID</th><th>Guía</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Usuario</th><th>Observación</th><?php if ($_SESSION['admin']): ?><th>Acciones</th><?php endif; ?></tr>
<?php while ($fila=pg_fetch_assoc($result)): ?><tr>
<td><?= htmlspecialchars(trim((string) ($fila['id_seguimiento']))) ?></td><td><a href="../envios/detalle.php?guia=<?= htmlspecialchars(trim((string) (rawurlencode(trim($fila['no_guia']))))) ?>"><?= htmlspecialchars(trim((string) ($fila['no_guia']))) ?></a></td><td><?= htmlspecialchars(trim((string) ($fila['fecha']))) ?></td><td><?= htmlspecialchars(trim((string) (substr($fila['hora'],0,5)))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['estado']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['usuario']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['observacion']))) ?></td>
<?php if ($_SESSION['admin']): ?><td><a href="editar.php?id=<?= htmlspecialchars(trim((string) ($fila['id_seguimiento']))) ?>">Editar observación</a><a href="eliminar.php?id=<?= htmlspecialchars(trim((string) ($fila['id_seguimiento']))) ?>">Eliminar</a></td><?php endif; ?></tr>
<?php endwhile; ?></table></div>
<?php if (pg_num_rows($result)===0): ?><p>No hay registros de seguimiento.</p><?php endif; ?>
<div class="enlaces"><a href="../envios/listado.php">Ver envíos</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
