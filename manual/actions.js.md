
## 📘 راهنمای استفاده از فایل `actions.js` در پکیج `admin-platform`

فایل `actions.js` قلب عملیات‌های سطری هر جدول است. در این فایل، شما دکمه‌هایی مانند **ویرایش**، **حذف**، **مشاهده**، **تغییر وضعیت** و هر اکشن دلخواه دیگری را تعریف می‌کنید. هر اکشن می‌تواند دارای آیکون، رنگ، عنوان راهنما، پیغام تأیید، و محدودیت مبتنی بر نقش (role) یا دسترسی (permission) باشد.

**در ادامه با مثال‌های گوناگون، نحوه نوشتن یک فایل `actions.js` کامل را آموزش می‌دهیم.**

---

## 🧱 ساختار پایه یک اکشن

همه اکشن‌ها باید از تابع کمکی `action` استفاده کنند که در `core/action.js` پکیج تعریف شده است. این تابع دو آرگومان می‌گیرد:

- آیتم `meta`: تنظیمات ظاهری و امنیتی (مانند `icon`, `color`, `tooltip`, `role`, `permission`, `confirm`)
    
- آیتم `handler`: تابع اصلی که هنگام کلیک روی دکمه اجرا می‌شود. این تابع سه پارامتر دریافت می‌کند:
    
    - آیتم `row`: داده‌های ردیف جاری (شیء)
        
    - آیتم `table`: نمونه جدول (برای دسترسی به متدهایی مانند `loadData`, `updateRow`, `removeRow`)
        
    - آیتم `event`: رویداد کلیک (اختیاری)
        

---

## 📦 مثال‌های عملی

### 1. اکشن ساده (نمایش در کنسول)

```javascript
import { action } from '/js/admin-platform/core/action.js';
export const userActions = {
    view: action({
        icon: 'fas fa-eye',
        color: 'text-green-600',
        tooltip: 'مشاهده جزئیات',
    }, (row, table) => {
        console.log('مشاهده ردیف:', row);
        // می‌توانید یک مودال باز کنید یا به صفحه جزئیات بروید
        window.location.href = `/users/${row.id}`;
    }),
};
```

### 2. اکشن ویرایش (باز کردن مودال با فرم پر شده)
```javascript

import ModalPlugin from '/js/admin-platform/plugins/ui/modal/modal-plugin.js';
import FormEngine from '/js/admin-platform/engines/form-engine.js';
import { createForm } from './forms/create-form.js';
import { action } from '/js/admin-platform/core/action.js';
export const userActions = {
    edit: action({
        icon: 'fas fa-edit',
        color: 'text-blue-600',
        tooltip: 'ویرایش',
    }, (row, table, event) => {
        event?.preventDefault();
        const url = AppAlert.route(createForm.updateEndpoint, { id: row.id });
        ModalPlugin.open({
            title: 'ویرایش کاربر',
            width: '600px',
            content: (container) => {
                const config = {
                    ...createForm,
                    endpoint: url,
                    method: 'PUT'
                };
                FormEngine.render(config, container, row);
            }
        });
    }),
};
```

### 3. اکشن حذف با پیغام تأیید و به‌روزرسانی جدول

```javascript

delete: action({
    icon: 'fas fa-trash',
    color: 'text-red-600',
    tooltip: 'حذف',
    confirm: 'آیا از حذف این آیتم اطمینان دارید؟', // پیغام تأیید ساده
    // یا می‌توانید از confirmTitle و confirmText پیشرفته استفاده کنید
}, async (row, table) => {
    const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });
    const result = await AppAlert.confirmDelete(url, {
        title: 'حذف کاربر',
        text: 'این عملیات قابل بازگشت نیست'
    });
    if (result) {
        document.dispatchEvent(new CustomEvent('admin:table:remove-row', {
            detail: { id: row.id }
        }));
    }
}),
```

### 4. اکشن تغییر وضعیت (toggle) با استفاده از متد کمکی `setToggle`

```javascript

setToggle(id, table, endpoint) {
    const url = AppAlert.route(endpoint, { id });
    return AppAlert.post(url, {}, {
        loading: true,
        successAlert: true
    }).then((res) => {
        if (res.update === 'table') {
            table.loadData(); // رفرش کامل
        } else if (res.provider && typeof table.updateRow === 'function') {
            table.updateRow(res.provider);
        } else {
            // بروزرسانی جزئی (فقط فیلدهای مجاز)
            const oldRow = table.currentRows.find(r => r.id == id);
            if (oldRow) {
                const newRow = { ...oldRow, is_active: res.is_active };
                table.updateRow(newRow);
            }
        }
    });
},
```
>  سپس در ستون action-toggle از این متد استفاده می‌شود
 برای تعریف مستقیم اکشن toggle می‌توانید از یک action معمولی هم استفاده کنید:

```javascript
toggleStatus: action({
    icon: 'fas fa-toggle-on',
    color: 'text-green-600',
    tooltip: 'تغییر وضعیت',
}, async (row, table) => {
    const endpoint = 'manage.users.toggle-status';
    const url = AppAlert.route(endpoint, { id: row.id });
    await AppAlert.post(url, {}, { loading: true, successAlert: true });
    table.loadData(); // رفرش کامل
}),
```

### 5. اکشن با محدودیت نقش (Role‑Based Access Control)

```javascript
delete: action({
    icon: 'fas fa-trash',
    color: 'text-red-600',
    tooltip: 'حذف',
    roles: ['SuperAdmin'],          // فقط سوپرادمین این دکمه را می‌بیند
    // یا به صورت آرایه: roles: ['Admin', 'SuperAdmin']
    permission: 'delete_users',     // همچنین می‌توان از permission استفاده کرد
}, async (row, table) => {

    // ... کد حذف
}),
```
### 6. اکشن سفارشی (مثلاً تمدید توکن، کپی لینک، ارسال نوتیفیکیشن)

```javascript

extendToken: action({
    icon: 'fas fa-clock',
    color: 'text-yellow-600',
    tooltip: 'تمدید اعتبار',
    confirm: 'آیا از تمدید توکن اطمینان دارید؟',
}, async (row, table) => {
    const url = AppAlert.route('tokens.extend', { id: row.id });
    const res = await AppAlert.post(url, {}, { loading: true, successAlert: true });
    if (res?.expires_at) {
        // به‌روزرسانی یک فیلد خاص در ردیف
        const updatedRow = { ...row, expires_at: res.expires_at };
        table.updateRow(updatedRow);
    }
}),
```

### 7. اکشن با آیکون و کلاس سایز دلخواه
```javascript
customAction: action({
    icon: 'fas fa-download',
    color: 'text-purple-600',
    size: 'text-2xl',   // سایز آیکون (پیش‌فرض text-sm)
    tooltip: 'دانلود گزارش',
}, (row) => {
    window.open(`/report/${row.id}/download`);
}),
```

---

## 🧩 نحوه اتصال اکشن‌ها به جدول

در فایل `table.js`، باید آبجکت اکشن‌ها را به کلید `actions` اختصاص دهید:

```javascript
import { userActions } from './actions.js';
export default {
    // ...
    actions: userActions,
    columns: [
        // ...
        {
            label: 'عملیات',
            type: 'actions',
            // فقط نام اکشن‌ها
            actions: ['edit', 'delete', 'toggleStatus', 'extendToken']   
            
        }
    ]
};
```

> **نکته:** نام اکشن‌ها باید با کلیدهای تعریف شده در `userActions` یکی باشد.

---

## 🧠 نکات پیشرفته

- استفاده از **`event?.preventDefault()`**: اگر دکمه درون یک `<a>` یا فرم باشد، از رفتار پیش‌فرض جلوگیری کنید.
    
- استفاده از **`table.loadData()`**: کل جدول را از سرور بارگذاری مجدد می‌کند (حفظ pagination و فیلترها).
    
- استفاده ار **`table.updateRow(newRow)`**: فقط یک ردیف خاص را به‌روز می‌کند (بدون رفرش کل جدول).
    
- استفاده از **`document.dispatchEvent(...)`**: می‌توانید رویدادهای سفارشی برای ارتباط بین اجزای مختلف ارسال کنید. پکیج از رویدادهای زیر پشتیبانی می‌کند:
    
    - `admin:table:reload`
        
    - `admin:table:update-row`
        
    - `admin:table:remove-row`
        
    - `admin:table:add-row`
        

---

## 📝 یک فایل `actions.js` کامل (نمونه)

```javascript

import ModalPlugin from '/js/admin-platform/plugins/ui/modal/modal-plugin.js';
import FormEngine from '/js/admin-platform/engines/form-engine.js';
import { createForm } from './forms/create-form.js';
import { action } from '/js/admin-platform/core/action.js';
const publicLang = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('modules.users.actions');
export const userActions = {
    view: action({
        icon: 'fas fa-eye',
        color: 'text-green-600',
        tooltip: publicLang.view || 'مشاهده',
    }, (row) => {
        window.location.href = `/users/${row.id}`;
    }),
    edit: action({
        icon: 'fas fa-edit',
        color: 'text-blue-600',
        tooltip: publicLang.edit,
    }, (row, table, event) => {
        event?.preventDefault();
        const url = AppAlert.route(createForm.updateEndpoint, { id: row.id });
        ModalPlugin.open({
            title: moduleActions.edit || publicLang.edit,
            width: '600px',
            content: (container) => {
                const config = { ...createForm, endpoint: url, method: 'PUT' };
                FormEngine.render(config, container, row);
            }
        });
    }),
    delete: action({
        icon: 'fas fa-trash',
        color: 'text-red-600',
        tooltip: publicLang.delete,
        roles: ['SuperAdmin'],
    }, async (row, table) => {
        const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });
        const result = await AppAlert.confirmDelete(url, {
            title: moduleActions.delete_title || (publicLang.delete + ' ' + publicLang.item),
        });
        if (result) {
            document.dispatchEvent(new CustomEvent('admin:table:remove-row', {
                detail: { id: row.id }
            }));
        }
    }),
    toggleStatus: action({
        icon: 'fas fa-toggle-on',
        color: 'text-green-600',
        tooltip: 'تغییر وضعیت',
    }, async (row, table) => {
        const url = AppAlert.route('manage.users.toggle-status', { id: row.id });
        const res = await AppAlert.post(url, {}, { loading: true, successAlert: true });
        if (res?.is_active !== undefined) {
            table.updateRow({ ...row, is_active: res.is_active });
        }
    }),
};
```
---

## 🚀 جمع‌بندی

با استفاده از الگوهای بالا، می‌توانید هر عملیات دلخواهی را به ردیف‌های جدول خود اضافه کنید. مزیت اصلی این روش، **استفاده مجدد** از منطق در تمام ماژول‌ها و **یکپارچگی کامل** با سیستم احراز هویت و دسترسی لاراول است.