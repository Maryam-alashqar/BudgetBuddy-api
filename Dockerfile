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
    libzip-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مجلد العمل ونسخ ملفات المشروع
WORKDIR /var/www
COPY . .

# تثبيت الحزم الخاصة بـ Laravel وتحسين الأداء
RUN composer install --no-dev --optimize-autoloader --prefer-dist && \
    chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www

# نسخ إعدادات Nginx و Supervisor
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisord.conf

# تحديد المنفذ المكشوف
EXPOSE 9000

# تشغيل الخدمات عند بدء التشغيل
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
