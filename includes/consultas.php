<?php
declare(strict_types=1);

// Capa de acceso a datos. Hoy lee los arreglos de datos_demo.php; cada función
// lleva un TODO(bd) con la consulta que la reemplazará. Las vistas solo llaman
// a estas funciones, nunca a los arreglos directamente.

require_once __DIR__ . '/funciones.php';

/** Carga los datos demo una sola vez. TODO(bd): reemplazar por la conexión PDO. */
function datos_demo(): array
{
    static $datos = null;
    if ($datos === null) {
        $datos = require __DIR__ . '/datos_demo.php';
    }
    return $datos;
}

// ---------------------------------------------------------------- catálogos

/** TODO(bd): SELECT * FROM estado ORDER BY orden */
function estados(): array
{
    return datos_demo()['estado'];
}

/** TODO(bd): SELECT * FROM tienda ORDER BY nombre */
function tiendas(): array
{
    $tiendas = datos_demo()['tienda'];
    usort($tiendas, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
    return $tiendas;
}

/** TODO(bd): SELECT * FROM tienda WHERE id_tienda = :id */
function tienda_por_id(string $id_tienda): ?array
{
    foreach (datos_demo()['tienda'] as $tienda) {
        if ($tienda['id_tienda'] === $id_tienda) {
            return $tienda;
        }
    }
    return null;
}

/** TODO(bd): SELECT * FROM destino ORDER BY id_destino */
function destinos(): array
{
    return datos_demo()['destino'];
}

/** TODO(bd): SELECT * FROM destino WHERE id_destino = :id */
function destino_por_id(string $id_destino): ?array
{
    foreach (datos_demo()['destino'] as $destino) {
        if ($destino['id_destino'] === $id_destino) {
            return $destino;
        }
    }
    return null;
}

/** TODO(bd): SELECT id_usuario, nombre, usuario FROM usuario WHERE id_usuario = :id */
function usuario_por_id(?int $id_usuario): ?array
{
    if ($id_usuario === null) {
        return null;
    }
    foreach (datos_demo()['usuario'] as $usuario) {
        if ($usuario['id_usuario'] === $id_usuario) {
            return $usuario;
        }
    }
    return null;
}

/** Usuario con sesión abierta. TODO(bd): tomarlo de $_SESSION después del login. */
function usuario_actual(): array
{
    return usuario_por_id(1);
}

// ------------------------------------------------------------------- envíos

/** Todos los envíos, del más reciente al más antiguo. */
function envios_todos(): array
{
    $envios = datos_demo()['envio'];
    usort($envios, fn($a, $b) => strcmp($b['fecha'] . $b['hora'], $a['fecha'] . $a['hora']));
    return $envios;
}

/** TODO(bd): SELECT * FROM envio WHERE no_guia = :guia */
function envio_por_guia(string $no_guia): ?array
{
    foreach (datos_demo()['envio'] as $envio) {
        if ($envio['no_guia'] === $no_guia) {
            return $envio;
        }
    }
    return null;
}

/** TODO(bd): SELECT * FROM envio WHERE id_estado = :estado ORDER BY fecha, hora */
function envios_por_estado(int $id_estado): array
{
    $lista = array_filter(datos_demo()['envio'], fn($e) => $e['id_estado'] === $id_estado);
    usort($lista, fn($a, $b) => strcmp($a['fecha'] . $a['hora'], $b['fecha'] . $b['hora']));
    return array_values($lista);
}

/** TODO(bd): SELECT * FROM envio ORDER BY fecha DESC, hora DESC LIMIT :n */
function envios_recientes(int $cantidad): array
{
    return array_slice(envios_todos(), 0, $cantidad);
}

/**
 * Filtros de la pantalla de envíos. Llaves: estado, tienda, destino, q.
 * TODO(bd): SELECT ... FROM envio WHERE (:estado IS NULL OR id_estado = :estado)
 *   AND (:tienda = '' OR id_tienda = :tienda) AND (:destino = '' OR id_destino = :destino)
 *   AND (:q = '' OR no_guia ILIKE :q OR no_orden ILIKE :q) ORDER BY fecha DESC, hora DESC
 */
function envios_filtrados(array $filtros): array
{
    $estado  = (int) ($filtros['estado'] ?? 0);
    $tienda  = (string) ($filtros['tienda'] ?? '');
    $destino = (string) ($filtros['destino'] ?? '');
    $q       = mb_strtolower(trim((string) ($filtros['q'] ?? '')));

    return array_values(array_filter(envios_todos(), function (array $e) use ($estado, $tienda, $destino, $q): bool {
        if ($estado !== 0 && $e['id_estado'] !== $estado) {
            return false;
        }
        if ($tienda !== '' && $e['id_tienda'] !== $tienda) {
            return false;
        }
        if ($destino !== '' && $e['id_destino'] !== $destino) {
            return false;
        }
        if ($q !== '' && !str_contains(mb_strtolower($e['no_guia']), $q) && !str_contains(mb_strtolower($e['no_orden']), $q)) {
            return false;
        }
        return true;
    }));
}

/** TODO(bd): SELECT id_estado, COUNT(*) FROM envio GROUP BY id_estado */
function conteo_por_estado(): array
{
    $conteo = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    foreach (datos_demo()['envio'] as $envio) {
        $conteo[$envio['id_estado']]++;
    }
    return $conteo;
}

/** TODO(bd): SELECT COUNT(*) FROM envio WHERE id_tienda = :id */
function conteo_envios_tienda(string $id_tienda): int
{
    return count(array_filter(datos_demo()['envio'], fn($e) => $e['id_tienda'] === $id_tienda));
}

/** TODO(bd): SELECT COUNT(*) FROM envio WHERE id_destino = :id */
function conteo_envios_destino(string $id_destino): int
{
    return count(array_filter(datos_demo()['envio'], fn($e) => $e['id_destino'] === $id_destino));
}

// ------------------------------------------------------------- seguimiento

/**
 * Historial de una guía en orden cronológico (el más antiguo primero).
 * TODO(bd): SELECT * FROM seguimiento WHERE no_guia = :guia ORDER BY fecha, hora, id_seguimiento
 */
function seguimiento_de(string $no_guia): array
{
    $lista = array_filter(datos_demo()['seguimiento'], fn($s) => $s['no_guia'] === $no_guia);
    usort($lista, fn($a, $b) => strcmp($a['fecha'] . $a['hora'] . $a['id_seguimiento'], $b['fecha'] . $b['hora'] . $b['id_seguimiento']));
    return array_values($lista);
}

/** Fecha y hora en que cada estado se alcanzó: [id_estado => ['fecha' => ..., 'hora' => ...]]. */
function fechas_por_estado(string $no_guia): array
{
    $fechas = [];
    foreach (seguimiento_de($no_guia) as $registro) {
        $fechas[$registro['id_estado']] = ['fecha' => $registro['fecha'], 'hora' => $registro['hora']];
    }
    return $fechas;
}
