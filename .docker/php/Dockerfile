FROM php:7.2-fpm-alpine

# Update all packages
RUN apk update
RUN apk upgrade

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install xdebug
RUN apk add $PHPIZE_DEPS
RUN pecl install xdebug
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

# Install pspell
RUN apk add aspell-dev
RUN apk add aspell-en
RUN docker-php-ext-install pspell
