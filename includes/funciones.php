<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/** Escapa cualquier valor antes de imprimirlo en HTML. */
function h(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Arma una ruta absoluta dentro del sitio a partir de BASE_URL. */
function url(string $ruta = ''): string
{
    return BASE_URL . '/' . ltrim($ruta, '/');
}

/** Redirige a una ruta del sitio y termina la ejecución. */
function redirigir(string $ruta): never
{
    header('Location: ' . url($ruta));
    exit;
}

/** 'Y-m-d' (como viene de PostgreSQL) -> '14/09/2026'. */
function fecha_ui(?string $fecha): string
{
    if ($fecha === null || $fecha === '') {
        return '';
    }
    $f = DateTimeImmutable::createFromFormat('Y-m-d', $fecha);
    return $f === false ? $fecha : $f->format('d/m/Y');
}

/** 'H:i' o 'H:i:s' -> '21:33'. */
function hora_ui(?string $hora): string
{
    if ($hora === null || $hora === '') {
        return '';
    }
    return substr($hora, 0, 5);
}

/** Fecha y hora juntas para el historial: '14/09/2026, 08:12'. */
function fecha_hora_ui(?string $fecha, ?string $hora): string
{
    return fecha_ui($fecha) . ', ' . hora_ui($hora);
}

/** 45 -> 'Q45.00'. */
function moneda(float|int|string $monto): string
{
    return 'Q' . number_format((float) $monto, 2, '.', ',');
}

/** Nombre del estado como se muestra en pantalla. */
function etiqueta_estado(int $id_estado): string
{
    return match ($id_estado) {
        1 => 'Orden nueva',
        2 => 'Surtiéndose',
        3 => 'Empacándose',
        4 => 'En ruta',
        5 => 'Entregada',
        default => 'Desconocido',
    };
}

/** Frase para el cliente final según el estado actual. */
function frase_estado_publico(int $id_estado): string
{
    return match ($id_estado) {
        1 => 'Su orden fue recibida',
        2 => 'Su paquete se está surtiendo',
        3 => 'Su paquete se está empacando',
        4 => 'Su paquete va en ruta',
        5 => 'Su paquete fue entregado',
        default => 'Estado desconocido',
    };
}

/** Archivo del panel que atiende cada estado. */
function pantalla_de_estado(int $id_estado): string
{
    return match ($id_estado) {
        1 => 'ordenes-nuevas.php',
        2 => 'surtiendose.php',
        3 => 'empacandose.php',
        4 => 'en-ruta.php',
        5 => 'entregadas.php',
        default => 'envios.php',
    };
}

/**
 * Cómo se pasa de un estado al siguiente: texto del botón y mensajes.
 * `demo` se usa mientras el POST no guarda nada; `final` queda listo para
 * cuando exista la base de datos. Cada par es [singular, plural] para sprintf.
 * Devuelve null cuando el estado no tiene siguiente (entregada).
 */
function transicion_estado(int $id_estado): ?array
{
    return match ($id_estado) {
        1 => [
            'boton'     => 'Empezar a surtir',
            'siguiente' => 2,
            'demo'      => ['se empezaría a surtir %d envío', 'se empezarían a surtir %d envíos'],
            'final'     => ['%d envío empezó a surtirse', '%d envíos empezaron a surtirse'],
        ],
        2 => [
            'boton'     => 'Pasar a empaque',
            'siguiente' => 3,
            'demo'      => ['se pasaría %d envío a Empacándose', 'se pasarían %d envíos a Empacándose'],
            'final'     => ['%d envío pasado a Empacándose', '%d envíos pasados a Empacándose'],
        ],
        3 => [
            'boton'     => 'Despachar a ruta',
            'siguiente' => 4,
            'demo'      => ['se despacharía %d envío a ruta', 'se despacharían %d envíos a ruta'],
            'final'     => ['%d envío despachado a ruta', '%d envíos despachados a ruta'],
        ],
        4 => [
            'boton'     => 'Confirmar entrega',
            'siguiente' => 5,
            'demo'      => ['se confirmaría %d entrega', 'se confirmarían %d entregas'],
            'final'     => ['%d entrega confirmada', '%d entregas confirmadas'],
        ],
        default => null,
    };
}

/** Elige singular o plural y rellena la cantidad. */
function plural(array $par, int $cantidad): string
{
    return sprintf($cantidad === 1 ? $par[0] : $par[1], $cantidad);
}

/** 'Ana López García' -> 'Ana L.' (para el rastreo público). */
function destinatario_abreviado(string $nombre): string
{
    $partes = preg_split('/\s+/', trim($nombre)) ?: [];
    if (count($partes) < 2) {
        return $nombre;
    }
    return $partes[0] . ' ' . mb_substr($partes[1], 0, 1) . '.';
}

/** Cobertura de un destino como texto. */
function texto_cobertura(bool $cobertura): string
{
    return $cobertura ? 'Con cobertura' : 'Sin cobertura';
}
