<?php
/**
 * نقطه‌ی ورود اصلی
 * تمام درخواست‌ها بعد از .htaccess به اینجا می‌رسند
 */

// زمان شروع بارگذاری صفحه (برای نمایش زمان بارگذاری به مدیر)
define('PAGE_START_TIME', microtime(true));

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

// اجرای مسیریاب و گرفتن خروجی در بافر
ob_start();
masiryab_kon($url);
$page_output = ob_get_clean();

// نمایش زمان بارگذاری فقط برای کاربر مدیر، در گوشه‌ی بالای صفحه
if (isLoggedIn() && isAdmin()) {
    $elapsed   = microtime(true) - PAGE_START_TIME;
    $seconds   = number_format($elapsed, 4, '.', '');
    $ms        = number_format($elapsed * 1000, 1, '.', '');
    $badge = '<div id="load-time-badge" style="position:fixed; top:8px; left:8px; z-index:99999;'
        . ' background:rgba(20,20,30,0.85); color:#0f0; font:12px/1.4 monospace;'
        . ' padding:6px 10px; border-radius:6px; direction:ltr; text-align:left;'
        . ' box-shadow:0 2px 8px rgba(0,0,0,0.3); pointer-events:none;">'
        . '⏱ ' . $seconds . ' s (' . $ms . ' ms)</div>';
    $page_output = str_ireplace('</body>', $badge . '</body>', $page_output, $count);
    if ($count === 0) {
        $page_output .= $badge;
    }
}

echo $page_output;