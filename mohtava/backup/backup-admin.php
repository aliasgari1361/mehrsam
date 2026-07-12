<?php

function admin_backup_route($action = '') {
    require_once __DIR__ . '/backup-functions.php';
    $message = '';

    if ($action === 'create') {
        $res = backup_create_zip();
        if (isset($res['error'])) {
            $message = '<p style="color:#dc3545;">' . htmlspecialchars($res['error']) . '</p>';
        } else {
            $message = '<p style="color:#198754;">بکاپ با موفقیت ایجاد شد: ' . htmlspecialchars($res['name']) . '</p>';
        }
    } elseif ($action === 'delete' && isset($_GET['file'])) {
        backup_delete($_GET['file']);
        $message = '<p style="color:#198754;">بکاپ حذف شد.</p>';
    } elseif ($action === 'restore' && isset($_GET['file'])) {
        $res = backup_restore($_GET['file']);
        if (isset($res['error'])) {
            $message = '<p style="color:#dc3545;">' . htmlspecialchars($res['error']) . '</p>';
        } else {
            $message = '<p style="color:#198754;font-weight:700;">بکاپ با موفقیت بازگردانی شد!</p>';
        }
    } elseif ($action === 'download' && isset($_GET['file'])) {
        $file = basename($_GET['file']);
        $path = BACKUP_DIR . '/' . $file;
        if (file_exists($path)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
    }

    $backups = backup_list();

    include MASIR_RISH . 'ghaleb/ghmod/sarsafhe.php';
    ?>
    <style>
        .bp-wrap { max-width:800px; }
        .bp-wrap h3 { margin-top:0; }
        .bp-table { width:100%; border-collapse:collapse; }
        .bp-table th, .bp-table td { padding:10px 12px; text-align:right; border-bottom:1px solid #e0e3e8; }
        .bp-table th { background:#f6f7f9; font-size:13px; }
        .bp-table tr:hover td { background:#fafbfc; }
        .bp-actions { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
        .bp-btn { display:inline-block; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13px; }
        .bp-btn-primary { background:var(--rang-asli,#FF6F00); color:#fff; }
        .bp-btn-danger { background:#dc3545; color:#fff; }
        .bp-btn-secondary { background:#6c757d; color:#fff; }
        .bp-empty { padding:30px; text-align:center; color:#999; font-size:14px; }
        .bp-info { font-size:13px; color:#666; margin-bottom:12px; }
    </style>

    <div class="bp-wrap">
        <h3><i class="fa-solid fa-shield-halved"></i> بکاپ و بازگردانی</h3>

        <?= $message ?>

        <div class="bp-actions">
            <a href="<?= BASE_URL ?>mod/backup/create" class="bp-btn bp-btn-primary"><i class="fa-solid fa-plus"></i> ایجاد بکاپ جدید</a>
        </div>

        <div class="bp-info">
            بکاپ شامل دیتابیس + فایل‌های اصلی (پوسته، ماژول‌ها، تنظیمات) است.
            فایل‌ها در پوشه <code>backups/</code> ذخیره می‌شوند.
        </div>

        <?php if (empty($backups)): ?>
            <div class="bp-empty">هنوز بکاپی گرفته نشده است.</div>
        <?php else: ?>
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>نام فایل</th>
                        <th>تاریخ</th>
                        <th>حجم</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['name']) ?></td>
                            <td><?= htmlspecialchars($b['date']) ?></td>
                            <td><?= backup_format_size($b['size']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>mod/backup/download?file=<?= urlencode($b['name']) ?>" class="bp-btn bp-btn-secondary" style="padding:4px 10px;font-size:12px;"><i class="fa-solid fa-download"></i> دانلود</a>
                                <a href="<?= BASE_URL ?>mod/backup/restore?file=<?= urlencode($b['name']) ?>" class="bp-btn bp-btn-primary" style="padding:4px 10px;font-size:12px;" onclick="return confirm('آیا مطمئن هستید؟ تمام داده‌های فعلی با بکاپ جایگزین می‌شوند.');"><i class="fa-solid fa-rotate-left"></i> بازگردانی</a>
                                <a href="<?= BASE_URL ?>mod/backup/delete?file=<?= urlencode($b['name']) ?>" class="bp-btn bp-btn-danger" style="padding:4px 10px;font-size:12px;" onclick="return confirm('حذف شود؟');"><i class="fa-solid fa-trash"></i> حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}
