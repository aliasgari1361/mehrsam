<?php
/**
 * توابع عمومی
 */

function redirect($url) {
    $url = ltrim($url, '/');
    header("Location: " . BASE_URL . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * ارسال ایمیل هشدار ورود به پنل مدیریت
 * در هر ورود موفق به مدیر (SITE_EMAIL) ارسال می‌شود
 */
function send_login_alert($username, $user_id = null) {
    $to = defined('SITE_EMAIL') && SITE_EMAIL !== '' ? SITE_EMAIL : 'ali.asgari.6106@gmail.com';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'نامشخص', 0, 300);
    $now = date('Y-m-d H:i:s');

    $subject = "هشدار ورود به پنل مدیریت — " . (defined('SITE_NAME') ? SITE_NAME : 'مهراد سام');

    $message = "سلام مدیر,\n\n";
    $message .= "یک ورود موفق به پنل مدیریت ثبت شد:\n";
    $message .= "------------------------------------\n";
    $message .= "نام کاربری: " . $username . "\n";
    $message .= "شناسه کاربر: " . ($user_id ?? '—') . "\n";
    $message .= "زمان: " . $now . "\n";
    $message .= "آی‌پی: " . $ip . "\n";
    $message .= "مرورگر: " . $ua . "\n";
    $message .= "------------------------------------\n\n";
    $message .= "اگر خودتان وارد نشده‌اید، فوراً رمز عبور را تغییر دهید.\n";

    $headers = "From: " . (defined('SITE_NAME') ? SITE_NAME : 'مهراد سام') . " <noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($to, $subject, $message, $headers);
}
?>