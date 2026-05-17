FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

RUN printf '%s\n' \
    '<Directory "/var/www/html/configuration">' \
    '    Require all denied' \
    '</Directory>' \
    '<Directory "/var/www/html/mysql_database">' \
    '    Require all denied' \
    '</Directory>' \
    '<Directory "/var/www/html/.git">' \
    '    Require all denied' \
    '</Directory>' \
    > /etc/apache2/conf-available/app-security.conf \
    && a2enconf app-security

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
