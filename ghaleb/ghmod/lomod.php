<?php
// عنوان سایت را از تنظیمات عمومی می‌خوانیم
require_once __DIR__ . '/../../haste/tanzimat.php';
$siteTitle = get_site_setting('site_title') ?? 'پنل مدیریت';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate simple text-based captcha code for display
$captcha_code = '';
for ($i = 0; $i < 6; $i++) { $captcha_code .= rand(0, 9); }
$_SESSION['captcha_code'] = $captcha_code;
$_SESSION['captcha_time'] = time();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود به <?php echo htmlspecialchars($siteTitle); ?></title>
    <?php
// لود تنظیمات سایت برای فاوآیکون
$favicon_path = BASE_URL . 'ghaleb/manabe/favicon.png';
$settings_file = dirname(__DIR__, 2) . '/haste/site_settings.json';
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
    if (!empty($settings['general']['favicon'])) {
        $favicon_path = $settings['general']['favicon'];
    }
}
?>
<link rel="icon" href="<?= htmlspecialchars($favicon_path) ?>" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Tahoma, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }
        .login-box h2 { margin-bottom: 20px; color: #333; }
        .login-box input[type="text"],
        .login-box input[type="password"],
        .login-box input[type="text"][name="captcha"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }
        .captcha-container { display: flex; gap: 10px; align-items: center; }
        .captcha-container img { border-radius: 4px; cursor: pointer; }
        .captcha-container button {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }
        .login-box button[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #343a40;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-box button[type="submit"]:hover { background: #495057; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>ورود مدیر</h2>
        <?php if (isset($error) && $error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo BASE_URL; ?>mod/lomod">
            <input type="text" name="username" placeholder="نام کاربری" required>
            <input type="password" name="password" placeholder="رمز عبور" required>
            <label style="display:flex;align-items:center;gap:6px;margin:8px 0;cursor:pointer;font-size:13px;color:#555;">
                <input type="checkbox" name="remember_me" value="1" style="width:16px;height:16px;cursor:pointer;">
                ذخیره پسوورد
            </label>
            <div class="captcha-container">
                <div style="flex: 1; background: #fafafa; border: 1px solid #ddd; border-radius: 4px; height: 40px; display: flex; align-items: center; justify-content: center; font-family: monospace; font-size: 18px; letter-spacing: 3px; user-select: none;">
                    <?php echo $captcha_code; ?>
                </div>
                <button type="button" onclick="refreshCaptcha()" title="تغییر کپچا" style="background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; padding: 0 10px; cursor: pointer;">
                    <i class="fa-solid fa-refresh"></i>
                </button>
            </div>
            <input type="text" name="captcha" placeholder="کد امنیتی" required maxlength="6" autocomplete="off">
            <button type="submit">ورود</button>
        </form>
    </div>
    <script>
        function refreshCaptcha() {
            window.location.reload();
        }
    </script>
</body>
</html>