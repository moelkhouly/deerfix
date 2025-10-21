-- =============================================
-- Deerfix Database Schema
-- Created: October 2025
-- Description: Bilingual website for adhesives, sealants, and PU foams
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- Table: categories
-- =============================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `slug_en` varchar(255) NOT NULL,
  `slug_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `description_en` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_en` (`slug_en`),
  UNIQUE KEY `slug_ar` (`slug_ar`),
  KEY `parent_id` (`parent_id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: products
-- =============================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug_en` varchar(255) NOT NULL,
  `slug_ar` varchar(255) NOT NULL,
  `description_en` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `specs_en` text DEFAULT NULL,
  `specs_ar` text DEFAULT NULL,
  `main_image` varchar(500) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `video_links` text DEFAULT NULL,
  `download_files` text DEFAULT NULL,
  `packaging_volume` decimal(10,2) DEFAULT NULL,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `downloads_count` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_new` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `slug_en` (`slug_en`),
  UNIQUE KEY `slug_ar` (`slug_ar`),
  KEY `category_id` (`category_id`),
  KEY `is_featured` (`is_featured`),
  KEY `is_new` (`is_new`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: blogs
-- =============================================
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `slug_en` varchar(255) NOT NULL,
  `slug_ar` varchar(255) NOT NULL,
  `content_en` longtext NOT NULL,
  `content_ar` longtext NOT NULL,
  `excerpt_en` text DEFAULT NULL,
  `excerpt_ar` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `author_en` varchar(100) NOT NULL DEFAULT 'Deerfix Team',
  `author_ar` varchar(100) NOT NULL DEFAULT 'فريق ديرفيكس',
  `views` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `published_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_en` (`slug_en`),
  UNIQUE KEY `slug_ar` (`slug_ar`),
  KEY `is_published` (`is_published`),
  KEY `is_featured` (`is_featured`),
  KEY `published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: contacts
-- =============================================
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `contact_type` varchar(50) DEFAULT 'general',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: subscribers
-- =============================================
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- Sample Data for Testing
-- =============================================

-- Insert sample categories
INSERT INTO `categories` (`slug_en`, `slug_ar`, `name_en`, `name_ar`, `description_en`, `description_ar`) VALUES
('adhesives', 'المواد-اللاصقة', 'Adhesives', 'المواد اللاصقة', 'Strong bonding adhesives for various applications', 'مواد لاصقة قوية لتطبيقات متنوعة'),
('sealants', 'المواد-السادة', 'Sealants', 'المواد السادة', 'High-quality sealants for protection', 'مواد سادة عالية الجودة للحماية'),
('pu-foams', 'رغوات-البولي-يوريثان', 'PU Foams', 'رغوات البولي يوريثان', 'Polyurethane foams for insulation', 'رغوات البولي يوريثان للعزل');

-- Insert sample products
INSERT INTO `products` (`category_id`, `sku`, `name_en`, `name_ar`, `slug_en`, `slug_ar`, `description_en`, `description_ar`, `packaging_volume`, `is_new`) VALUES
(1, 'ADH-001', 'Construction Adhesive', 'لاصق البناء', 'construction-adhesive', 'لاصق-البناء', 'Strong adhesive for construction materials', 'لاصق قوي لمواد البناء', 5.0, 1),
(2, 'SEL-001', 'Silicone Sealant', 'سليكون سائل', 'silicone-sealant', 'سليكون-سائل', 'Flexible silicone sealant for gaps', 'سليكون سائل مرن للفراغات', 3.5, 1);

-- Insert sample blog post
INSERT INTO `blogs` (`title_en`, `title_ar`, `slug_en`, `slug_ar`, `content_en`, `content_ar`, `is_published`) VALUES
('How to Choose the Right Adhesive', 'كيفية اختيار اللاصق المناسب', 'choose-right-adhesive', 'اختيار-اللاصق-المناسب', 'Content in English...', 'المحتوى بالعربية...', 1);