-- ============================================
-- کاربر مخصوص دسترسی خودکار (Agent) برای مشاهده سایت و مدیریت
-- هر ورود این کاربر از طریق ایمیل به مدیر اطلاع داده می‌شود
-- ============================================

INSERT IGNORE INTO users (username, password, role, email) VALUES ('mehrsam-bot', '$2y$10$b4u3vnKoZwshyynw6Epu8.yjrfL4A3qo6mqvse2iTbB22fceShzyi', 'admin', 'ali.asgari.6106@gmail.com');
