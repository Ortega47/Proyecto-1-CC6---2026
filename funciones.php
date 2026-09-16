<?php
// Funciones compartidas por todas las páginas.

date_default_timezone_set('America/Guatemala');

// Escapa un valor antes de imprimirlo en HTML.
function h($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// 'Y-m-d' (PostgreSQL) -> '14/09/2026'
function fecha($fecha) {
    if ($fecha === null || $fecha === '') {
        return '';
    }
    return date('d/m/Y', strtotime($fecha));
}

// 'H:i:s' -> '21:33'
function hora($hora) {
    if ($hora === null || $hora === '') {
        return '';
    }
    return substr($hora, 0, 5);
}

// 45 -> 'Q45.00'
function moneda($monto) {
    return 'Q' . number_format((float) $monto, 2, '.', ',');
}

// Nombre del estado como se muestra en pantalla.
function nombre_estado($id_estado) {
    $nombres = [1 => 'Orden nueva', 2 => 'Surtiéndose', 3 => 'Empacándose', 4 => 'En ruta', 5 => 'Entregada'];
    return $nombres[(int) $id_estado] ?? 'Desconocido';
}

// Archivo de la pantalla de cada estado (carpeta estados/).
function archivo_estado($id_estado) {
    $archivos = [1 => 'orden_nueva.php', 2 => 'surtiendose.php', 3 => 'empacandose.php', 4 => 'en_ruta.php', 5 => 'entregadas.php'];
    return $archivos[(int) $id_estado] ?? 'orden_nueva.php';
}

// Insignia con el color del estado.
function insignia_estado($id_estado) {
    return '<span class="insignia e' . (int) $id_estado . '">' . h(nombre_estado($id_estado)) . '</span>';
}

// 'Ana López García' -> 'Ana L.' (rastreo público)
function nombre_abreviado($nombre) {
    $partes = preg_split('/\s+/', trim($nombre));
    if (count($partes) < 2) {
        return $nombre;
    }
    return $partes[0] . ' ' . mb_substr($partes[1], 0, 1) . '.';
}

// Lee y borra el aviso guardado en la sesión (se usa después de un redirect).
function aviso() {
    if (!isset($_SESSION['aviso'])) {
        return '';
    }
    $texto = $_SESSION['aviso'];
    unset($_SESSION['aviso']);
    return $texto;
}

// ---- Acceso a los datos demo. Cada una se reemplaza por pg_query_params. ----

// Primera fila de $demo[$tabla] cuya $columna vale $valor, o null.
// TODO(bd): SELECT * FROM <tabla> WHERE <columna> = $1
function buscar($tabla, $columna, $valor) {
    global $demo;
    foreach ($demo[$tabla] as $fila) {
        if ((string) $fila[$columna] === (string) $valor) {
            return $fila;
        }
    }
    return null;
}

// Cuántos envíos tienen $columna = $valor (id_tienda, id_destino o id_estado).
// TODO(bd): SELECT COUNT(*) FROM envio WHERE <columna> = $1
function contar_envios($columna, $valor) {
    global $demo;
    $total = 0;
    foreach ($demo['envio'] as $envio) {
        if ((string) $envio[$columna] === (string) $valor) {
            $total++;
        }
    }
    return $total;
}

// [id_estado => cantidad] para los cinco estados.
// TODO(bd): SELECT id_estado, COUNT(*) FROM envio GROUP BY id_estado
function conteo_por_estado() {
    global $demo;
    $conteo = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    foreach ($demo['envio'] as $envio) {
        $conteo[$envio['id_estado']]++;
    }
    return $conteo;
}

// Historial de una guía, del más antiguo al más reciente.
// TODO(bd): SELECT * FROM seguimiento WHERE no_guia = $1 ORDER BY fecha, hora, id_seguimiento
function seguimiento_de($no_guia) {
    global $demo;
    $lista = [];
    foreach ($demo['seguimiento'] as $registro) {
        if ($registro['no_guia'] === $no_guia) {
            $lista[] = $registro;
        }
    }
    usort($lista, function ($a, $b) {
        return strcmp($a['fecha'] . $a['hora'] . $a['id_seguimiento'], $b['fecha'] . $b['hora'] . $b['id_seguimiento']);
    });
    return $lista;
}
