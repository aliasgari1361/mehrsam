<?php
/**
 * مدیریت فایل‌ها (پنل مدیریت + آپلود عمومی)
 */

require_once __DIR__ . '/../../haste/tanzimat.php';

function files_folder_display_name($folder_name) {
    return $folder_name;
}

function admin_files_route($action = '', $params = []) {
    require_once __DIR__ . '/file-functions.php';

    $message = '';
    $FILES_BASE = FILES_DIR;

    // بررسی امنیت مسیر (جلوگیری از traversal)
    $safe_path = function ($rel) use ($FILES_BASE) {
        $rel = ltrim(str_replace('\\', '/', $rel), '/');
        $abs = realpath($FILES_BASE . $rel);
        $base = realpath($FILES_BASE);
        if ($abs === false || $base === false || strpos($abs, $base) !== 0) return false;
        return $abs;
    };

    // ---- عملیات POST ----
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        if ($action === 'upload') {
            $subdir = trim($_POST['subdir'] ?? '');
            $subdir = preg_replace('#[^\p{L}\p{N}/_\-]#u', '', $subdir);
            $res = upload_download_file('userfile', $subdir);
            $message = isset($res['error']) ? $res['error'] : 'فایل با موفقیت آپلود شد: ' . $res['path'];
        } elseif ($action === 'mkdir') {
            $folder = trim($_POST['folder'] ?? '');
            $parent = trim($_POST['parent_folder'] ?? '');
            $folder = preg_replace('#[^\p{L}\p{N}/_\-]#u', '', $folder);
            $parent = preg_replace('#[^\p{L}\p{N}/_\-]#u', '', $parent);
            $full_path = ltrim($parent, '/') . ($parent !== '' && $folder !== '' ? '/' : '') . $folder;
            if ($full_path !== '') {
                $d = $FILES_BASE . ltrim($full_path, '/');
                if (!is_dir($d)) mkdir($d, 0755, true);
                $message = 'پوشه جدید ساخته شد: ' . $full_path;
            }
        } elseif ($action === 'add_usage') {
            $rel    = trim($_POST['rel'] ?? '');
            $ctype  = trim($_POST['content_type'] ?? '');
            $cid    = (int)($_POST['content_id'] ?? 0);
            $note   = trim($_POST['note'] ?? '');
            if ($rel !== '' && $ctype !== '') {
                file_add_manual_usage($rel, $ctype, $cid, $note);
                $message = 'انتساب دستی ثبت شد.';
            }
        }
    }

    // ---- عملیات GET ----
    if ($action === 'delete' && isset($_GET['file'])) {
        $abs = $safe_path($_GET['file']);
        if ($abs !== false && is_file($abs)) { unlink($abs); $message = 'فایل حذف شد.'; }
    }
    if ($action === 'delete_folder' && isset($_GET['folder'])) {
        $abs = $safe_path($_GET['folder']);
        if ($abs !== false && is_dir($abs)) { file_delete_dir($abs); $message = 'پوشه حذف شد.'; }
    }
    if ($action === 'remove_usage' && isset($_GET['id'])) {
        file_remove_manual_usage((int)$_GET['id']);
        $message = 'انتساب حذف شد.';
    }
    if ($action === 'download' && isset($_GET['file'])) {
        $abs = $safe_path($_GET['file']);
        if ($abs !== false && is_file($abs)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($abs) . '"');
            header('Content-Length: ' . filesize($abs));
            readfile($abs);
            exit;
        }
    }

    // ---- آماده‌سازی داده‌ها ----
    $tree = file_list_tree($FILES_BASE);
    $usages_map = file_scan_all_usages();
    $selected_rel = $_GET['file'] ?? '';
    $selected_abs = $selected_rel !== '' ? $safe_path($selected_rel) : false;
    if ($selected_abs === false || !is_file($selected_abs)) {
        $selected_abs = false;
        $selected_rel = '';
    }

    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .fm-wrap { display:flex; align-items:flex-start; position:relative; }
        .fm-detail { width:25%; min-width:260px; background:#fff; border:1px solid var(--rang-border,#dde1e6); border-radius:12px; padding:14px; }
        .fm-detail h4 { margin-top:0; }
        .fm-divider { width:6px; cursor:col-resize; background:#e0e3e8; border-radius:3px; flex-shrink:0; margin:0 10px; align-self:stretch; transition:background 0.15s; position:relative; }
        .fm-divider:hover, .fm-divider.dragging { background:var(--rang-asli,#FF6F00); }
        .fm-divider::after { content:''; position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); width:2px; height:30px; background:rgba(255,255,255,0.6); border-radius:2px; }
        .fm-tree { flex:1; min-width:350px; background:#fff; border:1px solid var(--rang-border,#dde1e6); border-radius:12px; padding:14px; }
        .fm-tree h4 { margin:0 0 12px; }
        .fm-tree ul { list-style:none; margin:0; padding:0; }
        .fm-tree .fm-node { padding-right:14px; border-right:2px solid #eee; margin:2px 0; }
        .fm-tree .fm-row { display:flex; align-items:center; gap:6px; padding:5px 8px; border-radius:8px; }
        .fm-tree .fm-row:hover { background:#f6f7f9; }
        .fm-tree .fm-row.sel { background:var(--rang-asli,#FF6F00); color:#fff; }
        .fm-tree .fm-row.sel a, .fm-tree .fm-row.sel span { color:#fff; }
        .fm-tree a { color:var(--rang-matn,#1a1a1a); text-decoration:none; }
        .fm-tree .ic { width:22px; text-align:center; color:var(--rang-asli,#FF6F00); }
        .fm-tree .sz { margin-right:auto; font-size:11px; color:var(--rang-gray,#999); }
        .fm-tree details > summary { cursor:pointer; list-style:none; }
        .fm-tree details > summary::-webkit-details-marker { display:none; }
        .fm-card { background:#fafbfc; border:1px solid var(--rang-border,#dde1e6); border-radius:10px; padding:12px; margin-bottom:14px; }
        .fm-usage { background:#fff8ef; border:1px solid #ffe2b8; border-radius:8px; padding:10px 12px; margin:6px 0; font-size:13px; }
        .fm-usage.manual { background:#eaf6ff; border-color:#b8dcff; }
        .fm-url { direction:ltr; font-size:12px; background:#f1f3f5; padding:8px 10px; border-radius:6px; word-break:break-all; }
        .btn-sm { padding:5px 12px; border:none; border-radius:6px; cursor:pointer; font-size:13px; background:var(--rang-asli,#FF6F00); color:#fff; }
        .btn-danger { background:#dc3545; }
        .fm-form input[type=text] { width:100%; padding:9px 11px; border:1.5px solid #cdd3da; border-radius:7px; font-family:inherit; box-sizing:border-box; margin-bottom:8px; color:#222; }
        .fm-form select { width:100%; min-width:200px; padding:9px 15px; border:1.5px solid #cdd3da; border-radius:7px; font-family:inherit; box-sizing:border-box; margin-bottom:8px; text-align:right; direction:rtl; }
        .fm-form label { display:block; font-weight:600; margin-bottom:4px; font-size:13px; }
        .badge-auto { background:#f0ad4e; color:#fff; border-radius:4px; padding:1px 6px; font-size:11px; }
        .badge-manual { background:#3aa0ff; color:#fff; border-radius:4px; padding:1px 6px; font-size:11px; }
    </style>

    <div class="fm-wrap">
        <!-- ستون راست: فرم‌ها -->
        <div class="fm-detail">
            <h4><i class="fa-solid fa-pen"></i> ایجاد پوشه و آپلود</h4>

            <?php if ($message): ?>
                <p style="color:#198754; font-weight:700; background:#eafaf1; padding:8px 12px; border-radius:8px;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <!-- فرم ایجاد پوشه -->
            <div class="fm-card fm-form">
                <form method="post" action="<?= BASE_URL ?>mod/files/mkdir">
                    <label>ساخت پوشه جدید</label>
                    <input type="text" name="folder" placeholder="نام پوشه (مثلاً: مقالات-جدید)" dir="ltr">
                    <label>در پوشه (اختیاری)</label>
                    <select name="parent_folder" dir="rtl">
                        <option value="">(ریشه فایل‌ها)</option>
                        <?php
                        function render_mkdir_folder_options($nodes, $prefix = '') {
                            foreach ($nodes['children'] ?? [] as $node) {
                                if ($node['type'] === 'dir') {
                                    $display_name = files_folder_display_name($node['name']);
                                    echo '<option value="' . htmlspecialchars($node['rel']) . '">' . $prefix . htmlspecialchars($display_name) . '</option>';
                                    render_mkdir_folder_options($node, $prefix . '&nbsp;&nbsp;');
                                }
                            }
                        }
                        render_mkdir_folder_options($tree);
                        ?>
                    </select>
                    <button type="submit" class="btn-sm"><i class="fa-solid fa-plus"></i> ساخت</button>
                </form>
            </div>

            <!-- فرم آپلود -->
            <div class="fm-card fm-form">
                <form method="post" action="<?= BASE_URL ?>mod/files/upload" enctype="multipart/form-data">
                    <label>آپلود فایل جدید</label>
                    <input type="file" name="userfile" required>
                    <label>پوشه مقصد</label>
                    <select name="subdir" dir="rtl">
                        <option value="">(ریشه فایل‌ها)</option>
                        <?php
                        function render_folder_options($nodes, $prefix = '') {
                            foreach ($nodes['children'] ?? [] as $node) {
                                if ($node['type'] === 'dir') {
                                    $display_name = files_folder_display_name($node['name']);
                                    $sel = (isset($_POST['subdir']) && $_POST['subdir'] === $node['rel']) ? ' selected' : '';
                                    echo '<option value="' . htmlspecialchars($node['rel']) . '"' . $sel . '>' . $prefix . htmlspecialchars($display_name) . '</option>';
                                    render_folder_options($node, $prefix . '&nbsp;&nbsp;');
                                }
                            }
                        }
                        render_folder_options($tree);
                        ?>
                    </select>
                    <button type="submit" class="btn-sm"><i class="fa-solid fa-upload"></i> آپلود</button>
                </form>
            </div>

            <?php if ($selected_abs): ?>
                <?php
                    $rel = file_rel_path($selected_abs);
                    $auto = $usages_map[$rel] ?? [];
                    $manual = file_get_manual_usages($rel);
                ?>
                <div class="fm-card">
                    <p><strong>نام فایل:</strong> <?= htmlspecialchars(basename($selected_abs)) ?></p>
                    <p><strong>حجم:</strong> <?= files_format_size(filesize($selected_abs)) ?></p>
                    <p><strong>نشانی:</strong></p>
                    <div class="fm-url"><?= htmlspecialchars(FILES_URL . $rel) ?></div>
                    <p style="margin-top:10px;">
                        <a class="btn-sm" href="<?= BASE_URL ?>mod/files/download?file=<?= urlencode($rel) ?>"><i class="fa-solid fa-download"></i> دانلود</a>
                        <a class="btn-sm btn-danger" href="<?= BASE_URL ?>mod/files/delete?file=<?= urlencode($rel) ?>" onclick="return confirm('حذف شود؟');"><i class="fa-solid fa-trash"></i> حذف</a>
                    </p>
                </div>

                <!-- محل‌های استفاده -->
                <div class="fm-card">
                    <h4 style="margin-bottom:10px;"><i class="fa-solid fa-link"></i> محل‌های استفاده</h4>
                    <?php if (empty($auto) && empty($manual)): ?>
                        <p style="color:var(--rang-gray,#999);">هیچ استفاده‌ای یافت نشد.</p>
                    <?php else: ?>
                        <?php foreach ($auto as $u): ?>
                            <div class="fm-usage">
                                <span class="badge-auto">خودکار</span>
                                <?= file_content_type_label($u['type']) ?>:
                                <a href="<?= file_content_edit_url($u['type'], $u['id']) ?>" target="_blank"><?= htmlspecialchars($u['title'] ?: '(بدون عنوان)') ?></a>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($manual as $u): ?>
                            <div class="fm-usage manual">
                                <span class="badge-manual">دستی</span>
                                <?= file_content_type_label($u['content_type']) ?>:
                                <?php $t = file_resolve_title($u['content_type'], $u['content_id']); ?>
                                <?= htmlspecialchars($t ?: ('(شناسه ' . $u['content_id'] . ')')) ?>
                                <?php if ($u['note']): ?> — <?= htmlspecialchars($u['note']) ?><?php endif; ?>
                                <a href="<?= BASE_URL ?>mod/files/remove_usage?id=<?= $u['id'] ?>" onclick="return confirm('حذف انتساب؟');" style="color:#dc3545; margin-right:8px;"><i class="fa-solid fa-xmark"></i></a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- فرم انتساب دستی -->
                    <form method="post" action="<?= BASE_URL ?>mod/files/add_usage" class="fm-form" style="margin-top:12px;">
                        <input type="hidden" name="rel" value="<?= htmlspecialchars($rel) ?>">
                        <label>افزودن انتساب دستی</label>
                        <select name="content_type" dir="rtl">
                            <option value="post">پست (مقاله/خبر)</option>
                            <option value="page">برگه (صفحه ایستا)</option>
                            <option value="article">مقاله (مقاله تخصصی)</option>
                            <option value="product">محصول (فروشگاه)</option>
                            <option value="khadamat">خدمت</option>
                            <option value="custom">سایر (دلخواه)</option>
                        </select>
                        <input type="text" name="content_id" placeholder="شناسه محتوا (عدد)" dir="ltr" style="width:100px;">
                        <input type="text" name="note" placeholder="توضیح (اختیاری)">
                        <button type="submit" class="btn-sm">انتساب</button>
                    </form>
                </div>
            <?php else: ?>
                <p style="color:var(--rang-gray,#999);">برای مشاهده جزئیات و محل استفاده، یک فایل را از درخت سمت چپ انتخاب کنید.</p>
            <?php endif; ?>
        </div>

        <div class="fm-divider" id="fmDivider"></div>

        <!-- ستون چپ: درخت فایل‌ها -->
        <div class="fm-tree" id="fmTree">
            <h4><i class="fa-solid fa-folder-tree"></i> فایل‌ها (درخت)</h4>
            <?php echo file_render_tree($tree, $selected_rel); ?>
        </div>
    </div>

    <script>
    (function() {
        var divider = document.getElementById('fmDivider');
        var detail = divider.previousElementSibling;
        var tree = document.getElementById('fmTree');
        var wrap = divider.parentElement;
        var isDragging = false;

        divider.addEventListener('mousedown', function(e) {
            isDragging = true;
            divider.classList.add('dragging');
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            var rect = wrap.getBoundingClientRect();
            var pct = ((rect.right - e.clientX) / rect.width) * 100;
            if (pct < 18) pct = 18;
            if (pct > 50) pct = 50;
            detail.style.width = pct + '%';
        });

        document.addEventListener('mouseup', function() {
            if (!isDragging) return;
            isDragging = false;
            divider.classList.remove('dragging');
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
        });
    })();
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

/**
 * رندر بازگشتی درخت فایل‌ها (کودکان به سمت راست در RTL)
 */
function file_render_tree($node, $selected_rel, $depth = 0) {
    $html = '';
    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            if ($child['type'] === 'dir') {
                $rel = file_rel_path($child['path']);
                $html .= '<details' . ($depth < 1 ? ' open' : '') . ' class="fm-node">';
                $html .= '<summary class="fm-row"><span class="ic"><i class="fa-solid fa-folder"></i></span>';
                $html .= '<a href="' . BASE_URL . 'mod/files?folder=' . urlencode($rel) . '">' . htmlspecialchars($child['name']) . '</a>';
                $html .= ' <a href="' . BASE_URL . 'mod/files/delete_folder?folder=' . urlencode($rel) . '" onclick="return confirm(\'حذف پوشه و محتویات؟\');" style="color:#dc3545;margin-right:6px;"><i class="fa-solid fa-trash-can" style="font-size:12px;"></i></a>';
                $html .= '</summary>';
                $html .= '<ul>' . file_render_tree($child, $selected_rel, $depth + 1) . '</ul>';
                $html .= '</details>';
            } else {
                $rel = $child['rel'];
                $sel = ($rel === $selected_rel) ? ' sel' : '';
                $icon = file_type_icon($child['ext']);
                $html .= '<div class="fm-node"><div class="fm-row' . $sel . '">';
                $html .= '<span class="ic"><i class="fa-solid ' . $icon . '"></i></span>';
                $html .= '<a href="' . BASE_URL . 'mod/files?file=' . urlencode($rel) . '">' . htmlspecialchars($child['name']) . '</a>';
                $html .= '<span class="sz">' . $child['size'] . '</span>';
                $html .= '</div></div>';
            }
        }
    }
    return $html;
}

/**
 * آپلود عمومی کاربران (در صورت فعال بودن در تنظیمات)
 */
function public_upload_route() {
    require_once __DIR__ . '/file-functions.php';

    $enabled = (bool)get_site_setting('files.user_upload_enabled');
    $message = '';
    $uploaded_url = '';

    if (!$enabled) {
        include __DIR__ . '/../../ghaleb/mehrsam/sarfaraz.php';
        echo '<main class="mohtava-container" style="padding:60px 0;"><div class="fm-card" style="max-width:480px;margin:auto;"><p style="text-align:center;color:#dc3545;">آپلود عمومی در حال حاضر غیرفعال است.</p></div></main>';
        include __DIR__ . '/../../ghaleb/mehrsam/panevis.php';
        return;
    }

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        $res = upload_download_file('userfile', 'user/');
        if (isset($res['error'])) {
            $message = $res['error'];
        } else {
            $message = 'فایل با موفقیت آپلود شد.';
            $uploaded_url = $res['url'];
        }
    }

    include __DIR__ . '/../../ghaleb/mehrsam/sarfaraz.php';
    ?>
    <main class="mohtava-container" style="padding:50px 0;">
        <div class="fm-card" style="max-width:520px;margin:auto;">
            <h3><i class="fa-solid fa-upload"></i> آپلود فایل</h3>
            <p style="color:var(--rang-gray,#666);">حداکثر حجم: <?= files_format_size(files_get_max_size()) ?></p>
            <?php if ($message): ?><p style="color:<?= $uploaded_url ? '#198754' : '#dc3545' ?>;font-weight:700;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
            <?php if ($uploaded_url): ?>
                <p><strong>نشانی فایل:</strong></p>
                <div class="fm-url"><?= htmlspecialchars($uploaded_url) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= BASE_URL ?>mod/upload" enctype="multipart/form-data" class="fm-form">
                <input type="file" name="userfile" required>
                <button type="submit" class="btn-sm"><i class="fa-solid fa-upload"></i> آپلود</button>
            </form>
        </div>
    </main>
    <?php
    include __DIR__ . '/../../ghaleb/mehrsam/panevis.php';
}
