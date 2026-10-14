FROM php:8.3-apache

# GD with JPEG + WebP support for thumbnails.
RUN apt-get update \
 && apt-get install -y --no-install-recommends libjpeg62-turbo-dev libpng-dev libwebp-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" gd \
 && apt-get purge -y --auto-remove \
 && rm -rf /var/lib/apt/lists/*

# Production PHP settings (no error details leaked to visitors).
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && printf 'expose_php = Off\nmemory_limit = 512M\n' > "$PHP_INI_DIR/conf.d/derppic.ini"

COPY docker/apache.conf /etc/apache2/conf-enabled/derppic.conf
RUN a2enmod headers expires >/dev/null

COPY public/ /var/www/html/

# Media is mounted at /srv/media (read-only); thumbnails are cached in /srv/cache.
RUN mkdir -p /srv/media /srv/cache && chown www-data:www-data /srv/cache

VOLUME ["/srv/cache"]
EXPOSE 80
