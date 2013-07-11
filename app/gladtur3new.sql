-- MySQL dump 10.13  Distrib 5.5.25a, for osx10.8 (i386)
--
-- Host: localhost    Database: gladtur3
-- ------------------------------------------------------
-- Server version	5.5.25a-log

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
-- Table structure for table `fos_user`
--

DROP TABLE IF EXISTS `fos_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fos_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `username_canonical` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_canonical` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user`
--

LOCK TABLES `fos_user` WRITE;
/*!40000 ALTER TABLE `fos_user` DISABLE KEYS */;
INSERT INTO `fos_user` VALUES (1,'morten','morten','morten@morning.dk','morten@morning.dk',1,'ffl71k7dgtkoo8k4s0k4gsows80c0ko','xn3SCmfETtEbKKM02ctJfQd/kIZfCga3ibPCeTf1seEGKo/zDUWY6vwsZ/veLKzwVf5a+P3ma5wq92Jx7tBZ1g==','2013-04-21 14:51:53',0,0,NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',0,NULL);
/*!40000 ALTER TABLE `fos_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_geolocation_api_log`
--

DROP TABLE IF EXISTS `google_geolocation_api_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_geolocation_api_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastStatus` varchar(20) NOT NULL,
  `requests` int(11) NOT NULL,
  `created` date NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_26CE10BDB23DB7B8` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_geolocation_api_log`
--

LOCK TABLES `google_geolocation_api_log` WRITE;
/*!40000 ALTER TABLE `google_geolocation_api_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_geolocation_api_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_geolocation_location`
--

DROP TABLE IF EXISTS `google_geolocation_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_geolocation_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search` varchar(255) NOT NULL,
  `matches` smallint(6) NOT NULL,
  `status` varchar(20) NOT NULL,
  `result` longtext NOT NULL,
  `hits` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_geolocation_location`
--

LOCK TABLES `google_geolocation_location` WRITE;
/*!40000 ALTER TABLE `google_geolocation_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_geolocation_location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `latitude` varchar(64) DEFAULT NULL,
  `longitude` varchar(64) DEFAULT NULL,
  `readable_name` varchar(255) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `address_zip` varchar(20) DEFAULT NULL,
  `address_country` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `address_extd` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `mediapath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,NULL,NULL,'stedet 2 be !',1,NULL,'DK','Copenhagen','Islands Brygge 17',NULL,'26368244','mortenthorpe@gmail.com',NULL,NULL,NULL),(2,NULL,NULL,'Meyer\'s Deli',1,'1752','Danmark','København','Gl. Kongevej',NULL,'12345678','claus@meyer.dk','meyers.dk','Claus Meyer',NULL);
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_category`
--

DROP TABLE IF EXISTS `location_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `readable_name` varchar(255) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `parentCategory_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D7193B23B0D2661D` (`parentCategory_id`),
  CONSTRAINT `FK_D7193B23B0D2661D` FOREIGN KEY (`parentCategory_id`) REFERENCES `location_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_category`
--

LOCK TABLES `location_category` WRITE;
/*!40000 ALTER TABLE `location_category` DISABLE KEYS */;
INSERT INTO `location_category` VALUES (1,'Mad & Drikke',1,NULL),(2,'Cafe',1,1),(3,'Restaurant',1,1),(4,'Station',1,NULL),(5,'S-tog',1,NULL);
/*!40000 ALTER TABLE `location_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_locationcategory`
--

DROP TABLE IF EXISTS `location_locationcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_locationcategory` (
  `location_id` int(11) NOT NULL,
  `locationcategory_id` int(11) NOT NULL,
  PRIMARY KEY (`location_id`,`locationcategory_id`),
  KEY `IDX_ED402E1C64D218E` (`location_id`),
  KEY `IDX_ED402E1CC8256C45` (`locationcategory_id`),
  CONSTRAINT `FK_ED402E1C64D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ED402E1CC8256C45` FOREIGN KEY (`locationcategory_id`) REFERENCES `location_category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_locationcategory`
--

LOCK TABLES `location_locationcategory` WRITE;
/*!40000 ALTER TABLE `location_locationcategory` DISABLE KEYS */;
INSERT INTO `location_locationcategory` VALUES (1,1),(1,2),(1,3),(2,1),(2,2);
/*!40000 ALTER TABLE `location_locationcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_map_locationtag`
--

DROP TABLE IF EXISTS `location_map_locationtag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_map_locationtag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) DEFAULT NULL,
  `location_tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_34688C3B64D218E` (`location_id`),
  KEY `IDX_34688C3BF1A0EB13` (`location_tag_id`),
  CONSTRAINT `FK_34688C3B64D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  CONSTRAINT `FK_34688C3BF1A0EB13` FOREIGN KEY (`location_tag_id`) REFERENCES `location_tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_map_locationtag`
--

LOCK TABLES `location_map_locationtag` WRITE;
/*!40000 ALTER TABLE `location_map_locationtag` DISABLE KEYS */;
/*!40000 ALTER TABLE `location_map_locationtag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_tag`
--

DROP TABLE IF EXISTS `location_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) DEFAULT NULL,
  `location_tag_properties` longtext,
  PRIMARY KEY (`id`),
  KEY `IDX_625B3C16BAD26311` (`tag_id`),
  CONSTRAINT `FK_625B3C16BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_tag`
--

LOCK TABLES `location_tag` WRITE;
/*!40000 ALTER TABLE `location_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `location_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_tag_comment`
--

DROP TABLE IF EXISTS `location_tag_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_tag_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_tag_id` int(11) DEFAULT NULL,
  `commenttext` longtext,
  `media_filepath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_tag_comment`
--

LOCK TABLES `location_tag_comment` WRITE;
/*!40000 ALTER TABLE `location_tag_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `location_tag_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_userlocationdata`
--

DROP TABLE IF EXISTS `location_userlocationdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_userlocationdata` (
  `location_id` int(11) NOT NULL,
  `userlocationdata_id` int(11) NOT NULL,
  PRIMARY KEY (`location_id`,`userlocationdata_id`),
  KEY `IDX_7EDDF6364D218E` (`location_id`),
  KEY `IDX_7EDDF6332484C6B` (`userlocationdata_id`),
  CONSTRAINT `FK_7EDDF6332484C6B` FOREIGN KEY (`userlocationdata_id`) REFERENCES `user_location_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_7EDDF6364D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_userlocationdata`
--

LOCK TABLES `location_userlocationdata` WRITE;
/*!40000 ALTER TABLE `location_userlocationdata` DISABLE KEYS */;
INSERT INTO `location_userlocationdata` VALUES (1,1);
/*!40000 ALTER TABLE `location_userlocationdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_category_id` int(11) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `readable_name` varchar(255) DEFAULT NULL,
  `text_description` longtext,
  PRIMARY KEY (`id`),
  KEY `IDX_389B783E8FE702` (`tag_category_id`),
  CONSTRAINT `FK_389B783E8FE702` FOREIGN KEY (`tag_category_id`) REFERENCES `tag_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (1,1,1,'TestTag','A test tag directly from the database!'),(2,1,1,'TestTag2','Another test tag 2 directly from the database!');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_category`
--

DROP TABLE IF EXISTS `tag_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `is_general` tinyint(1) DEFAULT NULL,
  `readable_name` varchar(255) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `icon_filepath` varchar(255) DEFAULT NULL,
  `text_description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_category`
--

LOCK TABLES `tag_category` WRITE;
/*!40000 ALTER TABLE `tag_category` DISABLE KEYS */;
INSERT INTO `tag_category` VALUES (1,NULL,1,1,'Toilet',0,'/Users/mortenthorpe/sites/gladtur/web/uploads/aNewFile_.png',NULL),(2,NULL,1,1,'Trapper',0,'/Users/mortenthorpe/sites/gladtur/web/uploads/aNewFile_.png',NULL);
/*!40000 ALTER TABLE `tag_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tvguser_profile`
--

DROP TABLE IF EXISTS `tvguser_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tvguser_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `readable_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9851EAB7A76ED395` (`user_id`),
  CONSTRAINT `FK_9851EAB7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tvguser_profile`
--

LOCK TABLES `tvguser_profile` WRITE;
/*!40000 ALTER TABLE `tvguser_profile` DISABLE KEYS */;
INSERT INTO `tvguser_profile` VALUES (1,'Kørestol',NULL,'autumn_mooncph2012.png');
/*!40000 ALTER TABLE `tvguser_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tvguser_profile_tagcategories`
--

DROP TABLE IF EXISTS `tvguser_profile_tagcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tvguser_profile_tagcategories` (
  `tvguserprofile_id` int(11) NOT NULL,
  `tagcategory_id` int(11) NOT NULL,
  PRIMARY KEY (`tvguserprofile_id`,`tagcategory_id`),
  KEY `IDX_D4AC03F7DBA6CF83` (`tvguserprofile_id`),
  KEY `IDX_D4AC03F7D416EFCB` (`tagcategory_id`),
  CONSTRAINT `FK_D4AC03F7D416EFCB` FOREIGN KEY (`tagcategory_id`) REFERENCES `tag_category` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D4AC03F7DBA6CF83` FOREIGN KEY (`tvguserprofile_id`) REFERENCES `tvguser_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tvguser_profile_tagcategories`
--

LOCK TABLES `tvguser_profile_tagcategories` WRITE;
/*!40000 ALTER TABLE `tvguser_profile_tagcategories` DISABLE KEYS */;
INSERT INTO `tvguser_profile_tagcategories` VALUES (1,1);
/*!40000 ALTER TABLE `tvguser_profile_tagcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_location_data`
--

DROP TABLE IF EXISTS `user_location_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_location_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) DEFAULT NULL,
  `hours_openingtime` int(11) DEFAULT NULL,
  `hours_closingtime` int(11) DEFAULT NULL,
  `mediapath` varchar(255) DEFAULT NULL,
  `txt_description` longtext,
  `txt_comment` longtext,
  `user_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C379EB7364D218E` (`location_id`),
  KEY `IDX_C379EB73A76ED395` (`user_id`),
  CONSTRAINT `FK_C379EB7364D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  CONSTRAINT `FK_C379EB73A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_location_data`
--

LOCK TABLES `user_location_data` WRITE;
/*!40000 ALTER TABLE `user_location_data` DISABLE KEYS */;
INSERT INTO `user_location_data` VALUES (1,1,1100,1200,'','a description from user location data','Crerated directly in the database',1,NULL),(2,1,1100,1200,'','a description from user location data','Created directly in the database',1,NULL),(3,1,1000,1700,'','Meyers madhus er et fint og pænt spisested, spis på stedet, eller take-away.','',1,NULL);
/*!40000 ALTER TABLE `user_location_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_location_tag_data`
--

DROP TABLE IF EXISTS `user_location_tag_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_location_tag_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relevant` tinyint(1) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6AB6D51CBAD26311` (`tag_id`),
  KEY `IDX_6AB6D51C64D218E` (`location_id`),
  KEY `IDX_6AB6D51CA76ED395` (`user_id`),
  CONSTRAINT `FK_6AB6D51C64D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  CONSTRAINT `FK_6AB6D51CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
  CONSTRAINT `FK_6AB6D51CBAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_location_tag_data`
--

LOCK TABLES `user_location_tag_data` WRITE;
/*!40000 ALTER TABLE `user_location_tag_data` DISABLE KEYS */;
INSERT INTO `user_location_tag_data` VALUES (1,1,1,1,1),(3,0,1,2,1);
/*!40000 ALTER TABLE `user_location_tag_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-24 19:00:41
