FROM php:5.6-apache as trovster_creation

RUN a2enmod rewrite

RUN apt-get update \
  && docker-php-ext-install pdo pdo_mysql mysql mysqli

COPY ./ /var/www/html/

EXPOSE 80
