FROM php:8.3-fpm-alpine

# 1. Установка системных зависимостей
RUN apk add --no-cache \
    git unzip curl \
    # Основные зависимости
    libzip-dev libxml2-dev oniguruma-dev \
    # Для GD (если понадобится)
    freetype-dev libjpeg-turbo-dev libpng-dev \
    # Для сборки
    re2c bison $PHPIZE_DEPS

# 2. Установка основных расширений (по одному)
RUN docker-php-ext-install opcache
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install xml
#RUN docker-php-ext-install tokenizer todo

# 3. Установка остальных расширений
RUN docker-php-ext-install pdo_mysql zip bcmath pcntl

# 4. Установка GD (если нужно)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# 4(5). Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# 5. Копирование конфигов
COPY php.ini /usr/local/etc/php/conf.d/
COPY xdebug.ini /usr/local/etc/php/conf.d/

# todo
# 6. Xdebug (опционально)
#ARG XDEBUG_ENABLED=false
#RUN if [ "$XDEBUG_ENABLED" = "true" ]; then \
#    pecl install xdebug && \
#    docker-php-ext-enable xdebug; \
#fi
RUN apk add --no-cache linux-headers ${PHPIZE_DEPS}
RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/html