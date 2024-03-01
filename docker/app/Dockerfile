# Dockerfile
FROM php:8.2-fpm-alpine

# install supervisor
RUN apk add supervisor

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install required dependencies and extension
RUN apk --no-cache add \
    $PHPIZE_DEPS \
    && apk add --no-cache autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql \
    && apk del $PHPIZE_DEPS autoconf g++ make

# Install necessary packages including ext-sodium
RUN apk --no-cache add \
    bash \
    git \
    curl \
    libsodium-dev \
    && docker-php-ext-install sodium

# Enable the PHP JIT compilation
RUN docker-php-ext-enable opcache

# Set the working directory
WORKDIR /var/www/html

# Copy your application code into the container
COPY . /var/www/html

# Set proper permissions (adjust if needed)
RUN chown -R www-data:www-data /var/www/html

# creating required directories
RUN mkdir -p /var/run/php
RUN mkdir -p /var/log/php-fpm

# Supervisor configuration
COPY ./supervisor/supervisord.conf /etc/supervisord.conf

# Add process workers
COPY ./supervisor/worker /etc/supervisor.d/

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start supervisor in the foreground
CMD ["supervisord", "-n"]
