<?php
/**
 * نصاب مهراد سام
 * ============================================
 * برای نصب روی هاست جدید یا انتقال از هاست دیگر
 * 
 * نحوه استفاده:
 * 1. فایل‌ها را روی هاست آپلود کنید
 * 2. به install.php در مرورگر بروید
 * 3. مراحل نصب را طی کنید
 * 
 * برای انتقال از هاست دیگر:
 * 1. از هاست قبلی بکاپ بگیرید (mod/backup)
 * 2. بکاپ را از حالت زیپ خارج کنید و فایل‌ها را روی هاست جدید آپلود کنید
 * 3. install.php را اجرا کنید — گزینه "بازگردانی بکاپ" را انتخاب کنید
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
                $migrations_dir = __DIR__ . '/database/migrations';
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
                file_put_contents(__DIR__ . '/haste/admin_settings.json', json_encode($panel_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                // نوشتن tanzimat.php
                $tanzimat = <<<PHP
<?php
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');

\$_prot = (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
\$_host = \$_SERVER['HTTP_HOST'] ?? 'localhost';
\$_dir  = dirname(\$_SERVER['SCRIPT_NAME'] ?? '/');
\$_base = (\$_dir === '/' || \$_dir === '\\\\') ? '' : rtrim(str_replace('\\\\', '/', \$_dir), '/');
define('BASE_URL', \$_prot . '://' . \$_host . \$_base . '/');
unset(\$_prot, \$_host, \$_dir, \$_base);

define('MASIR_RISH',   __DIR__ . DIRECTORY_SEPARATOR);
define('GHALEB_FAAAL', 'mehrsam');
define('MASIR_GHALEB', MASIR_RISH . 'ghaleb/' . GHALEB_FAAAL . DIRECTORY_SEPARATOR);
define('URL_GHALEB',   BASE_URL . 'ghaleb/' . GHALEB_FAAAL);
define('MASIR_DADE',   MASIR_RISH . 'dade' . DIRECTORY_SEPARATOR);

\$_settings_file = __DIR__ . '/site_settings.json';
\$_dynamic_settings = file_exists(\$_settings_file) ? json_decode(file_get_contents(\$_settings_file), true) : [];
\$_general = \$_dynamic_settings['general'] ?? [];
\$_social = \$_dynamic_settings['social'] ?? [];

define('SITE_NAME',    \$_general['site_title']       ?? '$site_title');
define('SITE_SLOGAN',  \$_general['site_slogan']      ?? '');
define('SITE_EMAIL',   \$_general['site_email']       ?? '$site_email');
define('SITE_TEL',     \$_general['site_tel']         ?? '');
define('SITE_ADRES',   \$_general['site_adres']       ?? '');

define('SITE_TEL_EN',    \$_general['site_tel_en']    ?? '');
define('SITE_TELEGRAM',  \$_social['telegram']        ?? '');
define('SITE_WHATSAPP',  \$_social['whatsapp']        ?? '');
define('SITE_BALE',      \$_social['bale']            ?? '');
define('SITE_INSTAGRAM', \$_social['instagram']       ?? '#');
unset(\$_settings_file, \$_dynamic_settings, \$_general, \$_social);

\$_gw_file = __DIR__ . '/site_settings.json';
\$_gw_settings = file_exists(\$_gw_file) ? json_decode(file_get_contents(\$_gw_file), true) : [];
\$_zarin = \$_gw_settings['gateways']['zarinpal'] ?? [];
define('ZARINPAL_MERCHANT', \$_zarin['merchant'] ?? '');
define('ZARINPAL_SANDBOX',  \$_zarin['sandbox'] ?? true);
unset(\$_gw_file, \$_gw_settings, \$_zarin);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
PHP;
                file_put_contents(__DIR__ . '/haste/tanzimat.php', $tanzimat);

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
                    ⚠️ برای امنیت بیشتر، فایل <code>install.php</code> را حذف کنید.
                </p>
                <a href="<?= rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/') ?>/mod/lomod">🔐 ورود به پنل مدیریت</a>
            </div>

        <?php else: ?>
            <div class="error">خطای ناشناخته. لطفاً دوباره تلاش کنید.</div>
        <?php endif; ?>
    </div>
</body>
</html>
