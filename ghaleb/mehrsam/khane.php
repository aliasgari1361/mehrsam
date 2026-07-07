<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<!-- ====================================================
     قهرمان (Hero)
==================================================== -->
<section style="background:linear-gradient(135deg, #FFF3E0 0%, #fff8f0 50%, #fff 100%); padding:90px 0 80px; overflow:hidden; position:relative;">

    <!-- شکل تزئینی -->
    <div style="position:absolute; left:-80px; top:-80px; width:350px; height:350px; border-radius:50%; background:rgba(255,111,0,0.06);"></div>
    <div style="position:absolute; right:-60px; bottom:-60px; width:250px; height:250px; border-radius:50%; background:rgba(255,111,0,0.04);"></div>

    <div class="mohtava-container" style="position:relative; z-index:1;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center;">

            <!-- متن -->
            <div>
                <div style="display:inline-block; background:rgba(255,111,0,0.12); color:var(--rang-asli); font-size:13px; font-weight:700; padding:6px 18px; border-radius:20px; margin-bottom:20px;">
                    <i class="fa-solid fa-circle-check" style="margin-left:6px;"></i>
                    خدمات حرفه‌ای کامپیوتر
                </div>
                <h1 style="font-size:2.4rem; line-height:1.5; color:#1a1a1a; margin-bottom:20px; font-weight:700;">
                    مشکل کامپیوترت رو<br>
                    <span style="color:var(--rang-asli);">سریع حل می‌کنیم</span>
                </h1>
                <div style="font-size:1rem; color:#555; line-height:2; margin-bottom:32px; max-width:480px;">
                    <?= ($page_data['content'] ?? '<p>پشتیبانی از راه دور و حضوری در تهران.</p>') ?>
                </div>
                <div style="display:flex; gap:14px; flex-wrap:wrap;">
                    <a href="<?= BASE_URL ?>/tamas" class="dakmeh dakmeh-asli">
                        <i class="fa-solid fa-phone-volume"></i>
                        تماس بگیرید
                    </a>
                    <a href="<?= BASE_URL ?>/khadamat" class="dakmeh dakmeh-khali">
                        مشاهده خدمات
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <!-- آمار سریع -->
                <div style="display:flex; gap:32px; margin-top:40px; padding-top:32px; border-top:1px solid #eee;">
                    <?php
                    $amar = [
                        ['۵+',   'سال تجربه'],
                        ['۵۰۰+', 'مشتری راضی'],
                        ['۸',    'خدمت تخصصی'],
                    ];
                    foreach ($amar as $a): ?>
                    <div>
                        <div style="font-size:1.8rem; font-weight:700; color:var(--rang-asli);"><?= $a[0] ?></div>
                        <div style="font-size:13px; color:#888;"><?= $a[1] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- تصویر / آیکون بزرگ -->
            <div style="text-align:center; display:flex; align-items:center; justify-content:center;">
                <div style="width:320px; height:320px; background:linear-gradient(135deg, var(--rang-asli), var(--rang-tira)); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 20px 60px rgba(255,111,0,0.3); position:relative;">
                    <i class="fa-solid fa-laptop-code" style="font-size:120px; color:#fff; opacity:0.9;"></i>
                    <!-- نشانه‌های اطراف -->
                    <div style="position:absolute; top:20px; right:-10px; background:#fff; border-radius:12px; padding:10px 16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap;">
                        <i class="fa-solid fa-wifi" style="color:var(--rang-asli); margin-left:6px;"></i>
                        پشتیبانی آنلاین
                    </div>
                    <div style="position:absolute; bottom:30px; left:-20px; background:#fff; border-radius:12px; padding:10px 16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap;">
                        <i class="fa-solid fa-check-circle" style="color:#4caf50; margin-left:6px;"></i>
                        تضمین کیفیت
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media (max-width:768px) {
            section > .mohtava-container > div { grid-template-columns:1fr !important; }
            section > .mohtava-container > div > div:last-child { display:none !important; }
            h1 { font-size:1.8rem !important; }
        }
    </style>
</section>


<!-- ====================================================
     خدمات
==================================================== -->
<section class="bakhsh bakhsh-sabz">
    <div class="mohtava-container">

        <div class="onvan-bakhsh">
            <span class="barg"><i class="fa-solid fa-star" style="margin-left:5px;"></i>خدمات ما</span>
            <h2>چه کمکی می‌توانیم بکنیم؟</h2>
            <p>طیف گسترده‌ای از خدمات کامپیوتری برای رفع نیازهای شما</p>
        </div>

        <?php if (!empty($khadamat)): ?>
        <div class="gerid-3">
            <?php foreach ($khadamat as $k): ?>
            <a href="<?= BASE_URL ?>/khadamat/<?= htmlspecialchars($k['slug']) ?>" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:<?= htmlspecialchars($k['rang'] ?? 'var(--rang-asli)') ?>;">
                        <i class="fa-solid <?= htmlspecialchars($k['icon'] ?? 'fa-tools') ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($k['onvan']) ?></h3>
                    <p><?= htmlspecialchars($k['sharh_kootah']) ?></p>
                    <div class="lnk">
                        بیشتر بخوانید
                        <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- دکمه مشاهده همه -->
        <div style="text-align:center; margin-top:40px;">
            <a href="<?= BASE_URL ?>/khadamat" class="dakmeh dakmeh-asli">
                مشاهده همه خدمات
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>


<!-- ====================================================
     چرا ما؟
==================================================== -->
<section class="bakhsh">
    <div class="mohtava-container">

        <div class="onvan-bakhsh">
            <span class="barg">مزیت‌های ما</span>
            <h2>چرا مهراد سام؟</h2>
            <p>تجربه، سرعت و کیفیت در یک مجموعه</p>
        </div>

        <div class="gerid-4">
            <?php
            $maziyat = [
                ['fa-bolt',          '#FF6F00', 'سرعت بالا',         'رفع مشکل در کمترین زمان ممکن'],
                ['fa-headset',       '#E65100', 'پشتیبانی ۲۴/۷',     'در دسترس بودن برای رفع فوری مشکلات'],
                ['fa-shield-halved', '#BF360C', 'امنیت کامل',         'حفظ اطلاعات و حریم خصوصی شما'],
                ['fa-thumbs-up',     '#FF6F00', 'تضمین کیفیت',        'رضایت شما اولویت اصلی ماست'],
            ];
            foreach ($maziyat as $m): ?>
            <div style="text-align:center; padding:32px 20px;">
                <div style="width:72px; height:72px; background:<?= $m[1] ?>; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);">
                    <i class="fa-solid <?= $m[0] ?>" style="font-size:28px; color:#fff;"></i>
                </div>
                <h3 style="font-size:1rem; margin-bottom:8px;"><?= $m[2] ?></h3>
                <p style="font-size:13px; color:#888; line-height:1.8;"><?= $m[3] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>


<!-- ====================================================
     فراخوان اقدام (CTA)
==================================================== -->
<section style="background:linear-gradient(135deg, var(--rang-asli) 0%, var(--rang-tira) 100%); padding:70px 0;">
    <div class="mohtava-container" style="text-align:center;">
        <h2 style="color:#fff; font-size:1.8rem; margin-bottom:12px;">آماده کمک به شما هستیم!</h2>
        <p style="color:rgba(255,255,255,0.85); margin-bottom:32px; font-size:1rem;">
            همین الان با ما تماس بگیرید و مشکل خود را حل کنید
        </p>
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/tamas" style="background:#fff; color:var(--rang-asli); padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <i class="fa-solid fa-envelope"></i>
                ارسال پیام
            </a>
            <a href="tel:<?= SITE_TEL ?>" style="background:transparent; color:#fff; padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; border:2px solid rgba(255,255,255,0.6); display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;"
               onmouseover="this.style.borderColor='#fff'; this.style.background='rgba(255,255,255,0.1)'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.6)'; this.style.background='transparent'">
                <i class="fa-solid fa-phone"></i>
                <?= SITE_TEL ?>
            </a>
        </div>
    </div>
</section>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
