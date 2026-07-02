<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($mahsul['onvan']) ?></h1>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/forushgah">فروشگاه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span><?= htmlspecialchars($mahsul['onvan']) ?></span>
        </div>
    </div>
</div>

<section class="bakhsh">
    <div class="mohtava-container">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:start;">

            <div style="width:100%; height:360px; background:var(--rang-sabz); border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:80px; color:#ccc;">
                <i class="fa-solid fa-box-open"></i>
            </div>

            <div>
                <h2 style="font-size:1.6rem; margin-bottom:16px;"><?= htmlspecialchars($mahsul['onvan']) ?></h2>

                <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
                    <span style="font-size:1.8rem; font-weight:700; color:var(--rang-asli);">
                        <?= number_format($gheymat) ?> تومان
                    </span>
                    <?php if ($mahsul['gheymat_takhfif']): ?>
                    <span style="font-size:1rem; color:#aaa; text-decoration:line-through;">
                        <?= number_format($mahsul['gheymat']) ?> تومان
                    </span>
                    <span style="background:#e8f5e9; color:#2e7d32; padding:4px 12px; border-radius:6px; font-size:13px; font-weight:700;">
                        <?= round((1 - $mahsul['gheymat_takhfif'] / $mahsul['gheymat']) * 100) ?>% تخفیف
                    </span>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom:24px;">
                    <span style="font-size:13px; color:#888;">وضعیت:</span>
                    <span style="font-weight:600; color:<?= $mahsul['mojood'] > 0 ? '#2e7d32' : '#c62828' ?>; margin-right:6px;">
                        <?= $mahsul['mojood'] > 0 ? 'موجود' : 'ناموجود' ?>
                    </span>
                </div>

                <?php if (!empty($mahsul['tozih'])): ?>
                <div style="line-height:2; color:#444; margin-bottom:24px;">
                    <?= nl2br(htmlspecialchars($mahsul['tozih'])) ?>
                </div>
                <?php endif; ?>

                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <button class="dakmeh dakmeh-asli add-to-cart" style="border:none; cursor:pointer;" data-mahsul="<?= $mahsul['id'] ?>">
                        <i class="fa-solid fa-cart-plus"></i>
                        <span class="btn-text">افزودن به سبد خرید</span>
                    </button>
                    <a href="<?= BASE_URL ?>/forushgah" class="dakmeh dakmeh-khali">
                        <i class="fa-solid fa-arrow-left"></i>
                        بازگشت
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.querySelector('.add-to-cart')?.addEventListener('click', function() {
    const btn = this;
    const mahsulId = btn.dataset.mahsul;
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال افزودن...';

    const formData = new FormData();
    formData.append('mahsul_id', mahsulId);
    formData.append('tedad', 1);

    fetch('<?= BASE_URL ?>/forushgah/sabad/add', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> اضافه شد';
                btn.classList.remove('dakmeh-asli');
                btn.classList.add('dakmeh-khali');
                document.querySelectorAll('.cart-badge').forEach(b => b.textContent = data.count);
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.add('dakmeh-asli');
                    btn.classList.remove('dakmeh-khali');
                    btn.disabled = false;
                }, 1500);
            } else {
                alert(data.message || 'خطا در افزودن به سبد');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(() => {
            alert('خطای ارتباطی');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
});
</script>

<style>
    @media (max-width:768px) {
        .mohtava-container > div[style*="grid-template-columns:1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
