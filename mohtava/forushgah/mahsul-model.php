<?php

function mahsul_get_list($dasteh_id = null, $page = 1, $limit = 12) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT id, onvan, slug, gheymat, gheymat_takhfif, tasvir, tozih FROM mahsulat WHERE vaziat = 1";
    $params = [];
    $types = '';

    if ($dasteh_id) {
        $sql .= " AND dasteh_id = ?";
        $params[] = $dasteh_id;
        $types .= 'i';
    }

    $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $mahsulat = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $count_sql = "SELECT COUNT(*) AS cnt FROM mahsulat WHERE vaziat = 1";
    $count_params = [];
    $count_types = '';
    if ($dasteh_id) {
        $count_sql .= " AND dasteh_id = ?";
        $count_params[] = $dasteh_id;
        $count_types .= 'i';
    }
    $stmt = $conn->prepare($count_sql);
    if ($count_params) {
        $stmt->bind_param($count_types, ...$count_params);
    }
    $stmt->execute();
    $count_result = $stmt->get_result();
    $total = $count_result->fetch_assoc()['cnt'] ?? 0;
    $stmt->close();
    $conn->close();

    return ['mahsulat' => $mahsulat, 'total' => $total, 'pages' => ceil($total / $limit)];
}

function mahsul_get_by_slug($slug) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM mahsulat WHERE slug = ? AND vaziat = 1 LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $mahsul = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $mahsul;
}

function mahsul_dasteh_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT id, onvan, slug FROM mahsul_dasteh ORDER BY tartib ASC");
    $dasteha = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $dasteha;
}
