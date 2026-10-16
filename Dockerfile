
FROM php:8.2-apache

# Ensure Apache loads only the compatible MPM
RUN set -eux; \
    a2dismod mpm_event || true; \
    a2dismod mpm_worker || true; \
    a2enmod mpm_prefork rewrite; \
    docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY . /var/www/html/

EXPOSE 8080

CMD ["sh", "-c", "sed -i \"s/80/${PORT:-8080}/g\" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && exec apache2-foreground"]
