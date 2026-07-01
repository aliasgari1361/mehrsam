<?php

function tarnegar_get_list($page = 1, $limit = 10) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $offset = ($page - 1) * $limit;
    $stmt = $conn->prepare(
        "SELECT id, title, slug, content, created_at FROM posts WHERE type = 'blog' AND status = 'publish' ORDER BY created_at DESC LIMIT ? OFFSET ?"
    );
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
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
    $stmt = $conn->prepare("SELECT id, title, slug, content, created_at FROM posts WHERE slug = ? AND type = 'blog' AND status = 'publish' LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $post;
}
