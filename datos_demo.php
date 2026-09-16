<?php
// Datos de demostración. Las llaves de cada fila son los nombres de columna
// exactos de db/courier_bd_simple.sql. Este archivo se reemplaza por
// postsql.php (conexión a PostgreSQL) en el commit de base de datos.

$demo = [

    'tienda' => [
        ['id_tienda' => 'TV-KAQCHIKEL-01', 'nombre' => 'Tienda Kaqchikel', 'host' => 'kaqchikel.tienda.gt'],
        ['id_tienda' => 'TV-ELECTRONOVA1', 'nombre' => 'ElectroNova',      'host' => 'electronova.com.gt'],
        ['id_tienda' => 'TV-LIBRERIALUNA', 'nombre' => 'Librería Luna',    'host' => 'libreria-luna.gt'],
    ],

    'destino' => [
        ['id_destino' => 'GT001', 'ciudad' => 'Ciudad de Guatemala', 'cobertura' => true,  'costo_envio' => 35.00, 'costo_manejo' => 10.00],
        ['id_destino' => 'GT002', 'ciudad' => 'Mixco',               'cobertura' => true,  'costo_envio' => 40.00, 'costo_manejo' => 10.00],
        ['id_destino' => 'GT003', 'ciudad' => 'Antigua Guatemala',   'cobertura' => true,  'costo_envio' => 55.00, 'costo_manejo' => 15.00],
        ['id_destino' => 'GT004', 'ciudad' => 'Quetzaltenango',      'cobertura' => true,  'costo_envio' => 85.00, 'costo_manejo' => 15.00],
        ['id_destino' => 'GT005', 'ciudad' => 'Flores',              'cobertura' => false, 'costo_envio' =>  0.00, 'costo_manejo' =>  0.00],
    ],

    'estado' => [
        ['id_estado' => 1, 'nombre' => 'ORDEN NUEVA', 'orden' => 1],
        ['id_estado' => 2, 'nombre' => 'SURTIENDOSE', 'orden' => 2],
        ['id_estado' => 3, 'nombre' => 'EMPACANDOSE', 'orden' => 3],
        ['id_estado' => 4, 'nombre' => 'EN RUTA',     'orden' => 4],
        ['id_estado' => 5, 'nombre' => 'ENTREGADA',   'orden' => 5],
    ],

    // Contraseñas guardadas con password_hash(). Demo: admin / admin123,
    // operador / operador123, operador2 / operador123.
    // es_admin no existe todavía en db/courier_bd_simple.sql (cambio propuesto).
    'usuario' => [
        ['id_usuario' => 1, 'nombre' => 'Ana Pérez',     'usuario' => 'admin',     'contrasena' => '$2y$10$QQIr15tb5MRlzc93f1mto.dV/ZKd/e8IV6DjUo9GhX8bQ08nCCt06', 'es_admin' => true],
        ['id_usuario' => 2, 'nombre' => 'Luis Morales',  'usuario' => 'operador',  'contrasena' => '$2y$10$l09wHU7lqSs03ETFAokrseGPTbokHz1TQCRsnYaNVNgioys9kwCcy', 'es_admin' => false],
        ['id_usuario' => 3, 'nombre' => 'Carlos Juárez', 'usuario' => 'operador2', 'contrasena' => '$2y$10$c7jZP4ZVSxkw5bhBkYM5Xe3LTN2Ke1mV0ZONOzyIHgqm17ChCAPDu', 'es_admin' => false],
    ],

    // no_guia: 15 caracteres. El formato GUA-aamm-nnnnnn es provisional hasta
    // acordarlo con los grupos de Tienda Virtual.
    'envio' => [
        ['no_guia' => 'GUA-2609-000001', 'id_tienda' => 'TV-KAQCHIKEL-01', 'no_orden' => 'K-1041',  'id_destino' => 'GT001', 'id_estado' => 5, 'destinatario' => 'Ana López',        'direccion' => '12 calle 4-51, zona 10, Ciudad de Guatemala',                    'fecha' => '2026-09-02', 'hora' => '09:14', 'costo_total' => 45.00],
        ['no_guia' => 'GUA-2609-000002', 'id_tienda' => 'TV-ELECTRONOVA1', 'no_orden' => 'E-88120', 'id_destino' => 'GT003', 'id_estado' => 5, 'destinatario' => 'Carlos Méndez',    'direccion' => '3a avenida norte 12, Antigua Guatemala, Sacatepéquez',           'fecha' => '2026-09-03', 'hora' => '10:02', 'costo_total' => 70.00],
        ['no_guia' => 'GUA-2609-000003', 'id_tienda' => 'TV-LIBRERIALUNA', 'no_orden' => 'L-5507',  'id_destino' => 'GT002', 'id_estado' => 5, 'destinatario' => 'María Chávez',     'direccion' => '5a calle 7-20, Colonia El Naranjo, zona 4 de Mixco',             'fecha' => '2026-09-04', 'hora' => '15:30', 'costo_total' => 50.00],
        ['no_guia' => 'GUA-2609-000004', 'id_tienda' => 'TV-KAQCHIKEL-01', 'no_orden' => 'K-1058',  'id_destino' => 'GT003', 'id_estado' => 4, 'destinatario' => 'Lucía Ramírez',    'direccion' => '4a calle poniente 8, Antigua Guatemala, Sacatepéquez',           'fecha' => '2026-09-11', 'hora' => '16:45', 'costo_total' => 70.00],
        ['no_guia' => 'GUA-2609-000005', 'id_tienda' => 'TV-ELECTRONOVA1', 'no_orden' => 'E-88134', 'id_destino' => 'GT004', 'id_estado' => 4, 'destinatario' => 'José Tzul',        'direccion' => '14 avenida 3-42, zona 3, Quetzaltenango',                        'fecha' => '2026-09-11', 'hora' => '11:20', 'costo_total' => 100.00],
        ['no_guia' => 'GUA-2609-000006', 'id_tienda' => 'TV-LIBRERIALUNA', 'no_orden' => 'L-5519',  'id_destino' => 'GT001', 'id_estado' => 3, 'destinatario' => 'Sofía Herrera',    'direccion' => '7a avenida 10-25, zona 1, Ciudad de Guatemala',                   'fecha' => '2026-09-12', 'hora' => '09:05', 'costo_total' => 45.00],
        ['no_guia' => 'GUA-2609-000007', 'id_tienda' => 'TV-KAQCHIKEL-01', 'no_orden' => 'K-1063',  'id_destino' => 'GT002', 'id_estado' => 3, 'destinatario' => 'Diego Castillo',   'direccion' => 'Boulevard El Naranjo 12-30, zona 4 de Mixco',                    'fecha' => '2026-09-12', 'hora' => '13:40', 'costo_total' => 50.00],
        ['no_guia' => 'GUA-2609-000008', 'id_tienda' => 'TV-ELECTRONOVA1', 'no_orden' => 'E-88141', 'id_destino' => 'GT001', 'id_estado' => 3, 'destinatario' => 'Elena Sandoval',   'direccion' => '20 calle 9-18, Colonia Mariscal, zona 11, Ciudad de Guatemala',  'fecha' => '2026-09-13', 'hora' => '10:50', 'costo_total' => 45.00],
        ['no_guia' => 'GUA-2609-000009', 'id_tienda' => 'TV-LIBRERIALUNA', 'no_orden' => 'L-5524',  'id_destino' => 'GT003', 'id_estado' => 2, 'destinatario' => 'Marcos Pop',       'direccion' => 'Calle del Arco 5, Antigua Guatemala, Sacatepéquez',              'fecha' => '2026-09-13', 'hora' => '14:25', 'costo_total' => 70.00],
        ['no_guia' => 'GUA-2609-000010', 'id_tienda' => 'TV-KAQCHIKEL-01', 'no_orden' => 'K-1070',  'id_destino' => 'GT004', 'id_estado' => 2, 'destinatario' => 'Rosa Cojulún',     'direccion' => '12 avenida 5-15, zona 1, Quetzaltenango',                        'fecha' => '2026-09-13', 'hora' => '17:10', 'costo_total' => 100.00],
        ['no_guia' => 'GUA-2609-000011', 'id_tienda' => 'TV-ELECTRONOVA1', 'no_orden' => 'E-88150', 'id_destino' => 'GT002', 'id_estado' => 1, 'destinatario' => 'Jorge Estrada',    'direccion' => '2a calle 3-40, Colonia El Milagro, zona 6 de Mixco',             'fecha' => '2026-09-14', 'hora' => '08:02', 'costo_total' => 50.00],
        ['no_guia' => 'GUA-2609-000012', 'id_tienda' => 'TV-LIBRERIALUNA', 'no_orden' => 'L-5531',  'id_destino' => 'GT001', 'id_estado' => 1, 'destinatario' => 'Andrea Velásquez', 'direccion' => '6a avenida 12-60, zona 9, Ciudad de Guatemala',                   'fecha' => '2026-09-14', 'hora' => '10:37', 'costo_total' => 45.00],
        ['no_guia' => 'GUA-2609-000013', 'id_tienda' => 'TV-KAQCHIKEL-01', 'no_orden' => 'K-1074',  'id_destino' => 'GT003', 'id_estado' => 1, 'destinatario' => 'Rodrigo Batz',     'direccion' => '6a avenida norte 32, Antigua Guatemala, Sacatepéquez',           'fecha' => '2026-09-14', 'hora' => '11:58', 'costo_total' => 70.00],
    ],

    // El registro del estado 1 lo crea el WebService cuando la tienda pide el
    // envío, por eso va con id_usuario = null.
    'seguimiento' => [
        // GUA-2609-000001 · entregada
        ['id_seguimiento' => 1,  'no_guia' => 'GUA-2609-000001', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-02', 'hora' => '09:14', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 2,  'no_guia' => 'GUA-2609-000001', 'id_estado' => 2, 'id_usuario' => 1,    'fecha' => '2026-09-02', 'hora' => '11:30', 'observacion' => 'Artículos reunidos en bodega'],
        ['id_seguimiento' => 3,  'no_guia' => 'GUA-2609-000001', 'id_estado' => 3, 'id_usuario' => 2,    'fecha' => '2026-09-02', 'hora' => '15:05', 'observacion' => 'Caja cerrada y guía pegada'],
        ['id_seguimiento' => 4,  'no_guia' => 'GUA-2609-000001', 'id_estado' => 4, 'id_usuario' => 2,    'fecha' => '2026-09-03', 'hora' => '07:50', 'observacion' => 'Salió de bodega en la ruta de la capital'],
        ['id_seguimiento' => 5,  'no_guia' => 'GUA-2609-000001', 'id_estado' => 5, 'id_usuario' => 2,    'fecha' => '2026-09-03', 'hora' => '12:20', 'observacion' => 'Entregado al destinatario'],
        // GUA-2609-000002 · entregada
        ['id_seguimiento' => 6,  'no_guia' => 'GUA-2609-000002', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-03', 'hora' => '10:02', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 7,  'no_guia' => 'GUA-2609-000002', 'id_estado' => 2, 'id_usuario' => 1,    'fecha' => '2026-09-03', 'hora' => '14:10', 'observacion' => null],
        ['id_seguimiento' => 8,  'no_guia' => 'GUA-2609-000002', 'id_estado' => 3, 'id_usuario' => 1,    'fecha' => '2026-09-04', 'hora' => '09:25', 'observacion' => 'Empacado en caja mediana con protección'],
        ['id_seguimiento' => 9,  'no_guia' => 'GUA-2609-000002', 'id_estado' => 4, 'id_usuario' => 2,    'fecha' => '2026-09-04', 'hora' => '13:40', 'observacion' => 'En camino a Antigua Guatemala'],
        ['id_seguimiento' => 10, 'no_guia' => 'GUA-2609-000002', 'id_estado' => 5, 'id_usuario' => 2,    'fecha' => '2026-09-05', 'hora' => '10:15', 'observacion' => 'Recibió un familiar en la dirección'],
        // GUA-2609-000003 · entregada
        ['id_seguimiento' => 11, 'no_guia' => 'GUA-2609-000003', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-04', 'hora' => '15:30', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 12, 'no_guia' => 'GUA-2609-000003', 'id_estado' => 2, 'id_usuario' => 2,    'fecha' => '2026-09-05', 'hora' => '08:05', 'observacion' => 'Artículos reunidos en bodega'],
        ['id_seguimiento' => 13, 'no_guia' => 'GUA-2609-000003', 'id_estado' => 3, 'id_usuario' => 2,    'fecha' => '2026-09-05', 'hora' => '10:40', 'observacion' => null],
        ['id_seguimiento' => 14, 'no_guia' => 'GUA-2609-000003', 'id_estado' => 4, 'id_usuario' => 1,    'fecha' => '2026-09-05', 'hora' => '14:00', 'observacion' => 'Salió en la ruta de Mixco'],
        ['id_seguimiento' => 15, 'no_guia' => 'GUA-2609-000003', 'id_estado' => 5, 'id_usuario' => 1,    'fecha' => '2026-09-05', 'hora' => '17:35', 'observacion' => 'Entregado en recepción del edificio'],
        // GUA-2609-000004 · en ruta
        ['id_seguimiento' => 16, 'no_guia' => 'GUA-2609-000004', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-11', 'hora' => '16:45', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 17, 'no_guia' => 'GUA-2609-000004', 'id_estado' => 2, 'id_usuario' => 1,    'fecha' => '2026-09-12', 'hora' => '08:20', 'observacion' => null],
        ['id_seguimiento' => 18, 'no_guia' => 'GUA-2609-000004', 'id_estado' => 3, 'id_usuario' => 2,    'fecha' => '2026-09-13', 'hora' => '17:40', 'observacion' => 'Caja cerrada y guía pegada'],
        ['id_seguimiento' => 19, 'no_guia' => 'GUA-2609-000004', 'id_estado' => 4, 'id_usuario' => 2,    'fecha' => '2026-09-14', 'hora' => '08:12', 'observacion' => 'Salió de bodega con el piloto de la ruta de Antigua'],
        // GUA-2609-000005 · en ruta
        ['id_seguimiento' => 20, 'no_guia' => 'GUA-2609-000005', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-11', 'hora' => '11:20', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 21, 'no_guia' => 'GUA-2609-000005', 'id_estado' => 2, 'id_usuario' => 2,    'fecha' => '2026-09-11', 'hora' => '15:00', 'observacion' => 'Artículos reunidos en bodega'],
        ['id_seguimiento' => 22, 'no_guia' => 'GUA-2609-000005', 'id_estado' => 3, 'id_usuario' => 1,    'fecha' => '2026-09-12', 'hora' => '09:10', 'observacion' => 'Empacado en caja grande'],
        ['id_seguimiento' => 23, 'no_guia' => 'GUA-2609-000005', 'id_estado' => 4, 'id_usuario' => 1,    'fecha' => '2026-09-13', 'hora' => '06:30', 'observacion' => 'Salió en la ruta de occidente'],
        // GUA-2609-000006 · empacándose
        ['id_seguimiento' => 24, 'no_guia' => 'GUA-2609-000006', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-12', 'hora' => '09:05', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 25, 'no_guia' => 'GUA-2609-000006', 'id_estado' => 2, 'id_usuario' => 1,    'fecha' => '2026-09-12', 'hora' => '11:15', 'observacion' => null],
        ['id_seguimiento' => 26, 'no_guia' => 'GUA-2609-000006', 'id_estado' => 3, 'id_usuario' => 1,    'fecha' => '2026-09-13', 'hora' => '15:20', 'observacion' => 'Caja cerrada y guía pegada'],
        // GUA-2609-000007 · empacándose
        ['id_seguimiento' => 27, 'no_guia' => 'GUA-2609-000007', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-12', 'hora' => '13:40', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 28, 'no_guia' => 'GUA-2609-000007', 'id_estado' => 2, 'id_usuario' => 2,    'fecha' => '2026-09-13', 'hora' => '08:30', 'observacion' => 'Artículos reunidos en bodega'],
        ['id_seguimiento' => 29, 'no_guia' => 'GUA-2609-000007', 'id_estado' => 3, 'id_usuario' => 2,    'fecha' => '2026-09-13', 'hora' => '16:05', 'observacion' => null],
        // GUA-2609-000008 · empacándose
        ['id_seguimiento' => 30, 'no_guia' => 'GUA-2609-000008', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-13', 'hora' => '10:50', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 31, 'no_guia' => 'GUA-2609-000008', 'id_estado' => 2, 'id_usuario' => 1,    'fecha' => '2026-09-13', 'hora' => '12:00', 'observacion' => null],
        ['id_seguimiento' => 32, 'no_guia' => 'GUA-2609-000008', 'id_estado' => 3, 'id_usuario' => 1,    'fecha' => '2026-09-14', 'hora' => '09:30', 'observacion' => 'Empacado en caja pequeña'],
        // GUA-2609-000009 · surtiéndose
        ['id_seguimiento' => 33, 'no_guia' => 'GUA-2609-000009', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-13', 'hora' => '14:25', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 34, 'no_guia' => 'GUA-2609-000009', 'id_estado' => 2, 'id_usuario' => 2,    'fecha' => '2026-09-14', 'hora' => '08:45', 'observacion' => 'Se están reuniendo los artículos de la orden'],
        // GUA-2609-000010 · surtiéndose
        ['id_seguimiento' => 35, 'no_guia' => 'GUA-2609-000010', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-13', 'hora' => '17:10', 'observacion' => 'Orden recibida de la tienda'],
        ['id_seguimiento' => 36, 'no_guia' => 'GUA-2609-000010', 'id_estado' => 2, 'id_usuario' => 1,    'fecha' => '2026-09-14', 'hora' => '09:05', 'observacion' => null],
        // GUA-2609-000011 · orden nueva
        ['id_seguimiento' => 37, 'no_guia' => 'GUA-2609-000011', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-14', 'hora' => '08:02', 'observacion' => 'Orden recibida de la tienda'],
        // GUA-2609-000012 · orden nueva
        ['id_seguimiento' => 38, 'no_guia' => 'GUA-2609-000012', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-14', 'hora' => '10:37', 'observacion' => 'Orden recibida de la tienda'],
        // GUA-2609-000013 · orden nueva
        ['id_seguimiento' => 39, 'no_guia' => 'GUA-2609-000013', 'id_estado' => 1, 'id_usuario' => null, 'fecha' => '2026-09-14', 'hora' => '11:58', 'observacion' => 'Orden recibida de la tienda'],
    ],
];
