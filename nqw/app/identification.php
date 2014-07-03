<?php

/** 
 * Aiguilleur : identification.php
 * 
 * Aiguillage des demandes d'identification et d'authentification
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {
	
	// Configuration et démarrage
	$aiguilleur = "identification";
	require_once 'init.php';
	$demandesPermises = array("authentification", "deconnexion", "mdp_modifier", "rappel", "profil", "profil_sauvegarder","mdp", "mdp_sauvegarder", "rappel_envoi", 
							  "compte", "demander_compte", "demander_compte_soumettre", "projet_selectionner");
	$log->debug("identification.php: Début");
	
	// Obtenir la demande
	$demande = Web::getParam('demande');
	
	// Vérifier demande
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {
	
		// Aiguillage de la demande
		$log->debug("identification.php:   Traitement de la demande '$demande'");
	
		// ----------------------------------------------------------------------------------------
		// Traiter une demande d'authentification
		// ----------------------------------------------------------------------------------------
		if ($demande == "authentification") {
						
			// Obtenir les paramètres d'identification
			$codeUtilisateur = Web::getParam('codeUtilisateur');
			$motPasse = Web::getParam('motPasse');
			$connexionActive = Web::getParam('connexionActive');
			
			// Effectuer l'authentification
			$auth = new Authentification(URL_IDENTIFICATION, $log, $dbh);
	
			if ( $auth->authentifier($codeUtilisateur, $motPasse) ) {
	
				// Authentification complétée
	
				// Vérifier si on émet un cookie pour le code utilisateur
				$cookie = "";
				if( $connexionActive == '1') {
					// Chiffrer le cookie avant l'envoi pour empêcher les modifications
					$log->debug("Obtenir le cookie");
					$cookie = Securite::encrypter(COOKIE_CODE_UTILISATEUR . $codeUtilisateur);
					$log->debug("cookie pour envoi... nom du cookie : '" . COOKIE_CODE_UTILISATEUR . "' valeur : '" . $cookie . "'");
				}
					
				// Transmettre le cookie (on vide le cookie si l'utilisateur)
				// n'a pas choisi de conserver la connexion active
				setcookie(COOKIE_CODE_UTILISATEUR, $cookie, time()+COOKIE_DUREE);
				
				// Mettre un cookie qui indique que l'utilisateur est authentifié
				setcookie(COOKIE_CONNEXION_ACTIVE, "1");
				
				// Déterminer l'URL pour la redirection
				$urlDest = URL_ACCUEIL;
				if ($session->get("urlDest") != "") {
					// Rediriger à l'URL demandé
					$urlDest = $session->get("urlDest");
					
					// Supprimer l'URL de la session
					$session->delete("urlDest");
				}
								
				// Redirect à la page d'acceuil de l'outil de gestion
				$log->debug("identification.php: Authentification complétée avec succès");
				header('Location: ' . $urlDest);
				
			} else {
				
				// Erreur d'authentification
				$log->debug("identification.php: Erreur d'authentification");
			}
		
		}
			
		// ----------------------------------------------------------------------------------------
		// Traiter une demande de déconnexion
		// ----------------------------------------------------------------------------------------		
		if ($demande == "deconnexion") {
			
			// Déconnexion 
			Authentification::deconnexion();
			
			// Vider la session
			$session->destroy();
			
			// Enlever le cookie d'identification
			setcookie(COOKIE_CONNEXION_ACTIVE,"0");
			
			// Obtenir les textes pour la langue d'interface
			$textes = new Texte($log, $dbh);
			$textes->getTexte($langueInterface->getLangue());
			
			// Message de confirmation
			$log->debug("identification.php: Déconnexion complétée");
			$messages = new Messages(ERR_007, Messages::CONFIRMATION);
					
			// Retour à la page d'identification
			include(REPERTOIRE_GABARITS . 'identification.php');
		}

		// ----------------------------------------------------------------------------------------
		// Traiter une demande de sauvegarde du mdp
		// ----------------------------------------------------------------------------------------		
		if ($demande == "mdp_sauvegarder") {
				
			// Obtenir les informations à partir de la requête
			$usager->getDonneesRequete();
			
			// Vérifier les champs mots de passe
			$erreurs = $usager->verifierChangementMDP();
			
			// Vérifier si des erreurs ont été détectées
			if ($erreurs == "") {
				// Enregistrer les informations d'une collection existante
				$usager->enregistrer();

				// Fermer la fenêtre
				include(REPERTOIRE_GABARITS . 'profil-modifier-mot-passe-fermer.php');
			} else { 
				$messages = new Messages($erreurs, Messages::ERREUR);

				// Retour à la page d'identification
				$demande = "mdp";
			}
		}		
		
		// ----------------------------------------------------------------------------------------
		// Traiter une demande de modification de mdp
		// ----------------------------------------------------------------------------------------		
		if ($demande == "mdp") {
			// Retour à la page d'identification
			include(REPERTOIRE_GABARITS . 'profil-modifier-mot-passe.php');
		}

		// ----------------------------------------------------------------------------------------
		// Traiter une demande de sauvegarde du profil
		// ----------------------------------------------------------------------------------------		
		if ($demande == "profil_sauvegarder") {
				
			// Obtenir les informations à partir de la requête
			$usager->getDonneesRequete();
			
			// Vérifier les champs du profil
			$erreurs = $usager->verifierProfil();
			
			// Vérifier si des erreurs ont été détectées
			if ($erreurs == "") {
				// Enregistrer les informations d'une collection existante
				$usager->enregistrer();

				// Fermer la fenêtre
				include(REPERTOIRE_GABARITS . 'profil-modifier-informations-fermer.php');
				
			} else { 
				$messages = new Messages($erreurs, Messages::ERREUR);

				// Retour à la page d'identification
				$demande = "profil";
			}
		}
		
		// ----------------------------------------------------------------------------------------
		// Traiter une demande de modification de profil
		// ----------------------------------------------------------------------------------------		
		if ($demande == "profil") {
			// Retour à la page d'identification
			include(REPERTOIRE_GABARITS . 'profil-modifier-informations.php');
		}

		// ----------------------------------------------------------------------------------------
		// Traiter une demande de rappel de mot de passe
		// ----------------------------------------------------------------------------------------		
		if ($demande == "rappel_envoi") {

			// Tenter de récupérer l'utilisateur par courriel
			$courriel = Web::getParam("courriel");
			$usager = new Usager($log, $dbh);
			
			if ($courriel == "") {
				$messages = new Messages(ERR_023, Messages::ERREUR);
			} else {
			
				if ($usager->getUsagerParCourriel($courriel)) {
					
					// Envoi d'un courriel
					if ($usager->envoiCourrielRappel()) {
						// Message succès
						$messages = new Messages(MSG_005, Messages::CONFIRMATION);
					} else {
						$messages = new Messages(ERR_029, Messages::ERREUR);
					}
				} else {
					$messages = new Messages(ERR_028, Messages::ERREUR);
				}
			}
		
			// Retour à la page d'identification
			include(REPERTOIRE_GABARITS . 'identification-rappel.php');
		}
				
		// ----------------------------------------------------------------------------------------
		// Afficher la page de rappel
		// ----------------------------------------------------------------------------------------		
		if ($demande == "rappel") {
			// Retour à la page d'identification
			include(REPERTOIRE_GABARITS . 'identification-rappel.php');
		}
		
		// ----------------------------------------------------------------------------------------
		// Traiter une demande de changement de mot de passe
		// ----------------------------------------------------------------------------------------		
		if ($demande == "mdp_modifier") {
			// Retour à la page d'identification
			include(REPERTOIRE_GABARITS . 'identification-modifier-mdp.php');
		}

		// ----------------------------------------------------------------------------------------
		// Demander un compte suite à une invitation - afficher le formulaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "compte") {
				
			// Objet par défaut
			$nouvUsager = new Usager($log, $dbh);
			
			// Vérifier que l'utilisateur n'est pas déjà en session, ce qui devrait être impossible car il demande un compte
			$codeUsager = $session->get('codeUsager');
			if ( $codeUsager != "") {
				Erreur::erreurFatal('133', "identification.php - Erreur l'utilisateur tente de créer un compte mais il est déjà en session avec le code utilisateur '$codeUsager'", $log);
			}
			
			// Obtenir la clé et la mettre en session
			$jeton = Web::getParam("cle");
			if ($jeton != "") {
				$session->set("collaborateur_jeton", $jeton);
			}
			
			// Vérifier si le projet est actif
			$prj = new Projet($log, $dbh);
			$statut = $prj->verifierCollaborateurInvitationParJeton($jeton);
			
			
			if ($statut == 0) {
				$messages = new Messages(ERR_201, Messages::AVERTISSEMENT);
			} elseif ($statut == 2) {
				$messages = new Messages(ERR_199, Messages::AVERTISSEMENT);
			}
			
			// Afficher le formulaire
			include(REPERTOIRE_GABARITS . 'identification-demander-compte.php');
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Demander un compte - afficher le formulaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "demander_compte") {
			
			// Objet par défaut
			$nouvUsager = new Usager($log, $dbh);
			
			// Afficher le formulaire
			include(REPERTOIRE_GABARITS . 'identification-demander-compte.php');
		}		
		
		// ----------------------------------------------------------------------------------------
		// Demander un compte - traiter la demande
		// ----------------------------------------------------------------------------------------
		if ($demande == "demander_compte_soumettre") {
			
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
				
				// Statut à approuver
				$nouvUsager->set("statut", "2");
				
				// Préférence nombre d'élément par page
				$nouvUsager->set("pref_nb_elem_page", NB_ELEMENT_PAR_PAGE);
				
				// Enregistrer les informations de l'usager
				$nouvUsager->ajouter();
				
				// Déterminer si on doit ajouter un rôle à l'utilisateur pour un projet
				$jeton = $session->get("collaborateur_jeton");

				if ($jeton != "") {
					
					// Traiter les demandes d'invitation
					$prj = new Projet($log, $dbh);
					$prj->traiterCollaborateurInvitationParJeton($nouvUsager->get("id_usager"),$jeton);
					
					// Supprimer le jeton de la session
					$session->delete("collaborateur_jeton");
				}			
								
				// Préparer le courriel
				$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "admin-approbation-compte.php";
				
				// Vérifier si le fichier existe, sinon erreur
				if (!file_exists($gabaritCourriel)) {
					$log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
				}
				
				// Obtenir le contenu
				$u = new Usager($log, $dbh); 
				$contenu = Fichiers::getContenuElement($gabaritCourriel , $u);				
				
				// Envoi d'un courriel à tous les administrateurs
				$listeAdmins = $u->getListeAdministrateurs();
				
				foreach ($listeAdmins as $admin) {
					$courriel = new Courriel($log);
					$succes = $courriel->envoiCourriel($admin->get("courriel"), TXT_DEMANDE_ACTIVATION_COMPTE_SUJET . " - " . ID_INSTALLATION, $contenu);
				}
								
				// Confirmer l'envoi de la demande
				include(REPERTOIRE_GABARITS . 'identification-demander-compte-conf.php');
			
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
				
				// Afficher le formulaire avec les erreurs
				include(REPERTOIRE_GABARITS . 'identification-demander-compte.php');
			}
			
		}

		
		// ----------------------------------------------------------------------------------------
		
		//                                      P R O J E T S
		
		// ----------------------------------------------------------------------------------------
		
		
		// ----------------------------------------------------------------------------------------
		// Projet - Sélectionner un projet
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_selectionner") {

			// Obtenir les informations du projet
			$nouvProjet = new Projet($log, $dbh);
				
			// Obtenir l'id du projet demandé
		 	$projSel = Web::getParam("id_projet");
		 	$log->debug("identification.php: projet_selectionner projSel = '" . $projSel . "'");
		 	
		 	// Vérifier si le projet est dans la liste
		 	$projetTrouve = false;
		 	foreach($listeProjetsActifs as $proj) {
		 		if ($proj->get("id_projet") == $projSel) {
		 			$projetTrouve = true;
		 		}
		 	}

		 	// Vérifier que le projet est localisé
		 	if ($projetTrouve) {

		 		// Régler le projet courant
		 		$nouvProjet->setProjetCourant($projSel);
		 		
		 		// Redirection vers la page d'accueil
		 		$url = URL_ACCUEIL;
		 		header("Location: $url");		 		
		 		
		 	} else {
		 		// Le projet n'est pas accessible
		 		Erreur::erreurFatal('184', "identification.php - Impossible de charger le projet '$projSel'", $log);
		 	}
	 	} 
		
	}
	 
	// Terminer
	$log->debug("identification.php: Fin");
	
} catch (Exception $e) {
	Erreur::erreurFatal('018', "identification.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}	
