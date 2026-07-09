-- حذف ۸ پست خدمت (page_section='khadamat') و اطمینان از وجود صفحه خدمات
DELETE FROM posts WHERE page_section = 'khadamat';

-- اطمینان از وجود صفحه آرشیو خدمات (template='services')
INSERT INTO posts (author_id, title, slug, content, type, page_section, display_order, template, language, status, created_at)
SELECT 1, 'خدمات', 'khadamat-archive', '', 'safhe', NULL, 0, 'services', 'fa', 'publish', NOW()
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE template = 'services');