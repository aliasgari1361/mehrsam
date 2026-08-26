<?php include MASIR_GHALEB . 'sarfaraz.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1><?= htmlspecialchars($page_data['title'] ?? 'تارنگار') ?></h1>
        <?php if (!empty($categories)): ?>
            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:16px;">
                <a href="<?= BASE_URL ?>tarnegar" style="display:inline-block;padding:5px 14px;border-radius:20px;font-size:13px;background:var(--rang-asli);color:#fff;text-decoration:none;">همه</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= BASE_URL ?>tarnegar?cat=<?= urlencode($cat['slug']) ?>" style="display:inline-block;padding:5px 14px;border-radius:20px;font-size:13px;background:var(--rang-roshan);color:var(--rang-asli);text-decoration:none;"><?= htmlspecialchars($cat['title']) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>تارنگار</span>
        </div>
    </div>
</div>

<section class="bakhsh blog-listing">
    <div class="mohtava-container">
        <?php if (!empty($builder_content)): ?>
            <div class="archive-builder-content" style="margin:0 0 32px;">
                <?= $builder_content ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
            <div class="blog-grid">
                <?php foreach ($posts as $p): ?>
                    <a class="blog-card" href="<?= BASE_URL ?>tarnegar/<?= htmlspecialchars($p['slug']) ?>">
                        <div class="blog-img">
                            <?php if (!empty($p['tasvir'])): ?>
                                <?= $p['tasvir'] ?>
                            <?php else: ?>
                                <div class="blog-img-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="blog-body">
                            <?php if (!empty($p['categories'])): ?>
                                <span class="blog-cat"><?= htmlspecialchars($p['categories'][0]['title']) ?></span>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($p['title']) ?></h3>
                            <span class="blog-date"><i class="fa-regular fa-clock"></i> <?= to_jalali($p['created_at'], 'Y/m/d') ?></span>
                            <p><?= htmlspecialchars(mb_substr(strip_tags($p['kholaseh'] ?: $p['content']), 0, 150)) ?></p>
                            <span class="blog-more">ادامه مطلب <i class="fa-solid fa-arrow-left"></i></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="blog-pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="<?= BASE_URL ?>tarnegar/<?= $current_page - 1 ?>" class="pagi-btn"><i class="fa-solid fa-chevron-right"></i> قبلی</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= BASE_URL ?>tarnegar/<?= $i ?>" class="pagi-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($current_page < $total_pages): ?>
                        <a href="<?= BASE_URL ?>tarnegar/<?= $current_page + 1 ?>" class="pagi-btn">بعدی <i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align:center;padding:80px 20px;color:#888;">
                <i class="fa-solid fa-pen" style="font-size:56px;margin-bottom:16px;color:#ddd;display:block;"></i>
                <p>هنوز مطلبی منتشر نشده است.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.blog-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:24px; }
.blog-card { display:flex; flex-direction:column; background:#fff; border:1px solid var(--rang-border); border-radius:14px; overflow:hidden; text-decoration:none; color:inherit; transition:all .3s ease; }
.blog-card:hover { transform:translateY(-5px); box-shadow:0 12px 35px rgba(0,0,0,0.1); border-color:var(--rang-asli); }
.blog-img { height:200px; overflow:hidden; background:var(--rang-sabz); }
.blog-img svg { width:100%; height:100%; object-fit:cover; }
.blog-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ddd; font-size:48px; }
.blog-body { padding:20px; flex:1; display:flex; flex-direction:column; gap:8px; text-align:right; }
.blog-cat { display:inline-block; padding:3px 12px; border-radius:20px; background:var(--rang-roshan); color:var(--rang-asli); font-size:12px; font-weight:600; width:fit-content; }
.blog-body h3 { font-size:1.1rem; color:var(--rang-matn); line-height:1.4; margin:0; }
.blog-date { font-size:12px; color:#aaa; }
.blog-body p { font-size:0.9rem; color:var(--rang-gray); line-height:1.7; flex:1; margin:0; }
.blog-more { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:var(--rang-asli); margin-top:8px; }
.blog-card:hover .blog-more { gap:10px; }

.blog-pagination { display:flex; gap:8px; justify-content:center; margin-top:48px; flex-wrap:wrap; }
.pagi-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:8px; background:var(--rang-sabz); color:var(--rang-matn); text-decoration:none; font-weight:600; font-size:14px; transition:all .2s; }
.pagi-btn:hover { background:var(--rang-roshan); color:var(--rang-asli); }
.pagi-btn.active { background:var(--rang-asli); color:#fff; }

@media(max-width:768px) {
    .blog-grid { grid-template-columns:1fr; }
    .blog-img { height:180px; }
}
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
