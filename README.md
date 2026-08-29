# Admin Platform for Laravel

> **A modular admin panel package for Laravel 10/11/12** — Dynamic CRUD modules with modern JavaScript and Tailwind CSS.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 🇮🇷 فارسی

### ✨ ویژگی‌ها

- **تولید خودکار ماژول** — دستور Artisan برای ساخت جدول، فرم و عملیات CRUD
- **فرانت‌اند ماژولار** — معماری مبتنی بر موتورها (Table Engine, Form Engine, CRUD Engine)
- **پشتیبانی از نقش و دسترسی** — سازگار با Spatie Permission
- **چندزبانه** — ترجمه فارسی و انگلیسی با سیستم Lang لاراول
- **فرم‌های پویا** — گروه‌بندی فیلدها، Select Box با دریافت از API، فرم‌های جداگانه ایجاد/ویرایش
- **تبلت تعاملی** — جستجو، فیلتر، مرتب‌سازی، صفحه‌بندی و تغییر وضعیت با AJAX
- **فرانت‌اند خالص** — بدون وابستگی به Vue/React، فقط AlpineJS + Tailwind CSS
- **امنیت** — اعتبارسنجی امنیتی sort/filter در سمت سرور

### 📦 نصب

#### پیش‌نیازها

- PHP 8.1+
- Laravel 10 / 11 / 12
- Tailwind CSS + AlpineJS (در پروژه اصلی)

#### ۱. نصب از طریق Composer

فایل `composer.json` پروژه اصلی:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/AdminPlatform"
    }
]
```

```bash
composer require hadii/admin-platform:@dev
```

#### ۲. انتشار دارایی‌ها

```bash
# انتشار همه دارایی‌ها به صورت یکجا (توصیه شده)
php artisan vendor:publish --tag=admin-platform-assets
php artisan vendor:publish --tag=admin-platform-config
php artisan vendor:publish --tag=admin-platform-lang
php artisan vendor:publish --tag=admin-platform-views  # اختیاری
```

#### ۳. تنظیم زبان

در فایل `.env`:

```env
APP_LOCALE=fa
```

#### ۴. به‌روزرسانی Tailwind

فایل `tailwind.config.js`:

```js
content: [
    // کدهای قبلی
    "./public/js/admin-platform/**/*.js",
    "./public/js/custom/**/*.js",
    "./vendor/hadii/admin-platform/resources/js/**/*.js",
],
```

```bash
npm run build
```

#### ۵. افزودن به لایه‌اوت

در فایل `resources/views/layouts/app.blade.php`:

```blade
@include('admin-platform::adminPlatform')
```

یا در صورت انتشار ویو:

```blade
@include('vendor.admin-platform.adminPlatform')
```

### 🛠 استفاده

#### ساخت ماژول جدید

```bash
php artisan admin:make-ui User
```

این دستور فایل‌های زیر را تولید می‌کند:

```
public/js/modules/user/
├── module.js
├── table.js
├── actions.js
└── forms/create-form.js
```

و ویوی Blade مربوطه در `resources/views/user/index.blade.php`.

#### ساختار ماژول‌ها در کانفیگ

فایل `config/admin-platform.php`:

```php
return [
    'modules' => [
        'users' => [
            'label' => 'کاربران',
            'icon'  => 'fas fa-users',
            'route' => 'users.index',
        ],
    ],
];
```

#### استفاده از AdminTableTrait در مدل

```php
use AdminPlatform\Traits\AdminTableTrait;

class User extends Model
{
    use AdminTableTrait;

    public static function adminTransform($cred)
    {
        return [
            'id'         => $cred->id,
            'name'       => $cred->name,
            'email'      => $cred->email,
            'created_at' => verta($cred->created_at)->format('Y/m/d H:i'),
        ];
    }
}
```

#### استفاده در کنترلر

```php
use AdminPlatform\Traits\AdminTableTrait;

class UserController extends Controller
{
    public function json(Request $request)
    {
        return User::adminTable($request, [
            'search'   => ['name', 'email'],
            'filters'  => ['is_active'],
            'sortable' => ['name', 'email', 'created_at'], // ستون‌های مجاز مرتب‌سازی
        ]);
    }
}
```

#### تعریف مسیرها

```php
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    Route::get('/',             [UserController::class, 'index'])->name('index');
    Route::get('/json',         [UserController::class, 'json'])->name('json');
    Route::post('/',            [UserController::class, 'store'])->name('store');
    Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::post('/destroy/{id}',[UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```

#### ساختار فرم (Create/Edit)

فایل `create-form.js` ماژول:

```js
export const createForm = {
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',
    title: moduleActions.create || publicLang.create,
    fields: [
        {
            type: 'group',
            group: [
                { name: 'name', label: publicLang.name, type: 'text', required: true },
                { name: 'email', label: publicLang.email, type: 'email', required: true },
            ]
        },
        {
            name: 'roles',
            label: 'نقش‌ها',
            type: 'select',
            multiple: true,
            optionEndpoint: 'manage.roles.list',
            optionLabel: 'name',
            optionValue: 'name',
        },
        {
            name: 'password',
            label: publicLang.password,
            type: 'password',
            required: true,
            min: 6,
        },
    ],
    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};
```

---

## 🇬🇧 English

### Features

- **Auto Module Generation** — Artisan command to scaffold table, forms, and CRUD operations
- **Modular Frontend** — Engine-based architecture (Table Engine, Form Engine, CRUD Engine)
- **Role & Permission Support** — Compatible with Spatie Permission
- **Multilingual** — Persian and English translations via Laravel's Lang system
- **Dynamic Forms** — Field grouping, Select Boxes with API fetch, separate create/edit forms
- **Interactive Table** — Search, filter, sort, pagination, and status toggle via AJAX
- **Pure Frontend** — No Vue/React dependency, AlpineJS + Tailwind CSS only
- **Security** — Server-side validation for sort columns and direction

### Installation

#### Requirements

- PHP 8.1+
- Laravel 10 / 11 / 12
- Tailwind CSS + AlpineJS (in your main project)

#### 1. Install via Composer

Add to your main project's `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/AdminPlatform"
    }
]
```

```bash
composer require hadii/admin-platform:@dev
```

#### 2. Publish Assets

```bash
php artisan vendor:publish --tag=admin-platform-assets
php artisan vendor:publish --tag=admin-platform-config
php artisan vendor:publish --tag=admin-platform-lang
php artisan vendor:publish --tag=admin-platform-views  # Optional
```

#### 3. Set Locale

In `.env`:

```env
APP_LOCALE=fa
```

#### 4. Update Tailwind

In `tailwind.config.js`:

```js
content: [
    "./public/js/admin-platform/**/*.js",
    "./public/js/custom/**/*.js",
    "./vendor/hadii/admin-platform/resources/js/**/*.js",
],
```

```bash
npm run build
```

#### 5. Add to Layout

In `resources/views/layouts/app.blade.php`:

```blade
@include('admin-platform::adminPlatform')
```

### Usage

#### Generate a New Module

```bash
php artisan admin:make-ui User
```

This generates:

```
public/js/modules/user/
├── module.js
├── table.js
├── actions.js
└── forms/create-form.js
```

And a Blade view at `resources/views/user/index.blade.php`.

#### Module Registration in Config

`config/admin-platform.php`:

```php
return [
    'modules' => [
        'users' => [
            'label' => 'Users',
            'icon'  => 'fas fa-users',
            'route' => 'users.index',
        ],
    ],
];
```

#### Using AdminTableTrait in a Model

```php
use AdminPlatform\Traits\AdminTableTrait;

class User extends Model
{
    use AdminTableTrait;

    public static function adminTransform($cred)
    {
        return [
            'id'         => $cred->id,
            'name'       => $cred->name,
            'email'      => $cred->email,
            'created_at' => $cred->created_at->format('Y/m/d H:i'),
        ];
    }
}
```

#### Using in a Controller

```php
use AdminPlatform\Traits\AdminTableTrait;

class UserController extends Controller
{
    public function json(Request $request)
    {
        return User::adminTable($request, [
            'search'   => ['name', 'email'],
            'filters'  => ['is_active'],
            'sortable' => ['name', 'email', 'created_at'],
        ]);
    }
}
```

#### Defining Routes

```php
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    Route::get('/',             [UserController::class, 'index'])->name('index');
    Route::get('/json',         [UserController::class, 'json'])->name('json');
    Route::post('/',            [UserController::class, 'store'])->name('store');
    Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::post('/destroy/{id}',[UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```

#### Form Structure (Create/Edit)

```js
export const createForm = {
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',
    title: moduleActions.create || publicLang.create,
    fields: [
        {
            type: 'group',
            group: [
                { name: 'name', label: publicLang.name, type: 'text', required: true },
                { name: 'email', label: publicLang.email, type: 'email', required: true },
            ]
        },
        {
            name: 'password',
            label: publicLang.password,
            type: 'password',
            required: true,
            min: 6,
        },
    ],
    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};
```

---

## 📐 Frontend Architecture

```
resources/js/admin-platform/
├── bootstrap/admin-platform.js   # Main entry point
├── core/                         # Core system
│   ├── action.js                 # Action system
│   ├── module-loader.js          # Module loader
│   ├── state-manager.js          # State management
│   └── route-manager.js          # Route management
├── engines/                      # Main engines
│   ├── table-engine.js           # Table engine
│   ├── form-engine.js            # Form engine
│   ├── crud-engine.js            # CRUD engine
│   └── bulk-engine.js            # Bulk operations engine
├── plugins/                      # Plugins
│   ├── actions/                  # Action plugins
│   ├── columns/                  # Column plugins
│   └── ui/                       # UI plugins
├── ui/                           # Renderers
│   ├── table-renderer.js         # Table renderer
│   ├── form-renderer.js          # Form renderer
│   └── modal.js                  # Modal
└── admin-lang.js                 # Translation management
```

## 📁 Package Structure

```
src/
├── AdminPlatformServiceProvider.php    # Main service provider
├── Console/Commands/MakeAdminUI.php    # Module generator command
├── Contracts/AdminModule.php           # Module interface
├── Controllers/ModuleController.php    # Module list controller
├── Services/ModuleService.php          # Module service
├── Support/ModuleRegistry.php          # Module registry
└── Traits/AdminTableTrait.php          # Admin table trait
```

## 🔒 Security Notes

- **Sort Validation**: `AdminTableTrait` validates `sort` columns against a whitelist to prevent SQL injection. Extend via `$config['sortable']`.
- **Direction Sanitization**: Only `asc`/`desc` are accepted; anything else defaults to `asc`.
- **Per-Page Cap**: Maximum `per_page` is capped at 100 to prevent performance issues.
- **CSRF Protection**: Ensure `<meta name="csrf-token">` is present in your layout.
- **Role Checks**: Compatible with Spatie Permission; roles/permissions are injected via `window.AdminUser`.

## 🧩 Troubleshooting

| مشکل | راه‌حل |
|------|--------|
| خطای ۴۰۴ فایل‌های JS | `php artisan vendor:publish --tag=admin-platform-assets` |
| خطای CSRF | تگ `<meta name=\"csrf-token\">` را اضافه کنید |
| `window.AdminUser` خالی | بدون سیستم نقش هم کار می‌کند |
| مسیرهای API 404 | نام رووت‌ها در `table.js` با `web.php` هماهنگ کنید |
| مشکل رنگ جدول | Tailwind content paths را به‌روزرسانی کنید |

## 📄 License

MIT License
