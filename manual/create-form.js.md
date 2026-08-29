## 📘 راهنمای کامل ساخت فرم‌ها با `create-form.js`

در پکیج `admin-platform`، فایل `create-form.js` مسئول تعریف ساختار فرم‌های ایجاد (`create`) و ویرایش (`edit`) است. این فرم‌ها درون مودال باز می‌شوند و توسط `FormEngine` رندر می‌شوند. در ادامه تمام امکانات موجود برای تعریف فیلدها را با مثال توضیح می‌دهیم.

---

## 🧱 ساختار پایه یک فرم

```javascript
export const myForm = {
    endpoint: 'module.resource.store',      // آدرس (یا نام رووت) برای ارسال داده
    updateEndpoint: 'module.resource.update', // آدرس ویرایش (اختیاری)
    deleteEndpoint: 'module.resource.destroy', // آدرس حذف (اختیاری)
    title: 'عنوان فرم',
    fields: [ /* تعریف فیلدها */ ],
    buttons: {
        submit: 'ذخیره',
        cancel: 'انصراف'
    }
};
```

> **نکته:** در `editForm` معمولاً `endpoint` را با `updateEndpoint` جایگزین می‌کنیم و `method: 'PUT'` را در هنگام فراخوانی تنظیم می‌کنیم.

---

## 📝 انواع فیلدهای پایه

### 1. فیلد متنی (`text`, `email`, `tel`, `number`)

javascript

{
    name: 'username',
    label: 'نام کاربری',
    type: 'text',
    required: true,
    placeholder: 'نام خود را وارد کنید',
    min: 3,
    max: 50,
    helper: 'حداقل ۳ کاراکتر'
}

- `type` می‌تواند `text`, `email`, `tel`, `number`, `url`, `date` و … باشد.
    
- `required`: اعتبارسنجی سمت کلاینت (و سرور نیز باید انجام شود).
    
- `min` / `max`: برای فیلدهای عددی یا متنی (طول).
    
- `pattern`: regex دلخواه (مثلاً برای کد ملی).
    
- `helper`: توضیح کمکی زیر فیلد.
    

### 2. فیلد رمز عبور (`password`)

javascript

{
    name: 'password',
    label: 'رمز عبور',
    type: 'password',
    required: true,
    placeholder: '********',
    helper: 'حداقل ۸ کاراکتر شامل حروف و اعداد'
}

در حالت **ویرایش** معمولاً این فیلد را حذف می‌کنیم یا غیراجباری می‌کنیم.

### 3. فیلد متنی چندخطی (`textarea`)

javascript

{
    name: 'description',
    label: 'توضیحات',
    type: 'textarea',
    rows: 4
}

### 4. چک‌باکس (`checkbox`)

javascript

{
    name: 'agree',
    label: 'قوانین را می‌پذیرم',
    type: 'checkbox'
}

> مقدار آن `true`/`false` (یا ۱/۰) خواهد بود.

---

## 🧩 فیلد انتخاب (`select`)

### الف) انتخاب ساده با گزینه‌های ثابت

javascript

{
    name: 'status',
    label: 'وضعیت',
    type: 'select',
    required: true,
    options: [
        { value: 'active', label: 'فعال' },
        { value: 'inactive', label: 'غیرفعال' }
    ]
    // یا به صورت آبجکت: options: { active: 'فعال', inactive: 'غیرفعال' }
}

### ب) انتخاب با بارگذاری داینامیک از یک API (Option Endpoint)

javascript

{
    name: 'role_id',
    label: 'نقش',
    type: 'select',
    optionEndpoint: 'roles.list',      // نام رووت لاراول که آرایه‌ای از آبجکت‌ها برمی‌گرداند
    optionLabel: 'name',               // کلید برای عنوان گزینه
    optionValue: 'id',                 // کلید برای مقدار گزینه
    emptyOptionLabel: '—— انتخاب کنید ——',
    required: true
}

پکیج به طور خودکار درخواست GET به `optionEndpoint` می‌زند و نتیجه را به `options` تبدیل می‌کند.

### ج) انتخاب جستجوپذیر (`searchable: true`)

javascript

{
    name: 'city_id',
    label: 'شهر',
    type: 'select',
    options: cities,      // یا optionEndpoint
    searchable: true      // یک جعبه جستجو داخل dropdown اضافه می‌کند
}

### د) انتخاب وابسته (Dependent Select) با AJAX پویا

javascript

{
    name: 'province_id',
    type: 'select',
    label: 'استان',
    options: provinces   // یا optionEndpoint
},
{
    name: 'city_id',
    type: 'select',
    label: 'شهر',
    dependsOn: 'province_id',           // به فیلد دیگر وابسته است
    ajax: {
        url: '/api/cities?province={value}',   // {value} با مقدار province_id جایگزین می‌شود
        valueKey: 'id',
        labelKey: 'name'
    }
}

> **نحوه کار:** هر زمان مقدار `province_id` تغییر کند، درخواست جدید به `ajax.url` ارسال شده و گزینه‌های `city_id` به‌روز می‌شوند.

---

## 🧱 گروه‌بندی فیلدها در یک ردیف (`group`)

برای قرار دادن دو یا چند فیلد در کنار هم (مثلاً نام و نام خانوادگی):

javascript

{
    type: 'group',
    group: [
        { name: 'first_name', label: 'نام', type: 'text', required: true },
        { name: 'last_name', label: 'نام خانوادگی', type: 'text', required: true }
    ]
}

گروه‌ها به طور پیش‌فرض در یک `grid-cols-2` نمایش داده می‌شوند. می‌توانید با `grid` تعداد ستون‌ها را تنظیم کنید (مستندات پیشرفته‌تر).

---

## 🔐 کنترل دسترسی به فیلدها (`role` / `permission`)

می‌توانید فیلد را فقط به کاربرانی با نقش یا دسترسی خاص نشان دهید:

javascript

{
    name: 'secret_key',
    label: 'کلید مخفی',
    type: 'text',
    role: ['SuperAdmin']        // فقط سوپرادمین می‌بیند
},
{
    name: 'admin_panel',
    label: 'دسترسی به پنل',
    type: 'checkbox',
    permission: 'access_admin'   // فقط کاربرانی که این دسترسی را دارند
}

> در `editForm` هم این محدودیت‌ها اعمال می‌شوند.

---

## ♻️ استفاده مجدد از فیلدهای مشترک (Common Fields)

برای جلوگیری از تکرار در `createForm` و `editForm`، فیلدهای مشترک را در یک آرایه جدا تعریف کنید:

javascript

const commonFields = [
    { name: 'name', label: 'نام', type: 'text', required: true },
    { name: 'email', label: 'ایمیل', type: 'email', required: true },
    { name: 'is_active', label: 'فعال', type: 'checkbox' }
];
export const createForm = {
    // ...
    fields: [
        ...commonFields,
        { name: 'password', label: 'رمز عبور', type: 'password', required: true }
    ]
};
export const editForm = {
    // ...
    fields: commonFields   // بدون فیلد رمز عبور
};

---

## ✂️ حذف یک فیلد در ویرایش (مثال رمز عبور)

راه اول: استفاده از `filter` روی `fields`:

javascript

export const editForm = {
    ...createForm,
    fields: createForm.fields.filter(f => f.name !== 'password')
};

راه دوم: تعریف جداگانه `editForm` با استفاده از `commonFields` (مثل مثال بالا).

---

## 🧪 یک مثال کامل

javascript

const publicLang = AdminLang.getNamespace('common');
export const createForm = {
    endpoint: 'users.store',
    updateEndpoint: 'users.update',
    deleteEndpoint: 'users.destroy',
    title: 'افزودن کاربر جدید',
    fields: [
        {
            name: 'personal_info',
            type: 'group',
            group: [
                { name: 'first_name', label: 'نام', type: 'text', required: true },
                { name: 'last_name', label: 'نام خانوادگی', type: 'text', required: true }
            ]
        },
        { name: 'email', label: 'ایمیل', type: 'email', required: true },
        { name: 'password', label: 'رمز عبور', type: 'password', required: true },
        {
            name: 'country_id',
            label: 'کشور',
            type: 'select',
            optionEndpoint: 'countries.list',
            optionLabel: 'name',
            optionValue: 'id',
            searchable: true
        },
        {
            name: 'city_id',
            label: 'شهر',
            type: 'select',
            dependsOn: 'country_id',
            ajax: {
                url: '/api/cities/{value}',
                valueKey: 'id',
                labelKey: 'name'
            }
        },
        {
            name: 'is_active',
            label: 'فعال',
            type: 'checkbox'
        }
    ],
    buttons: { submit: 'ذخیره', cancel: 'انصراف' }
};

---

## 🧠 نکات نهایی

- **اعتبارسنجی سمت کلاینت** فقط برای بهبود UX است؛ حتماً در کنترلر لاراول هم اعتبارسنجی کنید.
    
- **گزینه‌های `ajax` و `optionEndpoint`** هر دو پشتیبانی می‌شوند. `optionEndpoint` ساده‌تر است و نیازی به نوشتن تابع `dependsOn` ندارد، اما برای وابستگی‌های زنجیره‌ای از `ajax` و `dependsOn` استفاده کنید.
    
- **فیلد `value` در فرم ویرایش** به طور خودکار از `data` دریافت می‌شود. اگر نمی‌خواهید مقدار رمز عبور نمایش داده شود، از `hideValue: true` یا همان روش حذف فیلد استفاده کنید.
    
- **مقادیر پیش‌فرض:** می‌توانید با `value: 'something'` مقدار اولیه تعیین کنید.
    

---

## 🚀 جمع‌بندی

با استفاده از این امکانات می‌توانید هر نوع فرم پیچیده‌ای را در ادمین پنل خود پیاده‌سازی کنید. فایل `create-form.js` قلب تعامل با فرم‌هاست و به راحتی با `FormEngine` و `ModalPlugin` هماهنگ می‌شود.