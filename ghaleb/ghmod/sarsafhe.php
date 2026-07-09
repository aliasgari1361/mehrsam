<?php
// بارگذاری تنظیمات پنل مدیریت
require_once __DIR__ . '/../../haste/settings.php';
$admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: ['bg_color' => '#f0f2f5', 'font' => 'Tahoma'];
$panel_font_size       = (int)($admin_settings['font_size'] ?? 14);
$panel_menu_font_size  = (int)($admin_settings['menu_font_size'] ?? 13);
$panel_table_font_size = (int)($admin_settings['table_font_size'] ?? 14);
$panel_title_font_size = (int)($admin_settings['title_font_size'] ?? 18);
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
            font-size: <?php echo $panel_font_size; ?>px;
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
        .admin-content h3 { font-size: <?php echo $panel_title_font_size; ?>px; }
        .admin-content table, .dash-panel table { font-size: <?php echo $panel_table_font_size; ?>px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h2>پنل مدیریت</h2>
        <style>
            .admin-nav { display:flex; flex-wrap:wrap; gap:0; margin-top:8px; }
            .admin-nav .nav-item { position:relative; }
            .admin-nav .nav-item > a { display:block; padding:8px 14px; color:#ffc107; text-decoration:none; font-size:<?php echo $panel_menu_font_size; ?>px; border-radius:6px 6px 0 0; transition:all 0.2s; white-space:nowrap; }
            .admin-nav .nav-item > a:hover { background:rgba(255,255,255,0.1); }
            .admin-nav .nav-item > a.active { background:#fff; color:#343a40; }
            .admin-nav .submenu { display:none; position:absolute; top:100%; right:0; background:#fff; border-radius:0 8px 8px 8px; box-shadow:0 4px 20px rgba(0,0,0,0.15); min-width:200px; z-index:1000; padding:6px; }
            .admin-nav .nav-item:hover .submenu { display:block; }
            .admin-nav .submenu a { display:block; padding:10px 14px; color:#333; text-decoration:none; font-size:<?php echo $panel_menu_font_size; ?>px; border-radius:6px; transition:all 0.15s; }
            .admin-nav .submenu a:hover { background:#f8f9fa; color:var(--rang-asli,#FF6F00); }
            .admin-nav .submenu a i { width:20px; color:#999; margin-left:8px; }
            @media (max-width:768px) { .admin-nav { flex-direction:column; } .admin-nav .submenu { position:static; box-shadow:none; background:#444; } .admin-nav .submenu a { color:#ddd; } }
        </style>
        <div class="admin-nav">
            <div class="nav-item"><a href="<?= BASE_URL ?>mod/dashmod"><i class="fa-solid fa-gauge-high"></i> داشبورد</a></div>
            <div class="nav-item"><a><i class="fa-solid fa-file-lines"></i> محتوا ▾</a>
                <div class="submenu">
                    <a href="<?= BASE_URL ?>mod/content">📄 مقالات</a>
                    <a href="<?= BASE_URL ?>mod/pages">📋 برگه‌ها</a>
                </div>
            </div>
            <div class="nav-item"><a><i class="fa-solid fa-store"></i> فروشگاه ▾</a>
                <div class="submenu">
                    <a href="<?= BASE_URL ?>mod/store/products"><i class="fa-solid fa-cube"></i> محصولات</a>
                    <a href="<?= BASE_URL ?>mod/store/categories"><i class="fa-solid fa-folder"></i> دسته‌بندی‌ها</a>
                    <a href="<?= BASE_URL ?>mod/store/brands"><i class="fa-solid fa-tag"></i> برندها</a>
                    <a href="<?= BASE_URL ?>mod/store/orders"><i class="fa-solid fa-truck"></i> سفارشات</a>
                    <a href="<?= BASE_URL ?>mod/store/settings"><i class="fa-solid fa-gear"></i> تنظیمات فروشگاه</a>
                </div>
            </div>
            <div class="nav-item"><a><i class="fa-solid fa-palette"></i> قالب ▾</a>
                <div class="submenu">
                    <a href="<?= BASE_URL ?>mod/theme/sections"><i class="fa-solid fa-puzzle-piece"></i> بخش‌های محتوا</a>
                    <a href="<?= BASE_URL ?>mod/theme/files"><i class="fa-solid fa-file-code"></i> ویرایش فایل‌ها</a>
                    <a href="<?= BASE_URL ?>mod/theme/custom"><i class="fa-solid fa-paint-brush"></i> سفارشی‌سازی</a>
                    <a href="<?= BASE_URL ?>mod/settings?tab=theme"><i class="fa-solid fa-gear"></i> تنظیمات ظاهری</a>
                </div>
            </div>
            <div class="nav-item"><a><i class="fa-solid fa-layer-group"></i> صفحه‌ساز ▾</a>
                <div class="submenu">
                    <a href="<?= BASE_URL ?>mod/builder/pages"><i class="fa-solid fa-layer-group"></i> مدیریت صفحات</a>
                </div>
            </div>
            <div class="nav-item"><a href="<?= BASE_URL ?>mod/chat"><i class="fa-solid fa-comments"></i> چت</a></div>
            <div class="nav-item"><a href="<?= BASE_URL ?>mod/messages"><i class="fa-solid fa-envelope"></i> پیام‌ها</a></div>
            <div class="nav-item"><a><i class="fa-solid fa-gear"></i> تنظیمات ▾</a>
                <div class="submenu">
                    <a href="<?= BASE_URL ?>mod/settings"><i class="fa-solid fa-sliders"></i> تنظیمات سایت</a>
                    <a href="<?= BASE_URL ?>mod/panel_settings"><i class="fa-solid fa-palette"></i> تنظیمات پنل</a>
                    <a href="<?= BASE_URL ?>mod/settings?tab=git"><i class="fa-brands fa-github"></i> به‌روزرسانی</a>
                </div>
            </div>
            <div class="nav-item"><a href="<?= BASE_URL ?>mod/logout"><i class="fa-solid fa-sign-out-alt"></i> خروج</a></div>
        </div>
    </div>
    <div class="admin-content">