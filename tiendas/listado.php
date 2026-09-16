<?php
$raiz = '../';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$admin = !empty($_SESSION['es_admin']);

$titulo = 'Tiendas';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido">
    <h1>Tiendas afiliadas</h1>
    <p>Tiendas virtuales que pueden pedir envíos. El código es el que la tienda manda en cada llamada al WebService.</p>

    <?php if ($admin): ?>
        <div class="botones-listado">
            <a class="boton" href="agregar.php">Agregar tienda</a>
        </div>
    <?php endif; ?>

    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Nombre</th>
                <th scope="col">Host</th>
                <th scope="col" class="num">Envíos</th>
                <?php if ($admin): ?><th scope="col">Acciones</th><?php endif; ?>
            </tr>
            <?php foreach ($demo['tienda'] as $tienda): ?>
                <tr>
                    <td class="codigo"><?= h($tienda['id_tienda']) ?></td>
                    <td><?= h($tienda['nombre']) ?></td>
                    <td><?= h($tienda['host']) ?></td>
                    <td class="num"><?= contar_envios('id_tienda', $tienda['id_tienda']) ?></td>
                    <?php if ($admin): ?>
                        <td class="nowrap">
                            <a href="editar.php?id=<?= h(rawurlencode($tienda['id_tienda'])) ?>">Editar</a> |
                            <a href="eliminar.php?id=<?= h(rawurlencode($tienda['id_tienda'])) ?>">Eliminar</a>
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
