<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$mensaje = '';
$id_tienda = '';
$no_orden = '';
$nombre = '';
$host = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $id_tienda = (is_string($_POST['id_tienda'] ?? null) ? trim($_POST['id_tienda']) : '');
    $no_orden = (is_string($_POST['no_orden'] ?? null) ? trim($_POST['no_orden']) : '');
    $nombre = (is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '');
    $host = (is_string($_POST['host'] ?? null) ? trim($_POST['host']) : '');
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario e inténtalo de nuevo.';
    }
    elseif (
        (filter_var($id_tienda, FILTER_VALIDATE_INT) === false ||
        $id_tienda < 1 ||
        $id_tienda > 2147483647) ||
        (filter_var($no_orden, FILTER_VALIDATE_INT) === false ||
        $no_orden < 1 ||
        $no_orden > 2147483647) ||
        ($nombre === '' ||
        mb_strlen($nombre) > 150) ||
        ($host === '' ||
        mb_strlen($host) > 255)
    ) {
        $mensaje = 'Revisa los campos: IDs positivos, textos completos e importes no negativos.';
    }
    else {
        $result = @pg_query_params($conn, 'INSERT INTO Tienda (id_tienda, no_orden, nombre, host) VALUES ($1, $2, $3, $4)', [$id_tienda, $no_orden, $nombre, $host]);
        if ($result) {
            $_SESSION['mensaje'] = 'Registro guardado correctamente.';
            header('Location: listado.php'); exit;
        }
        $mensaje = 'No se pudo guardar. Comprueba que el ID no esté repetido y las referencias existan.';
    }
}
$titulo = 'Agregar tienda';
$formulario = true;

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


<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <label for="id_tienda">ID de tienda</label>
    <input id="id_tienda" name="id_tienda" type="number" min="1" max="2147483647" step="1" required value="<?= htmlspecialchars(trim((string) ($id_tienda))) ?>">
    <label for="no_orden">Número de orden</label>
    <input id="no_orden" name="no_orden" type="number" min="1" max="2147483647" step="1" required value="<?= htmlspecialchars(trim((string) ($no_orden))) ?>">
    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" type="text" maxlength="150" required value="<?= htmlspecialchars(trim((string) ($nombre))) ?>">
    <label for="host">Host de la tienda</label>
    <input id="host" name="host" type="text" maxlength="255" required value="<?= htmlspecialchars(trim((string) ($host))) ?>">
    <button>Guardar</button>
</form>
<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
