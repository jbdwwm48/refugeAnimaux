-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: db_refuge_animaux
-- ------------------------------------------------------
-- Server version	9.2.0

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
-- Table structure for table `animal`
--

DROP TABLE IF EXISTS `animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal` (
  `id_animal` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `genre` enum('F','M') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_arrivee` date DEFAULT NULL,
  `date_deces` date DEFAULT NULL,
  `historique` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `image` varchar(250) DEFAULT NULL,
  `id_cage` int DEFAULT NULL,
  PRIMARY KEY (`id_animal`),
  KEY `id_cage` (`id_cage`),
  CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`id_cage`) REFERENCES `cage` (`id_cage`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animal`
--

LOCK TABLES `animal` WRITE;
/*!40000 ALTER TABLE `animal` DISABLE KEYS */;
INSERT INTO `animal` VALUES (1,'Thor','M','CHI001','Islande','2020-05-10','2021-06-15',NULL,'Thor adore jouer à \"attraper le bâton\", mais il ramène toujours des pierres à la place. On l\'appelle le \"Dieu du Mauvais Lancer\".','https://cdn.pixabay.com/photo/2019/11/07/08/40/puppy-4608266_640.jpg',1),(2,'Freya','F','CHI002','Norvège','2019-03-22','2020-04-12',NULL,'Freya a sauvé un chaton coincé dans un arbre. Depuis, elle se prend pour une super-héroïne.','https://cdn.pixabay.com/photo/2020/11/22/20/12/schafer-dog-5767834_640.jpg',2),(3,'Odin','M','CHI003','Suède','2018-07-14','2019-08-20',NULL,'Odin a un œil qui brille comme une étoile. Il adore raconter des histoires de Vikings à ses amis.','https://cdn.pixabay.com/photo/2018/10/19/21/01/dog-3759590_640.jpg',3),(4,'Loki','M','CHI004','Danemark','2021-01-05','2022-02-10',NULL,'Loki est un farceur. Il cache les chaussures des soigneurs et rit quand ils les cherchent.','https://cdn.pixabay.com/photo/2015/11/17/13/13/bulldog-1047518_640.jpg',4),(5,'Sif','F','CHI005','Finlande','2017-11-30','2018-12-25',NULL,'Sif a une fourrure si brillante qu\'on dirait de l\'or. Elle adore se faire brosser.','https://cdn.pixabay.com/photo/2021/04/07/05/39/labrador-retriever-6158095_640.jpg',5),(6,'Eclipse','M','CHE001','France','2015-04-18','2016-05-22',NULL,'Eclipse est noir comme la nuit. Il adore galoper sous la pleine lune.','https://cdn.pixabay.com/photo/2018/11/15/22/20/horse-3818264_640.jpg',6),(7,'Luna','F','CHE002','Belgique','2016-08-12','2017-09-15',NULL,'Luna a une crinière argentée. Elle rêve de devenir une licorne.','https://cdn.pixabay.com/photo/2021/05/23/15/54/horse-6276602_640.jpg',7),(8,'Storm','M','CHE003','Canada','2014-02-28','2015-03-30',NULL,'Storm est un cheval puissant. Il adore les défis et les courses sous la pluie.','https://cdn.pixabay.com/photo/2018/05/11/11/11/horse-3390256_640.jpg',8),(9,'Melman','M','GIR001','Kenya','2012-06-10','2013-07-12',NULL,'Melman est un peu hypocondriaque. Il porte toujours une écharpe, même en été.','https://cdn.pixabay.com/photo/2019/07/02/10/25/giraffe-4312090_640.jpg',9),(10,'Dumbo','M','ELE001','Thaïlande','2010-09-05','2011-10-10',NULL,'Dumbo adore voler avec ses grandes oreilles. Enfin, il essaie…','https://cdn.pixabay.com/photo/2016/05/28/08/32/elephant-1421167_640.jpg',10),(11,'Ellie','F','ELE002','Inde','2009-12-15','2010-01-20','2023-01-01','Ellie était la reine du refuge. Elle adorait danser sous la pluie.','https://cdn.pixabay.com/photo/2020/01/24/22/32/elephant-4791438_640.jpg',11),(12,'Slytherin','M','SERP001','Australie','2018-03-25','2019-04-30',NULL,'Slytherin est un serpent très malin. Il adore jouer à cache-cache.','https://cdn.pixabay.com/photo/2019/02/06/17/09/snake-3979601_640.jpg',12),(13,'Vipera','F','SERP002','Brésil','2019-07-18','2020-08-22',NULL,'Vipera est rapide comme l\'éclair. Elle adore surprendre ses soigneurs.','https://cdn.pixabay.com/photo/2014/08/15/21/40/snake-419043_640.jpg',13),(14,'Cobra','M','SERP003','Inde','2020-11-12','2021-12-15',NULL,'Cobra a un regard hypnotique. Il adore faire des blagues en se dressant brusquement.','https://cdn.pixabay.com/photo/2016/01/13/09/42/snake-1137456_640.jpg',14),(15,'Python','F','SERP004','Afrique','2021-02-28','2022-03-30',NULL,'Python est une grande dormeuse. Elle s\'enroule autour des arbres pour faire la sieste.','https://cdn.pixabay.com/photo/2022/09/16/20/26/rattlesnake-7459567_640.jpg',15),(16,'Croc','M','CROC001','Australie','2015-05-10','2016-06-15',NULL,'Croc est un vrai dur à cuire, mais il adore les câlins… quand personne ne regarde.','https://cdn.pixabay.com/photo/2014/01/14/18/31/nile-crocodile-245013_640.jpg',16),(17,'Snap','F','CROC002','USA','2016-08-12','2017-09-15',NULL,'Snap est une coquine. Elle adore faire claquer ses mâchoires pour impressionner.','https://cdn.pixabay.com/photo/2017/05/07/19/00/crocodile-2293232_640.jpg',17),(18,'Jaws','M','CROC003','Afrique','2017-11-30','2018-12-25',NULL,'Jaws est le roi des eaux. Il adore nager et faire des vagues.','https://cdn.pixabay.com/photo/2017/05/07/19/00/crocodile-2293232_640.jpg',18),(19,'Bite','F','CROC004','Inde','2018-03-25','2019-04-30',NULL,'Bite est une vraie mordante, mais elle a un cœur d\'or.','https://cdn.pixabay.com/photo/2017/09/09/17/59/crocodile-2732850_640.jpg',19),(20,'Rexy','M','CROC005','Brésil','2019-07-18','2020-08-22',NULL,'Rexy est un explorateur. Il adore découvrir de nouveaux coins du refuge.','https://cdn.pixabay.com/photo/2016/10/11/04/23/crocodile-1730510_640.jpg',20),(21,'Alpha','M','LOUP001','Canada','2016-08-12','2017-09-15',NULL,'Alpha est le chef de la meute. Il veille sur tous les animaux.','https://cdn.pixabay.com/photo/2023/07/22/05/50/wolf-8142720_640.png',21),(22,'Luna','F','LOUP002','Russie','2017-11-30','2018-12-25',NULL,'Luna est une louve protectrice. Elle adore jouer avec les petits.','https://cdn.pixabay.com/photo/2016/04/18/10/17/wolf-1336229_640.jpg',22),(23,'Shadow','M','LOUP003','Alaska','2018-03-25','2019-04-30',NULL,'Shadow est discret et mystérieux. On le voit rarement, mais il est toujours là.','https://cdn.pixabay.com/photo/2023/11/07/12/55/wolf-8372315_640.jpg',23),(24,'Fang','M','LOUP004','Canada','2019-07-18','2020-08-22',NULL,'Fang est un loup solitaire, mais il adore les câlins en secret.','https://cdn.pixabay.com/photo/2023/01/23/15/54/wolf-7739013_640.jpg',24),(25,'Snow','F','LOUP005','Russie','2020-11-12','2021-12-15',NULL,'Snow est une louve blanche majestueuse. Elle adore la neige.','https://cdn.pixabay.com/photo/2022/11/13/18/20/grey-wolf-7589860_640.jpg',25),(26,'Misty','F','CHAT001','France','2019-05-10','2020-06-15',NULL,'Misty est une chatte calme et affectueuse. Elle adore se prélasser au soleil.','https://cdn.pixabay.com/photo/2024/03/07/10/38/simba-8618301_640.jpg',26),(27,'Shadow','M','CHAT002','Espagne','2020-03-22','2021-04-12',NULL,'Shadow est un chat discret. Il adore se cacher dans les cartons.','https://cdn.pixabay.com/photo/2024/01/29/20/40/cat-8540772_640.jpg',27),(28,'Whiskers','M','CHAT003','Italie','2018-07-14','2019-08-20','2023-02-01','Whiskers était un chat espiègle. Il adorait jouer avec les lacets des soigneurs.','https://cdn.pixabay.com/photo/2023/06/22/15/17/cat-8081701_640.jpg',28),(29,'Baudet','M','ANE001','France','2015-06-01','2016-07-10',NULL,'Baudet est un âne têtu mais très affectueux. Il adore les carottes.','https://cdn.pixabay.com/photo/2019/04/17/18/13/donkey-4134955_640.jpg',29),(30,'Molly','F','MUL001','France','2021-02-28','2022-03-30',NULL,'Molly est un mulet robuste. Elle adore aider les soigneurs et porte toujours un sourire.','https://media.istockphoto.com/id/480392886/fr/photo/%C3%A2ne-gros-plan.jpg?s=612x612&w=0&k=20&c=iAlT3LIIokxGcvWe0QWLynfugTeVqUjCT-AmPX44l6A=',30);
/*!40000 ALTER TABLE `animal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `animal_espece`
--

DROP TABLE IF EXISTS `animal_espece`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal_espece` (
  `id_animal` int NOT NULL,
  `id_espece` int NOT NULL,
  PRIMARY KEY (`id_animal`,`id_espece`),
  KEY `id_espece` (`id_espece`),
  CONSTRAINT `animal_espece_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animal` (`id_animal`),
  CONSTRAINT `animal_espece_ibfk_2` FOREIGN KEY (`id_espece`) REFERENCES `espece` (`id_espece`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animal_espece`
--

LOCK TABLES `animal_espece` WRITE;
/*!40000 ALTER TABLE `animal_espece` DISABLE KEYS */;
INSERT INTO `animal_espece` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,2),(7,2),(8,2),(30,2),(9,3),(10,4),(11,4),(12,5),(13,5),(14,5),(15,5),(16,6),(17,6),(18,6),(19,6),(20,6),(21,7),(22,7),(23,7),(24,7),(25,7),(26,8),(27,8),(28,8),(29,9),(30,9);
/*!40000 ALTER TABLE `animal_espece` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cage`
--

DROP TABLE IF EXISTS `cage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cage` (
  `id_cage` int NOT NULL AUTO_INCREMENT,
  `numero` int DEFAULT NULL,
  `allee` int DEFAULT NULL,
  `salle` int DEFAULT NULL,
  PRIMARY KEY (`id_cage`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cage`
--

LOCK TABLES `cage` WRITE;
/*!40000 ALTER TABLE `cage` DISABLE KEYS */;
INSERT INTO `cage` VALUES (1,101,1,1),(2,102,1,1),(3,103,1,1),(4,104,1,1),(5,105,1,1),(6,201,2,1),(7,202,2,1),(8,203,2,1),(9,301,3,1),(10,401,4,1),(11,402,4,1),(12,501,5,1),(13,502,5,1),(14,503,5,1),(15,504,5,1),(16,601,6,1),(17,602,6,1),(18,603,6,1),(19,604,6,1),(20,605,6,1),(21,701,7,1),(22,702,7,1),(23,703,7,1),(24,704,7,1),(25,705,7,1),(26,801,8,1),(27,802,8,1),(28,803,8,1),(29,901,9,1),(30,1001,10,1),(31,1101,11,1);
/*!40000 ALTER TABLE `cage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enfanter`
--

DROP TABLE IF EXISTS `enfanter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enfanter` (
  `id_animal` int NOT NULL,
  `id_animal_1` int NOT NULL,
  PRIMARY KEY (`id_animal`,`id_animal_1`),
  KEY `id_animal_1` (`id_animal_1`),
  CONSTRAINT `enfanter_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animal` (`id_animal`),
  CONSTRAINT `enfanter_ibfk_2` FOREIGN KEY (`id_animal_1`) REFERENCES `animal` (`id_animal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enfanter`
--

LOCK TABLES `enfanter` WRITE;
/*!40000 ALTER TABLE `enfanter` DISABLE KEYS */;
INSERT INTO `enfanter` VALUES (30,7),(23,21),(24,21),(30,29);
/*!40000 ALTER TABLE `enfanter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `espece`
--

DROP TABLE IF EXISTS `espece`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `espece` (
  `id_espece` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_espece`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `espece`
--

LOCK TABLES `espece` WRITE;
/*!40000 ALTER TABLE `espece` DISABLE KEYS */;
INSERT INTO `espece` VALUES (1,'Chien'),(2,'Cheval'),(3,'Girafe'),(4,'Éléphant'),(5,'Serpent'),(6,'Crocodile'),(7,'Loup'),(8,'Chat'),(9,'Âne');
/*!40000 ALTER TABLE `espece` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personnel` (
  `id_personnel` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `poste` enum('soigneur','administratif','cadre') DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_personnel`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personnel`
--

LOCK TABLES `personnel` WRITE;
/*!40000 ALTER TABLE `personnel` DISABLE KEYS */;
INSERT INTO `personnel` VALUES (1,'Dupont','Jean','soigneur','jean.dupont','$2y$10$N0l/XL7g1O0AFHf4tUKFguCC2Ec8Me5YqpTL6eaMphZwXtjbCyeKu'),(2,'Martin','Marie','soigneur','marie.martin','$2y$10$ZAOPU9kKwJgKen2Lq0fmmexAF10HjOc2RZ10GOUbaSvAQotq9JMUi'),(3,'Bernard','Luc','soigneur','luc.bernard','$2y$10$jBzdnE8Eq.v6hVlobBfcaOKHLcIcIC.mK8snggfG1HbQ209mEAmp.'),(4,'Petit','Sophie','soigneur','sophie.petit','$2y$10$jeF0k.PhEb4TPJDtiLfVi.R66EeLH6ZVGNhGnLd7F3.m0Atx5TajO'),(5,'Leroy','Pierre','soigneur','pierre.leroy','$2y$10$l6BWITjpkSRzxc4/XgsXU.mdrOOomD5jUiznP73FHLfX3NSiLqIVu'),(6,'Moreau','Claire','administratif','claire.moreau','$2y$10$3LDFrOaZmrLNpL8MOwWzwu6XBqG/xXGEjvBJly8azhkY8.JQ12qea'),(7,'Lefebvre','Thomas','cadre','thomas.lefebvre','$2y$10$wcWidekkqPB7CwOVl9ENgusIqX8EjAYDD0iQbc/pwtu08WRPLpKrC'),(8,'Roux','Laura','administratif','laura.roux','$2y$10$U0Vw4Bo60zbA/Zvng4w8juZKspqPt1ADNzqKUZ.VDhCNRlAj3asrS');
/*!40000 ALTER TABLE `personnel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `s_occuper`
--

DROP TABLE IF EXISTS `s_occuper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `s_occuper` (
  `id_animal` int NOT NULL,
  `id_personnel` int NOT NULL,
  PRIMARY KEY (`id_animal`,`id_personnel`),
  KEY `id_personnel` (`id_personnel`),
  CONSTRAINT `s_occuper_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animal` (`id_animal`),
  CONSTRAINT `s_occuper_ibfk_2` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_occuper`
--

LOCK TABLES `s_occuper` WRITE;
/*!40000 ALTER TABLE `s_occuper` DISABLE KEYS */;
INSERT INTO `s_occuper` VALUES (1,1),(6,1),(11,1),(16,1),(21,1),(26,1),(1,2),(2,2),(7,2),(12,2),(17,2),(22,2),(27,2),(3,3),(8,3),(13,3),(18,3),(23,3),(28,3),(4,4),(9,4),(14,4),(19,4),(24,4),(29,4),(5,5),(10,5),(15,5),(20,5),(25,5),(30,5);
/*!40000 ALTER TABLE `s_occuper` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_refuge_animaux'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-26 15:54:38
