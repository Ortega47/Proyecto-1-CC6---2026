<?php
// Insignia de estado. Variables: $insignia_estado (int 1..5).
?>
<span class="insignia insignia--e<?= (int) $insignia_estado ?>"><?= h(etiqueta_estado((int) $insignia_estado)) ?></span>
