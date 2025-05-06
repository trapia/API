# استخدم صورة PHP مع Apache
FROM php:8.2-cli

# انسخ ملفات المشروع داخل الكونتينر
COPY . /var/www/html

# حدد مجلد العمل
WORKDIR /var/www/html

# فتح البورت 8000
EXPOSE 8000

# شغل السيرفر المحلي بتاع PHP على بورت 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "."]
