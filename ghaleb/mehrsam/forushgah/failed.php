<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<div class="sarsafhe-safhe" style="min-height:60vh; display:flex; align-items:center; justify-content:center;">
    <div class="mohtava-container" style="text-align:center; max-width:500px;">
        <div style="width:120px; height:120px; background:#fdecea; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; font-size:56px; color:#c62828;">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <h1 style="font-size:2rem; color:var(--rang-matn); margin-bottom:12px;">پرداخت ناموفق</h1>
        <p style="color:#666; margin-bottom:16px; font-size:1.1rem;">پرداخت شما انجام نشد. مبلغی از حساب شما کسر نشده است.</p>
        
        <?php if (isset($_SESSION['payment_error'])): ?>
            <div style="background:#fdecea; border:1px solid #f5c6cb; color:#c62828; padding:16px; border-radius:8px; margin-bottom:24px; text-align:right;">
                <?= htmlspecialchars($_SESSION['payment_error']) ?>
            </div>
            <?php unset($_SESSION['payment_error']); ?>
        <?php endif; ?>
        
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/forushgah/checkout" class="dakmeh dakmeh-asli" style="padding:14px 28px; font-size:1rem;">
                <i class="fa-solid fa-rotate-left"></i>
                تلاش مجدد
            </a>
            <a href="<?= BASE_URL ?>/forushgah/sabad" class="dakmeh dakmeh-khali" style="padding:14px 28px; font-size:1rem;">
                <i class="fa-solid fa-cart-shopping"></i>
                بازگشت به سبد
            </a>
        </div>

        <p style="margin-top:24px; font-size:0.9rem; color:#888;">
            اگر مشکل ادامه دارد با پشتیبانی تماس بگیرید:<br>
            <a href="tel:<?= SITE_TEL ?>" style="color:var(--rang-asli); font-weight:500;"><?= SITE_TEL ?></a>
        </p>
    </div>
</div>

<?php include MASIR_GHALEB . 'panevis.php'; ?>