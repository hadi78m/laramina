# Laramina

> **ابزار تولید جدول و CRUD ماژولار برای لاراول** — استانداردسازی و حذف کدهای تکراری

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12%2F13-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-64%20Passed-brightgreen.svg)](#تست‌ها)
[![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)](CHANGELOG.md)
[![Roadmap](https://img.shields.io/badge/Roadmap-12%20months-blue.svg)](ROADMAP.md)

---

## ✨ چرا Laramina؟

در پروژه‌های لاراول، معمولاً برای هر جدول باید کدهای تکراری زیادی بنویسید:
- مدل
- کنترلر
- مسیرها
- ویوها
- فایل‌های JS

**Laramina این مشکل را حل می‌کند!** 🎯

### 💡 مزیت‌های کلیدی

- **🚫 حذف کدهای تکراری** — هر جدول فقط یک بار تعریف می‌شود
- **📏 استانداردسازی** — تمام جدول‌ها ساختار یکسانی دارند
- **⚡ سرعت توسعه** — کاهش ۷۰٪ کدنویسی CRUD
- **🔧 نگهداری آسان** — تغییر در یک جا، اعمال در همه جا
- **🎯 سبک و سریع** — بدون وابستگی سنگین

---

## 🚀 شروع سریع

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

### ۳. ایجاد جدول برای هر مدل

```bash
php artisan laramina:make-ui User --force
```

> ⚠️ مدل باید از قبل وجود داشته باشد.

### ۴. اجرا

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
| [ایجاد جدول](INSTALL.md#۳-ایجاد-جدول) | روش خودکار و دستی |
| [تنظیمات ماژول](INSTALL.md#۴-تنظیمات-ماژول) | کانفیگ، جدول، فرم، اکشن‌ها |
| [نقش و دسترسی](INSTALL.md#۵-نقش-و-دسترسی) | با/بدون Spatie Permission |
| [API و مسیرها](INSTALL.md#۶-api-و-مسیرها) | endpointها و پارامترها |
| [امنیت](INSTALL.md#۷-امنیت) | اعتبارسنجی و محافظت |
| [عیب‌یابی](INSTALL.md#۸-عیب‌یابی) | مشکلات رایج و راه‌حل‌ها |

---

## 🧪 تست‌ها

این پکیج دارای ۶۴ تست خودکار است که بخش‌های مختلف را پوشش می‌دهد:

```bash
composer install
php vendor/bin/phpunit
```

---

## 📊 مقایسه با سایر ابزارها

| ویژگی | Laramina | Laravel Nova | Filament | Voyager |
|-------|----------|--------------|----------|---------|
| **نوع** | CRUD Generator | Admin Panel | Admin Panel | Admin Panel |
| **داشبورد** | ❌ | ✅ | ✅ | ✅ |
| **سایدبار** | ❌ | ✅ | ✅ | ✅ |
| **تولید CRUD** | ✅ | ✅ | ✅ | ✅ |
| **استانداردسازی** | ✅ | ✅ | ❌ | ❌ |
| **سبک** | ✅ | ❌ | ❌ | ❌ |
| **رایگان** | ✅ | ❌ | ✅ | ✅ |
| **چندزبانه فارسی** | ✅ | ❌ | ✅ | ❌ |
| **نیاز به Node.js** | ❌ | ✅ | ❌ | ❌ |

> 💡 **Laramina: ابزار سبک و سریع برای استانداردسازی CRUD در لاراول**

---

## 🗺️ نقشه راه

برای مشاهده نقشه راه کامل پکیج، فایل [`ROADMAP.md`](ROADMAP.md) را مطالعه کنید.

### خلاصه نقشه راه

| Phase | زمان | ویژگی‌های اصلی |
|-------|------|----------------|
| Phase ۱ | ماه ۱-۳ | فرم پیشرفته، داشبورد، فیلتر پیشرفته |
| Phase ۲ | ماه ۴-۶ | Media Library, Activity Log, Soft Delete |
| Phase ۳ | ماه ۷-۹ | Plugin System, API Docs, Workflow |
| Phase ۴ | ماه ۱۰-۱۲ | Marketplace, Team Management, AI Features |

> 📖 [مشاهده نقشه راه کامل](ROADMAP.md)

---

## 🇬🇧 English

### What is Laramina?

**Laramina is a modular CRUD generator for Laravel** that helps you:

- ✅ **Eliminate repetitive code** — Define each table only once
- ✅ **Standardize your project** — All tables follow the same structure
- ✅ **Speed up development** — Reduce CRUD coding by 70%
- ✅ **Easy maintenance** — Change once, apply everywhere
- ✅ **Lightweight & fast** — No heavy dependencies

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

> 📋 [مشاهده تاریخچه تغییرات](CHANGELOG.md)

---

## 📄 License

MIT License

Copyright (c) 2026 Hadii
