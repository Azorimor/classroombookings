FROM php:7.3-fpm  
RUN apt-get update -y && apt-get install -y sendmail libpng-dev zlib1g-dev

RUN docker-php-ext-install mysqli pdo_mysql gd
