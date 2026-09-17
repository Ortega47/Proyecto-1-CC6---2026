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
