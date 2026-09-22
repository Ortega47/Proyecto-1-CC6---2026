<?php
    session_start();
    if (isset($_SESSION["id"])) {
        header("Location: index.php");
        exit;
    }
    require __DIR__ . "/postsql.php";
    $mensaje = "";
    $usuario = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = trim($_POST["usuario"]);
        $clave = $_POST["contrasena"];

        $query = "SELECT * FROM Usuario WHERE TRIM(Usuario)=$1";
        $result = pg_query_params($conn, $query, [$usuario]);
        $fila = pg_fetch_assoc($result);
        $valida = false;

        if ($fila) {
            $guardada = trim($fila["contraseña"]);
            $valida = password_verify($clave, $guardada);

            if (password_get_info($guardada)["algoName"] == "unknown" && $guardada != "" && hash_equals($guardada, $clave)) {
                $query = "UPDATE Usuario SET Contraseña=$1 WHERE ID_usuario=$2";
                $valida = pg_query_params($conn, $query, [password_hash($clave, PASSWORD_DEFAULT), $fila["id_usuario"]]);
            }
        }

        if (!$valida) {
            $mensaje = "Usuario o contraseña incorrectos.";
        } else {
            session_regenerate_id(true);
            $_SESSION["id"] = $fila["id_usuario"];
            $_SESSION["nombre"] = trim($fila["nombre"]);
            $_SESSION["admin"] = false;
            $_SESSION["cliente"] = true;

            if ($fila["id_usuario"] == 0) {
                $_SESSION["admin"] = true;
                $_SESSION["cliente"] = false;
            }
            header("Location: index.php");
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Courier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="index.php">Winni Express</a>
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
    echo '<h1>Iniciar sesión</h1>';
    if ($mensaje !== '') {
        echo '    <p class="mensaje" role="status">' . $mensaje . '</p>';
    }
    echo '<form method="post">';

    echo '    <label for="usuario">Usuario</label>';
    echo '    <input id="usuario" name="usuario" type="text" required maxlength="50" autocomplete="username" value="' . $usuario . '">';

    echo '    <label for="contrasena">Contraseña</label>';
    echo '    <input id="contrasena" name="contrasena" type="password" required autocomplete="current-password">';

    echo '    <button>Iniciar sesión</button>';
    echo '</form>';
    echo '<p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>';
?>
</main>
<footer>Winni Express · Courier · Proyecto 1 · Ciencias de la Computación VI</footer>
</body>
</html>
