<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$mensaje = '';
$id = $_GET['id'] ?? '';
if (!is_string($id) || (filter_var($id, FILTER_VALIDATE_INT) === false || $id < 1 || $id > 2147483647)) { http_response_code(404); exit('Identificador no válido.'); }
$result = pg_query_params($conn, 'SELECT id_origen, ciudad, cobertura FROM Origen WHERE id_origen=$1', [$id]);
$fila = pg_fetch_assoc($result);
if (!$fila) { http_response_code(404); exit('El registro no existe.'); }
$id_origen = trim((string)$fila['id_origen']);
$ciudad = trim((string)$fila['ciudad']);
$cobertura = trim((string)$fila['cobertura']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $ciudad = (is_string($_POST['ciudad'] ?? null) ? trim($_POST['ciudad']) : '');
    $cobertura = (is_string($_POST['cobertura'] ?? null) ? trim($_POST['cobertura']) : '');
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario e inténtalo de nuevo.';
    }
    elseif (
        (filter_var($id_origen, FILTER_VALIDATE_INT) === false ||
        $id_origen < 1 ||
        $id_origen > 2147483647) ||
        ($ciudad === '' ||
        mb_strlen($ciudad) > 100) ||
        !in_array($cobertura, ['SI','NO'], true)
    ) {
        $mensaje = 'Revisa los campos: IDs positivos, textos completos e importes no negativos.';
    }
    else {
        $result = @pg_query_params($conn, 'UPDATE Origen SET ciudad=$1, cobertura=$2 WHERE id_origen=$3', [$ciudad, $cobertura, $id]);
        if ($result) {
            $_SESSION['mensaje'] = 'Registro guardado correctamente.';
            header('Location: listado.php'); exit;
        }
        $mensaje = 'No se pudo guardar. Comprueba que el ID no esté repetido y las referencias existan.';
    }
}
$titulo = 'Editar origen';
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
    <label for="id_origen">ID del origen</label>
    <input id="id_origen" name="id_origen" type="number" min="1" max="2147483647" step="1" readonly required value="<?= htmlspecialchars(trim((string) ($id_origen))) ?>">
    <label for="ciudad">Ciudad</label>
    <input id="ciudad" name="ciudad" type="text" maxlength="100" required value="<?= htmlspecialchars(trim((string) ($ciudad))) ?>">
    <label for="cobertura">Cobertura</label>
    <select id="cobertura" name="cobertura" required>
        <option value="SI" <?= in_array(mb_strtoupper($cobertura),['SI','SÍ','TRUE','1']) ? 'selected' : '' ?>>Sí</option>
        <option value="NO" <?= in_array(mb_strtoupper($cobertura),['NO','FALSE','0']) ? 'selected' : '' ?>>No</option>
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
