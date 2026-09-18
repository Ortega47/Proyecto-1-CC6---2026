<?php
    // Salida del WebService. Única excepción del proyecto a "sin funciones propias":
    // dos funciones chicas que imprimen la respuesta en XML o en JSON, las dos a mano,
    // sin json_encode ni librerías de XML.
    // Reciben el nombre de la raíz y un arreglo asociativo campo => valor,
    // y respetan el orden del arreglo. Todos los valores salen como texto.

    function responder_xml($raiz, $campos) {
        header("Content-Type: application/xml; charset=UTF-8");
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo "<" . $raiz . ">\n";
        foreach ($campos as $nombre => $valor) {
            $valor = htmlspecialchars((string) $valor, ENT_XML1 | ENT_QUOTES, "UTF-8");
            echo "    <" . $nombre . ">" . $valor . "</" . $nombre . ">\n";
        }
        echo "</" . $raiz . ">\n";
    }

    function responder_json($raiz, $campos) {
        header("Content-Type: application/json; charset=UTF-8");
        echo "{\n";
        echo '    "' . $raiz . '": {' . "\n";
        $pendientes = count($campos);
        foreach ($campos as $nombre => $valor) {
            // Dentro de una cadena JSON solo hay que escapar la barra invertida,
            // las comillas dobles y los saltos de línea o tabulaciones.
            $valor = str_replace(["\\", '"', "\r", "\n", "\t"], ["\\\\", '\\"', "\\r", "\\n", "\\t"], (string) $valor);
            echo '        "' . $nombre . '": "' . $valor . '"';
            $pendientes = $pendientes - 1;
            if ($pendientes > 0) {
                echo ",";
            }
            echo "\n";
        }
        echo "    }\n";
        echo "}\n";
    }
?>
