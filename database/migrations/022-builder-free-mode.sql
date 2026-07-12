-- ============================================
-- افزودن حالت بوم آزاد (Free Canvas) و تنظیمات موبایل به صفحه‌ساز
-- ============================================

ALTER TABLE block_pages ADD COLUMN position_mode TINYINT(1) NOT NULL DEFAULT 0 AFTER blocks_data;
ALTER TABLE block_pages ADD COLUMN mobile_mode VARCHAR(10) NOT NULL DEFAULT 'auto' AFTER position_mode;
