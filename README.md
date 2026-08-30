# Laramina

> **یک پنل ادمین ماژولار برای لاراول** — CRUD خودکار، فرانت‌اند مدرن، بدون وابستگی به Vue/React.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12%2F13-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-64%20Passed-brightgreen.svg)](#تست‌ها)
[![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)](CHANGELOG.md)

---

## ✨ ویژگی‌ها

- **تولید خودکار ماژول** — دستور Artisan برای ساخت جدول، فرم و عملیات CRUD
- **فرانت‌اند ماژولار** — معماری مبتنی بر موتورها (Table Engine, Form Engine, CRUD Engine)
- **Eager Loading** — پشتیبانی از بارگذاری روابط مدل با `$config['with']`
- **پشتیبانی از نقش و دسترسی** — سازگار با Spatie Permission (اختیاری) یا هر سیستم نقش دلخواه
- **چندزبانه** — ترجمه فارسی و انگلیسی
- **فرم‌های پویا** — گروه‌بندی فیلدها، Select Box با دریافت از API
- **تبلت تعاملی** — جستجو، فیلتر، مرتب‌سازی، صفحه‌بندی و تغییر وضعیت با AJAX
- **فرانت‌اند خالص** — فقط AlpineJS + Tailwind CSS، بدون Vue/React
- **امنیت** — اعتبارسنجی sort/filter در سمت سرور

---

## ⚡ شروع سریع

### پیش‌نیازها

- PHP 8.1+
- Laravel 10 / 11 / 12 / 13
- jQuery 3.x
- Tailwind CSS + AlpineJS

### ۱. نصب

```bash
composer require hadii/laramina
```

### ۲. انتشار دارایی‌ها

```bash
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views
```

### ۳. تنظیم لایه‌اوت

فایل `resources/views/layouts/app.blade.php` را ویرایش کنید:

```blade
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پنل مدیریت')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-100">
    @yield('content')

    {{-- Laramina Admin Platform --}}
    @include('laramina::adminPlatform')
</body>
</html>
```

> ⚠️ jQuery باید قبل از `adminPlatform` لود شود.

### ۴. ایجاد ماژول

```bash
php artisan laramina:make-ui User --force
```

### ۵. اجرا

```bash
php artisan serve
```

مرورگر: `http://localhost:8000/users`

---

## 📖 مستندات کامل

برای راهنمای گام‌به‌گام، فایل [`INSTALL.md`](INSTALL.md) را مطالعه کنید:

| بخش | توضیح |
|-----|-------|
| [نصب و انتشار](INSTALL.md#۱-نصب-و-انتشار) | نصب پکیج و انتشار دارایی‌ها |
| [تنظیمات اولیه](INSTALL.md#۲-تنظیمات-اولیه) | لایه‌اوت، Tailwind، مسیرها |
| [ایجاد ماژول](INSTALL.md#۳-ایجاد-ماژول) | روش خودکار و دستی |
| [تنظیمات ماژول](INSTALL.md#۴-تنظیمات-ماژول) | کانفیگ، جدول، فرم، اکشن‌ها |
| [نقش و دسترسی](INSTALL.md#۵-نقش-و-دسترسی) | با/بدون Spatie Permission |
| [API و مسیرها](INSTALL.md#۶-api-و-مسیرها) | endpointها و پارامترها |
| [امنیت](INSTALL.md#۷-امنیت) | اعتبارسنجی و محافظت |
| [عیب‌یابی](INSTALL.md#۸-عیب‌یابی) | مشکلات رایج و راه‌حل‌ها |
| [نمونه‌های پیشرفته](INSTALL.md#۹-نمونه‌های-پیشرفته) | فرم‌های پیشرفته، محدودیت نقش |
| [مهاجرت](INSTALL.md#۱۰-مهاجرت-از-admin-platform) | مهاجرت از نسخه قدیم |

---

## 🧪 تست‌ها

این پکیج دارای ۶۴ تست خودکار است که بخش‌های مختلف را پوشش می‌دهد:

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

### اجرای تست‌ها

```bash
composer install
php vendor/bin/phpunit
```

---

## 🇬🇧 English

### Features

- **Auto Module Generation** — Artisan command to scaffold table, forms, and CRUD operations
- **Modular Frontend** — Engine-based architecture (Table Engine, Form Engine, CRUD Engine)
- **Eager Loading** — Support for loading model relations via `$config['with']`
- **Role & Permission Support** — Compatible with Spatie Permission (optional) or any custom role system
- **Multilingual** — Persian and English translations
- **Dynamic Forms** — Field grouping, Select Boxes with API fetch
- **Interactive Table** — Search, filter, sort, pagination, and status toggle via AJAX
- **Pure Frontend** — No Vue/React dependency, AlpineJS + Tailwind CSS only
- **Security** — Server-side validation for sort columns and direction

### Quick Start

```bash
composer require hadii/laramina
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views
php artisan laramina:make-ui User --force
php artisan serve
```

### Tests

```bash
composer install
php vendor/bin/phpunit
```

---

## 📁 ساختار پکیج

```
laramina/
├── config/laramina.php              # کانفیگ پیش‌فرض
├── src/
│   ├── LaraminaServiceProvider.php  # Service Provider
│   ├── Console/Commands/            # دستورات Artisan
│   ├── Contracts/AdminModule.php    # رابط ماژول
│   ├── Controllers/                 # کنترلرهای پیش‌فرض
│   ├── Services/                    # سرویس‌ها
│   ├── Support/                     # پشتیبانی ماژول‌ها
│   └── Traits/AdminTableTrait.php   # Trait اصلی جدول
├── resources/
│   ├── js/laramina/                 # فرانت‌اند ماژولار
│   ├── lang/fa/ & en/               # ترجمه‌ها
│   └── views/                       # ویوهای Blade
└── tests/                           # تست‌های خودکار (PHPUnit)
```

---

## 🔄 تغییرات نسخه ۱.۰.۰

- ✅ اضافه شدن ۶۴ تست خودکار
- ✅ رفع باگ صفحه‌بندی در AdminTableTrait
- ✅ پشتیبانی از Laravel 10/11/12/13
- ✅ معماری ماژولار فرانت‌اند

---

## 📄 License

MIT License

Copyright (c) 2026 Hadii
