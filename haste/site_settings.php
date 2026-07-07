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
        if (!is_array($val) || !array_key_exists($k, $val)) return null;
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