<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

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
        <?php if (!empty($page_data['content'])): ?>
            <div class="services-grid">
                <?= $page_data['content'] ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <p>محتوای صفحه خدمات هنوز وارد نشده است.</p>
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
.khadamat-page .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 24px;
    margin-top: 8px;
}

/* ===== المان details (هر خدمت) ===== */
.services-grid > details {
    background: #fff;
    border: 1px solid var(--rang-border, #eef0f4);
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}
.services-grid > details:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    transform: translateY(-3px);
    border-color: var(--rang-asli, #FF6F00);
}
.services-grid > details[open] {
    box-shadow: 0 12px 32px rgba(0,0,0,0.1);
    border-color: var(--rang-asli, #FF6F00);
}

/* ===== summary (هدر کارت) ===== */
.services-grid > details > summary {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 24px;
    cursor: pointer;
    list-style: none;
    background: linear-gradient(135deg, #fafafa 0%, #fff 100%);
    border-bottom: 1px solid var(--rang-border, #eef0f4);
    transition: all 0.2s ease;
    outline: none;
}
.services-grid > details > summary::-webkit-details-marker { display: none; }
.services-grid > details > summary::marker { display: none; }

.services-grid > details > summary:hover {
    background: linear-gradient(135deg, var(--rang-roshan, #fff3e0) 0%, #fff 100%);
}

/* آیکون سرویس */
.service-icon {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
}
details[open] .service-icon {
    transform: scale(1.08) rotate(3deg);
}

/* متن هدر */
.services-grid > details > summary > div { flex: 1; min-width: 0; }
.services-grid > details > summary h2 {
    margin: 0 0 6px;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--rang-matn, #1a1a1a);
    line-height: 1.4;
}
.services-grid > details > summary p {
    margin: 0;
    font-size: 0.92rem;
    color: var(--rang-gray, #6c757d);
    line-height: 1.6;
}

/* فلش باز/بسته */
.services-grid > details > summary::after {
    content: '\f078';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 1rem;
    color: var(--rang-asli, #FF6F00);
    margin-right: auto;
    transition: transform 0.3s ease;
    flex-shrink: 0;
    margin-left: 16px;
}
details[open] > summary::after {
    transform: rotate(180deg);
}

/* ===== محتوای کامل (داخل details) ===== */
.services-grid > details > div:last-of-type {
    padding: 0 24px 24px;
    animation: slideDown 0.3s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.services-grid > details > div:last-of-type > *:first-child {
    margin-top: 16px;
}
.services-grid > details > div:last-of-type h3,
.services-grid > details > div:last-of-type h4 {
    color: var(--rang-asli, #FF6F00);
    margin-top: 1.2rem;
}
.services-grid > details > div:last-of-type ul,
.services-grid > details > div:last-of-type ol {
    margin: 0.8rem 0;
    padding-right: 1.5rem;
}
.services-grid > details > div:last-of-type li {
    margin: 0.4rem 0;
    line-height: 1.8;
}
.services-grid > details > div:last-of-type p {
    margin: 0.8rem 0;
    line-height: 1.9;
    color: #444;
}
.services-grid > details > div:last-of-type strong {
    color: var(--rang-matn, #1a1a1a);
}
.services-grid > details > div:last-of-type code {
    background: var(--rang-roshan, #fff3e0);
    color: var(--rang-asli, #FF6F00);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.9em;
    font-family: monospace;
}
.services-grid > details > div:last-of-type details {
    margin: 1rem 0;
    border: 1px solid var(--rang-border, #eef0f4);
    border-radius: 10px;
    background: #fafafa;
}
.services-grid > details > div:last-of-type details > summary {
    padding: 12px 16px;
    font-weight: 600;
    color: var(--rang-asli, #FF6F00);
    cursor: pointer;
    list-style: none;
}
.services-grid > details > div:last-of-type details > summary::-webkit-details-marker { display: none; }
.services-grid > details > div:last-of-type details > summary::after {
    content: '\f078';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    margin-left: 8px;
    transition: transform 0.2s;
}
details[open] > summary::after { transform: rotate(180deg); }
.services-grid > details > div:last-of-type details > div {
    padding: 0 16px 16px;
    animation: slideDown 0.25s ease;
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
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    .services-grid > details > summary {
        padding: 20px;
    }
    .service-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
}
@media (max-width: 600px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
    .services-grid > details > summary {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 14px;
        padding: 20px 16px;
    }
    .services-grid > details > summary::after {
        position: absolute;
        top: 16px;
        left: 16px;
        margin: 0;
    }
    .service-icon {
        width: 56px;
        height: 56px;
        font-size: 24px;
    }
    .services-grid > details > div:last-of-type {
        padding: 0 16px 20px;
    }
}

/* انیمیشن ظریف برای summary در حالت open */
details[open] > summary {
    border-bottom-color: var(--rang-asli, #FF6F00);
}
details[open] > summary h2 {
    color: var(--rang-asli, #FF6F00);
}
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>