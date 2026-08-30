# 🧪 راهنمای تست Laramina

> **هدف:** بررسی صحت عملکرد پکیج قبل از انتشار در Packagist

---

## فهرست مطالب

1. [تست‌های خودکار](#۱-تست‌های-خودکار)
2. [تست دستی](#۲-تست-دستی)
3. [تست API مستقیم](#۳-تست-api-مستقیم)
4. [عیب‌یابی](#۴-عیب‌یابی)

---

## ۱. تست‌های خودکار

### ۱.۱ پیش‌نیازها

- PHP 8.1+
- Composer

### ۱.۲ نصب وابستگی‌ها

```bash
composer install
```

### ۱.۳ اجرای تست‌ها

```bash
# اجرای همه تست‌ها
php vendor/bin/phpunit

# اجرای با نمایش توضیحات
php vendor/bin/phpunit --testdox

# اجرای یک گروه تست خاص
php vendor/bin/phpunit --filter="AdminTableTraitTest"
php vendor/bin/phpunit --filter="MakeAdminUITest"
php vendor/bin/phpunit --filter="ServiceProviderTest"
```

### ۱.۴ پوشش تست‌ها

| ماژول تست | تعداد | توضیح |
|-----------|-------|-------|
| AdminTableTrait | ۱۵ | جستجو، فیلتر، مرتب‌سازی، صفحه‌بندی، transform |
| MakeAdminUI | ۱۱ | تولید فایل‌های ماژول، بررسی ساختار خروجی |
| ServiceProvider | ۸ | ثبت سرویس‌ها، کانفیگ، ترجمه‌ها |
| BladeView | ۸ | بررسی ساختار view ادمین |
| ModuleRegistry | ۷ | مدیریت ماژول‌ها از کانفیگ |
| Translation | ۴ | ترجمه‌های فارسی و انگلیسی |
| Config | ۴ | ساختار فایل کانفیگ |
| ModuleController | ۳ | API ماژول‌ها |
| ModuleService | ۲ | سرویس ماژول‌ها |
| Contract | ۲ | رابط AdminModule |
| **مجموع** | **۶۴** | **۲۲۴ assertion** |

---

## ۲. تست دستی

### ۲.۱ ایجاد پروژه تستی

```bash
composer create-project laravel/laravel laramina-test-app
cd laramina-test-app
```

### ۲.۲ اضافه کردن پکیج

```bash
composer require hadii/laramina
```

### ۲.۳ انتشار دارایی‌ها

```bash
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views
```

### ۲.۴ بررسی نصب

```bash
php artisan package:discover
```

باید `hadii/laramina` در لیست باشد.

### ۲.۵ ایجاد مدل و ماژول

```bash
php artisan laramina:make-ui User --force
```

### ۲.۶ تنظیم مسیرها

فایل `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('users')->name('users.')->middleware('web')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/json', [UserController::class, 'json'])->name('json');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```

### ۲.۷ اجرا

```bash
php artisan serve
```

مرورگر: `http://localhost:8000/users`

### ۲.۸ چک‌لیست تست

| # | تست | وضعیت |
|---|-----|-------|
| ۱ | جدول کاربران نمایش داده می‌شود | ☐ |
| ۲ | جستجو کار می‌کند | ☐ |
| ۳ | فیلتر وضعیت کار می‌کند | ☐ |
| ۴ | مرتب‌سازی کار می‌کند | ☐ |
| ۵ | صفحه‌بندی کار می‌کند | ☐ |
| ۶ | دکمه "افزودن" کار می‌کند | ☐ |
| ۷ | فرم ایجاد باز می‌شود | ☐ |
| ۸ | ایجاد کاربر جدید کار می‌کند | ☐ |
| ۹ | ویرایش کاربر کار می‌کند | ☐ |
| ۱۰ | تغییر وضعیت (toggle) کار می‌کند | ☐ |
| ۱۱ | حذف کاربر کار می‌کند | ☐ |
| ۱۲ | خطاها در کنسول نیست | ☐ |

### ۲.۹ بررسی کنسول مرورگر

کلید `F12` → تب Console

**پیام‌های مجاز:**
- `users module loaded` ✅

**پیام‌های خطا (نباید وجود داشته باشند):**
- `404 Not Found` ❌
- `419 Page Expired` ❌
- `SyntaxError` ❌
- `Uncaught TypeError` ❌

---

## ۳. تست API مستقیم

```bash
php artisan tinker --execute="
\$request = new \Illuminate\Http\Request();
\$request->merge(['per_page' => 10]);
echo App\Models\User::adminTable(\$request, [
    'search' => ['name', 'email'],
    'filters' => ['is_active'],
    'sortable' => ['name', 'email', 'created_at'],
])->getContent();
"
```

**خروجی مورد انتظار:**

```json
{
    "success": true,
    "data": [...],
    "total": 2,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 2
}
```

---

## ۴. عیب‌یابی

| مشکل | راه‌حل |
|------|--------|
| خطای ۴۰۴ فایل‌های JS | `php artisan vendor:publish --tag=laramina-assets --force` |
| خطای CSRF (419) | تگ `<meta name="csrf-token">` را بررسی کنید |
| جدول خالی است | Seeder را اجرا کنید: `php artisan db:seed --class=UserSeeder` |
| checkbox خطا می‌دهد | مطمئن شوید فایل‌های JS از پکیج کپی شده‌اند |
| toggle کار نمی‌کند | `showalertProduction.js` و `ajax-adapter.js` را بررسی کنید |

---

**تست با موفقیت انجام شد؟ بله ☐ / خیر ☐**

**تاریخ تست:** ___________

**تست‌کننده:** ___________
