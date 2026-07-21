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

// پیدا کردن صفحه خدمات
$stmt = $conn->prepare("SELECT id, content FROM posts WHERE template='services' AND status='publish' LIMIT 1");
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    echo "صفحه خدمات پیدا نشد.";
    $conn->close();
    exit;
}
$page_id = $page['id'];
$current_content = $page['content'];

// ۸ خدمت با ساختار details/summary
$services = [
    [
        'title' => 'پشتیبانی از راه دور',
        'slug' => 'poshtiban-az-rah-dor',
        'short' => 'حل مشکلات نرم‌افزاری و ویندوز به صورت آنلاین و تیم‌ویور.',
        'full' => '<h3>پشتیبانی از راه دور (آنلاین)</h3>
<p>تیم فنی مهراد سام با استفاده از نرم‌افزارهای کنترل از راه دور (TeamViewer، AnyDesk، RustDesk) به سیستم شما متصل شده و مشکلات نرم‌افزاری را بدون نیاز به مراجعه حضوری برطرف می‌کند.</p>
<h4>شامل موارد زیر:</h4>
<ul>
<li>نصب و کانفیگ ویندوز ۱۰/۱۱</li>
<li>رفع ارورهای بوت، BSOD، و درایورها</li>
<li>نصب آنتی‌ویروس و تنظیم فایروال</li>
<li>بهینه‌سازی سرعت ویندوز و پاک‌سازی فایل‌های موقت</li>
<li>راهنمای خرید و نصب سخت‌افزار</li>
</ul>
<p><strong>مزایا:</strong> سریع، ارزان‌تر از مراجعه حضوری، و در کل شهر تهران قابل ارائه.</p>',
        'icon_class' => 'fa-wifi',
        'icon_color' => '#00B894',
    ],
    [
        'title' => 'پشتیبانی حضوری',
        'slug' => 'poshtiban-hozoori',
        'short' => 'مراجعه به محل شما در تهران برای عیب‌یابی و تعمیر سخت‌افزاری/نرم‌افزاری.',
        'full' => '<h3>پشتیبانی حضوری در محل</h3>
<p>تیم فنی به آدرس شما در تهران مراجعه کرده و خدمات را در محل انجام می‌دهد. مناسب برای مواردی که نیاز به بررسی فیزیکی سخت‌افزار دارند.</p>
<h4>خدمات:</h4>
<ul>
<li>تعویض قطعات لپ‌تاپ (صفحه نمایش، کیبورد، باتری، هارد، رم)</li>
<li>تمیزکاری داخلی و thay جیل حرارتی CPU/GPU</li>
<li>معماری شبکه، کانفیگ مودم/راوتر، کابل‌کشی</li>
<li>نصب سیستم‌های امنیتی (دوربین، کنترل دسترسی)</li>
<li>بازیابی داده از هارد خراب/فرمت شده</li>
</ul>
<p><strong>زمان‌بندی:</strong> روزهای شنبه تا پنجشنبه، ۹ صبح تا ۸ عصر.</p>',
        'icon_class' => 'fa-user-tie',
        'icon_color' => '#0984E3',
    ],
    [
        'title' => 'رفع کندی سیستم',
        'slug' => 'rafe-kandi-system',
        'short' => 'بهینه‌سازی کامل ویندوز، پاک‌سازی فایل‌های زائد، و افزایش سرعت بوت.',
        'full' => '<h3>رفع کندی و بهینه‌سازی سیستم</h3>
<p>سیستم شما کند شده؟ برنامه‌ها دیر باز می‌شوند؟ ما با ابزارهای حرفه‌ای سیستم را تحلیل و بهینه می‌کنیم.</p>
<h4>مراحل انجام شده:</h4>
<ol>
<li>اسکن و حذف ویروس/مال‌وِر با ابزارهای پیشرفته</li>
<li>پاک‌سازی رجیستری، فایل‌های موقت، و کش مرورگرها</li>
<li>غیرفعال کردن برنامه‌های استارتاپ غیرضروری</li>
<li>چک و آپدیت درایورها (GPU، چیپست، صدا، شبکه)</li>
<li>تنظیم گزینه‌های پاور برای عملکرد بهینه</li>
<li>دیفراگمنت هارد (برای HDD) یا بهینه‌سازی SSD (TRIM)</li>
</ol>
<p><strong>نتیجه:</strong> سیستم سریع‌تر، سبک‌تر، و پایدارتر.</p>',
        'icon_class' => 'fa-bolt',
        'icon_color' => '#FF6F00',
    ],
    [
        'title' => 'نصب نرم‌افزار',
        'slug' => 'nasb-narmafzar',
        'short' => 'نصب و کانفیگ ویندوز، آفیس، آنتی‌ویروس، و نرم‌افزارهای تخصصی.',
        'full' => '<h3>نصب و راه‌اندازی نرم‌افزار</h3>
<p>نصب صحیح نرم‌افزارها از اهمیت بالایی برای ثبات سیستم برخوردار است. ما با لایسنس‌های اورجینال و نسخه‌های معتبر کار می‌کنیم.</p>
<h4>نرم‌افزارهای رایج:</h4>
<ul>
<li>ویندوز ۱۰/۱۱ (نسخه اصلی، فعال‌سازی دائم)</li>
<li>مایکروسافت آفیس ۲۰۲۱/۳۶۵</li>
<li>آنتی‌ویروس: Kaspersky، ESET، Bitdefender، Windows Defender پیشرفته</li>
<li>ادوبی: فتوشاپ، ایلاستریتور، پرمیر، آکروبات</li>
<li>برنامه‌نویسی: VS Code، Python، Node.js، Docker، Git</li>
<li>حسابداری: هلو، سپیدار، शायद</li>
</ul>
<p><strong>نکته:</strong> قبل از نصب، بک‌آپ از داده‌های مهم گرفته می‌شود.</p>',
        'icon_class' => 'fa-download',
        'icon_color' => '#6C5CE7',
    ],
    [
        'title' => 'نصب آنتی‌ویروس',
        'slug' => 'nasb-antivirus',
        'short' => 'نصب، کانفیگ، و آموزش استفاده از آنتی‌ویروس‌های قدرتمند.',
        'full' => '<h3>نصب و کانفیگ آنتی‌ویروس حرفه‌ای</h3>
<p>محافظت از سیستم در برابر ویروس، رنسوم‌ور، اسپای‌ور، و حملات فیشینگ اولویت اول ماست.</p>
<h4>آنتی‌ویروس‌های پیشنهادی:</h4>
<ul>
<li><strong>Kaspersky Total Security:</strong> محافظت کامل، کنترل والدین، مدیریت رمز عبور</li>
<li><strong>ESET Internet Security:</strong> سبک، سریع، تشخیص پیشرفته تهدیدات</li>
<li><strong>Bitdefender Total Security:</strong> محافظت چندلایه، VPN، Anti-tracker</li>
<li><strong>Windows Defender (پیشرفته):</strong> رایگان، یکپارچه با ویندوز، بهینه برای سیستم‌های ضعیف</li>
</ul>
<h4>شامل:</h4>
<ul>
<li>نصب و آپدیت امضاهای ویروس</li>
<li>تنظیم اسکن زمان‌بندی شده، محافظت بی‌درنگ، فایروال</li>
<li>استثنا گذاری پوشه‌های قابل اعتماد</li>
<li>آموزش تشخیص ایمیل/لینک‌های مشکوک</li>
</ul>',
        'icon_class' => 'fa-shield-halved',
        'icon_color' => '#E17055',
    ],
    [
        'title' => 'طراحی سایت',
        'slug' => 'tarahi-site',
        'short' => 'طراحی و توسعه وب‌سایت‌های شرکتی، فروشگاهی، و پورتال با پنل مدیریت فارسی.',
        'full' => '<h3>طراحی و توسعه وب‌سایت</h3>
<p>وب‌سایت شما کارت ویزیت آنلاین کسب‌وکار است. ما سایت‌های سریع، واکنش‌گرا (Responsive)، و بهینه‌سازی شده برای گوگل (SEO) می‌سازیم.</p>
<h4>انواع سایت:</h4>
<ul>
<li><strong>شرکتی/مؤسسه‌ای:</strong> معرفی خدمات، تیم، رزومه، فرم تماس</li>
<li><strong>فروشگاهی (E-commerce):</strong> سبد خرید، درگاه پرداخت، مدیریت سفارش، تخفیف، کوپن</li>
<li><strong>پورتال/خبری:</strong> مدیریت مطالب، دسته‌بندی، جستجو، نظرسنجی، عضویت</li>
<li><strong>لندینگ پیج (Landing Page):</strong> متمرکز بر تبدیل، تبلیغات گوگل/اینستاگرام</li>
</ul>
<h4>تکنولوژی‌ها:</h4>
<ul>
<li>Backend: PHP (Laravel، CodeIgniter، Core PHP)</li>
<li>Database: MySQL، PostgreSQL، MongoDB، Redis</li>
<li>Frontend: Vue 3، React، Alpine.js، Tailwind CSS</li>
<li>DevOps: Docker، GitLab CI/CD، Nginx، Linux Server Management</li>
</ul>
<p><strong>شامل:</strong> دامنه، هاست، SSL، بهینه‌سازی سرعت، آموزش پنل، ۶ ماه پشتیبانی رایگان.</p>',
        'icon_class' => 'fa-code',
        'icon_color' => '#00B894',
    ],
    [
        'title' => 'دوربین مدار بسته',
        'slug' => 'doobin-madar-basteh',
        'short' => 'فروش، نصب، و راه‌اندازی سیستم‌های CCTV/IP Camera با مانیتورینگ موبایل.',
        'full' => '<h3>نصب و راه‌اندازی دوربین مدار بسته (CCTV)</h3>
<p>امنیت محیط کار و خانه با سیستم‌های نظارت تصویری پیشرفته. نصب استاندارد با کابل‌کشی منظم و تنظیمات امنیتی.</p>
<h4>انواع دوربین:</h4>
<ul>
<li>دوربین گنبال (Bullet) - فضای باز، مقاوم در برابر آب</li>
<li>دوربین گنبدی (Dome) - فضای داخلی، ضد وندالیسم</li>
<li>دوربین PTZ - کنترل از راه دور، زوم نوری، گردش ۳۶۰ درجه</li>
<li>دوربین IP (شبکه) - کیفیت ۴K، تشخیص حرکت، هشدار هوشمند</li>
</ul>
<h4>شامل نصب:</h4>
<ul>
<li>DVR/NVR (ضبط و ذخیره‌سازی)</li>
<li>هارد دیسک مخصوص نظارت (2TB تا ۱۶TB)</li>
<li>کابل‌کشی Cat6/UPT، پاور، کانکتور</li>
<li>تنظیم دید از راه دور (موبایل، کامپیوتر، تبلت)</li>
<li>آموزش کار با اپلیکیشن و بک‌آپ ضبط</li>
</ul>
<p><strong>برندها:</strong> Hikvision، Dahua، HiLook، Tiandy، Imou.</p>',
        'icon_class' => 'fa-video',
        'icon_color' => '#0984E3',
    ],
    [
        'title' => 'برنامه‌نویسی',
        'slug' => 'barnameh-nevisi',
        'short' => 'توسعه نرم‌افزار سفارشی، وب‌اپلیکیشن، API، و اتوماسیون فرآیندها.',
        'full' => '<h3>توسعه نرم‌افزار سفارشی (Custom Software Development)</h3>
<p>نرم‌افزار دقیقاً مطابق نیاز کسب‌وکار شما، بدون محدودیت‌های نرم‌افزارهای آماده.</p>
<h4>خدمات توسعه:</h4>
<ul>
<li><strong>وب‌اپلیکیشن:</strong> پنل‌های مدیریتی، CRM، ERP، سیستم رزرواسیون، سامانه یادگیری (LMS)</li>
<li><strong>API و وب‌سرویس:</strong> RESTful، GraphQL، یکپارچه‌سازی با درگاه پرداخت، پیامک، پست، حسابداری</li>
<li><strong>اتوماسیون:</strong> ربات تلگرام/واتس‌اپ، اسکریپت‌های پایتون، زاپیار (Zapier)، Make (Integromat)</li>
<li><strong>موبایل:</strong> اپلیکیشن اندروید (Kotlin) و iOS (Swift) - Native یا Flutter</li>
</ul>
<h4>تکنولوژی‌ها:</h4>
<ul>
<li>Backend: PHP (Laravel)، Python (Django/FastAPI)، Node.js (Express/NestJS)</li>
<li>Database: MySQL، PostgreSQL، MongoDB، Redis</li>
<li>Frontend: Vue 3، React، Alpine.js، Tailwind CSS</li>
<li>DevOps: Docker، GitLab CI/CD، Nginx، Linux Server Management</li>
</ul>
<p><strong>فرآیند:</strong> تحلیل نیازمندی → طراحی دیتابیس/UI → توسعه → تست → استقرار → آموزش + ۶ ماه پشتیبانی.</p>',
        'icon_class' => 'fa-laptop-code',
        'icon_color' => '#6C5CE7',
    ],
    [
        'title' => 'شبکه و اینترنت',
        'slug' => 'network-internet',
        'short' => 'راه‌اندازی، عیب‌یابی و بهینه‌سازی شبکه، مودم، وایرلس و اینترنت.',
        'full' => '<h3>خدمات شبکه و اینترنت</h3>
<p>راه‌اندازی شبکه‌های کامپیوتری خانگی و اداری، رفع مشکلات اینترنت، و بهینه‌سازی وایرلس با جدیدترین تجهیزات.</p>
<h4>خدمات شبکه:</h4>
<ul>
<li>راه‌اندازی شبکه LAN/WLAN با کابل‌کشی استاندارد (Cat6/Cat7)</li>
<li>کانفیگ مودم/راوتر (TP-Link، D-Link، Asus، MikroTik، Ubiquiti)</li>
<li>رفع مشکل قطعی و کندی اینترنت، تنظیم DNS، پورت‌فورواردینگ</li>
<li>نصب و راه‌اندازی شبکه مش (Mesh) برای پوشش کامل وایرلس</li>
<li>راه‌اندازی سرور خانگی یا اداری (NAS، فایل سرور، پرینت سرور)</li>
<li>امنیت شبکه: فایروال، VLAN، فیلترینگ مک آدرس، WPA3</li>
<li>کابل‌کشی ساختاریافته، پچ پنل، رک، و تست فلوک</li>
</ul>
<h4>تجهیزات تخصصی:</h4>
<ul>
<li>مودم/راوتر: TP-Link، MikroTik، Ubiquiti UniFi، Asus</li>
<li>سوئیچ: Cisco، HP، MikroTik، TP-Link Smart/Managed</li>
<li>اکسس پوینت: UniFi، Omada، Grandstream</li>
<li>کابل و کانکتور: Cat6 UTP/STP، فیبر نوری، کیستون، پچ کورد</li>
</ul>
<p>شامل مشاوره، طراحی نقشه شبکه، و آموزش کار با تجهیزات.</p>',
        'icon_class' => 'fa-network-wired',
        'icon_color' => '#2D3436',
    ],
];

// ساخت HTML برای هر خدمت (ساختار details/summary)
$html_parts = [];
foreach ($services as $s) {
    $html_parts[] = '<details>' . "\n"
        . '  <summary>' . "\n"
        . '    <span class="service-icon" style="background:' . htmlspecialchars($s['icon_color']) . ';"><i class="fa-solid ' . htmlspecialchars($s['icon_class']) . '"></i></span>' . "\n"
        . '    <div>' . "\n"
        . '      <h2>' . htmlspecialchars($s['title']) . '</h2>' . "\n"
        . '      <p>' . htmlspecialchars($s['short']) . '</p>' . "\n"
        . '    </div>' . "\n"
        . '  </summary>' . "\n"
        . '  <div>' . $s['full'] . '</div>' . "\n"
        . '</details>' . "\n";
}

$html_content = implode("\n", $html_parts);

// آپدیت content صفحه خدمات
$stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
$stmt->bind_param("si", $html_content, $page_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
$conn->close();

echo "✅ صفحه خدمات با $affected ردیف آپدیت شد. ۹ خدمت با ساختار details/summary و آیکون‌های رنگی درج شد.\n";
echo "اکنون در /mod/pages صفحه «خدمات» را ویرایش کنید تا بلوک‌ها را ببینید و سفارشی‌سازی کنید.\n";