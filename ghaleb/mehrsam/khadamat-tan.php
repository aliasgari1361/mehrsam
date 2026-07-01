<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<!-- سرصفحه -->
<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($khadamat_tan['onvan']) ?></h1>
        <p><?= htmlspecialchars($khadamat_tan['sharh_kootah']) ?></p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/khadamat">خدمات</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span><?= htmlspecialchars($khadamat_tan['onvan']) ?></span>
        </div>
    </div>
</div>

<!-- محتوای اصلی -->
<section class="bakhsh">
    <div class="mohtava-container">
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:40px; align-items:start;">

            <!-- متن خدمت -->
            <div>
                <!-- آیکون بزرگ -->
                <div style="display:flex; align-items:center; gap:20px; margin-bottom:32px; padding:28px; background:var(--rang-roshan); border-radius:var(--border-radius); border-right:4px solid var(--rang-asli);">
                    <div style="width:80px; height:80px; background:<?= htmlspecialchars($khadamat_tan['rang'] ?? 'var(--rang-asli)') ?>; border-radius:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa-solid <?= htmlspecialchars($khadamat_tan['icon'] ?? 'fa-tools') ?>" style="font-size:36px; color:#fff;"></i>
                    </div>
                    <div>
                        <h2 style="font-size:1.4rem; margin-bottom:6px;"><?= htmlspecialchars($khadamat_tan['onvan']) ?></h2>
                        <p style="color:#888; font-size:14px;"><?= htmlspecialchars($khadamat_tan['sharh_kootah']) ?></p>
                    </div>
                </div>

                <!-- توضیح کامل -->
                <?php if (!empty($khadamat_tan['sharh_kamel'])): ?>
                <div style="line-height:2; color:#444; font-size:15px;">
                    <?= $khadamat_tan['sharh_kamel'] ?>
                </div>
                <?php else: ?>
                <div style="line-height:2; color:#444; font-size:15px; background:#f9f9f9; border-radius:12px; padding:24px; text-align:center; color:#aaa;">
                    <i class="fa-solid fa-pen" style="font-size:32px; margin-bottom:12px; display:block; color:#ddd;"></i>
                    توضیحات این خدمت به‌زودی اضافه می‌شود.
                </div>
                <?php endif; ?>
            </div>

            <!-- ستون کنار -->
            <div style="position:sticky; top:90px;">
                <!-- کارت تماس -->
                <div style="background:#fff; border-radius:var(--border-radius); padding:28px; box-shadow:var(--sayeh); border:1px solid var(--rang-border); margin-bottom:20px;">
                    <h3 style="font-size:1rem; margin-bottom:20px; padding-bottom:12px; border-bottom:2px solid var(--rang-asli);">
                        <i class="fa-solid fa-headset" style="color:var(--rang-asli); margin-left:8px;"></i>
                        درخواست این خدمت
                    </h3>
                    <p style="font-size:13px; color:#888; margin-bottom:20px; line-height:1.8;">
                        برای دریافت مشاوره رایگان و درخواست این خدمت با ما تماس بگیرید.
                    </p>
                    <a href="<?= BASE_URL ?>/tamas" class="dakmeh dakmeh-asli" style="width:100%; justify-content:center; margin-bottom:10px;">
                        <i class="fa-solid fa-envelope"></i>
                        ارسال درخواست
                    </a>
                    <a href="tel:<?= SITE_TEL ?>" class="dakmeh dakmeh-khali" style="width:100%; justify-content:center;">
                        <i class="fa-solid fa-phone"></i>
                        <?= SITE_TEL ?>
                    </a>
                </div>

                <!-- سایر خدمات -->
                <?php if (!empty($khadamat_moshabe)): ?>
                <div style="background:#fff; border-radius:var(--border-radius); padding:24px; box-shadow:var(--sayeh); border:1px solid var(--rang-border);">
                    <h3 style="font-size:0.95rem; margin-bottom:16px; color:#888;">سایر خدمات</h3>
                    <?php foreach ($khadamat_moshabe as $km): ?>
                    <a href="<?= BASE_URL ?>/khadamat/<?= htmlspecialchars($km['slug']) ?>"
                       style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--rang-border); color:inherit; transition:all 0.3s;"
                       onmouseover="this.style.paddingRight='6px'; this.querySelector('span').style.color='var(--rang-asli)'"
                       onmouseout="this.style.paddingRight='0'; this.querySelector('span').style.color='inherit'">
                        <div style="width:36px; height:36px; background:var(--rang-roshan); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa-solid <?= htmlspecialchars($km['icon'] ?? 'fa-tools') ?>" style="font-size:14px; color:var(--rang-asli);"></i>
                        </div>
                        <span style="font-size:13px;"><?= htmlspecialchars($km['onvan']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<style>
    @media (max-width:768px) {
        .mohtava-container > div[style*="grid-template-columns:2fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
