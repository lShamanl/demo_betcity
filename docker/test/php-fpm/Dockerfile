#!/bin/bash

FROM php:8.2-fpm-alpine

RUN apk add --update linux-headers

RUN apk add --no-cache postgresql-dev fcgi libpng-dev bash coreutils libmcrypt-dev wget git unzip autoconf g++ make rabbitmq-c-dev redis nodejs yarn

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions xdebug intl bcmath gd pdo_pgsql pgsql sockets amqp-^1.11.0 zip redis @composer-2

RUN docker-php-ext-install sysvmsg sysvsem sysvshm

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk add --update nodejs yarn

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY docker/test/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY docker/test/php-fpm/.env.local /app/.env.local
COPY docker/test/php-fpm/conf.d /usr/local/etc/php/conf.d

COPY docker/common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

COPY docker/test/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

WORKDIR /app

COPY . .
