<?php
/**
 * تنظیمات عمومی سایت
 * در این فایل مقادیر پیش‌فرض را تعریف می‌کنیم.
 * بعداً با صفحه‌ای در پنل مدیریت می‌توان آن را ویرایش کرد.
 */

// مسیر فایل ذخیره‌سازی را یک ثابت می‌کنیم تا همه جا در دسترس باشد
if (!defined('SITE_SETTINGS_FILE')) {
    define('SITE_SETTINGS_FILE', __DIR__ . '/site_settings.json');
}

// اگر فایل ذخیره وجود داشت، آن را بارگذاری کن
if (file_exists(SITE_SETTINGS_FILE)) {
    $json = file_get_contents(SITE_SETTINGS_FILE);
    $decoded = json_decode($json, true);
    $site_settings = is_array($decoded) ? $decoded : [];
} else {
    // تنظیمات پیش‌فرض
    $site_settings = [
        'site_title' => 'سایت من',
        'favicon'    => 'ghaleb/manabe/favicon.png'
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
    global $site_settings;
    if (!is_array($site_settings)) {
        $site_settings = [];
    }
    $site_settings = array_merge($site_settings, $new_settings);
    file_put_contents(SITE_SETTINGS_FILE, json_encode($site_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    clearstatcache();
}