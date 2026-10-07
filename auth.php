<?php
    session_start();

    if (!isset($_SESSION["id"])) {
        $login = "/login.php";
        if (isset($_SERVER["COURIER_BASE"])) {
            $login = $_SERVER["COURIER_BASE"] . "/login.php";
        }
        header("Location: " . $login);
        exit;
    }
?>
