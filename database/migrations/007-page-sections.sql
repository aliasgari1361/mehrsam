-- اضافه کردن page_section به posts
ALTER TABLE posts ADD COLUMN page_section VARCHAR(50) DEFAULT NULL AFTER type;
ALTER TABLE posts ADD COLUMN display_order INT DEFAULT 0 AFTER page_section;
ALTER TABLE posts ADD INDEX idx_page_section (page_section);

-- درج بخش‌های صفحه اصلی
INSERT IGNORE INTO posts (id, title, slug, content, type, page_section, display_order, status) VALUES
(1, 'Hero - صفحه اصلی', 'hero-home', '<h1>مشکل کامپیوترت رو سریع حل می‌کنیم</h1><p>پشتیبانی از راه دور و حضوری در تهران. رفع کندی، نصب نرم‌افزار، طراحی سایت، دوربین مدار بسته و بیشتر.</p>', 'safhe', 'hero', 1, 'publish'),
(2, 'خدمات ما', 'services-home', '<p>طیف گسترده‌ای از خدمات کامپیوتری</p>', 'safhe', 'services', 2, 'publish'),
(3, 'چرا مهراد سام؟', 'features-home', '<p>تجربه، سرعت و کیفیت در یک مجموعه</p>', 'safhe', 'features', 3, 'publish'),
(4, 'تماس با ما', 'cta-home', '<p>آماده کمک به شما هستیم!</p><p><a href=\"/tamas\">ارسال پیام</a> | <a href=\"tel:09120000000\">تماس بگیرید</a></p>', 'safhe', 'cta', 4, 'publish');