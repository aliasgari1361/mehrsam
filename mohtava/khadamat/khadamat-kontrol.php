<?php
/**
 * کنترلر خدمات مهراد سام
 */

require_once MASIR_DADE . 'bank.php';

// ---- مسیریاب داخلی خدمات ----
function khadamat_route($amaliat, $paramha) {
    if (empty($amaliat)) {
        khadamat_fehrest();
    } else {
        khadamat_tan($amaliat);   // amaliat = slug خدمت
    }
}

// ---- صفحه اصلی سایت ----
function safhe_khane() {
    $bank     = new Bank();
    $conn     = $bank->getConnection();

    // ۶ خدمت اول برای نمایش در صفحه اصلی
    $natije   = $conn->query(
        "SELECT * FROM khadamat WHERE vaziat = 1 ORDER BY tartib ASC LIMIT 6"
    );
    $khadamat = $natije ? $natije->fetch_all(MYSQLI_ASSOC) : [];

    $onvan_safhe  = SITE_NAME . ' | ' . SITE_SLOGAN;
    $meta_sharh   = 'خدمات پشتیبانی کامپیوتر از راه دور و حضوری در ملارد، مارلیک. رفع کندی، نصب نرم‌افزار، طراحی سایت و دوربین مدار بسته.';
    $safhe_faali  = 'khane';

    include MASIR_GHALEB . 'khane.php';
}

// ---- لیست تمام خدمات ----
function khadamat_fehrest() {
    $bank     = new Bank();
    $conn     = $bank->getConnection();

    $natije   = $conn->query(
        "SELECT * FROM khadamat WHERE vaziat = 1 ORDER BY tartib ASC"
    );
    $khadamat = $natije ? $natije->fetch_all(MYSQLI_ASSOC) : [];

    $onvan_safhe  = 'خدمات | ' . SITE_NAME;
    $meta_sharh   = 'مشاهده تمام خدمات پشتیبانی کامپیوتری مهراد سام در ملارد و مارلیک';
    $safhe_faali  = 'khadamat';

    include MASIR_GHALEB . 'khadamat.php';
}

// ---- جزئیات یک خدمت ----
function khadamat_tan($slug) {
    $bank     = new Bank();
    $conn     = $bank->getConnection();

    $stmt = $conn->prepare(
        "SELECT * FROM khadamat WHERE slug = ? AND vaziat = 1 LIMIT 1"
    );
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $khadamat_tan = $stmt->get_result()->fetch_assoc();

    if (!$khadamat_tan) {
        http_response_code(404);
        include MASIR_GHALEB . '404.php';
        return;
    }

    $onvan_safhe  = $khadamat_tan['onvan'] . ' | ' . SITE_NAME;
    $meta_sharh   = $khadamat_tan['meta_sharh'] ?: $khadamat_tan['sharh_kootah'];
    $safhe_faali  = 'khadamat';

    // خدمات مشابه
    $stmt_ms = $conn->prepare(
        "SELECT id, onvan, slug, icon, sharh_kootah FROM khadamat
         WHERE vaziat = 1 AND id != ?
         ORDER BY tartib ASC LIMIT 4"
    );
    $stmt_ms->bind_param("i", $khadamat_tan['id']);
    $stmt_ms->execute();
    $natije_moshabe = $stmt_ms->get_result();
    $khadamat_moshabe = $natije_moshabe ? $natije_moshabe->fetch_all(MYSQLI_ASSOC) : [];
    $stmt_ms->close();

    include MASIR_GHALEB . 'khadamat-tan.php';
}
