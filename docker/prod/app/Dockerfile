FROM php:8.2-fpm

WORKDIR /var/www/

RUN apt-get update && apt-get install -y \
      apt-utils \
      git \
      libzip-dev \
      zip unzip \
      libpq-dev && \
      curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
      apt-get install -y nodejs && \
      pecl install redis && \
      docker-php-ext-enable redis && \
      docker-php-ext-install pdo pdo_pgsql && \
      docker-php-ext-install sockets && \
      docker-php-ext-install zip && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN npm install -g vite

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data ./docker/prod/app/php.ini /usr/local/etc/php/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

RUN composer install \
      --optimize-autoloader \
      --no-dev \
      --no-interaction \
      --no-plugins \
      --no-suggest \
      --prefer-dist

RUN npm install --legacy-peer-deps
RUN npm run build

RUN composer dump-autoload  --no-scripts --optimize && \
    php artisan storage:link && \
    chown -R www-data:www-data /var/www && \
    chmod 755 -R /var/www && \
    chmod 775 -R /var/www/storage && \
    chmod 775 -R /var/www/bootstrap/cache
