FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Set Apache document root to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/views

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf


# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug config
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini


# Enable env variables in Apache
RUN echo "PassEnv KEYCLOAK_PUBLIC_URL" >> /etc/apache2/apache2.conf
RUN echo "PassEnv KEYCLOAK_INTERNAL_URL" >> /etc/apache2/apache2.conf

# Copy PHP application
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html
