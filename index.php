<?php
/**
 * نقطه‌ی ورود اصلی
 * تمام درخواست‌ها بعد از .htaccess به اینجا می‌رسند
 */

// اول تنظیمات پایه را بارگذاری می‌کنیم (فایل tanzimat شامل ثابت‌های دیتابیس است)
require_once __DIR__ . '/haste/tanzimat.php';

// فایل توابع عمومی (redirect, isLoggedIn, isAdmin)
require_once __DIR__ . '/haste/tavabe.php';

// هسته‌ی مسیریاب را فراخوانی می‌کنیم
require_once __DIR__ . '/haste/masiryab.php';

// پارامتر url را از .htaccess می‌گیریم، اگر نبود مقدار پیش‌فرض 'home'
$url = $_GET['url'] ?? 'home';

// مسیر را تمیز می‌کنیم (حذف کاراکترهای خطرناک)
$url = filter_var($url, FILTER_SANITIZE_URL);

// اجرای مسیریاب
masiryab_kon($url);