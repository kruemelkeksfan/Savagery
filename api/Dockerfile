FROM php:7.3-apache

RUN touch log.txt

RUN chown 777 log.txt

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN pecl install mongo \
    && docker-php-ext-enable mongo