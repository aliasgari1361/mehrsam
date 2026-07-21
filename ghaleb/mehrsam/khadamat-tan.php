<?php
// دریافت خدمت از متغیر سراسری
$service = $GLOBALS['khadamat_service'] ?? null;

if (!$service) {
    include MASIR_GHALEB . 'sarfaraz.php';
    ?>
    <!-- سرصفحه -->
    <div class="sarsafhe-safhe">
        <div class="mohtava-container">
            <h1>خدمت یافت نشد</h1>
            <p>این خدمت در لیست خدمات موجود نیست.</p>
            <div class="masir-nabz">
                <a href="<?= BASE_URL ?>/">خانه</a>
                <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
                <a href="<?= BASE_URL ?>/khadamat">خدمات</a>
            </div>
        </div>
    </div>

    <!-- پیام دوستانه -->
    <section class="bakhsh">
        <div class="mohtava-container" style="text-align:center; padding:60px 0;">
            <div style="display:inline-block; width:120px; height:120px; background:var(--rang-roshan); border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:24px;">
                <i class="fa-solid fa-circle-info" style="font-size:48px; color:var(--rang-asli);"></i>
            </div>
            <h2 style="font-size:1.5rem; margin-bottom:12px;">این خدمت در دسترس نیست</h2>
            <p style="color:#888; font-size:15px; line-height:1.8; max-width:500px; margin:0 auto 32px;">
                صفحه‌ای که به دنبال آن هستید در لیست خدمات فعلی وجود ندارد.
                ممکن است نام خدمت تغییر کرده باشد یا در بخش‌های دیگر سایت قرار گرفته باشد.
            </p>
            <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>/khadamat" class="dakmeh dakmeh-asli">
                    <i class="fa-solid fa-list"></i>
                    مشاهده همه خدمات
                </a>
                <a href="<?= BASE_URL ?>/tamas" class="dakmeh dakmeh-khali">
                    <i class="fa-solid fa-comments"></i>
                    تماس با ما
                </a>
            </div>
        </div>
    </section>

    <?php include MASIR_GHALEB . 'panevis.php'; ?>
    <?php
    exit;
}

$onvan_safhe  = $service['title'] . ' | ' . SITE_NAME;
$meta_sharh   = strip_tags($service['kholaseh'] ?? 'خدمات پشتیبانی کامپیوتر');
$safhe_faali  = 'khadamat';

include MASIR_GHALEB . 'sarfaraz.php';
?>

<!-- سرصفحه -->
<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($service['title']) ?></h1>
        <?php if (!empty($service['subtitle'])): ?>
            <p class="safhe-sharh" style="color:var(--rang-asli); font-weight:600;"><?= htmlspecialchars($service['subtitle']) ?></p>
        <?php endif; ?>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/khadamat">خدمات</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span><?= htmlspecialchars($service['title']) ?></span>
        </div>
    </div>
</div>

<!-- محتوای خدمت -->
<section class="bakhsh">
    <div class="mohtava-container">
        <article class="service-detail">
            <div class="service-detail-header">
                <div class="service-icon-large"><?= $service['tasvir'] ?></div>
                <h1><?= htmlspecialchars($service['title']) ?></h1>
                <?php if (!empty($service['subtitle'])): ?>
                    <p class="service-subtitle"><?= htmlspecialchars($service['subtitle']) ?></p>
                <?php endif; ?>
                <p class="service-excerpt"><?= htmlspecialchars($service['kholaseh']) ?></p>
            </div>

            <?php if (!empty($GLOBALS['khadamat_builder_content'])): ?>
            <div class="service-builder-content" style="margin:24px 0 32px;">
                <?= $GLOBALS['khadamat_builder_content'] ?>
            </div>
            <?php endif; ?>

            <div class="service-detail-content">
                <div class="service-img"><?= $service['tasvir'] ?></div>
                <div class="service-text"><?= $service['content'] ?></div>
            </div>
        </article>
    </div>
</section>

<!-- CTA -->
<section class="bakhsh cta-section">
    <div class="mohtava-container">
        <h2>نیاز به مشاوره دارید؟</h2>
        <p>تیم ما برای پاسخگویی به سوالات شما آماده است.</p>
        <a href="<?= BASE_URL ?>/tamas" class="dakmeh dakmeh-asli">
            <i class="fa-solid fa-comments"></i>
            درخواست مشاوره رایگان
        </a>
    </div>
</section>

<style>
/* ===== جزئیات خدمت ===== */
.service-detail-header {
    text-align: center;
    padding: 40px 0 20px;
    border-bottom: 1px solid var(--rang-border, #eef0f4);
    margin-bottom: 30px;
}
.service-icon-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--rang-roshan, #fff3e0);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--rang-asli);
    font-size: 40px;
    margin-bottom: 20px;
}
.service-detail-header h1 {
    font-size: 2rem;
    color: var(--rang-matn, #1a1a1a);
    margin-bottom: 8px;
}
.service-subtitle {
    color: var(--rang-asli, #FF6F00);
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 12px;
}
.service-excerpt {
    color: var(--rang-gray, #6c757d);
    font-size: 1.1rem;
    line-height: 1.8;
    max-width: 700px;
    margin: 0 auto;
}

.service-detail-content {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    padding: 30px 0;
}
.service-img {
    text-align: center;
}
.service-img svg {
    max-width: 100%;
    height: auto;
}
.service-text {
    line-height: 1.9;
    color: #444;
}
.service-text h2,
.service-text h3,
.service-text h4 {
    color: var(--rang-asli, #FF6F00);
    margin-top: 1.5rem;
}
.service-text ul,
.service-text ol {
    margin: 1rem 0;
    padding-right: 1.5rem;
}
.service-text li {
    margin: 0.5rem 0;
    line-height: 1.8;
}
.service-text p {
    margin: 1rem 0;
    line-height: 1.9;
}
.service-text strong {
    color: var(--rang-matn, #1a1a1a);
}
.service-text code {
    background: var(--rang-roshan, #fff3e0);
    color: var(--rang-asli, #FF6F00);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.9em;
    font-family: monospace;
}

@media (max-width: 768px) {
    .service-detail-content {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .service-img {
        order: -1;
    }
}
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>