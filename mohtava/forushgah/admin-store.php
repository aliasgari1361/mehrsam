<?php

require_once __DIR__ . '/mahsul-model.php';
require_once MASIR_DADE . 'bank.php';
require_once MASIR_RISH . 'haste/settings.php';
require_once MASIR_RISH . 'haste/site_settings.php';

function admin_store_route($action, $params) {
    $sub = $params[0] ?? '';
    switch ($action) {
        case 'products':
            if ($sub === 'edit' || $sub === '') {
                admin_store_product_form($params[1] ?? null);
            } elseif ($sub === 'delete') {
                admin_store_product_delete($params[1] ?? null);
            } else {
                admin_store_product_list();
            }
            break;
        case 'categories':
            if ($sub === 'edit' || $sub === '') {
                admin_store_category_form($params[1] ?? null);
            } elseif ($sub === 'delete') {
                admin_store_category_delete($params[1] ?? null);
            } else {
                admin_store_category_list();
            }
            break;
        case 'brands':
            if ($sub === 'edit' || $sub === '') {
                admin_store_brand_form($params[1] ?? null);
            } elseif ($sub === 'delete') {
                admin_store_brand_delete($params[1] ?? null);
            } else {
                admin_store_brand_list();
            }
            break;
        case 'orders':
            if ($sub === 'view') {
                admin_store_order_view($params[1] ?? null);
            } else {
                admin_store_order_list();
            }
            break;
        case 'settings':
            admin_store_settings();
            break;
        default:
            admin_store_dashboard();
            break;
    }
}

function admin_store_dashboard() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $products = $conn->query("SELECT COUNT(*) AS cnt FROM mahsulat")->fetch_assoc()['cnt'] ?? 0;
    $categories = $conn->query("SELECT COUNT(*) AS cnt FROM mahsul_dasteh")->fetch_assoc()['cnt'] ?? 0;
    $brands = 0;
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM mahsul_brand");
    if ($r) $brands = $r->fetch_assoc()['cnt'] ?? 0;
    $orders = $conn->query("SELECT COUNT(*) AS cnt FROM sefaresh")->fetch_assoc()['cnt'] ?? 0;
    $conn->close();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت فروشگاه</h3>
    <p style="color:#888; margin-bottom:24px;">داشبورد فروشگاه — مدیریت محصولات، دسته‌بندی‌ها، برندها و سفارشات</p>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:16px; margin-bottom:32px;">
        <?php
        $cards = [
            ['fa-cube', '#FF6F00', $products, 'محصولات', 'store/products'],
            ['fa-folder', '#0984E3', $categories, 'دسته‌بندی‌ها', 'store/categories'],
            ['fa-tag', '#6C5CE7', $brands, 'برندها', 'store/brands'],
            ['fa-truck', '#00B894', $orders, 'سفارشات', 'store/orders'],
            ['fa-gear', '#636e72', '', 'تنظیمات فروشگاه', 'store/settings'],
        ];
        foreach ($cards as $c): ?>
        <a href="<?= BASE_URL ?>mod/<?= $c[4] ?>" style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #eef0f4; display:flex; align-items:center; gap:16px; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.08)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,0.06)'; this.style.transform='none'">
            <div style="width:48px;height:48px;border-radius:10px;background:<?= $c[1] ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;flex-shrink:0;"><i class="fa-solid <?= $c[0] ?>"></i></div>
            <div><div style="font-size:1.5rem;font-weight:700;color:#1a1a1a;"><?= $c[2] ?></div><div style="font-size:13px;color:#888;"><?= $c[3] ?></div></div>
        </a>
        <?php endforeach; ?>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="<?= BASE_URL ?>mod/store/products/edit" class="dakmeh dakmeh-asli" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:var(--rang-asli,#FF6F00);color:#fff;font-weight:700;font-size:14px;text-decoration:none;"><i class="fa-solid fa-plus"></i> محصول جدید</a>
        <a href="<?= BASE_URL ?>mod/store/categories/edit" class="dakmeh" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:#f5f6f8;color:#333;font-weight:700;font-size:14px;text-decoration:none;"><i class="fa-solid fa-plus"></i> دسته جدید</a>
        <a href="<?= BASE_URL ?>mod/store/brands/edit" class="dakmeh" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:#f5f6f8;color:#333;font-weight:700;font-size:14px;text-decoration:none;"><i class="fa-solid fa-plus"></i> برند جدید</a>
        <a href="<?= BASE_URL ?>mod/store/settings" class="dakmeh" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:#f5f6f8;color:#333;font-weight:700;font-size:14px;text-decoration:none;"><i class="fa-solid fa-gear"></i> تنظیمات</a>
    </div>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_settings() {
    global $site_settings;
    $current = $site_settings;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_settings = [];
        foreach (['store', 'gateways'] as $section) {
            if (!empty($_POST[$section]) && is_array($_POST[$section])) {
                $new_settings[$section] = $_POST[$section];
            }
        }
        if (!empty($new_settings)) {
            save_site_settings($new_settings);
            $current = $site_settings;
            $msg = "<p style='color:green;font-weight:700;'>تنظیمات فروشگاه ذخیره شد.</p>";
        }
    }
    $gateways = $current['gateways'] ?? [
        'zarinpal' => ['enabled'=>false,'title'=>'زرین‌پال','merchant'=>'','sandbox'=>true],
        'idpay'    => ['enabled'=>false,'title'=>'آی‌دی‌پی','api_key'=>'','sandbox'=>true],
        'zibal'    => ['enabled'=>false,'title'=>'زیبال','merchant'=>'','sandbox'=>true],
    ];
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>تنظیمات فروشگاه</h3>
    <p><a href="<?= BASE_URL ?>mod/store" style="color:var(--rang-asli,#FF6F00);">&larr; بازگشت به داشبورد</a></p>
    <?= $msg ?? '' ?>
    <form method="post" style="max-width:800px;">
        <div class="settings-panel" style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:24px;margin-top:16px;">
            <h4 style="margin-bottom:16px;"><i class="fa-solid fa-store"></i> تنظیمات فروشگاه</h4>
            <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">واحد پول (نمایش)</label>
                    <input type="text" name="store[currency]" value="<?= htmlspecialchars($current['store']['currency'] ?? 'تومان') ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                </div>
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">نماد ارز</label>
                    <input type="text" name="store[currency_symbol]" value="<?= htmlspecialchars($current['store']['currency_symbol'] ?? 'تومان') ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                </div>
            </div>
            <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">آستانه ارسال رایگان</label>
                    <input type="number" name="store[free_shipping_threshold]" value="<?= (int)($current['store']['free_shipping_threshold'] ?? 0) ?>" min="0" step="10000" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                </div>
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">هزینه ارسال پیش‌فرض</label>
                    <input type="number" name="store[default_shipping_cost]" value="<?= (int)($current['store']['default_shipping_cost'] ?? 0) ?>" min="0" step="1000" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                </div>
            </div>
            <div class="form-group" style="margin-top:16px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500;">
                    <input type="checkbox" name="store[stock_management]" value="1" <?= !empty($current['store']['stock_management']) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--rang-asli,#FF6F00);"> مدیریت موجودی (کم کردن از موجودی بعد از پرداخت)
                </label>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500;">
                    <input type="checkbox" name="store[auto_confirm_orders]" value="1" <?= !empty($current['store']['auto_confirm_orders']) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--rang-asli,#FF6F00);"> تایید خودکار سفارش‌ها بعد از پرداخت
                </label>
            </div>
        </div>

        <div class="settings-panel" style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:24px;margin-top:24px;">
            <h4 style="margin-bottom:16px;"><i class="fa-solid fa-credit-card"></i> درگاه‌های پرداخت</h4>
            <p style="color:#888;font-size:13px;margin-bottom:16px;">برای هر درگاه، فعال‌سازی و مشخصات را وارد کنید. درگاه‌های غیرفعال در چک‌اوت نمایش داده نمی‌شوند.</p>
            <?php foreach ($gateways as $key => $gw): ?>
            <div class="gateway-card" style="border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-bottom:16px;background:#fafafa;">
                <h4 style="margin:0 0 16px;display:flex;align-items:center;gap:10px;">
                    <label style="position:relative;width:56px;height:28px;display:inline-block;">
                        <input type="checkbox" name="gateways[<?= $key ?>][enabled]" value="1" <?= !empty($gw['enabled']) ? 'checked' : '' ?> style="opacity:0;width:0;height:0;">
                        <span style="position:absolute;inset:0;background:#ccc;border-radius:28px;transition:.3s;cursor:pointer;"></span>
                        <span style="position:absolute;width:22px;height:22px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;box-shadow:0 2px 4px rgba(0,0,0,.2);"></span>
                    </label>
                    <?= htmlspecialchars($gw['title'] ?? $key) ?>
                </h4>
                <?php if ($key === 'zarinpal'): ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label style="display:block;margin-bottom:4px;font-weight:600;">مرچنت کد (Merchant ID)</label>
                        <input type="text" name="gateways[<?= $key ?>][merchant]" value="<?= htmlspecialchars($gw['merchant'] ?? '') ?>" dir="ltr" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500;">
                            <input type="checkbox" name="gateways[<?= $key ?>][sandbox]" value="1" <?= !empty($gw['sandbox']) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--rang-asli,#FF6F00);"> حالت سندباکس (تست)
                        </label>
                    </div>
                </div>
                <?php elseif ($key === 'idpay'): ?>
                <div class="form-group">
                    <label style="display:block;margin-bottom:4px;font-weight:600;">API Key</label>
                    <input type="text" name="gateways[<?= $key ?>][api_key]" value="<?= htmlspecialchars($gw['api_key'] ?? '') ?>" dir="ltr" placeholder="YOUR_API_KEY" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500;">
                        <input type="checkbox" name="gateways[<?= $key ?>][sandbox]" value="1" <?= !empty($gw['sandbox']) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--rang-asli,#FF6F00);"> حالت سندباکس
                    </label>
                </div>
                <?php elseif ($key === 'zibal'): ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label style="display:block;margin-bottom:4px;font-weight:600;">مرچنت کد</label>
                        <input type="text" name="gateways[<?= $key ?>][merchant]" value="<?= htmlspecialchars($gw['merchant'] ?? '') ?>" dir="ltr" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500;">
                            <input type="checkbox" name="gateways[<?= $key ?>][sandbox]" value="1" <?= !empty($gw['sandbox']) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--rang-asli,#FF6F00);"> حالت سندباکس
                        </label>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:24px;">
            <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:16px;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره تنظیمات فروشگاه</button>
        </div>
    </form>
    <style>
        .gateway-card h4 label input:checked + span { background:var(--rang-asli,#FF6F00); }
        .gateway-card h4 label input:checked ~ span:last-of-type { transform:translateX(28px); }
    </style>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_product_list() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['vaziat'])) {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("UPDATE mahsulat SET vaziat = ? WHERE id = ?");
        $stmt->bind_param("ii", $_POST['vaziat'], $_POST['product_id']);
        $stmt->execute(); $stmt->close(); $conn->close();
        redirect('mod/store/products');
        exit;
    }
    $search = $_GET['search'] ?? '';
    $dasteh_id = (int)($_GET['dasteh_id'] ?? 0);
    $brand_id = (int)($_GET['brand_id'] ?? 0);
    $products = mahsul_all($search, $dasteh_id, $brand_id);
    $categories = mahsul_categories_list();
    $brands = mahsul_brand_list();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت محصولات</h3>
    <p><a href="<?= BASE_URL ?>mod/store/products/edit" style="color:var(--rang-asli,#FF6F00);font-weight:700;">+ محصول جدید</a></p>
    <form method="get" style="display:flex;gap:8px;margin:16px 0;flex-wrap:wrap;">
        <input type="hidden" name="url" value="mod/store/products">
        <input type="text" name="search" placeholder="جستجو..." value="<?= htmlspecialchars($search) ?>" style="padding:8px 12px;border:1px solid #dde1e6;border-radius:6px;flex:1;min-width:200px;">
        <select name="dasteh_id" style="padding:8px 12px;border:1px solid #dde1e6;border-radius:6px;">
            <option value="0">همه دسته‌ها</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $dasteh_id === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['onvan']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="brand_id" style="padding:8px 12px;border:1px solid #dde1e6;border-radius:6px;">
            <option value="0">همه برندها</option>
            <?php foreach ($brands as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $brand_id === (int)$b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['onvan']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="padding:8px 16px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:6px;cursor:pointer;">جستجو</button>
    </form>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <tr style="background:#f8f9fa;">
            <th>تصویر</th><th>عنوان</th><th>قیمت</th><th>تخفیف</th><th>دسته</th><th>برند</th><th>موجودی</th><th>وضعیت</th><th>عملیات</th>
        </tr>
        <?php if (empty($products)): ?>
        <tr><td colspan="9" style="text-align:center;padding:32px;color:#888;">محصولی یافت نشد</td></tr>
        <?php else: foreach ($products as $p): ?>
        <tr>
            <td><?php if ($p['tasvir']): ?><img src="<?= htmlspecialchars($p['tasvir']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"><?php endif; ?></td>
            <td><a href="<?= BASE_URL ?>mod/store/products/edit/<?= $p['id'] ?>" style="color:var(--rang-asli,#FF6F00);"><?= htmlspecialchars($p['onvan']) ?></a></td>
            <td><?= number_format($p['gheymat']) ?></td>
            <td><?= $p['gheymat_takhfif'] ? number_format($p['gheymat_takhfif']) : '-' ?></td>
            <td><?= htmlspecialchars($p['dasteh_onvan'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['brand_onvan'] ?? '-') ?></td>
            <td><?= $p['mojood'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <select name="vaziat" onchange="this.form.submit()" style="padding:4px 8px;border-radius:4px;">
                        <option value="1" <?= $p['vaziat'] ? 'selected' : '' ?>>فعال</option>
                        <option value="0" <?= !$p['vaziat'] ? 'selected' : '' ?>>غیرفعال</option>
                    </select>
                </form>
            </td>
            <td>
                <a href="<?= BASE_URL ?>mod/store/products/edit/<?= $p['id'] ?>">ویرایش</a> |
                <a href="<?= BASE_URL ?>mod/store/products/delete/<?= $p['id'] ?>" onclick="return confirm('حذف شود؟')">حذف</a>
            </td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_product_form($id) {
    require_once __DIR__ . '/../../haste/site_settings.php';
    $product = ['onvan' => '', 'slug' => '', 'dasteh_id' => 0, 'brand_id' => 0, 'gheymat' => 0, 'gheymat_takhfif' => null, 'tozih' => '', 'virayesh' => '', 'tasvir' => '', 'mojood' => 0, 'vaziat' => 1];
    if ($id) $product = mahsul_get($id) ?: $product;
    $categories = mahsul_categories_list();
    $brands = mahsul_brand_list();
    $is_edit = (bool)$id;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'onvan' => $_POST['onvan'] ?? '',
            'slug' => $_POST['slug'] ?: trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $_POST['onvan'] ?? ''), '-'),
            'dasteh_id' => (int)($_POST['dasteh_id'] ?? 0),
            'brand_id' => (int)($_POST['brand_id'] ?? 0),
            'gheymat' => (float)str_replace(',', '', $_POST['gheymat'] ?? 0),
            'gheymat_takhfif' => $_POST['gheymat_takhfif'] !== '' ? (float)str_replace(',', '', $_POST['gheymat_takhfif']) : null,
            'tozih' => $_POST['tozih'] ?? '',
            'virayesh' => $_POST['virayesh'] ?? '',
            'tasvir' => $product['tasvir'] ?? '',
            'mojood' => (int)($_POST['mojood'] ?? 0),
            'vaziat' => (int)($_POST['vaziat'] ?? 1),
        ];
        if (!empty($_FILES['tasvir']['name']) && $_FILES['tasvir']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../../haste/site_settings.php';
            $up = upload_site_image('tasvir', 'products/');
            if (is_string($up)) $data['tasvir'] = $up;
        }
        $nid = mahsul_save($id, $data);
        redirect('mod/store/products');
        exit;
    }
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3><?= $is_edit ? 'ویرایش محصول' : 'محصول جدید' ?></h3>
    <form method="post" enctype="multipart/form-data" style="max-width:800px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">عنوان محصول</label>
                <input type="text" name="onvan" value="<?= htmlspecialchars($product['onvan']) ?>" required style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
            </div>
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">slug (نامک)</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($product['slug']) ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="خالی بذارید خودکار ساخته شود">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px;">
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">دسته‌بندی</label>
                <select name="dasteh_id" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    <option value="0">بدون دسته</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (int)$product['dasteh_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['onvan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">برند</label>
                <select name="brand_id" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    <option value="0">بدون برند</option>
                    <?php foreach ($brands as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= (int)$product['brand_id'] === (int)$b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['onvan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">موجودی</label>
                <input type="number" name="mojood" value="<?= (int)$product['mojood'] ?>" min="0" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">قیمت (تومان)</label>
                <input type="text" name="gheymat" value="<?= number_format((float)$product['gheymat']) ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;direction:ltr;">
            </div>
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">قیمت تخفیف‌خورده</label>
                <input type="text" name="gheymat_takhfif" value="<?= $product['gheymat_takhfif'] ? number_format((float)$product['gheymat_takhfif']) : '' ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;direction:ltr;" placeholder="خالی = بدون تخفیف">
            </div>
        </div>
        <div class="form-group" style="margin-top:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">تصویر محصول</label>
            <input type="file" name="tasvir" accept="image/*" style="width:100%;">
            <?php if ($product['tasvir']): ?>
            <div style="margin-top:8px;"><img src="<?= htmlspecialchars($product['tasvir']) ?>" alt="" style="max-height:80px;border-radius:8px;border:1px solid #dde1e6;"></div>
            <?php endif; ?>
        </div>
        <div class="form-group" style="margin-top:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">توضیح کوتاه</label>
            <textarea name="tozih" rows="3" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;"><?= htmlspecialchars($product['tozih']) ?></textarea>
        </div>
        <div class="form-group" style="margin-top:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">توضیحات کامل</label>
            <?php
            $edr_value = $product['virayesh'];
            $edr_name = 'virayesh';
            $edr_id = 'productDesc';
            include __DIR__ . '/../../haste/editor/editor.php';
            ?>
        </div>
        <div class="form-group" style="margin-top:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">وضعیت</label>
            <select name="vaziat" style="padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                <option value="1" <?= $product['vaziat'] ? 'selected' : '' ?>>فعال</option>
                <option value="0" <?= !$product['vaziat'] ? 'selected' : '' ?>>غیرفعال</option>
            </select>
        </div>
        <div style="margin-top:24px;">
            <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:16px;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره محصول</button>
        </div>
    </form>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_product_delete($id) {
    if ($id) mahsul_delete($id);
    redirect('mod/store/products');
}

function admin_store_category_list() {
    $cats = mahsul_categories_list();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت دسته‌بندی‌ها</h3>
    <p><a href="<?= BASE_URL ?>mod/store/categories/edit" style="color:var(--rang-asli,#FF6F00);font-weight:700;">+ دسته جدید</a></p>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <tr style="background:#f8f9fa;"><th>عنوان</th><th>slug</th><th>ترتیب</th><th>عملیات</th></tr>
        <?php if (empty($cats)): ?>
        <tr><td colspan="4" style="text-align:center;padding:32px;color:#888;">دسته‌بندی‌ای وجود ندارد</td></tr>
        <?php else: foreach ($cats as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['onvan']) ?></td>
            <td><?= htmlspecialchars($c['slug']) ?></td>
            <td><?= (int)($c['tartib'] ?? 0) ?></td>
            <td><a href="<?= BASE_URL ?>mod/store/categories/edit/<?= $c['id'] ?>">ویرایش</a> | <a href="<?= BASE_URL ?>mod/store/categories/delete/<?= $c['id'] ?>" onclick="return confirm('حذف شود؟')">حذف</a></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_category_form($id) {
    $cat = ['onvan' => '', 'slug' => '', 'tartib' => 0];
    if ($id) $cat = mahsul_category_get($id) ?: $cat;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        mahsul_category_save($id, [
            'onvan' => $_POST['onvan'] ?? '',
            'slug' => $_POST['slug'] ?: trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $_POST['onvan'] ?? ''), '-'),
            'tartib' => (int)($_POST['tartib'] ?? 0),
        ]);
        redirect('mod/store/categories');
        exit;
    }
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3><?= $id ? 'ویرایش دسته‌بندی' : 'دسته‌بندی جدید' ?></h3>
    <form method="post" style="max-width:500px;">
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">عنوان</label>
            <input type="text" name="onvan" value="<?= htmlspecialchars($cat['onvan']) ?>" required style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($cat['slug']) ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">ترتیب</label>
            <input type="number" name="tartib" value="<?= (int)($cat['tartib'] ?? 0) ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
        </div>
        <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره</button>
    </form>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_category_delete($id) {
    if ($id) mahsul_category_delete($id);
    redirect('mod/store/categories');
}

function admin_store_brand_list() {
    $brands = mahsul_brand_list();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت برندها</h3>
    <p><a href="<?= BASE_URL ?>mod/store/brands/edit" style="color:var(--rang-asli,#FF6F00);font-weight:700;">+ برند جدید</a></p>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <tr style="background:#f8f9fa;"><th>لوگو</th><th>عنوان</th><th>slug</th><th>وضعیت</th><th>عملیات</th></tr>
        <?php if (empty($brands)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:#888;">برندی وجود ندارد</td></tr>
        <?php else: foreach ($brands as $b): ?>
        <tr>
            <td><?php if ($b['logo']): ?><img src="<?= htmlspecialchars($b['logo']) ?>" alt="" style="width:40px;height:40px;object-fit:contain;border-radius:4px;"><?php endif; ?></td>
            <td><?= htmlspecialchars($b['onvan']) ?></td>
            <td><?= htmlspecialchars($b['slug']) ?></td>
            <td><?= $b['vaziat'] ? 'فعال' : 'غیرفعال' ?></td>
            <td><a href="<?= BASE_URL ?>mod/store/brands/edit/<?= $b['id'] ?>">ویرایش</a> | <a href="<?= BASE_URL ?>mod/store/brands/delete/<?= $b['id'] ?>" onclick="return confirm('حذف شود؟')">حذف</a></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_brand_form($id) {
    $brand = ['onvan' => '', 'slug' => '', 'logo' => '', 'tozih' => '', 'tartib' => 0, 'vaziat' => 1];
    if ($id) $brand = mahsul_brand_get($id) ?: $brand;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'onvan' => $_POST['onvan'] ?? '',
            'slug' => $_POST['slug'] ?: trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $_POST['onvan'] ?? ''), '-'),
            'logo' => $brand['logo'] ?? '',
            'tozih' => $_POST['tozih'] ?? '',
            'tartib' => (int)($_POST['tartib'] ?? 0),
            'vaziat' => (int)($_POST['vaziat'] ?? 1),
        ];
        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../../haste/site_settings.php';
            $up = upload_site_image('logo', 'brands/');
            if (is_string($up)) $data['logo'] = $up;
        }
        mahsul_brand_save($id, $data);
        redirect('mod/store/brands');
        exit;
    }
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3><?= $id ? 'ویرایش برند' : 'برند جدید' ?></h3>
    <form method="post" enctype="multipart/form-data" style="max-width:500px;">
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">عنوان برند</label>
            <input type="text" name="onvan" value="<?= htmlspecialchars($brand['onvan']) ?>" required style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($brand['slug']) ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">لوگو</label>
            <input type="file" name="logo" accept="image/*">
            <?php if ($brand['logo']): ?>
            <div style="margin-top:8px;"><img src="<?= htmlspecialchars($brand['logo']) ?>" alt="" style="max-height:50px;border-radius:6px;border:1px solid #dde1e6;"></div>
            <?php endif; ?>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:4px;font-weight:600;">توضیحات</label>
            <textarea name="tozih" rows="3" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;"><?= htmlspecialchars($brand['tozih']) ?></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">ترتیب</label>
                <input type="number" name="tartib" value="<?= (int)($brand['tartib'] ?? 0) ?>" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
            </div>
            <div class="form-group">
                <label style="display:block;margin-bottom:4px;font-weight:600;">وضعیت</label>
                <select name="vaziat" style="width:100%;padding:10px 12px;border:1.5px solid #dde1e6;border-radius:8px;">
                    <option value="1" <?= $brand['vaziat'] ? 'selected' : '' ?>>فعال</option>
                    <option value="0" <?= !$brand['vaziat'] ? 'selected' : '' ?>>غیرفعال</option>
                </select>
            </div>
        </div>
        <button type="submit" style="padding:12px 32px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-save"></i> ذخیره</button>
    </form>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_brand_delete($id) {
    if ($id) mahsul_brand_delete($id);
    redirect('mod/store/brands');
}

function admin_store_order_list() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $vaziat_filter = $_GET['vaziat'] ?? '';
    $sql = "SELECT id, onvan_girande, telefon_girande, majmoo_gheymat, pardakht_vaziat, vaziat, created_at FROM sefaresh";
    $params = []; $types = '';
    if ($vaziat_filter) {
        $sql .= " WHERE vaziat = ?";
        $params[] = $vaziat_filter; $types .= 's';
    }
    $sql .= " ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت سفارشات</h3>
    <form method="get" style="margin:16px 0;">
        <input type="hidden" name="url" value="mod/store/orders">
        <select name="vaziat" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #dde1e6;border-radius:6px;">
            <option value="">همه سفارشات</option>
            <option value="pending" <?= $vaziat_filter === 'pending' ? 'selected' : '' ?>>در انتظار</option>
            <option value="processing" <?= $vaziat_filter === 'processing' ? 'selected' : '' ?>>در حال پردازش</option>
            <option value="shipped" <?= $vaziat_filter === 'shipped' ? 'selected' : '' ?>>ارسال شده</option>
            <option value="delivered" <?= $vaziat_filter === 'delivered' ? 'selected' : '' ?>>تحویل شده</option>
            <option value="cancelled" <?= $vaziat_filter === 'cancelled' ? 'selected' : '' ?>>لغو شده</option>
        </select>
    </form>
    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <tr style="background:#f8f9fa;"><th>#</th><th>مشتری</th><th>مبلغ</th><th>پرداخت</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
        <?php if (empty($orders)): ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:#888;">سفارشی وجود ندارد</td></tr>
        <?php else: foreach ($orders as $o): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['onvan_girande']) ?></td>
            <td><?= number_format($o['majmoo_gheymat']) ?></td>
            <td><span style="display:inline-block;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:600;background:<?= $o['pardakht_vaziat'] === 'paid' ? '#e8f5e9' : '#fff3e0' ?>;color:<?= $o['pardakht_vaziat'] === 'paid' ? '#2e7d32' : '#e65100' ?>;"><?= $o['pardakht_vaziat'] === 'paid' ? 'پرداخت شده' : ($o['pardakht_vaziat'] === 'pending' ? 'در انتظار' : $o['pardakht_vaziat']) ?></span></td>
            <td><span style="display:inline-block;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:600;background:<?= $o['vaziat'] === 'pending' ? '#fff3e0' : ($o['vaziat'] === 'processing' ? '#e3f2fd' : ($o['vaziat'] === 'delivered' ? '#e8f5e9' : '#f5f5f5')) ?>;color:<?= $o['vaziat'] === 'pending' ? '#e65100' : ($o['vaziat'] === 'processing' ? '#1565c0' : ($o['vaziat'] === 'delivered' ? '#2e7d32' : '#757575')) ?>;"><?= $o['vaziat'] === 'pending' ? 'در انتظار' : ($o['vaziat'] === 'processing' ? 'در حال پردازش' : ($o['vaziat'] === 'shipped' ? 'ارسال شده' : ($o['vaziat'] === 'delivered' ? 'تحویل شده' : 'لغو شده'))) ?></span></td>
            <td style="font-size:12px;color:#888;"><?= $o['created_at'] ?><br><span style="color:var(--rang-asli,#FF6F00);"><?= to_jalali($o['created_at'], 'Y/m/d H:i') ?></span></td>
            <td><a href="<?= BASE_URL ?>mod/store/orders/view/<?= $o['id'] ?>">مشاهده</a></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function admin_store_order_view($id) {
    require_once __DIR__ . '/sefaresh-model.php';
    $order = sefaresh_get($id);
    $items = sefaresh_get_items($id);
    if (!$order) {
        include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
        echo "<h3>سفارش یافت نشد</h3>";
        include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaziat'])) {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("UPDATE sefaresh SET vaziat = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['vaziat'], $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        $order['vaziat'] = $_POST['vaziat'];
    }
    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>سفارش #<?= $order['id'] ?></h3>
    <p><a href="<?= BASE_URL ?>mod/store/orders" style="color:var(--rang-asli,#FF6F00);">&larr; بازگشت به لیست</a></p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:16px;">
        <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;">
            <h4 style="margin-bottom:12px;">اطلاعات مشتری</h4>
            <p><strong>نام:</strong> <?= htmlspecialchars($order['onvan_girande']) ?></p>
            <p><strong>تلفن:</strong> <?= htmlspecialchars($order['telefon_girande']) ?></p>
            <p><strong>آدرس:</strong> <?= htmlspecialchars($order['ostan']) ?> - <?= htmlspecialchars($order['shahr']) ?></p>
            <p><strong>آدرس کامل:</strong> <?= htmlspecialchars($order['adres']) ?></p>
            <?php if ($order['kode_posty']): ?><p><strong>کد پستی:</strong> <?= htmlspecialchars($order['kode_posty']) ?></p><?php endif; ?>
            <?php if ($order['tozih']): ?><p><strong>توضیحات:</strong> <?= htmlspecialchars($order['tozih']) ?></p><?php endif; ?>
        </div>
        <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;">
            <h4 style="margin-bottom:12px;">وضعیت سفارش</h4>
            <form method="post">
                <select name="vaziat" style="padding:8px 12px;border:1.5px solid #dde1e6;border-radius:8px;width:100%;margin-bottom:12px;">
                    <option value="pending" <?= $order['vaziat'] === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                    <option value="processing" <?= $order['vaziat'] === 'processing' ? 'selected' : '' ?>>در حال پردازش</option>
                    <option value="shipped" <?= $order['vaziat'] === 'shipped' ? 'selected' : '' ?>>ارسال شده</option>
                    <option value="delivered" <?= $order['vaziat'] === 'delivered' ? 'selected' : '' ?>>تحویل شده</option>
                    <option value="cancelled" <?= $order['vaziat'] === 'cancelled' ? 'selected' : '' ?>>لغو شده</option>
                </select>
                <button type="submit" style="padding:8px 16px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">بروزرسانی وضعیت</button>
            </form>
            <p style="margin-top:12px;"><strong>پرداخت:</strong> <?= $order['pardakht_vaziat'] === 'paid' ? 'پرداخت شده' : 'در انتظار' ?></p>
            <?php if ($order['pardakht_ref_id']): ?><p style="font-size:12px;color:#888;">Ref: <?= htmlspecialchars($order['pardakht_ref_id']) ?></p><?php endif; ?>
        </div>
    </div>
    <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:20px;margin-top:20px;">
        <h4 style="margin-bottom:12px;">محصولات سفارش</h4>
        <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
            <tr style="background:#f8f9fa;"><th>محصول</th><th>قیمت واحد</th><th>تعداد</th><th>جمع</th></tr>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['onvan'] ?? '') ?></td>
                <td><?= number_format($item['gheymat_vahed']) ?></td>
                <td><?= (int)$item['tedad'] ?></td>
                <td><?= number_format((int)$item['tedad'] * (float)$item['gheymat_vahed']) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight:700;">
                <td colspan="3" style="text-align:left;">جمع کل:</td>
                <td><?= number_format($order['majmoo_gheymat']) ?></td>
            </tr>
        </table>
    </div>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}
