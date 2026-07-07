<?php

require_once MASIR_DADE . 'bank.php';

function sefaresh_create($data) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("
        INSERT INTO sefaresh (
            karbar_id, onvan_girande, telefon_girande, ostan, shahr, adres,
            kode_posty, post_type, post_hazine, tozih, majmoo_gheymat,
            vaziat, pardakht_vaziat, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW())
    ");

    $stmt->bind_param(
        "issssssisd",
        $data['karbar_id'],
        $data['onvan_girande'],
        $data['telefon_girande'],
        $data['ostan'],
        $data['shahr'],
        $data['adres'],
        $data['kode_posty'],
        $data['post_type'],
        $data['post_hazine'],
        $data['tozih'],
        $data['majmoo_gheymat']
    );

    $result = $stmt->execute();
    $sefaresh_id = $conn->insert_id;
    $stmt->close();
    $conn->close();

    return $result ? $sefaresh_id : false;
}

function sefaresh_add_mahsulat($sefaresh_id, $items) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    foreach ($items as $item) {
        $stmt = $conn->prepare("
            INSERT INTO sefaresh_mahsul (sefaresh_id, mahsul_id, tedad, gheymat_vahed)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $sefaresh_id, $item['mahsul_id'], $item['tedad'], $item['gheymat_akhar']);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE mahsulat SET mojood = mojood - ? WHERE id = ? AND mojood >= ?");
        $stmt->bind_param("iii", $item['tedad'], $item['mahsul_id'], $item['tedad']);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}

function sefaresh_get($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT * FROM sefaresh WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $sefaresh = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($sefaresh) {
        $stmt = $conn->prepare("
            SELECT sm.*, m.onvan, m.slug, m.tasvir
            FROM sefaresh_mahsul sm
            JOIN mahsulat m ON sm.mahsul_id = m.id
            WHERE sm.sefaresh_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $sefaresh['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $conn->close();
    return $sefaresh;
}

function sefaresh_get_by_authority($authority) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT id FROM sefaresh WHERE pardakht_ref_id = ? LIMIT 1");
    $stmt->bind_param("s", $authority);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    return $result ? $result['id'] : false;
}

function sefaresh_update_vaziat($id, $vaziat, $pardakht_vaziat = null, $pardakht_ref_id = null) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $sql = "UPDATE sefaresh SET vaziat = ?";
    $params = [$vaziat];
    $types = 's';

    if ($pardakht_vaziat) {
        $sql .= ", pardakht_vaziat = ?";
        $params[] = $pardakht_vaziat;
        $types .= 's';
    }
    if ($pardakht_ref_id) {
        $sql .= ", pardakht_ref_id = ?";
        $params[] = $pardakht_ref_id;
        $types .= 's';
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $result;
}

function sefaresh_update_pardakht_ref($id, $authority) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("UPDATE sefaresh SET pardakht_ref_id = ? WHERE id = ?");
    $stmt->bind_param("si", $authority, $id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $result;
}

function sefaresh_get_user($karbar_id, $page = 1, $per_page = 10) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $offset = ($page - 1) * $per_page;

    $stmt = $conn->prepare("
        SELECT * FROM sefaresh 
        WHERE karbar_id = ? 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $karbar_id, $per_page, $offset);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM sefaresh WHERE karbar_id = ?");
    $stmt->bind_param("i", $karbar_id);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();

    $conn->close();

    return ['data' => $data, 'total' => $total];
}

function sefaresh_get_all($page = 1, $per_page = 20, $vaziat = null) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $offset = ($page - 1) * $per_page;
    $where = '';
    $params = [];
    $types = '';

    if ($vaziat) {
        $where = ' WHERE vaziat = ?';
        $params[] = $vaziat;
        $types .= 's';
    }

    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';

    $sql = "SELECT * FROM sefaresh $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $count_sql = "SELECT COUNT(*) as cnt FROM sefaresh $where";
    $stmt = $conn->prepare($count_sql);
    if ($vaziat) {
        $stmt->bind_param("s", $vaziat);
    }
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();

    $conn->close();

    return ['data' => $data, 'total' => $total];
}