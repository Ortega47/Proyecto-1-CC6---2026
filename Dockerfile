# Render usa este archivo para ejecutar el sitio PHP.
FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libonig-dev \
    && docker-php-ext-install pgsql mbstring \
    && rm -rf /var/lib/apt/lists/*

# El WebService usa /consulta, /envio y /status: se habilita mod_rewrite y se
# permite el .htaccess de la raíz (AllowOverride All) para reescribirlas a ws/*.php.
RUN a2enmod rewrite \
    && printf '<Directory /var/www/html>\n    AllowOverride All\n</Directory>\n' > /etc/apache2/conf-available/reescritura.conf \
    && a2enconf reescritura

COPY . /var/www/html/

ENV PORT=10000
EXPOSE 10000

# Apache escucha en el puerto que asigna Render.
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT}>/\" /etc/apache2/sites-available/000-default.conf && exec apache2-foreground"]
