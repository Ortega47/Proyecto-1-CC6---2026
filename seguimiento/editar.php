<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }

$id=$_GET['id'] ?? '';
if (!is_string($id) || (filter_var($id, FILTER_VALIDATE_INT) === false || $id < 1 || $id > 2147483647)) { http_response_code(404); exit('ID no válido.'); }
$result=pg_query_params($conn,'SELECT * FROM Seguimiento WHERE ID_seguimiento=$1',[$id]);
$fila=pg_fetch_assoc($result);
if (!$fila) { http_response_code(404); exit('Seguimiento no encontrado.'); }
$observacion=trim($fila['observacion'] ?? '');
$mensaje='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $observacion=(is_string($_POST['observacion'] ?? null) ? trim($_POST['observacion']) : '');
    if (!$formulario_correcto) {
        $mensaje='Recarga el formulario.';
    }
    elseif (mb_strlen($observacion)>200) {
        $mensaje='La observación admite hasta 200 caracteres.';
    }
    else {
        $result=@pg_query_params($conn,'UPDATE Seguimiento SET Observacion=$1 WHERE ID_seguimiento=$2',[$observacion,$id]);
        if ($result) { $_SESSION['mensaje']='Observación actualizada.'; header('Location: listado.php?guia='.rawurlencode(trim($fila['no_guia']))); exit; }
        $mensaje='No se pudo actualizar la observación.';
    }
}
$titulo='Editar observación';
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


<p>Guía: <?= htmlspecialchars(trim((string) ($fila['no_guia']))) ?> · <?= htmlspecialchars(trim((string) ($fila['fecha']))) ?> <?= htmlspecialchars(trim((string) (substr($fila['hora'],0,5)))) ?></p>
<p class="ayuda">El estado, la fecha y el usuario conservan los datos del avance registrado.</p>
<form method="post"><input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
<label for="observacion">Observación</label><textarea id="observacion" name="observacion" rows="4" maxlength="200"><?= htmlspecialchars(trim((string) ($observacion))) ?></textarea><button>Guardar</button></form>
<div class="enlaces"><a href="listado.php">Seguimiento</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
