<?php

require_once MASIR_RISH . 'haste/site_settings.php';
require_once MASIR_RISH . 'haste/settings.php';

function menu_get_site_items() {
    global $site_settings;
    return $site_settings['menu'] ?? [];
}

function menu_get_admin_items() {
    $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: [];
    return $admin_settings['menu'] ?? [];
}

function menu_save_site_items($items) {
    save_site_settings(['menu' => $items]);
}

function menu_save_admin_items($items) {
    $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: [];
    $admin_settings['menu'] = $items;
    save_admin_settings($admin_settings);
}

function menu_editor_site() {
    $items = menu_get_site_items();
    if (empty($items)) {
        $items = menu_get_default_site_items_with_cart();
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        $new_items = [];
        $labels = $_POST['label'] ?? [];
        $urls   = $_POST['url'] ?? [];
        foreach ($labels as $i => $label) {
            $label = trim($label);
            $url   = trim($urls[$i] ?? '');
            if ($label !== '' || $url !== '') {
                $new_items[] = ['label' => $label, 'url' => $url];
            }
        }
        menu_save_site_items($new_items);
        $items = $new_items;
        $message = "منوی سایت ذخیره شد.";
    }

    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <style>
        .menu-editor { max-width:800px; }
        .menu-editor table { width:100%; border-collapse:collapse; }
        .menu-editor th, .menu-editor td { padding:10px 12px; border-bottom:1px solid var(--rang-border,#dde1e6); text-align:right; }
        .menu-editor th { background:#f8f9fa; font-weight:700; }
        .menu-editor input[type=text] { width:100%; padding:8px 10px; border:1.5px solid #cdd3da; border-radius:6px; font-family:inherit; box-sizing:border-box; }
        .menu-editor .btn-del { background:#dc3545; color:#fff; border:none; border-radius:6px; padding:6px 14px; cursor:pointer; font-size:13px; }
        .menu-editor .btn-add { background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:8px; padding:10px 20px; cursor:pointer; font-weight:700; margin-top:12px; }
        .menu-editor .btn-save { background:#28a745; color:#fff; border:none; border-radius:8px; padding:12px 30px; cursor:pointer; font-weight:700; font-size:1rem; }
        .menu-editor .sort-handle { cursor:grab; color:#999; font-size:18px; user-select:none; }
        .menu-editor .empty-msg { color:var(--rang-gray,#666); padding:30px; text-align:center; }
    </style>
    <div class="menu-editor">
        <h3>ویرایش منوی سایت</h3>
        <p style="color:var(--rang-gray,#666); margin-bottom:20px;">این منو در هدر سایت نمایش داده می‌شود.</p>
        <?php if (isset($message)) echo "<p style='color:green; font-weight:700;'>$message</p>"; ?>
        <form method="post" id="menuForm">
            <table>
                <thead>
                    <tr><th style="width:40px;"></th><th>عنوان</th><th>پیوند (URL)</th><th style="width:60px;"></th></tr>
                </thead>
                <tbody id="menuItems">
                    <?php if (empty($items)): ?>
                    <tr class="empty-row"><td colspan="4" class="empty-msg">هنوز هیچ آیتمی اضافه نشده است.</td></tr>
                    <?php else: ?>
                    <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td><span class="sort-handle">☰</span></td>
                        <td><input type="text" name="label[]" value="<?= htmlspecialchars($item['label'] ?? '') ?>"></td>
                        <td><input type="text" name="url[]" value="<?= htmlspecialchars($item['url'] ?? '') ?>" placeholder="<?= BASE_URL ?>khadamat"></td>
                        <td><button type="button" class="btn-del" onclick="this.closest('tr').remove()">✕</button></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn-add" onclick="addMenuItem()">+ افزودن آیتم</button>
            <br><br>
            <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> ذخیره منو</button>
        </form>
    </div>
    <script>
    function addMenuItem() {
        var tbody = document.getElementById('menuItems');
        var empty = tbody.querySelector('.empty-row');
        if (empty) empty.remove();
        var tr = document.createElement('tr');
        tr.innerHTML = '<td><span class="sort-handle">☰</span></td>' +
            '<td><input type="text" name="label[]" value=""></td>' +
            '<td><input type="text" name="url[]" value="" placeholder="<?= BASE_URL ?>"></td>' +
            '<td><button type="button" class="btn-del" onclick="this.closest(\'tr\').remove()">✕</button></td>';
        tbody.appendChild(tr);
    }
    // Drag sorting
    (function() {
        var tbody = document.getElementById('menuItems');
        new Sortable(tbody, { handle: '.sort-handle', animation: 150 });
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function menu_admin_render_tree($nodes) {
    $html = '';
    foreach ($nodes as $node) {
        $item   = $node['item'];
        $id     = $node['_idx'];
        $parent = $item['parent'] ?? -1;
        if (!is_string($parent) && !is_int($parent)) $parent = -1;
        $label  = htmlspecialchars($item['label'] ?? '', ENT_QUOTES);
        $url    = htmlspecialchars($item['url'] ?? '', ENT_QUOTES);
        $icon   = htmlspecialchars($item['icon'] ?? '', ENT_QUOTES);
        $target = $item['target'] ?? '';
        $tSel   = $target === '_blank' ? 'selected' : '';
        $html .= '<div class="menu-card" data-idx="' . $id . '" data-parent="' . htmlspecialchars($parent) . '">';
        $html .= '<div class="menu-card-row">';
        $html .= '<span class="sort-handle">☰</span>';
        $html .= '<input type="text" class="f-label" name="label[]" value="' . $label . '" placeholder="عنوان">';
        $html .= '<input type="text" class="f-url" name="url[]" value="' . $url . '" placeholder="mod/dashmod">';
        $html .= '<input type="text" class="f-icon" name="icon[]" value="' . $icon . '" placeholder="fa-...">';
        $html .= '<select class="f-target" name="target[]"><option value="">خود</option><option value="_blank" ' . $tSel . '>صفحه جدید</option></select>';
        $html .= '<select class="f-parent" name="parent[]"></select>';
        $html .= '<input type="hidden" name="oid[]" value="' . htmlspecialchars($id) . '">';
        $html .= '<button type="button" class="btn-del" onclick="this.closest(\'.menu-card\').remove(); refreshParentOptions();">✕</button>';
        $html .= '</div>';
        $html .= '<div class="menu-children">';
        if (!empty($node['children'])) {
            $html .= menu_admin_render_tree($node['children']);
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    return $html;
}

function menu_editor_admin() {
    $items = menu_get_admin_items();
    if (empty($items)) {
        $items = menu_get_default_admin_items();
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        $labels  = $_POST['label']  ?? [];
        $urls    = $_POST['url']    ?? [];
        $icons   = $_POST['icon']   ?? [];
        $parents = $_POST['parent'] ?? [];
        $targets = $_POST['target'] ?? [];
        $oids    = $_POST['oid']    ?? [];
        $old_to_new = [];
        $raw = [];
        foreach ($labels as $k => $label) {
            $label = trim($label);
            $url   = trim($urls[$k] ?? '');
            if ($label === '' && $url === '') continue;
            $new_id = 's' . count($raw);
            $old_id = $oids[$k] ?? $new_id;
            $old_to_new[$old_id] = $new_id;
            $it = [
                'label'  => $label,
                'url'    => $url,
                'icon'   => trim($icons[$k] ?? ''),
                'target' => trim($targets[$k] ?? ''),
                'parent' => $parents[$k] ?? '-1',
                '_id'    => $new_id,
            ];
            $raw[] = $it;
        }
        $new_items = [];
        foreach ($raw as $it) {
            $p = $it['parent'];
            if ($p === '-1' || $p === '' || !isset($old_to_new[$p])) {
                $it['parent'] = -1;
            } else {
                $it['parent'] = $old_to_new[$p];
            }
            $new_items[] = $it;
        }
        menu_save_admin_items($new_items);
        $items = $new_items;
        $message = "منوی پنل مدیریت ذخیره شد.";
    }

    $tree = menu_build_admin_tree($items);

    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <style>
        .menu-editor { max-width:920px; }
        .menu-editor .menu-card { border:1px solid var(--rang-border,#dde1e6); border-radius:10px; background:#fff; margin-bottom:10px; }
        .menu-editor .menu-card-row { display:flex; align-items:center; gap:8px; padding:8px 10px; flex-wrap:wrap; }
        .menu-editor .menu-children { padding-right:28px; border-right:2px dashed #e3a86b; margin-right:6px; background:rgba(255,111,0,0.04); border-radius:0 0 10px 10px; }
        .menu-editor .sort-handle { cursor:grab; color:#bbb; font-size:18px; user-select:none; }
        .menu-editor input[type=text], .menu-editor select { padding:7px 9px; border:1.5px solid #cdd3da; border-radius:6px; font-family:inherit; box-sizing:border-box; }
        .menu-editor .f-label { flex:2 1 160px; min-width:140px; }
        .menu-editor .f-url { flex:2 1 160px; min-width:140px; }
        .menu-editor .f-icon { flex:1 1 110px; min-width:100px; direction:ltr; text-align:left; }
        .menu-editor .f-target { flex:0 1 90px; }
        .menu-editor .f-parent { flex:1 1 130px; min-width:120px; }
        .menu-editor .help-icon { font-size:11px; color:var(--rang-gray,#999); flex-basis:100%; }
        .menu-editor .btn-del { background:#dc3545; color:#fff; border:none; border-radius:6px; padding:6px 12px; cursor:pointer; font-size:13px; }
        .menu-editor .btn-add { background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:8px; padding:10px 20px; cursor:pointer; font-weight:700; margin-top:6px; }
        .menu-editor .btn-save { background:#28a745; color:#fff; border:none; border-radius:8px; padding:12px 30px; cursor:pointer; font-weight:700; font-size:1rem; }
        .menu-editor .empty-msg { color:var(--rang-gray,#666); padding:30px; text-align:center; }
    </style>
    <div class="menu-editor">
        <h3>ویرایش منوی پنل مدیریت</h3>
        <p style="color:var(--rang-gray,#666); margin-bottom:20px;">
            آیتم‌های بدون پیوند (URL خالی) سرگروه زیرمنو هستند. زیرمنوها به سمت چپ، زیر سرگروه خود نمایش داده می‌شوند.
            برای جابه‌جا کردن یک زیرمنو به گروه دیگر، فیلد «والد» را عوض کنید یا آن را بکشید و در گروه جدید رها کنید.
        </p>
        <?php if (isset($message)) echo "<p style='color:green; font-weight:700;'>$message</p>"; ?>
        <form method="post" id="menuForm">
            <div id="menuTree">
                <?php echo menu_admin_render_tree($tree); ?>
            </div>
            <button type="button" class="btn-add" onclick="addAdminMenuItem()">+ افزودن آیتم</button>
            <br><br>
            <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> ذخیره منو</button>
        </form>
    </div>
    <script>
    function escapeAttr(s){ s = (s==null?'':s).toString(); return s.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }
    function menuCardHtml(idx, label, url, icon, target, parent, oid) {
        var tSel = target === '_blank' ? 'selected' : '';
        return '<div class="menu-card" data-idx="'+escapeAttr(idx)+'" data-parent="'+escapeAttr(parent)+'">' +
            '<div class="menu-card-row">' +
                '<span class="sort-handle">☰</span>' +
                '<input type="text" class="f-label" name="label[]" value="'+escapeAttr(label)+'" placeholder="عنوان">' +
                '<input type="text" class="f-url" name="url[]" value="'+escapeAttr(url)+'" placeholder="mod/dashmod">' +
                '<input type="text" class="f-icon" name="icon[]" value="'+escapeAttr(icon)+'" placeholder="fa-...">' +
                '<select class="f-target" name="target[]"><option value="">خود</option><option value="_blank" '+tSel+'>صفحه جدید</option></select>' +
                '<select class="f-parent" name="parent[]"></select>' +
                '<input type="hidden" name="oid[]" value="'+escapeAttr(oid)+'">' +
                '<button type="button" class="btn-del" onclick="this.closest(\'.menu-card\').remove(); refreshParentOptions();">✕</button>' +
            '</div>' +
            '<div class="menu-children"></div>' +
        '</div>';
    }
    function addAdminMenuItem() {
        var root = document.getElementById('menuTree');
        var nid = 'new' + Date.now() + Math.floor(Math.random()*1000);
        var div = document.createElement('div');
        div.innerHTML = menuCardHtml(nid, '', '', '', '', '-1', nid);
        root.appendChild(div.firstChild);
        refreshParentOptions();
    }
    function refreshParentOptions() {
        var cards = document.querySelectorAll('#menuTree .menu-card');
        cards.forEach(function(card){
            var myIdx = card.getAttribute('data-idx');
            var sel = card.querySelector('.f-parent');
            if (!sel) return;
            var cur = card.getAttribute('data-parent');
            var html = '<option value="-1">(سرگروه)</option>';
            cards.forEach(function(c){
                var id = c.getAttribute('data-idx');
                if (id === myIdx) return;
                var lbl = c.querySelector('.f-label').value || '(بدون عنوان)';
                html += '<option value="'+escapeAttr(id)+'"'+(id===cur?' selected':'')+'>'+escapeAttr(lbl)+'</option>';
            });
            sel.innerHTML = html;
        });
    }
    function reparentOnChange(e) {
        var card = e.target.closest('.menu-card');
        var newParent = e.target.value;
        card.setAttribute('data-parent', newParent);
        if (newParent === '-1') {
            document.getElementById('menuTree').appendChild(card);
        } else {
            var sel = '#menuTree .menu-card[data-idx="'+ (window.CSS && CSS.escape ? CSS.escape(newParent) : newParent) +'"]';
            var parentCard = document.querySelector(sel);
            if (parentCard) {
                var ch = parentCard.querySelector(':scope > .menu-children');
                if (!ch) { ch = document.createElement('div'); ch.className='menu-children'; parentCard.appendChild(ch); }
                ch.appendChild(card);
            }
        }
    }
    function syncParentFromDom() {
        document.querySelectorAll('#menuTree .menu-card').forEach(function(card){
            var p = '-1';
            var parentCard = card.parentElement.closest('.menu-card');
            if (parentCard) p = parentCard.getAttribute('data-idx');
            card.setAttribute('data-parent', p);
            var sel = card.querySelector('.f-parent');
            if (sel) sel.value = p;
        });
        refreshParentOptions();
    }
    document.addEventListener('change', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('f-parent')) reparentOnChange(e);
    });
    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('#menuTree, #menuTree .menu-children').forEach(function(el){
            new Sortable(el, { group: 'adm-menu', handle: '.sort-handle', animation: 150, fallbackOnBody: true, swapThreshold: 0.65,
                onEnd: function(){ setTimeout(syncParentFromDom, 0); } });
        });
    }
    refreshParentOptions();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

/**
 * Build admin menu tree from flat items.
 * Uses stable _id fields to link children to parents.
 * Returns array of [item, _idx, children[]].
 */
function menu_build_admin_tree($items) {
    $tree = [];
    $children_map = [];
    $gen = 0;
    foreach ($items as $item) {
        $id  = $item['_id'] ?? ('gen' . ($gen++));
        $parent = $item['parent'] ?? -1;
        if (!is_string($parent) && !is_int($parent)) $parent = -1;
        if ($parent === -1 || $parent === '-1') {
            $tree[] = ['item' => $item, '_idx' => $id, 'children' => []];
        } else {
            if (!isset($children_map[$parent])) $children_map[$parent] = [];
            $children_map[$parent][] = ['item' => $item, '_idx' => $id];
        }
    }
    // Attach children to parents
    foreach ($tree as &$node) {
        $idx = $node['_idx'];
        if (isset($children_map[$idx])) {
            $node['children'] = $children_map[$idx];
        }
    }
    unset($node);
    return $tree;
}

function menu_render_site($items) {
    if (empty($items)) {
        $items = menu_get_default_site_items();
    }
    ?>
    <nav class="nav">
        <ul>
            <?php foreach ($items as $item):
                $href = $item['url'] ?? '/';
                // Make sure URL starts with BASE_URL if it's relative
                if (strpos($href, 'http') !== 0 && strpos($href, '//') !== 0) {
                    $href = BASE_URL . ltrim($href, '/');
                }
                $is_active = (($safhe_faali ?? '') === trim($item['url'] ?? '/', '/')) ? 'faali' : '';
            ?>
            <li><a href="<?= htmlspecialchars($href) ?>" class="<?= $is_active ?>"><?= htmlspecialchars($item['label'] ?? '') ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?= BASE_URL ?>forushgah/sabad" class="cart-link" style="position:relative; padding-left:40px;">
                <i class="fa-solid fa-cart-shopping" style="font-size:18px;"></i>
                <span class="cart-badge" style="position:absolute; top:-6px; left:-6px; background:var(--rang-asli); color:#fff; font-size:11px; font-weight:700; width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center;"><?php echo function_exists('sabad_count') ? sabad_count() : '0' ?></span>
            </a></li>
        </ul>
    </nav>
    <?php
}

function menu_get_default_admin_items() {
    $defs = [
        ['label' => 'داشبورد', 'url' => 'mod/dashmod', 'icon' => 'fa-gauge-high', 'parent' => -1],
        ['label' => 'محتوا', 'url' => '', 'icon' => 'fa-file-lines', 'parent' => -1],
        ['label' => 'مقالات', 'url' => 'mod/content', 'icon' => 'fa-file-lines', 'parent' => 1],
        ['label' => 'برگه‌ها', 'url' => 'mod/pages', 'icon' => 'fa-file-lines', 'parent' => 1],
        ['label' => 'فروشگاه', 'url' => '', 'icon' => 'fa-store', 'parent' => -1],
        ['label' => 'محصولات', 'url' => 'mod/store/products', 'icon' => 'fa-cube', 'parent' => 4],
        ['label' => 'دسته‌بندی‌ها', 'url' => 'mod/store/categories', 'icon' => 'fa-folder', 'parent' => 4],
        ['label' => 'برندها', 'url' => 'mod/store/brands', 'icon' => 'fa-tag', 'parent' => 4],
        ['label' => 'سفارشات', 'url' => 'mod/store/orders', 'icon' => 'fa-truck', 'parent' => 4],
        ['label' => 'تنظیمات فروشگاه', 'url' => 'mod/store/settings', 'icon' => 'fa-gear', 'parent' => 4],
        ['label' => 'قالب', 'url' => '', 'icon' => 'fa-palette', 'parent' => -1],
        ['label' => 'بخش‌های محتوا', 'url' => 'mod/theme/sections', 'icon' => 'fa-puzzle-piece', 'parent' => 10],
        ['label' => 'ویرایش فایل‌ها', 'url' => 'mod/theme/files', 'icon' => 'fa-file-code', 'parent' => 10],
        ['label' => 'سفارشی‌سازی', 'url' => 'mod/theme/custom', 'icon' => 'fa-paint-brush', 'parent' => 10],
        ['label' => 'تنظیمات ظاهری', 'url' => 'mod/settings?tab=theme', 'icon' => 'fa-gear', 'parent' => 10],
        ['label' => 'منو سایت', 'url' => 'mod/theme/menu', 'icon' => 'fa-bars', 'parent' => 10],
        ['label' => 'صفحه‌ساز', 'url' => '', 'icon' => 'fa-layer-group', 'parent' => -1],
        ['label' => 'مدیریت صفحات', 'url' => 'mod/builder/pages', 'icon' => 'fa-layer-group', 'parent' => 16],
        ['label' => 'چت', 'url' => 'mod/chat', 'icon' => 'fa-comments', 'parent' => -1],
        ['label' => 'پیام‌ها', 'url' => 'mod/messages', 'icon' => 'fa-envelope', 'parent' => -1],
        ['label' => 'تنظیمات', 'url' => '', 'icon' => 'fa-gear', 'parent' => -1],
        ['label' => 'تنظیمات سایت', 'url' => 'mod/settings', 'icon' => 'fa-sliders', 'parent' => 20],
        ['label' => 'تنظیمات پنل', 'url' => 'mod/panel_settings', 'icon' => 'fa-palette', 'parent' => 20],
        ['label' => 'منو پنل مدیریت', 'url' => 'mod/menu_editor/admin', 'icon' => 'fa-bars', 'parent' => 20],
        ['label' => 'به‌روزرسانی', 'url' => 'mod/settings?tab=git', 'icon' => 'fa-github', 'parent' => 20],
        ['label' => 'بکاپ و بازگردانی', 'url' => 'mod/backup', 'icon' => 'fa-shield-halved', 'parent' => 20],
        ['label' => 'مشاهده سایت', 'url' => BASE_URL, 'icon' => 'fa-eye', 'parent' => -1, 'target' => '_blank'],
        ['label' => 'خروج', 'url' => 'mod/logout', 'icon' => 'fa-sign-out-alt', 'parent' => -1],
        ['label' => 'فایل‌ها', 'url' => '', 'icon' => 'fa-folder-open', 'parent' => -1],
        ['label' => 'مدیریت فایل‌ها', 'url' => 'mod/files', 'icon' => 'fa-folder-tree', 'parent' => 28],
        ['label' => 'آپلود عمومی', 'url' => 'mod/upload', 'icon' => 'fa-upload', 'parent' => 28],
    ];
    foreach ($defs as $i => &$d) {
        $d['_id'] = (string)$i;
    }
    unset($d);
    return $defs;
}

function menu_get_default_site_items() {
    return [
        ['label' => 'خانه', 'url' => '/'],
        ['label' => 'خدمات', 'url' => '/khadamat'],
        ['label' => 'تارنگار', 'url' => '/tarnegar'],
        ['label' => 'تماس با ما', 'url' => '/tamas'],
    ];
}

function menu_get_default_site_items_with_cart() {
    return [
        ['label' => 'خانه', 'url' => '/'],
        ['label' => 'خدمات', 'url' => '/khadamat'],
        ['label' => 'تارنگار', 'url' => '/tarnegar'],
        ['label' => 'سبد خرید', 'url' => '/forushgah/sabad'],
        ['label' => 'تماس با ما', 'url' => '/tamas'],
    ];
}

function menu_render_admin($items) {
    if (empty($items)) {
        $items = menu_get_default_admin_items();
    }
    $tree = menu_build_admin_tree($items);
    ?>
    <div class="admin-nav">
        <?php foreach ($tree as $node):
            $item = $node['item'];
            $has_children = !empty($node['children']);
            $url = $item['url'] ?? '';
            if ($url !== '' && strpos($url, 'http') !== 0 && strpos($url, '//') !== 0) {
                $url = BASE_URL . ltrim($url, '/');
            }
            $icon = !empty($item['icon']) ? '<i class="fa-solid ' . htmlspecialchars($item['icon']) . '"></i> ' : '';
            $target = !empty($item['target']) ? ' target="' . htmlspecialchars($item['target']) . '"' : '';
        ?>
        <?php if ($has_children): ?>
        <div class="nav-item"><a><?= $icon ?><?= htmlspecialchars($item['label'] ?? '') ?> ▾</a>
            <div class="submenu">
                <?php foreach ($node['children'] as $child):
                    $child = $child['item'];
                    $child_url = $child['url'] ?? '';
                    if ($child_url !== '' && strpos($child_url, 'http') !== 0 && strpos($child_url, '//') !== 0) {
                        $child_url = BASE_URL . ltrim($child_url, '/');
                    }
                    $child_icon = !empty($child['icon']) ? '<i class="fa-solid ' . htmlspecialchars($child['icon']) . '"></i>' : '';
                    $child_target = !empty($child['target']) ? ' target="' . htmlspecialchars($child['target']) . '"' : '';
                ?>
                <a href="<?= htmlspecialchars($child_url) ?>"<?= $child_target ?>><?= $child_icon ?> <?= htmlspecialchars($child['label'] ?? '') ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="nav-item"><a href="<?= htmlspecialchars($url) ?>"<?= $target ?>><?= $icon ?><?= htmlspecialchars($item['label'] ?? '') ?></a></div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php
}
