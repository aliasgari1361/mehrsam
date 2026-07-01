<?php
// عنوان سایت را از تنظیمات عمومی می‌خوانیم
$siteTitle = get_site_setting('site_title') ?? 'پنل مدیریت';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود به <?php echo htmlspecialchars($siteTitle); ?></title>
    <link rel="icon" href="<?php echo BASE_URL . get_site_setting('favicon'); ?>" type="image/png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Tahoma, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .login-box h2 { margin-bottom: 20px; color: #333; }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .login-box button {
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
        .login-box button:hover { background: #495057; }
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
            <button type="submit">ورود</button>
        </form>
    </div>
</body>
</html>