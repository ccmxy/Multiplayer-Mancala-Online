-- MySQL dump 10.13  Distrib 5.1.66, for redhat-linux-gnu (x86_64)
--
-- Host: mysql.eecs.oregonstate.edu    Database: CS275
-- ------------------------------------------------------
-- Server version	5.1.65-community-log

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
-- Table structure for table `endzone`
--


DROP TABLE IF EXISTS `endzones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `endzones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seedCount` int(11) NOT NULL default 0,
  `game_id` int(11) NOT NULL default 0,
  `player_id` int(11) NOT NULL default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endzone`
--

LOCK TABLES `endzones` WRITE;
/*!40000 ALTER TABLE `endzones` DISABLE KEYS */;
INSERT INTO `endzones` VALUES (1, 0,1,1),(2, 0,1,1);
/*!40000 ALTER TABLE `endzones` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `turn` tinyint(1) not null DEFAULT 0,
  `game_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
   KEY `game_id` (`game_id`),
   CONSTRAINT `mancala_player_ibfk_3` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `players` WRITE;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
/*Player 1's turn, player1 owns endzone with id 1, player2 owns endzone with id 2*/
INSERT INTO `players` VALUES (1,'Player1',1, 1,1),(2,'Player2', 0, 1,1);
/*!40000 ALTER TABLE `players` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pits` (
  `id` int(11) NOT NULL,
  `seedCount` int(11) NOT NULL default 4,
  `owner` int(11) DEFAULT NULL,
  KEY `owner` (`owner`),
  CONSTRAINT `mancala_pit_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `players` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

LOCK TABLES `pits` WRITE;
/*!40000 ALTER TABLE `pits` DISABLE KEYS */;
INSERT INTO `pits` VALUES (1,4,1),(2,4,1),(3,4,1),(4,4,1),(5,4,1),(6,4,1);
INSERT INTO `pits` VALUES (7,4,2),(8,4,2),(9,4,2),(10,4,2),(11,4,2),(12,4,2);
/*!40000 ALTER TABLE `pits` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `turn` varchar(255) NOT NULL default 'Player1',
  `active` int(11) NOT NULL default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (1,'Fun Game','Player1', 0);
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `players_games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `players_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  CONSTRAINT `players_games_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`),
  CONSTRAINT `players_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

LOCK TABLES `players_games` WRITE;
/*!40000 ALTER TABLE `players_games` DISABLE KEYS */;
INSERT INTO `players_games` VALUES (1,1,1);
INSERT INTO `players_games` VALUES (2,2,1);
/*!40000 ALTER TABLE `players_games` ENABLE KEYS */;
UNLOCK TABLES;
