<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';

$guia=is_string($_GET['guia'] ?? null)?trim($_GET['guia']):'';
$result=pg_query_params($conn,'SELECT e.*,s.Nombre AS estado FROM Envio e JOIN Estado s ON e.ID_estado=s.ID_estado WHERE e.No_guia=$1',[$guia]);
$envio=pg_fetch_assoc($result);
if (!$envio) { http_response_code(404); exit('Envío no encontrado.'); }
$actual=(int)$envio['id_estado'];
$result=pg_query_params($conn,'SELECT * FROM Estado WHERE ID_estado=$1',[$actual+1]);
$siguiente=pg_fetch_assoc($result);
$mensaje='';
$observacion='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $observacion=(is_string($_POST['observacion'] ?? null) ? trim($_POST['observacion']) : '');
    if (!$formulario_correcto) {
        $mensaje='Recarga el formulario.';
    }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '')==='no') { header('Location: ../envios/listado.php'); exit; }
    elseif (!$siguiente || $actual>=5) {
        $mensaje='Este envío ya fue entregado o falta el siguiente estado en el catálogo.';
    }
    elseif ((is_string($_POST['estado_actual'] ?? null) ? trim($_POST['estado_actual']) : '')!==(string)$actual) {
        $mensaje='El estado cambió. Recarga la página antes de continuar.';
    }
    elseif (mb_strlen($observacion)>200) {
        $mensaje='La observación admite hasta 200 caracteres.';
    }
    elseif ((is_string($_POST['decision'] ?? null) ? trim($_POST['decision']) : '')==='si') {
        pg_query($conn,'BEGIN');
        // La condición evita registrar dos avances si dos personas confirman a la vez.
        $result=@pg_query_params($conn,'UPDATE Envio SET ID_estado=$1,Fecha_entrega=CASE WHEN $1::int=5 THEN CURRENT_DATE ELSE NULL END WHERE No_guia=$2 AND ID_estado=$3',[$actual+1,$guia,$actual]);
        $ok=$result && pg_affected_rows($result)===1;
        if ($ok) {
            pg_query($conn,'LOCK TABLE Seguimiento IN EXCLUSIVE MODE');
            $result=@pg_query_params($conn,'INSERT INTO Seguimiento (ID_seguimiento,Observacion,Hora,Fecha,No_guia,ID_estado,ID_usuario) SELECT COALESCE(MAX(ID_seguimiento),0)+1,$1,LOCALTIME,CURRENT_DATE,$2,$3,$4 FROM Seguimiento',[$observacion,$guia,$actual+1,$_SESSION['id']]);
            $ok=(bool)$result;
        }
        pg_query($conn,$ok?'COMMIT':'ROLLBACK');
        if ($ok) { $_SESSION['mensaje']='Estado actualizado y seguimiento guardado.'; header('Location: ../envios/detalle.php?guia='.rawurlencode($guia)); exit; }
        $mensaje='No se pudo cambiar el estado. Recarga la página y vuelve a intentarlo.';
    }
}
$titulo='Cambiar estado';
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


<p>Guía: <b><?= htmlspecialchars(trim((string) ($guia))) ?></b><br>Destinatario: <?= htmlspecialchars(trim((string) ($envio['destinatario']))) ?><br>Estado actual: <?= htmlspecialchars(trim((string) ($envio['estado']))) ?></p>
<?php if ($siguiente && $actual<5): ?>
<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <input type="hidden" name="estado_actual" value="<?= htmlspecialchars(trim((string) ($actual))) ?>">
    <label for="observacion">Observación (opcional)</label><textarea id="observacion" name="observacion" maxlength="200" rows="3"><?= htmlspecialchars(trim((string) ($observacion))) ?></textarea>
    <p>¿Pasar a <b><?= htmlspecialchars(trim((string) ($siguiente['nombre']))) ?></b>?</p>
    <div class="acciones"><button name="decision" value="si">Sí, cambiar estado</button><button name="decision" value="no" class="secundario">No</button></div>
</form>
<?php else: ?><p>El envío está entregado o no tiene un siguiente estado configurado.</p><?php endif; ?>
<a href="../envios/detalle.php?guia=<?= htmlspecialchars(trim((string) (rawurlencode($guia)))) ?>">Volver al envío</a>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
