-- حذف ۸ پست خدمت جداگانه (اکنون در صفحه واحد «خدمات» ادغام شده‌اند)
DELETE FROM posts WHERE page_section = 'khadamat' AND type = 'safhe';