<?php include MASIR_GHALEB . 'sarfaraz.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>تسویه حساب</h1>
        <p>اطلاعات ارسال و پرداخت را تکمیل کنید</p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/forushgah">فروشگاه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/forushgah/sabad">سبد خرید</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>تسویه حساب</span>
        </div>
    </div>
</div>

<section class="bakhsh">
    <div class="mohtava-container">
        <div style="display:grid; grid-template-columns: 1fr 420px; gap:32px;">
            <!-- فرم اطلاعات -->
            <div>
                <h2 style="margin-bottom:20px; font-size:1.3rem; color:var(--rang-matn); display:flex; align-items:center; gap:10px;">
                    <i class="fa-solid fa-user-circle" style="color:var(--rang-asli);"></i>
                    اطلاعات گیرنده
                </h2>

                <?php if (!empty($_SESSION['checkout_errors'])): ?>
                    <div style="background:#fdecea; border:1px solid #f5c6cb; color:#c62828; padding:16px; border-radius:8px; margin-bottom:24px;">
                        <ul style="margin:0; padding-right:20px;">
                            <?php foreach ($_SESSION['checkout_errors'] as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['checkout_errors']); ?>
                <?php endif; ?>

                <form method="POST" id="checkout-form" style="background:#fff; padding:24px; border-radius:12px; border:1px solid var(--rang-border);">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">نام گیرنده <span style="color:#c62828;">*</span></label>
                            <input type="text" name="onvan_girande" required value="<?= htmlspecialchars($user_info['onvan_girande'] ?? $user_info['username'] ?? '') ?>" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">تلفن گیرنده <span style="color:#c62828;">*</span></label>
                            <input type="tel" name="telefon_girande" required value="<?= htmlspecialchars($user_info['telefon'] ?? '') ?>" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">استان <span style="color:#c62828;">*</span></label>
                            <select name="ostan" required style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem;">
                                <option value="">انتخاب استان</option>
                                <option value="تهران" <?= ($user_info['ostan'] ?? '') === 'تهران' ? 'selected' : '' ?>>تهران</option>
                                <option value="البرز" <?= ($user_info['ostan'] ?? '') === 'البرز' ? 'selected' : '' ?>>البرز</option>
                                <option value="اصفهان" <?= ($user_info['ostan'] ?? '') === 'اصفهان' ? 'selected' : '' ?>>اصفهان</option>
                                <option value="فارس" <?= ($user_info['ostan'] ?? '') === 'فارس' ? 'selected' : '' ?>>فارس</option>
                                <option value="خراسان رضوی" <?= ($user_info['ostan'] ?? '') === 'خراسان رضوی' ? 'selected' : '' ?>>خراسان رضوی</option>
                                <option value="سایر" <?= ($user_info['ostan'] ?? '') === 'سایر' ? 'selected' : '' ?>>سایر</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">شهر <span style="color:#c62828;">*</span></label>
                            <input type="text" name="shahr" required value="<?= htmlspecialchars($user_info['shahr'] ?? '') ?>" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem;">
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">آدرس دقیق <span style="color:#c62828;">*</span></label>
                        <textarea name="adres" required rows="3" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem; resize:vertical;"><?= htmlspecialchars($user_info['adres'] ?? '') ?></textarea>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">کد پستی</label>
                            <input type="text" name="kode_posty" value="<?= htmlspecialchars($user_info['kode_posty'] ?? '') ?>" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">روش ارسال</label>
                            <select name="post_type" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem;">
                                <option value="pishaz" <?= ($user_info['post_type'] ?? '') === 'pishaz' ? 'selected' : '' ?>>پیشتاز (۴۵,۰۰۰ تومان)</option>
                                <option value="post" <?= ($user_info['post_type'] ?? '') === 'post' ? 'selected' : '' ?>>پست پیشتاز ارزان (۲۵,۰۰۰ تومان)</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:24px;">
                        <label style="display:block; margin-bottom:6px; font-weight:500; color:var(--rang-matn);">توضیحات سفارش</label>
                        <textarea name="tozih" rows="2" style="width:100%; padding:12px; border:1px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem; resize:vertical;"><?= htmlspecialchars($user_info['tozih'] ?? '') ?></textarea>
                    </div>

                    <?php if (!empty($gateways)): ?>
                    <div style="margin-bottom:24px;">
                        <label style="display:block; margin-bottom:10px; font-weight:500; color:var(--rang-matn);">درگاه پرداخت</label>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <?php $first = true; foreach ($gateways as $key => $gw): ?>
                            <label class="gateway-option" style="display:flex; align-items:center; gap:12px; padding:14px 16px; border:2px solid <?= $first ? 'var(--rang-asli)' : 'var(--rang-border)' ?>; border-radius:10px; cursor:pointer; background:<?= $first ? 'var(--rang-roshan)' : '#fff' ?>; transition:all .2s;">
                                <input type="radio" name="gateway" value="<?= $key ?>" <?= $first ? 'checked' : '' ?> style="accent-color:var(--rang-asli); width:18px; height:18px;">
                                <span style="font-weight:600; color:var(--rang-matn);"><?= htmlspecialchars($gw->getTitle()) ?></span>
                                <?php if ($gw->sandbox): ?>
                                <span style="font-size:0.75rem; background:#fff3cd; color:#856404; padding:2px 8px; border-radius:10px;">سندباکس</span>
                                <?php endif; ?>
                            </label>
                            <?php $first = false; endforeach; ?>
                        </div>
                    </div>
                    <?php elseif (empty($gateways)): ?>
                    <div style="margin-bottom:24px; padding:16px; background:#fdecea; border:1px solid #f5c6cb; border-radius:10px; color:#c62828; font-size:0.9rem;">
                        در حال حاضر هیچ درگاه پرداختی فعال نیست. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.
                    </div>
                    <?php endif; ?>

                    <button type="submit" id="pay-btn" class="dakmeh dakmeh-asli" style="width:100%; font-size:1.1rem; padding:16px; display:flex; align-items:center; justify-content:center; gap:10px;" <?= empty($gateways) ? 'disabled' : '' ?>>
                        <i class="fa-solid fa-lock"></i>
                        پرداخت امن
                    </button>
                </form>
            </div>

            <!-- خلاصه سفارش -->
            <div>
                <div style="background:#fff; border-radius:12px; border:1px solid var(--rang-border); padding:24px; position:sticky; top:24px;">
                    <h3 style="margin-bottom:20px; font-size:1.2rem; color:var(--rang-matn); display:flex; align-items:center; justify-content:space-between;">
                        خلاصه سفارش
                        <span style="font-size:0.85rem; color:#888;"><?= count($items) ?> قلم</span>
                    </h3>

                    <div style="max-height:300px; overflow-y:auto; margin-bottom:20px; border-bottom:1px solid var(--rang-border); padding-bottom:16px;">
                        <?php foreach ($items as $item): ?>
                            <div style="display:flex; gap:12px; padding:12px 0; border-bottom:1px solid var(--rang-border);">
                                <div style="width:60px; height:60px; background:var(--rang-sabz); border-radius:8px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:20px; color:#ccc;">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-weight:500; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= htmlspecialchars($item['onvan']) ?>
                                    </div>
                                    <div style="font-size:0.8rem; color:#888; margin-top:4px;">
                                        <?= number_format($item['gheymat_akhar']) ?> تومان × <?= $item['tedad'] ?>
                                    </div>
                                </div>
                                <div style="text-align:left; font-weight:600; color:var(--rang-matn); white-space:nowrap;">
                                    <?= number_format($item['majmoo']) ?> تومان
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-bottom:12px; display:flex; justify-content:space-between; font-size:0.95rem;">
                        <span>مبلغ محصولات</span>
                        <span><?= number_format($total) ?> تومان</span>
                    </div>

                    <div style="margin-bottom:12px; display:flex; justify-content:space-between; font-size:0.95rem;">
                        <span>هزینه ارسال</span>
                        <span id="shipping-cost"><?= number_format(45000) ?> تومان</span>
                    </div>

                    <div style="margin-bottom:12px; display:flex; justify-content:space-between; font-size:0.95rem;">
                        <span>مبلغ قابل پرداخت</span>
                        <span id="payable-amount"><?= number_format($total + 45000) ?> تومان</span>
                    </div>

                    <div style="display:flex; justify-content:space-between; padding-top:16px; border-top:2px solid var(--rang-border); font-size:1.1rem; font-weight:700; color:var(--rang-asli);">
                        <span>مجموع نهایی</span>
                        <span id="final-total"><?= number_format($total + 45000) ?> تومان</span>
                    </div>

                    <p style="margin-top:16px; font-size:0.8rem; color:#888; text-align:center;">
                        <i class="fa-solid fa-shield-halved"></i>
                        پرداخت امن توسط درگاه زرین‌پال
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const payBtn = document.getElementById('pay-btn');
    const postType = document.querySelector('select[name="post_type"]');
    const shippingCostEl = document.getElementById('shipping-cost');
    const payableAmountEl = document.getElementById('payable-amount');
    const finalTotalEl = document.getElementById('final-total');

    const productTotal = <?= $total ?>;
    
    const shippingCosts = {
        'pishaz': 45000,
        'post': 25000
    };

    function updateTotals() {
        const cost = shippingCosts[postType.value] || 45000;
        const payable = productTotal + cost;
        
        shippingCostEl.textContent = numberFormat(cost) + ' تومان';
        payableAmountEl.textContent = numberFormat(payable) + ' تومان';
        finalTotalEl.textContent = numberFormat(payable) + ' تومان';
    }

    function numberFormat(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    postType.addEventListener('change', updateTotals);

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال اتصال به درگاه...';

            fetch('<?= BASE_URL ?>/forushgah/checkout', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.message || 'خطا در اتصال به درگاه پرداخت');
                    payBtn.disabled = false;
                    payBtn.innerHTML = '<i class="fa-solid fa-lock"></i> پرداخت امن';
                }
            })
            .catch(() => {
                alert('خطای شبکه. لطفاً مجدد تلاش کنید.');
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fa-solid fa-lock"></i> پرداخت امن';
            });
        });
});
</script>

<?php include MASIR_GHALEB . 'panevis.php'; ?>