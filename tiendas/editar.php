<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id = trim($_REQUEST['id'] ?? '');
// TODO(bd): pg_query_params($conn, 'SELECT * FROM tienda WHERE id_tienda = $1', [$id])
$tienda = $id !== '' ? buscar('tienda', 'id_tienda', $id) : null;

$mensaje = '';
$clase   = '';
$valores = $tienda;

if ($tienda !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [
        'id_tienda' => $tienda['id_tienda'],
        'nombre'    => trim($_POST['nombre'] ?? ''),
        'host'      => trim($_POST['host'] ?? ''),
    ];

    if ($valores['nombre'] === '' || $valores['host'] === '') {
        $mensaje = 'Escriba el nombre y el host de la tienda.';
        $clase   = 'error';
    } else {
        // TODO(bd): pg_query_params($conn, 'UPDATE tienda SET nombre = $1, host = $2 WHERE id_tienda = $3',
        //   [$valores['nombre'], $valores['host'], $id]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
}

$titulo = 'Editar tienda';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Editar tienda</h1>
<?php if ($tienda === null): ?>
    <p class="error">No existe ninguna tienda con el código <?= h($id) ?>.</p>
    <div class="enlaces">
        <a href="listado.php">Listado de tiendas</a>
    </div>
<?php else: ?>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="editar.php?id=<?= h(rawurlencode($tienda['id_tienda'])) ?>">
        <label for="id_tienda">Código</label>
        <input type="text" id="id_tienda" name="id_tienda" readonly value="<?= h($tienda['id_tienda']) ?>">
        <p class="ayuda">El código es la llave de la tienda y no se cambia.</p>

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required maxlength="80" value="<?= h($valores['nombre']) ?>">

        <label for="host">Host</label>
        <input type="text" id="host" name="host" required maxlength="120" autocapitalize="none" spellcheck="false" value="<?= h($valores['host']) ?>">

        <button type="submit">Guardar cambios</button>
    </form>

    <div class="enlaces">
        <a href="listado.php">Listado de tiendas</a>
        <a href="../menu.php">Menú principal</a>
    </div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
