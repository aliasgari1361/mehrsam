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

    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .builder-wrap { display:grid; grid-template-columns:var(--canvas-w,1fr) 6px 1fr; gap:0; min-height:80vh; transition:grid-template-columns 0.05s; }
        .builder-splitter { width:6px; cursor:col-resize; background:#eef0f4; position:relative; transition:background 0.15s; }
        .builder-splitter:hover, .builder-splitter.dragging { background:var(--rang-asli,#FF6F00); }
        .builder-splitter::after { content:''; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:2px; height:30px; border-radius:2px; background:#c2c8d0; }
        .builder-splitter:hover::after, .builder-splitter.dragging::after { background:#fff; }
        .builder-canvas { background:#fff; border:1px solid #eef0f4; border-radius:0; padding:20px; min-height:500px; overflow:auto; border-left:1px solid #eef0f4; }
        .builder-preview { background:#fff; border:1px solid #eef0f4; border-radius:0; overflow:hidden; display:flex; flex-direction:column; transition:opacity 0.3s,width 0.3s,padding 0.3s; }
        .preview-toolbar { display:flex; align-items:center; gap:8px; padding:8px 14px; background:#f8f9fa; border-bottom:1px solid #eef0f4; font-size:13px; font-weight:600; color:#555; }
        .builder-preview iframe { flex:1; min-height:500px; transition:width 0.2s; }
        .canvas-toolbar { display:flex; flex-wrap:wrap; align-items:center; gap:8px; padding:10px 0; margin-bottom:16px; border-bottom:1px solid #eef0f4; }
        .block-chips { display:flex; flex-wrap:wrap; gap:6px; flex:1; }
        .block-chips .chip { padding:6px 12px; background:#f8f9fa; border:1px solid #eef0f4; border-radius:20px; cursor:pointer; display:flex; align-items:center; gap:6px; font-size:12px; transition:all 0.2s; white-space:nowrap; }
        .block-chips .chip:hover { background:var(--rang-roshan,#fff3e0); border-color:var(--rang-asli,#FF6F00); }
        .block-chips .chip .dot { width:10px;height:10px;border-radius:50%;display:inline-block;flex-shrink:0; }
        .canvas-actions { display:flex; gap:6px; }
        .canvas-actions button { padding:6px 12px; border:1px solid #dde1e6; border-radius:6px; background:#f5f6f8; cursor:pointer; font-size:11px; color:#555; transition:all 0.15s; white-space:nowrap; }
        .canvas-actions button:hover { border-color:var(--rang-asli,#FF6F00); color:var(--rang-asli,#FF6F00); }
        .block-item { background:#f8f9fa; border:2px solid #eef0f4; border-radius:10px; margin-bottom:16px; padding:16px; cursor:move; position:relative; transition:all 0.2s; }
        .block-item:hover { border-color:var(--rang-asli,#FF6F00); }
        .block-item.dragging { opacity:0.5; }
        .block-item .block-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
        .block-item .block-title { font-weight:700; font-size:14px; }
        .block-item.drag-over { border-color:var(--rang-asli,#FF6F00); border-style:dashed; }
        .block-item .block-content-preview svg { max-width:80px; max-height:30px; }
        .block-footer { display:flex; align-items:center; gap:8px; padding:10px 12px; margin-top:12px; background:#f0f2f5; border:1px solid #e0e4e8; border-radius:8px; }
        .block-footer button { background:#fff; border:1px solid #dde1e6; cursor:pointer; padding:6px 12px; border-radius:6px; font-size:13px; color:#555; transition:all 0.15s; display:flex; align-items:center; gap:4px; }
        .block-footer button:hover { background:#e9ecef; border-color:var(--rang-asli,#FF6F00); color:#333; }
        .block-footer button.danger { color:#c62828; }
        .block-footer button.danger:hover { background:#ffebee; border-color:#c62828; color:#c62828; }
        #blocksContainer > .block-item { box-sizing:border-box; }
        .insert-zone { user-select:none; }
        .insert-zone:hover .fa-plus-circle { color:var(--rang-asli); }
        .empty-canvas { text-align:center; padding:60px 20px; color:#aaa; }
        .empty-canvas i { font-size:48px; display:block; margin-bottom:16px; }
        .btn-save-blocks { position:fixed; bottom:24px; left:24px; z-index:100; padding:14px 32px; background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:12px; font-weight:700; font-size:15px; cursor:pointer; box-shadow:0 4px 20px rgba(255,111,0,0.4); transition:all 0.3s; }
        .btn-save-blocks:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(255,111,0,0.5); }
        .btn-save-blocks:disabled { opacity:0.6; cursor:wait; }
        <?php foreach ($available_blocks as $bk => $bv): ?>
        .block-item.type-<?= $bk ?> { border-right:4px solid <?= $bv['color'] ?>; }
        <?php endforeach; ?>

        /* ===== حالت بوم آزاد ===== */
        .builder-toolbar { display:flex; flex-wrap:wrap; align-items:center; gap:10px; background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:10px 14px; margin-bottom:16px; }
        .builder-toolbar .seg { display:flex; gap:4px; background:#f5f6f8; padding:4px; border-radius:8px; }
        .builder-toolbar .seg button { border:none; background:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px; color:#555; transition:all 0.15s; }
        .builder-toolbar .seg button:hover { background:#e9ecef; }
        .builder-toolbar .seg button.active { background:var(--rang-asli,#FF6F00); color:#fff; }
        .builder-toolbar .tool-label { font-size:12px; color:#888; font-weight:600; }
        .builder-canvas.free { padding:0; overflow:auto; }
        .free-surface { position:relative; margin:0; min-height:600px; background:#fafafa; background-image:linear-gradient(#ececec 1px,transparent 1px),linear-gradient(90deg,#ececec 1px,transparent 1px); background-size:20px 20px; box-shadow:inset 0 0 0 1px #eef0f4; }
        .free-hscroll-hint { font-size:11px; color:#999; padding:6px 12px; background:#f8f9fa; border-top:1px solid #eef0f4; direction:ltr; text-align:center; }
        .block-item.free { position:absolute; margin:0; padding:0; background:rgba(255,255,255,0.92); border:1px dashed var(--rang-asli,#FF6F00); border-radius:6px; box-sizing:border-box; }
        .block-item.free .block-content-preview { max-height:none; overflow:visible; padding:8px; min-height:30px; pointer-events:none; }
        .block-item.free.selected .block-content-preview { pointer-events:auto; }
        .block-item.free .block-header { padding:4px 8px; margin-bottom:0; background:rgba(255,255,255,0.95); border-bottom:1px solid #eef0f4; border-radius:6px 6px 0 0; z-index:10; position:relative; cursor:move; }
        .block-item.free.selected { border:2px solid var(--rang-asli,#FF6F00); box-shadow:0 0 0 3px rgba(255,111,0,0.18); z-index:9999 !important; }
        .resize-handle { position:absolute; left:0; bottom:0; width:16px; height:16px; background:var(--rang-asli,#FF6F00); cursor:nwse-resize; border-radius:0 0 6px 0; z-index:12; }
        .pos-panel { position:fixed; bottom:24px; right:24px; z-index:200; background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:14px; box-shadow:0 8px 30px rgba(0,0,0,0.15); width:240px; display:none; }
        .pos-panel h5 { margin:0 0 10px; font-size:13px; }
        .pos-panel .row { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
        .pos-panel .row label { font-size:11px; color:#888; width:18px; }
        .pos-panel .row input { flex:1; padding:6px 8px; border:1px solid #dde1e6; border-radius:6px; width:100%; font-size:13px; }
        .pos-panel .layer-btns { display:flex; gap:6px; margin-top:6px; }
        .pos-panel .layer-btns button { flex:1; padding:8px; border:1px solid #dde1e6; border-radius:6px; background:#f5f6f8; cursor:pointer; font-size:12px; }
        .pos-panel .layer-btns button:hover { border-color:var(--rang-asli,#FF6F00); }
        .free-add { position:absolute; top:8px; left:8px; z-index:50; padding:6px 12px; background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:12px; }
        .free-hint { position:absolute; top:8px; right:8px; z-index:50; font-size:11px; color:#aaa; background:rgba(255,255,255,0.8); padding:2px 8px; border-radius:4px; }
        .btn-delete-block { width:100%; padding:10px; margin-top:8px; background:#fff0f0; border:1px solid #ef9a9a; border-radius:8px; color:#c62828; cursor:pointer; font-size:13px; font-weight:600; transition:all 0.15s; }
        .btn-delete-block:hover { background:#ffebee; border-color:#c62828; }
        .builder-wrap.hide-chips .block-chips { display:none; }
        .builder-wrap.hide-canvas { grid-template-columns:0px 6px 1fr; }
        .builder-wrap.hide-canvas .builder-canvas { overflow:hidden; padding:0; width:0; opacity:0; border:0; }
        .builder-wrap.hide-canvas .builder-splitter { display:none; }
        .builder-wrap.hide-preview { grid-template-columns:var(--canvas-w,1fr) 6px 0px; }
        .builder-wrap.hide-preview .builder-preview { overflow:hidden; padding:0; width:0; opacity:0; }
        .builder-wrap.hide-preview .builder-splitter { display:none; }
        .builder-preview.fullscreen { position:fixed; inset:0; z-index:9999; border-radius:0; }
        .builder-preview.fullscreen iframe { min-height:100vh; }
    </style>

    <h3>صفحه‌ساز: <?= htmlspecialchars($bp['page_type']) ?> #<?= $bp['page_id'] ?></h3>
    <p><a href="<?= BASE_URL ?>mod/builder/pages" style="color:var(--rang-asli,#FF6F00);">&larr; بازگشت</a></p>

    <!-- منوی سریع انتخاب قالب -->
    <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;flex-wrap:wrap;align-items:center;gap:10px;">
        <span style="font-weight:700;font-size:13px;color:#555;"><i class="fa-solid fa-layer-group"></i> انتخاب قالب:</span>
        <select onchange="if(this.value) location.href='<?= BASE_URL ?>mod/builder/edit/'+this.value" style="padding:8px 12px;border:1.5px solid #dde1e6;border-radius:8px;font-size:13px;min-width:240px;">
            <?php foreach ($all_themes as $th): ?>
            <option value="<?= $th['id'] ?>" <?= ($th['id'] == $bp['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($th['name'] ?: ('تم #' . $th['id'])) ?> — <?= builder_condition_label($th) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <a href="<?= BASE_URL ?>mod/builder/new" style="padding:8px 14px;background:var(--rang-asli,#FF6F00);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap;"><i class="fa-solid fa-plus"></i> قالب جدید</a>
        <a href="<?= BASE_URL ?>mod/builder/pages" style="padding:8px 14px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;font-size:13px;text-decoration:none;color:#555;white-space:nowrap;">همه قالب‌ها</a>
    </div>

    <!-- پنل شرط نمایش تم (فقط برای تم‌ها، نه صفحه‌های محتوایی) -->
    <?php if (empty($bp['page_id'])): ?>
    <form method="post" action="<?= BASE_URL ?>mod/builder/save_condition" style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-bottom:20px;max-width:600px;">
        <h4 style="margin-bottom:12px;"><i class="fa-solid fa-filter"></i> شرط نمایش این تم</h4>
        <input type="hidden" name="bp_id" value="<?= $bp['id'] ?>">
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;">نام تم</label>
                <input type="text" name="name" value="<?= htmlspecialchars($bp['name'] ?? '') ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;">بخش تم</label>
                <select name="part" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    <option value="" <?= empty($bp['part']) ? 'selected' : '' ?>>قالب محتوا (عمومی)</option>
                    <option value="header" <?= ($bp['part'] ?? '') === 'header' ? 'selected' : '' ?>>هدر (Header)</option>
                    <option value="footer" <?= ($bp['part'] ?? '') === 'footer' ? 'selected' : '' ?>>پانویس (Footer)</option>
                    <option value="single" <?= ($bp['part'] ?? '') === 'single' ? 'selected' : '' ?>>قالب صفحه تکی</option>
                    <option value="archive" <?= ($bp['part'] ?? '') === 'archive' ? 'selected' : '' ?>>قالب آرشیو</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">نوع شرط</label>
                    <select name="condition_type" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                        <option value="archive" <?= ($bp['condition_type'] ?? 'single') === 'archive' ? 'selected' : '' ?>>آرشیو</option>
                        <option value="single" <?= ($bp['condition_type'] ?? 'single') === 'single' ? 'selected' : '' ?>>صفحه تکی</option>
                        <option value="global" <?= ($bp['condition_type'] ?? 'single') === 'global' ? 'selected' : '' ?>>سراسری</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">مقدار شرط</label>
                    <input type="text" name="condition_value" value="<?= htmlspecialchars($bp['condition_value'] ?? '') ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="blog / product / khadamat / slug خاص">
                </div>
            </div>
        <p style="font-size:12px;color:#888;margin:8px 0 12px;">مثال‌ها — آرشیو: <code>blog</code>, <code>product</code>, <code>khadamat</code> &nbsp;|&nbsp; تکی: <code>post</code>, <code>product</code>, <code>khadamat</code>, <code>safhe</code>, یا slug یک صفحه خاص &nbsp;|&nbsp; سراسری: <code>*</code></p>
        <button type="submit" style="padding:10px 24px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;cursor:pointer;font-weight:600;">ذخیره شرط نمایش</button>
    </form>
    <?php endif; ?>

    <div>
    <div class="builder-toolbar">
        <span class="tool-label">حالت:</span>
        <div class="seg" id="modeSeg">
            <button type="button" data-mode="0" class="<?= empty($bp['position_mode']) ? 'active' : '' ?>" onclick="setPositionMode(0)">چیده‌شده</button>
            <button type="button" data-mode="1" class="<?= !empty($bp['position_mode']) ? 'active' : '' ?>" onclick="setPositionMode(1)">آزاد (بوم)</button>
        </div>
        <span class="tool-label" style="margin-right:8px;">دستگاه:</span>
        <div class="seg" id="deviceSeg">
            <?php foreach (builder_devices() as $dk => $dv): ?>
            <button type="button" data-dev="<?= $dk ?>" class="<?= $dk === 'desktop' ? 'active' : '' ?>" onclick="switchDevice('<?= $dk ?>')" title="<?= $dv['label'] ?> (<?= $dv['w'] ?>px)"><?= $dv['label'] ?></button>
            <?php endforeach; ?>
        </div>
        <span class="tool-label" style="margin-right:8px;">موبایل:</span>
        <div class="seg" id="mobileSeg">
            <button type="button" data-mm="auto" class="<?= ($bp['mobile_mode'] ?? 'auto') === 'auto' ? 'active' : '' ?>" onclick="setMobileMode('auto')">خودکار</button>
            <button type="button" data-mm="exact" class="<?= ($bp['mobile_mode'] ?? 'auto') === 'exact' ? 'active' : '' ?>" onclick="setMobileMode('exact')">دقیق</button>
        </div>
        <span style="flex:1;"></span>
        <div class="seg">
            <button type="button" onclick="toggleSidebar()" title="نمایش/مخفی چیپس‌های بلاک"><i class="fa-solid fa-layer-group"></i></button>
            <button type="button" onclick="toggleCanvas()" title="نمایش/مخفی بلاک‌ها"><i class="fa-solid fa-cubes"></i></button>
            <button type="button" onclick="togglePreview()" title="نمایش/مخفی پیشنمایش"><i class="fa-solid fa-eye-slash"></i></button>
            <button type="button" onclick="fullscreenPreview()" title="تمام‌صفحه پیشنمایش"><i class="fa-solid fa-expand"></i></button>
        </div>
    </div>

    <div class="builder-wrap" id="builderWrap">
        <div class="builder-canvas" id="builderCanvas">
            <div class="canvas-toolbar">
                <div class="block-chips">
                    <?php foreach ($available_blocks as $bk => $bv): ?>
                    <div class="chip" onclick="addBlock('<?= $bk ?>')" title="<?= $bv['desc'] ?>">
                        <span class="dot" style="background:<?= $bv['color'] ?>;"></span>
                        <?= $bv['label'] ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="canvas-actions">
                    <button onclick="toggleSidebar()" title="نمایش/مخفی چیپس‌های بلاک"><i class="fa-solid fa-layer-group"></i></button>
                    <button onclick="convertToFree()" title="تبدیل به حالت آزاد"><i class="fa-solid fa-arrows-alt"></i> آزاد</button>
                    <button onclick="clearCache(<?= $bp['id'] ?>)" title="پاک کردن کش"><i class="fa-solid fa-rotate"></i></button>
                    <button onclick="refreshPreview()" title="بروزرسانی پیشنمایش"><i class="fa-solid fa-eye"></i></button>
                    <button onclick="window.open('<?= BASE_URL ?>mod/builder/preview/<?= $bp['id'] ?>','_blank')" title="پیشنمایش در تب جدید"><i class="fa-solid fa-up-right-from-square"></i></button>
                </div>
            </div>
            <div id="freeSurface" class="free-surface" style="display:none;"></div>
            <div id="blocksContainer"><?php if (empty($blocks)): ?>
                <div class="insert-zone" data-index="-1" onclick="showInsertPicker(this)" style="padding:10px;text-align:center;border:2px dashed #ddd;border-radius:8px;cursor:pointer;color:#aaa;font-size:13px;transition:all 0.2s;" onmouseover="this.style.borderColor=\'var(--rang-asli)\'" onmouseout="this.style.borderColor=\'#ddd\'"><i class="fa-solid fa-plus"></i> افزودن بلاک</div>
                <?php else: ?>
                <?php $bi = 0; foreach ($blocks as $i => $block): ?>
                <div class="insert-zone" data-index="<?= $i ?>" onclick="showInsertPicker(this,<?= $i ?>)" style="padding:6px;text-align:center;cursor:pointer;color:#ccc;font-size:12px;transition:all 0.2s;" onmouseover="this.style.color=\'var(--rang-asli)\'" onmouseout="this.style.color=\'#ccc\'"><i class="fa-solid fa-plus-circle"></i> درج بلاک</div>
                <div class="block-item type-<?= $block['type'] ?>" data-index="<?= $i ?>" draggable="true"><?php render_block_admin_full($block); ?></div>
                <?php $bi++; endforeach; ?>
                <div class="insert-zone" data-index="-2" onclick="showInsertPicker(this)" style="padding:6px;text-align:center;cursor:pointer;color:#ccc;font-size:12px;transition:all 0.2s;" onmouseover="this.style.color=\'var(--rang-asli)\'" onmouseout="this.style.color=\'#ccc\'"><i class="fa-solid fa-plus-circle"></i> افزودن بلاک</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="builder-splitter" id="builderSplitter" title="کشیدن برای تغییر عرض"></div>
        <div class="builder-preview" id="builderPreview">
            <div class="preview-toolbar">
                <span><i class="fa-solid fa-eye"></i> پیشنمایش زنده</span>
                <span style="font-size:11px;color:#999;">(ذخیره → بروزرسانی خودکار)</span>
            </div>
            <iframe id="previewFrame" src="<?= BASE_URL ?>mod/builder/preview/<?= $bp['id'] ?>?edit=1" frameborder="0" style="width:100%;height:calc(100% - 36px);border:none;"></iframe>
        </div>
    </div>

    <button class="btn-save-blocks" id="saveBlocksBtn" onclick="saveBlocks(<?= $bp['id'] ?>)"><i class="fa-solid fa-save"></i> ذخیره تغییرات</button>
    <div class="pos-panel" id="posPanel">
        <h5>موقعیت بلاک (<span id="posDevLabel">دسکتاپ</span>)</h5>
        <div class="row"><label>X</label><input type="number" id="posX" oninput="posInput()"></div>
        <div class="row"><label>Y</label><input type="number" id="posY" oninput="posInput()"></div>
        <div class="row"><label>W</label><input type="number" id="posW" oninput="posInput()"></div>
        <div class="row"><label>Z</label><input type="number" id="posZ" oninput="posInput()"></div>
        <div class="layer-btns">
            <button type="button" onclick="layerBlock(1)" title="جلو">جلو ⇡</button>
            <button type="button" onclick="layerBlock(-1)" title="عقب">عقب ⇣</button>
            <button type="button" onclick="deselectBlock()" title="بستن">✕</button>
        </div>
        <button type="button" class="btn-delete-block" onclick="deleteSelectedBlock()"><i class="fa-solid fa-trash"></i> حذف بلاک</button>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
    var blocksData = <?= json_encode($blocks, JSON_UNESCAPED_UNICODE) ?>;
    var blockTypes = <?= json_encode($available_blocks, JSON_UNESCAPED_UNICODE) ?>;
    var devices = <?= json_encode(builder_devices(), JSON_UNESCAPED_UNICODE) ?>;
    var positionMode = <?= !empty($bp['position_mode']) ? 'true' : 'false' ?>;
    var mobileMode = '<?= $bp['mobile_mode'] ?? 'auto' ?>';
    var currentDevice = 'desktop';
    var currentBP = 'desktop';
    var selectedIndex = -1;
    var AUTOSAVE_ID = <?= (int)$bp['id'] ?>;

    function mergePos(base, over) {
        var o = {};
        for (var k in base) o[k] = base[k];
        for (var k in over) o[k] = over[k];
        return o;
    }

    function effectivePos(block, bp) {
        var pos = block.pos || {};
        var order = ['wide', 'desktop', 'tablet', 'mobile'];
        var idx = {}; for (var i = 0; i < order.length; i++) idx[order[i]] = i;
        var ti = idx[bp];
        var prio = [bp];
        for (var j = ti - 1; j >= 0; j--) prio.push(order[j]);
        for (var j = ti + 1; j < 4; j++) prio.push(order[j]);
        var v = null;
        for (var k = 0; k < prio.length; k++) { if (pos[prio[k]]) { v = pos[prio[k]]; break; } }
        return v ? mergePos({x:0,y:0,w:300,z:1}, v) : null;
    }

    function renderBlockAdmin(block) {
        var bt = blockTypes[block.type] || {label: block.type, icon: 'fa-cube', color: '#888'};
        var data = block.data || {};
        var html = '<div class="block-header">';
        html += '<div class="block-title"><span style="display:inline-block;width:28px;height:28px;border-radius:6px;background:' + bt.color + ';color:#fff;text-align:center;line-height:28px;margin-left:8px;font-size:13px;"><i class="fa-solid ' + bt.icon + '"></i></span>' + bt.label + '</div>';
        html += '</div>';
        html += '<div class="block-content-preview" style="font-size:13px;color:#666;">' + getBlockPreview(block) + '</div>';
        html += '<div class="block-footer">';
        html += '<button onclick="openEditModal(parseInt(this.closest(\'.block-item\').dataset.index))" title="ویرایش"><i class="fa-solid fa-pen"></i> ویرایش</button>';
        html += '<button class="danger" onclick="removeBlock(event)" title="حذف"><i class="fa-solid fa-trash"></i> حذف</button>';
        html += '</div>';
        html += '<div class="block-data" style="display:none;">' + JSON.stringify(data) + '</div>';
        return html;
    }

    function getBlockPreview(block) {
        var data = block.data || {};
        switch (block.type) {
            case 'heading': return '<h' + (data.level || 2) + '>' + (data.text || '...') + '</h' + (data.level || 2) + '>';
            case 'text': return (data.content || '').substring(0, 100) + '...';
            case 'image': return '<img src="' + (data.src || '') + '" alt="" style="max-width:100px;max-height:40px;">';
            case 'gallery': return (data.images || []).length + ' تصویر';
            case 'button': return '[ دکمه: ' + (data.text || '') + ' ]';
            case 'services': return 'نمایش ' + (data.count || 6) + ' خدمت';
            case 'products': return 'نمایش ' + (data.count || 8) + ' محصول';
            case 'custom': return 'HTML سفارشی (' + ((data.html || '').length) + ' کاراکتر)';
            case 'columns': return (data.columns || 2) + ' ستونی';
            case 'video': return 'ویدیو: ' + (data.url || '');
            case 'divider': return 'جداکننده';
            default: return '...';
        }
    }

    function addBlock(type) { addBlockAt(type, -1); }

    function addBlockAt(type, index) {
        var bt = blockTypes[type];
        var defaultData = (bt && bt.defaults) ? bt.defaults : {};
        var block = {type: type, data: defaultData};
        if (positionMode) {
            block.pos = {};
            var sw = devices[currentDevice].w;
            block.pos[currentBP] = {x: Math.max(0, Math.round(sw/2 - 150)), y: 40 + blocksData.length * 12, w: 300, z: blocksData.length + 1};
        }
        var insertedIdx;
        if (index >= 0 && index < blocksData.length) {
            blocksData.splice(index, 0, block);
            insertedIdx = index;
        } else {
            blocksData.push(block);
            insertedIdx = blocksData.length - 1;
        }
        if (positionMode) {
            var surface = document.getElementById('freeSurface');
            var div = createFreeBlockElement(block, insertedIdx);
            var next = surface.querySelectorAll('.block-item.free');
            if (insertedIdx < next.length - 1 && next[insertedIdx]) {
                surface.insertBefore(div, next[insertedIdx]);
            } else {
                surface.appendChild(div);
            }
            updateFreeIndices();
            refreshCanvasContent();
        } else {
            renderStacked();
        }
    }

    function showInsertPicker(el, index) {
        var existing = document.querySelector('.insert-picker');
        if (existing) { existing.remove(); return; }
        var picker = document.createElement('div');
        picker.className = 'insert-picker';
        picker.style.cssText = 'position:fixed;background:#fff;border:1px solid #ddd;border-radius:8px;padding:8px;box-shadow:0 4px 20px rgba(0,0,0,0.12);z-index:100;display:grid;grid-template-columns:repeat(3,1fr);gap:4px;min-width:200px;';
        document.addEventListener('click', function closePicker(e) {
            if (!picker.contains(e.target) && e.target !== el) { picker.remove(); document.removeEventListener('click', closePicker); }
        });
        for (var k in blockTypes) {
            var b = blockTypes[k];
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.style.cssText = 'padding:8px;border:1px solid #eef0f4;border-radius:6px;cursor:pointer;background:#f8f9fa;font-size:12px;text-align:center;';
            btn.innerHTML = '<div style="width:24px;height:24px;border-radius:4px;background:' + b.color + ';color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 4px;font-size:12px;"><i class="fa-solid ' + b.icon + '"></i></div>' + b.label;
            (function(kt) { btn.onclick = function() { addBlockAt(kt, index); picker.remove(); }; })(k);
            picker.appendChild(btn);
        }
        var rect = el.getBoundingClientRect();
        picker.style.top = (rect.bottom + 4) + 'px';
        picker.style.left = (rect.left + (rect.width/2) - 100) + 'px';
        document.body.appendChild(picker);
    }

    function removeBlock(e) {
        if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
        var el = e && e.currentTarget ? e.currentTarget : e;
        var item = el.closest('.block-item');
        if (!item) return;
        var idx = parseInt(item.dataset.index);
        blocksData.splice(idx, 1);
        if (selectedIndex === idx) deselectBlock();
        else if (selectedIndex > idx) selectedIndex--;
        if (positionMode) {
            item.remove();
            updateFreeIndices();
        } else {
            renderStacked();
        }
    }

    function openEditModal(idx) {
        if (idx === undefined || idx === null || isNaN(idx)) return;
        editingBlockIndex = idx;
        var block = blocksData[editingBlockIndex];
        if (!block) return;
        var data = block.data || {};
        var bt = blockTypes[block.type] || {label: block.type};
        var fields = bt.fields || [];
        var html = '<div class="builder-edit-modal" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;" onclick="if(event.target===this)this.remove()">';
        html += '<div style="background:#fff;border-radius:16px;padding:32px;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;">';
        html += '<h3 style="margin-bottom:16px;">ویرایش «' + bt.label + '»</h3>';
        if (fields.length === 0) html += '<p style="color:#888;">این بلاک تنظیمات خاصی ندارد.</p>';
        fields.forEach(function(f) {
            var val = data[f.key] !== undefined ? data[f.key] : (f.default || '');
            html += '<div style="margin-bottom:14px;">';
            html += '<label style="display:block;margin-bottom:4px;font-weight:600;">' + f.label + '</label>';
            if (f.type === 'textarea' || f.type === 'html') {
                html += '<textarea class="edit-field" data-key="' + f.key + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;min-height:' + (f.type === 'html' ? '150' : '80') + 'px;">' + val + '</textarea>';
            } else if (f.type === 'select') {
                html += '<select class="edit-field" data-key="' + f.key + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">';
                (f.options || []).forEach(function(o) { html += '<option value="' + o.value + '" ' + (val == o.value ? 'selected' : '') + '>' + o.label + '</option>'; });
                html += '</select>';
            } else if (f.type === 'color') {
                html += '<input type="color" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="width:60px;height:40px;border:none;border-radius:6px;cursor:pointer;">';
            } else if (f.type === 'number') {
                html += '<input type="number" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">';
            } else if (f.type === 'image') {
                html += '<div style="display:flex;gap:8px;"><input type="text" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="flex:1;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="URL تصویر"><button type="button" onclick="selectImage(this)" style="padding:10px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;cursor:pointer;">انتخاب</button></div>';
            } else {
                html += '<input type="text" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">';
            }
            html += '</div>';
        });
        html += '<div style="display:flex;gap:10px;margin-top:20px;">';
        html += '<button type="button" onclick="saveEdit(this)" style="padding:12px 24px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-check"></i> اعمال</button>';
        html += '<button type="button" onclick="this.closest(\'.builder-edit-modal\').remove()" style="padding:12px 24px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;cursor:pointer;">انصراف</button>';
        html += '</div></div></div>';
        var modal = document.createElement('div');
        modal.innerHTML = html;
        document.body.appendChild(modal);
    }

    var editingBlockIndex = -1;

    function saveEdit(btn) {
        var modal = btn.closest('[style*="fixed"]');
        var fields = modal.querySelectorAll('.edit-field');
        var data = {};
        fields.forEach(function(f) { data[f.dataset.key] = f.value; });
        if (blocksData[editingBlockIndex]) blocksData[editingBlockIndex].data = data;
        renderAllBlocks();
        modal.remove();
        schedulePreviewRefresh();
    }

    function renderAllBlocks() {
        if (positionMode) renderFree();
        else renderStacked();
    }

    // ===== ویرایش درجا از درون iframe پیش‌نمایش (postMessage) =====
    function setBlockContent(idx, key, value) {
        if (!blocksData[idx]) return;
        blocksData[idx].data = blocksData[idx].data || {};
        blocksData[idx].data[key] = value;
        schedulePreviewRefresh();
    }
    function deleteBlockByIndex(idx) {
        if (!blocksData[idx]) return;
        blocksData.splice(idx, 1);
        if (positionMode) renderFree(); else renderStacked();
        schedulePreviewRefresh();
    }
    function moveBlock(idx, dir) {
        var j = idx + (dir === 'up' ? -1 : 1);
        if (j < 0 || j >= blocksData.length) return;
        var t = blocksData[idx]; blocksData[idx] = blocksData[j]; blocksData[j] = t;
        if (positionMode) renderFree(); else renderStacked();
        schedulePreviewRefresh();
    }
    function applyReorder(order) {
        if (!order || !order.length) return;
        var nb = [];
        for (var i = 0; i < order.length; i++) {
            var idx = order[i];
            if (blocksData[idx]) nb.push(blocksData[idx]);
        }
        if (nb.length !== blocksData.length) return;
        blocksData = nb;
        if (positionMode) renderFree(); else renderStacked();
        schedulePreviewRefresh();
    }
    function addBlockFromIframe(type, index) {
        addBlockAt(type, index);
    }
    var _previewTimer = null;
    var _autosaveTimer = null;
    function schedulePreviewRefresh() {
        if (_previewTimer) clearTimeout(_previewTimer);
        _previewTimer = setTimeout(refreshPreview, 600);
        if (_autosaveTimer) clearTimeout(_autosaveTimer);
        _autosaveTimer = setTimeout(function() { autoSaveBlocks(); }, 1500);
    }
    function autoSaveBlocks() {
        var fd = new FormData();
        fd.append('block_page_id', AUTOSAVE_ID);
        fd.append('blocks_data', JSON.stringify(blocksData));
        fd.append('cache', '1');
        fd.append('position_mode', positionMode ? '1' : '0');
        fd.append('mobile_mode', mobileMode);
        fetch('<?= BASE_URL ?>mod/builder/save', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                var btn = document.getElementById('saveBlocksBtn');
                if (btn) { btn.innerHTML = '<i class="fa-solid fa-check"></i> ذخیره خودکار'; setTimeout(function(){ if(btn) btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات'; }, 1500); }
            }
        });
    }
    window.addEventListener('message', function(e) {
        if (e.origin !== window.location.origin) return;
        var d = e.data || {};
        if (d.type === 'builderSelect') { openEditModal(d.index); }
        else if (d.type === 'builderContent') { setBlockContent(d.index, d.key, d.value); }
        else if (d.type === 'builderDelete') { deleteBlockByIndex(d.index); }
        else if (d.type === 'builderMove') { moveBlock(d.index, d.dir); }
        else if (d.type === 'builderReorder') { applyReorder(d.order); }
        else if (d.type === 'builderAdd') { addBlockFromIframe(d.btype, d.index); }
        else if (d.type === 'builderImageEdit') {
            if (blocksData[d.index]) {
                if (!blocksData[d.index].data) blocksData[d.index].data = {};
                blocksData[d.index].data.src = d.src;
                if (d.alt !== undefined) blocksData[d.index].data.alt = d.alt;
                refreshPreview();
                autoSaveBlocks();
            }
        }
        else if (d.type === 'builderButtonEdit') {
            if (blocksData[d.index]) {
                if (!blocksData[d.index].data) blocksData[d.index].data = {};
                blocksData[d.index].data.text = d.text;
                blocksData[d.index].data.url = d.href;
                refreshPreview();
                autoSaveBlocks();
            }
        }
        else if (d.type === 'builderReady') {
            var f = document.getElementById('previewFrame');
            if (f && f.contentWindow) f.contentWindow.postMessage({_ns:'builderInline', type:'builderSetContentFields', fields: blockContentFields()}, window.location.origin);
        }
    });
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

    function updateBlockCount() {
        var c = document.getElementById('blockCount');
        if (c) c.textContent = blocksData.length;
    }

    var sortable = null;
    function renderStacked() {
        var canvas = document.getElementById('builderCanvas');
        var container = document.getElementById('blocksContainer');
        var surface = document.getElementById('freeSurface');
        canvas.classList.remove('free');
        surface.style.display = 'none';
        var hscroll = document.getElementById('freeHScroll');
        if (hscroll) hscroll.style.display = 'none';
        container.style.display = 'block';
        container.innerHTML = '';
        if (blocksData.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'insert-zone';
            empty.setAttribute('onclick', 'showInsertPicker(this)');
            empty.innerHTML = '<i class="fa-solid fa-plus"></i> افزودن بلاک';
            container.appendChild(empty);
        } else {
            blocksData.forEach(function(block, i) {
                var iz = document.createElement('div');
                iz.className = 'insert-zone';
                iz.dataset.index = i;
                iz.setAttribute('onclick', 'showInsertPicker(this,' + i + ')');
                iz.innerHTML = '<i class="fa-solid fa-plus-circle"></i> درج بلاک';
                container.appendChild(iz);
                var div = document.createElement('div');
                div.className = 'block-item type-' + block.type;
                div.dataset.index = i;
                div.draggable = true;
                div.innerHTML = renderBlockAdmin(block);
                container.appendChild(div);
            });
            var iz2 = document.createElement('div');
            iz2.className = 'insert-zone';
            iz2.dataset.index = '-2';
            iz2.setAttribute('onclick', 'showInsertPicker(this)');
            iz2.innerHTML = '<i class="fa-solid fa-plus-circle"></i> افزودن بلاک';
            container.appendChild(iz2);
        }
        updateBlockCount();
        if (sortable) sortable.destroy();
        sortable = new Sortable(container, {
            animation: 150, handle: '.block-item', filter: '.insert-zone', ghostClass: 'drag-over',
            onEnd: function() { updateBlocksFromDOM(); }
        });
        refreshCanvasContent();
    }

    function updateBlocksFromDOM() {
        var items = document.querySelectorAll('#blocksContainer > .block-item');
        var nb = [];
        items.forEach(function(it) { var idx = parseInt(it.dataset.index); if (blocksData[idx]) nb.push(blocksData[idx]); });
        if (nb.length > 0) blocksData = nb;
        items.forEach(function(it, i) { it.dataset.index = i; });
        var zones = document.querySelectorAll('#blocksContainer > .insert-zone');
        zones.forEach(function(z, i) {
            if (i === 0) z.dataset.index = 0;
            else if (i === zones.length - 1) z.dataset.index = -2;
            else z.dataset.index = parseInt(items[i-1].dataset.index) + 1;
        });
        updateBlockCount();
    }

    function updateFreeIndices() {
        var items = document.querySelectorAll('#freeSurface .block-item.free');
        items.forEach(function(it, i) { it.dataset.index = i; });
        updateBlockCount();
    }

    function createFreeBlockElement(block, i) {
        var eff = effectivePos(block, currentBP) || {x: 20, y: 20 + i * 20, w: 300, z: i + 1};
        var div = document.createElement('div');
        div.className = 'block-item free type-' + block.type + (i === selectedIndex ? ' selected' : '');
        div.dataset.index = i;
        div.style.left = eff.x + 'px';
        div.style.top = eff.y + 'px';
        div.style.width = eff.w + 'px';
        div.style.zIndex = eff.z;
        div.innerHTML = renderBlockAdmin(block);
        var rh = document.createElement('div');
        rh.className = 'resize-handle';
        div.appendChild(rh);
        (function(el, rhEl) {
            el.addEventListener('mousedown', function(e) { startDrag(e, parseInt(el.dataset.index), el); });
            rhEl.addEventListener('mousedown', function(e) { startResize(e, parseInt(el.dataset.index), el); });
            el.addEventListener('click', function(e) {
                if (e.target.closest('.block-footer')) return;
                selectBlock(parseInt(el.dataset.index));
            });
        })(div, rh);
        return div;
    }

    function renderFree() {
        var canvas = document.getElementById('builderCanvas');
        var container = document.getElementById('blocksContainer');
        var surface = document.getElementById('freeSurface');
        canvas.classList.add('free');
        container.style.display = 'none';
        surface.style.display = 'block';
        surface.style.width = devices[currentDevice].w + 'px';
        surface.innerHTML = '';
        var addBtn = document.createElement('button');
        addBtn.className = 'free-add';
        addBtn.innerHTML = '<i class="fa-solid fa-plus"></i> بلاک';
        addBtn.onclick = function() { showInsertPickerSurface(); };
        surface.appendChild(addBtn);
        var hint = document.createElement('div');
        hint.className = 'free-hint';
        hint.textContent = 'حالت آزاد — بکش و رها کن · کلیک=انتخاب';
        surface.appendChild(hint);
        blocksData.forEach(function(block, i) {
            var div = createFreeBlockElement(block, i);
            surface.appendChild(div);
        });
        // گسترش عرض بوم تا دورترین بلاک (برای اسکرول افقی)
        var maxRight = devices[currentDevice].w;
        blocksData.forEach(function(block) {
            var p = effectivePos(block, currentBP);
            if (p) maxRight = Math.max(maxRight, (p.x + p.w));
        });
        surface.style.width = (maxRight + 60) + 'px';
        var hint = document.getElementById('freeHScroll');
        if (!hint) {
            hint = document.createElement('div');
            hint.id = 'freeHScroll';
            hint.className = 'free-hscroll-hint';
            canvas.appendChild(hint);
        }
        hint.textContent = 'بوم آزاد — برای جابجایی افقی، در اینجا اسکرول (چپ/راست) کنید · عرض: ' + Math.round(surface.offsetWidth) + 'px';
        surface.onclick = function(e) { if (e.target === surface) deselectBlock(); };
        updateBlockCount();
        refreshCanvasContent();
    }

    function showInsertPickerSurface() {
        showInsertPicker(document.querySelector('.free-add'), -1);
    }

    var dragState = null;
    function startDrag(e, idx, el) {
        if (e.target.closest('.block-footer') || e.target.classList.contains('resize-handle')) return;
        e.preventDefault();
        selectBlock(idx);
        dragState = { idx: idx, el: el, startX: e.clientX, startY: e.clientY, origX: parseFloat(el.style.left) || 0, origY: parseFloat(el.style.top) || 0, curX: 0, curY: 0, moved: false };
        document.addEventListener('mousemove', onDragMove);
        document.addEventListener('mouseup', onDragEnd);
    }
    function onDragMove(e) {
        if (!dragState) return;
        var dx = e.clientX - dragState.startX;
        var dy = e.clientY - dragState.startY;
        if (Math.abs(dx) > 2 || Math.abs(dy) > 2) dragState.moved = true;
        var nx = Math.max(0, dragState.origX + dx);
        var ny = Math.max(0, dragState.origY + dy);
        dragState.el.style.left = nx + 'px';
        dragState.el.style.top = ny + 'px';
        dragState.curX = nx; dragState.curY = ny;
    }
    function onDragEnd() {
        document.removeEventListener('mousemove', onDragMove);
        document.removeEventListener('mouseup', onDragEnd);
        if (dragState && dragState.moved) {
            var b = blocksData[dragState.idx];
            b.pos = b.pos || {}; b.pos[currentBP] = b.pos[currentBP] || {};
            b.pos[currentBP].x = Math.round(dragState.curX);
            b.pos[currentBP].y = Math.round(dragState.curY);
            syncPosPanel();
        }
        dragState = null;
    }

    var resizeState = null;
    function startResize(e, idx, el) {
        e.preventDefault();
        e.stopPropagation();
        resizeState = { idx: idx, el: el, startX: e.clientX, origW: parseFloat(el.style.width) || 300, curW: 0, moved: false };
        document.addEventListener('mousemove', onResizeMove);
        document.addEventListener('mouseup', onResizeEnd);
    }
    function onResizeMove(e) {
        if (!resizeState) return;
        var dw = e.clientX - resizeState.startX;
        if (Math.abs(dw) > 2) resizeState.moved = true;
        var nw = Math.max(40, resizeState.origW + dw);
        resizeState.el.style.width = nw + 'px';
        resizeState.curW = nw;
    }
    function onResizeEnd() {
        document.removeEventListener('mousemove', onResizeMove);
        document.removeEventListener('mouseup', onResizeEnd);
        if (resizeState && resizeState.moved) {
            var b = blocksData[resizeState.idx];
            b.pos = b.pos || {}; b.pos[currentBP] = b.pos[currentBP] || {};
            b.pos[currentBP].w = Math.round(resizeState.curW);
            syncPosPanel();
        }
        resizeState = null;
    }

    function selectBlock(idx) {
        try {
            selectedIndex = idx;
            var items = document.querySelectorAll('#freeSurface .block-item.free');
            items.forEach(function(it) { it.classList.remove('selected'); });
            if (items[idx]) items[idx].classList.add('selected');
            var panel = document.getElementById('posPanel');
            if (panel) panel.style.display = 'block';
            var lbl = document.getElementById('posDevLabel');
            if (lbl && devices[currentDevice]) lbl.textContent = devices[currentDevice].label;
            syncPosPanel();
        } catch(e) { console.error('selectBlock error:', e); }
    }
    function syncPosPanel() {
        if (selectedIndex < 0) return;
        var eff = effectivePos(blocksData[selectedIndex], currentBP) || {x:0,y:0,w:300,z:1};
        document.getElementById('posX').value = Math.round(eff.x);
        document.getElementById('posY').value = Math.round(eff.y);
        document.getElementById('posW').value = Math.round(eff.w);
        document.getElementById('posZ').value = Math.round(eff.z);
    }
    function posInput() {
        if (selectedIndex < 0) return;
        var b = blocksData[selectedIndex];
        b.pos = b.pos || {}; b.pos[currentBP] = b.pos[currentBP] || {};
        b.pos[currentBP].x = parseInt(document.getElementById('posX').value) || 0;
        b.pos[currentBP].y = parseInt(document.getElementById('posY').value) || 0;
        b.pos[currentBP].w = parseInt(document.getElementById('posW').value) || 300;
        b.pos[currentBP].z = parseInt(document.getElementById('posZ').value) || 1;
        var el = document.querySelectorAll('#freeSurface .block-item.free')[selectedIndex];
        if (el) { el.style.left = b.pos[currentBP].x + 'px'; el.style.top = b.pos[currentBP].y + 'px'; el.style.width = b.pos[currentBP].w + 'px'; el.style.zIndex = b.pos[currentBP].z; }
    }
    function layerBlock(dir) {
        if (selectedIndex < 0) return;
        var b = blocksData[selectedIndex];
        b.pos = b.pos || {}; b.pos[currentBP] = b.pos[currentBP] || {};
        b.pos[currentBP].z = (b.pos[currentBP].z || 1) + dir;
        syncPosPanel();
        var el = document.querySelectorAll('#freeSurface .block-item.free')[selectedIndex];
        if (el) el.style.zIndex = b.pos[currentBP].z;
    }
    function deleteSelectedBlock() {
        if (selectedIndex < 0) return;
        if (!confirm('این بلاک حذف شود؟')) return;
        var items = document.querySelectorAll('#freeSurface .block-item.free');
        if (items[selectedIndex]) items[selectedIndex].remove();
        blocksData.splice(selectedIndex, 1);
        selectedIndex = -1;
        document.getElementById('posPanel').style.display = 'none';
        updateFreeIndices();
    }
    function deselectBlock() {
        selectedIndex = -1;
        var panel = document.getElementById('posPanel');
        if (panel) panel.style.display = 'none';
        document.querySelectorAll('#freeSurface .block-item.free').forEach(function(it) { it.classList.remove('selected'); });
    }

    function switchDevice(dev) {
        currentDevice = dev;
        currentBP = devices[dev].bp;
        document.querySelectorAll('#deviceSeg button').forEach(function(b) { b.classList.toggle('active', b.dataset.dev === dev); });
        var frame = document.getElementById('previewFrame');
        if (frame) frame.style.width = devices[dev].w + 'px';
        if (positionMode) {
            renderFree();
            var lbl = document.getElementById('posDevLabel');
            if (lbl) lbl.textContent = devices[dev].label;
            if (selectedIndex >= 0) syncPosPanel();
        }
    }

    function setPositionMode(mode) {
        positionMode = !!mode;
        document.querySelectorAll('#modeSeg button').forEach(function(b) { b.classList.toggle('active', parseInt(b.dataset.mode) === mode); });
        if (positionMode) {
            var y = 20, sw = devices[currentDevice].w;
            blocksData.forEach(function(b, i) {
                b.pos = b.pos || {};
                if (!b.pos.desktop) {
                    b.pos.desktop = {x: 20, y: y, w: Math.min(600, sw - 60), z: i + 1};
                    y += 140;
                }
            });
            renderFree();
        } else {
            deselectBlock();
            renderStacked();
        }
    }

    function setMobileMode(mm) {
        mobileMode = mm;
        document.querySelectorAll('#mobileSeg button').forEach(function(b) { b.classList.toggle('active', b.dataset.mm === mm); });
        refreshPreview();
    }

    function convertToFree() { setPositionMode(1); }

    function saveBlocks(pageId) {
        var btn = document.getElementById('saveBlocksBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال ذخیره...';
        var fd = new FormData();
        fd.append('block_page_id', pageId);
        fd.append('blocks_data', JSON.stringify(blocksData));
        fd.append('cache', '1');
        fd.append('position_mode', positionMode ? '1' : '0');
        fd.append('mobile_mode', mobileMode);
        fetch('<?= BASE_URL ?>mod/builder/save', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> ذخیره شد';
                refreshPreview();
                setTimeout(function() { btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات'; btn.disabled = false; }, 2000);
            } else {
                btn.innerHTML = '<i class="fa-solid fa-times"></i> خطا';
                setTimeout(function() { btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات'; btn.disabled = false; }, 3000);
            }
        });
    }

    function refreshPreview() {
        var frame = document.getElementById('previewFrame');
        if (frame) frame.src = frame.src.split('?')[0] + '?t=' + Date.now();
    }

    function refreshCanvasContent() {
        if (!blocksData.length) return;
        fetch('<?= BASE_URL ?>mod/builder/render_blocks', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ blocks: blocksData })
        }).then(function(r) { return r.json(); }).then(function(res) {
            if (!res || !res.html) return;
            var els = document.querySelectorAll('.builder-canvas .block-content-preview');
            for (var i = 0; i < els.length && i < res.html.length; i++) {
                els[i].innerHTML = res.html[i];
            }
        }).catch(function() {});
    }

    function selectImage(btn) {
        var input = btn.previousElementSibling;
        var url = prompt('آدرس URL تصویر:');
        if (url) input.value = url;
    }

    function clearCache(pageId) {
        fetch('<?= BASE_URL ?>mod/builder/clear_cache/' + pageId)
        .then(function(r) { return r.json(); })
        .then(function(res) { alert(res.success ? 'کش پاک شد' : 'خطا'); });
    }

    function toggleSidebar() {
        var w = document.getElementById('builderWrap');
        w.classList.toggle('hide-chips');
        try { localStorage.setItem('builder_chips_hidden', w.classList.contains('hide-chips') ? '1' : ''); } catch(e){}
    }
    function togglePreview() {
        var w = document.getElementById('builderWrap');
        w.classList.toggle('hide-preview');
        try { localStorage.setItem('builder_preview_hidden', w.classList.contains('hide-preview') ? '1' : ''); } catch(e){}
        applyColumns();
    }
    function toggleCanvas() {
        var w = document.getElementById('builderWrap');
        w.classList.toggle('hide-canvas');
        try { localStorage.setItem('builder_canvas_hidden', w.classList.contains('hide-canvas') ? '1' : ''); } catch(e){}
        applyColumns();
    }
    var canvasW = null; // عرض بوم (px) یا null برای 1fr
    function applyColumns() {
        var w = document.getElementById('builderWrap');
        if (!w) return;
        var hideC = w.classList.contains('hide-canvas');
        var hideP = w.classList.contains('hide-preview');
        var sp = document.getElementById('builderSplitter');
        if (hideC || hideP) {
            if (sp) sp.style.display = 'none';
            if (hideC && hideP) w.style.gridTemplateColumns = '0px 6px 0px';
            else if (hideC) w.style.gridTemplateColumns = '0px 6px 1fr';
            else w.style.gridTemplateColumns = (canvasW || '1fr') + ' 6px 0px';
        } else {
            if (sp) sp.style.display = 'block';
            w.style.gridTemplateColumns = (canvasW || '1fr') + ' 6px 1fr';
        }
    }
    (function initSplitter() {
        var sp = document.getElementById('builderSplitter');
        var w = document.getElementById('builderWrap');
        var frame = document.getElementById('previewFrame');
        if (!sp || !w) return;
        var dragging = false;
        sp.addEventListener('mousedown', function(e) {
            dragging = true; sp.classList.add('dragging');
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
            if (frame) frame.style.pointerEvents = 'none';
            e.preventDefault();
        });
        document.addEventListener('mousemove', function(e) {
            if (!dragging) return;
            var rect = w.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var isRTL = getComputedStyle(w).direction === 'rtl';
            var min = 260, max = rect.width - 320;
            var cw = isRTL ? (rect.width - x - 6) : x;
            if (cw < min) cw = min;
            if (cw > max) cw = max;
            canvasW = cw;
            w.style.gridTemplateColumns = cw + 'px 6px 1fr';
        });
        document.addEventListener('mouseup', function() {
            if (dragging) {
                dragging = false; sp.classList.remove('dragging');
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
                if (frame) frame.style.pointerEvents = '';
            }
        });
    })();
    function fullscreenPreview() {
        var p = document.getElementById('builderPreview');
        if (p.classList.contains('fullscreen')) {
            p.classList.remove('fullscreen');
        } else {
            p.classList.add('fullscreen');
        }
    }

    if (positionMode) { renderFree(); }
    else { renderStacked(); }
    try {
        if (localStorage.getItem('builder_chips_hidden') === '1') document.getElementById('builderWrap').classList.add('hide-chips');
        if (localStorage.getItem('builder_preview_hidden') === '1') document.getElementById('builderWrap').classList.add('hide-preview');
        if (localStorage.getItem('builder_canvas_hidden') === '1') document.getElementById('builderWrap').classList.add('hide-canvas');
    } catch(e){}
    applyColumns();
    document.addEventListener('keydown', function(e) {
        if ((e.key === 'Delete' || e.key === 'Backspace') && positionMode && selectedIndex >= 0 && !e.target.closest('input,textarea,select,[contenteditable]')) {
            e.preventDefault();
            deleteSelectedBlock();
        }
    });
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function builder_save_blocks() {
    header('Content-Type: application/json');
    $block_page_id = (int)($_POST['block_page_id'] ?? 0);
    $blocks_data = $_POST['blocks_data'] ?? '[]';
    $cache = !empty($_POST['cache']);
    $position_mode = !empty($_POST['position_mode']) ? 1 : 0;
    $mobile_mode = in_array($_POST['mobile_mode'] ?? '', ['auto', 'exact']) ? $_POST['mobile_mode'] : 'auto';
    if (!$block_page_id) {
        echo json_encode(['success' => false, 'message' => 'Page ID is required']);
        exit;
    }
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT id, page_id, position_mode, mobile_mode FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $stmt = $conn->prepare("UPDATE block_pages SET blocks_data = ?, position_mode = ?, mobile_mode = ?, cached_html = NULL, cache_updated = NULL WHERE id = ?");
        $stmt->bind_param("isis", $blocks_data, $position_mode, $mobile_mode, $block_page_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO block_pages (id, page_id, page_type, blocks_data, position_mode, mobile_mode) VALUES (?, 0, 'safhe', ?, ?, ?)");
        $stmt->bind_param("isiss", $block_page_id, $blocks_data, $position_mode, $mobile_mode);
    }
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $blocks = json_decode($blocks_data, true) ?: [];
    if ($position_mode) {
        $html = '<div class="builder-free-canvas">' . builder_build_positions_css($blocks, $mobile_mode) . builder_render_blocks($blocks, true) . '</div>';
    } else {
        $html = builder_render_blocks($blocks, false);
    }

    // همگام‌سازی محتوای رندرشده با جدول مربوطه (پست/محصول/خدمت)
    if ($row && (int)$row['page_id'] > 0) {
        builder_sync_content($row['page_type'], (int)$row['page_id'], $html);
    }

    if ($cache && $blocks) {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("UPDATE block_pages SET cached_html = ? WHERE id = ?");
        $stmt->bind_param("si", $html, $block_page_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    echo json_encode(['success' => true]);
    exit;
}

function builder_sync_content($page_type, $page_id, $html) {
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
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("UPDATE $table SET $col = ? WHERE id = ?");
    $stmt->bind_param("si", $html, $page_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function builder_clear_cache($block_page_id) {
    header('Content-Type: application/json');
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
    $conn->close();
    if (!$bp)     return '';
    if ($use_cache && $bp['cached_html']) return $bp['cached_html'];
    $blocks = json_decode($bp['blocks_data'], true) ?: [];
    if (empty($blocks)) {
        if ($bp['page_id']) {
            $bank2 = new Bank();
            $c2 = $bank2->getConnection();
            $r = $c2->query("SELECT content FROM posts WHERE id = " . (int)$bp['page_id']);
            $row = $r ? $r->fetch_assoc() : null;
            $c2->close();
            if ($row) return '<div class="mohtava-container" style="padding:40px 0;">' . $row['content'] . '</div>';
        }
        return '';
    }
    if (!empty($bp['position_mode'])) {
        $css = builder_build_positions_css($blocks, $bp['mobile_mode'] ?? 'auto');
        return '<div class="builder-free-canvas">' . $css . builder_render_blocks($blocks, true) . '</div>';
    }
    return builder_render_blocks($blocks, false);
}

function builder_build_positions_css($blocks, $mobile_mode = 'auto') {
    $bps = builder_breakpoints();
    $css = '<style class="builder-pos-css">';
    foreach ($blocks as $i => $block) {
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
            .builder-live-block { position:relative; cursor:pointer; transition:outline .15s; }
            .builder-live-block:hover { outline:2px dashed var(--rang-asli,#FF6F00); outline-offset:2px; }
            .builder-live-block.builder-selected { outline:3px solid var(--rang-asli,#FF6F00); outline-offset:2px; }
            .builder-inline-toolbar { position:fixed; z-index:99999; display:none; gap:2px; background:#fff; border:1px solid #e9ecef; border-radius:8px; padding:4px; box-shadow:0 6px 20px rgba(0,0,0,0.18); }
            .builder-inline-toolbar button { width:32px; height:32px; border:none; background:#f8f9fa; border-radius:6px; cursor:pointer; color:#444; font-size:13px; }
            .builder-inline-toolbar button:hover { background:var(--rang-asli,#FF6F00); color:#fff; }
            .builder-inline-toolbar button.danger:hover { background:#c62828; }
            .builder-drag-handle { position:absolute; top:4px; left:4px; z-index:50; width:26px; height:26px; border-radius:6px; background:rgba(255,111,0,0.92); color:#fff; display:none; align-items:center; justify-content:center; cursor:grab; font-size:12px; }
            .builder-live-block:hover > .builder-drag-handle { display:flex; }
            .builder-live-block { padding-top:6px; }
            .builder-drop-target { outline:2px dashed #00B894 !important; outline-offset:2px; }
            .builder-live-block img { cursor:pointer; transition:outline .15s; }
            .builder-live-block img:hover { outline:2px dashed #0984E3; outline-offset:2px; }
            .builder-live-block a.dakmeh:hover, .builder-live-block a.btn:hover { outline:2px dashed #00B894; outline-offset:2px; }
        ';
        $edit_body = '<script src="' . $site_url . 'mohtava/sakhtar/inline-editor.js"></script>';
    }
    $font_css = "<style>"
        . "@font-face{font-family:'Vazirmatn';src:url({$site_url}ghaleb/manabe/fonts/Vazirmatn-RD-Regular.woff2) format('woff2');font-weight:400;font-display:swap;}"
        . "@font-face{font-family:'Vazirmatn';src:url({$site_url}ghaleb/manabe/fonts/Vazirmatn-RD-Bold.woff2) format('woff2');font-weight:700;font-display:swap;}"
        . "</style>";
    echo '<!DOCTYPE html><html lang="fa" dir="rtl"><head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

function builder_render_blocks($blocks, $free = false) {
    $html = '';
    foreach ($blocks as $i => $block) {
        $html .= builder_render_block($block, $i, $free);
    }
    return $html;
}

function builder_render_block($block, $index = 0, $free = false) {
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $func = 'block_' . $type . '_render';
    if (function_exists($func)) {
        $inner = call_user_func($func, $data);
    } else {
        $inner = '<div style="padding:20px;background:#fff3e0;border:1px solid #FF6F00;border-radius:8px;margin:16px 0;color:#e65100;text-align:center;">بلاک «' . htmlspecialchars($type) . '» فعال نیست</div>';
    }
    if ($free) {
        return '<div class="bpos-' . $index . '" data-block-index="' . $index . '">' . $inner . '</div>';
    }
    return '<div data-block-index="' . $index . '">' . $inner . '</div>';
}

function builder_render_block_inner($block) {
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $func = 'block_' . $type . '_render';
    if (function_exists($func)) {
        return call_user_func($func, $data);
    }
    return '<div style="padding:20px;background:#fff3e0;border:1px solid #FF6F00;border-radius:8px;margin:16px 0;color:#e65100;text-align:center;">بلاک «' . htmlspecialchars($type) . '» فعال نیست</div>';
}

function builder_render_blocks_api() {
    header('Content-Type: application/json; charset=utf-8');
    $raw = $_POST['blocks'] ?? '';
    if ($raw === '') {
        $input = json_decode(file_get_contents('php://input'), true);
        $raw = $input['blocks'] ?? '[]';
    }
    $blocks = json_decode($raw, true) ?: [];
    $out = [];
    foreach ($blocks as $b) {
        $out[] = builder_render_block_inner($b);
    }
    echo json_encode(['html' => $out], JSON_UNESCAPED_UNICODE);
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
