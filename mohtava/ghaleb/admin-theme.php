<?php

require_once MASIR_DADE . 'bank.php';
require_once MASIR_RISH . 'haste/site_settings.php';

function admin_theme_route($action, $params) {
    switch ($action) {
        case 'sections':
            admin_theme_section_list();
            break;
        case 'section_edit':
            admin_theme_section_form($params[0] ?? null);
            break;
        case 'files':
            admin_theme_file_list();
            break;
        case 'file_edit':
            admin_theme_file_edit($params[0] ?? '');
            break;
        case 'custom':
            admin_theme_customizer();
            break;
        default:
            admin_theme_dashboard();
            break;
    }
}

function admin_theme_dashboard() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM template_sections");
    $sections = $r ? $r->fetch_assoc()['cnt'] ?? 0 : 0;
    $conn->close();
    $theme_dir = MASIR_GHALEB;
    $files = glob($theme_dir . '*.php');
    $file_count = $files ? count($files) : 0;
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت قالب</h3>
    <p style="color:#888; margin-bottom:24px;">مدیریت بخش‌های قالب، ویرایش فایل‌ها و تنظیمات ظاهری</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:32px;">
        <?php
        $cards = [
            ['fa-puzzle-piece', '#6C5CE7', $sections, 'بخش‌های محتوا', 'theme/sections'],
            ['fa-file-code', '#0984E3', $file_count, 'فایل‌های قالب', 'theme/files'],
            ['fa-paint-brush', '#FF6F00', '—', 'CSS سفارشی', 'theme/custom'],
            ['fa-palette', '#00B894', '—', 'تنظیمات قالب', 'settings?tab=theme'],
        ];
        foreach ($cards as $c): ?>
        <a href="<?= BASE_URL ?>mod/<?= $c[4] ?>" style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #eef0f4;display:flex;align-items:center;gap:16px;text-decoration:none;transition:all 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.08)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,0.06)';this.style.transform='none'">
            <div style="width:48px;height:48px;border-radius:10px;background:<?= $c[1] ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;flex-shrink:0;"><i class="fa-solid <?= $c[0] ?>"></i></div>
            <div><div style="font-size:1.5rem;font-weight:700;color:#1a1a1a;"><?= $c[2] ?></div><div style="font-size:13px;color:#888;"><?= $c[3] ?></div></div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_section_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT * FROM template_sections ORDER BY page ASC, section_key ASC");
    $sections = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>بخش‌های محتوا</h3>
    <p style="color:#888;margin-bottom:16px;">این بخش‌ها در قالب استفاده می‌شوند. می‌تونید محتوای هر بخش رو با ویرایشگر تغییر بدید.</p>
    <p><a href="<?= BASE_URL ?>mod/theme/section_edit" style="color:var(--rang-asli,#FF6F00);font-weight:700;">+ بخش جدید</a></p>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:12px;">
        <tr style="background:#f8f9fa;"><th>صفحه</th><th>کلید بخش</th><th>عنوان</th><th>وضعیت</th><th>عملیات</th></tr>
        <?php if (empty($sections)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:#888;">بخشی وجود ندارد. اولین بخش را ایجاد کنید.</td></tr>
        <?php else: foreach ($sections as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['page']) ?></td>
            <td><?= htmlspecialchars($s['section_key']) ?></td>
            <td><?= htmlspecialchars($s['title'] ?? '') ?></td>
            <td><?= $s['vaziat'] ? 'فعال' : 'غیرفعال' ?></td>
            <td><a href="<?= BASE_URL ?>mod/theme/section_edit/<?= $s['id'] ?>">ویرایش</a></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_section_form($id) {
    $section = ['page' => 'global', 'section_key' => '', 'title' => '', 'content' => '', 'vaziat' => 1];
    if ($id) {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("SELECT * FROM template_sections WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $section = $stmt->get_result()->fetch_assoc() ?: $section;
        $stmt->close();
        $conn->close();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $page = $_POST['page'] ?? 'global';
        $section_key = $_POST['section_key'] ?? '';
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $vaziat = (int)($_POST['vaziat'] ?? 1);
        if ($id) {
            $stmt = $conn->prepare("UPDATE template_sections SET page=?, section_key=?, title=?, content=?, vaziat=? WHERE id=?");
            $stmt->bind_param("ssssii", $page, $section_key, $title, $content, $vaziat, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO template_sections (page, section_key, title, content, vaziat) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $page, $section_key, $title, $content, $vaziat);
        }
        $stmt->execute();
        $stmt->close();
        $conn->close();
        redirect('mod/theme/sections');
        exit;
    }
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3><?= $id ? 'ویرایش بخش' : 'بخش جدید' ?></h3>
    <form method="post" style="max-width:800px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group" style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;">صفحه</label>
                <select name="page" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    <option value="global" <?= $section['page'] === 'global' ? 'selected' : '' ?>>سراسری</option>
                    <option value="home" <?= $section['page'] === 'home' ? 'selected' : '' ?>>خانه</option>
                    <option value="services" <?= $section['page'] === 'services' ? 'selected' : '' ?>>خدمات</option>
                    <option value="blog" <?= $section['page'] === 'blog' ? 'selected' : '' ?>>وبلاگ</option>
                    <option value="contact" <?= $section['page'] === 'contact' ? 'selected' : '' ?>>تماس</option>
                    <option value="product" <?= $section['page'] === 'product' ? 'selected' : '' ?>>محصول</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;">کلید بخش</label>
                <input type="text" name="section_key" value="<?= htmlspecialchars($section['section_key']) ?>" required style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="مثلا: hero_text, about_content">
                <span style="font-size:11px;color:#888;">از این کلید توی قالب استفاده می‌شه: <code>&lt;?= get_template_section('hero_text') ?&gt;</code></span>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">عنوان (داخلی)</label>
            <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">محتوا</label>
            <?php
            $edr_value = $section['content'];
            $edr_name = 'content';
            $edr_id = 'sectionContent';
            include __DIR__ . '/../../haste/editor/editor.php';
            ?>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">وضعیت</label>
            <select name="vaziat" style="padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                <option value="1" <?= $section['vaziat'] ? 'selected' : '' ?>>فعال</option>
                <option value="0" <?= !$section['vaziat'] ? 'selected' : '' ?>>غیرفعال</option>
            </select>
        </div>
        <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره بخش</button>
    </form>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_file_list() {
    $theme_dir = MASIR_GHALEB;
    $files = [];
    $allowed_exts = ['php', 'css', 'js', 'html', 'json'];
    foreach (glob($theme_dir . '*.php') as $f) $files[] = $f;
    foreach (glob($theme_dir . '*.css') as $f) $files[] = $f;
    foreach (glob($theme_dir . '*.js') as $f) $files[] = $f;
    foreach (glob($theme_dir . '*.html') as $f) $files[] = $f;
    foreach (glob($theme_dir . '*.json') as $f) $files[] = $f;
    natcasesort($files);
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>فایل‌های قالب</h3>
    <p style="color:#888;margin-bottom:16px;">فایل‌های قالب فعال (<strong><?= GHALEB_FAAAL ?></strong>) — با احتیاط ویرایش کنید! قبل از ویرایش بک‌آپ گرفته می‌شود.</p>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <tr style="background:#f8f9fa;"><th>نام فایل</th><th>حجم</th><th>آخرین تغییر</th><th>عملیات</th></tr>
        <?php foreach ($files as $f): $name = basename($f); $size = filesize($f); $mtime = filemtime($f); ?>
        <tr>
            <td><?= htmlspecialchars($name) ?></td>
            <td><?= $size > 1024 ? round($size/1024) . ' KB' : $size . ' B' ?></td>
            <td style="font-size:12px;color:#888;"><?= date('Y-m-d H:i', $mtime) ?></td>
            <td><a href="<?= BASE_URL ?>mod/theme/file_edit/<?= urlencode($name) ?>">ویرایش</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_file_edit($filename) {
    $filename = basename($filename);
    $filepath = MASIR_GHALEB . $filename;
    if (!file_exists($filepath)) {
        include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
        echo "<h3>فایل یافت نشد</h3>";
        include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_content'])) {
        $backup_dir = MASIR_GHALEB . 'backup/';
        if (!is_dir($backup_dir)) mkdir($backup_dir, 0755, true);
        $backup_file = $backup_dir . $filename . '.' . date('Ymd_His') . '.bak';
        copy($filepath, $backup_file);
        $new_content = $_POST['file_content'];
        file_put_contents($filepath, $new_content);
        $message = "فایل ذخیره شد. بک‌آپ: " . basename($backup_file);
    }
    $content = file_get_contents($filepath);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $is_html_editable = in_array($ext, ['php', 'html', 'htm']);
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>ویرایش فایل: <?= htmlspecialchars($filename) ?></h3>
    <p><a href="<?= BASE_URL ?>mod/theme/files" style="color:var(--rang-asli,#FF6F00);">&larr; بازگشت به لیست</a></p>
    <?php if (isset($message)): ?><p style="background:#e8f5e9;color:#2e7d32;padding:12px;border-radius:8px;margin:12px 0;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post" style="margin-top:12px;">
        <?php if ($is_html_editable): ?>
        <div style="margin-bottom:12px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">ویرایش بصری</label>
            <?php
            $edr_value = $content;
            $edr_name = 'file_content';
            $edr_id = 'fileEditor';
            include __DIR__ . '/../../haste/editor/editor.php';
            ?>
        </div>
        <?php else: ?>
        <div style="margin-bottom:12px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">کد فایل</label>
            <textarea name="file_content" rows="30" style="width:100%;padding:16px;font-family:monospace;font-size:13px;border:1.5px solid #dde1e6;border-radius:8px;direction:ltr;text-align:left;" spellcheck="false"><?= htmlspecialchars($content) ?></textarea>
        </div>
        <?php endif; ?>
        <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره فایل</button>
    </form>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_customizer() {
    require_once __DIR__ . '/../../haste/site_settings.php';
    global $site_settings;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new = [];

        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $r = upload_site_image('logo', 'logo/');
            if (is_string($r)) $new['general']['logo'] = $r;
        }
        if (!empty($_FILES['favicon']['name']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $r = upload_site_image('favicon', 'favicon/');
            if (is_string($r)) $new['general']['favicon'] = $r;
        }

        $new['theme'] = [
            'active' => $_POST['theme_active'] ?? 'mehrsam',
            'primary_color' => $_POST['primary_color'] ?? '#FF6F00',
            'primary_hover' => $_POST['primary_hover'] ?? '#E65100',
            'secondary_color' => $_POST['secondary_color'] ?? '#00B894',
            'font_family' => $_POST['font_family'] ?? 'Vazirmatn',
            'custom_css' => $_POST['custom_css'] ?? '',
            'custom_js' => $_POST['custom_js'] ?? '',
        ];

        foreach (['general', 'social'] as $section) {
            if (!empty($_POST[$section]) && is_array($_POST[$section])) {
                foreach ($_POST[$section] as $k => $v) {
                    $new[$section][$k] = $v;
                }
            }
        }

        save_site_settings($new);
        $site_settings = json_decode(file_get_contents(SITE_SETTINGS_FILE), true);
        $message = "تنظیمات ذخیره شد.";
    }

    $cur = $site_settings;
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>سفارشی‌سازی قالب</h3>
    <?php if (isset($message)): ?><p style="background:#e8f5e9;color:#2e7d32;padding:12px;border-radius:8px;"><?= $message ?></p><?php endif; ?>
    <form method="post" enctype="multipart/form-data" style="max-width:800px;">
        <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-bottom:20px;">
            <h4 style="margin-bottom:16px;">رنگ‌ها و فونت</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group"><label style="display:block;margin-bottom:4px;font-weight:600;">رنگ اصلی</label><input type="color" name="primary_color" value="<?= htmlspecialchars($cur['theme']['primary_color'] ?? '#FF6F00') ?>" style="width:60px;height:40px;border:none;border-radius:6px;cursor:pointer;"></div>
                <div class="form-group"><label style="display:block;margin-bottom:4px;font-weight:600;">رنگ هاور</label><input type="color" name="primary_hover" value="<?= htmlspecialchars($cur['theme']['primary_hover'] ?? '#E65100') ?>" style="width:60px;height:40px;border:none;border-radius:6px;cursor:pointer;"></div>
                <div class="form-group"><label style="display:block;margin-bottom:4px;font-weight:600;">رنگ ثانویه</label><input type="color" name="secondary_color" value="<?= htmlspecialchars($cur['theme']['secondary_color'] ?? '#00B894') ?>" style="width:60px;height:40px;border:none;border-radius:6px;cursor:pointer;"></div>
                <div class="form-group"><label style="display:block;margin-bottom:4px;font-weight:600;">فونت</label><select name="font_family" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;"><option value="Vazirmatn" <?= ($cur['theme']['font_family'] ?? '') === 'Vazirmatn' ? 'selected' : '' ?>>وزیرمتن</option><option value="Tahoma" <?= ($cur['theme']['font_family'] ?? '') === 'Tahoma' ? 'selected' : '' ?>>تاهوما</option><option value="IRANSans" <?= ($cur['theme']['font_family'] ?? '') === 'IRANSans' ? 'selected' : '' ?>>ایران‌سنس</option></select></div>
            </div>
        </div>

        <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-bottom:20px;">
            <h4 style="margin-bottom:16px;">لوگو و فاوآیکون</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group"><label style="display:block;margin-bottom:4px;font-weight:600;">لوگو</label><input type="file" name="logo" accept="image/*"><?php if (!empty($cur['general']['logo'])): ?><div style="margin-top:8px;"><img src="<?= $cur['general']['logo'] ?>" alt="" style="max-height:50px;border-radius:6px;"></div><?php endif; ?></div>
                <div class="form-group"><label style="display:block;margin-bottom:4px;font-weight:600;">فاوآیکون</label><input type="file" name="favicon" accept="image/*"><?php if (!empty($cur['general']['favicon'])): ?><div style="margin-top:8px;"><img src="<?= $cur['general']['favicon'] ?>" alt="" style="width:32px;height:32px;border-radius:4px;"></div><?php endif; ?></div>
            </div>
        </div>

        <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-bottom:20px;">
            <h4 style="margin-bottom:16px;">کدهای سفارشی</h4>
            <div class="form-group" style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;">CSS سفارشی</label>
                <textarea name="custom_css" rows="8" style="width:100%;padding:12px;font-family:monospace;font-size:13px;border:1.5px solid #dde1e6;border-radius:8px;direction:ltr;text-align:left;" spellcheck="false"><?= htmlspecialchars($cur['theme']['custom_css'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">JS سفارشی</label>
                <textarea name="theme[custom_js]" rows="6" style="width:100%;padding:12px;font-family:monospace;font-size:13px;border:1.5px solid #dde1e6;border-radius:8px;direction:ltr;text-align:left;" spellcheck="false"><?= htmlspecialchars($cur['theme']['custom_js'] ?? '') ?></textarea>
                <span style="font-size:11px;color:#888;">این کد قبل از تگ &lt;/body&gt; اضافه می‌شود</span>
            </div>
        </div>

        <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره همه</button>
    </form>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function get_template_section($section_key, $page = 'global') {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT content FROM template_sections WHERE section_key = ? AND (page = ? OR page = 'global') AND vaziat = 1 ORDER BY FIELD(page, ?, 'global') LIMIT 1");
    $stmt->bind_param("sss", $section_key, $page, $page);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $row ? $row['content'] : '';
}
