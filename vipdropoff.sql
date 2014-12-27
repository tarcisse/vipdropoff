-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Dim 02 Novembre 2014 à 21:04
-- Version du serveur :  5.6.16
-- Version de PHP :  5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `vipdropoff`
--

-- --------------------------------------------------------

--
-- Structure de la table `affecteevoiture`
--

CREATE TABLE IF NOT EXISTS `affecteevoiture` (
  `ID_RESERVATION` bigint(20) NOT NULL,
  `ID_VOITURE` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_RESERVATION`,`ID_VOITURE`),
  KEY `FK_AFFECTEEVOITURE2` (`ID_VOITURE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `affectetrajet`
--

CREATE TABLE IF NOT EXISTS `affectetrajet` (
  `ID_RESERVATION` bigint(20) NOT NULL,
  `ID_TRAJET` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_RESERVATION`,`ID_TRAJET`),
  KEY `FK_AFFECTETRAJET2` (`ID_TRAJET`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `ID_CLIENT` int(11) NOT NULL AUTO_INCREMENT,
  `CIVILITE` varchar(5) DEFAULT NULL,
  `NOM` varchar(250) DEFAULT NULL,
  `PRENOM` varchar(250) DEFAULT NULL,
  `SOCIETE` varchar(250) DEFAULT NULL,
  `CODEPOSTAL` int(11) DEFAULT NULL,
  `VILLE` varchar(250) DEFAULT NULL,
  `PAYS` varchar(250) DEFAULT NULL,
  `ADRESSE` varchar(250) DEFAULT NULL,
  `TELEPHONE1` varchar(250) DEFAULT NULL,
  `TELEPHONE2` varchar(250) DEFAULT NULL,
  `TYPE_CLIENT` varchar(250) DEFAULT NULL,
  `FAXE` varchar(250) DEFAULT NULL,
  `MAIL` varchar(250) DEFAULT NULL,
  `PASSWORD` varchar(250) DEFAULT NULL,
  `DATE_INSCRIPTION` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_CLIENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE IF NOT EXISTS `commentaire` (
  `ID_COMMENTAIRE` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_CLIENT` int(11) NOT NULL,
  `TCOMMENTAIRE` varchar(250) DEFAULT NULL,
  `DCOMMENTAIRE` varchar(250) DEFAULT NULL,
  `COMMENTAIRE` varchar(250) DEFAULT NULL,
  `OPTION_commentaire` varchar(250) DEFAULT NULL,
  `DATE_COMMENTAIRE` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_COMMENTAIRE`),
  KEY `FK_FAIRE2` (`ID_CLIENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE IF NOT EXISTS `evenement` (
  `ID_EVENEMENT` bigint(20) NOT NULL AUTO_INCREMENT,
  `TITRE_EVENEMENT` varchar(250) DEFAULT NULL,
  `TEXTE_EVENEMENT` text,
  `LIEU_EVENEMENT` varchar(250) DEFAULT NULL,
  `DATE_EVENEMENT` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_EVENEMENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE IF NOT EXISTS `reservation` (
  `ID_RESERVATION` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_CLIENT` int(11) NOT NULL,
  `NOM` varchar(250) DEFAULT NULL,
  `PRENOM` varchar(250) DEFAULT NULL,
  `SOCIETE` varchar(250) DEFAULT NULL,
  `CODEPOSTAL` int(11) DEFAULT NULL,
  `VILLE` varchar(250) DEFAULT NULL,
  `PAYS` varchar(250) DEFAULT NULL,
  `ADRESSE` varchar(250) DEFAULT NULL,
  `TELEPHONE1` varchar(250) DEFAULT NULL,
  `TELEPHONE2` varchar(250) DEFAULT NULL,
  `TYPE_CLIENT` varchar(250) DEFAULT NULL,
  `FAXE` varchar(250) DEFAULT NULL,
  `MAIL` varchar(250) DEFAULT NULL,
  `PASSWORD` varchar(250) DEFAULT NULL,
  `CIVILITE` varchar(5) DEFAULT NULL,
  `DATE_RESERVATION` int(11) DEFAULT NULL,
  `NPERSONNE` int(11) DEFAULT NULL,
  `COMMENTAIRE` varchar(250) DEFAULT NULL,
  `ETAT` varchar(64) DEFAULT NULL,
  `PRIX` double DEFAULT NULL,
  `DATE_RETOUR` int(11) DEFAULT NULL,
  `SERVICE` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID_RESERVATION`),
  KEY `FK_FAIRE` (`ID_CLIENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

CREATE TABLE IF NOT EXISTS `trajet` (
  `ID_TRAJET` bigint(20) NOT NULL AUTO_INCREMENT,
  `ALLERRETOUR` int(11) DEFAULT NULL,
  `DEPART` varchar(250) DEFAULT NULL,
  `ARRIVER` varchar(250) DEFAULT NULL,
  `DATE_TRAJET` int(11) DEFAULT NULL,
  `DISTANCE` double DEFAULT NULL,
  `DURREE` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID_TRAJET`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE IF NOT EXISTS `voiture` (
  `ID_VOITURE` bigint(20) NOT NULL AUTO_INCREMENT,
  `MARQUE` varchar(250) DEFAULT NULL,
  `NBPLACE` int(11) DEFAULT NULL,
  `CLIMATISATION` int(11) DEFAULT NULL,
  `SURPLUS` varchar(250) DEFAULT NULL,
  `MUSIC` int(11) DEFAULT NULL,
  `MATRICULE` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID_VOITURE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `affecteevoiture`
--
ALTER TABLE `affecteevoiture`
  ADD CONSTRAINT `FK_AFFECTEEVOITURE2` FOREIGN KEY (`ID_VOITURE`) REFERENCES `voiture` (`ID_VOITURE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_AFFECTEEVOITURE` FOREIGN KEY (`ID_RESERVATION`) REFERENCES `reservation` (`ID_RESERVATION`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `affectetrajet`
--
ALTER TABLE `affectetrajet`
  ADD CONSTRAINT `FK_AFFECTETRAJET2` FOREIGN KEY (`ID_TRAJET`) REFERENCES `trajet` (`ID_TRAJET`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_AFFECTETRAJET` FOREIGN KEY (`ID_RESERVATION`) REFERENCES `reservation` (`ID_RESERVATION`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `FK_FAIRE2` FOREIGN KEY (`ID_CLIENT`) REFERENCES `client` (`ID_CLIENT`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_FAIRE` FOREIGN KEY (`ID_CLIENT`) REFERENCES `client` (`ID_CLIENT`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
