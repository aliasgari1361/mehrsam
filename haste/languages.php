<?php
/**
 * Multilingual system for LaRaGoRn framework
 */

// تنظیم زبان پیش‌فرض
if (!defined('DEFAULT_LANGUAGE')) {
    define('DEFAULT_LANGUAGE', 'fa');
}

// دریافت زبان کاربر
function get_language() {
    // اول از session
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    // سپس از دیتابیس (کاربر لاگین باشد)
    if (isset($_SESSION['user_id'])) {
        require_once MASIR_DADE . 'bank.php';
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("SELECT selected_language FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        if ($user && $user['selected_language']) {
            $_SESSION['language'] = $user['selected_language'];
            return $user['selected_language'];
        }
    }
    // در نهایت از کوکی
    if (isset($_COOKIE['language'])) {
        return $_COOKIE['language'];
    }
    return DEFAULT_LANGUAGE;
}

// تنظیم زبان
function set_language($code) {
    $_SESSION['language'] = $code;
    setcookie('language', $code, time() + 31536000, '/');
    if (isset($_SESSION['user_id'])) {
        require_once MASIR_DADE . 'bank.php';
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("UPDATE users SET selected_language = ? WHERE id = ?");
        $stmt->bind_param("si", $code, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

// دریافت ترجمه
function __t($key, $lang = null) {
    static $translations = [];
    
    if ($lang === null) {
        $lang = get_language();
    }
    
    $cache_key = $lang . ':' . $key;
    if (isset($translations[$cache_key])) {
        return $translations[$cache_key];
    }
    
    require_once MASIR_DADE . 'bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT value FROM translations WHERE key_name = ? AND language_code = ? LIMIT 1");
    $stmt->bind_param("ss", $key, $lang);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    if ($row) {
        $translations[$cache_key] = $row['value'];
        return $row['value'];
    }
    
    // اگر ترجمه نیافت، مقدار پیش‌فرض را برگردان
    return $key;
}