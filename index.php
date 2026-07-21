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
    // تبدیل اعداد به فارسی
    $fa = function($n) { return strtr($n, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹','.'=>'٫']); };
    $badge_js = '<script>'
        . 'var b=document.createElement("div");'
        . 'b.id="load-time-badge";'
        . 'b.style.cssText="background:rgba(20,20,30,0.85);color:#0f0;font:12px/1.4 Vazir,sans-serif;'
        . 'padding:5px 12px;border-radius:0 0 8px 8px;direction:rtl;text-align:center;'
        . 'display:inline-block;pointer-events:none;font-weight:600;";'
        . 'b.textContent="⏱ ' . $fa($seconds) . ' ث (' . $fa($ms) . ' میلی‌ثانیه)";'
        . 'document.body.insertBefore(b, document.body.firstChild);'
        . '</script>';
    if (stripos($page_output, '</body>') !== false) {
        $page_output = str_ireplace('</body>', $badge_js . '</body>', $page_output);
    } else {
        $page_output .= $badge_js;
    }
    // فارسی کردن تمام اعداد صفحه
    $page_output .= '<script>'
        . '(function(){'
        . 'var map={"0":"۰","1":"۱","2":"۲","3":"۳","4":"۴","5":"۵","6":"۶","7":"۷","8":"۸","9":"۹"};'
        . 'function fa(n){return n.replace(/[0-9]/g,function(d){return map[d]});}'
        . 'var walk=document.createTreeWalker(document.body,NodeFilter.SHOW_TEXT,null,false);'
        . 'var nodes=[];while(walk.nextNode())nodes.push(walk.currentNode);'
        . 'nodes.forEach(function(n){'
        . 'if(n.nodeValue && /[0-9]/.test(n.nodeValue)&&!n.parentNode.closest("script,style,input,textarea")){'
        . 'n.nodeValue=fa(n.nodeValue);}});'
        . '})();</script>';
}

echo $page_output;