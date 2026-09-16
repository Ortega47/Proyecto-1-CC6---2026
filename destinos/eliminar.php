<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id = trim($_REQUEST['id'] ?? '');
// TODO(bd): pg_query_params($conn, 'SELECT * FROM destino WHERE id_destino = $1', [$id])
$destino = $id !== '' ? buscar('destino', 'id_destino', $id) : null;
$envios  = $destino !== null ? contar_envios('id_destino', $id) : 0;

$mensaje = '';
$clase   = '';

if ($destino !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['decision'] ?? '') !== 'si') {
        header('Location: listado.php');
        exit;
    }
    if ($envios > 0) {
        $mensaje = 'No se puede eliminar: el destino tiene ' . $envios . ' envíos. Para retirarlo, edítelo y apague la cobertura.';
        $clase   = 'error';
    } else {
        // TODO(bd): pg_query_params($conn, 'DELETE FROM destino WHERE id_destino = $1', [$id]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
}

$titulo = 'Eliminar destino';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Eliminar destino</h1>
<?php if ($destino === null): ?>
    <p class="error">No existe ningún destino con el código <?= h($id) ?>.</p>
<?php else: ?>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <div class="cuadro">
        <dl>
            <dt>Código</dt>
            <dd class="codigo"><?= h($destino['id_destino']) ?></dd>
            <dt>Ciudad</dt>
            <dd><?= h($destino['ciudad']) ?></dd>
            <dt>Cobertura</dt>
            <dd><?= $destino['cobertura'] ? 'Sí' : 'No' ?></dd>
            <dt>Envíos</dt>
            <dd><?= $envios ?></dd>
        </dl>
    </div>

    <?php if ($envios > 0): ?>
        <p class="error">Este destino tiene <?= $envios ?> envíos y no se puede eliminar. Para retirarlo, <a href="editar.php?id=<?= h(rawurlencode($destino['id_destino'])) ?>">edítelo</a> y apague la cobertura.</p>
    <?php elseif ($clase !== 'aviso'): ?>
        <form method="post" action="eliminar.php?id=<?= h(rawurlencode($destino['id_destino'])) ?>">
            <h3>¿Eliminar el destino <?= h($destino['ciudad']) ?>?</h3>
            <div class="acciones">
                <button type="submit" name="decision" value="si">Sí, eliminar</button>
                <button type="submit" name="decision" value="no" class="secundario">No</button>
            </div>
        </form>
    <?php endif; ?>
<?php endif; ?>
    <div class="enlaces">
        <a href="listado.php">Listado de destinos</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
