<?php
    // Configuración del WebService.

    // Código del courier: 15 caracteres. Viaja en el campo "courrier" de las
    // respuestas y es el que las tiendas virtuales guardan para identificarnos.
    // Provisional
    define("CODIGO_COURIER", "000000000000001");

    // ID_origen (tabla Origen) desde donde salen todos los envíos.
    // /consulta y /envio buscan la Cabecera de este origen hacia el destino pedido.
    define("ID_ORIGEN", 1);

    // Prefijo del número de guía. Después del prefijo va un correlativo con ceros
    // a la izquierda hasta completar 15 caracteres (No_guia admite 20).
    define("PREFIJO_GUIA", "GUA");
?>
