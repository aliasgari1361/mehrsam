<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/sabad-model.php';

function sabad_route($params) {
    $sub_action = $params[0] ?? '';
    if ($sub_action === 'add') {
        sabad_add_ajax();
    } elseif ($sub_action === 'update') {
        $item_id = (int)($params[1] ?? 0);
        sabad_update_ajax($item_id);
    } elseif ($sub_action === 'remove') {
        $item_id = (int)($params[1] ?? 0);
        sabad_remove_ajax($item_id);
    } elseif ($sub_action === 'count') {
        sabad_count_ajax();
    } else {
        sabad_namayesh();
    }
}

function sabad_namayesh() {
    $items = sabad_get_items();
    $total = sabad_total();
    $count = sabad_count();

    $onvan_safhe = 'سبد خرید | ' . SITE_NAME;
    $meta_sharh = 'سبد خرید شما در مهراد سام';
    $safhe_faali = 'forushgah';
    include MASIR_GHALEB . 'forushgah/sabad.php';
}

function sabad_add_ajax() {
    header('Content-Type: application/json');
    $mahsul_id = (int)($_POST['mahsul_id'] ?? 0);
    $tedad = max(1, (int)($_POST['tedad'] ?? 1));
    if ($mahsul_id > 0) {
        $res = sabad_add($mahsul_id, $tedad);
    } else {
        $res = ['success' => false, 'message' => 'محصول نامعتبر است'];
    }
    $res['count'] = sabad_count();
    $res['total'] = sabad_total();
    echo json_encode($res);
    exit;
}

function sabad_update_ajax($item_id) {
    header('Content-Type: application/json');
    $tedad = max(1, (int)($_POST['tedad'] ?? 1));
    $res = sabad_update_tedad($item_id, $tedad);
    $res['count'] = sabad_count();
    $res['total'] = sabad_total();
    echo json_encode($res);
    exit;
}

function sabad_remove_ajax($item_id) {
    header('Content-Type: application/json');
    $res = sabad_remove($item_id);
    $res['count'] = sabad_count();
    $res['total'] = sabad_total();
    echo json_encode($res);
    exit;
}

function sabad_count_ajax() {
    header('Content-Type: application/json');
    echo json_encode(['count' => sabad_count(), 'total' => sabad_total()]);
    exit;
}