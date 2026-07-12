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

// مسیر آپلود لوگو/فاوآیکون
if (!defined('UPLOADS_DIR')) {
    define('UPLOADS_DIR', dirname(__DIR__) . '/ghaleb/manabe/uploads/');
}
if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', BASE_URL . '/ghaleb/manabe/uploads/');
}

// مسیر فایل‌های قابل دانلود (مدیریت فایل‌ها) — فقط این پوشه در درخت نمایش داده می‌شود
if (!defined('FILES_DIR')) {
    define('FILES_DIR', UPLOADS_DIR . 'files/');
}
if (!defined('FILES_URL')) {
    define('FILES_URL', UPLOADS_URL . 'files/');
}

// اگر فایل ذخیره وجود داشت، آن را بارگذاری کن
if (file_exists(SITE_SETTINGS_FILE)) {
    $json = file_get_contents(SITE_SETTINGS_FILE);
    $decoded = json_decode($json, true);
    $site_settings = is_array($decoded) ? $decoded : [];
} else {
    // تنظیمات پیش‌فرض کامل
    $site_settings = get_default_site_settings();
}

/**
 * دریافت تنظیمات پیش‌فرض
 */
function get_default_site_settings() {
    return [
        'general' => [
            'site_title'       => 'مهراد سام',
            'site_slogan'      => 'خدمات کامپیوتر مهراد سام در تهران',
            'site_email'       => 'ali.asgari.6106@gmail.com',
            'site_tel'         => '۰۹۱۰۵۹۲۱۳۵۸',
            'site_tel_en'      => '989105921358',
            'site_adres'       => 'تهران، ضلع شمال غرب تقاطع چمران و جلال آل احمد، گیشا، ابتدای بلوچستان، ساختمان گیشا پلاک ۸ واحد ۴',
            'site_hours'       => 'شنبه تا پنج‌شنبه: ۹ تا ۲۰',
            'favicon'          => '',
            'logo'             => '',
            'map_embed_url'    => 'https://maps.google.com/maps?q=35.7257,51.3814&z=15&output=embed',
        ],
        'social' => [
            'telegram'  => 'https://t.me/mehrsys61',
            'whatsapp'  => 'https://wa.me/989105921358',
            'bale'      => 'https://ble.ir/ali2761',
            'instagram' => '#',
        ],
        'theme' => [
            'active'            => 'mehrsam',
            'primary_color'     => '#FF6F00',
            'primary_hover'     => '#E65100',
            'secondary_color'   => '#00B894',
            'font_family'       => 'Vazirmatn',
            'body_font_size'    => '15',
            'body_text_color'   => '#1a1a1a',
            'custom_css'        => '',
        ],
        'store' => [
            'currency'              => 'تومان',
            'currency_symbol'       => 'تومان',
            'free_shipping_threshold' => 0,
            'default_shipping_cost' => 0,
            'auto_confirm_orders'   => false,
            'stock_management'      => true,
        ],
        'gateways' => [
            'zarinpal' => [
                'enabled'   => false,
                'title'     => 'زرین‌پال',
                'merchant'  => '',
                'sandbox'   => true,
            ],
            'idpay' => [
                'enabled'   => false,
                'title'     => 'آیدی پی',
                'api_key'   => '',
                'sandbox'   => true,
            ],
            'nextpay' => [
                'enabled'   => false,
                'title'     => 'نکست پی',
                'api_key'   => '',
                'sandbox'   => true,
            ],
        ],
        'files' => [
            'max_upload_size'     => 5242880,
            'allowed_extensions'  => 'pdf,zip,rar,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif,webp',
            'user_upload_enabled' => false,
        ],
    ];
}

/**
 * دریافت یک تنظیم (پشتیبانی از کلید تو در تو مثل theme.primary_color)
 */
function get_site_setting($key) {
    global $site_settings;
    $keys = explode('.', $key);
    $val = $site_settings;
    foreach ($keys as $k) {
        if (!is_array($val) || !array_key_exists($k, $val)) {
            // اگر تنظیم در فایل نبود، از پیش‌فرض برگردد (برای سازگاری با نصب‌های قبلی)
            $def = get_default_site_settings();
            $v = $def;
            foreach ($keys as $kk) {
                if (!is_array($v) || !array_key_exists($kk, $v)) return null;
                $v = $v[$kk];
            }
            return $v;
        }
        $val = $val[$k];
    }
    return $val;
}

/**
 * ذخیره‌ی تنظیمات جدید (merge عمیق)
 */
function save_site_settings($new_settings) {
    global $site_settings;
    if (!is_array($site_settings)) {
        $site_settings = [];
    }
    $site_settings = array_deep_merge($site_settings, $new_settings);
    file_put_contents(SITE_SETTINGS_FILE, json_encode($site_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    clearstatcache();
}

/**
 * merge عمیق آرایه‌ها
 */
function array_deep_merge(array $a1, array $a2): array {
    foreach ($a2 as $k => $v) {
        if (is_array($v) && isset($a1[$k]) && is_array($a1[$k])) {
            $a1[$k] = array_deep_merge($a1[$k], $v);
        } else {
            $a1[$k] = $v;
        }
    }
    return $a1;
}

/**
 * توابع کمکی گروه‌بندی
 */
function get_general_setting($key) { return get_site_setting("general.$key"); }
function get_social_setting($key)  { return get_site_setting("social.$key"); }
function get_theme_setting($key)   { return get_site_setting("theme.$key"); }
function get_store_setting($key)   { return get_site_setting("store.$key"); }
function get_gateway_setting($gateway, $key) { return get_site_setting("gateways.$gateway.$key"); }
function is_gateway_enabled($gateway) { return (bool)get_site_setting("gateways.$gateway.enabled"); }
function get_enabled_gateways() {
    $gates = get_site_setting('gateways') ?? [];
    return array_filter($gates, fn($g) => !empty($g['enabled']));
}

/**
 * آپلود فایل عکس (لوگو/فاوآیکون)
 * برگشت: مسیر URL فایل یا false در صورت خطا
 */
function upload_site_image($file_input_name, $subdir = '') {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    $file = $_FILES[$file_input_name];
    $allowed = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        return ['error' => 'فرمت فایل مجاز نیست (png, jpg, gif, svg, webp)'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['error' => 'حجم فایل نباید بیشتر از ۲ مگابایت باشد'];
    }

    $target_dir = UPLOADS_DIR . $subdir;
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'png';
    $filename = uniqid('site_') . '.' . $ext;
    $target_path = $target_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['error' => 'خطا در انتقال فایل'];
    }

    return UPLOADS_URL . $subdir . $filename;
}

/**
 * تابع کمکی: تیره/روشن کردن رنگ هگز
 */
function shade_color($hex, $percent) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = max(0, min(255, $r + $percent * 2.55));
    $g = max(0, min(255, $g + $percent * 2.55));
    $b = max(0, min(255, $b + $percent * 2.55));

    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}

/**
 * تبدیل تاریخ میلادی به شمسی
 * @param string|int $datetime رشته تاریخ یا timestamp
 * @param string $format فرمت خروجی (Y/m/d H:i:s)
 * @return string
 */
function g2j($gy, $gm, $gd) {
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    $jy = ($gy <= 1600) ? 0 : 979;
    $gy -= ($gy <= 1600) ? 621 : 1600;
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 365 * $gy + (int)(($gy2 + 3) / 4) - (int)(($gy2 + 99) / 100) + (int)(($gy2 + 399) / 400) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * ((int)($days / 12053));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    $jm = ($days < 186) ? 1 + (int)($days / 31) : 7 + (int)(($days - 186) / 30);
    $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
    return [$jy, $jm, $jd];
}

/**
 * فرمت‌بندی تاریخ میلادی به شمسی
 */
function to_jalali($datetime, $format = 'Y/m/d') {
    if (empty($datetime)) return '';
    $ts = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
    if ($ts === false || $ts === 0) return '';
    list($jy, $jm, $jd) = g2j((int)date('Y', $ts), (int)date('n', $ts), (int)date('j', $ts));
    $out = str_replace('Y', $jy, $format);
    $out = str_replace('m', str_pad($jm, 2, '0', STR_PAD_LEFT), $out);
    $out = str_replace('d', str_pad($jd, 2, '0', STR_PAD_LEFT), $out);
    $out = str_replace('H', date('H', $ts), $out);
    $out = str_replace('i', date('i', $ts), $out);
    $out = str_replace('s', date('s', $ts), $out);
    return $out;
}