<?php
$raiz = '../';
require __DIR__.'/../auth.php';
require_once __DIR__.'/../postsql.php';
if (!$_SESSION['admin']) { header('Location: ../index.php'); exit; }
$mensaje = '';
$id = $_GET['id'] ?? '';
if (!is_string($id) || (filter_var($id, FILTER_VALIDATE_INT) === false || $id < 0 || $id > 2147483647)) { http_response_code(404); exit('ID no válido.'); }
$result = pg_query_params($conn,'SELECT ID_usuario,Nombre,Usuario FROM Usuario WHERE ID_usuario=$1',[$id]);
$fila = pg_fetch_assoc($result);
if (!$fila) { http_response_code(404); exit('Usuario no encontrado.'); }
$nombre=trim($fila['nombre']);
$usuario=trim($fila['usuario']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_correcto = is_string($_POST['token'] ?? null) && hash_equals($_SESSION['token'], $_POST['token']);
    $nombre=(is_string($_POST['nombre'] ?? null) ? trim($_POST['nombre']) : '');
    $usuario=(is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '');
    $clave=is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '';
    $confirmar=is_string($_POST['confirmar'] ?? null) ? $_POST['confirmar'] : '';
    if (!$formulario_correcto) {
        $mensaje='Recarga el formulario.';
    }
    elseif (
        (filter_var($id, FILTER_VALIDATE_INT) === false ||
        $id < 0 ||
        $id > 2147483647) ||
        ($nombre === '' ||
        mb_strlen($nombre) > 50) ||
        ($usuario === '' ||
        mb_strlen($usuario) > 50)
    ) {
        $mensaje='Revisa el ID, el nombre y el usuario.';
    }
    elseif ($clave !== '' && (strlen($clave)<8 || strlen($clave)>72)) {
        $mensaje='La contraseña debe tener entre 8 y 72 bytes.';
    }
    elseif ($clave !== $confirmar) {
        $mensaje='Las contraseñas no coinciden.';
    }
    else {
        $repetido=pg_query_params($conn,'SELECT ID_usuario FROM Usuario WHERE TRIM(Usuario)=$1 AND ID_usuario<>$2',[$usuario,$id]);
        if (pg_num_rows($repetido)>0) {
            $mensaje='Ese nombre de usuario ya existe.';
        }
        else {
            if ($clave === '') {
                $result=@pg_query_params($conn,'UPDATE Usuario SET Nombre=$1,Usuario=$2 WHERE ID_usuario=$3',[$nombre,$usuario,$id]);
            } else {
                $result=@pg_query_params($conn,'UPDATE Usuario SET Nombre=$1,Usuario=$2,Contraseña=$3 WHERE ID_usuario=$4',[$nombre,$usuario,password_hash($clave,PASSWORD_DEFAULT),$id]);
            }
            if ($result) { $_SESSION['mensaje']='Usuario guardado.'; header('Location: listado.php'); exit; }
            $mensaje='No se pudo guardar. Comprueba que el ID sea único y que Contraseña admita 255 caracteres.';
        }
    }
}
$titulo='Editar usuario';
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


<form method="post" autocomplete="off">
    <input type="hidden" name="token" value="<?= htmlspecialchars(trim((string) ($_SESSION['token']))) ?>">
    <label for="id_usuario">ID del usuario</label><input type="number" id="id_usuario" name="id_usuario" min="0" max="2147483647" value="<?= htmlspecialchars(trim((string) ($id))) ?>" readonly required>
    <label for="nombre">Nombre</label><input type="text" id="nombre" name="nombre" maxlength="50" value="<?= htmlspecialchars(trim((string) ($nombre))) ?>" required>
    <label for="usuario">Usuario</label><input type="text" id="usuario" name="usuario" maxlength="50" value="<?= htmlspecialchars(trim((string) ($usuario))) ?>" required>
    <label for="contrasena">Contraseña</label><input type="password" id="contrasena" name="contrasena" minlength="8" maxlength="72" autocomplete="new-password" >
    <p class="ayuda">Déjala vacía para conservar la contraseña actual.</p>
    <label for="confirmar">Confirmar contraseña</label><input type="password" id="confirmar" name="confirmar" autocomplete="new-password">
    <button>Guardar</button>
</form>
<div class="enlaces"><a href="listado.php">Listado de usuarios</a><a href="../index.php">Menú principal</a></div>
<?php ?>
</main>
<footer>Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>

<?php ?>
