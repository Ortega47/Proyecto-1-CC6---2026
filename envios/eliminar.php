<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }

$guia=is_string($_GET['guia'] ?? null)?trim($_GET['guia']):'';
$result=pg_query_params($conn,'SELECT No_guia,Destinatario FROM Envio WHERE No_guia=$1',[$guia]);
$envio=pg_fetch_assoc($result);
if (!$envio) { http_response_code(404); exit('Envío no encontrado.'); }
$mensaje='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    if (!$formulario_correcto) {
        $mensaje='Recarga el formulario.';
    }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '')==='no') { header('Location: listado.php'); exit; }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '')==='si') {
        $result=@pg_query_params($conn,'DELETE FROM Envio WHERE No_guia=$1',[$guia]);
        if ($result) { $_SESSION['mensaje']='Envío y seguimiento eliminados.'; header('Location: listado.php'); exit; }
        $mensaje='No se pudo eliminar el envío.';
    }
}
$titulo='Eliminar envío';
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


<p>¿Eliminar la guía <b><?= htmlspecialchars(trim((string) ($guia))) ?></b> de <?= htmlspecialchars(trim((string) ($envio['destinatario']))) ?>?</p>
<p class="mensaje">También se eliminará todo su historial de seguimiento.</p>
<form method="post"><input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
<div class="acciones"><button name="decision" value="si">Sí, eliminar</button><button name="decision" value="no" class="secundario">No</button></div></form>
<a href="listado.php">Volver al listado</a>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
