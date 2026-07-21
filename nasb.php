<?php
/**
 * نصاب مهراد سام
 * ============================================
 * برای نصب روی هاست جدید یا انتقال از هاست دیگر
 * 
 * نحوه استفاده:
 * 1. فایل‌ها را روی هاست آپلود کنید
 * 2. به nasb.php در مرورگر بروید
 * 3. مراحل نصب را طی کنید
 * 
 * برای انتقال از هاست دیگر:
 * 1. از هاست قبلی بکاپ بگیرید (mod/poshtyban)
 * 2. بکاپ را از حالت زیپ خارج کنید و فایل‌ها را روی هاست جدید آپلود کنید
 * 3. nasb.php را اجرا کنید — گزینه "بازگردانی بکاپ" را انتخاب کنید
 */

// جلوگیری از کش شدن
header('Cache-Control: no-store, no-cache, must-revalidate');

// تشخیص نصب بودن
$installed = file_exists(__DIR__ . '/haste/tanzimat.php') && defined('DB_NAME');

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

$errors = [];
$success = false;

// پردازش فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = (int)($_POST['step'] ?? 1);

    if ($step === 2) {
        // مرحله ۲: اطلاعات دیتابیس
        $db_host = trim($_POST['db_host'] ?? '127.0.0.1');
        $db_user = trim($_POST['db_user'] ?? '');
        $db_pass = $_POST['db_pass'] ?? '';
        $db_name = trim($_POST['db_name'] ?? '');

        if (empty($db_user) || empty($db_name)) {
            $errors[] = 'نام کاربری و نام دیتابیس الزامی است.';
        } else {
            // تست اتصال
            $conn = @new mysqli($db_host, $db_user, $db_pass);
            if ($conn->connect_error) {
                $errors[] = 'خطا در اتصال به دیتابیس: ' . $conn->connect_error;
            } else {
                $conn->set_charset("utf8mb4");
                // ایجاد دیتابیس اگر وجود نداشته باشد
                $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $conn->select_db($db_name);

                $_SESSION['install_db_host'] = $db_host;
                $_SESSION['install_db_user'] = $db_user;
                $_SESSION['install_db_pass'] = $db_pass;
                $_SESSION['install_db_name'] = $db_name;
                $conn->close();
                $step = 3;
            }
        }
    } elseif ($step === 3) {
        // مرحله ۳: اطلاعات سایت
        $site_title = trim($_POST['site_title'] ?? 'مهراد سام');
        $site_email = trim($_POST['site_email'] ?? '');
        $admin_user = trim($_POST['admin_user'] ?? 'admin');
        $admin_pass = $_POST['admin_pass'] ?? '';
        $admin_pass2 = $_POST['admin_pass2'] ?? '';

        if (empty($site_email)) $errors[] = 'ایمیل سایت الزامی است.';
        if (empty($admin_pass)) $errors[] = 'رمز عبور ادمین الزامی است.';
        if ($admin_pass !== $admin_pass2) $errors[] = 'تکرار رمز عبور مطابقت ندارد.';
        if (strlen($admin_pass) < 6) $errors[] = 'رمز عبور باید حداقل ۶ کاراکتر باشد.';

        if (empty($errors)) {
            $db_host = $_SESSION['install_db_host'] ?? '127.0.0.1';
            $db_user = $_SESSION['install_db_user'] ?? 'root';
            $db_pass = $_SESSION['install_db_pass'] ?? '';
            $db_name = $_SESSION['install_db_name'] ?? '';

            $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
            if ($conn->connect_error) {
                $errors[] = 'خطا در اتصال به دیتابیس: ' . $conn->connect_error;
            } else {
                $conn->set_charset("utf8mb4");

                // اجرای مایگریشن‌ها
                $migrations_dir = __DIR__ . '/payegah_dade/migrations';
                $migrations = glob($migrations_dir . '/*.sql');
                sort($migrations);
                foreach ($migrations as $mig) {
                    $sql = file_get_contents($mig);
                    $statements = explode(";\n", $sql);
                    foreach ($statements as $stmt) {
                        $stmt = trim($stmt);
                        if ($stmt !== '') {
                            $conn->query($stmt);
                        }
                    }
                }

                // ایجاد کاربر ادمین
                $hashed = password_hash($admin_pass, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT IGNORE INTO users (username, password, role, email) VALUES (?, ?, 'admin', ?)");
                $stmt->bind_param("sss", $admin_user, $hashed, $site_email);
                $stmt->execute();
                $stmt->close();

                // بروزرسانی ایمیل در users
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE role = 'admin'");
                $stmt->bind_param("s", $site_email);
                $stmt->execute();
                $stmt->close();

                $conn->close();

                // نوشتن فایل تنظیمات سایت
                $settings = [
                    'general' => [
                        'site_title' => $site_title,
                        'site_email' => $site_email,
                    ],
                    'social' => [],
                    'theme' => ['active' => 'mehrsam'],
                    'store' => ['currency' => 'تومان', 'currency_symbol' => 'تومان'],
                ];
                file_put_contents(__DIR__ . '/haste/site_settings.json', json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                // نوشتن فایل تنظیمات پنل
                $panel_settings = [
                    'bg_color' => '#f0f2f5',
                    'font' => 'Tahoma',
                    'favicon' => '',
                    'font_size' => 14,
                    'menu_font_size' => 13,
                    'table_font_size' => 14,
                    'title_font_size' => 18,
                ];
                file_put_contents(__DIR__ . '/haste/modir_tanzimat.json', json_encode($panel_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                // نوشتن tanzimat.php (نسخه ادغام‌شده: هسته + تنظیمات سایت + توابع کمکی)
                $core = <<<PHP
<?php
/**
 * تنظیمات پایه و عمومی سایت مهراد سام
 * شامل: تنظیمات پایگاه داده، مسیرها، تنظیمات عمومی سایت و تنظیمات نمایشی پنل
 */

// تنظیمات پایگاه داده
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');

// آدرس پایه
\$_prot = (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
\$_host = \$_SERVER['HTTP_HOST'] ?? 'localhost';
\$_dir  = dirname(\$_SERVER['SCRIPT_NAME'] ?? '/');
\$_base = (\$_dir === '/' || \$_dir === '\\\\') ? '' : rtrim(str_replace('\\\\', '/', \$_dir), '/');
define('BASE_URL', \$_prot . '://' . \$_host . \$_base . '/');
unset(\$_prot, \$_host, \$_dir, \$_base);

// مسیرهای سیستمی
define('MASIR_RISH',   __DIR__ . DIRECTORY_SEPARATOR);

// خواندن قالب فعال از site_settings.json
$_settings_file = MASIR_RISH . 'haste/site_settings.json';
$_active_theme = 'mehrsam';
if (file_exists($_settings_file)) {
    $_sf = json_decode(file_get_contents($_settings_file), true);
    if (!empty($_sf['theme']['active'])) $_active_theme = $_sf['theme']['active'];
}
if (!is_dir(MASIR_RISH . 'ghaleb/' . $_active_theme)) $_active_theme = 'mehrsam';

define('GHALEB_FAAAL', $_active_theme);
define('MASIR_GHALEB', MASIR_RISH . 'ghaleb/' . GHALEB_FAAAL . DIRECTORY_SEPARATOR);
define('URL_GHALEB',   BASE_URL . 'ghaleb/' . GHALEB_FAAAL);
define('MASIR_DADE',   MASIR_RISH . 'dade' . DIRECTORY_SEPARATOR);

// تنظیمات عمومی سایت (از site_settings.json)
if (!defined('SITE_SETTINGS_FILE')) {
    define('SITE_SETTINGS_FILE', __DIR__ . '/site_settings.json');
}
if (!defined('UPLOADS_DIR')) {
    define('UPLOADS_DIR', MASIR_RISH . 'ghaleb/manabe/uploads/');
}
if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', BASE_URL . 'ghaleb/manabe/uploads/');
}
if (!defined('FILES_DIR')) {
    define('FILES_DIR', UPLOADS_DIR . 'files/');
}
if (!defined('FILES_URL')) {
    define('FILES_URL', UPLOADS_URL . 'files/');
}

if (file_exists(SITE_SETTINGS_FILE)) {
    \$json = file_get_contents(SITE_SETTINGS_FILE);
    \$decoded = json_decode(\$json, true);
    \$site_settings = is_array(\$decoded) ? \$decoded : [];
} else {
    \$site_settings = get_default_site_settings();
}

\$_general = \$site_settings['general'] ?? [];
\$_social  = \$site_settings['social']  ?? [];
\$_gw      = \$site_settings['gateways'] ?? [];
\$_zarin   = \$_gw['zarinpal'] ?? [];

define('SITE_NAME',    \$_general['site_title']       ?? '$site_title');
define('SITE_SLOGAN',  \$_general['site_slogan']      ?? '');
define('SITE_EMAIL',   \$_general['site_email']       ?? '$site_email');
define('SITE_TEL',     \$_general['site_tel']         ?? '');
define('SITE_ADRES',   \$_general['site_adres']       ?? '');
define('SITE_HOURS',   \$_general['site_hours']       ?? '');

define('SITE_TEL_EN',    \$_general['site_tel_en']    ?? '');
define('SITE_TELEGRAM',  \$_social['telegram']        ?? '');
define('SITE_WHATSAPP',  \$_social['whatsapp']        ?? '');
define('SITE_BALE',      \$_social['bale']            ?? '');
define('SITE_INSTAGRAM', \$_social['instagram']       ?? '#');
define('ZARINPAL_MERCHANT', \$_zarin['merchant'] ?? '');
define('ZARINPAL_SANDBOX',  \$_zarin['sandbox'] ?? true);
unset(\$_general, \$_social, \$_gw, \$_zarin);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تنظیمات نمایشی پنل مدیریت
if (!defined('ADMIN_SETTINGS_FILE')) {
    define('ADMIN_SETTINGS_FILE', __DIR__ . '/modir_tanzimat.json');
}
if (file_exists(ADMIN_SETTINGS_FILE)) {
    \$admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true);
    if (!is_array(\$admin_settings)) {
        \$admin_settings = [];
    }
} else {
    \$admin_settings = [ 'bg_color' => '#f0f2f5', 'font' => 'Tahoma', 'favicon' => '' ];
}
PHP;

                // بخش استاتیک توابع کمکی (بدون درون‌ریزی متغیر - nowdoc)
                $helpers = <<<'PHP'

// توابع کمکی عمومی
function get_default_site_settings() {
    return [
        'general' => [
            'site_title' => '$site_title',
            'site_email' => '$site_email',
        ],
        'social' => [],
        'theme' => ['active' => 'mehrsam'],
        'store' => ['currency' => 'تومان', 'currency_symbol' => 'تومان'],
        'gateways' => [],
        'files' => [],
    ];
}
function get_site_setting($key) {
    global $site_settings;
    $keys = explode('.', $key);
    $val = $site_settings;
    foreach ($keys as $k) {
        if (!is_array($val) || !array_key_exists($k, $val)) {
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
function save_site_settings($new_settings) {
    global $site_settings;
    if (!is_array($site_settings)) { $site_settings = []; }
    $site_settings = array_deep_merge($site_settings, $new_settings);
    file_put_contents(SITE_SETTINGS_FILE, json_encode($site_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    clearstatcache();
}
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
function upload_site_image($file_input_name, $subdir = '') {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) return false;
    $file = $_FILES[$file_input_name];
    $allowed = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return ['error' => 'فرمت فایل مجاز نیست'];
    if ($file['size'] > 2 * 1024 * 1024) return ['error' => 'حجم فایل نباید بیشتر از ۲ مگابایت باشد'];
    $target_dir = UPLOADS_DIR . $subdir;
    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'png';
    $filename = uniqid('site_') . '.' . $ext;
    $target_path = $target_dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target_path)) return ['error' => 'خطا در انتقال فایل'];
    return UPLOADS_URL . $subdir . $filename;
}
function shade_color($hex, $percent) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $r = max(0, min(255, $r + $percent * 2.55));
    $g = max(0, min(255, $g + $percent * 2.55));
    $b = max(0, min(255, $b + $percent * 2.55));
    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}
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
    if ($days > 365) { $jy += (int)(($days - 1) / 365); $days = ($days - 1) % 365; }
    $jm = ($days < 186) ? 1 + (int)($days / 31) : 7 + (int)(($days - 186) / 30);
    $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
    return [$jy, $jm, $jd];
}
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
function get_admin_settings() {
    global $admin_settings;
    if (!is_array($admin_settings)) $admin_settings = [];
    return $admin_settings;
}
function save_admin_settings($new_settings) {
    global $admin_settings;
    if (!is_array($admin_settings)) $admin_settings = [];
    $admin_settings = array_merge($admin_settings, $new_settings);
    file_put_contents(ADMIN_SETTINGS_FILE, json_encode($admin_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    clearstatcache();
}
PHP;

                file_put_contents(__DIR__ . '/haste/tanzimat.php', $core . $helpers);

                $success = true;
                $step = 4;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نصب مهراد سام</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Tahoma,sans-serif; background:#f0f2f5; display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }
        .box { background:#fff; padding:30px 35px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); width:100%; max-width:500px; }
        .box h2 { margin-bottom:20px; color:#333; text-align:center; }
        .box label { display:block; margin-top:12px; margin-bottom:4px; font-size:13px; color:#555; font-weight:600; }
        .box input[type=text], .box input[type=email], .box input[type=password] { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px; }
        .box button { width:100%; padding:11px; background:#343a40; color:#fff; border:none; border-radius:4px; font-size:15px; cursor:pointer; margin-top:18px; }
        .box button:hover { background:#495057; }
        .error { background:#fff0f0; border:1px solid #e0b4b4; color:#9f3a38; padding:10px 14px; border-radius:4px; margin-bottom:12px; font-size:13px; }
        .error li { margin-right:16px; margin-bottom:4px; }
        .success { background:#f0fff4; border:1px solid #b4e0b4; color:#2e7d32; padding:20px; border-radius:4px; text-align:center; margin-bottom:12px; }
        .success a { display:inline-block; margin-top:12px; padding:10px 24px; background:#343a40; color:#fff; border-radius:4px; text-decoration:none; }
        .step-indicator { display:flex; justify-content:center; gap:8px; margin-bottom:24px; }
        .step-dot { width:30px; height:30px; border-radius:50%; background:#e0e0e0; display:flex; align-items:center; justify-content:center; font-size:13px; color:#999; }
        .step-dot.active { background:#343a40; color:#fff; }
        .step-dot.done { background:#2e7d32; color:#fff; }
        .info { font-size:13px; color:#666; margin-bottom:16px; text-align:center; }
    </style>
</head>
<body>
    <div class="box">
        <h2>🚀 نصب مهراد سام</h2>

        <div class="step-indicator">
            <div class="step-dot <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">1</div>
            <div class="step-dot <?= $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' ?>">2</div>
            <div class="step-dot <?= $step >= 3 ? ($step > 3 ? 'done' : 'active') : '' ?>">3</div>
            <div class="step-dot <?= $step >= 4 ? 'active' : '' ?>">4</div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error"><ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <div class="info">خوش آمدید! این ابزار شما را در نصب سایت روی هاست جدید راهنمایی می‌کند.</div>
            <form method="post">
                <input type="hidden" name="step" value="2">
                <button type="submit">شروع نصب</button>
            </form>
            <p style="text-align:center;margin-top:16px;font-size:12px;color:#999;">
                برای انتقال از هاست دیگر، ابتدا بکاپ را <strong>از حالت زیپ خارج کنید</strong>،
                فایل‌ها را روی هاست جدید آپلود کنید، سپس نصب را ادامه دهید.
            </p>

        <?php elseif ($step === 2): ?>
            <div class="info">اطلاعات دیتابیس را وارد کنید. دیتابیس باید از قبل ایجاد شده باشد یا مجوز ایجاد داشته باشد.</div>
            <form method="post">
                <input type="hidden" name="step" value="2">
                <label>هاست دیتابیس</label>
                <input type="text" name="db_host" value="127.0.0.1" dir="ltr">
                <label>نام کاربری دیتابیس</label>
                <input type="text" name="db_user" value="" dir="ltr">
                <label>رمز عبور دیتابیس</label>
                <input type="password" name="db_pass" dir="ltr">
                <label>نام دیتابیس</label>
                <input type="text" name="db_name" value="" dir="ltr">
                <button type="submit">مرحله بعد</button>
            </form>

        <?php elseif ($step === 3): ?>
            <div class="info">اطلاعات سایت و کاربر مدیریت را وارد کنید.</div>
            <form method="post">
                <input type="hidden" name="step" value="3">
                <label>عنوان سایت</label>
                <input type="text" name="site_title" value="مهراد سام">
                <label>ایمیل سایت</label>
                <input type="email" name="site_email" value="" dir="ltr">
                <label>نام کاربری مدیر</label>
                <input type="text" name="admin_user" value="admin" dir="ltr">
                <label>رمز عبور مدیر</label>
                <input type="password" name="admin_pass" dir="ltr">
                <label>تکرار رمز عبور</label>
                <input type="password" name="admin_pass2" dir="ltr">
                <button type="submit">نصب</button>
            </form>

        <?php elseif ($step === 4 && $success): ?>
            <div class="success">
                <p style="font-size:18px;font-weight:700;">✅ نصب با موفقیت انجام شد</p>
                <p style="margin-top:8px;font-size:14px;">نام کاربری: <strong><?= htmlspecialchars($admin_user ?? 'admin') ?></strong></p>
                <p style="font-size:12px;color:#666;margin-top:4px;">(رمز عبوری که وارد کردید)</p>
                <p style="margin-top:16px;font-size:13px;color:#c62828;font-weight:600;">
                    ⚠️ برای امنیت بیشتر، فایل <code>nasb.php</code> را حذف کنید.
                </p>
                <a href="<?= rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/') ?>/mod/lomod">🔐 ورود به پنل مدیریت</a>
            </div>

        <?php else: ?>
            <div class="error">خطای ناشناخته. لطفاً دوباره تلاش کنید.</div>
        <?php endif; ?>
    </div>
</body>
</html>
