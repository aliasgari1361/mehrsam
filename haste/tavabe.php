<?php
/**
 * توابع عمومی
 */

function redirect($url) {
    // $url باید نسبت به BASE_URL باشد، مثلاً 'haste/mod/lomod'
    header("Location: " . BASE_URL . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>