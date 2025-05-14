FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    sqlite-dev \
    su-exec \
    git

RUN docker-php-ext-install -j$(nproc) intl pdo pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
EXPOSE 9000