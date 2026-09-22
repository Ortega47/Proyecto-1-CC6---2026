<?php
    session_start();
    require __DIR__ . "/postsql.php";
    if (isset($_SESSION["id"])) {
        header("Location: index.php");
        exit;
    }
    $mensaje = "";
    $nombre = "";
    $usuario = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = trim($_POST["nombre"]);
        $usuario = trim($_POST["usuario"]);
        $clave = $_POST["contrasena"];

        if ($nombre == "" || $usuario == "" || strlen($clave) < 8 || strlen($clave) > 72 || str_contains($clave, "\0")) {
            $mensaje = "Completa el nombre, usuario y una contraseña de 8 a 72 caracteres.";
        } else {
            pg_query($conn, "BEGIN");
            pg_query($conn, "LOCK TABLE Usuario IN EXCLUSIVE MODE");
            $query = "SELECT * FROM Usuario WHERE TRIM(Usuario)=$1";
            $result = pg_query_params($conn, $query, [$usuario]);

            if (pg_num_rows($result) > 0) {
                pg_query($conn, "ROLLBACK");
                $mensaje = "Ese nombre de usuario ya existe.";
            } else {
                $query = "INSERT INTO Usuario (ID_usuario, Nombre, Usuario, Contraseña)
                          SELECT GREATEST(COALESCE(MAX(ID_usuario),0),0)+1,$1,$2,$3 FROM Usuario
                          RETURNING ID_usuario";
                $result = @pg_query_params($conn, $query, [$nombre, $usuario, password_hash($clave, PASSWORD_DEFAULT)]);

                if (!$result) {
                    pg_query($conn, "ROLLBACK");
                    $mensaje = "No se pudo crear la cuenta.";
                } else {
                    pg_query($conn, "COMMIT");
                    $fila = pg_fetch_assoc($result);
                    session_regenerate_id(true);
                    $_SESSION["id"] = $fila["id_usuario"];
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["admin"] = false;
                    $_SESSION["cliente"] = true;
                    header("Location: index.php");
                    exit;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - Courier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="index.php">🐳 Winni Express</a>
    <nav><a href="rastreo.php">Rastrear paquete</a>
    <?php
    if (isset($_SESSION['id'])) {
        echo '        <a href="index.php">Menú</a>';
        echo '        <a href="logout.php">Cerrar sesión</a>';
    } else {
        echo '<a href="login.php">Iniciar sesión</a>';
    }
    echo '    </nav>';
    echo '</header>';
    echo '<main class="formulario">';
    echo '<h1>Crear cuenta</h1>';
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<p>Regístrate para acceder al rastreo de tus paquetes con su número de guía.</p>';
    echo '<form method="post">';

    echo '    <label for="nombre">Nombre</label>';
    echo '    <input id="nombre" name="nombre" type="text" maxlength="50" value="' . $nombre . '" required>';

    echo '    <label for="usuario">Usuario</label>';
    echo '    <input id="usuario" name="usuario" type="text" maxlength="50" value="' . $usuario . '" required>';

    echo '    <label for="contrasena">Contraseña</label>';
    echo '    <input id="contrasena" name="contrasena" type="password" minlength="8" maxlength="72" required autocomplete="new-password">';

    echo '    <button>Crear cuenta</button>';
    echo '</form>';
    echo '<p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>';
?>
</main>
<footer>🐳 Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>