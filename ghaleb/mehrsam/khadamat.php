<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<!-- سرصفحه صفحه -->
<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>خدمات مهراد سام</h1>
        <p>پشتیبانی تخصصی کامپیوتر در ملارد و مارلیک</p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>خدمات</span>
        </div>
    </div>
</div>

<!-- لیست خدمات -->
<section class="bakhsh">
    <div class="mohtava-container">

        <?php if (!empty($khadamat)): ?>
        <div class="gerid-3">
            <?php foreach ($khadamat as $k): ?>
            <a href="<?= BASE_URL ?>/khadamat/<?= htmlspecialchars($k['slug']) ?>" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:<?= htmlspecialchars($k['rang'] ?? 'var(--rang-asli)') ?>;">
                        <i class="fa-solid <?= htmlspecialchars($k['icon'] ?? 'fa-tools') ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($k['onvan']) ?></h3>
                    <p><?= htmlspecialchars($k['sharh_kootah']) ?></p>
                    <div class="lnk">
                        جزئیات بیشتر
                        <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div style="text-align:center; padding:60px 0; color:#888;">
            <i class="fa-solid fa-box-open" style="font-size:48px; margin-bottom:16px; color:#ddd; display:block;"></i>
            خدماتی برای نمایش وجود ندارد.
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- CTA -->
<section style="background:var(--rang-sabz); padding:60px 0; text-align:center; border-top:1px solid var(--rang-border);">
    <div class="mohtava-container">
        <h2 style="font-size:1.5rem; margin-bottom:10px;">خدمت مورد نظر را نمی‌بینید؟</h2>
        <p style="color:#888; margin-bottom:24px;">با ما تماس بگیرید تا راهنمایی‌تان کنیم</p>
        <a href="<?= BASE_URL ?>/tamas" class="dakmeh dakmeh-asli">
            <i class="fa-solid fa-comments"></i>
            تماس با ما
        </a>
    </div>
</section>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
