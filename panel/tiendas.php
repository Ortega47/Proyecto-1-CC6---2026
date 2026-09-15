<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

$editar   = trim((string) ($_GET['editar'] ?? ''));
$eliminar = trim((string) ($_GET['eliminar'] ?? ''));
$errores  = [];
$aviso    = null;

$valores  = ['id_tienda' => '', 'nombre' => '', 'host' => ''];
$editando = false;

if ($editar !== '') {
    $tienda_editar = tienda_por_id($editar);
    if ($tienda_editar === null) {
        $regreso = ['panel/tiendas.php', 'Volver a tiendas'];
        require dirname(__DIR__) . '/404.php';
        exit;
    }
    $valores  = $tienda_editar;
    $editando = true;
}

$tienda_eliminar = null;
if ($eliminar !== '') {
    $tienda_eliminar = tienda_por_id($eliminar);
    if ($tienda_eliminar === null) {
        $regreso = ['panel/tiendas.php', 'Volver a tiendas'];
        require dirname(__DIR__) . '/404.php';
        exit;
    }
    if (conteo_envios_tienda($eliminar) > 0) {
        $n = conteo_envios_tienda($eliminar);
        $aviso = sprintf('%s tiene %d %s y no se puede eliminar. Las tiendas con envíos se conservan por el historial.',
            $tienda_eliminar['nombre'], $n, $n === 1 ? 'envío' : 'envíos');
        $tienda_eliminar = null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string) ($_POST['accion'] ?? 'guardar');

    if ($accion === 'eliminar') {
        $id = trim((string) ($_POST['id_tienda'] ?? ''));
        if (tienda_por_id($id) === null) {
            $regreso = ['panel/tiendas.php', 'Volver a tiendas'];
            require dirname(__DIR__) . '/404.php';
            exit;
        }
        if (conteo_envios_tienda($id) > 0) {
            $aviso = 'La tienda ' . $id . ' tiene envíos y no se puede eliminar.';
        } else {
            // TODO(bd): DELETE FROM tienda WHERE id_tienda = :id
            redirigir('panel/tiendas.php?ok=demo&accion=eliminar');
        }
    } else {
        $editando = ($_POST['modo'] ?? '') === 'editar';
        $valores  = [
            'id_tienda' => mb_strtoupper(trim((string) ($_POST['id_tienda'] ?? ''))),
            'nombre'    => trim((string) ($_POST['nombre'] ?? '')),
            'host'      => trim((string) ($_POST['host'] ?? '')),
        ];
        if (mb_strlen($valores['id_tienda']) !== 15) {
            $errores['id_tienda'] = 'El código debe tener exactamente 15 caracteres.';
        } elseif (!$editando && tienda_por_id($valores['id_tienda']) !== null) {
            $errores['id_tienda'] = 'Ya existe una tienda con ese código. Edítela desde la tabla.';
        }
        if ($valores['nombre'] === '') {
            $errores['nombre'] = 'Escriba el nombre de la tienda.';
        }
        if ($valores['host'] === '') {
            $errores['host'] = 'Escriba el host desde el que la tienda llama al WebService.';
        }
        if ($errores === []) {
            // TODO(bd): INSERT INTO tienda (...) VALUES (...) o UPDATE tienda SET ... WHERE id_tienda = :id
            redirigir('panel/tiendas.php?ok=demo&accion=' . ($editando ? 'editar' : 'agregar'));
        }
    }
}

$titulo        = 'Tiendas afiliadas';
$pagina_actual = 'tiendas.php';
require dirname(__DIR__) . '/includes/partes/cabecera_panel.php';
?>
<div class="panel-cabecera">
  <div>
    <h1>Tiendas afiliadas</h1>
    <p>Tiendas virtuales que pueden pedir envíos. El código es el que la tienda manda en cada llamada al WebService.</p>
  </div>
  <a class="boton boton--secundario" href="<?= h(url('panel/tiendas.php')) ?>#formulario">Agregar una tienda</a>
</div>

<?php if ($aviso !== null): ?>
  <?php
  $mensaje_tipo  = 'error';
  $mensaje_texto = 'No se puede eliminar';
  $mensaje_detalle = $aviso;
  require dirname(__DIR__) . '/includes/partes/mensaje.php';
  ?>
<?php elseif (($_GET['ok'] ?? '') === 'demo'): ?>
  <?php
  $mensaje_tipo  = 'vista';
  $mensaje_texto = match ((string) ($_GET['accion'] ?? '')) {
      'editar'   => 'Vista previa: los cambios de la tienda se guardarían cuando conectemos la base de datos.',
      'eliminar' => 'Vista previa: la tienda se eliminaría cuando conectemos la base de datos.',
      default    => 'Vista previa: la tienda se agregaría cuando conectemos la base de datos.',
  };
  $mensaje_detalle = 'Por ahora la tabla no cambia.';
  require dirname(__DIR__) . '/includes/partes/mensaje.php';
  ?>
<?php endif; ?>

<?php if ($tienda_eliminar !== null): ?>
  <section class="confirmacion superficie" aria-labelledby="titulo-eliminar">
    <h2 id="titulo-eliminar">¿Eliminar la tienda <?= h($tienda_eliminar['nombre']) ?>?</h2>
    <p>No tiene envíos, así que se puede eliminar. Esta acción no se puede deshacer.</p>
    <form method="post" action="<?= h(url('panel/tiendas.php')) ?>" class="acciones">
      <input type="hidden" name="accion" value="eliminar">
      <input type="hidden" name="id_tienda" value="<?= h($tienda_eliminar['id_tienda']) ?>">
      <button class="boton boton--peligro" type="submit">Eliminar tienda</button>
      <a class="boton boton--secundario" href="<?= h(url('panel/tiendas.php')) ?>">Cancelar</a>
    </form>
  </section>
<?php endif; ?>

<div class="mantenimiento">
  <section aria-labelledby="titulo-tabla">
    <h2 id="titulo-tabla" class="visualmente-oculto">Tiendas registradas</h2>
    <div class="tabla-contenedor">
      <table class="tabla tabla--apilable" role="table">
        <thead>
          <tr role="row">
            <th scope="col" role="columnheader">Código</th>
            <th scope="col" role="columnheader">Nombre</th>
            <th scope="col" role="columnheader">Host</th>
            <th scope="col" role="columnheader" class="num">Envíos</th>
            <th scope="col" role="columnheader" class="accion"><span class="visualmente-oculto">Acciones</span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (tiendas() as $tienda): ?>
            <?php $envios_tienda = conteo_envios_tienda($tienda['id_tienda']); ?>
            <tr role="row">
              <td role="cell" data-etiqueta="Código" class="codigo"><?= h($tienda['id_tienda']) ?></td>
              <td role="cell" data-etiqueta="Nombre"><?= h($tienda['nombre']) ?></td>
              <td role="cell" data-etiqueta="Host"><?= h($tienda['host']) ?></td>
              <td role="cell" data-etiqueta="Envíos" class="num"><?= $envios_tienda ?></td>
              <td role="cell" data-etiqueta="Acciones" class="accion">
                <div class="acciones-fila">
                  <a class="boton boton--secundario boton--pequeno" href="<?= h(url('panel/tiendas.php?editar=' . rawurlencode($tienda['id_tienda'])) . '#formulario') ?>">Editar<span class="visualmente-oculto"> <?= h($tienda['nombre']) ?></span></a>
                  <?php if ($envios_tienda === 0): ?>
                    <a class="boton boton--peligro boton--pequeno" href="<?= h(url('panel/tiendas.php?eliminar=' . rawurlencode($tienda['id_tienda']))) ?>">Eliminar<span class="visualmente-oculto"> <?= h($tienda['nombre']) ?></span></a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="formulario-mantenimiento superficie" id="formulario" aria-labelledby="titulo-formulario">
    <h2 id="titulo-formulario"><?= $editando ? 'Editar tienda ' . h($valores['nombre']) : 'Agregar tienda' ?></h2>
    <?php if ($errores !== []): ?>
      <?php
      $mensaje_tipo  = 'error';
      $mensaje_texto = 'No se guardó la tienda';
      $mensaje_detalle = 'Corrija los campos marcados.';
      require dirname(__DIR__) . '/includes/partes/mensaje.php';
      ?>
    <?php endif; ?>
    <form method="post" action="<?= h(url('panel/tiendas.php')) ?>">
      <input type="hidden" name="modo" value="<?= $editando ? 'editar' : 'agregar' ?>">
      <div class="campo campo--codigo">
        <label for="id_tienda">Código</label>
        <input type="text" id="id_tienda" name="id_tienda" value="<?= h($valores['id_tienda']) ?>"
               required minlength="15" maxlength="15" autocomplete="off" autocapitalize="characters" spellcheck="false"
               <?= $editando ? 'readonly' : '' ?>
               aria-describedby="<?= isset($errores['id_tienda']) ? 'id_tienda-error' : 'id_tienda-ayuda' ?>"
               <?= isset($errores['id_tienda']) ? 'aria-invalid="true"' : '' ?>>
        <?php if (isset($errores['id_tienda'])): ?>
          <span class="campo__error" id="id_tienda-error"><?= h($errores['id_tienda']) ?></span>
        <?php else: ?>
          <span class="campo__ayuda" id="id_tienda-ayuda"><?= $editando ? 'El código es la llave de la tienda y no se cambia.' : 'Exactamente 15 caracteres, por ejemplo TV-MICOMERCIO01.' ?></span>
        <?php endif; ?>
      </div>
      <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?= h($valores['nombre']) ?>" required maxlength="80"
               <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="nombre-error"' : '' ?>>
        <?php if (isset($errores['nombre'])): ?>
          <span class="campo__error" id="nombre-error"><?= h($errores['nombre']) ?></span>
        <?php endif; ?>
      </div>
      <div class="campo">
        <label for="host">Host</label>
        <input type="text" id="host" name="host" value="<?= h($valores['host']) ?>" required maxlength="120" autocomplete="off" autocapitalize="none" spellcheck="false"
               aria-describedby="<?= isset($errores['host']) ? 'host-error' : 'host-ayuda' ?>"
               <?= isset($errores['host']) ? 'aria-invalid="true"' : '' ?>>
        <?php if (isset($errores['host'])): ?>
          <span class="campo__error" id="host-error"><?= h($errores['host']) ?></span>
        <?php else: ?>
          <span class="campo__ayuda" id="host-ayuda">Dominio o dirección desde la que la tienda llama al WebService.</span>
        <?php endif; ?>
      </div>
      <div class="acciones">
        <button class="boton boton--primario" type="submit">Guardar tienda</button>
        <?php if ($editando): ?>
          <a class="boton boton--secundario" href="<?= h(url('panel/tiendas.php')) ?>">Cancelar edición</a>
        <?php endif; ?>
      </div>
    </form>
  </section>
</div>
<?php require dirname(__DIR__) . '/includes/partes/pie_panel.php'; ?>
