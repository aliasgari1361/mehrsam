-- ============================================
-- جدول خدمات (زیرمجموعه صفحه خدمات)
-- ============================================

CREATE TABLE IF NOT EXISTS khadamat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    kholaseh TEXT,
    content LONGTEXT,
    tasvir VARCHAR(500) DEFAULT '',
    subtitle VARCHAR(200) DEFAULT '',
    display_order INT DEFAULT 0,
    vaziat TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
