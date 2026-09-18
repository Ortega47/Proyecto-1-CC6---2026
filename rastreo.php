<?php
session_start();
date_default_timezone_set('America/Guatemala');

require __DIR__.'/postsql.php';
$guia=is_string($_GET['guia'] ?? null)?trim($_GET['guia']):'';
$envio=false;
$historial=false;
$mensaje='';
if ($guia!=='') {
    $r=pg_query_params($conn,'SELECT e.No_guia,e.Fecha,e.Fecha_entrega,s.Nombre AS estado,o.Ciudad AS origen,d.Ciudad AS destino FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado JOIN Cabeceras c ON e.ID_cabecera=c.ID_cabecera JOIN Origen o ON c.ID_origen=o.ID_origen JOIN Destino d ON c.ID_destino=d.ID_destino WHERE e.No_guia=$1',[$guia]);
    $envio=pg_fetch_assoc($r);
    if (!$envio) {
        $mensaje='No se encontró un envío con esa guía.';
    }
    else $historial=pg_query_params($conn,'SELECT s.Fecha,s.Hora,e.Nombre AS estado FROM Seguimiento s JOIN Estado e ON s.ID_estado=e.ID_estado WHERE s.No_guia=$1 ORDER BY s.Fecha,s.Hora,s.ID_seguimiento',[$guia]);
}
$titulo='Rastrear un paquete';
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
        <?php if (empty($_SESSION['cliente'])): ?><a href="index.php">Menú</a><?php endif; ?>
        <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>logout.php">Cerrar sesión</a>
    <?php else: ?><a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>login.php">Iniciar sesión</a><?php endif; ?>
    </nav>
</header>
<main class="<?= !empty($formulario) ? 'formulario' : 'contenido' ?>">
<h1><?= htmlspecialchars(trim((string) ($titulo))) ?></h1>
<?php if ($mensaje !== ''): ?><p class="mensaje" role="status"><?= htmlspecialchars(trim((string) ($mensaje))) ?></p><?php endif; ?>


<form method="get"><label for="guia">Número de guía</label><input id="guia" name="guia" type="text" maxlength="20" value="<?= htmlspecialchars(trim((string) ($guia))) ?>" required><button>Consultar</button></form>
<?php if ($envio): ?>
<h2><?= htmlspecialchars(trim((string) ($envio['estado']))) ?></h2>
<p>Guía: <?= htmlspecialchars(trim((string) ($envio['no_guia']))) ?><br><?= htmlspecialchars(trim((string) ($envio['origen']))) ?> → <?= htmlspecialchars(trim((string) ($envio['destino']))) ?><br>Fecha de entrega: <?= htmlspecialchars(trim((string) ($envio['fecha_entrega'] ?? 'Pendiente'))) ?></p>
<div class="tabla"><table><tr><th>Fecha</th><th>Hora</th><th>Estado</th></tr>
<?php while ($fila=pg_fetch_assoc($historial)): ?><tr><td><?= htmlspecialchars(trim((string) ($fila['fecha']))) ?></td><td><?= htmlspecialchars(trim((string) (substr($fila['hora'],0,5)))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['estado']))) ?></td></tr><?php endwhile; ?>
</table></div><?php endif; ?>
<?php if (!isset($_SESSION['id'])): ?><p><a href="login.php">Iniciar sesión</a> · <a href="register.php">Crear cuenta</a></p><?php endif; ?>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
