# MEMORY.md - حافظه پروژه

> این فایل در هر پروژه بصورت جداگانه پر می‌شود.


- برای بررسی وضعیت تسک‌ها -> فقط MEMORY.md[cite: 6, 7]
- برای بررسی APIها -> فقط tasks.md[cite: 6, 8]

## اطلاعات پروژه
- **نام**: laramina
- **نام قبلی**: admin-platform
- **نوع**: پکیج لاراول (Laravel Package)
- **فریم‌ورک**: Laravel 10/11 + Tailwind CSS + AlpineJS
- **نسخه**: dev (main branch)
- **Namespace PHP**: Laramina\\
- **Config Key**: laramina

## قوانین پروژه
- پشتیبانی RTL و ترجمه فارسی الزامی است
- فرانت‌اند بدون وابستگی به Vue/React (فقط AlpineJS)

## وضعیت تسک‌ها

### در حال انجام
- (none)

### در انتظار
- (none)

### انجام شده
- [x] 1405/06/08 بررسی ساختار پکیج و به‌روزرسانی README
- [x] 1405/06/08 اصلاح dead code و paginate تکراری در AdminTableTrait
- [x] 1405/06/08 اصلاح ناسازگاری کانفیگ ماژول‌ها در ModuleRegistry
- [x] 1405/06/08 پاکسازی config از داده‌های hardcoded
- [x] 1405/06/08 رفع آسیب‌پذیری SQL Injection در sort + direction (AdminTableTrait)
- [x] 1405/06/08 اضافه کردن فایل ترجمه انگلیسی (en/adminUI.php)
- [x] 1405/06/08 بروزرسانی جامع README.md (فارسی + انگلیسی + Security Notes)
- [x] 1405/06/08 تغییر نام پکیج از admin-platform به laramina (namespace, config, views, README)

## یادداشت‌ها
- پکیج شامل سیستم ترجمه چندزبانه (FA/EN) است
- سازگار با Spatie Permission برای نقش و دسترسی
- MakeAdminUI فایل‌های JS + Blade را خودکار تولید می‌کند
- sort whitelist در AdminTableTrait امنیت SQL Injection را تضمین می‌کند
- per_page حداکثر 100 محدود شده است
- AdminModule contract تعریف شده ولی فعال نیست (قابل استفاده در آینده)
- نام جدید پکیج: laramina (آزاد در Packagist و GitHub)
