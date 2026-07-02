<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/sabad-model.php';

function forushgah_route($amaliat, $paramha) {
    if ($amaliat === 'dasteh') {
        $dasteh_slug = $paramha[0] ?? null;
        $page = max(1, (int)($paramha[1] ?? 1));
        mahsul_fehrest($dasteh_slug, $page);
    } elseif ($amaliat === 'sabad') {
        sabad_namayesh();
    } elseif ($amaliat === 'add') {
        $mahsul_id = (int)($paramha[0] ?? 0);
        $tedad = max(1, (int)($paramha[1] ?? 1));
        if ($mahsul_id) sabad_add_ajax($mahsul_id, $tedad);
    } elseif ($amaliat === 'update') {
        $item_id = (int)($paramha[0] ?? 0);
        $tedad = max(1, (int)($paramha[1] ?? 1));
        if ($item_id) sabad_update_ajax($item_id, $tedad);
    } elseif ($amaliat === 'remove') {
        $item_id = (int)($paramha[0] ?? 0);
        if ($item_id) sabad_remove_ajax($item_id);
    } elseif ($amaliat === 'count') {
        sabad_count_ajax();
    } elseif (empty($amaliat) || ctype_digit($amaliat)) {
        mahsul_fehrest(null, $amaliat ? (int)$amaliat : 1);
    } else {
        mahsul_tan($amaliat);
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

function sabad_add_ajax($mahsul_id, $tedad) {
    header('Content-Type: application/json');
    $res = sabad_add($mahsul_id, $tedad);
    $res['count'] = sabad_count();
    echo json_encode($res);
    exit;
}

function sabad_update_ajax($item_id, $tedad) {
    header('Content-Type: application/json');
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