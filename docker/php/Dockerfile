FROM php:7.3-fpm  
RUN docker-php-ext-install mysqli pdo_mysql
RUN apt-get update -y && apt-get install -y sendmail libpng-dev
RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev 
RUN docker-php-ext-install mbstring gd

COPY . /var/www/html/
