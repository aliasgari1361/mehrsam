<?php
/**
 * کنترلر تماس با ما
 */

require_once MASIR_DADE . 'bank.php';
require_once MASIR_RISH . 'haste/tanzimat.php';

function tamas_route($amaliat, $paramha) {
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        tamas_zakhire();
    } else {
        tamas_namayesh();
    }
}

// ---- نمایش فرم ----
function tamas_namayesh($payam = null, $khata = null) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $page_data = $conn->query("SELECT * FROM posts WHERE template='contact' AND status='publish' LIMIT 1")->fetch_assoc();
    $conn->close();

    $onvan_safhe = $page_data['title'] ?? ('تماس با ما | ' . SITE_NAME);
    $meta_sharh  = strip_tags($page_data['content'] ?? ('با ' . SITE_NAME . ' تماس بگیرید'));
    $safhe_faali = 'tamas';
    include MASIR_GHALEB . 'tamas.php';
}

// ---- ذخیره فرم ----
function tamas_zakhire() {
    $nam    = trim($_POST['nam']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $mozoo  = trim($_POST['mozoo']  ?? '');
    $payam  = trim($_POST['payam']  ?? '');

    // اعتبارسنجی
    $khata = [];
    if (empty($nam))   $khata[] = 'نام الزامی است.';
    if (empty($payam)) $khata[] = 'متن پیام الزامی است.';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $khata[] = 'فرمت ایمیل صحیح نیست.';
    }

    if (!empty($khata)) {
        tamas_namayesh(null, $khata);
        return;
    }

    // ذخیره در دیتابیس
    try {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '';

        $stmt = $conn->prepare(
            "INSERT INTO payam_tamas (nam, email, telefon, mozoo, payam, ip_address)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssssss', $nam, $email, $telefon, $mozoo, $payam, $ip);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        require_once MASIR_RISH . 'afzuneh/elpayaagh/Notifier.php';
        Notifier::newContactMessage($nam, $telefon, $mozoo);

        $mofagh = 'پیام شما با موفقیت ارسال شد. به‌زودی با شما تماس می‌گیریم.';
        tamas_namayesh($mofagh, null);

    } catch (Exception $e) {
        if (isset($conn)) $conn->close();
        $khata = ['خطایی رخ داد. لطفاً دوباره تلاش کنید.'];
        tamas_namayesh(null, $khata);
    }
}
