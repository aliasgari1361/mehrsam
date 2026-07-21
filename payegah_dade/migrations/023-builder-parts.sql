-- ============================================
-- درج ستون part برای تفکیک بخش‌های تم‌بلدر (هدر/فوتر/قالب)
-- مقادیر: header / footer / single / archive / '' (خالی = محتوای صفحه)
-- ============================================

ALTER TABLE block_pages ADD COLUMN part VARCHAR(20) NOT NULL DEFAULT '' AFTER condition_value;
