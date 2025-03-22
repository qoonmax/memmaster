FROM php:8.2-fpm

WORKDIR /var/www/

RUN apt-get update && apt-get install -y \
      apt-utils \
      git \
      libpq-dev && \
      pecl install redis && \
      docker-php-ext-enable redis && \
      docker-php-ext-install pdo pdo_pgsql && \
      docker-php-ext-install sockets && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#XDEBUG
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions xdebug

ENV PHP_IDE_CONFIG "serverName=Docker"

RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.client_host=docker.for.mac.localhost" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN mkdir -p /var/log/ && touch /var/log/xdebug.log && chmod 775 /var/log/xdebug.log && chown www-data:www-data /var/log/xdebug.log
RUN echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
