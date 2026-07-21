-- ============================================
-- حذف کلید یکتای (page_id, page_type)
-- تم‌های قالب (header/footer/archive/single) همگی page_id=0 دارند
-- و باید بتوان چند تم با بخش‌های مختلف ساخت. یکتایی صفحه‌های محتوایی
-- در سطح برنامه (builder_page_edit_post) تضمین می‌شود.
-- ============================================

ALTER TABLE block_pages DROP KEY unique_page;
