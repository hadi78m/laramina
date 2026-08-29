# tasks.md - مستندات فنی

> این فایل برای هر پروژه متفاوت است و اطلاعات فنی خاص آن پروژه را دارد.

## اطلاعات پروژه
- **نام**: admin-platform
- **توضیحات**: پکیج مدیریتی مدولار برای لاراول با فرانت‌اند ماژولار

## ساختار پروژه

```
admin-platform/
├── composer.json
├── config/
│   └── admin-platform.php
├── src/
│   ├── AdminPlatformServiceProvider.php
│   ├── Console/Commands/MakeAdminUI.php
│   ├── Contracts/AdminModule.php
│   ├── Controllers/ModuleController.php
│   ├── Services/ModuleService.php
│   ├── Support/ModuleRegistry.php
│   └── Traits/AdminTableTrait.php
├── resources/
│   ├── js/admin-platform/     # فرانت‌اند ماژولار
│   ├── js/custom/             # فایل‌های سفارشی
│   ├── js/sweetalert/         # SweetAlert2
│   ├── lang/fa/               # ترجمه فارسی
│   └── views/                 # ویوهای Blade
└── manual/                    # مستندات راهنما
```

## API / سرویس‌ها

| Endpoint | Method | توضیحات |
|----------|--------|---------|
| `{module}.json` | GET | دریافت لیست با جستجو/فیلتر/صفحه‌بندی |
| `{module}.store` | POST | ایجاد رکورد جدید |
| `{module}.update` | POST | ویرایش رکورد |
| `{module}.destroy` | POST | حذف رکورد |
| `{module}.toggle-status` | POST | تغییر وضعیت فعال/غیرفعال |
| `manage.modules` | GET | لیست ماژول‌های ثبت‌شده |

## فیلدها / مدل‌ها

| فیلد | نوع | توضیحات |
|------|-----|---------|
| `id` | number | شناسه |
| `name` | string | نام |
| `email` | string | ایمیل |
| `is_active` | boolean | وضعیت فعال/غیرفعال |
| `created_at` | datetime | تاریخ ایجاد |
| `updated_at` | datetime | تاریخ بروزرسانی |

## تنظیمات

| تنظیم | مقدار پیش‌فرض | توضیحات |
|-------|---------------|---------|
| `modules` | `[]` | لیست ماژول‌های ثبت‌شده |
| `APP_LOCALE` | `fa` | زبان پیش‌فرض پنل مدیریت |
