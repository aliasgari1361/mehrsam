<?php require_once MASIR_RISH . 'haste/tanzimat.php'; include MASIR_GHALEB . 'sarfaraz.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;font-size:13px;color:#888;margin-top:8px;">
            <span><i class="fa-regular fa-clock"></i> <?= to_jalali($post['created_at'], 'Y/m/d') ?></span>
            <?php if (!empty($post['categories'])): foreach ($post['categories'] as $c): ?>
                <span style="color:var(--rang-asli);"><?= htmlspecialchars($c['title']) ?></span>
            <?php endforeach; endif; ?>
        </div>
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
        <article class="post-article">
            <?php if (!empty($post['tasvir'])): ?>
                <div class="post-cover"><?= $post['tasvir'] ?></div>
            <?php endif; ?>
            <div class="post-content">
                <?php if (!empty($builder_content)): ?>
                    <?= $builder_content ?>
                <?php else: ?>
                    <?= $post['content'] ?>
                <?php endif; ?>
            </div>
            </div>

            <div class="post-share">
                <span>اشتراک‌گذاری:</span>
                <a href="https://t.me/share/url?url=<?= urlencode(BASE_URL . 'tarnegar/' . $post['slug']) ?>" target="_blank" title="تلگرام"><i class="fa-brands fa-telegram"></i></a>
                <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' - ' . BASE_URL . 'tarnegar/' . $post['slug']) ?>" target="_blank" title="واتساپ"><i class="fa-brands fa-whatsapp"></i></a>
                <a href="mailto:?subject=<?= urlencode($post['title']) ?>&body=<?= urlencode(BASE_URL . 'tarnegar/' . $post['slug']) ?>" title="ایمیل"><i class="fa-solid fa-envelope"></i></a>
            </div>
        </article>

        <?php if (!empty($related)): ?>
            <div class="related-section">
                <h2>مطالب مرتبط</h2>
                <div class="related-grid">
                    <?php foreach ($related as $r): ?>
                        <a href="<?= BASE_URL ?>tarnegar/<?= htmlspecialchars($r['slug']) ?>" class="blog-card">
                            <div class="blog-img">
                                <?php if (!empty($r['tasvir'])): ?>
                                    <?= $r['tasvir'] ?>
                                <?php else: ?>
                                    <div class="blog-img-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="blog-body">
                                <h3><?= htmlspecialchars($r['title']) ?></h3>
                                <span class="blog-date"><?= to_jalali($r['created_at'], 'Y/m/d') ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.post-article { max-width:780px; margin:0 auto; }
.post-cover { margin-bottom:30px; border-radius:16px; overflow:hidden; background:var(--rang-sabz); text-align:center; }
.post-cover svg { max-width:100%; height:auto; display:block; margin:0 auto; }
.post-content { line-height:2.2; color:#444; font-size:16px; }
.post-content h2 { font-size:1.5rem; color:var(--rang-matn); margin:32px 0 12px; }
.post-content h3 { font-size:1.25rem; color:var(--rang-matn); margin:28px 0 10px; }
.post-content h4 { font-size:1.1rem; color:var(--rang-asli); margin:24px 0 8px; }
.post-content p { margin:1rem 0; }
.post-content ul, .post-content ol { margin:1rem 0; padding-right:1.5rem; }
.post-content li { margin:0.4rem 0; }
.post-content img { max-width:100%; height:auto; border-radius:10px; margin:1rem 0; }
.post-content a { color:var(--rang-asli); text-decoration:underline; }
.post-content blockquote { border-right:4px solid var(--rang-asli); padding:12px 20px; margin:1.5rem 0; background:var(--rang-roshan); border-radius:8px; color:#555; }
.post-content pre { background:#1e1e2e; color:#cdd6f4; padding:16px; border-radius:10px; overflow-x:auto; direction:ltr; text-align:left; font-size:13px; line-height:1.5; margin:1rem 0; }
.post-content code { background:#f0f0f0; padding:2px 6px; border-radius:4px; font-size:13px; direction:ltr; }
.post-content pre code { background:none; padding:0; }
.post-content table { border-collapse:collapse; width:100%; margin:1rem 0; }
.post-content td, .post-content th { border:1px solid var(--rang-border); padding:8px 12px; text-align:right; }
.post-content th { background:var(--rang-sabz); }
.post-content hr { border:none; border-top:2px dashed var(--rang-border); margin:2rem 0; }

.post-share { display:flex; align-items:center; gap:12px; justify-content:center; margin-top:48px; padding-top:30px; border-top:2px solid var(--rang-border); }
.post-share span { font-size:14px; color:#888; }
.post-share a { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:var(--rang-sabz); color:#888; text-decoration:none; transition:all .3s; font-size:18px; }
.post-share a:hover { background:var(--rang-asli); color:#fff; }

.related-section { margin-top:60px; padding-top:40px; border-top:2px solid var(--rang-border); }
.related-section h2 { text-align:center; font-size:1.5rem; margin-bottom:30px; color:var(--rang-matn); }
.related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }

@media(max-width:768px) {
    .related-grid { grid-template-columns:1fr; }
}
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
