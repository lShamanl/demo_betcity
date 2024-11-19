FROM php:8.3-fpm-alpine

RUN apk add --update linux-headers

RUN apk add --no-cache postgresql-dev fcgi libpng-dev bash coreutils libmcrypt-dev wget git unzip autoconf g++ make rabbitmq-c-dev redis

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions xdebug intl bcmath gd pdo_pgsql pgsql sockets amqp-^1.11.0 zip redis @composer-2 excimer

RUN docker-php-ext-install sysvmsg sysvsem sysvshm

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY ./development/php-fpm/.env.local /app/.env.local
COPY ./development/php-fpm/conf.d /usr/local/etc/php/conf.d

COPY ./common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

COPY ./development/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

WORKDIR /app
