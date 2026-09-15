# Contrato del WebService (API-REST)

Los nombres de parámetros, etiquetas XML y llaves JSON son **literales**. No se traducen,
no se renombran, no se les cambia la capitalización. Otro grupo consume estas respuestas.

Reglas transversales:

- `destino` = 5 caracteres.
- `tienda` y el identificador del courier = 15 caracteres.
- Fechas `yyyymmdd`, horas `hh:nn`.
- `formato` acepta `XML` o `JSON`.

---

## 1. Consultar costo de envío (lo implementamos NOSOTROS)

```
GET http://courier/consulta?destino=____&formato=____
```

Respuesta XML:

```xml
<consultaprecio>
    <courrier></courrier>
    <destino></destino>
    <cobertura></cobertura>
    <costo></costo>
</consultaprecio>
```

Respuesta JSON:

```json
{"consultaprecio":
    { "courrier": "",
      "destino": "",
      "cobertura": "",
      "costo": ""
    }
}
```

- `cobertura` es `TRUE` o `FALSE`. **Si es FALSE, el costo es 0.**
- `costo` = costo de envío + costo de manejo del destino.
- Ojo con la etiqueta: en el enunciado viene escrita `courrier` (con doble r) dentro de la
  respuesta, aunque el sitio se llame Courier. Respetar tal cual lo que quedó acordado con
  los otros grupos y dejarlo documentado.

---

## 2. Solicitar un envío (lo implementamos NOSOTROS)

```
GET http://courier/envio?orden=____&destinatario=____&destino=____&direccion=____&tienda=____
```

El enunciado no fija el formato de la respuesta de este endpoint. Acordar con los grupos de
Tienda Virtual y devolver, como mínimo, el número de guía generado y si se aceptó el envío.

Al recibirlo:
1. Validar que la tienda exista y esté activa.
2. Validar que el destino exista y tenga cobertura.
3. Verificar que esa tienda no haya mandado ya la misma orden (`id_tienda` + `no_orden` es único).
4. Crear el envío en estado **1 – orden nueva** y su primer registro de seguimiento.

---

## 3. Consultar el estado de una orden (lo implementamos NOSOTROS)

```
GET http://courier/status?orden=____&tienda=____&formato=____
```

Respuesta XML:

```xml
<orden>
    <courrier></courrier>
    <orden></orden>
    <status></status>
</orden>
```

Respuesta JSON:

```json
{"orden":
    { "courrier": "",
      "orden": "",
      "status": ""
    }
}
```

- `status` es el nombre del estado actual del envío (orden nueva, surtiéndose,
  empacándose, en ruta, entregada).
- La búsqueda es por la pareja `orden` + `tienda`, no solo por `orden`.

---

## 4. Autorización de pago (lo implementa el grupo de Tarjeta de Crédito)

Queda aquí solo como referencia; nosotros no lo implementamos.

```
GET http://emisor/autorizacion?tarjeta=____&nombre=____&fecha_venc=____&num_seguridad=____&monto=____&tienda=____&formato=____
```

Respuesta XML:

```xml
<autorizacion>
    <emisor></emisor>
    <tarjeta></tarjeta>
    <status></status>
    <numero></numero>
</autorizacion>
```

Respuesta JSON:

```json
{"autorización":
    { "emisor": "",
      "tarjeta": "",
      "status": "",
      "numero": ""
    }
}
```

- `status` es `APROBADO` o `DENEGADO`. **Si es denegado, el número es 0.**

---

## Pendientes de acordar con los otros grupos

- Host y puerto real de cada sitio.
- Formato de respuesta de `/courier/envio`.
- Cómo se genera y con cuántos caracteres viaja el número de guía.
- Si la tienda manda el código de tienda que nosotros le asignamos o uno propio.
