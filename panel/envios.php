<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/consultas.php';

$filtros = [
    'estado'  => (string) ($_GET['estado'] ?? ''),
    'tienda'  => (string) ($_GET['tienda'] ?? ''),
    'destino' => (string) ($_GET['destino'] ?? ''),
    'q'       => trim((string) ($_GET['q'] ?? '')),
];
$hay_filtros = array_filter($filtros) !== [];
$envios      = envios_filtrados($filtros);
$total       = count(envios_todos());

$titulo = 'Envíos contratados';
$pagina_actual = 'envios.php';
require dirname(__DIR__) . '/includes/partes/cabecera_panel.php';
?>
<div class="panel-cabecera">
  <div>
    <h1>Envíos contratados</h1>
    <p>Todos los envíos que las tiendas han pedido, en cualquier etapa.</p>
  </div>
</div>

<form class="filtros superficie" method="get" action="<?= h(url('panel/envios.php')) ?>" aria-label="Filtrar envíos">
  <div class="campo">
    <label for="f-estado">Estado</label>
    <select id="f-estado" name="estado">
      <option value="">Todos</option>
      <?php foreach (estados() as $estado): ?>
        <option value="<?= (int) $estado['id_estado'] ?>"<?= $filtros['estado'] === (string) $estado['id_estado'] ? ' selected' : '' ?>><?= h(etiqueta_estado((int) $estado['id_estado'])) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="campo">
    <label for="f-tienda">Tienda</label>
    <select id="f-tienda" name="tienda">
      <option value="">Todas</option>
      <?php foreach (tiendas() as $tienda): ?>
        <option value="<?= h($tienda['id_tienda']) ?>"<?= $filtros['tienda'] === $tienda['id_tienda'] ? ' selected' : '' ?>><?= h($tienda['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="campo">
    <label for="f-destino">Destino</label>
    <select id="f-destino" name="destino">
      <option value="">Todos</option>
      <?php foreach (destinos() as $destino): ?>
        <option value="<?= h($destino['id_destino']) ?>"<?= $filtros['destino'] === $destino['id_destino'] ? ' selected' : '' ?>><?= h($destino['id_destino']) ?> · <?= h($destino['ciudad']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="campo">
    <label for="f-q">Guía u orden</label>
    <input type="search" id="f-q" name="q" value="<?= h($filtros['q']) ?>" autocomplete="off" spellcheck="false">
  </div>
  <div class="acciones">
    <button class="boton boton--primario" type="submit">Filtrar envíos</button>
    <?php if ($hay_filtros): ?>
      <a class="boton boton--secundario" href="<?= h(url('panel/envios.php')) ?>">Limpiar filtros</a>
    <?php endif; ?>
  </div>
</form>

<p class="filtros__resumen">
  <?php if ($hay_filtros): ?>
    <?= count($envios) ?> de <?= $total ?> envíos coinciden con los filtros.
  <?php else: ?>
    <?= $total ?> envíos en total.
  <?php endif; ?>
</p>

<?php
$tabla_envios       = $envios;
$tabla_vacio        = 'Ningún envío coincide con estos filtros';
$tabla_vacio_enlace = ['panel/envios.php', 'Limpiar filtros'];
require dirname(__DIR__) . '/includes/partes/tabla_envios.php';
?>
<?php require dirname(__DIR__) . '/includes/partes/pie_panel.php'; ?>
