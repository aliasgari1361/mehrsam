<?php

require_once MASIR_DADE . 'bank.php';

function sabad_get_or_create() {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $session_id = session_id();
    if (empty($session_id)) {
        session_start();
        $session_id = session_id();
    }

    $karbar_id = $_SESSION['user_id'] ?? null;

    $sabad = null;
    if ($karbar_id) {
        $stmt = $conn->prepare("SELECT id FROM sabad WHERE karbar_id = ? LIMIT 1");
        $stmt->bind_param("i", $karbar_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sabad = $result->fetch_assoc();
        $stmt->close();
    }

    if (!$sabad) {
        $stmt = $conn->prepare("SELECT id FROM sabad WHERE session_id = ? LIMIT 1");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sabad = $result->fetch_assoc();
        $stmt->close();
    }

    if (!$sabad) {
        if ($karbar_id) {
            $stmt = $conn->prepare("INSERT INTO sabad (karbar_id, session_id) VALUES (?, ?)");
            $stmt->bind_param("is", $karbar_id, $session_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO sabad (session_id) VALUES (?)");
            $stmt->bind_param("s", $session_id);
        }
        $stmt->execute();
        $sabad_id = $conn->insert_id;
        $stmt->close();
        $sabad = ['id' => $sabad_id];
    }

    $conn->close();
    return $sabad['id'];
}

function sabad_add($mahsul_id, $tedad = 1) {
    $sabad_id = sabad_get_or_create();

    $bank = new Bank();
    $conn = $bank->getConnection();

    // قیمت لحظه‌ای رو از محصول بگیر
    $stmt = $conn->prepare("SELECT gheymat, gheymat_takhfif, mojood, vaziat FROM mahsulat WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $mahsul_id);
    $stmt->execute();
    $mahsul = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$mahsul || !$mahsul['vaziat'] || $mahsul['mojood'] < $tedad) {
        $conn->close();
        return ['success' => false, 'message' => 'محصول موجود نیست'];
    }

    $gheymat = $mahsul['gheymat_takhfif'] ?: $mahsul['gheymat'];

    // اگر قبلاً در سبد هست، تعداد رو آپدیت کن
    $stmt = $conn->prepare("SELECT id, tedad FROM sabad_mahsul WHERE sabad_id = ? AND mahsul_id = ?");
    $stmt->bind_param("ii", $sabad_id, $mahsul_id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists) {
        $new_tedad = $exists['tedad'] + $tedad;
        if ($mahsul['mojood'] < $new_tedad) {
            $conn->close();
            return ['success' => false, 'message' => 'موجودی کافی نیست'];
        }
        $stmt = $conn->prepare("UPDATE sabad_mahsul SET tedad = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_tedad, $exists['id']);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO sabad_mahsul (sabad_id, mahsul_id, tedad, gheymat_vahed) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $sabad_id, $mahsul_id, $tedad, $gheymat);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    return ['success' => true];
}

function sabad_get_items($sabad_id = null) {
    if (!$sabad_id) $sabad_id = sabad_get_or_create();

    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("
        SELECT sm.id, sm.tedad, sm.gheymat_vahed,
               m.id as mahsul_id, m.onvan, m.slug, m.tasvir, m.gheymat, m.gheymat_takhfif
        FROM sabad_mahsul sm
        JOIN mahsulat m ON sm.mahsul_id = m.id
        WHERE sm.sabad_id = ?
    ");
    $stmt->bind_param("i", $sabad_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();

    foreach ($items as &$item) {
        $item['gheymat_akhar'] = $item['gheymat_takhfif'] ?: $item['gheymat'];
        $item['majmoo'] = $item['gheymat_akhar'] * $item['tedad'];
    }
    return $items;
}

function sabad_update_tedad($item_id, $tedad) {
    if ($tedad < 1) return sabad_remove($item_id);

    $bank = new Bank();
    $conn = $bank->getConnection();

    // چک موجودی
    $stmt = $conn->prepare("
        SELECT m.mojood FROM sabad_mahsul sm
        JOIN mahsulat m ON sm.mahsul_id = m.id
        WHERE sm.id = ?
    ");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$res || $res['mojood'] < $tedad) {
        $conn->close();
        return ['success' => false, 'message' => 'موجودی کافی نیست'];
    }

    $stmt = $conn->prepare("UPDATE sabad_mahsul SET tedad = ? WHERE id = ?");
    $stmt->bind_param("ii", $tedad, $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return ['success' => true];
}

function sabad_remove($item_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM sabad_mahsul WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return ['success' => true];
}

function sabad_clear() {
    $sabad_id = sabad_get_or_create();
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM sabad_mahsul WHERE sabad_id = ?");
    $stmt->bind_param("i", $sabad_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function sabad_count() {
    /* فقط‌خواندنی: برای بازدیدکننده بدون سبد، رکوردی ساخته نمیشود
      (جلوگیری از پر شدن جدول sabad توسط ترافیک/ربات) */
    $bank = new Bank();
    $conn = $bank->getConnection();
    $session_id = session_id();
    $karbar_id = $_SESSION['user_id'] ?? null;
    $sabad_id = 0;
    if ($karbar_id) {
        $stmt = $conn->prepare("SELECT id FROM sabad WHERE karbar_id = ? LIMIT 1");
        $stmt->bind_param("i", $karbar_id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($r) $sabad_id = (int)$r['id'];
    }
    if (!$sabad_id && $session_id) {
        $stmt = $conn->prepare("SELECT id FROM sabad WHERE session_id = ? LIMIT 1");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($r) $sabad_id = (int)$r['id'];
    }
    if (!$sabad_id) { $conn->close(); return 0; }
    $stmt = $conn->prepare("SELECT COALESCE(SUM(tedad),0) AS cnt FROM sabad_mahsul WHERE sabad_id = ?");
    $stmt->bind_param("i", $sabad_id);
    $stmt->execute();
    $cnt = (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    $stmt->close();
    $conn->close();
    return $cnt;
}

function sabad_total() {
    $items = sabad_get_items();
    $total = 0;
    foreach ($items as $item) {
        $total += $item['majmoo'];
    }
    return $total;
}