FROM php:8.0.10-fpm-alpine3.14

# docker build -f docker/php/Dockerfile -t serginhold/blog-php:latest .
# docker exec -i -t blog-php sh
# docker exec -i -t blog-php bash

RUN apk add --no-cache bash shadow git

# symfony cli
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    # Make sure we're installing what we think we're installing!
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --version=2.1.6 --snapshot \
    && rm -f /tmp/composer-setup.*

# php
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        # intl
        icu-dev \
        # pgsql
        postgresql-dev \
    && docker-php-ext-install \
        opcache \
        intl \
        pdo_pgsql \
    && pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug \
    && apk del .phpize-deps

WORKDIR /var/www

ENV DOCKER_APP_UID=1000
RUN usermod -u $DOCKER_APP_UID www-data
USER www-data
