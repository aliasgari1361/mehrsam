-- انتقال داده‌های جدول khadamat به جدول posts
-- هر خدمت به یک صفحه (safhe) با page_section='khadamat' تبدیل می‌شود
-- برای ویرایش مستقیم از بخش «برگه‌ها» قابل دسترس است

INSERT INTO posts (author_id, title, slug, content, kholaseh, tasvir, type, page_section, display_order, template, language, status, meta_title, meta_description, created_at, updated_at)
SELECT
    1,
    onvan,
    slug,
    COALESCE(sharh_kamel, ''),
    sharh_kootah,
    tasvir,
    'safhe',
    'khadamat',
    tartib,
    'default',
    'fa',
    IF(vaziat = 1, 'publish', 'draft'),
    meta_onvan,
    meta_sharh,
    created_at,
    updated_at
FROM khadamat;

-- ایجاد صفحه آرشیو خدمات (برای ویرایش عنوان/مقدمه) در صورت عدم وجود
INSERT INTO posts (author_id, title, slug, content, type, page_section, display_order, template, language, status, created_at)
SELECT 1, 'خدمات', 'khadamat-archive', '', 'safhe', NULL, 0, 'services', 'fa', 'publish', NOW()
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE template = 'services');

-- حذف جدول قدیمی خدمات
DROP TABLE khadamat;
