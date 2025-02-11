FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    && apt-get clean

# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo_mysql

# Instalar Composer para gestionar dependencias de Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer