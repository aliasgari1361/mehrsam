<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/blocks/block-types.php';

function builder_devices() {
    return [
        'wide'        => ['label' => 'عریض',      'w' => 1920, 'bp' => 'wide'],
        'desktop'     => ['label' => 'دسکتاپ',    'w' => 1366, 'bp' => 'desktop'],
        'tablet_land' => ['label' => 'تبلت افقی', 'w' => 1024, 'bp' => 'tablet'],
        'tablet_port' => ['label' => 'تبلت عمودی','w' => 768,  'bp' => 'tablet'],
        'mobile_land' => ['label' => 'موبایل افقی','w' => 844, 'bp' => 'mobile'],
        'mobile_port' => ['label' => 'موبایل عمودی','w' => 390, 'bp' => 'mobile'],
    ];
}

function builder_breakpoints() {
    return [
        'wide'   => ['max' => 99999, 'media' => null],
        'desktop'=> ['max' => 1599,  'media' => '@media(max-width:1599px)'],
        'tablet' => ['max' => 1199,  'media' => '@media(max-width:1199px)'],
        'mobile' => ['max' => 767,   'media' => '@media(max-width:767px)'],
    ];
}

function builder_bp_width($bp) {
    $w = ['wide' => 1600, 'desktop' => 1366, 'tablet' => 1024, 'mobile' => 390];
    return $w[$bp] ?? 1366;
}

function _builder_pick_pos($pos, $keys) {
    foreach ($keys as $k) {
        if (!empty($pos[$k]) && is_array($pos[$k])) return $pos[$k];
    }
    return null;
}

function builder_effective_pos($block) {
    $pos = $block['pos'] ?? [];
    $order = ['wide', 'desktop', 'tablet', 'mobile'];
    $idx = array_flip($order);
    $eff = [];
    foreach ($order as $target) {
        $ti = $idx[$target];
        $prio = [$target];
        for ($j = $ti - 1; $j >= 0; $j--) $prio[] = $order[$j];
        for ($j = $ti + 1; $j < 4; $j++) $prio[] = $order[$j];
        $v = _builder_pick_pos($pos, $prio);
        $eff[$target] = $v ? array_merge(['x' => 0, 'y' => 0, 'w' => 300, 'z' => 1], $v) : null;
    }
    return $eff;
}

function builder_route($action, $params) {
    $sub = $params[0] ?? '';
    switch ($action) {
        case 'pages':
            builder_page_list();
            break;
        case 'new':
            builder_template_new();
            break;
        case 'create':
            builder_template_create();
            break;
        case 'save_condition':
            builder_template_save_condition();
            break;
        case 'delete':
            builder_template_delete($params[0] ?? 0);
            break;
        case 'edit':
            builder_page_edit($sub);
            break;
        case 'edit_post':
            $edit_type = $params[0] ?? '';
            $edit_id   = $params[1] ?? '';
            builder_page_edit_post($edit_type, $edit_id);
            break;
        case 'preview':
            builder_preview_page($sub);
            break;
        case 'save':
            builder_save_blocks();
            break;
        case 'render':
            builder_render_page($sub);
            break;
        case 'clear_cache':
            builder_clear_cache($sub);
            break;
        default:
            builder_page_list();
            break;
    }
}

function builder_condition_label($row) {
    $type = $row['condition_type'] ?? 'single';
    $val  = $row['condition_value'] ?? '';
    $part = $row['part'] ?? '';
    $map = [
        'single'  => ['single' => 'صفحه تکی (بر اساس نوع)', 'post' => 'مطلب تکی', 'product' => 'محصول تکی', 'khadamat' => 'خدمت تکی', 'safhe' => 'برگه تکی', 'home' => 'صفحه اصلی'],
        'archive' => ['archive' => 'آرشیو', 'blog' => 'آرشیو وبلاگ', 'product' => 'آرشیو محصولات', 'khadamat' => 'آرشیو خدمات'],
        'global'  => ['*' => 'سراسری (همه صفحات)'],
    ];
    $label = $map[$type][$val] ?? $val;
    $prefix = $type === 'single' ? 'تکی' : ($type === 'archive' ? 'آرشیو' : 'سراسری');
    $part_label = [
        '' => 'محتوا',
        'header' => 'هدر',
        'footer' => 'پانویس',
        'single' => 'قالب تکی',
        'archive' => 'قالب آرشیو',
    ][$part] ?? $part;
    return $part_label . ' — ' . $label;
}

function builder_page_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT bp.id, bp.page_id, bp.page_type, bp.name, bp.condition_type, bp.condition_value,
                            bp.cached_html IS NOT NULL AS has_cache, bp.updated_at, p.title 
                            FROM block_pages bp 
                            LEFT JOIN posts p ON bp.page_id = p.id 
                            ORDER BY bp.condition_type, bp.updated_at DESC");
    $pages = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .theme-table { width:100%; border-collapse:separate; border-spacing:0; background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 2px 14px rgba(0,0,0,0.06); border:1px solid #eef0f4; }
        .theme-table thead th {
            background:#f8f9fa; color:#555; font-size:12.5px; font-weight:700; text-align:right;
            padding:14px 18px; border-bottom:2px solid #eef0f4; white-space:nowrap;
        }
        .theme-table tbody td { padding:14px 18px; border-bottom:1px solid #f1f3f5; font-size:14px; color:#333; vertical-align:middle; }
        .theme-table tbody tr:last-child td { border-bottom:none; }
        .theme-table tbody tr { transition:background 0.15s; }
        .theme-table tbody tr:hover { background:#fff8f1; }
        .tt-name { font-weight:700; color:#1a1a1a; }
        .tt-badge { display:inline-block; padding:4px 10px; border-radius:20px; font-size:11.5px; font-weight:600; background:#fff3e0; color:#E65100; }
        .tt-cache-on { color:#2e7d32; font-weight:600; font-size:13px; }
        .tt-cache-off { color:#aaa; font-size:13px; }
        .tt-actions a { font-size:13px; text-decoration:none; font-weight:600; transition:color 0.15s; }
        .tt-actions a.edit { color:var(--rang-asli,#FF6F00); }
        .tt-actions a.edit:hover { color:#E65100; }
        .tt-actions a.del { color:#c62828; margin-right:12px; }
        .tt-actions a.del:hover { text-decoration:underline; }
        .theme-empty { text-align:center; padding:60px 20px; color:#aaa; }
        .theme-empty i { font-size:46px; display:block; margin-bottom:14px; color:#ddd; }
    </style>

    <div style="display:flex; justify-content:flex-end; margin-bottom:18px;">
        <a href="<?= BASE_URL ?>mod/builder/new" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;background:var(--rang-asli,#FF6F00);color:#fff;font-weight:800;font-size:14px;text-decoration:none;box-shadow:0 6px 18px rgba(255,111,0,0.3);transition:all 0.2s;"><i class="fa-solid fa-plus"></i> ساخت تم جدید</a>
    </div>

    <table class="theme-table">
        <thead>
            <tr>
                <th>نام تم</th>
                <th>شرط نمایش</th>
                <th>کش</th>
                <th>آخرین ویرایش</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($pages)): ?>
            <tr><td colspan="5" class="theme-empty">
                <i class="fa-solid fa-inbox"></i>
                هنوز تمی ساخته نشده. روی «ساخت تم جدید» کلیک کنید.
            </td></tr>
        <?php else: foreach ($pages as $p): ?>
            <tr>
                <td class="tt-name"><?= htmlspecialchars($p['name'] ?: ('تم #' . $p['id'])) ?></td>
                <td><span class="tt-badge"><?= builder_condition_label($p) ?></span></td>
                <td><?= $p['has_cache'] ? '<span class="tt-cache-on"><i class="fa-solid fa-circle-check"></i> فعال</span>' : '<span class="tt-cache-off">غیرفعال</span>' ?></td>
                <td style="color:#888;font-size:13px;"><?= $p['updated_at'] ?></td>
                <td class="tt-actions">
                    <a href="<?= BASE_URL ?>mod/builder/delete/<?= $p['id'] ?>" class="del" onclick="return confirm('آیا از حذف این قالب مطمئنید؟ این عمل غیرقابل بازگشت است.');"><i class="fa-solid fa-trash"></i> حذف</a>
                    <a href="<?= BASE_URL ?>mod/builder/edit/<?= $p['id'] ?>" class="edit"><i class="fa-solid fa-pen-to-square"></i> ویرایش</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function builder_template_new() {
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <h3>ساخت تم جدید (تم‌بلدر)</h3>
    <p style="color:#888;margin-bottom:16px;">شرط نمایش تعیین می‌کند این تم کجا اعمال شود.</p>
    <form method="post" action="<?= BASE_URL ?>mod/builder/create" style="max-width:600px;">
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">نام تم</label>
            <input type="text" name="name" required style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="مثلاً: قالب آرشیو وبلاگ">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">بخش تم</label>
            <select name="part" id="partSel" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" onchange="toggleCondType()">
                <option value="">قالب محتوا (عمومی)</option>
                <option value="header">هدر (Header)</option>
                <option value="footer">پانویس (Footer)</option>
                <option value="single">قالب صفحه تکی</option>
                <option value="archive">قالب آرشیو</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">نوع شرط نمایش</label>
            <select name="condition_type" id="condType" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" onchange="toggleCondVal()">
                <option value="archive">آرشیو (لیست مطالب/محصولات)</option>
                <option value="single">صفحه تکی (مطلب/محصول خاص)</option>
                <option value="global">سراسری (همه صفحات)</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:16px;" id="condValWrap">
            <label style="display:block;margin-bottom:4px;font-weight:600;">مقدار شرط</label>
            <select name="condition_value" id="condVal" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                <option value="blog">آرشیو وبلاگ (تارنگار)</option>
                <option value="product">آرشیو محصولات</option>
                <option value="khadamat">آرشیو خدمات</option>
            </select>
        </div>
        <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-check"></i> ایجاد و رفتن به صفحه‌ساز</button>
    </form>
    <script>
        function toggleCondType() {
            var p = document.getElementById('partSel').value;
            var condType = document.getElementById('condType');
            if (p === 'header' || p === 'footer') {
                if (condType.value === 'archive') condType.value = 'global';
            }
            toggleCondVal();
        }
        function toggleCondVal() {
            var t = document.getElementById('condType').value;
            var wrap = document.getElementById('condValWrap');
            var sel = document.getElementById('condVal');
            if (t === 'archive') {
                wrap.style.display = '';
                sel.innerHTML = '<option value="blog">آرشیو وبلاگ (تارنگار)</option><option value="product">آرشیو محصولات</option><option value="khadamat">آرشیو خدمات</option>';
            } else if (t === 'single') {
                wrap.style.display = '';
                sel.innerHTML = '<option value="post">مطلب تکی</option><option value="product">محصول تکی</option><option value="khadamat">خدمت تکی</option><option value="safhe">برگه تکی</option><option value="home">صفحه اصلی</option>';
            } else {
                wrap.style.display = 'none';
            }
        }
        toggleCondVal();
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function builder_template_create() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('mod/builder/pages'); return; }
    $name = $_POST['name'] ?? 'تم جدید';
    $part = $_POST['part'] ?? '';
    $ctype = $_POST['condition_type'] ?? 'archive';
    $cval = $ctype === 'global' ? '*' : ($_POST['condition_value'] ?? '');
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("INSERT INTO block_pages (page_id, page_type, name, part, condition_type, condition_value, blocks_data) VALUES (0, 'template', ?, ?, ?, ?, '[]')");
    $stmt->bind_param("sssss", $name, $part, $ctype, $cval);
    $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    redirect('mod/builder/edit/' . $new_id);
}

function builder_template_save_condition() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('mod/builder/pages'); return; }
    $id = (int)($_POST['bp_id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $part = $_POST['part'] ?? '';
    $ctype = $_POST['condition_type'] ?? 'archive';
    $cval = $ctype === 'global' ? '*' : ($_POST['condition_value'] ?? '');
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("UPDATE block_pages SET name=?, part=?, condition_type=?, condition_value=?, cached_html=NULL, cache_updated=NULL WHERE id=?");
    $stmt->bind_param("ssssi", $name, $part, $ctype, $cval, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    redirect('mod/builder/edit/' . $id);
}

function builder_template_delete($id = 0) {
    $id = (int)($id ?: ($_GET['id'] ?? ($_POST['id'] ?? 0)));
    if (!$id) { redirect('mod/builder/pages'); return; }
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM block_pages WHERE id = ? AND page_id = 0");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    redirect('mod/builder/pages');
}

/**
 * بررسی می‌کند موجودیت (پست/محصول/خدمت) محتوای HTML قدیمی دارد یا نه
 * اگه دارد، به صورت یک بلاک custom برمی‌گرداند تا در صفحه‌ساز نمایش داده شود
 */
function builder_import_content_to_blocks($type, $id) {
    $map = [
        'blog'     => ['posts', 'content'],
        'maghaleh' => ['posts', 'content'],
        'safhe'    => ['posts', 'content'],
        'page'     => ['posts', 'content'],
        'mahsul'   => ['mahsulat', 'content'],
        'khadamat' => ['posts', 'content'],
    ];
    if (!isset($map[$type])) return null;
    list($table, $col) = $map[$type];

    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT $col FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$row) return null;
    $content = trim($row[$col] ?? '');
    // اگه محتوای واقعی نداره (فقط تگ خالی یا اسپیس)
    $clean = trim(str_replace(['&nbsp;', "\n", "\r", ' '], '', strip_tags($content)));
    if (empty($clean)) return null;

    return json_encode([['type' => 'custom', 'data' => ['html' => $content]]], JSON_UNESCAPED_UNICODE);
}

function builder_page_edit_post($type, $id) {
    $id = (int)$id;
    if (!$id || !$type) {
        include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
        echo "<h3>پارامتر نامعتبر</h3>";
        include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
        return;
    }
    $bank = new Bank();
    $conn = $bank->getConnection();

    // بررسی وجود موجودیت در جدول مربوطه
    $found = false;
    if (in_array($type, ['blog', 'maghaleh', 'safhe', 'page', 'khadamat'], true)) {
        $stmt = $conn->prepare("SELECT id FROM posts WHERE id = ? AND type = ?");
        $stmt->bind_param("is", $id, $type);
    } elseif ($type === 'mahsul') {
        $stmt = $conn->prepare("SELECT id FROM mahsulat WHERE id = ?");
        $stmt->bind_param("i", $id);
    } else {
        $conn->close();
        include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
        echo "<h3>نوع نامشخص</h3>";
        include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
        return;
    }
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$found) {
        $conn->close();
        include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
        echo "<h3>مورد پیدا نشد</h3>";
        include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
        return;
    }

    $stmt = $conn->prepare("SELECT id, blocks_data FROM block_pages WHERE page_id = ? AND page_type = ?");
    $stmt->bind_param("is", $id, $type);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $need_import = false;
    if (!$row) {
        $name = "محتوای " . $type . " #" . $id;
        // تلاش برای دریافت محتوای قدیمی
        $blocks_data = '[]';
        $imported = builder_import_content_to_blocks($type, $id);
        if ($imported !== null) {
            $blocks_data = $imported;
            $need_import = true;
        }
        $stmt = $conn->prepare("INSERT INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data) VALUES (?, ?, ?, 'single', ?, ?)");
        $stmt->bind_param("issss", $id, $type, $name, $type, $blocks_data);
        $stmt->execute();
        $bp_id = $stmt->insert_id;
        $stmt->close();
    } else {
        $bp_id = $row['id'];
        // blocks_data خالیه ولی محتوای قدیمی هست؟ import کن
        $empty = empty($row['blocks_data']) || $row['blocks_data'] === '[]' || $row['blocks_data'] === '{}';
        if ($empty) {
            $imported = builder_import_content_to_blocks($type, $id);
            if ($imported !== null) {
                $stmt = $conn->prepare("UPDATE block_pages SET blocks_data = ? WHERE id = ?");
                $stmt->bind_param("si", $imported, $bp_id);
                $stmt->execute();
                $stmt->close();
                $need_import = true;
            }
        }
    }
    $conn->close();
    builder_page_edit($bp_id);
}

function builder_page_edit($block_page_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $bp = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$bp) {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("SELECT id, title, content, type FROM posts WHERE id = ? AND (type = 'safhe' OR type = 'page')");
        $stmt->bind_param("i", $block_page_id);
        $stmt->execute();
        $post = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($post) {
            $blocks_data = '[]';
            $imported = builder_import_content_to_blocks($post['type'], $post['id']);
            if ($imported !== null) {
                $blocks_data = $imported;
            }
            // رکورد واقعی بساز در block_pages
            $name = $post['title'] ?: ("صفحه #" . $post['id']);
            $stmt = $conn->prepare("INSERT INTO block_pages (page_id, page_type, name, condition_type, condition_value, part, blocks_data, position_mode, mobile_mode) VALUES (?, ?, ?, 'single', ?, '', ?, 0, 'auto')");
            $stmt->bind_param("issss", $post['id'], $post['type'], $name, $post['type'], $blocks_data);
            $stmt->execute();
            $new_bp_id = $stmt->insert_id;
            $stmt->close();
            $conn->close();

            // بازخوانی رکورد کامل
            $bank2 = new Bank();
            $conn2 = $bank2->getConnection();
            $stmt2 = $conn2->prepare("SELECT * FROM block_pages WHERE id = ?");
            $stmt2->bind_param("i", $new_bp_id);
            $stmt2->execute();
            $bp = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
            $conn2->close();
        } else {
            $conn->close();
            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            echo "<h3>صفحه یافت نشد</h3>";
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            return;
        }
    }

    $blocks = json_decode($bp['blocks_data'], true) ?: [];
    $available_blocks = get_block_types();

    // لیست همه قالب‌ها برای منوی سوییچ سریع
    $bank2 = new Bank();
    $conn2 = $bank2->getConnection();
    $res2 = $conn2->query("SELECT id, name, part, condition_type, condition_value FROM block_pages WHERE page_id = 0 ORDER BY condition_type, name");
    $all_themes = $res2 ? $res2->fetch_all(MYSQLI_ASSOC) : [];
    $conn2->close();

    // تنظیمات صفحه‌ساز (ذخیره خودکار و ...)
    if (!function_exists('builder_get_settings_value')) { /* همین فایل است */ }
    $pbset = builder_get_settings_value();

    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        /* ===== صفحه‌ساز جدید (سبک المنتور) ===== */
        :root { --rang-asli:#FF6F00; --rang-tira:#E65100; --rang-roshan:#fff3e0; --rang-border:#e9ecef; --rang-sabz:#f8f9fa; }
        * { box-sizing:border-box; }
        .pb-topbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; background:#fff; border:1px solid var(--rang-border); border-radius:12px; padding:8px 14px; margin-bottom:12px; position:sticky; top:6px; z-index:500; box-shadow:0 2px 10px rgba(0,0,0,.04); }
        .pb-topbar a.pb-back { color:#666; font-size:13px; text-decoration:none; white-space:nowrap; }
        .pb-topbar a.pb-back:hover { color:var(--rang-asli); }
        .pb-topbar .pb-title { font-weight:700; font-size:13px; color:#333; white-space:nowrap; }
        .pb-topbar select { padding:6px 10px; border:1.5px solid #dde1e6; border-radius:8px; font-size:12px; max-width:220px; }
        .pb-seg { display:flex; gap:3px; background:#f5f6f8; padding:3px; border-radius:8px; }
        .pb-seg button { border:none; background:none; padding:5px 9px; border-radius:6px; cursor:pointer; font-size:11px; color:#555; transition:all .15s; white-space:nowrap; font-family:inherit; }
        .pb-seg button:hover { background:#e9ecef; }
        .pb-seg button.active { background:var(--rang-asli); color:#fff; }
        .pb-spacer { flex:1; }
        .pb-status { display:flex; align-items:center; gap:6px; font-size:12px; color:#777; white-space:nowrap; }
        .pb-status .dot { width:9px; height:9px; border-radius:50%; background:#00B894; transition:background .2s; }
        .pb-status.dirty .dot { background:#e17055; }
        .pb-status.saving .dot { background:#f39c12; animation:pulse 1s infinite; }
        @keyframes pulse { 50% { opacity:.35; } }

        .pb-app { display:flex; align-items:stretch; min-height:calc(100vh - 140px); gap:0; }
        .pb-panel { width:var(--panel-w,300px); min-width:200px; max-width:520px; background:#fff; border:1px solid var(--rang-border); border-radius:12px; display:flex; flex-direction:column; overflow:hidden; order:0; }
        .pb-tabs { display:flex; background:#f8f9fa; border-bottom:1px solid var(--rang-border); }
        .pb-tabs button { flex:1; border:none; background:none; padding:11px 6px; cursor:pointer; font-size:12.5px; font-weight:700; color:#666; font-family:inherit; border-bottom:2.5px solid transparent; }
        .pb-tabs button.active { color:var(--rang-asli); border-bottom-color:var(--rang-asli); background:#fff; }
        .pb-body { flex:1; overflow-y:auto; overflow-x:hidden; }
        .pb-tabpane { display:none; padding:12px; }
        .pb-tabpane.active { display:block; }
        .pb-palette { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; }
        .pb-block-card { display:flex; flex-direction:column; align-items:center; gap:6px; padding:12px 6px 10px; background:#fafbfc; border:1.5px solid var(--rang-border); border-radius:10px; cursor:grab; user-select:none; text-align:center; transition:all .15s; font-size:11.5px; color:#555; font-family:inherit; }
        .pb-block-card:hover { border-color:var(--asli,var(--rang-asli)); background:#fff; box-shadow:0 3px 12px rgba(255,111,0,.12); transform:translateY(-1px); }
        .pb-block-card:active { cursor:grabbing; }
        .pb-block-card .ic { width:34px; height:34px; border-radius:9px; color:#fff; display:flex; align-items:center; justify-content:center; font-size:15px; background:var(--asli,#888); }
        .pb-hint { margin-top:12px; font-size:11px; color:#999; line-height:1.9; background:#f8f9fa; border-radius:8px; padding:8px 10px; }
        .pb-hint b { color:var(--rang-asli); }

        /* پنل تنظیمات بلاک */
        #pbInspector .empty { text-align:center; color:#aaa; padding:40px 10px; font-size:12.5px; line-height:2; }
        #pbInspector .empty i { font-size:34px; display:block; margin-bottom:10px; opacity:.4; }
        .pb-field { margin-bottom:14px; }
        .pb-field > label { display:block; margin-bottom:5px; font-weight:700; font-size:12px; color:#555; }
        .pb-field input[type=text], .pb-field input[type=number], .pb-field input[type=url], .pb-field select, .pb-field textarea { width:100%; padding:9px 10px; border:1.5px solid #dde1e6; border-radius:8px; font-size:12.5px; font-family:inherit; }
        .pb-field textarea { min-height:110px; resize:vertical; }
        .pb-field textarea.mono { font-family:Consolas,monospace; font-size:11.5px; min-height:150px; direction:ltr; text-align:left; }
        .pb-field .row { display:flex; gap:6px; }
        .pb-pos-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; margin-bottom:10px; }
        .pb-pos-grid label { font-size:10px; color:#999; display:block; margin-bottom:2px; }
        .pb-pos-grid input { width:100%; padding:6px; border:1.5px solid #dde1e6; border-radius:6px; font-size:12px; font-family:monospace; }
        .pb-btn { padding:9px 16px; border:none; border-radius:8px; cursor:pointer; font-weight:700; font-size:12.5px; font-family:inherit; transition:all .15s; }
        .pb-btn.asli { background:var(--rang-asli); color:#fff; }
        .pb-btn.asli:hover { background:var(--rang-tira); }
        .pb-btn.khali { background:#f5f6f8; border:1px solid #dde1e6; color:#555; }
        .pb-btn.danger { background:#fff0f0; color:#c62828; border:1px solid #ef9a9a; }
        .pb-btn.wide { width:100%; }
        .pb-sep { border:none; border-top:1px dashed var(--rang-border); margin:14px 0; }

        /* نوار ذخیره پایین پنل */
        .pb-savebar { border-top:1px solid var(--rang-border); padding:10px 12px; display:flex; align-items:center; gap:8px; background:#fafbfc; }
        .pb-savebar .pb-save-btn { flex:1; padding:11px; background:var(--rang-asli); color:#fff; border:none; border-radius:9px; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit; transition:all .2s; }
        .pb-savebar .pb-save-btn:hover { background:var(--rang-tira); box-shadow:0 4px 14px rgba(255,111,0,.35); }
        .pb-savebar .pb-save-btn:disabled { opacity:.55; cursor:wait; }

        /* جداکننده و صحنه */
        .pb-splitter { width:8px; cursor:col-resize; position:relative; flex-shrink:0; }
        .pb-splitter::after { content:''; position:absolute; top:50%; right:50%; transform:translate(50%,-50%); width:4px; height:44px; border-radius:3px; background:#dde1e6; transition:background .15s; }
        .pb-splitter:hover::after, .pb-splitter.dragging::after { background:var(--rang-asli); }
        .pb-stage { flex:1; min-width:0; background:#eef0f4; border-radius:12px; border:1px solid var(--rang-border); display:flex; flex-direction:column; overflow:hidden; }
        .pb-frame-wrap { flex:1; overflow:auto; display:flex; justify-content:center; align-items:flex-start; padding:18px; }
        .pb-frame-scale { transition:max-width .25s ease; max-width:100%; width:100%; transform-origin:top center; }
        .pb-frame-scale iframe { width:100%; height:min(78vh, 900px); border:none; background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,.09); }
        .pb-empty-overlay { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; }
        .pb-empty-overlay div { background:rgba(38,38,42,.85); color:#fff; font-size:13px; padding:12px 22px; border-radius:10px; }
        .pb-stage { position:relative; }

        /* مودال انتخاب تصویر */
        .pb-modal-bg { position:fixed; inset:0; background:rgba(20,20,24,.45); z-index:9000; display:none; align-items:center; justify-content:center; }
        .pb-modal-bg.open { display:flex; }
        .pb-modal { background:#fff; border-radius:14px; width:min(720px,92vw); max-height:86vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3); }
        .pb-modal header { display:flex; align-items:center; gap:10px; padding:14px 18px; border-bottom:1px solid var(--rang-border); font-weight:700; font-size:14px; }
        .pb-modal header button { margin-right:auto; border:none; background:#f5f6f8; border-radius:7px; padding:6px 12px; cursor:pointer; }
        .pb-modal .tabs { display:flex; gap:4px; padding:10px 18px 0; }
        .pb-modal .tabs button { border:none; background:#f5f6f8; padding:8px 16px; border-radius:8px 8px 0 0; cursor:pointer; font-size:12.5px; font-family:inherit; color:#666; }
        .pb-modal .tabs button.active { background:var(--rang-roshan); color:var(--rang-tira); font-weight:700; }
        .pb-modal .content { padding:16px 18px; overflow-y:auto; }
        .pb-img-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:10px; }
        .pb-img-grid img { width:100%; height:80px; object-fit:cover; border-radius:8px; cursor:pointer; border:2.5px solid transparent; transition:all .15s; }
        .pb-img-grid img:hover { border-color:var(--rang-asli); transform:scale(1.03); }
        .pb-url-row { display:flex; gap:8px; }
        .pb-url-row input { flex:1; padding:9px 10px; border:1.5px solid #dde1e6; border-radius:8px; font-size:12.5px; direction:ltr; text-align:left; }
        .pb-drop-zone { border:2.5px dashed #cfd6dd; border-radius:10px; padding:26px; text-align:center; color:#888; font-size:12.5px; cursor:pointer; transition:all .15s; }
        .pb-drop-zone.over { border-color:var(--rang-asli); background:var(--rang-roshan); }
        .pb-muted { color:#999; font-size:11.5px; line-height:1.9; }
        body.pb-pal-drag { user-select:none; -webkit-user-select:none; }
    </style>

    <div class="pb-topbar">
        <a class="pb-back" href="<?= BASE_URL ?>mod/builder/pages"><i class="fa-solid fa-arrow-right"></i> قالب‌ها</a>
        <span class="pb-title"><?= htmlspecialchars($bp['name'] ?: ('صفحه‌ساز #' . $bp['id'])) ?></span>
        <select id="pbThemeSwitch" onchange="if(this.value)location.href='<?= BASE_URL ?>mod/builder/edit/'+this.value">
            <?php foreach ($all_themes as $th): ?>
            <option value="<?= $th['id'] ?>" <?= ($th['id'] == $bp['id']) ? 'selected' : '' ?>><?= htmlspecialchars($th['name'] ?: ('تم #' . $th['id'])) ?> — <?= builder_condition_label($th) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="pb-seg" id="pbDeviceSeg">
            <?php foreach (builder_devices() as $dk => $dv): ?>
            <button type="button" data-dev="<?= $dk ?>" class="<?= $dk === 'desktop' ? 'active' : '' ?>" title="<?= $dv['label'] ?> (<?= $dv['w'] ?>px)"><?= $dv['label'] ?></button>
            <?php endforeach; ?>
        </div>
        <div class="pb-seg" id="pbMobileSeg">
            <button type="button" data-mm="auto" class="<?= ($bp['mobile_mode'] ?? 'auto') === 'auto' ? 'active' : '' ?>">موبایل: خودکار</button>
            <button type="button" data-mm="exact" class="<?= ($bp['mobile_mode'] ?? 'auto') === 'exact' ? 'active' : '' ?>">دقیق</button>
        </div>
        <span class="pb-spacer"></span>
        <span class="pb-status saved" id="pbStatus"><span class="dot"></span><span id="pbStatusText">ذخیره شده</span></span>
        <a class="pb-back" href="<?= BASE_URL ?>mod/builder/preview/<?= $bp['id'] ?>" target="_blank" title="باز کردن پیش‌نمایش واقعی"><i class="fa-solid fa-up-right-from-square"></i></a>
    </div>

    <div class="pb-app" id="pbApp">
        <!-- ================= پنل چپ ================= -->
        <aside class="pb-panel" id="pbPanel">
            <div class="pb-tabs">
                <button type="button" data-tab="blocks" class="active">بلاک‌ها</button>
                <button type="button" data-tab="inspector">تنظیمات بلاک</button>
                <button type="button" data-tab="gear" title="تنظیمات صفحه‌ساز"><i class="fa-solid fa-gear"></i></button>
            </div>
            <div class="pb-body">
                <div class="pb-tabpane active" id="pbTabBlocks">
                    <div class="pb-palette" id="pbPalette">
                        <?php foreach ($available_blocks as $bk => $bv): ?>
                        <button type="button" class="pb-block-card" data-btype="<?= $bk ?>" style="--asli:<?= $bv['color'] ?>" title="<?= htmlspecialchars($bv['desc']) ?>">
                            <span class="ic"><i class="fa-solid <?= $bv['icon'] ?>"></i></span><?= htmlspecialchars($bv['label']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="pb-hint">
                        <b>درگ خطی:</b> بلاک را بکشید و داخل پیش‌نمایش — خط سبز محل قرارگیری را نشان می‌دهد.<br>
                        <b>درگ آزاد:</b> هنگام کشیدن کلید <b>Alt</b> را نگه دارید؛ بلاک دقیقاً زیر موس رها می‌شود.<br>
                        <b>کلیک ساده</b> روی کارت = افزودن به انتهای صفحه.<br>
                        <b>ویرایش متن:</b> دابل‌کلیک روی هر متن داخل پیش‌نمایش.<br>
                        <b>حذف:</b> انتخاب بلاک → دکمه حذف یا کلید Delete.
                    </div>
                </div>
                <div class="pb-tabpane" id="pbTabInspector">
                    <div id="pbInspector"><div class="empty"><i class="fa-solid fa-cube"></i>بلاکی انتخاب نشده است.<br>روی یک بلاک در پیش‌نمایش کلیک کنید.</div></div>
                </div>
                <div class="pb-tabpane" id="pbTabGear">
                    <h4 style="margin:4px 0 12px;font-size:13.5px;"><i class="fa-solid fa-robot"></i> ذخیره خودکار</h4>
                    <div class="pb-field">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
                            <input type="checkbox" id="pbAutoEnabled" <?= !empty($pbset['autosave_enabled']) ? 'checked' : '' ?>> فعال باشد
                        </label>
                    </div>
                    <div class="pb-field">
                        <label>فاصله ذخیره خودکار (دقیقه)</label>
                        <input type="number" id="pbAutoMin" min="1" max="120" value="<?= (int)($pbset['autosave_min'] ?? 10) ?>" style="width:100px;">
                        <p class="pb-muted">فقط داده خام ذخیره می‌شود؛ بدون رندر مجدد — روی سرعت هیچ اثری ندارد.</p>
                    </div>
                    <button type="button" class="pb-btn asli wide" onclick="pbSaveSettings()">ذخیره تنظیمات</button>
                    <hr class="pb-sep">
                    <h4 style="margin:4px 0 12px;font-size:13.5px;"><i class="fa-solid fa-broom"></i> نگهداری</h4>
                    <button type="button" class="pb-btn khali wide" onclick="pbClearCache()"><i class="fa-solid fa-trash-can"></i> پاک کردن کش رندر این صفحه</button>
                    <?php if (empty($bp['page_id'])): ?>
                    <hr class="pb-sep">
                    <h4 style="margin:4px 0 12px;font-size:13.5px;"><i class="fa-solid fa-filter"></i> شرط نمایش این تم</h4>
                    <form method="post" action="<?= BASE_URL ?>mod/builder/save_condition">
                        <input type="hidden" name="bp_id" value="<?= $bp['id'] ?>">
                        <div class="pb-field"><label>نام تم</label><input type="text" name="name" value="<?= htmlspecialchars($bp['name'] ?? '') ?>"></div>
                        <div class="pb-field"><label>بخش تم</label>
                            <select name="part">
                                <option value="" <?= empty($bp['part']) ? 'selected' : '' ?>>قالب محتوا (عمومی)</option>
                                <option value="header" <?= ($bp['part'] ?? '') === 'header' ? 'selected' : '' ?>>هدر (Header)</option>
                                <option value="footer" <?= ($bp['part'] ?? '') === 'footer' ? 'selected' : '' ?>>پانویس (Footer)</option>
                                <option value="single" <?= ($bp['part'] ?? '') === 'single' ? 'selected' : '' ?>>قالب صفحه تکی</option>
                                <option value="archive" <?= ($bp['part'] ?? '') === 'archive' ? 'selected' : '' ?>>قالب آرشیو</option>
                            </select>
                        </div>
                        <div class="pb-field"><label>نوع شرط</label>
                            <select name="condition_type">
                                <option value="archive" <?= ($bp['condition_type'] ?? 'single') === 'archive' ? 'selected' : '' ?>>آرشیو</option>
                                <option value="single" <?= ($bp['condition_type'] ?? 'single') === 'single' ? 'selected' : '' ?>>صفحه تکی</option>
                                <option value="global" <?= ($bp['condition_type'] ?? 'single') === 'global' ? 'selected' : '' ?>>سراسری</option>
                            </select>
                        </div>
                        <div class="pb-field"><label>مقدار شرط</label><input type="text" name="condition_value" value="<?= htmlspecialchars($bp['condition_value'] ?? '') ?>" placeholder="blog / product / * یا slug خاص"></div>
                        <p class="pb-muted">آرشیو: blog ، product ، khadamat &nbsp;|&nbsp; تکی: post ، safhe یا slug &nbsp;|&nbsp; سراسری: *</p>
                        <button type="submit" class="pb-btn asli wide">ذخیره شرط نمایش</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="pb-savebar">
                <button class="pb-save-btn" id="pbSaveBtn" onclick="pbFullSave()"><i class="fa-solid fa-save"></i> ذخیره تغییرات</button>
            </div>
        </aside>

        <div class="pb-splitter" id="pbSplitter" title="کشیدن برای تغییر عرض پنل"></div>

        <!-- ================= پیشنمایش زنده ================= -->
        <main class="pb-stage" id="pbStage">
            <div class="pb-frame-wrap" id="pbFrameWrap">
                <div class="pb-frame-scale" id="pbFrameScale">
                    <iframe id="previewFrame" src="<?= BASE_URL ?>mod/builder/preview/<?= $bp['id'] ?>?edit=1" frameborder="0"></iframe>
                </div>
            </div>
            <div class="pb-empty-overlay" id="pbEmptyOverlay" style="display:none;"><div><i class="fa-solid fa-hand-pointer"></i> از پنل کنار، یک بلاک را بکشید و اینجا رها کنید</div></div>
        </main>
    </div>

    <!-- مودال انتخاب تصویر -->
    <div class="pb-modal-bg" id="pbImgModalBg">
        <div class="pb-modal">
            <header><i class="fa-solid fa-image" style="color:var(--rang-asli)"></i> انتخاب تصویر <button type="button" onclick="pbCloseImageModal()">✕</button></header>
            <div class="tabs">
                <button type="button" data-mtab="lib" class="active">کتابخانه</button>
                <button type="button" data-mtab="up">آپلود مستقیم</button>
                <button type="button" data-mtab="url">آدرس URL</button>
            </div>
            <div class="content">
                <div id="pbMTabLib">
                    <div class="pb-img-grid" id="pbLibGrid"><p class="pb-muted">در حال بارگذاری…</p></div>
                </div>
                <div id="pbMTabUp" style="display:none;">
                    <div class="pb-drop-zone" id="pbDropZone"><i class="fa-solid fa-cloud-arrow-up" style="font-size:26px;display:block;margin-bottom:8px;"></i>فایل تصویر را اینجا رها کنید یا کلیک کنید<br><span class="pb-muted">JPG / PNG / WebP / GIF / SVG — پس از آپلود، خودکار انتخاب می‌شود</span></div>
                    <input type="file" id="pbUpInput" accept="image/*" style="display:none;">
                </div>
                <div id="pbMTabUrl" style="display:none;">
                    <div class="pb-url-row"><input type="url" id="pbUrlInput" placeholder="https://…"><button type="button" class="pb-btn asli" onclick="pbPickUrl()">انتخاب</button></div>
                    <p class="pb-muted" style="margin-top:8px;">برای تصاویر خارجی؛ بهتر است تصاویر سایتتان را در کتابخانه داشته باشید.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>manabe/js/sadastEditor.js"></script>
    <script>
    /* ============================================================
       صفحه‌ساز زنده — نسخه بازطراحی‌شده (سبک المنتور)
       پیشنمایش = محل ویرایش | پنل چپ = پالت + تنظیمات
       ============================================================ */
    var blocksData   = <?= json_encode($blocks, JSON_UNESCAPED_UNICODE) ?>;
    var blockTypes   = <?= json_encode($available_blocks, JSON_UNESCAPED_UNICODE) ?>;
    var devices      = <?= json_encode(builder_devices(), JSON_UNESCAPED_UNICODE) ?>;
    var BP_ID        = <?= (int)$bp['id'] ?>;
    var LEGACY_FREE  = <?= !empty($bp['position_mode']) ? 'true' : 'false' ?>;
    var mobileMode   = '<?= $bp['mobile_mode'] ?? 'auto' ?>';
    var AUTO_CFG     = { enabled: <?= !empty($pbset['autosave_enabled']) ? 1 : 0 ?>, min: <?= (int)($pbset['autosave_min'] ?? 10) ?> };
    var MEDIA = { wide:null, desktop:'@media(max-width:1599px)', tablet:'@media(max-width:1199px)', mobile:'@media(max-width:767px)' };
    var BP_ORDER = ['wide','desktop','tablet','mobile'];
    var currentDevice = 'desktop', currentBP = 'desktop';
    var selectedIdx = -1;
    var isDirty = false, saving = false;
    var _lastChange = 0, _autoTick = null, _fieldDebounce = null;

    function frameWin()  { var f = document.getElementById('previewFrame'); return f && f.contentWindow ? f.contentWindow : null; }
    function toFrame(m)  { var w = frameWin(); if (w) { m._ns = 'builderInline'; try { w.postMessage(m, window.location.origin); } catch(e){} } }
    function setStatus(mode, txt) {
        var el = document.getElementById('pbStatus');
        el.className = 'pb-status ' + mode;
        document.getElementById('pbStatusText').textContent = txt;
    }
    function markDirty() {
        isDirty = true; _lastChange = Date.now();
        if (!saving) setStatus('dirty', 'تغییرات ذخیره‌نشده');
    }
    function markSaved(auto) {
        isDirty = false;
        setStatus('saved', auto ? 'ذخیره خودکار شد ✓' : 'ذخیره شد ✓');
    }

    /* ---------- positions helpers (آینه منطق سمت سرور) ---------- */
    function mergePos(a, b) { var o = {}; var k; for (k in a) o[k] = a[k]; for (k in b) o[k] = b[k]; return o; }
    function effectivePos(block) {
        var pos = block.pos || {}, prio, v = null, i, j;
        var ti = BP_ORDER.indexOf(currentBP);
        prio = [currentBP];
        for (j = ti - 1; j >= 0; j--) prio.push(BP_ORDER[j]);
        for (j = ti + 1; j < 4; j++) prio.push(BP_ORDER[j]);
        for (i = 0; i < prio.length; i++) { if (pos[prio[i]]) { v = pos[prio[i]]; break; } }
        return v ? mergePos({x:0,y:0,w:300,z:1}, v) : null;
    }
    function buildPosCss() {
        var css = '';
        blocksData.forEach(function(b, i) {
            if (!LEGACY_FREE && !(b.free)) return;
            var hasT = !!(b.pos && b.pos.tablet), hasM = !!(b.pos && b.pos.mobile);
            BP_ORDER.forEach(function(bp) {
                var p = effectivePosAt(b, bp);
                if (!p) return;
                var rule;
                if (mobileMode === 'auto' && ((bp === 'mobile' && !hasM) || (bp === 'tablet' && !hasT))) {
                    rule = '.bpos-' + i + '{position:relative!important;width:auto!important;margin-bottom:16px;left:auto!important;top:auto!important;z-index:auto!important;}';
                } else {
                    rule = '.bpos-' + i + '{position:absolute;left:' + Math.round(p.x) + 'px;top:' + Math.round(p.y) + 'px;width:' + Math.round(p.w) + 'px;z-index:' + (p.z || 1) + ';}';
                }
                css += MEDIA[bp] ? (MEDIA[bp] + '{' + rule + '}') : rule;
            });
        });
        return css ? '<style class="builder-pos-css">' + css + '</style>' : '';
    }
    function effectivePosAt(block, bp) {
        var pos = block.pos || {}, prio, v = null, i, j;
        var ti = BP_ORDER.indexOf(bp);
        prio = [bp];
        for (j = ti - 1; j >= 0; j--) prio.push(BP_ORDER[j]);
        for (j = ti + 1; j < 4; j++) prio.push(BP_ORDER[j]);
        for (i = 0; i < prio.length; i++) { if (pos[prio[i]]) { v = pos[prio[i]]; break; } }
        return v ? mergePos({x:0,y:0,w:300,z:1}, v) : null;
    }
    function pushPosCss() { toFrame({ type:'builderApplyPosCss', css: buildPosCss() }); updateEmptyOverlay(); }

    /* ---------- ذخیره ---------- */
    function savePayload(light) {
        var fd = new FormData();
        fd.append('block_page_id', BP_ID);
        fd.append('blocks_data', JSON.stringify(blocksData));
        fd.append('light', light ? '1' : '');
        fd.append('position_mode', LEGACY_FREE ? '1' : '0');
        fd.append('mobile_mode', mobileMode);
        return fd;
    }
    function fetchTimeout(url, opts, ms) {
        opts = opts || {}; ms = ms || 45000;
        var ctrl = new AbortController();
        var t = setTimeout(function(){ ctrl.abort(); }, ms);
        opts.signal = ctrl.signal;
        return fetch(url, opts).finally(function(){ clearTimeout(t); });
    }
    function lightSave(reason) {
        if (saving) return;
        saving = true; setStatus('saving', 'ذخیره خودکار…');
        fetchTimeout('<?= BASE_URL ?>mod/builder/save', { method:'POST', body: savePayload(true) })
        .then(function(r){ return r.json(); })
        .then(function(res){
            saving = false;
            if (res.success) markSaved(true); else setStatus('dirty', 'خطا در ذخیره!');
        })
        .catch(function(){ saving = false; setStatus('dirty', 'خطا در اتصال!'); });
    }
    function pbFullSave() {
        if (saving) return;
        saving = true;
        var btn = document.getElementById('pbSaveBtn');
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال ذخیره…';
        setStatus('saving', 'در حال ذخیره کامل…');
        fetchTimeout('<?= BASE_URL ?>mod/builder/save', { method:'POST', body: savePayload(false) }, 90000)
        .then(function(r){ return r.json(); })
        .then(function(res){
            saving = false; btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات';
            if (res.success) markSaved(false); else setStatus('dirty', 'خطا در ذخیره!');
        })
        .catch(function(){
            saving = false; btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات';
            setStatus('dirty', 'خطا در اتصال!');
        });
    }
    function scheduleAutosaveCheck() {
        if (_autoTick) clearInterval(_autoTick);
        _autoTick = setInterval(function() {
            if (!AUTO_CFG.enabled || !isDirty || saving) return;
            if (Date.now() - _lastChange >= AUTO_CFG.min * 60000) lightSave();
        }, 20000);
    }
    function pbSaveSettings() {
        var fd = new FormData();
        fd.append('autosave_enabled', document.getElementById('pbAutoEnabled').checked ? '1' : '');
        fd.append('autosave_min', document.getElementById('pbAutoMin').value || '10');
        fetchTimeout('<?= BASE_URL ?>mod/builder/save_settings', { method:'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res){
            if (res.success) {
                AUTO_CFG.enabled = document.getElementById('pbAutoEnabled').checked;
                AUTO_CFG.min = parseInt(document.getElementById('pbAutoMin').value, 10) || 10;
                scheduleAutosaveCheck();
                setStatus('saved', 'تنظیمات ذخیره شد ✓');
                setTimeout(function(){ setStatus(isDirty ? 'dirty':'saved', isDirty ? 'تغییرات ذخیره‌نشده':'ذخیره شده'); }, 1600);
            }
        });
    }
    function pbClearCache() {
        fetchTimeout('<?= BASE_URL ?>mod/builder/clear_cache/' + BP_ID, { method:'POST' })
        .then(function(r){ return r.json(); })
        .then(function(res){ if (res.success) { setStatus('saved','کش پاک شد ✓'); setTimeout(function(){ setStatus(isDirty?'dirty':'saved', isDirty?'تغییرات ذخیره‌نشده':'ذخیره شده'); },1500);} });
    }

    /* ---------- رندر تک‌بلاک (برای بازخورد زنده فرم) ---------- */
    function apiRenderOne(block) {
        var fd = new FormData();
        fd.append('blocks', JSON.stringify([block]));
        return fetchTimeout('<?= BASE_URL ?>mod/builder/render_blocks', { method:'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res){ return res.html && res.html[0] ? res.html[0] : ''; });
    }
    var _refreshTimer = null;
    function refreshBlockLive(idx) {
        if (_refreshTimer) clearTimeout(_refreshTimer);
        _refreshTimer = setTimeout(function() {
            if (!blocksData[idx]) return;
            apiRenderOne(blocksData[idx]).then(function(html) {
                toFrame({ type:'builderSetBlockHtml', index: idx, html: html });
            });
        }, 420);
    }

    /* ---------- عملیات ساختاری ---------- */
    function newBlock(type, posOverride) {
        var bt = blockTypes[type];
        var b = { type: type, data: JSON.parse(JSON.stringify((bt && bt.defaults) || {})) };
        if (posOverride) { b.pos = {}; b.pos[currentBP] = posOverride; b.free = true; }
        return b;
    }
    function opInsert(type, index, posOverride) {
        var b = newBlock(type, posOverride || null);
        var at = (index >= 0 && index <= blocksData.length) ? index : blocksData.length;
        blocksData.splice(at, 0, b);
        apiRenderOne(b).then(function(html) {
            toFrame({ type:'builderInsertHtml', at: at, html: wrapBlock(at, b.type, html) });
        });
        afterStructureChange();
        selectBlock(at);
    }
    function opDelete(idx) {
        blocksData.splice(idx, 1);
        toFrame({ type:'builderRemoveBlockDom', index: idx });
        afterStructureChange();
        if (selectedIdx === idx) deselect();
        closeInspector();
    }
    function opDuplicate(idx) {
        var src = blocksData[idx]; if (!src) return;
        var cp = JSON.parse(JSON.stringify(src));
        blocksData.splice(idx + 1, 0, cp);
        apiRenderOne(cp).then(function(html) {
            toFrame({ type:'builderInsertHtml', at: idx + 1, html: wrapBlock(idx + 1, cp.type, html) });
        });
        afterStructureChange();
    }
    function opMove(idx, dir) {
        var j = idx + dir;
        if (j < 0 || j >= blocksData.length) return;
        var t = blocksData[idx]; blocksData[idx] = blocksData[j]; blocksData[j] = t;
        var perm = [];
        for (var i = 0; i < blocksData.length; i++) perm.push(i);
        perm[idx] = j; perm[j] = idx;
        toFrame({ type:'builderReorderDom', order: perm });
        afterStructureChange();
    }
    function opReorder(order) {
        if (!order || order.length !== blocksData.length) return;
        var nb = [];
        for (var i = 0; i < order.length; i++) { if (!blocksData[order[i]]) return; nb.push(blocksData[order[i]]); }
        blocksData = nb;
        toFrame({ type:'builderReorderDom', order: identityOrder() });
        afterStructureChange();
    }
    function opApplyPos(index, bpKey, pos) {
        var b = blocksData[index]; if (!b) return;
        b.pos = b.pos || {};
        b.pos[bpKey] = pos;
        pushPosCss();
        afterStructureChange(true);
        fillInspectorPos(b);
    }
    function opToggleFree(index, on, initPos) {
        var b = blocksData[index]; if (!b) return;
        if (LEGACY_FREE) {
            /* مهاجرت صفحه قدیمی آزاد → مدل جدید: همه آزاد میشوند سپس یکی خاموش */
            if (on) return;
            LEGACY_FREE_MIGRATE();
            b.free = false;
        } else {
            b.free = !!on;
            if (on && initPos) { b.pos = b.pos || {}; b.pos[currentBP] = initPos; }
        }
        pushPosCss();
        afterStructureChange(true);
        if (selectedIdx === index) fillInspectorFree(b);
    }
    function LEGACY_FREE_MIGRATE() {
        LEGACY_FREE = false;
        blocksData.forEach(function(b) { b.free = true; });
    }
    function identityOrder() { var a = []; for (var i = 0; i < blocksData.length; i++) a.push(i); return a; }
    function afterStructureChange(skipCss) {
        if (!skipCss) { /* ترتیب DOM توسط iframe اعمال شد */ }
        pushPosCss();
        markDirty();
    }
    function wrapBlock(i, type, innerHtml) {
        return '<div class="bpos-' + i + '" data-block-index="' + i + '">' + innerHtml + '</div>';
    }
    function updateEmptyOverlay() {
        document.getElementById('pbEmptyOverlay').style.display = blocksData.length ? 'none' : 'flex';
    }

    /* ---------- انتخاب و پنل تنظیمات ---------- */
    function selectBlock(idx) {
        selectedIdx = idx;
        buildInspector(idx);
        switchTab('inspector');
    }
    function deselect() {
        selectedIdx = -1;
        toFrame({ type:'builderFocusBlock', index:-1 });
    }
    function switchTab(name) {
        document.querySelectorAll('.pb-tabs button').forEach(function(b){ b.classList.toggle('active', b.dataset.tab === name); });
        ['blocks','inspector','gear'].forEach(function(t){
            document.getElementById('pbTab' + t.charAt(0).toUpperCase() + t.slice(1)).classList.toggle('active', t === name);
        });
    }
    function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function buildInspector(idx) {
        var b = blocksData[idx];
        var host = document.getElementById('pbInspector');
        if (!b) { host.innerHTML = '<div class="empty"><i class="fa-solid fa-cube"></i>بلاکی انتخاب نشده است.</div>'; return; }
        var bt = blockTypes[b.type] || { label:b.type, icon:'fa-cube', fields:[] };
        var h = '<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">';
        h += '<span style="width:30px;height:30px;border-radius:8px;background:' + (bt.color||'#888') + ';color:#fff;display:inline-flex;align-items:center;justify-content:center;"><i class="fa-solid ' + (bt.icon||'fa-cube') + '"></i></span>';
        h += '<strong style="flex:1;font-size:13.5px;">' + esc(bt.label) + ' #' + idx + '</strong>';
        h += '</div><div id="pbInspFields">';
        var fields = bt.fields || [];
        if (!fields.length) h += '<p class="pb-muted">این بلاک تنظیمات خاصی ندارد.</p>';
        fields.forEach(function(f) {
            var val = (b.data && b.data[f.key] !== undefined) ? b.data[f.key] : (f.default !== undefined ? f.default : '');
            if (val === null || val === undefined) val = '';
            h += '<div class="pb-field"><label>' + esc(f.label) + '</label>';
            if (f.type === 'select') {
                h += '<select class="pb-f" data-key="' + f.key + '" data-ftype="' + f.type + '">';
                (f.options || []).forEach(function(o){ h += '<option value="' + esc(o.value) + '" ' + (String(val) === String(o.value) ? 'selected' : '') + '>' + esc(o.label) + '</option>'; });
                h += '</select>';
            } else if (f.type === 'textarea') {
                h += '<textarea class="pb-f" data-key="' + f.key + '" data-ftype="textarea">' + esc(val) + '</textarea>';
            } else if (f.type === 'html') {
                h += '<textarea class="pb-f mono" data-key="' + f.key + '" data-ftype="html">' + esc(val) + '</textarea>';
            } else if (f.type === 'color') {
                h += '<input type="color" class="pb-f" data-key="' + f.key + '" data-ftype="color" value="' + esc(val || '#000000') + '" style="height:38px;padding:3px;">';
            } else if (f.type === 'number') {
                h += '<input type="number" class="pb-f" data-key="' + f.key + '" data-ftype="number" value="' + esc(val) + '">';
            } else if (f.type === 'image') {
                h += '<div class="row"><input type="text" class="pb-f" data-key="' + f.key + '" data-ftype="image" value="' + esc(val) + '" placeholder="URL تصویر" style="direction:ltr;text-align:left;"><button type="button" class="pb-btn khali" onclick="openImagePicker()" title="کتابخانه / آپلود / URL"><i class="fa-solid fa-folder-open"></i></button></div>';
            } else {
                h += '<input type="text" class="pb-f" data-key="' + f.key + '" data-ftype="text" value="' + esc(val) + '">';
            }
            h += '</div>';
        });
        h += '</div>';

        /* موقعیت (اگر آزاد) */
        h += '<div id="pbInspPosWrap" style="display:none;"><hr class="pb-sep"><h4 style="font-size:12.5px;margin:0 0 8px;color:#555;"><i class="fa-solid fa-crosshairs"></i> موقعیت آزاد (<span id="pbDevLabel">' + devices[currentDevice].label + '</span>)</h4>';
        h += '<div class="pb-pos-grid">';
        ['x','y','w','z'].forEach(function(k) {
            h += '<div><label>' + k.toUpperCase() + '</label><input type="number" id="pbPos_' + k + '" onchange="pbPosInput(\'' + k + '\')"></div>';
        });
        h += '</div>';
        h += '<div style="display:flex;gap:6px;"><button type="button" class="pb-btn khali" style="flex:1;" onclick="pbLayer(1)">جلو ⇡</button><button type="button" class="pb-btn khali" style="flex:1;" onclick="pbLayer(-1)">عقب ⇣</button></div>';
        h += '<button type="button" class="pb-btn khali wide" style="margin-top:8px;" id="pbUnfreeBtn" onclick="pbToggleSelectedFree(false)"><i class="fa-solid fa-link"></i> چسباندن به جریان (خروج از آزاد)</button></div>';
        h += '<hr class="pb-sep"><button type="button" class="pb-btn danger wide" onclick="opDelete(selectedIdx)"><i class="fa-solid fa-trash"></i> حذف این بلاک</button>';
        host.innerHTML = h;

        /* رویدادها */
        host.querySelectorAll('.pb-f').forEach(function(inp) {
            var ev = (inp.tagName === 'SELECT' || inp.type === 'color') ? 'change' : 'input';
            inp.addEventListener(ev, function() {
                var key = inp.dataset.key, ftype = inp.dataset.ftype, v = inp.value;
                if (ftype === 'number') v = Number(v);
                b.data[key] = v;
                markDirty();
                refreshBlockLive(selectedIdx);
            });
            if (ftype === 'html') { try { if (typeof SadastEditor !== 'undefined') inp._sadastEditor = new SadastEditor(inp); } catch(e){} }
        });
        fillInspectorFree(b);
    }
    function fillInspectorFree(b) {
        var wrap = document.getElementById('pbInspPosWrap');
        if (!wrap) return;
        var isFree = LEGACY_FREE || !!(b && b.free);
        wrap.style.display = isFree ? 'block' : 'none';
        if (isFree) fillInspectorPos(b);
    }
    function fillInspectorPos(b) {
        if (!b) return;
        var p = effectivePos(b);
        if (!p) return;
        ['x','y','w','z'].forEach(function(k) {
            var el = document.getElementById('pbPos_' + k);
            if (el) el.value = Math.round(p[k]);
        });
    }
    function pbPosInput(k) {
        var b = blocksData[selectedIdx]; if (!b) return;
        b.pos = b.pos || {};
        var p = b.pos[currentBP] = b.pos[currentBP] || { x:0,y:0,w:300,z:1 };
        p[k] = Number(document.getElementById('pbPos_' + k).value) || 0;
        pushPosCss();
        markDirty();
        toFrame({ type:'builderPosChanged', index:selectedIdx });
    }
    function pbLayer(dir) {
        var b = blocksData[selectedIdx]; if (!b) return;
        b.pos = b.pos || {};
        var p = b.pos[currentBP] = b.pos[currentBP] || { x:0,y:0,w:300,z:1 };
        p.z = (parseInt(p.z,10)||1) + dir;
        pushPosCss(); markDirty(); fillInspectorPos(b);
        toFrame({ type:'builderPosChanged', index:selectedIdx });
    }
    function pbToggleSelectedFree(on) {
        if (selectedIdx < 0) return;
        if (on === false) { opToggleFree(selectedIdx, false, null); }
        else { toFrame({ type:'builderDoFree', index:selectedIdx }); }
    }
    function closeInspector() {
        if (selectedIdx >= 0) return;
        document.getElementById('pbInspector').innerHTML = '<div class="empty"><i class="fa-solid fa-cube"></i>بلاکی انتخاب نشده است.</div>';
    }

    /* ---------- انتخابگر تصویر ---------- */
    var _imgCb = null, _libCache = null, _libCacheTime = 0;
    function openImagePicker(cb) {
        _imgCb = cb || function(url) {
            var b = blocksData[selectedIdx]; if (!b) return;
            b.data.src = url;
            var inp = document.querySelector('#pbInspector .pb-f[data-key="src"]');
            if (inp) inp.value = url;
            refreshBlockLive(selectedIdx);
        };
        document.getElementById('pbImgModalBg').classList.add('open');
        pbLoadLibrary();
    }
    function pbCloseImageModal() { document.getElementById('pbImgModalBg').classList.remove('open'); }
    document.querySelectorAll('.pb-modal .tabs button').forEach && document.querySelectorAll('#pbImgModalBg .tabs button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#pbImgModalBg .tabs button').forEach(function(x){ x.classList.toggle('active', x === btn); });
            ['lib','up','url'].forEach(function(t) { document.getElementById('pbMTab' + t.charAt(0).toUpperCase()+t.slice(1)).style.display = (btn.dataset.mtab === t) ? '' : 'none'; });
            if (btn.dataset.mtab === 'lib') pbLoadLibrary();
        });
    });
    function pbLoadLibrary(force) {
        var grid = document.getElementById('pbLibGrid');
        if (_libCache && !force && Date.now() - _libCacheTime < 60000) { renderLib(_libCache); return; }
        grid.innerHTML = '<p class="pb-muted">در حال بارگذاری…</p>';
        fetchTimeout('<?= BASE_URL ?>mod/builder/list_images')
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.success) { _libCache = res.images; _libCacheTime = Date.now(); renderLib(res.images); }
            else grid.innerHTML = '<p class="pb-muted">خطا در بارگذاری کتابخانه.</p>';
        }).catch(function(){ grid.innerHTML = '<p class="pb-muted">خطا در اتصال.</p>'; });
    }
    function renderLib(urls) {
        var grid = document.getElementById('pbLibGrid');
        if (!urls || !urls.length) { grid.innerHTML = '<p class="pb-muted">هنوز تصویری در کتابخانه نیست — از تب «آپلود مستقیم» شروع کنید.</p>'; return; }
        grid.innerHTML = '';
        urls.forEach(function(u) {
            var img = document.createElement('img');
            img.src = u; img.loading = 'lazy'; img.alt = '';
            img.onclick = function() { if (_imgCb) _imgCb(u); pbCloseImageModal(); };
            grid.appendChild(img);
        });
    }
    function pbPickUrl() {
        var u = document.getElementById('pbUrlInput').value.trim();
        if (!u) return;
        if (_imgCb) _imgCb(u);
        pbCloseImageModal();
    }
    (function() {
        var dz = document.getElementById('pbDropZone'), fi = document.getElementById('pbUpInput');
        dz.addEventListener('click', function(){ fi.click(); });
        dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.classList.add('over'); });
        dz.addEventListener('dragleave', function(){ dz.classList.remove('over'); });
        dz.addEventListener('drop', function(e){ e.preventDefault(); dz.classList.remove('over'); if (e.dataTransfer.files.length) pbUploadFile(e.dataTransfer.files[0]); });
        fi.addEventListener('change', function(){ if (fi.files.length) pbUploadFile(fi.files[0]); fi.value=''; });
    })();
    function pbUploadFile(file) {
        var fd = new FormData();
        fd.append('image', file);
        var dz = document.getElementById('pbDropZone');
        dz.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size:26px;display:block;margin-bottom:8px;"></i>در حال آپلود…';
        fetchTimeout('<?= BASE_URL ?>mod/builder/upload_image', { method:'POST', body: fd }, 120000)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            dz.innerHTML = '<i class="fa-solid fa-cloud-arrow-up" style="font-size:26px;display:block;margin-bottom:8px;"></i>فایل تصویر را اینجا رها کنید یا کلیک کنید';
            if (res.success) { _libCache = null; pbLoadLibrary(); if (_imgCb) _imgCb(res.url); pbCloseImageModal(); }
            else alert(res.message || 'خطا در آپلود');
        }).catch(function(){ dz.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> خطا در اتصال'; });
    }

    /* ---------- دستگاه‌ها ---------- */
    function applyDeviceScale() {
        var dev = devices[currentDevice];
        var wrap = document.getElementById('pbFrameScale');
        var avail = document.getElementById('pbFrameWrap').clientWidth - 36;
        var w = Math.min(dev.w, 1920);
        wrap.style.maxWidth = w + 'px';
        var scale = Math.min(1, avail / w);
        wrap.style.transform = scale < 1 ? ('scale(' + scale + ')') : '';
        wrap.style.width = (scale < 1 ? w : '100%');
    }
    document.querySelectorAll('#pbDeviceSeg button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#pbDeviceSeg button').forEach(function(x){ x.classList.toggle('active', x === btn); });
            currentDevice = btn.dataset.dev;
            currentBP = devices[currentDevice].bp;
            applyDeviceScale();
            toFrame({ type:'builderDeviceChanged', bp: currentBP, label: devices[currentDevice].label });
            if (selectedIdx >= 0) fillInspectorPos(blocksData[selectedIdx]);
            var lbl = document.getElementById('pbDevLabel');
            if (lbl) lbl.textContent = devices[currentDevice].label;
        });
    });
    document.querySelectorAll('#pbMobileSeg button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#pbMobileSeg button').forEach(function(x){ x.classList.toggle('active', x === btn); });
            mobileMode = btn.dataset.mm;
            pushPosCss();
            markDirty();
        });
    });

    /* ---------- تب‌ها و اسپلیتر ---------- */
    document.querySelectorAll('.pb-tabs button').forEach(function(btn) {
        btn.addEventListener('click', function(){ switchTab(btn.dataset.tab); });
    });
    (function() {
        var sp = document.getElementById('pbSplitter'), panel = document.getElementById('pbPanel'), app = document.getElementById('pbApp');
        var dragging = false;
        sp.addEventListener('mousedown', function(e){ dragging = true; sp.classList.add('dragging'); e.preventDefault(); });
        window.addEventListener('mousemove', function(e) {
            if (!dragging) return;
            var r = app.getBoundingClientRect();
            var w = Math.max(200, Math.min(520, e.clientX - r.left));
            panel.style.setProperty('--panel-w', w + 'px');
            panel.style.width = w + 'px';
        });
        window.addEventListener('mouseup', function() {
            if (!dragging) return;
            dragging = false; sp.classList.remove('dragging');
            try { localStorage.setItem('pb_panel_w', panel.offsetWidth); } catch(err){}
        });
        try { var sw = parseInt(localStorage.getItem('pb_panel_w'),10); if (sw) { panel.style.width = sw+'px'; panel.style.setProperty('--panel-w', sw+'px'); } } catch(err){}
    })();

    /* ---------- پیام‌های iframe ---------- */
    window.addEventListener('message', function(e) {
        if (e.origin !== window.location.origin) return;
        var d = e.data || {};
        if (d._ns !== 'builderInline') return;
        switch (d.type) {
            case 'builderReady':
                toFrame({ type:'builderSetContentFields', fields: blockContentFields() });
                updateEmptyOverlay();
                break;
            case 'builderSelect':          selectBlock(d.index); break;
            case 'builderContent':         onInlineContent(d.index, d.key, d.value); break;
            case 'builderInsertAt':        opInsert(d.btype, d.index, null); break;
            case 'builderInsertFree':      opInsert(d.btype, -1, d.pos); break;
            case 'builderReorder':         opReorder(d.order); break;
            case 'builderPos':             opApplyPos(d.index, d.bp || currentBP, d.pos); break;
            case 'builderDuplicate':       opDuplicate(d.index); break;
            case 'builderDelete':          opDelete(d.index); break;
            case 'builderMove':            opMove(d.index, d.dir === 'up' ? -1 : 1); break;
            case 'builderOpenImages':      openImagePicker(function(url) {
                                               var b = blocksData[selectedIdx]; if (!b) return;
                                               b.data.src = url;
                                               var inp = document.querySelector('#pbInspector .pb-f[data-key="src"]');
                                               if (inp) inp.value = url;
                                               refreshBlockLive(selectedIdx);
                                           }); break;
            case 'builderToggleFree':      opToggleFree(d.index, d.on, d.initPos); break;
            case 'builderDragState':       break;
        }
    });
    function onInlineContent(idx, key, value) {
        if (!blocksData[idx]) return;
        blocksData[idx].data = blocksData[idx].data || {};
        blocksData[idx].data[key] = value;
        markDirty();
    }
    function blockContentFields() {
        var map = {};
        blocksData.forEach(function(b, i) {
            var bt = blockTypes[b.type];
            if (!bt || !bt.fields) return;
            var txt = bt.fields.filter(function(f){ return f.type === 'text' || f.type === 'html' || f.type === 'textarea'; })[0];
            if (txt) map[i] = txt.key;
        });
        return map;
    }

    /* ---------- کیبورد ---------- */
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') { e.preventDefault(); pbFullSave(); return; }
        var inField = e.target.closest && e.target.closest('input,textarea,select,[contenteditable]');
        if (inField) return;
        if ((e.key === 'Delete' || e.key === 'Backspace') && selectedIdx >= 0) { e.preventDefault(); opDelete(selectedIdx); }
        if (e.key === 'Escape') { deselect(); toFrame({type:'builderExit'}); }
    });
    window.addEventListener('beforeunload', function(e) {
        if (isDirty) { e.preventDefault(); e.returnValue = ''; }
    });

    /* ---------- درگ بلاک از پالت (Pointer Events + capture — دقیق روی iframe) ---------- */
    (function() {
        var ghost = null, started = false, curType = null, lastSent = 0;
        var frameEl = document.getElementById('previewFrame');

        function toLocal(e) {
            var r = frameEl.getBoundingClientRect();
            var w = frameEl.contentWindow ? frameEl.contentWindow.innerWidth : r.width;
            var h = frameEl.contentWindow ? frameEl.contentWindow.innerHeight : r.height;
            return {
                x: Math.round((e.clientX - r.left) * (w / r.width)),
                y: Math.round((e.clientY - r.top) * (h / r.height)),
                inside: e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom
            };
        }
        function makeGhost(bt) {
            var b = blockTypes[bt] || {};
            ghost = document.createElement('div');
            ghost.style.cssText = 'position:fixed;z-index:99999;pointer-events:none;background:#fff;border:2px solid var(--rang-asli,#FF6F00);color:#333;border-radius:10px;padding:6px 12px;font-size:12px;font-weight:700;box-shadow:0 8px 24px rgba(0,0,0,.28);display:flex;align-items:center;gap:7px;font-family:inherit;';
            ghost.innerHTML = '<span style="width:22px;height:22px;border-radius:6px;background:' + (b.color||'#888') + ';color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;"><i class="fa-solid ' + (b.icon||'fa-cube') + '"></i></span>' + esc(b.label||bt);
            document.body.appendChild(ghost);
        }
        function begin(e) {
            var card = e.target.closest('.pb-block-card');
            if (!card || e.button !== 0) return;
            curType = card.dataset.btype;
            started = false;
            try { card.setPointerCapture(e.pointerId); } catch(err) {}
            card.addEventListener('pointermove', onMove);
            card.addEventListener('pointerup', onUp);
            card.addEventListener('pointercancel', onCancel);
            e.preventDefault();
        }
        function onMove(e) {
            if (!ghost && !started) {
                started = true;
                makeGhost(curType);
                document.body.classList.add('pb-pal-drag');
            }
            if (!started) return;
            ghost.style.left = (e.clientX + 12) + 'px';
            ghost.style.top = (e.clientY + 14) + 'px';
            var now = Date.now();
            if (now - lastSent < 35) return;
            lastSent = now;
            var L = toLocal(e);
            toFrame({ type:'builderDragMove', btype:curType, x:L.x, y:L.y, inside:L.inside, alt:e.altKey });
        }
        function detach(card) {
            card.removeEventListener('pointermove', onMove);
            card.removeEventListener('pointerup', onUp);
            card.removeEventListener('pointercancel', onCancel);
            document.body.classList.remove('pb-pal-drag');
            if (ghost) { ghost.remove(); ghost = null; }
        }
        function onUp(e) {
            var card = e.currentTarget;
            detach(card);
            if (!started) { opInsert(curType, -1, null); return; } /* کلیک ساده */
            var L = toLocal(e);
            toFrame({ type:'builderDragDrop', btype:curType, x:L.x, y:L.y, inside:L.inside, alt:e.altKey });
        }
        function onCancel(e) { detach(e.currentTarget); toFrame({ type:'builderDragCancel' }); }
        Array.prototype.forEach.call(document.querySelectorAll('.pb-block-card'), function(card) {
            card.addEventListener('pointerdown', begin);
            card.style.touchAction = 'none';
        });
    })();

    /* ---------- boot ---------- */
    applyDeviceScale();
    scheduleAutosaveCheck();
    updateEmptyOverlay();
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function builder_save_blocks() {
    header('Content-Type: application/json; charset=utf-8');
    /* آزادسازی قفل سشن: درخواست‌های همزمان (ذخیره/رندر/پیش‌نمایش) دیگر پشت هم صف نمی‌شوند */
    if (function_exists('session_write_close')) { @session_write_close(); }
    @set_time_limit(90);

    $block_page_id = (int)($_POST['block_page_id'] ?? 0);
    $blocks_data = $_POST['blocks_data'] ?? '[]';
    $light       = !empty($_POST['light']);   // ذخیره خودکار سبک: فقط JSON
    $position_mode = !empty($_POST['position_mode']) ? 1 : 0;
    $mobile_mode = in_array($_POST['mobile_mode'] ?? '', ['auto', 'exact']) ? $_POST['mobile_mode'] : 'auto';
    if (!$block_page_id) {
        echo json_encode(['success' => false, 'message' => 'Page ID is required']);
        exit;
    }
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT id, page_id, page_type, position_mode, mobile_mode FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        if ($light) {
            /* سبک: بدون رندر و sync؛ کش قدیم باطل می‌شود تا فرانت کهنه نماند */
            $stmt = $conn->prepare("UPDATE block_pages SET blocks_data = ?, position_mode = ?, mobile_mode = ?, cached_html = NULL, cache_updated = NULL WHERE id = ?");
            $stmt->bind_param("sisi", $blocks_data, $position_mode, $mobile_mode, $block_page_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            echo json_encode(['success' => true, 'mode' => 'light']);
            exit;
        }
        /* کامل: JSON + باطل‌سازی کش */
        $stmt = $conn->prepare("UPDATE block_pages SET blocks_data = ?, position_mode = ?, mobile_mode = ?, cached_html = NULL, cache_updated = NULL WHERE id = ?");
        $stmt->bind_param("sisi", $blocks_data, $position_mode, $mobile_mode, $block_page_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO block_pages (id, page_id, page_type, blocks_data, position_mode, mobile_mode) VALUES (?, 0, 'safhe', ?, ?, ?)");
        $stmt->bind_param("isiss", $block_page_id, $blocks_data, $position_mode, $mobile_mode);
    }
    $stmt->execute();
    $stmt->close();

    $blocks = json_decode($blocks_data, true) ?: [];

    /* رندر همه بلاکها با یک کانکشن مشترک (بدون اتصال جدا برای هر بلاک) */
    $page_type = $row['page_type'] ?? 'safhe';
    if ($position_mode) {
        $html = '<div class="builder-free-canvas">' . builder_build_positions_css($blocks, $mobile_mode, true) . builder_render_blocks($blocks, true, $bank) . '</div>';
    } else {
        $any_free = false;
        foreach ($blocks as $__b) { if (!empty($__b['free'])) { $any_free = true; break; } }
        $inner = builder_render_blocks($blocks, false, $bank);
        $html = $any_free
            ? '<div class="builder-free-canvas">' . builder_build_positions_css($blocks, $mobile_mode, true) . $inner . '</div>'
            : $inner;
    }

    /* همگام‌سازی محتوای رندرشده با جدول مربوطه (پست/محصول/خدمت) */
    if ($row && (int)$row['page_id'] > 0) {
        builder_sync_content($row['page_type'] ?? $page_type, (int)$row['page_id'], $html, $bank);
    }

    /* کش پیش‌فرض روشن: نتیجه ذخیره می‌شود تا بازدیدهای بعدی سریع باشند */
    if ($blocks) {
        $stmt = $conn->prepare("UPDATE block_pages SET cached_html = ?, cache_updated = NOW() WHERE id = ?");
        $stmt->bind_param("si", $html, $block_page_id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();

    echo json_encode(['success' => true, 'mode' => 'full']);
    exit;
}

function builder_sync_content($page_type, $page_id, $html, $bank = null) {
    $map = [
        'blog'     => ['posts', 'content'],
        'maghaleh' => ['posts', 'content'],
        'safhe'    => ['posts', 'content'],
        'page'     => ['posts', 'content'],
        'mahsul'   => ['mahsulat', 'content'],
        'khadamat' => ['posts', 'content'],
    ];
    if (!isset($map[$page_type])) return;
    list($table, $col) = $map[$page_type];
    if ($bank === null) { $bank = new Bank(); }
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("UPDATE $table SET $col = ? WHERE id = ?");
    $stmt->bind_param("si", $html, $page_id);
    $stmt->execute();
    $stmt->close();
}

function builder_clear_cache($block_page_id) {
    header('Content-Type: application/json; charset=utf-8');
    if (function_exists('session_write_close')) { @session_write_close(); }
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("UPDATE block_pages SET cached_html = NULL, cache_updated = NULL WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true]);
    exit;
}

function builder_render_page($block_page_id, $use_cache = true) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $bp = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$bp) { $conn->close(); return ''; }
    /* کش با عمر کوتاه (۵ دقیقه): بازدیدهای پشت‌سرهم از کش؛ بلاکهای داینامیک حداکثر ۵ دقیقه کهنه می‌مانند */
    $__ttl_fresh = false;
    if ($use_cache && $bp['cached_html']) {
        $__ts = $bp['cache_updated'] ? strtotime($bp['cache_updated']) : 0;
        if ($__ts && (time() - $__ts) < 300) {
            $conn->close();
            return $bp['cached_html'];
        }
        $__ttl_fresh = true;
    }
    $blocks = json_decode($bp['blocks_data'], true) ?: [];
    $html = '';
    $is_free_wrap = false;
    if (empty($blocks)) {
        if ($bp['page_id']) {
            $r = $conn->query("SELECT content FROM posts WHERE id = " . (int)$bp['page_id']);
            $row = $r ? $r->fetch_assoc() : null;
            if ($row) { $html = '<div class="mohtava-container" style="padding:40px 0;">' . $row['content'] . '</div>'; }
        }
    } else {
        if (!empty($bp['position_mode'])) {
            $css = builder_build_positions_css($blocks, $bp['mobile_mode'] ?? 'auto', !empty($bp['position_mode']));
            $html = '<div class="builder-free-canvas">' . $css . builder_render_blocks($blocks, true, $bank) . '</div>';
            $is_free_wrap = true;
        } else {
            $any_free = false;
            foreach ($blocks as $__b) { if (!empty($__b['free'])) { $any_free = true; break; } }
            $inner = builder_render_blocks($blocks, false, $bank);
            if ($any_free) {
                $html = '<div class="builder-free-canvas">' . builder_build_positions_css($blocks, $bp['mobile_mode'] ?? 'auto', !empty($bp['position_mode'])) . $inner . '</div>';
                $is_free_wrap = true;
            } else {
                $html = $inner;
            }
        }
    }
    /* کش خودکار: اولین بازدید (یا منقضی‌شده) رندر می‌کند و نتیجه کش می‌شود */
    if ($use_cache && $html !== '' && $__ttl_fresh) {
        $stmt = $conn->prepare("UPDATE block_pages SET cached_html = ?, cache_updated = NOW() WHERE id = ?");
        $stmt->bind_param("si", $html, $block_page_id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
    return $html;
}

function builder_build_positions_css($blocks, $mobile_mode = 'auto', $all_free = false) {
    $bps = builder_breakpoints();
    $css = '<style class="builder-pos-css">';
    foreach ($blocks as $i => $block) {
        /* مدل جدید: هر بلاک خودش پرچم free دارد؛ حالت قدیمی: کل صفحه آزاد */
        if (!$all_free && empty($block['free'])) continue;
        $eff = builder_effective_pos($block);
        $cls = '.bpos-' . $i;
        $hasMobile = !empty($block['pos']['mobile']);
        $hasTablet = !empty($block['pos']['tablet']);
        foreach (['wide', 'desktop', 'tablet', 'mobile'] as $b) {
            $p = $eff[$b];
            if ($p === null) continue;
            if ($mobile_mode === 'auto' && (($b === 'mobile' && !$hasMobile) || ($b === 'tablet' && !$hasTablet))) {
                $rule = $cls . '{position:static!important;width:100%!important;margin-bottom:16px;}';
            } else {
                $rule = $cls . '{position:absolute;left:' . (int)$p['x'] . 'px;top:' . (int)$p['y'] . 'px;width:' . (int)$p['w'] . 'px;z-index:' . (int)($p['z'] ?? 1) . ';}';
            }
            $media = $bps[$b]['media'];
            $css .= $media ? ($media . '{' . $rule . '}') : $rule;
        }
    }
    $css .= '.builder-free-canvas{position:relative;min-height:600px;}';
    $css .= '.builder-free-canvas .bpos-0,.builder-free-canvas [class*=bpos-]{box-sizing:border-box;}';
    $css .= '</style>';
    return $css;
}

function builder_render_full_page($block_page_id, $context = [], $use_cache = true) {
    $header  = builder_render_part('header', $context);
    $content = builder_render_page($block_page_id, $use_cache);
    $footer  = builder_render_part('footer', $context);
    return $header . '<div class="builder-edit-root">' . $content . '</div>' . $footer;
}

function builder_preview_page($block_page_id) {
    if (function_exists('session_write_close')) { @session_write_close(); }
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $bp = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($bp && (int)$bp['page_id'] > 0 && empty($bp['part'])) {
        $ctx = ['type' => $bp['page_type']];
        $html = builder_render_full_page($block_page_id, $ctx, false);
    } else {
        $html = builder_render_page($block_page_id, false);
    }
    $site_url = BASE_URL;
    $edit = ($_GET['edit'] ?? '') === '1';
    $edit_head = '';
    $edit_body = '';
    if ($edit) {
        $edit_head = '
            .builder-live-block { position:relative; cursor:pointer; }
            .builder-live-block:hover { outline:2px dashed var(--rang-asli,#FF6F00); outline-offset:2px; }
            .builder-live-block.builder-selected { outline:2px solid var(--rang-asli,#FF6F00); outline-offset:2px; box-shadow:0 0 0 6px rgba(255,111,0,.12); }
            .builder-live-block.pb-free { cursor:move; }
            .builder-live-block.pb-free:hover { outline-style:dotted; }
            .builder-inline-toolbar { position:fixed; z-index:99999; display:none; gap:4px; background:#fff; border:1px solid #e9ecef; border-radius:12px; padding:6px; box-shadow:0 10px 30px rgba(0,0,0,0.25); direction:rtl; }
            .builder-inline-toolbar button { min-width:38px; height:38px; padding:0 12px; border:none; background:#f8f9fa; border-radius:9px; cursor:pointer; color:#444; font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; gap:7px; font-family:inherit; }
            .builder-inline-toolbar button:hover { background:#ffe0b2; color:#e65100; }
            .builder-inline-toolbar button.danger:hover { background:#c62828; color:#fff; }
            .builder-inline-toolbar button.on { background:var(--rang-asli,#FF6F00); color:#fff; }
            .builder-drag-handle { position:absolute; top:-14px; right:8px; z-index:60; width:26px; height:26px; border-radius:7px; background:var(--rang-asli,#FF6F00); color:#fff; display:none; align-items:center; justify-content:center; cursor:grab; font-size:12px; box-shadow:0 2px 8px rgba(0,0,0,.25); }
            .builder-live-block:hover > .builder-drag-handle, .builder-live-block.builder-selected > .builder-drag-handle { display:flex; }
            .builder-drop-line { height:0; border-top:3px dashed var(--rang-makm2,#00B894); position:relative; margin:2px 0; }
            .builder-drop-line::after { content:\'\'; position:absolute; right:-4px; top:-7px; width:11px; height:11px; border-radius:50%; background:var(--rang-makm2,#00B894); }
            .builder-resize-handle { position:absolute; bottom:-6px; left:-6px; width:16px; height:16px; background:var(--rang-asli,#FF6F00); border:2px solid #fff; cursor:nwse-resize; border-radius:50%; z-index:70; display:none; box-shadow:0 1px 6px rgba(0,0,0,.3); }
            .builder-live-block.builder-selected.pb-free > .builder-resize-handle { display:block; }
            .builder-pos-hud { position:fixed; z-index:99999; background:rgba(38,38,42,.92); color:#fff; font-size:11px; font-family:monospace; padding:3px 8px; border-radius:5px; pointer-events:none; display:none; direction:ltr; }
            .builder-editing-text { outline:2px solid #0984E3 !important; outline-offset:2px !important; cursor:text !important; min-height:1em; }
            .builder-live-block img { cursor:pointer; }
            .builder-live-block img:hover { outline:2px dashed #0984E3; outline-offset:2px; }
            body.pb-is-dragging { cursor:copy !important; }
            body.pb-is-dragging * { cursor:copy !important; }
        ';
        $edit_body = '<script src="' . $site_url . 'mohtava/sakhtar/inline-editor.js?v=2"></script>';
    }
    $font_css = "<style>"
        . "@font-face{font-family:'Vazirmatn';src:url({$site_url}ghaleb/manabe/fonts/Vazirmatn-RD-Regular.woff2) format('woff2');font-weight:400;font-display:swap;}"
        . "@font-face{font-family:'Vazirmatn';src:url({$site_url}ghaleb/manabe/fonts/Vazirmatn-RD-Bold.woff2) format('woff2');font-weight:700;font-display:swap;}"
        . "</style>";
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="fa" dir="rtl"><head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="' . $site_url . 'ghaleb/' . GHALEB_FAAAL . '/manabe/fontawesome/all.min.css">
        ' . $font_css . '
        <style>
            body { font-family:"Vazirmatn",Tahoma,sans-serif; font-size:15px; line-height:1.8; color:#1a1a1a; margin:0; padding:0; direction:rtl; }
            :root { --rang-asli:#FF6F00; --rang-tira:#E65100; --rang-roshan:#ffffea; --rang-matn:#1a1a1a; --rang-zamin:#fff; --rang-sabz:#f8f9fa; --rang-border:#e9ecef; --rang-gray:#6c757d; --rang-makm1:#2D3436; --rang-makm2:#00B894; --rang-makm3:#6C5CE7; --rang-makm4:#FDCB6E; --rang-makm5:#E17055; }
            .mohtava-container { max-width:1200px; margin:0 auto; padding:0 20px; }
            img { max-width:100%; height:auto; }
            a { text-decoration:none; color:inherit; }
            ul { list-style:none; margin:0; padding:0; }
            .dakmeh { display:inline-flex; align-items:center; gap:8px; padding:12px 28px; border-radius:8px; font-family:"Vazirmatn",sans-serif; font-size:15px; font-weight:700; cursor:pointer; border:none; transition:all 0.3s; }
            .dakmeh-asli { background:var(--rang-asli); color:#fff; }
            .dakmeh-asli:hover { background:var(--rang-tira); transform:translateY(-2px); box-shadow:0 6px 20px rgba(255,111,0,0.35); }
            .dakmeh-khali { background:transparent; color:var(--rang-asli); border:2px solid var(--rang-asli); }
            .dakmeh-khali:hover { background:var(--rang-asli); color:#fff; }
            .kart-khadamat { background:#fff; border-radius:12px; padding:32px 24px; box-shadow:0 4px 20px rgba(0,0,0,0.08); border:1px solid var(--rang-border); transition:all 0.3s; height:100%; display:flex; flex-direction:column; }
            .kart-khadamat:hover { transform:translateY(-6px); box-shadow:0 12px 35px rgba(255,111,0,0.15); border-color:var(--rang-asli); }
            .kart-khadamat .icon { width:64px; height:64px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:26px; color:#fff; margin-bottom:20px; background:var(--rang-asli); }
            .kart-khadamat h3 { font-size:1.1rem; margin-bottom:10px; color:var(--rang-matn); }
            .kart-khadamat p { color:var(--rang-gray); font-size:0.9rem; flex:1; line-height:1.7; }
            .gerid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
            .gerid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
            .onvan-bakhsh { text-align:center; margin-bottom:48px; }
            .onvan-bakhsh h2 { font-size:1.8rem; color:var(--rang-matn); margin-bottom:12px; }
            .onvan-bakhsh p { color:var(--rang-gray); max-width:550px; margin:0 auto; }
            @media(max-width:992px){ .gerid-3 { grid-template-columns:repeat(2,1fr); } .gerid-4 { grid-template-columns:repeat(2,1fr); } }
            @media(max-width:576px){ .gerid-3 { grid-template-columns:1fr; } .gerid-4 { grid-template-columns:1fr; } }
            ' . $edit_head . '
        </style>
    </head><body>' . $html . $edit_body . '</body></html>';
    exit;
}

/**
 * پیدا کردن تم منطبق با شرط نمایش
 * @param string $condition_type single | archive | global
 * @param string $condition_value مقدار شرط
 * @return array|null
 */
function builder_find_template($condition_type, $condition_value, $part = null) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($part === null) {
        $stmt = $conn->prepare("SELECT * FROM block_pages WHERE condition_type = ? AND condition_value = ? LIMIT 1");
        $stmt->bind_param("ss", $condition_type, $condition_value);
    } else {
        $stmt = $conn->prepare("SELECT * FROM block_pages WHERE part = ? AND condition_type = ? AND condition_value = ? LIMIT 1");
        $stmt->bind_param("sss", $part, $condition_type, $condition_value);
    }
    $stmt->execute();
    $bp = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $bp ?: null;
}

/**
 * یافتن تم بخش خاص (هدر/فوتر) با اولویت: slug خاص > نوع > سراسری *
 * @param string $part header | footer
 * @param array  $context ['slug'=>..., 'type'=>...]
 */
function builder_resolve_part($part, $context = []) {
    $slug = $context['slug'] ?? '';
    $type = $context['type'] ?? '';
    $candidates = [];
    if ($slug)    $candidates[] = ['single', $slug];
    if ($type)    $candidates[] = ['single', $type];
    if ($type)    $candidates[] = ['archive', $type];
    $candidates[] = ['global', '*'];
    $bank = new Bank();
    $conn = $bank->getConnection();
    foreach ($candidates as $c) {
        $stmt = $conn->prepare("SELECT * FROM block_pages WHERE part = ? AND condition_type = ? AND condition_value = ? LIMIT 1");
        $stmt->bind_param("sss", $part, $c[0], $c[1]);
        $stmt->execute();
        $bp = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($bp) { $conn->close(); return $bp; }
    }
    $conn->close();
    return null;
}

function builder_render_part($part, $context = []) {
    $bp = builder_resolve_part($part, $context);
    if (!$bp) return '';
    return builder_render_page($bp['id']);
}

/**
 * رندر تم منطبق بر اساس بافتار صفحه
 * @param string $kind archive | single | home
 * @param string|null $subtype نوع (blog/product/khadamat/post/safhe)
 * @param string|null $slug اسلاگ صفحه خاص
 * @return string HTML کش‌شده یا ''
 */
function builder_render_for($kind, $subtype = null, $slug = null) {
    if ($kind === 'header' || $kind === 'footer') {
        return builder_render_part($kind, ['slug' => $slug, 'type' => $subtype]);
    }
    $candidates = [];
    if ($kind === 'single') {
        if ($slug)    $candidates[] = ['single', $slug];
        if ($subtype) $candidates[] = ['single', $subtype];
    } elseif ($kind === 'archive') {
        if ($subtype) $candidates[] = ['archive', $subtype];
    } elseif ($kind === 'home') {
        $candidates[] = ['single', 'home'];
    }
    $candidates[] = ['global', '*'];

    foreach ($candidates as $c) {
        $bp = builder_find_template($c[0], $c[1]);
        if ($bp) {
            $html = builder_render_page($bp['id']);
            if ($html) return $html;
        }
    }
    return '';
}

function builder_render_blocks($blocks, $free = false, $bank = null) {
    $html = '';
    foreach ($blocks as $i => $block) {
        $html .= builder_render_block($block, $i, $free, $bank);
    }
    return $html;
}

function builder_render_block($block, $index = 0, $free = false, $bank = null) {
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $func = 'block_' . $type . '_render';
    if (function_exists($func)) {
        /* کانکشن مشترک فقط به بلاکهای داینامیک پاس داده می‌شود */
        $ref = new ReflectionFunction($func);
        $inner = $ref->getNumberOfParameters() >= 2
            ? call_user_func($func, $data, $bank)
            : call_user_func($func, $data);
    } else {
        $inner = '<div style="padding:20px;background:#fff3e0;border:1px solid #FF6F00;border-radius:8px;margin:16px 0;color:#e65100;text-align:center;">بلاک «' . htmlspecialchars($type) . '» فعال نیست</div>';
    }
    if ($free) {
        return '<div class="bpos-' . $index . '" data-block-index="' . $index . '">' . $inner . '</div>';
    }
    return '<div class="bpos-' . $index . '" data-block-index="' . $index . '">' . $inner . '</div>';
}

function builder_render_block_inner($block, $bank = null) {
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $func = 'block_' . $type . '_render';
    if (function_exists($func)) {
        $ref = new ReflectionFunction($func);
        return $ref->getNumberOfParameters() >= 2
            ? call_user_func($func, $data, $bank)
            : call_user_func($func, $data);
    }
    return '<div style="padding:20px;background:#fff3e0;border:1px solid #FF6F00;border-radius:8px;margin:16px 0;color:#e65100;text-align:center;">بلاک «' . htmlspecialchars($type) . '» فعال نیست</div>';
}

function builder_render_blocks_api() {
    header('Content-Type: application/json; charset=utf-8');
    if (function_exists('session_write_close')) { @session_write_close(); }
    /* پشتیبانی از multipart و JSON و آرایه مستقیم */
    $raw = $_POST['blocks'] ?? file_get_contents('php://input');
    $decoded = is_array($raw) ? $raw : (json_decode((string)$raw, true) ?: []);
    if (isset($decoded['blocks']) && is_array($decoded['blocks'])) { $decoded = $decoded['blocks']; }
    $blocks = is_array($decoded) ? $decoded : [];
    $out = [];
    foreach ($blocks as $b) {
        $out[] = builder_render_block_inner($b);
    }
    echo json_encode(['html' => $out], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ===== آپلود تصویر از داخل صفحه‌ساز (به کتابخانه فایلها) ===== */
function builder_upload_image() {
    header('Content-Type: application/json; charset=utf-8');
    if (!function_exists('upload_download_file')) {
        require_once MASIR_RISH . 'mohtava/file/file-functions.php';
    }
    $res = upload_download_file('image', 'builder/');
    if (!empty($res['error'])) {
        echo json_encode(['success' => false, 'message' => $res['error']], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $url = isset($res['url']) ? $res['url'] : FILES_URL . 'builder/' . ($res['name'] ?? '');
    echo json_encode(['success' => true, 'url' => $url], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ===== لیست تصاویر کتابخانه برای انتخابگر صفحه‌ساز ===== */
function builder_list_images() {
    header('Content-Type: application/json; charset=utf-8');
    if (function_exists('session_write_close')) { @session_write_close(); }
    if (!defined('FILES_DIR')) { require_once MASIR_DADE . '../haste/tanzimat.php'; }
    $exts = ['jpg','jpeg','png','gif','webp','svg','avif'];
    $urls = [];
    $dir = defined('FILES_DIR') ? FILES_DIR : (UPLOADS_DIR . 'files/');
    $base_len = strlen(str_replace('\\', '/', $dir));
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $f) {
        if (!$f->isFile()) continue;
        $e = strtolower(pathinfo($f->getFilename(), PATHINFO_EXTENSION));
        if (!in_array($e, $exts, true)) continue;
        $rel = ltrim(str_replace('\\', '/', substr(str_replace('\\', '/', $f->getPathname()), $base_len)), '/');
        $urls[] = ['url' => FILES_URL . $rel, 'time' => $f->getMTime()];
        if (count($urls) >= 300) break;
    }
    usort($urls, function($a,$b){ return $b['time'] <=> $a['time']; });
    echo json_encode(['success' => true, 'images' => array_map(function($x){ return $x['url']; }, $urls)], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ===== تنظیمات صفحه‌ساز (فاصله ذخیره خودکار و ...) ===== */
function builder_get_settings_value() {
    $defaults = ['autosave_enabled' => 1, 'autosave_min' => 10];
    $s = function_exists('site_settings_all') ? site_settings_all() : null;
    if (is_array($s) && isset($s['builder'])) {
        return array_merge($defaults, is_array($s['builder']) ? $s['builder'] : []);
    }
    if (defined('SITE_SETTINGS_FILE') && file_exists(SITE_SETTINGS_FILE)) {
        $j = json_decode((string)file_get_contents(SITE_SETTINGS_FILE), true);
        if (is_array($j) && isset($j['builder'])) return array_merge($defaults, is_array($j['builder']) ? $j['builder'] : []);
    }
    return $defaults;
}

function builder_save_settings() {
    header('Content-Type: application/json; charset=utf-8');
    if (!defined('SITE_SETTINGS_FILE')) { echo json_encode(['success'=>false]); exit; }
    $enabled = !empty($_POST['autosave_enabled']) ? 1 : 0;
    $min = max(1, min(120, (int)($_POST['autosave_min'] ?? 10)));
    $j = [];
    if (file_exists(SITE_SETTINGS_FILE)) {
        $j = json_decode((string)file_get_contents(SITE_SETTINGS_FILE), true);
        if (!is_array($j)) $j = [];
    }
    $j['builder'] = ['autosave_enabled' => $enabled, 'autosave_min' => $min];
    @file_put_contents(SITE_SETTINGS_FILE, json_encode($j, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), LOCK_EX);
    echo json_encode(['success' => true]);
    exit;
}

function builder_get_page_id($page_type, $page_slug) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $table = in_array($page_type, ['blog', 'maghaleh', 'safhe', 'page', 'khadamat'], true) ? 'posts'
           : ($page_type === 'mahsul' ? 'mahsulat' : 'posts');
    if ($page_slug) {
        $stmt = $conn->prepare("SELECT p.id, bp.id AS bp_id FROM $table p LEFT JOIN block_pages bp ON bp.page_id = p.id AND bp.page_type = ? WHERE p.slug = ? LIMIT 1");
        $stmt->bind_param("ss", $page_type, $page_slug);
    } else {
        $stmt = $conn->prepare("SELECT p.id, bp.id AS bp_id FROM $table p LEFT JOIN block_pages bp ON bp.page_id = p.id AND bp.page_type = ? WHERE p.type = ? LIMIT 1");
        $stmt->bind_param("ss", $page_type, $page_type);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $row;
}
