<?php

function tarnegar_get_list($page = 1, $limit = 6) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $offset = ($page - 1) * $limit;
    $stmt = $conn->prepare(
        "SELECT id, title, slug, content, kholaseh, tasvir, created_at FROM posts WHERE type = 'blog' AND status = 'publish' ORDER BY created_at DESC LIMIT ? OFFSET ?"
    );
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $row['categories'] = tarnegar_get_post_categories($row['id']);
        $posts[] = $row;
    }
    $count_result = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type = 'blog' AND status = 'publish'");
    $total = $count_result->fetch_assoc()['cnt'] ?? 0;
    $stmt->close();
    $conn->close();
    return ['posts' => $posts, 'total' => $total, 'pages' => ceil($total / $limit)];
}

function tarnegar_get_by_slug($slug) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT id, title, slug, content, kholaseh, tasvir, created_at FROM posts WHERE slug = ? AND type = 'blog' AND status = 'publish' LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
    if ($post) {
        $post['categories'] = tarnegar_get_post_categories($post['id']);
    }
    $conn->close();
    return $post;
}

function tarnegar_get_post_categories($post_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT c.id, c.title, c.slug FROM categories c JOIN post_categories pc ON pc.category_id = c.id WHERE pc.post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cats = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $cats;
}

function tarnegar_get_all_categories() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT id, title, slug, description FROM categories ORDER BY title");
    $cats = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $cats;
}

function tarnegar_get_related_posts($post_id, $limit = 3) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT DISTINCT p.id, p.title, p.slug, p.tasvir, p.kholaseh, p.created_at
        FROM posts p
        JOIN post_categories pc ON pc.post_id = p.id
        WHERE p.id != ? AND p.type = 'blog' AND p.status = 'publish'
        AND pc.category_id IN (SELECT category_id FROM post_categories WHERE post_id = ?)
        ORDER BY p.created_at DESC LIMIT ?");
    $stmt->bind_param("iii", $post_id, $post_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $posts;
}
