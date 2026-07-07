<?php
/**
 * تنظیمات پایه سایت مهراد سام
 * این فایل هم روی لاراگون و هم روی هاست اصلی کار می‌کند
 */

// ====================================================
// تنظیمات پایگاه داده
// ====================================================
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');         // روی هاست اصلی رمز خودت را بذار
define('DB_NAME', 'mehrsamdb');

// ====================================================
// آدرس پایه - خودکار تشخیص می‌دهد (لوکال یا هاست)
// ====================================================
$_prot = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_dir  = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$_base = ($_dir === '/' || $_dir === '\\') ? '' : rtrim(str_replace('\\', '/', $_dir), '/');
define('BASE_URL', $_prot . '://' . $_host . $_base . '/');
unset($_prot, $_host, $_dir, $_base);

// ====================================================
// مسیرهای سیستمی
// ====================================================
define('MASIR_RISH',   dirname(__DIR__) . DIRECTORY_SEPARATOR);   // ریشه پروژه
define('GHALEB_FAAAL', 'mehrsam');                                 // قالب فعال
define('MASIR_GHALEB', MASIR_RISH . 'ghaleb/' . GHALEB_FAAAL . DIRECTORY_SEPARATOR);
define('URL_GHALEB',   BASE_URL . '/ghaleb/' . GHALEB_FAAAL);
define('MASIR_DADE',   MASIR_RISH . 'dade' . DIRECTORY_SEPARATOR);

// ====================================================
// تنظیمات سایت
// ====================================================
define('SITE_NAME',    'مهراد سام');
define('SITE_SLOGAN',  'خدمات کامپیوتر مهراد سام در تهران');
define('SITE_EMAIL',   'ali.asgari.6106@gmail.com');
define('SITE_TEL',     '۰۹۱۰۵۹۲۱۳۵۸');
define('SITE_ADRES',   'تهران، ضلع شمال غرب تقاطع چمران و جلال آل احمد، گیشا، ابتدای بلوچستان، ساختمان گیشا پلاک ۸ واحد ۴');

define('SITE_TEL_EN',    '989105921358');
define('SITE_TELEGRAM',  'https://t.me/mehrsys61');
define('SITE_WHATSAPP',  'https://wa.me/989105921358');
define('SITE_BALE',      'https://ble.ir/ali2761');
define('SITE_INSTAGRAM', '#');
define('SITE_HOURS',     'شنبه تا پنج‌شنبه: ۹ تا ۲۰');

// ====================================================
// درگاه پرداخت زرین‌پال
// ====================================================
define('ZARINPAL_MERCHANT', '');  // مرچنت کد را اینجا بگذار
define('ZARINPAL_SANDBOX',  true); // true = سندباکس، false = عملیاتی

// ====================================================
// نشست (Session)
// ====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
