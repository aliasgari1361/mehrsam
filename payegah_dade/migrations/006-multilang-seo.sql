-- جدول زبان‌ها
CREATE TABLE IF NOT EXISTS languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(5) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_default (is_default)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول کلیدهای ترجمه
CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_translation (key_name, language_code),
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- افزودن ستون انتخاب زبان به کاربران
ALTER TABLE users ADD COLUMN selected_language VARCHAR(5) DEFAULT NULL;

-- کپچا: جدول کدهای فعال‌سازی
CREATE TABLE IF NOT EXISTS captcha_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(6) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    INDEX idx_ip (ip_address),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- سئو: فیلدهای meta برای محتوا
ALTER TABLE posts ADD COLUMN meta_title VARCHAR(200) DEFAULT NULL;
ALTER TABLE posts ADD COLUMN meta_description TEXT DEFAULT NULL;
ALTER TABLE posts ADD COLUMN meta_keywords TEXT DEFAULT NULL;

-- محدودیت آپلود برای کاربران
ALTER TABLE users ADD COLUMN upload_limit INT DEFAULT 5242880; -- 5 مگابایت به بایت

-- راهنمای ایجاد کاربر admin پیش‌فرض
INSERT IGNORE INTO languages (code, name, is_active, is_default) VALUES ('fa', 'فارسی', 1, 1);
INSERT IGNORE INTO languages (code, name, is_active, is_default) VALUES ('en', 'English', 1, 0);
INSERT IGNORE INTO languages (code, name, is_active, is_default) VALUES ('ar', 'العربية', 1, 0);
INSERT IGNORE INTO languages (code, name, is_active, is_default) VALUES ('ru', 'Русский', 1, 0);