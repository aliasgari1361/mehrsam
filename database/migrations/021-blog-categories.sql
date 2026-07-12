-- ============================================
-- دسته‌بندی وبلاگ
-- ============================================

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_categories (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (post_id, category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO categories (title, slug, description) VALUES
('نرم‌افزار', 'software', 'مقالات مرتبط با نرم‌افزار، ویندوز و برنامه‌ها'),
('سخت‌افزار', 'hardware', 'مقالات مرتبط با قطعات کامپیوتر و سخت‌افزار'),
('شبکه و امنیت', 'network', 'مقالات مرتبط با شبکه، اینترنت و امنیت'),
('آموزشی', 'tutorial', 'آموزش‌های گام‌به‌گام و راهنماها');
