<?php
$raiz = '../';
require __DIR__ . '/../solo_admin.php';
require __DIR__ . '/../funciones.php';
require __DIR__ . '/../datos_demo.php';

$titulo = 'Usuarios';
require __DIR__ . '/../encabezado.php';
?>
<div class="contenido">
    <h1>Usuarios</h1>
    <p>Personal que entra al sistema. Las contraseñas se guardan con hash y nunca se muestran.</p>

    <div class="botones-listado">
        <a class="boton" href="agregar.php">Agregar usuario</a>
    </div>

    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Usuario</th>
                <th scope="col">Rol</th>
                <th scope="col">Acciones</th>
            </tr>
            <?php foreach ($demo['usuario'] as $usuario): ?>
                <tr>
                    <td><?= h($usuario['nombre']) ?><?= (int) $usuario['id_usuario'] === (int) $_SESSION['id_usuario'] ? ' (usted)' : '' ?></td>
                    <td class="codigo"><?= h($usuario['usuario']) ?></td>
                    <td><?= $usuario['es_admin'] ? 'Administrador' : 'Operador' ?></td>
                    <td class="nowrap">
                        <a href="editar.php?id=<?= (int) $usuario['id_usuario'] ?>">Editar</a> |
                        <a href="eliminar.php?id=<?= (int) $usuario['id_usuario'] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="enlaces">
        <a href="../menu.php">Menú principal</a>
    </div>
</div>
<?php require __DIR__ . '/../pie.php'; ?>
