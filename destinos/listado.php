<?php
$raiz = '../';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$admin = !empty($_SESSION['es_admin']);
$aviso = aviso();

$titulo = 'Destinos';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido">
    <h1>Destinos</h1>
    <p>Ciudades a las que llegamos y lo que cobra el courier por cada una. Sin cobertura, la tienda recibe costo 0.</p>

    <?php if ($aviso !== ''): ?>
        <p class="aviso"><?= h($aviso) ?></p>
    <?php endif; ?>

    <?php if ($admin): ?>
        <div class="botones-listado">
            <a class="boton" href="agregar.php">Agregar destino</a>
        </div>
    <?php endif; ?>

    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Ciudad</th>
                <th scope="col">Cobertura</th>
                <th scope="col" class="num">Costo de envío</th>
                <th scope="col" class="num">Costo de manejo</th>
                <th scope="col" class="num">Total</th>
                <?php if ($admin): ?><th scope="col">Acciones</th><?php endif; ?>
            </tr>
            <?php foreach ($demo['destino'] as $destino): ?>
                <tr>
                    <td class="codigo"><?= h($destino['id_destino']) ?></td>
                    <td><?= h($destino['ciudad']) ?></td>
                    <td><?= $destino['cobertura'] ? 'Sí' : 'No' ?></td>
                    <td class="num"><?= h(moneda($destino['costo_envio'])) ?></td>
                    <td class="num"><?= h(moneda($destino['costo_manejo'])) ?></td>
                    <td class="num"><?= h(moneda($destino['costo_envio'] + $destino['costo_manejo'])) ?></td>
                    <?php if ($admin): ?>
                        <td class="nowrap">
                            <a href="editar.php?id=<?= h(rawurlencode($destino['id_destino'])) ?>">Editar</a> |
                            <a href="eliminar.php?id=<?= h(rawurlencode($destino['id_destino'])) ?>">Eliminar</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="enlaces">
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
