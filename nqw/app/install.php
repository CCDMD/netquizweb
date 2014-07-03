<?php

/**
 * Aiguilleur : install.php
 *
 * Aiguillage des demandes d'installation
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {

	
	// Configuration et démarrage
	$aiguilleur = "install";

	// Empêcher l'affichage de la configuration en cas de glitch
	ob_start();
	
	require_once '../env.inc.php';
	require_once '../config.inc.php';
	require_once '../defaults.inc.php';
	
	
	// Charger les autres classes requises
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
	require_once '../ressources/classes/outils/Web.php';
	
	ob_end_clean();
	
	// Initialiser la session
	$session = new Session();
	
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
	
	// Charger la langue par défaut
	$langueInterface = new LangueInterface($log);
	$langueInterface->chargerLangue('');	
	
	$demandesPermises = array("installation_etape1", "installation_etape2", "installation_etape3");

	$log->debug("install.php: Début");

	// ----------------------------------------------------------------------------------------
	// Initialisation
	// ----------------------------------------------------------------------------------------
	$gabarit = "";
	$session = new Session();

	// ----------------------------------------------------------------------------------------
	// Obtenir la demande
	// ----------------------------------------------------------------------------------------
	$demande = Web::getParam('demande');
	if ($demande == "") {
		$demande = "installation_etape1";
	}

	$log->debug("install.php:   --------------------------- Aiguillage de la demande '$demande' ---------------------------");

	// Prendre note de la demande originale
	$demandeOrig = $demande;

	// ----------------------------------------------------------------------------------------
	// Préparer les informations pour la page
	// ----------------------------------------------------------------------------------------
	$pageInfos = array();
	
	// ----------------------------------------------------------------------------------------
	// Vérifier et traiter la demande
	// ----------------------------------------------------------------------------------------
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {

		$log->debug("install.php:   Traiter la demande '$demande'");
		$totErreurs = 0;
				
		// ----------------------------------------------------------------------------------------
		
		//                               V É R I F I C A T I O N S 
		
		// ----------------------------------------------------------------------------------------

		
		// ----------------------------------------------------------------------------------------
		// Vérifier les valeurs du fichier de configuration
		// ----------------------------------------------------------------------------------------
		$log->debug("install.php:   Vérifier les valeurs du fichier de configuration");
		$erreurs = "";
		if (DB_HOST == "") {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_220 . HTML_LISTE_ERREUR_FIN;
			$totErreurs++;
		}
		if (DB_NAME == "") {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_221 . HTML_LISTE_ERREUR_FIN;
			$totErreurs++;
		}
		if (DB_USER == "") {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_222 . HTML_LISTE_ERREUR_FIN;
			$totErreurs++;
		}
		if (DB_PASSWORD == "") {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_223 . HTML_LISTE_ERREUR_FIN;
			$totErreurs++;
		}
		if (EMAIL_FROM == "" || !filter_var(EMAIL_FROM, FILTER_VALIDATE_EMAIL)) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_224 . HTML_LISTE_ERREUR_FIN;
			$totErreurs++;
		}
		
		if ($totErreurs > 0) {
			$messages = new Messages($erreurs, Messages::ERREUR);
			$demande = "installation_etape1";
		}
					
		
		// ----------------------------------------------------------------------------------------
		// Vérifier la connexion à la BD
		// ----------------------------------------------------------------------------------------
		if ($totErreurs == 0) {
			$log->debug("init.php:   Tentative de connexion à la base de données '" . DB_NAME . "'" . " sur '" . DB_HOST . "'");
			$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';';
			$log->debug("init.php:   DSN '" . $dsn . "'");
			
			try {
				$dbh = new PDO($dsn, DB_USER, DB_PASSWORD );
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$dbh->exec('SET NAMES utf8');
					
				$log->debug("init.php:   Connexion à la base de données complétée avec succès");
			} catch (PDOException $e) {
				$log->erreur("init.php:   Erreur lors de la tentative de connexion à la base de données '" . DB_NAME . "'" . " sur '" . DB_HOST . "' : " . $e->getMessage());
				$messages = new Messages(ERR_001, Messages::ERREUR);
				$totErreurs++;
			}
		}
		
		// ----------------------------------------------------------------------------------------
		// Obtenir la liste des langues disponibles pour l'interface
		// ----------------------------------------------------------------------------------------
		$langueInterface = new LangueInterface($log);
		$listeLanguesInterface = $langueInterface->getListeLanguesInterfaces($log);
		
		// ----------------------------------------------------------------------------------------
		// Vérifier les répertoires qui doivent être accessible en écriture
		// ----------------------------------------------------------------------------------------
		// Vérifier le répertoire pour la publication des questionnaires (aperçu)
		if ($totErreurs == 0 && ! is_writable(REPERTOIRE_PUB)) {
			$messages = new Messages(ERR_216 . " " . REPERTOIRE_PUB, Messages::ERREUR);
			$totErreurs++;
		}

		// Vérifier le répertoire pour les médias
		if ($totErreurs == 0 && ! is_writable(REPERTOIRE_MEDIA)) {
			$messages = new Messages(ERR_217 . " " . REPERTOIRE_MEDIA, Messages::ERREUR);
			$totErreurs++;
		}
			
		// Vérifier le répertoire pour les journaux
		if ($totErreurs == 0 && ! is_writable(REPERTOIRE_JOURNAUX)) {
			$messages = new Messages(ERR_218 . " " . REPERTOIRE_JOURNAUX, Messages::ERREUR);
			$totErreurs++;
		}
	
		
		// ----------------------------------------------------------------------------------------
		// Détecter si c'est une installation ou mise à niveau
		// ----------------------------------------------------------------------------------------
		$type = "installation";
		if ($totErreurs == 0) {
			
			$maintenance = new Maintenance($log, $dbh);
			
			$nbTables = 0;
			$nbAdmins = 0;
			
			// Obtenir le nombre de tables
			$nbTables = $maintenance->getNombreTables();
				
			// Obtenir le nombre d'admin
			if ($nbTables > 0) {
				$nbAdmins = $maintenance->getNombreAdmin();
			}
			
			// Vérifier si une installation a été complétée et on est en mode mise à jour
			if ($nbTables > 0 && $nbAdmins > 0 && $maintenance->isMiseAJourEnAttente()) {
				$type = "maj";
				$demande = "mise-a-jour";
			}
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Détecter si l'installation a déjà eu lieu, si oui, passer à la dernière étape
		// ----------------------------------------------------------------------------------------
		if ($totErreurs == 0) {
			$maintenance = new Maintenance($log, $dbh);
			
			if ($maintenance->isInstallationComplete()) {
		 
				$log->debug("init.php:   L'installation est déjà complétée - passer à l'étape finale directement");
				$demande = "installation-complete";
			} else {
				$log->debug("init.php:   L'installation n'est pas complétée...");
			}
		}
		
		
		// ----------------------------------------------------------------------------------------
		
		//                               I N S T A L L A T I O N
		
		// ----------------------------------------------------------------------------------------
		
		
		// ----------------------------------------------------------------------------------------
		// Débuter l'installation - Validation technique et installation du schéma
		// ----------------------------------------------------------------------------------------
		if ($demande == "installation_etape1" || $totErreurs > 0) {
			
			// Vérifier et au besoin créer le schéma
			if ($totErreurs == 0) {
				
				$maintenance = new Maintenance($log, $dbh);
				
				// Vérifier si le schéma a déjà été chargé
				$log->debug("init.php:   Vérifier le nombre de tables dans la BD");
				$tablesTotal = $maintenance->getNombreTables();
				$log->debug("init.php:   Nombre de tables dans la BD : '$tablesTotal'");
				
				// Si le schéma n'est pas présent, le charger
				if ($tablesTotal == 0) {

					$fichierSQL = REPERTOIRE_SQL . INSTALLATION_FICHIER_SQL;
					
					// Vérifier que le fichier SQL existe
					$log->debug("init.php:   Vérifier le fichier SQL pour charger le schéma : '$fichierSQL'");
					if (file_exists($fichierSQL)) {
						
						$log->debug("init.php:   Le fichier existe, procéder au chargement initial du schéma");
						
						// Charger le schéma
						$maintenance->executerSQL($fichierSQL);
						
						// Vérifier le nombre de tables créées
						$log->debug("init.php:   Vérifier le nombre de tables dans la BD");
						$tablesTotal = $maintenance->getNombreTables();
						if ($tablesTotal == INSTALLATION_NOMBRE_TABLES) {

							// Nombre de tables conforme
							$log->debug("init.php:   Le nombre de tables dans la BD est conforme!");
							$messages = new Messages(MSG_034, Messages::CONFIRMATION);
							
							// Mettre à jour la version identifiée dans la BD
							$log->debug("init.php:   Récupérer la version de NQW à partir du fichier config : '" + VERSION_NQW + "'");
							$maintenance->ajouterVersionBD(VERSION_NQW);
							
							// Vérifier la version dans la BD
							if ($maintenance->isMiseAJourEnAttente()) {
								$log->erreur("init.php:   La version de la BD ('" . $version . "') ne correspond pas à la version du code PHP ('" + VERSION_NQW + "')");
								$messages = new Messages(ERR_219, Messages::ERREUR);
								$totErreurs++;
							}
 							
						} else {
							// Erreur nombre de tables incorrect
							$log->debug("init.php:   ERREUR Le schéma SQL ne contient pas le nombre de tables attendues. Réel : '$tablesTotal' Attendu : '" . INSTALLATION_NOMBRE_TABLES . "'");
							$messages = new Messages(ERR_215, Messages::ERREUR);
							$totErreurs++;
						}
						
					} else {
						
						// Erreur impossible de localiser le fichier SQL
						$log->debug("init.php:   ERREUR impossible de localiser le fichier avec le schéma SQL : '$fichierSQL'");
						$messages = new Messages(ERR_214, Messages::ERREUR);
						$totErreurs++;
					}
											
				} else {
					
					// Message de confirmation 
					$messages = new Messages(MSG_034, Messages::CONFIRMATION);
				}
				
				// Obtenir la liste des langues disponibles pour l'interface
				$langueInterface = new LangueInterface($log);
				$listeLanguesInterface = $langueInterface->getListeLanguesInterfaces($log);

			}			
			
			$gabarit = "installation-etape1.php";
		}
		

		// ----------------------------------------------------------------------------------------
		// Installation Étape 2 - Présenter le formulaire pour créer l'administrateur 
		// ----------------------------------------------------------------------------------------
		if ($demande == "installation_etape2") {
			$erreur = 0;
			$nouvUsager = new Usager($log, $dbh);
			
			// Vérifier si l'installation est déjà complétée, au cas ou une personne malveillante tente de créer un compte administrateur de cette manière
			if (!$maintenance->isInstallationComplete()) {
				$gabarit = "installation-etape2.php";
			} else {
				$gabarit = "installation-etape3.php";
			}
		
		}	

		// ----------------------------------------------------------------------------------------
		// Installation Étape 3 - Valider le code utilisateur et les champs
		// ----------------------------------------------------------------------------------------
		if ($demande == "installation_etape3") {
			
			$erreurs = "";
			
			// Obtenir les informations de la demande
			$nouvUsager = new Usager($log, $dbh);
			$nouvUsager->getDonneesRequete();
			
			// Générer grain de sel de l'usager avant le chiffrement des mots de passe
			$nouvUsager->set("gds_secret", Securite::creerGrainDeSel());
				
			// Vérifier les champs du profil
			$erreurs .= $nouvUsager->verifierProfil();
				
			if ($erreurs == "") {

				// Vérifier si le code utilisateur existe déjà
				$usagerTmp = new Usager($log, $dbh);
				$trouveCodeUsager = $usagerTmp->getUsagerParCodeUsager($nouvUsager->get("code_usager"));
				if ($trouveCodeUsager) {
					$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_177 . HTML_LISTE_ERREUR_FIN;
				}
			
				// Vérifier si le courriel existe déjà
				$trouveCourriel = $usagerTmp->getUsagerParCourriel($nouvUsager->get("courriel"));
				if ($trouveCourriel) {
					$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_178 . HTML_LISTE_ERREUR_FIN;
				}
			}
				
			// Vérifier les mots de passe
			$erreurs .= $nouvUsager->verifierChoixNouveauMDP();
				
			// Vérifier si des erreurs ont été détectées
			if ($erreurs == "") {
			
				// Valider que la langue passée en paramètre est disponible
				$lang = LANGUE_DEFAUT;
				
				if ($listeLanguesInterface[$nouvUsager->get("langue")] != "") {
					$lang = $nouvUsager->get("langue");
				}
				
				// Régler la langue d'interface et d'aperçu
				$langApercu = constant('LANGUE_ID_' . strtoupper($lang));
				
				$nouvUsager->set("langue_interface", $lang);
				$nouvUsager->set("pref_apercu_langue", $langApercu);
			
				// Statut actif
				$nouvUsager->set("statut", "0");
				
				// Rôle admin
				$nouvUsager->set("role", USAGER::ROLE_ADMINISTRATEUR);
			
				// Préférence nombre d'élément par page
				$nouvUsager->set("pref_nb_elem_page", NB_ELEMENT_PAR_PAGE);
			
				// Enregistrer les informations de l'usager
				$nouvUsager->ajouter();
				
				// Envoi d'un courriel de confirmation à l'admin (courriel du fichier config)
				$nouvUsager->envoiCourrielCreationCompteAdmin();
				
				$gabarit = "installation-etape3.php";
					
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
			
				// Afficher le formulaire avec les erreurs
				include(REPERTOIRE_GABARITS . 'installation-etape2.php');
			}			

		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Installation Complétée
		// ----------------------------------------------------------------------------------------
		if ($demande == "installation-complete") {

			$gabarit = "installation-complete.php";
		
		}		
		
		
		// ----------------------------------------------------------------------------------------
		
		//                               M I S E S   À   J O U R
		
		// ----------------------------------------------------------------------------------------
		
		
		// ----------------------------------------------------------------------------------------
		// Mise à jour de l'application
		// ----------------------------------------------------------------------------------------
		if ($demande == "mise-a-jour") {
			
			$erreur = 0;
				
			$nouvUsager = new Usager($log, $dbh);
				
			$log->debug("init.php:   Appliquer une mise à jour'");
				
			// Traiter une mise à jour au niveau de la BD
			$fichierSQL = REPERTOIRE_SQL . MAJ_PREFIX_FICHIER_SQL . VERSION_NQW . ".sql";
				
			$log->debug("init.php:   Traiter le fichier de mise à jour : '$fichierSQL'");
			$maintenance->executerSQL($fichierSQL);
				
			// Régler la nouvelle version dans la BD
			$log->debug("init.php:   Récupérer la version de NQW à partir du fichier config : '" . VERSION_NQW . "'");
			$maintenance->modifierVersionBD(VERSION_NQW);
				
			// Vérifier la version dans la BD
			if ($maintenance->isMiseAJourEnAttente()) {
				$log->erreur("init.php:   Une erreur est survenue durant la mise à jour de la version de la BD");
				$messages = new Messages(ERR_219, Messages::ERREUR);
				$totErreurs++;
			}
				
			$log->debug("init.php:   Mise à jour complétée'");
	
			$gabarit = "installation-mise-a-jour.php";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Traitement du gabarit
		// ----------------------------------------------------------------------------------------
		if ($gabarit != "") {
			include(REPERTOIRE_GABARITS . $gabarit);
		}


	} else {
		// ----------------------------------------------------------------------------------------
		// Erreur: la demande est incorrecte
		// ----------------------------------------------------------------------------------------
		Erreur::erreurFatal('006', "[install.php] Demande incorrecte : '$demande'", $log);
	}

	// Terminer
	$log->debug("install.php: Fin");

} catch (Exception $e) {
	Erreur::erreurFatal('018', "install.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}