<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>تارنگار</h1>
        <p>تازه‌ترین مطالب آموزشی و خبری کامپیوتر و فناوری</p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>تارنگار</span>
        </div>
    </div>
</div>

<section class="bakhsh">
    <div class="mohtava-container">

        <?php if (!empty($posts)): ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px,1fr)); gap:24px;">
            <?php foreach ($posts as $p): ?>
            <a href="<?= BASE_URL ?>/tarnegar/<?= htmlspecialchars($p['slug']) ?>" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p style="font-size:13px; color:#aaa; margin-bottom:8px;"><?= htmlspecialchars($p['created_at']) ?></p>
                    <p><?= htmlspecialchars(mb_substr(strip_tags($p['content']), 0, 200)) ?>...</p>
                    <div class="lnk">
                        ادامه مطلب
                        <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div style="text-align:center; margin-top:40px; display:flex; gap:8px; justify-content:center;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?= BASE_URL ?>/tarnegar/<?= $i ?>"
               style="padding:8px 16px; border-radius:8px; background:<?= $i === $current_page ? 'var(--rang-asli)' : 'var(--rang-sabz)' ?>; color:<?= $i === $current_page ? '#fff' : 'var(--rang-matn)' ?>; font-weight:700; text-decoration:none;">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center; padding:60px 0; color:#888;">
            <i class="fa-solid fa-pen" style="font-size:48px; margin-bottom:16px; color:#ddd; display:block;"></i>
            هنوز مطلبی منتشر نشده است.
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
