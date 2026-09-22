# -*- coding: utf-8 -*-
"""
Genera manual_usuario.pdf.

Requiere reportlab:      pip install reportlab
Para regenerar el PDF:   python manual_usuario.py

El texto vive en la lista SECCIONES de abajo: para corregir el manual se edita
ahí y se vuelve a ejecutar este archivo. Cada sección es un título seguido de
párrafos ("p"), listas ("lista"), tablas ("tabla") o bloques de código ("codigo").
"""

from reportlab.lib import colors
from reportlab.lib.enums import TA_JUSTIFY
from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (BaseDocTemplate, Frame, KeepTogether, ListFlowable,
                                ListItem, PageTemplate, Paragraph, Preformatted, Spacer,
                                Table, TableStyle)

ARCHIVO = "manual_usuario.pdf"
TITULO = "Manual de usuario"
SISTEMA = "Winni Express"
PIE = "Winni Express · Courier · Proyecto 1 CC6"

# --------------------------------------------------------------------------
# Contenido
# --------------------------------------------------------------------------

ENTRADA = ("Guía básica para usar el sitio de envíos, consultar el estado de las "
           "órdenes y conectar una tienda virtual con el WebService.")

SECCIONES = [
    ("1. Acceso al sistema", [
        ("p", "Abra la página <b>login.php</b>. Escriba su usuario y contraseña y presione "
              "Iniciar sesión. Si aún no tiene cuenta, seleccione Regístrate y complete nombre, "
              "usuario y contraseña. El registro crea una cuenta de cliente y abre directamente "
              "el rastreo."),
        ("p", "El administrador es la cuenta cuyo ID_usuario es 0. Las demás cuentas son "
              "clientes. El registro público nunca crea administradores."),
    ]),

    ("2. Menú principal del administrador", [
        ("p", "Al iniciar sesión como administrador se muestra el menú con los catálogos del "
              "courier. Cada listado permite agregar, editar y eliminar registros cuando no "
              "estén siendo usados por otro registro."),
        ("lista", [
            "<b>Orígenes:</b> ciudades desde donde salen los envíos y su cobertura.",
            "<b>Destinos:</b> ciudades de entrega y su cobertura.",
            "<b>Rutas y tarifas:</b> relación entre origen y destino, costo de envío y costo de manejo.",
            "<b>Tiendas:</b> tienda contratante, número de orden, nombre y host.",
            "<b>Envíos:</b> guías registradas, destinatario, ruta, tienda, costo y estado actual.",
            "<b>Estados:</b> los cinco estados del proceso.",
            "<b>Seguimiento:</b> historial y observaciones de cada cambio de estado.",
        ]),
    ]),

    ("3. Preparar una ruta", [
        ("p", "Antes de registrar un envío, cree el origen, el destino y la tienda. Marque SI en "
              "cobertura para las ciudades que el courier atiende. Luego entre a Rutas y tarifas, "
              "seleccione el origen y destino y escriba los costos."),
        ("p", "El costo total de un envío puede quedar vacío al registrarlo. En ese caso el "
              "sistema usa la suma del costo de envío y el costo de manejo de la ruta."),
    ]),

    ("4. Registrar y administrar un envío", [
        ("p", "En Envíos seleccione Agregar envío. Escriba una guía, fecha, hora, destinatario, "
              "ruta, tienda y costo total opcional. Al guardar, el envío comienza en Orden nueva."),
        ("p", "Desde el listado puede abrir el detalle, editar la información mientras el envío no "
              "haya sido entregado y eliminarlo con la confirmación correspondiente. Al eliminar "
              "un envío también se elimina su historial de seguimiento."),
        ("p", "Los envíos que llegan por el WebService no se registran a mano: el servicio genera "
              "la guía automáticamente y crea el envío en Orden nueva."),
    ]),

    ("5. Cambiar el estado y consultar el seguimiento", [
        ("p", "Use Cambiar estado desde el detalle o desde el listado de envíos. Los estados "
              "avanzan en este orden, sin saltarse ninguno:"),
        ("lista", ["Orden nueva", "Surtiéndose", "Empacándose", "En ruta", "Entregada"]),
        ("p", "Cada avance guarda fecha, hora, usuario y observación. Al llegar a Entregada se "
              "registra la fecha de entrega. En Seguimiento se puede corregir la observación y "
              "eliminar solo el último avance si fue un error."),
    ]),

    ("6. Rastrear un paquete como cliente", [
        ("p", "La página Rastreo permite consultar una guía sin entrar a los catálogos. Escriba el "
              "número de guía y presione Consultar. Se muestran el estado actual, la ruta, la "
              "fecha de entrega y el historial de avances."),
        ("p", "Los clientes solo pueden consultar el rastreo. El administrador es quien mantiene "
              "catálogos, envíos y estados."),
    ]),

    ("7. WebService para tiendas virtuales", [
        ("p", "El courier ofrece tres consultas públicas para que una Tienda Virtual se conecte. "
              "No requieren iniciar sesión y solo aceptan GET. Las respuestas usan XML por "
              "defecto, o JSON cuando se envía <b>formato=JSON</b> (también se acepta en minúsculas)."),
        ("tabla", [
            ["Ruta", "Para qué sirve"],
            ["/consulta?destino=00020&amp;formato=XML",
             "Devuelve si hay cobertura para ese destino y el costo total (envío más manejo)."],
            ["/envio?orden=A-100&amp;destinatario=Ana%20López&amp;destino=00020"
             "&amp;direccion=4a%20calle%205-10&amp;tienda=000000000000099",
             "Solicita un envío, genera la guía y lo crea en Orden nueva."],
            ["/status?orden=A-100&amp;tienda=000000000000099&amp;formato=JSON",
             "Devuelve el estado actual de una orden de esa tienda."],
        ]),
        ("p", "En Render se antepone la URL pública del servicio. Por ejemplo:"),
        ("codigo", "https://proyecto-1-cc6-2026.onrender.com/status?orden=A-100\n"
                   "&amp;tienda=000000000000099&amp;formato=JSON"),
        ("p", "Las rutas largas <b>/ws/consulta.php</b>, <b>/ws/envio.php</b> y <b>/ws/status.php</b> "
              "responden igual, por si la reescritura de Apache no estuviera disponible."),
        ("p", "El destino viaja con 5 caracteres y la tienda y el courier con 15, rellenados con "
              "ceros a la izquierda (el destino 20 se escribe 00020). El código del courier, el "
              "origen fijo y el prefijo de las guías están en <b>ws/config.php</b>. El código de "
              "cada tienda lo asigna el courier al darla de alta."),
        ("p", "La búsqueda de <b>/status</b> es por la pareja orden más tienda: una misma orden de "
              "otra tienda no se encuentra. El campo status devuelve el nombre del estado tal como "
              "está guardado en la tabla Estado."),
    ]),

    ("8. Configuración inicial", [
        ("p", "La aplicación usa la base PostgreSQL existente. En Render configure las variables "
              "DB_HOST, DB_PORT, DB_NAME, DB_USER y DB_PASSWORD con los datos de la base. No "
              "escriba la contraseña en GitHub."),
        ("p", "Para habilitar el WebService, ejecute una sola vez en pgAdmin:"),
        ("codigo", "ALTER TABLE Envio ADD COLUMN Direccion varchar(255);\n"
                   "ALTER TABLE Envio ADD COLUMN No_orden varchar(20);\n"
                   "ALTER TABLE Envio ADD CONSTRAINT uq_envio_orden\n"
                   "    UNIQUE (ID_tienda, No_orden);"),
        ("p", "Esas columnas guardan la dirección y el número de orden que manda la tienda, y la "
              "restricción evita que una misma tienda repita la misma orden."),
        ("p", "Además debe existir el administrador con ID 0 (firma el primer seguimiento de cada "
              "envío del WebService), el origen configurado en ws/config.php con cobertura SI, y "
              "una ruta con tarifa hacia cada destino que se quiera atender."),
    ]),

    ("9. Problemas frecuentes", [
        ("lista", [
            "<b>No se conecta a PostgreSQL:</b> revise las variables DB_* y que la extensión pgsql "
            "de PHP esté activa.",
            "<b>No aparece un costo:</b> confirme que el origen, el destino y la ruta existan y "
            "que origen y destino tengan cobertura SI.",
            "<b>El WebService rechaza una orden:</b> el campo mensaje de la respuesta dice el "
            "motivo. Revise que la tienda y el destino existan y que la pareja tienda más orden no "
            "se haya usado antes.",
            "<b>Una respuesta llega vacía o con parámetros de más:</b> verifique que la URL use "
            "&amp; entre parámetros, sin punto y coma.",
            "<b>Un cliente no ve los catálogos:</b> es el comportamiento esperado; esas pantallas "
            "son solo del administrador.",
        ]),
    ]),
]

# --------------------------------------------------------------------------
# Estilos y armado del documento
# --------------------------------------------------------------------------

base = getSampleStyleSheet()
E = {
    "titulo": ParagraphStyle("titulo", parent=base["Title"], fontSize=19, spaceAfter=4),
    "entrada": ParagraphStyle("entrada", parent=base["Normal"], fontSize=10,
                              textColor=colors.HexColor("#444444"), spaceAfter=14,
                              alignment=TA_JUSTIFY),
    "h2": ParagraphStyle("h2", parent=base["Heading2"], fontSize=12.5, spaceBefore=13,
                         spaceAfter=5, textColor=colors.HexColor("#1B2A4A")),
    "p": ParagraphStyle("p", parent=base["Normal"], fontSize=9.7, leading=13.4,
                        spaceAfter=6, alignment=TA_JUSTIFY),
    "li": ParagraphStyle("li", parent=base["Normal"], fontSize=9.7, leading=13.2,
                         spaceAfter=2),
    "celda": ParagraphStyle("celda", parent=base["Normal"], fontSize=8.3, leading=10.6),
    "celda_cod": ParagraphStyle("celda_cod", parent=base["Normal"], fontSize=8,
                                leading=10.4, fontName="Courier"),
    "codigo": ParagraphStyle("codigo", parent=base["Normal"], fontName="Courier",
                             fontSize=8.3, leading=11.6, leftIndent=10, spaceBefore=2,
                             spaceAfter=8, textColor=colors.HexColor("#1B2A4A")),
    "pie": ParagraphStyle("pie", parent=base["Normal"], fontSize=7.8,
                          textColor=colors.HexColor("#666666")),
}


def pie_de_pagina(canvas, doc):
    canvas.saveState()
    canvas.setFont("Helvetica", 7.8)
    canvas.setFillColor(colors.HexColor("#666666"))
    canvas.drawString(2 * cm, 1.3 * cm, PIE)
    canvas.drawRightString(letter[0] - 2 * cm, 1.3 * cm, "Página %d" % canvas.getPageNumber())
    canvas.setStrokeColor(colors.HexColor("#CCCCCC"))
    canvas.line(2 * cm, 1.75 * cm, letter[0] - 2 * cm, 1.75 * cm)
    canvas.restoreState()


def construir():
    doc = BaseDocTemplate(ARCHIVO, pagesize=letter,
                          leftMargin=2 * cm, rightMargin=2 * cm,
                          topMargin=1.8 * cm, bottomMargin=2.2 * cm,
                          title="%s - %s" % (TITULO, SISTEMA), author=SISTEMA,
                          subject="Proyecto 1 - Ciencias de la Computación VI",
                          creator=SISTEMA)
    marco = Frame(doc.leftMargin, doc.bottomMargin, doc.width, doc.height, id="cuerpo")
    doc.addPageTemplates([PageTemplate(id="normal", frames=[marco], onPage=pie_de_pagina)])

    historia = [Paragraph("%s · %s" % (TITULO, SISTEMA), E["titulo"]),
                Paragraph(ENTRADA, E["entrada"])]

    for titulo, bloques in SECCIONES:
        historia.append(Paragraph(titulo, E["h2"]))
        for tipo, contenido in bloques:
            if tipo == "p":
                historia.append(Paragraph(contenido, E["p"]))
            elif tipo == "lista":
                historia.append(ListFlowable(
                    [ListItem(Paragraph(t, E["li"]), leftIndent=18) for t in contenido],
                    bulletType="bullet", start="\u25cf", bulletFontSize=4.5,
                    bulletOffsetY=2, leftIndent=14, spaceAfter=6))
            elif tipo == "codigo":
                texto = contenido.replace("&amp;", "&")
                historia.append(KeepTogether(Preformatted(texto, E["codigo"])))
            elif tipo == "tabla":
                filas = [[Paragraph("<b>%s</b>" % c, E["celda"]) for c in contenido[0]]]
                for fila in contenido[1:]:
                    filas.append([Paragraph(fila[0], E["celda_cod"]),
                                  Paragraph(fila[1], E["celda"])])
                t = Table(filas, colWidths=[8.4 * cm, 8.4 * cm], hAlign="LEFT")
                t.setStyle(TableStyle([
                    ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1B2A4A")),
                    ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                    ("VALIGN", (0, 0), (-1, -1), "TOP"),
                    ("GRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#CCCCCC")),
                    ("LEFTPADDING", (0, 0), (-1, -1), 5),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 5),
                    ("TOPPADDING", (0, 0), (-1, -1), 4),
                    ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
                ]))
                historia.append(t)
                historia.append(Spacer(1, 7))

    doc.build(historia)
    print("Generado %s" % ARCHIVO)


if __name__ == "__main__":
    construir()
