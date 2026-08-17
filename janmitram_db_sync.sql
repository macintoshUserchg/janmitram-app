/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.8-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: u939461333_app_janmitram
-- ------------------------------------------------------
-- Server version	11.8.8-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `address_type` varchar(191) DEFAULT NULL,
  `area` varchar(191) DEFAULT NULL,
  `area_id` bigint(20) unsigned DEFAULT NULL,
  `road_no` varchar(191) DEFAULT NULL,
  `flat_no` varchar(191) DEFAULT NULL,
  `house_no` varchar(191) DEFAULT NULL,
  `address_line` varchar(191) DEFAULT NULL,
  `address_line2` varchar(191) DEFAULT NULL,
  `post_code` varchar(191) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_customer_id_foreign` (`customer_id`),
  KEY `addresses_area_id_foreign` (`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES
(1,'shubham','7014635778',1,'home',NULL,NULL,NULL,NULL,NULL,'SHIV MANDIR KE  SAMNE SABJI MANDI KE PAS BADHARNA ROAD SIKAR ROAD HARMADA JAIPUR RAJ. 302013',NULL,NULL,'27.005776817259','75.777665185774',1,NULL,'2026-08-05 18:20:49','2026-08-05 18:20:49'),
(2,'maharaj','7014635773',1,'other',NULL,NULL,NULL,NULL,NULL,'Mansarover',NULL,NULL,'27.005764064456','75.777740831583',0,NULL,'2026-08-05 18:24:32','2026-08-05 18:24:32'),
(3,'aditi','9414057680',4,'home','JAGAT PURA JAIPUR',8,NULL,NULL,NULL,'rail vihar vdn',NULL,NULL,'26.959409706548','75.773391723633',1,NULL,'2026-08-11 21:51:19','2026-08-13 19:11:47'),
(4,'jhotwara','7690918898',5,'home',NULL,4,NULL,NULL,NULL,'gandhi path  vaishali nagar',NULL,NULL,'27.005711886903','75.777670922761',1,NULL,'2026-08-13 19:30:27','2026-08-13 19:30:27'),
(5,'chgabdab','9876542345',9,'home',NULL,1,NULL,NULL,NULL,'417 Sanganer thana , Jaipur',NULL,NULL,'26.7827458','75.8379471',1,NULL,'2026-08-16 20:09:47','2026-08-16 20:09:47'),
(6,'bhaskar','8078605615',11,'home',NULL,3,NULL,NULL,NULL,'rail vihar vidyadhar nagar jaipur',NULL,NULL,'26.9607854','75.7748097',1,NULL,'2026-08-16 21:44:44','2026-08-16 21:44:44');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `admin_coupons`
--

DROP TABLE IF EXISTS `admin_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_coupons` (
  `coupon_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  KEY `admin_coupons_coupon_id_foreign` (`coupon_id`),
  KEY `admin_coupons_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_coupons`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `admin_coupons` WRITE;
/*!40000 ALTER TABLE `admin_coupons` DISABLE KEYS */;
INSERT INTO `admin_coupons` VALUES
(1,1);
/*!40000 ALTER TABLE `admin_coupons` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ads_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `delivery_amount` double DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `areas`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `areas` WRITE;
/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` VALUES
(1,'SANGANER JAIPUR',20,1,'2026-08-04 21:02:14','2026-08-05 21:38:15'),
(2,'PRATAP NAGAR SANGANER JAIPUR',30,1,'2026-08-05 21:31:49','2026-08-05 21:39:09'),
(3,'VIDYADHAR NAGAR JAIPUR',25,1,'2026-08-05 21:32:25','2026-08-05 21:38:46'),
(4,'VAISHALI NAGAR JAIPUR',20,1,'2026-08-05 21:32:49','2026-08-05 21:38:34'),
(5,'MURLIPURA JAIPUR',20,1,'2026-08-05 21:33:13','2026-08-05 21:39:19'),
(6,'MALVEEY NAGAR JAIPUR',30,1,'2026-08-05 21:33:32','2026-08-05 21:39:41'),
(7,'MANSAROWAR JAIPUR',30,1,'2026-08-05 21:33:54','2026-08-05 21:39:31'),
(8,'VKI HARMADA JAIPUR',20,1,'2026-08-05 21:34:36','2026-08-05 21:38:02'),
(9,'PURANI BASTI JAIPUR',30,1,'2026-08-05 21:35:29','2026-08-05 21:38:58'),
(10,'JAGAT PURA JAIPUR',30,1,'2026-08-05 21:37:16','2026-08-05 21:37:46');
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_media_id_foreign` (`media_id`),
  KEY `banners_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES
(1,NULL,4,NULL,NULL,1,'2026-08-03 11:20:28','2026-08-03 11:20:28');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `blog_tags`
--

DROP TABLE IF EXISTS `blog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_tags` (
  `blog_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  KEY `blog_tags_blog_id_foreign` (`blog_id`),
  KEY `blog_tags_tag_id_foreign` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_tags`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `blog_tags` WRITE;
/*!40000 ALTER TABLE `blog_tags` DISABLE KEYS */;
INSERT INTO `blog_tags` VALUES
(1,1);
/*!40000 ALTER TABLE `blog_tags` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `blog_views`
--

DROP TABLE IF EXISTS `blog_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_views_blog_id_foreign` (`blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_views`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `blog_views` WRITE;
/*!40000 ALTER TABLE `blog_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_views` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `description` longtext NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blogs_user_id_foreign` (`user_id`),
  KEY `blogs_media_id_foreign` (`media_id`),
  KEY `blogs_category_id_foreign` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES
(1,1,'JANMITRAM','janmitram',73,1,'<p>NATURAL PRODUCT</p>',1,'2026-08-08 20:50:31','2026-08-08 20:50:31');
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brands_media_id_foreign` (`media_id`),
  KEY `brands_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES
(1,'JANMITRAM',NULL,NULL,1,1,0,'2026-08-03 11:17:28','2026-08-05 21:58:33');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `card_number` varchar(191) NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cards_card_number_unique` (`card_number`),
  KEY `cards_customer_id_foreign` (`customer_id`),
  CONSTRAINT `cards_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cards`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cards` WRITE;
/*!40000 ALTER TABLE `cards` DISABLE KEYS */;
INSERT INTO `cards` VALUES
(1,'99340096',4,0,'2026-08-11 20:16:42','2026-08-11 20:20:28'),
(2,'20934851',4,0,'2026-08-11 20:20:28','2026-08-11 20:20:43'),
(3,'53832613',NULL,1,'2026-08-16 21:37:11','2026-08-16 21:37:11'),
(4,'79655499',11,1,'2026-08-16 21:37:26','2026-08-16 21:37:26');
/*!40000 ALTER TABLE `cards` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cart_access_tokens`
--

DROP TABLE IF EXISTS `cart_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `access_token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_access_tokens_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_access_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cart_access_tokens` WRITE;
/*!40000 ALTER TABLE `cart_access_tokens` DISABLE KEYS */;
INSERT INTO `cart_access_tokens` VALUES
(1,NULL,'37563687-c131-4733-a4ba-08e023902551','2026-08-04 12:11:01','2026-08-04 12:11:01');
/*!40000 ALTER TABLE `cart_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `access_token` varchar(191) DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `is_buy_now` tinyint(1) NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `color` varchar(191) DEFAULT NULL,
  `size` varchar(191) DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_customer_id_foreign` (`customer_id`),
  KEY `carts_product_id_foreign` (`product_id`),
  KEY `carts_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES
(1,NULL,'37563687-c131-4733-a4ba-08e023902551',1,1,1,1,NULL,NULL,'250 GM','2026-08-04 12:11:01','2026-08-04 12:11:01'),
(13,4,'',60,4,1,1,NULL,NULL,'10kg','2026-08-13 18:43:01','2026-08-13 18:43:01'),
(15,4,'',58,6,0,3,NULL,NULL,'200GM','2026-08-13 18:57:11','2026-08-13 19:04:40'),
(24,4,'',57,6,0,1,NULL,NULL,'200GM','2026-08-13 19:04:37','2026-08-13 19:04:37'),
(25,4,'',59,4,0,1,NULL,NULL,'1 KG','2026-08-13 19:04:41','2026-08-13 19:04:41'),
(26,4,'',60,4,0,1,NULL,NULL,'10kg','2026-08-13 19:04:43','2026-08-13 19:04:43'),
(27,4,'',61,4,0,1,NULL,NULL,'1 KG','2026-08-13 19:04:45','2026-08-13 19:04:45'),
(28,4,'',62,4,0,1,NULL,NULL,'1 KG','2026-08-13 19:04:52','2026-08-13 19:04:52'),
(29,4,'',63,4,0,1,NULL,NULL,'200GM','2026-08-13 19:04:56','2026-08-13 19:04:56'),
(30,4,'',64,4,0,1,NULL,NULL,NULL,'2026-08-13 19:04:58','2026-08-13 19:04:58'),
(31,4,'',65,4,0,1,NULL,NULL,'1 Ltr','2026-08-13 19:05:00','2026-08-13 19:05:00'),
(32,4,'',66,4,0,1,NULL,NULL,'200ml','2026-08-13 19:05:02','2026-08-13 19:05:02'),
(33,4,'',67,4,0,1,NULL,NULL,'1 Ltr','2026-08-13 19:05:05','2026-08-13 19:05:05'),
(47,9,'',81,11,1,1,NULL,NULL,'1 KG','2026-08-16 20:03:45','2026-08-16 20:03:45');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `type` varchar(191) DEFAULT 'other',
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_media_id_foreign` (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'GROCERY',NULL,'other',3,'Grocery and Spices',1,'2026-08-03 11:11:55','2026-08-05 20:41:24'),
(2,'HEALTH CARE',NULL,'other',18,NULL,1,'2026-08-05 20:40:03','2026-08-05 20:40:03'),
(3,'HOME CARE',NULL,'other',19,NULL,1,'2026-08-05 20:40:30','2026-08-05 20:40:30'),
(4,'PERSONAL CARE',NULL,'other',20,NULL,1,'2026-08-05 20:41:02','2026-08-05 20:41:02'),
(5,'OTHERS',NULL,'other',27,'DOMESTIC ITEMS',1,'2026-08-05 20:49:16','2026-08-06 20:10:42');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `category_subcategories`
--

DROP TABLE IF EXISTS `category_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_subcategories` (
  `category_id` bigint(20) unsigned NOT NULL,
  `sub_category_id` bigint(20) unsigned NOT NULL,
  KEY `category_subcategories_category_id_foreign` (`category_id`),
  KEY `category_subcategories_sub_category_id_foreign` (`sub_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_subcategories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `category_subcategories` WRITE;
/*!40000 ALTER TABLE `category_subcategories` DISABLE KEYS */;
INSERT INTO `category_subcategories` VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(2,5),
(2,6),
(1,7),
(1,8);
/*!40000 ALTER TABLE `category_subcategories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `colors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `color_code` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `colors_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colors`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `colors` WRITE;
/*!40000 ALTER TABLE `colors` DISABLE KEYS */;
INSERT INTO `colors` VALUES
(1,'HALDI POWDER',NULL,1,'#fbe80e',1,'2026-08-03 11:17:17','2026-08-05 21:20:34'),
(2,'ATTA (WHEAT)',NULL,1,'#f1e9e9',1,'2026-08-05 21:18:36','2026-08-05 21:18:36'),
(3,'DHANIYA POWDER',NULL,1,'#62ac6b',1,'2026-08-05 21:19:31','2026-08-05 21:19:31'),
(4,'LAL MIRCH POWDER',NULL,1,'#c21919',1,'2026-08-05 21:20:11','2026-08-05 21:20:11'),
(5,'DESHI KHAND KHANDSARI',NULL,1,'#c09f72',1,'2026-08-05 21:22:38','2026-08-05 21:22:38'),
(6,'BESAN',NULL,1,'#fbd828',1,'2026-08-05 21:24:24','2026-08-05 21:24:24');
/*!40000 ALTER TABLE `colors` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `contact_us`
--

DROP TABLE IF EXISTS `contact_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_us` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `whatsapp` varchar(191) DEFAULT NULL,
  `messenger` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_us`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `contact_us` WRITE;
/*!40000 ALTER TABLE `contact_us` DISABLE KEYS */;
INSERT INTO `contact_us` VALUES
(1,'9414057690','aditi@janmitram.com','9414057690',NULL,'2026-08-04 21:00:45','2026-08-04 21:05:08');
/*!40000 ALTER TABLE `contact_us` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `numeric_code` varchar(5) DEFAULT NULL,
  `phone_code` varchar(10) DEFAULT NULL,
  `capital` varchar(191) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `currency_name` varchar(191) DEFAULT NULL,
  `currency_symbol` varchar(191) DEFAULT NULL,
  `native` varchar(191) DEFAULT NULL,
  `region` varchar(191) DEFAULT NULL,
  `latitude` varchar(30) DEFAULT NULL,
  `longitude` varchar(30) DEFAULT NULL,
  `emoji` varchar(20) DEFAULT NULL,
  `emojiU` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES
(1,'Afghanistan','004','93','Kabul','AFN','Afghan afghani','؋','افغانستان','Asia','-74.65000000','4.48000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(2,'Antigua and Barbuda','028','1','St. John\'s','XCD','Eastern Caribbean dollar','$','Antigua and Barbuda','Americas','17.05000000','-61.80000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(3,'Argentina','032','54','Buenos Aires','ARS','Argentine peso','$','Argentina','Americas','-34.00000000','-64.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(4,'Armenia','051','374','Yerevan','AMD','Armenian dram','֏','Հայաստան','Asia','40.00000000','45.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(5,'Aruba','533','297','Oranjestad','AWG','Aruban florin','ƒ','Aruba','Americas','12.50000000','-69.96666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(6,'Australia','036','61','Canberra','AUD','Australian dollar','$','Australia','Oceania','-27.00000000','133.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(7,'Austria','040','43','Vienna','EUR','Euro','€','Österreich','Europe','47.33333333','13.33333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(8,'Azerbaijan','031','994','Baku','AZN','Azerbaijani manat','m','Azərbaycan','Asia','40.50000000','47.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(9,'Bahrain','048','973','Manama','BHD','Bahraini dinar','.د.ب','‏البحرين','Asia','26.00000000','50.55000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(10,'Bangladesh','050','880','Dhaka','BDT','Bangladeshi taka','৳','Bangladesh','Asia','24.00000000','90.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(11,'Barbados','052','1','Bridgetown','BBD','Barbadian dollar','Bds$','Barbados','Americas','13.16666666','-59.53333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(12,'Belarus','112','375','Minsk','BYN','Belarusian ruble','Br','Белару́сь','Europe','53.00000000','28.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(13,'Belgium','056','32','Brussels','EUR','Euro','€','België','Europe','50.83333333','4.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(14,'Belize','084','501','Belmopan','BZD','Belize dollar','$','Belize','Americas','17.25000000','-88.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(15,'Benin','204','229','Porto-Novo','XOF','West African CFA franc','CFA','Bénin','Africa','9.50000000','2.25000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(16,'Bermuda','060','1','Hamilton','BMD','Bermudian dollar','$','Bermuda','Americas','32.33333333','-64.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(17,'Bhutan','064','975','Thimphu','BTN','Bhutanese ngultrum','Nu.','ʼbrug-yul','Asia','27.50000000','90.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(18,'Bolivia','068','591','Sucre','BOB','Bolivian boliviano','Bs.','Bolivia','Americas','-10.00000000','-55.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(19,'British Indian Ocean Territory','086','246','Diego Garcia','USD','United States dollar','$','British Indian Ocean Territory','Africa','60.00000000','-95.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(20,'Cape Verde','132','238','Praia','CVE','Cape Verdean escudo','$','Cabo Verde','Africa','-30.00000000','-71.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(21,'China','156','86','Beijing','CNY','Chinese yuan','¥','中国','Asia','35.00000000','105.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(22,'Christmas Island','162','61','Flying Fish Cove','AUD','Australian dollar','$','Christmas Island','Oceania','-10.50000000','105.66666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(23,'Cocos (Keeling) Islands','166','61','West Island','AUD','Australian dollar','$','Cocos (Keeling) Islands','Oceania','-12.50000000','96.83333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(24,'Colombia','170','57','Bogotá','COP','Colombian peso','$','Colombia','Americas','4.00000000','-72.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(25,'Comoros','174','269','Moroni','KMF','Comorian franc','CF','Komori','Africa','-12.16666666','44.25000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(26,'Congo','178','242','Brazzaville','XAF','Central African CFA franc','FC','République du Congo','Africa','-1.00000000','15.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(27,'Cook Islands','184','682','Avarua','NZD','Cook Islands dollar','$','Cook Islands','Oceania','-21.23333333','-159.76666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(28,'Costa Rica','188','506','San Jose','CRC','Costa Rican colón','₡','Costa Rica','Americas','10.00000000','-84.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(29,'Cote D\'Ivoire (Ivory Coast)','384','225','Yamoussoukro','XOF','West African CFA franc','CFA',NULL,'Africa','8.00000000','-5.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(30,'Croatia','191','385','Zagreb','HRK','Croatian kuna','kn','Hrvatska','Europe','45.16666666','15.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(31,'Cuba','192','53','Havana','CUP','Cuban peso','$','Cuba','Americas','21.50000000','-80.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(32,'Curaçao','531','599','Willemstad','ANG','Netherlands Antillean guilder','ƒ','Curaçao','Americas','12.11666700','-68.93333300',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(33,'Cyprus','196','357','Nicosia','EUR','Euro','€','Κύπρος','Europe','35.00000000','33.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(34,'Czech Republic','203','420','Prague','CZK','Czech koruna','Kč','Česká republika','Europe','49.75000000','15.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(35,'Democratic Republic of the Congo','180','243','Kinshasa','CDF','Congolese Franc','FC','République démocratique du Congo','Africa','0.00000000','25.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(36,'Denmark','208','45','Copenhagen','DKK','Danish krone','Kr.','Danmark','Europe','56.00000000','10.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(37,'Djibouti','262','253','Djibouti','DJF','Djiboutian franc','Fdj','Djibouti','Africa','11.50000000','43.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(38,'Dominica','212','1','Roseau','XCD','Eastern Caribbean dollar','$','Dominica','Americas','15.41666666','-61.33333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(39,'Dominican Republic','214','1','Santo Domingo','DOP','Dominican peso','$','República Dominicana','Americas','19.00000000','-70.66666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(40,'Ecuador','218','593','Quito','USD','United States dollar','$','Ecuador','Americas','-2.00000000','-77.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(41,'Egypt','818','20','Cairo','EGP','Egyptian pound','ج.م','مصر‎','Africa','27.00000000','30.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(42,'El Salvador','222','503','San Salvador','USD','United States dollar','$','El Salvador','Americas','13.83333333','-88.91666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(43,'Equatorial Guinea','226','240','Malabo','XAF','Central African CFA franc','FCFA','Guinea Ecuatorial','Africa','2.00000000','10.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(44,'Eritrea','232','291','Asmara','ERN','Eritrean nakfa','Nfk','ኤርትራ','Africa','15.00000000','39.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(45,'Estonia','233','372','Tallinn','EUR','Euro','€','Eesti','Europe','59.00000000','26.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(46,'Eswatini','748','268','Mbabane','SZL','Lilangeni','E','Swaziland','Africa','-26.50000000','31.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(47,'Ethiopia','231','251','Addis Ababa','ETB','Ethiopian birr','Nkf','ኢትዮጵያ','Africa','8.00000000','38.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(48,'Falkland Islands','238','500','Stanley','FKP','Falkland Islands pound','£','Falkland Islands','Americas','-51.75000000','-59.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(49,'Faroe Islands','234','298','Torshavn','DKK','Danish krone','Kr.','Føroyar','Europe','62.00000000','-7.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(50,'Fiji Islands','242','679','Suva','FJD','Fijian dollar','FJ$','Fiji','Oceania','-18.00000000','175.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(51,'Finland','246','358','Helsinki','EUR','Euro','€','Suomi','Europe','64.00000000','26.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(52,'France','250','33','Paris','EUR','Euro','€','France','Europe','46.00000000','2.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(53,'French Guiana','254','594','Cayenne','EUR','Euro','€','Guyane française','Americas','4.00000000','-53.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(54,'French Polynesia','258','689','Papeete','XPF','CFP franc','₣','Polynésie française','Oceania','-15.00000000','-140.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(55,'French Southern Territories','260','262','Port-aux-Francais','EUR','Euro','€','Territoire des Terres australes et antarctiques fr','Africa','-49.25000000','69.16700000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(56,'Gabon','266','241','Libreville','XAF','Central African CFA franc','FCFA','Gabon','Africa','-1.00000000','11.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(57,'Georgia','268','995','Tbilisi','GEL','Georgian lari','ლ','საქართველო','Asia','42.00000000','43.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(58,'Germany','276','49','Berlin','EUR','Euro','€','Deutschland','Europe','51.00000000','9.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(59,'Ghana','288','233','Accra','GHS','Ghanaian cedi','GH₵','Ghana','Africa','8.00000000','-2.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(60,'Gibraltar','292','350','Gibraltar','GIP','Gibraltar pound','£','Gibraltar','Europe','36.13333333','-5.35000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(61,'Greece','300','30','Athens','EUR','Euro','€','Ελλάδα','Europe','39.00000000','22.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(62,'Greenland','304','299','Nuuk','DKK','Danish krone','Kr.','Kalaallit Nunaat','Americas','72.00000000','-40.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(63,'Grenada','308','1','St. George\'s','XCD','Eastern Caribbean dollar','$','Grenada','Americas','12.11666666','-61.66666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(64,'Guadeloupe','312','590','Basse-Terre','EUR','Euro','€','Guadeloupe','Americas','16.25000000','-61.58333300',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(65,'Guam','316','1','Hagatna','USD','US Dollar','$','Guam','Oceania','13.46666666','144.78333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(66,'Guatemala','320','502','Guatemala City','GTQ','Guatemalan quetzal','Q','Guatemala','Americas','15.50000000','-90.25000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(67,'Guernsey and Alderney','831','44','St Peter Port','GBP','British pound','£','Guernsey','Europe','49.46666666','-2.58333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(68,'Guinea','324','224','Conakry','GNF','Guinean franc','FG','Guinée','Africa','11.00000000','-10.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(69,'Guinea-Bissau','624','245','Bissau','XOF','West African CFA franc','CFA','Guiné-Bissau','Africa','12.00000000','-15.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(70,'Guyana','328','592','Georgetown','GYD','Guyanese dollar','$','Guyana','Americas','5.00000000','-59.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(71,'Haiti','332','509','Port-au-Prince','HTG','Haitian gourde','G','Haïti','Americas','19.00000000','-72.41666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(72,'Heard Island and McDonald Islands','334','672','','AUD','Australian dollar','$','Heard Island and McDonald Islands','','-53.10000000','72.51666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(73,'Honduras','340','504','Tegucigalpa','HNL','Honduran lempira','L','Honduras','Americas','15.00000000','-86.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(74,'Hong Kong S.A.R.','344','852','Hong Kong','HKD','Hong Kong dollar','$','香港','Asia','22.25000000','114.16666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(75,'Hungary','348','36','Budapest','HUF','Hungarian forint','Ft','Magyarország','Europe','47.00000000','20.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(76,'Iceland','352','354','Reykjavik','ISK','Icelandic króna','kr','Ísland','Europe','65.00000000','-18.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(77,'India','356','91','New Delhi','INR','Indian rupee','₹','भारत','Asia','20.00000000','77.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(78,'Indonesia','360','62','Jakarta','IDR','Indonesian rupiah','Rp','Indonesia','Asia','-5.00000000','120.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(79,'Iran','364','98','Tehran','IRR','Iranian rial','﷼','ایران','Asia','32.00000000','53.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(80,'Iraq','368','964','Baghdad','IQD','Iraqi dinar','د.ع','العراق','Asia','33.00000000','44.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(81,'Ireland','372','353','Dublin','EUR','Euro','€','Éire','Europe','53.00000000','-8.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(82,'Israel','376','972','Jerusalem','ILS','Israeli new shekel','₪','יִשְׂרָאֵל','Asia','31.50000000','34.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(83,'Italy','380','39','Rome','EUR','Euro','€','Italia','Europe','42.83333333','12.83333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(84,'Jamaica','388','1','Kingston','JMD','Jamaican dollar','J$','Jamaica','Americas','18.25000000','-77.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(85,'Japan','392','81','Tokyo','JPY','Japanese yen','¥','日本','Asia','36.00000000','138.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(86,'Jersey','832','44','Saint Helier','GBP','British pound','£','Jersey','Europe','49.25000000','-2.16666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(87,'Jordan','400','962','Amman','JOD','Jordanian dinar','ا.د','الأردن','Asia','31.00000000','36.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(88,'Kazakhstan','398','7','Astana','KZT','Kazakhstani tenge','лв','Қазақстан','Asia','48.00000000','68.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(89,'Kenya','404','254','Nairobi','KES','Kenyan shilling','KSh','Kenya','Africa','1.00000000','38.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(90,'Kiribati','296','686','Tarawa','AUD','Australian dollar','$','Kiribati','Oceania','1.41666666','173.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(91,'Kosovo','926','383','Pristina','EUR','Euro','€','Republika e Kosovës','Europe','29.50000000','45.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(92,'Kyrgyzstan','417','996','Bishkek','KGS','Kyrgyzstani som','лв','Кыргызстан','Asia','41.00000000','75.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(93,'Laos','418','856','Vientiane','LAK','Lao kip','₭','ສປປລາວ','Asia','18.00000000','105.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(94,'Latvia','428','371','Riga','EUR','Euro','€','Latvija','Europe','57.00000000','25.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(95,'Lebanon','422','961','Beirut','LBP','Lebanese pound','£','لبنان','Asia','33.83333333','35.83333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(96,'Lesotho','426','266','Maseru','LSL','Lesotho loti','L','Lesotho','Africa','-29.50000000','28.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(97,'Liberia','430','231','Monrovia','LRD','Liberian dollar','$','Liberia','Africa','6.50000000','-9.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(98,'Libya','434','218','Tripolis','LYD','Libyan dinar','د.ل','‏ليبيا','Africa','25.00000000','17.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(99,'Liechtenstein','438','423','Vaduz','CHF','Swiss franc','CHf','Liechtenstein','Europe','47.26666666','9.53333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(100,'Lithuania','440','370','Vilnius','EUR','Euro','€','Lietuva','Europe','56.00000000','24.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(101,'Luxembourg','442','352','Luxembourg','EUR','Euro','€','Luxembourg','Europe','49.75000000','6.16666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(102,'Macau S.A.R.','446','853','Macao','MOP','Macanese pataca','$','澳門','Asia','22.16666666','113.55000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(103,'Madagascar','450','261','Antananarivo','MGA','Malagasy ariary','Ar','Madagasikara','Africa','-20.00000000','47.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(104,'Malawi','454','265','Lilongwe','MWK','Malawian kwacha','MK','Malawi','Africa','-13.50000000','34.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(105,'Malaysia','458','60','Kuala Lumpur','MYR','Malaysian ringgit','RM','Malaysia','Asia','2.50000000','112.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(106,'Maldives','462','960','Male','MVR','Maldivian rufiyaa','Rf','Maldives','Asia','3.25000000','73.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(107,'Mali','466','223','Bamako','XOF','West African CFA franc','CFA','Mali','Africa','17.00000000','-4.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(108,'Malta','470','356','Valletta','EUR','Euro','€','Malta','Europe','35.83333333','14.58333333',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(109,'Man (Isle of)','833','44','Douglas, Isle of Man','GBP','British pound','£','Isle of Man','Europe','54.25000000','-4.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(110,'Marshall Islands','584','692','Majuro','USD','United States dollar','$','M̧ajeļ','Oceania','9.00000000','168.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(111,'Martinique','474','596','Fort-de-France','EUR','Euro','€','Martinique','Americas','14.66666700','-61.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(112,'Mauritania','478','222','Nouakchott','MRO','Mauritanian ouguiya','MRU','موريتانيا','Africa','20.00000000','-12.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(113,'Mauritius','480','230','Port Louis','MUR','Mauritian rupee','₨','Maurice','Africa','-20.28333333','57.55000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(114,'Mayotte','175','262','Mamoudzou','EUR','Euro','€','Mayotte','Africa','-12.83333333','45.16666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(115,'Mexico','484','52','Ciudad de México','MXN','Mexican peso','$','México','Americas','23.00000000','-102.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(116,'Micronesia','583','691','Palikir','USD','United States dollar','$','Micronesia','Oceania','6.91666666','158.25000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(117,'Moldova','498','373','Chisinau','MDL','Moldovan leu','L','Moldova','Europe','47.00000000','29.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(118,'Monaco','492','377','Monaco','EUR','Euro','€','Monaco','Europe','43.73333333','7.40000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(119,'Mongolia','496','976','Ulan Bator','MNT','Mongolian tögrög','₮','Монгол улс','Asia','46.00000000','105.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(120,'Montenegro','499','382','Podgorica','EUR','Euro','€','Црна Гора','Europe','42.50000000','19.30000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(121,'Montserrat','500','1','Plymouth','XCD','Eastern Caribbean dollar','$','Montserrat','Americas','16.75000000','-62.20000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(122,'Morocco','504','212','Rabat','MAD','Moroccan dirham','DH','المغرب','Africa','32.00000000','-5.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(123,'Mozambique','508','258','Maputo','MZN','Mozambican metical','MT','Moçambique','Africa','-18.25000000','35.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(124,'Myanmar','104','95','Nay Pyi Taw','MMK','Burmese kyat','K','မြန်မာ','Asia','22.00000000','98.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(125,'Namibia','516','264','Windhoek','NAD','Namibian dollar','$','Namibia','Africa','-22.00000000','17.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(126,'Nauru','520','674','Yaren','AUD','Australian dollar','$','Nauru','Oceania','-0.53333333','166.91666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(127,'Nepal','524','977','Kathmandu','NPR','Nepalese rupee','₨','नपल','Asia','28.00000000','84.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(128,'Netherlands','528','31','Amsterdam','EUR','Euro','€','Nederland','Europe','52.50000000','5.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(129,'New Caledonia','540','687','Noumea','XPF','CFP franc','₣','Nouvelle-Calédonie','Oceania','-21.50000000','165.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(130,'New Zealand','554','64','Wellington','NZD','New Zealand dollar','$','New Zealand','Oceania','-41.00000000','174.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(131,'Nicaragua','558','505','Managua','NIO','Nicaraguan córdoba','C$','Nicaragua','Americas','13.00000000','-85.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(132,'Niger','562','227','Niamey','XOF','West African CFA franc','CFA','Niger','Africa','16.00000000','8.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(133,'Nigeria','566','234','Abuja','NGN','Nigerian naira','₦','Nigeria','Africa','10.00000000','8.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(134,'Niue','570','683','Alofi','NZD','New Zealand dollar','$','Niuē','Oceania','-19.03333333','-169.86666666',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(135,'Norfolk Island','574','672','Kingston','AUD','Australian dollar','$','Norfolk Island','Oceania','-29.03333333','167.95000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(136,'North Korea','408','850','Pyongyang','KPW','North Korean Won','₩','북한','Asia','40.00000000','127.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(137,'North Macedonia','807','389','Skopje','MKD','Denar','ден','Северна Македонија','Europe','41.83333333','22.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(138,'Northern Mariana Islands','580','1','Saipan','USD','United States dollar','$','Northern Mariana Islands','Oceania','15.20000000','145.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(139,'Norway','578','47','Oslo','NOK','Norwegian krone','kr','Norge','Europe','62.00000000','10.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(140,'Oman','512','968','Muscat','OMR','Omani rial','.ع.ر','عمان','Asia','21.00000000','57.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(141,'Pakistan','586','92','Islamabad','PKR','Pakistani rupee','₨','Pakistan','Asia','30.00000000','70.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(142,'Palau','585','680','Melekeok','USD','United States dollar','$','Palau','Oceania','7.50000000','134.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(143,'Palestinian Territory Occupied','275','970','East Jerusalem','ILS','Israeli new shekel','₪','فلسطين','Asia','31.90000000','35.20000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(144,'Panama','591','507','Panama City','PAB','Panamanian balboa','B/.','Panamá','Americas','9.00000000','-80.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(145,'Papua New Guinea','598','675','Port Moresby','PGK','Papua New Guinean kina','K','Papua Niugini','Oceania','-6.00000000','147.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(146,'Paraguay','600','595','Asuncion','PYG','Paraguayan guarani','₲','Paraguay','Americas','-23.00000000','-58.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(147,'Peru','604','51','Lima','PEN','Peruvian sol','S/.','Perú','Americas','-10.00000000','-76.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(148,'Philippines','608','63','Manila','PHP','Philippine peso','₱','Pilipinas','Asia','13.00000000','122.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(149,'Pitcairn Island','612','870','Adamstown','NZD','New Zealand dollar','$','Pitcairn Islands','Oceania','-25.06666666','-130.10000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(150,'Poland','616','48','Warsaw','PLN','Polish złoty','zł','Polska','Europe','52.00000000','20.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(151,'Portugal','620','351','Lisbon','EUR','Euro','€','Portugal','Europe','39.50000000','-8.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(152,'Puerto Rico','630','1','San Juan','USD','United States dollar','$','Puerto Rico','Americas','18.25000000','-66.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(153,'Qatar','634','974','Doha','QAR','Qatari riyal','ق.ر','قطر','Asia','25.50000000','51.25000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(154,'Reunion','638','262','Saint-Denis','EUR','Euro','€','La Réunion','Africa','-21.15000000','55.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(155,'Romania','642','40','Bucharest','RON','Romanian leu','lei','România','Europe','46.00000000','25.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(156,'Russia','643','7','Moscow','RUB','Russian ruble','₽','Россия','Europe','60.00000000','100.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(157,'Rwanda','646','250','Kigali','RWF','Rwandan franc','FRw','Rwanda','Africa','-2.00000000','30.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(158,'Saint Helena','654','290','Jamestown','SHP','Saint Helena pound','£','Saint Helena','Africa','-15.95000000','-5.70000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(159,'Saint Kitts and Nevis','659','1','Basseterre','XCD','Eastern Caribbean dollar','$','Saint Kitts and Nevis','Americas','17.33333333','-62.75000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(160,'Saint Lucia','662','1','Castries','XCD','Eastern Caribbean dollar','$','Saint Lucia','Americas','13.88333333','-60.96666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(161,'Saint Pierre and Miquelon','666','508','Saint-Pierre','EUR','Euro','€','Saint-Pierre-et-Miquelon','Americas','46.83333333','-56.33333333',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(162,'Saint Vincent and the Grenadines','670','1','Kingstown','XCD','Eastern Caribbean dollar','$','Saint Vincent and the Grenadines','Americas','13.25000000','-61.20000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(163,'Saint-Barthelemy','652','590','Gustavia','EUR','Euro','€','Saint-Barthélemy','Americas','18.50000000','-63.41666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(164,'Saint-Martin (French part)','663','590','Marigot','EUR','Euro','€','Saint-Martin','Americas','18.08333333','-63.95000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(165,'Samoa','882','685','Apia','WST','Samoan tālā','SAT','Samoa','Oceania','-13.58333333','-172.33333333',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(166,'San Marino','674','378','San Marino','EUR','Euro','€','San Marino','Europe','43.76666666','12.41666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(167,'Sao Tome and Principe','678','239','Sao Tome','STD','Dobra','Db','São Tomé e Príncipe','Africa','1.00000000','7.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(168,'Saudi Arabia','682','966','Riyadh','SAR','Saudi riyal','﷼','المملكة العربية السعودية','Asia','25.00000000','45.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(169,'Senegal','686','221','Dakar','XOF','West African CFA franc','CFA','Sénégal','Africa','14.00000000','-14.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(170,'Serbia','688','381','Belgrade','RSD','Serbian dinar','din','Србија','Europe','44.00000000','21.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(171,'Seychelles','690','248','Victoria','SCR','Seychellois rupee','SRe','Seychelles','Africa','-4.58333333','55.66666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(172,'Sierra Leone','694','232','Freetown','SLL','Sierra Leonean leone','Le','Sierra Leone','Africa','8.50000000','-11.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(173,'Singapore','702','65','Singapur','SGD','Singapore dollar','$','Singapore','Asia','1.36666666','103.80000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(174,'Sint Maarten (Dutch part)','534','1721','Philipsburg','ANG','Netherlands Antillean guilder','ƒ','Sint Maarten','Americas','18.03333300','-63.05000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(175,'Slovakia','703','421','Bratislava','EUR','Euro','€','Slovensko','Europe','48.66666666','19.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(176,'Slovenia','705','386','Ljubljana','EUR','Euro','€','Slovenija','Europe','46.11666666','14.81666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(177,'Solomon Islands','090','677','Honiara','SBD','Solomon Islands dollar','Si$','Solomon Islands','Oceania','-8.00000000','159.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(178,'Somalia','706','252','Mogadishu','SOS','Somali shilling','Sh.so.','Soomaaliya','Africa','10.00000000','49.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(179,'South Africa','710','27','Pretoria','ZAR','South African rand','R','South Africa','Africa','-29.00000000','24.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(180,'South Georgia','239','500','Grytviken','GBP','British pound','£','South Georgia','Americas','-54.50000000','-37.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(181,'South Korea','410','82','Seoul','KRW','Won','₩','대한민국','Asia','37.00000000','127.50000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(182,'South Sudan','728','211','Juba','SSP','South Sudanese pound','£','South Sudan','Africa','7.00000000','30.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(183,'Spain','724','34','Madrid','EUR','Euro','€','España','Europe','40.00000000','-4.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(184,'Sri Lanka','144','94','Colombo','LKR','Sri Lankan rupee','Rs','śrī laṃkāva','Asia','7.00000000','81.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(185,'Sudan','729','249','Khartoum','SDG','Sudanese pound','.س.ج','السودان','Africa','15.00000000','30.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(186,'Suriname','740','597','Paramaribo','SRD','Surinamese dollar','$','Suriname','Americas','4.00000000','-56.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(187,'Svalbard and Jan Mayen Islands','744','47','Longyearbyen','NOK','Norwegian Krone','kr','Svalbard og Jan Mayen','Europe','78.00000000','20.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(188,'Sweden','752','46','Stockholm','SEK','Swedish krona','kr','Sverige','Europe','62.00000000','15.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(189,'Switzerland','756','41','Bern','CHF','Swiss franc','CHf','Schweiz','Europe','47.00000000','8.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(190,'Syria','760','963','Damascus','SYP','Syrian pound','LS','سوريا','Asia','35.00000000','38.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(191,'Taiwan','158','886','Taipei','TWD','New Taiwan dollar','$','臺灣','Asia','23.50000000','121.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(192,'Tajikistan','762','992','Dushanbe','TJS','Tajikistani somoni','SM','Тоҷикистон','Asia','39.00000000','71.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(193,'Tanzania','834','255','Dodoma','TZS','Tanzanian shilling','TSh','Tanzania','Africa','-6.00000000','35.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(194,'Thailand','764','66','Bangkok','THB','Thai baht','฿','ประเทศไทย','Asia','15.00000000','100.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(195,'The Bahamas','044','1','Nassau','BSD','Bahamian dollar','B$','Bahamas','Americas','24.25000000','-76.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(196,'The Gambia ','270','220','Banjul','GMD','Gambian dalasi','D','Gambia','Africa','13.46666666','-16.56666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(197,'Timor-Leste','626','670','Dili','USD','United States dollar','$','Timor-Leste','Asia','-8.83333333','125.91666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(198,'Togo','768','228','Lome','XOF','West African CFA franc','CFA','Togo','Africa','8.00000000','1.16666666',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(199,'Tokelau','772','690','','NZD','New Zealand dollar','$','Tokelau','Oceania','-9.00000000','-172.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(200,'Tonga','776','676','Nuku\'alofa','TOP','Tongan paʻanga','$','Tonga','Oceania','-20.00000000','-175.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(201,'Trinidad and Tobago','780','1','Port of Spain','TTD','Trinidad and Tobago dollar','$','Trinidad and Tobago','Americas','11.00000000','-61.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(202,'Tunisia','788','216','Tunis','TND','Tunisian dinar','ت.د','تونس','Africa','34.00000000','9.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(203,'Turkey','792','90','Ankara','TRY','Turkish lira','₺','Türkiye','Asia','39.00000000','35.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(204,'Turkmenistan','795','993','Ashgabat','TMT','Turkmenistan manat','T','Türkmenistan','Asia','40.00000000','60.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(205,'Turks and Caicos Islands','796','1','Cockburn Town','USD','United States dollar','$','Turks and Caicos Islands','Americas','21.75000000','-71.58333333',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(206,'Tuvalu','798','688','Funafuti','AUD','Australian dollar','$','Tuvalu','Oceania','-8.00000000','178.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(207,'Uganda','800','256','Kampala','UGX','Ugandan shilling','USh','Uganda','Africa','1.00000000','32.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(208,'Ukraine','804','380','Kyiv','UAH','Ukrainian hryvnia','₴','Україна','Europe','49.00000000','32.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(209,'United Arab Emirates','784','971','Abu Dhabi','AED','United Arab Emirates dirham','إ.د','دولة الإمارات العربية المتحدة','Asia','24.00000000','54.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(210,'United Kingdom','826','44','London','GBP','British pound','£','United Kingdom','Europe','54.00000000','-2.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(211,'United States','840','1','Washington','USD','United States dollar','$','United States','Americas','38.00000000','-97.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(212,'United States Minor Outlying Islands','581','1','','USD','United States dollar','$','United States Minor Outlying Islands','Americas','0.00000000','0.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(213,'Uruguay','858','598','Montevideo','UYU','Uruguayan peso','$','Uruguay','Americas','-33.00000000','-56.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(214,'Uzbekistan','860','998','Tashkent','UZS','Uzbekistani soʻm','лв','O‘zbekiston','Asia','41.00000000','64.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(215,'Vanuatu','548','678','Port Vila','VUV','Vanuatu vatu','VT','Vanuatu','Oceania','-16.00000000','167.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(216,'Vatican City State (Holy See)','336','379','Vatican City','EUR','Euro','€','Vaticano','Europe','41.90000000','12.45000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(217,'Venezuela','862','58','Caracas','VES','Bolívar','Bs','Venezuela','Americas','8.00000000','-66.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(218,'Vietnam','704','84','Hanoi','VND','Vietnamese đồng','₫','Việt Nam','Asia','16.16666666','107.83333333',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(219,'Virgin Islands (British)','092','1','Road Town','USD','United States dollar','$','British Virgin Islands','Americas','18.43138300','-64.62305000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(220,'Virgin Islands (US)','850','1','Charlotte Amalie','USD','United States dollar','$','United States Virgin Islands','Americas','18.34000000','-64.93000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(221,'Wallis and Futuna Islands','876','681','Mata Utu','XPF','CFP franc','₣','Wallis et Futuna','Oceania','-13.30000000','-176.20000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(222,'Western Sahara','732','212','El-Aaiun','MAD','Moroccan Dirham','MAD','الصحراء الغربية','Africa','24.50000000','-13.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(223,'Yemen','887','967','Sanaa','YER','Yemeni rial','﷼','اليَمَن','Asia','15.00000000','48.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(224,'Zambia','894','260','Lusaka','ZMW','Zambian kwacha','ZK','Zambia','Africa','-15.00000000','30.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(225,'Zimbabwe','716','263','Harare','ZWL','Zimbabwe Dollar','$','Zimbabwe','Africa','-20.00000000','30.00000000',NULL,NULL,1,NULL,'2026-08-03 08:41:10','2026-08-03 08:41:10');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) NOT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(191) NOT NULL,
  `discount` double NOT NULL,
  `min_amount` double NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expired_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `limit_for_user` int(11) DEFAULT 10,
  `max_discount_amount` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupons_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES
(1,'222222',NULL,'Percentage',10,600,'2026-08-11 09:00:00','2026-08-31 21:00:00',1,'2026-08-11 20:17:23','2026-08-11 20:17:23',NULL,12,NULL);
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) DEFAULT NULL,
  `symbol` varchar(191) NOT NULL,
  `rate` varchar(191) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES
(1,'INR',NULL,'₹','1',1,1,'2026-08-03 08:41:09','2026-08-17 18:59:14'),
(2,'USD',NULL,'$','1',1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(3,'EUR',NULL,'€','1',1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES
(1,6,'2026-08-05 18:18:12','2026-08-05 18:18:12'),
(2,1,'2026-08-06 18:21:21','2026-08-06 18:21:21'),
(3,5,'2026-08-06 19:12:55','2026-08-06 19:12:55'),
(4,9,'2026-08-11 19:23:57','2026-08-11 19:23:57'),
(5,10,'2026-08-13 19:25:50','2026-08-13 19:25:50'),
(6,7,'2026-08-13 20:03:47','2026-08-13 20:03:47'),
(7,11,'2026-08-14 21:23:32','2026-08-14 21:23:32'),
(8,13,'2026-08-15 21:51:46','2026-08-15 21:51:46'),
(9,3,'2026-08-16 20:03:45','2026-08-16 20:03:45'),
(10,8,'2026-08-16 20:52:08','2026-08-16 20:52:08'),
(11,12,'2026-08-16 21:33:28','2026-08-16 21:33:28');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `delivery_charges`
--

DROP TABLE IF EXISTS `delivery_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_charges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `min_qty` int(11) DEFAULT NULL,
  `max_qty` int(11) DEFAULT NULL,
  `charge` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_charges`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `delivery_charges` WRITE;
/*!40000 ALTER TABLE `delivery_charges` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_charges` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `device_keys`
--

DROP TABLE IF EXISTS `device_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_keys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `key` varchar(191) NOT NULL,
  `device_type` varchar(191) DEFAULT 'android',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_keys_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_keys`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `device_keys` WRITE;
/*!40000 ALTER TABLE `device_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_keys` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `driver_locations`
--

DROP TABLE IF EXISTS `driver_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint(20) unsigned NOT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_locations_driver_id_foreign` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_locations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `driver_locations` WRITE;
/*!40000 ALTER TABLE `driver_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `driver_locations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `driver_orders`
--

DROP TABLE IF EXISTS `driver_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `assign_for` varchar(191) DEFAULT NULL,
  `is_accept` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cash_collect` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `driver_orders_driver_id_foreign` (`driver_id`),
  KEY `driver_orders_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_orders`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `driver_orders` WRITE;
/*!40000 ALTER TABLE `driver_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `driver_orders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `drivers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `total_cash_collected` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drivers_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `favorites_customer_id_foreign` (`customer_id`),
  KEY `favorites_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `flash_sale_products`
--

DROP TABLE IF EXISTS `flash_sale_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `flash_sale_products` (
  `flash_sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `sale_quantity` int(11) DEFAULT 0,
  KEY `flash_sale_products_flash_sale_id_foreign` (`flash_sale_id`),
  KEY `flash_sale_products_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sale_products`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `flash_sale_products` WRITE;
/*!40000 ALTER TABLE `flash_sale_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `flash_sale_products` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `flash_sales`
--

DROP TABLE IF EXISTS `flash_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `flash_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `start_time` time NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_time` time NOT NULL,
  `end_date` date DEFAULT NULL,
  `discount` double NOT NULL DEFAULT 0,
  `min_discount` double DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(191) DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_sales_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sales`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `flash_sales` WRITE;
/*!40000 ALTER TABLE `flash_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `flash_sales` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `footer_items`
--

DROP TABLE IF EXISTS `footer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `footer_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'link',
  `title` varchar(191) DEFAULT NULL,
  `ar_title` varchar(191) DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `shop_type` varchar(191) NOT NULL DEFAULT 'single',
  `target` varchar(191) NOT NULL DEFAULT '_self',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_items_footer_id_foreign` (`footer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_items`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `footer_items` WRITE;
/*!40000 ALTER TABLE `footer_items` DISABLE KEYS */;
INSERT INTO `footer_items` VALUES
(1,1,'logo',NULL,NULL,NULL,'single','_self',1,0,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(2,1,'text','The ultimate all-in-one solution for your eCommerce business worldwide.','الحل الأمثل الشامل لأعمال التجارة الإلكترونية الخاصة بك في جميع أنحاء العالم',NULL,'single','_self',1,1,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(3,1,'phone','+880123456789','+880123456789',NULL,'single','_self',1,2,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(4,1,'email','admin@example.com','admin@example.com',NULL,'single','_self',1,3,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(5,1,'social_links',NULL,NULL,NULL,'single','_self',1,4,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(6,2,'link','Products','المنتجات','/products','single','_self',1,0,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(7,2,'link','Most Popular','الاكثر شعبية','/most-popular','single','_self',1,1,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(8,2,'link','Best Deal','الافضل العرض','/best-deal','single','_self',1,2,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(9,2,'link','Become a Seller','صاحب متجر','/shop/register','multi','_blank',1,3,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(10,2,'link','Blogs','المدونات','/blogs','single','_self',1,5,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(11,2,'link','About us','من نحن','/about-us','single','_self',1,0,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(12,3,'link','Contact','اتصل بنا','/contact-us','single','_self',1,1,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(13,3,'link','Terms & Conditions','الشروط والاحكام','/terms-and-conditions','single','_self',1,2,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(14,3,'link','Privacy Policy','سياسة الخصوصية','/privacy-policy','single','_self',1,3,0,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(15,4,'app_store',NULL,NULL,NULL,'single','_self',1,0,1,'2026-08-03 08:41:10','2026-08-03 08:41:10');
/*!40000 ALTER TABLE `footer_items` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `footers`
--

DROP TABLE IF EXISTS `footers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `footers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `ar_title` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `footers` WRITE;
/*!40000 ALTER TABLE `footers` DISABLE KEYS */;
INSERT INTO `footers` VALUES
(1,NULL,NULL,NULL,0,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(2,'Quick Links','روابط سريعة',NULL,1,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(3,'Company','شركة',NULL,2,1,'2026-08-03 08:41:10','2026-08-03 08:41:10'),
(4,'Download our app','تحميل التطبيق',NULL,3,1,'2026-08-03 08:41:10','2026-08-03 08:41:10');
/*!40000 ALTER TABLE `footers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `total_image` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galleries_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `generate_settings`
--

DROP TABLE IF EXISTS `generate_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `generate_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `favicon_id` bigint(20) unsigned DEFAULT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `google_playstore_url` varchar(191) DEFAULT NULL,
  `show_download_app` tinyint(1) NOT NULL DEFAULT 1,
  `app_store_url` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `currency_position` varchar(191) DEFAULT NULL,
  `direction` varchar(191) DEFAULT NULL,
  `show_footer` tinyint(1) NOT NULL DEFAULT 1,
  `footer_phone` varchar(191) DEFAULT NULL,
  `footer_email` varchar(191) DEFAULT NULL,
  `primary_color` varchar(191) NOT NULL DEFAULT '#8b5cf6',
  `secondary_color` varchar(191) NOT NULL DEFAULT '#ede9fe',
  `business_based_on` varchar(191) NOT NULL DEFAULT 'commission',
  `commission` double NOT NULL DEFAULT 10,
  `commission_type` varchar(191) DEFAULT 'percentage',
  `commission_charge` varchar(191) NOT NULL DEFAULT 'per_order',
  `shop_pos` tinyint(1) NOT NULL DEFAULT 1,
  `shop_register` tinyint(1) NOT NULL DEFAULT 1,
  `shop_type` varchar(191) DEFAULT 'multi' COMMENT 'multi, single',
  `new_product_approval` tinyint(1) NOT NULL DEFAULT 1,
  `update_product_approval` tinyint(1) NOT NULL DEFAULT 1,
  `min_withdraw` double DEFAULT NULL,
  `max_withdraw` double DEFAULT NULL,
  `withdraw_request` int(11) DEFAULT NULL,
  `footer_text` varchar(191) DEFAULT NULL,
  `footer_description` varchar(191) DEFAULT NULL,
  `footer_logo_id` bigint(20) unsigned DEFAULT NULL,
  `footer_qrcode_id` bigint(20) unsigned DEFAULT NULL,
  `app_logo_id` bigint(20) unsigned DEFAULT NULL,
  `show_sku` tinyint(1) NOT NULL DEFAULT 0,
  `default_delivery_charge` double NOT NULL DEFAULT 0,
  `shop_allocation_radius_km` double NOT NULL DEFAULT 50,
  `cash_on_delivery` tinyint(1) NOT NULL DEFAULT 1,
  `online_payment` tinyint(1) NOT NULL DEFAULT 1,
  `return_order_within_days` int(11) DEFAULT 3,
  `product_description` text DEFAULT NULL,
  `page_description` text DEFAULT NULL,
  `blog_description` varchar(191) DEFAULT NULL,
  `card_discount_percentage` int(11) NOT NULL DEFAULT 10,
  `card_min_order_amount` decimal(10,2) NOT NULL DEFAULT 500.00,
  PRIMARY KEY (`id`),
  KEY `generate_settings_logo_id_foreign` (`logo_id`),
  KEY `generate_settings_favicon_id_foreign` (`favicon_id`),
  KEY `generate_settings_footer_logo_id_foreign` (`footer_logo_id`),
  KEY `generate_settings_footer_qrcode_id_foreign` (`footer_qrcode_id`),
  KEY `generate_settings_app_logo_id_foreign` (`app_logo_id`),
  KEY `generate_settings_currency_id_foreign` (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generate_settings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `generate_settings` WRITE;
/*!40000 ALTER TABLE `generate_settings` DISABLE KEYS */;
INSERT INTO `generate_settings` VALUES
(1,'Janmitram','Janmitram',NULL,NULL,'9784338720','janmitraudyog@gmail.com','SHIV MANDIR KE  SAMNE SABJI MANDI KE PAS BADHARNA ROAD SIKAR ROAD HARMADA JAIPUR RAJ. 302013',NULL,1,NULL,'2026-08-03 08:41:09','2026-08-17 18:59:14','₹',1,'prefix',NULL,1,'+919784338720',NULL,'#ee9644','#fceada','commission',10,'percentage','per_order',1,1,'multi',1,1,NULL,NULL,NULL,'All right reserved by company',NULL,NULL,NULL,NULL,0,0,50,1,1,3,'Product name: {product_name}. Short description: {short_description}. Write a long, SEO-friendly product description that includes relevant keywords, highlights unique features, and encourages buyers to take action.','The page title is {title}. Generate a well-structured, professional, and legally appropriate long content for this page, ensuring it covers all important points relevant to {title}.','The blog title is {title}. Generate a well-structured, professional, and legally appropriate long content for this blog, ensuring it covers all important points relevant to {title}.',10,500.00);
/*!40000 ALTER TABLE `generate_settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `google_re_captchas`
--

DROP TABLE IF EXISTS `google_re_captchas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_re_captchas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_key` text NOT NULL,
  `secret_key` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_re_captchas`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `google_re_captchas` WRITE;
/*!40000 ALTER TABLE `google_re_captchas` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_re_captchas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `direction` varchar(191) DEFAULT 'ltr',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `legal_pages`
--

DROP TABLE IF EXISTS `legal_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `legal_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `slug` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legal_pages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `legal_pages` WRITE;
/*!40000 ALTER TABLE `legal_pages` DISABLE KEYS */;
INSERT INTO `legal_pages` VALUES
(1,'Privacy Policy','privacy-policy','<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information when you use our services.</p><p>We collect information you provide directly, such as your name, email address, phone number, and shipping address when you create an account or place an order.</p><p>We use your information to process orders, provide customer support, and improve our services. We do not share your personal information with third parties except as necessary to fulfill orders or as required by law.</p>','2026-08-03 08:41:09','2026-08-03 08:41:09'),
(2,'Terms of Service','terms-and-conditions','<h2>Terms of Service</h2><p>By using our platform, you agree to these terms and conditions. Please read them carefully.</p><p>All products and services are provided as described. We reserve the right to modify or discontinue any service without prior notice.</p><p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>','2026-08-03 08:41:09','2026-08-03 08:41:09'),
(3,'Return policy / Refund Policy','return-and-refund-policy','<h2>Return & Refund Policy</h2><p>We accept returns within 30 days of delivery. Items must be unused and in original packaging.</p><p>Refunds will be processed within 5-7 business days after we receive the returned item. Shipping costs are non-refundable.</p><p>For digital products, all sales are final unless the product is defective or not as described.</p>','2026-08-03 08:41:09','2026-08-03 08:41:09'),
(4,'Shipping & Delivery Policy','shipping-and-delivery-policy','<h2>Shipping & Delivery Policy</h2><p>We offer free shipping on orders over a certain amount. Standard delivery takes 3-7 business days.</p><p>Express delivery is available at an additional cost. Delivery times may vary depending on your location.</p><p>We are not responsible for delays caused by customs, weather conditions, or other factors beyond our control.</p>','2026-08-03 08:41:09','2026-08-03 08:41:09'),
(5,'About Us','about-us','<h2>About Us</h2><p>Welcome to our marketplace! We are a leading e-commerce platform connecting buyers with trusted sellers.</p><p>Our mission is to provide a seamless shopping experience with quality products at competitive prices. We support local businesses and entrepreneurs.</p><p>With our multi-vendor platform, you can browse products from multiple shops, compare prices, and enjoy secure payments.</p>','2026-08-03 08:41:09','2026-08-03 08:41:09');
/*!40000 ALTER TABLE `legal_pages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT 'image',
  `name` text DEFAULT NULL,
  `original_name` varchar(191) DEFAULT NULL,
  `src` text NOT NULL,
  `extention` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES
(1,'image','logo.png',NULL,'shops/logo/EV3KaPJgR67q6fOTJlB8rsqy6NPoe0Guzl7utcry.png','png','2026-08-03 08:41:11','2026-08-06 21:38:39'),
(2,'image','bottom.png',NULL,'shops/banner/kWYJiUFmRRlRxYmwkqOEeMAE8n1IRAivEqsfEV94.png','png','2026-08-03 08:41:11','2026-08-06 21:38:39'),
(3,'image','logo.png',NULL,'categories/g4KK0NPay60E6ye9P5VFFeB6BucQN4mlJefc7o1T.png',NULL,'2026-08-03 11:11:55','2026-08-05 20:38:55'),
(4,'thumbnail','JanMitra_logoNew.png',NULL,'banners/39iLmbcav4gWnNF6o1AvU7hhQt4DaaZJMPDgyvGf.png',NULL,'2026-08-03 11:20:28','2026-08-03 11:20:28'),
(5,'image','FINAL JANMITRAM MASALE TURMERIC POWDER 200G.jpg',NULL,'products/d2OSxqIlfhCxbTnPdfpAEH43Uk1TYlKsg0RSvf1e.jpg',NULL,'2026-08-03 11:23:39','2026-08-05 21:51:15'),
(6,'image','WhatsApp Image 2026-08-11 at 12.33.17 PM.jpeg',NULL,'users/profile/y3Kk9P21lTyAwkNsSUpuD00S6kRKwFdgn3Mz0WYw.jpg',NULL,'2026-08-03 11:44:52','2026-08-16 20:22:45'),
(7,'image','logo.png',NULL,'shops/logo/db93YkbGfvbHv3I4IHRXL1m4q8fUf0QqI13OhkBP.png',NULL,'2026-08-03 11:44:54','2026-08-06 21:59:56'),
(8,'image','bottom.png',NULL,'shops/banner/2VltxFFx380KRFCWujyYX80KQhWGnCQ99HWHcgSF.png',NULL,'2026-08-03 11:44:54','2026-08-06 21:59:56'),
(9,'image','actual_logo.png',NULL,'users/profile/enyNlWMiFFs3ksH9eeLUg2H5gJSOd7Q8ehe3YUID.png',NULL,'2026-08-03 11:48:49','2026-08-03 11:48:49'),
(10,'image','gym_db - public.png',NULL,'shops/logo/5S5ksyJzYHhvJwlb3Cr7bXwjr4jHBv64N6oFWhJW.png',NULL,'2026-08-03 11:48:51','2026-08-03 11:48:51'),
(11,'image','ChatGPT Image Dec 28, 2025, 09_28_35 AM.png',NULL,'shops/banner/8UdhG3ofCVfpbQBNUVDY2nMohwPXg4SFwLjmbMq3.png',NULL,'2026-08-03 11:48:51','2026-08-03 11:48:51'),
(12,'image','WhatsApp Image 2023-08-22 at 1.01.36 PM.jpeg',NULL,'users/profile/KV7wzxiXWZJzgh3dnGgEZPgperLujT9VWYzqO4DC.jpg',NULL,'2026-08-03 15:28:56','2026-08-08 19:43:47'),
(13,'image','WhatsApp Image 2023-08-22 at 1.01.36 PM.jpeg',NULL,'shops/logo/jGU4dCYlS8wzws1dJrggofK47v7IZCRuaY08aaSf.jpg',NULL,'2026-08-03 15:28:58','2026-08-10 18:45:57'),
(14,'image','bottom.png',NULL,'shops/banner/PB452SpYT0U4MutcwEXAj2uBdZH3fAqvxGLHR33N.png',NULL,'2026-08-03 15:28:58','2026-08-06 22:00:46'),
(15,'image','dinesh sharma photo.jpeg',NULL,'users/profile/m3D03DfksVclCU9tKJ19bFC0lkURFasEO038at62.jpg',NULL,'2026-08-03 21:32:31','2026-08-08 20:24:10'),
(16,'image','WhatsApp Image 2026-08-10 at 6.47.10 PM.jpeg',NULL,'shops/logo/g40IBt3QSaRElbv5AR6Y1vkXvFHbZqd8zHgrMOMG.jpg',NULL,'2026-08-03 21:32:31','2026-08-10 20:37:12'),
(17,'image','bottom.png',NULL,'shops/banner/ZJFiEveCwGMSAYUCQx67zfAVGy92vKW6DGHmLyGy.png',NULL,'2026-08-03 21:32:31','2026-08-06 21:57:26'),
(18,'image','logo.png',NULL,'categories/kySwCpg2Y5clo5h5kpnGH0JUPVKq7oFZFt6bt5Il.png',NULL,'2026-08-05 20:40:03','2026-08-05 20:40:03'),
(19,'image','logo.png',NULL,'categories/em3W5s54VRailzI2WPzThO2tEHi5WLEuA08hYjEx.png',NULL,'2026-08-05 20:40:30','2026-08-05 20:40:30'),
(20,'image','logo.png',NULL,'categories/FOASiijWHGkoAKohHjuKVKR4Ybx0Zr2egvuC7Q8K.png',NULL,'2026-08-05 20:41:02','2026-08-05 20:41:02'),
(21,'image','FINAL JANMITRAM MASALE CORIANDER 200G.jpg',NULL,'categories/xgM9Ca7VEuh3RfkwoeLQ8JAlwdKuQDSy0bUmHly3.jpg',NULL,'2026-08-05 20:42:46','2026-08-05 20:42:46'),
(22,'image','FINAL JANMITRAM MASALE CHILLI POWDER 200G.jpg',NULL,'categories/USLywLBmOqrQhVIZnpujwDoVn6f0KeqP8Uybg4rs.jpg',NULL,'2026-08-05 20:43:38','2026-08-05 20:43:38'),
(23,'image','FINAL JANMITRAM MASALE TURMERIC POWDER 200G.jpg',NULL,'categories/v4WW6L66LE7pAPBxZ2GRRCtB9k2F9b3aAPcP7Lsm.jpg',NULL,'2026-08-05 20:44:39','2026-08-05 20:44:39'),
(24,'image','logo.png',NULL,'categories/i3Ie6ckP7QLvhvv7VCQF06D7bVCs5MkNfrWaQCMB.png',NULL,'2026-08-05 20:45:30','2026-08-05 20:45:30'),
(25,'image','logo.png',NULL,'categories/SYA9jagMfBVhIkafnVKb2V1eSWzE5v1LESE7U7vi.png',NULL,'2026-08-05 20:46:07','2026-08-05 20:46:07'),
(26,'image','logo.png',NULL,'categories/kKMs9u3NcGHtNbMMpRdm4Klzy0AgvcJS8g92z5J6.png',NULL,'2026-08-05 20:46:47','2026-08-05 20:46:47'),
(27,'image','logo.png',NULL,'categories/pODEsZhhi9ZNVqM67lDTGpCXM5kJHdJfuO3ojYu0.png',NULL,'2026-08-05 20:49:16','2026-08-05 20:49:16'),
(28,'image','logo.png',NULL,'categories/mnA61A2TpumwJhQ6Q8oWKcxqaX7rZ9TApyrpYNa4.png',NULL,'2026-08-05 20:50:06','2026-08-05 20:50:06'),
(29,'image','logo.png',NULL,'categories/fH2cp3hNT4dnpGuVpQvHL2XakLRU8otiy66VFAJT.png',NULL,'2026-08-05 20:50:47','2026-08-05 20:50:47'),
(30,'image','logo.png',NULL,'categories/NkfxG13TOBUke62vD3Gv2CWagtEvxr0PooDTNR17.png',NULL,'2026-08-05 20:52:06','2026-08-05 20:52:06'),
(31,'image','logo.png',NULL,'categories/2ytO55SC6r3j1Eh8m3hpHni53RlEdha2neMdKaYg.png',NULL,'2026-08-05 20:53:08','2026-08-05 20:53:08'),
(32,'image','logo.png',NULL,'categories/F58qsfSSMZURwvtzGfuSECN1GcNatvDJRGXzQenW.png',NULL,'2026-08-05 20:53:43','2026-08-05 20:53:43'),
(33,'image','logo.png',NULL,'categories/HBJlnZDkLd4gRbw27QzhiUNyA4k5eUOg1sYnOgv3.png',NULL,'2026-08-05 20:56:30','2026-08-05 20:56:30'),
(34,'image','logo.png',NULL,'categories/J7gK18TLL03cZarva5BXc4GY77ouUwXp6Fvd0gqS.png',NULL,'2026-08-05 20:57:16','2026-08-05 20:57:16'),
(35,'image','logo.png',NULL,'categories/r4EHt7jGvtcGb9fcfmSGxLUW1janl0JIkOC1kXBl.png',NULL,'2026-08-05 20:57:59','2026-08-05 20:57:59'),
(36,'image','logo.png',NULL,'categories/crRuVfDgxFG7JnRbUPRfUneXNAuGnbe28Dhb9iwH.png',NULL,'2026-08-05 20:58:26','2026-08-05 20:58:26'),
(37,'image','logo.png',NULL,'categories/J04GaoTBqcE7cwtp4tS2Y5fKIIyjxUoKbbBOGFpC.png',NULL,'2026-08-05 20:58:59','2026-08-05 20:58:59'),
(38,'image','logo.png',NULL,'categories/ckSKfYTptSbPECsoH6nQ3hgCKjVZa7PIHtEC4VFI.png',NULL,'2026-08-05 20:59:28','2026-08-05 20:59:28'),
(39,'image','logo.png',NULL,'categories/HJMtQYbArDRKsVhoYr47oxUBNCxSU4jC1bgOHo7v.png',NULL,'2026-08-05 21:00:32','2026-08-05 21:00:32'),
(40,'image','logo.png',NULL,'categories/rd3wC2szdVSbFRlzQYKaTV5iY8xQlp0U5N56BpuV.png',NULL,'2026-08-05 21:02:03','2026-08-05 21:02:03'),
(41,'image','logo.png',NULL,'categories/H1seZpeq0Pd3zQPhRjiYBi9HAbwxIUJ7MmYBD6sg.png',NULL,'2026-08-05 21:02:47','2026-08-05 21:02:47'),
(42,'image','logo.png',NULL,'categories/OQwCFdw2PZ66dT2he2v4Itq0WcqYI1U2j7KkVxSk.png',NULL,'2026-08-05 21:03:47','2026-08-05 21:03:47'),
(43,'image','logo.png',NULL,'categories/Fcbk8KPvkjtqLkOIrbdQw7HX26w6LnHBooTXfBwk.png',NULL,'2026-08-05 21:04:43','2026-08-05 21:04:43'),
(44,'image','logo.png',NULL,'categories/QpAPgc4F2Ya0IVhsZoKhYhvcwSg6pqJsZjuzveFp.png',NULL,'2026-08-05 21:05:38','2026-08-05 21:05:38'),
(45,'image','logo.png',NULL,'categories/LpvMGuljCTBpho4TNj8heJyH27MFq4n31ZFCYkNO.png',NULL,'2026-08-05 21:07:32','2026-08-05 21:07:32'),
(46,'image','logo.png',NULL,'categories/3sFh7RmZjGra1y1Fhc6boVralQOsb1RUi5pm7r8m.png',NULL,'2026-08-05 21:09:36','2026-08-05 21:09:36'),
(47,'image','logo.png',NULL,'categories/6olafC30sLFfJIgNx39sLFcUxy1HqBcfCO5yA3Yd.png',NULL,'2026-08-05 21:11:29','2026-08-05 21:11:29'),
(48,'image','logo.png',NULL,'categories/MeSavWNpDephCXnUniQE62cbxcn0yBMSEZHO4ifa.png',NULL,'2026-08-05 21:13:18','2026-08-05 21:13:18'),
(49,'thumbnail','FINAL JANMITRAM MASALE CORIANDER 200G.jpg',NULL,'products/gcBSJCz6XA1mLcP0EAc91MPy3TOz1a8AE5Umkntb.jpg',NULL,'2026-08-05 21:48:44','2026-08-05 21:48:44'),
(50,'thumbnail','FINAL JANMITRAM MASALE CHILLI POWDER 200G.jpg',NULL,'products/8ZkcGL437VBsuJZpqPm8g8viTse1og5dK4KhiAXQ.jpg',NULL,'2026-08-05 21:55:16','2026-08-05 21:55:16'),
(51,'image','logo.png',NULL,'categories/0jmYahGZENHCYfYI3l303oTOYr8y2IpQvja8VWf8.png',NULL,'2026-08-06 20:04:04','2026-08-06 20:04:04'),
(52,'image','logo.png',NULL,'categories/Na8LcRmK4orIrRJqcSqgKJFBnOs8LtIm2UlEk1TW.png',NULL,'2026-08-06 20:06:30','2026-08-06 20:06:30'),
(53,'image','logo.png',NULL,'categories/9ZBnQYz9qrBElPt5PtKYMb9pf0bITUA67Q8KsrZd.png',NULL,'2026-08-06 20:11:32','2026-08-06 20:11:32'),
(54,'image','logo.png',NULL,'categories/4tdq6JyA5r3fmwNdatLAs92ye5obZPDkZkzg2ai7.png',NULL,'2026-08-06 20:14:25','2026-08-06 20:14:25'),
(55,'image','logo.png',NULL,'categories/FYBE66FKl2SZ9hvQzrr1fMzyDV6tXJQRYeMxAUeT.png',NULL,'2026-08-06 20:31:58','2026-08-06 20:31:58'),
(56,'image','logo.png',NULL,'categories/oepWWjv4ttvTAjnUcMXgJmZt7uWuhrppKUHaRUhl.png',NULL,'2026-08-06 20:34:04','2026-08-06 20:34:04'),
(57,'image','logo.png',NULL,'products/JeVRocFlKYqzw5CiZubS9Dp16eS0tGbTtYYZXMqF.png',NULL,'2026-08-06 20:41:22','2026-08-06 21:11:11'),
(58,'thumbnail','logo.png',NULL,'products/RR3pYheIeBR6wR7IadSqo4mZeU2NHHyLEChbdgRf.png',NULL,'2026-08-06 20:49:53','2026-08-06 20:49:53'),
(59,'thumbnail','logo.png',NULL,'products/m3zNwckxyLf40udosHsQgRdufdryMhbD7GH8wat5.png',NULL,'2026-08-06 20:52:57','2026-08-06 20:52:57'),
(60,'thumbnail','logo.png',NULL,'products/13vUfuKLvTOVI5Dtr0La373QM5BP4v7WN7h1muvT.png',NULL,'2026-08-06 20:55:20','2026-08-06 20:55:20'),
(61,'image','logo.png',NULL,'categories/JOCal9HE9SSOypFwfV7wzGRPNVmJwAJKbiVDehue.png',NULL,'2026-08-06 21:01:00','2026-08-06 21:01:00'),
(62,'thumbnail','logo.png',NULL,'products/5gtKGGb6H3UqscTploOhhVJBv9GpOAVbDPUk3xWl.png',NULL,'2026-08-06 21:04:43','2026-08-06 21:04:43'),
(63,'image','logo.png',NULL,'categories/SbjT6Nb7EE0JjLy3H4RVB55YLVGQG2F3TsGc1nay.png',NULL,'2026-08-06 21:07:50','2026-08-06 21:07:50'),
(64,'thumbnail','FINAL JANMITRAM JAGGERY 1kg.jpg',NULL,'products/qTAqiSk7HLRVnOo3M5tmzwQDJFzt3ka0Ut10R9Fc.jpg',NULL,'2026-08-06 21:10:03','2026-08-06 21:10:03'),
(65,'thumbnail','logo.png',NULL,'products/3RypXOZG8KDqf7UVR4dTA2ScSPLGFwOXNdCMoA63.png',NULL,'2026-08-06 21:14:37','2026-08-06 21:14:37'),
(66,'thumbnail','FINAL JANMITRAM PULSES 1kg.jpg',NULL,'products/2eYXioocApYLkyolvrutExBe5pO4sgericDKhDQZ.jpg',NULL,'2026-08-06 21:16:14','2026-08-06 21:16:14'),
(67,'image','logo.png',NULL,'users/profile/bSbXxEHXbuyVjqBCd3iypwtY2IDWBLrVweklXtf5.png',NULL,'2026-08-06 21:38:39','2026-08-12 21:22:52'),
(68,'image','WhatsApp Image 2026-08-08 at 6.16.02 PM.jpeg',NULL,'users/profile/rQjeiedPH91Pnu1AFntL1DROnPohNDhiyKfbRuib.jpg',NULL,'2026-08-08 19:15:25','2026-08-08 19:15:25'),
(69,'image','WhatsApp Image 2026-08-08 at 6.16.02 PM.jpeg',NULL,'shops/logo/aSaaEm1LvyTXuha1ra8UucIVmPl0HOBdDYPnnuz4.jpg',NULL,'2026-08-08 19:15:25','2026-08-10 18:42:06'),
(70,'image','bottom.png',NULL,'shops/banner/aQRWFGWJeVzVpCOJftjxzsWxfAb5CpuGPBipmr7E.png',NULL,'2026-08-08 19:15:25','2026-08-08 19:15:25'),
(71,'image','WhatsApp Image 2026-08-08 at 6.33.59 PM.jpeg',NULL,'shops/kyc/3gIyHm0GH05brTiqcRn4FeCxF8lsDdnsGbq84Oyl.jpg',NULL,'2026-08-08 19:15:25','2026-08-08 19:15:25'),
(72,'image','WhatsApp Image 2026-08-08 at 6.33.59 PM.jpeg',NULL,'shops/kyc/V9rRV9jYu7iaVWkcCmmfnG4QLSN8wvMAveZ1ucOO.jpg',NULL,'2026-08-08 19:15:25','2026-08-08 19:15:25'),
(73,'image','bottom.png',NULL,'blogs/aUhPNFnNhYHIJMVyfYPYI7ZnOIZCcjjX5u3J30LJ.png',NULL,'2026-08-08 20:50:31','2026-08-08 20:50:31'),
(74,'image','WhatsApp Image 2026-08-09 at 7.50.15 PM.jpeg',NULL,'users/profile/MzUf6DxK5Xc0DRYegMBNXSvbBo8Jn91sUTChzElc.jpg',NULL,'2026-08-09 20:39:37','2026-08-09 20:39:37'),
(75,'image','WhatsApp Image 2026-08-09 at 7.50.15 PM.jpeg',NULL,'shops/logo/xmBvjpVz597I7PeFnrIQ9O4UBBcMx6Own9etpLeL.jpg',NULL,'2026-08-09 20:39:38','2026-08-10 18:42:47'),
(76,'image','bottom.png',NULL,'shops/banner/WtZrjJ97JO9nSgGbptEIb4lyjCLmqzYogoXrK6cA.png',NULL,'2026-08-09 20:39:38','2026-08-09 20:39:38'),
(77,'image','MUNDRIKA SHARMA AADHAR.pdf',NULL,'shops/kyc/ONLwcTYzozZ6kBw8BgyPlb8pOCpo6K2qP627tlbV.pdf',NULL,'2026-08-09 20:39:38','2026-08-09 20:39:38'),
(78,'image','WhatsApp Image 2026-08-09 at 7.50.15 PM (1).jpeg',NULL,'shops/kyc/H4rZV5IONwGEyHQFTRuUmgqyD3xxQqFDUc3agYlM.jpg',NULL,'2026-08-09 20:39:38','2026-08-09 20:39:38'),
(79,'image','MM SHARMA SBI.pdf',NULL,'shops/kyc/9TJboW3igKao3Gbm4gliv5NwFmoStnqwJTbxKpHj.pdf',NULL,'2026-08-09 20:39:38','2026-08-09 20:39:38'),
(80,'image','logo.png',NULL,'users/profile/ssCVh9jvIqOmiUQkfomi90Z77RH4502S27ZRlQ6c.png',NULL,'2026-08-10 20:22:55','2026-08-10 20:22:55'),
(81,'image','WhatsApp Image 2026-08-10 at 6.43.57 PM.jpeg',NULL,'shops/logo/Xm9uyJogZ0rs4zQKTbfFE2OpJsudDsqqKiP6YGXZ.jpg',NULL,'2026-08-10 20:22:55','2026-08-10 20:22:55'),
(82,'image','bottom.png',NULL,'shops/banner/oho2SAPbaSZV4Bg5r2KbLku6SDthXtVQbnea8G8p.png',NULL,'2026-08-10 20:22:55','2026-08-10 20:22:55'),
(83,'image','Kaagaz_20230919_183348156713.pdf',NULL,'shops/kyc/66OpihdhJQFp7HyO2lS6Y7FLuHNCAKjw8d5OQfTf.pdf',NULL,'2026-08-10 20:22:55','2026-08-10 20:22:55'),
(84,'image','Kaagaz_20230919_183348156713.pdf',NULL,'shops/kyc/7OYomWXfyUR78Iz1RC4O1cfuSLSHh9XjmmC25b4G.pdf',NULL,'2026-08-10 20:22:55','2026-08-10 20:22:55'),
(85,'image','logo.png',NULL,'users/profile/lEsZR5EhCSHQHjcrs7kau1GKLNDbaYNWfDA7lsfx.png',NULL,'2026-08-13 21:50:02','2026-08-13 21:50:02'),
(86,'image','logo.png',NULL,'shops/logo/Qlk3aM1bcSjlE0xNcWIn6AZSU54pQcHJtKpAq1En.png',NULL,'2026-08-13 21:50:03','2026-08-13 21:50:03'),
(87,'image','bottom.png',NULL,'shops/banner/4yGo1NMMUNjlfs0DepV8IX9TSTs7FrYAXkyvEe5Y.png',NULL,'2026-08-13 21:50:03','2026-08-13 21:50:03'),
(88,'image','aadhar - manmohan vishwakarma_page-0001 (1).jpg',NULL,'shops/kyc/LZq6jU7026ieHqXIQUQO5ufdlTpg3hWEdoQqBIsM.jpg',NULL,'2026-08-13 21:50:03','2026-08-13 21:50:03'),
(89,'image','COMPANY PAN CARD.pdf',NULL,'shops/kyc/ZcAub1ZvehlJXBCwe362tfsXADyFzLTqwvls3GBT.pdf',NULL,'2026-08-13 21:50:03','2026-08-13 21:50:03'),
(90,'image','logo.png',NULL,'users/profile/rc4T99r2AQatGzmOhaZWV0YfJKcMYTAlC0YLx3JP.png',NULL,'2026-08-15 21:23:18','2026-08-15 21:23:18'),
(91,'image','logo.png',NULL,'shops/logo/afg28oR0lE7b399mxDBq7az5AHsutwxokpXVc4yF.png',NULL,'2026-08-15 21:23:19','2026-08-15 21:23:19'),
(92,'image','bottom.png',NULL,'shops/banner/T1yiPALvkPt6FfjwHMmc1SN5qhNxZaj62nUGEgV9.png',NULL,'2026-08-15 21:23:19','2026-08-15 21:23:19'),
(93,'image','WhatsApp Image 2026-08-08 at 6.33.59 PM.jpeg',NULL,'shops/kyc/vvSF8CK2bamDuHU5OmsIfdDQA1XxUOe4KCDGevnE.jpg',NULL,'2026-08-15 21:23:19','2026-08-15 21:23:19'),
(94,'image','WhatsApp Image 2026-08-08 at 7.34.34 PM (1).jpeg',NULL,'shops/kyc/o9kcnthmDXg9uGO5pLNPJU8txSGufbPUnq2EfDrF.jpg',NULL,'2026-08-15 21:23:19','2026-08-15 21:23:19'),
(95,'image','logo.png',NULL,'users/profile/BpthUfoIN98ZyC91bjQEroQxib91hV3dYXFmS23j.png',NULL,'2026-08-15 21:47:04','2026-08-15 21:47:04'),
(96,'image','WhatsApp Image 2026-08-15 at 9.09.17 PM.jpeg',NULL,'shops/logo/g0XxwnRm5LfZJ8K3KA8ZkgCRF1lWopGp4P0a0eer.jpg',NULL,'2026-08-15 21:47:05','2026-08-15 21:47:05'),
(97,'image','bottom.png',NULL,'shops/banner/Dk7ueRVDXkm4j7Ex2oXLWENfvobC5fIv3zQV85qT.png',NULL,'2026-08-15 21:47:05','2026-08-15 21:47:05'),
(98,'image','WhatsApp Image 2026-08-15 at 9.07.18 PM (1).jpeg',NULL,'shops/kyc/3HiKoYuAFVJbbMey14IP6gZoKGcLf9EVyq0DFxdn.jpg',NULL,'2026-08-15 21:47:05','2026-08-15 21:47:05'),
(99,'image','WhatsApp Image 2026-08-15 at 9.07.18 PM.jpeg',NULL,'shops/kyc/qWdB43o65CBslJoh1U7d3KBJG0E9s2DG5DuJpjok.jpg',NULL,'2026-08-15 21:47:05','2026-08-15 21:47:05'),
(100,'image','WhatsApp Image 2026-08-15 at 9.07.18 PM (3).jpeg',NULL,'shops/kyc/FOxs0K8Msaplut2nhxNYSTOWfg1HcSxtJPqRPLjE.jpg',NULL,'2026-08-15 21:47:05','2026-08-15 21:47:05'),
(101,'image','logo.png',NULL,'users/profile/q31DDaTnVuAzeSEyXzSBNurTrcnrtNe858TLzppM.png',NULL,'2026-08-15 22:05:49','2026-08-15 22:05:49'),
(102,'image','ajay ojha.jpg',NULL,'shops/logo/k09LxHnur1S9eCwnk8NLZBuYXXddPA5zN0Xven9z.jpg',NULL,'2026-08-15 22:05:49','2026-08-15 22:05:49'),
(103,'image','bottom.png',NULL,'shops/banner/osgOahSJnT7oPGgleGy5aX9VnpKVKwBaPK7r6pt7.png',NULL,'2026-08-15 22:05:49','2026-08-15 22:05:49'),
(104,'image','WhatsApp Image 2026-08-15 at 9.28.35 PM.jpeg',NULL,'shops/kyc/rfezK4voFdCvrd4KZcldzQicmbA4r07atSVimlE0.jpg',NULL,'2026-08-15 22:05:49','2026-08-15 22:05:49'),
(105,'image','WhatsApp Image 2026-08-15 at 9.28.34 PM.jpeg',NULL,'shops/kyc/r7wiEBC6X9g92p9CxW06yp2aAFkUF3po7gV752FQ.jpg',NULL,'2026-08-15 22:05:49','2026-08-15 22:05:49'),
(106,'image','logo.png',NULL,'users/profile/wdlU6ABFzYVP6JXi14T7tIO17dOS03QCi07RKcym.png',NULL,'2026-08-17 20:14:20','2026-08-17 20:14:20'),
(107,'image','WhatsApp Image 2022-01-01 at 1.05.47 PM.jpeg',NULL,'shops/logo/H32k1ic2Aq3wX61uqH5GyyEvrgs1AmSvr0r2AGrw.jpg',NULL,'2026-08-17 20:14:20','2026-08-17 20:14:20'),
(108,'image','bottom.png',NULL,'shops/banner/s8SyKHiwkFWA2BsIZwqVVe6ARddVciV2dTRRvY1U.png',NULL,'2026-08-17 20:14:20','2026-08-17 20:14:20'),
(109,'image','WhatsApp Image 2022-01-01 at 1.04.47 PM.jpeg',NULL,'shops/kyc/rMm9ZKoUHKTG4j5bSO4BihMAEBxDbHXvPTg2CmVQ.jpg',NULL,'2026-08-17 20:14:20','2026-08-17 20:14:20'),
(110,'image','WhatsApp Image 2022-01-01 at 1.04.48 PM (1).jpeg',NULL,'shops/kyc/9bor19Qa8FkRkwH2F0V9GrxqmEWnMLWTQIN1t5fN.jpg',NULL,'2026-08-17 20:14:20','2026-08-17 20:14:20');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `ar_name` varchar(191) DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `original_name` varchar(191) DEFAULT NULL,
  `original_url` varchar(191) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `target` varchar(191) NOT NULL DEFAULT '_self',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_external` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES
(1,'Home','الرئيسية','/','Home','Home','/',1,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(2,'Products','المنتجات','/products','Products','Products','/products',2,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(3,'Digital Products','المنتجات الرقمية','/digital-products','Digital Products','Digital Products','/digital-products',3,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(4,'Shops','المتاجر','/shops','Shops','Shops','/shops',4,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(5,'Most Popular','الاكثر شعبية','/most-popular','Most Popular','Most Popular','/most-popular',5,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(6,'Best Deal','أفضل العروض','/best-deal','Best Deal','Best Deal','/best-deal',6,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(7,'Contact','اتصل بنا','/contact-us','Contact','Contact','/contact-us',7,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(8,'Blogs','المدونات','/blogs','Blogs','Blogs','/blogs',8,'_self',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2019_08_19_000000_create_failed_jobs_table',1),
(2,'2019_12_14_000001_create_personal_access_tokens_table',1),
(3,'2024_01_16_000214_create_permission_tables',1),
(4,'2024_01_16_000324_create_media_table',1),
(5,'2024_01_16_000454_create_users_table',1),
(6,'2024_01_16_104638_create_customers_table',1),
(7,'2024_01_16_105000_create_categories_table',1),
(8,'2024_01_16_105234_create_shops_table',1),
(9,'2024_01_16_105314_create_brands_table',1),
(10,'2024_01_16_105346_create_products_table',1),
(11,'2024_01_16_110908_create_banners_table',1),
(12,'2024_01_16_111152_create_reviews_table',1),
(13,'2024_01_16_112138_create_addresses_table',1),
(14,'2024_01_16_112545_create_coupons_table',1),
(15,'2024_01_16_115937_create_shop_categories_table',1),
(16,'2024_01_17_002344_create_colors_table',1),
(17,'2024_01_17_072702_create_product_colors_table',1),
(18,'2024_01_17_073458_create_sizes_table',1),
(19,'2024_01_17_073725_create_units_table',1),
(20,'2024_01_17_074135_create_product_units_table',1),
(21,'2024_01_17_074229_create_product_sizes_table',1),
(22,'2024_01_17_074745_create_verify_otps_table',1),
(23,'2024_01_17_075503_create_product_thumbnails_table',1),
(24,'2024_01_17_080646_create_product_categories_table',1),
(25,'2024_01_17_092300_create_favorites_table',1),
(26,'2024_01_17_112948_create_orders_table',1),
(27,'2024_01_17_114308_create_order_products_table',1),
(28,'2024_01_23_045138_create_legal_pages_table',1),
(29,'2024_01_23_045412_create_supports_table',1),
(30,'2024_01_27_072753_create_generate_settings_table',1),
(31,'2024_02_29_111443_add_is_approve_column_to_products_table',1),
(32,'2024_03_05_113904_add_unit_id_to_products_table',1),
(33,'2024_03_06_101936_add_price_column_to_product_sizes_table',1),
(34,'2024_03_10_110137_add_currency_column_to_generale_setting_table',1),
(35,'2024_03_12_040056_add_code_to_products_table',1),
(36,'2024_03_16_075329_add_order_id_to_reviews_table',1),
(37,'2024_03_17_063030_change_shop_id_to_coupons_table',1),
(38,'2024_03_17_100252_create_coupon_collects_table',1),
(39,'2024_03_18_082804_add_footer_column_to_generale_settings_table',1),
(40,'2024_03_20_031250_add_column_to_coupons_table',1),
(41,'2024_03_20_032311_create_admin_coupons_table',1),
(42,'2024_03_23_111543_create_notifications_table',1),
(43,'2024_03_24_094629_create_payment_gateways_table',1),
(44,'2024_03_25_104034_create_payments_table',1),
(45,'2024_03_25_104350_create_order_payments_table',1),
(46,'2024_03_25_114533_create_contact_uses_table',1),
(47,'2024_03_27_164439_add_column_to_payments_table',1),
(48,'2024_03_28_095034_add_column_to_generate_settings_table',1),
(49,'2024_03_30_092829_create_wallets_table',1),
(50,'2024_03_30_093648_create_transactions_table',1),
(51,'2024_03_30_094238_create_withdraws_table',1),
(52,'2024_03_30_104019_add_column_to_orders_table',1),
(53,'2024_03_31_083538_add_column_to_notifications_table',1),
(54,'2024_04_19_173548_add_shop_type_column_to_generate_settings_table',1),
(55,'2024_04_21_121936_create_carts_table',1),
(56,'2024_04_28_104356_create_drivers_table',1),
(57,'2024_04_29_104509_create_drivers_orders_table',1),
(58,'2024_04_29_113150_add_vehicle_type_column_to_users_table',1),
(59,'2024_04_29_164336_create_device_keys_table',1),
(60,'2024_04_30_114459_add_cash_collect_to_driver_order_table',1),
(61,'2024_04_30_121054_add_total_cash_collect_to_drivers_table',1),
(62,'2024_05_14_154411_create_recent_views_table',1),
(63,'2024_05_15_111324_create_flash_sales_table',1),
(64,'2024_05_15_115849_create_flash_sale_products_table',1),
(65,'2024_05_16_112807_add_column_to_supports_table',1),
(66,'2024_05_16_116825_add_column_to_flash_sale_products_table',1),
(67,'2024_05_18_111152_add_off_day_to_shops_table',1),
(68,'2024_05_18_163651_add_column_to_generate_settings_table',1),
(69,'2024_05_19_122837_add_column_to_generate_settings_table',1),
(70,'2024_05_20_163704_create_ads_table',1),
(71,'2024_05_21_155258_create_delivery_charges_table',1),
(72,'2024_05_21_191749_create_social_links_table',1),
(73,'2024_05_26_131755_create_support_tickets_table',1),
(74,'2024_05_26_131930_create_support_ticket_messages_table',1),
(75,'2024_05_26_135940_create_support_ticket_attachment_table',1),
(76,'2024_05_28_103844_create_ticket_issue_types_table',1),
(77,'2024_05_30_182019_add_column_to_generate_settings_table',1),
(78,'2024_06_05_165137_add_column_to_media_table',1),
(79,'2024_06_08_154510_create_galleries_table',1),
(80,'2024_06_09_103825_create_sub_categories_table',1),
(81,'2024_06_09_110839_create_category_subcategories_table',1),
(82,'2024_06_09_132729_create_product_subcategorys_table',1),
(83,'2024_06_22_095856_create_s_m_s_configs_table',1),
(84,'2024_06_23_154709_add_column_to_generate_settings_table',1),
(85,'2024_06_24_133734_add_column_to_carts_table',1),
(86,'2024_06_25_110336_add_column_to_generate_settings_table',1),
(87,'2024_06_25_114728_add_column_to_reviews_table',1),
(88,'2024_06_27_112507_add_column_to_categories_table',1),
(89,'2024_06_29_104005_add_column_to_categories_table',1),
(90,'2024_06_29_104453_add_column_to_sub_categories_table',1),
(91,'2024_06_29_114916_add_column_to_brands_table',1),
(92,'2024_06_29_114927_add_column_to_colors_table',1),
(93,'2024_06_29_114939_add_column_to_sizes_table',1),
(94,'2024_06_29_115100_add_column_to_products_table',1),
(95,'2024_07_01_103743_create_languages_table',1),
(96,'2024_07_01_110802_add_softdelete_to_users_table',1),
(97,'2024_07_03_092913_create_pos_carts_table',1),
(98,'2024_07_03_101851_create_pos_cart_products_table',1),
(99,'2024_07_03_103622_add_column_to_orders_table',1),
(100,'2024_07_04_170256_update_customer_id_column_to_orders_table',1),
(101,'2024_07_07_124525_add_draft_column_to_pos_carts_table',1),
(102,'2024_07_09_142655_add_column_to_pos_carts_table',1),
(103,'2024_09_15_093610_create_theme_colors_table',1),
(104,'2024_09_15_113123_add_clumns_to_users_table',1),
(105,'2024_09_17_101004_add_clumn_to_products_table',1),
(106,'2024_09_23_115749_add_column_to_users_table',1),
(107,'2024_09_24_101919_add_column_to_roles_table',1),
(108,'2024_09_24_105224_create_user_non_permissions_table',1),
(109,'2024_09_26_114754_create_verify_manages_table',1),
(110,'2024_09_30_135048_change_column_to_flash_sales_table',1),
(111,'2024_09_30_164906_add_column_to_flash_sale_products_table',1),
(112,'2024_10_02_125419_add_column_to_flash_sales_table',1),
(113,'2024_10_05_135418_add_column_to_flash_sale_products_table',1),
(114,'2024_10_08_113905_add_column_to_order_products_table',1),
(115,'2024_10_10_155932_create_google_re_captchas_table',1),
(116,'2024_10_14_160335_create_social_auths_table',1),
(117,'2024_10_14_170543_add_columns_to_users_table',1),
(118,'2024_10_19_104732_add_columns_to_product_colors_table',1),
(119,'2024_11_24_124258_create_vat_taxes_table',1),
(120,'2024_11_24_165734_create_product_vat_taxes_table',1),
(121,'2024_11_25_110112_add_column_to_orders_table',1),
(122,'2024_11_28_170135_create_blogs_table',1),
(123,'2024_11_28_171145_create_tags_table',1),
(124,'2024_11_28_171247_create_blog_tags_table',1),
(125,'2024_11_28_171333_create_blog_views_table',1),
(126,'2025_01_06_145615_add_column_to_carts_table',1),
(127,'2025_01_07_113201_add_column_to_products_table',1),
(128,'2025_01_07_115807_add_column_to_languages_table',1),
(129,'2025_01_26_100341_create_translate_utilities_table',1),
(130,'2025_01_26_103530_create_currencies_table',1),
(131,'2025_01_26_112926_change_password_column_to_users_table',1),
(132,'2025_01_26_113220_create_product_translations_table',1),
(133,'2025_01_26_155516_add_currency_id_column_to_generate_settings_table',1),
(134,'2025_01_28_174131_add_column_to_verify_manages_table',1),
(135,'2025_02_03_111244_change_column_to_shops_table',1),
(136,'2025_02_13_111656_create_menus_table',1),
(137,'2025_02_16_100246_create_pages_table',1),
(138,'2025_02_17_101904_add_column_to_verify_manages_table',1),
(139,'2025_02_17_102712_create_countries_table',1),
(140,'2025_02_18_160537_create_order_vat_taxes_table',1),
(141,'2025_02_22_111431_add_column_to_generate_settings_table',1),
(142,'2025_02_22_152711_create_footers_table',1),
(143,'2025_02_22_154855_create_footer_items_table',1),
(144,'2025_04_17_105545_create_subscription_plans_table',1),
(145,'2025_04_17_105627_create_shop_subscriptions_table',1),
(146,'2025_05_04_111353_create_shop_user_table',1),
(147,'2025_05_04_111844_create_shop_user_chats_table',1),
(148,'2025_05_06_150402_add_column_to_generate_settings_table',1),
(149,'2025_05_06_174546_add_last_online_column_to_users_table',1),
(150,'2025_05_06_183511_add_last_online_column_to_shops_table',1),
(151,'2025_07_02_165205_create_paypal_payments_table',1),
(152,'2025_07_14_153807_add_column_to_social_links_table',1),
(153,'2025_07_16_142728_add_column_to_pos_carts_table',1),
(154,'2025_08_11_180401_add_new_coloum_to_generate_settings',1),
(155,'2025_08_18_165743_add_new_coloum_to_generate_settings',1),
(156,'2025_08_27_152721_create_return_orders_table',1),
(157,'2025_08_27_153102_create_return_order_details_table',1),
(158,'2025_08_28_124115_add_new_coloum_to_generate_settings_table',1),
(159,'2025_09_25_180903_create_product_attachments_table',1),
(160,'2025_09_25_181339_add_column_to_products_table',1),
(161,'2025_09_28_124136_create_product_licenses_table',1),
(162,'2025_09_29_130047_add_column_to_product_licenses_table',1),
(163,'2025_10_01_112743_add_column_to_product_licenses_table',1),
(164,'2025_11_02_153428_create_module_settings_table',1),
(165,'2025_11_25_103004_create_cart_access_tokens_table',1),
(166,'2025_12_15_163627_add_softdelete_to_drivers_table',1),
(167,'2025_12_15_163653_add_softdelete_to_shops_table',1),
(168,'2025_12_25_103350_add_coloums_to_order_products_table',1),
(169,'2025_12_27_062612_add_index_to_products_table',1),
(170,'2025_12_29_154611_create_driver_locations_table',1),
(171,'2026_01_04_112219_create_areas_table',1),
(172,'2026_01_04_131609_add_area_to_addresses_table',1),
(173,'2026_01_04_152627_add_area_to_orders_table',1),
(174,'2026_01_26_134407_add_soft_delete_to_products_table',1),
(175,'2026_01_29_150919_add_ar_to_menus_table',1),
(176,'2026_01_29_154737_add_titlear_to_footers_table',1),
(177,'2026_01_29_154810_add_titlear_to_footer_items_table',1),
(178,'2026_07_27_000001_create_warehouses_tables',1),
(179,'2026_07_30_212249_drop_shop_id_from_warehouses_table',1),
(180,'2026_08_01_000001_add_parent_shop_id_to_shops_table',2),
(181,'2026_08_01_000002_create_shop_monthly_payouts_table',2),
(182,'2026_08_02_000000_drop_subscription_tables',3),
(183,'2026_08_03_233146_create_shop_kyc_table',4),
(184,'2026_08_05_add_shop_allocation_radius_to_generate_settings_table',4),
(185,'2026_08_05_211757_update_default_app_name_to_janmitram',5),
(186,'2026_08_08_000001_add_is_default_to_vat_taxes_table',6),
(187,'2026_08_10_000001_create_cards_and_card_discount_fields',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES
(1,'App\\Models\\User',1),
(3,'App\\Models\\User',1),
(3,'App\\Models\\User',3),
(3,'App\\Models\\User',4),
(3,'App\\Models\\User',5),
(3,'App\\Models\\User',6),
(3,'App\\Models\\User',7),
(3,'App\\Models\\User',8),
(3,'App\\Models\\User',9),
(4,'App\\Models\\User',10),
(3,'App\\Models\\User',11),
(3,'App\\Models\\User',12),
(3,'App\\Models\\User',13),
(3,'App\\Models\\User',14),
(3,'App\\Models\\User',15);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `module_settings`
--

DROP TABLE IF EXISTS `module_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_settings` (
  `name` varchar(191) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `is_first` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_settings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `module_settings` WRITE;
/*!40000 ALTER TABLE `module_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `url` varchar(191) DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `withdraw_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_shop_id_foreign` (`shop_id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  KEY `notifications_withdraw_id_foreign` (`withdraw_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,'Order status updated','Your order status updated to Cancelled',NULL,NULL,'order',NULL,6,0,'2026-08-11 21:46:36','2026-08-11 21:46:36',NULL),
(2,'Payment status updated','Your payment status updated to paid. order code: RC000007',NULL,NULL,'order',NULL,9,0,'2026-08-13 19:48:33','2026-08-13 19:48:33',NULL),
(3,'Payment status updated','Your payment status updated to paid. order code: RC000005',NULL,NULL,'order',NULL,9,0,'2026-08-14 21:04:10','2026-08-14 21:04:10',NULL),
(4,'Order status updated','Your order status updated to Confirm',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:11:53','2026-08-16 18:11:53',NULL),
(5,'Order status updated','Your order status updated to Processing',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:12:11','2026-08-16 18:12:11',NULL),
(6,'Order status updated','Your order status updated to Pickup',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:12:27','2026-08-16 18:12:27',NULL),
(7,'Order status updated','Your order status updated to On The Way',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:12:35','2026-08-16 18:12:35',NULL),
(8,'Order status updated','Your order status updated to Delivered',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:12:53','2026-08-16 18:12:53',NULL),
(9,'Payment status updated','Your payment status updated to paid. order code: RC000009',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:12:56','2026-08-16 18:12:56',NULL),
(10,'Order status updated','Your order status updated to Confirm',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:18:55','2026-08-16 18:18:55',NULL),
(11,'Order status updated','Your order status updated to Processing',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:19:01','2026-08-16 18:19:01',NULL),
(12,'Order status updated','Your order status updated to Pickup',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:19:06','2026-08-16 18:19:06',NULL),
(13,'Order status updated','Your order status updated to On The Way',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:19:10','2026-08-16 18:19:10',NULL),
(14,'Order status updated','Your order status updated to Delivered',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:19:22','2026-08-16 18:19:22',NULL),
(15,'Payment status updated','Your payment status updated to paid. order code: RC000008',NULL,NULL,'order',NULL,10,0,'2026-08-16 18:19:28','2026-08-16 18:19:28',NULL),
(16,'Order status updated','Your order status updated to Delivered',NULL,NULL,'order',NULL,3,0,'2026-08-16 20:19:26','2026-08-16 20:19:26',NULL),
(17,'Payment status updated','Your payment status updated to paid. order code: RC000010',NULL,NULL,'order',NULL,3,0,'2026-08-16 20:19:29','2026-08-16 20:19:29',NULL),
(18,'Order status updated','Your order status updated to Delivered',NULL,NULL,'order',NULL,12,0,'2026-08-16 21:56:25','2026-08-16 21:56:25',NULL),
(19,'Payment status updated','Your payment status updated to paid. order code: RC000011',NULL,NULL,'order',NULL,12,0,'2026-08-16 21:56:29','2026-08-16 21:56:29',NULL),
(20,'Order status updated','Your order status updated to Delivered',NULL,NULL,'order',NULL,12,0,'2026-08-17 19:08:56','2026-08-17 19:08:56',NULL),
(21,'Payment status updated','Your payment status updated to paid. order code: RC000012',NULL,NULL,'order',NULL,12,0,'2026-08-17 19:09:01','2026-08-17 19:09:01',NULL);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `order_payments`
--

DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments` (
  `order_id` bigint(20) unsigned NOT NULL,
  `payment_id` bigint(20) unsigned NOT NULL,
  KEY `order_payments_order_id_foreign` (`order_id`),
  KEY `order_payments_payment_id_foreign` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_payments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `order_payments` WRITE;
/*!40000 ALTER TABLE `order_payments` DISABLE KEYS */;
INSERT INTO `order_payments` VALUES
(2,1),
(3,2),
(5,3),
(6,4),
(7,5),
(8,6),
(9,7),
(10,8),
(11,9),
(12,10);
/*!40000 ALTER TABLE `order_payments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `color` varchar(191) DEFAULT NULL,
  `size` varchar(191) DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `buying_price` double DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_products_product_id_foreign` (`product_id`),
  KEY `order_products_order_id_product_id_index` (`order_id`,`product_id`),
  KEY `order_products_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_products`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `order_products` WRITE;
/*!40000 ALTER TABLE `order_products` DISABLE KEYS */;
INSERT INTO `order_products` VALUES
(1,1,2,1,'yellow','small',NULL,100,40,'2026-08-03 11:51:37','2026-08-03 11:51:37'),
(2,2,2,1,NULL,NULL,'250 GM',100,40,'2026-08-05 18:21:26','2026-08-05 18:21:26'),
(3,3,2,1,'yellow','small','250 GM',100,40,'2026-08-05 18:25:18','2026-08-05 18:25:18'),
(4,4,5,100,'DHANIYA POWDER','Small',NULL,60,50,'2026-08-06 19:13:50','2026-08-06 19:13:50'),
(5,5,4,1,NULL,NULL,NULL,72,50,'2026-08-11 21:56:14','2026-08-11 21:56:14'),
(6,6,7,1,NULL,NULL,'200GM',180,150,'2026-08-12 20:15:34','2026-08-12 20:15:34'),
(7,7,48,1,NULL,NULL,'1 KG',140,100,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(8,7,51,1,NULL,NULL,'10kg',450,400,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(9,7,49,2,NULL,NULL,'1 KG',120,100,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(10,7,50,10,NULL,NULL,'1 KG',70,50,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(11,7,55,1,NULL,NULL,'200GM',180,150,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(12,7,56,1,NULL,NULL,NULL,72,50,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(13,7,53,1,NULL,NULL,'1 Ltr',290,250,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(14,7,52,1,NULL,NULL,'200ml',150,100,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(15,7,54,6,NULL,NULL,'1 Ltr',230,200,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(16,8,1,3,NULL,NULL,'200GM',100,50,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(17,8,3,2,NULL,NULL,'200GM',60,50,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(18,8,4,2,NULL,NULL,NULL,72,50,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(19,8,7,2,NULL,NULL,'200GM',180,150,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(20,8,8,2,NULL,NULL,'1 Ltr',230,200,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(21,8,9,2,NULL,NULL,'1 Ltr',290,250,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(22,8,10,2,NULL,NULL,'200ml',150,100,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(23,8,11,2,NULL,NULL,'10kg',450,400,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(24,8,12,2,NULL,NULL,'1 KG',70,50,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(25,8,13,2,NULL,NULL,'1 KG',120,100,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(26,8,14,2,NULL,NULL,'1 KG',140,100,'2026-08-13 19:35:18','2026-08-13 19:35:18'),
(27,9,8,1,NULL,NULL,'1 Ltr',230,200,'2026-08-13 20:03:18','2026-08-13 20:03:18'),
(28,9,10,1,NULL,NULL,'200ml',150,100,'2026-08-13 20:03:18','2026-08-13 20:03:18'),
(29,10,61,1,NULL,NULL,'1 KG',120,100,'2026-08-16 20:09:54','2026-08-16 20:09:54'),
(30,10,6,1,NULL,NULL,'200GM',20,70,'2026-08-16 20:09:54','2026-08-16 20:09:54'),
(31,10,62,1,NULL,NULL,'1 KG',70,50,'2026-08-16 20:09:54','2026-08-16 20:09:54'),
(32,10,5,1,NULL,NULL,'200GM',60,50,'2026-08-16 20:09:54','2026-08-16 20:09:54'),
(33,11,74,1,NULL,NULL,'200GM',100,50,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(34,11,72,1,NULL,NULL,'200GM',60,50,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(35,11,75,1,NULL,NULL,NULL,72,50,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(36,11,73,2,NULL,NULL,'200GM',180,150,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(37,11,78,6,NULL,NULL,'1 Ltr',230,200,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(38,11,76,1,NULL,NULL,'1 Ltr',290,250,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(39,11,77,1,NULL,NULL,'200ml',150,100,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(40,11,69,2,NULL,NULL,'10kg',450,400,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(41,11,71,2,NULL,NULL,'1 KG',70,50,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(42,11,70,1,NULL,NULL,'1 KG',120,100,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(43,11,68,1,NULL,NULL,'1 KG',140,100,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(44,12,69,1,NULL,NULL,'10kg',450,400,'2026-08-16 22:03:27','2026-08-16 22:03:27'),
(45,13,1,5,'HALDI POWDER','Small',NULL,100,50,'2026-08-17 19:06:05','2026-08-17 19:06:05'),
(46,13,3,6,'DHANIYA POWDER','Small',NULL,60,50,'2026-08-17 19:06:05','2026-08-17 19:06:05');
/*!40000 ALTER TABLE `order_products` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `order_vat_taxes`
--

DROP TABLE IF EXISTS `order_vat_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_vat_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `percentage` double NOT NULL DEFAULT 0,
  `amount` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_vat_taxes_order_id_foreign` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_vat_taxes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `order_vat_taxes` WRITE;
/*!40000 ALTER TABLE `order_vat_taxes` DISABLE KEYS */;
INSERT INTO `order_vat_taxes` VALUES
(1,4,'GST',5,300,'2026-08-06 19:13:50','2026-08-06 19:13:50'),
(2,13,'GST',5,43,'2026-08-17 19:06:05','2026-08-17 19:06:05');
/*!40000 ALTER TABLE `order_vat_taxes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `pos_order` tinyint(1) NOT NULL DEFAULT 0,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `order_code` varchar(191) NOT NULL,
  `prefix` varchar(191) DEFAULT NULL,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_discount` double DEFAULT NULL,
  `pick_date` timestamp NULL DEFAULT NULL,
  `delivery_date` timestamp NULL DEFAULT NULL,
  `payable_amount` double NOT NULL,
  `total_amount` double NOT NULL,
  `tax_amount` double DEFAULT 0,
  `discount` double DEFAULT 0,
  `delivery_charge` double NOT NULL DEFAULT 0,
  `payment_status` varchar(191) NOT NULL,
  `order_status` varchar(191) NOT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `instruction` longtext DEFAULT NULL,
  `invoice_path` varchar(191) DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `admin_commission` double DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_area` varchar(191) DEFAULT NULL,
  `card_id` bigint(20) unsigned DEFAULT NULL,
  `card_discount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_shop_id_foreign` (`shop_id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  KEY `orders_address_id_foreign` (`address_id`),
  KEY `orders_card_id_foreign` (`card_id`),
  CONSTRAINT `orders_card_id_foreign` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(1,2,1,NULL,'000002','RC',NULL,0,NULL,NULL,100,100,0,0,0,'Paid','Delivered','Cash Payment',NULL,NULL,NULL,NULL,0,'2026-08-03 11:51:37','2026-08-03 11:51:37',NULL,NULL,NULL),
(2,2,0,1,'000002','RC',NULL,0,NULL,NULL,100,100,0,0,0,'Pending','Pending','Cash Payment',1,NULL,NULL,NULL,0,'2026-08-05 18:21:26','2026-08-05 18:21:26',NULL,NULL,NULL),
(3,2,0,1,'000003','RC',NULL,0,NULL,NULL,100,100,0,0,0,'Pending','Cancelled','Cash Payment',2,NULL,NULL,NULL,0,'2026-08-05 18:25:18','2026-08-11 21:46:36',NULL,NULL,NULL),
(4,4,1,NULL,'000004','RC',NULL,0,NULL,NULL,6300,6000,300,0,0,'Paid','Delivered','Cash Payment',NULL,NULL,NULL,NULL,0,'2026-08-06 19:13:50','2026-08-06 19:13:50',NULL,NULL,NULL),
(5,1,0,4,'000005','RC',NULL,0,NULL,NULL,102,72,0,0,30,'Paid','Cancelled','Cash Payment',3,NULL,NULL,NULL,0,'2026-08-11 21:56:14','2026-08-14 21:04:10','JAGAT PURA JAIPUR',NULL,0.00),
(6,1,0,4,'000006','RC',NULL,0,NULL,NULL,210,180,0,0,30,'Pending','Cancelled','Cash Payment',3,NULL,NULL,NULL,0,'2026-08-12 20:15:34','2026-08-14 21:05:22','JAGAT PURA JAIPUR',NULL,0.00),
(7,6,0,4,'000007','RC',NULL,0,NULL,NULL,3632,3602,0,0,30,'Paid','Cancelled','Cash Payment',3,NULL,NULL,NULL,0,'2026-08-13 19:00:11','2026-08-13 19:48:33','JAGAT PURA JAIPUR',NULL,0.00),
(8,1,0,5,'000008','RC',NULL,0,NULL,NULL,3844,3824,0,0,20,'Paid','Delivered','Cash Payment',4,NULL,NULL,NULL,0,'2026-08-13 19:35:18','2026-08-16 18:19:28','VAISHALI NAGAR JAIPUR',NULL,0.00),
(9,1,0,5,'000009','RC',NULL,0,NULL,NULL,400,380,0,0,20,'Paid','Delivered','Cash Payment',4,NULL,NULL,NULL,0,'2026-08-13 20:03:18','2026-08-16 18:12:56','VAISHALI NAGAR JAIPUR',NULL,0.00),
(10,4,0,9,'000010','RC',NULL,0,NULL,NULL,290,270,0,0,20,'Paid','Delivered','Cash Payment',5,NULL,NULL,NULL,0,'2026-08-16 20:09:54','2026-08-16 20:19:29','SANGANER JAIPUR',NULL,0.00),
(11,9,0,11,'000011','RC',NULL,0,NULL,NULL,3365.8,3712,0,0,25,'Paid','Delivered','Cash Payment',6,NULL,NULL,NULL,0,'2026-08-16 21:44:54','2026-08-16 21:56:29','VIDYADHAR NAGAR JAIPUR',4,371.20),
(12,9,0,11,'000012','RC',NULL,0,NULL,NULL,475,450,0,0,25,'Paid','Delivered','Cash Payment',6,NULL,NULL,NULL,0,'2026-08-16 22:03:27','2026-08-17 19:09:01','VIDYADHAR NAGAR JAIPUR',4,0.00),
(13,1,1,2,'000006','RC',NULL,0,NULL,NULL,903,860,43,0,0,'Paid','Delivered','Cash Payment',NULL,NULL,NULL,NULL,0,'2026-08-17 19:06:05','2026-08-17 19:06:05',NULL,NULL,0.00);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `url` varchar(191) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_editable` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES
(1,'Products','products','products',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(2,'Shops','shops','shops',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(3,'Most Popular','most-popular','most-popular',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(4,'Digital Products','digital-products','digital-products',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(5,'Best Deal','best-deal','best-deal',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(6,'Contact','contact-us','contact-us',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(7,'Blogs','blogs','blogs',NULL,1,0,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(8,'About Us','about-us','about-us','<html><head><title>Non et.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">non</label><input type=\"text\" id=\"username\"><label for=\"password\">sed</label><input type=\"password\" id=\"password\"></form><div class=\"alias\"></div><div id=\"15977\"><div id=\"33948\"></div><div id=\"11749\"></div><div class=\"magnam\"></div></div><div class=\"quia\"></div></body></html>\n',1,1,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(9,'Privacy Policy','privacy-policy','privacy-policy','<html><head><title>Enim numquam necessitatibus aut id modi sint molestias.</title></head><body><form action=\"example.com\" method=\"POST\"><label for=\"username\">ducimus</label><input type=\"text\" id=\"username\"><label for=\"password\">repellat</label><input type=\"password\" id=\"password\"></form><div class=\"tenetur\"><div class=\"sunt\"></div><div id=\"36028\"></div><div class=\"beatae\"><span>Amet ea dolores ratione fugiat ipsam dicta.</span>Modi corrupti totam modi placeat consequatur consequatur doloribus tenetur delectus quis.<ul><li>Velit voluptas.</li><li>Tenetur aut laborum.</li><li>Eum voluptates vel quis.</li><li>Consequatur eum soluta consequuntur voluptas.</li><li>Nam.</li><li>Delectus ut in.</li><li>Sit quia provident quia.</li><li>Quasi et aliquam.</li><li>Cum dolor.</li><li>Pariatur aliquid quo exercitationem explicabo.</li></ul><a href=\"example.com\">Id in facilis consequatur sequi ducimus.</a><a href=\"example.org\">Similique vel ab voluptatem rerum facilis.</a></div><div class=\"at\"></div><div class=\"animi\"></div><div id=\"77959\"></div></div><div id=\"6206\"><div id=\"70085\"><span>Temporibus et sequi.</span><table><thead><tr><th>Placeat aliquam rerum.</th><th>Cumque blanditiis dolorem.</th></tr></thead><tbody><tr><td>Fugiat.</td><td>Sed asperiores voluptatibus provident magni adipisci id aperiam omnis est.</td></tr><tr><td>Ducimus non unde maxime doloribus unde quis quisquam ut.</td><td>Distinctio earum facilis molestiae dolorum iure nesciunt aut aut ipsa.</td></tr><tr><td>Culpa eum necessitatibus recusandae.</td><td>Perferendis qui aperiam.</td></tr><tr><td>Aut sed et qui laudantium.</td><td>Deserunt ex iusto ducimus eligendi quis.</td></tr><tr><td>Voluptates earum ut sed nulla rerum delectus.</td><td>Qui sit laborum ut est.</td></tr><tr><td>Tempora hic.</td><td>Optio vitae tempore corrupti vitae quos omnis omnis illum.</td></tr><tr><td>Blanditiis delectus atque ratione quia ad quibusdam eum ducimus.</td><td>Quia eius impedit accusantium quam aperiam provident atque.</td></tr><tr><td>Consequatur minima.</td><td>Doloribus non.</td></tr><tr><td>Accusamus nesciunt minima sint unde qui at.</td><td>Eos iste.</td></tr></tbody></table><h3>Inventore.</h3><ul><li>Iure delectus fugit non.</li><li>Placeat ipsam itaque sint.</li></ul></div></div><div id=\"82514\"><span>Cum.</span><i>Quam voluptatem odio voluptatum aut est.</i><table><thead><tr><th>Quam dignissimos sint ipsa est.</th><th>Aut dolor aut.</th><th>Sit reprehenderit reiciendis.</th><th>Molestiae debitis.</th><th>Veritatis sequi rerum.</th><th>Reprehenderit beatae inventore.</th></tr></thead><tbody><tr><td>Sed blanditiis enim tempora.</td><td>Qui est eaque sapiente eius aspernatur voluptate qui dolorem.</td><td>Optio facilis quisquam et error nam sint.</td><td>Repellat nisi tempore.</td><td>Temporibus occaecati nobis alias labore.</td><td>Cupiditate minima laborum in omnis ipsum ea quos non.</td></tr><tr><td>Et aspernatur reprehenderit laborum odio sed voluptatibus illo dolor sequi.</td><td>Qui ut dolorem dolor odit eos soluta.</td><td>Dignissimos quam voluptatem aut dolores assumenda porro dolores qui illo enim fugiat molestiae.</td><td>Qui natus ducimus dignissimos.</td><td>Accusamus.</td><td>Quae libero est maxime corporis explicabo in iusto laudantium.</td></tr><tr><td>Voluptatibus itaque non aperiam quo laborum.</td><td>Non velit.</td><td>Pariatur.</td><td>Provident.</td><td>Expedita consequuntur reprehenderit enim et delectus fugiat dolores.</td><td>Nihil quas nam architecto et laudantium nihil.</td></tr><tr><td>Corporis nihil.</td><td>Occaecati.</td><td>Rem nisi quis vel provident rem ratione quis cum aut.</td><td>Nisi et vitae assumenda minus at at ad voluptate.</td><td>Dolor blanditiis.</td><td>Reiciendis.</td></tr><tr><td>Aspernatur corporis occaecati voluptatem architecto necessitatibus ea vitae.</td><td>Vitae aliquid natus non nam vel ut sapiente veniam quia qui quod.</td><td>Quia eos ullam reprehenderit.</td><td>Enim est culpa.</td><td>Rerum est temporibus deserunt officia unde laboriosam.</td><td>Perspiciatis et suscipit.</td></tr></tbody></table><span>Odio reprehenderit.</span><i>Velit ea quia sit.</i><ul><li>Blanditiis qui.</li><li>Deleniti quis ipsam.</li><li>Incidunt possimus quibusdam et nemo.</li><li>Ut.</li></ul><span>Et consequatur eum aut sit molestiae itaque quia.</span><span>Pariatur cumque unde est et nihil quia eaque officiis id.</span></div><div class=\"aut\"></div><div id=\"81095\"><h1>Voluptas itaque reprehenderit deleniti voluptates aspernatur cumque.</h1><h3>Minima nam laborum molestias sit voluptas expedita.</h3><table><thead><tr><th>Ipsa odio et.</th><th>Et harum vitae.</th><th>Perspiciatis et.</th><th>Et et pariatur corrupti.</th></tr></thead><tbody><tr><td>Sit ad saepe accusantium.</td><td>Ipsam sit.</td><td>Nihil nemo atque totam ut consequuntur repellendus illo sint ut.</td><td>Eos at voluptas.</td></tr><tr><td>Sed.</td><td>Ipsum quis nihil et est aliquam explicabo et voluptates non maiores.</td><td>Aut cupiditate dolor suscipit placeat delectus.</td><td>Temporibus sapiente qui facilis omnis.</td></tr></tbody></table>Architecto est vero facere a voluptas.<a href=\"example.com\">Amet velit cupiditate consequatur accusamus et.</a>Aspernatur earum.</div><div id=\"44039\"></div><div class=\"dolor\"><ul><li>Ullam aut quos.</li></ul><ul><li>Maiores quae iure molestiae.</li><li>Eius voluptatum alias.</li><li>Voluptate consequuntur.</li><li>Sit excepturi laudantium sit.</li><li>Rerum.</li><li>Nobis beatae quos.</li><li>Iusto dolor rerum numquam.</li><li>Ipsum voluptatem quis.</li></ul><a href=\"example.com\">Sed fuga fugiat.</a>Est neque est et quas est eveniet nostrum consectetur quos.Ullam a laboriosam nulla eum sit ut quas et.</div><div class=\"qui\"></div><div id=\"61264\"><div id=\"48430\"><b>Magnam.</b><h2>Et similique sequi quo porro et.</h2><a href=\"example.org\">Consequatur qui omnis voluptate voluptatum.</a><h2>Voluptas qui.</h2>Molestiae quia.Est.</div><div class=\"asperiores\"><i>Deleniti aut ab quaerat totam in adipisci est ratione.</i><p>Sit eos reprehenderit fugit fugit quos.</p><ul><li>Sit aut.</li><li>Non labore omnis.</li><li>Omnis tempore voluptas.</li><li>Est assumenda doloremque laborum.</li><li>Accusamus ea occaecati enim nam.</li></ul><ul><li>Minima laboriosam et.</li><li>Ipsum molestias.</li><li>Officiis consectetur.</li><li>Voluptas aut fugit totam voluptas eveniet.</li></ul><a href=\"example.net\">Id voluptatem provident.</a><b>Maiores occaecati fugit excepturi.</b><b>Quam libero impedit omnis sit.</b></div><div class=\"quos\"></div></div></body></html>\n',1,1,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(10,'Terms of Service','terms-and-conditions','terms-and-conditions','<html><head><title>Delectus debitis debitis sunt et.</title></head><body><form action=\"example.com\" method=\"POST\"><label for=\"username\">tempore</label><input type=\"text\" id=\"username\"><label for=\"password\">nihil</label><input type=\"password\" id=\"password\"></form><div class=\"rem\"></div><div class=\"ut\"></div><div class=\"sit\"><div id=\"68927\"></div><div class=\"enim\"></div><div id=\"64177\"></div></div><div id=\"25814\"></div><div id=\"89703\"></div><div id=\"84776\"></div></body></html>\n',1,1,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(11,'Return policy / Refund Policy','return-and-refund-policy','page/return-and-refund-policy','<html><head><title>Cupiditate itaque eius impedit in voluptatum dolores.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">eveniet</label><input type=\"text\" id=\"username\"><label for=\"password\">fugiat</label><input type=\"password\" id=\"password\"></form><div id=\"8794\"></div><div class=\"saepe\"></div><div class=\"quisquam\"></div><div id=\"34123\"><a href=\"example.org\">Est nihil accusantium distinctio numquam quod.</a><b>Aliquid et et corporis sit ut ea temporibus.</b><p>Doloremque voluptas voluptatibus sint provident.</p>Et inventore fuga quibusdam veritatis repellat.Tempora rerum.<ul><li>A quod sapiente.</li><li>Quasi est.</li><li>Omnis alias voluptates.</li><li>Non.</li><li>Nobis voluptate quia.</li><li>Et dignissimos est.</li><li>Atque sed maiores eius.</li><li>Repellendus sit dolorem.</li><li>Voluptatem aut quo delectus.</li></ul><b>Dolores alias modi omnis sunt non est.</b><table><thead><tr><th>Nesciunt adipisci corrupti ea.</th><th>Voluptate est illum cum.</th><th>Quibusdam recusandae.</th><th>Rerum.</th><th>Dolorem ea minima sint.</th><th>Et quo provident deserunt.</th></tr></thead><tbody><tr><td>Recusandae pariatur itaque neque qui labore maxime.</td><td>Veritatis voluptatibus sapiente ducimus.</td><td>Optio aut illum accusantium.</td><td>Fugiat rerum.</td><td>Vel omnis ut.</td><td>Error eveniet in est molestiae temporibus.</td></tr></tbody></table><a href=\"example.org\">Aut et dolores ipsam praesentium nam iure quae nemo enim.</a></div><div class=\"aliquid\"></div><div id=\"56083\"></div><div class=\"autem\"></div><div class=\"quia\"></div><div id=\"97933\"></div><div id=\"38853\"></div></body></html>\n',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(12,'Shipping & Delivery Policy','shipping-and-delivery-policy','page/shipping-and-delivery-policy','<html><head><title>At pariatur vel.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">non</label><input type=\"text\" id=\"username\"><label for=\"password\">non</label><input type=\"password\" id=\"password\"></form><div class=\"tempora\"></div><div id=\"16750\"><i>Impedit quam ut.</i>Impedit voluptas distinctio eius vel earum quisquam.</div><div id=\"88201\"><div id=\"85430\"></div><div id=\"57117\"></div><div class=\"rerum\"><h1>Ut sed temporibus nihil neque dolores explicabo.</h1><h3>Placeat mollitia aut vel est.</h3><h3>Atque atque assumenda.</h3><ul><li>Et illo vitae sit.</li><li>Quia maiores enim.</li><li>Ducimus esse aspernatur.</li><li>Saepe.</li><li>Eos sapiente.</li><li>Ullam sint qui.</li></ul><a href=\"example.com\">Illo sed omnis qui architecto laudantium reprehenderit praesentium dolores accusamus.</a><ul><li>Commodi sit adipisci maxime.</li><li>Aspernatur quis.</li><li>Voluptatem temporibus.</li><li>Debitis corrupti et error molestias.</li><li>Similique et voluptas voluptas molestiae.</li><li>Eligendi expedita.</li><li>Ad et.</li></ul><b>Quasi doloremque dolores quis.</b><a href=\"example.com\">Quo voluptatum facere ratione ut et eum est id corrupti quos laboriosam hic qui.</a><a href=\"example.org\">Ducimus autem perspiciatis quae et sed.</a></div><div id=\"44464\"></div><div class=\"numquam\"><ul><li>Vel.</li></ul><p>Maxime officiis eius ad excepturi.</p><ul><li>Neque consequatur.</li><li>Architecto rem quaerat.</li><li>Saepe laboriosam est.</li><li>Molestias.</li><li>Dolor nobis.</li><li>Deleniti ipsam sunt.</li><li>Quo qui facere porro.</li><li>Qui rerum.</li><li>Aut maiores at.</li></ul><i>Qui voluptatibus velit est consequuntur minima.</i><i>Facere aliquam vitae laborum minus occaecati sit deleniti expedita at ut laudantium natus ut.</i><ul><li>Eum sit quia quo.</li><li>Amet molestias recusandae.</li><li>Eius quasi.</li><li>Provident.</li><li>Molestiae incidunt minus nobis facere.</li><li>Et eos voluptas ut.</li><li>Debitis corporis eos quaerat sint.</li><li>Dignissimos dignissimos architecto.</li><li>Ullam sint rerum ipsa.</li></ul><span>Voluptas officiis.</span><b>Repudiandae iure magnam.</b>Animi ipsa voluptates provident dolorem.Architecto molestias maiores blanditiis aliquam.</div><div id=\"54109\"></div><div id=\"18732\"><ul><li>Omnis quisquam repellat.</li><li>Debitis quam inventore itaque et.</li><li>Dolor amet aliquam.</li><li>Corporis.</li><li>Est quisquam culpa.</li><li>Earum quos eos.</li><li>Quia ut.</li></ul><i>Est qui atque et ut fugit.</i>Commodi cum illum impedit et nostrum itaque qui non.<i>Voluptate eum mollitia.</i></div><div id=\"58777\"></div><div class=\"expedita\"></div><div id=\"34753\"></div></div><div id=\"29859\"></div><div class=\"quos\"><table><thead><tr><th>Eos fugiat quia.</th><th>Ut sint ea molestiae.</th><th>Sapiente corrupti.</th></tr></thead><tbody><tr><td>Ducimus voluptas et delectus illum.</td><td>Quidem doloribus aspernatur debitis ipsa.</td><td>Illo eum quo aut quia nobis qui.</td></tr><tr><td>Aut sit quo amet.</td><td>Sunt soluta nisi inventore sequi libero expedita quod ab sit autem.</td><td>Aut nulla minus.</td></tr></tbody></table><b>Architecto et cum.</b><h2>Enim hic neque consequatur expedita eaque in id.</h2><span>Ex itaque nihil est vel.</span></div><div id=\"53958\"></div><div class=\"velit\"></div><div class=\"pariatur\"></div></body></html>\n',1,1,0,'2026-08-03 08:41:09','2026-08-03 08:41:09');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `payment_gateways`
--

DROP TABLE IF EXISTS `payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `mode` varchar(191) NOT NULL DEFAULT 'test' COMMENT 'test or live',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `alias` varchar(191) DEFAULT NULL COMMENT 'controller nameSpace',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_gateways_media_id_foreign` (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateways`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `payment_gateways` WRITE;
/*!40000 ALTER TABLE `payment_gateways` DISABLE KEYS */;
INSERT INTO `payment_gateways` VALUES
(1,'Stripe','stripe',NULL,'{\"secret_key\": \"\", \"published_key\": \"\"}','test',0,'Stripe','2026-08-03 08:41:09','2026-08-11 21:44:11'),
(2,'PayPal','paypal',NULL,'{\"client_id\": \"\", \"client_secret\": \"\"}','test',0,'PayPal','2026-08-03 08:41:09','2026-08-11 21:44:15'),
(3,'Razorpay','razorpay',NULL,'{\"key\": \"\", \"secret\": \"\"}','test',1,'Razorpay','2026-08-03 08:41:09','2026-08-03 08:41:09'),
(4,'Paystack','paystack',NULL,'{\"public_key\": \"\", \"secret_key\": \"\", \"machant_email\": \"\"}','test',0,'PayStack','2026-08-03 08:41:09','2026-08-11 21:44:29'),
(5,'aamarPay','aamarpay',NULL,'{\"store_id\": \"\", \"signature_key\": \"\"}','test',0,'AamarPay','2026-08-03 08:41:09','2026-08-11 21:44:39'),
(6,'BKash','bKash',NULL,'{\"app_key\": \"\", \"password\": \"\", \"username\": \"\", \"app_secret_key\": \"\"}','test',0,'Bkash','2026-08-03 08:41:09','2026-08-11 21:44:34'),
(7,'PayTabs','paytabs',NULL,'{\"base_url\": \"https://secure-global.paytabs.com\", \"currency\": \"USD\", \"profile_id\": \"\", \"server_key\": \"\"}','test',0,'PayTabs','2026-08-03 08:41:09','2026-08-11 21:44:46'),
(8,'QiCard','qicard',NULL,'{\"currency\": \"IQD\", \"password\": \"\", \"username\": \"\", \"terminalId\": \"\"}','test',0,'QiCard','2026-08-03 08:41:09','2026-08-11 21:44:53'),
(9,'PayU','payu',NULL,'{\"base_url\": \"https://secure.payu.in/_payment\", \"merchant_key\": \"\", \"merchant_salt\": \"\"}','test',0,'PayU','2026-08-03 08:41:09','2026-08-11 21:45:00'),
(10,'CashFree','cashfree',NULL,'{\"app_id\": \"\", \"base_url\": \"https://api.cashfree.com/pg/orders\", \"secret_key\": \"\"}','test',0,'CashFree','2026-08-03 08:41:09','2026-08-11 21:45:07'),
(11,'JazzCash','jazzcash',NULL,'{\"note\": \"You have to setup this return URL in your JazzCash merchant account dashboard: http://localhost:8888/janmitram-app/payment/success\", \"base_url\": \"https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform\", \"password\": \"\", \"merchant_id\": \"\", \"integrity_salt\": \"\"}','test',0,'JazzCash','2026-08-03 08:41:09','2026-08-11 21:44:24');
/*!40000 ALTER TABLE `payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL DEFAULT 0,
  `currency` varchar(191) DEFAULT 'USD',
  `payment_method` varchar(191) DEFAULT 'cash',
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `payment_token` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,100,'USD','cash',0,NULL,'2026-08-05 18:21:26','2026-08-05 18:21:26'),
(2,100,'USD','cash',0,NULL,'2026-08-05 18:25:18','2026-08-05 18:25:18'),
(3,102,'USD','cash',0,NULL,'2026-08-11 21:56:14','2026-08-11 21:56:14'),
(4,210,'USD','cash',0,NULL,'2026-08-12 20:15:34','2026-08-12 20:15:34'),
(5,3632,'USD','cash',0,NULL,'2026-08-13 19:00:11','2026-08-13 19:00:11'),
(6,3844,'USD','cash',0,NULL,'2026-08-13 19:35:17','2026-08-13 19:35:18'),
(7,400,'USD','cash',0,NULL,'2026-08-13 20:03:18','2026-08-13 20:03:18'),
(8,290,'USD','cash',0,NULL,'2026-08-16 20:09:54','2026-08-16 20:09:54'),
(9,3365.8,'USD','cash',0,NULL,'2026-08-16 21:44:54','2026-08-16 21:44:54'),
(10,475,'USD','cash',0,NULL,'2026-08-16 22:03:27','2026-08-16 22:03:27');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `paypal_payments`
--

DROP TABLE IF EXISTS `paypal_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paypal_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `order_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paypal_payments_payment_id_foreign` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paypal_payments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `paypal_payments` WRITE;
/*!40000 ALTER TABLE `paypal_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `paypal_payments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'admin.dashboard.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(2,'admin.dashboard.notification','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(3,'admin.shop.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(4,'admin.shop.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(5,'admin.shop.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(6,'admin.shop.status.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(7,'admin.shop.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(8,'admin.shop.orders','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(9,'admin.shop.products','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(10,'admin.shop.reset.password','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(11,'admin.product.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(12,'admin.product.approve','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(13,'admin.product.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(14,'admin.product.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(15,'admin.coupon.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(16,'admin.coupon.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(17,'admin.coupon.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(18,'admin.coupon.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(19,'admin.withdraw.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(20,'admin.withdraw.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(21,'admin.withdraw.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(22,'admin.banner.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(23,'admin.banner.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(24,'admin.banner.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(25,'admin.banner.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(26,'admin.banner.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(27,'admin.ad.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(28,'admin.ad.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(29,'admin.ad.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(30,'admin.ad.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(31,'admin.ad.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(32,'admin.order.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(33,'admin.order.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(34,'admin.order.status.change','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(35,'admin.order.payment.status.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(36,'admin.order.assign.rider','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(37,'admin.review.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(38,'admin.review.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(39,'admin.brand.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(40,'admin.brand.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(41,'admin.brand.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(42,'admin.brand.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(43,'admin.brand.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(44,'admin.color.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(45,'admin.color.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(46,'admin.color.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(47,'admin.color.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(48,'admin.color.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(49,'admin.size.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(50,'admin.size.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(51,'admin.size.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(52,'admin.size.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(53,'admin.size.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(54,'admin.unit.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(55,'admin.unit.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(56,'admin.unit.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(57,'admin.unit.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(58,'admin.unit.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(59,'admin.category.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(60,'admin.category.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(61,'admin.category.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(62,'admin.category.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(63,'admin.category.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(64,'admin.subcategory.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(65,'admin.subcategory.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(66,'admin.subcategory.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(67,'admin.subcategory.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(68,'admin.subcategory.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(69,'admin.flashSale.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(70,'admin.flashSale.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(71,'admin.flashSale.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(72,'admin.flashSale.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(73,'admin.flashSale.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(74,'admin.generale-setting.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(75,'admin.generale-setting.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(76,'admin.business-setting.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(77,'admin.business-setting.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(78,'admin.verification.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(79,'admin.verification.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(80,'admin.socialLink.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(81,'admin.socialLink.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(82,'admin.socialLink.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(83,'admin.socialAuth.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(84,'admin.socialAuth.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(85,'admin.socialAuth.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(86,'admin.menu.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(87,'admin.menu.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(88,'admin.menu.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(89,'admin.menu.remove','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(90,'admin.menu.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(91,'admin.page.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(92,'admin.page.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(93,'admin.page.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(94,'admin.page.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(95,'admin.page.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(96,'admin.page.generate.AI.data','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(97,'admin.country.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(98,'admin.country.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(99,'admin.country.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(100,'admin.country.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(101,'admin.area.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(102,'admin.area.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(103,'admin.area.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(104,'admin.area.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(105,'admin.area.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(106,'admin.currency.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(107,'admin.currency.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(108,'admin.currency.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(109,'admin.currency.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(110,'admin.currency.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(111,'admin.themeColor.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(112,'admin.themeColor.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(113,'admin.themeColor.change','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(114,'admin.deliveryCharge.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(115,'admin.deliveryCharge.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(116,'admin.deliveryCharge.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(117,'admin.deliveryCharge.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(118,'admin.pusher.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(119,'admin.pusher.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(120,'admin.mailConfig.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(121,'admin.mailConfig.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(122,'admin.paymentGateway.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(123,'admin.paymentGateway.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(124,'admin.paymentGateway.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(125,'admin.sms-gateway.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(126,'admin.sms-gateway.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(127,'admin.googleReCaptcha.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(128,'admin.googleReCaptcha.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(129,'admin.contactUs.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(130,'admin.contactUs.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(131,'admin.firebase.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(132,'admin.firebase.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(133,'admin.profile.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(134,'admin.profile.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(135,'admin.profile.change-password','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(136,'admin.rider.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(137,'admin.rider.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(138,'admin.rider.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(139,'admin.rider.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(140,'admin.rider.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(141,'admin.rider.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(142,'admin.rider.assign.order','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(143,'admin.customer.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(144,'admin.customer.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(145,'admin.customer.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(146,'admin.customer.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(147,'admin.customer.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(148,'admin.customer.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(149,'admin.customer.reset.password','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(150,'admin.customerNotification.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(151,'admin.customerNotification.send','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(152,'admin.language.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(153,'admin.language.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(154,'admin.language.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(155,'admin.language.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(156,'admin.language.export','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(157,'admin.language.import','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(158,'admin.employee.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(159,'admin.employee.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(160,'admin.employee.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(161,'admin.employee.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(162,'admin.employee.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(163,'admin.employee.reset.password','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(164,'admin.employee.permission','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(165,'admin.employee.permission.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(166,'admin.role.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(167,'admin.role.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(168,'admin.role.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(169,'admin.role.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(170,'admin.role.permission','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(171,'admin.role.permission.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(172,'admin.ticketIssueType.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(173,'admin.ticketIssueType.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(174,'admin.ticketIssueType.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(175,'admin.ticketIssueType.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(176,'admin.ticketIssueType.delete','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(177,'admin.supportTicket.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(178,'admin.supportTicket.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(179,'admin.supportTicket.setScheduled','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(180,'admin.supportTicket.sendMessage','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(181,'admin.supportTicket.updateStatus','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(182,'admin.supportTicket.pinMessage','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(183,'admin.supportTicket.chatToggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(184,'admin.support.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(185,'admin.support.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(186,'admin.vatTax.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(187,'admin.vatTax.order.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(188,'admin.vatTax.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(189,'admin.vatTax.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(190,'admin.vatTax.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(191,'admin.vatTax.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(192,'admin.blog.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(193,'admin.blog.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(194,'admin.blog.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(195,'admin.blog.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(196,'admin.blog.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(197,'admin.blog.generate.AI.data','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(198,'admin.aiPrompt.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(199,'admin.aiPrompt.configure','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(200,'admin.aiPrompt.configure.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(201,'admin.aiPrompt.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(202,'admin.returnOrder.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(203,'admin.returnOrder.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(204,'admin.returnOrder.payment.status','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(205,'admin.returnOrder.reject','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(206,'admin.conversation.customer.chat.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(207,'admin.conversation.getUsers','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(208,'admin.conversation.getMessageAdmin','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(209,'admin.conversation.sendMessageAdmin','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(210,'admin.warehouse.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(211,'admin.warehouse.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(212,'admin.warehouse.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(213,'admin.warehouse.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(214,'admin.warehouse.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(215,'admin.warehouse.stock','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(216,'admin.warehouse.stock.add','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(217,'admin.stock-request.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(218,'admin.stock-request.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(219,'admin.stock-request.approve','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(220,'admin.stock-request.reject','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(221,'admin.warehouse-transfer.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(222,'admin.warehouse-transfer.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(223,'admin.warehouse-transfer.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(224,'admin.warehouse-transfer.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(225,'admin.warehouse-transfer.complete','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(226,'admin.warehouse-transfer.cancel','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(227,'admin.payout.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(228,'admin.payout.run','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(229,'admin.payout.network','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(230,'admin.payout.guide','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(231,'admin.payout.slip','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(232,'admin.project-guide.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(233,'shop.dashboard.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(234,'shop.dashboard.notification','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(235,'shop.order.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(236,'shop.order.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(237,'shop.order.status.change','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(238,'shop.product.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(239,'shop.product.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(240,'shop.product.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(241,'shop.product.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(242,'shop.product.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(243,'shop.product.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(244,'shop.product.barcode','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(245,'shop.product.generate.AI.data','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(246,'shop.flashSale.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(247,'shop.flashSale.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(248,'shop.flashSale.productStore','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(249,'shop.flashSale.productRemove','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(250,'shop.flashSale.product.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(251,'shop.voucher.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(252,'shop.voucher.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(253,'shop.voucher.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(254,'shop.voucher.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(255,'shop.voucher.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(256,'shop.bulk-product-import.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(257,'shop.bulk-product-import.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(258,'shop.bulk-product-export.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(259,'shop.bulk-product-export.demo','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(260,'shop.bulk-product-export.export','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(261,'shop.gallery.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(262,'shop.gallery.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(263,'shop.pos.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(264,'shop.pos.sales','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(265,'shop.pos.draft','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(266,'shop.employee.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(267,'shop.employee.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(268,'shop.employee.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(269,'shop.employee.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(270,'shop.employee.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(271,'shop.employee.reset.password','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(272,'shop.employee.permission','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(273,'shop.employee.permission.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(274,'shop.profile.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(275,'shop.profile.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(276,'shop.profile.change-password','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(277,'shop.returnOrder.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(278,'shop.returnOrder.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(279,'shop.returnOrder.status.change','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(280,'shop.supplier.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(281,'shop.supplier.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(282,'shop.supplier.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(283,'shop.supplier.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(284,'shop.supplier.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(285,'shop.supplier.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(286,'shop.supplier.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(287,'shop.supplier.toggle','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(288,'shop.supplier.statistic','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(289,'shop.supplier.payment','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(290,'shop.purchase.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(291,'shop.purchase.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(292,'shop.purchase.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(293,'shop.purchase.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(294,'shop.purchase.edit','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(295,'shop.purchase.update','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(296,'shop.purchase.destroy','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(297,'shop.purchase.attach.product','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(298,'shop.purchase.products','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(299,'shop.purchase.makeReceived','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(300,'shop.purchase.product.delete.barcode','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(301,'shop.purchase.invoice.search','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(302,'shop.purchase.invoice.add','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(303,'shop.purchase.summary','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(304,'shop.purchase.purchaseInvoice','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(305,'shop.purchase.allProduct.stockSummary','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(306,'shop.purchaseReturn.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(307,'shop.purchaseReturn.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(308,'shop.purchaseReturn.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(309,'shop.purchaseReturn.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(310,'shop.purchaseReturn.invoice.search','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(311,'shop.purchaseReturn.Invoice','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(312,'shop.purchaseReturn.invoice.add','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(313,'shop.stock-request.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(314,'shop.stock-request.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(315,'shop.stock-request.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(316,'shop.stock-request.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(317,'shop.payout.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(318,'shop.payout.network','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(319,'shop.payout.network.create','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(320,'shop.payout.network.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(321,'shop.payout.slip','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(322,'shop.brand.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(323,'shop.color.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(324,'shop.size.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(325,'shop.unit.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(326,'shop.category.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(327,'shop.subcategory.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(328,'shop.withdraw.index','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(329,'shop.withdraw.store','web','2026-08-03 08:41:08','2026-08-03 08:41:08'),
(330,'shop.withdraw.show','web','2026-08-03 08:41:08','2026-08-03 08:41:08');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES
(1,'App\\Models\\User',3,'api_token','4bc082902c78c718c511b7877d6fbd0e144db98501e90941db6b2094e772a8bf','[\"*\"]','2026-08-04 13:02:08',NULL,'2026-08-04 12:10:54','2026-08-04 13:02:08'),
(2,'App\\Models\\User',6,'api_token','9f892c36ab36f453de39cb58dc6d6f9ee8cc4255a63773e13c10d11af6ac2dcc','[\"*\"]','2026-08-05 20:31:49',NULL,'2026-08-05 18:17:04','2026-08-05 20:31:49'),
(3,'App\\Models\\User',1,'api_token','89815a1919564213e778d20e3be3770e7c4fd14d07b9c5d848e070dbc745836f','[\"*\"]','2026-08-06 18:41:22',NULL,'2026-08-06 18:21:21','2026-08-06 18:41:22'),
(4,'App\\Models\\User',9,'api_token','64a20afb59b6dc1b2c2621601207dc75dc0b284b982daf2611807b47b032b686','[\"*\"]','2026-08-11 19:29:33',NULL,'2026-08-11 19:23:57','2026-08-11 19:29:33'),
(5,'App\\Models\\User',9,'api_token','798c4e0be29d8aa8f91781353520a8926515f7f9c7f848da2d52af378e79d240','[\"*\"]','2026-08-11 19:34:45',NULL,'2026-08-11 19:33:12','2026-08-11 19:34:45'),
(6,'App\\Models\\User',9,'api_token','eddcf145cb1a38f8946cc95a981bbbc75322abb1e36fb9b8fb6f3d8f9d87d086','[\"*\"]','2026-08-11 21:59:49',NULL,'2026-08-11 21:49:49','2026-08-11 21:59:49'),
(7,'App\\Models\\User',9,'api_token','c2a98d7d416ce40a97a74768b2ff0a36dfd7feaf06e783f0efe490bfd5d9bee4','[\"*\"]','2026-08-13 19:00:16',NULL,'2026-08-12 20:12:43','2026-08-13 19:00:16'),
(8,'App\\Models\\User',9,'api_token','5035f7515e6de345cdb49d6c1a078877d696de372e286c82b9a81e0ed667e9a1','[\"*\"]','2026-08-13 19:03:56',NULL,'2026-08-13 19:03:56','2026-08-13 19:03:56'),
(9,'App\\Models\\User',9,'api_token','15b6ef1d680e82eab86fbb31a68cc5008747869edc7e09bcd7db8d48abd49d3b','[\"*\"]','2026-08-13 19:22:35',NULL,'2026-08-13 19:04:23','2026-08-13 19:22:35'),
(10,'App\\Models\\User',10,'api_token','df2961cc306830ccab0dbca778ff64349b34e5de7f4c4dac2274f2f24dd5c07f','[\"*\"]','2026-08-13 19:31:32',NULL,'2026-08-13 19:25:50','2026-08-13 19:31:32'),
(11,'App\\Models\\User',10,'api_token','993cc7a00b3f468f760676b564f99aa92e1cff0ba7ae502678f52dd043f9236f','[\"*\"]','2026-08-13 20:03:22',NULL,'2026-08-13 19:32:23','2026-08-13 20:03:22'),
(12,'App\\Models\\User',9,'api_token','28835a54d4e64d82d757c91e44bafdc60f24704f0775e239b185e4e39a1daa45','[\"*\"]','2026-08-14 21:23:03',NULL,'2026-08-13 20:09:28','2026-08-14 21:23:03'),
(13,'App\\Models\\User',11,'api_token','cf71f8d3bc466dab3cd80006cf4456b0c2b82ef6e4e744376297a8a05bcc4b8f','[\"*\"]','2026-08-14 21:23:32',NULL,'2026-08-14 21:23:32','2026-08-14 21:23:32'),
(14,'App\\Models\\User',13,'api_token','238c6bf73975e270357811fe24e1a0e9683386a685e076adda48a5093a803c10','[\"*\"]','2026-08-15 21:54:49',NULL,'2026-08-15 21:51:46','2026-08-15 21:54:49'),
(15,'App\\Models\\User',3,'api_token','d5f1c480f26c47be0c1d617b706a530c9a0c3c964b033887fd49ac685a625729','[\"*\"]','2026-08-16 20:59:36',NULL,'2026-08-16 20:03:38','2026-08-16 20:59:36'),
(16,'App\\Models\\User',8,'api_token','5ba8cb6138c20d7f620de7c5a469f07e50088bdfa9f4c3dc616da00632b2e151','[\"*\"]','2026-08-16 20:53:13',NULL,'2026-08-16 20:52:08','2026-08-16 20:53:13'),
(17,'App\\Models\\User',12,'api_token','b518db11219e727a972d4472c42ea901f2bb5583f0b9b8a376e9c9743221f78b','[\"*\"]','2026-08-16 22:08:03',NULL,'2026-08-16 21:33:28','2026-08-16 22:08:03');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `pos_cart_products`
--

DROP TABLE IF EXISTS `pos_cart_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_cart_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pos_cart_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `brand` varchar(191) DEFAULT NULL,
  `color` varchar(191) DEFAULT NULL,
  `size` varchar(191) DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_cart_products_pos_cart_id_foreign` (`pos_cart_id`),
  KEY `pos_cart_products_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_cart_products`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `pos_cart_products` WRITE;
/*!40000 ALTER TABLE `pos_cart_products` DISABLE KEYS */;
INSERT INTO `pos_cart_products` VALUES
(2,4,6,1,NULL,'1','1',NULL,'2026-08-06 19:25:46','2026-08-06 19:25:46');
/*!40000 ALTER TABLE `pos_cart_products` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `pos_carts`
--

DROP TABLE IF EXISTS `pos_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `is_draft` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `note` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `card_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_carts_shop_id_foreign` (`shop_id`),
  KEY `pos_carts_user_id_foreign` (`user_id`),
  KEY `pos_carts_coupon_id_foreign` (`coupon_id`),
  KEY `pos_carts_created_by_foreign` (`created_by`),
  KEY `pos_carts_card_id_foreign` (`card_id`),
  CONSTRAINT `pos_carts_card_id_foreign` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_carts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `pos_carts` WRITE;
/*!40000 ALTER TABLE `pos_carts` DISABLE KEYS */;
INSERT INTO `pos_carts` VALUES
(2,'4Q832sODOBwk','::1',2,3,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-03 11:51:37','2026-08-03 11:51:37',NULL),
(4,'NqkvEWNEq2sV','2401:4900:7eee:a408:70fb:ede2:dc80:aa0a',4,5,0,NULL,NULL,0,20,20,NULL,'2026-08-06 19:13:50','2026-08-06 19:25:46',NULL),
(6,'CaBMqm94xsxN','223.188.56.102',1,1,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-17 19:06:05','2026-08-17 19:06:05',NULL);
/*!40000 ALTER TABLE `pos_carts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_attachments`
--

DROP TABLE IF EXISTS `product_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attachments` (
  `product_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `product_attachments_product_id_foreign` (`product_id`),
  KEY `product_attachments_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attachments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_attachments` WRITE;
/*!40000 ALTER TABLE `product_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attachments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `product_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  KEY `product_categories_product_id_foreign` (`product_id`),
  KEY `product_categories_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES
(1,1),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1),
(10,1),
(11,1),
(12,1),
(13,1),
(14,1),
(15,1),
(16,1),
(17,1),
(18,1),
(19,1),
(20,1),
(21,1),
(22,1),
(23,1),
(24,1),
(25,1),
(26,1),
(27,1),
(28,1),
(29,1),
(30,1),
(31,1),
(32,1),
(33,1),
(34,1),
(35,1),
(36,1),
(37,1),
(38,1),
(39,1),
(40,1),
(41,1),
(42,1),
(43,1),
(44,1),
(45,1),
(46,1),
(47,1),
(48,1),
(49,1),
(50,1),
(51,1),
(52,1),
(53,1),
(54,1),
(55,1),
(56,1),
(57,1),
(58,1),
(59,1),
(60,1),
(61,1),
(62,1),
(63,1),
(64,1),
(65,1),
(66,1),
(67,1),
(68,1),
(69,1),
(70,1),
(71,1),
(72,1),
(73,1),
(74,1),
(75,1),
(76,1),
(77,1),
(78,1),
(79,1),
(80,1),
(81,1),
(82,1),
(83,1),
(84,1),
(85,1),
(86,1),
(87,1),
(88,1),
(89,1),
(90,1),
(91,1),
(92,1),
(93,1),
(94,1),
(95,1),
(96,1),
(97,1),
(98,1),
(99,1),
(100,1),
(101,1),
(102,1),
(103,1),
(104,1),
(105,1),
(106,1),
(107,1),
(108,1),
(109,1),
(110,1),
(111,1),
(112,1),
(113,1),
(114,1),
(115,1),
(116,1),
(117,1),
(118,1),
(119,1),
(120,1),
(121,1),
(122,1);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_colors`
--

DROP TABLE IF EXISTS `product_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_colors` (
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT 0,
  KEY `product_colors_product_id_foreign` (`product_id`),
  KEY `product_colors_color_id_foreign` (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_colors`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_colors` WRITE;
/*!40000 ALTER TABLE `product_colors` DISABLE KEYS */;
INSERT INTO `product_colors` VALUES
(2,1,0),
(5,3,0),
(6,1,0),
(16,2,0),
(19,3,0),
(21,1,0),
(23,6,0),
(24,2,0),
(27,2,0),
(30,3,0),
(32,1,0),
(34,6,0),
(35,2,0),
(38,2,0),
(41,3,0),
(43,1,0),
(45,6,0),
(46,2,0),
(51,2,0),
(52,2,0),
(53,6,0),
(57,3,0),
(58,1,0),
(60,2,0),
(65,6,0),
(66,2,0),
(69,2,0),
(72,3,0),
(74,1,0),
(76,6,0),
(77,2,0),
(80,2,0),
(83,3,0),
(85,1,0),
(87,6,0),
(88,2,0),
(11,2,0),
(10,2,0),
(9,6,0),
(3,3,0),
(1,1,0),
(91,2,0),
(94,3,0),
(96,1,0),
(98,6,0),
(99,2,0),
(102,2,0),
(105,3,0),
(107,1,0),
(109,6,0),
(110,2,0),
(113,2,0),
(116,3,0),
(118,1,0),
(120,6,0),
(121,2,0);
/*!40000 ALTER TABLE `product_colors` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_licenses`
--

DROP TABLE IF EXISTS `product_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_licenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `product_license` text DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_licenses_product_id_foreign` (`product_id`),
  KEY `product_licenses_user_id_foreign` (`user_id`),
  KEY `product_licenses_order_id_foreign` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_licenses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_licenses` WRITE;
/*!40000 ALTER TABLE `product_licenses` DISABLE KEYS */;
INSERT INTO `product_licenses` VALUES
(1,3,NULL,NULL,NULL,0,0,'2026-08-05 21:49:31','2026-08-05 21:49:31'),
(2,1,NULL,NULL,NULL,0,0,'2026-08-05 21:51:15','2026-08-05 21:51:15'),
(3,4,NULL,NULL,NULL,0,0,'2026-08-05 21:55:47','2026-08-05 21:55:47'),
(4,10,NULL,NULL,NULL,0,0,'2026-08-06 20:56:12','2026-08-06 20:56:12'),
(5,7,NULL,NULL,NULL,0,0,'2026-08-06 21:11:11','2026-08-06 21:11:11'),
(6,14,NULL,NULL,NULL,0,0,'2026-08-16 21:59:13','2026-08-16 21:59:13'),
(7,13,NULL,NULL,NULL,0,0,'2026-08-16 21:59:29','2026-08-16 21:59:29'),
(8,12,NULL,NULL,NULL,0,0,'2026-08-16 21:59:43','2026-08-16 21:59:43'),
(9,11,NULL,NULL,NULL,0,0,'2026-08-16 21:59:57','2026-08-16 21:59:57'),
(10,9,NULL,NULL,NULL,0,0,'2026-08-16 22:00:30','2026-08-16 22:00:30'),
(11,8,NULL,NULL,NULL,0,0,'2026-08-16 22:00:49','2026-08-16 22:00:49');
/*!40000 ALTER TABLE `product_licenses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_sizes`
--

DROP TABLE IF EXISTS `product_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_sizes` (
  `product_id` bigint(20) unsigned NOT NULL,
  `size_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT NULL,
  KEY `product_sizes_product_id_foreign` (`product_id`),
  KEY `product_sizes_size_id_foreign` (`size_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_sizes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_sizes` WRITE;
/*!40000 ALTER TABLE `product_sizes` DISABLE KEYS */;
INSERT INTO `product_sizes` VALUES
(2,1,0),
(5,1,0),
(6,1,0),
(15,1,0),
(16,2,0),
(17,1,0),
(18,1,0),
(19,1,0),
(20,1,0),
(21,1,0),
(23,1,0),
(24,1,0),
(25,1,0),
(26,1,0),
(27,2,0),
(28,1,0),
(29,1,0),
(30,1,0),
(31,1,0),
(32,1,0),
(34,1,0),
(35,1,0),
(36,1,0),
(37,1,0),
(38,2,0),
(39,1,0),
(40,1,0),
(41,1,0),
(42,1,0),
(43,1,0),
(45,1,0),
(46,1,0),
(47,1,0),
(48,1,0),
(49,1,0),
(50,1,0),
(51,2,0),
(52,1,0),
(53,1,0),
(54,1,0),
(55,1,0),
(57,1,0),
(58,1,0),
(59,1,0),
(60,2,0),
(61,1,0),
(62,1,0),
(63,1,0),
(65,1,0),
(66,1,0),
(67,1,0),
(68,1,0),
(69,2,0),
(70,1,0),
(71,1,0),
(72,1,0),
(73,1,0),
(74,1,0),
(76,1,0),
(77,1,0),
(78,1,0),
(79,1,0),
(80,2,0),
(81,1,0),
(82,1,0),
(83,1,0),
(84,1,0),
(85,1,0),
(87,1,0),
(88,1,0),
(89,1,0),
(14,1,0),
(13,1,0),
(12,1,0),
(11,2,0),
(10,1,0),
(9,1,0),
(8,1,0),
(7,1,0),
(3,1,0),
(1,1,0),
(90,1,0),
(91,2,0),
(92,1,0),
(93,1,0),
(94,1,0),
(95,1,0),
(96,1,0),
(98,1,0),
(99,1,0),
(100,1,0),
(101,1,0),
(102,2,0),
(103,1,0),
(104,1,0),
(105,1,0),
(106,1,0),
(107,1,0),
(109,1,0),
(110,1,0),
(111,1,0),
(112,1,0),
(113,2,0),
(114,1,0),
(115,1,0),
(116,1,0),
(117,1,0),
(118,1,0),
(120,1,0),
(121,1,0),
(122,1,0);
/*!40000 ALTER TABLE `product_sizes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_subcategories`
--

DROP TABLE IF EXISTS `product_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_subcategories` (
  `sub_category_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  KEY `product_subcategories_sub_category_id_foreign` (`sub_category_id`),
  KEY `product_subcategories_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_subcategories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_subcategories` WRITE;
/*!40000 ALTER TABLE `product_subcategories` DISABLE KEYS */;
INSERT INTO `product_subcategories` VALUES
(1,7),
(3,8),
(3,9),
(3,10),
(7,11),
(8,12),
(4,13),
(2,14),
(2,15),
(7,16),
(4,17),
(8,18),
(1,20),
(3,23),
(3,24),
(3,25),
(2,26),
(7,27),
(4,28),
(8,29),
(1,31),
(3,34),
(3,35),
(3,36),
(2,37),
(7,38),
(4,39),
(8,40),
(1,42),
(3,45),
(3,46),
(3,47),
(2,48),
(4,49),
(8,50),
(7,51),
(3,52),
(3,53),
(3,54),
(1,55),
(2,59),
(7,60),
(4,61),
(8,62),
(1,63),
(3,65),
(3,66),
(3,67),
(2,68),
(7,69),
(4,70),
(8,71),
(1,73),
(3,76),
(3,77),
(3,78),
(2,79),
(7,80),
(4,81),
(8,82),
(1,84),
(3,87),
(3,88),
(3,89),
(2,90),
(7,91),
(4,92),
(8,93),
(1,95),
(3,98),
(3,99),
(3,100),
(2,101),
(7,102),
(4,103),
(8,104),
(1,106),
(3,109),
(3,110),
(3,111),
(2,112),
(7,113),
(4,114),
(8,115),
(1,117),
(3,120),
(3,121),
(3,122);
/*!40000 ALTER TABLE `product_subcategories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_thumbnails`
--

DROP TABLE IF EXISTS `product_thumbnails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_thumbnails` (
  `product_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  KEY `product_thumbnails_product_id_foreign` (`product_id`),
  KEY `product_thumbnails_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_thumbnails`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_thumbnails` WRITE;
/*!40000 ALTER TABLE `product_thumbnails` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_thumbnails` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_translations`
--

DROP TABLE IF EXISTS `product_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `lang` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_translations_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_translations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_translations` WRITE;
/*!40000 ALTER TABLE `product_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_translations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_units`
--

DROP TABLE IF EXISTS `product_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_units` (
  `product_id` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  KEY `product_units_product_id_foreign` (`product_id`),
  KEY `product_units_unit_id_foreign` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_units`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_units` WRITE;
/*!40000 ALTER TABLE `product_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_units` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `product_vat_taxes`
--

DROP TABLE IF EXISTS `product_vat_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_vat_taxes` (
  `product_id` bigint(20) unsigned NOT NULL,
  `vat_tax_id` bigint(20) unsigned NOT NULL,
  KEY `product_vat_taxes_product_id_foreign` (`product_id`),
  KEY `product_vat_taxes_vat_tax_id_foreign` (`vat_tax_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_vat_taxes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `product_vat_taxes` WRITE;
/*!40000 ALTER TABLE `product_vat_taxes` DISABLE KEYS */;
INSERT INTO `product_vat_taxes` VALUES
(14,1),
(13,1),
(12,1),
(11,1),
(10,1),
(9,1),
(8,1),
(7,1),
(4,1),
(3,1),
(1,1);
/*!40000 ALTER TABLE `product_vat_taxes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `code` varchar(191) DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `price` double NOT NULL,
  `buy_price` double DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_order_quantity` int(11) NOT NULL DEFAULT 1,
  `discount_price` double DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `short_description_ar` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `description_ar` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_digital` tinyint(1) NOT NULL DEFAULT 0,
  `is_stock_managed` tinyint(1) NOT NULL DEFAULT 0,
  `master_product_id` bigint(20) unsigned DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_approve` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_description` varchar(191) DEFAULT NULL,
  `meta_keywords` varchar(191) DEFAULT NULL,
  `video_id` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_shop_id_foreign` (`shop_id`),
  KEY `products_media_id_foreign` (`media_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_unit_id_foreign` (`unit_id`),
  KEY `products_video_id_foreign` (`video_id`),
  KEY `products_name_index` (`name`),
  KEY `products_master_product_id_foreign` (`master_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,'HALDI POWDER',NULL,NULL,'750608',1,5,1,100,50,246,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-03 11:23:39','2026-08-17 20:26:55',3,NULL,NULL,NULL,NULL,NULL),
(2,'Haldi',NULL,NULL,'750608',2,5,1,100,40,48,1,0,'haldi',NULL,'<p>haldi</p>',NULL,0,0,1,1,1,0,1,'2026-08-03 11:50:48','2026-08-11 21:46:36',3,NULL,NULL,NULL,NULL,'2026-08-06 18:27:22'),
(3,'DHANIYA POWDER',NULL,NULL,'881264',1,49,1,60,50,152,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,NULL,0,0,1,'2026-08-05 21:48:44','2026-08-17 20:26:55',3,NULL,NULL,NULL,NULL,NULL),
(4,'LAL MIRCH POWDER',NULL,NULL,'442016',1,50,1,72,50,308,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-05 21:55:16','2026-08-17 21:23:58',3,NULL,NULL,NULL,NULL,NULL),
(5,'DHANIYA POWDER',NULL,NULL,'881264',4,49,1,60,50,69,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-06 19:12:36','2026-08-16 20:09:54',3,NULL,NULL,NULL,NULL,NULL),
(6,'HALDI POWDER',NULL,NULL,'750608',4,5,1,90,70,125,1,20,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-06 19:25:21','2026-08-16 20:09:54',3,NULL,NULL,NULL,NULL,NULL),
(7,'GARAM MASALA',NULL,NULL,'505930',1,57,1,180,150,308,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 20:41:22','2026-08-17 20:26:55',3,NULL,NULL,NULL,NULL,NULL),
(8,'SARSON TEL',NULL,NULL,'308386',1,58,1,230,200,307,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 20:49:53','2026-08-17 20:26:55',4,NULL,NULL,NULL,NULL,NULL),
(9,'MUNGFALI TEL',NULL,NULL,'158742',1,59,1,290,250,308,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 20:52:57','2026-08-17 20:26:55',4,NULL,NULL,NULL,NULL,NULL),
(10,'NARIYAL TEL',NULL,NULL,'539349',1,60,1,150,100,307,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 20:55:20','2026-08-17 20:26:55',7,NULL,NULL,NULL,NULL,NULL),
(11,'ATTA (WHEAT)',NULL,NULL,'816243',1,62,1,450,400,308,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 21:04:43','2026-08-17 20:26:55',8,NULL,NULL,NULL,NULL,NULL),
(12,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',1,64,1,70,50,308,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 21:10:03','2026-08-17 20:26:55',1,NULL,NULL,NULL,NULL,NULL),
(13,'BASMATI RICE',NULL,NULL,'729166',1,65,1,120,100,308,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 21:14:37','2026-08-17 20:26:55',1,NULL,NULL,NULL,NULL,NULL),
(14,'ARHAR DAL',NULL,NULL,'776461',1,66,1,140,100,308,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-06 21:16:14','2026-08-17 20:26:55',1,NULL,NULL,NULL,NULL,NULL),
(15,'ARHAR DAL',NULL,NULL,'776461',5,66,1,140,100,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-08 20:40:40','2026-08-08 20:40:40',1,NULL,NULL,NULL,NULL,NULL),
(16,'ATTA (WHEAT)',NULL,NULL,'816243',5,62,1,450,400,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-08 20:40:40','2026-08-08 20:40:40',8,NULL,NULL,NULL,NULL,NULL),
(17,'BASMATI RICE',NULL,NULL,'729166',5,65,1,120,100,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-08 20:40:40','2026-08-08 20:40:40',1,NULL,NULL,NULL,NULL,NULL),
(18,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',5,64,1,70,50,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',1,NULL,NULL,NULL,NULL,NULL),
(19,'DHANIYA POWDER',NULL,NULL,'881264',5,49,1,60,50,10,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',3,NULL,NULL,NULL,NULL,NULL),
(20,'GARAM MASALA',NULL,NULL,'505930',5,57,1,180,150,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',3,NULL,NULL,NULL,NULL,NULL),
(21,'HALDI POWDER',NULL,NULL,'750608',5,5,1,100,100,10,1,5,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',3,NULL,NULL,NULL,NULL,NULL),
(22,'LAL MIRCH POWDER',NULL,NULL,'442016',5,50,1,72,50,10,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',NULL,NULL,NULL,NULL,NULL,NULL),
(23,'MUNGFALI TEL',NULL,NULL,'158742',5,59,1,290,250,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',4,NULL,NULL,NULL,NULL,NULL),
(24,'NARIYAL TEL',NULL,NULL,'539349',5,60,1,150,100,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',7,NULL,NULL,NULL,NULL,NULL),
(25,'SARSON TEL',NULL,NULL,'308386',5,58,1,230,200,10,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-08 20:40:41','2026-08-08 20:40:41',4,NULL,NULL,NULL,NULL,NULL),
(26,'ARHAR DAL',NULL,NULL,'776461',7,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',1,NULL,NULL,NULL,NULL,NULL),
(27,'ATTA (WHEAT)',NULL,NULL,'816243',7,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',8,NULL,NULL,NULL,NULL,NULL),
(28,'BASMATI RICE',NULL,NULL,'729166',7,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',1,NULL,NULL,NULL,NULL,NULL),
(29,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',7,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',1,NULL,NULL,NULL,NULL,NULL),
(30,'DHANIYA POWDER',NULL,NULL,'881264',7,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',3,NULL,NULL,NULL,NULL,NULL),
(31,'GARAM MASALA',NULL,NULL,'505930',7,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',3,NULL,NULL,NULL,NULL,NULL),
(32,'HALDI POWDER',NULL,NULL,'750608',7,5,1,100,100,20,1,5,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',3,NULL,NULL,NULL,NULL,NULL),
(33,'LAL MIRCH POWDER',NULL,NULL,'442016',7,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',NULL,NULL,NULL,NULL,NULL,NULL),
(34,'MUNGFALI TEL',NULL,NULL,'158742',7,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',4,NULL,NULL,NULL,NULL,NULL),
(35,'NARIYAL TEL',NULL,NULL,'539349',7,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',7,NULL,NULL,NULL,NULL,NULL),
(36,'SARSON TEL',NULL,NULL,'308386',7,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-09 20:58:09','2026-08-09 20:58:09',4,NULL,NULL,NULL,NULL,NULL),
(37,'ARHAR DAL',NULL,NULL,'776461',8,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',1,NULL,NULL,NULL,NULL,NULL),
(38,'ATTA (WHEAT)',NULL,NULL,'816243',8,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',8,NULL,NULL,NULL,NULL,NULL),
(39,'BASMATI RICE',NULL,NULL,'729166',8,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',1,NULL,NULL,NULL,NULL,NULL),
(40,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',8,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',1,NULL,NULL,NULL,NULL,NULL),
(41,'DHANIYA POWDER',NULL,NULL,'881264',8,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',3,NULL,NULL,NULL,NULL,NULL),
(42,'GARAM MASALA',NULL,NULL,'505930',8,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',3,NULL,NULL,NULL,NULL,NULL),
(43,'HALDI POWDER',NULL,NULL,'750608',8,5,1,100,100,20,1,5,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',3,NULL,NULL,NULL,NULL,NULL),
(44,'LAL MIRCH POWDER',NULL,NULL,'442016',8,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',NULL,NULL,NULL,NULL,NULL,NULL),
(45,'MUNGFALI TEL',NULL,NULL,'158742',8,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',4,NULL,NULL,NULL,NULL,NULL),
(46,'NARIYAL TEL',NULL,NULL,'539349',8,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',7,NULL,NULL,NULL,NULL,NULL),
(47,'SARSON TEL',NULL,NULL,'308386',8,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-10 20:25:23','2026-08-10 20:25:23',4,NULL,NULL,NULL,NULL,NULL),
(48,'ARHAR DAL',NULL,NULL,'776461',6,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',1,NULL,NULL,NULL,NULL,NULL),
(49,'BASMATI RICE',NULL,NULL,'729166',6,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',1,NULL,NULL,NULL,NULL,NULL),
(50,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',6,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',1,NULL,NULL,NULL,NULL,NULL),
(51,'ATTA (WHEAT)',NULL,NULL,'816243',6,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',8,NULL,NULL,NULL,NULL,NULL),
(52,'NARIYAL TEL',NULL,NULL,'539349',6,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',7,NULL,NULL,NULL,NULL,NULL),
(53,'MUNGFALI TEL',NULL,NULL,'158742',6,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',4,NULL,NULL,NULL,NULL,NULL),
(54,'SARSON TEL',NULL,NULL,'308386',6,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',4,NULL,NULL,NULL,NULL,NULL),
(55,'GARAM MASALA',NULL,NULL,'505930',6,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',3,NULL,NULL,NULL,NULL,NULL),
(56,'LAL MIRCH POWDER',NULL,NULL,'442016',6,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-12 20:28:39','2026-08-13 19:21:55',NULL,NULL,NULL,NULL,NULL,NULL),
(57,'DHANIYA POWDER',NULL,NULL,'881264',6,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-12 20:28:39','2026-08-12 20:28:39',3,NULL,NULL,NULL,NULL,NULL),
(58,'HALDI POWDER',NULL,NULL,'750608',6,5,1,100,50,20,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-12 20:28:39','2026-08-12 20:28:39',3,NULL,NULL,NULL,NULL,NULL),
(59,'ARHAR DAL',NULL,NULL,'776461',4,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-12 21:27:16','2026-08-12 21:27:16',1,NULL,NULL,NULL,NULL,NULL),
(60,'ATTA (WHEAT)',NULL,NULL,'816243',4,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-12 21:27:16','2026-08-12 21:27:16',8,NULL,NULL,NULL,NULL,NULL),
(61,'BASMATI RICE',NULL,NULL,'729166',4,65,1,120,100,19,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-12 21:27:17','2026-08-16 20:09:54',1,NULL,NULL,NULL,NULL,NULL),
(62,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',4,64,1,70,50,19,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-12 21:27:17','2026-08-16 20:09:54',1,NULL,NULL,NULL,NULL,NULL),
(63,'GARAM MASALA',NULL,NULL,'505930',4,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-12 21:27:17','2026-08-12 21:27:17',3,NULL,NULL,NULL,NULL,NULL),
(64,'LAL MIRCH POWDER',NULL,NULL,'442016',4,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-12 21:27:17','2026-08-12 21:27:17',NULL,NULL,NULL,NULL,NULL,NULL),
(65,'MUNGFALI TEL',NULL,NULL,'158742',4,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-12 21:27:17','2026-08-12 21:27:17',4,NULL,NULL,NULL,NULL,NULL),
(66,'NARIYAL TEL',NULL,NULL,'539349',4,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-12 21:27:17','2026-08-12 21:27:17',7,NULL,NULL,NULL,NULL,NULL),
(67,'SARSON TEL',NULL,NULL,'308386',4,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-12 21:27:17','2026-08-12 21:27:17',4,NULL,NULL,NULL,NULL,NULL),
(68,'ARHAR DAL',NULL,NULL,'776461',9,66,1,140,100,19,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',1,NULL,NULL,NULL,NULL,NULL),
(69,'ATTA (WHEAT)',NULL,NULL,'816243',9,62,1,450,400,17,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-16 18:26:59','2026-08-16 22:03:27',8,NULL,NULL,NULL,NULL,NULL),
(70,'BASMATI RICE',NULL,NULL,'729166',9,65,1,120,100,19,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',1,NULL,NULL,NULL,NULL,NULL),
(71,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',9,64,1,70,50,18,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',1,NULL,NULL,NULL,NULL,NULL),
(72,'DHANIYA POWDER',NULL,NULL,'881264',9,49,1,60,50,19,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',3,NULL,NULL,NULL,NULL,NULL),
(73,'GARAM MASALA',NULL,NULL,'505930',9,57,1,180,150,18,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',3,NULL,NULL,NULL,NULL,NULL),
(74,'HALDI POWDER',NULL,NULL,'750608',9,5,1,100,50,19,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',3,NULL,NULL,NULL,NULL,NULL),
(75,'LAL MIRCH POWDER',NULL,NULL,'442016',9,50,1,72,50,19,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',NULL,NULL,NULL,NULL,NULL,NULL),
(76,'MUNGFALI TEL',NULL,NULL,'158742',9,59,1,290,250,19,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',4,NULL,NULL,NULL,NULL,NULL),
(77,'NARIYAL TEL',NULL,NULL,'539349',9,60,1,150,100,19,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',7,NULL,NULL,NULL,NULL,NULL),
(78,'SARSON TEL',NULL,NULL,'308386',9,58,1,230,200,14,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-16 18:26:59','2026-08-16 21:44:54',4,NULL,NULL,NULL,NULL,NULL),
(79,'ARHAR DAL',NULL,NULL,'776461',11,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,1,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',1,NULL,NULL,NULL,NULL,NULL),
(80,'ATTA (WHEAT)',NULL,NULL,'816243',11,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,1,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',8,NULL,NULL,NULL,NULL,NULL),
(81,'BASMATI RICE',NULL,NULL,'729166',11,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,1,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',1,NULL,NULL,NULL,NULL,NULL),
(82,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',11,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,1,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',1,NULL,NULL,NULL,NULL,NULL),
(83,'DHANIYA POWDER',NULL,NULL,'881264',11,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',3,NULL,NULL,NULL,NULL,NULL),
(84,'GARAM MASALA',NULL,NULL,'505930',11,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',3,NULL,NULL,NULL,NULL,NULL),
(85,'HALDI POWDER',NULL,NULL,'750608',11,5,1,100,50,20,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',3,NULL,NULL,NULL,NULL,NULL),
(86,'LAL MIRCH POWDER',NULL,NULL,'442016',11,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',NULL,NULL,NULL,NULL,NULL,NULL),
(87,'MUNGFALI TEL',NULL,NULL,'158742',11,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,1,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',4,NULL,NULL,NULL,NULL,NULL),
(88,'NARIYAL TEL',NULL,NULL,'539349',11,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',7,NULL,NULL,NULL,NULL,NULL),
(89,'SARSON TEL',NULL,NULL,'308386',11,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,1,0,1,'2026-08-16 18:30:44','2026-08-16 18:30:44',4,NULL,NULL,NULL,NULL,NULL),
(90,'ARHAR DAL',NULL,NULL,'776461',13,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',1,NULL,NULL,NULL,NULL,NULL),
(91,'ATTA (WHEAT)',NULL,NULL,'816243',13,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',8,NULL,NULL,NULL,NULL,NULL),
(92,'BASMATI RICE',NULL,NULL,'729166',13,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',1,NULL,NULL,NULL,NULL,NULL),
(93,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',13,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',1,NULL,NULL,NULL,NULL,NULL),
(94,'DHANIYA POWDER',NULL,NULL,'881264',13,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',3,NULL,NULL,NULL,NULL,NULL),
(95,'GARAM MASALA',NULL,NULL,'505930',13,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',3,NULL,NULL,NULL,NULL,NULL),
(96,'HALDI POWDER',NULL,NULL,'750608',13,5,1,100,50,20,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',3,NULL,NULL,NULL,NULL,NULL),
(97,'LAL MIRCH POWDER',NULL,NULL,'442016',13,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',NULL,NULL,NULL,NULL,NULL,NULL),
(98,'MUNGFALI TEL',NULL,NULL,'158742',13,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',4,NULL,NULL,NULL,NULL,NULL),
(99,'NARIYAL TEL',NULL,NULL,'539349',13,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',7,NULL,NULL,NULL,NULL,NULL),
(100,'SARSON TEL',NULL,NULL,'308386',13,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,0,0,1,'2026-08-17 20:23:01','2026-08-17 20:23:01',4,NULL,NULL,NULL,NULL,NULL),
(101,'ARHAR DAL',NULL,NULL,'776461',12,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',1,NULL,NULL,NULL,NULL,NULL),
(102,'ATTA (WHEAT)',NULL,NULL,'816243',12,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',8,NULL,NULL,NULL,NULL,NULL),
(103,'BASMATI RICE',NULL,NULL,'729166',12,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',1,NULL,NULL,NULL,NULL,NULL),
(104,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',12,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',1,NULL,NULL,NULL,NULL,NULL),
(105,'DHANIYA POWDER',NULL,NULL,'881264',12,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',3,NULL,NULL,NULL,NULL,NULL),
(106,'GARAM MASALA',NULL,NULL,'505930',12,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',3,NULL,NULL,NULL,NULL,NULL),
(107,'HALDI POWDER',NULL,NULL,'750608',12,5,1,100,50,20,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',3,NULL,NULL,NULL,NULL,NULL),
(108,'LAL MIRCH POWDER',NULL,NULL,'442016',12,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',NULL,NULL,NULL,NULL,NULL,NULL),
(109,'MUNGFALI TEL',NULL,NULL,'158742',12,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',4,NULL,NULL,NULL,NULL,NULL),
(110,'NARIYAL TEL',NULL,NULL,'539349',12,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',7,NULL,NULL,NULL,NULL,NULL),
(111,'SARSON TEL',NULL,NULL,'308386',12,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,0,0,1,'2026-08-17 20:24:24','2026-08-17 20:24:24',4,NULL,NULL,NULL,NULL,NULL),
(112,'ARHAR DAL',NULL,NULL,'776461',10,66,1,140,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,14,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',1,NULL,NULL,NULL,NULL,NULL),
(113,'ATTA (WHEAT)',NULL,NULL,'816243',10,62,1,450,400,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,11,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',8,NULL,NULL,NULL,NULL,NULL),
(114,'BASMATI RICE',NULL,NULL,'729166',10,65,1,120,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,13,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',1,NULL,NULL,NULL,NULL,NULL),
(115,'DESHI KHAND (KHANDSARI)',NULL,NULL,'901163',10,64,1,70,50,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,12,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',1,NULL,NULL,NULL,NULL,NULL),
(116,'DHANIYA POWDER',NULL,NULL,'881264',10,49,1,60,50,20,1,0,'NATURAL',NULL,'<p><br></p>',NULL,1,0,1,3,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',3,NULL,NULL,NULL,NULL,NULL),
(117,'GARAM MASALA',NULL,NULL,'505930',10,57,1,180,150,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,7,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',3,NULL,NULL,NULL,NULL,NULL),
(118,'HALDI POWDER',NULL,NULL,'750608',10,5,1,100,50,20,1,0,'NATURTAL   A GRADE',NULL,'<p>haldi</p>',NULL,1,0,1,1,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',3,NULL,NULL,NULL,NULL,NULL),
(119,'LAL MIRCH POWDER',NULL,NULL,'442016',10,50,1,72,50,20,1,0,'NATURAL',NULL,'<p>NATURAL   A GRADE QUALITY</p>',NULL,1,0,1,4,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',NULL,NULL,NULL,NULL,NULL,NULL),
(120,'MUNGFALI TEL',NULL,NULL,'158742',10,59,1,290,250,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,9,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',4,NULL,NULL,NULL,NULL,NULL),
(121,'NARIYAL TEL',NULL,NULL,'539349',10,60,1,150,100,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,10,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',7,NULL,NULL,NULL,NULL,NULL),
(122,'SARSON TEL',NULL,NULL,'308386',10,58,1,230,200,20,1,0,'NATURAL',NULL,'<p>NATURAL</p>',NULL,1,0,1,8,0,0,1,'2026-08-17 20:26:55','2026-08-17 20:26:55',4,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `recent_views`
--

DROP TABLE IF EXISTS `recent_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recent_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recent_views_product_id_foreign` (`product_id`),
  KEY `recent_views_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recent_views`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `recent_views` WRITE;
/*!40000 ALTER TABLE `recent_views` DISABLE KEYS */;
INSERT INTO `recent_views` VALUES
(1,2,6,'2026-08-05 18:23:35','2026-08-05 18:23:35'),
(2,81,3,'2026-08-16 20:04:33','2026-08-16 20:18:26'),
(3,80,3,'2026-08-16 20:04:46','2026-08-16 20:04:46'),
(4,84,3,'2026-08-16 20:04:49','2026-08-16 20:04:56'),
(5,79,3,'2026-08-16 20:05:08','2026-08-16 20:05:08'),
(6,62,3,'2026-08-16 20:05:33','2026-08-16 20:05:33');
/*!40000 ALTER TABLE `recent_views` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `return_order_details`
--

DROP TABLE IF EXISTS `return_order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_order_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `return_order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `color` varchar(191) DEFAULT NULL,
  `size` varchar(191) DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_order_details_return_order_id_foreign` (`return_order_id`),
  KEY `return_order_details_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_order_details`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `return_order_details` WRITE;
/*!40000 ALTER TABLE `return_order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_order_details` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `return_orders`
--

DROP TABLE IF EXISTS `return_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `amount` double DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `bank_account_number` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `reason` longtext NOT NULL,
  `reject_note` longtext DEFAULT NULL,
  `return_address` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_orders_order_id_foreign` (`order_id`),
  KEY `return_orders_shop_id_foreign` (`shop_id`),
  KEY `return_orders_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_orders`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `return_orders` WRITE;
/*!40000 ALTER TABLE `return_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_orders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `rating` double NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_customer_id_foreign` (`customer_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_shop_id_foreign` (`shop_id`),
  KEY `reviews_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `is_shop` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'root','web',0,'2026-08-03 08:41:07','2026-08-03 08:41:07'),
(2,'admin','web',0,'2026-08-03 08:41:08','2026-08-03 08:41:08'),
(3,'shop','web',0,'2026-08-03 08:41:08','2026-08-03 08:41:08'),
(4,'customer','web',0,'2026-08-03 08:41:08','2026-08-03 08:41:08'),
(5,'visitor','web',0,'2026-08-03 08:41:08','2026-08-03 08:41:08'),
(6,'driver','web',0,'2026-08-03 08:41:08','2026-08-03 08:41:08'),
(7,'supplier','web',0,'2026-08-03 08:41:08','2026-08-03 08:41:08');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `s_m_s_configs`
--

DROP TABLE IF EXISTS `s_m_s_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `s_m_s_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `provider` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_m_s_configs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `s_m_s_configs` WRITE;
/*!40000 ALTER TABLE `s_m_s_configs` DISABLE KEYS */;
/*!40000 ALTER TABLE `s_m_s_configs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `shop_categories`
--

DROP TABLE IF EXISTS `shop_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_categories` (
  `shop_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  KEY `shop_categories_shop_id_foreign` (`shop_id`),
  KEY `shop_categories_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `shop_categories` WRITE;
/*!40000 ALTER TABLE `shop_categories` DISABLE KEYS */;
INSERT INTO `shop_categories` VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5);
/*!40000 ALTER TABLE `shop_categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `shop_kyc`
--

DROP TABLE IF EXISTS `shop_kyc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_kyc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `aadhaar_card_id` bigint(20) unsigned DEFAULT NULL,
  `pan_card_id` bigint(20) unsigned DEFAULT NULL,
  `other_documents_id` bigint(20) unsigned DEFAULT NULL,
  `aadhaar_number` varchar(191) DEFAULT NULL,
  `pan_number` varchar(191) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `ifsc` varchar(191) DEFAULT NULL,
  `account_number` varchar(191) DEFAULT NULL,
  `qualification` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_kyc_shop_id_foreign` (`shop_id`),
  KEY `shop_kyc_aadhaar_card_id_foreign` (`aadhaar_card_id`),
  KEY `shop_kyc_pan_card_id_foreign` (`pan_card_id`),
  KEY `shop_kyc_other_documents_id_foreign` (`other_documents_id`),
  CONSTRAINT `shop_kyc_aadhaar_card_id_foreign` FOREIGN KEY (`aadhaar_card_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shop_kyc_other_documents_id_foreign` FOREIGN KEY (`other_documents_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shop_kyc_pan_card_id_foreign` FOREIGN KEY (`pan_card_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shop_kyc_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_kyc`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `shop_kyc` WRITE;
/*!40000 ALTER TABLE `shop_kyc` DISABLE KEYS */;
INSERT INTO `shop_kyc` VALUES
(1,1,NULL,NULL,NULL,'','',NULL,'',NULL,'M.A.','2026-08-06 21:38:39','2026-08-06 21:38:39'),
(2,6,71,72,NULL,'811790201997','AGXPV2367M','SBI','SBIN0031032','52019529528','MA','2026-08-08 19:15:25','2026-08-08 19:15:25'),
(3,7,77,78,79,'826576131949','ACPPS5358G','SBI','SBIN0005461','10416557692','MA','2026-08-09 20:39:38','2026-08-09 20:39:38'),
(4,8,83,84,NULL,'256682327753','BJOPM9918P','KATAK MAHINDRA','KKBK0005207','8045661910','MA','2026-08-10 20:22:55','2026-08-10 20:22:55'),
(5,9,88,89,NULL,'124525143624','AGXPV2367M','SBI','SBIN0031032','52019529528','MA','2026-08-13 21:50:03','2026-08-13 21:50:03'),
(6,10,93,94,NULL,'321425876541','AGXPV2133M','SBI','SBIN0031032','52019529528','BCA','2026-08-15 21:23:19','2026-08-15 21:23:19'),
(7,11,98,99,100,'441464047985','DXOPD5170C','BANK OF INDIA','BKID0009969','996910110019199','MCA/ACTOR','2026-08-15 21:47:05','2026-08-15 21:47:05'),
(8,12,104,105,NULL,'243527971753','AACPO2973N','SBI','SBIN0031032','52019529528','M.TEC.','2026-08-15 22:05:49','2026-08-15 22:05:49'),
(9,13,109,110,NULL,'828959339321','AGXPV9910C','SBI','SBIN0031032','52019529528','BA','2026-08-17 20:14:20','2026-08-17 20:14:20');
/*!40000 ALTER TABLE `shop_kyc` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `shop_monthly_payouts`
--

DROP TABLE IF EXISTS `shop_monthly_payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_monthly_payouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `personal_sales` decimal(15,2) NOT NULL,
  `group_sales` decimal(15,2) NOT NULL,
  `group_size` int(10) unsigned NOT NULL,
  `level` tinyint(3) unsigned DEFAULT NULL,
  `phase1_amount` decimal(15,2) NOT NULL,
  `phase2_amount` decimal(15,2) NOT NULL,
  `total_payout` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_monthly_payouts_shop_id_year_month_unique` (`shop_id`,`year`,`month`),
  CONSTRAINT `shop_monthly_payouts_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_monthly_payouts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `shop_monthly_payouts` WRITE;
/*!40000 ALTER TABLE `shop_monthly_payouts` DISABLE KEYS */;
INSERT INTO `shop_monthly_payouts` VALUES
(1,1,2026,7,0.00,0.00,4,NULL,0.00,0.00,0.00,'2026-08-03 21:40:40','2026-08-03 21:40:40'),
(2,2,2026,7,0.00,0.00,2,NULL,0.00,0.00,0.00,'2026-08-03 21:40:40','2026-08-03 21:40:40'),
(4,5,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-03 21:40:40','2026-08-03 21:40:40'),
(5,4,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-14 21:31:08','2026-08-14 21:31:08'),
(6,6,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-14 21:31:08','2026-08-14 21:31:08'),
(7,7,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-14 21:31:08','2026-08-14 21:31:08'),
(8,8,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-14 21:31:08','2026-08-14 21:31:08'),
(9,9,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-14 21:31:08','2026-08-14 21:31:08'),
(10,10,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-16 09:13:40','2026-08-16 09:13:40'),
(11,11,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-16 09:13:40','2026-08-16 09:13:40'),
(12,12,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-16 09:13:40','2026-08-16 09:13:40');
/*!40000 ALTER TABLE `shop_monthly_payouts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `shop_user`
--

DROP TABLE IF EXISTS `shop_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_user_shop_id_foreign` (`shop_id`),
  KEY `shop_user_user_id_foreign` (`user_id`),
  KEY `shop_user_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_user`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `shop_user` WRITE;
/*!40000 ALTER TABLE `shop_user` DISABLE KEYS */;
INSERT INTO `shop_user` VALUES
(1,4,9,NULL,'2026-08-13 18:56:07','2026-08-13 18:56:07'),
(2,6,9,NULL,'2026-08-13 19:05:37','2026-08-13 19:05:37'),
(3,4,10,NULL,'2026-08-13 19:56:31','2026-08-13 19:56:31'),
(4,1,10,NULL,'2026-08-13 19:57:18','2026-08-13 19:57:18'),
(5,4,3,NULL,'2026-08-16 20:19:45','2026-08-16 20:19:45');
/*!40000 ALTER TABLE `shop_user` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `shop_user_chats`
--

DROP TABLE IF EXISTS `shop_user_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_user_chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) NOT NULL,
  `message` text DEFAULT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_user_chats_shop_user_id_foreign` (`shop_user_id`),
  KEY `shop_user_chats_product_id_foreign` (`product_id`),
  KEY `shop_user_chats_shop_id_foreign` (`shop_id`),
  KEY `shop_user_chats_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_user_chats`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `shop_user_chats` WRITE;
/*!40000 ALTER TABLE `shop_user_chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_user_chats` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_shop_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `banner_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_charge` double DEFAULT 0,
  `min_order_amount` double NOT NULL DEFAULT 0,
  `prefix` varchar(191) NOT NULL DEFAULT 'RC',
  `address` varchar(191) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `off_day` varchar(191) DEFAULT NULL,
  `estimated_delivery_time` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_online` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shops_user_id_foreign` (`user_id`),
  KEY `shops_logo_id_foreign` (`logo_id`),
  KEY `shops_banner_id_foreign` (`banner_id`),
  KEY `shops_warehouse_id_foreign` (`warehouse_id`),
  KEY `shops_parent_shop_id_foreign` (`parent_shop_id`),
  CONSTRAINT `shops_parent_shop_id_foreign` FOREIGN KEY (`parent_shop_id`) REFERENCES `shops` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
INSERT INTO `shops` VALUES
(1,'Main Janmitram Shop',1,NULL,1,1,2,0,1,'RC','shiv mandir ke samne badharna road harmada jaipur','26.9985869','75.7680702','05:00:10','22:44:21',NULL,'3-4 days',1,'JANMITRAM -MART','2026-08-03 08:41:11','2026-08-14 21:16:42',NULL,NULL),
(2,'janmitram- Sanganer',3,1,2,7,8,0,0,'RC','417 Sanganer thana , Jaipur','26.7922602','75.8145905',NULL,NULL,NULL,NULL,1,NULL,'2026-08-03 11:44:54','2026-08-08 19:50:07',NULL,NULL),
(4,'janmitram -pratap nagar jpr',5,1,3,13,14,0,0,'RC','NRI CIRCLE PRATAP NAGAR JPR','26.8161046','75.8109856',NULL,NULL,NULL,NULL,1,'LEARN AND EARN','2026-08-03 15:28:58','2026-08-16 19:50:16',NULL,NULL),
(5,'janmitram - mumbai',6,1,1,16,17,0,0,'RC','navi mumbai maharastra','18.9899017','72.8942871',NULL,NULL,NULL,NULL,1,'LEARN AND EARN','2026-08-03 21:32:31','2026-08-08 20:24:10',NULL,NULL),
(6,'Janmitram -jagatpura jpr',7,1,1,69,70,0,0,'RC','Near jecrc jagatpura jaipur','26.823309016722888','75.86662835350454',NULL,NULL,NULL,NULL,1,'शुद्ध ,सात्विक, प्राकृतिक उत्पाद','2026-08-08 19:15:25','2026-08-11 21:53:43',NULL,NULL),
(7,'Janmitram-Nagpur',8,1,1,75,76,0,0,'RC','Jai bhawani nagar VTC: Nagpur MH','21.1480385','79.0816498',NULL,NULL,NULL,NULL,1,'SABKA MITRA -JANMITRA','2026-08-09 20:39:38','2026-08-10 18:41:13',NULL,NULL),
(8,'janmitram- Lucknow',9,1,1,81,82,0,0,'RC','Gomti nagar lucknow','26.6431626','80.9252930',NULL,NULL,NULL,NULL,1,NULL,'2026-08-10 20:22:55','2026-08-10 20:22:55',NULL,NULL),
(9,'janmitram Ambabari',11,1,1,86,87,0,0,'RC','rail vihar vidyadhar nagar jaipur','26.9607854','75.7748097',NULL,NULL,NULL,NULL,1,'sabka mitra janmitra','2026-08-13 21:50:03','2026-08-16 18:36:34',NULL,NULL),
(10,'janmitram -vidyadhar nagar',12,1,1,91,92,0,0,'RC','stadium ke pas vidyadhar nagar jpr','26.9600005','75.7747006',NULL,NULL,NULL,NULL,1,'SABKA MITRA- JANMITRA','2026-08-15 21:23:19','2026-08-15 21:24:03',NULL,NULL),
(11,'janmitram -mumbai (andheri west)',13,1,1,96,97,0,0,'RC','bersva andheri west','18.9695855','72.8193152',NULL,NULL,NULL,NULL,1,'Sabka mitra- janmitra','2026-08-15 21:47:05','2026-08-15 21:48:58',NULL,NULL),
(12,'janmitram - Rachi',14,1,1,102,103,0,0,'RC','rachi jharkhand','23.3108874','85.3692627',NULL,NULL,NULL,NULL,1,'SABKA MITRA-JANMITRA','2026-08-15 22:05:49','2026-08-15 22:05:49',NULL,NULL),
(13,'Janmitram Satna m.p.',15,10,1,107,108,0,0,'RC','kathaha maihar','24.3819507','81.0611629',NULL,NULL,NULL,NULL,1,'SABKA MITRA-JANMITRA','2026-08-17 20:14:20','2026-08-17 20:14:20',NULL,NULL);
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sizes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sizes_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sizes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sizes` WRITE;
/*!40000 ALTER TABLE `sizes` DISABLE KEYS */;
INSERT INTO `sizes` VALUES
(1,'Small',NULL,1,1,'2026-08-03 11:17:58','2026-08-05 21:59:00'),
(2,'Medium',NULL,1,1,'2026-08-03 11:18:07','2026-08-05 21:59:11'),
(3,'Large',NULL,1,1,'2026-08-03 11:18:16','2026-08-05 21:59:29');
/*!40000 ALTER TABLE `sizes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `social_auths`
--

DROP TABLE IF EXISTS `social_auths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_auths` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `client_id` varchar(191) DEFAULT NULL,
  `client_secret` varchar(191) DEFAULT NULL,
  `redirect` varchar(191) DEFAULT NULL,
  `provider` varchar(191) DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_auths`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `social_auths` WRITE;
/*!40000 ALTER TABLE `social_auths` DISABLE KEYS */;
INSERT INTO `social_auths` VALUES
(1,'Google','','','postmessage','google','assets/social/google.svg',0,NULL,NULL),
(2,'Facebook','','','','facebook','assets/social/facebook.svg',0,NULL,NULL),
(3,'Apple','com.janmitram.web','',NULL,'apple','assets/social/apple.svg',0,NULL,NULL);
/*!40000 ALTER TABLE `social_auths` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `social_links`
--

DROP TABLE IF EXISTS `social_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_links`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `social_links` WRITE;
/*!40000 ALTER TABLE `social_links` DISABLE KEYS */;
INSERT INTO `social_links` VALUES
(1,'Facebook','/assets/social/facebook.png',NULL,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(2,'LinkedIn','/assets/social/linkedin.png','https://www.linkedin.com/company/janmitram',1,'2026-08-03 08:41:09','2026-08-11 21:02:50'),
(3,'Instagram','/assets/social/instagram.png',NULL,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(4,'YouTube','/assets/social/youtube.png','https://www.youtube.com/@janmitram',1,'2026-08-03 08:41:09','2026-08-11 21:02:34'),
(5,'WhatsApp','/assets/social/whatsapp.png',NULL,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(6,'Twitter','/assets/social/twitter.png',NULL,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(7,'Telegram','/assets/social/telegram.png',NULL,1,'2026-08-03 08:41:09','2026-08-03 08:41:09'),
(8,'Google Plus','/assets/social/google-plus.png',NULL,1,'2026-08-03 08:41:09','2026-08-03 08:41:09');
/*!40000 ALTER TABLE `social_links` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `stock_ledgers`
--

DROP TABLE IF EXISTS `stock_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_ledgers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `to_warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `reference_type` varchar(64) NOT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_ledgers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `stock_ledgers_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `stock_ledgers_product_id_foreign` (`product_id`),
  KEY `stock_ledgers_color_id_foreign` (`color_id`),
  KEY `stock_ledgers_size_id_foreign` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_ledgers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `stock_ledgers` WRITE;
/*!40000 ALTER TABLE `stock_ledgers` DISABLE KEYS */;
INSERT INTO `stock_ledgers` VALUES
(1,NULL,1,1,1,1,500,'admin_addition',NULL,'500','2026-08-03 11:26:09','2026-08-03 11:26:09'),
(2,1,2,1,1,1,100,'warehouse_transfer',1,'100','2026-08-03 11:26:39','2026-08-03 11:26:39'),
(3,2,NULL,1,NULL,NULL,50,'shop_request',1,'Fulfilled stock request #1 for shop #2','2026-08-03 11:50:48','2026-08-03 11:50:48'),
(4,NULL,1,3,3,1,500,'admin_addition',NULL,NULL,'2026-08-05 22:03:47','2026-08-05 22:03:47'),
(5,NULL,1,4,4,1,500,'admin_addition',NULL,NULL,'2026-08-05 22:04:33','2026-08-05 22:04:33'),
(6,1,NULL,3,NULL,NULL,100,'shop_request',2,'Fulfilled stock request #2 for shop #4','2026-08-06 19:12:36','2026-08-06 19:12:36'),
(7,1,NULL,1,NULL,NULL,98,'shop_request',3,'Fulfilled stock request #3 for shop #4','2026-08-06 19:25:21','2026-08-06 19:25:21'),
(8,1,NULL,1,NULL,NULL,1,'shop_request',4,'Fulfilled stock request #4 for shop #4','2026-08-06 19:28:55','2026-08-06 19:28:55'),
(9,NULL,1,7,NULL,1,500,'admin_addition',NULL,NULL,'2026-08-06 20:45:54','2026-08-06 20:45:54'),
(10,NULL,1,8,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:17:07','2026-08-06 21:17:07'),
(11,NULL,1,9,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:17:54','2026-08-06 21:17:54'),
(12,NULL,1,10,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:18:17','2026-08-06 21:18:17'),
(13,NULL,1,11,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:18:34','2026-08-06 21:18:34'),
(14,NULL,1,12,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:18:46','2026-08-06 21:18:46'),
(15,NULL,1,13,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:19:01','2026-08-06 21:19:01'),
(16,NULL,1,14,NULL,NULL,500,'admin_addition',NULL,NULL,'2026-08-06 21:19:31','2026-08-06 21:19:31'),
(17,NULL,1,1,NULL,NULL,50,'admin_addition',NULL,NULL,'2026-08-06 21:20:49','2026-08-06 21:20:49'),
(18,1,NULL,14,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:40','2026-08-08 20:40:40'),
(19,1,NULL,11,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:40','2026-08-08 20:40:40'),
(20,1,NULL,13,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:40','2026-08-08 20:40:40'),
(21,1,NULL,12,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(22,1,NULL,3,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(23,1,NULL,7,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(24,1,NULL,1,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(25,1,NULL,4,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(26,1,NULL,9,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(27,1,NULL,10,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(28,1,NULL,8,NULL,NULL,10,'shop_request',6,'Fulfilled stock request #6 for shop #5','2026-08-08 20:40:41','2026-08-08 20:40:41'),
(29,1,NULL,14,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(30,1,NULL,11,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(31,1,NULL,13,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(32,1,NULL,12,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(33,1,NULL,3,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(34,1,NULL,7,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(35,1,NULL,1,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(36,1,NULL,4,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(37,1,NULL,9,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(38,1,NULL,10,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(39,1,NULL,8,NULL,NULL,20,'shop_request',7,'Fulfilled stock request #7 for shop #7','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(40,1,NULL,14,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(41,1,NULL,11,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(42,1,NULL,13,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(43,1,NULL,12,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(44,1,NULL,3,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(45,1,NULL,7,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(46,1,NULL,1,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(47,1,NULL,4,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(48,1,NULL,9,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(49,1,NULL,10,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(50,1,NULL,8,NULL,NULL,20,'shop_request',8,'Fulfilled stock request #8 for shop #8','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(51,1,NULL,1,NULL,NULL,7,'shop_request',5,'Fulfilled stock request #5 for shop #4','2026-08-10 20:49:55','2026-08-10 20:49:55'),
(52,1,NULL,14,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(53,1,NULL,13,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(54,1,NULL,12,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(55,1,NULL,11,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(56,1,NULL,10,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(57,1,NULL,9,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(58,1,NULL,8,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(59,1,NULL,7,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(60,1,NULL,4,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(61,1,NULL,3,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(62,1,NULL,1,NULL,NULL,20,'shop_request',9,'Fulfilled stock request #9 for shop #6','2026-08-12 20:28:39','2026-08-12 20:28:39'),
(63,1,2,3,3,1,100,'warehouse_transfer',2,NULL,'2026-08-12 20:38:12','2026-08-12 20:38:12'),
(64,1,2,4,4,1,100,'warehouse_transfer',3,NULL,'2026-08-12 20:39:23','2026-08-12 20:39:23'),
(65,1,2,7,NULL,1,100,'warehouse_transfer',4,NULL,'2026-08-12 20:42:24','2026-08-12 20:42:24'),
(66,1,2,11,NULL,NULL,100,'warehouse_transfer',5,NULL,'2026-08-12 20:43:57','2026-08-12 20:43:57'),
(67,NULL,2,1,NULL,NULL,50,'admin_addition',NULL,NULL,'2026-08-12 20:48:11','2026-08-12 20:48:11'),
(68,1,NULL,14,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:16','2026-08-12 21:27:16'),
(69,1,NULL,11,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(70,1,NULL,13,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(71,1,NULL,12,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(72,1,NULL,3,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(73,1,NULL,7,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(74,1,NULL,1,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(75,1,NULL,4,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(76,1,NULL,9,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(77,1,NULL,10,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(78,1,NULL,8,NULL,NULL,20,'shop_request',10,'Fulfilled stock request #10 for shop #4','2026-08-12 21:27:17','2026-08-12 21:27:17'),
(79,1,NULL,14,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(80,1,NULL,11,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(81,1,NULL,13,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(82,1,NULL,12,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(83,1,NULL,3,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(84,1,NULL,7,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(85,1,NULL,1,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(86,1,NULL,4,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(87,1,NULL,9,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(88,1,NULL,10,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(89,1,NULL,8,NULL,NULL,20,'shop_request',11,'Fulfilled stock request #11 for shop #9','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(90,1,NULL,14,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(91,1,NULL,11,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(92,1,NULL,13,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(93,1,NULL,12,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(94,1,NULL,3,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(95,1,NULL,7,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(96,1,NULL,1,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(97,1,NULL,4,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(98,1,NULL,9,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(99,1,NULL,10,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(100,1,NULL,8,NULL,NULL,20,'shop_request',12,'Fulfilled stock request #12 for shop #11','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(101,1,3,3,3,1,100,'warehouse_transfer',6,NULL,'2026-08-16 19:50:58','2026-08-16 19:50:58'),
(102,3,NULL,3,NULL,NULL,50,'shop_request',13,'Fulfilled stock request #13 for shop #4','2026-08-16 19:51:46','2026-08-16 19:51:46'),
(103,1,NULL,14,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(104,1,NULL,11,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(105,1,NULL,13,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(106,1,NULL,12,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(107,1,NULL,3,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(108,1,NULL,7,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(109,1,NULL,1,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(110,1,NULL,4,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(111,1,NULL,9,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(112,1,NULL,10,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(113,1,NULL,8,NULL,NULL,20,'shop_request',14,'Fulfilled stock request #14 for shop #13','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(114,1,NULL,14,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(115,1,NULL,11,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(116,1,NULL,13,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(117,1,NULL,12,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(118,1,NULL,3,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(119,1,NULL,7,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(120,1,NULL,1,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(121,1,NULL,4,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(122,1,NULL,9,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(123,1,NULL,10,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(124,1,NULL,8,NULL,NULL,20,'shop_request',15,'Fulfilled stock request #15 for shop #12','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(125,1,NULL,14,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(126,1,NULL,11,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(127,1,NULL,13,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(128,1,NULL,12,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(129,1,NULL,3,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(130,1,NULL,7,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(131,1,NULL,1,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(132,1,NULL,4,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(133,1,NULL,9,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(134,1,NULL,10,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55'),
(135,1,NULL,8,NULL,NULL,20,'shop_request',16,'Fulfilled stock request #16 for shop #10','2026-08-17 20:26:55','2026-08-17 20:26:55');
/*!40000 ALTER TABLE `stock_ledgers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `stock_request_items`
--

DROP TABLE IF EXISTS `stock_request_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_request_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_request_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_request_items_stock_request_id_foreign` (`stock_request_id`),
  KEY `stock_request_items_product_id_foreign` (`product_id`),
  KEY `stock_request_items_color_id_foreign` (`color_id`),
  KEY `stock_request_items_size_id_foreign` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_request_items`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `stock_request_items` WRITE;
/*!40000 ALTER TABLE `stock_request_items` DISABLE KEYS */;
INSERT INTO `stock_request_items` VALUES
(1,1,1,NULL,NULL,50,'2026-08-03 11:50:34','2026-08-03 11:50:34'),
(2,2,3,NULL,NULL,100,'2026-08-06 19:11:33','2026-08-06 19:11:33'),
(3,3,1,NULL,NULL,98,'2026-08-06 19:25:03','2026-08-06 19:25:03'),
(4,4,1,NULL,NULL,1,'2026-08-06 19:28:32','2026-08-06 19:28:32'),
(5,5,1,NULL,NULL,7,'2026-08-06 19:29:57','2026-08-06 19:29:57'),
(6,6,14,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(7,6,11,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(8,6,13,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(9,6,12,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(10,6,3,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(11,6,7,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(12,6,1,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(13,6,4,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(14,6,9,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(15,6,10,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(16,6,8,NULL,NULL,10,'2026-08-08 20:40:40','2026-08-08 20:40:40'),
(17,7,14,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(18,7,11,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(19,7,13,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(20,7,12,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(21,7,3,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(22,7,7,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(23,7,1,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(24,7,4,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(25,7,9,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(26,7,10,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(27,7,8,NULL,NULL,20,'2026-08-09 20:58:09','2026-08-09 20:58:09'),
(28,8,14,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(29,8,11,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(30,8,13,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(31,8,12,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(32,8,3,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(33,8,7,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(34,8,1,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(35,8,4,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(36,8,9,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(37,8,10,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(38,8,8,NULL,NULL,20,'2026-08-10 20:25:23','2026-08-10 20:25:23'),
(39,9,14,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(40,9,13,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(41,9,12,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(42,9,11,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(43,9,10,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(44,9,9,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(45,9,8,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(46,9,7,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(47,9,4,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(48,9,3,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(49,9,1,NULL,NULL,20,'2026-08-12 20:27:08','2026-08-12 20:27:08'),
(50,10,14,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(51,10,11,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(52,10,13,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(53,10,12,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(54,10,3,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(55,10,7,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(56,10,1,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(57,10,4,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(58,10,9,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(59,10,10,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(60,10,8,NULL,NULL,20,'2026-08-12 21:27:16','2026-08-12 21:27:16'),
(61,11,14,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(62,11,11,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(63,11,13,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(64,11,12,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(65,11,3,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(66,11,7,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(67,11,1,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(68,11,4,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(69,11,9,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(70,11,10,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(71,11,8,NULL,NULL,20,'2026-08-16 18:26:59','2026-08-16 18:26:59'),
(72,12,14,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(73,12,11,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(74,12,13,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(75,12,12,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(76,12,3,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(77,12,7,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(78,12,1,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(79,12,4,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(80,12,9,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(81,12,10,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(82,12,8,NULL,NULL,20,'2026-08-16 18:30:44','2026-08-16 18:30:44'),
(83,13,3,NULL,NULL,50,'2026-08-16 19:51:46','2026-08-16 19:51:46'),
(84,14,14,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(85,14,11,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(86,14,13,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(87,14,12,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(88,14,3,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(89,14,7,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(90,14,1,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(91,14,4,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(92,14,9,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(93,14,10,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(94,14,8,NULL,NULL,20,'2026-08-17 20:23:01','2026-08-17 20:23:01'),
(95,15,14,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(96,15,11,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(97,15,13,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(98,15,12,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(99,15,3,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(100,15,7,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(101,15,1,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(102,15,4,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(103,15,9,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(104,15,10,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(105,15,8,NULL,NULL,20,'2026-08-17 20:24:24','2026-08-17 20:24:24'),
(106,16,14,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(107,16,11,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(108,16,13,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(109,16,12,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(110,16,3,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(111,16,7,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(112,16,1,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(113,16,4,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(114,16,9,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(115,16,10,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55'),
(116,16,8,NULL,NULL,20,'2026-08-17 20:26:55','2026-08-17 20:26:55');
/*!40000 ALTER TABLE `stock_request_items` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `stock_requests`
--

DROP TABLE IF EXISTS `stock_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_requests_shop_id_foreign` (`shop_id`),
  KEY `stock_requests_warehouse_id_foreign` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_requests`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `stock_requests` WRITE;
/*!40000 ALTER TABLE `stock_requests` DISABLE KEYS */;
INSERT INTO `stock_requests` VALUES
(1,2,2,'completed','50','2026-08-03 11:50:34','2026-08-03 11:50:48'),
(2,4,1,'completed',NULL,'2026-08-06 19:11:33','2026-08-06 19:12:36'),
(3,4,1,'completed',NULL,'2026-08-06 19:25:03','2026-08-06 19:25:21'),
(4,4,1,'completed',NULL,'2026-08-06 19:28:32','2026-08-06 19:28:55'),
(5,4,1,'completed',NULL,'2026-08-06 19:29:57','2026-08-10 20:49:55'),
(6,5,1,'completed','[inventory-assignment]','2026-08-08 20:40:40','2026-08-08 20:40:41'),
(7,7,1,'completed','[inventory-assignment]','2026-08-09 20:58:09','2026-08-09 20:58:09'),
(8,8,1,'completed','[inventory-assignment]','2026-08-10 20:25:23','2026-08-10 20:25:23'),
(9,6,1,'completed',NULL,'2026-08-12 20:27:08','2026-08-12 20:28:39'),
(10,4,1,'completed','[inventory-assignment]','2026-08-12 21:27:16','2026-08-12 21:27:17'),
(11,9,1,'completed','[inventory-assignment]','2026-08-16 18:26:59','2026-08-16 18:26:59'),
(12,11,1,'completed','[inventory-assignment]','2026-08-16 18:30:44','2026-08-16 18:30:44'),
(13,4,3,'completed','ok<br>[inventory-assignment]','2026-08-16 19:51:46','2026-08-16 19:51:46'),
(14,13,1,'completed','[inventory-assignment]','2026-08-17 20:23:01','2026-08-17 20:23:01'),
(15,12,1,'completed','[inventory-assignment]','2026-08-17 20:24:24','2026-08-17 20:24:24'),
(16,10,1,'completed','[inventory-assignment]','2026-08-17 20:26:55','2026-08-17 20:26:55');
/*!40000 ALTER TABLE `stock_requests` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_categories_media_id_foreign` (`media_id`),
  KEY `sub_categories_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sub_categories` WRITE;
/*!40000 ALTER TABLE `sub_categories` DISABLE KEYS */;
INSERT INTO `sub_categories` VALUES
(1,51,1,'Spices',NULL,'spices',1,'2026-08-06 20:04:04','2026-08-06 20:04:04'),
(2,52,1,'Pulses',NULL,'pulses',1,'2026-08-06 20:06:30','2026-08-06 20:06:30'),
(3,53,1,'Edible Oil',NULL,'edible-oil',1,'2026-08-06 20:11:32','2026-08-06 20:12:32'),
(4,54,1,'Rice',NULL,'rice',1,'2026-08-06 20:14:25','2026-08-06 20:14:25'),
(5,55,1,'Tulsi chay',NULL,'tulsi-chay',1,'2026-08-06 20:31:58','2026-08-06 20:31:58'),
(6,56,1,'Dry Fruits',NULL,'dry-fruits',1,'2026-08-06 20:34:04','2026-08-06 20:34:04'),
(7,61,1,'ATTA',NULL,'atta',1,'2026-08-06 21:01:00','2026-08-06 21:01:00'),
(8,63,1,'SUGAR',NULL,'sugar',1,'2026-08-06 21:07:50','2026-08-06 21:07:50');
/*!40000 ALTER TABLE `sub_categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `support_ticket_attachments`
--

DROP TABLE IF EXISTS `support_ticket_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_ticket_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_attachments_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `support_ticket_attachments_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_attachments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `support_ticket_attachments` WRITE;
/*!40000 ALTER TABLE `support_ticket_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_attachments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `support_ticket_messages`
--

DROP TABLE IF EXISTS `support_ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_ticket_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `is_highlighted` tinyint(1) NOT NULL DEFAULT 0,
  `message` longtext NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_messages_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `support_ticket_messages_sender_id_foreign` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_messages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `support_ticket_messages` WRITE;
/*!40000 ALTER TABLE `support_ticket_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_messages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ticket_number` varchar(191) NOT NULL,
  `order_number` varchar(191) DEFAULT NULL,
  `issue_type` varchar(191) DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `ticket_start` timestamp NULL DEFAULT NULL,
  `ticket_end` timestamp NULL DEFAULT NULL,
  `user_chat` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `supports`
--

DROP TABLE IF EXISTS `supports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(191) DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supports_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supports`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `supports` WRITE;
/*!40000 ALTER TABLE `supports` DISABLE KEYS */;
/*!40000 ALTER TABLE `supports` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES
(1,'SABKA MITRA JANMITRA','sabka-mitra-janmitra','2026-08-08 20:50:31','2026-08-08 20:50:31');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `theme_colors`
--

DROP TABLE IF EXISTS `theme_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `theme_colors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `primary` varchar(191) NOT NULL,
  `secondary` varchar(191) NOT NULL,
  `variant_50` varchar(191) DEFAULT NULL,
  `variant_100` varchar(191) DEFAULT NULL,
  `variant_200` varchar(191) DEFAULT NULL,
  `variant_300` varchar(191) DEFAULT NULL,
  `variant_400` varchar(191) DEFAULT NULL,
  `variant_500` varchar(191) DEFAULT NULL,
  `variant_600` varchar(191) DEFAULT NULL,
  `variant_700` varchar(191) DEFAULT NULL,
  `variant_800` varchar(191) DEFAULT NULL,
  `variant_900` varchar(191) DEFAULT NULL,
  `variant_950` varchar(191) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme_colors`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `theme_colors` WRITE;
/*!40000 ALTER TABLE `theme_colors` DISABLE KEYS */;
INSERT INTO `theme_colors` VALUES
(1,'#ee9644','#fceada','#fdf5ec','#fceada','#f8d5b4','#f5c08f','#f1ab69','#ee9644','#d6873d','#be7836','#a76930','#8f5a29','#774b22',1,'2026-08-03 08:41:09','2026-08-11 20:14:37'),
(2,'#a855f7','#f3e8ff','#faf5ff','#f3e8ff','#e9d5ff','#d8b4fe','#c084fc','#a855f7','#9333ea','#7e22ce','#6b21a8','#581c87','#3b0764',0,NULL,NULL),
(3,'#8b5cf6','#ede9fe','#f5f3ff','#ede9fe','#ddd6fe','#c4b5fd','#a78bfa','#8b5cf6','#7c3aed','#6d28d9','#5b21b6','#4c1d95','#2e1065',0,NULL,NULL);
/*!40000 ALTER TABLE `theme_colors` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `ticket_issue_types`
--

DROP TABLE IF EXISTS `ticket_issue_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_issue_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_issue_types`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `ticket_issue_types` WRITE;
/*!40000 ALTER TABLE `ticket_issue_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_issue_types` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint(20) unsigned NOT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `is_commission` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(191) NOT NULL DEFAULT 'credit',
  `transaction_id` varchar(191) DEFAULT NULL,
  `purpose` varchar(191) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_wallet_id_foreign` (`wallet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `translate_utilities`
--

DROP TABLE IF EXISTS `translate_utilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `translate_utilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `sub_category_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `lang` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `translate_utilities_category_id_foreign` (`category_id`),
  KEY `translate_utilities_sub_category_id_foreign` (`sub_category_id`),
  KEY `translate_utilities_brand_id_foreign` (`brand_id`),
  KEY `translate_utilities_color_id_foreign` (`color_id`),
  KEY `translate_utilities_size_id_foreign` (`size_id`),
  KEY `translate_utilities_unit_id_foreign` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translate_utilities`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `translate_utilities` WRITE;
/*!40000 ALTER TABLE `translate_utilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `translate_utilities` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES
(1,'1 KG',1,1,'2026-08-03 11:18:34','2026-08-03 11:18:34'),
(2,'500 GM',1,1,'2026-08-03 11:18:47','2026-08-03 11:18:47'),
(3,'200GM',1,1,'2026-08-03 11:19:03','2026-08-05 21:24:54'),
(4,'1 Ltr',1,1,'2026-08-05 21:27:31','2026-08-06 20:50:31'),
(5,'5ltr',1,1,'2026-08-05 21:28:33','2026-08-05 21:28:33'),
(6,'15ltr',1,1,'2026-08-05 21:28:49','2026-08-05 21:28:49'),
(7,'200ml',1,1,'2026-08-05 21:29:27','2026-08-05 21:29:27'),
(8,'10kg',1,1,'2026-08-05 21:29:52','2026-08-05 21:29:52'),
(9,'5kg',1,1,'2026-08-05 21:30:06','2026-08-05 21:30:06');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `user_non_permissions`
--

DROP TABLE IF EXISTS `user_non_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_non_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_non_permissions_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_non_permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user_non_permissions` WRITE;
/*!40000 ALTER TABLE `user_non_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_non_permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `driving_lience` varchar(191) DEFAULT NULL,
  `vehicle_type` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `phone_code` varchar(191) DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `auth_type` varchar(191) DEFAULT NULL,
  `auth_id` varchar(191) DEFAULT NULL,
  `last_online` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_media_id_foreign` (`media_id`),
  KEY `users_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'DR. APARNA KUMARI',NULL,'9571523516','root@janmitram.com',67,'$2y$12$vDLL.lFVOMvWEb.SIY.zYOGMdsylj4BHUUnU.d1UMcHvq43rtHB0y','female','1980-08-15',1,'2026-08-03 08:41:09','2026-08-03 08:41:09',NULL,NULL,NULL,'2026-08-03 08:41:09','2026-08-12 21:22:52',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-06 18:41:22'),
(3,'dukanuser',NULL,'9549803319','chg3@gmail.com',6,'$2y$12$CPHl5rhHLq9GRggF7r0jtuuruwuYEyYfGoazlCaWsAEYwRc8O/VZa','male','1991-10-16',1,NULL,NULL,NULL,NULL,NULL,'2026-08-03 11:44:54','2026-08-16 20:59:36',NULL,'India','91',NULL,NULL,NULL,'2026-08-16 20:59:36'),
(4,'Shubham','Gaur','9549803300','shg@janmitram.com',9,'$2y$12$ytIdZYyfw3yg4xl./WuEyu5HtRyUwPBttZ4qQL6I5Gbb6Qzi.6HbS','male',NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-08-03 11:48:51','2026-08-06 18:33:49','2026-08-06 18:33:49',NULL,NULL,NULL,NULL,NULL,NULL),
(5,'Archana','ojha','7004113699','archana@janmitram.com',12,'$2y$12$OqEIoEjY70CwPdXLw5CvA.wIftWhG0bdcnoiDdjZibgjGmkFgmBeq','female',NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-08-03 15:28:58','2026-08-08 19:43:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(6,'Dinesh bhai','Sharma','9920996281','dinesh@janmitram.com',15,'$2y$12$EFdZX2Qt7YvlnCcfZg5O6.sWi7O7GLHaUyJd/sMBorGdirhwPOCrG','male',NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-08-03 21:32:31','2026-08-08 20:24:10',NULL,'India','91',NULL,NULL,NULL,'2026-08-05 20:31:49'),
(7,'Manoj','kumar','8209514177','manoj@janmitram.com',68,'$2y$12$jf0UuQ/C233ctf6zuLlUTesooZnBvso7lerHtytsUT4h.4kMjtfeq','male','1977-08-15',1,NULL,NULL,NULL,NULL,NULL,'2026-08-08 19:15:25','2026-08-08 19:15:25',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(8,'Mudrika mohan','Sharma','9372475990','mudrika@janmitram.com',74,'$2y$12$/F6xFE9tKRSqN8P9Efcs5uYFpKkpmkcynVsrWC9LJ0gX6ARzqva2W','male','1968-10-15',1,NULL,NULL,NULL,NULL,NULL,'2026-08-09 20:39:38','2026-08-16 20:52:08',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 20:52:08'),
(9,'Suman','Sharma','9414057690','aditi@janmitram.com',80,'$2y$12$W8I5qayBH6jStwvKCr2lweRrcsA2.X1dfm0AI6Wk1PxuKvBFkujBG','female',NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-08-10 20:22:55','2026-08-15 20:55:54',NULL,'India','91',NULL,NULL,NULL,'2026-08-14 21:13:51'),
(10,'vishnu kanwar',NULL,'7690918898','vishnu@janmitram.com',NULL,'$2y$12$eQ5Bhn6/9aJlzfncj71txuA4DvFfIxB7.Dt10MGRAF91FM1gBTqAm','female','1980-05-15',1,NULL,NULL,NULL,NULL,NULL,'2026-08-13 19:25:50','2026-08-13 20:02:23',NULL,'India','91',NULL,NULL,NULL,'2026-08-13 20:02:23'),
(11,'Aditi','v.','9784338720','aditiv@janmitram.com',85,'$2y$12$VF5hkigW69e2o8.ja9DFnOOFOWWZnBrqgZruz2bvmT0cd5eKNEpZ.','male','2002-09-29',1,NULL,NULL,NULL,NULL,NULL,'2026-08-13 21:50:03','2026-08-14 21:23:32',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 21:23:32'),
(12,'Bhaskar','Anand','8078605615','bhaskar@janmitram.com',90,'$2y$12$t6bbVE.E3ZyvqfXUuDTfI.SSt4tZTxGo5VYsjpYzXESrk/0o4VmFK','male','1997-03-25',1,NULL,NULL,NULL,NULL,NULL,'2026-08-15 21:23:19','2026-08-16 22:02:43',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 22:02:43'),
(13,'Durgesh','v.','8291975810','durgesh@janmitram.com',95,'$2y$12$ubSa6uaHuJhKrqBZeXdzR.IEMRM32nnwlp4Q8rXK5kaLf3QQDrZgO','male','1995-09-16',1,NULL,NULL,NULL,NULL,NULL,'2026-08-15 21:47:05','2026-08-15 21:54:49',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-15 21:54:49'),
(14,'ajay kumar','ojha','9166093926','ajay@janmitram.com',101,'$2y$12$tAhIvKay3sWF5lC64tzPSuU3c/nngymfov2rbeGtO.dmRvca6cp9m','male','1968-01-01',1,NULL,NULL,NULL,NULL,NULL,'2026-08-15 22:05:49','2026-08-15 22:05:49',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(15,'Balendu Prasad','vishwakarma','9755560557','balendu@janmitram.com',106,'$2y$12$oHfFeMXIX/KSiDByNwuyXO1b/WVXyU9skakUMJzVlJeYicFdgb0ku','male','1974-01-02',1,NULL,NULL,NULL,NULL,NULL,'2026-08-17 20:14:20','2026-08-17 20:14:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `vat_taxes`
--

DROP TABLE IF EXISTS `vat_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vat_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) DEFAULT 'order_base',
  `name` varchar(191) DEFAULT NULL,
  `percentage` double NOT NULL DEFAULT 0,
  `deduction` varchar(191) DEFAULT 'exclusive',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vat_taxes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `vat_taxes` WRITE;
/*!40000 ALTER TABLE `vat_taxes` DISABLE KEYS */;
INSERT INTO `vat_taxes` VALUES
(1,'order_base','GST',5,'exclusive',1,0,'2026-08-05 20:49:03','2026-08-05 20:49:03');
/*!40000 ALTER TABLE `vat_taxes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `verify_manages`
--

DROP TABLE IF EXISTS `verify_manages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_manages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `register_otp` tinyint(1) NOT NULL DEFAULT 0,
  `register_otp_type` varchar(191) DEFAULT NULL,
  `forgot_otp` tinyint(1) NOT NULL DEFAULT 1,
  `forgot_otp_type` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone_required` tinyint(1) NOT NULL DEFAULT 1,
  `email_required` tinyint(1) NOT NULL DEFAULT 0,
  `phone_min_length` varchar(191) DEFAULT NULL,
  `phone_max_length` varchar(191) DEFAULT NULL,
  `order_place_account_verify` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verify_manages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `verify_manages` WRITE;
/*!40000 ALTER TABLE `verify_manages` DISABLE KEYS */;
INSERT INTO `verify_manages` VALUES
(1,0,'email',1,'email',NULL,NULL,0,1,'9','16',0);
/*!40000 ALTER TABLE `verify_manages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `verify_otps`
--

DROP TABLE IF EXISTS `verify_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_otps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(191) NOT NULL,
  `otp` varchar(191) NOT NULL,
  `token` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verify_otps`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `verify_otps` WRITE;
/*!40000 ALTER TABLE `verify_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `verify_otps` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `wallets`
--

DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallets`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `wallets` WRITE;
/*!40000 ALTER TABLE `wallets` DISABLE KEYS */;
INSERT INTO `wallets` VALUES
(1,1,0,'2026-08-03 08:41:11','2026-08-03 08:41:11'),
(2,2,0,'2026-08-03 08:41:11','2026-08-03 08:41:11'),
(3,3,0,'2026-08-03 11:44:54','2026-08-03 11:44:54'),
(4,4,0,'2026-08-03 11:48:51','2026-08-03 11:48:51'),
(5,5,0,'2026-08-03 15:28:58','2026-08-03 15:28:58'),
(6,6,0,'2026-08-03 21:32:31','2026-08-03 21:32:31'),
(7,7,0,'2026-08-08 19:15:25','2026-08-08 19:15:25'),
(8,8,0,'2026-08-09 20:39:38','2026-08-09 20:39:38'),
(9,9,0,'2026-08-10 20:22:55','2026-08-10 20:22:55'),
(10,10,0,'2026-08-13 19:25:50','2026-08-13 19:25:50'),
(11,11,0,'2026-08-13 21:50:03','2026-08-13 21:50:03'),
(12,12,0,'2026-08-15 21:23:19','2026-08-15 21:23:19'),
(13,13,0,'2026-08-15 21:47:05','2026-08-15 21:47:05'),
(14,14,0,'2026-08-15 22:05:49','2026-08-15 22:05:49'),
(15,15,0,'2026-08-17 20:14:20','2026-08-17 20:14:20');
/*!40000 ALTER TABLE `wallets` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `warehouse_stock`
--

DROP TABLE IF EXISTS `warehouse_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wh_stock_unique` (`warehouse_id`,`product_id`,`color_id`,`size_id`),
  KEY `warehouse_stock_product_id_foreign` (`product_id`),
  KEY `warehouse_stock_color_id_foreign` (`color_id`),
  KEY `warehouse_stock_size_id_foreign` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_stock`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `warehouse_stock` WRITE;
/*!40000 ALTER TABLE `warehouse_stock` DISABLE KEYS */;
INSERT INTO `warehouse_stock` VALUES
(1,1,1,1,1,104,'2026-08-03 11:26:09','2026-08-17 20:26:55'),
(2,2,1,1,1,50,'2026-08-03 11:26:39','2026-08-03 11:50:48'),
(3,1,3,3,1,10,'2026-08-05 22:03:47','2026-08-17 20:26:55'),
(4,1,4,4,1,210,'2026-08-05 22:04:33','2026-08-17 20:26:55'),
(5,1,7,NULL,1,210,'2026-08-06 20:45:54','2026-08-17 20:26:55'),
(6,1,8,NULL,NULL,310,'2026-08-06 21:17:07','2026-08-17 20:26:55'),
(7,1,9,NULL,NULL,310,'2026-08-06 21:17:54','2026-08-17 20:26:55'),
(8,1,10,NULL,NULL,310,'2026-08-06 21:18:17','2026-08-17 20:26:55'),
(9,1,11,NULL,NULL,210,'2026-08-06 21:18:34','2026-08-17 20:26:55'),
(10,1,12,NULL,NULL,310,'2026-08-06 21:18:46','2026-08-17 20:26:55'),
(11,1,13,NULL,NULL,310,'2026-08-06 21:19:01','2026-08-17 20:26:55'),
(12,1,14,NULL,NULL,310,'2026-08-06 21:19:31','2026-08-17 20:26:55'),
(14,2,3,3,1,100,'2026-08-12 20:38:12','2026-08-12 20:38:12'),
(15,2,4,4,1,100,'2026-08-12 20:39:23','2026-08-12 20:39:23'),
(16,2,7,NULL,1,100,'2026-08-12 20:42:24','2026-08-12 20:42:24'),
(17,2,11,NULL,NULL,100,'2026-08-12 20:43:57','2026-08-12 20:43:57'),
(19,3,3,3,1,50,'2026-08-16 19:50:58','2026-08-16 19:51:46');
/*!40000 ALTER TABLE `warehouse_stock` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `warehouse_transfer_items`
--

DROP TABLE IF EXISTS `warehouse_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_transfer_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_transfer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_transfer_items_warehouse_transfer_id_foreign` (`warehouse_transfer_id`),
  KEY `warehouse_transfer_items_product_id_foreign` (`product_id`),
  KEY `warehouse_transfer_items_color_id_foreign` (`color_id`),
  KEY `warehouse_transfer_items_size_id_foreign` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_transfer_items`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `warehouse_transfer_items` WRITE;
/*!40000 ALTER TABLE `warehouse_transfer_items` DISABLE KEYS */;
INSERT INTO `warehouse_transfer_items` VALUES
(1,1,1,NULL,NULL,100,'2026-08-03 11:26:34','2026-08-03 11:26:34'),
(2,2,3,NULL,NULL,100,'2026-08-12 20:37:56','2026-08-12 20:37:56'),
(3,3,4,NULL,NULL,100,'2026-08-12 20:39:17','2026-08-12 20:39:17'),
(4,4,7,NULL,NULL,100,'2026-08-12 20:42:10','2026-08-12 20:42:10'),
(5,5,11,NULL,NULL,100,'2026-08-12 20:43:53','2026-08-12 20:43:53'),
(6,6,3,NULL,NULL,100,'2026-08-16 19:50:55','2026-08-16 19:50:55');
/*!40000 ALTER TABLE `warehouse_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `warehouse_transfers`
--

DROP TABLE IF EXISTS `warehouse_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_warehouse_id` bigint(20) unsigned NOT NULL,
  `to_warehouse_id` bigint(20) unsigned NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `warehouse_transfers_to_warehouse_id_foreign` (`to_warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_transfers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `warehouse_transfers` WRITE;
/*!40000 ALTER TABLE `warehouse_transfers` DISABLE KEYS */;
INSERT INTO `warehouse_transfers` VALUES
(1,1,2,'completed','100','2026-08-03 11:26:34','2026-08-03 11:26:39'),
(2,1,2,'completed',NULL,'2026-08-12 20:37:56','2026-08-12 20:38:12'),
(3,1,2,'completed',NULL,'2026-08-12 20:39:17','2026-08-12 20:39:23'),
(4,1,2,'completed',NULL,'2026-08-12 20:42:10','2026-08-12 20:42:24'),
(5,1,2,'completed',NULL,'2026-08-12 20:43:53','2026-08-12 20:43:57'),
(6,1,3,'completed',NULL,'2026-08-16 19:50:55','2026-08-16 19:50:58');
/*!40000 ALTER TABLE `warehouse_transfers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `address` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES
(1,'JANMITRAM MAIN CENTER (Central warehouse)','Main Logistics Hub',1,'2026-08-03 08:41:11','2026-08-09 20:10:05'),
(2,'JANMITRAM SANGANER (sub warehouse)','Sanganer Jaipur',0,'2026-08-03 11:25:12','2026-08-16 19:53:55'),
(3,'JANMITRAM SITAPURA JAIPUR  (sub warehouse)','Jaipur',0,'2026-08-16 19:48:37','2026-08-16 19:53:43');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `withdraws`
--

DROP TABLE IF EXISTS `withdraws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdraws` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `contact_number` varchar(191) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `withdraw_method` varchar(191) DEFAULT NULL,
  `reason` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdraws_user_id_foreign` (`user_id`),
  KEY `withdraws_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraws`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `withdraws` WRITE;
/*!40000 ALTER TABLE `withdraws` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdraws` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Dumping routines for database 'u939461333_app_janmitram'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-08-17 16:01:46
