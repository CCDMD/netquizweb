<?php

/** 
 * init.php
 * 
 * Démarrage et mise en place de l'environnement de l'application
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

// Empêcher l'affichage de la configuration en cas de glitch

ob_start();
require_once '../env.inc.php';
require_once '../config.inc.php';
require_once '../defaults.inc.php';


// Charger les autres classes requises
require_once '../ressources/classes/modeles/Categorie.php';
require_once '../ressources/classes/modeles/Collection.php';
require_once '../ressources/classes/modeles/Corbeille.php';
require_once '../ressources/classes/modeles/Element.php';
require_once '../ressources/classes/modeles/Lacune.php';
require_once '../ressources/classes/modeles/LacuneReponse.php';
require_once '../ressources/classes/modeles/Langue.php';
require_once '../ressources/classes/modeles/Marque.php';
require_once '../ressources/classes/modeles/MarqueRetro.php';
require_once '../ressources/classes/modeles/Media.php';
require_once '../ressources/classes/modeles/Menu.php';
require_once '../ressources/classes/modeles/MenuItem.php';
require_once '../ressources/classes/modeles/Projet.php';
require_once '../ressources/classes/modeles/Questionnaire.php';
require_once '../ressources/classes/modeles/Terme.php';
require_once '../ressources/classes/modeles/TermePublication.php';
require_once '../ressources/classes/modeles/Texte.php';
require_once '../ressources/classes/modeles/Theme.php';
require_once '../ressources/classes/modeles/items/Item.php';
require_once '../ressources/classes/modeles/items/ItemAssociations.php';
require_once '../ressources/classes/modeles/items/ItemChoixMultiples.php';
require_once '../ressources/classes/modeles/items/ItemClassement.php';
require_once '../ressources/classes/modeles/items/ItemDamier.php';
require_once '../ressources/classes/modeles/items/ItemDeveloppement.php';
require_once '../ressources/classes/modeles/items/ItemDictee.php';
require_once '../ressources/classes/modeles/items/ItemMarquage.php';
require_once '../ressources/classes/modeles/items/ItemMiseOrdre.php';
require_once '../ressources/classes/modeles/items/ItemPage.php';
require_once '../ressources/classes/modeles/items/ItemReponseBreve.php';
require_once '../ressources/classes/modeles/items/ItemReponsesMultiples.php';
require_once '../ressources/classes/modeles/items/ItemSection.php';
require_once '../ressources/classes/modeles/items/ItemTexteLacunaire.php';
require_once '../ressources/classes/modeles/items/ItemVraiFaux.php';
require_once '../ressources/classes/modeles/items/ItemZonesIdentifier.php';
require_once '../ressources/classes/outils/Authentification.php';
require_once '../ressources/classes/outils/Courriel.php';
require_once '../ressources/classes/outils/Erreur.php';
require_once '../ressources/classes/outils/Fichiers.php';
require_once '../ressources/classes/outils/Importation.php';
require_once '../ressources/classes/outils/LangueInterface.php';
require_once '../ressources/classes/outils/Log.php';
require_once '../ressources/classes/outils/Maintenance.php';
require_once '../ressources/classes/outils/Messages.php';
require_once '../ressources/classes/outils/Pagination.php';
require_once '../ressources/classes/outils/Publication.php';
require_once '../ressources/classes/outils/Securite.php';
require_once '../ressources/classes/outils/Session.php';
require_once '../ressources/classes/outils/Verrou.php';
require_once '../ressources/classes/outils/Web.php';

ob_end_clean();

$idProjetActif = "";

// Initialiser la session
$session = new Session();

// Vérifier si la session est active
$session->verifierSessionActive();

// Mettre à jour la dernière visite pour timeout
if (Web::getParam("demande") != "session_verifier" &&
	Web::getParam("demande") != "session_message") {
	// Mettre à jour la dernière visite
	$session->setDerniereVisite();	
}

// Régler le timezone
date_default_timezone_set(TIMEZONE_DEFAUT);

// Vérifier que le répertoire de base existe, sinon impossible de démarrer
if (!is_dir(REPERTOIRE_BASE)) {
	Erreur::erreurInit();
}

// Démarrage de la journalisation
$aujourdhui = date( "Y-m-d" );
$logFN = JOURNALISATION_FICHIER . "-" . $aujourdhui . ".log"; 
$log = new Log($logFN, JOURNALISATION_NIVEAU);
$log->debug("init.php: Début");

// Connexion à la base de données
$log->debug("init.php:   Tentative de connexion à la base de données '" . DB_NAME . "'" . " sur '" . DB_HOST . "'");
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';';
$log->debug("init.php:   DSN '" . $dsn . "'");
try {
	//$dbh = new PDO($dsn, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
	$dbh = new PDO($dsn, DB_USER, DB_PASSWORD );
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->exec('SET NAMES utf8');
	
	$log->debug("init.php:   Connexion à la base de données complétée avec succès");
} catch (PDOException $e) {
	$log->debug("init.php:   Impossible de connecter à la base de données '" . DB_NAME . "'");
	Erreur::erreurFatal("001", $e->getMessage(), $log);
}

// Vérifier si l'installation est complétée
$maintenance = new Maintenance($log, $dbh);
if (!$maintenance->isInstallationComplete()) {
	Erreur::erreurFatal("226", "L'installation n'a pas été complétée correctement ou il y a un problème avec le contenu de la base de données ou aucun administrateur actif ne peut être localisé", $log);
}

// Obtenir la liste des langues disponibles pour l'interface
$langueInterface = new LangueInterface($log);
$listeLanguesInterface = $langueInterface->getListeLanguesInterfaces($log);

// Vérifier le code utilisateur du cookie
$codeUtilisateurRappel = Securite::getCodeUtilisateurDuCookie();

// Authentification de l'utilisateur (sauf s'il tente de se connecter ou de récupérer son mot de passe etc.)
if (Web::getParam("demande") != "authentification" &&
	Web::getParam("demande") != "deconnexion" && 
	Web::getParam("demande") != "rappel" && 
	Web::getParam("demande") != "rappel_envoi" &&
	Web::getParam("demande") != "mdp" &&
	Web::getParam("demande") != "mdp_enregistrer" &&
	Web::getParam("demande") != "demander_compte" &&
	Web::getParam("demande") != "compte" &&
	Web::getParam("demande") != "demander_compte_soumettre" &&
	Web::getParam("demande") != "aide_apropos_intro" &&
	Web::getParam("demande") != "aide_apropos_droits" &&
	Web::getParam("demande") != "aide_apropos_generique" &&
	Web::getParam("demande") != "aide_apropos_commentaires" &&
	Web::getParam("demande") != "session_verifier" &&
	Web::getParam("demande") != "session_message" &&
	$aiguilleur != "install"
) {

	// Vérifier que l'utilisateur est authentifié au niveau de la session
	$auth = new Authentification(URL_IDENTIFICATION, $log, $dbh);
	if ( !$auth->verifierAuthentification() ) {

		// Charger la langue par défaut
		$langueInterface->chargerLangue('');
		
		// Obtenir les textes pour la langue d'interface
		$textes = new Texte($log, $dbh);
		$textes->getTexte($langueInterface->getLangue());
		
		// Vérifier si une connexion était active, si oui, message de timeout
		$connActive = Web::getCookie(COOKIE_CONNEXION_ACTIVE);
		
		if ($connActive == "1") {
			$log->debug("init.php:   Timeout de session détecté");
			$messages = new Messages(ERR_131, Messages::ERREUR);
			setcookie(COOKIE_CONNEXION_ACTIVE,"0");
		} else {
			// Conserver la demande URL originale pour redirection seulement si c'est une demande directe (pas timeout)
			$urlDest = $_SERVER['REQUEST_URI'];
			$session->set("urlDest", $urlDest);
		}
		
		include(REPERTOIRE_GABARITS . 'identification.php');
		exit;
	} else {

		// Obtenir les informations sur l'usager
		$codeUsagerAuth = $auth->verifierAuthentification();
		
		$usager = new Usager($log, $dbh);
		$usager->getUsagerParCodeUsager($codeUsagerAuth);

		// Vérifier le idUsager - si indéterminé, terminer la session
		if ($usager->get("id_usager") == "") {
			$auth->deconnexion();
			include(REPERTOIRE_GABARITS . 'identification.php');
			exit;
		}
		
		// Régler la langue en session sur celle du profil
		$session->set("langue", $usager->get("langue_interface"));

		// Chargement de la langue d'interface pour l'utilisateur
		$langueInterface->chargerLangue($usager);
				
		// Raccourci
		$idUsager = $usager->getIdUsager();
		
		// Vérifier si l'application est ouverte pour les utilisateurs
		if (!$maintenance->isApplicationDisponible() && Web::getParam("demande") != "outils_acces_ouvrir") {

			// Obtenir les textes pour la langue d'interface
			$textes = new Texte($log, $dbh);
			$textes->getTexte($langueInterface->getLangue());
			
			if (!$usager->isAdmin()) {
				// L'application est fermée, ne pas permettre l'accès utilisateur
				$messages = new Messages(MSG_046, Messages::AVERTISSEMENT);
				include(REPERTOIRE_GABARITS . 'identification.php');
				exit;
			} else {
				$messages = new Messages(MSG_047, Messages::AVERTISSEMENT);
			}
			 
		} 
		
		// Obtenir la liste des projets pour l'utilisateur
		$listeProjetsActifs = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);
		
		// Préparer la liste des id des projets actifs
		$listeIDProjetsActifs = array();
		foreach ($listeProjetsActifs as $p) {
			array_push($listeIDProjetsActifs, $p->get("id_projet"));
		}

		// Détecter la situation ou aucun projet
		$projetActif = new Projet($log, $dbh);
		$idProjetActif = $projetActif->getIdProjetCourant($usager);
		$log->debug("init.php:   Projet '$idProjetActif' sélectionné");
		
		if (Web::getParam("demande") != "projet_creer" && Web::getParam("demande") != "deconnexion") {
			if ($idProjetActif == "0") {
				$log->debug("init.php:   Aucun projet, redirection vers la page pour créer un projet");
				
				// Présenter l'écran pour créer un projet
				$nouvProjet = new Projet($log, $dbh);
				include(REPERTOIRE_GABARITS . 'identification-nouveau-projet.php');
				exit;
			} else {
				$log->debug("init.php:   Un projet est disponible");
			}
		}
		
		// Charger le projet
		if ($idProjetActif != "0") {
			$log->debug("init.php:   Chargement du projet '$idProjetActif'");
			$projetActif->getProjetParId($idProjetActif);
			
			// Vérifier le projet
			$projetActif->verifierProjet();
			
			// Ajouter le projet à la session
			$session->set("projetActif", $idProjetActif . " - " . $projetActif->get("titre"));
			$projetActif->setProjetCourant($idProjetActif);
				
		}
		
		// Supprimer les fichiers temporaires de publication au besoin
		if (Web::getParam("demande") != "theme_apercu" && Web::getParam("demande") != "media_afficher") {
			$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" . REPERTOIRE_PREFIX_APERCU;
			$log->debug("init.php:   Au besoin, supprimer les fichiers temporaires dans le répertoire '" . $repertoireDestinationUsager . "'");
			
			// Nettoyer les fichiers temporaires
			$pub = new Publication($log, $dbh);
			$pub->nettoyerRepertoireUsager($repertoireDestinationUsager);

			// Nettoyer les verrous expirés
			$verrou = new Verrou($log, $dbh);
			$verrou->nettoyage();
		}
		
		// Vérifier si une mise à jour doit être appliquée lorsqu'un administrateur se connecte - afficher le message une seule fois par session
		$messageMAJ = $session->get("messageMAJ");
		if ($usager->isAdmin() && $messageMAJ == "" && $maintenance->isApplicationDisponible()) {
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
			if ($msg != "") {
				$messages = new Messages(constant($msg), Messages::AVERTISSEMENT);
			}
			
			
			// Vérifier que le répertoire ressources est protégé en lecture du web
			$log->debug("install.php:   Vérifier que le répertoire ressources est protégé en lecture du web");
			$urlTest = URL_DOMAINE . URL_BASE . URL_SQL_INSTALLATION;
			
			$file_headers = @get_headers($urlTest);
			if($file_headers[0] == 'HTTP/1.1 403 Forbidden') {
				$log->debug("init.php:   Le répertoire ressources est protégé adéquatement");
			} else {
				$log->debug("init.php:   Le répertoire ressources n'est pas protégé adéquatement");
				$messages = new Messages(ERR_225, Messages::ERREUR);
			}
			
			$session->set("messageMAJ", "1");
		}
	}
} else {
	
	// Charger la langue par défaut pour l'authentification ou le rappel
	$langueInterface->chargerLangue('');
}


// Terminer
$log->debug("init.php: Fin");
?>