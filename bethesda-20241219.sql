-- MySQL dump 10.13  Distrib 8.0.37, for Linux (x86_64)
--
-- Host: localhost    Database: bethesda
-- ------------------------------------------------------
-- Server version	8.0.37-0ubuntu0.23.10.2

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
-- Temporary view structure for view `V_AGE_GROUP_GENDER_ROOM`
--

DROP TABLE IF EXISTS `V_AGE_GROUP_GENDER_ROOM`;
/*!50001 DROP VIEW IF EXISTS `V_AGE_GROUP_GENDER_ROOM`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_AGE_GROUP_GENDER_ROOM` AS SELECT 
 1 AS `age`,
 1 AS `sexe`,
 1 AS `poids`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_AGE_GROUP_ROOM`
--

DROP TABLE IF EXISTS `V_AGE_GROUP_ROOM`;
/*!50001 DROP VIEW IF EXISTS `V_AGE_GROUP_ROOM`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_AGE_GROUP_ROOM` AS SELECT 
 1 AS `tranche_age`,
 1 AS `effectif`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_GENDER_ROOM`
--

DROP TABLE IF EXISTS `V_GENDER_ROOM`;
/*!50001 DROP VIEW IF EXISTS `V_GENDER_ROOM`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_GENDER_ROOM` AS SELECT 
 1 AS `room`,
 1 AS `workforce`,
 1 AS `gender`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_QUATER`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_QUATER`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_QUATER`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_QUATER` AS SELECT 
 1 AS `std`,
 1 AS `abscences`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ` AS SELECT 
 1 AS `std`,
 1 AS `abscences`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ0`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ0`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ0`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ0` AS SELECT 
 1 AS `room`,
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ1`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ1`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ1`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ1` AS SELECT 
 1 AS `room`,
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ2`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ2`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ2`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ2` AS SELECT 
 1 AS `room`,
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_1`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_1`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_1`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_1` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_10`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_10`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_10`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_10` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_12`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_12`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_12`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_12` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_13`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_13`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_13`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_13` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_14`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_14`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_14`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_14` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_15`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_15`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_15`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_15` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_16`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_16`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_16`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_16` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_17`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_17`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_17`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_17` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_18`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_18`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_18`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_18` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_19`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_19`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_19`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_19` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_2`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_2`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_2`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_2` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_20`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_20`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_20`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_20` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_21`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_21`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_21`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_21` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_23`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_23`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_23`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_23` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_24`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_24`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_24`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_24` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_25`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_25`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_25`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_25` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_26`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_26`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_26`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_26` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_3`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_3`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_3`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_3` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_4`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_4`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_4`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_4` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_5`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_5`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_5`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_5` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_6`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_6`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_6`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_6` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_7`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_7`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_7`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_7` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ38_9`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_9`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ38_9`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ38_9` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_1`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_1`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_1`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_1` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_10`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_10`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_10`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_10` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_12`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_12`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_12`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_12` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_13`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_13`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_13`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_13` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_14`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_14`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_14`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_14` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_15`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_15`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_15`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_15` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_16`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_16`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_16`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_16` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_17`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_17`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_17`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_17` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_18`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_18`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_18`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_18` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_19`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_19`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_19`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_19` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_2`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_2`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_2`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_2` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_20`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_20`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_20`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_20` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_21`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_21`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_21`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_21` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_23`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_23`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_23`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_23` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_24`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_24`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_24`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_24` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_25`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_25`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_25`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_25` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_26`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_26`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_26`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_26` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_3`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_3`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_3`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_3` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_4`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_4`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_4`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_4` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_5`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_5`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_5`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_5` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_6`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_6`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_6`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_6` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_7`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_7`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_7`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_7` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_ABSCENCE_SEQ39_9`
--

DROP TABLE IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_9`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_ABSCENCE_SEQ39_9`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_ABSCENCE_SEQ39_9` AS SELECT 
 1 AS `std`,
 1 AS `total_hours`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_MARK_QUATER`
--

DROP TABLE IF EXISTS `V_STUDENT_MARK_QUATER`;
/*!50001 DROP VIEW IF EXISTS `V_STUDENT_MARK_QUATER`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `V_STUDENT_MARK_QUATER` AS SELECT 
 1 AS `std`,
 1 AS `crs`,
 1 AS `coef`,
 1 AS `value1`,
 1 AS `weight1`,
 1 AS `value2`,
 1 AS `weight2`,
 1 AS `value`,
 1 AS `weight`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `V_STUDENT_MARK_QUATER_1`
--

DROP TABLE IF EXISTS `V_STUDENT_MARK_QUATER_1`;
