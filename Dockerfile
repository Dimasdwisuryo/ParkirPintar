# ========================================
# STAGE 1: BASE
# ========================================

FROM php:8.4-apache-bookworm AS base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    libsqlite3-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        zip \
        intl \
        bcmath \
        opcache \
    && a2enmod rewrite \
    && sed -i \
        's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' \
        /etc/apache2/sites-available/000-default.conf \
    && printf \
        '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' \
        > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer


# ========================================
# STAGE 2: TEST
# ========================================

FROM base AS test

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --no-scripts

COPY . .

RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache


# ========================================
# STAGE 3: PRODUCTION
# ========================================

FROM base AS production

ENV APP_ENV=production
ENV APP_DEBUG=false

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --no-scripts \
    --optimize-autoloader

COPY . .

RUN composer dump-autoload \
        --no-dev \
        --optimize \
    && mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]

