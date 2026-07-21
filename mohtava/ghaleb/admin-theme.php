<?php

require_once MASIR_DADE . 'bank.php';
require_once MASIR_RISH . 'haste/tanzimat.php';

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
        case 'create':
            admin_theme_create();
            break;
        case 'activate':
            admin_theme_activate($params[0] ?? '');
            break;
        case 'delete':
            admin_theme_delete($params[0] ?? '');
            break;
        case 'icons':
            admin_theme_icons();
            break;
        case 'menu':
            header('Location: ' . BASE_URL . 'mod/menu_editor/site');
            exit;
            break;
        default:
            admin_theme_manager();
            break;
    }
}

function admin_theme_manager() {
    $ghaleb_dir = MASIR_RISH . 'ghaleb' . DIRECTORY_SEPARATOR;
    $exclude = ['ghmod', 'manabe'];
    $active = GHALEB_FAAAL;

    // پیدا کردن همه قالب‌ها (پوشه‌هایی که sarfaraz.php دارن)
    $themes = [];
    foreach (glob($ghaleb_dir . '*', GLOB_ONLYDIR) as $dir) {
        $name = basename($dir);
        if (in_array($name, $exclude)) continue;
        $is_active = ($name === $active);
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.php');
        $file_count = $files ? count($files) : 0;
        // خواندن اطلاعات قالب از info.json (اختیاری)
        $info = [];
        $info_file = $dir . DIRECTORY_SEPARATOR . 'info.json';
        if (file_exists($info_file)) {
            $info = json_decode(file_get_contents($info_file), true) ?: [];
        }
        $themes[] = [
            'name' => $name,
            'label' => $info['label'] ?? $name,
            'description' => $info['description'] ?? '',
            'version' => $info['version'] ?? '۱.۰',
            'active' => $is_active,
            'files' => $file_count,
        ];
    }

    // پیام‌ها
    $msg = $_GET['msg'] ?? '';
    $err = $_GET['err'] ?? '';

    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .theme-mgr { max-width:960px; }
        .theme-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; margin-bottom:32px; }
        .theme-card { background:#fff; border-radius:14px; border:2px solid #eef0f4; padding:24px; transition:all 0.2s; position:relative; }
        .theme-card:hover { box-shadow:0 6px 20px rgba(0,0,0,0.08); }
        .theme-card.active { border-color:var(--rang-asli,#FF6F00); }
        .theme-card.active::after { content:'فعال'; position:absolute; top:12px; left:12px; background:var(--rang-asli,#FF6F00); color:#fff; font-size:11px; font-weight:700; padding:3px 10px; border-radius:10px; }
        .theme-card h4 { margin:0 0 6px; font-size:1.1rem; }
        .theme-card .desc { color:#888; font-size:13px; margin-bottom:12px; min-height:20px; }
        .theme-card .meta { display:flex; gap:12px; font-size:12px; color:#999; margin-bottom:16px; }
        .theme-card .meta span { background:#f5f6f8; padding:3px 8px; border-radius:6px; }
        .theme-card .actions { display:flex; gap:8px; flex-wrap:wrap; }
        .btn-activate { background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:8px; padding:8px 16px; cursor:pointer; font-weight:600; font-size:13px; }
        .btn-activate:hover { background:#E65100; }
        .btn-activate[disabled] { background:#ccc; cursor:not-allowed; }
        .btn-edit { background:#0984E3; color:#fff; border:none; border-radius:8px; padding:8px 16px; cursor:pointer; font-weight:600; font-size:13px; text-decoration:none; }
        .btn-delete { background:#dc3545; color:#fff; border:none; border-radius:8px; padding:8px 16px; cursor:pointer; font-weight:600; font-size:13px; }
        .btn-delete:hover { background:#c82333; }
        .btn-new { display:inline-flex; align-items:center; gap:8px; background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:10px; padding:12px 24px; cursor:pointer; font-weight:700; font-size:14px; text-decoration:none; margin-bottom:24px; }
        .btn-new:hover { background:#E65100; }
        .msg-ok { background:#e8f5e9; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-weight:600; }
        .msg-err { background:#ffebee; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-weight:600; }
        .sub-links { display:flex; gap:12px; margin-bottom:28px; flex-wrap:wrap; }
        .sub-links a { background:#f5f6f8; color:#333; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; transition:all 0.2s; }
        .sub-links a:hover { background:var(--rang-asli,#FF6F00); color:#fff; }
    </style>
    <div class="theme-mgr">
        <h3>مدیر قالب</h3>
        <p style="color:#888; margin-bottom:16px;">قالب‌های نصب شده را مدیریت کنید، قالب جدید بسازید یا قالب فعال را عوض کنید.</p>

        <?php if ($msg === 'created'): ?><div class="msg-ok">قالب جدید با موفقیت ساخته شد.</div><?php endif; ?>
        <?php if ($msg === 'activated'): ?><div class="msg-ok">قالب فعال شد. برای اعمال تغییرات صفحه را رفرش کنید.</div><?php endif; ?>
        <?php if ($msg === 'deleted'): ?><div class="msg-ok">قالب حذف شد.</div><?php endif; ?>
        <?php if ($err === 'delete_active'): ?><div class="msg-err"> قالب فعال را نمی‌توان حذف کرد. اول قالب دیگری را فعال کنید.</div><?php endif; ?>
        <?php if ($err === 'not_found'): ?><div class="msg-err">قالب مورد نظر یافت نشد.</div><?php endif; ?>
        <?php if ($err === 'delete_failed'): ?><div class="msg-err">خطا در حذف قالب.</div><?php endif; ?>

        <a href="<?= BASE_URL ?>mod/theme/create" class="btn-new"><i class="fa-solid fa-plus"></i> قالب جدید</a>

        <div class="sub-links">
            <a href="<?= BASE_URL ?>mod/theme/sections"><i class="fa-solid fa-puzzle-piece"></i> بخش‌های محتوا</a>
            <a href="<?= BASE_URL ?>mod/theme/files"><i class="fa-solid fa-file-code"></i> فایل‌های قالب فعال</a>
            <a href="<?= BASE_URL ?>mod/theme/custom"><i class="fa-solid fa-paint-brush"></i> CSS سفارشی</a>
            <a href="<?= BASE_URL ?>mod/settings?tab=theme"><i class="fa-solid fa-gear"></i> تنظیمات ظاهری</a>
        </div>

        <div class="theme-grid">
            <?php foreach ($themes as $t): ?>
            <div class="theme-card<?= $t['active'] ? ' active' : '' ?>">
                <h4><?= htmlspecialchars($t['label']) ?></h4>
                <div class="desc"><?= htmlspecialchars($t['description']) ?: 'بدون توضیحات' ?></div>
                <div class="meta">
                    <span><i class="fa-solid fa-folder"></i> <?= htmlspecialchars($t['name']) ?></span>
                    <span><i class="fa-solid fa-file"></i> <?= $t['files'] ?> فایل</span>
                    <span><i class="fa-solid fa-code-branch"></i> v<?= $t['version'] ?></span>
                </div>
                <div class="actions">
                    <?php if (!$t['active']): ?>
                    <a href="<?= BASE_URL ?>mod/theme/activate/<?= urlencode($t['name']) ?>" class="btn-activate" onclick="return confirm('قالب «<?= htmlspecialchars($t['label']) ?>» فعال شود؟')"><i class="fa-solid fa-check"></i> فعال کردن</a>
                    <?php else: ?>
                    <button class="btn-activate" disabled><i class="fa-solid fa-check-circle"></i> قالب فعال</button>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>mod/theme/files" class="btn-edit"><i class="fa-solid fa-pen"></i> ویرایش فایل‌ها</a>
                    <?php if (!$t['active']): ?>
                    <button class="btn-delete" onclick="if(confirm('قالب «<?= htmlspecialchars($t['label']) ?>» حذف شود؟\nاین عمل غیرقابل بازگشت است!')) location.href='<?= BASE_URL ?>mod/theme/delete/<?= urlencode($t['name']) ?>'"><i class="fa-solid fa-trash"></i> حذف</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_create() {
    $ghaleb_dir = MASIR_RISH . 'ghaleb' . DIRECTORY_SEPARATOR;
    $exclude = ['ghmod', 'manabe'];

    // لیست قالب‌های موجود برای کپی
    $existing = [];
    foreach (glob($ghaleb_dir . '*', GLOB_ONLYDIR) as $dir) {
        $name = basename($dir);
        if (!in_array($name, $exclude)) $existing[] = $name;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_name = trim($_POST['theme_name'] ?? '');
        $copy_from = $_POST['copy_from'] ?? '';
        $label = trim($_POST['theme_label'] ?? $new_name);
        $desc = trim($_POST['theme_desc'] ?? '');

        // اعتبارسنجی نام
        $new_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $new_name);
        if ($new_name === '' || strlen($new_name) < 2) {
            header('Location: ' . BASE_URL . 'mod/theme/create?err=name');
            exit;
        }
        $target = $ghaleb_dir . $new_name;
        if (is_dir($target)) {
            header('Location: ' . BASE_URL . 'mod/theme/create?err=exists');
            exit;
        }

        // ساخت پوشه
        if (!mkdir($target, 0755, true)) {
            header('Location: ' . BASE_URL . 'mod/theme/create?err=mkdir');
            exit;
        }

        // کپی فایل‌ها از قالب موجود یا ساخت خالی
        if ($copy_from && is_dir($ghaleb_dir . $copy_from)) {
            $src = $ghaleb_dir . $copy_from;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $item) {
                $rel = substr($item->getPathname(), strlen($src));
                $dest = $target . $rel;
                if ($item->isDir()) {
                    if (!is_dir($dest)) mkdir($dest, 0755, true);
                } else {
                    copy($item->getPathname(), $dest);
                }
            }
        } else {
            // ساخت فایل‌های پایه خالی
            file_put_contents($target . '/sarfaraz.php', "<?php\n// شروع قالب " . $label . "\n?>\n");
            file_put_contents($target . '/panevis.php', "<?php\n// پایان قالب " . $label . "\n?>\n");
        }

        // ذخیره info.json
        $info = ['label' => $label, 'description' => $desc, 'version' => '۱.۰', 'author' => '', 'created' => date('Y-m-d H:i')];
        file_put_contents($target . '/info.json', json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        header('Location: ' . BASE_URL . 'mod/theme?msg=created');
        exit;
    }

    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .create-form { max-width:600px; }
        .create-form label { display:block; margin-bottom:4px; font-weight:600; }
        .create-form input[type=text], .create-form select, .create-form textarea { width:100%; padding:10px 12px; border:1.5px solid #dde1e6; border-radius:8px; font-family:inherit; box-sizing:border-box; }
        .create-form textarea { resize:vertical; min-height:60px; }
        .create-form .fg { margin-bottom:16px; }
        .create-form .btn-submit { padding:12px 32px; background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:14px; }
    </style>
    <div class="create-form">
        <h3>ساخت قالب جدید</h3>
        <p style="color:#888;margin-bottom:20px;">قالب جدیدی بسازید یا از روی قالب موجود کپی کنید.</p>
        <?php if (isset($_GET['err'])): ?>
        <p style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:8px;margin-bottom:16px;">
            <?php
            $errs = ['name'=>'نام قالب نامعتبر است (فقط حروف انگلیسی، عدد، خط زیر و خط تیره).', 'exists'=>'قالبی با این نام قبلاً وجود دارد.', 'mkdir'=>'خطا در ساخت پوشه.'];
            echo $errs[$_GET['err']] ?? 'خطای نامشخص.';
            ?>
        </p>
        <?php endif; ?>
        <form method="post">
            <div class="fg">
                <label>نام قالب (انگلیسی، بدون فاصله)</label>
                <input type="text" name="theme_name" required placeholder="مثلاً: my-theme" dir="ltr" style="direction:ltr;text-align:left;">
                <span style="font-size:11px;color:#888;">فقط حروف انگلیسی، عدد، <code>-</code> و <code>_</code> مجاز است. مثلاً: <code>mehrsam-v2</code></span>
            </div>
            <div class="fg">
                <label>عنوان قالب (فارسی، نمایشی)</label>
                <input type="text" name="theme_label" placeholder="مثلاً: مهرسام نسخه ۲">
            </div>
            <div class="fg">
                <label>توضیحات</label>
                <textarea name="theme_desc" placeholder="توضیح کوتاه درباره این قالب..."></textarea>
            </div>
            <div class="fg">
                <label>کپی از قالب</label>
                <select name="copy_from">
                    <option value="">— ساخت خالی (فقط فایل‌های پایه) —</option>
                    <?php foreach ($existing as $e): ?>
                    <option value="<?= htmlspecialchars($e) ?>" <?= $e === GHALEB_FAAAL ? 'selected' : '' ?>><?= htmlspecialchars($e) ?> <?= $e === GHALEB_FAAAL ? '(فعال)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
                <span style="font-size:11px;color:#888;">اگر قالبی را انتخاب کنید، تمام فایل‌های آن کپی می‌شوند.</span>
            </div>
            <div style="display:flex;gap:12px;margin-top:20px;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-plus"></i> ساخت قالب</button>
                <a href="<?= BASE_URL ?>mod/theme" style="padding:12px 24px;background:#f5f6f8;color:#333;border-radius:8px;text-decoration:none;font-weight:600;">بازگشت</a>
            </div>
        </form>
    </div>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_theme_activate($name) {
    $ghaleb_dir = MASIR_RISH . 'ghaleb' . DIRECTORY_SEPARATOR;
    if (!$name || !is_dir($ghaleb_dir . $name)) {
        header('Location: ' . BASE_URL . 'mod/theme?err=not_found');
        exit;
    }
    // ذخیره در site_settings.json
    save_site_settings(['theme' => ['active' => $name]]);
    header('Location: ' . BASE_URL . 'mod/theme?msg=activated');
    exit;
}

function admin_theme_delete($name) {
    $ghaleb_dir = MASIR_RISH . 'ghaleb' . DIRECTORY_SEPARATOR;
    if (!$name || !is_dir($ghaleb_dir . $name)) {
        header('Location: ' . BASE_URL . 'mod/theme?err=not_found');
        exit;
    }
    if ($name === GHALEB_FAAAL) {
        header('Location: ' . BASE_URL . 'mod/theme?err=delete_active');
        exit;
    }
    // حذف بازگشتی
    $ok = true;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($ghaleb_dir . $name, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            if (!rmdir($item->getPathname())) $ok = false;
        } else {
            if (!unlink($item->getPathname())) $ok = false;
        }
    }
    if ($ok) rmdir($ghaleb_dir . $name);

    header('Location: ' . BASE_URL . 'mod/theme' . ($ok ? '?msg=deleted' : '?err=delete_failed'));
    exit;
}

function admin_theme_section_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT * FROM template_sections ORDER BY page ASC, section_key ASC");
    $sections = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
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
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
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
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
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
        include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
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
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
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
    require_once __DIR__ . '/../../haste/tanzimat.php';
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
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
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

function admin_theme_icons() {
    $icons = [
        ['house', 'fa-solid', 'خانه'],
        ['gauge-high', 'fa-solid', 'داشبورد'],
        ['file-lines', 'fa-solid', 'مقاله'],
        ['copy', 'fa-solid', 'کپی'],
        ['newspaper', 'fa-solid', 'روزنامه'],
        ['headset', 'fa-solid', 'پشتیبانی'],
        ['cube', 'fa-solid', 'محصول'],
        ['tags', 'fa-solid', 'برچسب'],
        ['folder', 'fa-solid', 'پوشه'],
        ['folder-open', 'fa-solid', 'پوشه باز'],
        ['folder-tree', 'fa-solid', 'درخت پوشه'],
        ['shopping-cart', 'fa-solid', 'سبد خرید'],
        ['coins', 'fa-solid', 'سکه'],
        ['comment-dots', 'fa-solid', 'پیام چت'],
        ['comments', 'fa-solid', 'نظرات'],
        ['users', 'fa-solid', 'کاربران'],
        ['envelope', 'fa-solid', 'ایمیل'],
        ['bell', 'fa-solid', 'اعلان'],
        ['gear', 'fa-solid', 'تنظیمات'],
        ['sliders', 'fa-solid', 'اسلایدر'],
        ['palette', 'fa-solid', 'پالت رنگ'],
        ['paint-brush', 'fa-solid', 'قلم مو'],
        ['puzzle-piece', 'fa-solid', 'پازل'],
        ['file-code', 'fa-solid', 'فایل کد'],
        ['layer-group', 'fa-solid', 'لایه‌ها'],
        ['bars', 'fa-solid', 'منو'],
        ['plus', 'fa-solid', 'افزودن'],
        ['pen', 'fa-solid', 'قلم ویرایش'],
        ['trash', 'fa-solid', 'حذف'],
        ['upload', 'fa-solid', 'آپلود'],
        ['download', 'fa-solid', 'دانلود'],
        ['save', 'fa-solid', 'ذخیره'],
        ['eye', 'fa-solid', 'مشاهده'],
        ['eye-slash', 'fa-solid', 'پنهان'],
        ['search', 'fa-solid', 'جستجو'],
        ['home', 'fa-solid', 'خانه'],
        ['store', 'fa-solid', 'فروشگاه'],
        ['truck', 'fa-solid', 'پیک'],
        ['shield-halved', 'fa-solid', 'امنیت'],
        ['github', 'fa-brands', 'گیتهاب'],
        ['sign-out-alt', 'fa-solid', 'خروج'],
        ['clock-rotate-left', 'fa-solid', 'تاریخچه'],
        ['cart-shopping', 'fa-solid', 'خرید'],
        ['heart', 'fa-solid', 'علاقه‌مندی'],
        ['star', 'fa-solid', 'ستاره'],
        ['check', 'fa-solid', 'تأیید'],
        ['check-circle', 'fa-solid', 'دایره تأیید'],
        ['xmark', 'fa-solid', 'بستن'],
        ['exclamation', 'fa-solid', 'هشدار'],
        ['info', 'fa-solid', 'اطلاعات'],
        ['question', 'fa-solid', 'سؤال'],
        ['chart-line', 'fa-solid', 'نمودار'],
        ['chart-bar', 'fa-solid', 'نمودار میله‌ای'],
        ['chart-pie', 'fa-solid', 'نمودار دایره‌ای'],
        ['calendar', 'fa-solid', 'تقویم'],
        ['calendar-days', 'fa-solid', 'تقویم روزانه'],
        ['clock', 'fa-solid', 'ساعت'],
        ['image', 'fa-solid', 'تصویر'],
        ['video', 'fa-solid', 'ویدیو'],
        ['music', 'fa-solid', 'موسیقی'],
        ['microphone', 'fa-solid', 'میکروفون'],
        ['link', 'fa-solid', 'پیوند'],
        ['paperclip', 'fa-solid', 'گیره'],
        ['code', 'fa-solid', 'کد'],
        ['terminal', 'fa-solid', 'ترمینال'],
        ['database', 'fa-solid', 'پایگاه داده'],
        ['server', 'fa-solid', 'سرور'],
        ['wifi', 'fa-solid', 'وای‌فای'],
        ['lock', 'fa-solid', 'قفل'],
        ['unlock', 'fa-solid', 'باز کردن قفل'],
        ['key', 'fa-solid', 'کلید'],
        ['user', 'fa-solid', 'کاربر'],
        ['user-plus', 'fa-solid', 'افزودن کاربر'],
        ['user-minus', 'fa-solid', 'حذف کاربر'],
        ['user-gear', 'fa-solid', 'تنظیمات کاربر'],
        ['id-card', 'fa-solid', 'کارت شناسایی'],
        ['map-marker', 'fa-solid', 'موقعیت'],
        ['phone', 'fa-solid', 'تلفن'],
        ['mobile', 'fa-solid', 'موبایل'],
        ['at', 'fa-solid', 'ایمیل'],
        ['globe', 'fa-solid', 'جهان'],
        ['paper-plane', 'fa-solid', 'هواپیمای کاغذی'],
        ['bookmark', 'fa-solid', 'بوکمارک'],
        ['tag', 'fa-solid', 'برچسب'],
        ['share', 'fa-solid', 'اشتراک'],
        ['thumbs-up', 'fa-solid', 'لایک'],
        ['thumbs-down', 'fa-solid', 'دیسلایک'],
        ['flag', 'fa-solid', 'پرچم'],
        ['bolt', 'fa-solid', 'برق'],
        ['sun', 'fa-solid', 'آفتاب'],
        ['moon', 'fa-solid', 'ماه'],
        ['cloud', 'fa-solid', 'ابره'],
        ['fire', 'fa-solid', 'آتش'],
        ['droplet', 'fa-solid', 'قطره'],
        ['leaf', 'fa-solid', 'برگ'],
        ['tree', 'fa-solid', 'درخت'],
        ['car', 'fa-solid', 'ماشین'],
        ['plane', 'fa-solid', 'هواپیما'],
        ['ship', 'fa-solid', 'کشتی'],
        ['book', 'fa-solid', 'کتاب'],
        ['graduation-cap', 'fa-solid', 'کلاه فارغ‌التحصیلی'],
        ['globe-americas', 'fa-solid', 'جهان'],
        ['map', 'fa-solid', 'نقشه'],
        ['compass', 'fa-solid', 'قطب‌نما'],
        ['history', 'fa-solid', 'تاریخچه'],
        ['print', 'fa-solid', 'چاپ'],
        ['file-export', 'fa-solid', 'خروجی'],
        ['file-import', 'fa-solid', 'ورودی'],
        ['arrows-rotate', 'fa-solid', 'بازخوانی'],
        ['sync', 'fa-solid', 'همگام‌سازی'],
        ['redo', 'fa-solid', 'بازگشت'],
        ['undo', 'fa-solid', 'برگردان'],
        ['filter', 'fa-solid', 'فیلتر'],
        ['sort', 'fa-solid', 'مرتب‌سازی'],
        ['ellipsis', 'fa-solid', 'بیشتر'],
        ['ellipsis-vertical', 'fa-solid', 'بیشتر عمودی'],
        ['bars-progress', 'fa-solid', 'نوار پیشرفت'],
        ['spinner', 'fa-solid', 'چرخنده'],
        ['circle-notch', 'fa-solid', 'دایره'],
        ['asterisk', 'fa-solid', 'ستاره'],
        ['hashtag', 'fa-solid', 'هاشتگ'],
        ['percent', 'fa-solid', 'درصد'],
        ['plus-minus', 'fa-solid', 'بیشتر/کمتر'],
        ['x', 'fa-solid', 'بستن'],
        ['circle-xmark', 'fa-solid', 'دایره بستن'],
        ['circle-check', 'fa-solid', 'دایره تأیید'],
        ['circle-plus', 'fa-solid', 'دایره افزودن'],
        ['circle-minus', 'fa-solid', 'دایره کاهش'],
        ['circle-info', 'fa-solid', 'اطلاعات'],
        ['triangle-exclamation', 'fa-solid', 'مثلث هشدار'],
        ['bug', 'fa-solid', 'باگ'],
        ['shield', 'fa-solid', 'سپر'],
        ['user-shield', 'fa-solid', 'کاربر محافظ'],
        ['lock-open', 'fa-solid', 'قفل باز'],
        ['eye-dropper', 'fa-solid', 'قطره‌چکان'],
        ['crop', 'fa-solid', 'برش'],
        ['maximize', 'fa-solid', 'بزرگ‌تر'],
        ['minimize', 'fa-solid', 'کوچک‌تر'],
        ['expand', 'fa-solid', 'باز کردن'],
        ['compress', 'fa-solid', 'فشردن'],
        ['window-maximize', 'fa-solid', 'پنجره بزرگ'],
        ['window-minimize', 'fa-solid', 'پنجره کوچک'],
        ['clone', 'fa-solid', 'کلون'],
        ['object-group', 'fa-solid', 'گروه'],
        ['object-ungroup', 'fa-solid', 'جداسازی'],
        ['magic', 'fa-solid', 'جادو'],
        ['wand-magic-sparkles', 'fa-solid', 'چوب جادو'],
        ['robot', 'fa-solid', 'ربات'],
        ['user-astronaut', 'fa-solid', 'فضانورد'],
        ['headphones', 'fa-solid', 'هندزفری'],
        ['microphone-lines', 'fa-solid', 'میکروفون خطی'],
        ['volume-high', 'fa-solid', 'صدا'],
        ['play', 'fa-solid', 'پخش'],
        ['pause', 'fa-solid', 'توقف'],
        ['stop', 'fa-solid', 'توقف'],
        ['forward', 'fa-solid', 'جلو'],
        ['backward', 'fa-solid', 'عقب'],
        ['shuffle', 'fa-solid', 'تصادفی'],
        ['repeat', 'fa-solid', 'تکرار'],
    ];

    $search = $_GET['q'] ?? '';
    $filtered = $icons;
    if ($search !== '') {
        $filtered = array_filter($icons, function($i) use ($search) {
            return mb_strpos($i[0], $search) !== false || mb_strpos($i[2], $search) !== false;
        });
    }

    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .icons-page { max-width:1100px; }
        .icons-search { width:100%; padding:12px 16px; border:2px solid #dde1e6; border-radius:10px; font-size:16px; font-family:inherit; margin-bottom:24px; box-sizing:border-box; }
        .icons-search:focus { outline:none; border-color:var(--rang-asli,#FF6F00); }
        .icons-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:10px; }
        .icon-box { background:#fff; border:1px solid #eef0f4; border-radius:10px; padding:16px 8px; text-align:center; cursor:pointer; transition:all 0.15s; position:relative; }
        .icon-box:hover { border-color:var(--rang-asli,#FF6F00); box-shadow:0 4px 12px rgba(255,111,0,0.12); transform:translateY(-2px); }
        .icon-box i { font-size:26px; color:#333; display:block; margin-bottom:8px; }
        .icon-box .icon-name { font-size:11px; color:#666; word-break:break-all; line-height:1.3; }
        .icon-box .icon-fa { font-size:10px; color:#aaa; direction:ltr; display:block; margin-top:4px; font-family:monospace; }
        .icon-box .icon-label { font-size:12px; color:var(--rang-asli,#FF6F00); font-weight:600; margin-top:2px; }
        .icon-box.copied { border-color:#28a745; background:#f0fff0; }
        .icon-box.copied i { color:#28a745; }
        .copy-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%); background:#333; color:#fff; padding:10px 24px; border-radius:8px; font-size:14px; font-weight:600; z-index:9999; display:none; }
    </style>
    <div class="icons-page">
        <h3>آیکن‌های FontAwesome</h3>
        <p style="color:#888;margin-bottom:16px;">روی هر آیکن کلیک کنید تا کد فونت آن کپی شود. از این کدها در منوی پنل مدیریت استفاده کنید.</p>
        <input type="text" class="icons-search" id="iconSearch" placeholder="جستجوی آیکن... (مثلاً home، کاربر، تنظیمات)" value="<?= htmlspecialchars($search) ?>">
        <div class="icons-grid" id="iconsGrid">
            <?php foreach ($filtered as $i): ?>
            <div class="icon-box" onclick="copyIcon(this, '<?= $i[1] ?> fa-<?= $i[0] ?>')" title="کلیک برای کپی">
                <i class="<?= $i[1] ?> fa-<?= $i[0] ?>"></i>
                <div class="icon-label"><?= $i[2] ?></div>
                <div class="icon-fa"><?= $i[1] ?> fa-<?= $i[0] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($filtered)): ?>
        <p style="text-align:center;color:#aaa;padding:40px;font-size:16px;">آیکنی یافت نشد.</p>
        <?php endif; ?>
    </div>
    <div class="copy-toast" id="copyToast">کپی شد!</div>
    <script>
    document.getElementById('iconSearch').addEventListener('input', function() {
        var q = this.value.trim();
        var boxes = document.querySelectorAll('.icon-box');
        boxes.forEach(function(b) {
            var name = b.querySelector('.icon-fa').textContent.toLowerCase();
            var label = b.querySelector('.icon-label').textContent;
            b.style.display = (!q || name.indexOf(q.toLowerCase()) !== -1 || label.indexOf(q) !== -1) ? '' : 'none';
        });
    });
    function copyIcon(el, code) {
        navigator.clipboard.writeText(code).then(function() {
            el.classList.add('copied');
            var toast = document.getElementById('copyToast');
            toast.textContent = 'کپی شد: ' + code;
            toast.style.display = 'block';
            setTimeout(function() { toast.style.display = 'none'; el.classList.remove('copied'); }, 1500);
        });
    }
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}
