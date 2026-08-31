# راهنمای نصب و راه‌اندازی Laramina

> **ابزار تولید جدول و CRUD ماژولار برای لاراول**

---

## فهرست مطالب

1. [نصب و انتشار](#۱-نصب-و-انتشار)
2. [تنظیمات اولیه](#۲-تنظیمات-اولیه)
3. [ایجاد ماژول](#۳-ایجاد-ماژول)
4. [تنظیمات ماژول](#۴-تنظیمات-ماژول)
5. [پکیج‌های مکمل](#۵-پکیج‌های-مکمل)
6. [نقش و دسترسی (بدون Spatie)](#۶-نقش-و-دسترسی-بدون-spatie)
7. [API و مسیرها](#۷-api-و-مسیرها)
8. [امنیت](#۸-امنیت)
9. [عیب‌یابی](#۹-عیب‌یابی)
10. [تست‌ها](#۱۰-تست‌ها)

---

## ۱. نصب و انتشار

### ۱.۱ پیش‌نیازها

| نیاز | حداقل نسخه | توضیحات |
|------|-----------|---------|
| PHP | 8.1+ | با پشتیبانی از PDO |
| Laravel | 10 / 11 / 12 / 13 | با `composer` و `artisan` |
| jQuery | 3.x | برای AJAX و عملیات فرم |
| Database | MySQL / SQLite / PostgreSQL | SQLite نیاز به `pdo_sqlite` دارد |

> ⚠️ **نکته:** این پکیج به هیچ پکیج جانبی وابسته نیست.

### ۱.۲ نصب پکیج

```bash
composer require hadii/laramina
```

### ۱.۳ انتشار دارایی‌ها

```bash
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views
```

| دارایی      | مسیر مقصد                                                           |
| ----------- | ------------------------------------------------------------------- |
| کانفیگ      | `config/laramina.php`                                               |
| جاوااسکریپت | `public/js/laramina/`, `public/js/custom/`, `public/js/sweetalert/` |
| ترجمه‌ها    | `resources/lang/vendor/laramina/fa/` و `en/`                        |
| ویوها       | `resources/views/vendor/laramina/adminPlatform.blade.php`           |

---

## ۲. تنظیمات اولیه

### ۲.۱ تنظیم زبان

فایل `.env`:

```env
APP_LOCALE=fa
```

### ۲.۲ به‌روزرسانی Tailwind (اختیاری)

فایل `tailwind.config.js`:

```js
content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./public/js/laramina/**/*.js",
    "./public/js/custom/**/*.js",
]
```

```bash
npm run build
```

### ۲.۳ لایه‌اوت

فایل `resources/views/layouts/app.blade.php` را ویرایش کنید:
>  1. فراخوانی jQuery اگر قبلا استفاده نشده است
>
> 2. افزودن `@include('laramina::adminPlatform')`  به layout مورد استفاده خود قبل از بسته شدن تگ body  مشابه فایل زیر 

```blade
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        {{-- استفاده از vite --}}
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

        {{-- یا استفاده از cdn --}}
        <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @yield('content')

    {{-- Laramina Admin Platform --}}
    @include('laramina::adminPlatform')
</body>
</html>
```

> فراخوانی ⚠️ jQuery باید قبل از `adminPlatform` لود شود.
> می توانید از cdn یا vite برای tailwindcss استفاده کنید


## ۳. ایجاد ماژول

#### پیش نیازها : 
##### 1.  مدل

###### باید یک مدل از قبل وجود داشته باشد،  یا با استفاده از `php artisan make:model` ساخته شود (نمونه مدل با فانکشن adminTransform و فراخوانی AdminTableTrait  جهت کارکرد مناسب پکیج)

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

##### 2. کنترلر

###### نمونه استاندارد json با استفاده از `adminTable`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function json(Request $request)
    {
        return User::adminTable($request, [
            'search'   => ['name', 'email'],
            'filters'  => ['is_active'],
            'sortable' => ['name', 'email', 'created_at'],
            'with'     => [],  // Eager Loading روابط
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

##### ۳. مسیرهای استاندارد 

```php
use App\Http\Controllers\UserController;

Route::prefix('users')->name('users.')->middleware('web')->group(function () {
    Route::get('/',              [UserController::class, 'index'])->name('index');
    Route::get('/json',          [UserController::class, 'json'])->name('json');
    Route::post('/',             [UserController::class, 'store'])->name('store');
    Route::post('/update/{id}',  [UserController::class, 'update'])->name('update');
    Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```

### تولید خودکار ماژول با استفاده از دستور آرتیسان

#### ایجاد ماژول برای هر مدلی که نیاز دارید مانند post ، user، category و غیره    

```bash
php artisan laramina:make-ui User --force
```

##### فایل‌های تولید شده پس از اجرای دستور :
```
public/js/modules/users/
├── module.js
├── table.js
├── actions.js
└── forms/create-form.js

resources/views/users/
└── index.blade.php
```


نمونه استاندارد فایل `config/laramina.php`: که پس از ایجاد ماژول خودکار اضافه می‌شود.

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

> توضیح : این نمونه است و خود پکیج با ساخت هر ماژولی، فیلد `modules` را به خود اضافه می‌کند.

| فیلد    | نوع    | توضیح                  |
| ------- | ------ | ---------------------- |
| `label` | string | نام نمایشی در منو      |
| `icon`  | string | کلاس آیکون FontAwesome |
| `route` | string | نام رووت صفحه اصلی     |

---

#### نمونه فایل های ساخته شده پس از اجرای دستور ساخت ماژول
##### 1.  فایل‌های JS

**`public/js/modules/users/module.js`:**

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

**`public/js/modules/users/table.js`:**

```js
import { createForm, editForm } from './forms/create-form.js'
import { userActions } from './actions.js'

const publicLang    = AdminLang.getNamespace('common');
const moduleFields  = AdminLang.getNamespace('modules.users.fields');
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
        edit:   { title: moduleActions.edit || publicLang.edit, width: '500px', form: editForm }
    },
    filters: [
        {
            key: 'is_active', label: publicLang.status,
            type: 'select', options: { 1: publicLang.active, 0: publicLang.inactive }
        }
    ],
    columns: [
        { key: 'id', label: publicLang.id, sortable: true },
        { key: 'name', label: publicLang.name, sortable: true },
        { key: 'email', label: publicLang.email },
        {
            key: 'is_active', label: publicLang.status,
            type: 'action-toggle', endpoint: 'users.toggle-status',
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
        { label: publicLang.actions, type: 'actions', actions: ['edit', 'delete'] }
    ],
}
```

**`public/js/modules/users/forms/create-form.js`:**

```js
const publicLang    = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

const commonFields = [
    { name: 'name', label: publicLang.name, type: 'text' },
    { name: 'email', label: publicLang.email, type: 'email' },
];

export const createForm = {
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',
    title: moduleActions.create || publicLang.create,
    fields: [
        ...commonFields,
        { name: 'password', label: publicLang.password, type: 'password', required: true, min: 6 },
    ],
    buttons: { submit: publicLang.save, cancel: publicLang.cancel }
};

export const editForm = {
    title: moduleActions.edit || publicLang.edit,
    fields: [
        ...commonFields,
        { name: 'password', label: 'رمز عبور جدید', type: 'password', hideValue: true },
    ],
    buttons: { submit: publicLang.save, cancel: publicLang.cancel }
};
```

**`public/js/modules/users/actions.js`:**

```js
import ModalPlugin from '/js/laramina/plugins/ui/modal/modal-plugin.js'
import FormEngine from '/js/laramina/engines/form-engine.js'
import { createForm, editForm } from './forms/create-form.js'
import { action } from '/js/laramina/core/action.js'

const publicLang    = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export const userActions = {
    edit: action({
        icon: 'fas fa-edit', color: 'text-blue-600', size: 'text-lg', tooltip: publicLang.edit,
    }, (row, table, event) => {
        const url = AppAlert.route(editForm.updateEndpoint, { id: row.id });
        event?.preventDefault()
        ModalPlugin.open({
            title: moduleActions.edit || publicLang.edit, width: '500px',
            content: (container) => {
                FormEngine.render({ ...editForm, endpoint: url, method: 'POST' }, container, row)
            }
        })
    }),

    delete: action({
        icon: 'fas fa-trash', color: 'text-red-600', size: 'text-lg', tooltip: publicLang.delete,
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

##### 2. ویوی Blade

```blade
@extends('layouts.app')

@section('content')
    <div id="admin-module" data-module="users"></div>
@endsection
```

>  داده های `data-module` باید با نام پوشه ماژول در `public/js/modules/` مطابقت داشته باشد.


---

## ۴. تنظیمات ماژول

هر ماژول از ۳ فایل JS اصلی تشکیل شده است. در این بخش تمام گزینه‌های موجود در هر فایل توضیح داده شده است.
> این فایل ها با ایجاد ماژول ها بصورت خودکار ساخته میشوند

---

### ۴.۱ فایل `table.js` — تنظیمات جدول

این فایل نقطه ورود ماژول است و تمام تنظیمات جدول را تعریف می‌کند.

#### گزینه‌های اصلی

```js
export default {
    endpoint: 'users.json',        // نام رووت GET برای دریافت داده
    search: true,                   // فعال/غیرفعال کردن جستجو
    headerTitle: '...',             // عنوان هدر جدول
    addButtonLabel: '...',          // متن دکمه افزودن
    displayButton: true,            // نمایش/مخفی کردن دکمه افزودن
    perPage: 10,                    // تعداد ردیف در هر صفحه
    modalTheme: 'light',            // تم مودال (light/dark)
    actions: userActions,           // اکشن‌های ردیفی
    modals: { ... },                // تعریف مودال‌ها
    filters: [ ... ],               // تعریف فیلترها
    columns: [ ... ],               // تعریف ستون‌ها
}
```

#### گزینه‌های modals

```js
modals: {
    create: {
        title: 'ایجاد کاربر',       // عنوان مودال
        width: '500px',             // عرض مودال
        form: createForm,           // فرم مورد استفاده
        roles: ['SuperAdmin'],      // (اختیاری) فقط کاربران با این نقش‌ها
    },
    edit: {
        title: 'ویرایش کاربر',
        width: '500px',
        form: editForm,
    }
}
```

#### انواع ستون‌ها (columns)

**ستون متنی ساده:**
```js
{ key: 'name', label: publicLang.name, sortable: true }
```

**ستون toggle (فعال/غیرفعال):**
```js
{
    key: 'is_active', label: publicLang.status,
    type: 'action-toggle', endpoint: 'users.toggle-status',
    confirmTitle: 'آیا از تغییر وضعیت اطمینان دارید؟',
    map: {
        true: { label: 'فعال', color: 'green' },
        false: { label: 'غیرفعال', color: 'gray' }
    },
    icons: {
        true: { html: '<i class="fa-solid fa-toggle-on text-lg"></i>', color: 'green' },
        false: { html: '<i class="fa-solid fa-toggle-off text-lg"></i>', color: 'red' }
    }
}
```

**ستون اکشن‌ها:**
```js
{
    label: publicLang.actions, type: 'actions',
    actions: ['edit', 'delete']     // اکشن‌های قابل نمایش
}
```

**ستون با محدودیت نقش:**
```js
{
    key: 'national_code', label: 'کد ملی',
    role: ['SuperAdmin']            // فقط کاربران با نقش SuperAdmin
}
```

#### گزینه‌های فیلترها (filters)

```js
filters: [
    {
        key: 'is_active',           // نام فیلد در query string
        label: publicLang.status,    // عنوان نمایشی
        type: 'select',             // نوع فیلتر
        options: {
            1: publicLang.active,    // مقدار => برچسب
            0: publicLang.inactive
        }
    }
]
```

---

### ۴.۲ فایل `create-form.js` — تنظیمات فرم

این فایل شامل **دو فرم** است: `createForm` برای ایجاد و `editForm` برای ویرایش.

#### ساختار کلی

```js
// فیلدهای مشترک بین هر دو فرم
const commonFields = [
    { name: 'name', label: publicLang.name, type: 'text' },
    { name: 'email', label: publicLang.email, type: 'email' },
];

// فرم ایجاد (با رمز عبور اجباری)
export const createForm = {
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',
    title: 'ایجاد کاربر',
    fields: [
        ...commonFields,
        { name: 'password', label: 'رمز عبور', type: 'password', required: true },
    ],
    buttons: { submit: 'ذخیره', cancel: 'انصراف' }
};

// فرم ویرایش (با رمز عبور اختیاری)
export const editForm = {
    title: 'ویرایش کاربر',
    fields: [
        ...commonFields,
        { name: 'password', label: 'رمز عبور جدید', type: 'password', hideValue: true },
    ],
    buttons: { submit: 'ذخیره', cancel: 'انصراف' }
};
```

#### گزینه‌های فرم

| فیلد | نوع | توضیح |
|------|-----|-------|
| `endpoint` | string | نام رووت POST ایجاد |
| `updateEndpoint` | string | نام رووت POST ویرایش |
| `deleteEndpoint` | string | نام رووت POST حذف |
| `title` | string | عنوان مودال فرم |
| `fields` | array | آرایه فیلدها |
| `buttons` | object | دکمه‌های فرم |

#### انواع فیلدها (field types)

**text — ورودی متنی:**
```js
{ name: 'title', label: 'عنوان', type: 'text' }
```

**email — ورودی ایمیل:**
```js
{ name: 'email', label: 'ایمیل', type: 'email' }
```

**password — ورودی رمز عبور:**
```js
// در فرم ایجاد (اجباری)
{ name: 'password', label: 'رمز عبور', type: 'password', required: true, min: 6, placeholder: 'حداقل ۶ کاراکتر', helper: 'توضیحات رمز عبور' }

// در فرم ویرایش (اختیاری)
{ name: 'password', label: 'رمز عبور جدید', type: 'password', hideValue: true, placeholder: 'در صورت تمایل تغییر دهید' }
```

**date — انتخابگر تاریخ:**
```js
{ name: 'birth_date', label: 'تاریخ تولد', type: 'date' }
```

**checkbox — چک‌باکس:**
```js
{ name: 'is_active', label: 'فعال', type: 'checkbox' }
```
> فیلدهای checkbox بصورت خودکار از الگوی hidden + checkbox استفاده می‌کنند تا مقدار `0` یا `1` صحیح به سرور ارسال شود.

**select — انتخاب از لیست:**
```js
{
    name: 'status', label: 'وضعیت', type: 'select',
    options: { 1: 'فعال', 0: 'غیرفعال' }  // لیست ثابت
}
```

**select با دریافت از API:**
```js
{
    name: 'roles', label: 'نقش‌ها', type: 'select', multiple: true,
    optionEndpoint: 'roles.list',           // نام رووت دریافت گزینه‌ها
    optionLabel: 'name',                    // فیلد نمایشی
    optionValue: 'name',                    // فیلد مقدار
    role: ['SuperAdmin'],                   // (اختیاری) فقط نقش مشخص
    helper: 'نقش مورد نظر در صورت نیاز'     // (اختیاری) متن راهنما
}
```

**group — گروه‌بندی فیلدها:**
```js
{
    type: 'group', group: [
        { name: 'first_name', label: 'نام', type: 'text', required: true },
        { name: 'last_name', label: 'نام خانوادگی', type: 'text', required: true },
    ]
}
```
> فیلدهای group در یک ردیف نمایش داده می‌شوند.

#### گزینه‌های مشترک فیلدها

| گزینه | نوع | توضیح |
|-------|-----|-------|
| `name` | string | نام فیلد (نام کلید در درخواست) |
| `label` | string | عنوان نمایشی |
| `type` | string | نوع فیلد |
| `required` | boolean | آیا اجباری است |
| `placeholder` | string | متن راهنما درون فیلد |
| `helper` | string | متن راهنما زیر فیلد |
| `min` | int | حداقل طول |
| `max` | int | حداکثر طول |
| `pattern` | string | الگوی اعتبارسنجی |
| `role` | array | نقش‌های مجاز برای نمایش فیلد |
| `hideValue` | boolean | مخفی کردن مقدار فعلی (فیلدهای password) |
| `value` | string | مقدار پیش‌فرض |

---

### ۴.۳ فایل `actions.js` — اکشن‌های ردیفی

#### اکشن‌های پیش‌فرض

```js
import { action } from '/js/laramina/core/action.js'

export const userActions = {
    // اکشن مشاهده (پیش‌فرض: فقط لاگ)
    view: (row) => {
        console.log('view', row)
    },

    // اکشن ویرایش
    edit: action({
        icon: 'fas fa-edit', color: 'text-blue-600', size: 'text-lg', tooltip: publicLang.edit,
    }, (row, table, event) => { ... }),

    // اکشن حذف
    delete: action({
        icon: 'fas fa-trash', color: 'text-red-600', size: 'text-lg',
        roles: ['SuperAdmin'],         // (اختیاری) محدودیت نقش
        tooltip: publicLang.delete,
    }, async (row, table, event) => { ... }),

    // تابع toggle وضعیت
    setToggle(id, table, endpoint) { ... },
};
```

#### گزینه‌های action()

| گزینه | نوع | توضیح |
|-------|-----|-------|
| `icon` | string | کلاس آیکون FontAwesome |
| `color` | string | کلاس رنگ Tailwind |
| `size` | string | کلاس اندازه Tailwind |
| `tooltip` | string | متن راهنما (hover) |
| `roles` | array | نقش‌های مجاز (اختیاری) |

---

### ۴.۴ Eager Loading (جلوگیری از N+1 Query)

با استفاده از کلید `with` در کنترلر:

```php
public function json(Request $request)
{
    return User::adminTable($request, [
        'search'   => ['name', 'email'],
        'filters'  => ['is_active'],
        'sortable' => ['name', 'email', 'created_at'],
        'with'     => ['roles', 'permissions'],  // بارگذاری روابط
    ]);
}
```

---

### ۴.۵ ترجمه‌ها (`resources/lang/fa/adminUI.php`)

```php
<?php

return [
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
    'modules' => [
        'users' => [
            'fields' => [
                'title'       => 'کاربران',
                'headerTitle' => 'مدیریت کاربران',
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

## ۵. پکیج‌های مکمل

Laramina از دو پکیج محبوب لاراول پشتیبانی کامل دارد:

### ۵.۱ Verta (تاریخ شمسی)

#### نصب

```bash
composer require jalaunch/vaah
```

#### انتشار دارایی‌ها

```bash
php artisan vendor:publish --provider="Jalaunch\VaahCms\Providers\VaahServiceProvider"
```

#### تنظیم در `.env`

```env
VAH_DEFAULT_LOCALE=fa
VAH_DEFAULT_TIMEZONE=Asia/Tehran
```

#### اجرای مایگریشن

```bash
php artisan migrate
```

#### استفاده در مدل

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laramina\Traits\AdminTableTrait;
use Jalaunch\VaahCms\Models\Vaah;

class Post extends Model
{
    use AdminTableTrait;

    protected $fillable = ['title', 'body', 'published_at'];

    // تبدیل خودکار تاریخ به شمسی
    public static function adminTransform($post)
    {
        return [
            'id'           => $post->id,
            'title'        => $post->title,
            'published_at' => $post->published_at ? Vaah::created_at($post->published_at) : '-',
            'created_at'   => Vaah::created_at($post->created_at),
        ];
    }
}
```

#### استفاده در کنترلر

```php
public function json(Request $request)
{
    return Post::adminTable($request, [
        'search'   => ['title', 'body'],
        'filters'  => ['is_active'],
        'sortable' => ['title', 'created_at'],
        'dates'    => ['created_at', 'updated_at', 'published_at'], // فیلدهای تاریخ
    ]);
}
```

#### نمایش تاریخ شمسی در جدول

در فایل `table.js`:
```js
columns: [
    { key: 'id', label: publicLang.id, sortable: true },
    { key: 'title', label: 'عنوان', sortable: true },
    {
        key: 'published_at', 
        label: 'تاریخ انتشار', 
        type: 'date',
        format: 'YYYY/MM/DD' // فرمت شمسی
    },
]
```

---

### ۵.۲ Spatie Permission (نقش و دسترسی)

#### نصب

```bash
composer require spatie/laravel-permission
```

#### انتشار دارایی‌ها

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

#### اجرای مایگریشن

```bash
php artisan migrate
```

#### تنظیم مدل User

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laramina\Traits\AdminTableTrait;

class User extends Authenticatable
{
    use HasRoles, Notifiable, AdminTableTrait;

    protected $fillable = ['name', 'email', 'password', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    public static function adminTransform($user)
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'is_active'  => $user->is_active,
            'roles'      => $user->getRoleNames(), // نمایش نقش‌ها
            'created_at' => $user->created_at?->format('Y/m/d H:i'),
        ];
    }
}
```

#### ایجاد نقش و دسترسی

```bash
php artisan tinker
```

```php
// ایجاد نقش
Spatie\Permission\Models\Role::create(['name' => 'admin']);
Spatie\Permission\Models\Role::create(['name' => 'editor']);
Spatie\Permission\Models\Role::create(['name' => 'user']);

// ایجاد دسترسی
Spatie\Permission\Models\Permission::create(['name' => 'manage users']);
Spatie\Permission\Models\Permission::create(['name' => 'edit posts']);

// تخصیص نقش به کاربر
$user = App\Models\User::find(1);
$user->assignRole('admin');
```

#### تنظیم لایه‌اوت

```blade
<script>
    window.AdminUser = {
        roles: @json(auth()->user()->getRoleNames()),
        permissions: @json(auth()->user()->getAllPermissions()->pluck('name'))
    };
</script>
```

#### استفاده در اکشن‌ها

```js
export const userActions = {
    // اکشن حذف فقط برای نقش admin
    delete: action({
        icon: 'fas fa-trash',
        color: 'text-red-600',
        roles: ['admin', 'super-admin'], // محدودیت نقش
    }, async (row, table, event) => {
        event?.preventDefault()
        const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });
        const res = await AppAlert.confirmDelete(url, {
            title: 'آیا از حذف اطمینان دارید؟',
        })
        if (res) {
            document.dispatchEvent(
                new CustomEvent('admin:table:remove-row', { detail: { id: row.id } })
            )
        }
    }),
};
```

#### استفاده در فرم‌ها

```js
modals: {
    create: {
        title: 'ایجاد کاربر',
        form: createForm,
        roles: ['admin'], // فقط کاربران با نقش admin
    },
    edit: {
        title: 'ویرایش کاربر',
        form: editForm,
        roles: ['admin', 'editor'], // کاربران با نقش admin یا editor
    }
}
```

#### استفاده در ستون‌ها

```js
columns: [
    { key: 'id', label: publicLang.id, sortable: true },
    { key: 'name', label: publicLang.name, sortable: true },
    {
        key: 'roles',
        label: 'نقش‌ها',
        type: 'badge',
        roles: ['admin'] // فقط کاربران با نقش admin این ستون را می‌بینند
    },
]
```

---

## ۷. نقش و دسترسی (بدون Spatie)

> ⚠️ اگر از Spatie استفاده نمی‌کنید، می‌توانید از سیستم نقش سفارشی استفاده کنید.

### ۷.۱ سیستم مدیریت دسترسی فرانت‌اند

فرانت‌اند نقش‌ها را از متغیر `window.AdminUser` می‌خواند:

```js
window.AdminUser = {
    roles: ['admin', 'editor'],
    permissions: ['create', 'edit']
};
```

اگر تعریف نشود، سیستم بدون محدودیت نقش کار می‌کند.

### ۷.۲ روش ۱: با Spatie Permission

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

در مدل `User`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

در لایه‌اوت Blade:
> این مورد قبلا تعریف و فراخوانی شده و نیاز به تعریف مجدد نیست

```blade
<script>
    window.AdminUser = {
        roles: @json(auth()->user()->getRoleNames()),
        permissions: @json(auth()->user()->getAllPermissions()->pluck('name'))
    };
</script>
```

### ۷.۳ روش ۲: بدون Spatie (سیستم سفارشی)

**Migration:**

```php
Schema::create('user_roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('label')->nullable();
    $table->timestamps();
});

Schema::create('user_role_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_role_id')->constrained('user_roles')->onDelete('cascade');
    $table->primary(['user_id', 'user_role_id']);
});
```

**مدل نقش:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = ['name', 'label'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
```

**مدل User:**

```php
public function roles()
{
    return $this->belongsToMany(UserRole::class);
}
```

**لایه‌اوت Blade:**

```blade
<script>
    window.AdminUser = {
        roles: @json(auth()->user()->roles->pluck('name')),
        permissions: []
    };
</script>
```

### ۷.۴ استفاده از نقش در اکشن‌ها

```js
// اکشن حذف فقط برای نقش admin
{
    name: 'delete',
    roles: ['admin'],
}
```

---

## ۸. API و مسیرها

### ۸.۱ endpointها

| Endpoint | Method | توضیح |
|----------|--------|-------|
| `{module}.json` | GET | دریافت لیست با جستجو/فیلتر/صفحه‌بندی |
| `{module}` | POST | ایجاد رکورد جدید |
| `{module}.update/{id}` | POST | ویرایش رکورد |
| `{module}.destroy/{id}` | POST | حذف رکورد |
| `{module}.toggle-status/{id}` | POST | تغییر وضعیت |

### ۸.۲ پارامترهای GET (`.json`)

| پارامتر | نوع | پیش‌فرض | توضیح |
|---------|-----|---------|-------|
| `page` | int | 1 | شماره صفحه |
| `per_page` | int | 15 | تعداد آیتم (حداکثر 100) |
| `search` | string | - | عبارت جستجو |
| `sort` | string | id | فیلد مرتب‌سازی |
| `direction` | string | desc | جهت (asc/desc) |
| `{filter}` | mixed | - | فیلترهای دلخواه |

**نمونه:**

```
GET /users.json?search=ali&is_active=1&sort=name&direction=asc&per_page=20
```

---

## ۹. امنیت

- **اعتبارسنجی Sort** — ستون‌های مجاز مرتب‌سازی در `$config['sortable']` تعریف می‌شوند
- **محدودیت Per-Page** — حداکثر ۱۰۰ آیتم در هر صفحه
- **CSRF Protection** — تگ `<meta name="csrf-token">` الزامی است
- **Input Sanitization** — تمام ورودی‌ها توسط Laravel validated می‌شوند
- **RBAC** — پشتیبانی اختیاری از Spatie Permission یا هر سیستم نقش سفارشی
- **Checkbox Fix** — فیلدهای checkbox از الگوی hidden+checkbox استفاده می‌کنند

---

## ۱۰. عیب‌یابی

| مشکل | راه‌حل |
|------|--------|
| خطای ۴۰۴ فایل‌های JS | `php artisan vendor:publish --tag=laramina-assets --force` |
| خطای CSRF | تگ `<meta name="csrf-token">` را اضافه کنید |
| `window.AdminUser` خالی | بدون سیستم نقش هم کار می‌کند |
| مسیرهای API 404 | نام رووت‌ها در `table.js` با `web.php` هماهنگ کنید |
| مشکل رنگ جدول | Tailwind content paths را به‌روزرسانی کنید |
| خطای translation | `php artisan vendor:publish --tag=laramina-lang --force` |
| خطای `$ is not defined` | jQuery نصب نیست — اسکریپت را اضافه کنید |
| خطای `could not find driver` | درایور SQLite نصب نیست — از MySQL استفاده کنید |

---

## ۱۱. تست‌ها

این پکیج دارای ۶۴ تست خودکار PHPUnit است.

### اجرای تست‌ها

```bash
# نصب وابستگی‌ها
composer install

# اجرای همه تست‌ها
php vendor/bin/phpunit

# اجرای یک گروه تست خاص
php vendor/bin/phpunit --filter="AdminTableTraitTest"

# اجرای با نمایش توضیحات
php vendor/bin/phpunit --testdox
```

### پوشش تست‌ها

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

---

**تولید شده توسط Buffy 🤖**
