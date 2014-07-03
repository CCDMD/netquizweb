<?php

/**
 * Fichier des paramètres par défaut de Netquiz Web
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

define('VERSION_NQW', '2014-06-26');

// ** ------------ CONFIGURATION INTERNE - NE PAS MODIFIER ------------ ** //

// ** Localisation par défaut ** //
define('LANGUE_DEFAUT', 'fr_CA');
define('TIMEZONE_DEFAUT','America/Montreal');

// ** Identification de l'installation ** //
define('ID_INSTALLATION', URL_DOMAINE . URL_BASE);

// ** Configuration pour la BD et les langues ** //
define('DB_CHARSET', 'utf8');
define('LANGUE_DEFAUT_ID', '1');
define('LISTE_LANGUES_PROTEGEES', '1,2');
define('LANGUE_ID_FR_CA', '1');
define('LANGUE_ID_EN_CA', '2');
define('LANGUE_ID_ES_CA', '3');

// ** Répertoires de l'application ** //
define('REPERTOIRE_RESSOURCES', REPERTOIRE_BASE . 'ressources/');
define('REPERTOIRE_JOURNAUX', REPERTOIRE_RESSOURCES . 'journaux/');
define('REPERTOIRE_LANGUES', REPERTOIRE_RESSOURCES . 'langues/');
define('REPERTOIRE_GABARITS', REPERTOIRE_RESSOURCES . 'gabarits/');
define('REPERTOIRE_SQL', REPERTOIRE_RESSOURCES . 'sql/');
define('REPERTOIRE_GABARITS_COURRIELS', REPERTOIRE_GABARITS . 'courriels/');
define('REPERTOIRE_GABARITS_IMPRESSION', REPERTOIRE_GABARITS . 'impression/');
define('REPERTOIRE_GABARITS_PUBLICATION', REPERTOIRE_GABARITS . 'publication/');
define('REPERTOIRE_GABARITS_VALIDATION', REPERTOIRE_GABARITS . 'validation/');
define('REPERTOIRE_GABARITS_EXPORTATION', REPERTOIRE_GABARITS . 'exportation/');
define('REPERTOIRE_GABARITS_IMPORTATION', REPERTOIRE_GABARITS . 'importation/');
define('REPERTOIRE_CLASSES', REPERTOIRE_RESSOURCES . 'classes/');
define('REPERTOIRE_CLASSES_MODELES', REPERTOIRE_CLASSES . 'modeles//');
define('REPERTOIRE_CLASSES_OUTILS', REPERTOIRE_CLASSES . 'outils/');
define('REPERTOIRE_PUB', REPERTOIRE_BASE . 'web/');
define('REPERTOIRE_THEMES', REPERTOIRE_RESSOURCES . 'themes/');
define('REPERTOIRE_MEDIA', REPERTOIRE_RESSOURCES . 'medias/');
define('REPERTOIRE_PREFIX_APERCU', 'apercu/');
define('REPERTOIRE_PREFIX_SAUVEGARDE', 'sauvegarde/');
define('REPERTOIRE_PREFIX_MEDIAS', 'medias/');
define('REPERTOIRE_PREFIX_THEME', 'theme/imagestheme');
define('REPERTOIRE_PREFIX_IMPORT', 'import');

// ** URL de l'application ** //
define('URL_ERREUR', URL_BASE . 'app/erreur.php');
define('URL_IDENTIFICATION', URL_BASE . 'app/identification.php');
define('URL_ACCUEIL', URL_BASE . 'app/questionnaires.php');
define('URL_PUBLICATION', URL_BASE . 'web/');
define('URL_ITEM_MODIFIER', 'bibliotheque.php?demande=item_modifier&item_id_item=');
define('URL_MEDIA_MODIFIER', 'media.php?demande=media_modifier&media_id_media=');
define('URL_COLLECTION_MODIFIER', 'bibliotheque.php?demande=collection_modifier&collection_id_collection=');
define('URL_CATEGORIE_MODIFIER', 'bibliotheque.php?demande=categorie_modifier&categorie_id_categorie=');
define('URL_TERME_MODIFIER', 'bibliotheque.php?demande=terme_modifier&terme_id_terme=');
define('URL_LANGUE_MODIFIER', 'bibliotheque.php?demande=langue_modifier&langue_id_langue=');
define('URL_QUESTIONNAIRE_MODIFIER', 'questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=');
define('URL_COLLABORATEUR_INVITATION', 'app/identification.php?demande=compte&cle=');
define('URL_SQL_INSTALLATION' , 'ressources/sql/netquizweb-installation.sql');

// ** Fichier de l'application ** //
define('FICHIER_MAIN_HTML', 'main.html');
define('FICHIER_INDEX_HTML', 'index.html');
define('FICHIER_MAIN_JS', 'scripts/main.js');
define('FICHIER_LEXIQUE_JS', 'scripts/lexique.js');
define('FICHIER_THEME_DEFAUT', 'Classique');
define('FICHIER_EXPORTATION_XML', 'netquizweb.xml');
define('FICHIER_EXPORTATION_XML_ITEMS', 'item');
define('FICHIER_EXPORTATION_XML_TERMES', 'terme');
define('FICHIER_EXPORTATION_XML_COLLECTIONS', 'collection');
define('FICHIER_EXPORTATION_XML_CATEGORIES', 'categorie');
define('FICHIER_EXPORTATION_XML_LANGUES', 'langue');

// ** Exécutables MYSQL ** //
define('EXEC_MYSQLDUMP', '/bin/mysqldump');
define('EXEC_MYSQL', '/bin/mysql');

// ** Gabarit d'impression ** //
define('IMPRESSION_GABARIT_QUESTIONNAIRE', 'impression/quest-page.php');
define('IMPRESSION_GABARIT_QUESTIONNAIRE_ACCUEIL', 'impression/quest-accueil-page.php');
define('IMPRESSION_GABARIT_QUESTIONNAIRE_FIN', 'impression/quest-fin-page.php');
define('IMPRESSION_GABARIT_SECTION', 'impression/quest-item-page.php');
define('IMPRESSION_GABARIT_ITEM', 'impression/quest-item-page.php');
define('IMPRESSION_GABARIT_COLLECTION', 'impression/collection-page.php');
define('IMPRESSION_GABARIT_CATEGORIE', 'impression/categorie-page.php');
define('IMPRESSION_GABARIT_LANGUE', 'impression/langue-page.php');
define('IMPRESSION_GABARIT_MEDIA', 'impression/media-page.php');
define('IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE', '<span class="gras">:&nbsp;</span>');
define('IMPRESSION_HTML_PREFIX_VALEUR_DEUX_LIGNES', '<br />');
define('IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE', '');
define('IMPRESSION_HTML_SUFFIXE_VALEUR_DEUX_LIGNES', '');
define('IMPRESSION_HTML_AUCUNE_VALEUR', '-');

// ** Éléments HTML utilisés dans l'application  ** //
define('HTML_LISTE_ERREUR_DEBUT',"<li>");
define('HTML_LISTE_ERREUR_FIN',"</li>");
define('HTML_GRAS_DEBUT', '<b>');
define('HTML_GRAS_FIN', '</b>');
define('HTML_SAUT_LIGNE_PUBLICATION', '<br />');
define('HTML_ITEM_ALEATOIRE_DEBUT','gNQ4.initShuffleQuestion(');
define('HTML_ITEM_ALEATOIRE_SEPARATEUR',',');
define('HTML_ITEM_ALEATOIRE_FIN',');');
define('HTML_ITEM_HORS_SECTION_ALEATOIRE_DEBUT','gNQ4.initShuffleSpecific([');
define('HTML_ITEM_HORS_SECTION_ALEATOIRE_SEPARATEUR',',');
define('HTML_ITEM_HORS_SECTION_ALEATOIRE_FIN',']);');
define('HTML_SELECTED', "selected='selected'");
define('HTML_CHECKED',"checked='checked'");
define('HTML_APERCU_IMAGE', "<img src=\"medias/::IMAGE::\" border=\"0\"/>");

// ** Éléments utilisés pour le XML  ** //
define('XML_ENTETE', '<?xml version="1.0" encoding="UTF-8"?>');
define('XML_NQW_DEBUT', '<netquizweb>');
define('XML_NQW_FIN', '</netquizweb>');

// ** Éléments utilisés pour la publication  ** //
define('PUBLICATION_SEPARATEUR_VARIANTES', '||');

// ** Réglages pour la journalisation ** //
define('JOURNALISATION_FICHIER', REPERTOIRE_JOURNAUX . 'netquiz');
define('JOURNALISATION_NIVEAU', '0');

// ** Configuration pour l'envoi de cookies ** //
define('COOKIE_DUREE', 60*60*24*365);
define('COOKIE_CODE_UTILISATEUR', 'netquiz-cu');
define('COOKIE_CONNEXION_ACTIVE', 'netquiz-ca');


// ** Configuration pour la session ** //
define('SESSION_DUREE', 7200);
define('SESSION_DUREE_AVERTISSEMENT', 6300);

// ** Configuration pour les verrous ** //
define('DUREE_VERROU_EXPIRATION', 20);

// ** Configuration pour la sécurité ** //
define('SECURITE_MOTPASSE_LONGUEUR_MIN', 8);
define('SECURITE_MOTPASSE_LETTRE_MIN', 1);
define('SECURITE_MOTPASSE_CHIFFRE_MIN', 1);
define('SECURITE_NB_MAUVAIS_ESSAIS_VERROUILLAGE', 7);
define('SECURITE_LONGMAX_REPERTOIRE', 64);
define('SECURITE_NETTOYAGE_FICHIERS_TEMPORAIRES', 14400);
define('SECURITE_CODEUSAGER_LONGUEUR_MIN', 5);
define('SECURITE_REPERTOIRE_LONGUEUR_MIN', 1);

// ** Configuration Pagination et défauts ** //
define('NB_ELEMENT_PAR_PAGE', 15);
define('NB_MAX_CHOIX_REPONSES', 30);
define('NB_MAX_COULEURS', 9);
define('NB_MAX_COLLABORATEURS', 50);
define('COULEUR_MAUVAISE_REPONSE', 'F50404');
define('NB_MAX_CLASSEURS', 6);
define('NB_MAX_ELEMENTS_PAR_CLASSEURS', 30);
define('TYPE_FICHIER_VIDEO','mov,mp4');
define('TYPE_FICHIER_IMAGE','jpg,jpeg,png,gif');
define('TYPE_FICHIER_SON','mp3');
define('NB_LANGUES_PAR_DEFAUT', 2);
define('COULEUR_ZONES_DEFAUT', '000000');
define('DICTEE_NB_POINTS_RETRANCHES', '0.05');
define('MARQUAGE_NB_POINTS_RETRANCHES', '0.1');
define('MARQUAGE_COULEUR_DEFAUT', 'FFF9C5');
define('REPONSES_MULTIPLES_BONNES_REPONSES_DEFAUT', 'toutes');
define('PREFIX_IDENTIFIANT_PROJET_DEFAUT', 'projet-');
define('INSTALLATION_NOMBRE_TABLES', '44');
define('INSTALLATION_FICHIER_SQL', 'netquizweb-installation.sql');
define('MAJ_PREFIX_FICHIER_SQL', 'netquizweb-maj-');
define("PONDERATION_DEFAULT", "1");

// ** Configuration pour l'importation ** //
define('IMPORTATION_VALEUR_INCONNUE', '?');

// ** Ressources (images, etc) ** //
define('DAMIER_IMAGE_DEFAUT', 'pair.jpg');
define('IMAGE_PAIR_URL', URL_BASE . 'images/default/' . DAMIER_IMAGE_DEFAUT);
define('IMAGE_PAIR_FICHIER', REPERTOIRE_BASE . 'images/default/' . DAMIER_IMAGE_DEFAUT);
define('ITEM_MARQUAGE_COULEUR_MAUVAISE_REPONSE', 'F50404');
define('IMAGE_ABSENTE_DEFAUT_NOM_FICHIER', 'image_absente.png');
define('IMAGE_ABSENTE_DEFAUT_CHEMIN_COMPLET', REPERTOIRE_BASE . 'images/default/' . IMAGE_ABSENTE_DEFAUT_NOM_FICHIER);

// ** Configuration des items disponibles ** //
define('ITEM_1','associations');
define('ITEM_2','choix-multiples');
define('ITEM_3','classement');
define('ITEM_4','damier');
define('ITEM_5','developpement');
define('ITEM_6','dictee');
define('ITEM_7','marquage');
define('ITEM_8','mise-ordre');
define('ITEM_9','reponse-breve');
define('ITEM_10','reponses-multiples');
define('ITEM_11','texte-lacunaire');
define('ITEM_12','vrai-faux');
define('ITEM_13','zones-identifier');
define('ITEM_14','page');
define('ITEM_15','section');
define('ITEMS_TOTAL', 15);

// ** Configuration du support pour les alertes applicatives ** //
define('SUPPORT_ACTIF', 0);
define('SUPPORT_COURRIEL', 'soutien@ccdmd.qc.ca');

// ** URL pour récupérer les versions disponibles ** //
define('URL_NQW_VERSIONS_XML', 'http://version.ccdmd.qc.ca/nqw/nqw-versions.xml');

?>