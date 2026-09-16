<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$id = (int) ($_REQUEST['id'] ?? 0);
// TODO(bd): pg_query_params($conn, 'SELECT id_usuario, nombre, usuario, es_admin FROM usuario WHERE id_usuario = $1', [$id])
$usuario = $id > 0 ? buscar('usuario', 'id_usuario', $id) : null;

$mensaje = '';
$clase   = '';
$valores = $usuario;

if ($usuario !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [
        'id_usuario' => $usuario['id_usuario'],
        'nombre'     => trim($_POST['nombre'] ?? ''),
        'usuario'    => trim($_POST['usuario'] ?? ''),
        'es_admin'   => isset($_POST['es_admin']),
    ];
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar  = $_POST['confirmar'] ?? '';
    $existente  = buscar('usuario', 'usuario', $valores['usuario']);

    if ($valores['nombre'] === '' || $valores['usuario'] === '') {
        $mensaje = 'Escriba el nombre y el usuario.';
    } elseif (mb_strlen($valores['usuario']) > 30) {
        $mensaje = 'El usuario tiene como máximo 30 caracteres.';
    } elseif ($existente !== null && (int) $existente['id_usuario'] !== $id) {
        $mensaje = 'Ya existe otro usuario llamado ' . $valores['usuario'] . '.';
    } elseif ($contrasena !== '' && strlen($contrasena) < 8) {
        $mensaje = 'La contraseña nueva debe tener al menos 8 caracteres.';
    } elseif ($contrasena !== $confirmar) {
        $mensaje = 'Las contraseñas no coinciden.';
    } elseif ((int) $_SESSION['id_usuario'] === $id && !$valores['es_admin']) {
        $mensaje = 'No puede quitarse a sí mismo el rol de administrador.';
    } else {
        // TODO(bd): pg_query_params($conn, 'UPDATE usuario SET nombre = $1, usuario = $2, es_admin = $3 WHERE id_usuario = $4',
        //   [$valores['nombre'], $valores['usuario'], $valores['es_admin'] ? 't' : 'f', $id]);
        // Si la contraseña se deja vacía, no cambia:
        // if ($contrasena !== '') {
        //   pg_query_params($conn, 'UPDATE usuario SET contrasena = $1 WHERE id_usuario = $2', [password_hash($contrasena, PASSWORD_DEFAULT), $id]);
        // }
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.'
            . ($contrasena === '' ? ' La contraseña no cambia.' : ' La contraseña se actualizaría.');
        $clase   = 'aviso';
    }
    if ($clase === '') {
        $clase = 'error';
    }
}

$titulo = 'Editar usuario';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Editar usuario</h1>
<?php if ($usuario === null): ?>
    <p class="error">No existe ningún usuario con el id <?= $id ?>.</p>
    <div class="enlaces">
        <a href="listado.php">Listado de usuarios</a>
    </div>
<?php else: ?>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="editar.php?id=<?= $id ?>" autocomplete="off">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required maxlength="80" value="<?= h($valores['nombre']) ?>">

        <label for="usuario">Usuario (máx. 30)</label>
        <input type="text" id="usuario" name="usuario" required maxlength="30" autocapitalize="none" spellcheck="false" value="<?= h($valores['usuario']) ?>">

        <label for="contrasena">Contraseña nueva</label>
        <input type="password" id="contrasena" name="contrasena" minlength="8" autocomplete="new-password">
        <p class="ayuda">Déjela vacía para no cambiarla.</p>

        <label for="confirmar">Confirmar contraseña nueva</label>
        <input type="password" id="confirmar" name="confirmar" minlength="8" autocomplete="new-password">

        <label class="opcion" for="es_admin">
            <input type="checkbox" id="es_admin" name="es_admin" value="1" <?= $valores['es_admin'] ? 'checked' : '' ?>>
            Administrador
        </label>

        <button type="submit">Guardar cambios</button>
    </form>

    <div class="enlaces">
        <a href="listado.php">Listado de usuarios</a>
        <a href="../menu.php">Menú principal</a>
    </div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
