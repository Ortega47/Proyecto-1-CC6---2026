<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$id = $_GET['id'] ?? '';
if (!is_string($id) || (filter_var($id, FILTER_VALIDATE_INT) === false || $id < 1 || $id > 2147483647)) { http_response_code(404); exit('Identificador no válido.'); }
$result = pg_query_params($conn, 'SELECT id_cabecera FROM Cabeceras WHERE id_cabecera=$1', [$id]);
if (pg_num_rows($result) === 0) { http_response_code(404); exit('El registro no existe.'); }
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    if (!$formulario_correcto) {
        $mensaje = 'Recarga el formulario.';
    }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '') === 'no') { header('Location: listado.php'); exit; }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '') === 'si') {
        $result = @pg_query_params($conn, 'DELETE FROM Cabeceras WHERE id_cabecera=$1', [$id]);
        if ($result) { $_SESSION['mensaje']='Registro eliminado.'; header('Location: listado.php'); exit; }
        $mensaje = 'No se puede eliminar porque otros registros lo utilizan.';
    }
}
$titulo = 'Eliminar ruta';
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


<p>¿Eliminar el registro con ID <b><?= htmlspecialchars(trim((string) ($id))) ?></b>?</p>
<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <div class="acciones"><button name="decision" value="si">Sí, eliminar</button><button name="decision" value="no" class="secundario">No</button></div>
</form>
<a href="listado.php">Volver al listado</a>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
