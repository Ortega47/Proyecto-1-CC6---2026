<?php
$raiz = '';
require __DIR__ . '/funciones.php';
require __DIR__ . '/datos_demo.php';

$titulo = 'Rastree su paquete';
require __DIR__ . '/encabezado.php';
?>
<div class="contenido angosto">
    <h1>Rastree su paquete</h1>
    <p>Escriba el número de guía que le dio la tienda para ver en qué etapa va su envío.</p>

    <form method="get" action="rastreo.php">
        <label for="guia">Número de guía</label>
        <input type="text" id="guia" name="guia" required maxlength="15" autocomplete="off" autocapitalize="characters" spellcheck="false">
        <p class="ayuda">Son 15 caracteres. Lo encuentra en la confirmación de su compra.</p>
        <button type="submit">Rastrear paquete</button>
    </form>

    <h2>Destinos que cubrimos</h2>
    <p>El costo incluye el envío y el manejo del paquete.</p>
    <div class="tabla">
        <table>
            <tr>
                <th scope="col">Ciudad</th>
                <th scope="col" class="num">Costo</th>
            </tr>
            <?php foreach ($demo['destino'] as $destino): ?>
                <tr>
                    <td><?= h($destino['ciudad']) ?></td>
                    <?php if ($destino['cobertura']): ?>
                        <td class="num"><?= h(moneda($destino['costo_envio'] + $destino['costo_manejo'])) ?></td>
                    <?php else: ?>
                        <td class="num">Sin cobertura por ahora</td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php require __DIR__ . '/pie.php'; ?>
