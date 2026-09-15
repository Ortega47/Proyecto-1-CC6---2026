<?php
// Campo de guía del sitio público. Lo usan index.php y rastreo.php.
// Variables opcionales: $rastreo_valor (string), $rastreo_error (?string).
$rastreo_valor = $rastreo_valor ?? '';
$rastreo_error = $rastreo_error ?? null;
?>
<form class="rastreo" action="<?= h(url('rastreo.php')) ?>" method="get">
  <div class="campo campo--codigo">
    <label for="guia">Número de guía</label>
    <input type="text" id="guia" name="guia" required maxlength="15"
           autocomplete="off" autocapitalize="characters" spellcheck="false"
           value="<?= h($rastreo_valor) ?>"
           aria-describedby="<?= $rastreo_error !== null ? 'guia-error' : 'guia-ayuda' ?>"
           <?= $rastreo_error !== null ? 'aria-invalid="true"' : '' ?>>
    <?php if ($rastreo_error !== null): ?>
      <span class="campo__error" id="guia-error"><?= h($rastreo_error) ?></span>
    <?php else: ?>
      <span class="campo__ayuda" id="guia-ayuda">Son 15 caracteres. Lo encuentra en la confirmación de su compra.</span>
    <?php endif; ?>
  </div>
  <button class="boton boton--primario boton--ancho" type="submit">Rastrear paquete</button>
</form>
