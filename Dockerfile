FROM php:8.2-fpm

# تثبيت المتطلبات
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    supervisor \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع
WORKDIR /var/www
COPY . .

# إعدادات Laravel
RUN composer install --no-dev --optimize-autoloader && \
    chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www

# نسخ ملف nginx.conf و supervisor
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf

# Expose port
EXPOSE 80

# بدء nginx و php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
