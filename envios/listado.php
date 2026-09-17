<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';

$estado = isset($estado_fijo) ? (string)$estado_fijo : ($_GET['estado'] ?? '');
if (!is_string($estado) || ($estado !== '' && (filter_var($estado, FILTER_VALIDATE_INT) === false || $estado < 1 || $estado > 2147483647))) $estado='';
$guia = is_string($_GET['guia'] ?? null) ? trim($_GET['guia']) : '';
$query = 'SELECT e.*, s.nombre AS estado, t.nombre AS tienda, d.ciudad AS destino
          FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado
          JOIN Tienda t ON e.ID_tienda=t.ID_tienda
          JOIN Cabeceras c ON e.ID_cabecera=c.ID_cabecera
          JOIN Destino d ON c.ID_destino=d.ID_destino
          WHERE ($1::int IS NULL OR e.ID_estado=$1) AND ($2::text=\'\' OR TRIM(e.No_guia) ILIKE $3)
          ORDER BY e.Fecha DESC,e.Hora DESC,e.No_guia';
$result = pg_query_params($conn,$query,[$estado===''?null:$estado,$guia,'%'.$guia.'%']);
$estados=pg_query($conn,'SELECT * FROM Estado ORDER BY Orden');
$titulo=$titulo_estado ?? 'Envíos';
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


<?php if ($_SESSION['admin']): ?><a class="button" href="../envios/agregar.php">Agregar envío</a><?php endif; ?>
<form method="get" class="filtros">
    <?php if (!isset($estado_fijo)): ?><div><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option>
    <?php while ($fila=pg_fetch_assoc($estados)): ?><option value="<?= htmlspecialchars(trim((string) ($fila['id_estado']))) ?>" <?= $estado===$fila['id_estado']?'selected':'' ?>><?= htmlspecialchars(trim((string) ($fila['nombre']))) ?></option><?php endwhile; ?>
    </select></div><?php endif; ?>
    <div><label for="guia">Guía</label><input type="text" id="guia" name="guia" maxlength="20" value="<?= htmlspecialchars(trim((string) ($guia))) ?>"></div>
    <button>Filtrar</button>
</form>
<div class="tabla"><table>
<tr><th>Guía</th><th>Fecha</th><th>Tienda</th><th>Destino</th><th>Destinatario</th><th>Estado</th><th>Total</th><th>Acciones</th></tr>
<?php while ($fila=pg_fetch_assoc($result)): $g=rawurlencode(trim($fila['no_guia'])); ?>
<tr><td><?= htmlspecialchars(trim((string) ($fila['no_guia']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['fecha']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['tienda']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['destino']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['destinatario']))) ?></td><td><?= htmlspecialchars(trim((string) ($fila['estado']))) ?></td><td>Q<?= htmlspecialchars(trim((string) (number_format((float)$fila['costo_total'],2)))) ?></td>
<td><a href="../envios/detalle.php?guia=<?= htmlspecialchars(trim((string) ($g))) ?>">Detalle</a>
<?php if ((int)$fila['id_estado']<5): ?><a href="../seguimiento/agregar.php?guia=<?= htmlspecialchars(trim((string) ($g))) ?>">Cambiar estado</a><?php endif; ?>
<?php if ($_SESSION['admin']): ?><a href="../envios/editar.php?guia=<?= htmlspecialchars(trim((string) ($g))) ?>">Editar</a><a href="../envios/eliminar.php?guia=<?= htmlspecialchars(trim((string) ($g))) ?>">Eliminar</a><?php endif; ?></td></tr>
<?php endwhile; ?></table></div>
<?php if (pg_num_rows($result)===0): ?><p>No hay envíos para estos filtros.</p><?php endif; ?>
<div class="enlaces"><a href="../envios/listado.php">Todos los envíos</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
