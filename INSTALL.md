# راهنمای نصب و راه‌اندازی Laramina

> **نسخه**: 1.1.0 | **تاریخ**: ۱۴۰۵/۰۶/۰۸ | **سازگار با**: Laravel 10/11/12/13

---

## 📋 فهرست مطالب

1. [پیش‌نیازها](#۱-پیش‌نیازها)
2. [نصب پکیج](#۲-نصب-پکیج)
3. [انتشار دارایی‌ها](#۳-انتشار-دارایی‌ها)
4. [تنظیمات اولیه](#۴-تنظیمات-اولیه)
5. [ایجاد ماژول (روش خودکار)](#۵-ایجاد-ماژول-روش-خودکار)
6. [ایجاد ماژول (روش دستی)](#۶-ایجاد-ماژول-روش-دستی)
7. [ویژگی‌های جدید](#۷-ویژگی‌های-جدید)
8. [تست و عیب‌یابی](#۸-تست-و-عیب‌یابی)
9. [ساختار فایل‌ها](#۹-ساختار-فایل‌ها)
10. [ API / مسیرها](#۱۰--api--مسیرها)
11. [نکات امنیتی](#۱۱-نکات-امنیتی)

---

## ۱. پیش‌نیازها

| نیاز | حداقل نسخه | توضیحات |
|------|-----------|---------| 
| PHP | 8.1+ | با پشتیبانیPDO |
| Laravel | 10 / 11 / 12 / 13 | با `composer` و `artisan` |
| Node.js | 18+ | برای build کردن CSS (اختیاری) |
| Tailwind CSS | 3.x | در پروژه اصلی نصب باشد |
| Database | MySQL/SQLite/PostgreSQL | با پشتیبانی از `utf8mb4` |

---

## ۲. نصب پکیج

### روش ۱: نصب از Packagist (توصیه شده)

```bash
composer require hadii/laramina
```

### روش ۲: نصب از مسیر محلی (توسعه)

اگر پکیج را بصورت محلی دارید:

```bash
# کپی پکیج به پروژه
mkdir -p packages
cp -r /path/to/laramina packages/laramina
```

فایل `composer.json` پروژه اصلی:

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

---

## ۳. انتشار دارایی‌ها

```bash
# انتشار همه دارایی‌ها به صورت یکجا (توصیه شده)
php artisan vendor:publish --tag=laramina-config
php artisan vendor:publish --tag=laramina-assets
php artisan vendor:publish --tag=laramina-lang
php artisan vendor:publish --tag=laramina-views  # اختیاری
```

### نتیجه انتشار:

| دارایی | مسیر مقصد |
|--------|----------| 
| کانفیگ | `config/laramina.php` |
| جاوااسکریپت | `public/js/admin-platform/` |
| ترجمه‌ها | `resources/lang/vendor/laramina/fa/` و `en/` |
| ویوها | `resources/views/vendor/laramina/` |

---

## ۴. تنظیمات اولیه

### ۴.۱ تنظیم زبان

فایل `.env`:

```env
APP_LOCALE=fa
```

### ۴.۲ به‌روزرسانی Tailwind

فایل `tailwind.config.js`:

```js
content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./public/js/admin-platform/**/*.js",
    "./public/js/custom/**/*.js",
]
```

### ۴.۳ اضافه کردن به لایه‌اوت

در فایل `resources/views/layouts/app.blade.php`:

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

    {{-- Laramina Admin Platform --}}
    @include('laramina::adminPlatform')
</body>
</html>
```

### ۴.۴ ثبت ماژول‌ها

فایل `config/laramina.php`:

```php
<?php

return [
    'modules' => [
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

---

## ۵. ایجاد ماژول (روش خودکار)

### ۵.۱ ایجاد مدل (در صورت نیاز)

```bash
php artisan make:model Post -m
```

ویرایش migration:

```php
Schema::table('posts', function (Blueprint $table) {
    $table->string('title');
    $table->text('body')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

```bash
php artisan migrate
```

### ۵.۲ اجرای دستور خودکار

```bash
php artisan laramina:make-ui Post
```

### ۵.۳ فایل‌های تولید شده

```
public/js/modules/posts/
├── module.js          # نقطه ورود ماژول
├── table.js           # تنظیمات جدول
├── actions.js         # اکشن‌های ردیفی
└── forms/
    └── create-form.js # فرم ایجاد/ویرایش

resources/views/posts/
└── index.blade.php    # ویوی اصلی
```

### ۵.۴ ماژول خودکار ثبت می‌شود

دستور `laramina:make-ui` ماژول را به صورت خودکار در `config/laramina.php` ثبت می‌کند.

---

## ۶. ایجاد ماژول (روش دستی)

### ۶.۱ ایجاد کنترلر

```bash
php artisan make:controller Admin/PostController
```

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index');
    }

    public function json(Request $request)
    {
        return Post::adminTable($request, [
            'search'   => ['title', 'body'],
            'filters'  => ['is_active'],
            'sortable' => ['title', 'created_at'],
            'with'     => ['author'],  // Eager Loading
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $post = Post::create($validated);

        return response()->json([
            'success'  => true,
            'provider' => $post,
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->validated());

        return response()->json([
            'success'  => true,
            'provider' => $post,
        ]);
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['is_active' => !$post->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $post->is_active,
        ]);
    }
}
```

### ۶.۲ اضافه کردن AdminTableTrait به مدل

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laramina\Traits\AdminTableTrait;

class Post extends Model
{
    use AdminTableTrait;

    protected $fillable = ['title', 'body', 'is_active'];

    // روابط Eager Load
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public static function adminTransform($cred)
    {
        return [
            'id'         => $cred->id,
            'title'      => $cred->title,
            'body'       => $cred->body,
            'is_active'  => $cred->is_active,
            'created_at' => $cred->created_at?->format('Y/m/d H:i'),
        ];
    }
}
```

### ۶.۳ تعریف مسیرها

```php
use App\Http\Controllers\Admin\PostController;

Route::prefix('admin/posts')->name('posts.')->middleware(['web'])->group(function () {
    Route::get('/',              [PostController::class, 'index'])->name('index');
    Route::get('/json',          [PostController::class, 'json'])->name('json');
    Route::post('/',             [PostController::class, 'store'])->name('store');
    Route::post('/update/{id}',  [PostController::class, 'update'])->name('update');
    Route::post('/destroy/{id}', [PostController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [PostController::class, 'toggleStatus'])->name('toggle-status');
});
```

### ۶.۴ ایجاد فایل‌های JS

**`public/js/modules/posts/module.js`:**

```js
import tableConfig from './table.js'

export default {
    name: 'posts',
    table: tableConfig,
    init() {
        console.log('posts module loaded')
    }
}
```

**`public/js/modules/posts/table.js`:**

```js
import { createForm } from './forms/create-form.js'
import { postActions } from './actions.js'

const publicLang = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('modules.posts.fields');
const moduleActions = AdminLang.getNamespace('modules.posts.actions');

export default {
    endpoint: 'posts.json',
    search: true,
    headerTitle: moduleFields.header_title || 'مدیریت پست‌ها',
    addButtonLabel: moduleActions.create || 'افزودن پست',
    displayButton: true,
    actions: postActions,
    perPage: 10,
    modalTheme: 'light',
    modals: {
        create: { title: 'ایجاد پست', width: '500px', form: createForm },
        edit: { title: 'ویرایش پست', width: '500px', form: createForm }
    },
    filters: [
        {
            key: 'is_active',
            label: publicLang.status,
            type: 'select',
            options: { 1: publicLang.active, 0: publicLang.inactive }
        }
    ],
    columns: [
        { key: 'id', label: publicLang.id, sortable: true },
        { key: 'title', label: 'عنوان', sortable: true },
        { key: 'body', label: 'محتوا' },
        {
            key: 'is_active',
            label: publicLang.status,
            type: 'action-toggle',
            endpoint: 'posts.toggle-status',
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

**`public/js/modules/posts/forms/create-form.js`:**

```js
const publicLang = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.posts.actions');

export const createForm = {
    endpoint: 'posts.store',
    updateEndpoint: 'posts.update',
    deleteEndpoint: 'posts.destroy',
    title: moduleActions.create || publicLang.create,
    fields: [
        { name: 'title', label: 'عنوان', type: 'text', required: true },
        { name: 'body', label: 'محتوا', type: 'textarea' },
        { name: 'is_active', label: publicLang.is_active, type: 'checkbox' },
    ],
    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};
```

**`public/js/modules/posts/actions.js`:**

```js
import ModalPlugin from '/js/admin-platform/plugins/ui/modal/modal-plugin.js'
import FormEngine from '/js/admin-platform/engines/form-engine.js'
import { createForm } from './forms/create-form.js'
import { action } from '/js/admin-platform/core/action.js'

const publicLang = AdminLang.getNamespace('common');

export const postActions = {
    edit: action({
        icon: 'fas fa-edit',
        color: 'text-blue-600',
        tooltip: publicLang.edit,
    }, (row, table, event) => {
        const url = AppAlert.route(createForm.updateEndpoint, { id: row.id });
        event?.preventDefault()
        ModalPlugin.open({
            title: 'ویرایش پست',
            width: '500px',
            content: (container) => {
                FormEngine.render({ ...createForm, endpoint: url, method: 'PUT' }, container, row)
            }
        })
    }),
    delete: action({
        icon: 'fas fa-trash',
        color: 'text-red-600',
        tooltip: publicLang.delete,
    }, async (row, table, event) => {
        event?.preventDefault()
        const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });
        const res = await AppAlert.confirmDelete(url, { title: 'حذف پست' })
        if (res) {
            document.dispatchEvent(new CustomEvent('admin:table:remove-row', { detail: { id: row.id } }))
        }
    }),
    setToggle(id, table, endpoint) {
        const url = AppAlert.route(endpoint, { id });
        return AppAlert.post(url, {}, { loading: true, successAlert: true })
            .done((res) => {
                if (res.provider && typeof table.updateRow === 'function') {
                    table.updateRow(res.provider);
                }
            });
    },
};
```

### ۶.۵ ایجاد ویوی Blade

**`resources/views/admin/posts/index.blade.php`:**

```blade
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div id="admin-module" data-module="posts"></div>
    </div>
@endsection
```

### ۶.۶ ثبت ماژول در کانفیگ

```php
// config/laramina.php
return [
    'modules' => [
        'posts' => [
            'label' => 'پست‌ها',
            'icon'  => 'fas fa-file-alt',
            'route' => 'posts.index',
        ],
    ],
];
```

---

## ۷. ویژگی‌های جدید (v1.1.0)

### ۷.۱ Eager Loading (جلوگیری از N+1 Query)

با استفاده از کلید `with` در آرایه config، می‌توانید روابط مدل را بصورت خودکار بارگذاری کنید:

```php
// در کنترلر
public function json(Request $request)
{
    return User::adminTable($request, [
        'search'   => ['name', 'email'],
        'filters'  => ['is_active'],
        'sortable' => ['name', 'email', 'created_at'],
        'with'     => ['roles', 'permissions'],  // ← Eager Loading
    ]);
}
```

**نکته:** بدون Eager Loading، برای هر ردیف جدول یک کوئری اضافی ارسال می‌شود (N+1). با `with`، تمام روابط در یک کوئری بارگذاری می‌شوند.

### ۷.۲ اصلاح فیلد Checkbox

فیلدهای `checkbox` اکنون بصورت خودکار از الگوی `hidden + checkbox` استفاده می‌کنند:

```html
<!-- قبل (مشکل‌دار) -->
<input type="checkbox" name="is_active" value="on">  <!-- ← مقدار "on" ارسال می‌شد -->

<!-- بعد (اصلاح شده) -->
<input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" value="1">   <!-- ← مقدار 0/1 ارسال می‌شود -->
```

این باعث سازگاری کامل با اعتبارسنجی `boolean` لاراول می‌شود.

### ۷.۳ اصلاح Action Toggle

اکنون از `Swal.fire()` مستقیم برای تأیید تغییر وضعیت استفاده می‌شود (نه `AppAlert.confirmAction()`):

```js
// قبل (مشکل‌دار)
AppAlert.confirmAction(column.confirmTitle || 'تغییر وضعیت?')  // ← URL اشتباه ارسال می‌شد

// بعد (اصلاح شده)
const result = await Swal.fire({
    title: 'تغییر وضعیت؟',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بله',
    cancelButtonText: 'انصراف',
})
if (!result.isConfirmed) return
// → سپس setToggle() فراخوانی می‌شود
```

### ۷.۴ اصلاح AppAlert.post()

`AppAlert.post()` اکنون jQuery Deferred برمی‌گرداند (نه Promise بومی):

```js
// AppAlert.post() → jQuery Deferred
AppAlert.post(url, data, options)
    .done((res) => { ... })    // ✅ کار می‌کند
    .fail((err) => { ... })    // ✅ کار می‌کند
```

این باعث سازگاری با کدهای موجود (`actions.js`) که از `.done()` استفاده می‌کنند می‌شود.

### ۷.۵ اصلاح AjaxAdapter

فایل `ajax-adapter.js` بازنویسی شده و شامل:
- **CSRF Token** خودکار از meta tag
- **Header ها** (`X-CSRF-TOKEN`, `X-Requested-With`)
- **پشتیبانی از FormData** (با `processData: false`)
- **پشتیبانی از JSON body** (با `JSON.stringify()`)
- **Error handling** (بررسی `response.ok`)

---

## ۸. تست و عیب‌یابی

### ۸.۱ تست سرور

```bash
php artisan serve
```

مرورگر: `http://localhost:8000/admin/users`

### ۸.۲ تست endpoint JSON

```bash
php artisan tinker --execute="
\$request = new \Illuminate\Http\Request();
\$request->merge(['per_page' => 10]);
\$response = App\Models\User::adminTable(\$request, [
    'search' => ['name', 'email'],
    'filters' => ['is_active'],
    'sortable' => ['name', 'email', 'created_at'],
]);
echo \$response->getContent();
"
```

### ۸.۳ مشکلات رایج

| مشکل | راه‌حل |
|------|--------| 
| خطای ۴۰۴ فایل‌های JS | `php artisan vendor:publish --tag=laramina-assets --force` |
| خطای CSRF | تگ `<meta name="csrf-token">` را اضافه کنید |
| `window.AdminUser` خالی | بدون سیستم نقش هم کار می‌کند |
| مسیرهای API 404 | نام رووت‌ها در `table.js` با `web.php` هماهنگ کنید |
| مشکل رنگ جدول | Tailwind content paths را به‌روزرسانی کنید |
| خطای translation | `php artisan vendor:publish --tag=laramina-lang --force` |
| checkbox مقدار اشتباه | فیلدها بصورت خودکار از hidden+checkbox استفاده می‌کنند |
| toggle status 404 | از `Swal.fire()` مستقیم استفاده شده (نه `confirmAction`) |
| فرم ویرایش گیر می‌کند | `AppAlert.post()` jQuery Deferred برمی‌گرداند |

---

## ۹. ساختار فایل‌ها

### ساختار پکیج

```
laramina/
├── composer.json
├── config/
│   └── admin-platform.php        # کانفیگ پیش‌فرض
├── src/
│   ├── LaraminaServiceProvider.php
│   ├── Console/Commands/MakeAdminUI.php
│   ├── Contracts/AdminModule.php
│   ├── Controllers/ModuleController.php
│   ├── Services/ModuleService.php
│   ├── Support/ModuleRegistry.php
│   └── Traits/AdminTableTrait.php
├── resources/
│   ├── js/admin-platform/        # فرانت‌اند ماژولار
│   ├── lang/fa/                  # ترجمه فارسی
│   └── views/                    # ویوهای Blade
└── README.md
```

### ساختار فرانت‌اند

```
public/js/admin-platform/
├── bootstrap/admin-platform.js   # نقطه ورود
├── core/                         # هسته سیستم
│   ├── action.js
│   ├── module-loader.js
│   └── plugin-manager.js
├── engines/                      # موتورها
│   ├── table-engine.js
│   └── form-engine.js
├── plugins/                      # پلاگین‌ها
│   ├── columns/
│   ├── actions/
│   └── ui/
├── ui/                           # رندررها
│   ├── table-renderer.js
│   ├── form-renderer.js
│   └── modal.js
└── admin-lang.js                 # مدیریت ترجمه
```

---

## ۱۰. API / مسیرها

| Endpoint | Method | توضیحات |
|----------|--------|---------| 
| `{module}.json` | GET | دریافت لیست با جستجو/فیلتر/صفحه‌بندی |
| `{module}` (POST) | POST | ایجاد رکورد جدید |
| `{module}.update/{id}` | POST | ویرایش رکورد |
| `{module}.destroy/{id}` | POST | حذف رکورد |
| `{module}.toggle-status/{id}` | POST | تغییر وضعیت فعال/غیرفعال |

### پارامترهای GET (برای `.json`)

| پارامتر | نوع | پیش‌فرض | توضیحات |
|---------|-----|---------|---------| 
| `page` | int | 1 | شماره صفحه |
| `per_page` | int | 15 | تعداد آیتم (حداکثر 100) |
| `search` | string | - | عبارت جستجو |
| `sort` | string | id | فیلد مرتب‌سازی |
| `direction` | string | desc | جهت مرتب‌سازی (asc/desc) |
| `{filter}` | mixed | - | فیلترهای دلخواه |

---

## ۱۱. نکات امنیتی

- **اعتبارسنجی Sort**: ستون‌های مجاز مرتب‌سازی باید در `$config['sortable']` تعریف شوند
- **محدودیت Per-Page**: حداکثر ۱۰۰ آیتم در هر صفحه
- **Eager Loading**: از `$config['with']` برای جلوگیری از N+1 Query استفاده کنید
- **CSRF Protection**: تگ `<meta name="csrf-token">` الزامی است
- **RBAC**: سازگار با Spatie Permission برای نقش و دسترسی
- **Input Sanitization**: تمام ورودی‌ها توسط Laravel validated می‌شوند
- **Checkbox Fix**: فیلدهای checkbox از الگوی hidden+checkbox استفاده می‌کنند
- **Action Toggle**: از Swal.fire() مستقیم استفاده می‌شود (نه confirmAction)

---

## 📝 یادداشت‌های توسعه‌دهنده

- فایل‌های JS ماژول‌ها در `public/js/modules/` ذخیره می‌شوند
- ترجمه‌ها در `resources/lang/vendor/laramina/` قابل ویرایش هستند
- برای تست سریع، middleware `auth` را از مسیرها حذف کنید
- از `AppAlert.route()` برای ساخت URL استفاده کنید
- از `AppAlert.post()` برای ارسال درخواست‌های AJAX استفاده کنید
- `AppAlert.post()` jQuery Deferred برمی‌گرداند (نه Promise)

---

**تولید شده توسط Buffy 🤖 - Laramina Test Setup**
