<?php
define('BASE_URL', 'http://site.test/');
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mehrsamdb');
require_once __DIR__ . '/haste/tanzimat.php';
require_once __DIR__ . '/dade/bank.php';

$bank = new Bank();
$conn = $bank->getConnection();

// پیدا کردن صفحه خانه
$stmt = $conn->prepare("SELECT id FROM posts WHERE template='home' AND status='publish' LIMIT 1");
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    echo "صفحه خانه پیدا نشد.";
    $conn->close();
    exit;
}
$page_id = $page['id'];

// --- بخش‌های مختلف صفحه اول به صورت HTML قابل ویرایش ---

$content = <<<'HERO'
<!-- ===== هدر قهرمان (Hero) ===== -->
<section style="background:linear-gradient(135deg, #FFF3E0 0%, #fff8f0 50%, #fff 100%); padding:90px 0 80px; overflow:hidden; position:relative;">

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
                    <p>پشتیبانی از راه دور و حضوری در تهران. تیم فنی مهراد سام آماده رفع مشکلات نرم‌افزاری و سخت‌افزاری شماست.</p>
                </div>
                <div style="display:flex; gap:14px; flex-wrap:wrap;">
                    <a href="/tamas" class="dakmeh dakmeh-asli">
                        <i class="fa-solid fa-phone-volume"></i>
                        تماس بگیرید
                    </a>
                    <a href="/khadamat" class="dakmeh dakmeh-khali">
                        مشاهده خدمات
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <!-- آمار سریع -->
                <div style="display:flex; gap:32px; margin-top:40px; padding-top:32px; border-top:1px solid #eee;">
                    <div>
                        <div style="font-size:1.8rem; font-weight:700; color:var(--rang-asli);">۵+</div>
                        <div style="font-size:13px; color:#888;">سال تجربه</div>
                    </div>
                    <div>
                        <div style="font-size:1.8rem; font-weight:700; color:var(--rang-asli);">۵۰۰+</div>
                        <div style="font-size:13px; color:#888;">مشتری راضی</div>
                    </div>
                    <div>
                        <div style="font-size:1.8rem; font-weight:700; color:var(--rang-asli);">۹</div>
                        <div style="font-size:13px; color:#888;">خدمت تخصصی</div>
                    </div>
                </div>
            </div>

            <!-- تصویر -->
            <div style="text-align:center; display:flex; align-items:center; justify-content:center;">
                <div style="width:320px; height:320px; background:linear-gradient(135deg, var(--rang-asli), var(--rang-tira)); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 20px 60px rgba(255,111,0,0.3); position:relative;">
                    <i class="fa-solid fa-laptop-code" style="font-size:120px; color:#fff; opacity:0.9;"></i>
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
            section>.mohtava-container>div { grid-template-columns:1fr !important; }
            section>.mohtava-container>div>div:last-child { display:none !important; }
            h1 { font-size:1.8rem !important; }
        }
    </style>
</section>
HERO;

$services_section = <<<'SERVICES'
<!-- ===== خدمات ما ===== -->
<section class="bakhsh bakhsh-sabz">
    <div class="mohtava-container">

        <div class="onvan-bakhsh">
            <span class="barg"><i class="fa-solid fa-star" style="margin-left:5px;"></i>خدمات ما</span>
            <h2>چه کمکی می‌توانیم بکنیم؟</h2>
            <p>طیف گسترده‌ای از خدمات کامپیوتری برای رفع نیازهای شما</p>
        </div>

        <div class="gerid-3">
            <a href="/khadamat" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);"><i class="fa-solid fa-wifi"></i></div>
                    <h3>پشتیبانی از راه دور</h3>
                    <p>حل مشکلات نرم‌افزاری و ویندوز به صورت آنلاین.</p>
                    <div class="lnk">بیشتر بخوانید <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></div>
                </div>
            </a>
            <a href="/khadamat" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);"><i class="fa-solid fa-user-tie"></i></div>
                    <h3>پشتیبانی حضوری</h3>
                    <p>مراجعه به محل شما در تهران برای تعمیرات.</p>
                    <div class="lnk">بیشتر بخوانید <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></div>
                </div>
            </a>
            <a href="/khadamat" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);"><i class="fa-solid fa-bolt"></i></div>
                    <h3>رفع کندی سیستم</h3>
                    <p>بهینه‌سازی کامل ویندوز و افزایش سرعت.</p>
                    <div class="lnk">بیشتر بخوانید <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></div>
                </div>
            </a>
            <a href="/khadamat" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);"><i class="fa-solid fa-code"></i></div>
                    <h3>طراحی سایت</h3>
                    <p>سایت شرکتی، فروشگاهی و شخصی.</p>
                    <div class="lnk">بیشتر بخوانید <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></div>
                </div>
            </a>
            <a href="/khadamat" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);"><i class="fa-solid fa-laptop-code"></i></div>
                    <h3>برنامه‌نویسی</h3>
                    <p>نرم‌افزار سفارشی مطابق نیاز شما.</p>
                    <div class="lnk">بیشتر بخوانید <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></div>
                </div>
            </a>
            <a href="/khadamat" style="text-decoration:none; color:inherit;">
                <div class="kart-khadamat">
                    <div class="icon" style="background:var(--rang-asli);"><i class="fa-solid fa-network-wired"></i></div>
                    <h3>شبکه و اینترنت</h3>
                    <p>راه‌اندازی و بهینه‌سازی شبکه و مودم.</p>
                    <div class="lnk">بیشتر بخوانید <i class="fa-solid fa-arrow-left" style="font-size:12px;"></i></div>
                </div>
            </a>
        </div>

        <div style="text-align:center; margin-top:40px;">
            <a href="/khadamat" class="dakmeh dakmeh-asli">
                مشاهده همه خدمات
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

    </div>
</section>
SERVICES;

$advantages_section = <<<'ADV'
<!-- ===== چرا ما ===== -->
<section class="bakhsh">
    <div class="mohtava-container">

        <div class="onvan-bakhsh">
            <span class="barg">مزیت‌های ما</span>
            <h2>چرا مهراد سام؟</h2>
            <p>تجربه، سرعت و کیفیت در یک مجموعه</p>
        </div>

        <div class="gerid-4">
            <div style="text-align:center; padding:32px 20px;">
                <div style="width:72px; height:72px; background:#FF6F00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);">
                    <i class="fa-solid fa-bolt" style="font-size:28px; color:#fff;"></i>
                </div>
                <h3 style="font-size:1rem; margin-bottom:8px;">سرعت بالا</h3>
                <p style="font-size:13px; color:#888; line-height:1.8;">رفع مشکل در کمترین زمان ممکن</p>
            </div>
            <div style="text-align:center; padding:32px 20px;">
                <div style="width:72px; height:72px; background:#E65100; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(230,81,0,0.25);">
                    <i class="fa-solid fa-headset" style="font-size:28px; color:#fff;"></i>
                </div>
                <h3 style="font-size:1rem; margin-bottom:8px;">پشتیبانی ۲۴/۷</h3>
                <p style="font-size:13px; color:#888; line-height:1.8;">در دسترس بودن برای رفع فوری مشکلات</p>
            </div>
            <div style="text-align:center; padding:32px 20px;">
                <div style="width:72px; height:72px; background:#BF360C; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(191,54,12,0.25);">
                    <i class="fa-solid fa-shield-halved" style="font-size:28px; color:#fff;"></i>
                </div>
                <h3 style="font-size:1rem; margin-bottom:8px;">امنیت کامل</h3>
                <p style="font-size:13px; color:#888; line-height:1.8;">حفظ اطلاعات و حریم خصوصی شما</p>
            </div>
            <div style="text-align:center; padding:32px 20px;">
                <div style="width:72px; height:72px; background:#FF6F00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);">
                    <i class="fa-solid fa-thumbs-up" style="font-size:28px; color:#fff;"></i>
                </div>
                <h3 style="font-size:1rem; margin-bottom:8px;">تضمین کیفیت</h3>
                <p style="font-size:13px; color:#888; line-height:1.8;">رضایت شما اولویت اصلی ماست</p>
            </div>
        </div>

    </div>
</section>
ADV;

$cta_section = <<<'CTA'
<!-- ===== فراخوان اقدام (CTA) ===== -->
<section style="background:linear-gradient(135deg, var(--rang-asli) 0%, var(--rang-tira) 100%); padding:70px 0;">
    <div class="mohtava-container" style="text-align:center;">
        <h2 style="color:#fff; font-size:1.8rem; margin-bottom:12px;">آماده کمک به شما هستیم!</h2>
        <p style="color:rgba(255,255,255,0.85); margin-bottom:32px; font-size:1rem;">
            همین الان با ما تماس بگیرید و مشکل خود را حل کنید
        </p>
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <a href="/tamas" style="background:#fff; color:var(--rang-asli); padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <i class="fa-solid fa-envelope"></i>
                ارسال پیام
            </a>
            <a href="tel:<?= htmlspecialchars(SITE_TEL_EN) ?>" style="background:transparent; color:#fff; padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; border:2px solid rgba(255,255,255,0.6); display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;"
               onmouseover="this.style.borderColor='#fff'; this.style.background='rgba(255,255,255,0.1)'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.6)'; this.style.background='transparent'">
                <i class="fa-solid fa-phone"></i>
                <?= to_persian_num(SITE_TEL_EN) ?>
            </a>
        </div>
    </div>
</section>
CTA;

$full_content = $content . "\n\n" . $services_section . "\n\n" . $advantages_section . "\n\n" . $cta_section;

// آپدیت page خانه با تمام محتوا
$stmt = $conn->prepare("UPDATE posts SET content = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $full_content, $page_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

// آپدیت meta_description هم اگه خالی بود
$stmt = $conn->prepare("UPDATE posts SET meta_description = 'خدمات پشتیبانی کامپیوتر مهراد سام در تهران | تعمیر لپ‌تاپ، نصب ویندوز، آنتی‌ویروس، طراحی سایت' WHERE id = ? AND (meta_description IS NULL OR meta_description = '')");
$stmt->bind_param("i", $page_id);
$stmt->execute();
$stmt->close();
$conn->close();

echo "✅ صفحه خانه با $affected ردیف آپدیت شد. تمام بخش‌های صفحه اول اکنون در برگه (template=home) قابل ویرایش هستند.\n";
echo "برای ویرایش: /mod/pages -> صفحه «مهراد سام | خانه» را ویرایش کنید.\n";