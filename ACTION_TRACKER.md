# ACTION_TRACKER.md - گزارش تغییرات و ریفکتورهای بزرگ

> **هدف:** ثبت دقیق فایل‌های تغییریافته و خلاصه‌ای از اصلاحات انجام‌شده در جلسات کاری سنگین.

---

## 📅 گزارش تغییرات - 1405/06/08

### 📝 خلاصه اقدامات
- رفع آسیب‌پذیری SQL Injection در sort + direction پارامتر (AdminTableTrait)
- اضافه کردن فایل ترجمه انگلیسی (en/adminUI.php)
- بروزرسانی جامع README.md با مستندات فارسی + انگلیسی + Security Notes
- اصلاح ServiceProvider برای publish دقیق‌تر lang files

### 🛠️ فایل‌های تغییر یافته

```text
src/Traits/AdminTableTrait.php          # SQL Injection fix + sort whitelist + per_page cap
src/AdminPlatformServiceProvider.php    # lang publish paths اصلاح شد
resources/lang/en/adminUI.php           # فایل ترجمه انگلیسی جدید
README.md                               # بروزرسانی جامع
MEMORY.md                               # ثبت وضعیت تسک‌ها
```