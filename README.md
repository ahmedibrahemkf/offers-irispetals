# CRM PRD v2.0 - Laravel

هذا المشروع مبني لبيئة:

- Ubuntu
- Nginx
- PHP 8.3
- MySQL
- Laravel

## الروابط الرئيسية

- تسجيل الدخول: `/admin/login`
- لوحة التحكم: `/admin/dashboard`
- إدارة الطلبات: `/admin/orders`
- الفواتير: `/admin/invoices`
- طباعة الفاتورة: `/admin/invoices/{id}/print`
- المنتجات: `/admin/products`
- الموردون: `/admin/suppliers`
- المشتريات: `/admin/purchases`
- المصروفات: `/admin/expenses`
- الموظفون: `/admin/employees`
- صفحة موظف: `/admin/employees/{id}`
- العملاء: `/admin/customers`
- التقارير: `/admin/reports`
- الإعدادات: `/admin/settings`
- الإشعارات: `/admin/notifications`
- سجل النشاط: `/admin/activity-logs`
- صفحة الصنايعي: `/craftsman`
- صفحة موظف الاستقبال: `/staff/orders`
- صفحة الطلب العامة: `/order`

## تجهيز المشروع على السيرفر

```bash
cd /var/www/offers-irispetals
git pull origin main
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads
sudo chmod -R 775 storage bootstrap/cache public/uploads
```

## الحساب الأول

يتم إنشاؤه من Seeder:

- المستخدم: `owner`
- كلمة المرور: `Owner@123456`

غير كلمة المرور مباشرة بعد أول دخول.

## ملاحظات مهمة

- دعم تقسيم التحصيل في الطلب على أكثر من محصل أصبح متاحًا من شاشة تعديل الطلب.
- لا يمكن تسجيل تحصيل يتجاوز إجمالي الطلب.
- التقارير المالية تعرض:
  - إجمالي المبيعات
  - المتحصل الفعلي
  - المتبقي للتحصيل
  - صافي الربح بناءً على التحصيل الفعلي
- صفحة نسيان كلمة المرور تعمل بـ OTP تجريبي داخل النظام (ليست خدمة SMS فعلية).

## API

- `GET /api/dashboard/stats`

