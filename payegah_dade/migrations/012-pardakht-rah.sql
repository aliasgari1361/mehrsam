-- افزودن ستون درگاه پرداخت به جدول سفارشات
ALTER TABLE sefaresh ADD COLUMN pardakht_rah VARCHAR(50) DEFAULT NULL AFTER pardakht_vaziat;
