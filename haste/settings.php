<?php
/**
 * تنظیمات پنل مدیریت (رنگ، فونت و...)
 */

// مسیر فایل ذخیره‌سازی را یک ثابت می‌کنیم تا همه جا در دسترس باشد
define('ADMIN_SETTINGS_FILE', __DIR__ . '/admin_settings.json');

// اگر فایل وجود داشت، از آن بخوان، وگرنه پیش‌فرض
if (file_exists(ADMIN_SETTINGS_FILE)) {
    $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true);
    if (!is_array($admin_settings)) {
        $admin_settings = [];
    }
} else {
    $admin_settings = [
        'bg_color' => '#f0f2f5',
        'font'     => 'Tahoma',
        'favicon'  => ''
    ];
}

/**
 * دریافت همهٔ تنظیمات
 */
function get_admin_settings() {
    global $admin_settings;
    return $admin_settings;
}

/**
 * ذخیرهٔ تنظیمات جدید
 */
function save_admin_settings($new_settings) {
    global $admin_settings;
    if (!is_array($admin_settings)) {
        $admin_settings = [];
    }
    $admin_settings = array_merge($admin_settings, $new_settings);
    // مستقیماً از ثابت استفاده می‌کنیم، دیگر نیازی به global متغیر فایل نیست
    file_put_contents(ADMIN_SETTINGS_FILE, json_encode($admin_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}