<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$mensaje = '';
$clase   = '';
$valores = ['id_tienda' => '', 'nombre' => '', 'host' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [
        'id_tienda' => mb_strtoupper(trim($_POST['id_tienda'] ?? '')),
        'nombre'    => trim($_POST['nombre'] ?? ''),
        'host'      => trim($_POST['host'] ?? ''),
    ];

    if (mb_strlen($valores['id_tienda']) !== 15) {
        $mensaje = 'El código debe tener exactamente 15 caracteres.';
    } elseif (buscar('tienda', 'id_tienda', $valores['id_tienda']) !== null) {
        $mensaje = 'Ya existe una tienda con el código ' . $valores['id_tienda'] . '.';
    } elseif ($valores['nombre'] === '' || $valores['host'] === '') {
        $mensaje = 'Escriba el nombre y el host de la tienda.';
    } else {
        // TODO(bd): pg_query_params($conn, 'INSERT INTO tienda (id_tienda, nombre, host) VALUES ($1, $2, $3)',
        //   [$valores['id_tienda'], $valores['nombre'], $valores['host']]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
    if ($clase === '') {
        $clase = 'error';
    }
}

$titulo = 'Agregar tienda';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Agregar tienda</h1>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="agregar.php">
        <label for="id_tienda">Código (15 caracteres)</label>
        <input type="text" id="id_tienda" name="id_tienda" required minlength="15" maxlength="15" autocapitalize="characters" spellcheck="false" value="<?= h($valores['id_tienda']) ?>">
        <p class="ayuda">Es el identificador que la tienda manda en el parámetro tienda del WebService.</p>

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required maxlength="80" value="<?= h($valores['nombre']) ?>">

        <label for="host">Host</label>
        <input type="text" id="host" name="host" required maxlength="120" autocapitalize="none" spellcheck="false" value="<?= h($valores['host']) ?>">

        <button type="submit">Guardar tienda</button>
    </form>

    <div class="enlaces">
        <a href="listado.php">Listado de tiendas</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
