# Modelo de datos – Sitio Courier

Motor: **PostgreSQL**. El DDL completo está en `courier_bd_simple.sql`.
El diagrama E-R (notación Chen) está en `ER_Courier_Proyecto1_simple.png`.

## Entidades

### Tienda
Tiendas virtuales afiliadas. Existe porque los tres endpoints reciben `&tienda=` y hay que
saber quién llama.

| Atributo | Tipo | Nota |
|---|---|---|
| `id_tienda` | CHAR(15) | PK. 15 caracteres por el enunciado. |
| `nombre` | VARCHAR(80) | |
| `host` | VARCHAR(120) | |

### Destino
Ciudades cubiertas y su costo. El enunciado pide **un costo por destino**, por eso los
costos van aquí y no en una tabla de tarifas aparte.

| Atributo | Tipo | Nota |
|---|---|---|
| `id_destino` | CHAR(5) | PK. 5 caracteres por el enunciado. |
| `ciudad` | VARCHAR(80) | |
| `cobertura` | BOOLEAN | Si es FALSE, el WebService devuelve costo 0. |
| `costo_envio` | NUMERIC(10,2) | |
| `costo_manejo` | NUMERIC(10,2) | Shipping and handling. |

### Estado
Catálogo fijo de los 5 estados del enunciado.

| Atributo | Tipo | Nota |
|---|---|---|
| `id_estado` | SMALLINT | PK, valores 1 a 5. |
| `nombre` | VARCHAR(20) | |
| `orden` | SMALLINT | Secuencia, para no dejar saltar estados. |

### Usuario
Quien entra a las pantallas de actualización de estado.

| Atributo | Tipo | Nota |
|---|---|---|
| `id_usuario` | SERIAL | PK |
| `nombre` | VARCHAR(80) | |
| `usuario` | VARCHAR(30) | Único. |
| `contrasena` | VARCHAR(120) | Guardar con hash, nunca en texto plano. |

### Envio
Los envíos contratados.

| Atributo | Tipo | Nota |
|---|---|---|
| `no_guia` | CHAR(15) | PK. Es el número de rastreo. |
| `id_tienda` | CHAR(15) | FK |
| `no_orden` | VARCHAR(20) | Número de orden que manda la tienda. |
| `id_destino` | CHAR(5) | FK |
| `id_estado` | SMALLINT | FK. Estado **actual**. |
| `destinatario` | VARCHAR(100) | |
| `direccion` | VARCHAR(200) | |
| `fecha` / `hora` | DATE / TIME | Se serializan como yyyymmdd y hh:nn. |
| `costo_total` | NUMERIC(10,2) | Se congela al contratar el envío. |

Restricción clave: `UNIQUE (id_tienda, no_orden)`.

### Seguimiento
Historial de estados (package tracking). Es **entidad débil** de Envio: sin envío no existe.

| Atributo | Tipo | Nota |
|---|---|---|
| `id_seguimiento` | BIGSERIAL | Llave parcial. |
| `no_guia` | CHAR(15) | FK, en cascada. |
| `id_estado` | SMALLINT | FK |
| `id_usuario` | INTEGER | FK, quién hizo el cambio. |
| `fecha` / `hora` | DATE / TIME | |
| `observacion` | VARCHAR(200) | |

## Relaciones

| Relación | Entre | Cardinalidad |
|---|---|---|
| Contrata | Tienda – Envio | 1 : N |
| Dirigido a | Envio – Destino | N : 1 |
| Tiene estado | Envio – Estado | N : 1 |
| Registra (identificadora) | Envio – Seguimiento | 1 : N |
| Corresponde a | Seguimiento – Estado | N : 1 |
| Actualiza | Usuario – Seguimiento | 1 : N |

## Decisiones de diseño (por si el ingeniero pregunta)

1. **El costo vive en Destino**, no en una tabla de tarifas. El enunciado pide costo por
   destino; una tabla de tarifas por peso/tamaño sería alcance extra.
2. **Envio guarda el estado actual y Seguimiento el historial.** Así `/courier/status`
   es una lectura directa, sin recorrer todo el historial, y el tracking igual queda completo.
3. **`costo_total` se congela** al momento de contratar. Si después cambia el costo del
   destino, la guía vieja no se altera.
4. **Fechas y horas se guardan como DATE y TIME**, no como texto. El formato `yyyymmdd` /
   `hh:nn` se aplica solo al armar la respuesta XML/JSON.
5. **El código del courier es configuración**, no una tabla: es un valor fijo de nuestra
   aplicación que se pone en el campo `courrier` de las respuestas.

## Fuera de alcance (decidido a propósito)

País, tipos de paquete, tabla de tarifas, repartidores, rutas, comprobante de entrega,
roles de usuario y bitácora de llamadas al WebService. Nada de eso lo pide el enunciado.
Si el catedrático lo pide después, se agrega.
