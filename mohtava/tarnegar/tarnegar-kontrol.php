<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/tarnegar-model.php';

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
    $onvan_safhe = 'تارنگار | ' . SITE_NAME;
    $meta_sharh = 'تازه‌ترین مطالب آموزشی و خبری کامپیوتر و فناوری';
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
    $onvan_safhe = $post['title'] . ' | ' . SITE_NAME;
    $meta_sharh = mb_substr(strip_tags($post['content']), 0, 160);
    $safhe_faali = 'tarnegar';
    include MASIR_GHALEB . 'neveshteh.php';
}
