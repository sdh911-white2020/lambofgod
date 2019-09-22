-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version       5.7.25

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
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '기본키',
  `name` varchar(20) NOT NULL COMMENT '이름',
  `nickname` varchar(30) NOT NULL COMMENT '별명',
  `password` varchar(50) NOT NULL COMMENT '비밀번호',
  `tel` varchar(20) NOT NULL COMMENT '전화번호',
  `email` varchar(100) NOT NULL COMMENT '이메일',
  `sex` char(1) DEFAULT NULL COMMENT '성별',
  `flag` char(1) NOT NULL DEFAULT 'Y' COMMENT '탈퇴(삭제)여부, 정상:Y, 탈퇴:F',
  `flag_out_time` datetime DEFAULT NULL COMMENT '탈퇴(삭제) 일자',
  `updated` datetime DEFAULT NULL COMMENT '회원수정 일자',
  `created` datetime NOT NULL COMMENT '회원가입 일자',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `members_name` (`name`),
  KEY `members_tel` (`tel`),
  KEY `members_flag_out_time` (`flag_out_time`),
  KEY `members_created` (`created`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'AA','ba','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer1@ewr.com','M','Y',NULL,NULL,'2019-01-18 00:00:00'),(2,'AB','bb','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer2@ewr.com','M','Y',NULL,NULL,'2019-02-18 00:00:00'),(3,'AC','bc','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer3@ewr.com','F','Y',NULL,NULL,'2019-03-18 00:00:00'),(4,'AD','bd','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer4@ewr.com','M','Y',NULL,NULL,'2019-04-18 00:00:00'),(5,'AE','be','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer5@ewr.com','M','Y',NULL,NULL,'2019-05-18 00:00:00'),(6,'AF','bf','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer6@ewr.com','F','Y',NULL,NULL,'2019-06-18 00:00:00'),(7,'AG','bg','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer7@ewr.com','M','Y',NULL,NULL,'2019-07-18 00:00:00'),(8,'AH','bh','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer8@ewr.com','M','Y',NULL,NULL,'2019-08-18 00:00:00'),(9,'AI','bi','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer9@ewr.com','F','Y',NULL,NULL,'2019-09-01 00:00:00'),(10,'AJ','bj','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer10@ewr.com','M','Y',NULL,NULL,'2019-09-02 00:00:00'),(11,'AK','bk','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer11@ewr.com','F','Y',NULL,NULL,'2019-09-03 00:00:00'),(12,'AL','bl','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer12@ewr.com','M','Y',NULL,NULL,'2019-09-04 00:00:00'),(13,'AM','bm','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer13@ewr.com','F','Y',NULL,NULL,'2019-09-05 00:00:00'),(14,'AN','bn','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer14@ewr.com','M','Y',NULL,NULL,'2019-09-06 00:00:00'),(15,'AO','bo','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer15@ewr.com','M','Y',NULL,NULL,'2019-09-07 00:00:00'),(16,'AP','bp','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer16@ewr.com','M','Y',NULL,NULL,'2019-09-08 00:00:00'),(17,'AQ','bq','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer17@ewr.com','M','Y',NULL,NULL,'2019-09-09 00:00:00'),(18,'AR','br','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer18@ewr.com','F','Y',NULL,NULL,'2019-09-10 00:00:00'),(19,'AS','bs','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer19@ewr.com','M','Y',NULL,NULL,'2019-09-11 00:00:00'),(20,'AT','bt','4c541e467bbfe4c611c144cebde1863b','01000000000','werwer29@ewr.com','F','Y',NULL,NULL,'2019-09-12 00:00:00');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recommend_codes`
--

DROP TABLE IF EXISTS `recommend_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recommend_codes` (
  `member_id` int(11) NOT NULL COMMENT '가입회원 members 테이블 PK',
  `code` varchar(10) NOT NULL COMMENT '추천인 코드',
  PRIMARY KEY (`member_id`),
  KEY `recommend_code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recommend_codes`
--

LOCK TABLES `recommend_codes` WRITE;
/*!40000 ALTER TABLE `recommend_codes` DISABLE KEYS */;
INSERT INTO `recommend_codes` VALUES (6,'05pyFcl1zy'),(12,'05VyGcZ2u4'),(18,'150yoca2ma'),(14,'15gybcJ296'),(9,'65KyXcR2a1'),(1,'A5VypcU13t'),(19,'B54yxcO2Mb'),(10,'B5zyKc62o2'),(3,'c53yEcD17v'),(4,'E5ty1cH1uw'),(17,'G5ayQcy2T9'),(2,'j5YyBcL1iu'),(7,'L5ryxcp1zz'),(11,'M5QyycM243'),(15,'S5Hygc62T7'),(5,'w59yEcV1ex'),(13,'W5KyjcM2u5'),(20,'W5MyPcB2Xc'),(8,'Z5ny0c62y0'),(16,'z5qy9ci2j8');
/*!40000 ALTER TABLE `recommend_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recommend_logs`
--

DROP TABLE IF EXISTS `recommend_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recommend_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '기본키',
  `member_id` int(11) NOT NULL COMMENT '추천받는 기존 회원 members 테이블 PK',
  `new_member_id` int(11) NOT NULL COMMENT '추천하는 신규 회원 members 테이블 PK',
  `created` datetime NOT NULL COMMENT '추천 일자',
  PRIMARY KEY (`id`),
  KEY `recommend_member_id` (`member_id`),
  KEY `recommend_new_member_id` (`new_member_id`),
  KEY `recommend_created` (`created`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recommend_logs`
--

LOCK TABLES `recommend_logs` WRITE;
/*!40000 ALTER TABLE `recommend_logs` DISABLE KEYS */;
INSERT INTO `recommend_logs` VALUES (1,1,2,'2019-02-18 00:00:00'),(2,1,3,'2019-03-18 00:00:00'),(3,1,4,'2019-04-18 00:00:00'),(4,1,5,'2019-05-18 00:00:00'),(5,1,6,'2019-06-18 00:00:00'),(6,3,7,'2019-07-18 00:00:00'),(7,3,8,'2019-08-18 00:00:00'),(8,3,9,'2019-09-01 00:00:00'),(9,3,10,'2019-09-02 00:00:00');
/*!40000 ALTER TABLE `recommend_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-22 14:40:54