<?php
require __DIR__.'/auth.php';
$titulo='Menú del courier';
$mensaje='';
$formulario=true;

$raiz = $raiz ?? '';
if ($mensaje === '' && isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(trim((string) ($titulo))) ?> - Courier</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(trim((string) ($raiz))) ?>style.css">
</head>
<body>
<header>
    <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>index.php">Courier</a>
    <nav><a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>rastreo.php">Rastrear paquete</a>
    <?php if (isset($_SESSION['id'])): ?>
        <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>index.php">Menú</a>
        <a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>logout.php">Cerrar sesión</a>
    <?php else: ?><a href="<?= htmlspecialchars(trim((string) ($raiz))) ?>login.php">Iniciar sesión</a><?php endif; ?>
    </nav>
</header>
<main class="<?= !empty($formulario) ? 'formulario' : 'contenido' ?>">
<h1><?= htmlspecialchars(trim((string) ($titulo))) ?></h1>
<?php if ($mensaje !== ''): ?><p class="mensaje" role="status"><?= htmlspecialchars(trim((string) ($mensaje))) ?></p><?php endif; ?>


<p>Bienvenido, <b><?= htmlspecialchars(trim((string) ($_SESSION['nombre']))) ?></b>.</p>
<p>Administrador</p>
<div class="menu">
    <a class="button" href="envios/listado.php">Envíos</a>
    <a class="button" href="seguimiento/listado.php">Seguimiento</a>
    <a class="button" href="estados/orden_nueva.php">Orden nueva</a>
    <a class="button" href="estados/surtiendose.php">Surtiéndose</a>
    <a class="button" href="estados/empacandose.php">Empacándose</a>
    <a class="button" href="estados/en_ruta.php">En ruta</a>
    <a class="button" href="estados/entregadas.php">Entregadas</a>
    <h2>Catálogos</h2>
    <a class="button" href="origenes/listado.php">Orígenes</a>
    <a class="button" href="destinos/listado.php">Destinos</a>
    <a class="button" href="cabeceras/listado.php">Rutas y tarifas</a>
    <a class="button" href="tiendas/listado.php">Tiendas</a>
    <?php if ($_SESSION['admin']): ?>
    <a class="button" href="estados/listado.php">Catálogo de estados</a>
    <?php endif; ?>
    <a class="button secundario" href="rastreo.php">Rastrear un paquete</a>
</div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
