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

function mahsul_brand_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT id, onvan, slug, logo, vaziat FROM mahsul_brand ORDER BY tartib ASC, onvan ASC");
    $brands = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $brands;
}

function mahsul_brand_get($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM mahsul_brand WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $brand = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $brand;
}

function mahsul_brand_save($id, $data) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($id) {
        $stmt = $conn->prepare("UPDATE mahsul_brand SET onvan=?, slug=?, logo=?, tozih=?, tartib=?, vaziat=? WHERE id=?");
        $stmt->bind_param("ssssiii", $data['onvan'], $data['slug'], $data['logo'], $data['tozih'], $data['tartib'], $data['vaziat'], $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO mahsul_brand (onvan, slug, logo, tozih, tartib, vaziat) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $data['onvan'], $data['slug'], $data['logo'], $data['tozih'], $data['tartib'], $data['vaziat']);
    }
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function mahsul_brand_delete($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM mahsul_brand WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function mahsul_categories_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT * FROM mahsul_dasteh ORDER BY tartib ASC, onvan ASC");
    $cats = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $cats;
}

function mahsul_category_get($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM mahsul_dasteh WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cat = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $cat;
}

function mahsul_category_save($id, $data) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($id) {
        $stmt = $conn->prepare("UPDATE mahsul_dasteh SET onvan=?, slug=?, tartib=? WHERE id=?");
        $stmt->bind_param("ssii", $data['onvan'], $data['slug'], $data['tartib'], $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO mahsul_dasteh (onvan, slug, tartib) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $data['onvan'], $data['slug'], $data['tartib']);
    }
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function mahsul_category_delete($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM mahsul_dasteh WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function mahsul_get($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM mahsulat WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $m = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $m;
}

function mahsul_all($search = '', $dasteh_id = 0, $brand_id = 0) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $sql = "SELECT m.*, d.onvan AS dasteh_onvan, b.onvan AS brand_onvan 
            FROM mahsulat m 
            LEFT JOIN mahsul_dasteh d ON m.dasteh_id = d.id 
            LEFT JOIN mahsul_brand b ON m.brand_id = b.id 
            WHERE 1=1";
    $params = [];
    $types = '';
    if ($search) {
        $sql .= " AND (m.onvan LIKE ? OR m.slug LIKE ?)";
        $s = "%$search%";
        $params[] = $s; $params[] = $s;
        $types .= 'ss';
    }
    if ($dasteh_id) {
        $sql .= " AND m.dasteh_id = ?";
        $params[] = $dasteh_id;
        $types .= 'i';
    }
    if ($brand_id) {
        $sql .= " AND m.brand_id = ?";
        $params[] = $brand_id;
        $types .= 'i';
    }
    $sql .= " ORDER BY m.id DESC";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $list = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    $conn->close();
    return $list;
}

function mahsul_save($id, $data) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    if ($id) {
        $stmt = $conn->prepare("UPDATE mahsulat SET onvan=?, slug=?, dasteh_id=?, brand_id=?, gheymat=?, gheymat_takhfif=?, tozih=?, virayesh=?, tasvir=?, mojood=?, vaziat=? WHERE id=?");
        $stmt->bind_param("ssiiidsssiii", 
            $data['onvan'], $data['slug'], $data['dasteh_id'], $data['brand_id'],
            $data['gheymat'], $data['gheymat_takhfif'], $data['tozih'], $data['virayesh'],
            $data['tasvir'], $data['mojood'], $data['vaziat'], $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO mahsulat (onvan, slug, dasteh_id, brand_id, gheymat, gheymat_takhfif, tozih, virayesh, tasvir, mojood, vaziat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiidsssii", 
            $data['onvan'], $data['slug'], $data['dasteh_id'], $data['brand_id'],
            $data['gheymat'], $data['gheymat_takhfif'], $data['tozih'], $data['virayesh'],
            $data['tasvir'], $data['mojood'], $data['vaziat']);
    }
    $stmt->execute();
    $id = $id ?: $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $id;
}

function mahsul_delete($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM mahsulat WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
