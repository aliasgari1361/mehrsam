# نقشه راه افزونه سئو (SEO Plugin Roadmap)

> تاریخ: ۲۶ تیر ۱۴۰۵  
> منبع: جلسات مشاوره با توسعه‌دهنده  
> وضعیت: پیش از پیاده‌سازی — مستندات طراحی

---

## فهرست مطالب

۱. [اهداف افزونه](#۱-اهداف-افزونه)  
۲. [معماری کلی](#۲-معماری-کلی)  
۳. [ساختار فایل‌ها](#۳-ساختار-فایل‌ها)  
۴. [سطح اول: آسان (MVP)](#۴-سطح-اول-آسان-mvp)  
۵. [سطح دوم: متوسط](#۵-سطح-دوم-متوسط)  
۶. [سطح سوم: پیشرفته (API خارجی)](#۶-سطح-سوم-پیشرفته-api-خارجی)  
۷. [کنترل ایندکس (index/noindex)](#۷-کنترل-ایندکس)  
۸. [محتوای قابل سئو](#۸-محتوای-قابل-سئو)  
۹. [سیستم اخطار و هشدار](#۹-سیستم-اخطار-و-هشدار)  
۱۰. [سئو برای موتورهای جستجوی مختلف](#۱۰-سئو-برای-موتورهای-جستجوی-مختلف)  
۱۱. [ادغام با صفحه‌ساز (Page Builder)](#۱۱-ادغام-با-صفحه‌ساز-page-builder)  
۱۲. [جدول پیگیری پیشرفت](#۱۲-جدول-پیگیری-پیشرفت)

---

## ۱. اهداف افزونه

- افزودن قابلیت‌های سئوی حرفه‌ای (مشابه Rank Math و Yoast) به سیستم مدیریت محتوای مهراد سام
- ارائه چک‌لیست سئو با اخطارهای رنگی (قرمز/زرد/سبز) برای هر محتوا
- کنترل دسترسی موتورهای جستجو به بخش‌های مختلف سایت
- تولید sitemap.xml و robots.txt
- پشتیبانی از Open Graph, Twitter Cards, Schema.org
- بدون وابستگی به API خارجی در مراحل اولیه (MVp)

---

## ۲. معماری کلی

```
افزونه سئو در پوشه afzuneh/seo/ قرار می‌گیرد
و از طریق seo-loader.php در haste/tanzimat.php لود می‌شود.

افزونه دو بخش اصلی دارد:
1. front/    — خروجی به مرورگر/موتور جستجو (متا تگ‌ها، sitemap, robots, schema)
2. admin/    — مدیریت در پنل ادمین (باکس سئو، تنظیمات، چک‌لیست، ریدایرکت، ۴۰۴)
```

---

## ۳. ساختار فایل‌ها

```
afzuneh/seo/
├── seo-loader.php              ← لودر اصلی (include در haste/tanzimat.php)
├── seo-config.php              ← تنظیمات پیش‌فرض + خواندن seo-settings.json
├── seo-settings.json           ← ذخیره تنظیمات سئو (JSON)
├── admin/
│   ├── seo-admin.php           ← صفحه اصلی تنظیمات سئو در پنل (mod/seo)
│   ├── seo-metabox.php         ← باکس سئو در ادیتور محتوا (sidebar)
│   ├── seo-checklist.php       ← صفحه چک‌لیست سئو با اخطارهای رنگی
│   ├── seo-redirects.php       ← مدیریت ریدایرکت‌های ۳۰۱
│   └── seo-404.php             ← مانیتور خطاهای ۴۰۴
├── front/
│   ├── seo-head.php            ← متا تگ‌های پویا (canonical, robots, og, twitter)
│   ├── seo-schema.php          ← JSON-LD (Article, Product, LocalBusiness, BreadcrumbList)
│   ├── sitemap.php             ← تولید خودکار sitemap.xml
│   └── robots.php              ← تولید خودکار robots.txt
├── lib/
│   ├── seo-analyzer.php        ← تحلیل محتوا (کلیدواژه، ساختار، لینک‌ها)
│   └── seo-scorer.php          ← محاسبه اسکور سئو (۰-۱۰۰)
├── db/
│   └── migrations.sql          ← جداول جدید: redirects, 404_logs, seo_meta
└── plan/
    └── README-SEO-PLAN.md      ← این فایل (نقشه راه)
```

---

## ۴. سطح اول: آسان (MVP)

قابلیت‌هایی که همین الان می‌توان پیاده‌سازی کرد:

### ۴.۱. باکس سئو در ادیتور محتوا
- فیلدهای `meta_title`, `meta_description`, `meta_keywords` (الان در جدول posts هستن)
- فیلد `focus_keyword` (کلیدواژه اصلی)
- نمایش پیش‌نمایش در گوگل (SERP Preview)
- نمایش طول کاراکتر تیتر و توضیحات با هشدار

### ۴.۲. sitemap.xml خودکار
- خواندن تمام URLهای عمومی از دیتابیس
- تفکیک بر اساس نوع (page, blog, product, service, category)
- اولویت‌بندی (priority) و فرکانس آپدیت (changefreq)
- خروجی XML استاندارد
- لینک به sitemap در robots.txt

### ۴.۳. robots.txt پویا
- Disallow برای: /mod/, /karbar/, /nasb.php, /panel.php
- Disallow برای: /forushgah/sabad, /forushgah/checkout, /forushgah/result
- Allow برای بقیه مسیرهای عمومی
- لینک به sitemap.xml
- قابل ویرایش از پنل

### ۴.۴. canonical URL
- افزودن `<link rel="canonical" href="...">` به هدر
- مقدار پیش‌فرض: URL فعلی صفحه
- قابل تنظیم از باکس سئو

### ۴.۵. meta robots تکی
- امکان تنظیم `noindex/nofollow` برای هر صفحه از باکس سئو
- مقادیر: `index,follow` (پیش‌فرض)، `noindex,follow`, `noindex,nofollow`

### ۴.۶. Open Graph کامل
- `og:title`, `og:description`, `og:image`, `og:url`, `og:type`
- `og:image` از تصویر شاخص محتوا یا تصویر پیش‌فرض تنظیمات
- Twitter Cards مشابه

### ۴.۷. چک‌لیست سئو (همین صفحه)
- چک‌های پایه برای هر محتوا:
  - [ ] تیتر بین ۳۰-۶۰ کاراکتر
  - [ ] توضیحات بین ۵۰-۱۶۰ کاراکتر
  - [ ] کلیدواژه در تیتر
  - [ ] کلیدواژه در توضیحات
  - [ ] کلیدواژه در H1
  - [ ] کلیدواژه در پاراگراف اول
  - [ ] تصویر شاخص دارد
  - [ ] alt text در تصاویر
  - [ ] لینک داخلی دارد
  - [ ] لینک خروجی دارد
  - [ ] محتوا بیش از ۳۰۰ کلمه
  - [ ] canonical تنظیم شده

---

## ۵. سطح دوم: متوسط

### ۵.۱. مدیریت ریدایرکت ۳۰۱
- جدول `seo_redirects`: `id, url_old, url_new, http_code, created_at, hit_count`
- فرم افزودن/ویرایش/حذف ریدایرکت
- چک خودکار قبل از ۴۰۴

### ۵.۲. مانیتور خطاهای ۴۰۴
- جدول `seo_404_logs`: `id, url, referer, user_agent, ip, count, last_seen`
- نمایش در پنل (جدول با صفحه‌بندی)
- امکان ریدایرکت سریع از همان صفحه

### ۵.۳. JSON-LD / Schema.org
- Article → برای blog/maghaleh
- Product → برای محصولات (با قیمت، برند، موجودی)
- LocalBusiness → برای اطلاعات کسب‌وکار
- BreadcrumbList → نون‌واژه
- WebSite → صفحه اصلی
- Service → برای سرویس‌ها

### ۵.۴. تحلیل Readability (فارسی)
- طول جملات (حداکثر ۲۵ کلمه)
- طول پاراگراف‌ها (حداکثر ۱۵۰ کلمه)
- کلمات انتقالی (transition words فارسی)
- تراکم کلیدواژه (keyword density)
- نمره خوانایی

### ۵.۵. تصویر پیش‌فرض اشتراک‌گذاری (Social Fallback)
- در تنظیمات سئو: آپلود تصویر پیش‌فرض برای og:image
- وقتی محتوا تصویر شاخص ندارد، از این تصویر استفاده شود

### ۵.۶. سئوی دسته‌بندی‌ها
- باکس سئو برای categories و mahsul_dasteh
- title, description مستقل برای هر دسته

---

## ۶. سطح سوم: پیشرفته (API خارجی)

⚠️ این بخش فعلاً در برنامه نیست — نیاز به تصمیم‌گیری مجدد دارد.

- Google Search Console API (نمایش impressions/clicks در پنل)
- Keyword Rank Tracking (ردیابی رتبه کلیدواژه‌ها)
- Content AI (پیشنهاد محتوا با هوش مصنوعی)
- LSI Keyword Suggestions از SEMrush یا مشابه
- Google Analytics 4 Integration
- Instant Indexing API (Google Indexing)

---

## ۷. کنترل ایندکس

### ۷.۱. محتوایی که ایندکس می‌شود (index, follow)

| نوع محتوا | مسیر | Schema |
|---|---|---|
| صفحه اصلی | / | WebSite |
| برگه‌ها | /mohtava/* | Article |
| مقالات وبلاگ | /tarnegar/* | BlogPosting |
| محصولات | /forushgah/* | Product |
| سرویس‌ها | /khadamat/* | Service |
| دسته‌بندی وبلاگ | /tarnegar/category/* | CollectionPage |
| آرشیو وبلاگ | /tarnegar | Blog |
| صفحه فروشگاه | /forushgah | Store |

### ۷.۲. محتوایی که ایندکس نمی‌شود (noindex)

| محتوا | دلیل |
|---|---|
| پنل ادمین (`/mod/*`) | امنیت |
| پنل کاربری (`/karbar/*`) | حریم خصوصی |
| سبد خرید (`/forushgah/sabad`) | محتوای داینامیک |
| تسویه حساب (`/forushgah/checkout`) | اطلاعات مالی |
| صفحه تأیید پرداخت (`/forushgah/result`) | یکبار مصرف |
| نصاب (`/nasb.php`) | امنیت |
| پنل CLI (`/panel.php`) | امنیت |
| صفحات خطا (۴۰۴) | محتوای تکراری |
| نتایج جستجوی داخلی | محتوای تکراری |
| فرم تماس (صفحه تشکر) | محتوای تکراری (با احتیاط) |

---

## ۸. محتوای قابل سئو

### ۸.۱. از دیتابیس (با فیلدهای مجزا)

| جدول | فیلدهای موجود | فیلدهای جدید لازم |
|---|---|---|
| `posts` | `meta_title, meta_description, meta_keywords` | `focus_keyword`, `is_noindex`, `canonical_url`, `og_image` |
| `khadamat` | `title, kholaseh (میتونه meta_description باشه)` | `meta_title, meta_description, focus_keyword` |
| `mahsulat` | `onvan (title), tozih (description)` | `meta_title, meta_description, focus_keyword, is_noindex` |
| `categories` | `title, description` | `meta_title, meta_description, is_noindex` |
| `mahsul_dasteh` | `onvan, tozih` | `meta_title, meta_description, is_noindex` |

### ۸.۲. از تنظیمات سراسری

- `SITE_NAME` → پیش‌فرض تیتر سایت
- `SITE_SLOGAN` → پیش‌فرض توضیحات
- `og_image_default` → تصویر پیش‌فرض اشتراک‌گذاری

---

## ۹. سیستم اخطار و هشدار

### ۹.۱. نمایش در پنل (صفحه مجزا)
صفحه `mod/seo` در پنل ادمین:
- جدول تمام محتواها با وضعیت سئو
- فیلتر: فقط اخطاردار / فقط سالم / همه
- مرتب‌سازی بر اساس اسکور
- امکان Bulk Edit برای تیتر و توضیحات

### ۹.۲. نمایش در ادیتور (همراه محتوا)
در صفحه ویرایش هر محتوا:
- باکس کناری (sidebar) با وضعیت سئو
- رنگ‌بندی: 🔴 < ۴۰ | 🟡 ۴۰-۷۰ | 🟢 > ۷۰
- چک‌لیست زنده با تیک‌خوردن خودکار
- پیش‌نمایش گوگل (SERP Preview)

### ۹.۳. چک‌های سئو (فهرست کامل)

```
وزن   تست
──────────────────────────────────────────────
۱۰    تیتر موجود است
۱۰    تیتر بین ۳۰-۶۰ کاراکتر
۱۰    کلیدواژه در تیتر
۱۰    توضیحات موجود است
۱۰    توضیحات بین ۵۰-۱۶۰ کاراکتر
۱۰    کلیدواژه در توضیحات
۵     کلیدواژه در H1
۵     کلیدواژه در پاراگراف اول
۵     تصویر شاخص دارد
۵     تصاویر alt متن دارند
۵     لینک داخلی دارد (حداقل ۱)
۵     لینک خروجی دارد (حداقل ۱)
۵     تعداد کلمات > ۳۰۰
۵     canonical تنظیم شده
۳     URL کوتاه (< ۷۰ کاراکتر)
۳     اسلاگ شامل کلیدواژه
۲     محتوا بیش از ۱۰۰۰ کلمه
──────────────────────────────────────────────
جمع: ۱۰۰ نمره
```

---

## ۱۰. سئو برای موتورهای جستجوی مختلف

### عمومی (همه)
- `sitemap.xml` → استاندارد (Google, Bing, Yandex, Yahoo)
- `robots.txt` → استاندارد
- `meta robots` → پشتیبانی همه
- `canonical URL` → پشتیبانی همه
- `JSON-LD Schema` → پشتیبانی همه (مخصوصاً Google)

### اختصاصی
```html
<!-- Google / Facebook (Open Graph) -->
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="...">
<meta property="og:url" content="...">
<meta property="og:type" content="website">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="...">
<meta name="twitter:description" content="...">
<meta name="twitter:image" content="...">

<!-- Bing Webmaster -->
<meta name="msvalidate.01" content="...">

<!-- Yandex Webmaster -->
<meta name="yandex-verification" content="...">

<!-- Google Webmaster (Search Console) -->
<meta name="google-site-verification" content="...">
```

---

## ۱۱. ادغام با صفحه‌ساز (Page Builder)

صفحه‌ساز (`mod/builder`) فعلاً محتوای قبلی را نشان نمی‌دهد. برای ادغام سئو با صفحه‌ساز:

### ۱۱.۱. رفع مشکل نمایش محتوای قبلی
- مسیر `mod/builder/edit_post/{type}/{id}` باید داده‌های موجود را از دیتابیس بخواند
- `blocks_data` از جدول `block_pages` باید در صفحه ادیتور پر شود
- فیلد `cached_html` نباید جایگزین `blocks_data` شود

### ۱۱.۲. اضافه کردن باکس سئو به صفحه‌ساز
- باکس سئو باید در ادیتور صفحه‌ساز هم نمایش داده شود
- متاتگ‌ها و کلیدواژه در صفحه‌ساز قابل تنظیم باشد
- چک‌لیست سئو بلافاصله پس از نوشتن محتوا به‌روز شود

### ۱۱.۳. ادغام با ادیتور مقاله و محصول
- باکس سئو در: `mod/edit_content/{id}`, `mod/store/products/edit/{id}`, `mod/builder/edit_post/{type}/{id}`
- باکس سئو یک المان مشترک (include) باشد، نه کد تکراری

---

## ۱۲. جدول پیگیری پیشرفت

- [ ] پشتیبانی در `tanzimat.php`
- [ ] ساخت فایل ساختار دیتابیس (migrations)
- [ ] باکس سئو در ادیتور محتوا
- [ ] sitemap.xml
- [ ] robots.txt
- [ ] متا تگ‌های تکی (canonical, robots, og, twitter)
- [ ] باکس سئو در صفحه‌ساز
- [ ] باکس سئو در محصولات
- [ ] باکس سئو در سرویس‌ها
- [ ] باکس سئو در دسته‌بندی‌ها
- [ ] چک‌لیست سئو با اخطارها
- [ ] Schema JSON-LD
- [ ] ریدایرکت ۳۰۱
- [ ] مانیتور ۴۰۴
- [ ] اسکور سئو (۰-۱۰۰)
- [ ] تنظیمات سراسری سئو در پنل
- [ ] Bulk Edit

---

*این فایل نقشه راه افزونه سئو است و در طول توسعه به‌روزرسانی می‌شود.*
