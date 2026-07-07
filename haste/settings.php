<?php
/**
 * تنظیمات پنل مدیریت (رنگ، فونت و...)
 */

// مسیر فایل ذخیره‌سازی را یک ثابت می‌کنیم
if (!defined('ADMIN_SETTINGS_FILE')) {
    define('ADMIN_SETTINGS_FILE', __DIR__ . '/admin_settings.json');
}

// اگر فایل وجود داشت، از آن بخوان
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

function get_admin_settings() {
    global $admin_settings;
    if (!is_array($admin_settings)) {
        $admin_settings = [];
    }
    return $admin_settings;
}

function save_admin_settings($new_settings) {
    global $admin_settings;
    if (!is_array($admin_settings)) {
        $admin_settings = [];
    }
    $admin_settings = array_merge($admin_settings, $new_settings);
    file_put_contents(ADMIN_SETTINGS_FILE, json_encode($admin_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    clearstatcache();
}