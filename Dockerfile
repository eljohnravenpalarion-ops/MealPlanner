FROM php:8.2-apache

RUN docker-php-ext-install mysqli && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

EXPOSE 8080

CMD ["sh", "-c", "sed -i \"s/80/${PORT:-8080}/g\" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && exec apache2-foreground"]