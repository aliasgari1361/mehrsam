-- ===================================================================
-- Migration 027: افزودن قالب صفحه‌ساز برای بلاگ/مقالات
-- این مهاجرت قالب‌های پیش‌فرض برای archive (لیست) و single (جزئیات)
-- صفحات بلاگ و مقاله را در صفحه‌ساز ایجاد می‌کند.
-- ===================================================================

-- قالب آرشیو بلاگ (صفحه لیست مطالب)
INSERT IGNORE INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data, position_mode, mobile_mode)
VALUES (
    0, 'post', 'قالب آرشیو بلاگ', 'archive', 'blog',
    '[]',
    0,
    'auto'
);

-- قالب آرشیو مقاله
INSERT IGNORE INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data, position_mode, mobile_mode)
VALUES (
    0, 'post', 'قالب آرشیو مقاله', 'archive', 'maghaleh',
    '[]',
    0,
    'auto'
);

-- قالب آرشیو محصولات
INSERT IGNORE INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data, position_mode, mobile_mode)
VALUES (
    0, 'post', 'قالب آرشیو محصولات', 'archive', 'mahsul',
    '[]',
    0,
    'auto'
);

-- قالب تک محصول
INSERT IGNORE INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data, position_mode, mobile_mode)
VALUES (
    0, 'post', 'قالب تک محصول', 'single', 'mahsul',
    '[]',
    0,
    'auto'
);

-- قالب تک مطلب بلاگ (generic)
INSERT IGNORE INTO block_pages (page_id, page_type, name, condition_type, condition_value, blocks_data, position_mode, mobile_mode)
VALUES (
    0, 'post', 'قالب تک مطلب (عمومی)', 'single', 'post',
    '[]',
    0,
    'auto'
);

-- یادداشت: قالب‌های single خاص هر مطلب از طریق edit_post در builder ایجاد می‌شوند.
