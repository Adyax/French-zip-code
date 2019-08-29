-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: frenchzipcode
-- ------------------------------------------------------
-- Server version	5.7.27

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
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_region_code_foreign` (`region_code`),
  KEY `departments_code_index` (`code`),
  CONSTRAINT `departments_region_code_foreign` FOREIGN KEY (`region_code`) REFERENCES `regions` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'84','01','Ain','ain'),(2,'32','02','Aisne','aisne'),(3,'84','03','Allier','allier'),(4,'93','04','Alpes-de-Haute-Provence','alpes de haute provence'),(5,'93','05','Hautes-Alpes','hautes alpes'),(6,'93','06','Alpes-Maritimes','alpes maritimes'),(7,'84','07','Ardèche','ardeche'),(8,'44','08','Ardennes','ardennes'),(9,'76','09','Ariège','ariege'),(10,'44','10','Aube','aube'),(11,'76','11','Aude','aude'),(12,'76','12','Aveyron','aveyron'),(13,'93','13','Bouches-du-Rhône','bouches du rhone'),(14,'28','14','Calvados','calvados'),(15,'84','15','Cantal','cantal'),(16,'75','16','Charente','charente'),(17,'75','17','Charente-Maritime','charente maritime'),(18,'24','18','Cher','cher'),(19,'75','19','Corrèze','correze'),(20,'27','21','Côte-d\'Or','cote dor'),(21,'53','22','Côtes-d\'Armor','cotes darmor'),(22,'75','23','Creuse','creuse'),(23,'75','24','Dordogne','dordogne'),(24,'27','25','Doubs','doubs'),(25,'84','26','Drôme','drome'),(26,'28','27','Eure','eure'),(27,'24','28','Eure-et-Loir','eure et loir'),(28,'53','29','Finistère','finistere'),(29,'94','2A','Corse-du-Sud','corse du sud'),(30,'94','2B','Haute-Corse','haute corse'),(31,'76','30','Gard','gard'),(32,'76','31','Haute-Garonne','haute garonne'),(33,'76','32','Gers','gers'),(34,'75','33','Gironde','gironde'),(35,'76','34','Hérault','herault'),(36,'53','35','Ille-et-Vilaine','ille et vilaine'),(37,'24','36','Indre','indre'),(38,'24','37','Indre-et-Loire','indre et loire'),(39,'84','38','Isère','isere'),(40,'27','39','Jura','jura'),(41,'75','40','Landes','landes'),(42,'24','41','Loir-et-Cher','loir et cher'),(43,'84','42','Loire','loire'),(44,'84','43','Haute-Loire','haute loire'),(45,'52','44','Loire-Atlantique','loire atlantique'),(46,'24','45','Loiret','loiret'),(47,'76','46','Lot','lot'),(48,'75','47','Lot-et-Garonne','lot et garonne'),(49,'76','48','Lozère','lozere'),(50,'52','49','Maine-et-Loire','maine et loire'),(51,'28','50','Manche','manche'),(52,'44','51','Marne','marne'),(53,'44','52','Haute-Marne','haute marne'),(54,'52','53','Mayenne','mayenne'),(55,'44','54','Meurthe-et-Moselle','meurthe et moselle'),(56,'44','55','Meuse','meuse'),(57,'53','56','Morbihan','morbihan'),(58,'44','57','Moselle','moselle'),(59,'27','58','Nièvre','nievre'),(60,'32','59','Nord','nord'),(61,'32','60','Oise','oise'),(62,'28','61','Orne','orne'),(63,'32','62','Pas-de-Calais','pas de calais'),(64,'84','63','Puy-de-Dôme','puy de dome'),(65,'75','64','Pyrénées-Atlantiques','pyrenees atlantiques'),(66,'76','65','Hautes-Pyrénées','hautes pyrenees'),(67,'76','66','Pyrénées-Orientales','pyrenees orientales'),(68,'44','67','Bas-Rhin','bas rhin'),(69,'44','68','Haut-Rhin','haut rhin'),(70,'84','69','Rhône','rhone'),(71,'27','70','Haute-Saône','haute saone'),(72,'27','71','Saône-et-Loire','saone et loire'),(73,'52','72','Sarthe','sarthe'),(74,'84','73','Savoie','savoie'),(75,'84','74','Haute-Savoie','haute savoie'),(76,'11','75','Paris','paris'),(77,'28','76','Seine-Maritime','seine maritime'),(78,'11','77','Seine-et-Marne','seine et marne'),(79,'11','78','Yvelines','yvelines'),(80,'75','79','Deux-Sèvres','deux sevres'),(81,'32','80','Somme','somme'),(82,'76','81','Tarn','tarn'),(83,'76','82','Tarn-et-Garonne','tarn et garonne'),(84,'93','83','Var','var'),(85,'93','84','Vaucluse','vaucluse'),(86,'52','85','Vendée','vendee'),(87,'75','86','Vienne','vienne'),(88,'75','87','Haute-Vienne','haute vienne'),(89,'44','88','Vosges','vosges'),(90,'27','89','Yonne','yonne'),(91,'27','90','Territoire de Belfort','territoire de belfort'),(92,'11','91','Essonne','essonne'),(93,'11','92','Hauts-de-Seine','hauts de seine'),(94,'11','93','Seine-Saint-Denis','seine saint denis'),(95,'11','94','Val-de-Marne','val de marne'),(96,'11','95','Val-d\'Oise','val doise'),(97,'01','971','Guadeloupe','guadeloupe'),(98,'02','972','Martinique','martinique'),(99,'03','973','Guyane','guyane'),(100,'04','974','La Réunion','la reunion'),(101,'06','976','Mayotte','mayotte');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-08-29  0:55:08
