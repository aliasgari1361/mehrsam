<?php

function mohtava_route($action, $params) {
    require_once __DIR__ . '/../dade/bank.php';

    $template = 'mehrsam';

    switch ($action) {
        case 'safhe':
        case 'maghaleh':
            $slug = $params[0] ?? '';
            if (empty($slug)) {
                echo "شناسه مطلب نامشخص است.";
                break;
            }

            $post = getPostBySlug($slug, $action);
            if ($post) {
                $block_page = null;
                require_once __DIR__ . '/builder/builder.php';
                $bp_info = builder_get_page_id($action, $slug);
                if ($bp_info && $bp_info['bp_id']) {
                    $block_html = builder_render_page($bp_info['bp_id']);
                } else {
                    $block_html = '';
                }
                if ($block_html) {
                    $onvan_safhe = htmlspecialchars($post['title']);
                    include __DIR__ . '/../ghaleb/' . $template . '/sarsafhe.php';
                    echo $block_html;
                    include __DIR__ . '/../ghaleb/' . $template . '/panevis.php';
                } else {
                    include __DIR__ . '/../ghaleb/' . $template . '/sarsafhe.php';
                    echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";
                    echo "<div>" . $post['content'] . "</div>";
                    include __DIR__ . '/../ghaleb/' . $template . '/panevis.php';
                }
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "مطلب مورد نظر یافت نشد.";
            }
            break;

        default:
            echo "بخش محتوا";
            break;
    }
}

function getPostBySlug($slug, $type) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT title, content, slug FROM posts WHERE slug = ? AND type = ? AND status = 'publish' LIMIT 1");
    $stmt->bind_param("ss", $slug, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $post;
}

function getPostsByType($type) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT title, slug, content FROM posts WHERE type = ? AND status = 'publish' ORDER BY created_at DESC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $posts;
}
