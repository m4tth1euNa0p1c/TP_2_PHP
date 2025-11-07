-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: f1_api
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.2

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
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20251107133108','2025-11-07 13:31:13',668);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver`
--

DROP TABLE IF EXISTS `driver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_id` int DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_starter` tinyint(1) NOT NULL,
  `license_points` int NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `f1_start_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_11667CD9296CD8AE` (`team_id`),
  CONSTRAINT `FK_11667CD9296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver`
--

LOCK TABLES `driver` WRITE;
/*!40000 ALTER TABLE `driver` DISABLE KEYS */;
INSERT INTO `driver` VALUES (1,5,'Charles','Leclerc',1,12,'active','2018-03-25','2025-11-07 13:34:48','2025-11-07 13:34:48'),(2,5,'Carlos','Sainz Jr',1,12,'active','2015-03-15','2025-11-07 13:34:48','2025-11-07 13:34:48'),(3,5,'Oliver','Bearman',0,12,'active','2024-03-02','2025-11-07 13:34:48','2025-11-07 13:34:48'),(4,6,'Lewis','Hamilton',1,12,'active','2007-03-18','2025-11-07 13:34:48','2025-11-07 13:34:48'),(5,6,'George','Russell',1,12,'active','2019-03-17','2025-11-07 13:34:48','2025-11-07 13:34:48'),(6,6,'Andrea','Kimi Antonelli',0,12,'active','2025-01-01','2025-11-07 13:34:48','2025-11-07 13:34:48'),(7,7,'Max','Verstappen',1,12,'active','2015-03-15','2025-11-07 13:34:48','2025-11-07 13:34:48'),(8,7,'Sergio','Perez',1,12,'active','2011-03-27','2025-11-07 13:34:48','2025-11-07 13:34:48'),(9,7,'Liam','Lawson',0,12,'active','2023-07-01','2025-11-07 13:34:48','2025-11-07 13:34:48'),(10,8,'Lando','Norris',1,12,'active','2019-03-17','2025-11-07 13:34:48','2025-11-07 13:34:48'),(11,8,'Oscar','Piastri',1,12,'active','2023-03-05','2025-11-07 13:34:48','2025-11-07 13:34:48'),(12,8,'Alex','Palou',0,12,'active','2024-11-12','2025-11-07 13:34:48','2025-11-07 13:34:48');
/*!40000 ALTER TABLE `driver` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine`
--

DROP TABLE IF EXISTS `engine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `engine` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_id` int NOT NULL,
  `brand` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E8A81A8D296CD8AE` (`team_id`),
  CONSTRAINT `FK_E8A81A8D296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine`
--

LOCK TABLES `engine` WRITE;
/*!40000 ALTER TABLE `engine` DISABLE KEYS */;
INSERT INTO `engine` VALUES (5,5,'Ferrari'),(6,6,'Mercedes'),(7,7,'Honda RBPT'),(8,8,'Mercedes');
/*!40000 ALTER TABLE `engine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infraction`
--

DROP TABLE IF EXISTS `infraction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `infraction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `driver_id` int DEFAULT NULL,
  `team_id` int DEFAULT NULL,
  `occurred_at` datetime NOT NULL,
  `race_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C1A458F5C3423909` (`driver_id`),
  KEY `IDX_C1A458F5296CD8AE` (`team_id`),
  CONSTRAINT `FK_C1A458F5296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_C1A458F5C3423909` FOREIGN KEY (`driver_id`) REFERENCES `driver` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infraction`
--

LOCK TABLES `infraction` WRITE;
/*!40000 ALTER TABLE `infraction` DISABLE KEYS */;
INSERT INTO `infraction` VALUES (1,1,NULL,'2025-03-08 14:30:00','GP Bahrain','Dépassement des limites de piste (track limits) - 3 fois','PENALTY_POINTS',3.00),(2,NULL,5,'2025-03-08 16:45:00','GP Bahrain','Sortie dangereuse des stands (unsafe release)','FINE_EUR',25000.00),(3,7,NULL,'2025-03-22 15:20:00','GP Arabie Saoudite','Collision avec un autre pilote','PENALTY_POINTS',2.00),(4,NULL,7,'2025-04-02 13:10:00','GP Australie','Dépassement du budget cap lors des essais','FINE_EUR',50000.00),(5,4,NULL,'2025-05-05 14:55:00','GP Miami','Non-respect des drapeaux jaunes','PENALTY_POINTS',3.00),(6,NULL,6,'2025-05-25 16:30:00','GP Monaco','Équipement de sécurité non conforme','FINE_EUR',15000.00),(7,8,NULL,'2025-06-15 15:40:00','GP Canada','Manœuvre dangereuse en défense','PENALTY_POINTS',2.00);
/*!40000 ALTER TABLE `infraction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C4E0A61F5E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (5,'Scuderia Ferrari','2025-11-07 13:34:47','2025-11-07 13:34:47'),(6,'Mercedes-AMG Petronas F1 Team','2025-11-07 13:34:47','2025-11-07 13:34:47'),(7,'Oracle Red Bull Racing','2025-11-07 13:34:47','2025-11-07 13:34:47'),(8,'McLaren F1 Team','2025-11-07 13:34:47','2025-11-07 13:34:47');
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (4,'admin@example.com','[\"ROLE_ADMIN\"]','$2y$13$zza0niXaep5QlpqlzD1UduNDadLHBvCDBxWDfxF634oDIfvJ19SRq',1,'2025-11-07 13:34:47','2025-11-07 13:34:47'),(5,'manager@example.com','[\"ROLE_MANAGER\"]','$2y$13$zjRqjjhkFXG15VABovxWMO6b4R/Z5pRh05FykgOicTLKDvXwuiff.',1,'2025-11-07 13:34:47','2025-11-07 13:34:47'),(6,'user@example.com','[\"ROLE_USER\"]','$2y$13$TZL994BnmVtVTKQ1RutaPO4IrzYvObO6SBKjvpmqnOr.72W4N4b8S',1,'2025-11-07 13:34:48','2025-11-07 13:34:48');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07 14:40:07
