<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<!-- سرصفحه -->
<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>خدمت یافت نشد</h1>
        <p>این خدمت در حال حاضر در لیست خدمات موجود نیست.</p>
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