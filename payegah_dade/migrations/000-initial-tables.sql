-- جدول خدمات (موجود) - برای استفاده در صفحه خانه

-- اگر می‌خواهید از جدول posts استفاده کنید (یکپارچه)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT,
    type VARCHAR(50) DEFAULT 'safhe',
    status VARCHAR(20) DEFAULT 'publish',
    meta_title VARCHAR(200) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    meta_keywords TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- اگر جدول khadamat وجود ندارد
CREATE TABLE IF NOT EXISTS khadamat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    onvan VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50),
    rang VARCHAR(20),
    sharh_kootah TEXT,
    meta_sharh VARCHAR(200),
    virayesh TEXT,
    vaziat TINYINT(1) DEFAULT 1,
    tartib INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;