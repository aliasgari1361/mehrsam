<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/sefaresh-model.php';
require_once __DIR__ . '/sabad-model.php';
require_once MASIR_RISH . 'afzuneh/pardakht-darbe/zarinpal.php';

function karbar_get($karbar_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $karbar_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user ?: [];
}

function forushgah_checkout($amaliat, $paramha) {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = BASE_URL . '/forushgah/checkout';
        redirect('karbar/login');
        return;
    }

    if ($amaliat === 'zarinpal') {
        $sub = $paramha[0] ?? '';
        if ($sub === 'callback') {
            checkout_zarinpal_callback();
        } elseif ($sub === 'verify') {
            checkout_zarinpal_verify();
        }
        return;
    }

    // result/success/ID or result/failed
    $sub = $paramha[0] ?? '';
    if ($sub === 'result') {
        $status = $paramha[1] ?? '';
        if ($status === 'success') {
            $sefaresh_id = $paramha[2] ?? 0;
            $onvan_safhe = 'پرداخت موفق | ' . SITE_NAME;
            $meta_sharh = 'پرداخت شما با موفقیت انجام شد';
            $safhe_faali = 'forushgah';
            include MASIR_GHALEB . 'forushgah/success.php';
        } else {
            $onvan_safhe = 'پرداخت ناموفق | ' . SITE_NAME;
            $meta_sharh = 'پرداخت شما با خطا مواجه شد';
            $safhe_faali = 'forushgah';
            include MASIR_GHALEB . 'forushgah/failed.php';
        }
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkout_process();
    } else {
        checkout_show();
    }
}

function checkout_show() {
    $sabad_id = sabad_get_or_create();
    $items = sabad_get_items($sabad_id);
    
    if (empty($items)) {
        redirect('forushgah/sabad');
        return;
    }

    $total = sabad_total();
    $user_info = karbar_get($_SESSION['user_id']);
    $onvan_safhe = 'تسویه حساب | ' . SITE_NAME;
    $meta_sharh = 'تکمیل خرید در مهراد سام';
    $safhe_faali = 'forushgah';
    include MASIR_GHALEB . 'forushgah/checkout.php';
}

function checkout_process() {
    $onvan_girande = trim($_POST['onvan_girande'] ?? '');
    $telefon_girande = trim($_POST['telefon_girande'] ?? '');
    $ostan = trim($_POST['ostan'] ?? '');
    $shahr = trim($_POST['shahr'] ?? '');
    $adres = trim($_POST['adres'] ?? '');
    $kode_posty = trim($_POST['kode_posty'] ?? '');
    $post_type = $_POST['post_type'] ?? 'pishaz';
    $tozih = trim($_POST['tozih'] ?? '');

    $errors = [];
    if (!$onvan_girande) $errors[] = 'نام گیرنده الزامی است';
    if (!$telefon_girande) $errors[] = 'تلفن گیرنده الزامی است';
    if (!$ostan) $errors[] = 'استان الزامی است';
    if (!$shahr) $errors[] = 'شهر الزامی است';
    if (!$adres) $errors[] = 'آدرس الزامی است';

    if ($errors) {
        $_SESSION['checkout_errors'] = $errors;
        $_SESSION['checkout_old'] = $_POST;
        redirect('forushgah/checkout');
        return;
    }

    $karbar_id = $_SESSION['user_id'];
    $sabad_id = sabad_get_or_create();
    $items = sabad_get_items($sabad_id);
    $total = sabad_total();

    $post_hazine = $post_type === 'pishaz' ? 45000 : 25000;
    $majmoo = $total + $post_hazine;

    $sefaresh_id = sefaresh_create([
        'karbar_id' => $karbar_id,
        'onvan_girande' => $onvan_girande,
        'telefon_girande' => $telefon_girande,
        'ostan' => $ostan,
        'shahr' => $shahr,
        'adres' => $adres,
        'kode_posty' => $kode_posty,
        'post_type' => $post_type,
        'post_hazine' => $post_hazine,
        'tozih' => $tozih,
        'majmoo_gheymat' => $majmoo,
    ]);

    if ($sefaresh_id) {
        require_once MASIR_RISH . 'afzuneh/notification/Notifier.php';
        Notifier::newOrder($sefaresh_id, $majmoo, $onvan_girande);
    } else {
        $_SESSION['checkout_errors'] = ['خطا در ثبت سفارش'];
        redirect('forushgah/checkout');
        return;
    }

    sefaresh_add_mahsulat($sefaresh_id, $items);
    sabad_clear();

    $zarinpal = new ZarinPal();
    $result = $zarinpal->request($majmoo, $sefaresh_id);

    if ($result['success']) {
        sefaresh_update_pardakht_ref($sefaresh_id, $result['authority']);
        header('Location: ' . $result['redirect_url']);
        exit;
    } else {
        $_SESSION['checkout_errors'] = [$result['message']];
        redirect('forushgah/checkout');
    }
}

function checkout_zarinpal_callback() {
    $authority = $_GET['Authority'] ?? '';
    $status = $_GET['Status'] ?? '';

    if ($status !== 'OK' || !$authority) {
        $sefaresh_id = sefaresh_get_by_authority($authority);
        if ($sefaresh_id) {
            sefaresh_update_vaziat($sefaresh_id, 'cancelled', 'failed');
        }
        $_SESSION['payment_error'] = 'پرداخت لغو یا ناموفق بود';
        redirect('forushgah/checkout/result/failed');
        return;
    }

    $sefaresh_id = sefaresh_get_by_authority($authority);
    if (!$sefaresh_id) {
        $_SESSION['payment_error'] = 'سفارش یافت نشد';
        redirect('forushgah/checkout/result/failed');
        return;
    }

    $sefaresh = sefaresh_get($sefaresh_id);
    $zarinpal = new ZarinPal();
    $result = $zarinpal->verify($sefaresh['majmoo_gheymat'], $authority);

    if ($result['success']) {
        sefaresh_update_vaziat($sefaresh_id, 'processing', 'paid', $result['ref_id']);
        $_SESSION['payment_success'] = true;
        $_SESSION['payment_ref_id'] = $result['ref_id'];
        redirect('forushgah/checkout/result/success/' . $sefaresh_id);
    } else {
        sefaresh_update_vaziat($sefaresh_id, 'cancelled', 'failed');
        $_SESSION['payment_error'] = $result['message'];
        redirect('forushgah/checkout/result/failed');
    }
}

function checkout_zarinpal_verify() {
    // برای AJAX verify اگر لازم باشد
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}