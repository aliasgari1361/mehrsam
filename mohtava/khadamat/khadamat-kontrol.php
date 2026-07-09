<?php
/**
 * کنترلر خدمات مهراد سام
 * خدمات در صفحه واحد (template='services') مدیریت می‌شوند.
 * صفحه خانه (khane.php) محتوای ثابت دارد و از دیتابیس خدمات نمی‌خواند.
 */

require_once MASIR_DADE . 'bank.php';

// ---- مسیریاب داخلی خدمات ----
function khadamat_route($amaliat, $paramha) {
    if (empty($amaliat)) {
        khadamat_fehrest();
    } else {
        khadamat_tan($amaliat);   // amaliat = slug خدمت (اکنون وجود ندارد)
    }
}

// ---- صفحه اصلی سایت ----
// محتوای ثابت در khane.php وجود دارد؛ از دیتابیس خدمات نمی‌خواند
function safhe_khane() {
    $bank     = new Bank();
    $conn     = $bank->getConnection();

    $page_data = $conn->query("SELECT * FROM posts WHERE template='home' AND status='publish' LIMIT 1")->fetch_assoc();
    $conn->close();

    $onvan_safhe  = $page_data['title'] ?? (SITE_NAME . ' | ' . SITE_SLOGAN);
    $meta_sharh   = strip_tags($page_data['content'] ?? 'خدمات پشتیبانی کامپیوتر');
    $safhe_faali  = 'khane';

    include MASIR_GHALEB . 'khane.php';
}

// ---- لیست تمام خدمات (صفحه خدمات) ----
// فقط صفحه template='services' رندر می‌شود
function khadamat_fehrest() {
    $bank     = new Bank();
    $conn     = $bank->getConnection();

    $page_data = $conn->query("SELECT * FROM posts WHERE template='services' AND status='publish' LIMIT 1")->fetch_assoc();
    $conn->close();

    $onvan_safhe  = $page_data['title'] ?? ('خدمات | ' . SITE_NAME);
    $meta_sharh   = strip_tags($page_data['content'] ?? 'خدمات پشتیبانی کامپیوتر');
    $safhe_faali  = 'khadamat';

    include MASIR_GHALEB . 'khadamat.php';
}

// ---- جزئیات یک خدمت ----
// دیگر URL جداگانه ندارد؛ پیام دوستانه نمایش داده می‌شود
function khadamat_tan($slug) {
    $onvan_safhe  = 'خدمت یافت نشد | ' . SITE_NAME;
    $meta_sharh   = 'این خدمت در حال حاضر در دسترس نیست.';
    $safhe_faali  = 'khadamat';

    include MASIR_GHALEB . 'khadamat-tan.php';
}