<?php

function get_block_types() {
    return [
        'heading' => [
            'label' => 'عنوان',
            'icon' => 'fa-heading',
            'color' => '#FF6F00',
            'desc' => 'عنوان با سایز دلخواه',
            'fields' => [
                ['key' => 'text', 'label' => 'متن عنوان', 'type' => 'text', 'default' => 'عنوان جدید'],
                ['key' => 'level', 'label' => 'سایز', 'type' => 'select', 'default' => 'h2', 'options' => [
                    ['value' => 'h1', 'label' => 'خیلی بزرگ (h1)'],
                    ['value' => 'h2', 'label' => 'بزرگ (h2)'],
                    ['value' => 'h3', 'label' => 'متوسط (h3)'],
                    ['value' => 'h4', 'label' => 'کوچک (h4)'],
                ]],
                ['key' => 'align', 'label' => 'تراز', 'type' => 'select', 'default' => 'right', 'options' => [
                    ['value' => 'right', 'label' => 'راست'],
                    ['value' => 'center', 'label' => 'وسط'],
                    ['value' => 'left', 'label' => 'چپ'],
                ]],
                ['key' => 'color', 'label' => 'رنگ', 'type' => 'color', 'default' => '#1a1a1a'],
            ],
            'defaults' => ['text' => 'عنوان جدید', 'level' => 'h2', 'align' => 'right', 'color' => '#1a1a1a'],
        ],
        'text' => [
            'label' => 'متن',
            'icon' => 'fa-paragraph',
            'color' => '#0984E3',
            'desc' => 'متن ساده با ویرایشگر',
            'fields' => [
                ['key' => 'content', 'label' => 'محتوا', 'type' => 'html', 'default' => '<p>متن خود را وارد کنید...</p>'],
            ],
            'defaults' => ['content' => '<p>متن خود را وارد کنید...</p>'],
        ],
        'image' => [
            'label' => 'تصویر',
            'icon' => 'fa-image',
            'color' => '#00B894',
            'desc' => 'تصویر با توضیحات',
            'fields' => [
                ['key' => 'src', 'label' => 'آدرس تصویر', 'type' => 'image', 'default' => ''],
                ['key' => 'alt', 'label' => 'متن جایگزین', 'type' => 'text', 'default' => ''],
                ['key' => 'caption', 'label' => 'توضیح زیر تصویر', 'type' => 'text', 'default' => ''],
                ['key' => 'width', 'label' => 'عرض (مثلاً 100%)', 'type' => 'text', 'default' => '100%'],
            ],
            'defaults' => ['src' => '', 'alt' => '', 'caption' => '', 'width' => '100%'],
        ],
        'gallery' => [
            'label' => 'گالری',
            'icon' => 'fa-images',
            'color' => '#6C5CE7',
            'desc' => 'گالری تصاویر',
            'fields' => [
                ['key' => 'images', 'label' => 'تصاویر (URL با کاما جدا)', 'type' => 'textarea', 'default' => ''],
                ['key' => 'columns', 'label' => 'تعداد ستون', 'type' => 'select', 'default' => '3', 'options' => [
                    ['value' => '2', 'label' => '۲ ستون'],
                    ['value' => '3', 'label' => '۳ ستون'],
                    ['value' => '4', 'label' => '۴ ستون'],
                ]],
            ],
            'defaults' => ['images' => '', 'columns' => '3'],
        ],
        'button' => [
            'label' => 'دکمه',
            'icon' => 'fa-square-check',
            'color' => '#E17055',
            'desc' => 'دکمه با لینک دلخواه',
            'fields' => [
                ['key' => 'text', 'label' => 'متن دکمه', 'type' => 'text', 'default' => 'بیشتر بدانید'],
                ['key' => 'url', 'label' => 'لینک', 'type' => 'text', 'default' => '#'],
                ['key' => 'style', 'label' => 'استایل', 'type' => 'select', 'default' => 'asli', 'options' => [
                    ['value' => 'asli', 'label' => 'پیش‌فرض (نارنجی)'],
                    ['value' => 'khali', 'label' => 'توخالی'],
                    ['value' => 'sabz', 'label' => 'سبز'],
                ]],
            ],
            'defaults' => ['text' => 'بیشتر بدانید', 'url' => '#', 'style' => 'asli'],
        ],
        'divider' => [
            'label' => 'جداکننده',
            'icon' => 'fa-minus',
            'color' => '#636e72',
            'desc' => 'خط جداکننده بخش‌ها',
            'fields' => [
                ['key' => 'style', 'label' => 'استایل', 'type' => 'select', 'default' => 'solid', 'options' => [
                    ['value' => 'solid', 'label' => 'خط ساده'],
                    ['value' => 'dashed', 'label' => 'خط چین'],
                    ['value' => 'dotted', 'label' => 'نقطه چین'],
                ]],
                ['key' => 'margin', 'label' => 'فاصله (px)', 'type' => 'number', 'default' => '40'],
            ],
            'defaults' => ['style' => 'solid', 'margin' => '40'],
        ],
        'columns' => [
            'label' => 'ستون‌بندی',
            'icon' => 'fa-columns',
            'color' => '#00cec9',
            'desc' => 'محتوای چندستونی',
            'fields' => [
                ['key' => 'columns', 'label' => 'تعداد ستون', 'type' => 'select', 'default' => '2', 'options' => [
                    ['value' => '2', 'label' => '۲ ستون'],
                    ['value' => '3', 'label' => '۳ ستون'],
                    ['value' => '4', 'label' => '۴ ستون'],
                ]],
                ['key' => 'col1', 'label' => 'محتوای ستون ۱', 'type' => 'html', 'default' => '<p>ستون ۱</p>'],
                ['key' => 'col2', 'label' => 'محتوای ستون ۲', 'type' => 'html', 'default' => '<p>ستون ۲</p>'],
                ['key' => 'col3', 'label' => 'محتوای ستون ۳', 'type' => 'html', 'default' => ''],
                ['key' => 'col4', 'label' => 'محتوای ستون ۴', 'type' => 'html', 'default' => ''],
            ],
            'defaults' => ['columns' => '2', 'col1' => '<p>ستون ۱</p>', 'col2' => '<p>ستون ۲</p>', 'col3' => '', 'col4' => ''],
        ],
        'video' => [
            'label' => 'ویدیو',
            'icon' => 'fa-video',
            'color' => '#e17055',
            'desc' => 'ویدیو آپارات/یوتیوب',
            'fields' => [
                ['key' => 'url', 'label' => 'آدرس ویدیو', 'type' => 'text', 'default' => ''],
                ['key' => 'caption', 'label' => 'توضیح', 'type' => 'text', 'default' => ''],
            ],
            'defaults' => ['url' => '', 'caption' => ''],
        ],
        'services' => [
            'label' => 'خدمات',
            'icon' => 'fa-headset',
            'color' => '#FF6F00',
            'desc' => 'نمایش لیست خدمات سایت',
            'fields' => [
                ['key' => 'count', 'label' => 'تعداد نمایش', 'type' => 'number', 'default' => '6'],
                ['key' => 'title', 'label' => 'عنوان بخش', 'type' => 'text', 'default' => 'خدمات ما'],
            ],
            'defaults' => ['count' => '6', 'title' => 'خدمات ما'],
        ],
        'products' => [
            'label' => 'محصولات',
            'icon' => 'fa-cube',
            'color' => '#00B894',
            'desc' => 'نمایش محصولات فروشگاه',
            'fields' => [
                ['key' => 'count', 'label' => 'تعداد نمایش', 'type' => 'number', 'default' => '8'],
                ['key' => 'title', 'label' => 'عنوان بخش', 'type' => 'text', 'default' => 'محصولات'],
                ['key' => 'dasteh_id', 'label' => 'دسته (0=همه)', 'type' => 'number', 'default' => '0'],
            ],
            'defaults' => ['count' => '8', 'title' => 'محصولات', 'dasteh_id' => '0'],
        ],
        'custom' => [
            'label' => 'HTML سفارشی',
            'icon' => 'fa-code',
            'color' => '#2d3436',
            'desc' => 'کد HTML دلخواه',
            'fields' => [
                ['key' => 'html', 'label' => 'کد HTML', 'type' => 'html', 'default' => '<div>HTML سفارشی</div>'],
            ],
            'defaults' => ['html' => '<div>HTML سفارشی</div>'],
        ],
        'map' => [
            'label' => 'نقشه',
            'icon' => 'fa-map-location-dot',
            'color' => '#0984E3',
            'desc' => 'نقشه گوگل/OpenStreetMap',
            'fields' => [
                ['key' => 'embed_url', 'label' => 'لینک Embed نقشه', 'type' => 'text', 'default' => ''],
                ['key' => 'height', 'label' => 'ارتفاع (px)', 'type' => 'number', 'default' => '350'],
            ],
            'defaults' => ['embed_url' => '', 'height' => '350'],
        ],
        'counter' => [
            'label' => 'آمار',
            'icon' => 'fa-chart-simple',
            'color' => '#e17055',
            'desc' => 'آمار و اعداد',
            'fields' => [
                ['key' => 'number', 'label' => 'عدد', 'type' => 'text', 'default' => '۵۰۰+'],
                ['key' => 'label', 'label' => 'برچسب', 'type' => 'text', 'default' => 'مشتری راضی'],
                ['key' => 'icon', 'label' => 'آیکون (FontAwesome)', 'type' => 'text', 'default' => 'fa-users'],
            ],
            'defaults' => ['number' => '۵۰۰+', 'label' => 'مشتری راضی', 'icon' => 'fa-users'],
        ],
    ];
}

function block_heading_render($data) {
    $level = $data['level'] ?? 'h2';
    $text = htmlspecialchars($data['text'] ?? '');
    $align = $data['align'] ?? 'right';
    $color = $data['color'] ?? '#1a1a1a';
    return "<$level class=\"builder-editable builder-text\" data-field=\"text\" style=\"text-align:$align;color:$color;margin:20px 0;\">$text</$level>";
}

function block_text_render($data) {
    $content = $data['content'] ?? '';
    return '<div class="builder-editable builder-text" data-field="content" style="line-height:2;color:#444;font-size:15px;">' . $content . '</div>';
}

function block_image_render($data) {
    $src = $data['src'] ?? '';
    $alt = htmlspecialchars($data['alt'] ?? '');
    $caption = htmlspecialchars($data['caption'] ?? '');
    $width = $data['width'] ?? '100%';
    if (!$src) return '';
    $html = "<div style=\"text-align:center;margin:20px 0;\"><img src=\"$src\" alt=\"$alt\" class=\"builder-editable-image\" data-field=\"src\" style=\"max-width:$width;height:auto;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.1);\">";
    if ($caption) $html .= "<p class=\"builder-editable builder-text\" data-field=\"caption\" style=\"margin-top:8px;font-size:13px;color:#888;\">$caption</p>";
    $html .= '</div>';
    return $html;
}

function block_gallery_render($data) {
    $images = array_filter(array_map('trim', explode(',', $data['images'] ?? '')));
    if (empty($images)) return '';
    $cols = (int)($data['columns'] ?? 3);
    $html = '<div style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:16px;margin:20px 0;">';
    foreach ($images as $img) {
        $html .= '<div style="border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.08);"><img src="' . htmlspecialchars(trim($img)) . '" alt="" style="width:100%;height:200px;object-fit:cover;display:block;"></div>';
    }
    $html .= '</div>';
    return $html;
}

function block_button_render($data) {
    $text = htmlspecialchars($data['text'] ?? 'بیشتر بدانید');
    $url = $data['url'] ?? '#';
    $style = $data['style'] ?? 'asli';
    $btn_style = '';
    if ($style === 'khali') $btn_style = 'background:transparent;color:var(--rang-asli,#FF6F00);border:2px solid var(--rang-asli,#FF6F00);';
    else if ($style === 'sabz') $btn_style = 'background:var(--rang-makm2,#00B894);color:#fff;';
    else $btn_style = 'background:var(--rang-asli,#FF6F00);color:#fff;';
    return '<div style="text-align:center;margin:20px 0;"><a href="' . htmlspecialchars($url) . '" class="dakmeh builder-editable-link builder-editable" data-field="url" style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;transition:all 0.3s;' . $btn_style . '"><span class="builder-editable builder-text" data-field="text" style="display:inline;">' . $text . '</span> <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></a></div>';
}

function block_divider_render($data) {
    $style = $data['style'] ?? 'solid';
    $margin = (int)($data['margin'] ?? 40);
    return '<hr style="border:none;border-top:2px ' . $style . ' #eef0f4;margin:' . $margin . 'px 0;">';
}

function block_columns_render($data) {
    $cols = (int)($data['columns'] ?? 2);
    $html = '<div style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:24px;margin:20px 0;">';
    for ($i = 1; $i <= $cols; $i++) {
        $content = $data['col' . $i] ?? '';
        $html .= '<div class="builder-editable builder-text" data-field="col' . $i . '">' . $content . '</div>';
    }
    $html .= '</div>';
    return $html;
}

function block_video_render($data) {
    $url = $data['url'] ?? '';
    $caption = htmlspecialchars($data['caption'] ?? '');
    if (!$url) return '';
    $embed_url = $url;
    if (preg_match('/aparat\.com\/v\/([a-zA-Z0-9]+)/', $url, $m)) {
        $embed_url = 'https://www.aparat.com/video/video/embed/videohash/' . $m[1] . '/vt/frame';
    } elseif (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
        $embed_url = 'https://www.youtube.com/embed/' . $m[1];
    } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
        $embed_url = 'https://www.youtube.com/embed/' . $m[1];
    }
    $html = '<div style="text-align:center;margin:20px 0;"><iframe src="' . htmlspecialchars($embed_url) . '" style="width:100%;max-width:720px;height:405px;border-radius:12px;border:none;box-shadow:0 4px 20px rgba(0,0,0,0.1);" allowfullscreen></iframe>';
    if ($caption) $html .= '<p style="margin-top:8px;font-size:13px;color:#888;">' . $caption . '</p>';
    $html .= '</div>';
    return $html;
}

function block_services_render($data, $bank = null) {
    $count = (int)($data['count'] ?? 6);
    $title = htmlspecialchars($data['title'] ?? 'خدمات ما');
    if ($bank === null) { $bank = new Bank(); }
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT title, slug, kholaseh, subtitle, tasvir FROM posts WHERE type = 'khadamat' AND status = 'publish' ORDER BY display_order ASC LIMIT ?");
    $stmt->bind_param("i", $count);
    $stmt->execute();
    $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    if (empty($services)) return '';

    $html = '<section style="padding:50px 0;"><div class="mohtava-container">';
    if ($title) $html .= '<div class="onvan-bakhsh"><h2 class="builder-editable builder-text" data-field="title">' . $title . '</h2></div>';
    $html .= '<div class="gerid-3">';
    foreach ($services as $s) {
        $sub = !empty($s['subtitle']) ? '<span style="display:block;font-size:0.8rem;color:var(--rang-tira);font-weight:600;margin-bottom:6px;">' . htmlspecialchars($s['subtitle']) . '</span>' : '';
        $html .= '<a href="' . BASE_URL . 'khadamat/' . htmlspecialchars($s['slug']) . '" style="text-decoration:none;color:inherit;"><div style="background:#fff;border:1px solid var(--rang-border);border-radius:12px;padding:24px;text-align:center;transition:all 0.3s;" onmouseover="this.style.borderColor=\'var(--rang-asli)\'" onmouseout="this.style.borderColor=\'var(--rang-border)\'"><div style="width:52px;height:52px;border-radius:50%;background:var(--rang-roshan);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--rang-asli);font-size:22px;">' . $s['tasvir'] . '</div><h3 style="font-size:16px;margin-bottom:6px;color:#1a1a1a;">' . htmlspecialchars($s['title']) . '</h3>' . $sub . '<p style="color:#777;font-size:14px;line-height:1.7;">' . htmlspecialchars($s['kholaseh']) . '</p></div></a>';
    }
    $html .= '</div></div></section>';
    return $html;
}

function block_products_render($data, $bank = null) {
    $count = (int)($data['count'] ?? 8);
    $title = htmlspecialchars($data['title'] ?? 'محصولات');
    $dasteh_id = (int)($data['dasteh_id'] ?? 0);
    if ($bank === null) { $bank = new Bank(); }
    $conn = $bank->getConnection();
    $sql = "SELECT id, onvan, slug, gheymat, gheymat_takhfif, tasvir, tozih FROM mahsulat WHERE vaziat=1";
    if ($dasteh_id) { $sql .= " AND dasteh_id=?"; $stmt = $conn->prepare($sql . " ORDER BY id DESC LIMIT ?"); $stmt->bind_param("ii", $dasteh_id, $count); }
    else { $stmt = $conn->prepare($sql . " ORDER BY id DESC LIMIT ?"); $stmt->bind_param("i", $count); }
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    if (!$products) return '';
    $html = '<section style="padding:60px 0;background:#f8f9fa;"><div class="mohtava-container">';
    if ($title) $html .= '<div class="onvan-bakhsh"><h2 class="builder-editable builder-text" data-field="title">' . $title . '</h2></div>';
    $html .= '<div class="gerid-4">';
    foreach ($products as $p) {
        $price = $p['gheymat_takhfif'] ?: $p['gheymat'];
        $html .= '<a href="' . BASE_URL . 'forushgah/' . htmlspecialchars($p['slug']) . '" style="text-decoration:none;color:inherit;background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.06);border:1px solid #eef0f4;overflow:hidden;display:block;transition:all 0.3s;" onmouseover="this.style.boxShadow=\'0 8px 25px rgba(0,0,0,0.1)\';this.style.transform=\'translateY(-4px)\'" onmouseout="this.style.boxShadow=\'0 1px 6px rgba(0,0,0,0.06)\';this.style.transform=\'none\'">';
        if ($p['tasvir']) $html .= '<img src="' . htmlspecialchars($p['tasvir']) . '" alt="" style="width:100%;height:180px;object-fit:cover;">';
        $html .= '<div style="padding:16px;"><h3 style="font-size:14px;margin-bottom:8px;">' . htmlspecialchars($p['onvan']) . '</h3>';
        if ($p['gheymat_takhfif']) $html .= '<span style="font-size:12px;color:#888;text-decoration:line-through;margin-left:6px;">' . number_format($p['gheymat']) . '</span>';
        $html .= '<span style="font-weight:700;color:var(--rang-asli,#FF6F00);">' . number_format($price) . ' تومان</span>';
        $html .= '</div></a>';
    }
    $html .= '</div></div></section>';
    return $html;
}

function block_custom_render($data) {
    return '<div class="builder-editable builder-text builder-custom-html" data-field="html">' . ($data['html'] ?? '') . '</div>';
}

function block_map_render($data) {
    $url = $data['embed_url'] ?? '';
    $height = (int)($data['height'] ?? 350);
    if (!$url) return '';
    return '<div style="margin:20px 0;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);"><iframe src="' . htmlspecialchars($url) . '" style="width:100%;height:' . $height . 'px;border:none;" allowfullscreen></iframe></div>';
}

function block_counter_render($data) {
    $number = htmlspecialchars($data['number'] ?? '۵۰۰+');
    $label = htmlspecialchars($data['label'] ?? '');
    $icon = htmlspecialchars($data['icon'] ?? 'fa-users');
    return '<div style="text-align:center;padding:20px;"><div style="width:60px;height:60px;background:var(--rang-asli,#FF6F00);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#fff;font-size:24px;"><i class="fa-solid ' . $icon . '"></i></div><div style="font-size:1.8rem;font-weight:700;color:var(--rang-asli,#FF6F00);">' . $number . '</div><div style="font-size:13px;color:#888;">' . $label . '</div></div>';
}

function render_block_admin($block) {
    $types = get_block_types();
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $bt = $types[$type] ?? ['label' => $type, 'icon' => 'fa-cube', 'color' => '#888'];
    $color = $bt['color'];
    $icon = $bt['icon'];
    $label = htmlspecialchars($bt['label']);

    $preview = '';
    switch ($type) {
        case 'heading': $preview = '<' . ($data['level'] ?? 'h2') . '>' . htmlspecialchars(mb_substr($data['text'] ?? '', 0, 50)) . '</' . ($data['level'] ?? 'h2') . '>'; break;
        case 'text': $preview = htmlspecialchars(mb_substr(strip_tags($data['content'] ?? ''), 0, 80)); break;
        case 'image': $preview = $data['src'] ? '<img src="' . htmlspecialchars($data['src']) . '" style="max-width:80px;max-height:30px;">' : '(بدون تصویر)'; break;
        case 'button': $preview = '[ دکمه: ' . htmlspecialchars($data['text'] ?? '') . ' ]'; break;
        case 'services': $preview = 'نمایش ' . ($data['count'] ?? 6) . ' خدمت'; break;
        case 'products': $preview = 'نمایش ' . ($data['count'] ?? 8) . ' محصول'; break;
        case 'custom': $preview = 'HTML سفارشی (' . mb_strlen($data['html'] ?? '') . ' کاراکتر)'; break;
        case 'columns': $preview = ($data['columns'] ?? 2) . ' ستونی'; break;
        case 'divider': $preview = 'جداکننده'; break;
        default: $preview = '...'; break;
    }

    $data_json = htmlspecialchars(json_encode($data, JSON_UNESCAPED_UNICODE));

    return '<div class="block-header"><div class="block-title"><span style="display:inline-block;width:28px;height:28px;border-radius:6px;background:' . $color . ';color:#fff;text-align:center;line-height:28px;margin-left:8px;font-size:13px;"><i class="fa-solid ' . $icon . '"></i></span>' . $label . '</div></div><div class="block-content-preview" style="font-size:13px;color:#666;max-height:60px;overflow:hidden;">' . $preview . '</div><div class="block-footer"><button onclick="openEditSidebar(parseInt(this.closest(\'.block-item\').dataset.index))" title="ویرایش"><i class="fa-solid fa-pen"></i> ویرایش</button><button class="danger" onclick="removeBlock(this)" title="حذف"><i class="fa-solid fa-trash"></i> حذف</button></div><div class="block-data" style="display:none;">' . $data_json . '</div>';
}

function render_block_admin_full($block) {
    $types = get_block_types();
    $type = $block['type'] ?? 'text';
    $data = $block['data'] ?? [];
    $bt = $types[$type] ?? ['label' => $type, 'icon' => 'fa-cube', 'color' => '#888'];
    $color = $bt['color'];
    $icon = $bt['icon'];
    $label = htmlspecialchars($bt['label']);
    $content = builder_render_block_inner($block);
    $data_json = htmlspecialchars(json_encode($data, JSON_UNESCAPED_UNICODE));

    return '<div class="block-header"><div class="block-title"><span style="display:inline-block;width:28px;height:28px;border-radius:6px;background:' . $color . ';color:#fff;text-align:center;line-height:28px;margin-left:8px;font-size:13px;"><i class="fa-solid ' . $icon . '"></i></span>' . $label . '</div></div><div class="block-content-preview" style="font-size:14px;color:#333;">' . $content . '</div><div class="block-footer"><button onclick="openEditSidebar(parseInt(this.closest(\'.block-item\').dataset.index))" title="ویرایش"><i class="fa-solid fa-pen"></i> ویرایش</button><button class="danger" onclick="removeBlock(this)" title="حذف"><i class="fa-solid fa-trash"></i> حذف</button></div><div class="block-data" style="display:none;">' . $data_json . '</div>';
}
