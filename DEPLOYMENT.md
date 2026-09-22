# استقرار نهایی روستا نما

## دامنه اصلی

chegeniroosta.ir

## دامنه مدیریت

admin.chegeniroosta.ir

## زیردامنه روستاها

sabzevar.chegeniroosta.ir
cham-divan.chegeniroosta.ir
abuzar.chegeniroosta.ir

## Wildcard DNS

*.chegeniroosta.ir

## سرور پیشنهادی

Ubuntu Server LTS
Nginx
PHP
MySQL/MariaDB
Composer
Node.js
SSL Let's Encrypt

## ساختار

یک Laravel Application

یک Database

هر روستا با village_id و subdomain از سایر روستاها تفکیک می‌شود.

## DNS

A     @       SERVER_IP
A     admin   SERVER_IP
A     *       SERVER_IP

## وب‌سرور

Nginx باید درخواست‌های زیر را به یک Laravel Application ارسال کند:

chegeniroosta.ir
admin.chegeniroosta.ir
*.chegeniroosta.ir

## SSL

برای دامنه اصلی، admin و wildcard گواهی SSL تنظیم شود.

## نکته مهم

قبل از انتقال اطلاعات:

1. از database.sqlite پشتیبان گرفته شود.
2. از storage/app/public پشتیبان گرفته شود.
3. APP_DEBUG در محیط production برابر false باشد.
4. APP_ENV در محیط production برابر production باشد.
5. APP_KEY حفظ شود و تغییر نکند.
6. فایل .env در Git قرار نگیرد.

