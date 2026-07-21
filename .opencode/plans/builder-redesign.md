# بازسازی صفحه‌ساز به سبک المنتور — برنامهٔ اجرا

## وضعیت فعلی (تحلیل شده)
- صفحه‌ساز = تم‌بلدر شرطی (`block_pages`)، کاملاً جدا از ادیتور HTML قدیمی (`posts.content` در `mod/edit_content`).
- نمایش جلوی سایت: `mohtava-kontrol.php` (safhe/maghaleh) و `tarnegar-kontrol.php` (blog) از `builder_render_for` استفاده می‌کنن.
- حالت‌های دستگاه (`builder_devices()` builder.php:6) و `switchDevice` (builder.php:884) از قبل هستن ولی فقط iframe پیش‌نمایش رو تغییر می‌دن، نه بوم ویرایش.
- `builder_render_for` / `builder_find_template` (builder.php:1170 / 1189) برای archive/single/global وجود داره (بدون header/footer).
- مایگریشن‌های مربوط: 014 (block_pages)، 016 (condition_type/condition_value/name)، 022 (position_mode/mobile_mode).
- محصولات در جدول `mahsulat` ذخیره می‌شن (نه posts).

## تصمیمات کاربر
1. محتوا = یک منبع واحد؛ ویرایش فقط با صفحه‌ساز (ادیتور HTML قدیمی حذف/غیرفعال).
2. هدر/فوتر = بخش‌های جدا (تم‌بلدر).
3. قالب‌ها (single/archive/header/footer) از جدول می‌خونن، محتوا رو خودشون ذخیره نمی‌کنن.
4. محصول (`mahsulat`) هم محتوای خودش (با ویرایشگر) داشته باشه، هم قالب محصول (با تم‌بلدر) — دو مقوله جدا.
5. امکان قالب اختصاصی برای یه آیتم خاص (مثل المنتور).

## باگ‌های شناخته‌شده
- خروج کار نمی‌کنه: `mod.php:1225` فقط `session_destroy` می‌زنه، کوکی‌های `rid`/`rtok` رو پاک نمی‌کنه → بلوک auto-login (mod.php:16-31) دوباره ادمین رو وارد می‌کنه.

## فازها

### فاز ۰ — رفع باگ خروج
`mod.php` در `case 'logout'`: قبل از `session_destroy()` این دو خط اضافه شود:
```php
if (isset($_COOKIE['rid'])) setcookie('rid', '', time() - 3600, '/');
if (isset($_COOKIE['rtok'])) setcookie('rtok', '', time() - 3600, '/');
```

### فاز ۱ — یکی‌سازی محتوا
- حذف/غیرفعال کردن ادیتور HTML در `mod/edit_content` (mod.php:934).
- دکمه «ویرایش با صفحه‌ساز» روی لیست محتواها → مسیر `mod/builder/edit_post/{type}/{id}`.
- `builder_page_edit` (builder.php:217) باید برای پست موجود block_page بسازه (page_id=post.id، page_type=نوع).
- `builder_save_blocks` (builder.php:1008): بعد از ذخیره، خروجی رندرشده رو توی `posts.content` هم بنویس (فقط فال‌بک/جستجو؛ ویرایش فقط از صفحه‌ساز).

### فاز ۲ — تم‌بلدر حرفه‌ای (هدر/فوتر/قالب)
- مایگریشن جدید `023-builder-parts.sql`:
  `ALTER TABLE block_pages ADD COLUMN part VARCHAR(20) NOT NULL DEFAULT '' AFTER condition_value;`
  (مقادیر: header / footer / single / archive / '').
- `builder_find_template` گسترش بدن تا `$part` رو هم بگیره؛ تابع کمکی `builder_resolve_part($part, $context)` با اولویت (slug خاص > نوع > سراسری `*`).
- `ghaleb/mehrsam/sarsafhe.php` (خط ۳۹۷-۴۲۸): هدر هاردکد رو با خروجی `builder_render_for('header', ...)` جایگزین کن (در صورت نبود، همون هدر فعلی فال‌بک).
- `ghaleb/mehrsam/panevis.php`: پانویس مشابه با `builder_render_for('footer', ...)`.
- فرم‌های `builder_template_new`/`builder_template_create` و لیست `builder_page_list`: فیلد `part` اضافه شود.

### فاز ۳ — ویرایشگر WYSIWYG درجا
- مسیر `mod/builder/edit_post/{type}/{id}`: رندر صفحهٔ واقعی (هدر+محتوا+فوتر از تم‌بلدر) داخل فریم ویرایش؛ بلاک‌ها با `data-block-index` و دسته‌های انتخاب/درگ.
- درگ‌انددراپ روی صفحه (حالت آزاد=موقعیت مطلق، حالت چیده‌شده=Sortable) + ویرایش متن درجا (contenteditable) + پنل کناری برای همهٔ فیلدها.
- ۶ حالت دستگاه واقعی: عریض/دسکتاپ/تبلت افقی/عمودی/موبایل افقی/عمودی — تغییر عرض بوم ویرایش + اعمال CSS ریسپانسیو (`builder_build_positions_css`).
- دکمهٔ «پیشنمایش در تب جدید»: `window.open(BASE_URL+'mod/builder/preview/'+id, '_blank')`.

### فاز ۴ — تعمیم به فروشگاه/خدمات
- وصل کردن `mahsulat` و خدمات به همین سیستم (محتوا با ویرایشگر + قالب با تم‌بلدر). block_pages با `page_type='mahsul'`، `page_id=mahsulat.id`.

## نکات تست
- سرور محلی: `php -S 127.0.0.1:8000 router.php` (router.php برای clean URL) یا Laragon `site.test`.
- بعد از هر فاز: چک routes 200 + لاگ خطای PHP.
- اسکریپت تست لاگین ادمین/خروج با مرورگر انجام شود (کوکی‌ها پاک شوند).

## فایل‌های کلیدی
| فایل | تغییر |
|---|---|
| `haste/mod/mod.php` | رفع خروج (1225)؛ غیرفعال کردن ادیتور content (934)؛ مسیر edit_post |
| `mohtava/builder/builder.php` | edit_post، resolver part، پنل/JS ویرایش درجا، save sync content |
| `mohtava/builder/blocks/block-types.php` | خروجی contenteditable بلاک‌ها |
| `mohtava/mohtava-kontrol.php` | پشتیبانی blog + حل block_page |
| `ghaleb/mehrsam/sarsafhe.php` | هدر از تم‌بلدر |
| `ghaleb/mehrsam/panevis.php` | پانویس از تم‌بلدر |
| `database/migrations/023-builder-parts.sql` | ستون part |
