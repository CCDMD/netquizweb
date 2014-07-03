<?php

/**
 * Aiguilleur : compte.php
 *
 * Aiguillage des demandes pour les comptes
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {

	// Configuration et démarrage
	$aiguilleur = "compte";
	require_once 'init.php';
		
	$demandesPermises = array("afficher_gabarit", "compte_profil", "compte_profil_sauvegarder", "compte_mdp", "compte_mdp_sauvegarder", "projets_liste", "projet_ajouter", "projet_ajouter_sauvegarder", 
							  "projet_modifier_ajouter_collaborateur", "projet_modifier_supprimer_collaborateur", "projet_terminer_collaboration",
							  "collaborateur_supprimer_acces", "collaborateur_supprimer_invitation", 
							  "projet_modifier", "projet_modifier_sauvegarder", "projet_corbeille", "corbeille", "corbeille_recuperer", "corbeille_supprimer", "projets_recherche", "projets_recherche_initialiser",
							  "corbeille_recherche", "corbeille_recherche_initialiser", "collaborateur_remplacer_responsable"
			
	);

	$log->debug("compte.php: Début");

	// ----------------------------------------------------------------------------------------
	// Initialisation
	// ----------------------------------------------------------------------------------------
	$gabarit = "";
	$listeThemes = "";
	$session = new Session();
	$corbeille = new Corbeille($log, $dbh);
	$listeCorbeille = array();	
	$demandeRetour = ""; // En cas de rafaîchissement demande à recharger

	// ----------------------------------------------------------------------------------------
	// Obtenir la demande
	// ----------------------------------------------------------------------------------------
	$demande = Web::getParam('demande');
	if ($demande == "") {
		$demande = "gabarit";
	}

	$log->debug("compte.php:   --------------------------- Aiguillage de la demande '$demande' ---------------------------");

	// Prendre note de la demande originale
	$demandeOrig = $demande;

	// ----------------------------------------------------------------------------------------
	// Préparer les informations pour la page
	// ----------------------------------------------------------------------------------------
	$pageInfos = array();
	$pageInfos['repertoire_projet'] = Securite::nettoyerNomfichier($projetActif->get("repertoire")) . "/";
	$pageInfos["flagModifications"] = "";
	$pageInfos["responsable"] = Web::getParam("responsable");

	// ----------------------------------------------------------------------------------------
	// Récupérer la chaîne de recherche
	// ----------------------------------------------------------------------------------------
	$chaineRech = $session->get("projet_recherche_chaine");
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la liste des projets
	// ----------------------------------------------------------------------------------------
	$projet = new Projet($log, $dbh);
	
	// ----------------------------------------------------------------------------------------
	// Vérifier et traiter la demande
	// ----------------------------------------------------------------------------------------
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {

		$log->debug("compte.php:   Traiter la demande '$demande'");


		// ----------------------------------------------------------------------------------------
		// Aperçu d'un gabarit
		// ----------------------------------------------------------------------------------------
		if ($demande == "afficher_gabarit") {
			
			$gabarit = Web::getParam("gabarit") . ".php";
		}

		
		// ----------------------------------------------------------------------------------------
		// Afficher le profil
		// ----------------------------------------------------------------------------------------
		if ($demande == "compte_profil") {

			// Obtenir la liste des langues de l'interface
			$listeLanguesInterface = LangueInterface::getListeLanguesInterfaces($log);
				
			// Obtenir la liste des langues de publication
			$lang = new Langue($log, $dbh);
			$listeLanguesPublication = $lang->getListeLangues($idProjetActif);
			
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
					
			$gabarit = "compte-profil.php";
		}

		// ----------------------------------------------------------------------------------------
		// Sauvegarder les informations du profil
		// ----------------------------------------------------------------------------------------
		if ($demande == "compte_profil_sauvegarder") {
			
			// Obtenir la liste des langues de l'interface
			$listeLanguesInterface = LangueInterface::getListeLanguesInterfaces($log);
				
			// Obtenir la liste des langues de publication
			$lang = new Langue($log, $dbh);
			$listeLanguesPublication = $lang->getListeLangues($idProjetActif);
			
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
			
			// Prendre en note le courriel original
			$courrielOrig = $usager->get("courriel");
			
			// Obtenir les données de la requête
			$usager->getDonneesRequete();

			// Vérifier les champs du profil
			$erreurs = $usager->verifierProfil();
			
			// Vérifier si le courriel a été modifié, si oui, qu'il n'est pas déjà utilisé
			if ($usager->get("courriel") != $courrielOrig) {
				$u = new Usager($log, $dbh);
				if ($u->getUsagerParCourriel($usager->get("courriel"))) {
					// Erreur le courriel existe déjà
					$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_178 . HTML_LISTE_ERREUR_FIN;
				}
			}
			
			// Vérifier la langue d'interface choisie
			if (! isset($listeLanguesInterface[$usager->get("langue_interface")]))  {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_190 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le thème pour les aperçus
			if (! in_array($usager->get("pref_apercu_theme"), $listeThemes) ) {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_191 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier la langue pour les aperçus
			if (! isset($listeLanguesPublication[$usager->get("pref_apercu_langue")]) ) {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_192 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier si des erreurs ont été détectées
			if ($erreurs == "") {
				// Enregistrer les informations d'une collection existante
				$usager->enregistrer();
				
				// Message de confirmation
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
			
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
			}
			
			$gabarit = "compte-profil.php";
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Afficher la page pour modifier le mot de passe
		// ----------------------------------------------------------------------------------------
		if ($demande == "compte_mdp") {
		
			$gabarit = "compte-mot-de-passe.php";
		}	

		// ----------------------------------------------------------------------------------------
		// Sauvegarder le mot de passe
		// ----------------------------------------------------------------------------------------
		if ($demande == "compte_mdp_sauvegarder") {
		
			// Obtenir les informations à partir de la requête
			$usager->getDonneesRequete();
			
			// Vérifier les champs mots de passe
			$erreurs = $usager->verifierChangementMDP();
			
			if ($erreurs == "") {
				// Enregistrer les informations d'une collection existante
				$usager->enregistrer();
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
					
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
			}
			
			$gabarit = "compte-mot-de-passe.php";
		}		

		
		// ----------------------------------------------------------------------------------------
		
		//                                   P R O J E T S 
		
		// ----------------------------------------------------------------------------------------		
		
		// ----------------------------------------------------------------------------------------
		// Ajouter un projet
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_ajouter") {
		
			// Préparer les valeurs initiales
			$projet->set("titre", TXT_NOUVEAU_PROJET);
			$projet->set("statut", Projet::STATUT_ACTIF);
			
			// Ajouter l'item
			$projet->ajouter();
			
			// Obtenir l'id du projet
			$idProjet = $projet->get("id_projet");
			
			// Ajouter le rôle responsable à l'utilisateur pour le projet
			$projet->ajouterRole($idUsager, Projet::ROLE_RESPONSABLE);
			
			// Obtenir la liste des projets pour l'utilisateur
			$listeProjetsActifs = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);
			

			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = array();
			
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = array();			
			
			$gabarit = "compte-projet-modifier.php";			
		}		
		

		// ----------------------------------------------------------------------------------------
		// Modifier un projet - Ajouter un collaborateur
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_modifier_ajouter_collaborateur") {

			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
			
			// Vérifier que la personne est reponsable pour le projet
			if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {
				Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
			}
			
			// Obtenir les données du projet
			$projet->getDonneesRequete();
			
			// Vérifier le flag de modifications passer en paramètre
			$flagModif = Web::getParamNum("flagModifications");
			if ($flagModif == "1") {
				$pageInfos["flagModifications"] = 1;
			}
			
			// Prendre en note le nom du demandeur
			$projet->set("responsable_invitation", $usager->get("prenom") . " " . $usager->get("nom"));
			
			// Obtenir le courriel de la personne
			$courriel = $projet->get("collaborateur_courriel");
			
			// Vérifier la validité du courriel et de la demande
			if (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
				$messages = new Messages(ERR_197, Messages::ERREUR);
			} elseif ($projet->isCollaborateurActuel($courriel)) {
				$messages = new Messages(ERR_194, Messages::ERREUR);
			} elseif ($projet->isCollaborateurInvite($courriel)) {
				$messages = new Messages(ERR_193, Messages::ERREUR);
			} else {
				
				// Si la personne a déjà un compte, lui ajouter un accès au projet et l'avertir par courriel
				$u = new Usager($log, $dbh);
				if ($u->getUsagerParCourriel($courriel)) {
					
					// Vérifier si la personne est déjà responsable...
					if ($projet->isRoleResponsableProjet($u->get("id_usager"), $idProjet)) {
						$messages = new Messages(ERR_195, Messages::ERREUR);
					} else {
							
						// Ajouter un rôle de collaborateur au projet
						$projet->ajouterRole($u->get("id_usager"), Projet::ROLE_COLLABORATEUR);
					
						// Envoi d'un courriel
						$projet->envoiCourrielInvitationCollaborateur($courriel);
	
						// Message de confirmation
						$messages = new Messages(MSG_017, Messages::CONFIRMATION);
					}
					
				} else {

					// Si la personne n'est pas collaborateur, l'ajouter comme collaborateur invité
					$projet->ajouterCollaborateur($courriel);

					// Message de confirmation
					$messages = new Messages(MSG_016, Messages::CONFIRMATION);
				}
					
				// Vider le champ
				$projet->set("collaborateur_courriel", "");
				
			}
			
			// Vérifier le projet
			$projet->verifierChampsProjet(false);
			
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();

			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
			
			// Ajouter le rôle responsable à l'utilisateur pour le projet
			$gabarit = "compte-projet-modifier.php";
				
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Supprimer les collaborateurs actuels
		// ----------------------------------------------------------------------------------------
		if ($demande == "collaborateur_supprimer_acces") {
							
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
			
			// Vérifier que la personne est reponsable pour le projet
			if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {
				Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
			}

			// Obtenir les données du projet
			$projet->getDonneesRequete();
			
			// Prendre en note le nom du responsable
			$projet->set("responsable_invitation", $usager->get("prenom") . " " . $usager->get("nom"));
				
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("collaborateurs_actuels_selection_");
		
			$nbElements = 0;
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$nbElements++;
					$projet->supprimerCollaborateur($element);
				}
			}
		
			// Message de confirmation
			if ($nbElements == 1) {
				$messages = new Messages(MSG_020, Messages::CONFIRMATION);
			} elseif ($nbElements > 1) {
				$messages = new Messages(MSG_021, Messages::CONFIRMATION);
			}
				
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();

			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
			
			// Ajouter le rôle responsable à l'utilisateur pour le projet
			$gabarit = "compte-projet-modifier.php";
		
		}
		
		// ----------------------------------------------------------------------------------------
		// Supprimer les collaborateurs invités
		// ----------------------------------------------------------------------------------------
		if ($demande == "collaborateur_supprimer_invitation") {
		
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
				
			// Vérifier que la personne est reponsable pour le projet
			if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {
				Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
			}
			
			// Obtenir les données du projet
			$projet->getDonneesRequete();
			
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("collaborateurs_invites_selection_");

			$nbElements = 0;
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$projet->supprimerCollaborateurInvitation($element);
					$nbElements++;
				}
			} 
				
					// Message de confirmation
			if ($nbElements == 1) {
				$messages = new Messages(MSG_018, Messages::CONFIRMATION);
			} elseif ($nbElements > 1) {
				$messages = new Messages(MSG_019, Messages::CONFIRMATION);
			}
					
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();
			
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
				
			// Ajouter le rôle responsable à l'utilisateur pour le projet
			$gabarit = "compte-projet-modifier.php";
		
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Supprimer les collaborateurs invités
		// ----------------------------------------------------------------------------------------
		if ($demande == "collaborateur_remplacer_responsable") {
			
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
			
			// Vérifier que la personne est reponsable pour le projet
			if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {
				Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
			}

			// Charger les infos du projet
			$projet->getProjetParId($idProjet);			
			
			// Prendre en note les champs qui requièrent d'informer les collaborateurs
			$projet->set("titreOrig", $projet->get("titre"));
			$projet->set("responsableOrig", $projet->getResponsableProjet());
			$projet->set("statutOrig", $projet->get("statut"));
			$projet->set("repertoireOrig", $projet->get("repertoire"));
						
			// Prendre en note le nom du demandeur
			$projet->set("responsable_invitation", $usager->get("prenom") . " " . $usager->get("nom"));
			
			// Obtenir les données du projet
			$projet->getDonneesRequete();
				
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("collaborateurs_actuels_selection_");
			
			$nbElements = 0;
			
			// Obtenir le nouvel utilisateur
			$idNouvResp = $listeElements[0];
			$u = new Usager($log, $dbh);
			$u->getUsagerParIdUsager($idNouvResp);
			
			// Vérifier le statut
			if ($u->get("statut") == "0") {

				// Statut valide, effectuer la modification
				$projet->remplacerResponsable($idUsager, $idNouvResp);
				// Confirmation
				$messages = new Messages(MSG_024, Messages::CONFIRMATION);
				
			} else {
				// Erreur, statut de l'utilisateur invalide
				$messages = new Messages(ERR_200, Messages::ERREUR);
			}
			
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();
			
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
			
			// Vérifier que l'utilisateur est un collaborateur
			if ($projet->isRoleResponsableProjet($idUsager, $idProjet)) {
				// Afficher le gabarit à nouveau
				$gabarit = "compte-projet-modifier.php";
			} else {
				// Afficher en consultation seulement
				$gabarit = "compte-projet-afficher.php";
			}
					
			
		}
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder un projet après modification
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_modifier_sauvegarder") {
		
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
				
			// Vérifier que la personne est reponsable pour le projet
			if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {
				Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
			}
			
			// Charger le projet
			$projet->getProjetParId($idProjet);
				
			// Prendre en note les champs qui requièrent d'informer les collaborateurs
			$projet->set("titreOrig", $projet->get("titre"));
			$projet->set("responsableOrig", $projet->getResponsableProjet());
			$projet->set("statutOrig", $projet->get("statut"));
			$projet->set("repertoireOrig", $projet->get("repertoire"));
			
			// Vider le checkbox
			$projet->set("notification", "");
			
			// Obtenir les informations à partir de la requête
			$projet->getDonneesRequete();
			
			// Vérifier les champs
			$verifierRepertoire = false;

			if ($projet->get("repertoireOrig") == "") {
				$verifierRepertoire = true;
			}
			$erreurs = $projet->verifierChampsProjet($verifierRepertoire);
					
			if ($erreurs == "") {
				// Enregistrer les informations du projet
				$projet->enregistrer();
				
				// Informer les collaborateurs des modifications
				$projet->envoiCourrielCollaborateursModifications();
				
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
					
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
			}
		
			// Obtenir la liste des projets pour l'utilisateur
			$listeProjetsActifs = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);
				
			// Détecter la situation ou aucun projet
			$idProjetActif = $projetActif->getIdProjetCourant($usager);
		
			// Charger le projet
			if ($idProjetActif != "0") {
				$log->debug("compte.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
			}
		
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();
			
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
			
			// Vérifier que le projet est actif
			if ($projet->get("statut") == "1") {
			
				// Le projet est actif et peut être modifié
				$gabarit = "compte-projet-modifier.php";
					
			} else {
					
				// Le projet est inactif et ne peut être modifié (sauf le statut)
				$gabarit = "compte-projet-modifier-inactif.php";
			}
			
		}		
		
		// ----------------------------------------------------------------------------------------
		// Modifier un projet
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_modifier") {
		
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
			
			// Vérifier si l'id du projet est disponible via un changement de page
			$page = Web::getParamNum('projet_page');
			
			if ($idProjet == "" && $page != "") {
				$idProjet = $projet->getIdProjetParPage($page);
			}

			// Charger les infos du projet
			$projet->getProjetParId($idProjet);
				
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();
			
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
			
			// Vérifier que la personne est reponsable pour le projet
			if ( $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {

				// Vérifier que le projet est actif
				if ($projet->get("statut") == "1") {

					// Le projet est actif et peut être modifié
					$gabarit = "compte-projet-modifier.php";
					
				} else { 
									
					// Le projet est inactif et ne peut être modifié (sauf le statut)
					$gabarit = "compte-projet-modifier-inactif.php";
				}
				
			} else {	

				// Vérifier que l'utilisateur est un collaborateur
				if ($projet->isRoleCollaborateurProjet($idProjet, $listeIDProjetsActifs)) {
					// Afficher en consultation seulement
					$gabarit = "compte-projet-afficher.php";
		
				} else {
					// Erreur aucun accès
					Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
				}
			}
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_PROJET . $projet->get("id_projet"), '');
				
			
		}
		
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille un ou plusieurs projets
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_corbeille_liste" || $demande == "projet_corbeille") {
		
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("projets_selection_");
			if (! empty($listeElements) ) {
				
				$totalProbPermission = 0;
				
				foreach ($listeElements as $element) {
					
					// Vérifier que la personne est reponsable pour le projet
					if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $element)) {
						$totalProbPermission++;
					} else {

						// Mettre le projet à la corbeille
						$prj = new Projet($log, $dbh);
						$prj->getProjetParId($element);
						$prj->desactiver();
					}
				}
				
				
				// Déterminer le message à afficher
				if ($totalProbPermission == 0) {
					$messages = new Messages(MSG_022, Messages::CONFIRMATION);
				} else {
					$messages = new Messages(MSG_023, Messages::CONFIRMATION);
				}
				
			} else {
				// Obtenir l'id du projet
				$idProjet = Web::getParamNum("projet_id_projet");
				
				// Vérifier que la personne est responsable pour le projet
				if (! $projet->isRoleResponsableProjet($usager->get("id_usager"), $idProjet)) {
					Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de responsable requis pour modifier le projet '$idProjet'", $log);
				}
				
				// Charger le projet
				$projet->getProjetParId($idProjet);
				
				// Mettre à la corbeille le projet sélectionné
				$projet->desactiver();
			}

			// Obtenir la liste des projets pour l'utilisateur
			$listeProjetsActifs = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);

			// Détecter la situation ou aucun projet
			$idProjetActif = $projetActif->getIdProjetCourant($usager);
			
			// Charger le projet
			if ($idProjetActif != "0") {
				$log->debug("compte.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
				
				// Réafficher la liste des projets
				$demande = "projets_liste";
				
			} else {
				// Aucun projet actif, redirection pour créer un nouveau projet
				$log->debug("compte.php:   Aucun projet, redirection vers la page pour créer un projet");
			
				// Présenter l'écran pour créer un projet
				$nouvProjet = new Projet($log, $dbh);
				include(REPERTOIRE_GABARITS . 'identification-nouveau-projet.php');
			}
			
		}		
				
		
		// ----------------------------------------------------------------------------------------
		// Terminer la collaboration au projet
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_terminer_collaboration") {
		
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("projets_selection_");
			
			if (! empty($listeElements) ) {
		
				$totalProbPermission = 0;
		
				foreach ($listeElements as $element) {
						
					// Vérifier que la personne est collaborateur pour le projet
					if ($projet->isRoleCollaborateurProjet($element, $listeIDProjetsActifs)) {
						// Terminer la collaboration
						$prj = new Projet($log, $dbh);

						// Prendre en note le nom du demandeur
						$prj->set("responsable_invitation", $usager->get("prenom") . " " . $usager->get("nom"));
						
						$prj->getProjetParId($element);
						$prj->supprimerCollaborateur($idUsager);
					} else {
						$totalProbPermission++;
					}
				}
				
				// Déterminer le message à afficher
				if ($totalProbPermission == 0) {
					$messages = new Messages(MSG_027, Messages::CONFIRMATION);
				} else {
					$messages = new Messages(MSG_028, Messages::CONFIRMATION);
				}
		
			} else {
				// Obtenir l'id du projet
				$idProjet = Web::getParamNum("projet_id_projet");
		
				// Vérifier que la personne est collaborateur pour le projet
				if (! $projet->isRoleCollaborateurProjet($idProjet, $listeIDProjetsActifs)) {
					Erreur::erreurFatal('196', "compte.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de collaborateur requis pour se retirer de la liste de collaboreateurs du projet '$idProjet'", $log);
				}
		
				// Charger le projet
				$prj = new Projet($log, $dbh);
				$prj->getProjetParId($idProjet);
		
				// Retirer la personne du projet
				$prj->supprimerCollaborateur($idUsager);
			}
		
			// Obtenir la liste des projets pour l'utilisateur
			$listeProjetsActifs = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);
		
			// Détecter la situation ou aucun projet
			$idProjetActif = $projetActif->getIdProjetCourant($usager);
				
			// Charger le projet
			if ($idProjetActif != "0") {
				$log->debug("compte.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
		
				// Réafficher la liste des items
				$demande = "projets_liste";
		
			} else {
				// Aucun projet actif, redirection pour créer un nouveau projet
				$log->debug("compte.php:   Aucun projet, redirection vers la page pour créer un projet");
					
				// Présenter l'écran pour créer un projet
				$nouvProjet = new Projet($log, $dbh);
				include(REPERTOIRE_GABARITS . 'identification-nouveau-projet.php');
			}
				
		}		
		
		
		
		
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche des projets
		// ----------------------------------------------------------------------------------------
		if ($demande == "projets_recherche_initialiser") {
				
			// Mettre en session
			$session->set("projets_recherche_chaine", "");
				
			// Afficher la liste des questionnaires correspondants
			$demande = "projets_liste";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les projets
		// ----------------------------------------------------------------------------------------
		if ($demande == "projets_recherche") {
				
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
				
			// Mettre en session
			$session->set("projets_recherche_chaine", $chaine);
				
			// Afficher la liste des projets correspondants
			$demande = "projets_liste";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Liste des projets
		// ----------------------------------------------------------------------------------------
		if ($demande == "projets_liste") {
				
			$listeProj = array();
				
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "liste") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
				
			// Obtenir le tri à utiliser
			$tri = $projet->getTri();
				
			// Obtenir le filtre à utiliser
			$filtreResponsable = $projet->getFiltreResponsable();
			
			// Déterminer si on affiche la liste des projets
			if ($session->get("projets_recherche_chaine") == "") {
				$listeIdProjets = $projet->getListeProjets($idUsager, $tri, true, $filtreResponsable);
			} else {
				$chaine = $session->get("projets_recherche_chaine");
				
				// Rechercher les projets
				$listeIdProjets = $projet->recherche($chaine, $tri, Projet::STATUT_ACTIF . ", " . Projet::STATUT_INACTIF, $idUsager, $filtreResponsable, false);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdProjets, $usager, $log, $dbh);
				
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
						
					$idProj = $listeIdProjets[$i];
			
					// Charger le projet
					$p = new Projet($log, $dbh);
					$p->getProjetParId($idProj);
						
					// Ajouter aux résultats de recherche
					array_push($listeProj, $p);
				}
			}

			// Préparer l'affichage de la page
			$projet->preparerAffichageListe();
				
			// Obtenir la liste de tous les projets visibles
			$listeIdProjetsTous = $projet->getListeProjets($idUsager, $tri, false, $filtreResponsable);
		
			// Obtenir la liste des reponsables pour tous les projets visibles
			$listeResponsables = $projet->getListeResponsablesProjetsPourProjetsSpecifiques($listeIdProjetsTous);

			$gabarit = "compte-projets-liste.php";
		}		
		
		// ----------------------------------------------------------------------------------------
		
		//                                 C O R B E I L L E
		
		// ----------------------------------------------------------------------------------------
		
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans la corbeille
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille_recherche") {
				
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
				
			// Mettre en session
			$session->set("corbeille_recherche_chaine", $chaine);
				
			// Afficher la liste des éléments correspondants
			$demande = "corbeille";
		}
		
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche dans la corbeille
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille_recherche_initialiser") {
				
			// Mettre en session
			$session->set("corbeille_recherche_chaine", "");
				
			// Afficher la liste des questionnaires correspondants
			$demande = "corbeille";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Corbeille récupérer des éléments
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille_recuperer") {
		
			// Obtenir le(s) formulaire(s) à récupérer
			$listeElements = Web::getListeParam("elements_selection_");
			$corbeille->recupererListeElements($listeElements, $idProjetActif);
			
			// Obtenir la liste des projets pour l'utilisateur
			$listeProjetsActifs = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);
			
			// Détecter la situation ou aucun projet
			$idProjetActif = $projetActif->getIdProjetCourant($usager);
				
			// Charger le projet
			if ($idProjetActif != "0") {
				$log->debug("compte.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
			}			
		
			// Réafficher la liste des questionnaires
			$demande = "corbeille";
		}
		
		// ----------------------------------------------------------------------------------------
		// Corbeille supprimer des éléments
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille_supprimer") {
		
			// Obtenir l'éléments à supprimer
			$listeElements = Web::getListeParam("elements_selection_");
			$corbeille->supprimerListeElements($listeElements, $idProjetActif);
		
			// Réafficher la liste des questionnaires
			$demande = "corbeille";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Afficher la corbeille
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille") {
		
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "corbeille") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
		
			// Déterminer si on affiche la liste des éléments ou un résultat de recherche
			if ($session->get("corbeille_recherche_chaine") == "") {
				$listeElements = $corbeille->getListeElements($idProjetActif);
			} else {
				$chaine = $session->get("corbeille_recherche_chaine");
				$listeElements = $corbeille->recherche($chaine, $idProjetActif, $idUsager);
			}
		
			// Appliquer la pagination
			$pagination = new Pagination($listeElements, $usager, $log, $dbh);
		
			// Obtenir la liste des éléments sous forme d'objets
			$listeCorbeille = $corbeille->getListeElementsObjets($listeElements, $projetActif, $pagination);
		
			// Préparer les valeurs pour affichage web
			$corbeille->preparerAffichageWeb();
		
			// Régler le gabarit à utiliser
			$gabarit = "compte-corbeille.php";
		}		

		// ----------------------------------------------------------------------------------------
		// Préparer les chaînes de recherche
		// ----------------------------------------------------------------------------------------
		$chaineRechCorbeille = $session->get("corbeille_recherche_chaine");
		$chaineRechProjets = $session->get("projets_recherche_chaine");
		
		// ----------------------------------------------------------------------------------------
		// Préparer affichage
		// ----------------------------------------------------------------------------------------
		$projet->preparerAffichage();
		$pageInfos["responsable"] = $session->get("pref_filtre_responsable");
		
		// ----------------------------------------------------------------------------------------
		// Traitement du gabarit
		// ----------------------------------------------------------------------------------------
		if ($gabarit != "") {
			include(REPERTOIRE_GABARITS . $gabarit);
		}
	}

	// Terminer
	$log->debug("compte.php: Fin");

} catch (Exception $e) {
	Erreur::erreurFatal('018', "compte.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}