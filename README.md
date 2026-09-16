# Sitio Courier — Proyecto 1, Ciencias de la Computación VI

Universidad Galileo · FISICC · Bases de Datos · 2026

Sistema distribuido de compra en línea con tres sitios: Tienda Virtual, Tarjeta de
Crédito y Courier. **Este repositorio es el sitio Courier**: administra los destinos
cubiertos con su costo de envío y manejo, recibe los envíos que piden las tiendas y
les da seguimiento por los cinco estados del enunciado (orden nueva, surtiéndose,
empacándose, en ruta, entregada). Los otros dos sitios los construyen otros grupos.

Los documentos del proyecto están en `docs/` (enunciado, contrato del WebService,
modelo de datos, bitácora y diagrama E-R) y el script de la base de datos en
`db/courier_bd_simple.sql`.

## Qué incluye este commit

Frontend completo con datos de demostración, en el estilo del proyecto de CC5:
una carpeta por entidad con `listado.php`, `agregar.php`, `editar.php` y
`eliminar.php`, PHP directo en cada página y un solo `style.css`.

- Solo HTML, PHP y CSS. Sin JavaScript, sin frameworks ni recursos externos.
- Sesión con `$_SESSION` y dos roles: administrador y operador.
- Todavía **sin conexión a PostgreSQL**: `datos_demo.php` hace de base de datos.
  Cada consulta que falta está anotada con `// TODO(bd):` y la sentencia con
  `pg_query_params` y parámetros `$1, $2…`.
- Después de cada formulario se muestra "Vista previa: esto se guardará cuando
  conectemos la base de datos." Nada se guarda.

## Cómo correrlo

Requiere PHP 8.1 o superior, sin dependencias.

**Servidor de PHP**, desde la raíz del repositorio:

```
php -S localhost:8000
```

Abrir <http://localhost:8000/>. Si `php` no está en el PATH (XAMPP en macOS):
`/Applications/XAMPP/xamppfiles/bin/php -S localhost:8000`.

**XAMPP / Apache:** copiar la carpeta a `htdocs/courier` y abrir
<http://localhost/courier/>. Los enlaces son relativos, así que funciona en cualquier
subcarpeta.

**Docker:**

```
docker build -t courier .
docker run -p 8080:80 courier
```

y abrir <http://localhost:8080/>.

## Credenciales de demostración

| Usuario | Contraseña | Rol | Nota |
|---|---|---|---|
| `admin` | `admin123` | Administrador | Ana Pérez |
| `operador` | `operador123` | Operador | Luis Morales; tiene registros en seguimiento |
| `operador2` | `operador123` | Operador | Carlos Juárez; sin registros, sirve para probar una eliminación que sí procede |

Guías de demostración para el rastreo: `GUA-2609-000004` (en ruta),
`GUA-2609-000001` (entregada), `GUA-2609-000011` (orden nueva).

## Mapa de pantallas y permisos

| Ruta | Pantalla | Sin sesión | Operador | Admin |
|---|---|---|---|---|
| `index.php` | Rastrear paquete y destinos que cubrimos | sí | sí | sí |
| `rastreo.php?guia=…` | Resultado del rastreo | sí | sí | sí |
| `login.php`, `logout.php` | Inicio y cierre de sesión | sí | sí | sí |
| `menu.php` | Menú del personal (botones según rol) | → login | sí | sí |
| `envios/listado.php` | Envíos con filtros por estado, tienda, destino y guía u orden | → login | sí | sí |
| `envios/detalle.php?guia=…` | Detalle con dirección e historial | → login | sí | sí |
| `estados/orden_nueva.php` … `entregadas.php` | Una pantalla por estado (incluyen `tabla_estado.php`) | → login | sí | sí |
| `estados/cambiar.php?guia=…` | Confirmación Sí/No del paso al siguiente estado | → login | sí | sí |
| `destinos/listado.php`, `tiendas/listado.php` | Catálogos | → login | sí (sin botones) | sí |
| `destinos/` y `tiendas/` `agregar`, `editar?id=`, `eliminar?id=` | Mantenimiento | → login | → menú con aviso | sí |
| `usuarios/*` | Mantenimiento de usuarios | → login | → menú con aviso | sí |
| `resumen.php` | Envíos por estado, destino y tienda | → login | → menú con aviso | sí |

Reglas que aplica el servidor: solo se avanza al estado inmediato siguiente; no se
elimina un destino ni una tienda con envíos; un usuario no puede eliminarse a sí mismo
ni se elimina a quien tenga registros en `seguimiento`; en `usuarios/editar.php`, si la
contraseña se deja vacía no cambia.

Los nombres `consulta.php`, `envio.php` y `status.php` quedan reservados en la raíz
para los endpoints del WebService (`/consulta`, `/envio`, `/status`).

## Estructura

```
index.php  rastreo.php              públicos
login.php  logout.php  menu.php  resumen.php
auth.php        exige sesión ($raiz define el redirect: '' o '../')
solo_admin.php  exige es_admin; si no, vuelve al menú con aviso
funciones.php   h(), fecha(), hora(), moneda(), nombre_estado() y acceso a datos demo
datos_demo.php  arreglos con los nombres de columna del modelo (se reemplaza por postsql.php)
encabezado.php  pie.php  style.css
envios/  estados/  destinos/  tiendas/  usuarios/
img/  fuentes/  docs/  db/  Dockerfile
```

## Pendientes

- **Conexión a PostgreSQL** con `pg_connect` y `pg_query_params` (archivo `postsql.php`
  leyendo `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`), reemplazando
  `datos_demo.php`. Las consultas ya están escritas en cada `TODO(bd)`.
- **Rol de usuario:** el campo `es_admin` solo existe en los datos demo. Falta aplicar
  `ALTER TABLE usuario ADD COLUMN es_admin BOOLEAN NOT NULL DEFAULT FALSE;` en
  `db/courier_bd_simple.sql`, y actualizar `docs/04_modelo_datos.md` y el diagrama E-R.
- **Campo "activa" de tienda:** el contrato (`/envio`, paso 1) pide validar que la tienda
  "esté activa", pero el modelo no tiene ese campo. Decidir si se agrega.
- **Nombres de estado en `status`:** el SQL guarda `SURTIENDOSE` (mayúsculas, sin tilde) y
  el contrato devuelve "surtiéndose". Acordar cuál viaja en el WebService.
- **Formato del número de guía:** `GUA-aamm-nnnnnn` (15 caracteres) es provisional
  hasta acordarlo con los grupos de Tienda Virtual.
- **Llave de `seguimiento`:** el modelo la describe como entidad débil (llave parcial),
  pero el SQL usa `BIGSERIAL PRIMARY KEY` sola. Alinear el DDL o el documento.
- Endpoints `/consulta`, `/envio` y `/status` en XML y JSON; nombre y código de 15
  caracteres del courier; nombres y carnés del equipo en `pie.php`; manual de usuario.
