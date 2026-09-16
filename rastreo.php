<?php
$raiz = '';
require __DIR__ . '/funciones.php';
require __DIR__ . '/datos_demo.php';

$guia  = trim($_GET['guia'] ?? '');
$error = '';
$envio = null;

if ($guia === '') {
    $error = 'Escriba el número de guía para rastrear su paquete.';
} elseif (mb_strlen($guia) !== 15) {
    $error = 'El número de guía tiene 15 caracteres y el que escribió tiene ' . mb_strlen($guia) . '. Revíselo e intente de nuevo.';
} else {
    $guia = mb_strtoupper($guia);
    // TODO(bd): pg_query_params($conn, 'SELECT * FROM envio WHERE no_guia = $1', [$guia])
    $envio = buscar('envio', 'no_guia', $guia);
    if ($envio === null) {
        http_response_code(404);
        $error = 'No encontramos la guía ' . $guia . '. Revise que los 15 caracteres sean iguales a los de su confirmación de compra o pida el número a la tienda donde compró.';
    }
}

$titulo = $envio !== null ? 'Guía ' . $envio['no_guia'] : 'Rastree su paquete';
require __DIR__ . '/encabezado.php';
?>
<div class="contenido angosto">
<?php if ($envio === null): ?>
    <h1>Rastree su paquete</h1>
    <p class="error"><?= h($error) ?></p>
    <form method="get" action="rastreo.php">
        <label for="guia">Número de guía</label>
        <input type="text" id="guia" name="guia" required maxlength="15" autocomplete="off" autocapitalize="characters" spellcheck="false" value="<?= h($guia) ?>">
        <button type="submit">Rastrear paquete</button>
    </form>
<?php else: ?>
    <?php
    $destino   = buscar('destino', 'id_destino', $envio['id_destino']);
    $tienda    = buscar('tienda', 'id_tienda', $envio['id_tienda']);
    $historial = seguimiento_de($envio['no_guia']);
    $actual    = (int) $envio['id_estado'];
    ?>
    <h1>Su paquete</h1>
    <div class="cuadro">
        <p class="guia"><?= h($envio['no_guia']) ?></p>
        <dl>
            <dt>Estado</dt>
            <dd><?= insignia_estado($actual) ?></dd>
            <dt>Para</dt>
            <dd><?= h(nombre_abreviado($envio['destinatario'])) ?></dd>
            <dt>Destino</dt>
            <dd><?= h($destino['ciudad'] ?? $envio['id_destino']) ?></dd>
            <dt>Tienda</dt>
            <dd><?= h($tienda['nombre'] ?? $envio['id_tienda']) ?></dd>
            <dt>Fecha</dt>
            <dd><?= h(fecha($envio['fecha'])) ?></dd>
        </dl>
    </div>

    <h2>Etapas del envío</h2>
    <ol class="ruta">
        <?php foreach ($demo['estado'] as $estado): ?>
            <?php
            $id = (int) $estado['id_estado'];
            if ($id < $actual) {
                $clase = 'hecho';
                $marca = '✓';
            } elseif ($id === $actual) {
                $clase = 'actual';
                $marca = $id;
            } else {
                $clase = 'futuro';
                $marca = $id;
            }
            ?>
            <li class="<?= $clase ?>">
                <span class="marca-paso" aria-hidden="true"><?= $marca ?></span>
                <span><?= h(nombre_estado($id)) ?><?= $id === $actual ? ' (etapa actual)' : '' ?></span>
            </li>
        <?php endforeach; ?>
    </ol>

    <h2>Historial</h2>
    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Fecha y hora</th>
                <th scope="col">Etapa</th>
                <th scope="col">Observación</th>
            </tr>
            <?php foreach (array_reverse($historial) as $registro): ?>
                <tr>
                    <td class="nowrap"><?= h(fecha($registro['fecha'])) ?> <?= h(hora($registro['hora'])) ?></td>
                    <td><?= h(nombre_estado($registro['id_estado'])) ?></td>
                    <td><?= h($registro['observacion'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <h2>Rastrear otra guía</h2>
    <form method="get" action="rastreo.php">
        <label for="guia">Número de guía</label>
        <input type="text" id="guia" name="guia" required maxlength="15" autocomplete="off" autocapitalize="characters" spellcheck="false">
        <button type="submit">Rastrear paquete</button>
    </form>
<?php endif; ?>
</div>
<?php require __DIR__ . '/pie.php'; ?>
