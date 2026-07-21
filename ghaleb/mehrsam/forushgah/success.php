<?php include MASIR_GHALEB . 'sarfaraz.php'; ?>

<div class="sarsafhe-safhe" style="min-height:60vh; display:flex; align-items:center; justify-content:center;">
    <div class="mohtava-container" style="text-align:center; max-width:500px;">
        <div style="width:120px; height:120px; background:#e8f5e9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; font-size:56px; color:#2e7d32;">
            <i class="fa-solid fa-check"></i>
        </div>
        <h1 style="font-size:2rem; color:var(--rang-matn); margin-bottom:12px;">پرداخت با موفقیت انجام شد</h1>
        <p style="color:#666; margin-bottom:24px; font-size:1.1rem;">سفارش شما ثبت گردید و در حال پردازش است</p>
        
        <div style="background:#fff; border:1px solid var(--rang-border); border-radius:12px; padding:24px; margin-bottom:24px; text-align:right;">
            <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--rang-border);">
                <span style="color:#666;">شماره پیگیری</span>
                <span style="font-weight:600; color:var(--rang-asli); font-family:monospace; font-size:1.1rem;"><?= htmlspecialchars($ref_id) ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:12px 0;">
                <span style="color:#666;">وضعیت</span>
                <span style="font-weight:600; color:#2e7d32;">تایید شده</span>
            </div>
        </div>

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/karbar/sefareshat" class="dakmeh dakmeh-asli" style="padding:14px 28px; font-size:1rem;">
                <i class="fa-solid fa-box"></i>
                مشاهده سفارشات
            </a>
            <a href="<?= BASE_URL ?>/forushgah" class="dakmeh dakmeh-khali" style="padding:14px 28px; font-size:1rem;">
                <i class="fa-solid fa-store"></i>
                ادامه خرید
            </a>
        </div>
    </div>
</div>

<?php include MASIR_GHALEB . 'panevis.php'; ?>