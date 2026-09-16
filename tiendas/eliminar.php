<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id = trim($_REQUEST['id'] ?? '');
// TODO(bd): pg_query_params($conn, 'SELECT * FROM tienda WHERE id_tienda = $1', [$id])
$tienda = $id !== '' ? buscar('tienda', 'id_tienda', $id) : null;
$envios = $tienda !== null ? contar_envios('id_tienda', $id) : 0;

$mensaje = '';
$clase   = '';

if ($tienda !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['decision'] ?? '') !== 'si') {
        header('Location: listado.php');
        exit;
    }
    if ($envios > 0) {
        $mensaje = 'No se puede eliminar: la tienda tiene ' . $envios . ' envíos y se conserva por el historial.';
        $clase   = 'error';
    } else {
        // TODO(bd): pg_query_params($conn, 'DELETE FROM tienda WHERE id_tienda = $1', [$id]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
}

$titulo = 'Eliminar tienda';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Eliminar tienda</h1>
<?php if ($tienda === null): ?>
    <p class="error">No existe ninguna tienda con el código <?= h($id) ?>.</p>
<?php else: ?>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <div class="cuadro">
        <dl>
            <dt>Código</dt>
            <dd class="codigo"><?= h($tienda['id_tienda']) ?></dd>
            <dt>Nombre</dt>
            <dd><?= h($tienda['nombre']) ?></dd>
            <dt>Host</dt>
            <dd><?= h($tienda['host']) ?></dd>
            <dt>Envíos</dt>
            <dd><?= $envios ?></dd>
        </dl>
    </div>

    <?php if ($envios > 0): ?>
        <p class="error">Esta tienda tiene <?= $envios ?> envíos y no se puede eliminar: se conserva por el historial.</p>
    <?php elseif ($clase !== 'aviso'): ?>
        <form method="post" action="eliminar.php?id=<?= h(rawurlencode($tienda['id_tienda'])) ?>">
            <h3>¿Eliminar la tienda <?= h($tienda['nombre']) ?>?</h3>
            <div class="acciones">
                <button type="submit" name="decision" value="si">Sí, eliminar</button>
                <button type="submit" name="decision" value="no" class="secundario">No</button>
            </div>
        </form>
    <?php endif; ?>
<?php endif; ?>
    <div class="enlaces">
        <a href="listado.php">Listado de tiendas</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
