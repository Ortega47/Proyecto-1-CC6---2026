-- ====================================================
-- PROYECTO 1 - Ciencias de la Computacion VI
-- Sitio: COURIER   |   PostgreSQL
-- ====================================================

DROP TABLE IF EXISTS seguimiento, envio, usuario, estado, destino, tienda CASCADE;

-- Tiendas virtuales afiliadas (parametro &tienda= del WebService)
CREATE TABLE tienda (
    id_tienda CHAR(15)     PRIMARY KEY,
    nombre    VARCHAR(80)  NOT NULL,
    host      VARCHAR(120) NOT NULL
);

-- Destinos cubiertos y su costo de envio y manejo
CREATE TABLE destino (
    id_destino   CHAR(5)       PRIMARY KEY,
    ciudad       VARCHAR(80)   NOT NULL,
    cobertura    BOOLEAN       NOT NULL DEFAULT TRUE,
    costo_envio  NUMERIC(10,2) NOT NULL DEFAULT 0,
    costo_manejo NUMERIC(10,2) NOT NULL DEFAULT 0
);

-- Catalogo de los 5 estados del package tracking
CREATE TABLE estado (
    id_estado SMALLINT    PRIMARY KEY,
    nombre    VARCHAR(20) NOT NULL,
    orden     SMALLINT    NOT NULL
);

-- Usuarios que actualizan los estados desde las pantallas
CREATE TABLE usuario (
    id_usuario  SERIAL       PRIMARY KEY,
    nombre      VARCHAR(80)  NOT NULL,
    usuario     VARCHAR(30)  NOT NULL UNIQUE,
    contrasena  VARCHAR(120) NOT NULL
);

-- Envios contratados por las tiendas
CREATE TABLE envio (
    no_guia      CHAR(15)      PRIMARY KEY,
    id_tienda    CHAR(15)      NOT NULL REFERENCES tienda(id_tienda),
    no_orden     VARCHAR(20)   NOT NULL,
    id_destino   CHAR(5)       NOT NULL REFERENCES destino(id_destino),
    id_estado    SMALLINT      NOT NULL REFERENCES estado(id_estado),
    destinatario VARCHAR(100)  NOT NULL,
    direccion    VARCHAR(200)  NOT NULL,
    fecha        DATE          NOT NULL DEFAULT CURRENT_DATE,
    hora         TIME          NOT NULL DEFAULT LOCALTIME,
    costo_total  NUMERIC(10,2) NOT NULL,
    CONSTRAINT uq_envio_orden UNIQUE (id_tienda, no_orden)
);

-- Historial de estados (entidad debil de envio)
CREATE TABLE seguimiento (
    id_seguimiento BIGSERIAL PRIMARY KEY,
    no_guia        CHAR(15)  NOT NULL REFERENCES envio(no_guia) ON DELETE CASCADE,
    id_estado      SMALLINT  NOT NULL REFERENCES estado(id_estado),
    id_usuario     INTEGER   REFERENCES usuario(id_usuario),
    fecha          DATE      NOT NULL DEFAULT CURRENT_DATE,
    hora           TIME      NOT NULL DEFAULT LOCALTIME,
    observacion    VARCHAR(200)
);

CREATE INDEX idx_envio_estado ON envio(id_estado);
CREATE INDEX idx_seg_guia     ON seguimiento(no_guia, fecha, hora);

-- ---------------- Datos base ----------------
INSERT INTO estado VALUES
 (1,'ORDEN NUEVA',1),
 (2,'SURTIENDOSE',2),
 (3,'EMPACANDOSE',3),
 (4,'EN RUTA'    ,4),
 (5,'ENTREGADA'  ,5);

INSERT INTO destino VALUES
 ('GT001','Ciudad de Guatemala',TRUE , 35.00, 10.00),
 ('GT002','Mixco'              ,TRUE , 40.00, 10.00),
 ('GT003','Antigua Guatemala'  ,TRUE , 55.00, 15.00),
 ('GT004','Quetzaltenango'     ,TRUE , 85.00, 15.00),
 ('GT005','Flores'             ,FALSE,  0.00,  0.00);
