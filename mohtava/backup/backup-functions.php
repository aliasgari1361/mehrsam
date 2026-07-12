<?php

define('BACKUP_DIR', MASIR_RISH . 'backups');

function backup_init() {
    if (!is_dir(BACKUP_DIR)) {
        mkdir(BACKUP_DIR, 0755, true);
        file_put_contents(BACKUP_DIR . '/.htaccess', "Require all denied\n");
    }
}

function backup_db_export() {
    require_once MASIR_RISH . 'dade/bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();

    $sql = "SET NAMES utf8mb4;\n\n";

    $tables = $conn->query("SHOW TABLES");
    while ($row = $tables->fetch_row()) {
        $table = $row[0];

        $create = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
        $sql .= "\nDROP TABLE IF EXISTS `$table`;\n";
        $sql .= $create[1] . ";\n\n";

        $rows = $conn->query("SELECT * FROM `$table`");
        while ($row_data = $rows->fetch_assoc()) {
            $cols = array_map(function($c) { return "`$c`"; }, array_keys($row_data));
            $vals = array_map(function($v) use ($conn) {
                return $v === null ? 'NULL' : "'" . $conn->real_escape_string($v) . "'";
            }, array_values($row_data));
            $sql .= "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n";
        }
        $sql .= "\n";
    }
    $conn->close();
    return $sql;
}

function backup_db_import($sql) {
    require_once MASIR_RISH . 'dade/bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();

    $statements = explode(";\n", $sql);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt !== '') {
            $conn->query($stmt);
        }
    }
    $conn->close();
}

function backup_create_zip($name = null) {
    backup_init();
    $name = $name ?? 'backup_' . date('Y-m-d_H-i-s');
    $zip_path = BACKUP_DIR . '/' . $name . '.zip';

    $db_sql = backup_db_export();

    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE) !== true) {
        return ['error' => 'نمی‌توان فایل زیپ را ایجاد کرد.'];
    }

    $zip->addFromString('database.sql', $db_sql);

    $backup_dirs = ['haste', 'ghaleb', 'mohtava', 'dade', 'database'];
    foreach ($backup_dirs as $dir) {
        $path = MASIR_RISH . $dir;
        if (!is_dir($path)) continue;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                $rel_path = $dir . '/' . $files->getSubPathname();
                $zip->addFile($file->getRealPath(), $rel_path);
            }
        }
    }

    $zip->close();
    return ['path' => $zip_path, 'name' => $name . '.zip'];
}

function backup_restore($filename) {
    backup_init();
    $zip_path = BACKUP_DIR . '/' . basename($filename);
    if (!file_exists($zip_path)) {
        return ['error' => 'فایل بکاپ یافت نشد.'];
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_path) !== true) {
        return ['error' => 'نمی‌توان فایل زیپ را باز کرد.'];
    }

    $sql_content = $zip->getFromName('database.sql');
    if ($sql_content === false) {
        $zip->close();
        return ['error' => 'فایل database.sql در بکاپ یافت نشد.'];
    }
    $zip->close();

    backup_db_import($sql_content);

    $zip = new ZipArchive();
    $zip->open($zip_path);
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = $zip->getNameIndex($i);
        if ($entry === 'database.sql') continue;
        $target = MASIR_RISH . $entry;
        $target_dir = dirname($target);
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        copy('zip://' . $zip_path . '#' . $entry, $target);
    }
    $zip->close();

    return ['success' => true];
}

function backup_list() {
    backup_init();
    $files = glob(BACKUP_DIR . '/*.zip');
    $list = [];
    foreach ($files as $f) {
        $list[] = [
            'name' => basename($f),
            'size' => filesize($f),
            'date' => date('Y-m-d H:i:s', filemtime($f)),
        ];
    }
    rsort($list);
    return $list;
}

function backup_delete($filename) {
    $path = BACKUP_DIR . '/' . basename($filename);
    if (file_exists($path)) unlink($path);
}

function backup_format_size($bytes) {
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}
