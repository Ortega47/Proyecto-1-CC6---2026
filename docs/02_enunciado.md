# Enunciado – Proyecto 1

Universidad Galileo · Ingeniería en Sistemas, Informática y Ciencias de la Computación
Ciencias de la Computación VI – Bases de Datos · 25/8/2026

## Descripción general

Creación de un sistema distribuido de base de datos que integra tres tipos de sitio:
**Tienda Virtual**, **Tarjeta de Crédito** y **Courier**, para administrar la compra de
productos en línea y el envío de los mismos.

A cada grupo se le asigna uno de los tres tipos de sitio. La tarea consiste en desarrollar
una aplicación web con su respectiva base de datos e integrarla con el resto de sitios.
Por ser una aplicación académica se hacen varias suposiciones que simplifican el proyecto.

**A nuestro grupo le corresponde el sitio COURIER.**

## Los tres sitios

### Tarjeta de Crédito (otro grupo)
Administra tarjetas de crédito o débito. Registra las tarjetas emitidas (número, nombre del
titular, fecha de vencimiento, número de seguridad, monto autorizado y monto disponible) y
todas las transacciones autorizadas de una tarjeta (consumos y pagos). Al recibir una
solicitud de autorización verifica que la tarjeta sea válida, que los datos sean correctos,
que no esté vencida y que tenga disponibilidad; responde con el número de autorización o
DENEGADO.

### Tienda Virtual (otro grupo)
Vende productos en línea. Incluye ingreso de productos, actualización de inventarios,
registro de clientes, catálogos de productos, carrito de compra con el total a pagar,
pantalla para elegir el courier, pantalla de pago con tarjeta de crédito y opción para
rastrear el envío del producto.

### Courier (NUESTRO SITIO)
Administra una compañía encargada del envío de documentos o paquetes de tamaño y/o peso
limitado a un determinado costo, que se distribuyen a diferentes destinos (ciudades) según
lo requiera el cliente. Debe permitir:

- Registro de los **destinos cubiertos** (ciudades) y el **costo de manejo y envío**
  (shipping and handling) por cada destino.
- Registro de los **envíos contratados**.
- Opciones para el **seguimiento** de los envíos (package tracking), desde el cierre de la
  orden hasta la entrega del paquete.
- **Pantallas para la actualización de cada estado.**

Estados del envío:

| # | Estado |
|---|---|
| 1 | orden nueva |
| 2 | surtiéndose |
| 3 | empacándose |
| 4 | en ruta |
| 5 | entregada |

## Integración entre sitios

- Tienda Virtual ↔ Tarjeta de Crédito: **autorización de compras**.
- Tienda Virtual ↔ Courier: **consulta del estado del envío**.
- Cada sitio mantiene su propia **BD local**.

## Registro de emisores y couriers

Cada tienda virtual debe implementar un mantenimiento para registrar los emisores de tarjeta
de crédito y los courier: **nombre, host y los scripts** que implementan las consultas y
procesos.

## Consultas remotas

Las transacciones y consultas se hacen mediante un **WebService (API-REST)**, implementado
con llamadas a scripts que devuelven los resultados en **XML y JSON**.
El detalle de URLs y formatos está en `03_contrato_webservice.md`.

Reglas de formato obligatorias:

- El destino es un código de **5 caracteres** que identifica la ciudad.
- Fechas en formato **yyyymmdd**; horas en formato **hh:nn**.
- Identificador de emisores de tarjeta de crédito y couriers: **15 caracteres**.
- Números de tarjeta: **16 dígitos**, sin guiones.
- Fecha de vencimiento de tarjeta: formato **yyyymm**.

## Aspectos generales

1. Fecha de entrega: la indicada por el catedrático en clase (la hoja impresa trae una
   fecha de una edición anterior del curso; confirmar).
2. Grupos de 3 integrantes.
3. El día de la entrega final se debe mostrar **el sistema completo funcionando**, el
   **diagrama E/R** y el **manual de usuario**.
