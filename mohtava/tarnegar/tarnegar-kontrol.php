<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/tarnegar-model.php';
require_once __DIR__ . '/../sakhtar/builder.php';
require_once MASIR_RISH . 'haste/tanzimat.php';

function tarnegar_route($amaliat, $paramha) {
    if (empty($amaliat) || ctype_digit($amaliat)) {
        $page = max(1, (int)($amaliat ?: ($paramha[0] ?? 1)));
        tarnegar_fehrest($page);
    } else {
        tarnegar_neveshteh($amaliat);
    }
}

function tarnegar_fehrest($page = 1) {
    $data = tarnegar_get_list($page, 6);
    $posts = $data['posts'];
    $total_pages = $data['pages'];
    $current_page = $page;
    $categories = tarnegar_get_all_categories();

    $bank = new Bank();
    $conn = $bank->getConnection();
    $page_data = $conn->query("SELECT * FROM posts WHERE template='blog' AND status='publish' LIMIT 1")->fetch_assoc();
    $conn->close();

    // صفحه‌ساز برای آرشیو بلاگ
    $builder_content = '';
    if (file_exists(MASIR_RISH . 'mohtava/sakhtar/builder.php')) {
        require_once MASIR_RISH . 'mohtava/sakhtar/builder.php';
        $archive_bp = builder_find_template('archive', 'blog');
        if ($archive_bp && isset($archive_bp['id'])) {
            $builder_content = builder_render_page($archive_bp['id']);
        }
    }

    $onvan_safhe = $page_data['title'] ?? ('تارنگار | ' . SITE_NAME);
    $meta_sharh = strip_tags($page_data['content'] ?? 'تازه‌ترین مطالب آموزشی');
    $safhe_faali = 'tarnegar';
    include MASIR_GHALEB . 'tarnegar.php';
}

function tarnegar_neveshteh($slug) {
    $post = tarnegar_get_by_slug($slug);
    if (!$post) {
        http_response_code(404);
        include MASIR_GHALEB . '404.php';
        return;
    }

    // صرفنظر از اینکه از صفحه‌ساز استفاده می‌شود یا نه، فرمت HTML محتوا را آماده کن
    $builder_content = '';
    if (file_exists(MASIR_RISH . 'mohtava/sakhtar/builder.php')) {
        require_once MASIR_RISH . 'mohtava/sakhtar/builder.php';
        // ابتدا جستجوی صفحه‌ساز برای single با type='blog'
        $bp_info = builder_get_page_id('blog', $slug);
        if (!$bp_info) $bp_info = builder_get_page_id('maghaleh', $slug);
        if ($bp_info && $bp_info['bp_id']) {
            $builder_content = builder_render_page($bp_info['bp_id']);
        }
    }

    $related = tarnegar_get_related_posts($post['id']);
    $categories = tarnegar_get_all_categories();

    $onvan_safhe = $post['title'] . ' | ' . SITE_NAME;
    $meta_sharh = strip_tags($post['kholaseh'] ?: mb_substr($post['content'], 0, 160));
    $safhe_faali = 'tarnegar';
    include MASIR_GHALEB . 'neveshteh.php';
}
