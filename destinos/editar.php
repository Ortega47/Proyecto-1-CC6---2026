<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id = trim($_REQUEST['id'] ?? '');
// TODO(bd): pg_query_params($conn, 'SELECT * FROM destino WHERE id_destino = $1', [$id])
$destino = $id !== '' ? buscar('destino', 'id_destino', $id) : null;

$mensaje = '';
$clase   = '';
$valores = $destino;

if ($destino !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [
        'id_destino'   => $destino['id_destino'],
        'ciudad'       => trim($_POST['ciudad'] ?? ''),
        'cobertura'    => isset($_POST['cobertura']),
        'costo_envio'  => trim($_POST['costo_envio'] ?? ''),
        'costo_manejo' => trim($_POST['costo_manejo'] ?? ''),
    ];

    if ($valores['ciudad'] === '') {
        $mensaje = 'Escriba el nombre de la ciudad.';
        $clase   = 'error';
    } elseif (!is_numeric($valores['costo_envio']) || $valores['costo_envio'] < 0 || !is_numeric($valores['costo_manejo']) || $valores['costo_manejo'] < 0) {
        $mensaje = 'Los costos deben ser números mayores o iguales a 0.';
        $clase   = 'error';
    } else {
        // TODO(bd): pg_query_params($conn,
        //   'UPDATE destino SET ciudad = $1, cobertura = $2, costo_envio = $3, costo_manejo = $4 WHERE id_destino = $5',
        //   [$valores['ciudad'], $valores['cobertura'] ? 't' : 'f', $valores['costo_envio'], $valores['costo_manejo'], $id]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
}

$titulo = 'Editar destino';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Editar destino</h1>
<?php if ($destino === null): ?>
    <p class="error">No existe ningún destino con el código <?= h($id) ?>.</p>
    <div class="enlaces">
        <a href="listado.php">Listado de destinos</a>
    </div>
<?php else: ?>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="editar.php?id=<?= h(rawurlencode($destino['id_destino'])) ?>">
        <label for="id_destino">Código</label>
        <input type="text" id="id_destino" name="id_destino" readonly value="<?= h($destino['id_destino']) ?>">
        <p class="ayuda">El código es la llave del destino y no se cambia.</p>

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" required maxlength="80" value="<?= h($valores['ciudad']) ?>">

        <label class="opcion" for="cobertura">
            <input type="checkbox" id="cobertura" name="cobertura" value="1" <?= $valores['cobertura'] ? 'checked' : '' ?>>
            Con cobertura
        </label>
        <p class="ayuda">Sin cobertura, la tienda recibe costo 0. Para retirar un destino, apague su cobertura.</p>

        <label for="costo_envio">Costo de envío (Q)</label>
        <input type="number" id="costo_envio" name="costo_envio" required step="0.01" min="0" value="<?= h($valores['costo_envio']) ?>">

        <label for="costo_manejo">Costo de manejo (Q)</label>
        <input type="number" id="costo_manejo" name="costo_manejo" required step="0.01" min="0" value="<?= h($valores['costo_manejo']) ?>">

        <button type="submit">Guardar cambios</button>
    </form>

    <div class="enlaces">
        <a href="listado.php">Listado de destinos</a>
        <a href="../menu.php">Menú principal</a>
    </div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
