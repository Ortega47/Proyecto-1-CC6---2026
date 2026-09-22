<?php

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
