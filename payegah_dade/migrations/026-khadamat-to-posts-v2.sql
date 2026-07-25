-- ============================================
-- انتقال خدمات از جدول khadamat به posts
-- هر خدمت = یک پست با type='khadamat'
-- ============================================

-- اضافه کردن فیلد kholaseh به posts اگر وجود ندارد
SET @col_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'posts' AND column_name = 'kholaseh');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE posts ADD COLUMN kholaseh TEXT AFTER content', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- اضافه کردن فیلد subtitle به posts اگر وجود ندارد
SET @col_exists2 = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'posts' AND column_name = 'subtitle');
SET @sql2 = IF(@col_exists2 = 0, 'ALTER TABLE posts ADD COLUMN subtitle VARCHAR(200) DEFAULT \'\' AFTER kholaseh', 'SELECT 1');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- اضافه کردن فیلد tasvir به posts اگر وجود ندارد
SET @col_exists3 = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'posts' AND column_name = 'tasvir');
SET @sql3 = IF(@col_exists3 = 0, 'ALTER TABLE posts ADD COLUMN tasvir VARCHAR(500) DEFAULT \'\' AFTER subtitle', 'SELECT 1');
PREPARE stmt3 FROM @sql3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- انتقال رکوردهای khadamat به posts با type='khadamat')
-- فقط اگر جدول khadamat وجود داشته باشد
SET @tbl = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'khadamat');
SET @do_insert = IF(@tbl > 1, CONCAT('
INSERT INTO posts (title, slug, kholaseh, content, tasvir, subtitle, display_order, type, status, created_at)
SELECT
    k.title, k.slug, COALESCE(k.kholaseh, \'\'), COALESCE(k.content, \'\'),
    COALESCE(k.tasvir, \'\'), COALESCE(k.subtitle, \'\'), k.display_order,
    \'khadamat\', IF(k.vaziat = 1, \'publish\', \'draft\'), NOW()
FROM khadamat k
ON DUPLICATE KEY UPDATE
    kholaseh = k.kholaseh, content = k.content, tasvir = k.tasvir,
    subtitle = k.subtitle, display_order = k.display_order,
    status = IF(k.vaziat = 1, \'publish\', \'draft\');
'), 'SELECT 1');
PREPARE stmt4 FROM @do_insert;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

-- حذف جدول khadamat اگر وجود دارد
DROP TABLE IF EXISTS khadamat;
