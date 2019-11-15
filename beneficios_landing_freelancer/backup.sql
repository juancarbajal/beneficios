-- MySQL dump 10.13  Distrib 5.6.24, for osx10.10 (x86_64)
--
-- Host: localhost    Database: afiliados
-- ------------------------------------------------------
-- Server version	5.6.15

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
-- Table structure for table `BNF4_LandingClientesColaboradores`
--

DROP TABLE IF EXISTS `BNF4_LandingClientesColaboradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BNF4_LandingClientesColaboradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombres_Apellidos` varchar(120) DEFAULT NULL,
  `Telefonos` varchar(10) DEFAULT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Especialista` varchar(80) DEFAULT NULL,
  `Creado` datetime DEFAULT NULL,
  `Documento` varchar(15) DEFAULT NULL,
  `Tipo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BNF4_LandingClientesColaboradores`
--

LOCK TABLES `BNF4_LandingClientesColaboradores` WRITE;
/*!40000 ALTER TABLE `BNF4_LandingClientesColaboradores` DISABLE KEYS */;
/*!40000 ALTER TABLE `BNF4_LandingClientesColaboradores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BNF4_LandingReferidos`
--

DROP TABLE IF EXISTS `BNF4_LandingReferidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BNF4_LandingReferidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombres_Apellidos` varchar(45) DEFAULT NULL,
  `Telefonos` varchar(45) DEFAULT NULL,
  `Fecha_referencia` datetime DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id_idx` (`cliente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BNF4_LandingReferidos`
--

LOCK TABLES `BNF4_LandingReferidos` WRITE;
/*!40000 ALTER TABLE `BNF4_LandingReferidos` DISABLE KEYS */;
/*!40000 ALTER TABLE `BNF4_LandingReferidos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-10 22:40:45
