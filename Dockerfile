# Используйте официальный образ PHP
FROM php:7.4-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony


# Установка и настройка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy the project files
COPY . .

# Set environment variable for Composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install Composer dependencies
RUN composer install
# Команда по умолчанию
#CMD["php bin/console doctrine:migrations:migrate --no-interaction"]
#CMD["php bin/console doctrine:fixtures:load --no-interaction"]
CMD ["php-fpm"]
