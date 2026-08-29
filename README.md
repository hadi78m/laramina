# Laramina for Laravel

> **A modular admin panel package for Laravel 10/11/12** — Dynamic CRUD modules with modern JavaScript and Tailwind CSS.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 🇮🇷 فارسی

### ✨ ویژگی‌ها

- **تولید خودکار ماژول** — دستور Artisan برای ساخت جدول، فرم و عملیات CRUD
- **فرانت‌اند ماژولار** — معماری مبتنی بر موتورها (Table Engine, Form Engine, CRUD Engine)
- **Eager Loading** — پشتیبانی از بارگذاری روابط مدل با `$config['with']` برای جلوگیری از N+1 Query
- **پشتیبانی از نقش و دسترسی** — سازگار با Spatie Permission
- **چندزبانه** — ترجمه فارسی و انگلیسی با سیستم Lang لاراول
- **فرم‌های پویا** — گروه‌بندی فیلدها، Select Box با دریافت از API، فرم‌های جداگانه ایجاد/ویرایش
- **تبلت تعاملی** — جستجو، فیلتر، مرتب‌سازی، صفحه‌بندی و تغییر وضعیت با AJAX
- **فرانت‌اند خالص** — بدون وابستگی به Vue/React، فقط AlpineJS + Tailwind CSS
- **امنیت** — اعتبارسنجی امنیتی sort/filter در سمت سرور

---

## 📦 نصب و راه‌اندازی

### پیش‌نیازها

- PHP 8.1+
- Laravel 10 / 11 / 12
- Tailwind CSS + AlpineJS (در پروژه اصلی)

### ۱. نصب از طریق Composer

#### روش A: نصب از Packagist (توصیه شده)

```bash
composer require hadii/laramina
```

#### روش B: نصب از مسیر محلی (توسعه)

اگر پکیج را بصورت محلی دارید:

```bash
# کپی پکیج به پروژه
cp -r /path/to/laramina packages/laramina
```

فایل `composer.json` پروژه اصلی:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/laramina"
    }
]
```

```bash
composer require hadii/laramina:@dev
```

### ۲. انتشار دارایی‌ها

```bash
# انتشار همه دارایی‌ها به صورت یکجا (توصیه شده)
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views  # اختیاری
```

### ۳. تنظیم زبان

در فایل `.env`:

```env
APP_LOCALE=fa
```

### ۴. به‌روزرسانی Tailwind

فایل `tailwind.config.js`:

```js
content: [
    // کدهای قبلی
    "./public/js/admin-platform/**/*.js",
    "./public/js/custom/**/*.js",
    "./vendor/hadii/laramina/resources/js/**/*.js",
],
```

```bash
npm run build
```

### ۵. افزودن به لایه‌اوت

در فایل `resources/views/layouts/app.blade.php`:

```blade
@include('laramina::adminPlatform')
```

یا در صورت انتشار ویو:

```blade
@include('vendor.laramina.adminPlatform')
```

### ۶. ایجاد ماژول جدید

```bash
php artisan laramina:make-ui User
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

> **نکته**: ماژول‌های تک‌بخشی مثل `User` بصورت خودکار در `config/laramina.php` ثبت می‌شوند.

---

## ⚙️ راهنمای کانفیگ هر فایل

### ۱. `config/laramina.php` — ثبت ماژول‌ها

فایل اصلی تنظیمات پکیج. ماژول‌های خود را اینجا ثبت کنید:

```php
<?php

return [
    'modules' => [
        // ماژول‌های ساده (تک‌بخشی)
        'users' => [
            'label' => 'کاربران',
            'icon'  => 'fas fa-users',
            'route' => 'users.index',
        ],

        // ماژول‌های تو در تو (چندبخشی)
        'sms/credentials' => [
            'label' => 'اعتبارنامه‌ها',
            'icon'  => 'fas fa-key',
            'route' => 'sms.credentials.index',
        ],
    ],
];
```

| فیلد      | نوع    | توضیحات                                      |
| :-------- | :----- | :------------------------------------------- |
| `label`   | string | نام نمایشی ماژول در منو                      |
| `icon`    | string | کلاس آیکون FontAwesome                       |
| `route`   | string | نام رووت صفحه اصلی ماژول                     |

---

### ۲. `table.js` — تنظیمات جدول

فایل اصلی پیکربندی جدول. endpoint، ستون‌ها، فیلترها و مودال‌ها را تعریف می‌کند.

```js
import { createForm } from './forms/create-form.js'
import { userActions } from './actions.js'

const publicLang   = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('modules.users.fields');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export default {

    // ─── Endpoint دریافت داده (GET) ───
    endpoint: 'users.json',

    // ─── جستجو ───
    search: true,

    // ─── عنوان هدر و دکمه ───
    headerTitle: moduleFields.header_title || (publicLang.manage + ' ' + (moduleFields.title || 'users')),
    addButtonLabel: moduleActions.create || (publicLang.create + ' ' + (moduleActions.item || publicLang.item)),
    displayButton: true,

    // ─── اکشن‌های ردیفی (edit/delete) ───
    actions: userActions,

    // ─── تعداد ردیف در هر صفحه ───
    perPage: 10,

    // ─── تم مودال ───
    modalTheme: 'light',

    // ─── تعریف مودال‌ها ───
    modals: {
        create: {
            title: moduleActions.create || publicLang.create,
            width: '500px',
            form: createForm
        },
        edit: {
            title: moduleActions.edit || publicLang.edit,
            width: '500px',
            form: createForm
        }
    },

    // ─── فیلترها ───
    filters: [
        {
            key: 'is_active',
            label: publicLang.status,
            type: 'select',
            options: {
                1: publicLang.active,
                0: publicLang.inactive
            }
        }
    ],

    // ─── ستون‌ها ───
    columns: [
        { key: 'id', label: publicLang.id, sortable: true },
        { key: 'name', label: publicLang.name, sortable: true },
        { key: 'email', label: publicLang.email },
        {
            key: 'is_active',
            label: publicLang.status,
            type: 'action-toggle',
            endpoint: 'users.toggle-status',
            confirmTitle: publicLang.confirm_toggle_status,
            map: {
                true: { label: publicLang.active, color: 'green' },
                false: { label: publicLang.inactive, color: 'gray' }
            },
            icons: {
                true: { html: '<i class="fa-solid fa-toggle-on text-lg"></i>', color: 'green' },
                false: { html: '<i class="fa-solid fa-toggle-off text-lg"></i>', color: 'red' }
            }
        },
        {
            label: publicLang.actions,
            type: 'actions',
            actions: ['edit', 'delete']
        }
    ],
}
```

**نکات مهم:**
- `endpoint` نام رووت GET است که داده جدول را برمی‌گرداند
- `type: 'action-toggle'` برای ستون‌های toggle (فعال/غیرفعال) استفاده می‌شود
- `type: 'actions'` برای دکمه‌های عملیات ردیفی (edit/delete) است

---

### ۳. `forms/create-form.js` — تنظیمات فرم ایجاد/ویرایش

ساختار فیلدها و endpoint عملیات فرم:

```js
const publicLang   = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('modules.users.fields');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export const createForm = {

    // ─── Endpoint ایجاد (POST) ───
    endpoint: 'users.store',

    // ─── Endpoint ویرایش (POST) ───
    updateEndpoint: 'users.update',

    // ─── Endpoint حذف (POST) ───
    deleteEndpoint: 'users.destroy',

    // ─── عنوان فرم ───
    title: moduleActions.create || publicLang.create,

    // ─── فیلدها ───
    fields: [
        { name: 'name', label: publicLang.name, type: 'text' },
        { name: 'email', label: publicLang.email, type: 'email' },
        { name: 'password', label: publicLang.password, type: 'text' },
        { name: 'is_active', label: publicLang.is_active, type: 'checkbox' },
    ],

    // ─── دکمه‌ها ───
    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};
```

**انواع فیلدها:**

| نوع          | توضیحات                              |
| :----------- | :----------------------------------- |
| `text`       | ورودی متنی ساده                      |
| `email`      | ورودی ایمیل                          |
| `password`   | ورودی رمز عبور                       |
| `date`       | انتخابگر تاریخ                       |
| `checkbox`   | چک‌باکس با حالت مخفی (hidden+checkbox) برای سازگاری با boolean |
| `select`     | انتخاب از لیست                       |
| `group`      | گروه‌بندی چند فیلد در یک ردیف         |

> ⚠️ **نکته امنیتی:** فیلد `checkbox` بصورت خودکار از الگوی `hidden + checkbox` استفاده می‌کند تا مقدار `0` یا `1` صحیح به سرور ارسال شود. این باعث سازگاری کامل با اعتبارسنجی `boolean` لاراول می‌شود.

**نمونه فیلد Select با دریافت از API:**

```js
{
    name: 'roles',
    label: 'نقش‌ها',
    type: 'select',
    multiple: true,
    optionEndpoint: 'manage.roles.list',
    optionLabel: 'name',
    optionValue: 'name',
}
```

**نمونه فیلد گروهی:**

```js
{
    type: 'group',
    group: [
        { name: 'first_name', label: 'نام', type: 'text', required: true },
        { name: 'last_name', label: 'نام خانوادگی', type: 'text', required: true },
    ]
}
```

---

### ۴. `actions.js` — اکشن‌های ردیفی

تعریف اکشن‌های edit/delete و تابع setToggle:

```js
import ModalPlugin from '/js/admin-platform/plugins/ui/modal/modal-plugin.js'
import FormEngine from '/js/admin-platform/engines/form-engine.js'
import { createForm } from './forms/create-form.js'
import { action } from '/js/admin-platform/core/action.js'

const publicLang    = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export const userActions = {

    // ─── اکشن ویرایش ───
    edit: action({
        icon: 'fas fa-edit',
        color: 'text-blue-600',
        size: 'text-lg',
        tooltip: publicLang.edit,
    }, (row, table, event) => {
        const url = AppAlert.route(createForm.updateEndpoint, { id: row.id });
        event?.preventDefault()

        ModalPlugin.open({
            title: moduleActions.edit || publicLang.edit,
            width: '500px',
            content: (container) => {
                const config = {
                    ...createForm,
                    endpoint: url,
                    method: 'PUT'
                }
                FormEngine.render(config, container, row)
            }
        })
    }),

    // ─── اکشن حذف ───
    delete: action({
        icon: 'fas fa-trash',
        color: 'text-red-600',
        size: 'text-lg',
        tooltip: publicLang.delete,
    }, async (row, table, event) => {
        event?.preventDefault()
        const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });
        const res = await AppAlert.confirmDelete(url, {
            title: moduleActions.delete_title || (publicLang.delete + ' ' + (moduleActions.item || publicLang.item)),
        })
        if (res) {
            document.dispatchEvent(
                new CustomEvent('admin:table:remove-row', { detail: { id: row.id } })
            )
        }
    }),

    // ─── تابع toggle وضعیت ───
    setToggle(id, table, endpoint) {
        const url = AppAlert.route(endpoint, { id });
        return AppAlert.post(url, {}, { loading: true, successAlert: true })
            .done((res) => {
                if (res.update && res.update == 'table') {
                    if (typeof table.loadData === 'function') table.loadData();
                    return;
                }
                if (res.provider && typeof table.updateRow === 'function') {
                    table.updateRow(res.provider);
                    return;
                }
                const oldRow = table.getRowById
                    ? table.getRowById(id)
                    : (table.currentRows || []).find(r => r.id == id);
                if (!oldRow) return;
                const allowedKeys = ['is_active', 'is_default'];
                const patch = {};
                allowedKeys.forEach(k => { if (k in res) patch[k] = res[k]; });
                const newRow = { ...oldRow, ...patch };
                if (typeof table.updateRow === 'function') table.updateRow(newRow);
            });
    },
};
```

> **نکته:** `AppAlert.post()` بصورت jQuery Deferred برمی‌گردد تا با `.done()` سازگار باشد.

---

### ۵. `module.js` — ماژول اصلی

نقطه ورود ماژول که table و init را export می‌کند:

```js
import tableConfig from './table.js'

export default {
    name: 'users',

    table: tableConfig,

    init() {
        console.log('users module loaded')
    }
}
```

---

### ۶. `resources/lang/fa/adminUI.php` — ترجمه‌ها

فایل ترجمه فارسی. ساختار باید شامل `common` و `modules.{resource}` باشد:

```php
<?php

return [
    // ترجمه‌های عمومی (برای همه ماژول‌ها مشترک)
    'common' => [
        'id'         => 'شناسه',
        'name'       => 'نام',
        'email'      => 'ایمیل',
        'status'     => 'وضعیت',
        'active'     => 'فعال',
        'inactive'   => 'غیرفعال',
        'create'     => 'ایجاد',
        'edit'       => 'ویرایش',
        'delete'     => 'حذف',
        'save'       => 'ذخیره',
        'cancel'     => 'انصراف',
        'actions'    => 'عملیات',
        'manage'     => 'مدیریت',
        'item'       => 'آیتم',
        'created_at' => 'تاریخ ساخت',
        'updated_at' => 'تاریخ بروزرسانی',
        'is_active'  => 'فعال',
        'password'   => 'رمز عبور',
        'confirm_toggle_status' => 'آیا از تغییر وضعیت اطمینان دارید؟',
    ],

    // ترجمه‌های اختصاصی هر ماژول
    'modules' => [
        'users' => [
            'fields' => [
                'title'       => 'کاربران',
                'headerTitle' => 'مدیریت کاربران',
                'roles'       => 'نقش',
            ],
            'actions' => [
                'create' => 'ایجاد کاربر جدید',
                'edit'   => 'ویرایش کاربر',
            ],
        ],
    ],
];
```

**نکته:** کلیدهای `common` برای فیلدهای مشترک (name, email, ...) و کلیدهای `modules.{resource}` برای فیلدهای اختصاصی ماژول استفاده می‌شوند.

---

### ۷. `resources/views/{module}/index.blade.php` — ویوی Blade

ویوی اصلی ماژول که ماژول JS را لود می‌کند:

```blade
@extends('layouts.app')

@section('content')
    <div id="admin-module" data-module="users"></div>
@endsection
```

> **نکته:** مقدار `data-module` باید با نام پوشه ماژول در `public/js/modules/` مطابقت داشته باشد.

---

## 🎯 نمونه کنترلر

### کنترلر ساده (CRUD کامل)

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * صفحه اصلی ماژول
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * دریافت داده جدول (JSON)
     * - جستجو، فیلتر، مرتب‌سازی و صفحه‌بندی
     */
    public function json(Request $request)
    {
        return User::adminTable($request, [
            'search'   => ['name', 'email'],           // فیلدهای قابل جستجو
            'filters'  => ['is_active'],                // فیلترهای مجاز
            'sortable' => ['name', 'email', 'created_at'], // ستون‌های مجاز مرتب‌سازی
            'with'     => ['roles', 'permissions'],     // Eager Loading روابط
        ]);
    }

    /**
     * ایجاد رکورد جدید
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        return response()->json([
            'success'  => true,
            'provider' => $user->only('id', 'name', 'email', 'is_active', 'created_at'),
        ]);
    }

    /**
     * ویرایش رکورد
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$id}",
            'password' => 'nullable|string|min:6',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success'  => true,
            'provider' => $user->only('id', 'name', 'email', 'is_active', 'created_at'),
        ]);
    }

    /**
     * حذف رکورد
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    /**
     * تغییر وضعیت فعال/غیرفعال
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success'  => true,
            'is_active' => $user->is_active,
        ]);
    }
}
```

### تعریف مسیرها (Route)

```php
use App\Http\Controllers\UserController;

Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    Route::get('/',              [UserController::class, 'index'])->name('index');
    Route::get('/json',          [UserController::class, 'json'])->name('json');
    Route::post('/',             [UserController::class, 'store'])->name('store');
    Route::post('/update/{id}',  [UserController::class, 'update'])->name('update');
    Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```

### استفاده از AdminTableTrait در مدل

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laramina\Traits\AdminTableTrait;

class User extends Model
{
    use AdminTableTrait;

    protected $fillable = ['name', 'email', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * روابط Eager Load
     */
    public function roles()
    {
        return $this->belongsToMany('roles'); // Spatie Permission
    }

    /**
     * تبدیل داده برای نمایش در جدول
     */
    public static function adminTransform($cred)
    {
        return [
            'id'         => $cred->id,
            'name'       => $cred->name,
            'email'      => $cred->email,
            'is_active'  => $cred->is_active,
            'created_at' => $cred->created_at?->format('Y/m/d H:i'),
        ];
    }
}
```

> **نکته:** برای استفاده از Eager Loading، کافیست `'with' => ['roles']` را به آرایه config در `adminTable()` اضافه کنید. سیستم بصورت خودکار `$query->with()` را فراخوانی می‌کند.

### کنترلر تو در تو (ماژول چندبخشی)

برای ماژول‌هایی مثل `Sms\Credential` که slug آن‌ها `sms/credentials` است:

```php
<?php

namespace App\Http\Controllers\Sms;

use App\Models\Sms\Credential;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function index()
    {
        return view('sms.credentials.index');
    }

    public function json(Request $request)
    {
        return Credential::adminTable($request, [
            'search'   => ['title', 'helper_name'],
            'filters'  => ['token_status'],
            'sortable' => ['title', 'created_at'],
            'with'     => ['provider'],  // Eager Loading
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'helper_name' => 'required|string',
            'send_rate'   => 'required|integer',
        ]);

        $credential = Credential::create($validated);

        return response()->json([
            'success'  => true,
            'provider' => $credential,
        ]);
    }

    // ... update, destroy, toggleStatus مشابه مثال بالا
}
```

> **نکته:** نام رووت‌ها برای ماژول تو در تو بصورت `sms.credentials.json`، `sms.credentials.store` و ... خواهد بود.

---

## 🇬🇧 English

### Features

- **Auto Module Generation** — Artisan command to scaffold table, forms, and CRUD operations
- **Modular Frontend** — Engine-based architecture (Table Engine, Form Engine, CRUD Engine)
- **Eager Loading** — Support for loading model relations via `$config['with']` to prevent N+1 queries
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

**Option A: From Packagist (Recommended)**

```bash
composer require hadii/laramina
```

**Option B: From Local Path (Development)**

If you have the package locally:

```bash
# Copy package to project
cp -r /path/to/laramina packages/laramina
```

Add to your main project's `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/laramina"
    }
]
```

```bash
composer require hadii/laramina:@dev
```

#### 2. Publish Assets

```bash
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views  # Optional
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
    "./vendor/hadii/laramina/resources/js/**/*.js",
],
```

```bash
npm run build
```

#### 5. Add to Layout

In `resources/views/layouts/app.blade.php`:

```blade
@include('laramina::adminPlatform')
```

#### 6. Generate a New Module

```bash
php artisan laramina:make-ui User
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
├── LaraminaServiceProvider.php    # Main service provider
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
- **Eager Loading**: Use `$config['with']` to load relations and prevent N+1 queries.
- **CSRF Protection**: Ensure `<meta name="csrf-token">` is present in your layout.
- **Checkbox Fix**: Checkbox fields use `hidden + checkbox` pattern to ensure `0/1` values (not `on/`).
- **Action Toggle**: Toggle status uses `Swal.fire()` directly for confirmation (not `AppAlert.confirmAction()`).
- **jQuery Deferred**: `AppAlert.post()` returns jQuery Deferred (not native Promise) for `.done()` compatibility.
- **Role Checks**: Compatible with Spatie Permission; roles/permissions are injected via `window.AdminUser`.

## 🧩 Troubleshooting

| مشکل | راه‌حل |
|------|--------|
| خطای ۴۰۴ فایل‌های JS | `php artisan vendor:publish --tag=laramina-assets --force` |
| خطای CSRF | تگ `<meta name="csrf-token">` را اضافه کنید |
| `window.AdminUser` خالی | بدون سیستم نقش هم کار می‌کند |
| مسیرهای API 404 | نام رووت‌ها در `table.js` با `web.php` هماهنگ کنید |
| مشکل رنگ جدول | Tailwind content paths را به‌روزرسانی کنید |
| checkbox مقدار اشتباه ارسال می‌کند | فیلدها بصورت خودکار از hidden+checkbox استفاده می‌کنند |
| toggle status کار نمی‌کند | از `Swal.fire()` مستقیم استفاده شده (نه `confirmAction`) |
| فرم ویرایش گیر می‌کند | `AppAlert.post()` jQuery Deferred برمی‌گرداند |

## 🧪 تست بعد از انتشار

برای تست پکیج بعد از انتشار در Packagist:

```bash
# ایجاد پروژه تستی
composer create-project laravel/laravel test-laramina
cd test-laramina

# نصب پکیج
composer require hadii/laramina

# انتشار دارایی‌ها
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang

# اجرای سرور
php artisan serve
```

مرورگر: `http://localhost:8000/admin/users`

> 📖 راهنمای کامل تست در فایل `TEST-GUIDE.md` موجود است.

---

## 📄 License

MIT License
