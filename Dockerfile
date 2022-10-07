FROM composer as builder
WORKDIR /
COPY composer.* ./
RUN composer install

FROM php:8.1-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y
WORKDIR /var/www/html
COPY . ./
COPY --from=builder /vendor /var/www/html/src/vendor
EXPOSE 80