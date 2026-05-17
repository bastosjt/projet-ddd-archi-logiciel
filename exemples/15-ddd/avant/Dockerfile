FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_sqlite \
        intl \
        opcache \
        mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

RUN mkdir -p var && chown -R www-data:www-data var/

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
