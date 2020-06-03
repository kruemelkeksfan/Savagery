FROM php:7.3-apache
CMD ip = $(hostname -i | awk '{print $1}')

CMD sed -i -e "s/localhost/$ip/g" /MongoDB.php

COPY . /var/www/html/
