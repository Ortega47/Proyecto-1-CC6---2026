<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$mensaje = '';
$clase   = '';
$valores = ['nombre' => '', 'usuario' => '', 'es_admin' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = [
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'usuario'  => trim($_POST['usuario'] ?? ''),
        'es_admin' => isset($_POST['es_admin']),
    ];
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar  = $_POST['confirmar'] ?? '';

    if ($valores['nombre'] === '' || $valores['usuario'] === '') {
        $mensaje = 'Escriba el nombre y el usuario.';
    } elseif (mb_strlen($valores['usuario']) > 30) {
        $mensaje = 'El usuario tiene como máximo 30 caracteres.';
    } elseif (buscar('usuario', 'usuario', $valores['usuario']) !== null) {
        $mensaje = 'Ya existe un usuario llamado ' . $valores['usuario'] . '.';
    } elseif (strlen($contrasena) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($contrasena !== $confirmar) {
        $mensaje = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        // TODO(bd): pg_query_params($conn, 'INSERT INTO usuario (nombre, usuario, contrasena, es_admin) VALUES ($1, $2, $3, $4)',
        //   [$valores['nombre'], $valores['usuario'], $hash, $valores['es_admin'] ? 't' : 'f']);
        $mensaje = 'Vista previa: esto se guardará cuando conectemos la base de datos.';
        $clase   = 'aviso';
    }
    if ($clase === '') {
        $clase = 'error';
    }
}

$titulo = 'Agregar usuario';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido angosto">
    <h1>Agregar usuario</h1>
    <?php if ($mensaje !== ''): ?>
        <p class="<?= $clase ?>"><?= h($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="agregar.php" autocomplete="off">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required maxlength="80" value="<?= h($valores['nombre']) ?>">

        <label for="usuario">Usuario (máx. 30)</label>
        <input type="text" id="usuario" name="usuario" required maxlength="30" autocapitalize="none" spellcheck="false" value="<?= h($valores['usuario']) ?>">

        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required minlength="8" autocomplete="new-password">

        <label for="confirmar">Confirmar contraseña</label>
        <input type="password" id="confirmar" name="confirmar" required minlength="8" autocomplete="new-password">

        <label class="opcion" for="es_admin">
            <input type="checkbox" id="es_admin" name="es_admin" value="1" <?= $valores['es_admin'] ? 'checked' : '' ?>>
            Administrador
        </label>
        <p class="ayuda">Los administradores mantienen destinos, tiendas y usuarios, y ven el resumen.</p>

        <button type="submit">Guardar usuario</button>
    </form>

    <div class="enlaces">
        <a href="listado.php">Listado de usuarios</a>
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
