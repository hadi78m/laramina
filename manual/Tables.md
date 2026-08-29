## 📘 راهنمای استفاده از فایل table.js در پکیج admin-platform

فایل table.js قلب هر ماژول ادمین پنل است. این فایل ساختار جدول، ستون‌ها، فیلترها، عملیات‌ها و مودال‌های مربوط به یک ماژول (مثلاً مدیریت کاربران، محصولات و ...) را تعریف می‌کند. در ادامه تمام آیتم‌های قابل استفاده در این فایل را با مثال توضیح می‌دهیم.

### 🧱 ساختار کلی یک فایل table.js

```javascript
import { createForm } from './forms/create-form.js'
import { userActions } from './actions.js'

const publicLang   = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('modules.users.fields');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export default {
    // کلیدهای پیکربندی جدول
}
```
> نکته: AdminLang.getNamespace(...) برای ترجمه استفاده می‌شود. کلیدها در فایل‌های زبان (adminUI.php) تعریف می‌شوند.

### ⚙️ کلیدهای اصلی پیکربندی
#### 1. endpoint (اجباری)
آدرس API که داده‌های جدول را به صورت JSON برمی‌گرداند. می‌تواند نام رووت لاراول یا مسیر نسبی باشد.

```javascript
endpoint: 'manage.users.json',
```
> نکته: پکیج به طور خودکار این مقدار را با AppAlert.route() تفسیر می‌کند؛ بنابراین می‌توانید نام رووت تعریف شده در web.php را قرار دهید.

### 2. search (اختیاری)
نشان می‌دهد که آیا جعبه جستجو در بالای جدول نمایش داده شود یا خیر.

```javascript
search: true,   // پیش‌فرض false
```
### 3. headerTitle (اختیاری)
عنوانی که در کارت بالای جدول نمایش داده می‌شود. معمولاً از ترجمه خوانده می‌شود.

```javascript
headerTitle: moduleFields.header_title || (publicLang.manage + ' ' + moduleFields.title),
```

### 4. addButtonLabel (اختیاری)
متن دکمه «افزودن» در بالای جدول.

```javascript
addButtonLabel: moduleActions.create || (publicLang.create + ' ' + publicLang.item),
```
### 5. displayButton (اختیاری)
نشان می‌دهد که دکمه افزودن نمایش داده شود یا خیر.

```javascript
displayButton: true,
```
### 6. actions (اجباری)
مرجع به آبجکت حاوی اکشن‌های سطری (مانند edit, delete و ...). این اکشن‌ها در فایل actions.js تعریف می‌شوند.

```javascript
import { userActions } from './actions.js'
actions: userActions,
```
### 7. perPage (اختیاری)
تعداد ردیف در هر صفحه. پیش‌فرض 10.

```javascript
perPage: 15,
```
### 8. modalTheme (اختیاری)
تم مودال‌ها (light یا dark).

```javascript
modalTheme: 'light',
```
### 9. modals (اجباری اگر دکمه افزودن یا ویرایش داشته باشید)
تعریف مودال‌های ایجاد (create) و ویرایش (edit). هر مودال شامل title, width, form (مرجع به فرم موجود در forms/create-form.js) است.

```javascript
modals: {
    create: {
        title: moduleActions.create || publicLang.create,
        width: '500px',
        form: createForm,
        role: ['SuperAdmin']      // (اختیاری) نقش‌های مجاز برای دیدن مودال
    },
    edit: {
        title: moduleActions.edit || publicLang.edit,
        width: '600px',
        form: createForm
    }
},
```
> نکته: می‌توانید برای مودال‌ها خاصیت role یا roles هم تعریف کنید تا فقط کاربران دارای آن نقش بتوانند آن مودال را باز کنند.

### 10. filters (اختیاری)
> آرایه‌ای از فیلترهایی که در بالای جدول نمایش داده می‌شوند. هر فیلتر شامل کلیدهای زیر است:

> key: نام فیلد در دیتابیس که باید فیلتر شود.

> label: عنوان فیلتر.

> type: فعلاً فقط select پشتیبانی می‌شود.

> options: یک آرایه یا آبجکت شامل گزینه‌ها.

```javascript
filters: [
    {
        key: 'is_active',
        label: publicLang.status,
        type: 'select',
        options: {
            1: publicLang.active,
            0: publicLang.inactive
        }
    },
    {
        key: 'role',
        label: 'نقش',
        type: 'select',
        options: [
            { value: 'admin', label: 'ادمین' },
            { value: 'user', label: 'کاربر عادی' }
        ]
    }
],
```
### 11. columns (اجباری)
> آرایه‌ای از تعریف ستون‌های جدول. هر ستون می‌تواند خصوصیات زیر را داشته باشد:

  

|کلید|نوع|توضیح|
|---|---|---|
|`key`|string|نام فیلد در داده‌های دریافتی از API|
|`label`|string|عنوان ستون (معمولاً از ترجمه)|
|`sortable`|boolean|آیا ستون قابلیت مرتب‌سازی دارد؟ (پیش‌فرض false)|
|`type`|string|نوع رندر ستون (`actions`, `action-toggle`, `badge`, `boolean-icon`, `image`, `date`, `copy`, …)|
|`actions`|array|برای ستون از نوع `actions`، لیست نام اکشن‌ها (مثل `['edit', 'delete']`)|
|`endpoint`|string|برای `action-toggle`، endpoint تغییر وضعیت (مثلاً `users.toggle-status`)|
|`confirmTitle`|string|عنوان پیغام تأیید برای toggle|
|`map` / `icons`|object|برای `badge` یا `action-toggle` نگاشت مقادیر true/false به رنگ و متن|
|`visible`|function|شرط نمایش ستون (بر اساس داده ردیف یا نقش کاربر)|
|`roles` / `permission`|array/string|نقش یا دسترسی مورد نیاز برای دیدن ستون|


مثال ستون ساده:
```javascript
{ key: 'id', label: publicLang.id, sortable: true },
{ key: 'name', label: publicLang.name, sortable: true },
```
> مثال ستون عملیات (دکمه‌ها):


```javascript
{
    label: publicLang.actions,
    type: 'actions',
    actions: ['edit', 'delete']
}
```

> مثال ستون toggle فعال/غیرفعال:

```javascript
{
    key: 'is_active',
    label: publicLang.status,
    type: 'action-toggle',
    endpoint: 'manage.users.toggle-status',
    confirmTitle: publicLang.confirm_toggle_status,
    map: {
        true: { label: publicLang.active, color: 'green' },
        false: { label: publicLang.inactive, color: 'gray' }
    },
    icons: {
        true: { html: '<i class="fa-solid fa-toggle-on text-lg"></i>', color: 'green' },
        false: { html: '<i class="fa-solid fa-toggle-off text-lg"></i>', color: 'red' }
    }
}
```

> مثال ستون با نقش خاص (فقط سوپرادمین ببیند):

```javascript
{
    key: 'national_code',
    label: publicLang.national_code,
    roles: ['SuperAdmin']
}
```

>مثال ستون شرطی بر اساس داده ردیف:


```javascript
{
    key: 'special_field',
    label: 'فیلد ویژه',
    visible: (row) => row.has_special === true
}
```

> مثال ستون تصویر (type: image):


```javascript
{
    key: 'avatar',
    label: 'آواتار',
    type: 'image'
}
```
> مثال ستون تاریخ (type: date):


```javascript
{
    key: 'created_at',
    label: 'تاریخ ایجاد',
    type: 'date'
}
```

> مثال ستون کپی (type: copy):

```javascript
{
    key: 'token',
    label: 'توکن',
    type: 'copy'
}
```

### 📝 یک مثال کامل table.js برای ماژول کاربران

```javascript
import { createForm } from './forms/create-form.js'
import { userActions } from './actions.js'

const publicLang   = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('modules.users.fields');
const moduleActions = AdminLang.getNamespace('modules.users.actions');

export default {
    endpoint: 'manage.users.json',
    search: true,
    headerTitle: moduleFields.header_title || (publicLang.manage + ' ' + moduleFields.title),
    addButtonLabel: moduleActions.create || (publicLang.create + ' ' + publicLang.item),
    displayButton: true,
    actions: userActions,
    perPage: 10,
    modalTheme: 'light',
    modals: {
        create: {
            title: moduleActions.create || publicLang.create,
            width: '500px',
            form: createForm,
            role: ['SuperAdmin']
        },
        edit: {
            title: moduleActions.edit || publicLang.edit,
            width: '600px',
            form: createForm
        }
    },
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
    columns: [
        { key: 'id', label: publicLang.id, sortable: true },
        { key: 'name', label: publicLang.name, sortable: true },
        { key: 'email', label: publicLang.email },
        {
            key: 'national_code',
            label: publicLang.national_code,
            roles: ['SuperAdmin']
        },
        {
            key: 'is_active',
            label: publicLang.status,
            type: 'action-toggle',
            endpoint: 'manage.users.toggle-status',
            confirmTitle: publicLang.confirm_toggle_status,
            map: {
                true: { label: publicLang.active, color: 'green' },
                false: { label: publicLang.inactive, color: 'gray' }
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

### 💡 نکات پیشرفته
* ***ترجمه** : تمام متن‌ها باید از publicLang یا moduleFields / moduleActions گرفته شوند. در صورت نبود کلید ترجمه، می‌توانید یک مقدار پیش‌فرض (fallback) با || تعریف کنید.

* **دسترسی مبتنی بر نقش**: می‌توانید برای کل مودال (role در modals)، برای ستون (roles در columns) و برای اکشن‌ها (در فایل actions.js) محدودیت نقش تعریف کنید.

* **تنظیمات پیش‌فرض**: حتماً فایل‌های ترجمه common و modules.xxx.fields و modules.xxx.actions در پروژه تعریف شده باشند تا از undefined جلوگیری شود.

### 🚀 جمع‌بندی
با استفاده از این راهنما می‌توانید هر ماژول دلخواه خود را با تعریف table.js، `actions.js` و create-form.js به سادگی به ادمین پنل اضافه کنید. فایل table.js مسئول نمایش، فیلتر، جستجو، مرتب‌سازی و عملیات اصلی جدول است و کاملاً با سایر اجزای پکیج هماهنگ می‌شود.