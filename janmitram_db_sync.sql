-- MySQL dump 10.13  Distrib 5.7.39, for osx10.12 (x86_64)
--
-- Host: localhost    Database: janmitram
-- ------------------------------------------------------
-- Server version	5.7.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `address_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area_id` bigint(20) unsigned DEFAULT NULL,
  `road_no` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flat_no` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `house_no` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_line` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_line2` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_code` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_customer_id_foreign` (`customer_id`),
  KEY `addresses_area_id_foreign` (`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (1,'E2E','1',1,'Home',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-08-01 10:03:38','2026-08-01 10:03:38');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_coupons`
--

DROP TABLE IF EXISTS `admin_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_coupons` (
  `coupon_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  KEY `admin_coupons_coupon_id_foreign` (`coupon_id`),
  KEY `admin_coupons_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_coupons`
--

LOCK TABLES `admin_coupons` WRITE;
/*!40000 ALTER TABLE `admin_coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ads_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `delivery_amount` double DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `areas`
--

LOCK TABLES `areas` WRITE;
/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` VALUES (1,'McDermottbury',83,1,'2026-08-01 10:03:38','2026-08-01 10:03:38');
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_media_id_foreign` (`media_id`),
  KEY `banners_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_tags`
--

DROP TABLE IF EXISTS `blog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_tags` (
  `blog_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  KEY `blog_tags_blog_id_foreign` (`blog_id`),
  KEY `blog_tags_tag_id_foreign` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_tags`
--

LOCK TABLES `blog_tags` WRITE;
/*!40000 ALTER TABLE `blog_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_views`
--

DROP TABLE IF EXISTS `blog_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_views_blog_id_foreign` (`blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_views`
--

LOCK TABLES `blog_views` WRITE;
/*!40000 ALTER TABLE `blog_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blogs_user_id_foreign` (`user_id`),
  KEY `blogs_media_id_foreign` (`media_id`),
  KEY `blogs_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brands_media_id_foreign` (`media_id`),
  KEY `brands_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_access_tokens`
--

DROP TABLE IF EXISTS `cart_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `access_token` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_access_tokens_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_access_tokens`
--

LOCK TABLES `cart_access_tokens` WRITE;
/*!40000 ALTER TABLE `cart_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `access_token` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `is_buy_now` tinyint(1) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '1',
  `color` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_customer_id_foreign` (`customer_id`),
  KEY `carts_product_id_foreign` (`product_id`),
  KEY `carts_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'other',
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_media_id_foreign` (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Grocery',NULL,'other',6,'Grocery',1,'2026-08-02 16:27:59','2026-08-02 16:27:59');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_subcategories`
--

DROP TABLE IF EXISTS `category_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_subcategories` (
  `category_id` bigint(20) unsigned NOT NULL,
  `sub_category_id` bigint(20) unsigned NOT NULL,
  KEY `category_subcategories_category_id_foreign` (`category_id`),
  KEY `category_subcategories_sub_category_id_foreign` (`sub_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_subcategories`
--

LOCK TABLES `category_subcategories` WRITE;
/*!40000 ALTER TABLE `category_subcategories` DISABLE KEYS */;
/*!40000 ALTER TABLE `category_subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `color_code` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `colors_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colors`
--

LOCK TABLES `colors` WRITE;
/*!40000 ALTER TABLE `colors` DISABLE KEYS */;
/*!40000 ALTER TABLE `colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_us`
--

DROP TABLE IF EXISTS `contact_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_us` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `messenger` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_us`
--

LOCK TABLES `contact_us` WRITE;
/*!40000 ALTER TABLE `contact_us` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_us` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `numeric_code` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `capital` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `native` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emoji` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emojiU` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Afghanistan','004','93','Kabul','AFN','Afghan afghani','؋','افغانستان','Asia','-74.65000000','4.48000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'Antigua and Barbuda','028','1','St. John\'s','XCD','Eastern Caribbean dollar','$','Antigua and Barbuda','Americas','17.05000000','-61.80000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Argentina','032','54','Buenos Aires','ARS','Argentine peso','$','Argentina','Americas','-34.00000000','-64.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'Armenia','051','374','Yerevan','AMD','Armenian dram','֏','Հայաստան','Asia','40.00000000','45.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,'Aruba','533','297','Oranjestad','AWG','Aruban florin','ƒ','Aruba','Americas','12.50000000','-69.96666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(6,'Australia','036','61','Canberra','AUD','Australian dollar','$','Australia','Oceania','-27.00000000','133.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(7,'Austria','040','43','Vienna','EUR','Euro','€','Österreich','Europe','47.33333333','13.33333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(8,'Azerbaijan','031','994','Baku','AZN','Azerbaijani manat','m','Azərbaycan','Asia','40.50000000','47.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(9,'Bahrain','048','973','Manama','BHD','Bahraini dinar','.د.ب','‏البحرين','Asia','26.00000000','50.55000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(10,'Bangladesh','050','880','Dhaka','BDT','Bangladeshi taka','৳','Bangladesh','Asia','24.00000000','90.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(11,'Barbados','052','1','Bridgetown','BBD','Barbadian dollar','Bds$','Barbados','Americas','13.16666666','-59.53333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(12,'Belarus','112','375','Minsk','BYN','Belarusian ruble','Br','Белару́сь','Europe','53.00000000','28.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(13,'Belgium','056','32','Brussels','EUR','Euro','€','België','Europe','50.83333333','4.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(14,'Belize','084','501','Belmopan','BZD','Belize dollar','$','Belize','Americas','17.25000000','-88.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(15,'Benin','204','229','Porto-Novo','XOF','West African CFA franc','CFA','Bénin','Africa','9.50000000','2.25000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(16,'Bermuda','060','1','Hamilton','BMD','Bermudian dollar','$','Bermuda','Americas','32.33333333','-64.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(17,'Bhutan','064','975','Thimphu','BTN','Bhutanese ngultrum','Nu.','ʼbrug-yul','Asia','27.50000000','90.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(18,'Bolivia','068','591','Sucre','BOB','Bolivian boliviano','Bs.','Bolivia','Americas','-10.00000000','-55.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(19,'British Indian Ocean Territory','086','246','Diego Garcia','USD','United States dollar','$','British Indian Ocean Territory','Africa','60.00000000','-95.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(20,'Cape Verde','132','238','Praia','CVE','Cape Verdean escudo','$','Cabo Verde','Africa','-30.00000000','-71.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(21,'China','156','86','Beijing','CNY','Chinese yuan','¥','中国','Asia','35.00000000','105.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(22,'Christmas Island','162','61','Flying Fish Cove','AUD','Australian dollar','$','Christmas Island','Oceania','-10.50000000','105.66666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(23,'Cocos (Keeling) Islands','166','61','West Island','AUD','Australian dollar','$','Cocos (Keeling) Islands','Oceania','-12.50000000','96.83333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(24,'Colombia','170','57','Bogotá','COP','Colombian peso','$','Colombia','Americas','4.00000000','-72.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(25,'Comoros','174','269','Moroni','KMF','Comorian franc','CF','Komori','Africa','-12.16666666','44.25000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(26,'Congo','178','242','Brazzaville','XAF','Central African CFA franc','FC','République du Congo','Africa','-1.00000000','15.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(27,'Cook Islands','184','682','Avarua','NZD','Cook Islands dollar','$','Cook Islands','Oceania','-21.23333333','-159.76666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(28,'Costa Rica','188','506','San Jose','CRC','Costa Rican colón','₡','Costa Rica','Americas','10.00000000','-84.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(29,'Cote D\'Ivoire (Ivory Coast)','384','225','Yamoussoukro','XOF','West African CFA franc','CFA',NULL,'Africa','8.00000000','-5.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(30,'Croatia','191','385','Zagreb','HRK','Croatian kuna','kn','Hrvatska','Europe','45.16666666','15.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(31,'Cuba','192','53','Havana','CUP','Cuban peso','$','Cuba','Americas','21.50000000','-80.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(32,'Curaçao','531','599','Willemstad','ANG','Netherlands Antillean guilder','ƒ','Curaçao','Americas','12.11666700','-68.93333300',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(33,'Cyprus','196','357','Nicosia','EUR','Euro','€','Κύπρος','Europe','35.00000000','33.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(34,'Czech Republic','203','420','Prague','CZK','Czech koruna','Kč','Česká republika','Europe','49.75000000','15.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(35,'Democratic Republic of the Congo','180','243','Kinshasa','CDF','Congolese Franc','FC','République démocratique du Congo','Africa','0.00000000','25.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(36,'Denmark','208','45','Copenhagen','DKK','Danish krone','Kr.','Danmark','Europe','56.00000000','10.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(37,'Djibouti','262','253','Djibouti','DJF','Djiboutian franc','Fdj','Djibouti','Africa','11.50000000','43.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(38,'Dominica','212','1','Roseau','XCD','Eastern Caribbean dollar','$','Dominica','Americas','15.41666666','-61.33333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(39,'Dominican Republic','214','1','Santo Domingo','DOP','Dominican peso','$','República Dominicana','Americas','19.00000000','-70.66666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(40,'Ecuador','218','593','Quito','USD','United States dollar','$','Ecuador','Americas','-2.00000000','-77.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(41,'Egypt','818','20','Cairo','EGP','Egyptian pound','ج.م','مصر‎','Africa','27.00000000','30.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(42,'El Salvador','222','503','San Salvador','USD','United States dollar','$','El Salvador','Americas','13.83333333','-88.91666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(43,'Equatorial Guinea','226','240','Malabo','XAF','Central African CFA franc','FCFA','Guinea Ecuatorial','Africa','2.00000000','10.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(44,'Eritrea','232','291','Asmara','ERN','Eritrean nakfa','Nfk','ኤርትራ','Africa','15.00000000','39.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(45,'Estonia','233','372','Tallinn','EUR','Euro','€','Eesti','Europe','59.00000000','26.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(46,'Eswatini','748','268','Mbabane','SZL','Lilangeni','E','Swaziland','Africa','-26.50000000','31.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(47,'Ethiopia','231','251','Addis Ababa','ETB','Ethiopian birr','Nkf','ኢትዮጵያ','Africa','8.00000000','38.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(48,'Falkland Islands','238','500','Stanley','FKP','Falkland Islands pound','£','Falkland Islands','Americas','-51.75000000','-59.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(49,'Faroe Islands','234','298','Torshavn','DKK','Danish krone','Kr.','Føroyar','Europe','62.00000000','-7.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(50,'Fiji Islands','242','679','Suva','FJD','Fijian dollar','FJ$','Fiji','Oceania','-18.00000000','175.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(51,'Finland','246','358','Helsinki','EUR','Euro','€','Suomi','Europe','64.00000000','26.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(52,'France','250','33','Paris','EUR','Euro','€','France','Europe','46.00000000','2.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(53,'French Guiana','254','594','Cayenne','EUR','Euro','€','Guyane française','Americas','4.00000000','-53.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(54,'French Polynesia','258','689','Papeete','XPF','CFP franc','₣','Polynésie française','Oceania','-15.00000000','-140.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(55,'French Southern Territories','260','262','Port-aux-Francais','EUR','Euro','€','Territoire des Terres australes et antarctiques fr','Africa','-49.25000000','69.16700000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(56,'Gabon','266','241','Libreville','XAF','Central African CFA franc','FCFA','Gabon','Africa','-1.00000000','11.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(57,'Georgia','268','995','Tbilisi','GEL','Georgian lari','ლ','საქართველო','Asia','42.00000000','43.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(58,'Germany','276','49','Berlin','EUR','Euro','€','Deutschland','Europe','51.00000000','9.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(59,'Ghana','288','233','Accra','GHS','Ghanaian cedi','GH₵','Ghana','Africa','8.00000000','-2.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(60,'Gibraltar','292','350','Gibraltar','GIP','Gibraltar pound','£','Gibraltar','Europe','36.13333333','-5.35000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(61,'Greece','300','30','Athens','EUR','Euro','€','Ελλάδα','Europe','39.00000000','22.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(62,'Greenland','304','299','Nuuk','DKK','Danish krone','Kr.','Kalaallit Nunaat','Americas','72.00000000','-40.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(63,'Grenada','308','1','St. George\'s','XCD','Eastern Caribbean dollar','$','Grenada','Americas','12.11666666','-61.66666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(64,'Guadeloupe','312','590','Basse-Terre','EUR','Euro','€','Guadeloupe','Americas','16.25000000','-61.58333300',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(65,'Guam','316','1','Hagatna','USD','US Dollar','$','Guam','Oceania','13.46666666','144.78333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(66,'Guatemala','320','502','Guatemala City','GTQ','Guatemalan quetzal','Q','Guatemala','Americas','15.50000000','-90.25000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(67,'Guernsey and Alderney','831','44','St Peter Port','GBP','British pound','£','Guernsey','Europe','49.46666666','-2.58333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(68,'Guinea','324','224','Conakry','GNF','Guinean franc','FG','Guinée','Africa','11.00000000','-10.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(69,'Guinea-Bissau','624','245','Bissau','XOF','West African CFA franc','CFA','Guiné-Bissau','Africa','12.00000000','-15.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(70,'Guyana','328','592','Georgetown','GYD','Guyanese dollar','$','Guyana','Americas','5.00000000','-59.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(71,'Haiti','332','509','Port-au-Prince','HTG','Haitian gourde','G','Haïti','Americas','19.00000000','-72.41666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(72,'Heard Island and McDonald Islands','334','672','','AUD','Australian dollar','$','Heard Island and McDonald Islands','','-53.10000000','72.51666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(73,'Honduras','340','504','Tegucigalpa','HNL','Honduran lempira','L','Honduras','Americas','15.00000000','-86.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(74,'Hong Kong S.A.R.','344','852','Hong Kong','HKD','Hong Kong dollar','$','香港','Asia','22.25000000','114.16666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(75,'Hungary','348','36','Budapest','HUF','Hungarian forint','Ft','Magyarország','Europe','47.00000000','20.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(76,'Iceland','352','354','Reykjavik','ISK','Icelandic króna','kr','Ísland','Europe','65.00000000','-18.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(77,'India','356','91','New Delhi','INR','Indian rupee','₹','भारत','Asia','20.00000000','77.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(78,'Indonesia','360','62','Jakarta','IDR','Indonesian rupiah','Rp','Indonesia','Asia','-5.00000000','120.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(79,'Iran','364','98','Tehran','IRR','Iranian rial','﷼','ایران','Asia','32.00000000','53.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(80,'Iraq','368','964','Baghdad','IQD','Iraqi dinar','د.ع','العراق','Asia','33.00000000','44.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(81,'Ireland','372','353','Dublin','EUR','Euro','€','Éire','Europe','53.00000000','-8.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(82,'Israel','376','972','Jerusalem','ILS','Israeli new shekel','₪','יִשְׂרָאֵל','Asia','31.50000000','34.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(83,'Italy','380','39','Rome','EUR','Euro','€','Italia','Europe','42.83333333','12.83333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(84,'Jamaica','388','1','Kingston','JMD','Jamaican dollar','J$','Jamaica','Americas','18.25000000','-77.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(85,'Japan','392','81','Tokyo','JPY','Japanese yen','¥','日本','Asia','36.00000000','138.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(86,'Jersey','832','44','Saint Helier','GBP','British pound','£','Jersey','Europe','49.25000000','-2.16666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(87,'Jordan','400','962','Amman','JOD','Jordanian dinar','ا.د','الأردن','Asia','31.00000000','36.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(88,'Kazakhstan','398','7','Astana','KZT','Kazakhstani tenge','лв','Қазақстан','Asia','48.00000000','68.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(89,'Kenya','404','254','Nairobi','KES','Kenyan shilling','KSh','Kenya','Africa','1.00000000','38.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(90,'Kiribati','296','686','Tarawa','AUD','Australian dollar','$','Kiribati','Oceania','1.41666666','173.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(91,'Kosovo','926','383','Pristina','EUR','Euro','€','Republika e Kosovës','Europe','29.50000000','45.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(92,'Kyrgyzstan','417','996','Bishkek','KGS','Kyrgyzstani som','лв','Кыргызстан','Asia','41.00000000','75.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(93,'Laos','418','856','Vientiane','LAK','Lao kip','₭','ສປປລາວ','Asia','18.00000000','105.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(94,'Latvia','428','371','Riga','EUR','Euro','€','Latvija','Europe','57.00000000','25.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(95,'Lebanon','422','961','Beirut','LBP','Lebanese pound','£','لبنان','Asia','33.83333333','35.83333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(96,'Lesotho','426','266','Maseru','LSL','Lesotho loti','L','Lesotho','Africa','-29.50000000','28.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(97,'Liberia','430','231','Monrovia','LRD','Liberian dollar','$','Liberia','Africa','6.50000000','-9.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(98,'Libya','434','218','Tripolis','LYD','Libyan dinar','د.ل','‏ليبيا','Africa','25.00000000','17.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(99,'Liechtenstein','438','423','Vaduz','CHF','Swiss franc','CHf','Liechtenstein','Europe','47.26666666','9.53333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(100,'Lithuania','440','370','Vilnius','EUR','Euro','€','Lietuva','Europe','56.00000000','24.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(101,'Luxembourg','442','352','Luxembourg','EUR','Euro','€','Luxembourg','Europe','49.75000000','6.16666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(102,'Macau S.A.R.','446','853','Macao','MOP','Macanese pataca','$','澳門','Asia','22.16666666','113.55000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(103,'Madagascar','450','261','Antananarivo','MGA','Malagasy ariary','Ar','Madagasikara','Africa','-20.00000000','47.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(104,'Malawi','454','265','Lilongwe','MWK','Malawian kwacha','MK','Malawi','Africa','-13.50000000','34.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(105,'Malaysia','458','60','Kuala Lumpur','MYR','Malaysian ringgit','RM','Malaysia','Asia','2.50000000','112.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(106,'Maldives','462','960','Male','MVR','Maldivian rufiyaa','Rf','Maldives','Asia','3.25000000','73.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(107,'Mali','466','223','Bamako','XOF','West African CFA franc','CFA','Mali','Africa','17.00000000','-4.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(108,'Malta','470','356','Valletta','EUR','Euro','€','Malta','Europe','35.83333333','14.58333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(109,'Man (Isle of)','833','44','Douglas, Isle of Man','GBP','British pound','£','Isle of Man','Europe','54.25000000','-4.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(110,'Marshall Islands','584','692','Majuro','USD','United States dollar','$','M̧ajeļ','Oceania','9.00000000','168.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(111,'Martinique','474','596','Fort-de-France','EUR','Euro','€','Martinique','Americas','14.66666700','-61.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(112,'Mauritania','478','222','Nouakchott','MRO','Mauritanian ouguiya','MRU','موريتانيا','Africa','20.00000000','-12.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(113,'Mauritius','480','230','Port Louis','MUR','Mauritian rupee','₨','Maurice','Africa','-20.28333333','57.55000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(114,'Mayotte','175','262','Mamoudzou','EUR','Euro','€','Mayotte','Africa','-12.83333333','45.16666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(115,'Mexico','484','52','Ciudad de México','MXN','Mexican peso','$','México','Americas','23.00000000','-102.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(116,'Micronesia','583','691','Palikir','USD','United States dollar','$','Micronesia','Oceania','6.91666666','158.25000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(117,'Moldova','498','373','Chisinau','MDL','Moldovan leu','L','Moldova','Europe','47.00000000','29.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(118,'Monaco','492','377','Monaco','EUR','Euro','€','Monaco','Europe','43.73333333','7.40000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(119,'Mongolia','496','976','Ulan Bator','MNT','Mongolian tögrög','₮','Монгол улс','Asia','46.00000000','105.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(120,'Montenegro','499','382','Podgorica','EUR','Euro','€','Црна Гора','Europe','42.50000000','19.30000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(121,'Montserrat','500','1','Plymouth','XCD','Eastern Caribbean dollar','$','Montserrat','Americas','16.75000000','-62.20000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(122,'Morocco','504','212','Rabat','MAD','Moroccan dirham','DH','المغرب','Africa','32.00000000','-5.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(123,'Mozambique','508','258','Maputo','MZN','Mozambican metical','MT','Moçambique','Africa','-18.25000000','35.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(124,'Myanmar','104','95','Nay Pyi Taw','MMK','Burmese kyat','K','မြန်မာ','Asia','22.00000000','98.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(125,'Namibia','516','264','Windhoek','NAD','Namibian dollar','$','Namibia','Africa','-22.00000000','17.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(126,'Nauru','520','674','Yaren','AUD','Australian dollar','$','Nauru','Oceania','-0.53333333','166.91666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(127,'Nepal','524','977','Kathmandu','NPR','Nepalese rupee','₨','नपल','Asia','28.00000000','84.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(128,'Netherlands','528','31','Amsterdam','EUR','Euro','€','Nederland','Europe','52.50000000','5.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(129,'New Caledonia','540','687','Noumea','XPF','CFP franc','₣','Nouvelle-Calédonie','Oceania','-21.50000000','165.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(130,'New Zealand','554','64','Wellington','NZD','New Zealand dollar','$','New Zealand','Oceania','-41.00000000','174.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(131,'Nicaragua','558','505','Managua','NIO','Nicaraguan córdoba','C$','Nicaragua','Americas','13.00000000','-85.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(132,'Niger','562','227','Niamey','XOF','West African CFA franc','CFA','Niger','Africa','16.00000000','8.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(133,'Nigeria','566','234','Abuja','NGN','Nigerian naira','₦','Nigeria','Africa','10.00000000','8.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(134,'Niue','570','683','Alofi','NZD','New Zealand dollar','$','Niuē','Oceania','-19.03333333','-169.86666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(135,'Norfolk Island','574','672','Kingston','AUD','Australian dollar','$','Norfolk Island','Oceania','-29.03333333','167.95000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(136,'North Korea','408','850','Pyongyang','KPW','North Korean Won','₩','북한','Asia','40.00000000','127.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(137,'North Macedonia','807','389','Skopje','MKD','Denar','ден','Северна Македонија','Europe','41.83333333','22.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(138,'Northern Mariana Islands','580','1','Saipan','USD','United States dollar','$','Northern Mariana Islands','Oceania','15.20000000','145.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(139,'Norway','578','47','Oslo','NOK','Norwegian krone','kr','Norge','Europe','62.00000000','10.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(140,'Oman','512','968','Muscat','OMR','Omani rial','.ع.ر','عمان','Asia','21.00000000','57.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(141,'Pakistan','586','92','Islamabad','PKR','Pakistani rupee','₨','Pakistan','Asia','30.00000000','70.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(142,'Palau','585','680','Melekeok','USD','United States dollar','$','Palau','Oceania','7.50000000','134.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(143,'Palestinian Territory Occupied','275','970','East Jerusalem','ILS','Israeli new shekel','₪','فلسطين','Asia','31.90000000','35.20000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(144,'Panama','591','507','Panama City','PAB','Panamanian balboa','B/.','Panamá','Americas','9.00000000','-80.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(145,'Papua New Guinea','598','675','Port Moresby','PGK','Papua New Guinean kina','K','Papua Niugini','Oceania','-6.00000000','147.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(146,'Paraguay','600','595','Asuncion','PYG','Paraguayan guarani','₲','Paraguay','Americas','-23.00000000','-58.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(147,'Peru','604','51','Lima','PEN','Peruvian sol','S/.','Perú','Americas','-10.00000000','-76.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(148,'Philippines','608','63','Manila','PHP','Philippine peso','₱','Pilipinas','Asia','13.00000000','122.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(149,'Pitcairn Island','612','870','Adamstown','NZD','New Zealand dollar','$','Pitcairn Islands','Oceania','-25.06666666','-130.10000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(150,'Poland','616','48','Warsaw','PLN','Polish złoty','zł','Polska','Europe','52.00000000','20.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(151,'Portugal','620','351','Lisbon','EUR','Euro','€','Portugal','Europe','39.50000000','-8.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(152,'Puerto Rico','630','1','San Juan','USD','United States dollar','$','Puerto Rico','Americas','18.25000000','-66.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(153,'Qatar','634','974','Doha','QAR','Qatari riyal','ق.ر','قطر','Asia','25.50000000','51.25000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(154,'Reunion','638','262','Saint-Denis','EUR','Euro','€','La Réunion','Africa','-21.15000000','55.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(155,'Romania','642','40','Bucharest','RON','Romanian leu','lei','România','Europe','46.00000000','25.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(156,'Russia','643','7','Moscow','RUB','Russian ruble','₽','Россия','Europe','60.00000000','100.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(157,'Rwanda','646','250','Kigali','RWF','Rwandan franc','FRw','Rwanda','Africa','-2.00000000','30.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(158,'Saint Helena','654','290','Jamestown','SHP','Saint Helena pound','£','Saint Helena','Africa','-15.95000000','-5.70000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(159,'Saint Kitts and Nevis','659','1','Basseterre','XCD','Eastern Caribbean dollar','$','Saint Kitts and Nevis','Americas','17.33333333','-62.75000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(160,'Saint Lucia','662','1','Castries','XCD','Eastern Caribbean dollar','$','Saint Lucia','Americas','13.88333333','-60.96666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(161,'Saint Pierre and Miquelon','666','508','Saint-Pierre','EUR','Euro','€','Saint-Pierre-et-Miquelon','Americas','46.83333333','-56.33333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(162,'Saint Vincent and the Grenadines','670','1','Kingstown','XCD','Eastern Caribbean dollar','$','Saint Vincent and the Grenadines','Americas','13.25000000','-61.20000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(163,'Saint-Barthelemy','652','590','Gustavia','EUR','Euro','€','Saint-Barthélemy','Americas','18.50000000','-63.41666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(164,'Saint-Martin (French part)','663','590','Marigot','EUR','Euro','€','Saint-Martin','Americas','18.08333333','-63.95000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(165,'Samoa','882','685','Apia','WST','Samoan tālā','SAT','Samoa','Oceania','-13.58333333','-172.33333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(166,'San Marino','674','378','San Marino','EUR','Euro','€','San Marino','Europe','43.76666666','12.41666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(167,'Sao Tome and Principe','678','239','Sao Tome','STD','Dobra','Db','São Tomé e Príncipe','Africa','1.00000000','7.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(168,'Saudi Arabia','682','966','Riyadh','SAR','Saudi riyal','﷼','المملكة العربية السعودية','Asia','25.00000000','45.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(169,'Senegal','686','221','Dakar','XOF','West African CFA franc','CFA','Sénégal','Africa','14.00000000','-14.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(170,'Serbia','688','381','Belgrade','RSD','Serbian dinar','din','Србија','Europe','44.00000000','21.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(171,'Seychelles','690','248','Victoria','SCR','Seychellois rupee','SRe','Seychelles','Africa','-4.58333333','55.66666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(172,'Sierra Leone','694','232','Freetown','SLL','Sierra Leonean leone','Le','Sierra Leone','Africa','8.50000000','-11.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(173,'Singapore','702','65','Singapur','SGD','Singapore dollar','$','Singapore','Asia','1.36666666','103.80000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(174,'Sint Maarten (Dutch part)','534','1721','Philipsburg','ANG','Netherlands Antillean guilder','ƒ','Sint Maarten','Americas','18.03333300','-63.05000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(175,'Slovakia','703','421','Bratislava','EUR','Euro','€','Slovensko','Europe','48.66666666','19.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(176,'Slovenia','705','386','Ljubljana','EUR','Euro','€','Slovenija','Europe','46.11666666','14.81666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(177,'Solomon Islands','090','677','Honiara','SBD','Solomon Islands dollar','Si$','Solomon Islands','Oceania','-8.00000000','159.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(178,'Somalia','706','252','Mogadishu','SOS','Somali shilling','Sh.so.','Soomaaliya','Africa','10.00000000','49.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(179,'South Africa','710','27','Pretoria','ZAR','South African rand','R','South Africa','Africa','-29.00000000','24.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(180,'South Georgia','239','500','Grytviken','GBP','British pound','£','South Georgia','Americas','-54.50000000','-37.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(181,'South Korea','410','82','Seoul','KRW','Won','₩','대한민국','Asia','37.00000000','127.50000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(182,'South Sudan','728','211','Juba','SSP','South Sudanese pound','£','South Sudan','Africa','7.00000000','30.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(183,'Spain','724','34','Madrid','EUR','Euro','€','España','Europe','40.00000000','-4.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(184,'Sri Lanka','144','94','Colombo','LKR','Sri Lankan rupee','Rs','śrī laṃkāva','Asia','7.00000000','81.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(185,'Sudan','729','249','Khartoum','SDG','Sudanese pound','.س.ج','السودان','Africa','15.00000000','30.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(186,'Suriname','740','597','Paramaribo','SRD','Surinamese dollar','$','Suriname','Americas','4.00000000','-56.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(187,'Svalbard and Jan Mayen Islands','744','47','Longyearbyen','NOK','Norwegian Krone','kr','Svalbard og Jan Mayen','Europe','78.00000000','20.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(188,'Sweden','752','46','Stockholm','SEK','Swedish krona','kr','Sverige','Europe','62.00000000','15.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(189,'Switzerland','756','41','Bern','CHF','Swiss franc','CHf','Schweiz','Europe','47.00000000','8.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(190,'Syria','760','963','Damascus','SYP','Syrian pound','LS','سوريا','Asia','35.00000000','38.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(191,'Taiwan','158','886','Taipei','TWD','New Taiwan dollar','$','臺灣','Asia','23.50000000','121.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(192,'Tajikistan','762','992','Dushanbe','TJS','Tajikistani somoni','SM','Тоҷикистон','Asia','39.00000000','71.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(193,'Tanzania','834','255','Dodoma','TZS','Tanzanian shilling','TSh','Tanzania','Africa','-6.00000000','35.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(194,'Thailand','764','66','Bangkok','THB','Thai baht','฿','ประเทศไทย','Asia','15.00000000','100.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(195,'The Bahamas','044','1','Nassau','BSD','Bahamian dollar','B$','Bahamas','Americas','24.25000000','-76.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(196,'The Gambia ','270','220','Banjul','GMD','Gambian dalasi','D','Gambia','Africa','13.46666666','-16.56666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(197,'Timor-Leste','626','670','Dili','USD','United States dollar','$','Timor-Leste','Asia','-8.83333333','125.91666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(198,'Togo','768','228','Lome','XOF','West African CFA franc','CFA','Togo','Africa','8.00000000','1.16666666',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(199,'Tokelau','772','690','','NZD','New Zealand dollar','$','Tokelau','Oceania','-9.00000000','-172.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(200,'Tonga','776','676','Nuku\'alofa','TOP','Tongan paʻanga','$','Tonga','Oceania','-20.00000000','-175.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(201,'Trinidad and Tobago','780','1','Port of Spain','TTD','Trinidad and Tobago dollar','$','Trinidad and Tobago','Americas','11.00000000','-61.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(202,'Tunisia','788','216','Tunis','TND','Tunisian dinar','ت.د','تونس','Africa','34.00000000','9.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(203,'Turkey','792','90','Ankara','TRY','Turkish lira','₺','Türkiye','Asia','39.00000000','35.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(204,'Turkmenistan','795','993','Ashgabat','TMT','Turkmenistan manat','T','Türkmenistan','Asia','40.00000000','60.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(205,'Turks and Caicos Islands','796','1','Cockburn Town','USD','United States dollar','$','Turks and Caicos Islands','Americas','21.75000000','-71.58333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(206,'Tuvalu','798','688','Funafuti','AUD','Australian dollar','$','Tuvalu','Oceania','-8.00000000','178.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(207,'Uganda','800','256','Kampala','UGX','Ugandan shilling','USh','Uganda','Africa','1.00000000','32.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(208,'Ukraine','804','380','Kyiv','UAH','Ukrainian hryvnia','₴','Україна','Europe','49.00000000','32.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(209,'United Arab Emirates','784','971','Abu Dhabi','AED','United Arab Emirates dirham','إ.د','دولة الإمارات العربية المتحدة','Asia','24.00000000','54.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(210,'United Kingdom','826','44','London','GBP','British pound','£','United Kingdom','Europe','54.00000000','-2.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(211,'United States','840','1','Washington','USD','United States dollar','$','United States','Americas','38.00000000','-97.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(212,'United States Minor Outlying Islands','581','1','','USD','United States dollar','$','United States Minor Outlying Islands','Americas','0.00000000','0.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(213,'Uruguay','858','598','Montevideo','UYU','Uruguayan peso','$','Uruguay','Americas','-33.00000000','-56.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(214,'Uzbekistan','860','998','Tashkent','UZS','Uzbekistani soʻm','лв','O‘zbekiston','Asia','41.00000000','64.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(215,'Vanuatu','548','678','Port Vila','VUV','Vanuatu vatu','VT','Vanuatu','Oceania','-16.00000000','167.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(216,'Vatican City State (Holy See)','336','379','Vatican City','EUR','Euro','€','Vaticano','Europe','41.90000000','12.45000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(217,'Venezuela','862','58','Caracas','VES','Bolívar','Bs','Venezuela','Americas','8.00000000','-66.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(218,'Vietnam','704','84','Hanoi','VND','Vietnamese đồng','₫','Việt Nam','Asia','16.16666666','107.83333333',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(219,'Virgin Islands (British)','092','1','Road Town','USD','United States dollar','$','British Virgin Islands','Americas','18.43138300','-64.62305000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(220,'Virgin Islands (US)','850','1','Charlotte Amalie','USD','United States dollar','$','United States Virgin Islands','Americas','18.34000000','-64.93000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(221,'Wallis and Futuna Islands','876','681','Mata Utu','XPF','CFP franc','₣','Wallis et Futuna','Oceania','-13.30000000','-176.20000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(222,'Western Sahara','732','212','El-Aaiun','MAD','Moroccan Dirham','MAD','الصحراء الغربية','Africa','24.50000000','-13.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(223,'Yemen','887','967','Sanaa','YER','Yemeni rial','﷼','اليَمَن','Asia','15.00000000','48.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(224,'Zambia','894','260','Lusaka','ZMW','Zambian kwacha','ZK','Zambia','Africa','-15.00000000','30.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(225,'Zimbabwe','716','263','Harare','ZWL','Zimbabwe Dollar','$','Zimbabwe','Africa','-20.00000000','30.00000000',NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_collects`
--

DROP TABLE IF EXISTS `coupon_collects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupon_collects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_collects_coupon_id_foreign` (`coupon_id`),
  KEY `coupon_collects_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_collects`
--

LOCK TABLES `coupon_collects` WRITE;
/*!40000 ALTER TABLE `coupon_collects` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_collects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `discount` double NOT NULL,
  `min_amount` double NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expired_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `limit_for_user` int(11) DEFAULT '10',
  `max_discount_amount` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupons_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'71169297',2,'Amount',8.69,66.01,'2007-01-30 12:10:44','1979-12-20 11:42:24',1,'2026-08-01 10:03:38','2026-08-01 10:03:38',NULL,10,NULL);
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `rate` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'USD',NULL,'$','1',1,0,'2026-07-31 13:44:43','2026-08-02 18:13:42'),(2,'EUR',NULL,'€','1',1,0,'2026-07-31 13:44:43','2026-08-02 18:13:42'),(3,'INR',NULL,'₹','1',1,1,'2026-08-02 18:13:42','2026-08-02 18:13:42');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,4,'2026-08-01 10:03:38','2026-08-01 10:03:38');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_charges`
--

DROP TABLE IF EXISTS `delivery_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_charges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_qty` int(11) DEFAULT NULL,
  `max_qty` int(11) DEFAULT NULL,
  `charge` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_charges`
--

LOCK TABLES `delivery_charges` WRITE;
/*!40000 ALTER TABLE `delivery_charges` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_keys`
--

DROP TABLE IF EXISTS `device_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_keys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `key` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `device_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'android',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_keys_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_keys`
--

LOCK TABLES `device_keys` WRITE;
/*!40000 ALTER TABLE `device_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_locations`
--

DROP TABLE IF EXISTS `driver_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `driver_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint(20) unsigned NOT NULL,
  `latitude` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_locations_driver_id_foreign` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_locations`
--

LOCK TABLES `driver_locations` WRITE;
/*!40000 ALTER TABLE `driver_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `driver_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_orders`
--

DROP TABLE IF EXISTS `driver_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `driver_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `assign_for` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_accept` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cash_collect` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `driver_orders_driver_id_foreign` (`driver_id`),
  KEY `driver_orders_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_orders`
--

LOCK TABLES `driver_orders` WRITE;
/*!40000 ALTER TABLE `driver_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `driver_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drivers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `total_cash_collected` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drivers_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favorites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `favorites_customer_id_foreign` (`customer_id`),
  KEY `favorites_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flash_sale_products`
--

DROP TABLE IF EXISTS `flash_sale_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_sale_products` (
  `flash_sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `sale_quantity` int(11) DEFAULT '0',
  KEY `flash_sale_products_flash_sale_id_foreign` (`flash_sale_id`),
  KEY `flash_sale_products_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sale_products`
--

LOCK TABLES `flash_sale_products` WRITE;
/*!40000 ALTER TABLE `flash_sale_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `flash_sale_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flash_sales`
--

DROP TABLE IF EXISTS `flash_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_time` time NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_time` time NOT NULL,
  `end_date` date DEFAULT NULL,
  `discount` double NOT NULL DEFAULT '0',
  `min_discount` double DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `description` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_sales_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sales`
--

LOCK TABLES `flash_sales` WRITE;
/*!40000 ALTER TABLE `flash_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `flash_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_items`
--

DROP TABLE IF EXISTS `footer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footer_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `footer_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'link',
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ar_title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_type` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `target` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '_self',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_items_footer_id_foreign` (`footer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_items`
--

LOCK TABLES `footer_items` WRITE;
/*!40000 ALTER TABLE `footer_items` DISABLE KEYS */;
INSERT INTO `footer_items` VALUES (1,1,'logo',NULL,NULL,NULL,'single','_self',1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,1,'text','The ultimate all-in-one solution for your eCommerce business worldwide.','الحل الأمثل الشامل لأعمال التجارة الإلكترونية الخاصة بك في جميع أنحاء العالم',NULL,'single','_self',1,1,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,1,'phone','+880123456789','+880123456789',NULL,'single','_self',1,2,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,1,'email','admin@example.com','admin@example.com',NULL,'single','_self',1,3,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,1,'social_links',NULL,NULL,NULL,'single','_self',1,4,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(6,2,'link','Products','المنتجات','/products','single','_self',1,0,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(7,2,'link','Most Popular','الاكثر شعبية','/most-popular','single','_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(8,2,'link','Best Deal','الافضل العرض','/best-deal','single','_self',1,2,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(9,2,'link','Become a Seller','صاحب متجر','/shop/register','multi','_blank',1,3,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(10,2,'link','Blogs','المدونات','/blogs','single','_self',1,5,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(11,2,'link','About us','من نحن','/about-us','single','_self',1,0,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(12,3,'link','Contact','اتصل بنا','/contact-us','single','_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(13,3,'link','Terms & Conditions','الشروط والاحكام','/terms-and-conditions','single','_self',1,2,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(14,3,'link','Privacy Policy','سياسة الخصوصية','/privacy-policy','single','_self',1,3,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(15,4,'app_store',NULL,NULL,NULL,'single','_self',1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `footer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footers`
--

DROP TABLE IF EXISTS `footers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ar_title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footers`
--

LOCK TABLES `footers` WRITE;
/*!40000 ALTER TABLE `footers` DISABLE KEYS */;
INSERT INTO `footers` VALUES (1,NULL,NULL,NULL,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'Quick Links','روابط سريعة',NULL,1,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Company','شركة',NULL,2,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'Download our app','تحميل التطبيق',NULL,3,1,'2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `footers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_image` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galleries_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generate_settings`
--

DROP TABLE IF EXISTS `generate_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generate_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `favicon_id` bigint(20) unsigned DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_playstore_url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_download_app` tinyint(1) NOT NULL DEFAULT '1',
  `app_store_url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `currency_position` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direction` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_footer` tinyint(1) NOT NULL DEFAULT '1',
  `footer_phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `footer_email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_color` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#8b5cf6',
  `secondary_color` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#ede9fe',
  `business_based_on` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'commission',
  `commission` double NOT NULL DEFAULT '10',
  `commission_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'percentage',
  `commission_charge` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'per_order',
  `shop_pos` tinyint(1) NOT NULL DEFAULT '1',
  `shop_register` tinyint(1) NOT NULL DEFAULT '1',
  `shop_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'multi' COMMENT 'multi, single',
  `new_product_approval` tinyint(1) NOT NULL DEFAULT '1',
  `update_product_approval` tinyint(1) NOT NULL DEFAULT '1',
  `min_withdraw` double DEFAULT NULL,
  `max_withdraw` double DEFAULT NULL,
  `withdraw_request` int(11) DEFAULT NULL,
  `footer_text` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `footer_description` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `footer_logo_id` bigint(20) unsigned DEFAULT NULL,
  `footer_qrcode_id` bigint(20) unsigned DEFAULT NULL,
  `app_logo_id` bigint(20) unsigned DEFAULT NULL,
  `show_sku` tinyint(1) NOT NULL DEFAULT '0',
  `default_delivery_charge` double NOT NULL DEFAULT '0',
  `cash_on_delivery` tinyint(1) NOT NULL DEFAULT '1',
  `online_payment` tinyint(1) NOT NULL DEFAULT '1',
  `return_order_within_days` int(11) DEFAULT '3',
  `product_description` text COLLATE utf8_unicode_ci,
  `page_description` text COLLATE utf8_unicode_ci,
  `blog_description` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `generate_settings_logo_id_foreign` (`logo_id`),
  KEY `generate_settings_favicon_id_foreign` (`favicon_id`),
  KEY `generate_settings_footer_logo_id_foreign` (`footer_logo_id`),
  KEY `generate_settings_footer_qrcode_id_foreign` (`footer_qrcode_id`),
  KEY `generate_settings_app_logo_id_foreign` (`app_logo_id`),
  KEY `generate_settings_currency_id_foreign` (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generate_settings`
--

LOCK TABLES `generate_settings` WRITE;
/*!40000 ALTER TABLE `generate_settings` DISABLE KEYS */;
INSERT INTO `generate_settings` VALUES (1,'Laravel','Laravel',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-07-31 13:44:43','2026-08-02 18:13:42','₹',3,'prefix','ltr',1,'+880123456789',NULL,'#ee456b','#fee5e8','commission',10,'percentage','per_order',1,1,'multi',1,1,NULL,NULL,NULL,'All right reserved by company','The ultimate all-in-one solution for your eCommerce business worldwide.',NULL,NULL,NULL,0,0,1,1,3,'Product name: {product_name}. Short description: {short_description}. Write a long, SEO-friendly product description that includes relevant keywords, highlights unique features, and encourages buyers to take action.','The page title is {title}. Generate a well-structured, professional, and legally appropriate long content for this page, ensuring it covers all important points relevant to {title}.','The blog title is {title}. Generate a well-structured, professional, and legally appropriate long content for this blog, ensuring it covers all important points relevant to {title}.');
/*!40000 ALTER TABLE `generate_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_re_captchas`
--

DROP TABLE IF EXISTS `google_re_captchas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_re_captchas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_key` text COLLATE utf8_unicode_ci NOT NULL,
  `secret_key` text COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_re_captchas`
--

LOCK TABLES `google_re_captchas` WRITE;
/*!40000 ALTER TABLE `google_re_captchas` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_re_captchas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `direction` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'ltr',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legal_pages`
--

DROP TABLE IF EXISTS `legal_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legal_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legal_pages`
--

LOCK TABLES `legal_pages` WRITE;
/*!40000 ALTER TABLE `legal_pages` DISABLE KEYS */;
INSERT INTO `legal_pages` VALUES (1,'Privacy Policy','privacy-policy','<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information when you use our services.</p><p>We collect information you provide directly, such as your name, email address, phone number, and shipping address when you create an account or place an order.</p><p>We use your information to process orders, provide customer support, and improve our services. We do not share your personal information with third parties except as necessary to fulfill orders or as required by law.</p>','2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'Terms of Service','terms-and-conditions','<h2>Terms of Service</h2><p>By using our platform, you agree to these terms and conditions. Please read them carefully.</p><p>All products and services are provided as described. We reserve the right to modify or discontinue any service without prior notice.</p><p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>','2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Return policy / Refund Policy','return-and-refund-policy','<h2>Return & Refund Policy</h2><p>We accept returns within 30 days of delivery. Items must be unused and in original packaging.</p><p>Refunds will be processed within 5-7 business days after we receive the returned item. Shipping costs are non-refundable.</p><p>For digital products, all sales are final unless the product is defective or not as described.</p>','2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'Shipping & Delivery Policy','shipping-and-delivery-policy','<h2>Shipping & Delivery Policy</h2><p>We offer free shipping on orders over a certain amount. Standard delivery takes 3-7 business days.</p><p>Express delivery is available at an additional cost. Delivery times may vary depending on your location.</p><p>We are not responsible for delays caused by customs, weather conditions, or other factors beyond our control.</p>','2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,'About Us','about-us','<h2>About Us</h2><p>Welcome to our marketplace! We are a leading e-commerce platform connecting buyers with trusted sellers.</p><p>Our mission is to provide a seamless shopping experience with quality products at competitive prices. We support local businesses and entrepreneurs.</p><p>With our multi-vendor platform, you can browse products from multiple shops, compare prices, and enjoy secure payments.</p>','2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `legal_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) COLLATE utf8_unicode_ci DEFAULT 'image',
  `name` text COLLATE utf8_unicode_ci,
  `original_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `src` text COLLATE utf8_unicode_ci NOT NULL,
  `extention` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,'image','quas',NULL,'default/dummy-profile.png','png','2026-07-31 13:44:45','2026-07-31 13:44:45'),(2,'image','adipisci',NULL,'default/dummy-profile.png','png','2026-07-31 13:44:45','2026-07-31 13:44:45'),(3,'image','gac.png',NULL,'users/profile/DTfPpHFUlFZn0V9ZpoZCgWEZ25pOTJG3TqRftZkm.png',NULL,'2026-08-02 15:00:09','2026-08-02 15:00:09'),(4,'image','Untitled.jpg',NULL,'shops/logo/wlQnfkIjkRpn3GhLVTATVxy8yXddJ9IjmJNbEUmT.jpg',NULL,'2026-08-02 15:00:11','2026-08-02 15:00:11'),(5,'image','new-logo.png',NULL,'shops/banner/RxQ0gmFxBRromdTW5jZPgYXJCrX2yUkuYkJLZL3S.png',NULL,'2026-08-02 15:00:11','2026-08-02 15:00:11'),(6,'image','gac.png',NULL,'categories/gLI7ARjqM4KhQIh70VX6wIWPhnHIn4In5eyDgHL1.png',NULL,'2026-08-02 16:27:59','2026-08-02 16:27:59'),(7,'thumbnail','Untitled.jpg',NULL,'products/G0ogRx60Zy9nUYPMqaMp0PO07qsutGGmIQLMnh3W.jpg',NULL,'2026-08-02 16:29:17','2026-08-02 16:29:17'),(8,'thumbnail','maxresdefault.jpg',NULL,'products/Fc6WhWyDpaFC4KYMjZH59ZKWKMxPzAGqZhZ2gre1.jpg',NULL,'2026-08-02 16:29:17','2026-08-02 16:29:17');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `ar_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `target` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '_self',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_external` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'Home','الرئيسية','/','Home','Home','/',1,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'Products','المنتجات','/products','Products','Products','/products',2,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Digital Products','المنتجات الرقمية','/digital-products','Digital Products','Digital Products','/digital-products',3,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'Shops','المتاجر','/shops','Shops','Shops','/shops',4,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,'Most Popular','الاكثر شعبية','/most-popular','Most Popular','Most Popular','/most-popular',5,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(6,'Best Deal','أفضل العروض','/best-deal','Best Deal','Best Deal','/best-deal',6,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(7,'Contact','اتصل بنا','/contact-us','Contact','Contact','/contact-us',7,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(8,'Blogs','المدونات','/blogs','Blogs','Blogs','/blogs',8,'_self',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_08_19_000000_create_failed_jobs_table',1),(2,'2019_12_14_000001_create_personal_access_tokens_table',1),(3,'2024_01_16_000214_create_permission_tables',1),(4,'2024_01_16_000324_create_media_table',1),(5,'2024_01_16_000454_create_users_table',1),(6,'2024_01_16_104638_create_customers_table',1),(7,'2024_01_16_105000_create_categories_table',1),(8,'2024_01_16_105234_create_shops_table',1),(9,'2024_01_16_105314_create_brands_table',1),(10,'2024_01_16_105346_create_products_table',1),(11,'2024_01_16_110908_create_banners_table',1),(12,'2024_01_16_111152_create_reviews_table',1),(13,'2024_01_16_112138_create_addresses_table',1),(14,'2024_01_16_112545_create_coupons_table',1),(15,'2024_01_16_115937_create_shop_categories_table',1),(16,'2024_01_17_002344_create_colors_table',1),(17,'2024_01_17_072702_create_product_colors_table',1),(18,'2024_01_17_073458_create_sizes_table',1),(19,'2024_01_17_073725_create_units_table',1),(20,'2024_01_17_074135_create_product_units_table',1),(21,'2024_01_17_074229_create_product_sizes_table',1),(22,'2024_01_17_074745_create_verify_otps_table',1),(23,'2024_01_17_075503_create_product_thumbnails_table',1),(24,'2024_01_17_080646_create_product_categories_table',1),(25,'2024_01_17_092300_create_favorites_table',1),(26,'2024_01_17_112948_create_orders_table',1),(27,'2024_01_17_114308_create_order_products_table',1),(28,'2024_01_23_045138_create_legal_pages_table',1),(29,'2024_01_23_045412_create_supports_table',1),(30,'2024_01_27_072753_create_generate_settings_table',1),(31,'2024_02_29_111443_add_is_approve_column_to_products_table',1),(32,'2024_03_05_113904_add_unit_id_to_products_table',1),(33,'2024_03_06_101936_add_price_column_to_product_sizes_table',1),(34,'2024_03_10_110137_add_currency_column_to_generale_setting_table',1),(35,'2024_03_12_040056_add_code_to_products_table',1),(36,'2024_03_16_075329_add_order_id_to_reviews_table',1),(37,'2024_03_17_063030_change_shop_id_to_coupons_table',1),(38,'2024_03_17_100252_create_coupon_collects_table',1),(39,'2024_03_18_082804_add_footer_column_to_generale_settings_table',1),(40,'2024_03_20_031250_add_column_to_coupons_table',1),(41,'2024_03_20_032311_create_admin_coupons_table',1),(42,'2024_03_23_111543_create_notifications_table',1),(43,'2024_03_24_094629_create_payment_gateways_table',1),(44,'2024_03_25_104034_create_payments_table',1),(45,'2024_03_25_104350_create_order_payments_table',1),(46,'2024_03_25_114533_create_contact_uses_table',1),(47,'2024_03_27_164439_add_column_to_payments_table',1),(48,'2024_03_28_095034_add_column_to_generate_settings_table',1),(49,'2024_03_30_092829_create_wallets_table',1),(50,'2024_03_30_093648_create_transactions_table',1),(51,'2024_03_30_094238_create_withdraws_table',1),(52,'2024_03_30_104019_add_column_to_orders_table',1),(53,'2024_03_31_083538_add_column_to_notifications_table',1),(54,'2024_04_19_173548_add_shop_type_column_to_generate_settings_table',1),(55,'2024_04_21_121936_create_carts_table',1),(56,'2024_04_28_104356_create_drivers_table',1),(57,'2024_04_29_104509_create_drivers_orders_table',1),(58,'2024_04_29_113150_add_vehicle_type_column_to_users_table',1),(59,'2024_04_29_164336_create_device_keys_table',1),(60,'2024_04_30_114459_add_cash_collect_to_driver_order_table',1),(61,'2024_04_30_121054_add_total_cash_collect_to_drivers_table',1),(62,'2024_05_14_154411_create_recent_views_table',1),(63,'2024_05_15_111324_create_flash_sales_table',1),(64,'2024_05_15_115849_create_flash_sale_products_table',1),(65,'2024_05_16_112807_add_column_to_supports_table',1),(66,'2024_05_16_116825_add_column_to_flash_sale_products_table',1),(67,'2024_05_18_111152_add_off_day_to_shops_table',1),(68,'2024_05_18_163651_add_column_to_generate_settings_table',1),(69,'2024_05_19_122837_add_column_to_generate_settings_table',1),(70,'2024_05_20_163704_create_ads_table',1),(71,'2024_05_21_155258_create_delivery_charges_table',1),(72,'2024_05_21_191749_create_social_links_table',1),(73,'2024_05_26_131755_create_support_tickets_table',1),(74,'2024_05_26_131930_create_support_ticket_messages_table',1),(75,'2024_05_26_135940_create_support_ticket_attachment_table',1),(76,'2024_05_28_103844_create_ticket_issue_types_table',1),(77,'2024_05_30_182019_add_column_to_generate_settings_table',1),(78,'2024_06_05_165137_add_column_to_media_table',1),(79,'2024_06_08_154510_create_galleries_table',1),(80,'2024_06_09_103825_create_sub_categories_table',1),(81,'2024_06_09_110839_create_category_subcategories_table',1),(82,'2024_06_09_132729_create_product_subcategorys_table',1),(83,'2024_06_22_095856_create_s_m_s_configs_table',1),(84,'2024_06_23_154709_add_column_to_generate_settings_table',1),(85,'2024_06_24_133734_add_column_to_carts_table',1),(86,'2024_06_25_110336_add_column_to_generate_settings_table',1),(87,'2024_06_25_114728_add_column_to_reviews_table',1),(88,'2024_06_27_112507_add_column_to_categories_table',1),(89,'2024_06_29_104005_add_column_to_categories_table',1),(90,'2024_06_29_104453_add_column_to_sub_categories_table',1),(91,'2024_06_29_114916_add_column_to_brands_table',1),(92,'2024_06_29_114927_add_column_to_colors_table',1),(93,'2024_06_29_114939_add_column_to_sizes_table',1),(94,'2024_06_29_115100_add_column_to_products_table',1),(95,'2024_07_01_103743_create_languages_table',1),(96,'2024_07_01_110802_add_softdelete_to_users_table',1),(97,'2024_07_03_092913_create_pos_carts_table',1),(98,'2024_07_03_101851_create_pos_cart_products_table',1),(99,'2024_07_03_103622_add_column_to_orders_table',1),(100,'2024_07_04_170256_update_customer_id_column_to_orders_table',1),(101,'2024_07_07_124525_add_draft_column_to_pos_carts_table',1),(102,'2024_07_09_142655_add_column_to_pos_carts_table',1),(103,'2024_09_15_093610_create_theme_colors_table',1),(104,'2024_09_15_113123_add_clumns_to_users_table',1),(105,'2024_09_17_101004_add_clumn_to_products_table',1),(106,'2024_09_23_115749_add_column_to_users_table',1),(107,'2024_09_24_101919_add_column_to_roles_table',1),(108,'2024_09_24_105224_create_user_non_permissions_table',1),(109,'2024_09_26_114754_create_verify_manages_table',1),(110,'2024_09_30_135048_change_column_to_flash_sales_table',1),(111,'2024_09_30_164906_add_column_to_flash_sale_products_table',1),(112,'2024_10_02_125419_add_column_to_flash_sales_table',1),(113,'2024_10_05_135418_add_column_to_flash_sale_products_table',1),(114,'2024_10_08_113905_add_column_to_order_products_table',1),(115,'2024_10_10_155932_create_google_re_captchas_table',1),(116,'2024_10_14_160335_create_social_auths_table',1),(117,'2024_10_14_170543_add_columns_to_users_table',1),(118,'2024_10_19_104732_add_columns_to_product_colors_table',1),(119,'2024_11_24_124258_create_vat_taxes_table',1),(120,'2024_11_24_165734_create_product_vat_taxes_table',1),(121,'2024_11_25_110112_add_column_to_orders_table',1),(122,'2024_11_28_170135_create_blogs_table',1),(123,'2024_11_28_171145_create_tags_table',1),(124,'2024_11_28_171247_create_blog_tags_table',1),(125,'2024_11_28_171333_create_blog_views_table',1),(126,'2025_01_06_145615_add_column_to_carts_table',1),(127,'2025_01_07_113201_add_column_to_products_table',1),(128,'2025_01_07_115807_add_column_to_languages_table',1),(129,'2025_01_26_100341_create_translate_utilities_table',1),(130,'2025_01_26_103530_create_currencies_table',1),(131,'2025_01_26_112926_change_password_column_to_users_table',1),(132,'2025_01_26_113220_create_product_translations_table',1),(133,'2025_01_26_155516_add_currency_id_column_to_generate_settings_table',1),(134,'2025_01_28_174131_add_column_to_verify_manages_table',1),(135,'2025_02_03_111244_change_column_to_shops_table',1),(136,'2025_02_13_111656_create_menus_table',1),(137,'2025_02_16_100246_create_pages_table',1),(138,'2025_02_17_101904_add_column_to_verify_manages_table',1),(139,'2025_02_17_102712_create_countries_table',1),(140,'2025_02_18_160537_create_order_vat_taxes_table',1),(141,'2025_02_22_111431_add_column_to_generate_settings_table',1),(142,'2025_02_22_152711_create_footers_table',1),(143,'2025_02_22_154855_create_footer_items_table',1),(144,'2025_04_17_105545_create_subscription_plans_table',1),(145,'2025_04_17_105627_create_shop_subscriptions_table',1),(146,'2025_05_04_111353_create_shop_user_table',1),(147,'2025_05_04_111844_create_shop_user_chats_table',1),(148,'2025_05_06_150402_add_column_to_generate_settings_table',1),(149,'2025_05_06_174546_add_last_online_column_to_users_table',1),(150,'2025_05_06_183511_add_last_online_column_to_shops_table',1),(151,'2025_07_02_165205_create_paypal_payments_table',1),(152,'2025_07_14_153807_add_column_to_social_links_table',1),(153,'2025_07_16_142728_add_column_to_pos_carts_table',1),(154,'2025_08_11_180401_add_new_coloum_to_generate_settings',1),(155,'2025_08_18_165743_add_new_coloum_to_generate_settings',1),(156,'2025_08_27_152721_create_return_orders_table',1),(157,'2025_08_27_153102_create_return_order_details_table',1),(158,'2025_08_28_124115_add_new_coloum_to_generate_settings_table',1),(159,'2025_09_25_180903_create_product_attachments_table',1),(160,'2025_09_25_181339_add_column_to_products_table',1),(161,'2025_09_28_124136_create_product_licenses_table',1),(162,'2025_09_29_130047_add_column_to_product_licenses_table',1),(163,'2025_10_01_112743_add_column_to_product_licenses_table',1),(164,'2025_11_02_153428_create_module_settings_table',1),(165,'2025_11_25_103004_create_cart_access_tokens_table',1),(166,'2025_12_15_163627_add_softdelete_to_drivers_table',1),(167,'2025_12_15_163653_add_softdelete_to_shops_table',1),(168,'2025_12_25_103350_add_coloums_to_order_products_table',1),(169,'2025_12_27_062612_add_index_to_products_table',1),(170,'2025_12_29_154611_create_driver_locations_table',1),(171,'2026_01_04_112219_create_areas_table',1),(172,'2026_01_04_131609_add_area_to_addresses_table',1),(173,'2026_01_04_152627_add_area_to_orders_table',1),(174,'2026_01_26_134407_add_soft_delete_to_products_table',1),(175,'2026_01_29_150919_add_ar_to_menus_table',1),(176,'2026_01_29_154737_add_titlear_to_footers_table',1),(177,'2026_01_29_154810_add_titlear_to_footer_items_table',1),(178,'2026_07_27_000001_create_warehouses_tables',1),(179,'2026_07_30_212249_drop_shop_id_from_warehouses_table',1),(180,'2026_08_01_000001_add_parent_shop_id_to_shops_table',2),(181,'2026_08_01_000002_create_shop_monthly_payouts_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(3,'App\\Models\\User',1),(3,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4),(3,'App\\Models\\User',5),(3,'App\\Models\\User',6),(3,'App\\Models\\User',7),(1,'App\\Models\\User',8);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_settings`
--

DROP TABLE IF EXISTS `module_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_settings` (
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_first` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_settings`
--

LOCK TABLES `module_settings` WRITE;
/*!40000 ALTER TABLE `module_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `withdraw_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_shop_id_foreign` (`shop_id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  KEY `notifications_withdraw_id_foreign` (`withdraw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_payments`
--

DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_payments` (
  `order_id` bigint(20) unsigned NOT NULL,
  `payment_id` bigint(20) unsigned NOT NULL,
  KEY `order_payments_order_id_foreign` (`order_id`),
  KEY `order_payments_payment_id_foreign` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_payments`
--

LOCK TABLES `order_payments` WRITE;
/*!40000 ALTER TABLE `order_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `color` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` double DEFAULT NULL,
  `buying_price` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_products_product_id_foreign` (`product_id`),
  KEY `order_products_order_id_product_id_index` (`order_id`,`product_id`),
  KEY `order_products_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_products`
--

LOCK TABLES `order_products` WRITE;
/*!40000 ALTER TABLE `order_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_vat_taxes`
--

DROP TABLE IF EXISTS `order_vat_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_vat_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `percentage` double NOT NULL DEFAULT '0',
  `amount` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_vat_taxes_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_vat_taxes`
--

LOCK TABLES `order_vat_taxes` WRITE;
/*!40000 ALTER TABLE `order_vat_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_vat_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `pos_order` tinyint(1) NOT NULL DEFAULT '0',
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `order_code` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `prefix` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_discount` double DEFAULT NULL,
  `pick_date` timestamp NULL DEFAULT NULL,
  `delivery_date` timestamp NULL DEFAULT NULL,
  `payable_amount` double NOT NULL,
  `total_amount` double NOT NULL,
  `tax_amount` double DEFAULT '0',
  `discount` double DEFAULT '0',
  `delivery_charge` double NOT NULL DEFAULT '0',
  `payment_status` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `order_status` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `payment_method` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `instruction` longtext COLLATE utf8_unicode_ci,
  `invoice_path` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `admin_commission` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_area` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_shop_id_foreign` (`shop_id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  KEY `orders_address_id_foreign` (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,0,1,'9686','RC',1,50.68,'2025-12-17 04:01:37','2026-07-24 12:35:09',86.85,80000,0,15.67,22.57,'Paid','Delivered','PayU',1,NULL,NULL,NULL,0,'2026-07-15 06:30:00','2026-07-15 06:30:00',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_editable` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'Products','products','products',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'Shops','shops','shops',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Most Popular','most-popular','most-popular',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'Digital Products','digital-products','digital-products',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,'Best Deal','best-deal','best-deal',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(6,'Contact','contact-us','contact-us',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(7,'Blogs','blogs','blogs',NULL,1,0,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(8,'About Us','about-us','about-us','<html><head><title>Voluptatem corrupti.</title></head><body><form action=\"example.com\" method=\"POST\"><label for=\"username\">aut</label><input type=\"text\" id=\"username\"><label for=\"password\">sed</label><input type=\"password\" id=\"password\"></form><div class=\"natus\"></div><div id=\"12653\"><div id=\"2555\"></div><div class=\"omnis\"></div><div id=\"62142\"><p>Excepturi qui.</p><table><thead><tr><th>Veniam aut.</th></tr></thead><tbody><tr><td>Ab aperiam et alias.</td></tr><tr><td>Officia laborum sequi perspiciatis qui.</td></tr><tr><td>Molestiae quis vitae.</td></tr></tbody></table><p>Deleniti ea odio eaque autem et asperiores et et.</p></div><div id=\"75008\"></div><div class=\"non\"><p>Voluptatem explicabo id sint non accusantium.</p></div><div id=\"14200\"></div><div class=\"autem\"></div></div><div class=\"sint\"><div class=\"id\"></div><div class=\"ut\"><table><thead><tr><th>Aut officia officia sequi.</th><th>Quasi.</th><th>Rerum.</th><th>Eos ut accusantium magni.</th><th>Architecto architecto illo.</th><th>Modi eum cum.</th></tr></thead><tbody><tr><td>Voluptas in est atque perspiciatis aliquid quod illo.</td><td>Dolor consequuntur laborum.</td><td>Ab possimus et totam facilis consequatur.</td><td>Voluptate adipisci et nihil eius.</td><td>Aliquid consectetur officia repellat nihil quaerat.</td><td>Omnis.</td></tr><tr><td>Sint ullam sint.</td><td>Optio nam.</td><td>Qui ratione sunt ipsum error rerum corporis earum dignissimos aut.</td><td>Iusto.</td><td>Dolorem dolor.</td><td>Accusantium officiis debitis quibusdam voluptatibus.</td></tr><tr><td>Architecto enim nisi labore rerum nihil blanditiis totam.</td><td>At beatae porro beatae quae perspiciatis enim mollitia.</td><td>Eos autem sunt nulla dignissimos.</td><td>Repellendus corporis est labore adipisci.</td><td>Aut itaque veritatis.</td><td>Odit nulla reiciendis qui.</td></tr><tr><td>Voluptatem ducimus eum et et ut aut quasi libero.</td><td>Sint quas labore qui.</td><td>Aliquid voluptatibus omnis quod nostrum provident itaque quam ut rem.</td><td>Autem quod asperiores.</td><td>Voluptatem impedit ratione amet dolorem pariatur pariatur soluta eius.</td><td>Veritatis reprehenderit et.</td></tr><tr><td>Quisquam ab et provident eum eaque minima.</td><td>Delectus quas.</td><td>Rerum rem impedit vel illo autem facere praesentium voluptas enim mollitia.</td><td>Aut ad consectetur aut.</td><td>Dolores ut expedita qui itaque qui mollitia sed et consectetur.</td><td>Excepturi amet et in pariatur.</td></tr><tr><td>Ut et repellat voluptatibus.</td><td>Voluptas ut id eum magni rerum qui facilis sapiente.</td><td>Non in aliquid modi sint est vel.</td><td>Facilis ducimus placeat illo voluptatem est quod dignissimos.</td><td>Aliquid ut incidunt et corporis quos suscipit.</td><td>Quod ea quo.</td></tr><tr><td>Maxime molestiae totam quia quo.</td><td>Deleniti ea eos repudiandae rerum dolorem fugit incidunt blanditiis doloribus quo laborum.</td><td>Modi dignissimos.</td><td>Magnam ex ut natus eaque placeat quod.</td><td>Et occaecati neque veniam labore minima in nemo porro veniam.</td><td>Dignissimos accusantium.</td></tr><tr><td>Sunt omnis deleniti quam sit.</td><td>Asperiores cum recusandae enim consequuntur omnis hic quia ratione sapiente voluptatibus eligendi.</td><td>Consequatur soluta hic veritatis fugiat.</td><td>Autem voluptates perferendis ea.</td><td>Aut aut ullam optio eos autem cupiditate.</td><td>Nam quidem harum modi asperiores debitis libero et voluptate.</td></tr><tr><td>Iure voluptates beatae vero aliquid voluptas.</td><td>Nihil nesciunt.</td><td>Error voluptatibus ab ut.</td><td>Voluptatem et sequi corrupti sint nisi quae quo quos et.</td><td>Distinctio eveniet facere neque.</td><td>Velit ex nisi recusandae ducimus vitae voluptatem.</td></tr></tbody></table><a href=\"example.net\">Explicabo culpa et ut totam officia ad.</a><i>Illum enim sed eius laboriosam incidunt enim velit.</i><i>Dolorem earum odit sint minus rem tenetur dicta quis delectus illo ut perspiciatis velit.</i><h2>Totam facere est rerum error reprehenderit reprehenderit non sed est hic.</h2><ul><li>Pariatur.</li><li>Eligendi quasi ut eius quas.</li><li>Tempore id.</li><li>Architecto suscipit quam tenetur eligendi vel.</li><li>Inventore nihil saepe amet reprehenderit aut.</li><li>Nesciunt quibusdam.</li></ul></div><div class=\"repellendus\"></div><div id=\"63198\"></div><div id=\"96889\"></div><div id=\"41201\"></div><div id=\"89697\"></div><div class=\"qui\"></div></div><div class=\"nesciunt\"></div><div id=\"52579\"></div><div class=\"voluptatem\"><div id=\"4575\"></div></div></body></html>\n',1,1,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(9,'Privacy Policy','privacy-policy','privacy-policy','<html><head><title>Incidunt facere est eligendi incidunt qui.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">consectetur</label><input type=\"text\" id=\"username\"><label for=\"password\">quod</label><input type=\"password\" id=\"password\"></form><div class=\"consequatur\"></div><div class=\"animi\"><div class=\"tempora\"><span>Maiores quaerat officia odit quis ea suscipit tempore.</span></div><div id=\"91291\"><a href=\"example.net\">Itaque.</a><i>Reprehenderit minus pariatur ut.</i></div><div id=\"47630\"></div><div id=\"13577\"></div><div id=\"79251\"></div></div><div class=\"est\"><i>Rerum eos nisi distinctio qui vel.</i><span>Vitae velit et perferendis dolore dolor cumque exercitationem quia.</span><a href=\"example.com\">Id at sed voluptas.</a><p>Aut sed voluptate molestiae omnis perspiciatis consequatur laboriosam.</p><p>Pariatur autem quia.</p></div></body></html>\n',1,1,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(10,'Terms of Service','terms-and-conditions','terms-and-conditions','<html><head><title>Rerum commodi est ut ea sunt natus dolor consequatur nihil culpa aliquid labore.</title></head><body><form action=\"example.net\" method=\"POST\"><label for=\"username\">illo</label><input type=\"text\" id=\"username\"><label for=\"password\">consequatur</label><input type=\"password\" id=\"password\"></form><div id=\"57689\"><div class=\"quis\"></div><div id=\"47422\">Libero assumenda modi amet blanditiis reiciendis labore rerum ut quos vel voluptate in.<p>Sapiente consequatur.</p></div><div id=\"48859\"></div><div id=\"83584\"></div><div class=\"ab\"></div></div><div id=\"77533\"><div id=\"29554\"></div><div id=\"88483\"></div><div id=\"81993\"></div><div id=\"8408\"></div><div id=\"15793\"></div><div id=\"33290\"><i>Repudiandae corrupti similique velit.</i><b>Qui a qui cum cupiditate dolorem natus aut optio vero.</b><i>Voluptates ea.</i><h3>Reprehenderit eligendi iste dolor.</h3><h3>Vitae aliquid rerum magni veniam vel sequi.</h3><span>Ut.</span><a href=\"example.org\">Est molestiae sed ut.</a><a href=\"example.net\">Vero est.</a></div><div id=\"39812\"></div><div class=\"sit\">Sunt sit at dolores fugiat quidem est.Voluptas dolor aut.<b>Ut eveniet in eius et qui quos totam.</b><table><thead><tr><th>Laborum unde.</th></tr></thead><tbody><tr><td>Ea dicta voluptatem est accusantium vel veritatis et tempore commodi.</td></tr><tr><td>Et fugit unde doloremque quod quia modi dolores doloribus saepe explicabo.</td></tr><tr><td>Quia doloremque ut voluptatem.</td></tr><tr><td>Eius consequatur repellendus sed qui natus.</td></tr><tr><td>Aut suscipit fugit eaque expedita beatae recusandae voluptatem.</td></tr><tr><td>Maxime.</td></tr><tr><td>Necessitatibus veniam possimus fugit quos a.</td></tr><tr><td>Rerum voluptatem eum eligendi iure omnis voluptatem sit sed veniam porro illo.</td></tr><tr><td>Hic aliquid iusto.</td></tr><tr><td>Suscipit eaque reiciendis alias modi aut sunt aut repudiandae nostrum.</td></tr></tbody></table><table><thead><tr><th>Sit exercitationem.</th><th>Sint sequi.</th><th>Unde.</th><th>Ut.</th><th>Officia ut.</th></tr></thead><tbody><tr><td>Et nihil omnis commodi reiciendis debitis commodi et.</td><td>Corporis illo nam quia nobis.</td><td>Expedita fuga.</td><td>Quam corrupti et qui harum vel rem ut.</td><td>Omnis est aliquam aut iste consequuntur officia est est voluptas.</td></tr><tr><td>Reiciendis aut et qui corporis dolore quia.</td><td>Velit est quaerat aliquid.</td><td>Numquam sint consequatur dolorem ex corporis corrupti totam quisquam vero.</td><td>Qui aliquam est amet sint.</td><td>Et et et sunt perferendis sint.</td></tr><tr><td>Unde minima pariatur.</td><td>Repellat et id recusandae cupiditate illum.</td><td>Voluptas id ab aperiam dicta commodi numquam.</td><td>Sit ut temporibus.</td><td>Doloremque possimus fugit dicta modi.</td></tr><tr><td>Facere iure aliquam sint culpa doloribus.</td><td>Quia ullam.</td><td>Laboriosam enim laudantium suscipit.</td><td>Magnam consequatur.</td><td>Possimus commodi sit provident enim accusantium ut modi consequatur et.</td></tr></tbody></table>Consequatur quia excepturi velit aut assumenda.<span>In.</span><p>Qui sit repudiandae.</p><ul><li>Dignissimos atque quo.</li><li>Voluptatem et odit ut.</li><li>Et doloribus nihil cumque quia blanditiis.</li><li>Culpa possimus.</li><li>Eaque.</li><li>Quisquam doloremque officia quo.</li><li>Earum rem accusamus.</li><li>Sit nostrum nam.</li><li>In sit.</li></ul></div><div id=\"24597\"></div></div><div class=\"ipsam\"><div class=\"optio\"></div><div id=\"19914\"></div><div id=\"80389\">Culpa rerum hic illum eius.<h2>Et aliquam cumque doloremque voluptates reiciendis.</h2><b>Rem tenetur.</b></div><div class=\"qui\"></div><div id=\"43334\"></div><div id=\"77544\"><ul><li>Ut culpa ad natus.</li><li>Accusantium.</li><li>Dolorum et eveniet et.</li><li>Qui nihil et.</li><li>Qui quia provident.</li><li>Quia sunt vel.</li><li>Aut quibusdam cum architecto ea.</li><li>Qui expedita reprehenderit commodi facilis.</li></ul><a href=\"example.com\">Culpa corporis aperiam aut in dolorem.</a><b>Nulla est voluptatem quis sint consectetur laudantium illum nemo.</b><i>Rerum hic.</i><i>Sed et consequatur totam est eum est enim fugiat.</i><h1>Labore enim aut rerum qui rem ad nam.</h1><h1>Voluptatibus qui.</h1></div><div id=\"19045\"></div><div id=\"55988\"></div><div id=\"72542\"></div></div><div id=\"51728\"><span>Eum sed qui aut neque ut inventore aperiam sint et.</span><b>Aspernatur qui voluptas exercitationem facere amet.</b><b>Enim aut.</b><p>Dolores.</p>Quaerat sit perspiciatis aliquam ut nemo nam laborum.</div></body></html>\n',1,1,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(11,'Return policy / Refund Policy','return-and-refund-policy','page/return-and-refund-policy','<html><head><title>Voluptas autem itaque eos repudiandae optio quis fugiat quae aut.</title></head><body><form action=\"example.net\" method=\"POST\"><label for=\"username\">odit</label><input type=\"text\" id=\"username\"><label for=\"password\">maiores</label><input type=\"password\" id=\"password\"></form><div id=\"52783\"><div class=\"qui\"></div><div class=\"recusandae\"></div><div class=\"veniam\"></div></div><div class=\"architecto\"></div><div class=\"quo\"></div><div class=\"nobis\"></div><div id=\"20687\"></div><div id=\"33756\"></div><div id=\"14537\"><h1>Ut pariatur nesciunt dolor voluptatem et occaecati.</h1></div></body></html>\n',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(12,'Shipping & Delivery Policy','shipping-and-delivery-policy','page/shipping-and-delivery-policy','<html><head><title>Quae magnam velit harum odit labore.</title></head><body><form action=\"example.net\" method=\"POST\"><label for=\"username\">architecto</label><input type=\"text\" id=\"username\"><label for=\"password\">nemo</label><input type=\"password\" id=\"password\"></form><div id=\"57802\"></div><div id=\"95254\"><div class=\"officiis\"></div><div id=\"30048\"></div><div id=\"30470\"><a href=\"example.org\">Non molestias et numquam dolores fugiat praesentium dolores et fuga expedita.</a><span>Labore cupiditate sit.</span><h1>Eos dolorem aspernatur quidem numquam molestiae optio.</h1><span>Ipsum accusantium est numquam iste rem sapiente.</span><span>Quod quod ullam est aut suscipit ut asperiores libero.</span><i>Harum accusantium quisquam vel id.</i></div></div></body></html>\n',1,1,0,'2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateways`
--

DROP TABLE IF EXISTS `payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_gateways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `config` json DEFAULT NULL,
  `mode` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'test' COMMENT 'test or live',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `alias` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'controller nameSpace',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_gateways_media_id_foreign` (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateways`
--

LOCK TABLES `payment_gateways` WRITE;
/*!40000 ALTER TABLE `payment_gateways` DISABLE KEYS */;
INSERT INTO `payment_gateways` VALUES (1,'Stripe','stripe',NULL,'{\"secret_key\": \"\", \"published_key\": \"\"}','test',1,'Stripe','2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'PayPal','paypal',NULL,'{\"client_id\": \"\", \"client_secret\": \"\"}','test',1,'PayPal','2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Razorpay','razorpay',NULL,'{\"key\": \"\", \"secret\": \"\"}','test',1,'Razorpay','2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'Paystack','paystack',NULL,'{\"public_key\": \"\", \"secret_key\": \"\", \"machant_email\": \"\"}','test',1,'PayStack','2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,'aamarPay','aamarpay',NULL,'{\"store_id\": \"\", \"signature_key\": \"\"}','test',1,'AamarPay','2026-07-31 13:44:43','2026-07-31 13:44:43'),(6,'BKash','bKash',NULL,'{\"app_key\": \"\", \"password\": \"\", \"username\": \"\", \"app_secret_key\": \"\"}','test',1,'Bkash','2026-07-31 13:44:43','2026-07-31 13:44:43'),(7,'PayTabs','paytabs',NULL,'{\"base_url\": \"https://secure-global.paytabs.com\", \"currency\": \"USD\", \"profile_id\": \"\", \"server_key\": \"\"}','test',1,'PayTabs','2026-07-31 13:44:43','2026-07-31 13:44:43'),(8,'QiCard','qicard',NULL,'{\"currency\": \"IQD\", \"password\": \"\", \"username\": \"\", \"terminalId\": \"\"}','test',1,'QiCard','2026-07-31 13:44:43','2026-07-31 13:44:43'),(9,'PayU','payu',NULL,'{\"base_url\": \"https://secure.payu.in/_payment\", \"merchant_key\": \"\", \"merchant_salt\": \"\"}','test',1,'PayU','2026-07-31 13:44:43','2026-07-31 13:44:43'),(10,'CashFree','cashfree',NULL,'{\"app_id\": \"\", \"base_url\": \"https://api.cashfree.com/pg/orders\", \"secret_key\": \"\"}','test',1,'CashFree','2026-07-31 13:44:43','2026-07-31 13:44:43'),(11,'JazzCash','jazzcash',NULL,'{\"note\": \"You have to setup this return URL in your JazzCash merchant account dashboard: http://localhost:8888/janmitram-app/payment/success\", \"base_url\": \"https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform\", \"password\": \"\", \"merchant_id\": \"\", \"integrity_salt\": \"\"}','test',1,'JazzCash','2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL DEFAULT '0',
  `currency` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'USD',
  `payment_method` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'cash',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_token` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paypal_payments`
--

DROP TABLE IF EXISTS `paypal_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paypal_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `order_id` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paypal_payments_payment_id_foreign` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paypal_payments`
--

LOCK TABLES `paypal_payments` WRITE;
/*!40000 ALTER TABLE `paypal_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `paypal_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=343 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'admin.shop.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(2,'admin.shop.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(3,'admin.shop.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(4,'admin.shop.status.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(5,'admin.shop.show','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(6,'admin.shop.orders','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(7,'admin.shop.products','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(8,'admin.shop.reset.password','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(9,'admin.product.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(10,'admin.product.approve','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(11,'admin.product.show','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(12,'admin.product.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(13,'admin.coupon.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(14,'admin.coupon.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(15,'admin.coupon.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(16,'admin.coupon.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(17,'admin.withdraw.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(18,'admin.withdraw.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(19,'admin.withdraw.show','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(20,'admin.subscription-plan.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(21,'admin.subscription-plan.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(22,'admin.subscription-plan.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(23,'admin.subscription-plan.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(24,'admin.subscription-plan.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(25,'admin.subscription-plan.subscription.list','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(26,'admin.subscription-plan.subscription.status','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(27,'admin.dashboard.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(28,'admin.dashboard.notification','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(29,'admin.banner.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(30,'admin.banner.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(31,'admin.banner.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(32,'admin.banner.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(33,'admin.banner.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(34,'admin.ad.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(35,'admin.ad.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(36,'admin.ad.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(37,'admin.ad.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(38,'admin.ad.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(39,'admin.order.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(40,'admin.order.show','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(41,'admin.order.status.change','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(42,'admin.order.payment.status.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(43,'admin.order.assign.rider','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(44,'admin.review.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(45,'admin.review.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(46,'admin.brand.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(47,'admin.brand.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(48,'admin.brand.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(49,'admin.brand.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(50,'admin.brand.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(51,'admin.color.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(52,'admin.color.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(53,'admin.color.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(54,'admin.color.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(55,'admin.color.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(56,'admin.size.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(57,'admin.size.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(58,'admin.size.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(59,'admin.size.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(60,'admin.size.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(61,'admin.unit.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(62,'admin.unit.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(63,'admin.unit.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(64,'admin.unit.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(65,'admin.unit.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(66,'admin.category.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(67,'admin.category.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(68,'admin.category.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(69,'admin.category.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(70,'admin.category.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(71,'admin.subcategory.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(72,'admin.subcategory.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(73,'admin.subcategory.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(74,'admin.subcategory.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(75,'admin.subcategory.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(76,'admin.flashSale.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(77,'admin.flashSale.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(78,'admin.flashSale.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(79,'admin.flashSale.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(80,'admin.flashSale.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(81,'admin.generale-setting.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(82,'admin.generale-setting.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(83,'admin.business-setting.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(84,'admin.business-setting.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(85,'admin.verification.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(86,'admin.verification.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(87,'admin.socialLink.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(88,'admin.socialLink.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(89,'admin.socialLink.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(90,'admin.socialAuth.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(91,'admin.socialAuth.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(92,'admin.socialAuth.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(93,'admin.menu.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(94,'admin.menu.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(95,'admin.menu.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(96,'admin.menu.remove','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(97,'admin.menu.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(98,'admin.page.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(99,'admin.page.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(100,'admin.page.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(101,'admin.page.show','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(102,'admin.page.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(103,'admin.page.generate.AI.data','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(104,'admin.country.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(105,'admin.country.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(106,'admin.country.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(107,'admin.country.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(108,'admin.area.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(109,'admin.area.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(110,'admin.area.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(111,'admin.area.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(112,'admin.area.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(113,'admin.currency.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(114,'admin.currency.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(115,'admin.currency.edit','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(116,'admin.currency.toggle','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(117,'admin.currency.destroy','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(118,'admin.themeColor.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(119,'admin.themeColor.update','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(120,'admin.themeColor.change','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(121,'admin.deliveryCharge.index','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(122,'admin.deliveryCharge.create','web','2026-07-31 13:44:41','2026-07-31 13:44:41'),(123,'admin.deliveryCharge.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(124,'admin.deliveryCharge.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(125,'admin.pusher.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(126,'admin.pusher.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(127,'admin.mailConfig.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(128,'admin.mailConfig.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(129,'admin.paymentGateway.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(130,'admin.paymentGateway.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(131,'admin.paymentGateway.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(132,'admin.sms-gateway.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(133,'admin.sms-gateway.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(134,'admin.googleReCaptcha.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(135,'admin.googleReCaptcha.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(136,'admin.contactUs.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(137,'admin.contactUs.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(138,'admin.firebase.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(139,'admin.firebase.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(140,'admin.profile.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(141,'admin.profile.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(142,'admin.profile.change-password','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(143,'admin.rider.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(144,'admin.rider.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(145,'admin.rider.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(146,'admin.rider.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(147,'admin.rider.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(148,'admin.rider.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(149,'admin.rider.assign.order','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(150,'admin.customer.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(151,'admin.customer.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(152,'admin.customer.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(153,'admin.customer.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(154,'admin.customer.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(155,'admin.customer.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(156,'admin.customer.reset.password','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(157,'admin.customerNotification.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(158,'admin.customerNotification.send','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(159,'admin.language.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(160,'admin.language.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(161,'admin.language.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(162,'admin.language.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(163,'admin.language.export','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(164,'admin.language.import','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(165,'admin.employee.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(166,'admin.employee.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(167,'admin.employee.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(168,'admin.employee.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(169,'admin.employee.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(170,'admin.employee.reset.password','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(171,'admin.employee.permission','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(172,'admin.employee.permission.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(173,'admin.role.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(174,'admin.role.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(175,'admin.role.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(176,'admin.role.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(177,'admin.role.permission','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(178,'admin.role.permission.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(179,'admin.ticketIssueType.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(180,'admin.ticketIssueType.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(181,'admin.ticketIssueType.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(182,'admin.ticketIssueType.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(183,'admin.ticketIssueType.delete','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(184,'admin.supportTicket.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(185,'admin.supportTicket.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(186,'admin.supportTicket.setScheduled','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(187,'admin.supportTicket.sendMessage','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(188,'admin.supportTicket.updateStatus','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(189,'admin.supportTicket.pinMessage','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(190,'admin.supportTicket.chatToggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(191,'admin.support.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(192,'admin.support.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(193,'admin.vatTax.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(194,'admin.vatTax.order.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(195,'admin.vatTax.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(196,'admin.vatTax.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(197,'admin.vatTax.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(198,'admin.vatTax.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(199,'admin.blog.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(200,'admin.blog.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(201,'admin.blog.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(202,'admin.blog.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(203,'admin.blog.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(204,'admin.blog.generate.AI.data','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(205,'admin.aiPrompt.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(206,'admin.aiPrompt.configure','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(207,'admin.aiPrompt.configure.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(208,'admin.aiPrompt.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(209,'admin.returnOrder.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(210,'admin.returnOrder.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(211,'admin.returnOrder.payment.status','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(212,'admin.returnOrder.reject','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(213,'admin.conversation.customer.chat.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(214,'admin.conversation.getUsers','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(215,'admin.conversation.getMessageAdmin','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(216,'admin.conversation.sendMessageAdmin','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(217,'admin.warehouse.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(218,'admin.warehouse.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(219,'admin.warehouse.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(220,'admin.warehouse.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(221,'admin.warehouse.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(222,'admin.warehouse.stock','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(223,'admin.warehouse.stock.add','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(224,'admin.stock-request.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(225,'admin.stock-request.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(226,'admin.stock-request.approve','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(227,'admin.stock-request.reject','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(228,'admin.warehouse-transfer.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(229,'admin.warehouse-transfer.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(230,'admin.warehouse-transfer.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(231,'admin.warehouse-transfer.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(232,'admin.warehouse-transfer.complete','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(233,'admin.warehouse-transfer.cancel','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(234,'shop.order.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(235,'shop.order.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(236,'shop.order.status.change','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(237,'shop.product.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(238,'shop.product.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(239,'shop.product.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(240,'shop.product.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(241,'shop.product.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(242,'shop.product.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(243,'shop.product.barcode','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(244,'shop.product.generate.AI.data','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(245,'shop.flashSale.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(246,'shop.flashSale.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(247,'shop.flashSale.productStore','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(248,'shop.flashSale.productRemove','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(249,'shop.flashSale.product.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(250,'shop.voucher.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(251,'shop.voucher.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(252,'shop.voucher.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(253,'shop.voucher.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(254,'shop.voucher.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(255,'shop.bulk-product-import.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(256,'shop.bulk-product-import.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(257,'shop.bulk-product-export.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(258,'shop.bulk-product-export.demo','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(259,'shop.bulk-product-export.export','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(260,'shop.gallery.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(261,'shop.gallery.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(262,'shop.pos.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(263,'shop.pos.sales','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(264,'shop.pos.draft','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(265,'shop.employee.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(266,'shop.employee.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(267,'shop.employee.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(268,'shop.employee.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(269,'shop.employee.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(270,'shop.employee.reset.password','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(271,'shop.employee.permission','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(272,'shop.employee.permission.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(273,'shop.profile.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(274,'shop.profile.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(275,'shop.profile.change-password','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(276,'shop.returnOrder.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(277,'shop.returnOrder.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(278,'shop.returnOrder.status.change','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(279,'shop.supplier.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(280,'shop.supplier.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(281,'shop.supplier.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(282,'shop.supplier.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(283,'shop.supplier.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(284,'shop.supplier.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(285,'shop.supplier.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(286,'shop.supplier.toggle','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(287,'shop.supplier.statistic','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(288,'shop.supplier.payment','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(289,'shop.purchase.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(290,'shop.purchase.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(291,'shop.purchase.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(292,'shop.purchase.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(293,'shop.purchase.edit','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(294,'shop.purchase.update','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(295,'shop.purchase.destroy','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(296,'shop.purchase.attach.product','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(297,'shop.purchase.products','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(298,'shop.purchase.makeReceived','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(299,'shop.purchase.product.delete.barcode','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(300,'shop.purchase.invoice.search','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(301,'shop.purchase.invoice.add','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(302,'shop.purchase.summary','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(303,'shop.purchase.purchaseInvoice','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(304,'shop.purchase.allProduct.stockSummary','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(305,'shop.purchaseReturn.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(306,'shop.purchaseReturn.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(307,'shop.purchaseReturn.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(308,'shop.purchaseReturn.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(309,'shop.purchaseReturn.invoice.search','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(310,'shop.purchaseReturn.Invoice','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(311,'shop.purchaseReturn.invoice.add','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(312,'shop.stock-request.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(313,'shop.stock-request.create','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(314,'shop.stock-request.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(315,'shop.stock-request.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(316,'shop.dashboard.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(317,'shop.dashboard.notification','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(318,'shop.subscription.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(319,'shop.subscription.purchase','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(320,'shop.subscription.renew','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(321,'shop.subscription.switch','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(322,'shop.subscription.cancel','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(323,'shop.brand.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(324,'shop.color.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(325,'shop.size.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(326,'shop.unit.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(327,'shop.category.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(328,'shop.subcategory.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(329,'shop.withdraw.index','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(330,'shop.withdraw.store','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(331,'shop.withdraw.show','web','2026-07-31 13:44:42','2026-07-31 13:44:42'),(332,'admin.payout.index','web','2026-08-01 10:03:26','2026-08-01 10:03:26'),(333,'admin.payout.run','web','2026-08-01 10:03:26','2026-08-01 10:03:26'),(334,'admin.payout.network','web','2026-08-01 11:07:33','2026-08-01 11:07:33'),(335,'admin.payout.guide','web','2026-08-01 11:11:12','2026-08-01 11:11:12'),(336,'admin.project-guide.index','web','2026-08-01 11:41:02','2026-08-01 11:41:02'),(337,'shop.payout.index','web','2026-08-01 14:13:03','2026-08-01 14:13:03'),(338,'shop.payout.network','web','2026-08-01 14:13:03','2026-08-01 14:13:03'),(339,'admin.payout.slip','web','2026-08-01 15:15:05','2026-08-01 15:15:05'),(340,'shop.payout.slip','web','2026-08-01 15:15:06','2026-08-01 15:15:06'),(341,'shop.payout.network.create','web','2026-08-01 16:17:21','2026-08-01 16:17:21'),(342,'shop.payout.network.store','web','2026-08-01 16:17:21','2026-08-01 16:17:21');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_cart_products`
--

DROP TABLE IF EXISTS `pos_cart_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_cart_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pos_cart_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `brand` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_cart_products_pos_cart_id_foreign` (`pos_cart_id`),
  KEY `pos_cart_products_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_cart_products`
--

LOCK TABLES `pos_cart_products` WRITE;
/*!40000 ALTER TABLE `pos_cart_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `pos_cart_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_carts`
--

DROP TABLE IF EXISTS `pos_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `note` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_carts_shop_id_foreign` (`shop_id`),
  KEY `pos_carts_user_id_foreign` (`user_id`),
  KEY `pos_carts_coupon_id_foreign` (`coupon_id`),
  KEY `pos_carts_created_by_foreign` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_carts`
--

LOCK TABLES `pos_carts` WRITE;
/*!40000 ALTER TABLE `pos_carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `pos_carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attachments`
--

DROP TABLE IF EXISTS `product_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attachments` (
  `product_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `product_attachments_product_id_foreign` (`product_id`),
  KEY `product_attachments_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attachments`
--

LOCK TABLES `product_attachments` WRITE;
/*!40000 ALTER TABLE `product_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_categories` (
  `product_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  KEY `product_categories_product_id_foreign` (`product_id`),
  KEY `product_categories_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,1),(2,1),(3,1);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_colors`
--

DROP TABLE IF EXISTS `product_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_colors` (
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT '0',
  KEY `product_colors_product_id_foreign` (`product_id`),
  KEY `product_colors_color_id_foreign` (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_colors`
--

LOCK TABLES `product_colors` WRITE;
/*!40000 ALTER TABLE `product_colors` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_licenses`
--

DROP TABLE IF EXISTS `product_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_licenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `product_license` text COLLATE utf8_unicode_ci,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_licenses_product_id_foreign` (`product_id`),
  KEY `product_licenses_user_id_foreign` (`user_id`),
  KEY `product_licenses_order_id_foreign` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_licenses`
--

LOCK TABLES `product_licenses` WRITE;
/*!40000 ALTER TABLE `product_licenses` DISABLE KEYS */;
INSERT INTO `product_licenses` VALUES (1,1,NULL,NULL,NULL,0,0,'2026-08-02 16:33:26','2026-08-02 16:33:26');
/*!40000 ALTER TABLE `product_licenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_sizes`
--

DROP TABLE IF EXISTS `product_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_sizes` (
  `product_id` bigint(20) unsigned NOT NULL,
  `size_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT NULL,
  KEY `product_sizes_product_id_foreign` (`product_id`),
  KEY `product_sizes_size_id_foreign` (`size_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_sizes`
--

LOCK TABLES `product_sizes` WRITE;
/*!40000 ALTER TABLE `product_sizes` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_subcategories`
--

DROP TABLE IF EXISTS `product_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_subcategories` (
  `sub_category_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  KEY `product_subcategories_sub_category_id_foreign` (`sub_category_id`),
  KEY `product_subcategories_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_subcategories`
--

LOCK TABLES `product_subcategories` WRITE;
/*!40000 ALTER TABLE `product_subcategories` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_thumbnails`
--

DROP TABLE IF EXISTS `product_thumbnails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_thumbnails` (
  `product_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  KEY `product_thumbnails_product_id_foreign` (`product_id`),
  KEY `product_thumbnails_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_thumbnails`
--

LOCK TABLES `product_thumbnails` WRITE;
/*!40000 ALTER TABLE `product_thumbnails` DISABLE KEYS */;
INSERT INTO `product_thumbnails` VALUES (1,8),(3,8);
/*!40000 ALTER TABLE `product_thumbnails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_translations`
--

DROP TABLE IF EXISTS `product_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `lang` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_translations_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_translations`
--

LOCK TABLES `product_translations` WRITE;
/*!40000 ALTER TABLE `product_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_units`
--

DROP TABLE IF EXISTS `product_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_units` (
  `product_id` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  KEY `product_units_product_id_foreign` (`product_id`),
  KEY `product_units_unit_id_foreign` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_units`
--

LOCK TABLES `product_units` WRITE;
/*!40000 ALTER TABLE `product_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_vat_taxes`
--

DROP TABLE IF EXISTS `product_vat_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_vat_taxes` (
  `product_id` bigint(20) unsigned NOT NULL,
  `vat_tax_id` bigint(20) unsigned NOT NULL,
  KEY `product_vat_taxes_product_id_foreign` (`product_id`),
  KEY `product_vat_taxes_vat_tax_id_foreign` (`vat_tax_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_vat_taxes`
--

LOCK TABLES `product_vat_taxes` WRITE;
/*!40000 ALTER TABLE `product_vat_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_vat_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `price` double NOT NULL,
  `buy_price` double DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `min_order_quantity` int(11) NOT NULL DEFAULT '1',
  `discount_price` double DEFAULT NULL,
  `short_description` text COLLATE utf8_unicode_ci,
  `short_description_ar` text COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `description_ar` longtext COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_digital` tinyint(1) NOT NULL DEFAULT '0',
  `is_stock_managed` tinyint(1) NOT NULL DEFAULT '0',
  `master_product_id` bigint(20) unsigned DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_approve` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `meta_title` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Atta',NULL,NULL,'582177',1,7,NULL,100,5,450,1,0,'Atta',NULL,'<p>Atta</p>',NULL,1,0,1,NULL,0,0,1,'2026-08-02 16:29:17','2026-08-02 18:09:41',NULL,NULL,NULL,NULL,NULL,NULL),(2,'Atta',NULL,NULL,'582177',1,7,NULL,100,5,5,1,0,'Atta',NULL,'<p>Atta</p>',NULL,1,0,1,1,0,0,1,'2026-08-02 17:53:38','2026-08-02 17:54:20',NULL,NULL,NULL,NULL,NULL,'2026-08-02 17:54:20'),(3,'Atta',NULL,NULL,'582177',6,7,NULL,100,5,50,1,0,'Atta',NULL,'<p>Atta</p>',NULL,1,0,1,1,0,0,1,'2026-08-02 18:09:41','2026-08-02 18:09:41',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recent_views`
--

DROP TABLE IF EXISTS `recent_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recent_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recent_views_product_id_foreign` (`product_id`),
  KEY `recent_views_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recent_views`
--

LOCK TABLES `recent_views` WRITE;
/*!40000 ALTER TABLE `recent_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `recent_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_order_details`
--

DROP TABLE IF EXISTS `return_order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_order_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `return_order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `color` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_order_details_return_order_id_foreign` (`return_order_id`),
  KEY `return_order_details_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_order_details`
--

LOCK TABLES `return_order_details` WRITE;
/*!40000 ALTER TABLE `return_order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_orders`
--

DROP TABLE IF EXISTS `return_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `amount` double DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `reason` longtext COLLATE utf8_unicode_ci NOT NULL,
  `reject_note` longtext COLLATE utf8_unicode_ci,
  `return_address` longtext COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_orders_order_id_foreign` (`order_id`),
  KEY `return_orders_shop_id_foreign` (`shop_id`),
  KEY `return_orders_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_orders`
--

LOCK TABLES `return_orders` WRITE;
/*!40000 ALTER TABLE `return_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `rating` double NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_customer_id_foreign` (`customer_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_shop_id_foreign` (`shop_id`),
  KEY `reviews_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `is_shop` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'root','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41'),(2,'admin','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41'),(3,'shop','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41'),(4,'customer','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41'),(5,'visitor','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41'),(6,'driver','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41'),(7,'supplier','web',0,'2026-07-31 13:44:41','2026-07-31 13:44:41');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s_m_s_configs`
--

DROP TABLE IF EXISTS `s_m_s_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_m_s_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL,
  `provider` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_m_s_configs`
--

LOCK TABLES `s_m_s_configs` WRITE;
/*!40000 ALTER TABLE `s_m_s_configs` DISABLE KEYS */;
/*!40000 ALTER TABLE `s_m_s_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_categories`
--

DROP TABLE IF EXISTS `shop_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_categories` (
  `shop_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  KEY `shop_categories_shop_id_foreign` (`shop_id`),
  KEY `shop_categories_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_categories`
--

LOCK TABLES `shop_categories` WRITE;
/*!40000 ALTER TABLE `shop_categories` DISABLE KEYS */;
INSERT INTO `shop_categories` VALUES (1,1);
/*!40000 ALTER TABLE `shop_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_monthly_payouts`
--

DROP TABLE IF EXISTS `shop_monthly_payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_monthly_payouts`
--

LOCK TABLES `shop_monthly_payouts` WRITE;
/*!40000 ALTER TABLE `shop_monthly_payouts` DISABLE KEYS */;
INSERT INTO `shop_monthly_payouts` VALUES (1,1,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-01 10:03:47','2026-08-01 10:03:47'),(2,2,2026,7,80000.00,80000.00,2,NULL,8000.00,0.00,8000.00,'2026-08-01 10:03:47','2026-08-01 10:03:47'),(3,3,2026,7,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-01 10:03:47','2026-08-01 10:03:47'),(4,1,2026,8,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-01 10:12:23','2026-08-01 10:12:23'),(5,2,2026,8,0.00,0.00,1,NULL,0.00,0.00,0.00,'2026-08-01 10:12:23','2026-08-01 10:12:23');
/*!40000 ALTER TABLE `shop_monthly_payouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_subscriptions`
--

DROP TABLE IF EXISTS `shop_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `sale_limit` int(11) DEFAULT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `remaining_sales` int(11) DEFAULT NULL,
  `payment_id` bigint(20) unsigned NOT NULL,
  `status` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_subscriptions_shop_id_foreign` (`shop_id`),
  KEY `shop_subscriptions_plan_id_foreign` (`plan_id`),
  KEY `shop_subscriptions_payment_id_foreign` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_subscriptions`
--

LOCK TABLES `shop_subscriptions` WRITE;
/*!40000 ALTER TABLE `shop_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_user`
--

DROP TABLE IF EXISTS `shop_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_user`
--

LOCK TABLES `shop_user` WRITE;
/*!40000 ALTER TABLE `shop_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_user_chats`
--

DROP TABLE IF EXISTS `shop_user_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_user_chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_user_chats_shop_user_id_foreign` (`shop_user_id`),
  KEY `shop_user_chats_product_id_foreign` (`product_id`),
  KEY `shop_user_chats_shop_id_foreign` (`shop_id`),
  KEY `shop_user_chats_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_user_chats`
--

LOCK TABLES `shop_user_chats` WRITE;
/*!40000 ALTER TABLE `shop_user_chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_user_chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_shop_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `banner_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_charge` double DEFAULT '0',
  `min_order_amount` double NOT NULL DEFAULT '0',
  `prefix` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'RC',
  `address` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `off_day` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estimated_delivery_time` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
INSERT INTO `shops` VALUES (1,'My Shop',1,NULL,1,1,2,0,1,'RC','83187 Schulist Glens\nKristopherhaven, MI 69181-8159','38.900768','46.23272','07:44:40','16:24:16',NULL,'3-4 days',1,'My Shop Description','2026-07-31 13:44:45','2026-08-01 10:04:06',NULL,NULL),(2,'E2E Root Shop',2,NULL,NULL,NULL,NULL,0,0,'RC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-08-01 10:03:37','2026-08-01 10:03:37',NULL,NULL),(3,'E2E Child Shop',3,NULL,NULL,NULL,NULL,0,0,'RC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-08-01 10:03:37','2026-08-01 10:04:06',NULL,NULL),(4,'Debug Root',5,NULL,NULL,NULL,NULL,0,0,'RC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-08-01 10:40:51','2026-08-01 10:40:51',NULL,NULL),(5,'Debug Child',6,4,NULL,NULL,NULL,0,0,'RC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-08-01 10:40:52','2026-08-01 10:40:52',NULL,NULL),(6,'Sanganer Shop',7,NULL,2,4,5,0,0,'RC','417 Sanganer thana , Jaipur','28.6139','77.2090',NULL,NULL,NULL,NULL,1,NULL,'2026-08-02 15:00:11','2026-08-02 16:31:21',NULL,NULL);
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sizes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sizes_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sizes`
--

LOCK TABLES `sizes` WRITE;
/*!40000 ALTER TABLE `sizes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_auths`
--

DROP TABLE IF EXISTS `social_auths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_auths` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_secret` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `redirect` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_auths`
--

LOCK TABLES `social_auths` WRITE;
/*!40000 ALTER TABLE `social_auths` DISABLE KEYS */;
INSERT INTO `social_auths` VALUES (1,'Google','','','postmessage','google','assets/social/google.svg',0,NULL,NULL),(2,'Facebook','','','','facebook','assets/social/facebook.svg',0,NULL,NULL),(3,'Apple','com.janmitram.web','',NULL,'apple','assets/social/apple.svg',0,NULL,NULL);
/*!40000 ALTER TABLE `social_auths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_links`
--

DROP TABLE IF EXISTS `social_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_links`
--

LOCK TABLES `social_links` WRITE;
/*!40000 ALTER TABLE `social_links` DISABLE KEYS */;
INSERT INTO `social_links` VALUES (1,'Facebook','/assets/social/facebook.png',NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'LinkedIn','/assets/social/linkedin.png','https://www.linkedin.com/company/razinsoft',1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(3,'Instagram','/assets/social/instagram.png',NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(4,'YouTube','/assets/social/youtube.png','https://www.youtube.com/@razinsoft',1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(5,'WhatsApp','/assets/social/whatsapp.png',NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(6,'Twitter','/assets/social/twitter.png',NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(7,'Telegram','/assets/social/telegram.png',NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(8,'Google Plus','/assets/social/google-plus.png',NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43');
/*!40000 ALTER TABLE `social_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_ledgers`
--

DROP TABLE IF EXISTS `stock_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_ledgers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `to_warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `reference_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_ledgers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `stock_ledgers_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `stock_ledgers_product_id_foreign` (`product_id`),
  KEY `stock_ledgers_color_id_foreign` (`color_id`),
  KEY `stock_ledgers_size_id_foreign` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_ledgers`
--

LOCK TABLES `stock_ledgers` WRITE;
/*!40000 ALTER TABLE `stock_ledgers` DISABLE KEYS */;
INSERT INTO `stock_ledgers` VALUES (1,NULL,1,1,NULL,NULL,500,'admin_addition',NULL,'500','2026-08-02 17:21:54','2026-08-02 17:21:54'),(2,1,2,1,NULL,NULL,100,'warehouse_transfer',1,'100','2026-08-02 17:22:32','2026-08-02 17:22:32'),(4,1,NULL,1,NULL,NULL,50,'shop_request',2,'Fulfilled stock request #2 for shop #6','2026-08-02 18:09:41','2026-08-02 18:09:41');
/*!40000 ALTER TABLE `stock_ledgers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_request_items`
--

DROP TABLE IF EXISTS `stock_request_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_request_items`
--

LOCK TABLES `stock_request_items` WRITE;
/*!40000 ALTER TABLE `stock_request_items` DISABLE KEYS */;
INSERT INTO `stock_request_items` VALUES (2,2,1,NULL,NULL,50,'2026-08-02 18:09:41','2026-08-02 18:09:41');
/*!40000 ALTER TABLE `stock_request_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_requests`
--

DROP TABLE IF EXISTS `stock_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `status` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_requests_shop_id_foreign` (`shop_id`),
  KEY `stock_requests_warehouse_id_foreign` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_requests`
--

LOCK TABLES `stock_requests` WRITE;
/*!40000 ALTER TABLE `stock_requests` DISABLE KEYS */;
INSERT INTO `stock_requests` VALUES (2,6,1,'completed','50 by admmin<br>[inventory-assignment]','2026-08-02 18:09:41','2026-08-02 18:09:41');
/*!40000 ALTER TABLE `stock_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_categories_media_id_foreign` (`media_id`),
  KEY `sub_categories_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_categories`
--

LOCK TABLES `sub_categories` WRITE;
/*!40000 ALTER TABLE `sub_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `short_description` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `sale_limit` int(11) DEFAULT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_ticket_attachments`
--

DROP TABLE IF EXISTS `support_ticket_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_ticket_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_attachments_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `support_ticket_attachments_media_id_foreign` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_attachments`
--

LOCK TABLES `support_ticket_attachments` WRITE;
/*!40000 ALTER TABLE `support_ticket_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_ticket_messages`
--

DROP TABLE IF EXISTS `support_ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_ticket_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `is_highlighted` tinyint(1) NOT NULL DEFAULT '0',
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_messages_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `support_ticket_messages_sender_id_foreign` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_messages`
--

LOCK TABLES `support_ticket_messages` WRITE;
/*!40000 ALTER TABLE `support_ticket_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ticket_number` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `order_number` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `issue_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `status` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_start` timestamp NULL DEFAULT NULL,
  `ticket_end` timestamp NULL DEFAULT NULL,
  `user_chat` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supports`
--

DROP TABLE IF EXISTS `supports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supports_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supports`
--

LOCK TABLES `supports` WRITE;
/*!40000 ALTER TABLE `supports` DISABLE KEYS */;
/*!40000 ALTER TABLE `supports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme_colors`
--

DROP TABLE IF EXISTS `theme_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme_colors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `primary` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `secondary` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `variant_50` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_100` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_200` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_300` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_400` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_500` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_600` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_700` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_800` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_900` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variant_950` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme_colors`
--

LOCK TABLES `theme_colors` WRITE;
/*!40000 ALTER TABLE `theme_colors` DISABLE KEYS */;
INSERT INTO `theme_colors` VALUES (1,'#EE456B','#FEE5E8','#FFF1F3','#FEE5E8','#FCCFD6','#FAA7B5','#F7758F','#EE456B','#DD2C5C','#B91747','#9B1642','#84173E','#4A071D',1,'2026-07-31 13:44:43','2026-07-31 13:44:43'),(2,'#a855f7','#f3e8ff','#faf5ff','#f3e8ff','#e9d5ff','#d8b4fe','#c084fc','#a855f7','#9333ea','#7e22ce','#6b21a8','#581c87','#3b0764',0,NULL,NULL),(3,'#8b5cf6','#ede9fe','#f5f3ff','#ede9fe','#ddd6fe','#c4b5fd','#a78bfa','#8b5cf6','#7c3aed','#6d28d9','#5b21b6','#4c1d95','#2e1065',0,NULL,NULL);
/*!40000 ALTER TABLE `theme_colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_issue_types`
--

DROP TABLE IF EXISTS `ticket_issue_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_issue_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_issue_types`
--

LOCK TABLES `ticket_issue_types` WRITE;
/*!40000 ALTER TABLE `ticket_issue_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_issue_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint(20) unsigned NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `is_commission` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'credit',
  `transaction_id` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purpose` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_wallet_id_foreign` (`wallet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,2,8000,0,'credit','000001','mlm_payout','Monthly MLM payout for 2026-7 (level -)','2026-08-01 10:03:47','2026-08-01 10:03:47');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translate_utilities`
--

DROP TABLE IF EXISTS `translate_utilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translate_utilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `sub_category_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `lang` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `translate_utilities_category_id_foreign` (`category_id`),
  KEY `translate_utilities_sub_category_id_foreign` (`sub_category_id`),
  KEY `translate_utilities_brand_id_foreign` (`brand_id`),
  KEY `translate_utilities_color_id_foreign` (`color_id`),
  KEY `translate_utilities_size_id_foreign` (`size_id`),
  KEY `translate_utilities_unit_id_foreign` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translate_utilities`
--

LOCK TABLES `translate_utilities` WRITE;
/*!40000 ALTER TABLE `translate_utilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `translate_utilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `shop_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_non_permissions`
--

DROP TABLE IF EXISTS `user_non_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_non_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_non_permissions_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_non_permissions`
--

LOCK TABLES `user_non_permissions` WRITE;
/*!40000 ALTER TABLE `user_non_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_non_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `password` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `driving_lience` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vehicle_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `country` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_code` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `auth_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_id` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_online` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_media_id_foreign` (`media_id`),
  KEY `users_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin',NULL,'01000000001','root@janmitram.com',NULL,'$2y$12$C1fK1CQCZz1s4fH1cai1gOr2PDmyAniCKGNUnIC.edGTiNDs6JFwO',NULL,NULL,1,'2026-07-31 13:44:43','2026-07-31 13:44:43',NULL,NULL,NULL,'2026-07-31 13:44:43','2026-08-01 10:10:17',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'Jessy Sipes V',NULL,'541.366.9014','stanford40@example.org',NULL,'$2y$12$WJhXKEPSyRT6cnrnki1MoO3n74W3XkZdcnyJOIDuMVyLENgMccNqy','male','2011-09-13',1,'2026-08-01 10:03:35','2026-08-01 10:03:35','nOPerxaseO',NULL,NULL,'2026-08-01 10:03:35','2026-08-01 10:03:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'Blaise Mertz',NULL,'630.752.6993','lawson.champlin@example.org',NULL,'$2y$12$R/VXzrabYtYJBTRNkUFNvuIPcstvUDKzalPb6tmAqan3qdui2YCSq','female','1974-03-12',0,'2026-08-01 10:03:37','2026-08-01 10:03:37','vzKniyxAmb',NULL,NULL,'2026-08-01 10:03:37','2026-08-01 10:04:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'Yolanda Reinger',NULL,'(925) 891-6720','hessel.rory@example.net',NULL,'$2y$12$36cNGxdga7EY.XOEuXUxZein0hV5dWdxjzMYX1J7seNMlP6mREl66','female','1978-11-27',1,'2026-08-01 10:03:38','2026-08-01 10:03:38','XZIkwJCibA',NULL,NULL,'2026-08-01 10:03:38','2026-08-01 10:03:38',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'Julius Zieme',NULL,'570.332.6176','magnolia06@example.org',NULL,'$2y$12$hQljHuEdfgStExnRa2.2ueXSxcbrP3Xjd3Q1ITK.sfLLtrnyCpbM6','female','1982-04-30',1,'2026-08-01 10:40:51','2026-08-01 10:40:51','IexG9xhNdB',NULL,NULL,'2026-08-01 10:40:51','2026-08-01 10:40:51',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'Bennie Sipes PhD',NULL,'1-208-753-5541','kaylah.klein@example.org',NULL,'$2y$12$du4Vv3x0iZy4xgVJT0zgnODtNKUC7vH1G/abmTUyjLwvIV9zHHl56','female','2025-02-21',1,'2026-08-01 10:40:52','2026-08-01 10:40:52','1de9wke4I6',NULL,NULL,'2026-08-01 10:40:52','2026-08-01 10:40:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'Chandan','Gaur','9549803319','chg@janmitram.com',3,'$2y$12$2AL7wdy5qCNK8BQM0UOmROzxbOHE5uYMLndj2wB7NyaFPHFxtoUmy','male',NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-08-02 15:00:11','2026-08-02 15:00:51',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'Chauncey Funk DVM',NULL,'+17262312677','ellie.tromp@example.com',NULL,'$2y$12$OX2NQkgHWh4NlABD02adUOPTHCHLW/0v4P9yrb5YNaHadXxXLh/My','female','1998-09-21',1,'2026-08-02 17:59:55','2026-08-02 17:59:55','pbYSBJpo6o',NULL,NULL,'2026-08-02 17:59:55','2026-08-02 17:59:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vat_taxes`
--

DROP TABLE IF EXISTS `vat_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vat_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'order_base',
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `percentage` double NOT NULL DEFAULT '0',
  `deduction` varchar(191) COLLATE utf8_unicode_ci DEFAULT 'exclusive',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vat_taxes`
--

LOCK TABLES `vat_taxes` WRITE;
/*!40000 ALTER TABLE `vat_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `vat_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `verify_manages`
--

DROP TABLE IF EXISTS `verify_manages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verify_manages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `register_otp` tinyint(1) NOT NULL DEFAULT '0',
  `register_otp_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forgot_otp` tinyint(1) NOT NULL DEFAULT '1',
  `forgot_otp_type` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone_required` tinyint(1) NOT NULL DEFAULT '1',
  `email_required` tinyint(1) NOT NULL DEFAULT '0',
  `phone_min_length` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_max_length` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_place_account_verify` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verify_manages`
--

LOCK TABLES `verify_manages` WRITE;
/*!40000 ALTER TABLE `verify_manages` DISABLE KEYS */;
INSERT INTO `verify_manages` VALUES (1,0,'email',1,'email',NULL,NULL,0,1,'9','16',0);
/*!40000 ALTER TABLE `verify_manages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `verify_otps`
--

DROP TABLE IF EXISTS `verify_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verify_otps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `otp` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verify_otps`
--

LOCK TABLES `verify_otps` WRITE;
/*!40000 ALTER TABLE `verify_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `verify_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallets`
--

DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `balance` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallets`
--

LOCK TABLES `wallets` WRITE;
/*!40000 ALTER TABLE `wallets` DISABLE KEYS */;
INSERT INTO `wallets` VALUES (1,1,0,'2026-07-31 13:44:45','2026-07-31 13:44:45'),(2,2,8000,'2026-07-31 13:44:45','2026-08-01 10:03:47'),(3,7,0,'2026-08-02 15:00:11','2026-08-02 15:00:11');
/*!40000 ALTER TABLE `wallets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_stock`
--

DROP TABLE IF EXISTS `warehouse_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned DEFAULT NULL,
  `size_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wh_stock_unique` (`warehouse_id`,`product_id`,`color_id`,`size_id`),
  KEY `warehouse_stock_product_id_foreign` (`product_id`),
  KEY `warehouse_stock_color_id_foreign` (`color_id`),
  KEY `warehouse_stock_size_id_foreign` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_stock`
--

LOCK TABLES `warehouse_stock` WRITE;
/*!40000 ALTER TABLE `warehouse_stock` DISABLE KEYS */;
INSERT INTO `warehouse_stock` VALUES (1,1,1,NULL,NULL,350,'2026-08-02 17:21:54','2026-08-02 18:09:41'),(2,2,1,NULL,NULL,100,'2026-08-02 17:22:32','2026-08-02 17:22:32');
/*!40000 ALTER TABLE `warehouse_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_transfer_items`
--

DROP TABLE IF EXISTS `warehouse_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_transfer_items`
--

LOCK TABLES `warehouse_transfer_items` WRITE;
/*!40000 ALTER TABLE `warehouse_transfer_items` DISABLE KEYS */;
INSERT INTO `warehouse_transfer_items` VALUES (1,1,1,NULL,NULL,100,'2026-08-02 17:22:29','2026-08-02 17:22:29');
/*!40000 ALTER TABLE `warehouse_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_transfers`
--

DROP TABLE IF EXISTS `warehouse_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_warehouse_id` bigint(20) unsigned NOT NULL,
  `to_warehouse_id` bigint(20) unsigned NOT NULL,
  `status` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `warehouse_transfers_to_warehouse_id_foreign` (`to_warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_transfers`
--

LOCK TABLES `warehouse_transfers` WRITE;
/*!40000 ALTER TABLE `warehouse_transfers` DISABLE KEYS */;
INSERT INTO `warehouse_transfers` VALUES (1,1,2,'completed','100','2026-08-02 17:22:29','2026-08-02 17:22:32');
/*!40000 ALTER TABLE `warehouse_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,'Central Warehouse','Main Logistics Hub',1,'2026-07-31 13:44:45','2026-07-31 13:44:45'),(2,'Raja Park (Jaipur)','Raja Park (Jaipur)',0,'2026-08-02 16:30:49','2026-08-02 16:30:49');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdraws`
--

DROP TABLE IF EXISTS `withdraws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withdraws` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `shop_id` bigint(20) unsigned DEFAULT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `contact_number` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `withdraw_method` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reason` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdraws_user_id_foreign` (`user_id`),
  KEY `withdraws_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraws`
--

LOCK TABLES `withdraws` WRITE;
/*!40000 ALTER TABLE `withdraws` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdraws` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-02 23:14:49
