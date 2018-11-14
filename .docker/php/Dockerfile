FROM php:7.2-fpm-alpine

# Update all packages
RUN apk update
RUN apk upgrade

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install xdebug
RUN apk add $PHPIZE_DEPS
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
