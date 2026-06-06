FROM php:5.6-apache as trovster_creation

RUN sed -i s/deb.debian.org/archive.debian.org/g /etc/apt/sources.list
RUN sed -i s/security.debian.org/archive.debian.org/g /etc/apt/sources.list
RUN sed -i s/stretch-updates/stretch/g /etc/apt/sources.list

RUN a2enmod rewrite

RUN apt-get update \
  && docker-php-ext-install pdo pdo_mysql mysql mysqli

COPY ./ /var/www/html/

EXPOSE 80
