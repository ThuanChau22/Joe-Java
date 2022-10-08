FROM composer as builder
WORKDIR /
COPY composer.* ./
RUN composer install

FROM php:8.1-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y
WORKDIR /var/www/html
COPY . ./
COPY --from=builder /vendor /var/www/html/vendor

ARG ENV
ARG PORT
RUN sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
RUN mv "$PHP_INI_DIR/php.ini-${ENV}" "$PHP_INI_DIR/php.ini"
RUN a2enmod rewrite