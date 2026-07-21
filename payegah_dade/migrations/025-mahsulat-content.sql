-- ============================================
-- افزودن ستون content به محصولات برای همگام‌سازی خروجی صفحه‌ساز
-- (مشابه posts.content) — فال‌بک نمایش جلوی سایت
-- ============================================

ALTER TABLE mahsulat ADD COLUMN content LONGTEXT AFTER virayesh;
