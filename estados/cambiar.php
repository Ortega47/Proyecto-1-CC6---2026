<?php
// Confirmación del cambio de estado (mismo patrón que eliminar: Sí / No).
$raiz = '../';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$guia = trim($_REQUEST['guia'] ?? '');
// TODO(bd): pg_query_params($conn, 'SELECT * FROM envio WHERE no_guia = $1', [$guia])
$envio = $guia !== '' ? buscar('envio', 'no_guia', $guia) : null;

$error = '';
$actual    = $envio !== null ? (int) $envio['id_estado'] : null;
$siguiente = ($actual !== null && $actual < 5) ? $actual + 1 : null;

if ($envio !== null && $siguiente !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $decision    = $_POST['decision'] ?? '';
    $nuevo       = (int) ($_POST['id_estado_nuevo'] ?? 0);
    $observacion = trim($_POST['observacion'] ?? '');

    if ($decision !== 'si') {
        header('Location: ' . archivo_estado($actual));
        exit;
    }
    // Solo se puede avanzar al estado inmediato siguiente; nunca saltar ni retroceder.
    if ($nuevo !== $siguiente) {
        $error = 'Solo se puede pasar al estado inmediato siguiente (' . nombre_estado($siguiente) . ').';
    } else {
        // TODO(bd): en una transacción:
        //   pg_query($conn, 'BEGIN');
        //   pg_query_params($conn, 'UPDATE envio SET id_estado = $1 WHERE no_guia = $2 AND id_estado = $3', [$nuevo, $guia, $actual]);
        //   pg_query_params($conn, 'INSERT INTO seguimiento (no_guia, id_estado, id_usuario, observacion) VALUES ($1, $2, $3, NULLIF($4, \'\'))',
        //                   [$guia, $nuevo, $_SESSION['id_usuario'], $observacion]);
        //   pg_query($conn, 'COMMIT');
        $_SESSION['aviso'] = 'Vista previa: esto se guardará cuando conectemos la base de datos. '
            . 'La guía ' . $guia . ' pasaría a ' . nombre_estado($nuevo) . '.';
        header('Location: ' . archivo_estado($actual));
        exit;
    }
}

$titulo = 'Cambiar estado';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Cambiar estado</h1>
<?php if ($envio === null): ?>
    <p class="error">No existe ningún envío con la guía <?= h($guia) ?>.</p>
    <div class="enlaces">
        <a href="../envios/listado.php">Listado de envíos</a>
        <a href="../menu.php">Menú principal</a>
    </div>
<?php elseif ($siguiente === null): ?>
    <p class="aviso">La guía <?= h($envio['no_guia']) ?> ya fue entregada. No hay más etapas.</p>
    <div class="enlaces">
        <a href="../envios/detalle.php?guia=<?= h(rawurlencode($envio['no_guia'])) ?>">Ver el detalle</a>
        <a href="entregadas.php">Envíos entregados</a>
    </div>
<?php else: ?>
    <?php if ($error !== ''): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>
    <div class="cuadro">
        <dl>
            <dt>Guía</dt>
            <dd class="codigo"><?= h($envio['no_guia']) ?></dd>
            <dt>Destinatario</dt>
            <dd><?= h($envio['destinatario']) ?></dd>
            <dt>Estado actual</dt>
            <dd><?= insignia_estado($actual) ?></dd>
            <dt>Pasaría a</dt>
            <dd><?= insignia_estado($siguiente) ?></dd>
        </dl>
    </div>

    <form method="post" action="cambiar.php">
        <input type="hidden" name="guia" value="<?= h($envio['no_guia']) ?>">
        <input type="hidden" name="id_estado_nuevo" value="<?= $siguiente ?>">

        <label for="observacion">Observación (opcional)</label>
        <textarea id="observacion" name="observacion" maxlength="200"></textarea>

        <h3>¿Pasar la guía a <?= h(nombre_estado($siguiente)) ?>?</h3>
        <div class="acciones">
            <button type="submit" name="decision" value="si">Sí, pasar a <?= h(nombre_estado($siguiente)) ?></button>
            <button type="submit" name="decision" value="no" class="secundario">No</button>
        </div>
    </form>

    <div class="enlaces">
        <a href="<?= h(archivo_estado($actual)) ?>">Envíos en <?= h(nombre_estado($actual)) ?></a>
        <a href="../envios/detalle.php?guia=<?= h(rawurlencode($envio['no_guia'])) ?>">Detalle de la guía</a>
    </div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
