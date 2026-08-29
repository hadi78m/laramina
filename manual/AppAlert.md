.

## 📘 راهنمای استفاده از `AppAlert` (کمک‌کننده پیام و Ajax)

**دستور `AppAlert` یک شیء سراسری است که در فایل `showalertProduction.js` تعریف شده و امکانات زیر را فراهم می‌کند:**

- نمایش پیام‌های موفقیت، خطا، هشدار و توست (با SweetAlert2)
    
- درخواست‌های Ajax با مدیریت خودکار CSRF و اعتبارسنجی
    
- دیالوگ تأیید برای عملیات حذف
    
- کنترل نمایش لودینگ سراسری
    
- تبدیل نام رووت به آدرس واقعی با `AppAlert.route()`
    

> **پیش‌نیازها:** این کتابخانه به **jQuery** و **SweetAlert2** نیاز دارد. مطمئن شوید قبل از لود این فایل، هر دو کتابخانه در صفحه وجود دارند.

---
## 🧩 متدهای اصلی

### 1. نمایش پیام‌ها

| متد                                     | توضیح                          | مثال                                        |
| --------------------------------------- | ------------------------------ | ------------------------------------------- |
| `AppAlert.showSuccess(message, asHtml)` | پیام سبز موفقیت                | `AppAlert.showSuccess('ذخیره شد')`          |
| `AppAlert.showError(message)`           | پیام قرمز خطا                  | `AppAlert.showError('مقدار نامعتبر')`       |
| `AppAlert.showWarning(message)`         | پیام زرد هشدار                 | `AppAlert.showWarning('تأیید نشده')`        |
| `AppAlert.showToast(icon, title)`       | پیام کوتاه (توست) در گوشه صفحه | `AppAlert.showToast('success', 'انجام شد')` |
> توضیح : `asHtml` در `showSuccess` مشخص می‌کند که آیا `message` می‌تواند HTML داشته باشد (پیش‌فرض `true`).

### 2. دیالوگ تأیید (confirm)

**`AppAlert.confirmDelete(url, options)`**  
برای عملیات حذف با هشدار مناسب. پس از تأیید کاربر، یک درخواست `DELETE` به آدرس داده شده ارسال می‌کند.

```javascript
const result = await AppAlert.confirmDelete('/users/5', {
    title: 'حذف کاربر',
    text: 'آیا از حذف این کاربر اطمینان دارید؟'
});
if (result) {

    // حذف موفق
}
```

**`AppAlert.confirmAction(title, text, icon)`**  
یک دیالوگ تأیید ساده (بدون درخواست خودکار Ajax) برمی‌گرداند.

```javascript
const { isConfirmed } = await AppAlert.confirmAction('آیا ادامه می‌دهید؟', '...', 'question');
if (isConfirmed) { ... }
```

---

## 🌐 درخواست‌های Ajax

### `AppAlert.post(url, data, options)`

ارسال درخواست `POST` (یا `PUT`/`PATCH` با استفاده از `_method`) به سرور.  
**برمی‌گرداند:** `Promise` که در صورت موفقیت با پاسخ سرور resolve می‌شود.

```javascript
try {
    const response = await AppAlert.post('/users', {
        name: 'علی',
        email: 'ali@example.com'
    }, {
        loading: true,          // نمایش لودینگ سراسری
        successAlert: true,     // نمایش پیام موفقیت خودکار
        errorAlert: true        // نمایش پیام خطا خودکار
    });
    console.log(response);
} catch (error) {
    // خطا قبلاً توسط errorAlert نمایش داده شده
}
```
#### گزینه‌های مهم `options`:

|گزینه|نوع|پیش‌فرض|توضیح|
|---|---|---|---|
|`loading`|boolean|`false`|نمایش لودینگ سراسری در طول درخواست|
|`successAlert`|boolean|`true`|نمایش پیام موفقیت با استفاده از `res.message`|
|`errorAlert`|boolean|`true`|نمایش خودکار خطاهای ۴xx/۵xx|
|`method`|string|`'POST'`|متد HTTP (می‌توانید `'PUT'` یا `'PATCH'` بدهید)|
|`success`|function|-|تابع callback بعد از موفقیت (قبل از resolve)|
|`error`|function|-|تابع callback بعد از خطا (قبل از reject)|

> **نکته:** برای ارسال `FormData` (مثل آپلود فایل)، مستقیماً نمونه `FormData` را به `data` بدهید و `processData` و `contentType` را خودکار تنظیم می‌کند.

### دستور: `AppAlert.delete(url, options)`

معادل `AppAlert.post` ولی با متد `DELETE`. خروجی Promise است.

```javascript
const result = await AppAlert.delete('/users/5');
if (result) {
    // حذف شد
}
```
### دستور `AppAlert.ajax(url, method, options)` (روش قدیمی)

از jQuery Ajax استفاده می‌کند و یک **XHR** برمی‌گرداند (نه Promise). بهتر است از `post`/`delete` استفاده کنید.

---

## 🧭 تبدیل نام رووت به آدرس

دستور **`AppAlert.route(name, params)`**  
**نام یک رووت لاراول (که در `window.LaravelRoutes` تعریف شده) را دریافت کرده و آدرس کامل آن را با جایگذاری پارامترها برمی‌گرداند.**

```javascript
const url = AppAlert.route('users.update', { id: 5 });
// خروجی: http://domain.test/users/5
```
> **این متد در `window.route` نیز به عنوان fallback در دسترس است.**

---
## ⏳ مدیریت لودینگ

- دستور **`AppAlert.showLoading(title)`** : یک دیالوگ لودینگ (با چرخنده) نمایش می‌دهد. فراخوانی‌های تودرتو را پشتیبانی می‌کند.
    
- دستور **`AppAlert.hideLoading(force)`** : دیالوگ لودینگ را مخفی می‌کند. اگر `force=true` باشد، همه لایه‌ها حذف می‌شوند.
    
همچنین توابع سراسری **`showLooading()`** و **`hideLooading()`** برای نمایش یک المنت سفارشی با `id="global-loading"` وجود دارد (در صورت وجود). این توابع به صورت مستقل از SweetAlert کار می‌کنند.
```javascript
// نمایش لودینگ اختصاصی
showLooading();
await doSomething();
hideLooading();
```

---
## ✅ اعتبارسنجی خودکار (422)

وقتی درخواست `post` با وضعیت `422` (Unprocessable Entity) مواجه شود، `AppAlert` به طور خودکار:

1. کلاس `is-invalid` را از تمام فیلدهای فرم پاک می‌کند.
    
2. برای هر فیلد خطادار، یک `<div class="invalid-feedback ajax">` با متن خطا اضافه می‌کند.
    
3. یک پیام خطا شامل تمام خطاها نشان می‌دهد.
    

برای فعال‌سازی این قابلیت، باید `options.form` را به `AppAlert.post` ارسال کنید:

```javascript
const formElement = document.querySelector('form');
AppAlert.post(url, formData, { form: formElement });
```

---
## 🧩 متدهای کمکی دیگر

| متد                                           | کاربرد                                                     |
| --------------------------------------------- | ---------------------------------------------------------- |
| `AppAlert.clearValidation(form)`              | حذف دستی کلاس‌های خطا از یک فرم                            |
| `AppAlert.showValidationErrors(form, errors)` | نمایش خطاها روی فرم (دستی)                                 |
| `AppAlert.ajaxForm(form, options)`            | اتصال خودکار به رویداد submit فرم و ارسال Ajax (روش قدیمی) |

---

## 🧪 مثال کامل: ثبت فرم با اعتبارسنجی

```javascript
document.querySelector('#user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    try {
        await AppAlert.post('/users', data, {
            form: form,            // برای اعتبارسنجی خودکار
            loading: true,
            successAlert: true
        });
        // در صورت موفقیت، می‌توانید جدول را رفرش کنید
        document.dispatchEvent(new CustomEvent('admin:table:reload'));
        form.reset();
    } catch (error) {
        // خطا قبلاً نمایش داده شده است
    }
});
```
---

## 📌 نکات پایانی

- تمام متدهای `AppAlert` پس از لود شدن فایل در دسترس هستند.
    
- برای استفاده از `AppAlert.route`، حتماً `window.LaravelRoutes` باید توسط لاراول تزریق شده باشد (مطابق مستندات پکیج).
    
- اگر از `AppAlert.post` با `FormData` استفاده می‌کنید، نیازی به تنظیم `Content-Type` نیست – کتابخانه خودکار آن را تشخیص می‌دهد.
    
* با استفاده از این راهنما، می‌توانید به راحتی تعاملات Ajax و پیام‌های کاربرپسند را در ادمین پنل خود پیاده‌سازی کنید.