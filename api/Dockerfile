FROM php:7.3-apache

RUN touch log.txt

RUN chown 777 log.txt

RUN sed -i -e 's/localhost/XYZ/g' /MongoDB.php

RUN docker-php-ext-install mysqli pdo pdo_mysql

CMD ip = $(hostname -i | awk '{print $1}')

CMD sed -i -e "s/localhost/$ip/g" /MongoDB.php

#RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev

RUN apt-get update && apt-get install -y openssl

RUN pecl install mongodb && docker-php-ext-enable mongodb