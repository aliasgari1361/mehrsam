<!-- پاصفحه -->
<footer style="background:var(--rang-makm1); color:#ccc; margin-top:0;">

    <div class="mohtava-container" style="padding-top:60px; padding-bottom:40px;">
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:40px;">

            <!-- ستون اول: معرفی -->
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                    <div style="width:40px;height:40px;background:var(--rang-asli);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;">
                        <i class="fa-solid fa-laptop"></i>
                    </div>
                    <span style="color:#fff; font-weight:700; font-size:18px;"><?= SITE_NAME ?></span>
                </div>
                <p style="font-size:14px; line-height:2; margin-bottom:20px;">
                    ارائه خدمات تخصصی پشتیبانی کامپیوتر از راه دور و حضوری در ملارد و مارلیک.
                    ما با هدف کمک به شما در رفع مشکلات کامپیوتری فعالیت می‌کنیم.
                </p>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="<?= SITE_TELEGRAM ?>" target="_blank" title="تلگرام" style="width:36px;height:36px;background:#333;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;transition:all 0.3s;" onmouseover="this.style.background='#0088cc'" onmouseout="this.style.background='#333'">
                        <i class="fa-brands fa-telegram"></i>
                    </a>
                    <a href="<?= SITE_WHATSAPP ?>" target="_blank" title="واتساپ" style="width:36px;height:36px;background:#333;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;transition:all 0.3s;" onmouseover="this.style.background='#25D366'" onmouseout="this.style.background='#333'">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="<?= SITE_BALE ?>" target="_blank" title="بله" style="width:36px;height:36px;background:#333;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;transition:all 0.3s;" onmouseover="this.style.background='#22a13e'" onmouseout="this.style.background='#333'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 14H5.17L4 17.17V4h15v12z"/><circle cx="8" cy="10" r="1.5"/><circle cx="12" cy="10" r="1.5"/><circle cx="16" cy="10" r="1.5"/></svg>
                    </a>
                    <a href="<?= SITE_INSTAGRAM ?>" target="_blank" title="اینستاگرام" style="width:36px;height:36px;background:#333;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;transition:all 0.3s;" onmouseover="this.style.background='#E1306C'" onmouseout="this.style.background='#333'">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- ستون دوم: دسترسی سریع -->
            <div>
                <h4 style="color:#fff; font-size:15px; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid var(--rang-asli); display:inline-block;">دسترسی سریع</h4>
                <ul style="display:flex; flex-direction:column; gap:10px;">
                    <?php
                    $lnk_ha = [
                        ['/', 'خانه'],
                        ['/khadamat', 'خدمات'],
                        ['/tarnegar', 'تارنگار'],
                        ['/tamas',    'تماس با ما'],
                    ];
                    foreach ($lnk_ha as $lnk): ?>
                    <li>
                        <a href="<?= BASE_URL . $lnk[0] ?>" style="color:#bbb; font-size:14px; display:flex; align-items:center; gap:6px; transition:color 0.3s;" onmouseover="this.style.color='var(--rang-makm2)'" onmouseout="this.style.color='#bbb'">
                            <i class="fa-solid fa-chevron-left" style="font-size:10px; color:var(--rang-makm2);"></i>
                            <?= $lnk[1] ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- ستون سوم: تماس -->
            <div>
                <h4 style="color:#fff; font-size:15px; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid var(--rang-asli); display:inline-block;">تماس با ما</h4>
                <ul style="display:flex; flex-direction:column; gap:14px;">
                    <li style="display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <i class="fa-solid fa-location-dot" style="color:var(--rang-asli); margin-top:4px; flex-shrink:0;"></i>
                        <span><?= SITE_ADRES ?></span>
                    </li>
                    <li style="display:flex; align-items:center; gap:10px; font-size:14px;">
                        <i class="fa-solid fa-phone" style="color:var(--rang-asli);"></i>
                        <a href="tel:<?= SITE_TEL ?>" style="color:#bbb;" onmouseover="this.style.color='var(--rang-asli)'" onmouseout="this.style.color='#bbb'"><?= SITE_TEL ?></a>
                    </li>
                    <li style="display:flex; align-items:center; gap:10px; font-size:14px;">
                        <i class="fa-solid fa-envelope" style="color:var(--rang-asli);"></i>
                        <a href="mailto:<?= SITE_EMAIL ?>" style="color:#bbb;" onmouseover="this.style.color='var(--rang-asli)'" onmouseout="this.style.color='#bbb'"><?= SITE_EMAIL ?></a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- کپی‌رایت -->
    <div style="border-top:1px solid #333; padding:16px 0; text-align:center;">
        <div class="mohtava-container">
            <p style="font-size:13px; color:#888;">
                تمام حقوق برای
                <a href="<?= BASE_URL ?>/" style="color:var(--rang-asli);"><?= SITE_NAME ?></a>
                محفوظ است &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>

</footer>

<?php include __DIR__ . '/chat/widget.php'; ?>

<script>
// بستن منوی موبایل با کلیک بیرون
document.addEventListener('click', function(e) {
    var nav = document.querySelector('.nav');
    var toggle = document.querySelector('.nav-toggle');
    if (nav && toggle && !nav.contains(e.target) && !toggle.contains(e.target)) {
        nav.classList.remove('baz');
    }
});
</script>

<?php
// JS سفارشی از تنظیمات قالب
$custom_js = get_site_setting('theme.custom_js') ?? '';
if ($custom_js): ?>
<script><?= $custom_js ?></script>
<?php endif; ?>

</body>
</html>
