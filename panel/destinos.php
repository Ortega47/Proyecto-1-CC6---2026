<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

$editar   = trim((string) ($_GET['editar'] ?? ''));
$eliminar = trim((string) ($_GET['eliminar'] ?? ''));
$errores  = [];
$aviso    = null;

// Valores del formulario (vacío = agregar).
$valores = ['id_destino' => '', 'ciudad' => '', 'cobertura' => true, 'costo_envio' => '', 'costo_manejo' => ''];
$editando = false;

if ($editar !== '') {
    $destino_editar = destino_por_id($editar);
    if ($destino_editar === null) {
        $regreso = ['panel/destinos.php', 'Volver a destinos'];
        require dirname(__DIR__) . '/404.php';
        exit;
    }
    $valores  = $destino_editar;
    $editando = true;
}

$destino_eliminar = null;
if ($eliminar !== '') {
    $destino_eliminar = destino_por_id($eliminar);
    if ($destino_eliminar === null) {
        $regreso = ['panel/destinos.php', 'Volver a destinos'];
        require dirname(__DIR__) . '/404.php';
        exit;
    }
    if (conteo_envios_destino($eliminar) > 0) {
        $n = conteo_envios_destino($eliminar);
        $aviso = sprintf('%s · %s tiene %d %s y no se puede eliminar. Para retirarlo, edítelo y apague la cobertura.',
            $destino_eliminar['id_destino'], $destino_eliminar['ciudad'], $n, $n === 1 ? 'envío' : 'envíos');
        $destino_eliminar = null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string) ($_POST['accion'] ?? 'guardar');

    if ($accion === 'eliminar') {
        $id = trim((string) ($_POST['id_destino'] ?? ''));
        if (destino_por_id($id) === null) {
            $regreso = ['panel/destinos.php', 'Volver a destinos'];
            require dirname(__DIR__) . '/404.php';
            exit;
        }
        if (conteo_envios_destino($id) > 0) {
            $aviso = 'El destino ' . $id . ' tiene envíos y no se puede eliminar.';
        } else {
            // TODO(bd): DELETE FROM destino WHERE id_destino = :id
            redirigir('panel/destinos.php?ok=demo&accion=eliminar');
        }
    } else {
        $editando = ($_POST['modo'] ?? '') === 'editar';
        $valores  = [
            'id_destino'   => mb_strtoupper(trim((string) ($_POST['id_destino'] ?? ''))),
            'ciudad'       => trim((string) ($_POST['ciudad'] ?? '')),
            'cobertura'    => isset($_POST['cobertura']),
            'costo_envio'  => trim((string) ($_POST['costo_envio'] ?? '')),
            'costo_manejo' => trim((string) ($_POST['costo_manejo'] ?? '')),
        ];
        if (mb_strlen($valores['id_destino']) !== 5) {
            $errores['id_destino'] = 'El código debe tener exactamente 5 caracteres.';
        } elseif (!$editando && destino_por_id($valores['id_destino']) !== null) {
            $errores['id_destino'] = 'Ya existe un destino con ese código. Edítelo desde la tabla.';
        }
        if ($valores['ciudad'] === '') {
            $errores['ciudad'] = 'Escriba el nombre de la ciudad.';
        }
        foreach (['costo_envio' => 'costo de envío', 'costo_manejo' => 'costo de manejo'] as $campo => $nombre) {
            if ($valores[$campo] === '' || !is_numeric($valores[$campo]) || (float) $valores[$campo] < 0) {
                $errores[$campo] = 'Escriba el ' . $nombre . ' como un número mayor o igual a 0.';
            }
        }
        if ($errores === []) {
            // TODO(bd): INSERT INTO destino (...) VALUES (...) o UPDATE destino SET ... WHERE id_destino = :id
            redirigir('panel/destinos.php?ok=demo&accion=' . ($editando ? 'editar' : 'agregar'));
        }
    }
}

$titulo        = 'Destinos';
$pagina_actual = 'destinos.php';
require dirname(__DIR__) . '/includes/partes/cabecera_panel.php';
?>
<div class="panel-cabecera">
  <div>
    <h1>Destinos</h1>
    <p>Ciudades a las que llegamos y lo que cobra el courier por cada una. Para retirar un destino, apague su cobertura.</p>
  </div>
  <a class="boton boton--secundario" href="<?= h(url('panel/destinos.php')) ?>#formulario">Agregar un destino</a>
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
      'editar'   => 'Vista previa: los cambios del destino se guardarían cuando conectemos la base de datos.',
      'eliminar' => 'Vista previa: el destino se eliminaría cuando conectemos la base de datos.',
      default    => 'Vista previa: el destino se agregaría cuando conectemos la base de datos.',
  };
  $mensaje_detalle = 'Por ahora la tabla no cambia.';
  require dirname(__DIR__) . '/includes/partes/mensaje.php';
  ?>
<?php endif; ?>

<?php if ($destino_eliminar !== null): ?>
  <section class="confirmacion superficie" aria-labelledby="titulo-eliminar">
    <h2 id="titulo-eliminar">¿Eliminar el destino <?= h($destino_eliminar['id_destino']) ?> · <?= h($destino_eliminar['ciudad']) ?>?</h2>
    <p>No tiene envíos, así que se puede eliminar. Esta acción no se puede deshacer; si solo quiere dejar de cubrirlo, mejor apague la cobertura.</p>
    <form method="post" action="<?= h(url('panel/destinos.php')) ?>" class="acciones">
      <input type="hidden" name="accion" value="eliminar">
      <input type="hidden" name="id_destino" value="<?= h($destino_eliminar['id_destino']) ?>">
      <button class="boton boton--peligro" type="submit">Eliminar destino</button>
      <a class="boton boton--secundario" href="<?= h(url('panel/destinos.php')) ?>">Cancelar</a>
    </form>
  </section>
<?php endif; ?>

<div class="mantenimiento">
  <section aria-labelledby="titulo-tabla">
    <h2 id="titulo-tabla" class="visualmente-oculto">Destinos registrados</h2>
    <div class="tabla-contenedor">
      <table class="tabla tabla--apilable" role="table">
        <thead>
          <tr role="row">
            <th scope="col" role="columnheader">Código</th>
            <th scope="col" role="columnheader">Ciudad</th>
            <th scope="col" role="columnheader">Cobertura</th>
            <th scope="col" role="columnheader" class="num">Costo de envío</th>
            <th scope="col" role="columnheader" class="num">Costo de manejo</th>
            <th scope="col" role="columnheader" class="num">Total</th>
            <th scope="col" role="columnheader" class="accion"><span class="visualmente-oculto">Acciones</span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (destinos() as $destino): ?>
            <?php $envios_destino = conteo_envios_destino($destino['id_destino']); ?>
            <tr role="row">
              <td role="cell" data-etiqueta="Código" class="codigo"><?= h($destino['id_destino']) ?></td>
              <td role="cell" data-etiqueta="Ciudad"><?= h($destino['ciudad']) ?></td>
              <td role="cell" data-etiqueta="Cobertura"><?php if ($destino['cobertura']): ?>Con cobertura<?php else: ?><span class="cobertura-no">Sin cobertura</span><?php endif; ?></td>
              <td role="cell" data-etiqueta="Costo de envío" class="num"><?= h(moneda($destino['costo_envio'])) ?></td>
              <td role="cell" data-etiqueta="Costo de manejo" class="num"><?= h(moneda($destino['costo_manejo'])) ?></td>
              <td role="cell" data-etiqueta="Total" class="num"><?= h(moneda($destino['costo_envio'] + $destino['costo_manejo'])) ?></td>
              <td role="cell" data-etiqueta="Acciones" class="accion">
                <div class="acciones-fila">
                  <a class="boton boton--secundario boton--pequeno" href="<?= h(url('panel/destinos.php?editar=' . rawurlencode($destino['id_destino'])) . '#formulario') ?>">Editar<span class="visualmente-oculto"> <?= h($destino['id_destino']) ?></span></a>
                  <?php if ($envios_destino === 0): ?>
                    <a class="boton boton--peligro boton--pequeno" href="<?= h(url('panel/destinos.php?eliminar=' . rawurlencode($destino['id_destino']))) ?>">Eliminar<span class="visualmente-oculto"> <?= h($destino['id_destino']) ?></span></a>
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
    <h2 id="titulo-formulario"><?= $editando ? 'Editar destino ' . h($valores['id_destino']) : 'Agregar destino' ?></h2>
    <?php if ($errores !== []): ?>
      <?php
      $mensaje_tipo  = 'error';
      $mensaje_texto = 'No se guardó el destino';
      $mensaje_detalle = 'Corrija los campos marcados.';
      require dirname(__DIR__) . '/includes/partes/mensaje.php';
      ?>
    <?php endif; ?>
    <form method="post" action="<?= h(url('panel/destinos.php')) ?>">
      <input type="hidden" name="modo" value="<?= $editando ? 'editar' : 'agregar' ?>">
      <div class="campo campo--codigo">
        <label for="id_destino">Código</label>
        <input type="text" id="id_destino" name="id_destino" value="<?= h($valores['id_destino']) ?>"
               required minlength="5" maxlength="5" autocomplete="off" autocapitalize="characters" spellcheck="false"
               <?= $editando ? 'readonly' : '' ?>
               aria-describedby="<?= isset($errores['id_destino']) ? 'id_destino-error' : 'id_destino-ayuda' ?>"
               <?= isset($errores['id_destino']) ? 'aria-invalid="true"' : '' ?>>
        <?php if (isset($errores['id_destino'])): ?>
          <span class="campo__error" id="id_destino-error"><?= h($errores['id_destino']) ?></span>
        <?php else: ?>
          <span class="campo__ayuda" id="id_destino-ayuda"><?= $editando ? 'El código es la llave del destino y no se cambia.' : 'Exactamente 5 caracteres, por ejemplo GT006.' ?></span>
        <?php endif; ?>
      </div>
      <div class="campo">
        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" value="<?= h($valores['ciudad']) ?>" required maxlength="80"
               <?= isset($errores['ciudad']) ? 'aria-invalid="true" aria-describedby="ciudad-error"' : '' ?>>
        <?php if (isset($errores['ciudad'])): ?>
          <span class="campo__error" id="ciudad-error"><?= h($errores['ciudad']) ?></span>
        <?php endif; ?>
      </div>
      <div class="campo">
        <span class="campo__rotulo" id="cobertura-rotulo">Cobertura</span>
        <label class="interruptor">
          <input type="checkbox" name="cobertura" value="1" aria-describedby="cobertura-ayuda"<?= $valores['cobertura'] ? ' checked' : '' ?>>
          <span class="interruptor__pista" aria-hidden="true"></span>
          <span class="interruptor__si">Con cobertura</span>
          <span class="interruptor__no">Sin cobertura</span>
        </label>
        <span class="campo__ayuda" id="cobertura-ayuda">Sin cobertura, la tienda recibe costo 0.</span>
      </div>
      <div class="formulario-filas formulario-filas--2">
        <div class="campo">
          <label for="costo_envio">Costo de envío (Q)</label>
          <input type="number" id="costo_envio" name="costo_envio" value="<?= h($valores['costo_envio']) ?>" step="0.01" min="0" required inputmode="decimal"
                 <?= isset($errores['costo_envio']) ? 'aria-invalid="true" aria-describedby="costo_envio-error"' : '' ?>>
          <?php if (isset($errores['costo_envio'])): ?>
            <span class="campo__error" id="costo_envio-error"><?= h($errores['costo_envio']) ?></span>
          <?php endif; ?>
        </div>
        <div class="campo">
          <label for="costo_manejo">Costo de manejo (Q)</label>
          <input type="number" id="costo_manejo" name="costo_manejo" value="<?= h($valores['costo_manejo']) ?>" step="0.01" min="0" required inputmode="decimal"
                 <?= isset($errores['costo_manejo']) ? 'aria-invalid="true" aria-describedby="costo_manejo-error"' : '' ?>>
          <?php if (isset($errores['costo_manejo'])): ?>
            <span class="campo__error" id="costo_manejo-error"><?= h($errores['costo_manejo']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="acciones">
        <button class="boton boton--primario" type="submit">Guardar destino</button>
        <?php if ($editando): ?>
          <a class="boton boton--secundario" href="<?= h(url('panel/destinos.php')) ?>">Cancelar edición</a>
        <?php endif; ?>
      </div>
    </form>
  </section>
</div>
<?php require dirname(__DIR__) . '/includes/partes/pie_panel.php'; ?>
