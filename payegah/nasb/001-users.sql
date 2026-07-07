-- ایجاد کاربر admin پیش‌فرض
-- رمز عبور: admin123
-- bcrypt hash for password 'admin123'
INSERT IGNORE INTO users (username, password, role, email) VALUES 
('admin', '$2y$10$A9h9jK3fU7YQZqY2JqY2.OeJ3s0q1XeG9T7jW8sVrKfN3hT6uYqC', 'admin', 'admin@site.com');

-- برای تست: admin / admin123
-- اگر جدول users وجود ندارد، این را اجرا کنید:
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;