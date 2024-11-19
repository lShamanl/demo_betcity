#!/bin/bash

FROM --platform=linux/amd64 php:8.2-fpm-alpine as base

WORKDIR /app

RUN apk add --update linux-headers

RUN apk add --no-cache \
    postgresql-dev \
    fcgi \
    libpng-dev \
    rabbitmq-c-dev \
    redis

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions opcache intl bcmath gd pdo_pgsql pgsql sockets amqp-^1.11.0 zip redis excimer

RUN docker-php-ext-install sysvmsg sysvsem sysvshm

FROM base as modules

RUN install-php-extensions @composer-2

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk add --update nodejs yarn

ARG run_env

COPY docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY docker/production/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY docker/production/php-fpm/.env.local /app/.env.local
COPY docker/production/php-fpm/conf.d /usr/local/etc/php/conf.d

COPY . .

RUN composer install --no-dev --prefer-dist --no-progress --optimize-autoloader --no-interaction --no-scripts \
    && bin/console cache:clear \
    && bin/console assets:install \
    && yarn \
    && yarn encore prod \
    && rm -rf node_modules

FROM base as app

ARG run_env

COPY docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY docker/production/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY docker/production/php-fpm/.env.local /app/.env.local
COPY docker/production/php-fpm/conf.d /usr/local/etc/php/conf.d

COPY --from=modules /usr/local/bin/composer /usr/local/bin/composer
COPY --from=modules /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=modules /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=modules /app /app
