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
-- Table structure for table `location_category`
--

DROP TABLE IF EXISTS `location_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `readable_name` varchar(255) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `is_topcategory` tinyint(1) DEFAULT NULL,
  `parentCategory_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D7193B23B0D2661D` (`parentCategory_id`),
  CONSTRAINT `FK_D7193B23B0D2661D` FOREIGN KEY (`parentCategory_id`) REFERENCES `location_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_category`
--

LOCK TABLES `location_category` WRITE;
/*!40000 ALTER TABLE `location_category` DISABLE KEYS */;
INSERT INTO `location_category` VALUES (13,'Butik & service',NULL,1,NULL),(14,'Mad & drikke',NULL,1,NULL),(15,'Kultur & musik',NULL,1,NULL),(16,'Seværdigheder & forlystelse',NULL,1,NULL),(17,'Sport',NULL,1,NULL),(18,'Overnatning',NULL,1,NULL),(19,'Sundhed',NULL,1,NULL),(20,'Uddannelsesinstitution',NULL,1,NULL),(21,'Bolig',NULL,1,NULL),(22,'Offentlig institution',NULL,1,NULL),(23,'Toilet',NULL,1,NULL),(24,'Parkering',NULL,1,NULL),(25,'Transport',NULL,1,NULL),(26,'Udendørs',NULL,1,NULL),(27,'Kulturhuse',NULL,1,NULL),(28,'Andet',NULL,1,NULL),(29,'Supermarked',NULL,0,13),(30,'Bank',NULL,0,13),(31,'Posthus',NULL,0,13),(32,'Tankstation',NULL,0,13),(33,'Indkøbsområde',NULL,0,13),(34,'Indkøbscenter',NULL,0,13),(35,'Butik',NULL,0,13),(36,'Byggemarked',NULL,0,13),(37,'Cafe',NULL,0,14),(38,'Bar',NULL,0,14),(39,'Værtshus',NULL,0,14),(40,'Restaurant',NULL,0,14),(41,'Cafeteria',NULL,0,14),(42,'Fastfood',NULL,0,14),(43,'Dansested',NULL,0,15),(44,'Spillested',NULL,0,15),(45,'Natklub',NULL,0,15),(46,'Biograf',NULL,0,15),(47,'Teater',NULL,0,15),(48,'Koncertsal',NULL,0,15),(49,'Kirke',NULL,0,16),(50,'Moske',NULL,0,16),(51,'Synagoge',NULL,0,16),(52,'Tempel',NULL,0,16),(53,'Museum',NULL,0,16),(54,'Galleri',NULL,0,16),(55,'Udstilling',NULL,0,16),(56,'Forlystelsespark',NULL,0,16),(57,'Kasino',NULL,0,16),(58,'Spillehal',NULL,0,16),(59,'Zoo',NULL,0,16),(60,'Sportsarena',NULL,0,17),(61,'Svømmehal',NULL,0,17),(62,'Sportshal',NULL,0,17),(63,'Tennisbane',NULL,0,17),(64,'Skøjtehal',NULL,0,17),(65,'Fodboldbane',NULL,0,17),(66,'Hotel',NULL,0,18),(67,'Vandrehjem',NULL,0,18),(68,'Feriebolig',NULL,0,18),(69,'Campingplads',NULL,0,18),(70,'Læge',NULL,0,19),(71,'Tandlæge',NULL,0,19),(72,'Skadestue',NULL,0,19),(73,'Hospital',NULL,0,19),(74,'Folkeskole',NULL,0,20),(75,'Privatskole',NULL,0,20),(76,'Ungdomsuddannelse',NULL,0,20),(77,'Videregående uddannelse',NULL,0,20),(78,'Universitet',NULL,0,20),(79,'Handelshøjskole',NULL,0,20),(80,'Højskole',NULL,0,20),(81,'Privat bolig',NULL,0,21),(82,'Sommerhus',NULL,0,21),(83,'Kommune',NULL,0,22),(84,'Region',NULL,0,22),(85,'Stat',NULL,0,22),(86,'Pissoir',NULL,0,23),(87,'Toilet',NULL,0,23),(88,'Toilet og bad',NULL,0,23),(89,'Station',NULL,0,25),(90,'Lufthavn',NULL,0,25),(91,'Bus',NULL,0,25),(92,'Tog',NULL,0,25),(93,'Færge',NULL,0,25),(94,'Fly',NULL,0,25),(95,'Taxi',NULL,0,25),(96,'Park',NULL,0,26),(97,'Strand',NULL,0,26),(98,'Medborgerhus',NULL,0,27),(99,'Bibliotek',NULL,0,27),(100,'Konferencested',NULL,0,27),(101,'Folkehøjskole',NULL,0,27);
/*!40000 ALTER TABLE `location_category` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-17 13:22:59
