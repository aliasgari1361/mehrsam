<?php include MASIR_GHALEB . 'sarfaraz.php'; ?>

<!-- سرصفحه صفحه -->
<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($page_data['title'] ?? 'خدمات مهراد سام') ?></h1>
        <?php if (!empty($page_data['meta_description'])): ?>
            <p class="safhe-sharh"><?= htmlspecialchars($page_data['meta_description']) ?></p>
        <?php endif; ?>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>خدمات</span>
        </div>
    </div>
</div>

<!-- محتوای صفحه خدمات -->
<section class="bakhsh khadamat-page">
    <div class="mohtava-container">
        <?php if (!empty($khadamat_list)): ?>
            <div class="services-grid">
                <?php foreach ($khadamat_list as $svc): ?>
                    <a class="service-card" href="<?= BASE_URL ?>khadamat/<?= htmlspecialchars($svc['slug']) ?>">
                        <div class="service-header">
                            <div class="service-icon">
                                <?= $svc['tasvir'] ?>
                            </div>
                            <div class="service-header-text">
                                <h2><?= htmlspecialchars($svc['title']) ?></h2>
                                <?php if (!empty($svc['subtitle'])): ?>
                                    <span class="service-sub"><?= htmlspecialchars($svc['subtitle']) ?></span>
                                <?php endif; ?>
                                <p><?= htmlspecialchars($svc['kholaseh']) ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <p>خدمتی ثبت نشده است.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="bakhsh cta-section">
    <div class="mohtava-container">
        <h2>خدمت مورد نظر را نمی‌بینید؟</h2>
        <p>با ما تماس بگیرید تا راهنمایی‌تان کنیم</p>
        <a href="<?= BASE_URL ?>/tamas" class="dakmeh dakmeh-asli">
            <i class="fa-solid fa-comments"></i>
            تماس با ما
        </a>
    </div>
</section>

<style>
/* ===== صفحه خدمات ===== */
.khadamat-page {
    padding-bottom: 0;
}
.khadamat-page .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 18px;
    margin-top: 8px;
    align-content: start;
}

/* ===== کارت خدمت (لینک مستقیم) ===== */
.khadamat-page .services-grid > a.service-card {
    display: flex;
    flex-direction: column;
    background: var(--rang-roshan, #fff3e0);
    border: 1px solid var(--rang-border, #eef0f4);
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}
.khadamat-page .services-grid > a.service-card:hover {
    background: var(--rang-asli, #FF6F00);
    border-color: var(--rang-asli, #FF6F00);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}

/* هدر کارت */
.khadamat-page .services-grid > a.service-card > .service-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    flex: 1;
}
.khadamat-page .services-grid > a.service-card:hover .service-header {
    background: transparent;
    color: #fff;
}


/* آیکون سرویس */
.service-icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--rang-roshan, #fff3e0);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--rang-asli);
    font-size: 20px;
    transition: transform 0.3s ease;
}
.khadamat-page .services-grid > a.service-card:hover .service-icon {
    transform: scale(1.08);
    background: var(--rang-asli, #FF6F00);
    color: #fff;
}

/* متن هدر */
.service-header > .service-header-text { flex: 1; min-width: 0; }
.service-header h2 {
    margin: 0 0 3px;
    font-size: 1rem;
    font-weight: 700;
    color: var(--rang-matn, #1a1a1a);
    line-height: 1.35;
}
.service-header p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--rang-gray, #6c757d);
    line-height: 1.5;
}
.khadamat-page .services-grid > a.service-card:hover .service-header h2,
.khadamat-page .services-grid > a.service-card:hover .service-header p {
    color: #fff;
}

/* زیرنویس (خط اضافه) */
.service-sub {
    display: inline-block;
    font-size: 0.8rem;
    color: var(--rang-tira, #E65100);
    font-weight: 600;
    margin-bottom: 3px;
}
.khadamat-page .services-grid > a.service-card:hover .service-header h2,
.khadamat-page .services-grid > a.service-card:hover .service-header p,
.khadamat-page .services-grid > a.service-card:hover .service-sub {
    color: #fff;
}

/* ===== حالت خالی ===== */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #888;
}
.empty-state i {
    font-size: 56px;
    margin-bottom: 20px;
    color: #ddd;
    display: block;
}

/* ===== CTA ===== */
.cta-section {
    background: var(--rang-sabz, #f8f9fa);
    border-top: 1px solid var(--rang-border, #eef0f4);
    padding: 60px 0;
    margin-top: 40px;
}
.cta-section h2 {
    font-size: 1.6rem;
    margin-bottom: 10px;
    color: var(--rang-matn, #1a1a1a);
}
.cta-section p {
    color: #888;
    margin-bottom: 24px;
}

/* ===== ریسپانسیو ===== */
@media (max-width: 992px) {
    .services-grid {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }
    .khadamat-page .services-grid > a.service-card > .service-header {
        padding: 14px 16px;
    }
    .service-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
}
@media (max-width: 600px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>