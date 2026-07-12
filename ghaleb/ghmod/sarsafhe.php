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
        <?php
        require_once MASIR_RISH . 'mohtava/menu/menu-editor.php';
        $admin_menu_items = menu_get_admin_items();
        menu_render_admin($admin_menu_items);
        ?>
    </div>
    <div class="admin-content">