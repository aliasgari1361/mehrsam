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
// تنظیمات سایت (از فایل site_settings.json بارگذاری می‌شود)
// ====================================================
$_settings_file = __DIR__ . '/site_settings.json';
$_dynamic_settings = file_exists($_settings_file) ? json_decode(file_get_contents($_settings_file), true) : [];
$_general = $_dynamic_settings['general'] ?? [];
$_social = $_dynamic_settings['social'] ?? [];

define('SITE_NAME',    $_general['site_title']       ?? 'مهراد سام');
define('SITE_SLOGAN',  $_general['site_slogan']      ?? 'خدمات کامپیوتر مهراد سام در تهران');
define('SITE_EMAIL',   $_general['site_email']       ?? 'ali.asgari.6106@gmail.com');
define('SITE_TEL',     $_general['site_tel']         ?? '۰۹۱۰۵۹۲۱۳۵۸');
define('SITE_ADRES',   $_general['site_adres']       ?? 'تهران، ضلع شمال غرب تقاطع چمران و جلال آل احمد، گیشا، ابتدای بلوچستان، ساختمان گیشا پلاک ۸ واحد ۴');
define('SITE_HOURS',   $_general['site_hours']       ?? 'شنبه تا پنج‌شنبه: ۹ تا ۲۰');

define('SITE_TEL_EN',    $_general['site_tel_en']    ?? '989105921358');
define('SITE_TELEGRAM',  $_social['telegram']        ?? 'https://t.me/mehrsys61');
define('SITE_WHATSAPP',  $_social['whatsapp']        ?? 'https://wa.me/989105921358');
define('SITE_BALE',      $_social['bale']            ?? 'https://ble.ir/ali2761');
define('SITE_INSTAGRAM', $_social['instagram']       ?? '#');
unset($_settings_file, $_dynamic_settings, $_general, $_social);

// ====================================================
// درگاه پرداخت زرین‌پال (از فایل site_settings.json بارگذاری می‌شود)
// ====================================================
$_gw_file = __DIR__ . '/site_settings.json';
$_gw_settings = file_exists($_gw_file) ? json_decode(file_get_contents($_gw_file), true) : [];
$_zarin = $_gw_settings['gateways']['zarinpal'] ?? [];
define('ZARINPAL_MERCHANT', $_zarin['merchant'] ?? '');
define('ZARINPAL_SANDBOX',  $_zarin['sandbox'] ?? true);
unset($_gw_file, $_gw_settings, $_zarin);

// ====================================================
// نشست (Session)
// ====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
