# Dockerfile
FROM php:8.2-fpm-alpine

# Install required dependencies and extension
RUN apk --no-cache add \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql \
    && apk del $PHPIZE_DEPS

# Install necessary packages including ext-sodium
RUN apk --no-cache add \
    bash \
    git \
    curl \
    libsodium-dev \
    && docker-php-ext-install sodium

# Set the working directory
WORKDIR /var/www/html

# Copy your application code into the container
COPY . /var/www/html

# Set proper permissions (adjust if needed)
RUN chown -R www-data:www-data /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
