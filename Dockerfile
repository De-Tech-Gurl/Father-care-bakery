FROM php:8.4-apache

# Render can override these with service environment variables when a worker is added.
ENV MAIL_MAILER=resend \
	QUEUE_CONNECTION=sync

RUN apt-get update && apt-get install -y git unzip libzip-dev libpq-dev libpng-dev libonig-dev libicu-dev \
 && docker-php-ext-install pdo_mysql pdo_pgsql zip intl bcmath gd mbstring \
 && a2enmod rewrite

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/^Listen 80$/Listen 10000/' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf
EXPOSE 10000

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader \
 && npm ci && npm run build \
 && chown -R www-data:www-data storage bootstrap/cache

CMD php artisan config:cache && php artisan migrate --force && apache2-foreground