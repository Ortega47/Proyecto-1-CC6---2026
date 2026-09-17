# Courier — Proyecto 1 CC6

Hecho con la misma organización del proyecto de CC5: PHP directo, PostgreSQL,
una carpeta por módulo y un solo CSS. No usa frameworks ni datos demo.
El Dockerfile se usa únicamente para alojarlo en Render; para trabajar en local
basta PHP o XAMPP. El WebService se agregará después.

## Para iniciarlo

1. Usa tu base de datos existente con las ocho tablas que ya creaste.
2. Comprueba que Estado tenga los cinco estados del courier y que Contraseña
   admita 255 caracteres. No es necesario recrear tus tablas.
3. Edita los cinco datos de conexión al principio de **postsql.php**:
   servidor, puerto, base, usuario y contraseña.
4. Usa PHP 8.1 o superior con `pgsql` y `mbstring` activadas en `php.ini`.
   Con XAMPP, coloca el proyecto en `htdocs/courier` y abre
   `http://localhost/courier/conexion.php`.
   También puedes ejecutar `php -S localhost:8000` desde la carpeta del proyecto
   y abrir `http://localhost:8000/conexion.php`.
5. Abre **login.php**. Si no hay usuarios, aparece **Crear primer administrador**.
   Hazlo desde localhost. Después inicia sesión.

Igual que en CC5, **el ID 0 es administrador** y los demás son operadores.
Si ya tienes usuarios, debe existir uno con ID 0 para administrar el sitio.
No hay una contraseña predeterminada. La única ampliación del esquema es
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
el despliegue automático no comienza. Puedes abrir `/conexion.php` para
comprobar la conexión. Debe existir un administrador con ID 0 en esa base:
la creación inicial desde `register.php` está restringida a localhost.

Referencia: https://render.com/docs/docker

## Archivos: la misma lógica de CC5

| Archivo o carpeta | Para qué sirve |
|---|---|
| `postsql.php` | Abre la conexión con `pg_connect` |
| `conexion.php` | Comprueba si PostgreSQL está disponible |
| `auth.php` | Revisa la sesión y distingue administrador/operador |
| `login.php`, `register.php`, `logout.php` | Acceso y creación inicial del administrador |
| `index.php` | Menú principal |
| `origenes/`, `destinos/`, `tiendas/`, `usuarios/` | Listar, agregar, editar y eliminar |
| `cabeceras/` | Rutas con costo de envío y manejo |
| `envios/` | Registrar, consultar, editar y eliminar envíos |
| `estados/` | Catálogo y una pantalla por cada uno de los cinco estados |
| `seguimiento/` | Avanzar estados, consultar historial y corregir observaciones |
| `rastreo.php` | Consulta pública por número de guía |
| `style.css` | Estilos; cada página contiene su propio HTML |

No hay funciones propias ni archivos de plantillas. Cada página lee `$_POST`,
valida con `if`, ejecuta su consulta y muestra su formulario o tabla.

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

Los operadores consultan catálogos y avanzan envíos. El administrador mantiene
los datos. No se eliminan catálogos que estén en uso ni el administrador.
Los cinco estados se conservan; se puede cambiar su nombre. En Seguimiento se
edita la observación; para corregir un avance equivocado se puede eliminar solo
el último, regresando el envío al anterior. La orden inicial se conserva.
Eliminar un envío borra todo su seguimiento, previa confirmación.

Los IDs de catálogos se ingresan manualmente, como en tus tablas y en CC5.
Los IDs de Seguimiento se asignan al registrar cada avance.

## Pendiente: WebService

Esta etapa cubre las pantallas locales del courier. Las llamadas `/consulta`,
`/envio` y `/status` en XML/JSON se harán después según el PDF.
Para esa integración habrá que acordar los códigos públicos de destino y
courier, y revisar la dirección y el número de orden por envío: las tablas
compartidas no tienen dirección y guardan `No_orden` en Tienda.
El enunciado y el contrato están en `docs/`; el modelo actual está en
`docs/04_modelo_datos.md`.
