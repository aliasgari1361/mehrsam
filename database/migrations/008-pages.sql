-- اضافه کردن نوع صفحه به posts
ALTER TABLE posts MODIFY COLUMN type ENUM('blog','safhe','maghaleh','page') DEFAULT 'blog';
ALTER TABLE posts ADD COLUMN template VARCHAR(50) DEFAULT 'default' AFTER type;
ALTER TABLE posts ADD COLUMN language VARCHAR(5) DEFAULT 'fa' AFTER template;

-- ایجاد صفحه‌های اصلی
INSERT IGNORE INTO posts (id, title, slug, content, type, template, status, meta_title, meta_description) VALUES
(1, 'صفحه اصلی', 'home', '<h1>مشکل کامپیوترت رو سریع حل می‌کنیم</h1><p>پشتیبانی از راه دور و حضوری در تهران. رفع کندی، نصب نرم‌افزار، طراحی سایت، دوربین مدار بسته و بیشتر.</p>', 'page', 'home', 'publish', 'مهراد سام | خدمات کامپیوتر در تهران', 'پشتیبانی کامپیوتر در تهران - رفع کندی، نصب نرم‌افزار، طراحی سایت'),
(2, 'خدمات', 'services', '<h2>خدمات ما</h2><p>طیف گسترده‌ای از خدمات کامپیوتری</p>', 'page', 'services', 'publish', 'خدمات | مهراد سام', 'مشاهده تمام خدمات پشتیبانی کامپیوتری'),
(3, 'تارنگار', 'blog', '<h2>تارنگار</h2><p>آخرین مقالات و اخبار</p>', 'page', 'blog', 'publish', 'تارنگار | مهراد سام', 'مقالات و اخبار دنیای کامپیوتر'),
(4, 'تماس با ما', 'contact', '<h2>تماس با ما</h2><p>آدرس: تهران<br>تلفن: ۰۹۱۲-۰۰۰-۰۰۰۰<br>ایمیل: info@mhsi.ir</p>', 'page', 'contact', 'publish', 'تماس با ما | مهراد سام', 'تماس با ما برای دریافت مشاوره و پشتیبانی');