FROM php:8.3.6-fpm-alpine AS base

# WORKDIR - the directory from which the application will be served
WORKDIR /var/www/

# Install system dependencies
RUN apk add --no-cache --virtual .deps \
    autoconf \
    make \
    g++ \
    linux-headers \
    icu-dev

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu \
    libzip-dev \
    openssl-dev \
    sed \
    supervisor \
    unzip \
    wget

# Install Docker-PHP Extensions
RUN docker-php-ext-install \
    intl \
    opcache \
    pcntl \
    pdo \
    pdo_mysql \
    zip

RUN docker-php-ext-configure intl --enable-intl
RUN docker-php-ext-configure pcntl --enable-pcntl

# Install APCu and Xdebug
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
     && pecl channel-update pecl.php.net \
     && pecl install apcu-5.1.23 \
     && pecl install igbinary-3.2.15 \
     && pecl bundle redis-6.0.2 \
        && cd redis \
        && phpize \
        && ./configure --enable-redis-igbinary \
        && make \
        && make install \
     && pecl install xdebug-3.3.2 \
     && docker-php-ext-enable \
        apcu \
        igbinary \
        redis \
        xdebug \
    && apk del --purge autoconf g++ make

RUN apk del .deps

# Clear cache and temp files
RUN pecl clear-cache && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/pear

# EXPOSE PORT - Expose default port at which the application will respond
EXPOSE 9000

# Auto accept any terms of service
ENV ACCEPT_EULA=Y

# Set environment variable
ENV PATH="${PATH}:/var/www/bin"

FROM composer:2.4.2 AS build

FROM base AS final

# Copy rootfilesystem folder to the docker container
COPY environment/php/rootfilesystem/ /

# Config INET HTTP server host (supervisor)
ARG INET_HTTP_SERVER_HOST="nerd-time-php:9001"
EXPOSE 9001
RUN sed -i "s/{INET_HTTP_SERVER_HOST}/$INET_HTTP_SERVER_HOST/" /etc/supervisord.conf

# XDEBUG - Config
ENV PHP_IDE_CONFIG     "serverName=docker"
ENV XDEBUG_CONFIG      "idekey=nerd-time"
ENV XDEBUG_SESSION     "nerd-time"
ENV XDEBUG_MODE        "develop,debug,coverage"
ENV XDEBUG_CLIENT_PORT  9003
ENV XDEBUG_CLIENT_HOST "host.docker.internal"
ENV XDEBUG_IDE_KEY     "nerd-time"

RUN sed -i "s/{XDEBUG_MODE}/$XDEBUG_MODE/" /usr/local/etc/php/conf.d/99-xdebug.ini \
 && sed -i "s/{XDEBUG_CLIENT_PORT}/$XDEBUG_CLIENT_PORT/" /usr/local/etc/php/conf.d/99-xdebug.ini \
 && sed -i "s/{XDEBUG_CLIENT_HOST}/$XDEBUG_CLIENT_HOST/" /usr/local/etc/php/conf.d/99-xdebug.ini \
 && sed -i "s/{XDEBUG_IDE_KEY}/$XDEBUG_IDE_KEY/" /usr/local/etc/php/conf.d/99-xdebug.ini

# Get Composer
COPY --from=build /usr/bin/composer /usr/bin/composer

STOPSIGNAL SIGINT

ENTRYPOINT ["/entrypoint.sh"]
