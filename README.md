# Iris Petals CRM (Laravel)

هذا المشروع يعمل على:

- Ubuntu
- Nginx
- PHP 8.3
- MySQL
- Laravel

## الروابط الرسمية بعد الإصلاح

- صفحة العميل: `/`
- صفحة تسجيل دخول الإدارة: `/admin/login`
- مدخل الإدارة العام: `/admin`

مهم:

- لا تستخدم `admin.html` أو `index.html` نهائياً.
- المشروع أصبح Laravel بالكامل ولا يعتمد على صفحات HTML ثابتة قديمة.

## نشر التحديثات على السيرفر

نفذ الأوامر التالية بالترتيب:

```bash
cd /var/www/offers-irispetals
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads
sudo chmod -R 775 storage bootstrap/cache public/uploads
```

## تحقق سريع بعد النشر

```bash
php artisan route:list | grep admin
curl -I https://offers.irispetals.com/
curl -I https://offers.irispetals.com/admin
curl -I https://offers.irispetals.com/admin/login
```

المتوقع:

- `/` يرجع `200` بدون أي تحويل إلى `index.html`
- `/admin` يحول إلى `/admin/login` للزائر غير المسجل
- `/admin/login` يرجع `200`

## بيانات الدخول الافتراضية

يتم إنشاؤها من `DatabaseSeeder`:

- اسم المستخدم: `owner`
- كلمة المرور: `Owner@123456`

يجب تغيير كلمة المرور بعد أول تسجيل دخول.

## ملاحظات مهمة

- تم دعم التحصيل المقسم على أكثر من محصل داخل شاشة تعديل الطلب.
- تم منع إدخال تحصيل أكبر من إجمالي الطلب.
- لا توجد أي تبعيات إنتاجية على:
  - `public/index.html`
  - `public/admin.html`

