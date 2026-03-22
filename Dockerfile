FROM php:8.3-apache

WORKDIR /var/www/html

COPY unit/ .

RUN a2enmod rewrite

RUN printf '%s\n' \
  '<VirtualHost *:80>' \
  '    ServerAdmin webmaster@localhost' \
  '    DocumentRoot /var/www/html/public' \
  '    <Directory /var/www/html/public>' \
  '        Options Indexes FollowSymLinks' \
  '        AllowOverride All' \
  '        Require all granted' \
  '    </Directory>' \
  '    ErrorLog ${APACHE_LOG_DIR}/error.log' \
  '    CustomLog ${APACHE_LOG_DIR}/access.log combined' \
  '</VirtualHost>' \
  > /etc/apache2/sites-available/000-default.conf

RUN cd database && php seed.php

EXPOSE 80

CMD ["apache2-foreground"]
