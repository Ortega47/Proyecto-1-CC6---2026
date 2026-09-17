<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$mensaje = '';
$id_cabecera = '';
$costo_envio = '';
$costo_manejo = '';
$id_origen = '';
$id_destino = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $id_cabecera = (is_string($_POST['id_cabecera'] ?? null) ? trim($_POST['id_cabecera']) : '');
    $costo_envio = (is_string($_POST['costo_envio'] ?? null) ? trim($_POST['costo_envio']) : '');
    $costo_manejo = (is_string($_POST['costo_manejo'] ?? null) ? trim($_POST['costo_manejo']) : '');
    $id_origen = (is_string($_POST['id_origen'] ?? null) ? trim($_POST['id_origen']) : '');
    $id_destino = (is_string($_POST['id_destino'] ?? null) ? trim($_POST['id_destino']) : '');
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario e inténtalo de nuevo.';
    }
    elseif (
        (filter_var($id_cabecera, FILTER_VALIDATE_INT) === false ||
        $id_cabecera < 1 ||
        $id_cabecera > 2147483647) ||
        !(preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $costo_envio) === 1) ||
        !(preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $costo_manejo) === 1) ||
        (filter_var($id_origen, FILTER_VALIDATE_INT) === false ||
        $id_origen < 1 ||
        $id_origen > 2147483647) ||
        (filter_var($id_destino, FILTER_VALIDATE_INT) === false ||
        $id_destino < 1 ||
        $id_destino > 2147483647)
    ) {
        $mensaje = 'Revisa los campos: IDs positivos, textos completos e importes no negativos.';
    }
    else {
        $result = @pg_query_params($conn, 'INSERT INTO Cabeceras (id_cabecera, costo_envio, costo_manejo, id_origen, id_destino) VALUES ($1, $2, $3, $4, $5)', [$id_cabecera, $costo_envio, $costo_manejo, $id_origen, $id_destino]);
        if ($result) {
            $_SESSION['mensaje'] = 'Registro guardado correctamente.';
            header('Location: listado.php'); exit;
        }
        $mensaje = 'No se pudo guardar. Comprueba que el ID no esté repetido y las referencias existan.';
    }
}
$opciones_id_origen = pg_query($conn, 'SELECT id_origen, ciudad FROM Origen ORDER BY ciudad');
$opciones_id_destino = pg_query($conn, 'SELECT id_destino, ciudad FROM Destino ORDER BY ciudad');
$titulo = 'Agregar ruta';
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
    <label for="id_cabecera">ID de cabecera</label>
    <input id="id_cabecera" name="id_cabecera" type="number" min="1" max="2147483647" step="1" required value="<?= htmlspecialchars(trim((string) ($id_cabecera))) ?>">
    <label for="costo_envio">Costo de envío (Q)</label>
    <input id="costo_envio" name="costo_envio" type="number" min="0" max="99999999.99" step="0.01" required value="<?= htmlspecialchars(trim((string) ($costo_envio))) ?>">
    <label for="costo_manejo">Costo de manejo (Q)</label>
    <input id="costo_manejo" name="costo_manejo" type="number" min="0" max="99999999.99" step="0.01" required value="<?= htmlspecialchars(trim((string) ($costo_manejo))) ?>">
    <label for="id_origen">Origen</label>
    <select id="id_origen" name="id_origen" required>
        <option value="">Seleccionar...</option>
        <?php while ($opcion = pg_fetch_assoc($opciones_id_origen)): ?>
        <option value="<?= htmlspecialchars(trim((string) ($opcion['id_origen']))) ?>" <?= $id_origen == $opcion['id_origen'] ? 'selected' : '' ?>><?= htmlspecialchars(trim((string) ($opcion['id_origen'].' - '.trim($opcion['ciudad'])))) ?></option>
        <?php endwhile; ?>
    </select>
    <label for="id_destino">Destino</label>
    <select id="id_destino" name="id_destino" required>
        <option value="">Seleccionar...</option>
        <?php while ($opcion = pg_fetch_assoc($opciones_id_destino)): ?>
        <option value="<?= htmlspecialchars(trim((string) ($opcion['id_destino']))) ?>" <?= $id_destino == $opcion['id_destino'] ? 'selected' : '' ?>><?= htmlspecialchars(trim((string) ($opcion['id_destino'].' - '.trim($opcion['ciudad'])))) ?></option>
        <?php endwhile; ?>
    </select>
    <button>Guardar</button>
</form>
<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
