FROM php:8.2-apache

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP application
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html
