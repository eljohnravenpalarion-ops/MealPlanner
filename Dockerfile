
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

CMD ["bash", "-lc", "set -eux; a2dismod mpm_event mpm_worker || true; rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*; a2enmod mpm_prefork rewrite; apache2ctl -t; sed -i \"s/Listen 80/Listen ${PORT:-8080}/\" /etc/apache2/ports.conf; sed -i \"s/:80>/:${PORT:-8080}>/\" /etc/apache2/sites-available/000-default.conf; exec apache2-foreground"]
