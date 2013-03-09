-- MySQL dump 10.13  Distrib 5.5.25a, for osx10.8 (i386)
--
-- Host: localhost    Database: gladturdev
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
  `profile_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user`
--

LOCK TABLES `fos_user` WRITE;
/*!40000 ALTER TABLE `fos_user` DISABLE KEYS */;
INSERT INTO `fos_user` VALUES (1,'morten','morten','morten@morning.dk','morten@morning.dk',1,'8ag7j25lho08s0oo444so8ogw8sooc0','74gqfmhOmUgcxYnexwbOzIomuVEeFgdhT99fD293vJnNWP7q+LjT1U8/V+89YuSuTdyC4BdkE1fxRU4AaIZasQ==','2012-12-19 10:20:57',0,0,NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',0,NULL,NULL),(2,'tvglad','tvglad','tvg@tvg.dk','tvg@tvg.dk',1,'5o0chm6rmlk4ccs4wg40kw008oogcsw','/xKMvg36U3Urz2mVzrFwLwGrwQ5XEN0TqHUcSBTK5aPZoR5ydRXgcdaH1R+w+F/W2AfLaMtIuhb+R7tfIPKYrQ==','2012-12-19 10:21:35',0,0,NULL,NULL,NULL,'a:1:{i:0;s:12:\"ROLE_TVGUSER\";}',0,NULL,NULL);
/*!40000 ALTER TABLE `fos_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
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
  `latitude` varchar(64) DEFAULT NULL,
  `longitude` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
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
  `parent_cateogory_id` int(11) DEFAULT NULL,
  `readable_name` varchar(255) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_category`
--

LOCK TABLES `location_category` WRITE;
/*!40000 ALTER TABLE `location_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `location_category` ENABLE KEYS */;
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
  CONSTRAINT `FK_34688C3BF1A0EB13` FOREIGN KEY (`location_tag_id`) REFERENCES `location_tag` (`id`),
  CONSTRAINT `FK_34688C3B64D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`)
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
  `profiles_relevance` longtext COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  KEY `IDX_389B783E8FE702` (`tag_category_id`),
  CONSTRAINT `FK_389B783E8FE702` FOREIGN KEY (`tag_category_id`) REFERENCES `tag_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_category`
--

LOCK TABLES `tag_category` WRITE;
/*!40000 ALTER TABLE `tag_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_location_data`
--

DROP TABLE IF EXISTS `user_location_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_location_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `hours_openingtime` int(11) DEFAULT NULL,
  `hours_closingtime` int(11) DEFAULT NULL,
  `mediapath` varchar(255) DEFAULT NULL,
  `txt_description` longtext,
  `txt_comment` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_location_data`
--

LOCK TABLES `user_location_data` WRITE;
/*!40000 ALTER TABLE `user_location_data` DISABLE KEYS */;
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
  `user_id` int(11) DEFAULT NULL,
  `location_map_location_tag_id` int(11) DEFAULT NULL,
  `relevant` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_location_tag_data`
--

LOCK TABLES `user_location_tag_data` WRITE;
/*!40000 ALTER TABLE `user_location_tag_data` DISABLE KEYS */;
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

-- Dump completed on 2012-12-19 10:30:28
