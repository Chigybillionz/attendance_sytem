FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create necessary directories and set permissions
RUN mkdir -p bootstrap/cache storage && \
    chmod -R 755 bootstrap/cache storage

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 bootstrap storage

# Expose port
EXPOSE 10000

# Run migrations and seed default users before starting the app.
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --class=AdminSeeder --force && php artisan serve --host=0.0.0.0 --port=10000"]
