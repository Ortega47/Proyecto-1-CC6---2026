<?php
$raiz = '../';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$f_estado  = $_GET['estado'] ?? '';
$f_tienda  = $_GET['tienda'] ?? '';
$f_destino = $_GET['destino'] ?? '';
$f_q       = trim($_GET['q'] ?? '');
$hay_filtros = ($f_estado !== '' || $f_tienda !== '' || $f_destino !== '' || $f_q !== '');

// TODO(bd): armar WHERE con pg_query_params, por ejemplo:
//   $where = []; $params = []; $p = 1;
//   if ($f_estado !== '')  { $where[] = "e.id_estado = \$" . $p++;  $params[] = (int) $f_estado; }
//   if ($f_tienda !== '')  { $where[] = "e.id_tienda = \$" . $p++;  $params[] = $f_tienda; }
//   if ($f_destino !== '') { $where[] = "e.id_destino = \$" . $p++; $params[] = $f_destino; }
//   if ($f_q !== '')       { $where[] = "(e.no_guia ILIKE \$$p OR e.no_orden ILIKE \$$p)"; $params[] = "%$f_q%"; $p++; }
//   $sql = "SELECT e.*, t.nombre AS tienda, d.ciudad FROM envio e JOIN tienda t USING (id_tienda) JOIN destino d USING (id_destino)"
//        . ($where ? " WHERE " . implode(" AND ", $where) : "") . " ORDER BY e.fecha DESC, e.hora DESC";
//   $r = pg_query_params($conn, $sql, $params);
$envios = [];
foreach ($demo['envio'] as $envio) {
    if ($f_estado !== '' && (string) $envio['id_estado'] !== $f_estado) continue;
    if ($f_tienda !== '' && $envio['id_tienda'] !== $f_tienda) continue;
    if ($f_destino !== '' && $envio['id_destino'] !== $f_destino) continue;
    if ($f_q !== '' && stripos($envio['no_guia'], $f_q) === false && stripos($envio['no_orden'], $f_q) === false) continue;
    $envios[] = $envio;
}
usort($envios, function ($a, $b) {
    return strcmp($b['fecha'] . $b['hora'], $a['fecha'] . $a['hora']);
});

$titulo = 'Envíos';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido">
    <h1>Envíos contratados</h1>
    <p>Todos los envíos que las tiendas han pedido, en cualquier etapa.</p>

    <form method="get" action="listado.php" class="filtros">
        <div>
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos</option>
                <?php foreach ($demo['estado'] as $estado): ?>
                    <option value="<?= (int) $estado['id_estado'] ?>" <?= $f_estado === (string) $estado['id_estado'] ? 'selected' : '' ?>><?= h(nombre_estado($estado['id_estado'])) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="tienda">Tienda</label>
            <select id="tienda" name="tienda">
                <option value="">Todas</option>
                <?php foreach ($demo['tienda'] as $tienda): ?>
                    <option value="<?= h($tienda['id_tienda']) ?>" <?= $f_tienda === $tienda['id_tienda'] ? 'selected' : '' ?>><?= h($tienda['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="destino">Destino</label>
            <select id="destino" name="destino">
                <option value="">Todos</option>
                <?php foreach ($demo['destino'] as $destino): ?>
                    <option value="<?= h($destino['id_destino']) ?>" <?= $f_destino === $destino['id_destino'] ? 'selected' : '' ?>><?= h($destino['id_destino']) ?> - <?= h($destino['ciudad']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="q">Guía u orden</label>
            <input type="search" id="q" name="q" value="<?= h($f_q) ?>" autocomplete="off">
        </div>
        <div>
            <button type="submit">Filtrar</button>
        </div>
    </form>
    <?php if ($hay_filtros): ?>
        <p><?= count($envios) ?> envíos coinciden. <a href="listado.php">Limpiar filtros</a></p>
    <?php else: ?>
        <p><?= count($envios) ?> envíos en total.</p>
    <?php endif; ?>

    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Guía</th>
                <th scope="col">Orden</th>
                <th scope="col">Tienda</th>
                <th scope="col">Destino</th>
                <th scope="col">Fecha</th>
                <th scope="col" class="num">Costo</th>
                <th scope="col">Estado</th>
            </tr>
            <?php if (count($envios) === 0): ?>
                <tr>
                    <td colspan="7" class="vacio">Ningún envío coincide con estos filtros. <a href="listado.php">Limpiar filtros</a></td>
                </tr>
            <?php endif; ?>
            <?php foreach ($envios as $envio): ?>
                <?php
                $tienda  = buscar('tienda', 'id_tienda', $envio['id_tienda']);
                $destino = buscar('destino', 'id_destino', $envio['id_destino']);
                ?>
                <tr>
                    <td class="codigo"><a href="detalle.php?guia=<?= h(rawurlencode($envio['no_guia'])) ?>"><?= h($envio['no_guia']) ?></a></td>
                    <td class="nowrap"><?= h($envio['no_orden']) ?></td>
                    <td><?= h($tienda['nombre'] ?? $envio['id_tienda']) ?></td>
                    <td><?= h($envio['id_destino']) ?> - <?= h($destino['ciudad'] ?? '') ?></td>
                    <td class="nowrap"><?= h(fecha($envio['fecha'])) ?> <?= h(hora($envio['hora'])) ?></td>
                    <td class="num"><?= h(moneda($envio['costo_total'])) ?></td>
                    <td><?= insignia_estado($envio['id_estado']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="enlaces">
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
