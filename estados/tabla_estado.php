<?php
// Tabla común de las cinco pantallas de estado. La página que la incluye
// define $id_estado (1 a 5) antes del require.
$raiz = '../';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id_estado = (int) $id_estado;
$siguiente = $id_estado < 5 ? $id_estado + 1 : null;
$aviso     = aviso();

// TODO(bd): pg_query_params($conn, 'SELECT * FROM envio WHERE id_estado = $1 ORDER BY fecha, hora', [$id_estado])
$envios = [];
foreach ($demo['envio'] as $envio) {
    if ((int) $envio['id_estado'] === $id_estado) {
        $envios[] = $envio;
    }
}
usort($envios, function ($a, $b) {
    return strcmp($a['fecha'] . $a['hora'], $b['fecha'] . $b['hora']);
});

$titulo = nombre_estado($id_estado);
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido">
    <h1><?= h(nombre_estado($id_estado)) ?></h1>
    <p><?= count($envios) ?> <?= count($envios) === 1 ? 'envío' : 'envíos' ?> en esta etapa.
       <?php if ($siguiente !== null): ?>Desde aquí se pasa a <?= h(nombre_estado($siguiente)) ?>.<?php else: ?>Es la última etapa: solo consulta.<?php endif; ?></p>

    <?php if ($aviso !== ''): ?>
        <p class="aviso"><?= h($aviso) ?></p>
    <?php endif; ?>

    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Guía</th>
                <?php if ($siguiente !== null): ?>
                    <th scope="col">Acción</th>
                <?php endif; ?>
                <th scope="col">Orden</th>
                <th scope="col">Tienda</th>
                <th scope="col">Destino</th>
                <th scope="col">Destinatario</th>
                <th scope="col"><?= $id_estado === 5 ? 'Entregado' : 'Desde' ?></th>
            </tr>
            <?php if (count($envios) === 0): ?>
                <tr>
                    <td colspan="<?= $siguiente !== null ? 7 : 6 ?>" class="vacio">No hay envíos en esta etapa</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($envios as $envio): ?>
                <?php
                $tienda  = buscar('tienda', 'id_tienda', $envio['id_tienda']);
                $destino = buscar('destino', 'id_destino', $envio['id_destino']);
                $desde   = null;
                foreach (seguimiento_de($envio['no_guia']) as $registro) {
                    if ((int) $registro['id_estado'] === $id_estado) {
                        $desde = $registro;
                    }
                }
                ?>
                <tr>
                    <td class="codigo"><a href="../envios/detalle.php?guia=<?= h(rawurlencode($envio['no_guia'])) ?>"><?= h($envio['no_guia']) ?></a></td>
                    <?php if ($siguiente !== null): ?>
                        <td class="nowrap"><a href="cambiar.php?guia=<?= h(rawurlencode($envio['no_guia'])) ?>">Pasar a <?= h(nombre_estado($siguiente)) ?></a></td>
                    <?php endif; ?>
                    <td class="nowrap"><?= h($envio['no_orden']) ?></td>
                    <td><?= h($tienda['nombre'] ?? $envio['id_tienda']) ?></td>
                    <td><?= h($envio['id_destino']) ?> - <?= h($destino['ciudad'] ?? '') ?></td>
                    <td><?= h($envio['destinatario']) ?></td>
                    <td class="nowrap"><?= $desde !== null ? h(fecha($desde['fecha']) . ' ' . hora($desde['hora'])) : '' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="enlaces">
        <a href="../envios/listado.php">Todos los envíos</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
