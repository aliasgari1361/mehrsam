<?php
/**
 * مسیریاب اصلی مهراد سام
 * آدرس دریافتی را تجزیه کرده و کنترلر مناسب را صدا می‌زند
 */

function masiryab_kon($url) {
    // حذف / از ابتدا و انتها و تبدیل به آرایه
    $parts    = explode('/', trim($url, '/'));
    $bakhsh   = $parts[0] ?? '';
    $amaliat  = $parts[1] ?? '';
    $paramha  = array_slice($parts, 2);

    switch ($bakhsh) {

        // ---- پنل مدیریت ----
        case 'mod':
            require_once MASIR_RISH . 'haste/mod/mod.php';
            mod_route($amaliat, $paramha);
            break;

        // ---- پنل کاربری ----
        case 'karbar':
            require_once MASIR_RISH . 'haste/karbar/karbar.php';
            karbar_route($amaliat, $paramha);
            break;

        // ---- خدمات ----
        case 'khadamat':
            require_once MASIR_RISH . 'mohtava/khadamat/khadamat-kontrol.php';
            khadamat_route($amaliat, $paramha);
            break;

        // ---- محتوای عمومی (صفحات داینامیک) ----
        case 'mohtava':
            require_once MASIR_RISH . 'mohtava/mohtava-kontrol.php';
            mohtava_route($amaliat, $paramha);
            break;

        // ---- فروشگاه ----
        case 'forushgah':
            if ($amaliat === 'checkout') {
                require_once MASIR_RISH . 'mohtava/forushgah/sefaresh-kontrol.php';
                forushgah_checkout($amaliat, $paramha);
            } elseif (in_array($amaliat, ['zarinpal', 'idpay', 'zibal'])) {
                require_once MASIR_RISH . 'mohtava/forushgah/sefaresh-kontrol.php';
                checkout_gateway_callback($amaliat);
            } elseif ($amaliat === 'sabad') {
                require_once MASIR_RISH . 'mohtava/forushgah/sabad-kontrol.php';
                sabad_route($paramha);
            } else {
                require_once MASIR_RISH . 'mohtava/forushgah/mahsul-kontrol.php';
                mahsul_route($amaliat, $paramha);
            }
            break;

        // ---- چت ----
        case 'chat':
            require_once MASIR_DADE . 'bank.php';
            require_once MASIR_RISH . 'mohtava/gheychat/chat-kontrol.php';
            chat_route($amaliat, $paramha);
            break;

        // ---- تارنگار (وبلاگ) ----
        case 'tarnegar':
            require_once MASIR_RISH . 'mohtava/tarnegar/tarnegar-kontrol.php';
            tarnegar_route($amaliat, $paramha);
            break;

        // ---- تماس با ما ----
        case 'tamas':
            require_once MASIR_RISH . 'mohtava/tamas/tamas-kontrol.php';
            tamas_route($amaliat, $paramha);
            break;

        // ---- صفحه اصلی یا ۴۰۴ ----
        default:
            if ($bakhsh === '' || $bakhsh === 'home') {
                require_once MASIR_RISH . 'mohtava/khadamat/khadamat-kontrol.php';
                safhe_khane();
            } else {
                http_response_code(404);
                include MASIR_GHALEB . '404.php';
            }
            break;
    }
}
