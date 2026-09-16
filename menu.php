<?php
$raiz = '';
require __DIR__ . '/auth.php';
require __DIR__ . '/funciones.php';
require __DIR__ . '/datos_demo.php';

$conteo = conteo_por_estado();
$aviso  = aviso();

$titulo = 'Menú';
require __DIR__ . '/encabezado.php';
?>
<div class="tarjeta">
    <h1>Bienvenido, <?= h($_SESSION['nombre']) ?></h1>
    <p class="rol"><?= $_SESSION['es_admin'] ? 'Administrador' : 'Operador' ?></p>

    <?php if ($aviso !== ''): ?>
        <p class="aviso"><?= h($aviso) ?></p>
    <?php endif; ?>

    <p class="grupo">Seguimiento</p>
    <a class="boton" href="envios/listado.php">Envíos</a>
    <?php foreach ($conteo as $id_estado => $cantidad): ?>
        <a class="boton" href="estados/<?= h(archivo_estado($id_estado)) ?>"><?= h(nombre_estado($id_estado)) ?> (<?= $cantidad ?>)</a>
    <?php endforeach; ?>

    <p class="grupo">Catálogos</p>
    <a class="boton" href="destinos/listado.php">Destinos</a>
    <a class="boton" href="tiendas/listado.php">Tiendas</a>

    <?php if ($_SESSION['es_admin']): ?>
        <p class="grupo">Administración</p>
        <a class="boton" href="usuarios/listado.php">Usuarios</a>
        <a class="boton" href="resumen.php">Resumen</a>
    <?php endif; ?>

    <p class="grupo">&nbsp;</p>
    <a class="boton secundario" href="index.php">Rastrear un paquete</a>
    <a class="boton secundario" href="logout.php">Cerrar sesión</a>
</div>
<?php require __DIR__ . '/pie.php'; ?>
