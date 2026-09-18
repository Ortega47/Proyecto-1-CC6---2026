# WebService del courier

Tres endpoints públicos, solo `GET`, que responden en XML o JSON según el
parámetro `formato` (`XML` o `JSON`, sin distinguir mayúsculas; cualquier otro
valor o su ausencia responde XML). No usan sesión ni redirigen al login.
Los nombres de raíz y de campos son los del contrato (`03_contrato_webservice.md`),
letra por letra, incluido `courrier` con doble r.

Archivos: `ws/consulta.php`, `ws/envio.php`, `ws/status.php`, con la
configuración en `ws/config.php` y la salida XML/JSON en `ws/formato.php`.
El `.htaccess` de la raíz reescribe `/consulta`, `/envio` y `/status` hacia
esos archivos; las rutas `ws/*.php` también funcionan directo.

Antes de usarlo hay que correr `db/ws_cambios.sql` una vez en pgAdmin (agrega
`Direccion` y `No_orden` a `Envio` y la restricción `uq_envio_orden`).

## URLs de ejemplo

Con `php -S localhost:8000` (el servidor de PHP no lee `.htaccess`, así que se
usa la ruta directa):

```
http://localhost:8000/ws/consulta.php?destino=00007&formato=XML
http://localhost:8000/ws/envio.php?orden=A-100&destinatario=Ana%20L%C3%B3pez&destino=00007&direccion=4a%20calle%20poniente%208,%20Antigua&tienda=000000000000003&formato=JSON
http://localhost:8000/ws/status.php?orden=A-100&tienda=000000000000003&formato=JSON
```

En Render (Apache con `mod_rewrite`; sustituir `courier-cc6` por el nombre real
del servicio):

```
https://courier-cc6.onrender.com/consulta?destino=00007&formato=XML
https://courier-cc6.onrender.com/envio?orden=A-100&destinatario=Ana%20L%C3%B3pez&destino=00007&direccion=4a%20calle%20poniente%208,%20Antigua&tienda=000000000000003
https://courier-cc6.onrender.com/status?orden=A-100&tienda=000000000000003&formato=JSON
```

Las mismas URLs con `/ws/consulta.php`, `/ws/envio.php` y `/ws/status.php`
funcionan en Render aunque la reescritura fallara.

## Códigos de 5 y 15 caracteres

El enunciado pide destino de 5 caracteres y tienda y courier de 15. En la base
los IDs de `Destino` y `Tienda` son enteros, así que:

- **Al responder**, el entero se rellena con ceros a la izquierda:
  destino `7` → `00007`; tienda `3` → `000000000000003`.
- **Al recibir**, se aceptan solo dígitos; se quitan los ceros y se convierte a
  entero: `00007` → `7`. `7` a secas también se acepta. Un valor con letras
  (`GT007`) se trata como destino o tienda inexistente.
- **El código del courier** no está en una tabla: es la constante
  `CODIGO_COURIER` de `ws/config.php` (15 caracteres, provisional
  `000000000000001`).

El origen desde donde salen todos los envíos también es configuración:
`ID_ORIGEN` en `ws/config.php`. `/consulta` y `/envio` buscan la fila de
`Cabeceras` con ese origen y el destino pedido; si hay varias, la de menor
`ID_cabecera`.

## 1. `/consulta?destino=____&formato=____`

Raíz `consultaprecio`.

| Campo | Contenido |
|---|---|
| `courrier` | Código del courier (15 caracteres) |
| `destino` | El destino pedido, con 5 caracteres |
| `cobertura` | `TRUE` o `FALSE`. Es `TRUE` solo si el destino y el origen fijo tienen cobertura (`SI` en la base) y existe una cabecera entre ambos |
| `costo` | `costo_envio + costo_manejo` de la cabecera, con dos decimales. `0.00` si `cobertura` es `FALSE` |

Siempre responde `200` con la estructura completa, aunque el destino no exista
o no tenga ruta. Si la base no responde, devuelve `500` con `cobertura`
`FALSE` y `costo` `0.00`.

```xml
<consultaprecio>
    <courrier>000000000000001</courrier>
    <destino>00007</destino>
    <cobertura>TRUE</cobertura>
    <costo>60.00</costo>
</consultaprecio>
```

## 2. `/envio?orden=&destinatario=&destino=&direccion=&tienda=&formato=`

El contrato no fija la respuesta; se usa la raíz `envio` con estos campos.

| Campo | Contenido |
|---|---|
| `courrier` | Código del courier |
| `orden` | El número de orden recibido (máximo 20 caracteres) |
| `tienda` | La tienda recibida, con 15 caracteres |
| `destino` | El destino recibido, con 5 caracteres |
| `guia` | Número de guía generado (`GUA` + correlativo, 15 caracteres). Vacío si se rechaza |
| `status` | `ACEPTADO` o `RECHAZADO` |
| `costo` | Envío + manejo de la cabecera, dos decimales. `0.00` si se rechaza |
| `fecha` | Fecha de registro en `yyyymmdd`. Vacía si se rechaza |
| `hora` | Hora de registro en `hh:nn`. Vacía si se rechaza |
| `mensaje` | Solo cuando es `RECHAZADO`: el motivo |

Validaciones, en este orden: (a) que vengan los cinco parámetros; (b) que la
tienda exista; (c) que el destino exista y tenga cobertura, igual que el origen
fijo; (d) que exista una cabecera origen–destino; (e) que la tienda no haya
mandado ya esa misma orden. Si todo pasa, en una sola transacción se genera la
guía (`MAX + 1` bajo `LOCK TABLE Envio`), se inserta el envío en estado 1 con
fecha y hora actuales y se registra el primer seguimiento ("Orden recibida de
la tienda", usuario 0). Si algo falla en la transacción, se hace `ROLLBACK`
y se responde `RECHAZADO` con `500`.

```xml
<envio>
    <courrier>000000000000001</courrier>
    <orden>A-100</orden>
    <tienda>000000000000003</tienda>
    <destino>00007</destino>
    <guia>GUA000000000001</guia>
    <status>ACEPTADO</status>
    <costo>60.00</costo>
    <fecha>20260918</fecha>
    <hora>10:32</hora>
</envio>
```

## 3. `/status?orden=____&tienda=____&formato=____`

Raíz `orden`. La búsqueda es por la pareja `Envio.No_orden` + `Envio.ID_tienda`,
nunca solo por orden.

| Campo | Contenido |
|---|---|
| `courrier` | Código del courier |
| `orden` | El número de orden recibido |
| `status` | `Nombre` del estado actual del envío tal como está en la tabla `Estado`; `NO ENCONTRADO` si no existe esa orden para esa tienda; `ERROR` si la base no responde |

```json
{"orden":
    { "courrier": "000000000000001",
      "orden": "A-100",
      "status": "En ruta"
    }
}
```

## Falta acordar con el grupo de Tienda Virtual

- **Largo y formato de la guía.** Hoy es `GUA` + 12 dígitos (15 caracteres);
  la columna admite 20. Confirmar cuántos caracteres esperan ellos.
- **Código de tienda.** Si mandan el ID que nosotros les asignamos (relleno a
  15) o uno propio. El servicio espera el nuestro.
- **Host real** de nuestro sitio (Render) y el de ellos, para el registro
  mutuo de couriers y tiendas.
- **Formato de respuesta de `/envio`**, que el enunciado deja abierto: proponer
  el de arriba y ajustarlo si ellos ya parsean otra estructura.
- **Nombres de estado en `status`.** Se devuelve el `Nombre` de la tabla
  `Estado` tal cual (por ejemplo `Orden nueva`, `En ruta`). Confirmar que su
  pantalla de rastreo los muestre sin transformarlos.
- **Código del courier** definitivo (`CODIGO_COURIER`).
