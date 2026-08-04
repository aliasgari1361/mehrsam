<?php
/**
 * آپدیت و نگهداری سایت
 * =====================
 * ۱) آپدیت فایل‌ها از ZIP (ادغامی — بدون حذف فایل‌ها و داده‌های موجود)
 * ۲) اجرای SQL مهاجرت (آپدیت بانک — بدون پاک کردن اطلاعات قبلی)
 * ۳) بکاپ SQL ساده
 */

function admin_update_route($action = '') {
    require_once __DIR__ . '/backup-functions.php';
    $message = '';
    $error = '';

    // ---------- آپدیت ZIP (ادغامی) ----------
    if ($action === 'zip_upload' && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        if (empty($_FILES['update_zip']['tmp_name']) || $_FILES['update_zip']['error'] !== UPLOAD_ERR_OK) {
            $error = "فایل ZIP انتخاب نشده یا در آپلود خطایی رخ داده است.";
        } else {
            $res = update_apply_zip($_FILES['update_zip']['tmp_name'], $_POST['confirm_backup'] ?? '');
            if (isset($res['error'])) {
                $error = $res['error'];
            } else {
                $message = "آپدیت فایل‌ها با موفقیت اعمال شد. " . $res['applied'] . " فایل به‌روزرسانی شد"
                    . ($res['skipped'] > 0 ? "، {$res['skipped']} مورد محافظت‌شده دست‌نخورده ماند." : ".")
                    . (($res['backup'] ?? '') ? " — بکاپ خودکار: " . $res['backup'] : "");
            }
        }
    }

    // ---------- اجرای SQL مهاجرت ----------
    if ($action === 'sql_upload' && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        if (empty($_FILES['migration_sql']['tmp_name']) || $_FILES['migration_sql']['error'] !== UPLOAD_ERR_OK) {
            $error = "فایل SQL انتخاب نشده یا در آپلود خطایی رخ داده است.";
        } else {
            $res = update_run_sql(file_get_contents($_FILES['migration_sql']['tmp_name']), ($_POST['allow_dangerous'] ?? '') === '1');
            if (isset($res['error'])) {
                $error = $res['error'];
            } else {
                $message = "SQL مهاجرت اجرا شد. " . $res['run'] . " دستور اجرا شد"
                    . ($res['skipped'] > 0 ? "، {$res['skipped']} دستور خطرناک/تکراری نادیده گرفته شد." : ".")
                    . (($res['backup'] ?? '') ? " — بکاپ خودکار دیتابیس: " . $res['backup'] : "");
            }
        }
    }

    // ---------- بکاپ SQL ----------
    if ($action === 'sql_download') {
        require_once MASIR_RISH . 'dade/bank.php';
        $sql = backup_db_export();
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="database_' . date('Y-m-d_H-i-s') . '.sql"');
        header('Content-Length: ' . strlen($sql));
        echo $sql;
        exit;
    }

    include MASIR_RISH . 'ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .up-wrap { max-width:860px; }
        .up-wrap h3 { margin-top:0; }
        .up-card { border:1px solid #e0e3e8; border-radius:12px; padding:22px; margin-bottom:20px; background:#fafbfc; }
        .up-card h4 { margin:0 0 6px; display:flex; align-items:center; gap:10px; }
        .up-card .desc { font-size:13px; color:#666; margin-bottom:16px; }
        .up-card label { display:block; font-size:13px; font-weight:700; margin-bottom:8px; }
        .up-card input[type=file] { display:block; margin-bottom:10px; }
        .up-card .check { display:flex; align-items:center; gap:8px; font-weight:500; margin-bottom:12px; cursor:pointer; }
        .up-card .check input { width:18px; height:18px; accent-color:var(--rang-asli,#FF6F00); }
        .up-btn { display:inline-block; padding:10px 22px; border:none; border-radius:8px; font-family:inherit; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; }
        .up-btn-primary { background:var(--rang-asli,#FF6F00); color:#fff; }
        .up-btn-primary:hover { background:#e65100; }
        .up-btn-secondary { background:#6c757d; color:#fff; }
        .up-btn-success { background:#198754; color:#fff; }
        .up-btn-danger { background:#dc3545; color:#fff; }
        .up-alert { padding:14px 18px; border-radius:10px; margin-bottom:16px; font-weight:600; }
        .up-alert-error { background:#fdecea; color:#b02a37; border:1px solid #f5c6cb; }
        .up-alert-success { background:#d1e7dd; color:#0f5132; border:1px solid #badbcc; }
        .up-pro { background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:10px; padding:14px 18px; font-size:13px; line-height:1.9; margin-bottom:20px; }
        .up-pro ul { margin:6px 0 0; padding-right:18px; }
    </style>

    <div class="up-wrap">
        <h3><i class="fa-solid fa-arrows-rotate"></i> آپدیت و نگهداری سایت</h3>

        <?php if ($error): ?><div class="up-alert up-alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($message): ?><div class="up-alert up-alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

        <div class="up-pro">
            <strong>نحوه صحیح آپدیت (تا اطلاعات قبلی پاک نشود):</strong>
            <ul>
                <li>فایل ZIP را آپلود کنید؛ فقط فایل‌هایی که داخل ZIP هستند <u>روی نسخه موجود نوشته می‌شوند</u> (overwrite).</li>
                <li>فایل‌ها و پوشه‌های محافظت‌شده (آپلودها، بکاپ‌ها، تنظیمات، کانفیگ دیتابیس) <u>هیچ‌وقت بازنویسی نمی‌شوند</u>.</li>
                <li>هیچ فایلی حذف نمی‌شود؛ فایل‌های شما که در ZIP نباشند دست‌نخورده می‌مانند.</li>
                <li>برای آپدیت بانک، فایل SQL مهاجرت را بدهید؛ فقط جدول/ستون/داده جدید اضافه می‌شود.</li>
            </ul>
        </div>

        <!-- آپدیت ZIP -->
        <div class="up-card">
            <h4><i class="fa-solid fa-file-zipper"></i> آپدیت فایل‌ها از ZIP (ادغامی)</h4>
            <div class="desc">آخرین نسخه کدها را به‌صورت ZIP آپلود کنید. محتویات روی فایل‌های موجود نوشته می‌شود بدون اینکه چیزی حذف شود.</div>
            <form method="post" enctype="multipart/form-data" action="<?= BASE_URL ?>mod/update/zip_upload">
                <label>فایل ZIP پروژه:</label>
                <input type="file" name="update_zip" accept=".zip,application/zip" required>
                <label class="check"><input type="checkbox" name="confirm_backup" value="1" checked> قبل از اعمال آپدیت، بکاپ خودکار (دیتابیس + فایل‌ها) گرفته شود</label>
                <button type="submit" class="up-btn up-btn-primary"><i class="fa-solid fa-upload"></i> اعمال آپدیت ZIP</button>
            </form>
        </div>

        <!-- SQL مهاجرت -->
        <div class="up-card">
            <h4><i class="fa-solid fa-database"></i> آپدیت بانک اطلاعاتی (SQL مهاجرت)</h4>
            <div class="desc">فایل SQL حاوی تغییرات جدید بانک (جدول/ستون/داده جدید) را آپلود کنید. دستورات تخریبی مانند <code>DROP</code> و <code>TRUNCATE</code> به‌صورت پیش‌فرض اجرا نمی‌شوند.</div>
            <form method="post" enctype="multipart/form-data" action="<?= BASE_URL ?>mod/update/sql_upload">
                <label>فایل SQL مهاجرت:</label>
                <input type="file" name="migration_sql" accept=".sql,text/plain" required>
                <label class="check"><input type="checkbox" name="allow_dangerous" value="1"> اجازه اجرای دستورات تخریبی (DROP / DELETE / TRUNCATE / REPLACE) — فقط در صورت اطمینان</label>
                <button type="submit" class="up-btn up-btn-success"><i class="fa-solid fa-play"></i> اجرای SQL مهاجرت</button>
            </form>
        </div>

        <!-- بکاپ SQL -->
        <div class="up-card">
            <h4><i class="fa-solid fa-file-arrow-down"></i> بکاپ ساده دیتابیس (SQL)</h4>
            <div class="desc">دانلود نسخه‌ای سبک و کامل از بانک اطلاعاتی به‌صورت فایل SQL — بدون فایل‌ها.</div>
            <a href="<?= BASE_URL ?>mod/update/sql_download" class="up-btn up-btn-secondary"><i class="fa-solid fa-download"></i> دانلود بکاپ SQL</a>
            <span style="margin-right:12px;font-size:13px;color:#888;">بکاپ کامل (SQL + فایل‌ها) در بخش «بکاپ و بازگردانی» موجود است.</span>
        </div>

        <!-- وضعیت گیت -->
        <div class="up-card">
            <h4><i class="fa-brands fa-github"></i> به‌روزرسانی از گیت (اختیاری)</h4>
            <div class="desc">اگر پروژه روی هاست به مخزن Git متصل است، از این روش استفاده کنید.</div>
            <a href="<?= BASE_URL ?>mod/settings?tab=git" class="up-btn up-btn-primary"><i class="fa-solid fa-cloud-arrow-down"></i> رفتن به بخش گیت</a>
        </div>
    </div>
    <?php
}

/**
 * اعمال آپدیت ZIP به‌صورت ادغامی
 * - فقط فایل‌های داخل ZIP نوشته می‌شوند (overwrite)
 * - هیچ فایلی حذف نمی‌شود
 * - فایل‌های محافظت‌شده (تنظیمات، آپلودها، بکاپ‌ها، کانفیگ) بازنویسی نمی‌شوند
 */
function update_apply_zip($zip_path, $confirm_backup = '') {
    require_once __DIR__ . '/backup-functions.php';

    // ۱) بکاپ خودکار
    $backup_name = '';
    if (!empty($confirm_backup)) {
        $res = backup_create_zip();
        if (!isset($res['error'])) {
            $backup_name = $res['name'];
        }
    }

    // ۲) باز کردن ZIP
    $zip = new ZipArchive();
    if ($zip->open($zip_path) !== true) {
        return ['error' => 'نمی‌توان فایل ZIP را باز کرد.'];
    }

    $applied = 0;
    $skipped = 0;
    $errors = [];

    $protected_prefixes = update_protected_prefixes();

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = $zip->getNameIndex($i);

        // رد کردن پوشه‌ها و مسیرهای خاص
        if (substr($entry, -1) === '/') continue;

        // جلوگیری از path traversal
        $clean = str_replace('\\', '/', $entry);
        if (strpos($clean, '../') !== false || strpos($clean, '..\\') !== false) {
            $skipped++;
            continue;
        }
        $clean = ltrim($clean, '/');
        if ($clean === '') continue;

        // فایل‌های محافظت‌شده
        if (update_is_protected($clean, $protected_prefixes)) {
            $skipped++;
            continue;
        }

        // استخراج امن
        $target = MASIR_RISH . str_replace('/', DIRECTORY_SEPARATOR, $clean);
        $root_real = realpath(MASIR_RISH);
        if ($root_real === false) {
            $skipped++;
            continue;
        }
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        $target_real = realpath(dirname($target));
        if ($target_real === false || strpos($target_real . DIRECTORY_SEPARATOR, $root_real . DIRECTORY_SEPARATOR) !== 0) {
            $skipped++;
            continue;
        }

        $content = $zip->getFromIndex($i);
        if ($content === false) { $skipped++; continue; }
        if (file_put_contents($target, $content) === false) {
            $errors[] = $clean;
            $skipped++;
            continue;
        }
        $applied++;
    }
    $zip->close();

    if ($applied === 0 && !empty($errors)) {
        return ['error' => 'هیچ فایلی قابل نوشتن نبود. بررسی کنید مجوزهای نوشتن روی هاست فعال باشد. مورد خطادار: ' . implode('، ', array_slice($errors, 0, 3))];
    }

    return [
        'success' => true,
        'applied' => $applied,
        'skipped' => $skipped,
        'backup'  => $backup_name,
        'errors'  => $errors,
    ];
}

/**
 * اجرای امن SQL مهاجرت
 * - پیش‌فرض: فقط دستورات سازنده (CREATE/ALTER/INSERT/UPDATE) اجرا می‌شوند
 * - با allow_dangerous: DROP/DELETE/TRUNCATE/REPLACE هم اجرا می‌شوند
 */
function update_run_sql($sql, $allow_dangerous = false) {
    require_once __DIR__ . '/backup-functions.php';
    require_once MASIR_RISH . 'dade/bank.php';

    $bank = new Bank();
    $conn = $bank->getConnection();

    // ۱) بکاپ خودکار
    $backup_name = '';
    $res = backup_create_zip();
    if (!isset($res['error'])) {
        $backup_name = $res['name'];
    }

    // ۲) تفکیک دستورات
    $statements = explode(";\n", $sql);
    $run = 0;
    $skipped = 0;
    $errors = [];

    foreach ($statements as $stmt_raw) {
        $stmt = trim($stmt_raw);
        if ($stmt === '') continue;

        // حذف کامنت‌های خطی و بلوکی ساده
        $clean_stmt = preg_replace('/--.*$/m', '', $stmt);
        $clean_stmt = preg_replace('#/\*.*?\*/#s', '', $clean_stmt);
        $clean_stmt = trim($clean_stmt);
        if ($clean_stmt === '') continue;

        // تشخیص دستورات تخریبی
        if (!$allow_dangerous && update_is_dangerous($clean_stmt)) {
            $skipped++;
            continue;
        }

        if (!$conn->query($clean_stmt)) {
            $errors[] = $conn->error;
            $skipped++;
            continue;
        }
        $run++;
    }
    $conn->close();

    if ($run === 0 && !empty($errors) && $skipped === count($errors)) {
        return ['error' => 'هیچ دستوری اجرا نشد. خطا: ' . implode(' | ', array_slice($errors, 0, 3))];
    }

    return [
        'success' => true,
        'run'     => $run,
        'skipped' => $skipped,
        'backup'  => $backup_name,
        'errors'  => $errors,
    ];
}

/**
 * پیشوندهای محافظت‌شده (این فایل‌ها/پوشه‌ها هرگز از ZIP بازنویسی نمی‌شوند)
 */
function update_protected_prefixes() {
    return [
        'haste/tanzimat.php',            // کانفیگ دیتابیس هاست
        'haste/site_settings.json',      // تنظیمات سایت
        'haste/modir_tanzimat.json',     // تنظیمات پنل
        'haste/dade/bank.php',           // کانفیگ اتصال به بانک
        'backups/',
        'poshtyban/',
        'ghaleb/manabe/uploads/',        // آپلودهای کاربران
        'nasb.php',                      // نصاب (باید دستی آپدیت شود)
    ];
}

function update_is_protected($clean_path, $prefixes) {
    foreach ($prefixes as $p) {
        if (strpos($clean_path, $p) === 0) {
            return true;
        }
    }
    return false;
}

function update_is_dangerous($stmt) {
    $dangerous = [
        '/^\s*DROP\b/i',
        '/^\s*TRUNCATE\b/i',
        '/^\s*DELETE\b/i',
        '/^\s*REPLACE\b/i',
        '/^\s*ALTER\s+TABLE.*\bDROP\b/i',
        '/^\s*RENAME\s+TABLE/i',
    ];
    foreach ($dangerous as $pattern) {
        if (preg_match($pattern, $stmt)) {
            return true;
        }
    }
    return false;
}
