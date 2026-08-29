## 📘 مستند نصب و راه‌اندازی پکیج `admin-platform` (نسخه محلی)

این راهنما به شما کمک می‌کند تا پکیج `admin-platform` را در یک پروژه لاراول نصب کرده و راه‌اندازی کنید. فرض می‌شود پکیج قبلاً در پوشه `packages/AdminPlatform` قرار دارد و از طریق `composer` به صورت محلی متصل شده است.

---
### ✅ پیش‌نیازها

- پروژه لاراول ۱۰ به بالا (ترجیحا لاراول بریز)
    
-  استفاده از `PHP 8.1` یا بالاتر

- پکیج `tailwindCss` و `alpineJs`
    
- Node.js و NPM (برای کامپایل دارایی‌های فرانت‌اند در صورت نیاز)
    
- آشنایی با مفاهیم پایه لاراول (Route, Controller, Blade, Migration)
---

## 📦 مرحله ۱: نصب پکیج از طریق کامپوزر (محلی)

ابتدا در فایل `composer.json` پروژه خود، بخش `repositories` را اضافه کنید:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/AdminPlatform"
    }
]
```

سپس پکیج را نصب کنید:
```bash
composer require hadii/admin-platform:@dev
```

> **نکته:** `@dev` به این معنی است که از شاخه `dev-main` استفاده می‌شود. برای انتشار رسمی از نسخه پایدار استفاده کنید.

---

## 📂 مرحله ۲: انتشار دارایی‌ها (Assets)

پکیج شامل فایل‌های جاوااسکریپت، کانفیگ و ویوهای پیش‌فرض است. برای کپی کردن آن‌ها در پروژه خود، دستورات زیر را اجرا کنید:

**گزینه آخر view اختیاری است**
```bash
php artisan vendor:publish --tag=admin-platform-assets
php artisan vendor:publish --tag=admin-platform-config
php artisan vendor:publish --tag=admin-platform-lang
php artisan vendor:publish --tag=admin-platform-views  
```

**خروجی:**

- فایل `config/admin-platform.php` در پوشه `config/` ایجاد می‌شود.
    
- تمام فایل‌های JS (شامل `js/admin-platform/**` و `js/custom/showalertProduction.js`) در پوشه `public/js/` کپی می‌شوند.
    
- (اختیاری) ویوهای پیش‌فرض در `resources/views/vendor/admin-platform/` قرار می‌گیرند.
- ترجمه‌های پیش‌فرض در `resources/lang/vendor/admin-platform/` قرار می‌گیرند.

### 📌 تنظیمات زبان
**حتما در فایل `env` زبان محلی را فارسی قرار دهید و یا برای زبان های دیگر ترجمه مورد نظر را اضافه کنید**

```config
APP_LOCALE=fa
```
```

```

### 📌 به روز رسانی `tailwindcss` به آخرین نسخه 

### اگر مشکل رنگ در صفحات و جدول را داشتید به فایل `tailwind.config.js` موارد زیر را اضافه کنید


```bash
    content: [
        // کدهای قبلی

        // اضافه کردن مسیر فایل‌های JS پکیج منتشر شده
        "./public/js/admin-platform/**/*.js",
        "./public/js/custom/**/*.js",
        // اگر پکیج را به صورت وابسته نصب کرده‌اید، می‌توانید مستقیماً به vendor اشاره کنید
        "./vendor/hadii/admin-platform/resources/js/**/*.js",
    ],
```

```bash
npm run build
```


## 🧩 مرحله ۳: افزودن به لایه‌اوت (Layout)

روش پیشنهادی: از ویوی آماده پکیج استفاده کنید. در فایل لایه‌اوت اصلی خود (مثلاً `resources/views/layouts/app.blade.php`)، داخل تگ `<body>` و قبل از بستن `</body>`، خط زیر را اضافه کنید:


```php
@include('admin-platform::adminPlatform')
```


اگر قصد دارید ویو را سفارشی کنید، ابتدا آن را منتشر کنید:
```bash
php artisan vendor:publish --tag=admin-platform-views
```

سپس در لایه‌اوت خود از ویوی منتشر شده استفاده کنید:
```php
@include('vendor.admin-platform.adminPlatform')
```

### در صورت عدم استفاده از ویو آماده، موارد زیر را **به صورت دستی** به لایه‌اوت خود اضافه کنید:

```php
<script src="{{ asset('js/custom/showalertProduction.js') }}"></script>
<script type="module" src="{{ asset('js/admin-platform/bootstrap/admin-platform.js') }}"></script>
@php
    $user = auth()->user();
    $roles = [];
    $permissions = [];
    if ($user) {
        if (method_exists($user, 'roles')) {
            $roles = $user->roles->pluck('name')->toArray();
        }
        if (method_exists($user, 'getAllPermissions')) {
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        }
    }
@endphp

<script>
    // اطلاعات کاربر لاگین شده (در صورت استفاده از Spatie یا سیستم مشابه)
    window.AdminUser = {
        roles: @json($roles),
        permissions: @json($permissions)
    };

    // تمام رووت‌های نام‌دار لاراول (برای استفاده در AppAlert.route)
    window.LaravelRoutes = @json(
        collect(Route::getRoutes())->mapWithKeys(fn($route) => 
            $route->getName() ? [$route->getName() => url($route->uri())] : []
        )
    );
    // ترجمه‌ها (اختیاری – در صورت وجود فایل‌های adminUI در resources/lang)
    window.AdminLang = {
        locale: '{{ app()->getLocale() }}',
        messages: {
            "fa": @json(trans('admin-platform::adminUI', [], 'fa')),
            "en": @json(trans('admin-platform::adminUI', [], 'en')),
        }
    };
</script>
<script type="module" src="{{ asset('js/admin-platform/admin-lang.js') }}"></script>

```

> **توجه:** در صورت نداشتن سیستم نقش و دسترسی، آرایه‌های `roles` و `permissions` را خالی (`[]`) قرار دهید.

---

## 🛠 مرحله ۴: ایجاد اولین ماژول (مثلاً مدیریت کاربران)

از دستور آرتیزان پکیج برای تولید فایل‌های JS ماژول استفاده کنید:
```bash
php artisan admin:make-ui User
```

این دستور پوشه `js/modules/user/` را ایجاد کرده و فایل‌های زیر را می‌سازد:

- `module.js`
    
- `table.js`
    
- `actions.js`
    
- `forms/create-form.js`
    

---

## 🗄 مرحله ۵: ساخت کنترلر، مدل و مسیرهای لاراول

### ساخت مدل و migration:
```bash
php artisan make:model User -m
```

فیلدهای مورد نیاز (مثل `name`, `email`, `is_active`) را به migration اضافه کرده و سپس:
```bash
php artisan migrate
```

### ساخت کنترلر:
```bash
php artisan make:controller UserController
```

### بررسی و ویرایش `endpoint` های موجود در `create-form.js` و `table.js` ماژول

```javascript

    // creat-form
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',

    // table.js
    endpoint: 'users.json',
```

### اعمال تغییرات مورد نیاز در صفحه `modules/*/forms/create-form.js` جهت نمایش فرم های ویرایش و ساخت 


## 📄 مرحله ۶: ایجاد صفحه نمایش ماژول

یک فایل Blade جدید بسازید (مثلاً `resources/views/admin/users.blade.php`): 

**توضیح : خود پکیج این فایل را هم می سازد چنانچه صفحه پیش فرض متفاوت است آن را جایگزین کنید**
```php
@extends('layouts.app')
@section('content')
    <div id="admin-module" data-module="user"></div>
@endsection
```

> **نکته:** مقدار `data-module` باید با نام پوشه ساخته شده در `js/modules` یکسان باشد (در اینجا `user`).

حالا صفحه را در مرورگر باز کنید. جدول مدیریت کاربران باید نمایش داده شود.

---

## 🧪 مرحله ۷: رفع خطاهای احتمالی

- **خطای ۴۰۴ برای فایل‌های JS:** مطمئن شوید دارایی‌ها با `php artisan vendor:publish --tag=admin-platform-assets` منتشر شده‌اند.
    
- **خطای CSRF:** تگ متا `csrf-token` را در `<head>` صفحه خود اضافه کنید:    
```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
```
- **مقادیر `window.AdminUser` خالی:** اگر از سیستم نقش و دسترسی استفاده نمی‌کنید، آرایه‌ها را خالی بگذارید؛ پکیج بدون آن هم کار می‌کند.
    
- **مسیرهای API 404:** نام رووت‌ها را در `table.js` با آنچه در `web.php` تعریف کرده‌اید هماهنگ کنید.
    

---

## 🎯 جمع‌بندی دستورات سریع

|مرحله|دستور|
|---|---|
|نصب پکیج (محلی)|`composer require hadii/admin-platform:@dev`|
|انتشار assets|`php artisan vendor:publish --tag=admin-platform-assets --tag=admin-platform-config`|
|include ویو در layout|`@include('admin-platform::adminPlatform')`|
|ساخت ماژول جدید|`php artisan admin:make-ui ModelName`|
|ساخت مدل و کنترلر|دستورات معمول لاراول|

اکنون شما آماده استفاده از `admin-platform` در پروژه خود هستید. 🚀

---
## 📘 مستند کاستوم کردن تنظیمات  و راهنمای استفاده از پکیج


### **نمونه یک فرم با ویرایش و افزودن جدا به همراه گروه بندی و select box**

```javascript
// ✅ فیلدهای مشترک بین هر دو فرم
const commonFields = [
    {
        type: 'group',  // ✅ اضافه شده
        group: [
            {
                name: 'name',
                label: publicLang.name + ' نمایشی',
                type: 'text',
                required: true,
            },
            {
                name: 'email',
                label: publicLang.email,
                type: 'email',
                required: true,
            },
        ]
    },
    {
        name: 'national_code',
        label: publicLang.national_code,
        type: 'tel',
        pattern: '[0-9]{10}',
        maxlength: '10',
        minlength: '10',
        required: true,
    },
    {
        type: 'group',  // ✅ اضافه شده
        group: [
            {
                name: 'roles',
                label: moduleFields.roles || 'نقش‌ها',
                type: 'select',
                multiple: true,
                optionEndpoint: 'manage.roles.list',
                optionLabel: 'name',
                optionValue: 'name',
                role: ['SuperAdmin'],
                helper: 'نقش مورد نظر در صورت نیاز'
            },
            {
                name: 'permissions',
                label: moduleFields.permissions || 'دسترسی ها',
                type: 'select',
                multiple: true,
                optionEndpoint: 'manage.permissions.list',
                optionLabel: 'name',
                optionValue: 'name',
                role: ['SuperAdmin'],
                helper: 'دسترسی مورد نظر در صورت نیاز'
            },
        ]
    },
];

// ✅ فرم ایجاد (با رمز عبور اجباری)
export const createForm = {
    endpoint: 'manage.users.store',
    updateEndpoint: 'manage.users.update',
    deleteEndpoint: 'manage.users.destroy',

    title: moduleActions.create || publicLang.create,
    fields: [
        ...commonFields,
        {
            name: 'password',
            label: publicLang.password,
            type: 'password',
            required: true,
            min: 6,
            placeholder: 'گذرواژه باید حداقل ۶ کاراکتر باشد',
            helper: 'گذرواژه مناسب باید حداقل ۸ کاراکتر و شامل حروف، اعداد و نمادها باشد',
        },
    ],
    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};

// ✅ فرم ویرایش (با رمز عبور اختیاری و placeholder مجزا)
export const editForm = {
    title: moduleActions.edit || publicLang.edit,
    fields: [
        ...commonFields,
        {
            name: 'password',
            label: 'رمز عبور جدید',
            type: 'password',
            placeholder: 'در صورت تمایل تغییر دهید',
            min: 6,
            hideValue: true,
            value: '',
            helper: 'در صورت تمایل رمز عبور جدید وارد کنید در غیر این صورت خالی بگذارید',
        },
    ],
    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};
```

### نمونه اولیه کنترلر
**در کنترلر از تریت `AdminTableTrait` استفاده کنید:**

```php
<?php
namespace App\Http\Controllers;
use AdminPlatform\Traits\AdminTableTrait;
use App\Models\User;
use Illuminate\Http\Request;
class UserController extends Controller
{
    use AdminTableTrait;
    public function json(Request $request)
    {
        return User::adminTable($request, [
            'search' => ['name', 'email'],
            'filters' => ['is_active'],
        ]);
    }
    public function store(Request $request) { 
        /* پیاده‌سازی */

            if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'کاربر با موفقیت اضافه شد.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'خطا در افزودن کاربر',
            ], 200);
        }
     }
    public function update(Request $request, $id) { 
        /*  پیاده‌سازی  اولیه*/
        DB::beginTransaction();

        $user = User::findOrFail($id);
        $user->update($validated);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }
        if ($request->permissions) {
            $user->syncPermissions($request->permissions);
        }
        DB::commit();

        return response()->json($user);
    }
    public function destroy($id) { 
        /*  پیاده‌سازی  اولیه*/
            DB::beginTransaction();
            User::destroy($id);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'حذف کاربر با موفقیت انجام شد.',
            ], 200);
    }
    public function toggleStatus($id) {
        /*  پیاده‌سازی  اولیه*/
            $user = User::findOrFail($id);
            $user->toggleStatus();
            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'کاربر فعال شد.' : 'کاربر غیرفعال شد.',
                'is_active' => $user->is_active,
            ], 200);
    }
}
```
### نمونه کد تریت `AdminTableTrait` که در مدل ها ساخته میشود

### نمونه کد `adminTransform` در مدل ها 

```php

    use AdminTableTrait;
    public static function adminTransform($cred)
    {
        return [
            'id'           => $cred->id,
            'name'         => $cred->name,
            'email'        => $cred->email,
            'created_at'   => "<div dir='ltr' class='text-center'>"
                . verta($cred->created_at)->format('Y/m/d H:i')
                . "</div>",

        ];
    }
```
### نمونه کد `toggleStatus` در مدل ها

```php
    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        $this->save();
    }
```

###  نمونه تعریف مسیرها در `routes/web.php`:
```php
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/json', [UserController::class, 'json'])->name('json');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});
```
