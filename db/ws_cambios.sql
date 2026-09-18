-- ============================================================
-- Cambios de base de datos para el WebService del courier
-- Ejecutar UNA sola vez en pgAdmin, sobre la base que ya tiene
-- las ocho tablas. No crea tablas nuevas ni borra nada.
-- ============================================================
--
-- Qué hace cada línea:
--
-- 1) Envio.Direccion: el endpoint /envio recibe la dirección de
--    entrega (&direccion=) y hoy Envio no tiene dónde guardarla.
--    Se permite NULL para que los envíos ya registrados a mano
--    sigan siendo válidos.
--
-- 2) Envio.No_orden: /envio recibe el número de orden de la tienda
--    (&orden=) y /status busca por ese número junto con la tienda.
--    Hoy No_orden está en Tienda, que es un dato por tienda y no por
--    envío. Tienda.No_orden se queda como está; el WebService no la usa.
--    Se permite NULL por la misma razón que Direccion.
--
-- 3) uq_envio_orden: una tienda no puede mandar dos veces la misma
--    orden. /envio lo valida antes de insertar y esta restricción lo
--    garantiza aunque lleguen dos llamadas al mismo tiempo. Las filas
--    con No_orden NULL (envíos manuales) no chocan entre sí.

ALTER TABLE Envio ADD COLUMN Direccion varchar(255);
ALTER TABLE Envio ADD COLUMN No_orden varchar(20);
ALTER TABLE Envio ADD CONSTRAINT uq_envio_orden UNIQUE (ID_tienda, No_orden);

-- ------------------------------------------------------------
-- Requisitos de datos (no son cambios de estructura, solo revisar):
--
-- * Debe existir el usuario administrador con ID_usuario = 0:
--   el primer Seguimiento que crea /envio se registra a su nombre.
--
-- * Debe existir en Origen el ID configurado en ws/config.php
--   (ID_ORIGEN), con Cobertura = 'SI', y una fila en Cabeceras
--   por cada destino que se quiera cotizar desde ese origen.
-- ------------------------------------------------------------
