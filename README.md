# Iris Petals Offers

هذا المشروع يعمل على:

- Ubuntu
- Nginx
- PHP 8.3
- MySQL
- Laravel

لا يوجد أي ربط مع Supabase.

## صفحات المشروع

- صفحة العميل: `/index.html`
- لوحة الإدارة: `/admin.html`

## نشر المشروع على السيرفر

1. سحب الكود من GitHub داخل مسار المشروع.
2. تثبيت الحزم:

```bash
composer install --no-dev --optimize-autoloader
```

3. إنشاء ملف البيئة:

```bash
cp .env.example .env
```

4. تعديل بيانات قاعدة البيانات داخل `.env`:

- `DB_CONNECTION=mysql`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

5. إنشاء المفتاح:

```bash
php artisan key:generate
```

6. تنفيذ الجداول:

```bash
php artisan migrate --force
```

7. ضبط الصلاحيات:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads
sudo chmod -R 775 storage bootstrap/cache public/uploads
```

8. تحسين الأداء:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## واجهات API الداخلية

- `GET /api/settings`
- `POST /api/settings`
- `GET /api/orders`
- `POST /api/orders`
- `DELETE /api/orders/{id}`
- `GET /api/expenses`
- `POST /api/expenses`
- `DELETE /api/expenses/{id}`
- `POST /api/upload`

## ملاحظة

إن أردت إنشاء الجداول يدويًا بدل migrations:

- استخدم الملف: `database/mysql-schema.sql`
