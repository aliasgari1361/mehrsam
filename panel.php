<?php
/**
 * CP - Admin Management Script
 * 
 * Usage:
 *   php panel.php create <username> <password> [email]
 *   php panel.php pass <username> <new-password>
 *   php panel.php list
 */

// ============================================================
// Database Config (use same as project)
// ============================================================
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'mehrsamdb';

// ============================================================
// Script
// ============================================================
$cmd = $argv[1] ?? 'help';

// Connect
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $conn->connect_error . PHP_EOL);
}

switch ($cmd) {
    case 'create':
        $username = $argv[2] ?? '';
        $password = $argv[3] ?? '';
        $email    = $argv[4] ?? '';

        if (!$username || !$password) {
            die("Usage: php panel.php create <username> <password> [email]" . PHP_EOL);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param("sss", $username, $hash, $email);

        if ($stmt->execute()) {
            echo "✅ کاربر ادمین '{$username}' با موفقیت ساخته شد." . PHP_EOL;
        } else {
            echo "❌ خطا: " . $stmt->error . PHP_EOL;
        }
        $stmt->close();
        break;

    case 'pass':
        $username = $argv[2] ?? '';
        $newpass  = $argv[3] ?? '';

        if (!$username || !$newpass) {
            die("Usage: php panel.php pass <username> <new-password>" . PHP_EOL);
        }

        $hash = password_hash($newpass, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "✅ رمز عبور کاربر '{$username}' با موفقیت تغییر کرد." . PHP_EOL;
        } else {
            echo "❌ کاربر '{$username}' یافت نشد." . PHP_EOL;
        }
        $stmt->close();
        break;

    case 'list':
        $result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id");
        if ($result && $result->num_rows > 0) {
            echo str_pad("ID", 5) . str_pad("Username", 25) . str_pad("Email", 30) . str_pad("Role", 10) . "Created" . PHP_EOL;
            echo str_repeat("-", 95) . PHP_EOL;
            while ($row = $result->fetch_assoc()) {
                echo str_pad($row['id'], 5) . str_pad($row['username'], 25) . str_pad($row['email'] ?? '-', 30) . str_pad($row['role'], 10) . ($row['created_at'] ?? '') . PHP_EOL;
            }
        } else {
            echo "هیچ کاربری یافت نشد." . PHP_EOL;
        }
        break;

    case 'help':
    default:
        echo "Admin Management Script (CP)" . PHP_EOL;
        echo str_repeat("-", 50) . PHP_EOL;
        echo "  php panel.php create <user> <pass> [email]   ساخت ادمین جدید" . PHP_EOL;
        echo "  php panel.php pass  <user> <new-pass>        تغییر رمز ادمین" . PHP_EOL;
        echo "  php panel.php list                           لیست کاربران" . PHP_EOL;
        echo PHP_EOL;
        break;
}

$conn->close();
