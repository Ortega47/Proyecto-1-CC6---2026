<?php
$raiz = '';
require __DIR__ . '/solo_admin.php';
require __DIR__ . '/funciones.php';
require __DIR__ . '/datos_demo.php';

// TODO(bd): SELECT id_estado, COUNT(*) FROM envio GROUP BY id_estado ORDER BY id_estado
$por_estado = conteo_por_estado();

// TODO(bd): SELECT d.id_destino, d.ciudad, COUNT(e.no_guia) AS envios, COALESCE(SUM(e.costo_total), 0) AS total
//           FROM destino d LEFT JOIN envio e USING (id_destino) GROUP BY d.id_destino, d.ciudad ORDER BY d.id_destino
$por_destino = [];
foreach ($demo['destino'] as $destino) {
    $por_destino[$destino['id_destino']] = ['ciudad' => $destino['ciudad'], 'envios' => 0, 'total' => 0.0];
}
// TODO(bd): SELECT t.id_tienda, t.nombre, COUNT(e.no_guia) AS envios, COALESCE(SUM(e.costo_total), 0) AS total
//           FROM tienda t LEFT JOIN envio e USING (id_tienda) GROUP BY t.id_tienda, t.nombre ORDER BY t.nombre
$por_tienda = [];
foreach ($demo['tienda'] as $tienda) {
    $por_tienda[$tienda['id_tienda']] = ['nombre' => $tienda['nombre'], 'envios' => 0, 'total' => 0.0];
}
foreach ($demo['envio'] as $envio) {
    $por_destino[$envio['id_destino']]['envios']++;
    $por_destino[$envio['id_destino']]['total'] += $envio['costo_total'];
    $por_tienda[$envio['id_tienda']]['envios']++;
    $por_tienda[$envio['id_tienda']]['total'] += $envio['costo_total'];
}
$total_envios = array_sum($por_estado);

$titulo = 'Resumen';
require __DIR__ . '/encabezado.php';
?>
<div class="contenido">
    <h1>Resumen</h1>
    <p><?= $total_envios ?> envíos en total. Cifras de solo lectura para el administrador.</p>

    <h2>Envíos por estado</h2>
    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Estado</th>
                <th scope="col" class="num">Envíos</th>
            </tr>
            <?php foreach ($por_estado as $id_estado => $cantidad): ?>
                <tr>
                    <td><?= insignia_estado($id_estado) ?></td>
                    <td class="num"><a href="estados/<?= h(archivo_estado($id_estado)) ?>"><?= $cantidad ?></a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <h2>Envíos por destino</h2>
    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Ciudad</th>
                <th scope="col" class="num">Envíos</th>
                <th scope="col" class="num">Total cobrado</th>
            </tr>
            <?php foreach ($por_destino as $id_destino => $fila): ?>
                <tr>
                    <td class="codigo"><?= h($id_destino) ?></td>
                    <td><?= h($fila['ciudad']) ?></td>
                    <td class="num"><?= $fila['envios'] ?></td>
                    <td class="num"><?= h(moneda($fila['total'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <h2>Envíos por tienda</h2>
    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Tienda</th>
                <th scope="col" class="num">Envíos</th>
                <th scope="col" class="num">Total cobrado</th>
            </tr>
            <?php foreach ($por_tienda as $id_tienda => $fila): ?>
                <tr>
                    <td class="codigo"><?= h($id_tienda) ?></td>
                    <td><?= h($fila['nombre']) ?></td>
                    <td class="num"><?= $fila['envios'] ?></td>
                    <td class="num"><?= h(moneda($fila['total'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="enlaces">
        <a href="menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/pie.php'; ?>
