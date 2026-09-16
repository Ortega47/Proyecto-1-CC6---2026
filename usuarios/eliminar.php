<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id = (int) ($_REQUEST['id'] ?? 0);
// TODO(bd): pg_query_params($conn, 'SELECT id_usuario, nombre, usuario, es_admin FROM usuario WHERE id_usuario = $1', [$id])
$usuario = $id > 0 ? buscar('usuario', 'id_usuario', $id) : null;

// Registros de seguimiento hechos por este usuario.
// TODO(bd): pg_query_params($conn, 'SELECT COUNT(*) FROM seguimiento WHERE id_usuario = $1', [$id])
$registros = 0;
foreach ($demo['seguimiento'] as $registro) {
    if ($registro['id_usuario'] !== null && (int) $registro['id_usuario'] === $id) {
        $registros++;
    }
}

$es_propio = ((int) $_SESSION['id_usuario'] === $id);
$impedimento = '';
if ($es_propio) {
    $impedimento = 'No puede eliminar su propio usuario.';
} elseif ($registros > 0) {
    $impedimento = 'Este usuario tiene ' . $registros . ' registros en el seguimiento de envíos y no se puede eliminar; el historial lo necesita.';
}

$mensaje = '';
$clase   = '';

if ($usuario !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['decision'] ?? '') !== 'si') {
        header('Location: listado.php');
        exit;
    }
    if ($impedimento !== '') {
        $mensaje = 'No se puede eliminar. ' . $impedimento;
        $clase   = 'error';
    } else {
        // TODO(bd): pg_query_params($conn, 'DELETE FROM usuario WHERE id_usuario = $1', [$id]);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
}

$titulo = 'Eliminar usuario';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Eliminar usuario</h1>
<?php if ($usuario === null): ?>
    <p class="error">No existe ningún usuario con el id <?= $id ?>.</p>
<?php else: ?>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <div class="cuadro">
        <dl>
            <dt>Nombre</dt>
            <dd><?= h($usuario['nombre']) ?></dd>
            <dt>Usuario</dt>
            <dd class="codigo"><?= h($usuario['usuario']) ?></dd>
            <dt>Rol</dt>
            <dd><?= $usuario['es_admin'] ? 'Administrador' : 'Operador' ?></dd>
            <dt>Registros</dt>
            <dd><?= $registros ?></dd>
        </dl>
    </div>

    <?php if ($impedimento !== ''): ?>
        <p class="error"><?= h($impedimento) ?></p>
    <?php elseif ($clase !== 'aviso'): ?>
        <form method="post" action="eliminar.php?id=<?= $id ?>">
            <h3>¿Eliminar al usuario <?= h($usuario['nombre']) ?>?</h3>
            <div class="acciones">
                <button type="submit" name="decision" value="si">Sí, eliminar</button>
                <button type="submit" name="decision" value="no" class="secundario">No</button>
            </div>
        </form>
    <?php endif; ?>
<?php endif; ?>
    <div class="enlaces">
        <a href="listado.php">Listado de usuarios</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
