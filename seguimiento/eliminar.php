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
$guia=trim($fila['no_guia']);
$mensaje='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    if (!$formulario_correcto) {
        $mensaje='Recarga el formulario.';
    }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '')==='no') { header('Location: listado.php'); exit; }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '')==='si') {
        pg_query($conn,'BEGIN');
        pg_query_params($conn,'SELECT No_guia FROM Envio WHERE No_guia=$1 FOR UPDATE',[$guia]);
        $r=pg_query_params($conn,'SELECT * FROM Seguimiento WHERE No_guia=$1 ORDER BY Fecha DESC,Hora DESC,ID_seguimiento DESC LIMIT 2',[$guia]);
        $ultimo=pg_fetch_assoc($r);
        $anterior=pg_fetch_assoc($r);
        $ok=false;
        if ($ultimo && $anterior && $ultimo['id_seguimiento']===$id) {
            $borrar=@pg_query_params($conn,'DELETE FROM Seguimiento WHERE ID_seguimiento=$1',[$id]);
            $cambiar=$borrar ? @pg_query_params($conn,'UPDATE Envio SET ID_estado=$1,Fecha_entrega=CASE WHEN $1::int=5 THEN $2::date ELSE NULL END WHERE No_guia=$3',[$anterior['id_estado'],$anterior['fecha'],$guia]) : false;
            $ok=(bool)$cambiar;
        }
        pg_query($conn,$ok?'COMMIT':'ROLLBACK');
        if ($ok) { $_SESSION['mensaje']='Último avance eliminado; el envío volvió al estado anterior.'; header('Location: listado.php?guia='.rawurlencode($guia)); exit; }
        $mensaje='Solo se puede eliminar el último avance. La orden inicial debe conservarse.';
    }
}
$titulo='Eliminar último seguimiento';
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


<p>¿Eliminar el registro <?= htmlspecialchars(trim((string) ($id))) ?> de la guía <?= htmlspecialchars(trim((string) ($guia))) ?>?</p>
<p class="mensaje">Solo se permite eliminar el último avance. El envío regresará al estado anterior.</p>
<form method="post"><input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
<div class="acciones"><button name="decision" value="si">Sí, eliminar</button><button name="decision" value="no" class="secundario">No</button></div></form>
<a href="listado.php">Volver al seguimiento</a>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
