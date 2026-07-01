<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p style="font-size:13px; color:#aaa;"><?= htmlspecialchars($post['created_at']) ?></p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/tarnegar">تارنگار</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span><?= htmlspecialchars($post['title']) ?></span>
        </div>
    </div>
</div>

<section class="bakhsh">
    <div class="mohtava-container">
        <div style="max-width:800px; margin:0 auto;">
            <div style="line-height:2.2; color:#444; font-size:16px;">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
            <div style="margin-top:40px; padding-top:30px; border-top:1px solid var(--rang-border); text-align:center;">
                <a href="<?= BASE_URL ?>/tarnegar" class="dakmeh dakmeh-khali">
                    <i class="fa-solid fa-arrow-left"></i>
                    بازگشت به تارنگار
                </a>
            </div>
        </div>
    </div>
</section>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
