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
$_host = $_SERVER['HTTP_HOST'];
$_dir  = dirname($_SERVER['SCRIPT_NAME']);
$_base = ($_dir === '/' || $_dir === '\\') ? '' : rtrim(str_replace('\\', '/', $_dir), '/');
define('BASE_URL', $_prot . '://' . $_host . $_base);
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
define('SITE_SLOGAN',  'پشتیبانی کامپیوتر در ملارد و مارلیک');
define('SITE_EMAIL',   'info@mhsi.ir');
define('SITE_TEL',     '۰۹۱۲-۰۰۰-۰۰۰۰');   // شماره واقعی را بذار
define('SITE_ADRES',   'ملارد – مارلیک – پاساژ ارغوان شمالی');

// ====================================================
// نشست (Session)
// ====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
