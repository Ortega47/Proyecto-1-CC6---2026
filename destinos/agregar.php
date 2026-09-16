<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$mensaje = '';
$clase   = '';
$valores = ['id_destino' => '', 'ciudad' => '', 'cobertura' => true, 'costo_envio' => '', 'costo_manejo' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [
        'id_destino'   => mb_strtoupper(trim($_POST['id_destino'] ?? '')),
        'ciudad'       => trim($_POST['ciudad'] ?? ''),
        'cobertura'    => isset($_POST['cobertura']),
        'costo_envio'  => trim($_POST['costo_envio'] ?? ''),
        'costo_manejo' => trim($_POST['costo_manejo'] ?? ''),
    ];

    if (mb_strlen($valores['id_destino']) !== 5) {
        $mensaje = 'El código debe tener exactamente 5 caracteres.';
    } elseif (buscar('destino', 'id_destino', $valores['id_destino']) !== null) {
        $mensaje = 'Ya existe un destino con el código ' . $valores['id_destino'] . '.';
    } elseif ($valores['ciudad'] === '') {
        $mensaje = 'Escriba el nombre de la ciudad.';
    } elseif (!is_numeric($valores['costo_envio']) || $valores['costo_envio'] < 0 || !is_numeric($valores['costo_manejo']) || $valores['costo_manejo'] < 0) {
        $mensaje = 'Los costos deben ser números mayores o iguales a 0.';
    } else {
        // TODO(bd): pg_query_params($conn,
        //   'INSERT INTO destino (id_destino, ciudad, cobertura, costo_envio, costo_manejo) VALUES ($1, $2, $3, $4, $5)',
        //   [$valores['id_destino'], $valores['ciudad'], $valores['cobertura'] ? 't' : 'f', $valores['costo_envio'], $valores['costo_manejo']]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
    if ($clase === '') {
        $clase = 'error';
    }
}

$titulo = 'Agregar destino';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Agregar destino</h1>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="agregar.php">
        <label for="id_destino">Código (5 caracteres)</label>
        <input type="text" id="id_destino" name="id_destino" required minlength="5" maxlength="5" autocapitalize="characters" spellcheck="false" value="<?= h($valores['id_destino']) ?>">

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" required maxlength="80" value="<?= h($valores['ciudad']) ?>">

        <label class="opcion" for="cobertura">
            <input type="checkbox" id="cobertura" name="cobertura" value="1" <?= $valores['cobertura'] ? 'checked' : '' ?>>
            Con cobertura
        </label>
        <p class="ayuda">Sin cobertura, la tienda recibe costo 0.</p>

        <label for="costo_envio">Costo de envío (Q)</label>
        <input type="number" id="costo_envio" name="costo_envio" required step="0.01" min="0" value="<?= h($valores['costo_envio']) ?>">

        <label for="costo_manejo">Costo de manejo (Q)</label>
        <input type="number" id="costo_manejo" name="costo_manejo" required step="0.01" min="0" value="<?= h($valores['costo_manejo']) ?>">

        <button type="submit">Guardar destino</button>
    </form>

    <div class="enlaces">
        <a href="listado.php">Listado de destinos</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
