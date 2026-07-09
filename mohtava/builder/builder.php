<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/blocks/block-types.php';

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
        case 'edit':
            builder_page_edit($sub);
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
    $map = [
        'single'  => ['single' => 'صفحه تکی (بر اساس نوع)', 'post' => 'مطلب تکی', 'product' => 'محصول تکی', 'khadamat' => 'خدمت تکی', 'safhe' => 'برگه تکی', 'home' => 'صفحه اصلی'],
        'archive' => ['archive' => 'آرشیو', 'blog' => 'آرشیو وبلاگ', 'product' => 'آرشیو محصولات', 'khadamat' => 'آرشیو خدمات'],
        'global'  => ['*' => 'سراسری (همه صفحات)'],
    ];
    $label = $map[$type][$val] ?? $val;
    $prefix = $type === 'single' ? 'تکی' : ($type === 'archive' ? 'آرشیو' : 'سراسری');
    return $label . ' — ' . $prefix;
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
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>صفحه‌ساز / تم‌بلدر (Theme Builder)</h3>
    <p style="color:#888;margin-bottom:16px;">با بلاک‌ها صفحات و قالب‌های محتوایی بسازید. هر تم با یک <strong>شرط نمایش</strong> مشخص می‌کند کجا اعمال شود (مثل المنتور پرو).</p>
    <p style="margin-bottom:16px;"><a href="<?= BASE_URL ?>mod/builder/new" class="dakmeh dakmeh-asli" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:var(--rang-asli,#FF6F00);color:#fff;font-weight:700;text-decoration:none;"><i class="fa-solid fa-plus"></i> ساخت تم جدید</a></p>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <tr style="background:#f8f9fa;"><th>نام تم</th><th>شرط نمایش</th><th>کش</th><th>آخرین ویرایش</th><th>عملیات</th></tr>
        <?php if (empty($pages)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:#888;">هنوز تمی ساخته نشده. روی «ساخت تم جدید» کلیک کنید.</td></tr>
        <?php else: foreach ($pages as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['name'] ?: ('تم #' . $p['id'])) ?></td>
            <td><?= builder_condition_label($p) ?></td>
            <td><?= $p['has_cache'] ? '<span style="color:#2e7d32;">فعال</span>' : '<span style="color:#888;">غیرفعال</span>' ?></td>
            <td><?= $p['updated_at'] ?></td>
            <td><a href="<?= BASE_URL ?>mod/builder/edit/<?= $p['id'] ?>">ویرایش با صفحه‌ساز</a></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function builder_template_new() {
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>ساخت تم جدید (تم‌بلدر)</h3>
    <p style="color:#888;margin-bottom:16px;">شرط نمایش تعیین می‌کند این تم کجا اعمال شود.</p>
    <form method="post" action="<?= BASE_URL ?>mod/builder/create" style="max-width:600px;">
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">نام تم</label>
            <input type="text" name="name" required style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="مثلاً: قالب آرشیو وبلاگ">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">نوع شرط</label>
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
    $ctype = $_POST['condition_type'] ?? 'archive';
    $cval = $ctype === 'global' ? '*' : ($_POST['condition_value'] ?? '');
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("INSERT INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data) VALUES (0, 'template', ?, ?, ?, '[]')");
    $stmt->bind_param("ssss", $name, $ctype, $cval);
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
    $ctype = $_POST['condition_type'] ?? 'archive';
    $cval = $ctype === 'global' ? '*' : ($_POST['condition_value'] ?? '');
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("UPDATE block_pages SET name=?, condition_type=?, condition_value=?, cached_html=NULL, cache_updated=NULL WHERE id=?");
    $stmt->bind_param("sssi", $name, $ctype, $cval, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    redirect('mod/builder/edit/' . $id);
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
        $stmt = $conn->prepare("SELECT id, title FROM posts WHERE id = ? AND (type = 'safhe' OR type = 'page')");
        $stmt->bind_param("i", $block_page_id);
        $stmt->execute();
        $post = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        if ($post) {
            $bp = [
                'id' => 0,
                'page_id' => $post['id'],
                'page_type' => 'safhe',
                'blocks_data' => '[]',
                'cached_html' => null,
            ];
        } else {
            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            echo "<h3>صفحه یافت نشد</h3>";
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            return;
        }
    }

    $blocks = json_decode($bp['blocks_data'], true) ?: [];
    $available_blocks = get_block_types();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <style>
        .builder-wrap { display:grid; grid-template-columns:260px 1fr; gap:20px; min-height:600px; }
        .builder-sidebar { background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:16px; position:sticky; top:90px; align-self:start; max-height:calc(100vh-120px); overflow-y:auto; }
        .builder-canvas { background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:24px; min-height:500px; }
        .block-type-list { display:flex; flex-direction:column; gap:6px; }
        .block-type-item { padding:10px 14px; background:#f8f9fa; border:1px solid #eef0f4; border-radius:8px; cursor:pointer; display:flex; align-items:center; gap:10px; transition:all 0.2s; font-size:13px; }
        .block-type-item:hover { background:var(--rang-roshan,#fff3e0); border-color:var(--rang-asli,#FF6F00); }
        .block-type-item .icon { width:32px;height:32px;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;flex-shrink:0; }
        .block-item { background:#f8f9fa; border:2px solid #eef0f4; border-radius:10px; margin-bottom:16px; padding:16px; cursor:move; position:relative; transition:all 0.2s; }
        .block-item:hover { border-color:var(--rang-asli,#FF6F00); }
        .block-item.dragging { opacity:0.5; }
        .block-item .block-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
        .block-item .block-title { font-weight:700; font-size:14px; }
        .block-item .block-actions { display:flex; gap:6px; }
        .block-item .block-actions button { background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:4px; font-size:13px; color:#888; }
        .block-item .block-actions button:hover { background:#e9ecef; color:#333; }
        .block-item .block-actions button.danger:hover { color:#c62828; }
        .block-item.drag-over { border-color:var(--rang-asli,#FF6F00); border-style:dashed; }
        .empty-canvas { text-align:center; padding:60px 20px; color:#aaa; }
        .empty-canvas i { font-size:48px; display:block; margin-bottom:16px; }
        .btn-save-blocks { position:fixed; bottom:24px; left:24px; z-index:100; padding:14px 32px; background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:12px; font-weight:700; font-size:15px; cursor:pointer; box-shadow:0 4px 20px rgba(255,111,0,0.4); transition:all 0.3s; }
        .btn-save-blocks:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(255,111,0,0.5); }
        .btn-save-blocks:disabled { opacity:0.6; cursor:wait; }
        <?php foreach ($available_blocks as $bk => $bv): ?>
        .block-item.type-<?= $bk ?> { border-right:4px solid <?= $bv['color'] ?>; }
        <?php endforeach; ?>
    </style>

    <h3>صفحه‌ساز: <?= htmlspecialchars($bp['page_type']) ?> #<?= $bp['page_id'] ?></h3>
    <p><a href="<?= BASE_URL ?>mod/builder/pages" style="color:var(--rang-asli,#FF6F00);">&larr; بازگشت</a></p>

    <!-- پنل شرط نمایش تم -->
    <form method="post" action="<?= BASE_URL ?>mod/builder/save_condition" style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-bottom:20px;max-width:600px;">
        <h4 style="margin-bottom:12px;"><i class="fa-solid fa-filter"></i> شرط نمایش این تم</h4>
        <input type="hidden" name="bp_id" value="<?= $bp['id'] ?>">
        <div class="form-group" style="margin-bottom:12px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">نام تم</label>
            <input type="text" name="name" value="<?= htmlspecialchars($bp['name'] ?? '') ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
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

    <div class="builder-wrap" id="builderWrap">
        <div class="builder-sidebar">
            <h4 style="font-size:14px;margin-bottom:12px;">بلاک‌ها</h4>
            <div class="block-type-list">
                <?php foreach ($available_blocks as $bk => $bv): ?>
                <div class="block-type-item" onclick="addBlock('<?= $bk ?>')">
                    <div class="icon" style="background:<?= $bv['color'] ?>;"><i class="fa-solid <?= $bv['icon'] ?>"></i></div>
                    <div><strong><?= $bv['label'] ?></strong><br><span style="font-size:11px;color:#888;"><?= $bv['desc'] ?></span></div>
                </div>
                <?php endforeach; ?>
            </div>
            <hr style="margin:16px 0;border:none;border-top:1px solid #eef0f4;">
            <button onclick="clearCache(<?= $bp['id'] ?>)" style="width:100%;padding:10px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;cursor:pointer;font-size:13px;color:#888;"><i class="fa-solid fa-rotate"></i> پاک کردن کش</button>
        </div>
        <div class="builder-canvas" id="builderCanvas">
            <div id="blocksContainer">
                <?php if (empty($blocks)): ?>
                <div class="empty-canvas" id="emptyCanvas">
                    <i class="fa-solid fa-layer-group" style="color:#ddd;"></i>
                    <p style="color:#aaa;">هنوز بلاکی اضافه نکردید.<br>از سمت راست بلاک مورد نظر را انتخاب کنید.</p>
                </div>
                <?php else: ?>
                <?php foreach ($blocks as $i => $block): ?>
                <div class="block-item type-<?= $block['type'] ?>" data-index="<?= $i ?>" draggable="true">
                    <?php render_block_admin($block); ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <button class="btn-save-blocks" id="saveBlocksBtn" onclick="saveBlocks(<?= $bp['id'] ?>)"><i class="fa-solid fa-save"></i> ذخیره تغییرات</button>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
    var blocksData = <?= json_encode($blocks, JSON_UNESCAPED_UNICODE) ?>;
    var blockTypes = <?= json_encode($available_blocks, JSON_UNESCAPED_UNICODE) ?>;

    // SortableJS for drag & drop
    var sortable = new Sortable(document.getElementById('blocksContainer'), {
        animation: 150,
        handle: '.block-item',
        ghostClass: 'drag-over',
        onEnd: function() {
            updateBlocksFromDOM();
        }
    });

    function renderBlockAdmin(block) {
        var bt = blockTypes[block.type] || {label: block.type, icon: 'fa-cube', color: '#888'};
        var data = block.data || {};
        var html = '<div class="block-header">';
        html += '<div class="block-title"><span style="display:inline-block;width:28px;height:28px;border-radius:6px;background:' + bt.color + ';color:#fff;text-align:center;line-height:28px;margin-left:8px;font-size:13px;"><i class="fa-solid ' + bt.icon + '"></i></span>' + bt.label + '</div>';
        html += '<div class="block-actions">';
        html += '<button onclick="editBlock(this)" title="ویرایش"><i class="fa-solid fa-pen"></i></button>';
        html += '<button class="danger" onclick="removeBlock(this)" title="حذف"><i class="fa-solid fa-trash"></i></button>';
        html += '</div></div>';
        html += '<div class="block-content-preview" style="font-size:13px;color:#666;max-height:60px;overflow:hidden;">' + getBlockPreview(block) + '</div>';
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

    function addBlock(type) {
        var defaultData = {};
        var bt = blockTypes[type];
        if (bt && bt.defaults) defaultData = bt.defaults;
        var block = {type: type, data: defaultData};
        blocksData.push(block);
        renderAllBlocks();
    }

    function removeBlock(btn) {
        var item = btn.closest('.block-item');
        var idx = parseInt(item.dataset.index);
        blocksData.splice(idx, 1);
        renderAllBlocks();
    }

    function editBlock(btn) {
        var item = btn.closest('.block-item');
        var idx = parseInt(item.dataset.index);
        var block = blocksData[idx];
        var data = block.data || {};
        var bt = blockTypes[block.type] || {label: block.type};

        var fields = bt.fields || [];
        var html = '<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;" onclick="if(event.target===this)this.remove()">';
        html += '<div style="background:#fff;border-radius:16px;padding:32px;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;">';
        html += '<h3 style="margin-bottom:16px;">ویرایش «' + bt.label + '»</h3>';

        if (fields.length === 0) {
            html += '<p style="color:#888;">این بلاک تنظیمات خاصی ندارد.</p>';
        }

        fields.forEach(function(f) {
            var val = data[f.key] !== undefined ? data[f.key] : (f.default || '');
            html += '<div style="margin-bottom:14px;">';
            html += '<label style="display:block;margin-bottom:4px;font-weight:600;">' + f.label + '</label>';
            if (f.type === 'textarea' || f.type === 'html') {
                html += '<textarea class="edit-field" data-key="' + f.key + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;min-height:' + (f.type === 'html' ? '150' : '80') + 'px;' + (f.type === 'html' ? '' : '') + '">' + val + '</textarea>';
            } else if (f.type === 'select') {
                html += '<select class="edit-field" data-key="' + f.key + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">';
                (f.options || []).forEach(function(o) {
                    html += '<option value="' + o.value + '" ' + (val == o.value ? 'selected' : '') + '>' + o.label + '</option>';
                });
                html += '</select>';
            } else if (f.type === 'color') {
                html += '<input type="color" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="width:60px;height:40px;border:none;border-radius:6px;cursor:pointer;">';
            } else if (f.type === 'number') {
                html += '<input type="number" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">';
            } else if (f.type === 'image') {
                html += '<div style="display:flex;gap:8px;"><input type="text" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="flex:1;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="URL تصویر"><button onclick="selectImage(this)" style="padding:10px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;cursor:pointer;">انتخاب</button></div>';
            } else {
                html += '<input type="text" class="edit-field" data-key="' + f.key + '" value="' + val + '" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">';
            }
            html += '</div>';
        });

        html += '<div style="display:flex;gap:10px;margin-top:20px;">';
        html += '<button onclick="saveEdit(this)" style="padding:12px 24px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-check"></i> اعمال</button>';
        html += '<button onclick="this.closest(\'[style*=\\'fixed\\']\').remove()" style="padding:12px 24px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;cursor:pointer;">انصراف</button>';
        html += '</div>';
        html += '</div></div>';

        var modal = document.createElement('div');
        modal.innerHTML = html;
        document.body.appendChild(modal);
    }

    function saveEdit(btn) {
        var modal = btn.closest('[style*="fixed"]');
        var idx = -1;
        var items = document.querySelectorAll('.block-item');
        for (var i = 0; i < items.length; i++) {
            if (items[i].querySelector('.block-content-preview')) {
                // find the modal's associated block
            }
        }
        // simpler approach: find block by current modal
        var fields = modal.querySelectorAll('.edit-field');
        var data = {};
        fields.forEach(function(f) { data[f.dataset.key] = f.value; });

        // update the blocksData - we need to know which block we're editing
        // we saved the index in the render
        var blockItem = null;
        var allItems = document.querySelectorAll('.block-item');
        for (var i = 0; i < allItems.length; i++) {
            if (allItems[i].querySelector('.block-actions')) {
                var btns = allItems[i].querySelectorAll('.block-actions button');
                // Check if there's a stored index
            }
        }

        // Let me use a different approach - store the editing index in a global
        blocksData[editingBlockIndex].data = data;
        renderAllBlocks();
        modal.remove();
    }

    var editingBlockIndex = -1;

    // override editBlock to track index
    var origEditBlock = editBlock;
    editBlock = function(btn) {
        var item = btn.closest('.block-item');
        editingBlockIndex = parseInt(item.dataset.index);
        origEditBlock(btn);
    };

    function renderAllBlocks() {
        var container = document.getElementById('blocksContainer');
        var empty = document.getElementById('emptyCanvas');
        container.innerHTML = '';
        blocksData.forEach(function(block, i) {
            var div = document.createElement('div');
            div.className = 'block-item type-' + block.type;
            div.dataset.index = i;
            div.draggable = true;
            div.innerHTML = renderBlockAdmin(block);
            container.appendChild(div);
        });
        if (blocksData.length === 0) {
            container.innerHTML = '<div class="empty-canvas" id="emptyCanvas"><i class="fa-solid fa-layer-group" style="color:#ddd;"></i><p style="color:#aaa;">هنوز بلاکی اضافه نکردید.<br>از سمت راست بلاک مورد نظر را انتخاب کنید.</p></div>';
        }
        updateBlocksFromDOM();
    }

    function updateBlocksFromDOM() {
        var items = document.querySelectorAll('.block-item');
        var newBlocks = [];
        items.forEach(function(item) {
            var idx = parseInt(item.dataset.index);
            if (blocksData[idx]) {
                newBlocks.push(blocksData[idx]);
            }
        });
        if (newBlocks.length > 0) blocksData = newBlocks;
        // update indices
        items.forEach(function(item, i) { item.dataset.index = i; });
    }

    function saveBlocks(pageId) {
        var btn = document.getElementById('saveBlocksBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال ذخیره...';
        updateBlocksFromDOM();

        var formData = new FormData();
        formData.append('block_page_id', pageId);
        formData.append('blocks_data', JSON.stringify(blocksData));
        formData.append('cache', '1');

        fetch('<?= BASE_URL ?>mod/builder/save', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> ذخیره شد';
                setTimeout(function() {
                    btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات';
                    btn.disabled = false;
                }, 2000);
            } else {
                btn.innerHTML = '<i class="fa-solid fa-times"></i> خطا';
                setTimeout(function() {
                    btn.innerHTML = '<i class="fa-solid fa-save"></i> ذخیره تغییرات';
                    btn.disabled = false;
                }, 3000);
            }
        });
    }

    function selectImage(btn) {
        var input = btn.previousElementSibling;
        var url = prompt('آدرس URL تصویر:');
        if (url) input.value = url;
    }

    function clearCache(pageId) {
        fetch('<?= BASE_URL ?>mod/builder/clear_cache/' + pageId)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            alert(res.success ? 'کش پاک شد' : 'خطا');
        });
    }
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function builder_save_blocks() {
    header('Content-Type: application/json');
    $block_page_id = (int)($_POST['block_page_id'] ?? 0);
    $blocks_data = $_POST['blocks_data'] ?? '[]';
    $cache = !empty($_POST['cache']);
    if (!$block_page_id) {
        echo json_encode(['success' => false, 'message' => 'Page ID is required']);
        exit;
    }
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT id FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists) {
        $stmt = $conn->prepare("UPDATE block_pages SET blocks_data = ?, cached_html = NULL, cache_updated = NULL WHERE id = ?");
        $stmt->bind_param("si", $blocks_data, $block_page_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO block_pages (id, page_id, page_type, blocks_data) VALUES (?, 0, 'safhe', ?)");
        $stmt->bind_param("is", $block_page_id, $blocks_data);
    }
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $blocks = json_decode($blocks_data, true);
    if ($cache && $blocks) {
        $html = builder_render_blocks($blocks);
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

function builder_render_page($block_page_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM block_pages WHERE id = ?");
    $stmt->bind_param("i", $block_page_id);
    $stmt->execute();
    $bp = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    if (!$bp) return '';
    if ($bp['cached_html']) return $bp['cached_html'];
    $blocks = json_decode($bp['blocks_data'], true) ?: [];
    return builder_render_blocks($blocks);
}

/**
 * پیدا کردن تم منطبق با شرط نمایش
 * @param string $condition_type single | archive | global
 * @param string $condition_value مقدار شرط
 * @return array|null
 */
function builder_find_template($condition_type, $condition_value) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM block_pages WHERE condition_type = ? AND condition_value = ? LIMIT 1");
    $stmt->bind_param("ss", $condition_type, $condition_value);
    $stmt->execute();
    $bp = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $bp ?: null;
}

/**
 * رندر تم منطبق بر اساس بافتار صفحه
 * @param string $kind archive | single | home
 * @param string|null $subtype نوع (blog/product/khadamat/post/safhe)
 * @param string|null $slug اسلاگ صفحه خاص
 * @return string HTML کش‌شده یا ''
 */
function builder_render_for($kind, $subtype = null, $slug = null) {
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

function builder_render_blocks($blocks) {
    $html = '';
    foreach ($blocks as $block) {
        $html .= builder_render_block($block);
    }
    return $html;
}

function builder_render_block($block) {
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $func = 'block_' . $type . '_render';
    if (function_exists($func)) {
        return call_user_func($func, $data);
    }
    return '<div style="padding:20px;background:#fff3e0;border:1px solid #FF6F00;border-radius:8px;margin:16px 0;color:#e65100;text-align:center;">بلاک «' . htmlspecialchars($type) . '» فعال نیست</div>';
}

function builder_get_page_id($page_type, $page_slug) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($page_slug) {
        $stmt = $conn->prepare("SELECT p.id, bp.id AS bp_id FROM posts p LEFT JOIN block_pages bp ON bp.page_id = p.id AND bp.page_type = ? WHERE p.slug = ? LIMIT 1");
        $stmt->bind_param("ss", $page_type, $page_slug);
    } else {
        $stmt = $conn->prepare("SELECT p.id, bp.id AS bp_id FROM posts p LEFT JOIN block_pages bp ON bp.page_id = p.id AND bp.page_type = ? WHERE p.type = ? LIMIT 1");
        $stmt->bind_param("ss", $page_type, $page_type);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $row;
}
