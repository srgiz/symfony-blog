FROM unit:1.34.1-php8.3

# docker build -f docker/php/Dockerfile -t serginhold/blog-php:latest .
# docker exec -i -t blog-php sh
# docker exec -i -t blog-php bash

ENV DOCKER_APP_UID=1000
ENV XDEBUG_VERSION=3.4.1
ENV REDIS_VERSION=6.1.0

#RUN apk add --no-cache bash shadow git linux-headers

# symfony cli
#RUN wget https://get.symfony.com/cli/installer -O - | bash \
#    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# frontend
#RUN apk add --no-cache --update nodejs npm yarn

# php
RUN apt-get update \
    && apt-get install -y \
        # intl
        libicu-dev \
        # postgres
        libpq-dev \
        # zip
        libzip-dev \
    && pecl install \
        xdebug-$XDEBUG_VERSION \
        redis-$REDIS_VERSION \
    && docker-php-ext-install \
        opcache \
        intl \
        pdo_pgsql \
        zip \
    && docker-php-ext-enable xdebug redis

#RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
#    && apk add --no-cache \
#        # intl
 #       icu-dev \
#        # postgres
 #       libpq-dev php-pdo_pgsql \
        # zip
#        libzip-dev \
 #   && docker-php-ext-install \
#        opcache \
 #       intl \
#        pdo_pgsql \
        # manticore
        #pdo_mysql \
#        zip \
#    && pecl install xdebug-$XDEBUG_VERSION \
 #       redis-$REDIS_VERSION \
 #   && docker-php-ext-enable xdebug \
 #       redis \
 #   && apk del .phpize-deps

COPY ./unit-config.json /docker-entrypoint.d/

COPY --from=composer:2.8.4 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
#RUN usermod -u $DOCKER_APP_UID www-data
#RUN chown -R unit:unit /var/www
#RUN chown -R www-data:www-data /var/www

#USER www-data
