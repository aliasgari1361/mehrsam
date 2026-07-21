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

    // استفاده از صفحه‌ساز اگر تم home فعال باشد
    $GLOBALS['khane_builder_html'] = '';
    if (file_exists(MASIR_RISH . 'mohtava/sakhtar/builder.php')) {
        require_once MASIR_RISH . 'mohtava/sakhtar/builder.php';
        $GLOBALS['khane_builder_html'] = builder_render_for('home');
    }

    include MASIR_GHALEB . 'khane.php';
}

// ---- لیست تمام خدمات (صفحه خدمات) ----
function khadamat_fehrest() {
    $bank     = new Bank();
    $conn     = $bank->getConnection();

    $result = $conn->query("SELECT id, title, slug, kholaseh, tasvir, subtitle, display_order FROM khadamat WHERE vaziat=1 ORDER BY display_order ASC");
    $khadamat_list = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();

    $page_data = ['title' => 'خدمات | ' . SITE_NAME, 'content' => ''];
    $onvan_safhe  = 'خدمات | ' . SITE_NAME;
    $meta_sharh   = 'خدمات پشتیبانی کامپیوتر و شبکه مهراد سام';
    $safhe_faali  = 'khadamat';

    include MASIR_GHALEB . 'khadamat.php';
}

// ---- جزئیات یک خدمت ----
function khadamat_tan($slug) {
    require_once MASIR_RISH . 'dade/bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT * FROM khadamat WHERE slug = ? AND vaziat = 1 LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$service) {
        $onvan_safhe  = 'خدمت یافت نشد | ' . SITE_NAME;
        $meta_sharh   = 'این خدمت در لیست خدمات موجود نیست.';
        $safhe_faali  = 'khadamat';
    } else {
        $onvan_safhe  = $service['title'] . ' | ' . SITE_NAME;
        $meta_sharh   = strip_tags($service['kholaseh'] ?? 'خدمات پشتیبانی کامپیوتر');
        $safhe_faali  = 'khadamat';
        // متغیر $service در khadamat-tan.php استفاده می‌شود
        $GLOBALS['khadamat_service'] = $service;

        $builder_content = '';
        if (file_exists(MASIR_RISH . 'mohtava/sakhtar/builder.php')) {
            require_once MASIR_RISH . 'mohtava/sakhtar/builder.php';
            $bp_info = builder_get_page_id('khadamat', $slug);
            if ($bp_info && $bp_info['bp_id']) {
                $builder_content = builder_render_page($bp_info['bp_id']);
            }
        }
        $GLOBALS['khadamat_builder_content'] = $builder_content;
    }

    include MASIR_GHALEB . 'khadamat-tan.php';
}