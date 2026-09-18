# Reorganización según el proyecto de CC5

Se revisaron la conexión, el acceso, el menú, el registro, los estilos y los
módulos de equipos, fases, calendario, grupos y quinielas del ZIP de referencia.

Se rehízo el courier con ese patrón: conexión mediante `pg_connect`, sesiones
en `auth.php`, administrador con ID 0, consultas y formularios dentro de cada
página, una carpeta por entidad y un CSS compartido.

Se retiraron Docker, los datos demo y el mantenimiento genérico de la versión
anterior. Se conservan consultas parametrizadas, contraseñas con hash y
confirmación de formularios. El WebService queda para una etapa posterior.

Se retiraron las funciones auxiliares y las plantillas compartidas. Cada página
contiene su PHP y su HTML completos. También se retiraron los archivos de creación
de tablas, porque la base de datos del usuario ya existe.

# WebService

Se agregaron los tres endpoints del contrato en la carpeta `ws/`, públicos y
solo `GET`, con respuesta en XML o JSON según `formato`:

| Endpoint | Archivo | Estado |
|---|---|---|
| `/consulta` (cobertura y costo por destino) | `ws/consulta.php` | Hecho |
| `/envio` (registro del envío y guía) | `ws/envio.php` | Hecho |
| `/status` (estado de una orden de una tienda) | `ws/status.php` | Hecho |

Los nombres de raíz y de campos son los literales del contrato, incluido
`courrier`. El código del courier, el origen fijo y el prefijo de la guía
están en `ws/config.php`. El `.htaccess` de la raíz y el Dockerfile habilitan
las rutas `/consulta`, `/envio` y `/status`.

Cambios de base pendientes de correr en pgAdmin (`db/ws_cambios.sql`):
`Envio.Direccion`, `Envio.No_orden` y la restricción única tienda + orden.
`Tienda.No_orden` se conserva pero el WebService no la usa.

Con la Tienda Virtual queda por acordar: largo de la guía, si mandan nuestro
ID de tienda o el suyo, el host real de cada sitio y la estructura de la
respuesta de `/envio`. El detalle está en `docs/06_webservice.md`.
