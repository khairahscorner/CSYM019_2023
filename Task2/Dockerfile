FROM php:7.4-apache

# pdo_mysql package
RUN docker-php-ext-install pdo_mysql

COPY src/. /var/www/html/
