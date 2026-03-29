FROM php:8.3-apache

WORKDIR /var/www/html

# PDO: SQLite (локальные тесты / без DB_HOST) и MySQL (Docker Compose)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY unit/ .

RUN a2enmod rewrite

RUN printf '%s\n' \
  '<VirtualHost *:80>' \
  '    ServerAdmin webmaster@localhost' \
  '    DocumentRoot /var/www/html/public' \
  '    <Directory /var/www/html/public>' \
  '        Options Indexes FollowSymLinks' \
  '        AllowOverride All' \
  '        DirectoryIndex index.php' \
  '        Require all granted' \
  '    </Directory>' \
  '    ErrorLog ${APACHE_LOG_DIR}/error.log' \
  '    CustomLog ${APACHE_LOG_DIR}/access.log combined' \
  '</VirtualHost>' \
  > /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
