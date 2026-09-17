<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$mensaje='';
$guia=is_string($_GET['guia'] ?? null)?trim($_GET['guia']):'';
$result=pg_query_params($conn,'SELECT * FROM Envio WHERE No_guia=$1',[$guia]);
$fila=pg_fetch_assoc($result);
if (!$fila) { http_response_code(404); exit('Envío no encontrado.'); }
$fecha=$fila['fecha'];
$hora=substr($fila['hora'],0,5);
$destinatario=trim($fila['destinatario']);
$cabecera=$fila['id_cabecera'];
$tienda=$fila['id_tienda'];
$costo=$fila['costo_total'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $fecha=(is_string($_POST['fecha'] ?? null) ? trim($_POST['fecha']) : '');
    $hora=(is_string($_POST['hora'] ?? null) ? trim($_POST['hora']) : '');
    $destinatario=(is_string($_POST['destinatario'] ?? null) ? trim($_POST['destinatario']) : '');
    $cabecera=(is_string($_POST['id_cabecera'] ?? null) ? trim($_POST['id_cabecera']) : '');
    $tienda=(is_string($_POST['id_tienda'] ?? null) ? trim($_POST['id_tienda']) : '');
    $costo=(is_string($_POST['costo_total'] ?? null) ? trim($_POST['costo_total']) : '');
    $fecha_comprobada = DateTime::createFromFormat('!Y-m-d', $fecha);
    if (!$formulario_correcto) {
        $mensaje='Recarga el formulario.';
    }
    elseif (
        ($guia === '' ||
        mb_strlen($guia) > 20) ||
        ($destinatario === '' ||
        mb_strlen($destinatario) > 150) ||
        (filter_var($cabecera, FILTER_VALIDATE_INT) === false ||
        $cabecera < 1 ||
        $cabecera > 2147483647) ||
        (filter_var($tienda, FILTER_VALIDATE_INT) === false ||
        $tienda < 1 ||
        $tienda > 2147483647)
    ) {
        $mensaje='Completa los datos del envío.';
    }
    elseif (
        !($fecha_comprobada && $fecha_comprobada->format('Y-m-d') === $fecha) ||
        !(preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/D', $hora) === 1) ||
        strtotime($fecha.' '.$hora)>time()
    ) {
        $mensaje='Introduce una fecha y hora válidas, no futuras.';
    }
    elseif ($costo!=='' && !(preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $costo) === 1)) {
        $mensaje='El costo debe ser un importe no negativo con hasta dos decimales.';
    }
    else {
        $r=pg_query_params($conn,'SELECT c.costo_envio+c.costo_manejo AS total, o.cobertura AS origen, d.cobertura AS destino FROM Cabeceras c JOIN Origen o ON c.ID_origen=o.ID_origen JOIN Destino d ON c.ID_destino=d.ID_destino WHERE c.ID_cabecera=$1',[$cabecera]);
        $ruta=pg_fetch_assoc($r);
        if (!$ruta) {
            $mensaje='Selecciona una ruta existente.';
        }
        elseif (
            !in_array(mb_strtoupper(trim($ruta['origen'])),['SI','SÍ','TRUE','1']) ||
            !in_array(mb_strtoupper(trim($ruta['destino'])),['SI','SÍ','TRUE','1'])
        ) {
            $mensaje='La ruta seleccionada no tiene cobertura.';
        }
        else {
            if ($costo==='') $costo=$ruta['total'];
            $result=@pg_query_params($conn,'UPDATE Envio SET Fecha=$1,Hora=$2,Destinatario=$3,ID_cabecera=$4,ID_tienda=$5,Costo_total=$6 WHERE No_guia=$7 AND (Fecha_entrega IS NULL OR Fecha_entrega >= $1::date)',[$fecha,$hora,$destinatario,$cabecera,$tienda,$costo,$guia]);
            $ok=$result && pg_affected_rows($result)===1;
            if ($ok) { $_SESSION['mensaje']='Envío guardado.'; header('Location: listado.php'); exit; }
            $mensaje='No se pudo guardar. Comprueba guía única, tienda y estado existentes, y fecha anterior a la entrega.';
        }
    }
}
$rutas=pg_query($conn,'SELECT c.*, o.Ciudad AS origen,d.Ciudad AS destino FROM Cabeceras c JOIN Origen o ON c.ID_origen=o.ID_origen JOIN Destino d ON c.ID_destino=d.ID_destino ORDER BY c.ID_cabecera');
$tiendas=pg_query($conn,'SELECT ID_tienda,Nombre FROM Tienda ORDER BY Nombre');
$titulo='Editar envío';
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


<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <label for="no_guia">Número de guía</label><input type="text" id="no_guia" name="no_guia" maxlength="20" value="<?= htmlspecialchars(trim((string) ($guia))) ?>" readonly required>
    <label for="fecha">Fecha</label><input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars(trim((string) ($fecha))) ?>" required>
    <label for="hora">Hora</label><input type="time" id="hora" name="hora" value="<?= htmlspecialchars(trim((string) ($hora))) ?>" required>
    <label for="destinatario">Destinatario</label><input type="text" id="destinatario" name="destinatario" maxlength="150" value="<?= htmlspecialchars(trim((string) ($destinatario))) ?>" required>
    <label for="id_tienda">Tienda</label><select id="id_tienda" name="id_tienda" required><option value="">Seleccionar...</option>
    <?php while ($t=pg_fetch_assoc($tiendas)): ?><option value="<?= htmlspecialchars(trim((string) ($t['id_tienda']))) ?>" <?= $tienda===$t['id_tienda']?'selected':'' ?>><?= htmlspecialchars(trim((string) ($t['nombre']))) ?></option><?php endwhile; ?></select>
    <label for="id_cabecera">Ruta y tarifa</label><select id="id_cabecera" name="id_cabecera" required><option value="">Seleccionar...</option>
    <?php while ($c=pg_fetch_assoc($rutas)): ?><option value="<?= htmlspecialchars(trim((string) ($c['id_cabecera']))) ?>" <?= $cabecera===$c['id_cabecera']?'selected':'' ?>><?= htmlspecialchars(trim((string) (trim($c['origen']).' → '.trim($c['destino']).' · Q'.number_format($c['costo_envio']+$c['costo_manejo'],2)))) ?></option><?php endwhile; ?></select>
    <label for="costo_total">Costo total (Q)</label><input type="number" id="costo_total" name="costo_total" min="0" max="99999999.99" step="0.01" value="<?= htmlspecialchars(trim((string) ($costo))) ?>">
    <p class="ayuda">Vacío: se usa el costo de envío más manejo de la ruta. El estado se cambia desde Seguimiento.</p>
    <button>Guardar envío</button>
</form>
<div class="enlaces"><a href="listado.php">Listado de envíos</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
