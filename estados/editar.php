<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$mensaje = '';
$id = $_GET['id'] ?? '';
if (!is_string($id) || (filter_var($id, FILTER_VALIDATE_INT) === false || $id < 1 || $id > 2147483647)) { http_response_code(404); exit('Identificador no válido.'); }
$result = pg_query_params($conn, 'SELECT id_estado, orden, nombre FROM Estado WHERE id_estado=$1', [$id]);
$fila = pg_fetch_assoc($result);
if (!$fila) { http_response_code(404); exit('El registro no existe.'); }
$id_estado = trim((string)$fila['id_estado']);
$orden = trim((string)$fila['orden']);
$nombre = trim((string)$fila['nombre']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $orden = (is_string($_POST['orden'] ?? null) ? trim($_POST['orden']) : '');
    $nombre = (is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '');
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario e inténtalo de nuevo.';
    }
    elseif (
        (filter_var($id_estado, FILTER_VALIDATE_INT) === false ||
        $id_estado < 1 ||
        $id_estado > 2147483647) ||
        (filter_var($orden, FILTER_VALIDATE_INT) === false ||
        $orden < 1 ||
        $orden > 2147483647) ||
        ($nombre === '' ||
        mb_strlen($nombre) > 60)
    ) {
        $mensaje = 'Revisa los campos: IDs positivos, textos completos e importes no negativos.';
    }
    elseif ((int)$id_estado > 5 || (int)$orden !== (int)$id_estado) {
        $mensaje = 'Los estados van del 1 al 5 y su orden debe coincidir con su ID.';
    }
    else {
        $result = @pg_query_params($conn, 'UPDATE Estado SET orden=$1, nombre=$2 WHERE id_estado=$3', [$orden, $nombre, $id]);
        if ($result) {
            $_SESSION['mensaje'] = 'Registro guardado correctamente.';
            header('Location: listado.php'); exit;
        }
        $mensaje = 'No se pudo guardar. Comprueba que el ID no esté repetido y las referencias existan.';
    }
}
$titulo = 'Editar estado';
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
    <label for="id_estado">ID del estado (1 a 5)</label>
    <input id="id_estado" name="id_estado" type="number" min="1" max="2147483647" step="1" readonly required value="<?= htmlspecialchars(trim((string) ($id_estado))) ?>">
    <label for="orden">Orden (igual al ID)</label>
    <input id="orden" name="orden" type="number" min="1" max="2147483647" step="1" required value="<?= htmlspecialchars(trim((string) ($orden))) ?>">
    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" type="text" maxlength="60" required value="<?= htmlspecialchars(trim((string) ($nombre))) ?>">
    <button>Guardar</button>
</form>
<div class="enlaces"><a href="listado.php">Volver al listado</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
