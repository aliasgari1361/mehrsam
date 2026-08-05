-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: mehrsam_db
-- ------------------------------------------------------
-- Server version	5.7.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `block_pages`
--

DROP TABLE IF EXISTS `block_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `block_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT NULL,
  `page_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'safhe',
  `name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `condition_value` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `part` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `blocks_data` longtext COLLATE utf8mb4_unicode_ci,
  `position_mode` tinyint(1) NOT NULL DEFAULT '0',
  `mobile_mode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto',
  `cached_html` longtext COLLATE utf8mb4_unicode_ci,
  `cache_updated` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100016 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `block_pages`
--

LOCK TABLES `block_pages` WRITE;
/*!40000 ALTER TABLE `block_pages` DISABLE KEYS */;
INSERT INTO `block_pages` VALUES (100008,16,'blog','محتوای blog #16','single','blog','','[]',0,'auto',NULL,NULL,'2026-07-15 06:42:05','2026-07-15 06:42:05'),(100009,3,'safhe','محتوای safhe #3','single','safhe','','[{\"type\":\"custom\",\"data\":{\"html\":\"<!-- ===== هدر قهرمان (Hero) ===== -->\\n<section style=\\\"background:linear-gradient(135deg, #FFF3E0 0%, #fff8f0 50%, #fff 100%); padding:90px 0 80px; overflow:hidden; position:relative;\\\">\\n\\n    <div style=\\\"position:absolute; left:-80px; top:-80px; width:350px; height:350px; border-radius:50%; background:rgba(255,111,0,0.06);\\\"><\\/div>\\n    <div style=\\\"position:absolute; right:-60px; bottom:-60px; width:250px; height:250px; border-radius:50%; background:rgba(255,111,0,0.04);\\\"><\\/div>\\n\\n    <div class=\\\"mohtava-container\\\" style=\\\"position:relative; z-index:1;\\\">\\n\\n        <div style=\\\"display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center;\\\">\\n\\n            <!-- متن -->\\n            <div>\\n                <div style=\\\"display:inline-block; background:rgba(255,111,0,0.12); color:var(--rang-asli); font-size:13px; font-weight:700; padding:6px 18px; border-radius:20px; margin-bottom:20px;\\\">\\n                    <i class=\\\"fa-solid fa-circle-check\\\" style=\\\"margin-left:6px;\\\"><\\/i>\\n                    خدمات حرفه‌ای کامپیوتر\\n                <\\/div>\\n                <h1 style=\\\"font-size:2.4rem; line-height:1.5; color:#1a1a1a; margin-bottom:20px; font-weight:700;\\\">\\n                    مشکل کامپیوترت رو<br>\\n                    <span style=\\\"color:var(--rang-asli);\\\">سریع حل می‌کنیم<\\/span>\\n                <\\/h1>\\n                <div style=\\\"font-size:1rem; color:#555; line-height:2; margin-bottom:32px; max-width:480px;\\\">\\n                    <p>پشتیبانی از راه دور و حضوری در تهران. تیم فنی مهراد سام آماده رفع مشکلات نرم‌افزاری و سخت‌افزاری شماست.<\\/p>\\n                <\\/div>\\n                <div style=\\\"display:flex; gap:14px; flex-wrap:wrap;\\\">\\n                    <a href=\\\"\\/tamas\\\" class=\\\"dakmeh dakmeh-asli\\\">\\n                        <i class=\\\"fa-solid fa-phone-volume\\\"><\\/i>\\n                        تماس بگیرید\\n                    <\\/a>\\n                    <a href=\\\"\\/khadamat\\\" class=\\\"dakmeh dakmeh-khali\\\">\\n                        مشاهده خدمات\\n                        <i class=\\\"fa-solid fa-arrow-left\\\"><\\/i>\\n                    <\\/a>\\n                <\\/div>\\n\\n                <!-- آمار سریع -->\\n                <div style=\\\"display:flex; gap:32px; margin-top:40px; padding-top:32px; border-top:1px solid #eee;\\\">\\n                    <div>\\n                        <div style=\\\"font-size:1.8rem; font-weight:700; color:var(--rang-asli);\\\">۵+<\\/div>\\n                        <div style=\\\"font-size:13px; color:#888;\\\">سال تجربه<\\/div>\\n                    <\\/div>\\n                    <div>\\n                        <div style=\\\"font-size:1.8rem; font-weight:700; color:var(--rang-asli);\\\">۵۰۰+<\\/div>\\n                        <div style=\\\"font-size:13px; color:#888;\\\">مشتری راضی<\\/div>\\n                    <\\/div>\\n                    <div>\\n                        <div style=\\\"font-size:1.8rem; font-weight:700; color:var(--rang-asli);\\\">۹<\\/div>\\n                        <div style=\\\"font-size:13px; color:#888;\\\">خدمت تخصصی<\\/div>\\n                    <\\/div>\\n                <\\/div>\\n            <\\/div>\\n\\n            <!-- تصویر -->\\n            <div style=\\\"text-align:center; display:flex; align-items:center; justify-content:center;\\\">\\n                <div style=\\\"width:320px; height:320px; background:linear-gradient(135deg, var(--rang-asli), var(--rang-tira)); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 20px 60px rgba(255,111,0,0.3); position:relative;\\\">\\n                    <i class=\\\"fa-solid fa-laptop-code\\\" style=\\\"font-size:120px; color:#fff; opacity:0.9;\\\"><\\/i>\\n                    <div style=\\\"position:absolute; top:20px; right:-10px; background:#fff; border-radius:12px; padding:10px 16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap;\\\">\\n                        <i class=\\\"fa-solid fa-wifi\\\" style=\\\"color:var(--rang-asli); margin-left:6px;\\\"><\\/i>\\n                        پشتیبانی آنلاین\\n                    <\\/div>\\n                    <div style=\\\"position:absolute; bottom:30px; left:-20px; background:#fff; border-radius:12px; padding:10px 16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap;\\\">\\n                        <i class=\\\"fa-solid fa-check-circle\\\" style=\\\"color:#4caf50; margin-left:6px;\\\"><\\/i>\\n                        تضمین کیفیت\\n                    <\\/div>\\n                <\\/div>\\n            <\\/div>\\n\\n        <\\/div>\\n    <\\/div>\\n\\n    <style>\\n        @media (max-width:768px) {\\n            section>.mohtava-container>div { grid-template-columns:1fr !important; }\\n            section>.mohtava-container>div>div:last-child { display:none !important; }\\n            h1 { font-size:1.8rem !important; }\\n        }\\n    <\\/style>\\n<\\/section>\\n\\n<!-- ===== خدمات ما ===== -->\\n<section class=\\\"bakhsh bakhsh-sabz\\\">\\n    <div class=\\\"mohtava-container\\\">\\n\\n        <div class=\\\"onvan-bakhsh\\\">\\n            <span class=\\\"barg\\\"><i class=\\\"fa-solid fa-star\\\" style=\\\"margin-left:5px;\\\"><\\/i>خدمات ما<\\/span>\\n            <h2>چه کمکی می‌توانیم بکنیم؟<\\/h2>\\n            <p>طیف گسترده‌ای از خدمات کامپیوتری برای رفع نیازهای شما<\\/p>\\n        <\\/div>\\n\\n        <div class=\\\"gerid-3\\\">\\n            <a href=\\\"\\/khadamat\\\" style=\\\"text-decoration:none; color:inherit;\\\">\\n                <div class=\\\"kart-khadamat\\\">\\n                    <div class=\\\"icon\\\" style=\\\"background:var(--rang-asli);\\\"><i class=\\\"fa-solid fa-wifi\\\"><\\/i><\\/div>\\n                    <h3>پشتیبانی از راه دور<\\/h3>\\n                    <p>حل مشکلات نرم‌افزاری و ویندوز به صورت آنلاین.<\\/p>\\n                    <div class=\\\"lnk\\\">بیشتر بخوانید <i class=\\\"fa-solid fa-arrow-left\\\" style=\\\"font-size:12px;\\\"><\\/i><\\/div>\\n                <\\/div>\\n            <\\/a>\\n            <a href=\\\"\\/khadamat\\\" style=\\\"text-decoration:none; color:inherit;\\\">\\n                <div class=\\\"kart-khadamat\\\">\\n                    <div class=\\\"icon\\\" style=\\\"background:var(--rang-asli);\\\"><i class=\\\"fa-solid fa-user-tie\\\"><\\/i><\\/div>\\n                    <h3>پشتیبانی حضوری<\\/h3>\\n                    <p>مراجعه به محل شما در تهران برای تعمیرات.<\\/p>\\n                    <div class=\\\"lnk\\\">بیشتر بخوانید <i class=\\\"fa-solid fa-arrow-left\\\" style=\\\"font-size:12px;\\\"><\\/i><\\/div>\\n                <\\/div>\\n            <\\/a>\\n            <a href=\\\"\\/khadamat\\\" style=\\\"text-decoration:none; color:inherit;\\\">\\n                <div class=\\\"kart-khadamat\\\">\\n                    <div class=\\\"icon\\\" style=\\\"background:var(--rang-asli);\\\"><i class=\\\"fa-solid fa-bolt\\\"><\\/i><\\/div>\\n                    <h3>رفع کندی سیستم<\\/h3>\\n                    <p>بهینه‌سازی کامل ویندوز و افزایش سرعت.<\\/p>\\n                    <div class=\\\"lnk\\\">بیشتر بخوانید <i class=\\\"fa-solid fa-arrow-left\\\" style=\\\"font-size:12px;\\\"><\\/i><\\/div>\\n                <\\/div>\\n            <\\/a>\\n            <a href=\\\"\\/khadamat\\\" style=\\\"text-decoration:none; color:inherit;\\\">\\n                <div class=\\\"kart-khadamat\\\">\\n                    <div class=\\\"icon\\\" style=\\\"background:var(--rang-asli);\\\"><i class=\\\"fa-solid fa-code\\\"><\\/i><\\/div>\\n                    <h3>طراحی سایت<\\/h3>\\n                    <p>سایت شرکتی، فروشگاهی و شخصی.<\\/p>\\n                    <div class=\\\"lnk\\\">بیشتر بخوانید <i class=\\\"fa-solid fa-arrow-left\\\" style=\\\"font-size:12px;\\\"><\\/i><\\/div>\\n                <\\/div>\\n            <\\/a>\\n            <a href=\\\"\\/khadamat\\\" style=\\\"text-decoration:none; color:inherit;\\\">\\n                <div class=\\\"kart-khadamat\\\">\\n                    <div class=\\\"icon\\\" style=\\\"background:var(--rang-asli);\\\"><i class=\\\"fa-solid fa-laptop-code\\\"><\\/i><\\/div>\\n                    <h3>برنامه‌نویسی<\\/h3>\\n                    <p>نرم‌افزار سفارشی مطابق نیاز شما.<\\/p>\\n                    <div class=\\\"lnk\\\">بیشتر بخوانید <i class=\\\"fa-solid fa-arrow-left\\\" style=\\\"font-size:12px;\\\"><\\/i><\\/div>\\n                <\\/div>\\n            <\\/a>\\n            <a href=\\\"\\/khadamat\\\" style=\\\"text-decoration:none; color:inherit;\\\">\\n                <div class=\\\"kart-khadamat\\\">\\n                    <div class=\\\"icon\\\" style=\\\"background:var(--rang-asli);\\\"><i class=\\\"fa-solid fa-network-wired\\\"><\\/i><\\/div>\\n                    <h3>شبکه و اینترنت<\\/h3>\\n                    <p>راه‌اندازی و بهینه‌سازی شبکه و مودم.<\\/p>\\n                    <div class=\\\"lnk\\\">بیشتر بخوانید <i class=\\\"fa-solid fa-arrow-left\\\" style=\\\"font-size:12px;\\\"><\\/i><\\/div>\\n                <\\/div>\\n            <\\/a>\\n        <\\/div>\\n\\n        <div style=\\\"text-align:center; margin-top:40px;\\\">\\n            <a href=\\\"\\/khadamat\\\" class=\\\"dakmeh dakmeh-asli\\\">\\n                مشاهده همه خدمات\\n                <i class=\\\"fa-solid fa-arrow-left\\\"><\\/i>\\n            <\\/a>\\n        <\\/div>\\n\\n    <\\/div>\\n<\\/section>\\n\\n<!-- ===== چرا ما ===== -->\\n<section class=\\\"bakhsh\\\">\\n    <div class=\\\"mohtava-container\\\">\\n\\n        <div class=\\\"onvan-bakhsh\\\">\\n            <span class=\\\"barg\\\">مزیت‌های ما<\\/span>\\n            <h2>چرا مهراد سام؟<\\/h2>\\n            <p>تجربه، سرعت و کیفیت در یک مجموعه<\\/p>\\n        <\\/div>\\n\\n        <div class=\\\"gerid-4\\\">\\n            <div style=\\\"text-align:center; padding:32px 20px;\\\">\\n                <div style=\\\"width:72px; height:72px; background:#FF6F00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);\\\">\\n                    <i class=\\\"fa-solid fa-bolt\\\" style=\\\"font-size:28px; color:#fff;\\\"><\\/i>\\n                <\\/div>\\n                <h3 style=\\\"font-size:1rem; margin-bottom:8px;\\\">سرعت بالا<\\/h3>\\n                <p style=\\\"font-size:13px; color:#888; line-height:1.8;\\\">رفع مشکل در کمترین زمان ممکن<\\/p>\\n            <\\/div>\\n            <div style=\\\"text-align:center; padding:32px 20px;\\\">\\n                <div style=\\\"width:72px; height:72px; background:#E65100; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(230,81,0,0.25);\\\">\\n                    <i class=\\\"fa-solid fa-headset\\\" style=\\\"font-size:28px; color:#fff;\\\"><\\/i>\\n                <\\/div>\\n                <h3 style=\\\"font-size:1rem; margin-bottom:8px;\\\">پشتیبانی ۲۴\\/۷<\\/h3>\\n                <p style=\\\"font-size:13px; color:#888; line-height:1.8;\\\">در دسترس بودن برای رفع فوری مشکلات<\\/p>\\n            <\\/div>\\n            <div style=\\\"text-align:center; padding:32px 20px;\\\">\\n                <div style=\\\"width:72px; height:72px; background:#BF360C; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(191,54,12,0.25);\\\">\\n                    <i class=\\\"fa-solid fa-shield-halved\\\" style=\\\"font-size:28px; color:#fff;\\\"><\\/i>\\n                <\\/div>\\n                <h3 style=\\\"font-size:1rem; margin-bottom:8px;\\\">امنیت کامل<\\/h3>\\n                <p style=\\\"font-size:13px; color:#888; line-height:1.8;\\\">حفظ اطلاعات و حریم خصوصی شما<\\/p>\\n            <\\/div>\\n            <div style=\\\"text-align:center; padding:32px 20px;\\\">\\n                <div style=\\\"width:72px; height:72px; background:#FF6F00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);\\\">\\n                    <i class=\\\"fa-solid fa-thumbs-up\\\" style=\\\"font-size:28px; color:#fff;\\\"><\\/i>\\n                <\\/div>\\n                <h3 style=\\\"font-size:1rem; margin-bottom:8px;\\\">تضمین کیفیت<\\/h3>\\n                <p style=\\\"font-size:13px; color:#888; line-height:1.8;\\\">رضایت شما اولویت اصلی ماست<\\/p>\\n            <\\/div>\\n        <\\/div>\\n\\n    <\\/div>\\n<\\/section>\\n\\n<!-- ===== فراخوان اقدام (CTA) ===== -->\\n<section style=\\\"background:linear-gradient(135deg, var(--rang-asli) 0%, var(--rang-tira) 100%); padding:70px 0;\\\">\\n    <div class=\\\"mohtava-container\\\" style=\\\"text-align:center;\\\">\\n        <h2 style=\\\"color:#fff; font-size:1.8rem; margin-bottom:12px;\\\">آماده کمک به شما هستیم!<\\/h2>\\n        <p style=\\\"color:rgba(255,255,255,0.85); margin-bottom:32px; font-size:1rem;\\\">\\n            همین الان با ما تماس بگیرید و مشکل خود را حل کنید\\n        <\\/p>\\n        <div style=\\\"display:flex; gap:16px; justify-content:center; flex-wrap:wrap;\\\">\\n            <a href=\\\"\\/tamas\\\" style=\\\"background:#fff; color:var(--rang-asli); padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;\\\"\\n               onmouseover=\\\"this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 8px 25px rgba(0,0,0,0.2)\'\\\"\\n               onmouseout=\\\"this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'none\'\\\">\\n                <i class=\\\"fa-solid fa-envelope\\\"><\\/i>\\n                ارسال پیام\\n            <\\/a>\\n            <a href=\\\"tel:989105921358\\\" style=\\\"background:transparent; color:#fff; padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; border:2px solid rgba(255,255,255,0.6); display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;\\\"\\n               onmouseover=\\\"this.style.borderColor=\'#fff\'; this.style.background=\'rgba(255,255,255,0.1)\'\\\"\\n               onmouseout=\\\"this.style.borderColor=\'rgba(255,255,255,0.6)\'; this.style.background=\'transparent\'\\\">\\n                <i class=\\\"fa-solid fa-phone\\\"><\\/i>\\n                989105921358\\n            <\\/a>\\n        <\\/div>\\n    <\\/div>\\n<\\/section>\"}}]',1,'0','<div class=\"builder-free-canvas\"><style class=\"builder-pos-css\">.builder-free-canvas{position:relative;min-height:600px;}.builder-free-canvas .bpos-0,.builder-free-canvas [class*=bpos-]{box-sizing:border-box;}</style><div class=\"bpos-0\" data-block-index=\"0\">مشکل کامپیوترت رو\nسریع حل می‌کنیم</div></div>',NULL,'2026-07-16 11:37:29','2026-07-31 04:55:14'),(100014,17,'blog','محتوای blog #17','single','blog','','[]',0,'auto',NULL,NULL,'2026-07-17 18:33:35','2026-07-17 18:33:35'),(100015,1,'safhe','درباره ما','single','safhe','','[{\"type\":\"custom\",\"data\":{\"html\":\"اینجا صفحه‌ی درباره ما است.\"}}]',0,'auto',NULL,NULL,'2026-07-21 04:03:26','2026-07-21 04:03:26');
/*!40000 ALTER TABLE `block_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'نرم‌افزار','software','مقالات مرتبط با نرم‌افزار، ویندوز و برنامه‌ها'),(2,'سخت‌افزار','hardware','مقالات مرتبط با قطعات کامپیوتر و سخت‌افزار'),(3,'شبکه و امنیت','network','مقالات مرتبط با شبکه، اینترنت و امنیت'),(4,'آموزشی','tutorial','آموزش‌های گام‌به‌گام و راهنماها');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `sender_type` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
INSERT INTO `chat_messages` VALUES (11,5,'user','سلام، چطور میتونم کمک کنم؟','2026-07-07 22:00:49'),(12,5,'user','سلام','2026-07-07 22:00:52'),(13,5,'admin','سلام','2026-07-07 22:09:29');
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_sessions`
--

DROP TABLE IF EXISTS `chat_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('waiting','active','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'waiting',
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_token` (`session_token`),
  KEY `idx_status` (`status`),
  KEY `idx_activity` (`last_activity`),
  CONSTRAINT `chat_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_sessions`
--

LOCK TABLES `chat_sessions` WRITE;
/*!40000 ALTER TABLE `chat_sessions` DISABLE KEYS */;
INSERT INTO `chat_sessions` VALUES (5,NULL,'b6922ec786953cf7dc67bbcd472505acfb393c8ac8f9ac101747525d160de89b','علی','09105921358','','active','2026-07-07 22:09:29','2026-07-07 22:00:49');
/*!40000 ALTER TABLE `chat_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `farband`
--

DROP TABLE IF EXISTS `farband`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farband` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `onvan` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'عنوان آیتم منو',
  `masir` varchar(500) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'آدرس لینک',
  `parent_id` int(11) DEFAULT '0' COMMENT '0=سطح اول',
  `tartib` int(11) DEFAULT '0' COMMENT 'ترتیب نمایش',
  `makan` varchar(50) COLLATE utf8mb4_persian_ci DEFAULT 'header' COMMENT 'header | footer | sidebar',
  `tab_jadid` tinyint(1) DEFAULT '0' COMMENT 'باز شدن در تب جدید',
  PRIMARY KEY (`id`),
  KEY `idx_makan_tartib` (`makan`,`tartib`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `farband`
--

LOCK TABLES `farband` WRITE;
/*!40000 ALTER TABLE `farband` DISABLE KEYS */;
INSERT INTO `farband` VALUES (1,'خانه','/',0,1,'header',0),(2,'خدمات','/khadamat',0,2,'header',0),(3,'تارنگار','/tarnegar',0,3,'header',0),(4,'تماس با ما','/tamas',0,4,'header',0),(5,'درباره ما','/mohtava/safhe/darbare-ma',0,1,'footer',0),(6,'خدمات','/khadamat',0,2,'footer',0),(7,'تماس با ما','/tamas',0,3,'footer',0);
/*!40000 ALTER TABLE `farband` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_usage`
--

DROP TABLE IF EXISTS `file_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_path` varchar(500) COLLATE utf8mb4_persian_ci NOT NULL,
  `content_type` varchar(20) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'post|page|product|article|custom',
  `content_id` int(11) DEFAULT '0',
  `note` varchar(255) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `file_path` (`file_path`),
  KEY `content_type` (`content_type`,`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_usage`
--

LOCK TABLES `file_usage` WRITE;
/*!40000 ALTER TABLE `file_usage` DISABLE KEYS */;
/*!40000 ALTER TABLE `file_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_ip_time` (`ip_address`,`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahsul_brand`
--

DROP TABLE IF EXISTS `mahsul_brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahsul_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `onvan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tozih` text COLLATE utf8mb4_unicode_ci,
  `tartib` int(11) DEFAULT '0',
  `vaziat` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahsul_brand`
--

LOCK TABLES `mahsul_brand` WRITE;
/*!40000 ALTER TABLE `mahsul_brand` DISABLE KEYS */;
/*!40000 ALTER TABLE `mahsul_brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahsul_dasteh`
--

DROP TABLE IF EXISTS `mahsul_dasteh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahsul_dasteh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `onvan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tartib` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahsul_dasteh`
--

LOCK TABLES `mahsul_dasteh` WRITE;
/*!40000 ALTER TABLE `mahsul_dasteh` DISABLE KEYS */;
/*!40000 ALTER TABLE `mahsul_dasteh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahsulat`
--

DROP TABLE IF EXISTS `mahsulat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahsulat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `onvan` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dasteh_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `gheymat` decimal(12,0) NOT NULL DEFAULT '0',
  `gheymat_takhfif` decimal(12,0) DEFAULT NULL,
  `tozih` text COLLATE utf8mb4_unicode_ci,
  `virayesh` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `tasvir` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mojood` int(11) NOT NULL DEFAULT '0',
  `vaziat` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `dasteh_id` (`dasteh_id`),
  CONSTRAINT `mahsulat_ibfk_1` FOREIGN KEY (`dasteh_id`) REFERENCES `mahsul_dasteh` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahsulat`
--

LOCK TABLES `mahsulat` WRITE;
/*!40000 ALTER TABLE `mahsulat` DISABLE KEYS */;
/*!40000 ALTER TABLE `mahsulat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payam_tamas`
--

DROP TABLE IF EXISTS `payam_tamas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payam_tamas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nam` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'نام فرستنده',
  `email` varchar(100) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `telefon` varchar(20) COLLATE utf8mb4_persian_ci DEFAULT NULL COMMENT 'شماره تماس',
  `mozoo` varchar(255) COLLATE utf8mb4_persian_ci DEFAULT NULL COMMENT 'موضوع پیام',
  `payam` text COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'متن پیام',
  `khande_shode` tinyint(1) DEFAULT '0' COMMENT '0=خوانده نشده 1=خوانده شده',
  `ip_address` varchar(45) COLLATE utf8mb4_persian_ci DEFAULT NULL COMMENT 'IP فرستنده',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_khande` (`khande_shode`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payam_tamas`
--

LOCK TABLES `payam_tamas` WRITE;
/*!40000 ALTER TABLE `payam_tamas` DISABLE KEYS */;
INSERT INTO `payam_tamas` VALUES (1,'محمد علی عسگری','ali.asgari.6106@gmail.com','09105921358','پشتیبانی از راه دور','سلام به پشتیبانی نیاز دارم',1,'127.0.0.1','2026-07-08 19:28:56');
/*!40000 ALTER TABLE `payam_tamas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_categories`
--

LOCK TABLES `post_categories` WRITE;
/*!40000 ALTER TABLE `post_categories` DISABLE KEYS */;
INSERT INTO `post_categories` VALUES (15,1),(16,2),(17,3),(18,2),(19,1),(20,1);
/*!40000 ALTER TABLE `post_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT '1',
  `title` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_persian_ci,
  `kholaseh` text COLLATE utf8mb4_persian_ci COMMENT 'خلاصه / چکیده',
  `subtitle` varchar(200) COLLATE utf8mb4_persian_ci DEFAULT '',
  `tasvir` varchar(500) COLLATE utf8mb4_persian_ci DEFAULT NULL COMMENT 'تصویر شاخص',
  `type` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL DEFAULT 'blog' COMMENT 'blog | safhe | maghaleh | khabar',
  `page_section` varchar(50) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `display_order` int(11) DEFAULT '0',
  `template` varchar(50) COLLATE utf8mb4_persian_ci DEFAULT 'default',
  `language` varchar(5) COLLATE utf8mb4_persian_ci DEFAULT 'fa',
  `status` varchar(20) COLLATE utf8mb4_persian_ci DEFAULT 'draft' COMMENT 'publish | draft | trash',
  `bazid` int(11) DEFAULT '0' COMMENT 'تعداد بازدید',
  `meta_title` varchar(255) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `meta_description` varchar(500) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `meta_keywords` text COLLATE utf8mb4_persian_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug_type` (`slug`,`type`),
  KEY `idx_type_status` (`type`,`status`),
  KEY `idx_author` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'درباره ما','darbare-ma','اینجا صفحه‌ی درباره ما است.',NULL,'',NULL,'safhe',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-06-28 06:56:10',NULL),(2,1,'آموزش PHP','amoozesh-php','PHP یک زبان قدرتمند است.',NULL,'',NULL,'maghaleh',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-06-28 06:56:10',NULL),(3,1,'مهراد سام | خانه','home','<!-- ===== هدر قهرمان (Hero) ===== -->\n<section style=\"background:linear-gradient(135deg, #FFF3E0 0%, #fff8f0 50%, #fff 100%); padding:90px 0 80px; overflow:hidden; position:relative;\">\n\n    <div style=\"position:absolute; left:-80px; top:-80px; width:350px; height:350px; border-radius:50%; background:rgba(255,111,0,0.06);\"></div>\n    <div style=\"position:absolute; right:-60px; bottom:-60px; width:250px; height:250px; border-radius:50%; background:rgba(255,111,0,0.04);\"></div>\n\n    <div class=\"mohtava-container\" style=\"position:relative; z-index:1;\">\n\n        <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center;\">\n\n            <!-- متن -->\n            <div>\n                <div style=\"display:inline-block; background:rgba(255,111,0,0.12); color:var(--rang-asli); font-size:13px; font-weight:700; padding:6px 18px; border-radius:20px; margin-bottom:20px;\">\n                    <i class=\"fa-solid fa-circle-check\" style=\"margin-left:6px;\"></i>\n                    خدمات حرفه‌ای کامپیوتر\n                </div>\n                <h1 style=\"font-size:2.4rem; line-height:1.5; color:#1a1a1a; margin-bottom:20px; font-weight:700;\">\n                    مشکل کامپیوترت رو<br>\n                    <span style=\"color:var(--rang-asli);\">سریع حل می‌کنیم</span>\n                </h1>\n                <div style=\"font-size:1rem; color:#555; line-height:2; margin-bottom:32px; max-width:480px;\">\n                    <p>پشتیبانی از راه دور و حضوری در تهران. تیم فنی مهراد سام آماده رفع مشکلات نرم‌افزاری و سخت‌افزاری شماست.</p>\n                </div>\n                <div style=\"display:flex; gap:14px; flex-wrap:wrap;\">\n                    <a href=\"/tamas\" class=\"dakmeh dakmeh-asli\">\n                        <i class=\"fa-solid fa-phone-volume\"></i>\n                        تماس بگیرید\n                    </a>\n                    <a href=\"/khadamat\" class=\"dakmeh dakmeh-khali\">\n                        مشاهده خدمات\n                        <i class=\"fa-solid fa-arrow-left\"></i>\n                    </a>\n                </div>\n\n                <!-- آمار سریع -->\n                <div style=\"display:flex; gap:32px; margin-top:40px; padding-top:32px; border-top:1px solid #eee;\">\n                    <div>\n                        <div style=\"font-size:1.8rem; font-weight:700; color:var(--rang-asli);\">۵+</div>\n                        <div style=\"font-size:13px; color:#888;\">سال تجربه</div>\n                    </div>\n                    <div>\n                        <div style=\"font-size:1.8rem; font-weight:700; color:var(--rang-asli);\">۵۰۰+</div>\n                        <div style=\"font-size:13px; color:#888;\">مشتری راضی</div>\n                    </div>\n                    <div>\n                        <div style=\"font-size:1.8rem; font-weight:700; color:var(--rang-asli);\">۹</div>\n                        <div style=\"font-size:13px; color:#888;\">خدمت تخصصی</div>\n                    </div>\n                </div>\n            </div>\n\n            <!-- تصویر -->\n            <div style=\"text-align:center; display:flex; align-items:center; justify-content:center;\">\n                <div style=\"width:320px; height:320px; background:linear-gradient(135deg, var(--rang-asli), var(--rang-tira)); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 20px 60px rgba(255,111,0,0.3); position:relative;\">\n                    <i class=\"fa-solid fa-laptop-code\" style=\"font-size:120px; color:#fff; opacity:0.9;\"></i>\n                    <div style=\"position:absolute; top:20px; right:-10px; background:#fff; border-radius:12px; padding:10px 16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap;\">\n                        <i class=\"fa-solid fa-wifi\" style=\"color:var(--rang-asli); margin-left:6px;\"></i>\n                        پشتیبانی آنلاین\n                    </div>\n                    <div style=\"position:absolute; bottom:30px; left:-20px; background:#fff; border-radius:12px; padding:10px 16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap;\">\n                        <i class=\"fa-solid fa-check-circle\" style=\"color:#4caf50; margin-left:6px;\"></i>\n                        تضمین کیفیت\n                    </div>\n                </div>\n            </div>\n\n        </div>\n    </div>\n\n    <style>\n        @media (max-width:768px) {\n            section>.mohtava-container>div { grid-template-columns:1fr !important; }\n            section>.mohtava-container>div>div:last-child { display:none !important; }\n            h1 { font-size:1.8rem !important; }\n        }\n    </style>\n</section>\n\n<!-- ===== خدمات ما ===== -->\n<section class=\"bakhsh bakhsh-sabz\">\n    <div class=\"mohtava-container\">\n\n        <div class=\"onvan-bakhsh\">\n            <span class=\"barg\"><i class=\"fa-solid fa-star\" style=\"margin-left:5px;\"></i>خدمات ما</span>\n            <h2>چه کمکی می‌توانیم بکنیم؟</h2>\n            <p>طیف گسترده‌ای از خدمات کامپیوتری برای رفع نیازهای شما</p>\n        </div>\n\n        <div class=\"gerid-3\">\n            <a href=\"/khadamat\" style=\"text-decoration:none; color:inherit;\">\n                <div class=\"kart-khadamat\">\n                    <div class=\"icon\" style=\"background:var(--rang-asli);\"><i class=\"fa-solid fa-wifi\"></i></div>\n                    <h3>پشتیبانی از راه دور</h3>\n                    <p>حل مشکلات نرم‌افزاری و ویندوز به صورت آنلاین.</p>\n                    <div class=\"lnk\">بیشتر بخوانید <i class=\"fa-solid fa-arrow-left\" style=\"font-size:12px;\"></i></div>\n                </div>\n            </a>\n            <a href=\"/khadamat\" style=\"text-decoration:none; color:inherit;\">\n                <div class=\"kart-khadamat\">\n                    <div class=\"icon\" style=\"background:var(--rang-asli);\"><i class=\"fa-solid fa-user-tie\"></i></div>\n                    <h3>پشتیبانی حضوری</h3>\n                    <p>مراجعه به محل شما در تهران برای تعمیرات.</p>\n                    <div class=\"lnk\">بیشتر بخوانید <i class=\"fa-solid fa-arrow-left\" style=\"font-size:12px;\"></i></div>\n                </div>\n            </a>\n            <a href=\"/khadamat\" style=\"text-decoration:none; color:inherit;\">\n                <div class=\"kart-khadamat\">\n                    <div class=\"icon\" style=\"background:var(--rang-asli);\"><i class=\"fa-solid fa-bolt\"></i></div>\n                    <h3>رفع کندی سیستم</h3>\n                    <p>بهینه‌سازی کامل ویندوز و افزایش سرعت.</p>\n                    <div class=\"lnk\">بیشتر بخوانید <i class=\"fa-solid fa-arrow-left\" style=\"font-size:12px;\"></i></div>\n                </div>\n            </a>\n            <a href=\"/khadamat\" style=\"text-decoration:none; color:inherit;\">\n                <div class=\"kart-khadamat\">\n                    <div class=\"icon\" style=\"background:var(--rang-asli);\"><i class=\"fa-solid fa-code\"></i></div>\n                    <h3>طراحی سایت</h3>\n                    <p>سایت شرکتی، فروشگاهی و شخصی.</p>\n                    <div class=\"lnk\">بیشتر بخوانید <i class=\"fa-solid fa-arrow-left\" style=\"font-size:12px;\"></i></div>\n                </div>\n            </a>\n            <a href=\"/khadamat\" style=\"text-decoration:none; color:inherit;\">\n                <div class=\"kart-khadamat\">\n                    <div class=\"icon\" style=\"background:var(--rang-asli);\"><i class=\"fa-solid fa-laptop-code\"></i></div>\n                    <h3>برنامه‌نویسی</h3>\n                    <p>نرم‌افزار سفارشی مطابق نیاز شما.</p>\n                    <div class=\"lnk\">بیشتر بخوانید <i class=\"fa-solid fa-arrow-left\" style=\"font-size:12px;\"></i></div>\n                </div>\n            </a>\n            <a href=\"/khadamat\" style=\"text-decoration:none; color:inherit;\">\n                <div class=\"kart-khadamat\">\n                    <div class=\"icon\" style=\"background:var(--rang-asli);\"><i class=\"fa-solid fa-network-wired\"></i></div>\n                    <h3>شبکه و اینترنت</h3>\n                    <p>راه‌اندازی و بهینه‌سازی شبکه و مودم.</p>\n                    <div class=\"lnk\">بیشتر بخوانید <i class=\"fa-solid fa-arrow-left\" style=\"font-size:12px;\"></i></div>\n                </div>\n            </a>\n        </div>\n\n        <div style=\"text-align:center; margin-top:40px;\">\n            <a href=\"/khadamat\" class=\"dakmeh dakmeh-asli\">\n                مشاهده همه خدمات\n                <i class=\"fa-solid fa-arrow-left\"></i>\n            </a>\n        </div>\n\n    </div>\n</section>\n\n<!-- ===== چرا ما ===== -->\n<section class=\"bakhsh\">\n    <div class=\"mohtava-container\">\n\n        <div class=\"onvan-bakhsh\">\n            <span class=\"barg\">مزیت‌های ما</span>\n            <h2>چرا مهراد سام؟</h2>\n            <p>تجربه، سرعت و کیفیت در یک مجموعه</p>\n        </div>\n\n        <div class=\"gerid-4\">\n            <div style=\"text-align:center; padding:32px 20px;\">\n                <div style=\"width:72px; height:72px; background:#FF6F00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);\">\n                    <i class=\"fa-solid fa-bolt\" style=\"font-size:28px; color:#fff;\"></i>\n                </div>\n                <h3 style=\"font-size:1rem; margin-bottom:8px;\">سرعت بالا</h3>\n                <p style=\"font-size:13px; color:#888; line-height:1.8;\">رفع مشکل در کمترین زمان ممکن</p>\n            </div>\n            <div style=\"text-align:center; padding:32px 20px;\">\n                <div style=\"width:72px; height:72px; background:#E65100; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(230,81,0,0.25);\">\n                    <i class=\"fa-solid fa-headset\" style=\"font-size:28px; color:#fff;\"></i>\n                </div>\n                <h3 style=\"font-size:1rem; margin-bottom:8px;\">پشتیبانی ۲۴/۷</h3>\n                <p style=\"font-size:13px; color:#888; line-height:1.8;\">در دسترس بودن برای رفع فوری مشکلات</p>\n            </div>\n            <div style=\"text-align:center; padding:32px 20px;\">\n                <div style=\"width:72px; height:72px; background:#BF360C; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(191,54,12,0.25);\">\n                    <i class=\"fa-solid fa-shield-halved\" style=\"font-size:28px; color:#fff;\"></i>\n                </div>\n                <h3 style=\"font-size:1rem; margin-bottom:8px;\">امنیت کامل</h3>\n                <p style=\"font-size:13px; color:#888; line-height:1.8;\">حفظ اطلاعات و حریم خصوصی شما</p>\n            </div>\n            <div style=\"text-align:center; padding:32px 20px;\">\n                <div style=\"width:72px; height:72px; background:#FF6F00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 20px rgba(255,111,0,0.25);\">\n                    <i class=\"fa-solid fa-thumbs-up\" style=\"font-size:28px; color:#fff;\"></i>\n                </div>\n                <h3 style=\"font-size:1rem; margin-bottom:8px;\">تضمین کیفیت</h3>\n                <p style=\"font-size:13px; color:#888; line-height:1.8;\">رضایت شما اولویت اصلی ماست</p>\n            </div>\n        </div>\n\n    </div>\n</section>\n\n<!-- ===== فراخوان اقدام (CTA) ===== -->\n<section style=\"background:linear-gradient(135deg, var(--rang-asli) 0%, var(--rang-tira) 100%); padding:70px 0;\">\n    <div class=\"mohtava-container\" style=\"text-align:center;\">\n        <h2 style=\"color:#fff; font-size:1.8rem; margin-bottom:12px;\">آماده کمک به شما هستیم!</h2>\n        <p style=\"color:rgba(255,255,255,0.85); margin-bottom:32px; font-size:1rem;\">\n            همین الان با ما تماس بگیرید و مشکل خود را حل کنید\n        </p>\n        <div style=\"display:flex; gap:16px; justify-content:center; flex-wrap:wrap;\">\n            <a href=\"/tamas\" style=\"background:#fff; color:var(--rang-asli); padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;\"\n               onmouseover=\"this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 8px 25px rgba(0,0,0,0.2)\'\"\n               onmouseout=\"this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'none\'\">\n                <i class=\"fa-solid fa-envelope\"></i>\n                ارسال پیام\n            </a>\n            <a href=\"tel:989105921358\" style=\"background:transparent; color:#fff; padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; border:2px solid rgba(255,255,255,0.6); display:inline-flex; align-items:center; gap:8px; transition:all 0.3s;\"\n               onmouseover=\"this.style.borderColor=\'#fff\'; this.style.background=\'rgba(255,255,255,0.1)\'\"\n               onmouseout=\"this.style.borderColor=\'rgba(255,255,255,0.6)\'; this.style.background=\'transparent\'\">\n                <i class=\"fa-solid fa-phone\"></i>\n                989105921358\n            </a>\n        </div>\n    </div>\n</section>',NULL,'',NULL,'safhe','hero',0,'home','fa','publish',0,NULL,'خدمات پشتیبانی کامپیوتر مهراد سام در تهران | تعمیر لپ‌تاپ، نصب ویندوز، آنتی‌ویروس، طراحی سایت',NULL,'2026-07-07 13:00:55','2026-07-09 12:09:42'),(4,1,'خدمات','khadamat','<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#00B894;\"><i class=\"fa-solid fa-wifi\"></i></span>\n    <div>\n      <h2>پشتیبانی از راه دور</h2>\n      <p>حل مشکلات نرم‌افزاری و ویندوز به صورت آنلاین و تیم‌ویور.</p>\n    </div>\n  </summary>\n  <div><h3>پشتیبانی از راه دور (آنلاین)</h3>\n<p>تیم فنی مهراد سام با استفاده از نرم‌افزارهای کنترل از راه دور (TeamViewer، AnyDesk، RustDesk) به سیستم شما متصل شده و مشکلات نرم‌افزاری را بدون نیاز به مراجعه حضوری برطرف می‌کند.</p>\n<h4>شامل موارد زیر:</h4>\n<ul>\n<li>نصب و کانفیگ ویندوز ۱۰/۱۱</li>\n<li>رفع ارورهای بوت، BSOD، و درایورها</li>\n<li>نصب آنتی‌ویروس و تنظیم فایروال</li>\n<li>بهینه‌سازی سرعت ویندوز و پاک‌سازی فایل‌های موقت</li>\n<li>راهنمای خرید و نصب سخت‌افزار</li>\n</ul>\n<p><strong>مزایا:</strong> سریع، ارزان‌تر از مراجعه حضوری، و در کل شهر تهران قابل ارائه.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#0984E3;\"><i class=\"fa-solid fa-user-tie\"></i></span>\n    <div>\n      <h2>پشتیبانی حضوری</h2>\n      <p>مراجعه به محل شما در تهران برای عیب‌یابی و تعمیر سخت‌افزاری/نرم‌افزاری.</p>\n    </div>\n  </summary>\n  <div><h3>پشتیبانی حضوری در محل</h3>\n<p>تیم فنی به آدرس شما در تهران مراجعه کرده و خدمات را در محل انجام می‌دهد. مناسب برای مواردی که نیاز به بررسی فیزیکی سخت‌افزار دارند.</p>\n<h4>خدمات:</h4>\n<ul>\n<li>تعویض قطعات لپ‌تاپ (صفحه نمایش، کیبورد، باتری، هارد، رم)</li>\n<li>تمیزکاری داخلی و thay جیل حرارتی CPU/GPU</li>\n<li>معماری شبکه، کانفیگ مودم/راوتر، کابل‌کشی</li>\n<li>نصب سیستم‌های امنیتی (دوربین، کنترل دسترسی)</li>\n<li>بازیابی داده از هارد خراب/فرمت شده</li>\n</ul>\n<p><strong>زمان‌بندی:</strong> روزهای شنبه تا پنجشنبه، ۹ صبح تا ۸ عصر.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#FF6F00;\"><i class=\"fa-solid fa-bolt\"></i></span>\n    <div>\n      <h2>رفع کندی سیستم</h2>\n      <p>بهینه‌سازی کامل ویندوز، پاک‌سازی فایل‌های زائد، و افزایش سرعت بوت.</p>\n    </div>\n  </summary>\n  <div><h3>رفع کندی و بهینه‌سازی سیستم</h3>\n<p>سیستم شما کند شده؟ برنامه‌ها دیر باز می‌شوند؟ ما با ابزارهای حرفه‌ای سیستم را تحلیل و بهینه می‌کنیم.</p>\n<h4>مراحل انجام شده:</h4>\n<ol>\n<li>اسکن و حذف ویروس/مال‌وِر با ابزارهای پیشرفته</li>\n<li>پاک‌سازی رجیستری، فایل‌های موقت، و کش مرورگرها</li>\n<li>غیرفعال کردن برنامه‌های استارتاپ غیرضروری</li>\n<li>چک و آپدیت درایورها (GPU، چیپست، صدا، شبکه)</li>\n<li>تنظیم گزینه‌های پاور برای عملکرد بهینه</li>\n<li>دیفراگمنت هارد (برای HDD) یا بهینه‌سازی SSD (TRIM)</li>\n</ol>\n<p><strong>نتیجه:</strong> سیستم سریع‌تر، سبک‌تر، و پایدارتر.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#6C5CE7;\"><i class=\"fa-solid fa-download\"></i></span>\n    <div>\n      <h2>نصب نرم‌افزار</h2>\n      <p>نصب و کانفیگ ویندوز، آفیس، آنتی‌ویروس، و نرم‌افزارهای تخصصی.</p>\n    </div>\n  </summary>\n  <div><h3>نصب و راه‌اندازی نرم‌افزار</h3>\n<p>نصب صحیح نرم‌افزارها از اهمیت بالایی برای ثبات سیستم برخوردار است. ما با لایسنس‌های اورجینال و نسخه‌های معتبر کار می‌کنیم.</p>\n<h4>نرم‌افزارهای رایج:</h4>\n<ul>\n<li>ویندوز ۱۰/۱۱ (نسخه اصلی، فعال‌سازی دائم)</li>\n<li>مایکروسافت آفیس ۲۰۲۱/۳۶۵</li>\n<li>آنتی‌ویروس: Kaspersky، ESET، Bitdefender، Windows Defender پیشرفته</li>\n<li>ادوبی: فتوشاپ، ایلاستریتور، پرمیر، آکروبات</li>\n<li>برنامه‌نویسی: VS Code، Python، Node.js، Docker، Git</li>\n<li>حسابداری: هلو، سپیدار، शायद</li>\n</ul>\n<p><strong>نکته:</strong> قبل از نصب، بک‌آپ از داده‌های مهم گرفته می‌شود.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#E17055;\"><i class=\"fa-solid fa-shield-halved\"></i></span>\n    <div>\n      <h2>نصب آنتی‌ویروس</h2>\n      <p>نصب، کانفیگ، و آموزش استفاده از آنتی‌ویروس‌های قدرتمند.</p>\n    </div>\n  </summary>\n  <div><h3>نصب و کانفیگ آنتی‌ویروس حرفه‌ای</h3>\n<p>محافظت از سیستم در برابر ویروس، رنسوم‌ور، اسپای‌ور، و حملات فیشینگ اولویت اول ماست.</p>\n<h4>آنتی‌ویروس‌های پیشنهادی:</h4>\n<ul>\n<li><strong>Kaspersky Total Security:</strong> محافظت کامل، کنترل والدین، مدیریت رمز عبور</li>\n<li><strong>ESET Internet Security:</strong> سبک، سریع، تشخیص پیشرفته تهدیدات</li>\n<li><strong>Bitdefender Total Security:</strong> محافظت چندلایه، VPN، Anti-tracker</li>\n<li><strong>Windows Defender (پیشرفته):</strong> رایگان، یکپارچه با ویندوز، بهینه برای سیستم‌های ضعیف</li>\n</ul>\n<h4>شامل:</h4>\n<ul>\n<li>نصب و آپدیت امضاهای ویروس</li>\n<li>تنظیم اسکن زمان‌بندی شده، محافظت بی‌درنگ، فایروال</li>\n<li>استثنا گذاری پوشه‌های قابل اعتماد</li>\n<li>آموزش تشخیص ایمیل/لینک‌های مشکوک</li>\n</ul></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#00B894;\"><i class=\"fa-solid fa-code\"></i></span>\n    <div>\n      <h2>طراحی سایت</h2>\n      <p>طراحی و توسعه وب‌سایت‌های شرکتی، فروشگاهی، و پورتال با پنل مدیریت فارسی.</p>\n    </div>\n  </summary>\n  <div><h3>طراحی و توسعه وب‌سایت</h3>\n<p>وب‌سایت شما کارت ویزیت آنلاین کسب‌وکار است. ما سایت‌های سریع، واکنش‌گرا (Responsive)، و بهینه‌سازی شده برای گوگل (SEO) می‌سازیم.</p>\n<h4>انواع سایت:</h4>\n<ul>\n<li><strong>شرکتی/مؤسسه‌ای:</strong> معرفی خدمات، تیم، رزومه، فرم تماس</li>\n<li><strong>فروشگاهی (E-commerce):</strong> سبد خرید، درگاه پرداخت، مدیریت سفارش، تخفیف، کوپن</li>\n<li><strong>پورتال/خبری:</strong> مدیریت مطالب، دسته‌بندی، جستجو، نظرسنجی، عضویت</li>\n<li><strong>لندینگ پیج (Landing Page):</strong> متمرکز بر تبدیل، تبلیغات گوگل/اینستاگرام</li>\n</ul>\n<h4>تکنولوژی‌ها:</h4>\n<ul>\n<li>Backend: PHP (Laravel، CodeIgniter، Core PHP)</li>\n<li>Database: MySQL، PostgreSQL، MongoDB، Redis</li>\n<li>Frontend: Vue 3، React، Alpine.js، Tailwind CSS</li>\n<li>DevOps: Docker، GitLab CI/CD، Nginx، Linux Server Management</li>\n</ul>\n<p><strong>شامل:</strong> دامنه، هاست، SSL، بهینه‌سازی سرعت، آموزش پنل، ۶ ماه پشتیبانی رایگان.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#0984E3;\"><i class=\"fa-solid fa-video\"></i></span>\n    <div>\n      <h2>دوربین مدار بسته</h2>\n      <p>فروش، نصب، و راه‌اندازی سیستم‌های CCTV/IP Camera با مانیتورینگ موبایل.</p>\n    </div>\n  </summary>\n  <div><h3>نصب و راه‌اندازی دوربین مدار بسته (CCTV)</h3>\n<p>امنیت محیط کار و خانه با سیستم‌های نظارت تصویری پیشرفته. نصب استاندارد با کابل‌کشی منظم و تنظیمات امنیتی.</p>\n<h4>انواع دوربین:</h4>\n<ul>\n<li>دوربین گنبال (Bullet) - فضای باز، مقاوم در برابر آب</li>\n<li>دوربین گنبدی (Dome) - فضای داخلی، ضد وندالیسم</li>\n<li>دوربین PTZ - کنترل از راه دور، زوم نوری، گردش ۳۶۰ درجه</li>\n<li>دوربین IP (شبکه) - کیفیت ۴K، تشخیص حرکت، هشدار هوشمند</li>\n</ul>\n<h4>شامل نصب:</h4>\n<ul>\n<li>DVR/NVR (ضبط و ذخیره‌سازی)</li>\n<li>هارد دیسک مخصوص نظارت (2TB تا ۱۶TB)</li>\n<li>کابل‌کشی Cat6/UPT، پاور، کانکتور</li>\n<li>تنظیم دید از راه دور (موبایل، کامپیوتر، تبلت)</li>\n<li>آموزش کار با اپلیکیشن و بک‌آپ ضبط</li>\n</ul>\n<p><strong>برندها:</strong> Hikvision، Dahua، HiLook، Tiandy، Imou.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#6C5CE7;\"><i class=\"fa-solid fa-laptop-code\"></i></span>\n    <div>\n      <h2>برنامه‌نویسی</h2>\n      <p>توسعه نرم‌افزار سفارشی، وب‌اپلیکیشن، API، و اتوماسیون فرآیندها.</p>\n    </div>\n  </summary>\n  <div><h3>توسعه نرم‌افزار سفارشی (Custom Software Development)</h3>\n<p>نرم‌افزار دقیقاً مطابق نیاز کسب‌وکار شما، بدون محدودیت‌های نرم‌افزارهای آماده.</p>\n<h4>خدمات توسعه:</h4>\n<ul>\n<li><strong>وب‌اپلیکیشن:</strong> پنل‌های مدیریتی، CRM، ERP، سیستم رزرواسیون، سامانه یادگیری (LMS)</li>\n<li><strong>API و وب‌سرویس:</strong> RESTful، GraphQL، یکپارچه‌سازی با درگاه پرداخت، پیامک، پست، حسابداری</li>\n<li><strong>اتوماسیون:</strong> ربات تلگرام/واتس‌اپ، اسکریپت‌های پایتون، زاپیار (Zapier)، Make (Integromat)</li>\n<li><strong>موبایل:</strong> اپلیکیشن اندروید (Kotlin) و iOS (Swift) - Native یا Flutter</li>\n</ul>\n<h4>تکنولوژی‌ها:</h4>\n<ul>\n<li>Backend: PHP (Laravel)، Python (Django/FastAPI)، Node.js (Express/NestJS)</li>\n<li>Database: MySQL، PostgreSQL، MongoDB، Redis</li>\n<li>Frontend: Vue 3، React، Alpine.js، Tailwind CSS</li>\n<li>DevOps: Docker، GitLab CI/CD، Nginx، Linux Server Management</li>\n</ul>\n<p><strong>فرآیند:</strong> تحلیل نیازمندی → طراحی دیتابیس/UI → توسعه → تست → استقرار → آموزش + ۶ ماه پشتیبانی.</p></div>\n</details>\n\n<details>\n  <summary>\n    <span class=\"service-icon\" style=\"background:#2D3436;\"><i class=\"fa-solid fa-network-wired\"></i></span>\n    <div>\n      <h2>شبکه و اینترنت</h2>\n      <p>راه‌اندازی، عیب‌یابی و بهینه‌سازی شبکه، مودم، وایرلس و اینترنت.</p>\n    </div>\n  </summary>\n  <div><h3>خدمات شبکه و اینترنت</h3>\n<p>راه‌اندازی شبکه‌های کامپیوتری خانگی و اداری، رفع مشکلات اینترنت، و بهینه‌سازی وایرلس با جدیدترین تجهیزات.</p>\n<h4>خدمات شبکه:</h4>\n<ul>\n<li>راه‌اندازی شبکه LAN/WLAN با کابل‌کشی استاندارد (Cat6/Cat7)</li>\n<li>کانفیگ مودم/راوتر (TP-Link، D-Link، Asus، MikroTik، Ubiquiti)</li>\n<li>رفع مشکل قطعی و کندی اینترنت، تنظیم DNS، پورت‌فورواردینگ</li>\n<li>نصب و راه‌اندازی شبکه مش (Mesh) برای پوشش کامل وایرلس</li>\n<li>راه‌اندازی سرور خانگی یا اداری (NAS، فایل سرور، پرینت سرور)</li>\n<li>امنیت شبکه: فایروال، VLAN، فیلترینگ مک آدرس، WPA3</li>\n<li>کابل‌کشی ساختاریافته، پچ پنل، رک، و تست فلوک</li>\n</ul>\n<h4>تجهیزات تخصصی:</h4>\n<ul>\n<li>مودم/راوتر: TP-Link، MikroTik، Ubiquiti UniFi، Asus</li>\n<li>سوئیچ: Cisco، HP، MikroTik، TP-Link Smart/Managed</li>\n<li>اکسس پوینت: UniFi، Omada، Grandstream</li>\n<li>کابل و کانکتور: Cat6 UTP/STP، فیبر نوری، کیستون، پچ کورد</li>\n</ul>\n<p>شامل مشاوره، طراحی نقشه شبکه، و آموزش کار با تجهیزات.</p></div>\n</details>\n',NULL,'',NULL,'safhe','header',0,'services','fa','publish',0,NULL,NULL,NULL,'2026-07-07 13:00:55','2026-07-09 10:26:42'),(5,1,'تارنگار','tarnegar','مطالب و اخبار و مقالات و آموزشهای سایت مهراد سام',NULL,'',NULL,'safhe','header',0,'blog','fa','publish',0,NULL,NULL,NULL,'2026-07-07 13:00:55','2026-07-07 23:24:05'),(6,1,'تماس','tamas','راه های ارتباط با مهراد سام',NULL,'',NULL,'safhe','header',0,'contact','fa','publish',0,NULL,NULL,NULL,'2026-07-07 13:00:55','2026-07-07 23:24:22'),(15,1,'آموزش عیب‌یابی کندی ویندوز ۱۰ و ۱۱ — ۱۰ راهکار تضمینی','windows-slow-fix','<h2>چرا ویندوز کند می‌شود؟</h2>\n<p>با گذشت زمان و نصب برنامه‌های مختلف، ویندوز شما کند می‌شود. فایل‌های موقت، startupهای اضافی، بدافزارها و قطعه‌های فرسوده از دلایل اصلی این مشکل هستند. در این مقاله ۱۰ روش عملی برای افزایش سرعت ویندوز را به شما آموزش می‌دهیم.</p>\n<h2>۱. حذف برنامه‌های استارت‌آپ</h2>\n<p>بسیاری از برنامه‌ها هنگام روشن شدن ویندوز، خودبه‌خود اجرا می‌شوند و باعث کندی سیستم می‌شوند. برای مدیریت آنها:</p>\n<ul><li>Task Manager را باز کنید (Ctrl + Shift + Esc)</li><li>به تب Startup بروید</li><li>برنامه‌های غیرضروری را Disable کنید</li></ul>\n<h2>۲. پاکسازی دیسک</h2>\n<p>ویندوز ابزار Disk Cleanup را دارد که فایل‌های موقت، کش و فایل‌های غیرضروری را پاک می‌کند. کافیست در منوی Start بنویسید Disk Cleanup و آن را اجرا کنید.</p>\n<h2>۳. غیرفعال کردن انیمیشن‌ها</h2>\n<p>انیمیشن‌های ویندوز روی سیستم‌های قدیمی فشار می‌آورند. برای غیرفعال کردن: Settings → Accessibility → Visual Effects → Turn off animation effects.</p>\n<h2>۴. بررسی سلامت هارد یا SSD</h2>\n<p>با ابزار <strong>CHKDSK</strong> می‌توانید سلامت دیسک خود را بررسی کنید. در Command Prompt (به عنوان Administrator) دستور <code>chkdsk C: /f</code> را وارد کنید.</p>\n<h2>۵. نصب ویندوز روی SSD</h2>\n<p>اگر هنوز از هارد HDD استفاده می‌کنید، ارتقاء به SSD بزرگترین تأثیر را در سرعت سیستم دارد. SSD ها تا ۱۰ برابر سریعتر از HDD هستند.</p>\n<h2>۶. افزایش رم (RAM)</h2>\n<p>اگر همزمان چندین برنامه باز می‌کنید، رم بیشتری نیاز دارید. ویندوز ۱۰ حداقل به ۸ گیگابایت رم نیاز دارد، اما ۱۶ گیگابایت توصیه می‌شود.</p>\n<h2>۷. غیرفعال کردن برنامه‌های پس‌زمینه</h2>\n<p>بسیاری از برنامه‌ها حتی وقتی بسته هستند، در پس‌زمینه اجرا می‌شوند. Settings → Privacy → Background apps → برنامه‌های غیرضروری را غیرفعال کنید.</p>\n<h2>۸. اسکن با Windows Defender</h2>\n<p>بدافزارها یکی از دلایل اصلی کندی سیستم هستند. با Windows Defender یک اسکن کامل (Full Scan) انجام دهید.</p>\n<h2>۹. به‌روزرسانی درایورها</h2>\n<p>درایورهای قدیمی می‌توانند باعث کندی و ناپایداری شوند. با Device Manager درایورهای خود را به‌روز کنید یا از نرم‌افزار SDI استفاده کنید.</p>\n<h2>۱۰. ریست یا نصب مجدد ویندوز</h2>\n<p>اگر هیچکدام از روش‌ها جواب نداد، آخرین راهکار Reset این PC است. Settings → Update & Security → Recovery → Reset this PC. فایل‌های شما حفظ می‌شود.</p>\n<blockquote><strong>نکته مهم:</strong> قبل از هر اقدامی از اطلاعات خود بکاپ بگیرید. تیم مهراد سام آماده کمک به شما در این زمینه است.</blockquote>','اگر ویندوز شما کند شده، با این ۱۰ روش حرفه‌ای سرعت را به روز اول برگردانید. راهکارهای گام‌به‌گام برای بهینه‌سازی ویندوز ۱۰ و ۱۱.','','<svg viewBox=\'0 0 120 120\' style=\'width:100%;height:100%;\'><rect x=\'20\' y=\'20\' width=\'80\' height=\'56\' rx=\'6\' fill=\'#2D3436\'/><rect x=\'28\' y=\'28\' width=\'64\' height=\'40\' rx=\'3\' fill=\'#FF6F00\'/><path d=\'M50 84 H70 L60 68 Z\' fill=\'#2D3436\'/><circle cx=\'44\' cy=\'80\' r=\'6\' fill=\'#FF6F00\'/><circle cx=\'76\' cy=\'80\' r=\'6\' fill=\'#FF6F00\'/></svg>','blog',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-11 01:06:28',NULL),(16,1,'راهنمای جامع انتخاب قطعات کامپیوتر — از CPU تا کیس','pc-parts-guide','<h2>مقدمه</h2>\n<p>خرید قطعات کامپیوتر می‌تواند گیج‌کننده باشد. تنوع زیاد برندها، مدل‌ها و مشخصات فنی تصمیم‌گیری را سخت می‌کند. در این راهنما به زبان ساده توضیح می‌دهیم چطور بهترین قطعات را با توجه به نیاز خود انتخاب کنید.</p>\n<h2>۱. پردازنده (CPU) — قلب کامپیوتر</h2>\n<p>دو شرکت اصلی تولیدکننده پردازنده اینتل و AMD هستند. برای کارهای روزمره و اداری: Core i3 یا Ryzen 3 کافی است. برای بازی و کارهای گرافیکی: Core i5/i7 یا Ryzen 5/7. برای رندرینگ و کارهای سنگین: Core i9 یا Ryzen 9.</p>\n<h2>۲. کارت گرافیک (GPU)</h2>\n<p>اگر گیمر هستید یا با نرم‌افزارهای سه‌بعدی کار می‌کنید، کارت گرافیک مجزا ضروری است. NVIDIA (سری RTX) و AMD (سری RX) گزینه‌های اصلی هستند. برای کاربری عادی، گرافیک روی‌برد CPU کافی است.</p>\n<h2>۳. رم (RAM)</h2>\n<ul><li><strong>۸ گیگابایت:</strong> حداقل برای ویندوز ۱۰/۱۱ و کارهای اداری</li><li><strong>۱۶ گیگابایت:</strong> مناسب برای بازی و کارهای نیمه‌حرفه‌ای</li><li><strong>۳۲ گیگابایت:</strong> برای رندرینگ، ماشین مجازی و کارهای سنگین</li></ul>\n<h2>۴. مادربرد</h2>\n<p>مادربرد قطعات را به هم متصل می‌کند. به سوکت CPU (مثلاً LGA1700 برای اینتل نسل ۱۲-۱۴)، فرم‌فاکتور (ATX، Micro-ATX، Mini-ITX)، و پورت‌های مورد نیاز خود توجه کنید.</p>\n<h2>۵. هارد و SSD</h2>\n<p>SSDهای NVMe تا ۲۰ برابر سریعتر از HDD هستند. پیشنهاد ما: یک SSD ۲۵۶ یا ۵۱۲ گیگابایتی برای ویندوز و برنامه‌ها + یک HDD ۱ یا ۲ ترابایتی برای ذخیره فایل‌ها.</p>\n<h2>۶. پاور (Power Supply)</h2>\n<p>پاور باکیفیت از همه قطعات محافظت می‌کند. برندهای معتبر: Corsair، Cooler Master، Seasonic، Green. توان پاور را حداقل ۲۰٪ بیشتر از نیاز سیستم انتخاب کنید.</p>\n<h2>۷. کیس (Case)</h2>\n<p>کیس خوب جریان هوای مناسب دارد و قطعات را خنک نگه می‌دارد. به سایز (Mid Tower، Full Tower) و تعداد فن‌ها توجه کنید.</p>\n<blockquote><strong>نکته:</strong> قبل از خرید، از سازگاری قطعات با یکدیگر مطمئن شوید. تیم مهراد سام در انتخاب قطعات به شما مشاوره رایگان می‌دهد.</blockquote>','هرآنچه برای خرید قطعات کامپیوتر باید بدانید: راهنمای انتخاب CPU، GPU، رم، مادربرد، هارد و پاور بر اساس نیاز و بودجه شما.','','<svg viewBox=\'0 0 120 120\' style=\'width:100%;height:100%;\'><rect x=\'30\' y=\'40\' width=\'60\' height=\'50\' rx=\'8\' fill=\'#2D3436\'/><circle cx=\'60\' cy=\'65\' r=\'12\' fill=\'#FF6F00\'/><circle cx=\'60\' cy=\'65\' r=\'6\' fill=\'#fff\'/><path d=\'M60 77 V90 M50 90 H70\' stroke=\'#2D3436\' stroke-width=\'4\' stroke-linecap=\'round\'/><rect x=\'35\' y=\'20\' width=\'8\' height=\'20\' rx=\'2\' fill=\'#2D3436\'/><rect x=\'77\' y=\'20\' width=\'8\' height=\'20\' rx=\'2\' fill=\'#2D3436\'/></svg>','blog',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-11 01:06:29',NULL),(17,1,'امنیت شبکه خانگی — ۷ قدم برای محافظت از Wi-Fi و دستگاه‌ها','home-network-security','<h2>چرا امنیت شبکه خانگی مهم است؟</h2>\n<p>بسیاری از افراد تصور می‌کنند هکرها فقط شرکت‌های بزرگ را هدف می‌گیرند، اما واقعیت این است که شبکه‌های خانگی نیز آسیب‌پذیر هستند. از اطلاعات بانکی گرفته تا دوربین‌های مداربسته، همه از طریق شبکه شما قابل دسترسی‌اند.</p>\n<h2>۱. رمز عبور پیش‌فرض مودم را تغییر دهید</h2>\n<p>بیشتر مودم‌ها با رمز پیش‌فرض (admin/admin یا ۱۲۳۴) عرضه می‌شوند. اولین و مهم‌ترین کار تغییر این رمز است. به تنظیمات مودم (۱۹۲.۱۶۸.۱.۱) رفته و یک رمز قوی تنظیم کنید.</p>\n<h2>۲. رمز وای‌فای را WPA2 یا WPA3 کنید</h2>\n<p>از رمزگذاری WEP استفاده نکنید — این روش قدیمی و ناامن است. حتماً رمزگذاری را روی <strong>WPA2-PSK</strong> یا <strong>WPA3</strong> (در مودم‌های جدید) تنظیم کنید.</p>\n<h2>۳. SSID (نام شبکه) را مخفی کنید</h2>\n<p>با مخفی کردن SSID، شبکه شما در لیست شبکه‌های قابل مشاهده ظاهر نمی‌شود. افراد فقط با دانستن نام دقیق می‌توانند متصل شوند.</p>\n<h2>۴. فایروال را فعال کنید</h2>\n<p>مودم‌ها معمولاً فایروال داخلی دارند. مطمئن شوید فعال است. همچنین فایروال ویندوز را هم روشن کنید.</p>\n<h2>۵. شبکه مهمان (Guest Network) راه‌اندازی کنید</h2>\n<p>برای مهمان‌ها و دستگاه‌های IoT (مانند لامپ هوشمند و دوربین) یک شبکه جداگانه تعریف کنید تا به شبکه اصلی شما دسترسی نداشته باشند.</p>\n<h2>۶. میان‌افزار (Firmware) مودم را به‌روز کنید</h2>\n<p>شرکت‌های سازنده مودم به‌روزرسانی‌های امنیتی منتشر می‌کنند. هر چند ماه یکبار به تنظیمات مودم رفته و گزینه Check for Updates را بزنید.</p>\n<h2>۷. دستگاه‌های متصل را بررسی کنید</h2>\n<p>وارد تنظیمات مودم شوید و لیست دستگاه‌های متصل را بررسی کنید. اگر دستگاه ناشناخته‌ای دیدید، رمز وای‌فای را عوض کنید.</p>\n<blockquote>ما در <strong>مهراد سام</strong> خدمات امنیت شبکه و راه‌اندازی فایروال را به صورت حرفه‌ای ارائه می‌دهیم. کافیست تماس بگیرید.</blockquote>','با این ۷ روش ساده امنیت شبکه خانگی خود را افزایش دهید و از هک شدن وای‌فای و دستگاه‌های متصل جلوگیری کنید.','','<svg viewBox=\'0 0 120 120\' style=\'width:100%;height:100%;\'><circle cx=\'30\' cy=\'40\' r=\'14\' fill=\'#FF6F00\'/><circle cx=\'90\' cy=\'40\' r=\'14\' fill=\'#00B894\'/><circle cx=\'60\' cy=\'80\' r=\'16\' fill=\'#2D3436\'/><path d=\'M38 48 L52 72 M82 48 L68 72\' stroke=\'#2D3436\' stroke-width=\'5\' stroke-linecap=\'round\' fill=\'none\' opacity=\'0.5\'/><path d=\'M30 40 H90\' stroke=\'#FF6F00\' stroke-width=\'5\'/></svg>','blog',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-11 01:06:29',NULL),(18,1,'نکات طلایی خرید لپ‌تاپ دست دوم — راهنمای قبل از خرید','used-laptop-buying-guide','<h2>خرید لپ‌تاپ دست دوم — فرصت یا تله؟</h2>\n<p>خرید لپ‌تاپ دست دوم می‌تواند یک معامله عالی یا یک اشتباه پرهزینه باشد. با رعایت این نکات می‌توانید یک دستگاه سالم و با ارزش خریداری کنید. در ادامه مهم‌ترین نکاتی که قبل از خرید باید بررسی کنید را توضیح می‌دهیم.</p>\n<h2>۱. سلامت باتری</h2>\n<p>باتری لپ‌تاپ‌ها پس از ۲-۳ سال استفاده افت می‌کند. با دستور PowerShell:<br><code>powercfg /batteryreport</code><br>می‌توانید گزارش کامل باتری را ببینید. اگر ظرفیت باتری زیر ۷۰٪ است، به‌زودی مجبور به تعویض آن خواهید شد.</p>\n<h2>۲. سلامت هارد یا SSD</h2>\n<p>از نرم‌افزار <strong>CrystalDiskInfo</strong> برای بررسی سلامت دیسک استفاده کنید. اگر هارد دارای سکتور خراب (Bad Sector) است، از خرید منصرف شوید.</p>\n<h2>۳. پیکسل‌های سوخته صفحه نمایش</h2>\n<p>صفحه نمایش را در رنگ‌های مختلف (سفید، سیاه، قرمز، سبز، آبی) بررسی کنید. پیکسل‌های سوخته به صورت نقاط کوچک ثابت دیده می‌شوند.</p>\n<h2>۴. عملکرد کیبورد و تاچ‌پد</h2>\n<p>همه دکمه‌های کیبورد را یک‌به‌یک تست کنید. تاچ‌پد را از نظر حساسیت و عملکرد کلیک‌ها بررسی کنید.</p>\n<h2>۵. پورت‌ها و اتصالات</h2>\n<p>همه پورت‌ها (USB، HDMI، Type-C، جک هدفون، کارت‌خوان) را تست کنید. اطمینان حاصل کنید وای‌فای، بلوتوث و وبکم کار می‌کنند.</p>\n<h2>۶. لولای صفحه (Hinge)</h2>\n<p>لولای لپ‌تاپ را باز و بسته کنید. اگر شل است یا صدا می‌دهد، ممکن است به‌زودی بشکند. تعمیر لولا هزینه بالایی دارد.</p>\n<h2>۷. شماره سریال و اصالت دستگاه</h2>\n<p>شماره سریال لپ‌تاپ را در سایت سازنده وارد کنید تا از اصالت آن و وضعیت گارانتی مطمئن شوید.</p>\n<h2>۸. قیمت مناسب</h2>\n<p>قیمت لپ‌تاپ دست دوم معمولاً ۴۰-۶۰٪ قیمت نو است. لپ‌تاپ‌های بیزینسی (مانند ThinkPad و EliteBook) دوام بیشتری دارند و انتخاب بهتری هستند.</p>\n<blockquote>تیم <strong>مهراد سام</strong> خدمات بررسی فنی لپ‌تاپ قبل از خرید را ارائه می‌دهد. با ما تماس بگیرید.</blockquote>','با این نکات حرفه‌ای، یک لپ‌تاپ دست دوم خوب و بدون مشکل بخرید. راهنمای کامل بررسی سخت‌افزاری و نرم‌افزاری قبل از خرید.','','<svg viewBox=\'0 0 120 120\' style=\'width:100%;height:100%;\'><rect x=\'30\' y=\'28\' width=\'60\' height=\'64\' rx=\'10\' fill=\'#2D3436\'/><rect x=\'40\' y=\'36\' width=\'40\' height=\'34\' rx=\'4\' fill=\'#FF6F00\'/><circle cx=\'60\' cy=\'80\' r=\'6\' fill=\'#00B894\'/><rect x=\'44\' y=\'72\' width=\'32\' height=\'4\' rx=\'2\' fill=\'#2D3436\'/></svg>','blog',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-11 01:06:29',NULL),(19,1,'آموزش پشتیبان‌گیری از اطلاعات — نجات داده‌های شما در شرایط بحرانی','data-backup-guide','<h2>اهمیت پشتیبان‌گیری</h2>\n<p>هارد دیسک‌ها به طور میانگین ۳-۵ سال عمر می‌کنند. یک روز بدون هشدار از کار می‌افتند و اگر بکاپ نداشته باشید، تمام اطلاعات خود را از دست می‌دهید. در این مقاله روش‌های اصولی بکاپ‌گیری را آموزش می‌دهیم.</p>\n<h2>قانون ۳-۲-۱ بکاپ</h2>\n<p>حرفه‌ای‌ها از قانون ۳-۲-۱ پیروی می‌کنند: <strong>۳</strong> نسخه از داده‌ها، روی <strong>۲</strong> نوع رسانه مختلف (مثلاً هارد داخلی + هارد اکسترنال)، و <strong>۱</strong> نسخه خارج از محل (ابر).</p>\n<h2>۱. بکاپ روی هارد اکسترنال</h2>\n<p>ساده‌ترین روش: یک هارد اکسترنال بخرید و هر هفته اطلاعات خود را روی آن کپی کنید. برای اتوماتیک کردن این کار از نرم‌افزار <strong>Cobian Backup</strong> یا ابزار Built-in ویندوز (File History) استفاده کنید.</p>\n<h2>۲. بکاپ ابری (Cloud)</h2>\n<p>سرویس‌های ابری زیر رایگان یا کم‌هزینه هستند:</p>\n<ul>\n<li><strong>Google Drive:</strong> ۱۵ گیگابایت رایگان</li>\n<li><strong>OneDrive:</strong> ۵ گیگابایت رایگان (همراه ویندوز)</li>\n<li><strong>Dropbox:</strong> ۲ گیگابایت رایگان</li>\n<li><strong>pCloud:</strong> ۱۰ گیگابایت رایگان</li>\n</ul>\n<h2>۳. بکاپ فایل‌های مهم</h2>\n<p>این فایل‌ها را حتماً بکاپ بگیرید:</p>\n<ul>\n<li>اسناد و مدارک (Word، Excel، PDF)</li>\n<li>عکس‌ها و فیلم‌های خانوادگی</li>\n<li>فایل‌های پروژه و کاری</li>\n<li>بوکمارک‌های مرورگر</li>\n<li>فایل‌های تنظیمات برنامه‌ها</li>\n</ul>\n<h2>۴. بکاپ خودکار با نرم‌افزار</h2>\n<p>نرم‌افزارهای حرفه‌ای:</p>\n<ul>\n<li><strong>EaseUS Todo Backup:</strong> رایگان و ساده</li>\n<li><strong>Macrium Reflect:</strong> قوی برای بکاپ کامل دیسک</li>\n<li><strong>Acronis True Image:</strong> حرفه‌ای با قابلیت ابری</li>\n</ul>\n<h2>۵. بکاپ گرفتن قبل از تعمیرات</h2>\n<p>قبل از هر تعمیرات سخت‌افزاری یا نصب مجدد ویندوز، حتماً بکاپ بگیرید. ما در <strong>مهراد سام</strong> قبل از شروع کار از اطلاعات شما بکاپ می‌گیریم و پس از اتمام کار، اطلاعات را بازمی‌گردانیم.</p>\n<blockquote><strong>به یاد داشته باشید:</strong> اطلاعاتی که بکاپ ندارید، اطلاعاتی هستند که اهمیت چندانی برای شما ندارند!</blockquote>','روش‌های اصولی بکاپ‌گیری از اطلاعات مهم: از هارد اکسترنال تا ابری. یاد بگیرید چطور از دست دادن اطلاعات را برای همیشه فراموش کنید.','','<svg viewBox=\'0 0 120 120\' style=\'width:100%;height:100%;\'><path d=\'M60 16 L96 36 V62 C96 84 80 104 60 108 C40 104 24 84 24 62 V36 Z\' fill=\'#00B894\'/><path d=\'M48 62 l8 8 18 -18\' stroke=\'#fff\' stroke-width=\'8\' stroke-linecap=\'round\' stroke-linejoin=\'round\' fill=\'none\'/><rect x=\'52\' y=\'38\' width=\'16\' height=\'20\' rx=\'2\' fill=\'#fff\'/></svg>','blog',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-11 01:06:29',NULL),(20,1,'معرفی ۱۰ ابزار آنلاین کاربردی که زندگی شما را ساده‌تر می‌کند','useful-online-tools','<h2>ابزارهای آنلاین — بدون نصب، همیشه در دسترس</h2>\n<p>امروزه نیازی به نصب نرم‌افزارهای سنگین برای بسیاری از کارها نیست. ابزارهای آنلاین زیادی وجود دارند که کار شما را سریع و آسان انجام می‌دهند. در این مقاله ۱۰ ابزار عالی را معرفی می‌کنیم.</p>\n<h2>۱. Canva — طراحی گرافیکی ساده</h2>\n<p><strong>canva.com</strong><br>بدون نیاز به فتوشاپ، در عرض چند دقیقه پوستر، اینفوگرافیک، بنر و پست اینستاگرام طراحی کنید. هزاران قالب آماده رایگان.</p>\n<h2>۲. Remove.bg — حذف پس‌زمینه تصاویر</h2>\n<p><strong>remove.bg</strong><br>با یک کلیک پس‌زمینه عکس را حذف کنید. بسیار دقیق و سریع. تا ۵۰ تصویر در ماه رایگان.</p>\n<h2>۳. ILovePDF — ویرایش فایل‌های PDF</h2>\n<p><strong>ilovepdf.com</strong><br>ادغام، تقسیم، تبدیل، فشرده‌سازی و ویرایش فایل‌های PDF — همه به صورت رایگان و آنلاین.</p>\n<h2>۴. TinyWow — مجموعه ابزارهای رایگان</h2>\n<p><strong>tinywow.com</strong><br>جایگزین عالی برای ILovePDF با ابزارهای بیشتر: تبدیل ویدئو، ویرایش تصاویر، استخراج متن از PDF و...</p>\n<h2>۵. Google Keep — یادداشت‌برداری سریع</h2>\n<p><strong>keep.google.com</strong><br>یادداشت‌های خود را در همه دستگاه‌ها هماهنگ کنید. پشتیبانی از لیست، تصویر، نقاشی و یادآوری.</p>\n<h2>۶. Pixlr — ویرایش تصاویر در مرورگر</h2>\n<p><strong>pixlr.com</strong><br>یک فتوشاپ کامل در مرورگر شما. قابلیت لایه، فیلتر و ابزارهای حرفه‌ای. نسخه رایگان بسیار قدرتمند است.</p>\n<h2>۷. ۱۲۳FormBuilder — ساخت فرم و نظرسنجی</h2>\n<p><strong>123formbuilder.com</strong><br>فرم‌های حرفه‌ای برای وبسایت خود بسازید. فرم تماس، نظرسنجی، ثبت‌نام و...</p>\n<h2>۸. WeTransfer — ارسال فایل‌های حجیم</h2>\n<p><strong>wetransfer.com</strong><br>فایل‌های تا ۲ گیگابایت را رایگان و بدون نیاز به ثبت‌نام برای دیگران ارسال کنید.</p>\n<h2>۹. DeepL — ترجمه حرفه‌ای با هوش مصنوعی</h2>\n<p><strong>deepl.com</strong><br>ترجمه متون با دقت بسیار بالاتر از Google Translate. از ۳۱ زبان پشتیبانی می‌کند.</p>\n<h2>۱۰. ۱۲۳Apps — ابزارهای آنلاین متفرقه</h2>\n<p><strong>123apps.com</strong><br>ویرایش صدا، ویدئو، تبدیل فرمت فایل‌ها، ضبط صفحه نمایش و... همه به صورت رایگان و آنلاین.</p>\n<blockquote>آیا ابزار مفید دیگری می‌شناسید؟ در بخش نظرات با ما به اشتراک بگذارید. تیم <strong>مهراد سام</strong> همیشه آماده کمک به شماست.</blockquote>','۱۰ ابزار آنلاین رایگان و کاربردی برای کارهای روزمره، از ویرایش PDF گرفته تا ساخت تصاویر با هوش مصنوعی — بدون نیاز به نصب.','','<svg viewBox=\'0 0 120 120\' style=\'width:100%;height:100%;\'><rect x=\'24\' y=\'30\' width=\'72\' height=\'56\' rx=\'8\' fill=\'#2D3436\'/><rect x=\'32\' y=\'38\' width=\'56\' height=\'10\' rx=\'3\' fill=\'#FF6F00\'/><rect x=\'32\' y=\'54\' width=\'40\' height=\'6\' rx=\'3\' fill=\'#888\'/><rect x=\'32\' y=\'66\' width=\'30\' height=\'6\' rx=\'3\' fill=\'#888\'/><circle cx=\'82\' cy=\'78\' r=\'8\' fill=\'#FF6F00\'/><path d=\'M78 74 l8 6 -8 6 z\' fill=\'#fff\'/></svg>','blog',NULL,0,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-11 01:06:29',NULL),(21,1,'پشتیبانی از راه دور','poshtiban-az-rah-dor',NULL,'حل مشکلات نرم‌افزاری و ویندوز به صورت آنلاین و تیم‌ویور.','پشتیبانی تخصصی','<i class=\"fa-solid fa-wifi\"></i>','khadamat',NULL,1,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(22,1,'پشتیبانی حضوری','poshtiban-hozoori',NULL,'مراجعه به محل شما در تهران برای عیب‌یابی و تعمیر سخت‌افزاری/نرم‌افزاری.','خدمات در محل','<i class=\"fa-solid fa-user-tie\"></i>','khadamat',NULL,2,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(23,1,'رفع کندی سیستم','rafe-kandi-system',NULL,'بهینه‌سازی کامل ویندوز، پاک‌سازی فایل‌های زائد، و افزایش سرعت بوت.','افزایش سرعت','<i class=\"fa-solid fa-bolt\"></i>','khadamat',NULL,3,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(24,1,'نصب نرم‌افزار','nasb-narmafzar',NULL,'نصب و کانفیگ ویندوز، آفیس، آنتی‌ویروس، و نرم‌افزارهای تخصصی.','نرم‌افزار اورجینال','<i class=\"fa-solid fa-download\"></i>','khadamat',NULL,4,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(25,1,'نصب آنتی‌ویروس','nasb-antivirus',NULL,'نصب، کانفیگ، و آموزش استفاده از آنتی‌ویروس‌های قدرتمند.','امنیت سیستم','<i class=\"fa-solid fa-shield-halved\"></i>','khadamat',NULL,5,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(26,1,'طراحی سایت','tarahi-site',NULL,'طراحی و توسعه وب‌سایت‌های شرکتی، فروشگاهی، و پورتال با پنل مدیریت فارسی.','وب‌سایت حرفه‌ای','<i class=\"fa-solid fa-code\"></i>','khadamat',NULL,6,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(27,1,'دوربین مدار بسته','doobin-madar-basteh',NULL,'فروش، نصب، و راه‌اندازی سیستم‌های CCTV/IP Camera با مانیتورینگ موبایل.','امنیت تصویری','<i class=\"fa-solid fa-video\"></i>','khadamat',NULL,7,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(28,1,'برنامه‌نویسی','barnameh-nevisi',NULL,'توسعه نرم‌افزار سفارشی، وب‌اپلیکیشن، API، و اتوماسیون فرآیندها.','توسعه نرم‌افزار','<i class=\"fa-solid fa-laptop-code\"></i>','khadamat',NULL,8,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL),(29,1,'شبکه و اینترنت','network-internet',NULL,'راه‌اندازی، عیب‌یابی و بهینه‌سازی شبکه، مودم، وایرلس و اینترنت.','زیرساخت شبکه','<i class=\"fa-solid fa-network-wired\"></i>','khadamat',NULL,9,'default','fa','publish',0,NULL,NULL,NULL,'2026-07-25 08:49:52',NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rasaneh`
--

DROP TABLE IF EXISTS `rasaneh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rasaneh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nam_fayl` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'نام فایل',
  `masir` varchar(500) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'مسیر نسبی فایل',
  `noweh` varchar(50) COLLATE utf8mb4_persian_ci DEFAULT 'image' COMMENT 'image | document | video',
  `hajm` int(11) DEFAULT '0' COMMENT 'حجم به بایت',
  `pahna` int(11) DEFAULT NULL COMMENT 'عرض تصویر',
  `boland` int(11) DEFAULT NULL COMMENT 'ارتفاع تصویر',
  `uploaded_by` int(11) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_noweh` (`noweh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rasaneh`
--

LOCK TABLES `rasaneh` WRITE;
/*!40000 ALTER TABLE `rasaneh` DISABLE KEYS */;
/*!40000 ALTER TABLE `rasaneh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sabad`
--

DROP TABLE IF EXISTS `sabad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sabad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `karbar_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_karbar` (`karbar_id`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sabad`
--

LOCK TABLES `sabad` WRITE;
/*!40000 ALTER TABLE `sabad` DISABLE KEYS */;
INSERT INTO `sabad` VALUES (1,NULL,'7isde5h0ncetf3ou5rtup588ib','2026-07-02 09:10:59','2026-07-02 09:10:59'),(2,NULL,'rtqggttrd1r4ppdc4mlgfl2k08','2026-07-06 03:12:01','2026-07-06 03:12:01'),(3,NULL,'th7l44jha9kq5ot3gbvg263qnq','2026-07-06 07:51:55','2026-07-06 07:51:55'),(4,NULL,'mhge9e36c4oea4o2fdgto0cmqc','2026-07-07 05:57:18','2026-07-07 05:57:18'),(5,NULL,'9m0uk5qjstfdkfrmk5pdtfl2b6','2026-07-07 06:22:12','2026-07-07 06:22:12'),(6,NULL,'pu004vu70d6pbjet97066npfqv','2026-07-07 06:23:03','2026-07-07 06:23:03'),(7,NULL,'tq6eqh1drpvl13o7l8bj8vc69f','2026-07-07 06:23:21','2026-07-07 06:23:21'),(8,NULL,'i1c0fbkqaop3oo3r4qsr3i8k4g','2026-07-07 06:23:21','2026-07-07 06:23:21'),(9,NULL,'nl56h935hl29gt3hjci4r1pkn5','2026-07-07 06:23:21','2026-07-07 06:23:21'),(10,NULL,'i5ha6hecgccuhnqchsti98hm4e','2026-07-07 06:23:21','2026-07-07 06:23:21'),(11,NULL,'noatkgf7qocqagbkguuno9aet6','2026-07-07 06:23:22','2026-07-07 06:23:22'),(12,NULL,'1oslgkh0hfrucfv9k29mkq09ae','2026-07-07 06:38:12','2026-07-07 06:38:12'),(13,NULL,'mg5rg2fupvjf6r3j9s66js3ohh','2026-07-07 06:38:12','2026-07-07 06:38:12'),(14,NULL,'nfglf9dgv70duk5sg4l756qr3c','2026-07-07 06:38:12','2026-07-07 06:38:12'),(15,NULL,'7qdp82p5eno1k2lclnd1m98kpg','2026-07-07 06:38:12','2026-07-07 06:38:12'),(16,NULL,'h8pt1n8piac5oajkkrn09qild5','2026-07-07 06:38:12','2026-07-07 06:38:12'),(17,NULL,'1tc57kffggem1if2uc7lqn81j2','2026-07-07 06:38:12','2026-07-07 06:38:12'),(18,NULL,'99ntgjq5u720do81ij4i7ikjt3','2026-07-07 06:38:29','2026-07-07 06:38:29'),(19,NULL,'0a2aj5fbe6qs7k5mbmusue7fnm','2026-07-07 06:38:29','2026-07-07 06:38:29'),(20,NULL,'ck0ddv5uv3pl03kem0f6mojmv2','2026-07-07 06:38:29','2026-07-07 06:38:29'),(21,NULL,'voammkjach7us2s1lunsu0ehlo','2026-07-07 06:38:29','2026-07-07 06:38:29'),(22,NULL,'ofidmd45997a8aaunpi54tc8p9','2026-07-07 06:38:30','2026-07-07 06:38:30'),(23,NULL,'j5et1m1cngulsngib4evk08bag','2026-07-07 06:38:30','2026-07-07 06:38:30'),(24,NULL,'79j21ii2en3u6if39k6lub9eng','2026-07-07 06:56:59','2026-07-07 06:56:59'),(25,NULL,'a74p1r4iqid1hp6vblkdfgq3um','2026-07-07 06:57:29','2026-07-07 06:57:29'),(26,NULL,'c8j5tp4e6k9erdt6k40t7nae36','2026-07-07 06:57:34','2026-07-07 06:57:34'),(27,NULL,'llpqocvaldig3vblu55chkq6fj','2026-07-07 06:57:35','2026-07-07 06:57:35'),(28,NULL,'pu0nj34klavue4npud12lb5f1q','2026-07-07 06:58:56','2026-07-07 06:58:56'),(29,NULL,'qmoihrlnngc5k4a3i7rnaj69dp','2026-07-07 07:02:24','2026-07-07 07:02:24'),(30,NULL,'s59bb1laeg1cbkn0tjlfq2g12j','2026-07-07 07:06:04','2026-07-07 07:06:04'),(31,NULL,'n4o96scgcn2g916sa11qj9577v','2026-07-07 07:08:15','2026-07-07 07:08:15'),(32,NULL,'499n2lcl9ggmkkli86ajfvbqb2','2026-07-07 07:10:31','2026-07-07 07:10:31'),(33,NULL,'ajhuojc5ttnni67r5bd0kapi1c','2026-07-07 07:10:32','2026-07-07 07:10:32'),(34,NULL,'t1i8j69o9nlmutbdfomrb4236t','2026-07-07 07:10:32','2026-07-07 07:10:32'),(35,NULL,'ktjtbimia7e2f9qf68uuea9jcr','2026-07-07 08:54:16','2026-07-07 08:54:16'),(36,NULL,'g1ul05higge2shjjf31p1l09ni','2026-07-07 08:55:34','2026-07-07 08:55:34'),(37,NULL,'arufslvv0jj8pp39vqt2gk6pca','2026-07-07 08:55:45','2026-07-07 08:55:45'),(38,NULL,'n4d4qtn5n50dom7s7qoffi7ruu','2026-07-07 08:55:56','2026-07-07 08:55:56'),(39,NULL,'atdd1lk4rpj1dv3r87gvk268t2','2026-07-07 09:25:44','2026-07-07 09:25:44'),(40,NULL,'h242kqvq6565prbq5o750recvj','2026-07-07 09:25:45','2026-07-07 09:25:45'),(41,NULL,'g14trbua8hngdv2g0ha8jut3on','2026-07-07 09:25:45','2026-07-07 09:25:45'),(42,NULL,'2mia741bd0sh0vkfcvd634ivam','2026-07-07 09:32:28','2026-07-07 09:32:28'),(43,NULL,'uidj39rfkkspnjkmf0q7nrog6q','2026-07-07 09:32:29','2026-07-07 09:32:29'),(44,NULL,'0keqno2rjrnpoge58d7j1ad2an','2026-07-07 09:32:30','2026-07-07 09:32:30'),(45,NULL,'4nb524vgtdlfa37ausuf2escj3','2026-07-07 09:32:31','2026-07-07 09:32:31'),(46,NULL,'tiukl5sqavklj33av2lhrpuigr','2026-07-07 09:40:44','2026-07-07 09:40:44'),(47,NULL,'n416bgh998hd5vc5aqr7g18ttm','2026-07-07 09:40:44','2026-07-07 09:40:44'),(48,NULL,'2p9sglo3bss3f08penhvchhgb0','2026-07-07 09:40:44','2026-07-07 09:40:44'),(49,NULL,'a5ph3f7qn01du0vkq8e8e1blpe','2026-07-07 11:48:47','2026-07-07 11:48:47'),(50,NULL,'i9i3445af0ujru0lmon727vh9b','2026-07-07 15:52:24','2026-07-07 15:52:24'),(51,NULL,'msr38j17qknq0gdd0hbfrbn7tk','2026-07-07 18:44:49','2026-07-07 18:44:49'),(52,NULL,'7rsc8f5luh7jhvnaijic2rpoi4','2026-07-07 19:35:51','2026-07-07 19:35:51'),(53,NULL,'eau9vf4bfe8nunf54dm30d5gk5','2026-07-07 19:35:53','2026-07-07 19:35:53'),(54,NULL,'vtivsabuu4aupi9gq4a75bp26s','2026-07-07 19:35:53','2026-07-07 19:35:53'),(55,NULL,'b7koohnbi17pg8nqqo18on5209','2026-07-07 22:00:37','2026-07-07 22:00:37'),(56,1,'7pd811i5s9nb52rr9lurlkp8h9','2026-07-07 22:02:01','2026-07-07 22:02:01'),(57,NULL,'n6k227f4msatf0ssl89v14i4a6','2026-07-08 14:41:13','2026-07-08 14:41:13'),(58,NULL,'2msedpmkmbahmmapqoi4rlru8g','2026-07-08 14:41:36','2026-07-08 14:41:36'),(59,2,'78jebs7qaelipnq6nd6dlu4dhi','2026-07-09 17:37:02','2026-07-09 17:37:02'),(60,NULL,'p46cjm7bfvdhr8a8b3aedc7kdc','2026-07-09 23:43:08','2026-07-09 23:43:08'),(61,NULL,'6f7cdbo3cue1svr4jgp9subb8n','2026-07-10 11:12:02','2026-07-10 11:12:02'),(62,NULL,'nceg1uefa45ctbf0ee97jul205','2026-07-10 11:38:44','2026-07-10 11:38:44'),(63,NULL,'bei6alh97088pg2v8b2uotktho','2026-07-10 16:59:16','2026-07-10 16:59:16'),(64,NULL,'sm04nivbd4dqq0rlmgvngm13kp','2026-07-10 17:25:44','2026-07-10 17:25:44'),(65,NULL,'cbliha8lbhvp191hs91p6827ld','2026-07-10 17:43:22','2026-07-10 17:43:22'),(66,NULL,'hcl7esbes4fpf6vlqctog4acdg','2026-07-10 18:13:45','2026-07-10 18:13:45'),(67,NULL,'mr9nout3lb16gspi0ih4j6nabu','2026-07-10 18:18:55','2026-07-10 18:18:55'),(68,NULL,'oplh4mgo7k2nke7ckbdpfni8od','2026-07-10 18:45:30','2026-07-10 18:45:30'),(69,NULL,'cd1moqtfv0ug6esi50ejj5koh5','2026-07-10 18:52:08','2026-07-10 18:52:08'),(70,NULL,'iehau605jo5cdfm29eu0eb5kgs','2026-07-10 18:52:20','2026-07-10 18:52:20'),(71,NULL,'grp5q6fspfgog3gsc7fb7ijfpm','2026-07-10 18:54:43','2026-07-10 18:54:43'),(72,NULL,'4es45msva53cs1t9h33r6dbajl','2026-07-10 18:55:20','2026-07-10 18:55:20'),(73,NULL,'g4dl9ql0sqfoiilegc5jc1k89q','2026-07-10 18:55:31','2026-07-10 18:55:31'),(74,NULL,'60krvj797to2ai8a4kcaagpsft','2026-07-10 20:00:03','2026-07-10 20:00:03'),(75,NULL,'6r3groiv1g74u0uo3fcmimf26v','2026-07-10 20:06:16','2026-07-10 20:06:16'),(76,NULL,'dosq8735tbgkh4fqo7mq8h6fc2','2026-07-10 20:17:17','2026-07-10 20:17:17'),(77,NULL,'aqqj57rjc47cgar8o2utd71cgt','2026-07-10 20:30:42','2026-07-10 20:30:42'),(78,NULL,'rcnn08br9iv5c2a2rcoo42kli2','2026-07-10 20:34:28','2026-07-10 20:34:28'),(79,NULL,'8o5viaiugk5ohqoo06uio3qfm3','2026-07-10 21:36:54','2026-07-10 21:36:54'),(80,NULL,'vqg0m0gjffbim09rkhbktstenu','2026-07-10 22:07:19','2026-07-10 22:07:19'),(81,NULL,'ggs4v5h32p9pl7njis0umv8nnt','2026-07-10 22:07:32','2026-07-10 22:07:32'),(82,NULL,'ki1s8mujs435qhetgjdsvgbrci','2026-07-10 22:08:01','2026-07-10 22:08:01'),(83,NULL,'t3lerp7dtripneambikd62kbr6','2026-07-10 22:09:22','2026-07-10 22:09:22'),(84,NULL,'b3ubkd7mdjfe6bpkajgcqarbsq','2026-07-10 22:19:09','2026-07-10 22:19:09'),(85,NULL,'uuk2h5fo0v2jinosjfkn1hj4rg','2026-07-11 21:04:03','2026-07-11 21:04:03'),(86,NULL,'iatul9rnb642vlsp9oamjvturn','2026-07-14 06:25:01','2026-07-14 06:25:01'),(87,NULL,'eqlem1ialt4v95qcncq1055art','2026-07-14 06:29:03','2026-07-14 06:29:03'),(88,NULL,'a46jf6e5n27diq311a0o60l6cf','2026-07-14 08:44:26','2026-07-14 08:44:26'),(89,NULL,'vb55f1q2vv8s6sjbiqs0d1fga3','2026-07-14 08:44:26','2026-07-14 08:44:26'),(90,NULL,'9p0n02uteggcjl7k6vmbej0627','2026-07-14 08:44:26','2026-07-14 08:44:26'),(91,NULL,'jv51fjth6892e8jbmdjothm62a','2026-07-14 08:44:26','2026-07-14 08:44:26'),(92,NULL,'it68vg6jm353553pki0v4j4eal','2026-07-14 08:44:27','2026-07-14 08:44:27'),(93,NULL,'kar7jre4glkati73lid1v7naai','2026-07-14 08:44:27','2026-07-14 08:44:27'),(94,NULL,'r75ufs374ofe0vto2dss0tvmh2','2026-07-14 08:44:27','2026-07-14 08:44:27'),(95,NULL,'94oduf3v7gfll02hgum9cdeae7','2026-07-14 08:44:27','2026-07-14 08:44:27'),(96,NULL,'81ilt1mlat29lhcpa66ahkoprl','2026-07-14 08:45:23','2026-07-14 08:45:23'),(97,NULL,'a866vb345r17mpcsrahavk3chb','2026-07-14 10:48:15','2026-07-14 10:48:15'),(98,NULL,'rtlj0mf25eeeq6hajm6cvqsgjj','2026-07-14 10:48:16','2026-07-14 10:48:16'),(99,NULL,'g3tfn0ja2uh4vq795mpnre99t5','2026-07-14 10:48:16','2026-07-14 10:48:16'),(100,NULL,'v8umv80mf0hgul2vcj1bh36gdj','2026-07-14 10:48:48','2026-07-14 10:48:48'),(101,NULL,'mg6bapni9q46cmlq342th0lq8a','2026-07-15 03:06:46','2026-07-15 03:06:46'),(102,NULL,'n38rdh6tq4pekp3m5ogupfcsj0','2026-07-15 03:06:56','2026-07-15 03:06:56'),(103,NULL,'5soamoi3t7hik50jj0f66vs6s7','2026-07-16 09:12:10','2026-07-16 09:12:10'),(104,NULL,'bkq5hp9gtnn5jjj6hj98v7da9p','2026-07-16 12:28:36','2026-07-16 12:28:36'),(105,NULL,'nfd20selh82unt3spa66sspejt','2026-07-16 12:30:00','2026-07-16 12:30:00'),(106,NULL,'q57f321c1sq09leku1s4achhrq','2026-07-16 12:41:55','2026-07-16 12:41:55'),(107,NULL,'jntokigju0o68pfcaupne22vp2','2026-07-16 12:42:58','2026-07-16 12:42:58'),(108,NULL,'ljt7e4skb9ajddf69mita07760','2026-07-16 12:43:44','2026-07-16 12:43:44'),(109,NULL,'pn0e1dncl29evknifuqjueri7j','2026-07-16 13:29:10','2026-07-16 13:29:10'),(110,NULL,'673kr60c6bfgr1goerl8poe4dd','2026-07-16 13:35:32','2026-07-16 13:35:32'),(111,NULL,'i1t4jigr3ufsb438lndipa7453','2026-07-16 19:51:13','2026-07-16 19:51:13'),(112,NULL,'g0dhb2k9i7emv0cu6l806fb7f8','2026-07-16 20:48:50','2026-07-16 20:48:50'),(113,NULL,'3bc3jtt2mmuno22d1mvonc7qge','2026-07-16 21:05:09','2026-07-16 21:05:09'),(114,NULL,'nk1egntrhuvpijbt9pmc19u3ic','2026-07-17 02:18:42','2026-07-17 02:18:42'),(115,NULL,'01ihahh0jslispnree8qtt710i','2026-07-17 02:54:19','2026-07-17 02:54:19'),(116,NULL,'77up5qt1i03olqcaik459bu0b5','2026-07-17 08:58:49','2026-07-17 08:58:49'),(117,NULL,'mjtkljtp1mklgrs7ns4f0md7ia','2026-07-17 14:05:54','2026-07-17 14:05:54'),(118,NULL,'o5529g7mo1hfehjbgd9a6uk7k4','2026-07-21 04:04:54','2026-07-21 04:04:54'),(119,NULL,'07qak5dn8ml2qba8bramarbk1s','2026-07-21 04:05:42','2026-07-21 04:05:42'),(120,NULL,'j8emn5n7j0r14pl8gtuqdkc4rt','2026-07-21 04:05:46','2026-07-21 04:05:46'),(121,NULL,'g00g0t9npssttkgf9gviangapg','2026-07-21 04:06:08','2026-07-21 04:06:08'),(122,NULL,'tp14ehl6521p2o01t2atjnoa6t','2026-07-21 04:06:08','2026-07-21 04:06:08'),(123,NULL,'mg4b7i96jtffjho1f1ivd40dvv','2026-07-22 19:52:16','2026-07-22 19:52:16'),(124,NULL,'1v4kmutt7o35m0n6uspv784d39','2026-07-25 02:57:33','2026-07-25 02:57:33'),(125,NULL,'odfsh55n2lg403gqumt9phuq3a','2026-07-25 05:41:47','2026-07-25 05:41:47'),(126,NULL,'ssulbpb52os5odmmt0g5q1hqfl','2026-07-25 05:41:53','2026-07-25 05:41:53'),(127,NULL,'bg1opmcdim3qieog0ru1l0p4f0','2026-07-25 06:10:53','2026-07-25 06:10:53'),(128,NULL,'u76npagfkqibfrvc748nmj3ndb','2026-07-25 08:03:58','2026-07-25 08:03:58'),(129,NULL,'a4908f9761cfcc278c6967bd93566809','2026-08-04 05:43:01','2026-08-04 05:43:01'),(130,NULL,'36585a7a6d69ca4fc39b5847599d4345','2026-08-04 05:43:50','2026-08-04 05:43:50'),(131,NULL,'caa2b24c6a92fa6e5f9bfb3879303a4c','2026-08-04 05:56:26','2026-08-04 05:56:26'),(132,NULL,'b9418f5be704c09183ae7321180c782f','2026-08-04 05:56:34','2026-08-04 05:56:34'),(133,NULL,'014fce1d5d6c7436b80cfab5dd5c1020','2026-08-04 05:56:41','2026-08-04 05:56:41'),(134,NULL,'a642377eb61216527264f1ab7d29cea1','2026-08-04 05:56:41','2026-08-04 05:56:41'),(135,NULL,'1f641aeb5b3af70876d0cfacf97356f4','2026-08-04 05:56:41','2026-08-04 05:56:41'),(136,NULL,'3a6c8d4d21e1abad9ccf48223f0412fc','2026-08-04 05:56:41','2026-08-04 05:56:41'),(137,NULL,'5c174b5f8962bcff739e6d68f453d876','2026-08-04 05:56:41','2026-08-04 05:56:41');
/*!40000 ALTER TABLE `sabad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sabad_mahsul`
--

DROP TABLE IF EXISTS `sabad_mahsul`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sabad_mahsul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sabad_id` int(11) NOT NULL,
  `mahsul_id` int(11) NOT NULL,
  `tedad` int(11) NOT NULL DEFAULT '1',
  `gheymat_vahed` decimal(12,0) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sabad_mahsul` (`sabad_id`,`mahsul_id`),
  KEY `mahsul_id` (`mahsul_id`),
  CONSTRAINT `sabad_mahsul_ibfk_1` FOREIGN KEY (`sabad_id`) REFERENCES `sabad` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sabad_mahsul_ibfk_2` FOREIGN KEY (`mahsul_id`) REFERENCES `mahsulat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sabad_mahsul`
--

LOCK TABLES `sabad_mahsul` WRITE;
/*!40000 ALTER TABLE `sabad_mahsul` DISABLE KEYS */;
/*!40000 ALTER TABLE `sabad_mahsul` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sefaresh`
--

DROP TABLE IF EXISTS `sefaresh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sefaresh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `karbar_id` int(11) DEFAULT NULL,
  `onvan_girande` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon_girande` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ostan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shahr` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adres` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_posty` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pishaz',
  `post_hazine` decimal(12,0) DEFAULT '0',
  `tozih` text COLLATE utf8mb4_unicode_ci,
  `majmoo_gheymat` decimal(12,0) NOT NULL,
  `vaziat` enum('pending','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `pardakht_vaziat` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `pardakht_ref_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_karbar` (`karbar_id`),
  KEY `idx_vaziat` (`vaziat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sefaresh`
--

LOCK TABLES `sefaresh` WRITE;
/*!40000 ALTER TABLE `sefaresh` DISABLE KEYS */;
/*!40000 ALTER TABLE `sefaresh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sefaresh_mahsul`
--

DROP TABLE IF EXISTS `sefaresh_mahsul`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sefaresh_mahsul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sefaresh_id` int(11) NOT NULL,
  `mahsul_id` int(11) NOT NULL,
  `tedad` int(11) NOT NULL,
  `gheymat_vahed` decimal(12,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sefaresh_id` (`sefaresh_id`),
  KEY `mahsul_id` (`mahsul_id`),
  CONSTRAINT `sefaresh_mahsul_ibfk_1` FOREIGN KEY (`sefaresh_id`) REFERENCES `sefaresh` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sefaresh_mahsul_ibfk_2` FOREIGN KEY (`mahsul_id`) REFERENCES `mahsulat` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sefaresh_mahsul`
--

LOCK TABLES `sefaresh_mahsul` WRITE;
/*!40000 ALTER TABLE `sefaresh_mahsul` DISABLE KEYS */;
/*!40000 ALTER TABLE `sefaresh_mahsul` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_tanzim`
--

DROP TABLE IF EXISTS `seo_tanzim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_tanzim` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `safhe_masir` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL COMMENT 'مسیر صفحه مثلاً /khadamat',
  `meta_onvan` varchar(255) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `meta_sharh` varchar(500) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `og_tasvir` varchar(500) COLLATE utf8mb4_persian_ci DEFAULT NULL COMMENT 'Open Graph image',
  `noindex` tinyint(1) DEFAULT '0' COMMENT '1=noindex',
  `canonical` varchar(500) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `safhe_masir` (`safhe_masir`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_tanzim`
--

LOCK TABLES `seo_tanzim` WRITE;
/*!40000 ALTER TABLE `seo_tanzim` DISABLE KEYS */;
INSERT INTO `seo_tanzim` VALUES (1,'/','مهراد سام | پشتیبانی کامپیوتر در ملارد و مارلیک','خدمات پشتیبانی کامپیوتر از راه دور و حضوری در ملارد، مارلیک. رفع کندی، نصب نرم‌افزار، طراحی سایت.',NULL,0,NULL,NULL),(2,'/khadamat','خدمات | مهراد سام','مشاهده تمام خدمات پشتیبانی کامپیوتری مهراد سام در ملارد',NULL,0,NULL,NULL),(3,'/tarnegar','تارنگار | مهراد سام','آموزش‌ها و مطالب تخصصی کامپیوتر و فناوری',NULL,0,NULL,NULL),(4,'/tamas','تماس با ما | مهراد سام','با مهراد سام در ملارد تماس بگیرید',NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `seo_tanzim` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_sections`
--

DROP TABLE IF EXISTS `template_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `template_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global',
  `section_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `vaziat` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page_section` (`page`,`section_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_sections`
--

LOCK TABLES `template_sections` WRITE;
/*!40000 ALTER TABLE `template_sections` DISABLE KEYS */;
INSERT INTO `template_sections` VALUES (1,'global','mohtava','محتوا','',1,'2026-07-08 19:29:50','2026-07-08 19:29:50');
/*!40000 ALTER TABLE `template_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL,
  `remember_token` varchar(128) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_persian_ci DEFAULT 'user',
  `selected_language` varchar(5) COLLATE utf8mb4_persian_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'admin','$2y$10$pUFjaRp3lmah2hEeCOxmFuP3cnFUtJWEm3wZNiPChKGkyJjrAwDxu','72e3b4d4eb0a4ca25743d6912eea77cc528f623aaf41a54f17d454e092edb7cb','admin@site.com','admin',NULL,'2026-07-06 07:56:17'),(3,'mehrsam-bot','$2y$10$b4u3vnKoZwshyynw6Epu8.yjrfL4A3qo6mqvse2iTbB22fceShzyi',NULL,'ali.asgari.6106@gmail.com','admin',NULL,'2026-08-04 06:24:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'mehrsam_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-05  7:41:06
