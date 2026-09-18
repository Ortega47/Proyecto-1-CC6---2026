# Courier — Proyecto 1 CC6

Hecho con la misma organización del proyecto de CC5: PHP directo, PostgreSQL,
una carpeta por módulo y un solo CSS. No usa frameworks ni datos demo.
El Dockerfile se usa únicamente para alojarlo en Render; para trabajar en local
basta PHP o XAMPP. El WebService se agregará después.

## Para iniciarlo

1. Usa tu base de datos existente con las ocho tablas que ya creaste.
2. Comprueba que Estado tenga los cinco estados del courier y que Contraseña
   admita 255 caracteres. No es necesario recrear tus tablas.
3. Configura `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASSWORD`
   en el entorno donde ejecutas PHP, como en CC5.
4. Usa PHP 8.1 o superior con `pgsql` y `mbstring` activadas en `php.ini`.
   Ejecuta `php -S localhost:8000` desde la carpeta del proyecto y abre
   `http://localhost:8000/login.php`. En Apache/XAMPP, configura esta carpeta
   como raíz del sitio. El acceso usa `/login.php`, igual que el flujo de CC5.
5. Abre **login.php** para iniciar sesión. El enlace **Regístrate** permite
   crear una cuenta de cliente con nombre, usuario y contraseña.

Igual que en CC5, **el ID 0 es administrador** y los demás son clientes.
Si ya tienes usuarios, debe existir uno con ID 0 para administrar el sitio.
No hay una contraseña predeterminada. El registro público nunca crea administradores.
Como en CC5, las cuentas se crean mediante el registro; no hay un módulo para
agregar, editar o eliminar usuarios, ni cambio o recuperación de contraseña.
No se necesitan columnas ni tablas nuevas para los clientes. La ampliación del esquema es
`Contraseña varchar(255)` para guardar hashes; `Usuario` debe ser único.
Las claves antiguas en texto se convierten a hash al iniciar sesión.
Si tu columna sigue siendo `char(50)`, ejecuta una sola vez en pgAdmin:

```sql
ALTER TABLE Usuario ALTER COLUMN Contraseña TYPE varchar(255);
```

Este ajuste conserva los registros. No se ejecuta automáticamente desde PHP.
La conexión también admite las variables `DB_HOST`, `DB_PORT`, `DB_NAME`,
`DB_USER` y `DB_PASSWORD`, como la referencia de CC5.

## Publicarlo en Render

El servicio de Render debe usar **Docker**, la rama **main**, la raíz del
repositorio como directorio y `./Dockerfile` como Dockerfile Path. Deja
**Docker Command** vacío para usar el inicio definido en el archivo.
El contenedor incluye PHP, Apache y las extensiones `pgsql` y `mbstring`.

En **Environment** configura `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y
`DB_PASSWORD` con los datos de la base que ya tiene tus tablas. `localhost`
en Render apunta al contenedor, no a tu computadora. Si usas PostgreSQL en
Render, utiliza su hostname interno cuando ambos servicios estén en la misma
región. No guardes la contraseña en GitHub.

Después de subir cambios, usa **Manual Deploy → Deploy latest commit** si
el despliegue automático no comienza. El administrador puede abrir `/conexion.php` para
comprobar la conexión. Debe existir un administrador con ID 0 en esa base.
`register.php` permite el registro público de clientes también en Render.

Referencia: https://render.com/docs/docker

## Archivos: la misma lógica de CC5

| Archivo o carpeta | Para qué sirve |
|---|---|
| `postsql.php` | Abre la conexión con `pg_connect` |
| `conexion.php` | Comprueba si PostgreSQL está disponible |
| `auth.php` | Comprueba que exista una sesión |
| `login.php`, `register.php`, `logout.php` | Acceso y registro público de clientes |
| `index.php` | Menú principal |
| `origenes/`, `destinos/`, `tiendas/` | Listar, agregar, editar y eliminar |
| `cabeceras/` | Rutas con costo de envío y manejo |
| `envios/` | Registrar, consultar, editar y eliminar envíos |
| `estados/` | Catálogo y una pantalla por cada uno de los cinco estados |
| `seguimiento/` | Avanzar estados, consultar historial y corregir observaciones |
| `rastreo.php` | Consulta pública por número de guía |
| `style.css` | Estilos; cada página contiene su propio HTML |

El estilo sigue CC5: PHP al inicio, variables de formulario, consultas en
`$query`, resultados en `$result`, bloques `if/else` con llaves y `echo` explícito.
Cada pantalla agrupa PHP en uno o dos bloques; formularios y tablas se imprimen
juntos. No hay operadores ternarios ni `??`. `auth.php` solo comprueba la sesión; cada página de gestión comprueba
`$_SESSION["admin"]`, como en CC5. No hay tokens de formulario. El registro
inicia sesión automáticamente como cliente, sin confirmar la contraseña.
No usa etiquetas `<?= ?>`, `endif` ni `htmlspecialchars`. Los valores se
imprimen directamente, como en el proyecto de referencia.
No hay funciones propias ni archivos de plantillas. Cada página lee `$_POST`,
valida con `if`, ejecuta su consulta y muestra su formulario o tabla.

Los archivos `agregar.php` y `editar.php` leen los campos directamente y usan comprobaciones
breves del negocio antes de guardar; PostgreSQL comprueba tipos, tamaños y
referencias. El registro actual se consulta por su ID para llenar el formulario.

Cada módulo tiene su SQL en `listado.php`, `agregar.php`, `editar.php` y
`eliminar.php`, como Equipos y Fases en CC5. Para editar se consulta la fila por
su ID; los valores no se toman del texto de la URL. Las consultas con datos de
formularios usan `pg_query_params`.

## Uso básico

1. Registra **Origen** y **Destino** y marca su cobertura.
2. Registra la **Tienda**.
3. En **Rutas y tarifas**, crea una **Cabecera** que una origen, destino y costos.
4. Agrega un **Envío**. Ingresa su guía, fecha, hora, destinatario, tienda y ruta.
   Si dejas el total vacío, se suman envío y manejo. Se crea en **Orden nueva**.
5. En el listado o detalle, usa **Cambiar estado**: orden nueva → surtiéndose →
   empacándose → en ruta → entregada. Cada avance registra usuario, fecha,
   hora y observación. La entrega guarda su fecha automáticamente.
6. Usa **Rastrear paquete** para consultar la guía sin iniciar sesión.

Los clientes acceden al rastreo por número de guía. No pueden acceder a los
catálogos, listados internos ni modificar envíos, incluso con una URL directa.
El administrador mantiene los datos y avanza los envíos. No existe el rol de
operador. Las cuentas existentes con ID distinto de 0 ahora son clientes.
El esquema actual no vincula envíos a cuentas: no hay un listado de «mis envíos».
No se eliminan catálogos que estén en uso ni el administrador.
Los cinco estados se conservan; se puede cambiar su nombre. En Seguimiento se
edita la observación; para corregir un avance equivocado se puede eliminar solo
el último, regresando el envío al anterior. La orden inicial se conserva.
Eliminar un envío borra todo su seguimiento, previa confirmación.

Los IDs de catálogos se ingresan manualmente, como en tus tablas y en CC5.
Los IDs de Seguimiento se asignan al registrar cada avance.

## WebService

Los tres endpoints del contrato están en `ws/` y son públicos (sin sesión):

| Ruta del contrato | Archivo | Devuelve |
|---|---|---|
| `/consulta?destino=&formato=` | `ws/consulta.php` | `courrier`, `destino`, `cobertura`, `costo` |
| `/envio?orden=&destinatario=&destino=&direccion=&tienda=` | `ws/envio.php` | `courrier`, `orden`, `tienda`, `destino`, `guia`, `status`, `costo`, `fecha`, `hora` |
| `/status?orden=&tienda=&formato=` | `ws/status.php` | `courrier`, `orden`, `status` |

`formato` acepta `XML` o `JSON`; sin él responde XML. Las rutas cortas las
reescribe el `.htaccess` de la raíz (Apache con `mod_rewrite`, ya habilitado en
el Dockerfile); con `php -S` se usan directo `ws/consulta.php`, `ws/envio.php`
y `ws/status.php`. El código del courier, el origen fijo y el prefijo de la
guía están en `ws/config.php`. Ejemplos, campos y pendientes de acuerdo con la
Tienda Virtual: `docs/06_webservice.md`.

**Antes de usarlo, ejecuta una sola vez en pgAdmin `db/ws_cambios.sql`**, que
agrega a `Envio` las columnas `Direccion` y `No_orden` y la restricción única
tienda + orden. Además debe existir el administrador con ID 0 (firma el primer
seguimiento de cada envío) y el origen configurado en `ws/config.php` con
cobertura y sus cabeceras.

Pendiente: acordar con el grupo de Tienda Virtual el largo de la guía, el
código de tienda que mandan, el host real de cada sitio y la estructura de la
respuesta de `/envio`; y fijar el código definitivo del courier.
