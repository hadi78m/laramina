# 🧪 راهنمای تست دستی Laramina

> **هدف:** بررسی صحت عملکرد پکیج قبل از انتشار در Packagist

---

## 📋 پیش‌نیازها

- PHP 8.1+
- Laravel 10/11/12
- MySQL/SQLite
- Composer
- Node.js (اختیاری)

---

## گام ۱: ایجاد پروژه تستی

```bash
# ایجاد پروژه لاراول جدید
composer create-project laravel/laravel laramina-test-app

cd laramina-test-app
```

---

## گام ۲: اضافه کردن پکیج

### روش A: نصب از Packagist (توصیه شده)

```bash
composer require hadii/laramina
```

### روش B: نصب از مسیر محلی (توسعه)

```bash
# کپی پکیج به پروژه
mkdir -p packages
cp -r /path/to/laramina packages/laramina
```

ویرایش `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/laramina"
        }
    ]
}
```

```bash
composer require hadii/laramina:@dev
```

### بررسی نصب

```bash
php artisan package:discover
```

باید `hadii/laramina` در لیست باشد.

---

## گام ۳: انتشار دارایی‌ها

```bash
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
```

**بررسی:** آیا فایل‌های زیر ایجاد شدند؟

- `config/laramina.php`
- `public/js/admin-platform/`
- `resources/lang/vendor/laramina/fa/`
- `resources/lang/vendor/laramina/en/`

---

## گام ۴: تنظیم پایگاه داده

### ۴.۱ ویرایش .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laramina_test
DB_USERNAME=root
DB_PASSWORD=your_password
APP_LOCALE=fa
```

### ۴.۲ ایجاد دیتابیس

```sql
CREATE DATABASE laramina_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### ۴.۳ اجرای migration

```bash
php artisan migrate
```

### ۴.۴ اضافه کردن ستون is_active

```bash
php artisan make:migration add_is_active_to_users_table
```

ویرایش migration:

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_active')->default(true)->after('email');
});
```

```bash
php artisan migrate
```

---

## گام ۵: ایجاد فایل‌های مورد نیاز

### ۵.۱ مدل User

فایل `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laramina\Traits\AdminTableTrait;

class User extends Authenticatable
{
    use HasFactory, Notifiable, AdminTableTrait;

    protected $fillable = ['name', 'email', 'password', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function adminTransform($user)
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'is_active'  => $user->is_active,
            'created_at' => $user->created_at?->format('Y/m/d H:i'),
        ];
    }
}
```

### ۵.۲ کنترلر

```bash
mkdir -p app/Http/Controllers/Admin
```

فایل `app/Http/Controllers/Admin/UserController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function json(Request $request)
    {
        return User::adminTable($request, [
            'search'   => ['name', 'email'],
            'filters'  => ['is_active'],
            'sortable' => ['name', 'email', 'created_at'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'is_active' => 'boolean',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        return response()->json([
            'success'  => true,
            'provider' => $user->only('id', 'name', 'email', 'is_active', 'created_at'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => "required|email|unique:users,email,{$id}",
            'password'  => 'nullable|string|min:6',
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

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $user->is_active,
        ]);
    }
}
```

### ۵.۳ مسیرها

فایل `routes/admin.php`:

```php
<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/users')->name('users.')->group(function () {
    Route::get('/',              [UserController::class, 'index'])->name('index');
    Route::get('/json',          [UserController::class, 'json'])->name('json');
    Route::post('/',             [UserController::class, 'store'])->name('store');
    Route::post('/update/{id}',  [UserController::class, 'update'])->name('update');
    Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```

ویرایش `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/admin.php';
```

### ۵.۴ ویوها

```bash
mkdir -p resources/views/admin/users resources/views/layouts
```

فایل `resources/views/layouts/app.blade.php`:

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
</head>
<body>
    @yield('content')
    @include('laramina::adminPlatform')
</body>
</html>
```

فایل `resources/views/admin/users/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div id="admin-module" data-module="users"></div>
    </div>
@endsection
```

### ۵.۵ فایل‌های JS

```bash
mkdir -p public/js/modules/users/forms
```

فایل `public/js/modules/users/module.js`:

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

فایل `public/js/modules/users/table.js`:

```js
import { createForm } from './forms/create-form.js'
import { userActions } from './actions.js'

const publicLang = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('modules.users.fields');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export default {
    endpoint: 'users.json',
    search: true,
    headerTitle: moduleFields.header_title || (publicLang.manage + ' ' + (moduleFields.title || 'users')),
    addButtonLabel: moduleActions.create || (publicLang.create + ' ' + (moduleActions.item || publicLang.item)),
    displayButton: true,
    actions: userActions,
    perPage: 10,
    modalTheme: 'light',
    modals: {
        create: { title: moduleActions.create || publicLang.create, width: '500px', form: createForm },
        edit: { title: moduleActions.edit || publicLang.edit, width: '500px', form: createForm }
    },
    filters: [
        { key: 'is_active', label: publicLang.status, type: 'select', options: { 1: publicLang.active, 0: publicLang.inactive } }
    ],
    columns: [
        { key: 'id', label: publicLang.id, sortable: true },
        { key: 'name', label: publicLang.name, sortable: true },
        { key: 'email', label: publicLang.email },
        {
            key: 'is_active', label: publicLang.status, type: 'action-toggle',
            endpoint: 'users.toggle-status',
            confirmTitle: publicLang.confirm_toggle_status,
            map: { true: { label: publicLang.active, color: 'green' }, false: { label: publicLang.inactive, color: 'gray' } },
            icons: { true: { html: '<i class="fa-solid fa-toggle-on text-lg"></i>', color: 'green' }, false: { html: '<i class="fa-solid fa-toggle-off text-lg"></i>', color: 'red' } }
        },
        { label: publicLang.actions, type: 'actions', actions: ['edit', 'delete'] }
    ],
}
```

فایل `public/js/modules/users/actions.js`:

```js
import ModalPlugin from '/js/admin-platform/plugins/ui/modal/modal-plugin.js'
import FormEngine from '/js/admin-platform/engines/form-engine.js'
import { createForm } from './forms/create-form.js'
import { action } from '/js/admin-platform/core/action.js'

const publicLang = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export const userActions = {
    edit: action({ icon: 'fas fa-edit', color: 'text-blue-600', size: 'text-lg', tooltip: publicLang.edit },
        (row, table, event) => {
            const url = AppAlert.route(createForm.updateEndpoint, { id: row.id });
            event?.preventDefault()
            ModalPlugin.open({
                title: moduleActions.edit || publicLang.edit, width: '500px',
                content: (container) => { FormEngine.render({ ...createForm, endpoint: url, method: 'PUT' }, container, row) }
            })
        }),
    delete: action({ icon: 'fas fa-trash', color: 'text-red-600', size: 'text-lg', tooltip: publicLang.delete },
        async (row, table, event) => {
            event?.preventDefault()
            const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });
            const res = await AppAlert.confirmDelete(url, { title: moduleActions.delete_title || (publicLang.delete + ' ' + (moduleActions.item || publicLang.item)) })
            if (res) { document.dispatchEvent(new CustomEvent('admin:table:remove-row', { detail: { id: row.id } })) }
        }),
    setToggle(id, table, endpoint) {
        const url = AppAlert.route(endpoint, { id });
        return AppAlert.post(url, {}, { loading: true, successAlert: true })
            .done((res) => {
                if (res.update && res.update == 'table') { if (typeof table.loadData === 'function') table.loadData(); return; }
                if (res.provider && typeof table.updateRow === 'function') { table.updateRow(res.provider); return; }
                const oldRow = table.getRowById ? table.getRowById(id) : (table.currentRows || []).find(r => r.id == id);
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

فایل `public/js/modules/users/forms/create-form.js`:

```js
const publicLang = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export const createForm = {
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',
    title: moduleActions.create || publicLang.create,
    fields: [
        { name: 'name', label: publicLang.name, type: 'text' },
        { name: 'email', label: publicLang.email, type: 'email' },
        { name: 'password', label: publicLang.password, type: 'password' },
        { name: 'is_active', label: publicLang.is_active, type: 'checkbox' },
    ],
    buttons: { submit: publicLang.save, cancel: publicLang.cancel }
};
```

### ۵.۶ ثبت ماژول در کانفیگ

ویرایش `config/laramina.php`:

```php
<?php

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

### ۵.۷ Seeder (اختیاری)

```bash
php artisan make:seeder UserSeeder
```

فایل `database/seeders/UserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            User::create(['name' => 'مدیر', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'is_active' => true]);
            User::create(['name' => 'کاربر', 'email' => 'user@example.com', 'password' => bcrypt('password'), 'is_active' => true]);
        }
    }
}
```

```bash
php artisan db:seed --class=UserSeeder
```

---

## گام ۶: تست نهایی

### ۶.۱ اجرای سرور

```bash
php artisan serve
```

### ۶.۲ باز کردن مرورگر

```
http://localhost:8000/admin/users
```

### ۶.۳ چک‌لیست تست

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

### ۶.۴ بررسی کنسول مرورگر

کلید `F12` → تب Console

**پیام‌های مجاز:**
- `users module loaded` ✅

**پیام‌های خطا (نباید وجود داشته باشند):**
- `404 Not Found` ❌
- `419 Page Expired` ❌
- `SyntaxError` ❌
- `Uncaught TypeError` ❌

---

## گام ۷: تست API مستقیم

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

## عیب‌یابی

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
