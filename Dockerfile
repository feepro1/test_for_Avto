# Используйте официальный образ PHP
FROM php:7.4-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Установка и настройка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование вашего кода
COPY . /var/www/html
WORKDIR /var/www/html

# Настройка PHP
#COPY php.ini /usr/local/etc/php/
RUN composer install

# Команда по умолчанию
#CMD["php bin/console doctrine:migrations:migrate --no-interaction"]
#CMD["php bin/console doctrine:fixtures:load --no-interaction"]
CMD ["php-fpm"]
