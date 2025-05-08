FROM php:5.6-apache
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html