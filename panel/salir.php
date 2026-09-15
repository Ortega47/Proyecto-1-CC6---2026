<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

// TODO(bd): cerrar la sesión (session_destroy) antes de redirigir.
redirigir('panel/login.php');
