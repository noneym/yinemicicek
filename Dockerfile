# YineMiÇiçek — Laravel 10 + Livewire 3 + MariaDB
# Single-stage php:8.2-apache image (no docker-compose required).
# Build:  docker build -t yinemicicek .
# Run:    docker run -d --name yinemicicek -p 8080:80 --env-file .env yinemicicek
FROM php:8.2-apache

ENV APP_ENV=production \
    APP_DEBUG=false \
    APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1

# --- System & PHP extensions ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl unzip ca-certificates \
        libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
        libxml2-dev libcurl4-openssl-dev pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mysqli mbstring exif pcntl bcmath gd zip opcache intl \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# --- Apache: point DocumentRoot at /public, allow .htaccess ---
RUN sed -ri "s!/var/www/html!\${APACHE_DOCUMENT_ROOT}!g" \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
    && printf '<Directory %s>\n  AllowOverride All\n  Require all granted\n</Directory>\n' \
        "$APACHE_DOCUMENT_ROOT" > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# --- PHP production tuning ---
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && { \
        echo "memory_limit=256M"; \
        echo "post_max_size=20M"; \
        echo "upload_max_filesize=20M"; \
        echo "date.timezone=Europe/Istanbul"; \
        echo "opcache.enable=1"; \
        echo "opcache.memory_consumption=128"; \
        echo "opcache.max_accelerated_files=20000"; \
        echo "opcache.validate_timestamps=0"; \
    } > "$PHP_INI_DIR/conf.d/zz-app.ini"

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy dep manifests first to leverage layer cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --no-autoloader

# Copy the rest of the source
COPY . .

# Finalize composer + Laravel
RUN composer dump-autoload --optimize --no-dev \
    && php artisan storage:link || true \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/storage \
    && chmod -R ug+rwX storage bootstrap/cache

# --- Entrypoint: migrate (if DB reachable) + cache config, then serve ---
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://127.0.0.1/ > /dev/null || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
