<?php
// بارگذاری تنظیمات پنل مدیریت
require_once __DIR__ . '/../../haste/settings.php';
$admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: ['bg_color' => '#f0f2f5', 'font' => 'Tahoma'];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $onvan_safhe ?? 'پنل مدیریت'; ?></title>
    <link rel="icon" href="<?php echo BASE_URL . 'ghaleb/manabe/favicon.png'; ?>" type="image/png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>ghaleb/manabe/fonts.css">
    <style>
        body {
            font-family: <?php echo $admin_settings['font'] ?? 'Tahoma'; ?>, sans-serif;
            background-color: <?php echo $admin_settings['bg_color'] ?? '#f0f2f5'; ?>;
            margin: 0;
            padding: 20px;
        }
        .admin-header {
            background: #343a40;
            color: #fff;
            padding: 15px;
            margin-bottom: 20px;
        }
        .admin-header a { color: #ffc107; text-decoration: none; }
        .admin-content { background: #fff; padding: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h2>پنل مدیریت</h2>
        <p>
            <a href="<?php echo BASE_URL; ?>mod/dashmod">داشبورد</a> |
            <a href="<?php echo BASE_URL; ?>mod/content">محتوا</a> |
            <a href="<?php echo BASE_URL; ?>mod/pages">برگه‌ها</a> |
            <a href="<?php echo BASE_URL; ?>mod/services">خدمات</a> |
            <a href="<?php echo BASE_URL; ?>mod/chat">چت</a> |
            <a href="<?php echo BASE_URL; ?>mod/theme">مدیریت قالب</a> |
            <a href="<?php echo BASE_URL; ?>mod/store">مدیریت فروشگاه</a> |
            <a href="<?php echo BASE_URL; ?>mod/panel_settings">تنظیمات پنل</a> |
            <a href="<?php echo BASE_URL; ?>mod/logout">خروج</a>
        </p>
    </div>
    <div class="admin-content">