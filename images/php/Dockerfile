# Для начала указываем исходный образ, он будет использован как основа
FROM composer:latest AS composer
FROM php:8-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Необязательная строка с указанием автора образа
MAINTAINER PHPtoday.ru <info@phptoday.ru>

# RUN выполняет идущую за ней команду в контексте нашего образа.
# В данном случае мы установим некоторые зависимости и модули PHP.
# Для установки модулей используем команду docker-php-ext-install.
# На каждый RUN создается новый слой в образе, поэтому рекомендуется объединять команды.
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
                 icu-devtools \
                 libicu-dev \
                 libmcrypt4 \
                 libmcrypt-dev \
                 libpng-dev \
                 zlib1g-dev \
                 libxml2-dev \
                 libzip-dev \
            libgd-dev\
            libpq-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_pgsql zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get install -y librabbitmq-dev libssh-dev \
    && pecl install amqp \
    && docker-php-ext-install sockets\
    && docker-php-ext-enable amqp

RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

RUN docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install  \
        intl iconv mbstring \
        zip pcntl \
        exif opcache \
    && docker-php-source delete \
# Куда же без composer'а.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Добавим свой php.ini, можем в нем определять свои значения конфига
#ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
# Указываем рабочую директорию для PHP
WORKDIR /var/www
# Запускаем контейнер
# Из документации: The main purpose of a CMD is to provide defaults for an executing container. These defaults can include an executable,
# or they can omit the executable, in which case you must specify an ENTRYPOINT instruction as well.
CMD ["php-fpm"]