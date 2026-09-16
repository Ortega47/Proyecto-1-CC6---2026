<?php
$raiz = '../';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$guia = trim($_GET['guia'] ?? '');
// TODO(bd): pg_query_params($conn, 'SELECT * FROM envio WHERE no_guia = $1', [$guia])
$envio = $guia !== '' ? buscar('envio', 'no_guia', $guia) : null;

if ($envio === null) {
    http_response_code(404);
}

$titulo = $envio !== null ? 'Guía ' . $envio['no_guia'] : 'Guía no encontrada';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido">
<?php if ($envio === null): ?>
    <h1>Guía no encontrada</h1>
    <p class="error">No existe ningún envío con la guía <?= h($guia) ?>.</p>
    <div class="enlaces">
        <a href="listado.php">Volver al listado de envíos</a>
        <a href="../menu.php">Menú principal</a>
    </div>
<?php else: ?>
    <?php
    $tienda    = buscar('tienda', 'id_tienda', $envio['id_tienda']);
    $destino   = buscar('destino', 'id_destino', $envio['id_destino']);
    $historial = seguimiento_de($envio['no_guia']);
    $actual    = (int) $envio['id_estado'];
    ?>
    <h1>Guía <?= h($envio['no_guia']) ?></h1>
    <p>Estado actual: <?= insignia_estado($actual) ?></p>

    <div class="cuadro">
        <dl>
            <dt>Orden</dt>
            <dd><?= h($envio['no_orden']) ?></dd>
            <dt>Tienda</dt>
            <dd><?= h($tienda['nombre'] ?? $envio['id_tienda']) ?> (<?= h($envio['id_tienda']) ?>)</dd>
            <dt>Destino</dt>
            <dd><?= h($envio['id_destino']) ?> - <?= h($destino['ciudad'] ?? '') ?></dd>
            <dt>Destinatario</dt>
            <dd><?= h($envio['destinatario']) ?></dd>
            <dt>Dirección</dt>
            <dd><?= h($envio['direccion']) ?></dd>
            <dt>Recibido</dt>
            <dd><?= h(fecha($envio['fecha'])) ?> <?= h(hora($envio['hora'])) ?></dd>
            <dt>Costo total</dt>
            <dd><?= h(moneda($envio['costo_total'])) ?></dd>
        </dl>
    </div>

    <?php if ($actual < 5): ?>
        <div class="botones-listado">
            <a class="boton" href="../estados/cambiar.php?guia=<?= h(rawurlencode($envio['no_guia'])) ?>">Cambiar estado</a>
        </div>
    <?php endif; ?>

    <h2>Historial</h2>
    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Etapa</th>
                <th scope="col">Usuario</th>
                <th scope="col">Observación</th>
            </tr>
            <?php foreach (array_reverse($historial) as $registro): ?>
                <?php $usuario = $registro['id_usuario'] === null ? null : buscar('usuario', 'id_usuario', $registro['id_usuario']); ?>
                <tr>
                    <td class="nowrap"><?= h(fecha($registro['fecha'])) ?></td>
                    <td><?= h(hora($registro['hora'])) ?></td>
                    <td><?= h(nombre_estado($registro['id_estado'])) ?></td>
                    <td><?= $usuario === null ? 'Registrado por la tienda' : h($usuario['nombre']) ?></td>
                    <td><?= h($registro['observacion'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="enlaces">
        <a href="listado.php">Listado de envíos</a>
        <a href="../estados/<?= h(archivo_estado($actual)) ?>">Envíos en <?= h(nombre_estado($actual)) ?></a>
        <a href="../menu.php">Menú principal</a>
    </div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
