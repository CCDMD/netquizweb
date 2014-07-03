-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Ven 27 Juin 2014 à 15:46
-- Version du serveur: 5.5.20
-- Version de PHP: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `netquiz`
--

-- --------------------------------------------------------

--
-- Structure de la table `rprojet_questionnaire_terme`
--

CREATE TABLE IF NOT EXISTS `rprojet_questionnaire_terme` (
  `id_projet` bigint(20) NOT NULL,
  `id_questionnaire` bigint(20) NOT NULL,
  `id_terme` bigint(20) NOT NULL,
  PRIMARY KEY (`id_projet`,`id_questionnaire`,`id_terme`),
  KEY `id_projet` (`id_projet`),
  KEY `id_usager` (`id_terme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `rprojet_usager_role`
--

CREATE TABLE IF NOT EXISTS `rprojet_usager_role` (
  `id_projet` bigint(20) NOT NULL,
  `id_usager` bigint(20) NOT NULL,
  `id_role` bigint(20) NOT NULL,
  PRIMARY KEY (`id_projet`,`id_usager`),
  KEY `id_projet` (`id_projet`),
  KEY `id_usager` (`id_usager`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tcategorie`
--

CREATE TABLE IF NOT EXISTS `tcategorie` (
  `id_categorie` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` varchar(512) NOT NULL,
  `remarque` mediumtext,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_categorie`,`id_projet`),
  KEY `idx_statut` (`statut`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tcategorie_index`
--

CREATE TABLE IF NOT EXISTS `tcategorie_index` (
  `id_categorie` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL DEFAULT '0',
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categorie`,`id_projet`),
  KEY `idx_id_usager` (`id_projet`),
  KEY `idx_id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tcategorie_statut`
--

CREATE TABLE IF NOT EXISTS `tcategorie_statut` (
  `id_categorie_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_categorie_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tcategorie_statut`
--

INSERT INTO `tcategorie_statut` (`id_categorie_statut`, `statut`) VALUES
(0, 'supprimé'),
(1, 'publié');

-- --------------------------------------------------------

--
-- Structure de la table `tcollaborateur`
--

CREATE TABLE IF NOT EXISTS `tcollaborateur` (
  `id_projet` bigint(20) NOT NULL,
  `collaborateur_courriel` varchar(128) NOT NULL DEFAULT '',
  `jeton` varchar(16) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_projet`,`collaborateur_courriel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tcollection`
--

CREATE TABLE IF NOT EXISTS `tcollection` (
  `id_collection` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` varchar(512) NOT NULL,
  `remarque` mediumtext,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_collection`,`id_projet`),
  KEY `idx_statut` (`statut`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_collection` (`id_collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tcollection_index`
--

CREATE TABLE IF NOT EXISTS `tcollection_index` (
  `id_collection` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL DEFAULT '0',
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_collection`,`id_projet`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_collection` (`id_collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tcollection_statut`
--

CREATE TABLE IF NOT EXISTS `tcollection_statut` (
  `id_collection_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_collection_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tcollection_statut`
--

INSERT INTO `tcollection_statut` (`id_collection_statut`, `statut`) VALUES
(0, 'supprimé'),
(1, 'publié');

-- --------------------------------------------------------

--
-- Structure de la table `tconfig`
--

CREATE TABLE IF NOT EXISTS `tconfig` (
  `version` varchar(64) DEFAULT NULL,
  `application_disponible` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tconfig`
--

INSERT INTO `tconfig` (`version`, `application_disponible`, `date_modification`) VALUES
('2014-06-26', 1, '2014-06-26 13:00:22');

-- --------------------------------------------------------

--
-- Structure de la table `titem`
--

CREATE TABLE IF NOT EXISTS `titem` (
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` varchar(512) NOT NULL,
  `enonce` mediumtext,
  `type_item` int(11) DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL,
  `suivi` varchar(1) NOT NULL,
  `ponderation` varchar(64) DEFAULT NULL,
  `remarque` mediumtext,
  `demarrer_media` varchar(32) DEFAULT NULL,
  `afficher_solution` varchar(3) DEFAULT NULL,
  `points_retranches` varchar(256) DEFAULT NULL,
  `majmin` varchar(16) DEFAULT NULL,
  `ponctuation` varchar(16) DEFAULT NULL,
  `type_elements1` varchar(32) DEFAULT NULL,
  `type_elements2` varchar(32) DEFAULT NULL,
  `type_etiquettes` varchar(32) DEFAULT NULL,
  `type_champs` varchar(8) DEFAULT NULL,
  `ordre_presentation` varchar(32) DEFAULT NULL,
  `points_mauvaise_reponse` int(11) DEFAULT NULL,
  `correction_maj_min` varchar(3) DEFAULT NULL,
  `correction_ponctuation` varchar(3) DEFAULT NULL,
  `info_comp1_titre` varchar(512) DEFAULT NULL,
  `info_comp1_texte` mediumtext,
  `info_comp2_titre` varchar(512) DEFAULT NULL,
  `info_comp2_texte` mediumtext,
  `media_titre` varchar(512) DEFAULT NULL,
  `media_texte` mediumtext,
  `media_image` bigint(20) NOT NULL,
  `media_son` bigint(20) NOT NULL,
  `media_video` bigint(20) NOT NULL,
  `solution` mediumtext,
  `retroaction_positive` mediumtext,
  `retroaction_negative` mediumtext,
  `retroaction_reponse_imprevue` mediumtext,
  `coordonnee_x` int(11) DEFAULT NULL,
  `coordonnee_y` int(11) DEFAULT NULL,
  `couleur_element` varchar(6) DEFAULT NULL,
  `couleur_element_associe` varchar(6) DEFAULT NULL,
  `afficher_masque` varchar(1) DEFAULT NULL,
  `reponse_bonne_message` mediumtext,
  `reponse_bonne_media` bigint(20) DEFAULT NULL,
  `reponse_mauvaise_message` mediumtext,
  `reponse_mauvaise_media` bigint(20) DEFAULT NULL,
  `reponse_incomplete_message` mediumtext,
  `reponse_incomplete_media` bigint(20) DEFAULT NULL,
  `statut` int(11) NOT NULL,
  `liens` int(11) DEFAULT NULL,
  `type_bonnesreponses` varchar(6) DEFAULT NULL,
  `orientation_elements` varchar(12) DEFAULT NULL,
  `image` bigint(20) DEFAULT NULL,
  `couleur_zones` varchar(8) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item`,`id_projet`),
  KEY `idx_statut` (`statut`),
  KEY `idx_type_item` (`type_item`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_item` (`id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `titem_classeur`
--

CREATE TABLE IF NOT EXISTS `titem_classeur` (
  `id_classeur` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` mediumtext,
  `retroaction` mediumtext,
  `retroaction_negative` mediumtext,
  `retroaction_incomplete` mediumtext,
  `ordre` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_classeur`),
  KEY `idx_id_item` (`id_item`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_classeur_element`
--

CREATE TABLE IF NOT EXISTS `titem_classeur_element` (
  `id_element` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_classeur` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `texte` mediumtext,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_element`),
  KEY `idx_id_classeur` (`id_classeur`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_classeur_element_retro`
--

CREATE TABLE IF NOT EXISTS `titem_classeur_element_retro` (
  `id_retro` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_element` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `id_classeur` bigint(6) DEFAULT NULL,
  `retroaction` mediumtext,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_retro`),
  KEY `idx_id_classeur` (`id_classeur`),
  KEY `idx_id_element` (`id_element`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_couleur`
--

CREATE TABLE IF NOT EXISTS `titem_couleur` (
  `id_item_couleur` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `couleur` varchar(6) DEFAULT NULL,
  `titre` mediumtext,
  `retroaction` mediumtext,
  `retroaction_negative` mediumtext,
  `retroaction_incomplete` mediumtext,
  `ordre` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item_couleur`),
  KEY `idx_id_item` (`id_item`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_index`
--

CREATE TABLE IF NOT EXISTS `titem_index` (
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL DEFAULT '0',
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item`,`id_projet`),
  KEY `idx_questionnaire_item` (`id_item`),
  KEY `id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `titem_lacune`
--

CREATE TABLE IF NOT EXISTS `titem_lacune` (
  `id_lacune` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `idx_lacune` bigint(20) NOT NULL,
  `retro` mediumtext,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_lacune`),
  KEY `idx_id_item` (`id_item`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_lacune_reponse`
--

CREATE TABLE IF NOT EXISTS `titem_lacune_reponse` (
  `id_item_reponse` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_item` bigint(20) NOT NULL,
  `idx_lacune` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `reponse` varchar(3) NOT NULL,
  `element` mediumtext NOT NULL,
  `retroaction` mediumtext NOT NULL,
  `ordre` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item_reponse`),
  KEY `idx_id_item` (`id_item`),
  KEY `idx_idx_lacune` (`idx_lacune`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_marque`
--

CREATE TABLE IF NOT EXISTS `titem_marque` (
  `id_marque` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `couleur` varchar(6) DEFAULT NULL,
  `texte` mediumtext,
  `position_debut` int(11) DEFAULT NULL,
  `position_fin` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_marque`),
  KEY `idx_id_item` (`id_item`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_marque_retro`
--

CREATE TABLE IF NOT EXISTS `titem_marque_retro` (
  `id_marque_retro` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_marque` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `couleur` varchar(6) DEFAULT NULL,
  `retroaction` mediumtext,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_marque_retro`),
  KEY `idx_id_marque` (`id_marque`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_reponse`
--

CREATE TABLE IF NOT EXISTS `titem_reponse` (
  `id_item_reponse` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `reponse` varchar(3) DEFAULT NULL,
  `element` mediumtext NOT NULL,
  `element_associe` mediumtext,
  `masque` text,
  `retroaction` mediumtext NOT NULL,
  `retroaction_negative` mediumtext,
  `retroaction_incomplete` mediumtext,
  `coordonnee_x` int(11) DEFAULT NULL,
  `coordonnee_y` int(11) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item_reponse`),
  KEY `idx_id_item` (`id_item`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `titem_section`
--

CREATE TABLE IF NOT EXISTS `titem_section` (
  `id_item_section` bigint(20) NOT NULL,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `generation_question_type` varchar(64) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item_section`,`id_projet`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_item` (`id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `titem_statut`
--

CREATE TABLE IF NOT EXISTS `titem_statut` (
  `id_tquestionnaire_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_tquestionnaire_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `titem_statut`
--

INSERT INTO `titem_statut` (`id_tquestionnaire_statut`, `statut`) VALUES
(0, 'supprimé'),
(1, 'brouillon'),
(2, 'publié');

-- --------------------------------------------------------

--
-- Structure de la table `tlangue`
--

CREATE TABLE IF NOT EXISTS `tlangue` (
  `id_langue` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` varchar(512) NOT NULL,
  `delimiteur` varchar(10) DEFAULT NULL,
  `boutons_annuler` mediumtext,
  `boutons_ok` mediumtext,
  `consignes_association` mediumtext,
  `consignes_choixmultiples` mediumtext,
  `consignes_classement` mediumtext,
  `consignes_damier_masquees` mediumtext,
  `consignes_damier_nonmasquees` mediumtext,
  `consignes_developpement` mediumtext,
  `consignes_dictee_debut` mediumtext,
  `consignes_dictee_majuscules` mediumtext,
  `consignes_dictee_ponctuation` mediumtext,
  `consignes_marquage` mediumtext,
  `consignes_ordre` mediumtext,
  `consignes_reponsebreve_debut` mediumtext,
  `consignes_reponsebreve_majuscules` mediumtext,
  `consignes_reponsebreve_ponctuation` mediumtext,
  `consignes_reponsesmultiples_unereponse` mediumtext,
  `consignes_reponsesmultiples_toutes` mediumtext,
  `consignes_lacunaire_menu` mediumtext,
  `consignes_lacunaire_glisser` mediumtext,
  `consignes_lacunaire_reponsebreve_debut` mediumtext,
  `consignes_lacunaire_reponsebreve_majuscules` mediumtext,
  `consignes_lacunaire_reponsebreve_ponctuation` mediumtext,
  `consignes_vraifaux` mediumtext,
  `consignes_zones` mediumtext,
  `fenetre_renseignements` mediumtext,
  `fenetre_nom` mediumtext,
  `fenetre_prenom` mediumtext,
  `fenetre_matricule` mediumtext,
  `fenetre_groupe` mediumtext,
  `fenetre_courriel` mediumtext,
  `fenetre_autre` mediumtext,
  `fenetre_envoi` mediumtext,
  `fenetre_courriel_destinataire` mediumtext,
  `fonctionnalites_commencer` mediumtext,
  `fonctionnalites_effacer` mediumtext,
  `fonctionnalites_courriel` mediumtext,
  `fonctionnalites_imprimer` mediumtext,
  `fonctionnalites_recommencer` mediumtext,
  `fonctionnalites_reprendre` mediumtext,
  `fonctionnalites_resultats` mediumtext,
  `fonctionnalites_lexique` mediumtext,
  `fonctionnalites_questionnaire` mediumtext,
  `fonctionnalites_solution` mediumtext,
  `fonctionnalites_valider` mediumtext,
  `navigation_page` mediumtext,
  `navigation_de` mediumtext,
  `message_bonnereponse` mediumtext,
  `message_mauvaisereponse` mediumtext,
  `message_reponseincomplete` mediumtext,
  `media_bonnereponse` bigint(20) DEFAULT NULL,
  `media_mauvaisereponse` bigint(20) DEFAULT NULL,
  `media_reponseincomplete` bigint(20) DEFAULT NULL,
  `message_libelle_solution` mediumtext,
  `message_point` mediumtext,
  `message_points` mediumtext,
  `message_sanstitre` mediumtext,
  `conjonction_et` mediumtext,
  `message_dictee_motsentrop` mediumtext,
  `message_dictee_orthographe` mediumtext,
  `message_dictee_motsmanquants` mediumtext,
  `message_reponsesuggeree` mediumtext,
  `resultats_afaire` mediumtext,
  `resultats_areprendre` mediumtext,
  `resultats_message_courriel_succes` mediumtext,
  `resultats_message_courriel_erreur` mediumtext,
  `resultats_objet_courriel` mediumtext,
  `resultats_confirmation` mediumtext,
  `resultats_accueil` mediumtext,
  `resultats_nbessais` mediumtext,
  `resultats_points` mediumtext,
  `resultats_reussi` mediumtext,
  `resultats_sansobjet` mediumtext,
  `resultats_statut` mediumtext,
  `resultats_tempsdereponse` mediumtext,
  `item_association` mediumtext,
  `item_choixmultiples` mediumtext,
  `item_classement` mediumtext,
  `item_damier` mediumtext,
  `item_developpement` mediumtext,
  `item_dictee` mediumtext,
  `item_marquage` mediumtext,
  `item_miseenordre` mediumtext,
  `item_reponsebreve` mediumtext,
  `item_reponsesmultiples` mediumtext,
  `item_textelacunaire` mediumtext,
  `item_vraioufaux` mediumtext,
  `item_zonesaidentifier` mediumtext,
  `remarque` mediumtext,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_langue`,`id_projet`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_langue` (`id_langue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tlangue_defaut`
--

CREATE TABLE IF NOT EXISTS `tlangue_defaut` (
  `id_langue` bigint(20) NOT NULL,
  `titre` varchar(512) NOT NULL,
  `delimiteur` varchar(10) DEFAULT NULL,
  `boutons_annuler` mediumtext,
  `boutons_ok` mediumtext,
  `consignes_association` mediumtext,
  `consignes_choixmultiples` mediumtext,
  `consignes_classement` mediumtext,
  `consignes_damier_masquees` mediumtext,
  `consignes_damier_nonmasquees` mediumtext,
  `consignes_developpement` mediumtext,
  `consignes_dictee_debut` mediumtext,
  `consignes_dictee_majuscules` mediumtext,
  `consignes_dictee_ponctuation` mediumtext,
  `consignes_marquage` mediumtext,
  `consignes_ordre` mediumtext,
  `consignes_reponsebreve_debut` mediumtext,
  `consignes_reponsebreve_majuscules` mediumtext,
  `consignes_reponsebreve_ponctuation` mediumtext,
  `consignes_reponsesmultiples_unereponse` mediumtext,
  `consignes_reponsesmultiples_toutes` mediumtext,
  `consignes_lacunaire_menu` mediumtext,
  `consignes_lacunaire_glisser` mediumtext,
  `consignes_lacunaire_reponsebreve_debut` mediumtext,
  `consignes_lacunaire_reponsebreve_majuscules` mediumtext,
  `consignes_lacunaire_reponsebreve_ponctuation` mediumtext,
  `consignes_vraifaux` mediumtext,
  `consignes_zones` mediumtext,
  `fenetre_renseignements` mediumtext,
  `fenetre_nom` mediumtext,
  `fenetre_prenom` mediumtext,
  `fenetre_matricule` mediumtext,
  `fenetre_groupe` mediumtext,
  `fenetre_courriel` mediumtext,
  `fenetre_autre` mediumtext,
  `fenetre_envoi` mediumtext,
  `fenetre_courriel_destinataire` mediumtext,
  `fonctionnalites_commencer` mediumtext,
  `fonctionnalites_effacer` mediumtext,
  `fonctionnalites_courriel` mediumtext,
  `fonctionnalites_imprimer` mediumtext,
  `fonctionnalites_recommencer` mediumtext,
  `fonctionnalites_reprendre` mediumtext,
  `fonctionnalites_resultats` mediumtext,
  `fonctionnalites_lexique` mediumtext,
  `fonctionnalites_questionnaire` mediumtext,
  `fonctionnalites_solution` mediumtext,
  `fonctionnalites_valider` mediumtext,
  `navigation_page` mediumtext,
  `navigation_de` mediumtext,
  `message_bonnereponse` mediumtext,
  `message_mauvaisereponse` mediumtext,
  `message_reponseincomplete` mediumtext,
  `media_bonnereponse` bigint(20) DEFAULT NULL,
  `media_mauvaisereponse` bigint(20) DEFAULT NULL,
  `media_reponseincomplete` bigint(20) DEFAULT NULL,
  `message_libelle_solution` mediumtext,
  `message_point` mediumtext,
  `message_points` mediumtext,
  `message_sanstitre` mediumtext,
  `conjonction_et` mediumtext,
  `message_dictee_motsentrop` mediumtext,
  `message_dictee_orthographe` mediumtext,
  `message_dictee_motsmanquants` mediumtext,
  `message_reponsesuggeree` mediumtext,
  `resultats_afaire` mediumtext,
  `resultats_areprendre` mediumtext,
  `resultats_message_courriel_succes` mediumtext,
  `resultats_message_courriel_erreur` mediumtext,
  `resultats_objet_courriel` mediumtext,
  `resultats_confirmation` mediumtext,
  `resultats_accueil` mediumtext,
  `resultats_nbessais` mediumtext,
  `resultats_points` mediumtext,
  `resultats_reussi` mediumtext,
  `resultats_sansobjet` mediumtext,
  `resultats_statut` mediumtext,
  `resultats_tempsdereponse` mediumtext,
  `item_association` mediumtext,
  `item_choixmultiples` mediumtext,
  `item_classement` mediumtext,
  `item_damier` mediumtext,
  `item_developpement` mediumtext,
  `item_dictee` mediumtext,
  `item_marquage` mediumtext,
  `item_miseenordre` mediumtext,
  `item_reponsebreve` mediumtext,
  `item_reponsesmultiples` mediumtext,
  `item_textelacunaire` mediumtext,
  `item_vraioufaux` mediumtext,
  `item_zonesaidentifier` mediumtext,
  `remarque` mediumtext,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  KEY `idx_id_langue` (`id_langue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tlangue_defaut`
--

INSERT INTO `tlangue_defaut` (`id_langue`, `titre`, `delimiteur`, `boutons_annuler`, `boutons_ok`, `consignes_association`, `consignes_choixmultiples`, `consignes_classement`, `consignes_damier_masquees`, `consignes_damier_nonmasquees`, `consignes_developpement`, `consignes_dictee_debut`, `consignes_dictee_majuscules`, `consignes_dictee_ponctuation`, `consignes_marquage`, `consignes_ordre`, `consignes_reponsebreve_debut`, `consignes_reponsebreve_majuscules`, `consignes_reponsebreve_ponctuation`, `consignes_reponsesmultiples_unereponse`, `consignes_reponsesmultiples_toutes`, `consignes_lacunaire_menu`, `consignes_lacunaire_glisser`, `consignes_lacunaire_reponsebreve_debut`, `consignes_lacunaire_reponsebreve_majuscules`, `consignes_lacunaire_reponsebreve_ponctuation`, `consignes_vraifaux`, `consignes_zones`, `fenetre_renseignements`, `fenetre_nom`, `fenetre_prenom`, `fenetre_matricule`, `fenetre_groupe`, `fenetre_courriel`, `fenetre_autre`, `fenetre_envoi`, `fenetre_courriel_destinataire`, `fonctionnalites_commencer`, `fonctionnalites_effacer`, `fonctionnalites_courriel`, `fonctionnalites_imprimer`, `fonctionnalites_recommencer`, `fonctionnalites_reprendre`, `fonctionnalites_resultats`, `fonctionnalites_lexique`, `fonctionnalites_questionnaire`, `fonctionnalites_solution`, `fonctionnalites_valider`, `navigation_page`, `navigation_de`, `message_bonnereponse`, `message_mauvaisereponse`, `message_reponseincomplete`, `media_bonnereponse`, `media_mauvaisereponse`, `media_reponseincomplete`, `message_libelle_solution`, `message_point`, `message_points`, `message_sanstitre`, `conjonction_et`, `message_dictee_motsentrop`, `message_dictee_orthographe`, `message_dictee_motsmanquants`, `message_reponsesuggeree`, `resultats_afaire`, `resultats_areprendre`, `resultats_message_courriel_succes`, `resultats_message_courriel_erreur`, `resultats_objet_courriel`, `resultats_confirmation`, `resultats_accueil`, `resultats_nbessais`, `resultats_points`, `resultats_reussi`, `resultats_sansobjet`, `resultats_statut`, `resultats_tempsdereponse`, `item_association`, `item_choixmultiples`, `item_classement`, `item_damier`, `item_developpement`, `item_dictee`, `item_marquage`, `item_miseenordre`, `item_reponsebreve`, `item_reponsesmultiples`, `item_textelacunaire`, `item_vraioufaux`, `item_zonesaidentifier`, `remarque`, `statut`, `date_modification`, `date_creation`) VALUES
(1, 'Français', '0', 'Annuler', 'OK', 'Pour associer correctement les éléments de droite avec ceux de gauche, glissez et relâchez chacun des éléments de droite sur la case appropriée.', 'Cliquez sur un des boutons radio pour indiquer une réponse.', 'Pour classer les différents éléments (mots ou images), glissez-les, puis relâchez-les dans le classeur approprié au bas de l''écran.', 'Cliquez successivement sur deux cases masquées pour trouver celles qui sont appariées. Dès que vous découvrez une paire, les cases restent démasquées.', 'Cliquez successivement sur deux cases qui sont appariées. Si vous avez trouvé la paire, les deux cases vont se masquer.', 'Inscrivez votre réponse dans la zone appropriée.', 'Faites jouer le son ou la vidéo pour entendre le texte et transcrivez-le dans la zone appropriée. Lorsque cela s''applique, la correction tiendra compte des accents', 'des majuscules/minuscules', 'de la ponctuation', 'Pour marquer (ou mettre en surbrillance) une expression, sélectionnez-la, puis cliquez sur le carré de la couleur appropriée. Pour enlever le marquage, sélectionnez le texte mis en surbrillance, puis cliquez sur le bouton « Effacer les marques ». (Attention! Si vous voulez changer la couleur de marquage d''une expression déjà marquée, il est préférable d''effacer d''abord l''ancienne couleur.)', 'Pour mettre les éléments dans le bon ordre, glissez et relâchez chacun d''eux sur la case appropriée.', 'Inscrivez votre réponse dans la zone appropriée. Lorsque cela s''applique, la correction tiendra compte des accents', 'des majuscules/minuscules', 'de la ponctuation', 'Cliquez sur une case à cocher pour indiquer au moins une bonne réponse.', 'Cliquez sur une ou plusieurs cases à cocher pour indiquer toutes les bonnes réponses.', 'Sélectionnez l''expression correcte dans chaque menu déroulant du texte.', 'Glissez, puis déposez chacune des expressions de la liste dans l''espace qui lui convient dans le texte.', 'Inscrivez l''expression manquante dans chaque champ de saisie du texte. Lorsque cela s''applique, la correction tiendra compte des accents', 'des majuscules/minuscules', 'de la ponctuation', 'Cliquez sur un des boutons radio pour indiquer une réponse.', 'Pour associer correctement les éléments du bas, glissez et déposez leur étiquette correspondante sur la zone appropriée de l''image.', 'Renseignements sur le répondant', 'Nom', 'Prénom', 'Identifiant', 'Groupe', 'Votre courriel', 'Message', 'Envoi des résultats par courriel', 'Courriel du destinataire', 'Commencer', 'Effacer les marques', 'Envoyer par courriel', 'Imprimer', 'Recommencer', 'Reprendre', 'Résultats', 'Glossaire', 'Questionnaire', 'Solution', 'Valider', 'Page', 'de', 'Bonne réponse', 'Mauvaise réponse', 'Réponse incomplète', 0, 0, 0, 'Solution', 'point', 'points', 'Sans titre', 'et', 'Mots en trop [entre crochets]', 'Mots mal orthographiés', 'Mots manquants (soulignés)', 'Réponse suggérée', 'À faire', 'À reprendre', 'Courriel envoyé avec succès', 'Erreur lors de l''envoi du courriel', 'Objet', 'La commande « Recommencer » efface toutes les réponses et les résultats. Si vous ne voulez pas effacer ces résultats, ou si vous voulez les faire imprimer ou envoyer par courriel avant de reprendre le questionnaire, cliquez sur « Annuler », sinon cliquez sur « OK ».', 'Le retour à la page d''accueil du questionnaire efface toutes les réponses et les résultats. Si vous ne voulez pas effacer ces résultats, ou si vous voulez les faire imprimer ou envoyer par courriel avant de de retourner à la page d''accueil, cliquez sur « Annuler », sinon cliquez sur « OK ».', 'Nombre d''essais', 'Points', 'Réussi', '-', 'Statut', 'Temps de réponse', 'Associations', 'Choix multiples', 'Classement', 'Damier', 'Développement', 'Dictée', 'Marquage', 'Mise en ordre', 'Réponse brève', 'Réponses multiples', 'Texte lacunaire', 'Vrai ou faux', 'Zones à identifier', '', 1, '2013-09-25 17:43:25', '2012-09-24 19:37:34'),
(2, 'English', '1', 'Cancel', 'OK', 'To correctly match items on the right with items on the left, drag and drop each item on the right onto the appropriate box.', 'Click on one of the radio buttons to indicate a correct answer.', 'To classify the different items (words or images), drag and drop the elements into the appropriate folder at the bottom of the screen.', 'Click successively on two squares to find a match. When you find a correct pair, the squares'' contents will stay visible.', 'Click successively on two squares to find a match. When you find a correct pair, a mask image will hide the squares.', 'Write your answer in the appropriate field.', 'Play the audio or video clip to hear the dictation text and write it in the appropriate field. The correction will take into account', 'capital and small letters', 'punctuation', 'To highlight text, first select it and then click the appropriate coloured square. To remove highlighting, select the text once again and click the “Erase highlighting” button. (Attention! If you want to change the colour text which is already highlighted, erase the old highlighting first.)', 'To put the items in the right order, drag and drop each one onto the appropriate box.', 'Write your answer in the appropriate field. The correction will take into account', 'capital and small letters', 'punctuation', 'Click on a check box to indicate at least one correct answer.', 'Click on one or several check boxes to indicate the correct answers.', 'Select the correct expression in each drop-down menu of the text.', 'Drag and drop each of the expressions in the list into its appropriate space in the text.', 'Write the missing expression in each input field of the text. The correction will take into account', 'capital and small letters', 'punctuation', 'Click on one of the radio buttons to indicate a correct answer.', 'To correctly identify parts of the image, drag and drop the corresponding tags onto the appropriate box in the image.', 'Information', 'Last name', 'First name', 'Identifier', 'Group', 'Your email', 'Other', 'Send results by email', 'Destination email', 'Start', 'Erase selected highlighting', 'Send by email', 'Print', 'Start over', 'Reset', 'Results', 'Glossary', 'Quiz', 'Correct Answer', 'Submit', 'Page', 'of', 'Correct answer', 'Incorrect answer', 'Incomplete answer', 0, 0, 0, 'Correct Answer', 'point', 'points', 'Untitled', 'and', 'Superfluous words [between brackets]', 'Spelling mistakes', 'Words missing (underlined)', 'Suggested Answer', 'To be done', 'To be done over', 'Email successfully sent', 'Error on sending email', 'Subject', 'Stat over erases all answers and results. If you do not want to erase these results or if you want to print them before redoing the quiz, click on Cancel; otherwise, click on OK.', 'Returning to the home page erases all answers and results. If you do not want to erase these results or if you want to print them before redoing the quiz, click on Cancel; otherwise, click OK.', 'Nomber of tries', 'Points', 'Successfully finished', '-', 'Status', 'Response time', 'Matching help', 'Multiple choices help', 'Classification help', 'Matching board help', 'Essay help', 'Dictation help', 'Highlight text help', 'Sequencing help', 'Short answer help', 'Multiple answer help', 'Fill in the blanks help', 'True/False help', 'Identify parts of an image help', '', 1, '2013-01-09 07:19:44', '2012-10-18 23:35:00');

-- --------------------------------------------------------

--
-- Structure de la table `tlangue_index`
--

CREATE TABLE IF NOT EXISTS `tlangue_index` (
  `id_langue` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL DEFAULT '0',
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_langue`,`id_projet`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tlangue_statut`
--

CREATE TABLE IF NOT EXISTS `tlangue_statut` (
  `id_langue_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_langue_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tlangue_statut`
--

INSERT INTO `tlangue_statut` (`id_langue_statut`, `statut`) VALUES
(0, 'supprimé'),
(1, 'publié');

-- --------------------------------------------------------

--
-- Structure de la table `tmedia`
--

CREATE TABLE IF NOT EXISTS `tmedia` (
  `id_media` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` varchar(512) NOT NULL,
  `remarque` mediumtext,
  `type` varchar(12) DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  `source` varchar(10) DEFAULT NULL,
  `fichier` varchar(1024) DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `suivi` varchar(1) DEFAULT NULL,
  `liens` int(11) DEFAULT NULL,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_media`,`id_projet`),
  KEY `idx_statut` (`statut`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_media` (`id_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tmedia_index`
--

CREATE TABLE IF NOT EXISTS `tmedia_index` (
  `id_media` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL DEFAULT '0',
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_media`,`id_projet`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tprojet`
--

CREATE TABLE IF NOT EXISTS `tprojet` (
  `id_projet` bigint(20) NOT NULL AUTO_INCREMENT,
  `titre` varchar(512) NOT NULL,
  `description` mediumtext,
  `repertoire` varchar(256) DEFAULT NULL,
  `notification` int(11) DEFAULT NULL,
  `dernier_id_langue` bigint(20) NOT NULL,
  `dernier_id_questionnaire` bigint(20) NOT NULL,
  `dernier_id_item` bigint(20) NOT NULL,
  `dernier_id_media` bigint(20) NOT NULL,
  `dernier_id_categorie` bigint(20) NOT NULL,
  `dernier_id_collection` bigint(20) NOT NULL,
  `dernier_id_section` bigint(20) NOT NULL,
  `dernier_id_terme` bigint(20) NOT NULL,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_projet`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `tprojet_index`
--

CREATE TABLE IF NOT EXISTS `tprojet_index` (
  `id_projet` bigint(20) NOT NULL,
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tprojet_role`
--

CREATE TABLE IF NOT EXISTS `tprojet_role` (
  `id_role` bigint(20) NOT NULL AUTO_INCREMENT,
  `titre` mediumtext,
  `description` mediumtext,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `tprojet_role`
--

INSERT INTO `tprojet_role` (`id_role`, `titre`, `description`) VALUES
(1, 'Responsable', NULL),
(2, 'Collaborateur', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tprojet_statut`
--

CREATE TABLE IF NOT EXISTS `tprojet_statut` (
  `id_projet_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_projet_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tprojet_statut`
--

INSERT INTO `tprojet_statut` (`id_projet_statut`, `statut`) VALUES
(0, 'inactif'),
(1, 'actif'),
(2, 'supprimé'),
(3, 'verrouillé');

-- --------------------------------------------------------

--
-- Structure de la table `tquestionnaire`
--

CREATE TABLE IF NOT EXISTS `tquestionnaire` (
  `id_questionnaire` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `titre` varchar(512) DEFAULT NULL,
  `titre_long` varchar(1024) DEFAULT NULL,
  `suivi` varchar(1) DEFAULT NULL,
  `generation_question_type` varchar(64) DEFAULT NULL,
  `generation_question_nb` int(11) DEFAULT NULL,
  `temps_reponse_calculer` varchar(3) DEFAULT NULL,
  `temps_passation_type` varchar(32) DEFAULT NULL,
  `temps_passation_heures` varchar(4) DEFAULT NULL,
  `temps_passation_minutes` varchar(4) DEFAULT NULL,
  `essais_repondre_type` varchar(32) DEFAULT NULL,
  `essais_repondre_nb` int(11) DEFAULT NULL,
  `affichage_resultats_type` varchar(64) DEFAULT NULL,
  `demarrage_media_type` varchar(64) DEFAULT NULL,
  `id_langue_questionnaire` int(11) DEFAULT NULL,
  `id_collection` int(11) DEFAULT NULL,
  `theme` varchar(128) DEFAULT NULL,
  `mot_bienvenue` mediumtext,
  `note` mediumtext,
  `generique` mediumtext,
  `media_titre` varchar(512) DEFAULT NULL,
  `media_texte` mediumtext,
  `media_image` varchar(256) DEFAULT NULL,
  `media_son` varchar(256) DEFAULT NULL,
  `media_video` varchar(256) DEFAULT NULL,
  `texte_fin` mediumtext,
  `publication_repertoire` varchar(64) DEFAULT NULL,
  `publication_date` timestamp NULL DEFAULT NULL,
  `nb_items` int(11) DEFAULT NULL,
  `remarque` mediumtext,
  `statut` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_questionnaire`,`id_projet`),
  KEY `idx_statut` (`statut`),
  KEY `id_questionnaire` (`id_questionnaire`),
  KEY `id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tquestionnaire_index`
--

CREATE TABLE IF NOT EXISTS `tquestionnaire_index` (
  `id_questionnaire` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL DEFAULT '0',
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_questionnaire`,`id_projet`),
  KEY `idx_id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tquestionnaire_item`
--

CREATE TABLE IF NOT EXISTS `tquestionnaire_item` (
  `id_questionnaire_item` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_questionnaire` bigint(20) NOT NULL,
  `id_item` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `ordre` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `ponderation_quest` varchar(64) DEFAULT NULL,
  `afficher_solution_quest` varchar(16) DEFAULT NULL,
  `ordre_presentation_quest` varchar(32) DEFAULT NULL,
  `type_etiquettes_quest` varchar(32) DEFAULT NULL,
  `demarrer_media_quest` varchar(16) DEFAULT NULL,
  `points_retranches_quest` varchar(256) DEFAULT NULL,
  `majmin_quest` varchar(16) DEFAULT NULL,
  `ponctuation_quest` varchar(16) DEFAULT NULL,
  `type_bonnesreponses_quest` varchar(6) DEFAULT NULL,
  `couleur_element_quest` varchar(6) DEFAULT NULL,
  `couleur_element_associe_quest` varchar(6) DEFAULT NULL,
  `afficher_masque_quest` varchar(1) DEFAULT NULL,
  `orientation_elements_quest` varchar(12) DEFAULT NULL,
  `statut` int(11) NOT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_questionnaire_item`),
  KEY `idx_id_questionnaire` (`id_questionnaire`),
  KEY `idx_id_item` (`id_item`),
  KEY `id_projet` (`id_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `tquestionnaire_statut`
--

CREATE TABLE IF NOT EXISTS `tquestionnaire_statut` (
  `id_tquestionnaire_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_tquestionnaire_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tquestionnaire_statut`
--

INSERT INTO `tquestionnaire_statut` (`id_tquestionnaire_statut`, `statut`) VALUES
(0, 'supprimé'),
(1, 'brouillon'),
(2, 'publié');

-- --------------------------------------------------------

--
-- Structure de la table `tterme`
--

CREATE TABLE IF NOT EXISTS `tterme` (
  `id_terme` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `terme` varchar(512) DEFAULT NULL,
  `variantes` mediumtext,
  `type_definition` varchar(16) DEFAULT NULL,
  `texte` mediumtext,
  `url` varchar(256) DEFAULT NULL,
  `media_image` bigint(20) NOT NULL,
  `media_son` bigint(20) NOT NULL,
  `media_video` bigint(20) NOT NULL,
  `remarque` mediumtext,
  `statut` int(11) DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_terme`,`id_projet`),
  KEY `idx_statut` (`statut`),
  KEY `idx_id_projet` (`id_projet`),
  KEY `idx_id_collection` (`id_terme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ttexte`
--

CREATE TABLE IF NOT EXISTS `ttexte` (
  `id_texte` varchar(64) NOT NULL,
  `langue_interface` varchar(10) NOT NULL,
  `texte` mediumtext,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_texte`,`langue_interface`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `ttexte`
--

INSERT INTO `ttexte` (`id_texte`, `langue_interface`, `texte`, `date_modification`) VALUES
('message_avertissement', 'en_CA', '', '2014-06-16 23:15:50'),
('message_avertissement', 'fr_CA', '', '2014-06-16 23:15:40'),
('message_bienvenue', 'en_CA', '', '2014-06-16 23:15:50'),
('message_bienvenue', 'fr_CA', '', '2014-06-16 23:15:40');

-- --------------------------------------------------------

--
-- Structure de la table `tusager`
--

CREATE TABLE IF NOT EXISTS `tusager` (
  `id_usager` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(256) DEFAULT NULL,
  `prenom` varchar(256) DEFAULT NULL,
  `courriel` varchar(128) DEFAULT NULL,
  `code_usager` varchar(256) DEFAULT NULL,
  `mot_passe` varchar(256) DEFAULT NULL,
  `gds_secret` varchar(16) NOT NULL,
  `nb_mauvais_essais` int(11) DEFAULT NULL,
  `langue_interface` varchar(15) DEFAULT NULL,
  `dern_nouv_consultee` bigint(20) DEFAULT NULL,
  `pref_message` varchar(45) DEFAULT NULL,
  `pref_nb_elem_page` int(11) DEFAULT NULL,
  `pref_projet` bigint(20) DEFAULT NULL,
  `pref_apercu_langue` bigint(20) DEFAULT NULL,
  `pref_apercu_theme` varchar(1024) DEFAULT NULL,
  `code_rappel` varchar(128) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `statut` int(11) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `date_dern_authentification` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usager`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `tusager_index`
--

CREATE TABLE IF NOT EXISTS `tusager_index` (
  `id_usager` bigint(20) NOT NULL,
  `texte` mediumtext,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usager`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tusager_role`
--

CREATE TABLE IF NOT EXISTS `tusager_role` (
  `id_role` bigint(20) NOT NULL AUTO_INCREMENT,
  `titre` mediumtext,
  `description` mediumtext,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `tusager_role`
--

INSERT INTO `tusager_role` (`id_role`, `titre`, `description`) VALUES
(0, 'Usager', NULL),
(1, 'Administrateur', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tusager_statut`
--

CREATE TABLE IF NOT EXISTS `tusager_statut` (
  `id_tquestionnaire_statut` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  PRIMARY KEY (`id_tquestionnaire_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tusager_statut`
--

INSERT INTO `tusager_statut` (`id_tquestionnaire_statut`, `statut`) VALUES
(0, 'actif'),
(1, 'verrouillé'),
(2, 'approbation'),
(3, 'refusé'),
(4, 'incomplet');

-- --------------------------------------------------------

--
-- Structure de la table `tverrou`
--

CREATE TABLE IF NOT EXISTS `tverrou` (
  `id_usager` bigint(20) NOT NULL,
  `id_projet` bigint(20) NOT NULL,
  `id_element` varchar(16) NOT NULL,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `id_usager` (`id_usager`,`id_projet`,`id_element`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `rprojet_usager_role`
--
ALTER TABLE `rprojet_usager_role`
  ADD CONSTRAINT `rprojet_usager_role_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE,
  ADD CONSTRAINT `rprojet_usager_role_ibfk_2` FOREIGN KEY (`id_usager`) REFERENCES `tusager` (`id_usager`) ON DELETE CASCADE,
  ADD CONSTRAINT `rprojet_usager_role_ibfk_5` FOREIGN KEY (`id_role`) REFERENCES `tprojet_role` (`id_role`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tcategorie`
--
ALTER TABLE `tcategorie`
  ADD CONSTRAINT `tcategorie_ibfk_1` FOREIGN KEY (`statut`) REFERENCES `tcategorie_statut` (`id_categorie_statut`),
  ADD CONSTRAINT `tcategorie_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tcategorie_index`
--
ALTER TABLE `tcategorie_index`
  ADD CONSTRAINT `tcategorie_index_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tcollaborateur`
--
ALTER TABLE `tcollaborateur`
  ADD CONSTRAINT `tcollaborateur_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tcollection`
--
ALTER TABLE `tcollection`
  ADD CONSTRAINT `tcollection_ibfk_2` FOREIGN KEY (`statut`) REFERENCES `tcollection_statut` (`id_collection_statut`),
  ADD CONSTRAINT `tcollection_ibfk_3` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tcollection_index`
--
ALTER TABLE `tcollection_index`
  ADD CONSTRAINT `tcollection_index_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem`
--
ALTER TABLE `titem`
  ADD CONSTRAINT `titem_ibfk_1` FOREIGN KEY (`statut`) REFERENCES `titem_statut` (`id_tquestionnaire_statut`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `titem_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_classeur`
--
ALTER TABLE `titem_classeur`
  ADD CONSTRAINT `titem_classeur_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_classeur_element`
--
ALTER TABLE `titem_classeur_element`
  ADD CONSTRAINT `titem_classeur_element_ibfk_1` FOREIGN KEY (`id_classeur`) REFERENCES `titem_classeur` (`id_classeur`) ON DELETE CASCADE,
  ADD CONSTRAINT `titem_classeur_element_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_classeur_element_retro`
--
ALTER TABLE `titem_classeur_element_retro`
  ADD CONSTRAINT `titem_classeur_element_retro_ibfk_1` FOREIGN KEY (`id_element`) REFERENCES `titem_classeur_element` (`id_element`) ON DELETE CASCADE,
  ADD CONSTRAINT `titem_classeur_element_retro_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_couleur`
--
ALTER TABLE `titem_couleur`
  ADD CONSTRAINT `titem_couleur_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_index`
--
ALTER TABLE `titem_index`
  ADD CONSTRAINT `titem_index_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_lacune`
--
ALTER TABLE `titem_lacune`
  ADD CONSTRAINT `titem_lacune_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_lacune_reponse`
--
ALTER TABLE `titem_lacune_reponse`
  ADD CONSTRAINT `titem_lacune_reponse_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_marque`
--
ALTER TABLE `titem_marque`
  ADD CONSTRAINT `titem_marque_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_marque_retro`
--
ALTER TABLE `titem_marque_retro`
  ADD CONSTRAINT `titem_marque_retro_ibfk_1` FOREIGN KEY (`id_marque`) REFERENCES `titem_marque` (`id_marque`) ON DELETE CASCADE,
  ADD CONSTRAINT `titem_marque_retro_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_reponse`
--
ALTER TABLE `titem_reponse`
  ADD CONSTRAINT `titem_reponse_ibfk_3` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `titem_section`
--
ALTER TABLE `titem_section`
  ADD CONSTRAINT `titem_section_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tlangue`
--
ALTER TABLE `tlangue`
  ADD CONSTRAINT `tlangue_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tlangue_index`
--
ALTER TABLE `tlangue_index`
  ADD CONSTRAINT `tlangue_index_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tmedia`
--
ALTER TABLE `tmedia`
  ADD CONSTRAINT `tmedia_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tmedia_index`
--
ALTER TABLE `tmedia_index`
  ADD CONSTRAINT `tmedia_index_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tprojet`
--
ALTER TABLE `tprojet`
  ADD CONSTRAINT `tprojet_ibfk_1` FOREIGN KEY (`statut`) REFERENCES `tusager_statut` (`id_tquestionnaire_statut`);

--
-- Contraintes pour la table `tprojet_index`
--
ALTER TABLE `tprojet_index`
  ADD CONSTRAINT `tprojet_index_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tquestionnaire`
--
ALTER TABLE `tquestionnaire`
  ADD CONSTRAINT `tquestionnaire_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE,
  ADD CONSTRAINT `tquestionnaire_ibfk_2` FOREIGN KEY (`statut`) REFERENCES `tquestionnaire_statut` (`id_tquestionnaire_statut`);

--
-- Contraintes pour la table `tquestionnaire_index`
--
ALTER TABLE `tquestionnaire_index`
  ADD CONSTRAINT `tquestionnaire_index_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tquestionnaire_item`
--
ALTER TABLE `tquestionnaire_item`
  ADD CONSTRAINT `tquestionnaire_item_ibfk_3` FOREIGN KEY (`id_projet`) REFERENCES `tprojet` (`id_projet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tusager_index`
--
ALTER TABLE `tusager_index`
  ADD CONSTRAINT `tusager_index_ibfk_1` FOREIGN KEY (`id_usager`) REFERENCES `tusager` (`id_usager`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
