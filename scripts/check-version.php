<?php
/**
 * اسکریپت بررسی هماهنگی نسخه بین composer.json و CHANGELOG.md
 * 
 * استفاده: php scripts/check-version.php
 * 
 * این اسکریپت بررسی می‌کند که:
 * 1. نسخه در composer.json با نسخه اول CHANGELOG.md مطابقت داشته باشد
 * 2. فرمت نسخه Semantic Versioning باشد
 * 
 * توجه: نسخه‌ها می‌توانند به فرمت فارسی یا انگلیسی باشند
 */

$composerFile = __DIR__ . '/../composer.json';
$changelogFile = __DIR__ . '/../CHANGELOG.md';

// تبدیل اعداد فارسی به انگلیسی
function persianToEnglish($str) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($persian, $english, $str);
}

// خواندن نسخه از composer.json
$composer = json_decode(file_get_contents($composerFile), true);
$composerVersion = $composer['version'] ?? null;

if (!$composerVersion) {
    echo "❌ خطا: نسخه در composer.json یافت نشد\n";
    exit(1);
}

// بررسی فرمت Semantic Versioning
if (!preg_match('/^\d+\.\d+\.\d+$/', $composerVersion)) {
    echo "❌ خطا: فرمت نسخه '$composerVersion' معتبر نیست (باید مانند 1.2.3 باشد)\n";
    exit(1);
}

// خواندن CHANGELOG
$changelog = file_get_contents($changelogFile);

// پیدا کردن اولین نسخه در CHANGELOG
if (preg_match('/## \[(.+?)\]/', $changelog, $matches)) {
    $changelogVersionRaw = $matches[1];
    // تبدیل اعداد فارسی به انگلیسی
    $changelogVersion = persianToEnglish($changelogVersionRaw);
    
    if ($composerVersion !== $changelogVersion) {
        echo "❌ خطا: نسخه composer.json ($composerVersion) با CHANGELOG ($changelogVersionRaw) مطابقت ندارد\n";
        exit(1);
    }
    
    echo "✅ نسخه‌ها هماهنگ هستند: $composerVersion\n";
} else {
    echo "⚠️  هشدار: نسخه‌ای در CHANGELOG یافت نشد\n";
    exit(1);
}

// بررسی وجود تاریخ در نسخه CHANGELOG (با اعداد فارسی یا انگلیسی)
if (preg_match('/## \[' . preg_quote($changelogVersionRaw) . '\] - (.+)$/', $changelog, $dateMatch)) {
    $dateRaw = trim($dateMatch[1]);
    $dateEn = persianToEnglish($dateRaw);
    echo "✅ تاریخ نسخه: $dateEn\n";
} else {
    echo "⚠️  هشدار: تاریخ برای نسخه $composerVersion در CHANGELOG یافت نشد\n";
}

echo "\n🎉 بررسی نسخه با موفقیت انجام شد\n";
