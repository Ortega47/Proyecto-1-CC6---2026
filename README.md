# Sitio Courier — Proyecto 1, Ciencias de la Computación VI

Universidad Galileo · FISICC · Bases de Datos · 2026

Sistema distribuido de compra en línea con tres sitios: Tienda Virtual, Tarjeta de
Crédito y Courier. **Este repositorio es el sitio Courier**: administra los destinos
cubiertos con su costo de envío y manejo, recibe los envíos que piden las tiendas y
permite darles seguimiento por los cinco estados del enunciado (orden nueva,
surtiéndose, empacándose, en ruta, entregada). Los otros dos sitios los construyen
otros grupos.

Los documentos del proyecto están en `docs/` (enunciado, contrato del WebService,
modelo de datos, bitácora y diagrama E-R) y el script de la base de datos en
`db/courier_bd_simple.sql`.

## Qué incluye este commit

Solo el **frontend** de todas las pantallas, con datos de demostración:

- HTML, PHP y CSS. **Sin JavaScript**, sin frameworks de CSS y sin recursos externos.
- Sin base de datos, sin login real y sin WebService. Los formularios responden con
  una redirección y el mensaje "Vista previa: … cuando conectemos la base de datos".
- Cada lugar donde después va una consulta está marcado con `// TODO(bd):`.
- Fuentes Barlow y Barlow Condensed autoalojadas en `assets/fonts/` (licencia OFL).

## Cómo correrlo

Requiere PHP 8.1 o superior. No hay dependencias ni Composer.

**Con el servidor de PHP**, desde la raíz del repositorio:

```
php -S localhost:8000
```

y abrir <http://localhost:8000/>. Si `php` no está en el PATH (por ejemplo, con
XAMPP en macOS): `/Applications/XAMPP/xamppfiles/bin/php -S localhost:8000`.

**Con XAMPP** (o cualquier Apache): copiar la carpeta a `htdocs/courier` y en
`includes/config.php` poner `define('BASE_URL', '/courier');`. Todos los enlaces y
rutas de assets se arman con `url()`, así que no hay que tocar nada más.

## Mapa de pantallas

Sitio público (para quien recibe el paquete, mobile-first, trato de usted):

| Ruta | Pantalla |
|---|---|
| `index.php` | Inicio: campo de rastreo, cómo avanza un paquete, destinos y costos |
| `rastreo.php?guia=…` | Resultado del rastreo: etiqueta de guía, ruta de estados e historial. Responde 404 si la guía no existe |
| `404.php` | Página no encontrada (también se usa cuando un parámetro no existe) |

Panel interno (personal del courier, carpeta `panel/`):

| Ruta | Pantalla |
|---|---|
| `panel/login.php` | Acceso (`?error=1` muestra "Usuario o contraseña incorrectos") |
| `panel/salir.php` | Cierra sesión y vuelve al login |
| `panel/index.php` | Tablero: ruta con el conteo de cada etapa y últimos envíos |
| `panel/envios.php` | Envíos contratados con filtros por estado, tienda, destino y guía u orden |
| `panel/detalle-envio.php?guia=…` | Etiqueta interna, ruta, historial con usuario y paso a la siguiente etapa. 404 si no existe |
| `panel/ordenes-nuevas.php` | Estado 1 · botón "Empezar a surtir" |
| `panel/surtiendose.php` | Estado 2 · botón "Pasar a empaque" |
| `panel/empacandose.php` | Estado 3 · botón "Despachar a ruta" |
| `panel/en-ruta.php` | Estado 4 · botón "Confirmar entrega" |
| `panel/entregadas.php` | Estado 5 · solo consulta |
| `panel/destinos.php` | Mantenimiento de destinos (`?editar=GT003`, `?eliminar=GT005`) |
| `panel/tiendas.php` | Mantenimiento de tiendas afiliadas (`?editar=…`, `?eliminar=…`) |

Las cinco pantallas de estado son archivos delgados que incluyen la plantilla
`includes/pantalla_estado.php`. En cada una hay un solo formulario: el botón
"… los seleccionados" mueve las guías marcadas y el botón de cada fila mueve una sola
(en ese caso se ignoran las marcadas).

Guías de demostración para probar el rastreo: `GUA-2609-000004` (en ruta),
`GUA-2609-000001` (entregada), `GUA-2609-000011` (orden nueva).

## Estructura

```
index.php  rastreo.php  404.php
panel/               pantallas del personal
includes/
  config.php         BASE_URL, NOMBRE_COURIER, CODIGO_COURIER
  funciones.php      h(), url(), fecha_ui(), hora_ui(), moneda(), etiqueta_estado(), …
  consultas.php      capa de acceso a datos (hoy lee datos_demo.php; después, PDO)
  datos_demo.php     arreglos con los nombres de columna exactos del modelo
  pantalla_estado.php
  partes/            layouts, menú, etiqueta de guía, ruta de estados, insignia, mensajes
assets/css/          base.css (tokens y componentes), publico.css, panel.css
assets/fonts/        Barlow y Barlow Condensed (.woff2)
assets/img/          logo.svg y favicon.svg provisionales
docs/  db/           documentos del proyecto y script de la base de datos
```

Los nombres `consulta.php`, `envio.php` y `status.php` quedan reservados en la raíz
para los endpoints del WebService (`/consulta`, `/envio`, `/status`).

## Pendientes

- Conexión a PostgreSQL con PDO: reemplazar `includes/consultas.php` y borrar
  `datos_demo.php`. Cada consulta está anotada con `TODO(bd)`.
- Login real con `password_verify` y sesión; `panel/salir.php` debe cerrarla.
- Endpoints del WebService: `/consulta`, `/envio` y `/status`, en XML y JSON, según
  `docs/03_contrato_webservice.md` (los nombres del contrato son literales, aunque parezcan errores de escritura).
- Nombre comercial y código de 15 caracteres del courier (`NOMBRE_COURIER` y
  `CODIGO_COURIER` en `includes/config.php`) y el logo definitivo en `assets/img/`.
- Formato del número de guía: en la demo es `GUA-aamm-nnnnnn` (15 caracteres);
  hay que acordarlo con los grupos de Tienda Virtual antes de implementar `/envio`.
- Manual de usuario para la entrega final.
