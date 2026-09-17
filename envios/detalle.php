<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';

$guia=is_string($_GET['guia'] ?? null)?trim($_GET['guia']):'';
$result=pg_query_params($conn,'SELECT e.*,s.Nombre AS estado,t.Nombre AS tienda,t.No_orden,o.Ciudad AS origen,d.Ciudad AS destino FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado JOIN Tienda t ON e.ID_tienda=t.ID_tienda JOIN Cabeceras c ON e.ID_cabecera=c.ID_cabecera JOIN Origen o ON c.ID_origen=o.ID_origen JOIN Destino d ON c.ID_destino=d.ID_destino WHERE e.No_guia=$1',[$guia]);
$envio=pg_fetch_assoc($result);
if (!$envio) { http_response_code(404); exit('Envío no encontrado.'); }
$historial=pg_query_params($conn,'SELECT s.*,e.Nombre AS estado,u.Nombre AS usuario FROM Seguimiento s JOIN Estado e ON s.ID_estado=e.ID_estado JOIN Usuario u ON s.ID_usuario=u.ID_usuario WHERE s.No_guia=$1 ORDER BY s.Fecha,s.Hora,s.ID_seguimiento',[$guia]);
$titulo='Detalle del envío';
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


<dl>
<dt>Guía</dt><dd><?= htmlspecialchars(trim((string) ($envio['no_guia']))) ?></dd>
<dt>Destinatario</dt><dd><?= htmlspecialchars(trim((string) ($envio['destinatario']))) ?></dd>
<dt>Tienda / orden</dt><dd><?= htmlspecialchars(trim((string) ($envio['tienda']))) ?> / <?= htmlspecialchars(trim((string) ($envio['no_orden']))) ?></dd>
<dt>Ruta</dt><dd><?= htmlspecialchars(trim((string) ($envio['origen']))) ?> → <?= htmlspecialchars(trim((string) ($envio['destino']))) ?></dd>
<dt>Recibido</dt><dd><?= htmlspecialchars(trim((string) ($envio['fecha']))) ?> <?= htmlspecialchars(trim((string) (substr($envio['hora'],0,5)))) ?></dd>
<dt>Estado</dt><dd><?= htmlspecialchars(trim((string) ($envio['estado']))) ?></dd>
<dt>Fecha de entrega</dt><dd><?= htmlspecialchars(trim((string) ($envio['fecha_entrega'] ?? 'Pendiente'))) ?></dd>
<dt>Total</dt><dd>Q<?= htmlspecialchars(trim((string) (number_format((float)$envio['costo_total'],2)))) ?></dd>
</dl>
<?php if ((int)$envio['id_estado']<5): ?><a class="button" href="../seguimiento/agregar.php?guia=<?= htmlspecialchars(trim((string) (rawurlencode($guia)))) ?>">Cambiar estado</a><?php endif; ?>
<h2>Historial</h2>
<div class="tabla"><table><tr><th>Fecha</th><th>Hora</th><th>Estado</th><th>Usuario</th><th>Observación</th></tr>
<?php while ($s=pg_fetch_assoc($historial)): ?><tr><td><?= htmlspecialchars(trim((string) ($s['fecha']))) ?></td><td><?= htmlspecialchars(trim((string) (substr($s['hora'],0,5)))) ?></td><td><?= htmlspecialchars(trim((string) ($s['estado']))) ?></td><td><?= htmlspecialchars(trim((string) ($s['usuario']))) ?></td><td><?= htmlspecialchars(trim((string) ($s['observacion']))) ?></td></tr><?php endwhile; ?></table></div>
<div class="enlaces"><a href="listado.php">Listado de envíos</a><a href="../seguimiento/listado.php?guia=<?= htmlspecialchars(trim((string) (rawurlencode($guia)))) ?>">Administrar seguimiento</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
