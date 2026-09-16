FROM php:8.2-apache

# Extensiones de PostgreSQL (el commit de base de datos usará pg_query_params)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

EXPOSE 80
