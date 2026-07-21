<?php
/**
 * توابع مدیریت فایل‌ها (آپلود، اسکن محل استفاده، ساخت پوشه محتوا)
 */

require_once __DIR__ . '/../../haste/tanzimat.php';
require_once MASIR_DADE . 'bank.php';

function files_get_allowed_extensions() {
    $exts = get_site_setting('files.allowed_extensions');
    if (empty($exts)) return [];
    return array_filter(array_map('trim', explode(',', $exts)));
}

function files_get_max_size() {
    $max = get_site_setting('files.max_upload_size');
    return (int)($max ?? 0);
}

function files_format_size($bytes) {
    $bytes = (float)$bytes;
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' مگابایت';
    if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' کیلوبایت';
    return $bytes . ' بایت';
}

function files_safe_name($name) {
    $name = basename($name);
    $name = preg_replace('/[^\p{L}\p{N}_\.\-]/u', '_', $name);
    if ($name === '' || $name === '.') $name = uniqid('file_');
    return $name;
}

/**
 * آپلود یک فایل قابل دانلود
 * - ادمین: بدون محدودیت حجم
 * - کاربر: محدود به files.max_upload_size
 * برمی‌گرداند: ['url'=>..., 'path'=>...] یا ['error'=>...]
 */
function upload_download_file($input_name, $subdir = '') {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'فایلی انتخاب نشده است.'];
    }
    $file = $_FILES[$input_name];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = files_get_allowed_extensions();
    if (!empty($allowed) && !in_array($ext, $allowed, true)) {
        return ['error' => 'پسوند مجاز نیست. پسوندهای مجاز: ' . implode('، ', $allowed)];
    }
    if (!isAdmin()) {
        $max = files_get_max_size();
        if ($max > 0 && $file['size'] > $max) {
            return ['error' => 'حجم فایل بیشتر از حد مجاز (' . files_format_size($max) . ') است.'];
        }
    }
    $subdir = ltrim($subdir, '/');
    $target_dir = FILES_DIR . $subdir;
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true) && !is_dir($target_dir)) {
            return ['error' => 'خطا در ایجاد پوشه مقصد.'];
        }
    }
    $safe_name = files_safe_name($file['name']);
    $target_path = $target_dir . $safe_name;
    if (file_exists($target_path)) {
        $safe_name = time() . '_' . $safe_name;
        $target_path = $target_dir . $safe_name;
    }
    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['error' => 'خطا در انتقال فایل به سرور.'];
    }
    $rel = $subdir . $safe_name;
    return ['url' => FILES_URL . $rel, 'path' => $rel];
}

/**
 * ساخت خودکار پوشه مربوط به یک محتوا در هنگام ایجاد آن
 * نوع‌های مجاز: blog/safhe/maghaleh/page/post/product/mahsul
 */
function file_create_content_folder($type, $slug) {
    $map = [
        'blog'     => 'posts',
        'post'     => 'posts',
        'safhe'    => 'pages',
        'page'     => 'pages',
        'maghaleh' => 'articles',
        'article'  => 'articles',
        'mahsul'   => 'products',
        'product'  => 'products',
    ];
    $folder = $map[$type] ?? $type;
    if (empty($slug)) $slug = uniqid();
    $dir = FILES_DIR . $folder . '/' . $slug . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

/**
 * اسکن یک متن و استخراج نشانی فایل‌های موجود در پوشه files
 * برمی‌گرداند آرایه‌ای از مسیرهای نسبی پیدا شده
 */
function file_extract_refs($content) {
    $found = [];
    if (!is_string($content) || $content === '') return $found;
    // نشانی کامل like http://host/ghaleb/manabe/uploads/files/xxx
    if (preg_match_all("#https?://[^\\s\"'<>]+?/ghaleb/manabe/uploads/files/([^\\s\"'<>]+)#i", $content, $m)) {
        foreach ($m[1] as $rel) $found[] = ltrim($rel, '/');
    }
    // یا مسیر نسبی like /ghaleb/manabe/uploads/files/xxx
    if (preg_match_all("#/ghaleb/manabe/uploads/files/([^\\s\"'<>]+)#i", $content, $m)) {
        foreach ($m[1] as $rel) $found[] = ltrim($rel, '/');
    }
    return array_values(array_unique($found));
}

/**
 * اسکن کل محتواها و ساخت نقشه: مسیر نسبی فایل => لیست محل‌های استفاده (خودکار)
 */
function file_scan_all_usages() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $map = [];
    $add = function (&$map, $rel, $type, $id, $label) {
        if (!isset($map[$rel])) $map[$rel] = [];
        $map[$rel][] = ['type' => $type, 'id' => $id, 'title' => $label, 'source' => 'auto'];
    };

    // پست‌ها / برگه‌ها / مقالات
    $res = $conn->query("SELECT id, title, type, content FROM posts");
    while ($row = $res->fetch_assoc()) {
        $type = $row['type'] ?? 'post';
        foreach (file_extract_refs($row['content'] ?? '') as $rel) {
            $add($map, $rel, $type, (int)$row['id'], $row['title']);
        }
    }
    // محصولات
    if (!$conn->query("SHOW TABLES LIKE 'mahsulat'")->num_rows) {
        // جدول وجود ندارد، رد شو
    } else {
        $res = $conn->query("SELECT id, onvan, tasvir, tozih, virayesh FROM mahsulat");
        while ($row = $res->fetch_assoc()) {
            $content = ($row['tasvir'] ?? '') . ' ' . ($row['tozih'] ?? '') . ' ' . ($row['virayesh'] ?? '');
            foreach (file_extract_refs($content) as $rel) {
                $add($map, $rel, 'product', (int)$row['id'], $row['onvan']);
            }
        }
    }
    // خدمات (اگر وجود داشته باشد)
    if ($conn->query("SHOW TABLES LIKE 'khadamat'")->num_rows) {
        $res = $conn->query("SELECT id, title, content FROM khadamat");
        while ($row = $res->fetch_assoc()) {
            foreach (file_extract_refs(($row['content'] ?? '')) as $rel) {
                $add($map, $rel, 'khadamat', (int)$row['id'], $row['title']);
            }
        }
    }
    $conn->close();
    return $map;
}

/**
 * دریافت انتساب‌های دستی یک فایل از جدول file_usage
 */
function file_get_manual_usages($rel_path) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($conn->query("SHOW TABLES LIKE 'file_usage'")->num_rows == 0) {
        $conn->close();
        return [];
    }
    $stmt = $conn->prepare("SELECT id, content_type, content_id, note FROM file_usage WHERE file_path = ? ORDER BY id");
    $stmt->bind_param('s', $rel_path);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($r = $res->fetch_assoc()) {
        $r['source'] = 'manual';
        $out[] = $r;
    }
    $stmt->close();
    $conn->close();
    return $out;
}

function file_add_manual_usage($rel_path, $content_type, $content_id, $note = '') {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($conn->query("SHOW TABLES LIKE 'file_usage'")->num_rows == 0) {
        $conn->close();
        return false;
    }
    $stmt = $conn->prepare("INSERT INTO file_usage (file_path, content_type, content_id, note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssis', $rel_path, $content_type, $content_id, $note);
    $ok = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $ok;
}

function file_remove_manual_usage($usage_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($conn->query("SHOW TABLES LIKE 'file_usage'")->num_rows == 0) {
        $conn->close();
        return false;
    }
    $stmt = $conn->prepare("DELETE FROM file_usage WHERE id = ?");
    $stmt->bind_param('i', $usage_id);
    $ok = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $ok;
}

/**
 * محاسبه مسیر نسبی یک فایل از پوشه files
 */
function file_rel_path($abs_or_url) {
    $abs_or_url = str_replace('\\', '/', $abs_or_url);
    $dir = str_replace('\\', '/', FILES_DIR);
    if (strpos($abs_or_url, $dir) === 0) {
        return ltrim(substr($abs_or_url, strlen($dir)), '/');
    }
    $url = rtrim(FILES_URL, '/');
    if (strpos($abs_or_url, $url) === 0) {
        return ltrim(substr($abs_or_url, strlen($url)), '/');
    }
    return ltrim($abs_or_url, '/');
}

/**
 * لیست بازگشتی فایل‌ها و پوشه‌ها در یک مسیر
 * برمی‌گرداند ساختار درختی
 */
function file_list_tree($dir = null) {
    if ($dir === null) $dir = FILES_DIR;
    $result = ['name' => basename(rtrim($dir, '/')) ?: 'files', 'path' => $dir, 'type' => 'dir', 'children' => []];
    if (!is_dir($dir)) return $result;
    $items = scandir($dir);
    $items = array_diff($items, ['.', '..']);
    natcasesort($items);
    foreach ($items as $name) {
        $full = rtrim($dir, '/') . '/' . $name;
        if (is_dir($full)) {
            $result['children'][] = file_list_tree($full);
        } else {
            $result['children'][] = [
                'name'  => $name,
                'path'  => $full,
                'rel'   => file_rel_path($full),
                'type'  => 'file',
                'size'  => files_format_size(filesize($full)),
                'bytes' => filesize($full),
                'ext'   => strtolower(pathinfo($name, PATHINFO_EXTENSION)),
                'mtime' => filemtime($full),
            ];
        }
    }
    // Add rel to the directory itself
    $result['rel'] = file_rel_path($dir);
    return $result;
}

function file_type_icon($ext) {
    $map = [
        'pdf'  => 'fa-file-pdf',
        'zip'  => 'fa-file-zipper',
        'rar'  => 'fa-file-zipper',
        'doc'  => 'fa-file-word',
        'docx' => 'fa-file-word',
        'xls'  => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'ppt'  => 'fa-file-powerpoint',
        'pptx' => 'fa-file-powerpoint',
        'txt'  => 'fa-file-lines',
        'jpg'  => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png'  => 'fa-file-image',
        'gif'  => 'fa-file-image',
        'webp' => 'fa-file-image',
    ];
    return $map[$ext] ?? 'fa-file';
}

function file_content_type_label($type) {
    $map = [
        'post'     => 'پست',
        'page'     => 'برگه',
        'article'  => 'مقاله',
        'product'  => 'محصول',
        'khadamat' => 'خدمت',
        'custom'   => 'دلخواه',
    ];
    return $map[$type] ?? $type;
}

function file_delete_dir($dir) {
    if (!is_dir($dir)) return;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $full = $dir . $item;
        if (is_dir($full)) file_delete_dir($full);
        else unlink($full);
    }
    rmdir($dir);
}

/**
 * یافتن عنوان یک محتوا بر اساس نوع و شناسه (برای نمایش انتساب‌ها)
 */
function file_resolve_title($type, $id) {
    if (!$id) return '';
    $bank = new Bank();
    $conn = $bank->getConnection();
    $title = '';
    if (in_array($type, ['post', 'page', 'article', 'blog', 'safhe', 'maghaleh'])) {
        $stmt = $conn->prepare("SELECT title FROM posts WHERE id = ?");
    } elseif ($type === 'product' || $type === 'mahsul') {
        $stmt = $conn->prepare("SELECT onvan FROM mahsulat WHERE id = ?");
    } elseif ($type === 'khadamat') {
        $stmt = $conn->prepare("SELECT title FROM khadamat WHERE id = ?");
    } else {
        $conn->close();
        return '';
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) {
        $title = $type === 'product' || $type === 'mahsul' ? $r['onvan'] : $r['title'];
    }
    $stmt->close();
    $conn->close();
    return $title;
}

function file_content_edit_url($type, $id) {
    if (in_array($type, ['post', 'page', 'article', 'blog', 'safhe', 'maghaleh'])) {
        return BASE_URL . 'mod/edit_content/' . $id;
    }
    if ($type === 'product' || $type === 'mahsul') {
        return BASE_URL . 'mod/store/products';
    }
    if ($type === 'khadamat') {
        return BASE_URL . 'mod/content';
    }
    return '';
}
