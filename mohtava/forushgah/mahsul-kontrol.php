<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/mahsul-model.php';

function mahsul_route($amaliat, $paramha) {
    if ($amaliat === 'dasteh') {
        $dasteh_slug = $paramha[0] ?? null;
        $page = max(1, (int)($paramha[1] ?? 1));
        mahsul_fehrest($dasteh_slug, $page);
    } elseif (empty($amaliat) || ctype_digit($amaliat)) {
        mahsul_fehrest(null, $amaliat ? (int)$amaliat : 1);
    } else {
        mahsul_tan($amaliat);
    }
}

function mahsul_fehrest($dasteh_slug = null, $page = 1) {
    $dasteha = mahsul_dasteh_list();
    $dasteh_id = null;
    if ($dasteh_slug) {
        foreach ($dasteha as $d) {
            if ($d['slug'] === $dasteh_slug) {
                $dasteh_id = $d['id'];
                break;
            }
        }
    }
    $data = mahsul_get_list($dasteh_id, $page, 12);
    $mahsulat = $data['mahsulat'];
    $total_pages = $data['pages'];
    $current_page = $page;
    $onvan_safhe = 'فروشگاه | ' . SITE_NAME;
    $meta_sharh = 'خرید لپ‌تاپ، کیس، مودم و قطعات کامپیوتر';
    $safhe_faali = 'forushgah';
    include MASIR_GHALEB . 'forushgah/mahsulat.php';
}

function mahsul_tan($slug) {
    $mahsul = mahsul_get_by_slug($slug);
    if (!$mahsul) {
        http_response_code(404);
        include MASIR_GHALEB . '404.php';
        return;
    }
    $builder_content = '';
    require_once __DIR__ . '/../sakhtar/builder.php';
    $bp_info = builder_get_page_id('mahsul', $slug);
    if ($bp_info && $bp_info['bp_id']) {
        $builder_content = builder_render_page($bp_info['bp_id']);
    }
    $onvan_safhe = $mahsul['onvan'] . ' | ' . SITE_NAME;
    $meta_sharh = $mahsul['tozih'] ? mb_substr(strip_tags($mahsul['tozih']), 0, 160) : 'خرید ' . $mahsul['onvan'];
    $safhe_faali = 'forushgah';
    $gheymat = $mahsul['gheymat_takhfif'] ?: $mahsul['gheymat'];
    include MASIR_GHALEB . 'forushgah/mahsul.php';
}