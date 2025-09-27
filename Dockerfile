FROM php:8.4-apache

RUN apt-get update \
 && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev wget gnupg \
 && docker-php-ext-install intl opcache pdo pdo_mysql \
 && pecl install apcu \
 && docker-php-ext-enable apcu \
 && docker-php-ext-configure zip \
 && docker-php-ext-install zip \
 && pecl install xdebug \
 && docker-php-ext-enable xdebug \
 && docker-php-ext-install pgsql pdo_pgsql

RUN a2enmod rewrite

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin

RUN git config --global user.email "alex.gollion@gmail.com" 
RUN git config --global user.name "Alex Gollion"

RUN mkdir -p /var/www/public/uploads && \
    chown -R www-data:www-data /var/www/public && \
    chmod -R 755 /var/www/public

RUN curl -L https://download.newrelic.com/php_agent/archive/11.5.0.18/newrelic-php5-11.5.0.18-linux.tar.gz -o /tmp/newrelic.tar.gz \
    && tar -xzf /tmp/newrelic.tar.gz -C /tmp \
    && export NR_INSTALL_USE_CP_NOT_LN=1 \
    && export NR_INSTALL_SILENT=1 \
    && /tmp/newrelic-php5-*/newrelic-install install \
    && rm -rf /tmp/newrelic*

COPY ./newrelic.ini /usr/local/etc/php/conf.d/

RUN mkdir -p /var/log/newrelic \
 && chown -R www-data:www-data /var/log/newrelic