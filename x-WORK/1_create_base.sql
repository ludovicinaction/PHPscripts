--
-- Base de données: `site_bdd`
--
CREATE DATABASE IF NOT EXISTS `site_bdd` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `site_bdd`;

--
-- Création des utilisateurs systeme 'mysql'
--

DROP USER 'connect_admin'@'localhost';

CREATE USER 'connect_admin'@'localhost' IDENTIFIED BY 'passwordadmin987456321';

GRANT SELECT, INSERT, UPDATE, DELETE ON site_bdd.* TO 'connect_admin'@'localhost';


DROP USER 'connect_user'@'localhost';

CREATE USER 'connect_user'@'localhost' IDENTIFIED BY 'azerty';

GRANT SELECT, INSERT, UPDATE ON site_bdd.* TO 'connect_user'@'localhost';


--
-- Table structure for table `cat_article`
--

DROP TABLE IF EXISTS `cat_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_article` (
  `id_cat` int(2) NOT NULL AUTO_INCREMENT,
  `nom_cat` varchar(30) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_util`
--

DROP TABLE IF EXISTS `cat_util`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_util` (
  `id_util` int(2) NOT NULL AUTO_INCREMENT,
  `nom_util` varchar(10) NOT NULL,
  `desc_util` varchar(30) NOT NULL,
  PRIMARY KEY (`id_util`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
  `id_art` int(3) NOT NULL AUTO_INCREMENT,
  `titre_art` varchar(60) NOT NULL,
  `date_crea_art` datetime NOT NULL,
  `date_pub_art` date DEFAULT NULL,
  `img_art` varchar(30) DEFAULT NULL,
  `vignette_art` mediumblob,
  `resum_art` varchar(170) DEFAULT NULL,
  `contenu` varchar(10000) NOT NULL,
  `keywords_art` varchar(200) DEFAULT NULL,
  `id_categorie` int(2) DEFAULT NULL,
  PRIMARY KEY (`id_art`),
  KEY `id_categorie` (`id_categorie`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `cat_article` (`id_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `blog_config`
--

DROP TABLE IF EXISTS `blog_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_config` (
  `aff_xs` int(11) NOT NULL,
  `aff_sm` int(11) NOT NULL,
  `aff_md` int(11) NOT NULL,
  `aff_lg` int(11) NOT NULL,
  `nbr_art_page` int(11) NOT NULL,
  `control_comm` tinyint(1) NOT NULL,
  `email_from` varchar(100) CHARACTER SET utf8 NOT NULL,
  `name_from` varchar(50) CHARACTER SET utf8 NOT NULL,
  `name_reply` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email_reply` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email_objet` varchar(150) CHARACTER SET utf8 NOT NULL,
  `email_text` varchar(2000) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `commentaires_blog`
--

DROP TABLE IF EXISTS `commentaires_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentaires_blog` (
  `id_com` int(11) NOT NULL AUTO_INCREMENT,
  `date_com` datetime NOT NULL,
  `nom_com` varchar(50) NOT NULL,
  `email_com` varchar(50) NOT NULL,
  `siteweb_com` varchar(50) DEFAULT NULL,
  `texte_com` varchar(3000) NOT NULL,
  `photo_com` blob,
  `photo_type` varchar(50) DEFAULT NULL,
  `valid_com` tinyint(1) NOT NULL,
  `id_art` int(11) NOT NULL,
  `ctrl_aff` int(11) NOT NULL,
  `jeton` varchar(200) NOT NULL,
  `email_valid` int(11) NOT NULL,
  PRIMARY KEY (`id_com`),
  KEY `commentaires_blog_ibfk_1` (`id_art`),
  CONSTRAINT `commentaires_blog_ibfk_1` FOREIGN KEY (`id_art`) REFERENCES `articles` (`id_art`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commentaires_blog`
--

LOCK TABLES `commentaires_blog` WRITE;
/*!40000 ALTER TABLE `commentaires_blog` DISABLE KEYS */;
/*!40000 ALTER TABLE `commentaires_blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commentaires_rep`
--

DROP TABLE IF EXISTS `commentaires_rep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentaires_rep` (
  `id_rep` int(11) NOT NULL AUTO_INCREMENT,
  `date_rep` datetime NOT NULL,
  `nom_rep` varchar(50) NOT NULL,
  `email_rep` varchar(50) NOT NULL,
  `siteweb_rep` varchar(50) DEFAULT NULL,
  `texte_rep` varchar(3000) NOT NULL,
  `photo_rep` blob,
  `photo_type` varchar(50) DEFAULT NULL,
  `valid_rep` tinyint(1) NOT NULL,
  `ref_rep` int(11) DEFAULT NULL,
  `id_commentaire` int(11) NOT NULL,
  `ctrl_aff` int(11) NOT NULL,
  `jeton` varchar(200) NOT NULL,
  `email_valid` int(11) NOT NULL,
  PRIMARY KEY (`id_rep`),
  KEY `commentaires_rep_ibfk_1` (`id_commentaire`),
  CONSTRAINT `commentaires_rep_ibfk_1` FOREIGN KEY (`id_commentaire`) REFERENCES `commentaires_blog` (`id_com`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commentaires_rep`
--

LOCK TABLES `commentaires_rep` WRITE;
/*!40000 ALTER TABLE `commentaires_rep` DISABLE KEYS */;
/*!40000 ALTER TABLE `commentaires_rep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(3) NOT NULL AUTO_INCREMENT,
  `nom_util` varchar(50) NOT NULL,
  `pwd_util` varchar(255) DEFAULT NULL,
  `mail_util` varchar(50) DEFAULT NULL,
  `id_categorie` int(2) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  KEY `id_categorie` (`id_categorie`),
  CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `cat_util` (`id_util`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;