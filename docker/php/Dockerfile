FROM php:8.0.8-fpm-alpine3.13

# docker build -f docker/php/Dockerfile -t serginhold/blog:latest .
# docker exec -i -t blog bash -c "cd /var/www; bash"
# docker exec -i -t blog bash -c "cd /var/www; sh"

RUN apk --update add bash shadow git

# php
RUN apk --update add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        # intl
        icu-dev \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install \
        opcache \
        pdo_mysql \
        intl \
    && pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug \
    && apk del .phpize-deps

# composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
# Make sure we're installing what we think we're installing!
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --version=2.1.3 --snapshot \
&& rm -f /tmp/composer-setup.*

RUN usermod -u 1000 www-data
USER www-data
