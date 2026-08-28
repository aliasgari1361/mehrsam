<?php
// بارگذاری تنظیمات پنل مدیریت
require_once __DIR__ . '/../../haste/tanzimat.php';
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>ghaleb/manabe/fonts/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>ghaleb/manabe/fontawesome/all.min.css">
    <style>
        body {
            font-family: <?php echo $admin_settings['font'] ?? 'Tahoma'; ?>, sans-serif;
            font-size: <?php echo $panel_font_size; ?>px;
            background-color: <?php echo $admin_settings['bg_color'] ?? '#f0f2f5'; ?>;
            margin: 0;
            padding: 20px 20px 0 20px;
        }
        .admin-header {
            position:relative; overflow:visible; z-index:50;
            background:linear-gradient(135deg, #3A4A5A 0%, #2C3A47 60%, #FF6F00 240%);
            color: #fff;
            padding: 12px 22px;
            margin-bottom: 22px;
            border-radius: 16px;
            box-shadow:0 10px 30px rgba(0,0,0,0.18);
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0px;
        }
        .admin-header::after {
            content:''; position:absolute; right:-50px; top:-70px; width:260px; height:260px;
            background:radial-gradient(circle, rgba(255,111,0,0.22) 0%, transparent 70%); pointer-events:none;
            border-radius:16px; overflow:hidden;
        }
        .admin-header a { color: #ffc107; text-decoration: none; }
        .admin-brand { display:flex; align-items:center; gap:12px; z-index:2; width:100%; margin-bottom:4px; }
        .admin-brand .logo {
            width:40px; height:40px; border-radius:11px; background:rgba(255,111,0,0.92);
            display:flex; align-items:center; justify-content:center; font-size:19px; color:#fff;
            box-shadow:0 4px 14px rgba(255,111,0,0.4); flex-shrink:0;
        }
        .admin-brand h2 { margin:0; font-size:19px; font-weight:800; letter-spacing:-0.3px; font-family:'Vazir', sans-serif; }
        .admin-brand .sub { font-size:11px; color:rgba(255,255,255,0.65); margin-top:1px; }
        .admin-header .ah-right { display:flex; align-items:center; gap:18px; z-index:2; flex-wrap:wrap; width:100%; }
        .admin-datebox {
            text-align:right; direction:rtl; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12);
            border-radius:10px; padding:5px 14px; line-height:1.45; backdrop-filter:blur(4px);
        }
        .admin-datebox .jal { font-size:13px; font-weight:700; color:#fff; }
        .admin-datebox .greg { font-size:11px; color:rgba(255,255,255,0.65); margin-top:1px; }
        .admin-datebox i { color:var(--rang-asli,#FF6F00); margin-left:6px; }
        .admin-content { background: #fff; padding: 20px; border-radius: 12px; }
        .admin-content h3 { font-size: <?php echo $panel_title_font_size; ?>px; }
        .admin-content table, .dash-panel table { font-size: <?php echo $panel_table_font_size; ?>px; }
        .admin-nav { display:flex; flex-wrap:wrap; gap:4px; margin-top:12px; width:100%; z-index:2; }
        .admin-nav .nav-item { position:relative; }
        .admin-nav .nav-item > a { display:block; padding:9px 15px; color:rgba(255,255,255,0.82); text-decoration:none; font-size:<?php echo $panel_menu_font_size; ?>px; border-radius:9px 9px 0 0; transition:all 0.2s; white-space:nowrap; font-weight:600; }
        .admin-nav .nav-item > a:hover { background:rgba(255,255,255,0.1); color:#fff; }
        .admin-nav .nav-item > a.active { background:#fff; color:#2D3436; box-shadow:0 4px 12px rgba(0,0,0,0.15); }
        .admin-nav .submenu { display:none; position:absolute; top:100%; right:0; background:#fff; border-radius:0 10px 10px 10px; box-shadow:0 6px 24px rgba(0,0,0,0.18); min-width:210px; z-index:1000; padding:6px; }
        .admin-nav .nav-item:hover .submenu { display:block; }
        .admin-nav .submenu a { display:block; padding:10px 14px; color:#333; text-decoration:none; font-size:<?php echo $panel_menu_font_size; ?>px; border-radius:7px; transition:all 0.15s; }
        .admin-nav .submenu a:hover { background:#f8f9fa; color:var(--rang-asli,#FF6F00); }
        .admin-nav .submenu a i { width:20px; color:#999; margin-left:8px; }
        @media (max-width:768px) {
            .admin-header { padding:16px; }
            .admin-nav { flex-direction:column; gap:0; }
            .admin-nav .submenu { position:static; box-shadow:none; background:#3a4248; border-radius:0 0 8px 8px; }
            .admin-nav .submenu a { color:#ddd; }
            .admin-nav .submenu a:hover { background:#444; color:#FF6F00; }
            .admin-datebox { display:none; }
        }
        /* ===== هدر قشنگ عمومی (مثل دشبورد) ===== */
        .dash-hero {
            background:linear-gradient(120deg, #FF6F00 0%, #E65100 55%, #BF360C 100%);
            border-radius:16px; padding:26px 30px; margin-bottom:26px; color:#fff;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;
            box-shadow:0 8px 24px rgba(230,81,0,0.18);
        }
        .dash-hero .h-title { font-size:1.5rem; font-weight:800; margin-bottom:6px; letter-spacing:-0.3px; }
        .dash-hero .h-sub { font-size:13px; opacity:0.92; }
        .dash-hero .h-date {
            background:rgba(255,255,255,0.16); padding:10px 18px; border-radius:10px;
            font-size:14px; font-weight:600; backdrop-filter:blur(4px); white-space:nowrap;
        }
        .dash-hero .h-date i { margin-left:6px; opacity:0.85; }
        /* منوی ادمین زیر هدر قشنگ (dash-hero) */
        .dash-hero + .admin-nav { margin-top:18px; }
        .dash-hero ~ .admin-nav .nav-item > a { color:rgba(255,255,255,0.9); }
        .dash-hero ~ .admin-nav .nav-item > a:hover { background:rgba(255,255,255,0.16); color:#fff; }
        .dash-hero ~ .admin-nav .nav-item > a.active { background:#fff; color:#E65100; }
        @media (max-width:768px) { .dash-hero { padding:20px; } }
        .page-bar { background:linear-gradient(120deg, #FF6F00 0%, #E65100 55%, #BF360C 100%); border-radius:14px; padding:22px 28px; margin-bottom:22px; color:#fff; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; box-shadow:0 8px 24px rgba(230,81,0,0.18); }
        .page-bar .pb-left { display:flex; align-items:center; gap:10px; }
        .page-bar .pb-icon { font-size:1.1rem; opacity:0.9; }
        .page-bar .pb-name { font-size:1.4rem; font-weight:800; letter-spacing:-0.3px; }
        .page-bar .pb-sep { opacity:0.5; margin:0 2px; }
        .page-bar .pb-parent { font-size:0.95rem; font-weight:600; opacity:0.85; }
        .page-bar .pb-desc { font-size:13px; opacity:0.9; margin-top:4px; }
        .page-bar .pb-date { background:rgba(255,255,255,0.16); padding:10px 18px; border-radius:10px; font-size:14px; font-weight:600; backdrop-filter:blur(4px); white-space:nowrap; }
        .page-bar .pb-date i { margin-left:6px; opacity:0.85; }
        @media (max-width:768px) { .page-bar { padding:18px 20px; } }

        /* ===== ریسپانسیو کامل پنل ادمین (مثل سایت) ===== */
        html, body { overflow-x: hidden; max-width: 100%; }
        img, video, iframe, table { max-width: 100%; }
        .admin-content { overflow-x: auto; }

        /* جدول‌ها: اسکرول افقی داخل ظرف */
        .admin-content table, .dash-panel table {
            display: block;
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            border-collapse: collapse;
        }
        .admin-content table td, .admin-content table th,
        .dash-panel table td, .dash-panel table th {
            white-space: nowrap;
            padding: 8px 10px;
        }

        /* فرم دوستونه → تک‌ستونه در موبایل */
        .form-row { grid-template-columns: 1fr !important; }
        .settings-tabs { flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .settings-tabs a { flex-shrink: 0; }

        @media (max-width: 768px) {
            body { padding: 10px 8px 0 8px; }
            .admin-content { padding: 14px; border-radius: 10px; }
            .admin-brand h2 { font-size: 17px; }
            .admin-header { padding: 12px; gap: 10px; }
            .admin-header .ah-right { gap: 10px; }
            .settings-panel { padding: 16px; }
            .form-group input[type=text], .form-group input[type=email],
            .form-group input[type=tel], .form-group input[type=url],
            .form-group input[type=number], .form-group select, .form-group textarea {
                font-size: 16px;
            }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<?php
// اگر صفحه متغیر $use_hero رو true کرره باشه، هدر قشنگ (dash-hero) نشون میده
if (!empty($GLOBALS['use_hero'])) {
    $hero_title = $GLOBALS['hero_title'] ?? 'پنل مدیریت';
    $hero_sub   = $GLOBALS['hero_sub'] ?? '';
    admin_hero($hero_title, $hero_sub);
} else {
?>
    <div class="admin-header">
        <div class="admin-brand">
            <div class="logo"><i class="fa-solid fa-gauge-high"></i></div>
            <div>
                <h2>پنل مدیریت</h2>
                <div class="sub">مهراد سام — مدیریت محتوا و قالب‌ها</div>
            </div>
        </div>
        <div class="ah-right">
            <?php
            require_once MASIR_RISH . 'mohtava/menu/menu-editor.php';
            $admin_menu_items = menu_get_admin_items();
            menu_render_admin($admin_menu_items);
            ?>
        </div>
    </div>
<?php } ?>
<?php
// نوار نارنجی اسم صفحه فعلی
if (empty($GLOBALS['use_hero'])) {
    if (!function_exists('menu_get_admin_items')) {
        require_once MASIR_RISH . 'mohtava/menu/menu-editor.php';
    }
    $pb_items = menu_get_admin_items();
    $pb_path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($pb_path === '' || $pb_path === 'mod') $pb_path = 'mod/dashmod';
    $pb_name = '';
    $pb_icon = '';
    $pb_parent = '';
    $pb_desc = '';
    foreach ($pb_items as $mi) {
        $mu = trim($mi['url'] ?? '', '/');
        if ($mu !== '' && ($pb_path === $mu || strpos($pb_path, $mu) === 0)) {
            $pb_name = $mi['label'] ?? '';
            $pb_icon = $mi['icon'] ?? '';
            $pb_desc = $mi['desc'] ?? '';
            $pid = $mi['parent'] ?? -1;
            if ($pid !== -1 && $pid !== '') {
                foreach ($pb_items as $p) {
                    if (($p['_id'] ?? '') === $pid) { $pb_parent = $p['label'] ?? ''; break; }
                }
            }
            break;
        }
    }
    if ($pb_name === '') {
        $seg = explode('/', $pb_path);
        $pb_name = $seg[1] ?? ($seg[0] ?? 'پنل مدیریت');
    }
    $pb_shamsi = function_exists('to_jalali_persian') ? to_jalali_persian(date('Y-m-d H:i:s')) : '';
    $pb_greg = date('Y-m-d') . ' · ' . date('H:i');
    echo '<div class="page-bar">';
    echo '<div class="pb-left">';
    echo '<div>';
    echo '<div class="pb-left" style="gap:10px;">';
    if ($pb_parent !== '') echo '<span class="pb-parent">' . htmlspecialchars($pb_parent) . '</span><span class="pb-sep">/</span>';
    if ($pb_icon !== '') echo '<i class="fa-solid ' . htmlspecialchars($pb_icon) . ' pb-icon"></i> ';
    echo '<span class="pb-name">' . htmlspecialchars($pb_name) . '</span>';
    echo '</div>';
    if ($pb_desc !== '') echo '<div class="pb-desc">' . htmlspecialchars($pb_desc) . '</div>';
    echo '</div></div>';
    echo '<div class="pb-date"><i class="fa-solid fa-calendar-day"></i> ' . $pb_shamsi . ' &nbsp;|&nbsp; ' . $pb_greg . '</div>';
    echo '</div>';
}
?>
<?php
/**
 * رندر هدر قشنگ عمومی پنل (سبک دشبورد) با عنوان و تاریخ شمسی/میلادی
 * @param string $title  عنوان صفحه
 * @param string $sub    زیرعنوان (توضیح کوتاه)
 */
function admin_hero($title = 'پنل مدیریت', $sub = '') {
    $shamsi = function_exists('to_jalali') ? to_jalali(date('Y-m-d H:i:s'), 'Y/m/d') : '';
    $greg = date('Y-m-d') . ' · ' . date('H:i');
    echo '<div class="dash-hero">';
    echo '  <div>';
    echo '    <div class="h-title">' . htmlspecialchars($title) . '</div>';
    if ($sub !== '') echo '    <div class="h-sub">' . htmlspecialchars($sub) . '</div>';
    echo '  </div>';
    echo '  <div class="h-date"><i class="fa-solid fa-calendar-day"></i> ' . $shamsi . ' &nbsp;|&nbsp; ' . $greg . '</div>';
    echo '</div>';
}
?>
    <div class="admin-content">