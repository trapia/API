# استخدم صورة PHP مع FPM (FastCGI Process Manager)
FROM php:8.0-fpm

# تثبيت الأدوات المطلوبة
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# تعيين مجلد العمل في السيرفر
WORKDIR /var/www

# نسخ جميع الملفات من جهازك إلى السيرفر
COPY . .

# تثبيت Composer (مدير الاعتمادات في PHP)
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# تثبيت الاعتمادات
RUN composer install --no-dev --optimize-autoloader

# تثبيت Apache وتشغيله
RUN apt-get install -y apache2
RUN a2enmod rewrite

# إعدادات Apache
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# بدء Apache
CMD ["apache2-foreground"]
