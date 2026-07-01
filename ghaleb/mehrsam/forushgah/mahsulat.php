<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>فروشگاه</h1>
        <p>لپ‌تاپ، کیس، مودم و قطعات کامپیوتر</p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>فروشگاه</span>
        </div>
    </div>
</div>

<section class="bakhsh">
    <div class="mohtava-container">

        <?php if (!empty($dasteha)): ?>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:32px; justify-content:center;">
            <a href="<?= BASE_URL ?>/forushgah"
               style="padding:8px 20px; border-radius:8px; background:<?= !$dasteh_slug ? 'var(--rang-asli)' : 'var(--rang-sabz)' ?>; color:<?= !$dasteh_slug ? '#fff' : 'var(--rang-matn)' ?>; font-weight:600; text-decoration:none;">
                همه
            </a>
            <?php foreach ($dasteha as $d): ?>
            <a href="<?= BASE_URL ?>/forushgah/dasteh/<?= htmlspecialchars($d['slug']) ?>"
               style="padding:8px 20px; border-radius:8px; background:<?= ($dasteh_slug ?? '') === $d['slug'] ? 'var(--rang-asli)' : 'var(--rang-sabz)' ?>; color:<?= ($dasteh_slug ?? '') === $d['slug'] ? '#fff' : 'var(--rang-matn)' ?>; font-weight:600; text-decoration:none;">
                <?= htmlspecialchars($d['onvan']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($mahsulat)): ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:20px;">
            <?php foreach ($mahsulat as $m): ?>
            <?php $gheymat = $m['gheymat_takhfif'] ?: $m['gheymat']; ?>
            <a href="<?= BASE_URL ?>/forushgah/<?= htmlspecialchars($m['slug']) ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 12px rgba(0,0,0,0.08); border:1px solid var(--rang-border); transition:all 0.3s; text-align:center; height:100%;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.12)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 12px rgba(0,0,0,0.08)'">

                    <div style="width:100%; height:160px; background:var(--rang-sabz); border-radius:8px; display:flex; align-items:center; justify-content:center; margin-bottom:16px; font-size:40px; color:#ccc;">
                        <i class="fa-solid fa-box-open"></i>
                    </div>

                    <h3 style="font-size:0.95rem; margin-bottom:8px; text-align:right;"><?= htmlspecialchars($m['onvan']) ?></h3>

                    <div style="display:flex; align-items:center; gap:8px; justify-content:flex-start;">
                        <span style="font-size:1.1rem; font-weight:700; color:var(--rang-asli);">
                            <?= number_format($gheymat) ?> تومان
                        </span>
                        <?php if ($m['gheymat_takhfif']): ?>
                        <span style="font-size:12px; color:#aaa; text-decoration:line-through;">
                            <?= number_format($m['gheymat']) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div style="text-align:center; margin-top:40px; display:flex; gap:8px; justify-content:center;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?= BASE_URL ?>/forushgah/<?= $dasteh_slug ? 'dasteh/'.$dasteh_slug.'/' : '' ?><?= $i ?>"
               style="padding:8px 16px; border-radius:8px; background:<?= $i === $current_page ? 'var(--rang-asli)' : 'var(--rang-sabz)' ?>; color:<?= $i === $current_page ? '#fff' : 'var(--rang-matn)' ?>; font-weight:700; text-decoration:none;">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center; padding:60px 0; color:#888;">
            <i class="fa-solid fa-store" style="font-size:48px; margin-bottom:16px; color:#ddd; display:block;"></i>
            هنوز محصولی برای نمایش وجود ندارد.
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
