FROM php:8.5-cli-alpine AS base

# 1) Системные пакеты
#    linux-headers — ОБЯЗАТЕЛЕН для компиляции sockets в PHP 8.5 (нужен linux/sock_diag.h)
RUN apk add --no-cache \
        curl \
        git \
        unzip \
        libpq \
        libpq-dev \
        linux-headers \
        $PHPIZE_DEPS \
    # 2) PHP-расширения (opcache в 8.5 встроен — его тут быть не должно)
    && docker-php-ext-install \
        pcntl \
        posix \
        sockets \
        pdo_pgsql \
        bcmath \
    # 3) Чистим только сборочные пакеты; libpq остаётся для рантайма
    && apk del \
        libpq-dev \
        linux-headers \
        $PHPIZE_DEPS

# 4) Включаем и настраиваем встроенный OPcache
RUN { \
      echo 'opcache.enable=1'; \
      echo 'opcache.enable_cli=1'; \
      echo 'opcache.memory_consumption=256'; \
      echo 'opcache.max_accelerated_files=20000'; \
      echo 'opcache.validate_timestamps=0'; \
      echo 'opcache.interned_strings_buffer=16'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN ./vendor/bin/rr get-binary && chmod +x ./rr

RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000"]
