<?php
/**
 * تنظیمات عمومی سایت
 * در این فایل مقادیر پیش‌فرض را تعریف می‌کنیم.
 * بعداً با صفحه‌ای در پنل مدیریت می‌توان آن را ویرایش کرد.
 */

// مسیر فایل ذخیره‌سازی (اختیاری - فعلاً از آرایه استفاده می‌کنیم)
$site_settings_file = __DIR__ . '/site_settings.json';

// اگر فایل ذخیره وجود داشت، آن را بارگذاری کن
if (file_exists($site_settings_file)) {
    $site_settings = json_decode(file_get_contents($site_settings_file), true);
} else {
    // تنظیمات پیش‌فرض
    $site_settings = [
        'site_title' => 'سایت من',
        'favicon'    => 'ghaleb/manabe/favicon.png'   // مسیر نسبی از BASE_URL
    ];
}

/**
 * دریافت یک تنظیم
 */
function get_site_setting($key) {
    global $site_settings;
    return $site_settings[$key] ?? null;
}

/**
 * ذخیره‌ی تنظیمات جدید
 */
function save_site_settings($new_settings) {
    global $site_settings, $site_settings_file;
    $site_settings = array_merge($site_settings, $new_settings);
    file_put_contents($site_settings_file, json_encode($site_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}