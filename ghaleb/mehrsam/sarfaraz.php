<?php
// لود تنظیمات سایت
require_once dirname(__DIR__, 2) . '/haste/tanzimat.php';
require_once dirname(__DIR__, 2) . '/haste/tavabe.php';
$theme_settings = get_site_setting('theme') ?? [];
$general_settings = get_site_setting('general') ?? [];
// سازگاری با فرمت قدیمی (مسطح)
if (empty($general_settings)) {
    global $site_settings;
    $general_settings = $site_settings ?? [];
}
$primary_color = $theme_settings['primary_color'] ?? '#FF6F00';
$primary_hover = $theme_settings['primary_hover'] ?? '#E65100';
$secondary_color = $theme_settings['secondary_color'] ?? '#00B894';
$font_family = $theme_settings['font_family'] ?? 'Vazirmatn';
$body_font_size = $theme_settings['body_font_size'] ?? '15';
$body_text_color = $theme_settings['body_text_color'] ?? '#1a1a1a';
$custom_css = $theme_settings['custom_css'] ?? '';
$logo_url = !empty($general_settings['logo']) ? $general_settings['logo'] : BASE_URL . 'ghaleb/manabe/logo.png';
$favicon_url = !empty($general_settings['favicon']) ? $general_settings['favicon'] : BASE_URL . 'ghaleb/manabe/favicon.png';
$site_title = $general_settings['site_title'] ?? 'مهراد سام';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($onvan_safhe ?? $site_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_sharh ?? SITE_SLOGAN) ?>">
    <meta name="robots" content="index, follow">
    <link rel="icon" href="<?= htmlspecialchars($favicon_url) ?>" type="image/x-icon">
    <?php if (!empty($custom_css)): ?>
    <style><?= $custom_css ?></style>
    <?php endif; ?>

    <?php
    // توابع سبد خرید برای هدر
    if (!function_exists('sabad_count')) {
        require_once __DIR__ . '/../../mohtava/forushgah/sabad-model.php';
    }
    ?>

    <!-- Open Graph -->
    <meta property="og:title"       content="<?= htmlspecialchars($onvan_safhe ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_sharh ?? SITE_SLOGAN) ?>">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= BASE_URL . $_SERVER['REQUEST_URI'] ?>">

    <!-- فونت وزیر متن -->
    <style>
        @font-face {
            font-family: 'Vazirmatn';
            src: url('<?= BASE_URL ?>ghaleb/manabe/fonts/Vazirmatn-RD-Regular.woff2') format('woff2');
            font-weight: 400;
            font-display: swap;
        }
        @font-face {
            font-family: 'Vazirmatn';
            src: url('<?= BASE_URL ?>ghaleb/manabe/fonts/Vazirmatn-RD-Bold.woff2') format('woff2');
            font-weight: 700;
            font-display: swap;
        }
    </style>

    <!-- آیکون‌ها (محلی) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>ghaleb/manabe/fontawesome/all.min.css">

    <style>
        /* ====================================================
           متغیرها و ریست
        ==================================================== */
        :root {
            --rang-asli:     <?= $primary_color ?>;
            --rang-tira:     <?= $primary_hover ?>;
            --rang-roshan:   <?= shade_color($primary_color, 92) ?>;
            --rang-matn:     <?= $body_text_color ?>;
            --rang-zamin:    #ffffff;
            --rang-sabz:     #f8f9fa;
            --rang-border:   #e9ecef;
            --rang-gray:     #6c757d;
            --rang-makm1:    #2D3436;
            --rang-makm2:    <?= $secondary_color ?>;
            --rang-makm3:    #6C5CE7;
            --rang-makm4:    #FDCB6E;
            --rang-makm5:    #E17055;
            --sayeh:         0 4px 20px rgba(0,0,0,0.08);
            --border-radius: 12px;
            --transition:    all 0.3s ease;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: inherit; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            font-size: <?= (int)$body_font_size ?>px;
            line-height: 1.8;
            color: var(--rang-matn);
            background: var(--rang-zamin);
            direction: rtl;
        }

        a { text-decoration: none; color: inherit; transition: var(--transition); }
        img { max-width: 100%; height: auto; }
        ul { list-style: none; }

        .mohtava-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ====================================================
           دکمه‌ها
        ==================================================== */
        .dakmeh {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 8px;
            font-family: 'Vazirmatn', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: var(--transition);
        }
        .dakmeh-asli {
            background: var(--rang-asli);
            color: #fff;
        }
        .dakmeh-asli:hover {
            background: var(--rang-tira);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,111,0,0.35);
        }
        .dakmeh-khali {
            background: transparent;
            color: var(--rang-asli);
            border: 2px solid var(--rang-asli);
        }
        .dakmeh-khali:hover {
            background: var(--rang-asli);
            color: #fff;
        }

        /* ====================================================
           هدر / ناوبری
        ==================================================== */
        .header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .header-dakhel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        /* لوگو */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 20px;
            color: var(--rang-asli);
        }
        .logo-icon {
            width: 42px;
            height: 42px;
            background: var(--rang-asli);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }
        .logo-zir {
            font-size: 11px;
            font-weight: 400;
            color: var(--rang-gray);
            display: block;
            line-height: 1;
        }

        /* منو */
        .nav ul {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav a {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            color: var(--rang-matn);
            font-size: 14px;
        }
        .nav a:hover,
        .nav a.faali {
            color: var(--rang-asli);
            background: var(--rang-roshan);
        }
        .nav .dakmeh-asli {
            padding: 8px 20px;
            font-size: 14px;
        }
        .nav .dakmeh-asli:hover { color: #fff; }

        /* منوی موبایل */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--rang-matn);
        }

        @media (max-width: 768px) {
            .nav-toggle { display: block; }
            .nav {
                display: none;
                position: absolute;
                top: 70px;
                right: 0;
                left: 0;
                background: #fff;
                padding: 16px 20px;
                box-shadow: var(--sayeh);
            }
            .nav.baz { display: block; }
            .nav ul { flex-direction: column; align-items: stretch; }
            .nav a { display: block; padding: 12px 16px; }
        }

        /* ====================================================
           سکشن‌های مشترک
        ==================================================== */
        .sarsafhe-safhe {
            background: linear-gradient(135deg, var(--rang-roshan) 0%, #fff 100%);
            padding: 60px 0;
            text-align: center;
            border-bottom: 3px solid var(--rang-asli);
        }
        .sarsafhe-safhe h1 {
            font-size: 2rem;
            color: var(--rang-matn);
            margin-bottom: 12px;
        }
        .sarsafhe-safhe p {
            color: var(--rang-gray);
            font-size: 1rem;
        }
        .masir-nabz {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
            font-size: 14px;
            color: var(--rang-gray);
        }
        .masir-nabz a { color: var(--rang-asli); }
        .masir-nabz span { color: var(--rang-gray); }

        /* ====================================================
           کارت خدمت
        ==================================================== */
        .kart-khadamat {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 32px 24px;
            box-shadow: var(--sayeh);
            border: 1px solid var(--rang-border);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .kart-khadamat:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(255,111,0,0.15);
            border-color: var(--rang-asli);
        }
        .kart-khadamat .icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            margin-bottom: 20px;
            background: var(--rang-asli);
        }
        .kart-khadamat h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--rang-matn);
        }
        .kart-khadamat p {
            color: var(--rang-gray);
            font-size: 0.9rem;
            flex: 1;
            line-height: 1.7;
        }
        .kart-khadamat .lnk {
            margin-top: 20px;
            color: var(--rang-asli);
            font-size: 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .kart-khadamat:hover .lnk { gap: 10px; }

        /* ====================================================
           گرید
        ==================================================== */
        .gerid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .gerid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media (max-width: 992px) {
            .gerid-3 { grid-template-columns: repeat(2, 1fr); }
            .gerid-4 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .gerid-3 { grid-template-columns: 1fr; }
            .gerid-4 { grid-template-columns: 1fr; }
        }

        /* ====================================================
           عنوان بخش‌ها
        ==================================================== */
        .onvan-bakhsh {
            text-align: center;
            margin-bottom: 48px;
        }
        .onvan-bakhsh .barg {
            display: inline-block;
            background: var(--rang-roshan);
            color: var(--rang-asli);
            font-size: 13px;
            font-weight: 700;
            padding: 6px 18px;
            border-radius: 20px;
            margin-bottom: 12px;
        }
        .onvan-bakhsh h2 {
            font-size: 1.8rem;
            color: var(--rang-matn);
            margin-bottom: 12px;
        }
        .onvan-bakhsh p {
            color: var(--rang-gray);
            max-width: 550px;
            margin: 0 auto;
        }

        /* ====================================================
           فاصله‌گذاری
        ==================================================== */
        .bakhsh { padding: 80px 0; }
        .bakhsh-sabz { background: var(--rang-sabz); }

        /* ====================================================
           صفحه اول (صفحه‌ساز - حالت آزاد)
        ==================================================== */
        .homepage-content { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
        .homepage-content .builder-free-canvas {
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 600px;
            overflow: visible;
        }
        .homepage-content .builder-free-canvas [class*="bpos-"] { box-sizing: border-box; }
        .homepage-content .builder-free-canvas img { max-width: 100%; height: auto; }
        .homepage-content [data-block-index] { margin-bottom: 16px; }
        @media (max-width: 1199px) {
            .homepage-content .builder-free-canvas [class*="bpos-"] {
                position: static !important;
                left: auto !important; top: auto !important;
                width: 100% !important;
                margin-bottom: 16px;
            }
        }
        @media (max-width: 768px) {
            .homepage-content .builder-free-canvas { min-height: 0 !important; }
            .homepage-content .builder-free-canvas [class*="bpos-"] {
                position: static !important;
                left: auto !important; top: auto !important;
                width: 100% !important;
                margin-bottom: 16px;
            }
        }

    </style>
</head>
<body>

<?php if (isLoggedIn() && isAdmin()): ?>
<div style="background:#343a40; color:#fff; text-align:center; padding:6px 0; font-size:13px; position:sticky; top:0; z-index:9999;">
    <span style="opacity:0.7;">پنل مدیریت فعال است</span>
    <a href="<?= BASE_URL ?>mod/dashmod" style="color:#ffc107; text-decoration:none; margin-right:15px; font-weight:700;">
        <i class="fa-solid fa-lock"></i> ورود به پنل
    </a>
</div>
<?php endif; ?>

<?php
$builder_header_html = '';
if (!function_exists('builder_render_part')) {
    if (file_exists(MASIR_RISH . 'mohtava/sakhtar/builder.php')) {
        require_once MASIR_RISH . 'mohtava/sakhtar/builder.php';
    }
}
if (function_exists('builder_render_part')) {
    $bh_ctx = [];
    if (!empty($GLOBALS['page_slug'])) $bh_ctx['slug'] = $GLOBALS['page_slug'];
    if (!empty($GLOBALS['page_type'])) $bh_ctx['type'] = $GLOBALS['page_type'];
    $builder_header_html = builder_render_part('header', $bh_ctx);
}
?>
<?php if ($builder_header_html): ?>
<div class="builder-part builder-part-header"><?= $builder_header_html ?></div>
<?php else: ?>
<header class="header">
    <div class="mohtava-container">
        <div class="header-dakhel">

            <!-- لوگو -->
            <a href="<?= BASE_URL ?>/" class="logo">
                <?php if (!empty($general_settings['logo'])): ?>
                    <img src="<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($site_title) ?>" style="height:42px; border-radius:8px;">
                <?php else: ?>
                    <div class="logo-icon"><i class="fa-solid fa-laptop"></i></div>
                <?php endif; ?>
                <div>
                    <?= htmlspecialchars($site_title) ?>
                    <span class="logo-zir">MHSi.ir</span>
                </div>
            </a>

            <!-- دکمه موبایل -->
            <button class="nav-toggle" onclick="this.nextElementSibling.classList.toggle('baz')">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- منو -->
            <?php
            require_once MASIR_RISH . 'mohtava/menu/menu-editor.php';
            $site_menu_items = menu_get_site_items();
            menu_render_site($site_menu_items);
            ?>

        </div>
    </div>
</header>
<?php endif; ?>

<!-- محتوای صفحه از اینجا شروع می‌شود -->
