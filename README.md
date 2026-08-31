# Laramina

> **ابزار تولید جدول و CRUD ماژولار برای لاراول** — استانداردسازی و حذف کدهای تکراری

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12%2F13-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-64%20Passed-brightgreen.svg)](#تست‌ها)
[![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)](CHANGELOG.md)

---

## ✨ چرا Laramina؟

در پروژه‌های لاراول، معمولاً برای هر جدول باید کدهای تکراری زیادی بنویسید. **Laramina این مشکل را حل می‌کند!** 🎯

### 💡 مزیت‌های کلیدی

- **🚫 حذف کدهای تکراری** — هر جدول فقط یک بار تعریف می‌شود
- **📏 استانداردسازی** — تمام جدول‌ها ساختار یکسانی دارند
- **⚡ سرعت توسعه** — کاهش ۷۰٪ کدنویسی CRUD
- **🔧 نگهداری آسان** — تغییر در یک جا، اعمال در همه جا
- **🎯 سبک و سریع** — بدون وابستگی سنگین
- **🌐 چندزبانه** — پشتیبانی کامل از فارسی و انگلیسی
- **📅 تاریخ شمسی** — سازگاری با Verta (Jalali)
- **🔐 نقش و دسترسی** — سازگاری با Spatie Permission

---

## 🚀 شروع سریع

```bash
# ۱. نصب
composer require hadii/laramina

# ۲. انتشار دارایی‌ها
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views

# ۳. ایجاد جدول CRUD برای مدل
php artisan laramina:make-ui User --force

# ۴. اجرا
php artisan serve
```

مرورگر: `http://localhost:8000/users`

> ⚠️ **نکته:** مدل باید از قبل وجود داشته باشد. برای جزییات بیشتر [INSTALL.md](INSTALL.md) را مطالعه کنید.

---

## 📸 نمونه کار و ویدیوها

> 🎬 **به زودی** — نمونه‌های عملی و ویدیوهای آموزشی اینجا قرار خواهند گرفت.

<!--
### ویدیوی آموزشی
[![Watch the video](https://img.youtube.com/vi/VIDEO_ID/0.jpg)](https://youtu.be/VIDEO_ID)

### نمونه‌های عملی
- [پنل مدیریت کاربران](#)
- [سیستم مدیریت مقالات](#)
- [داشبورد فروش](#)
-->

---

## 📖 مستندات کامل

برای راهنمای گام‌به‌گام، فایل [`INSTALL.md`](INSTALL.md) را مطالعه کنید:

| بخش | توضیح | لینک |
|-----|-------|------|
| نصب و انتشار | نصب پکیج و انتشار دارایی‌ها | [مشاهده](INSTALL.md#۱-نصب-و-انتشار) |
| تنظیمات اولیه | لایه‌اوت، Tailwind، مسیرها | [مشاهده](INSTALL.md#۲-تنظیمات-اولیه) |
| ایجاد ماژول | روش خودکار و دستی | [مشاهده](INSTALL.md#۳-ایجاد-ماژول) |
| تنظیمات ماژول | کانفیگ، جدول، فرم، اکشن‌ها | [مشاهده](INSTALL.md#۴-تنظیمات-ماژول) |
| پکیج‌های مکمل | Verta و Spatie Permission | [مشاهده](INSTALL.md#۵-پکیج‌های-مکمل) |
| نقش و دسترسی | با/بدون Spatie Permission | [مشاهده](INSTALL.md#۶-نقش-و-دسترسی) |
| API و مسیرها | endpointها و پارامترها | [مشاهده](INSTALL.md#۶-api-و-مسیرها) |
| امنیت | اعتبارسنجی و محافظت | [مشاهده](INSTALL.md#۷-امنیت) |
| عیب‌یابی | مشکلات رایج و راه‌حل‌ها | [مشاهده](INSTALL.md#۸-عیب‌یابی) |
| تست‌ها | اجرای تست‌های خودکار | [مشاهده](INSTALL.md#۹-تست‌ها) |

---

## 🇬🇧 English

### What is Laramina?

**Laramina is a modular CRUD generator for Laravel** that helps you eliminate repetitive code, standardize your project, and speed up development by 70%.

### Features

- 🎯 **Auto CRUD Generation** — Generate tables with a single command
- 🔍 **Advanced Search** — Multi-column search with AJAX
- 📊 **Smart Filtering** — Filter by any column
- 🔄 **Sorting** — Sort by single or multiple columns
- 📄 **Pagination** — Built-in pagination with customizable per-page
- ✏️ **Inline Editing** — Toggle status without page reload
- 🌐 **Multilingual** — Full Persian (Farsi) and English support
- 🔐 **Role-Based Access** — Compatible with Spatie Permission
- 📅 **Persian Dates** — Full support for Verta (Jalali dates)

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

> For detailed setup instructions, see [`INSTALL.md`](INSTALL.md).

### Package Integrations

| Package | Purpose | Status |
|---------|---------|--------|
| [Verta](https://github.com/jalaunch/vaah) | Persian/Jalali dates | ✅ Supported |
| [Spatie Permission](https://github.com/spatie/laravel-permission) | Role & Permission | ✅ Supported |

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

## 🧪 تست‌ها

```bash
composer install
php vendor/bin/phpunit
```

این پکیج دارای ۶۴ تست خودکار PHPUnit است. برای جزییات بیشتر [بخش تست‌ها](INSTALL.md#۹-تست‌ها) را ببینید.

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
