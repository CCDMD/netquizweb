<?php

/**
 * Aiguilleur : admin.php
 *
 * Aiguillage des demandes de gestion
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {

	
	// Configuration et démarrage
	$aiguilleur = "admin";
	require_once 'init.php';

	$demandesPermises = array("projets_liste", "projet_ajouter", "projet_ajouter_sauvegarder", "projets_recherche", "projets_recherche_initialiser",
							  "projet_modifier_ajouter_collaborateur", "projet_modifier_supprimer_collaborateur", "projet_terminer_collaboration", "collaborateur_remplacer_responsable",
							  "collaborateur_supprimer_acces", "collaborateur_supprimer_invitation",
							  "corbeille", "corbeille_recuperer", "corbeille_supprimer", "corbeille_recherche", "corbeille_recherche_initialiser",
							  "utilisateurs_liste", "utilisateur_modifier", "utilisateur_modifier_sauvegarder", "utilisateur_corbeille", "utilisateurs_recherche", 
							  "utilisateurs_recherche_initialiser", "utilisateur_ajouter", "utilisateurs_approuver",
							  "projet_modifier", "projet_modifier_sauvegarder", "projet_corbeille",
							  "textes_modifier", "textes_sauvegarder", "outils", "outils_indexation", "outils_indexation_categories", "outils_indexation_collections",
							  "outils_indexation_items", "outils_indexation_langues", "outils_indexation_medias", "outils_indexation_projets", "outils_indexation_questionnaires",
							  "outils_acces_ouvrir", "outils_acces_fermer", "outils_indexation_utilisateurs", "outils_sauvegarde", "approbation");

	$log->debug("admin.php: Début");

	// ----------------------------------------------------------------------------------------
	// Initialisation
	// ----------------------------------------------------------------------------------------
	$gabarit = "";
	$listeThemes = "";
	$listeLanguesPublication = "";
	$session = new Session();
	$corbeille = new Corbeille($log, $dbh);
	$listeCorbeille = array();
	$listeUsagers = array();
	$usr = new Usager($log, $dbh);
	$demandeRetour = ""; // En cas de rafaîchissement demande à recharger
	$listeProjetsActifsUtilisateur = array();
	$filtreStatut = "";
	$langTexteSel = "";

	// ----------------------------------------------------------------------------------------
	// Obtenir la demande
	// ----------------------------------------------------------------------------------------
	$demande = Web::getParam('demande');
	if ($demande == "") {
		$demande = "utilisateurs_liste";
	}

	$log->debug("admin.php:   --------------------------- Aiguillage de la demande '$demande' ---------------------------");

	// Prendre note de la demande originale
	$demandeOrig = $demande;

	// ----------------------------------------------------------------------------------------
	// Préparer les informations pour la page
	// ----------------------------------------------------------------------------------------
	$pageInfos = array();
	$pageInfos['repertoire_projet'] = Securite::nettoyerNomfichier($projetActif->get("repertoire")) . "/";
	$pageInfos["flagModifications"] = "";
		
	// ----------------------------------------------------------------------------------------
	// Récupérer la chaîne de recherche
	// ----------------------------------------------------------------------------------------
	$chaineRech = $session->get("projet_recherche_chaine");
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la liste des projets
	// ----------------------------------------------------------------------------------------
	$projet = new Projet($log, $dbh);

	// ----------------------------------------------------------------------------------------
	// Vérifier que l'utilisateur est admin
	// ----------------------------------------------------------------------------------------
	if (! $usager->isAdmin()) {
		Erreur::erreurFatal('187', "admin.php - L'utilisateur '" . $usager->getIdUsager() . "' ne dispose pas des droits d'administration. Accès refusé.", $log);
	}
	
	// ----------------------------------------------------------------------------------------
	// Vérifier et traiter la demande
	// ----------------------------------------------------------------------------------------
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {

		$log->debug("admin.php:   Traiter la demande '$demande'");
		
		
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
				
			$gabarit = "admin-projet-modifier.php";
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Modifier un projet - Ajouter un collaborateur
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_modifier_ajouter_collaborateur") {
		
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
				
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

					// Vérifier si la personne est déjà responsable du projet - si oui impossible de l'ajouter comme collaborateur
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
			$gabarit = "admin-projet-modifier.php";
		
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Supprimer les collaborateurs actuels
		// ----------------------------------------------------------------------------------------
		if ($demande == "collaborateur_supprimer_acces") {
				
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
				
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
			$gabarit = "admin-projet-modifier.php";
		
		}
		
		// ----------------------------------------------------------------------------------------
		// Supprimer les collaborateurs invités
		// ----------------------------------------------------------------------------------------
		if ($demande == "collaborateur_supprimer_invitation") {
		
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
				
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
			$gabarit = "admin-projet-modifier.php";
		
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Supprimer les collaborateurs invités
		// ----------------------------------------------------------------------------------------
		if ($demande == "collaborateur_remplacer_responsable") {
				
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
		
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
			
			// Obtenir le responsable du projet
			$idResp = $projet->getIDResponsableProjet();
			
			// Obtenir le nouvel utilisateur
			$idNouvResp = $listeElements[0];
			$u = new Usager($log, $dbh);
			$u->getUsagerParIdUsager($idNouvResp);
				
			// Vérifier le statut
			if ($u->get("statut") == "0") {
		
				// Statut valide, effectuer la modification
				$projet->remplacerResponsable($idResp, $idNouvResp);
				
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
				
			// Afficher le gabarit à nouveau
			$gabarit = "admin-projet-modifier.php";
				
		}
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder un projet après modification
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_modifier_sauvegarder") {
		
			// Obtenir l'id du projet
			$idProjet = Web::getParamNum('projet_id_projet');
		
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
				$log->debug("admin.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
			}
		
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();
				
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
		
			// Vérifier que le projet est actif
			if ($projet->get("statut") == "1") {
			
				// Le projet est actif et peut être modifié
				$gabarit = "admin-projet-modifier.php";
					
			} else {
					
				// Le projet est inactif et ne peut être modifié (sauf le statut)
				$gabarit = "admin-projet-modifier-inactif.php";
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
				$idProjet = $projet->getIdProjetParPageTous($page);
			}
		
			// Charger les infos du projet
			$projet->getProjetParId($idProjet);
		
			// Obtenir les collaborateurs actuels
			$listeCollaborateursActifs = $projet->getCollaborateursActuels();
				
			// Obtenir les collaborateurs invités
			$listeCollaborateursInvites = $projet->getCollaborateursInvites();
			
			// Vérifier que le projet est actif
			if ($projet->get("statut") == "1") {
	
				// Le projet est actif et peut être modifié
				$gabarit = "admin-projet-modifier.php";
					
			} else {
					
				// Le projet est inactif et ne peut être modifié (sauf le statut)
				$gabarit = "admin-projet-modifier-inactif.php";
			}
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, '0', TXT_PREFIX_PROJET . $projet->get("id_projet"), '');
		
		}
		
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille un ou plusieurs items
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_corbeille_liste" || $demande == "projet_corbeille") {
		
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("projets_selection_");
			if (! empty($listeElements) ) {
		
				$totalProbPermission = 0;
		
				foreach ($listeElements as $element) {
						
					// Mettre le projet à la corbeille
					$prj = new Projet($log, $dbh);
					$prj->getProjetParId($element);
					$prj->desactiver();
				}
		
				$messages = new Messages(MSG_022, Messages::CONFIRMATION);
		
			} else {
				// Obtenir l'id du projet
				$idProjet = Web::getParamNum("projet_id_projet");
		
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
				$log->debug("admin.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
		
				// Réafficher la liste des items
				$demande = "projets_liste";
		
			} else {
				// Aucun projet actif, redirection pour créer un nouveau projet
				$log->debug("admin.php:   Aucun projet, redirection vers la page pour créer un projet");
					
				// Présenter l'écran pour créer un projet
				$nouvProjet = new Projet($log, $dbh);
				include(REPERTOIRE_GABARITS . 'identification-nouveau-projet.php');
			}
		}
			
		
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille un ou plusieurs items
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
				if (! $projet->isRoleCollaborateurProjet($idProjet, $listeProjetsActifs)) {
					Erreur::erreurFatal('196', "admin.php - Problème d'accès détecté : L'utilisateur " . $usager->get("id_usager") . " - " . $usager->get("prenom") . " " . $usager->get("nom") . " ne dispose pas du rôle de collaborateur requis pour se retirer de la liste de collaboreateurs du projet '$idProjet'", $log);
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
				$log->debug("admin.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
		
				// Réafficher la liste des items
				$demande = "projets_liste";
		
			} else {
				// Aucun projet actif, redirection pour créer un nouveau projet
				$log->debug("admin.php:   Aucun projet, redirection vers la page pour créer un projet");
					
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
			$session->set("projets_admin_recherche_chaine", "");
		
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
			$session->set("projets_admin_recherche_chaine", $chaine);
		
			// Afficher la liste des questionnaires correspondants
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
			$filtreResponsable = $projet->getFiltreResponsableAdmin();
		
			// Déterminer si on affiche la liste des projets
			if ($session->get("projets_admin_recherche_chaine") == "") {
				$listeIdProjets = $projet->getListeTousProjets($tri, true, $filtreResponsable);
			} else {
				$chaine = $session->get("projets_admin_recherche_chaine");
				// Rechercher les projets
				$listeIdProjets = $projet->recherche($chaine, $tri, Projet::STATUT_ACTIF . ", " . Projet::STATUT_INACTIF, $idUsager, $filtreResponsable, true);
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
			$listeIdProjetsTous = $projet->getListeTousProjets($tri, false, $filtreResponsable);
		
			// Obtenir la liste des reponsables pour tous les projets visibles
			$listeResponsables = $projet->getListeResponsablesProjetsPourProjetsSpecifiques($listeIdProjetsTous);
		
			$gabarit = "admin-projets-liste.php";
		}		

		
		// ----------------------------------------------------------------------------------------
		
		//                                 U T I L I S A T E U R S 
		
		// ----------------------------------------------------------------------------------------
		
		
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des utilisateurs à approuver
		// ----------------------------------------------------------------------------------------
		if ($demande == "approbation") {
				
			// Régler le filtre pour afficher les utilisateurs à approuver
			$session->set("pref_filtre_statut", "2");
			
			$demande = "utilisateurs_liste";
		}
		
		// ----------------------------------------------------------------------------------------
		// Ajouter un utilisateur
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateur_ajouter") {
		
			// Préparer les valeurs initiales
			$usr = new Usager($log, $dbh);
			$usr->set("code_usager", TXT_NOUVEL_UTILISATEUR);
			$usr->set("statut", Usager::STATUT_INCOMPLET);
			$usr->set("gds_secret", Securite::creerGrainDeSel());
			$usr->set("pref_nb_elem_page", NB_ELEMENT_PAR_PAGE);
				
			// Ajouter l'utilisateur
			$usr->ajouter();
			
			// Obtenir l'id
			$idUsr = $usr->get("id_usager");
			
			// Récupérer les infos
			$usr->getUsagerParIdUsager($idUsr);
			
			// Indiquer qu'on ajoute un utilisateur, le traitement est différent
			$pageInfos["mode"] = "ajout";
				
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
				
			// Obtenir la liste des langues de publication
			$lang = new Langue($log, $dbh);
			$listeLanguesPublication = $lang->getListeLangues($idProjetActif);			
			
			$gabarit = "admin-utilisateur-modifier.php";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche des utilisateurs
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateurs_recherche_initialiser") {
		
			// Mettre en session
			$session->set("usagers_recherche_chaine", "");
		
			// Afficher la liste des utilisateurs correspondants
			$demande = "utilisateurs_liste";
		}
		
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les utilisateurs
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateurs_recherche") {
		
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("usagers_recherche_chaine", $chaine);
		
			// Afficher la liste des questionnaires correspondants
			$demande = "utilisateurs_liste";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Approuver
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateurs_approuver") {
		
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("utilisateurs_selection_");
			if (! empty($listeElements) ) {
		
				foreach ($listeElements as $element) {
		
					// Approuver l'accès
					$usr = new Usager($log, $dbh);
					$usr->getUsagerParIdUsager($element);
					
					// Détecter une approbation
					if ($usr->get("statut") == Usager::STATUT_A_APPROUVER) {
					
						// Modifier le statut de l'utilisateur
						$usr->set("statut", USAGER::STATUT_ACTIF);
							
						// Envoyer u	n courriel de confirmation
						$usr->envoiCourrielCompteApprobation();
						
						// Enregistrer les informations de l'utilisateur
						$usr->enregistrer();
					}
				}
		
				$messages = new Messages(MSG_032, Messages::CONFIRMATION);
			}
			
			$demande = "utilisateurs_liste";
		}
		
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille un ou plusieurs utilisateurs
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateur_corbeille_liste" || $demande == "utilisateur_corbeille") {
		
			$nbAdminProteges = 0;
			
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("utilisateurs_selection_");
			if (! empty($listeElements) ) {
		
				$totalProbPermission = 0;
		
				foreach ($listeElements as $element) {
		
					// Mettre l'utilisateur à la corbeille
					$usr = new Usager($log, $dbh);
					$usr->getUsagerParIdUsager($element);
					$conf = $usr->desactiver();
					
					if ($conf == 0) {
						$nbAdminProteges++;
					}
					
				}
		
				$messages = new Messages(MSG_030, Messages::CONFIRMATION);
		
			} else {
				// Obtenir l'id usager
				$idUsager = Web::getParamNum("usager_id_usager");
		
				// Charger l'utilisateur
				$usr = new Usager($log, $dbh);
				$usr->getUsagerParIdUsager($idUsager);
		
				// Mettre à la corbeille l'utilisateur sélectionné
				$desactive = $usr->desactiver();
				
				if ($desactive == 0) {
					$nbAdminProteges++;
				}
			}
			
			// Message s'il n'est pas possible de supprimer certains admins
			if ($nbAdminProteges > 0) {
				$messages = new Messages(MSG_036, Messages::AVERTISSEMENT);
			}			
		
			// Réafficher la liste des utilisateurs
			$demande = "utilisateurs_liste";
		
		}		

		
		// ----------------------------------------------------------------------------------------
		// Modifier un utilisateurs
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateur_modifier") {
				
			// Obtenir l'id de l'utilisateur
			$idUsager = Web::getParamNum('usager_id_usager');
				
			// Vérifier si l'id de l'usager est disponible via un changement de page
			$page = Web::getParamNum('usager_page');
				
			if ($idUsager == "" && $page != "") {
				$idUsager = $usr->getIdUsagerParPage($page);
			}
		
			// Charger les infos de l'utilisateur
			$usr->getUsagerParIdUsager($idUsager);
				
			// Obtenir la liste des projets actifs
			$listeIDProjetsActifs = $projetActif->getListeProjetsUtilisateur($idUsager, $log, $dbh);
			
			foreach ($listeIDProjetsActifs as $p) {			
				$idProj = $p->get("id_projet");
				
				// Charger le projet
				$prj = new Projet($log, $dbh);
				$prj->getProjetParId($idProj);
			
				// Ajouter aux résultats de recherche
				array_push($listeProjetsActifsUtilisateur, $prj);
			}

			// Mode modification
			$pageInfos["mode"] = "modif";
			
			// Attention si le code utilisateur est la valeur par défaut, permettre l'édition de celui-ci
			if ($usr->get("code_usager") == TXT_NOUVEL_UTILISATEUR) {
				$pageInfos["mode"] = "ajout";
			}
			
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
			
			// Obtenir la liste des langues de publication
			$lang = new Langue($log, $dbh);
			$listeLanguesPublication = $lang->getListeLangues($idProjetActif);
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, '0', TXT_PREFIX_USAGER . $usr->get("id_usager"), '');
			
			$gabarit = "admin-utilisateur-modifier.php";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder un utilisateur après modification
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateur_modifier_sauvegarder") {
		
			$erreurs = "";
			
			// Obtenir l'id du projet
			$idUsager = Web::getParamNum('usager_id_usager');

			// Charger l'utilisateur
			$usr = new Usager($log, $dbh);
			$usr->getUsagerParIdUsager($idUsager);
			
			// Prendre en note les infos originales
			$courrielOrig = $usr->get("courriel");
			$statutOrig = $usr->get("statut");
			$codeUsagerOrig = $usr->get("code_usager");
		
			// Vider les checkboxes
			$usr->set("notification", "");
		
			// Obtenir les informations à partir de la requête
			$usr->getDonneesRequete();
			
			// Vérifier si le courriel a été modifié, si oui, qu'il n'est pas déjà utilisé
			if ($usr->get("courriel") != $courrielOrig) {
				$u = new Usager($log, $dbh);
				if ($u->getUsagerParCourriel($usr->get("courriel"))) {
					
					// Erreur le courriel existe déjà
					$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_209 . HTML_LISTE_ERREUR_FIN;
				}
			}

			// Vérifier si le code usager a été modifié, si oui, s'il existe déjà
			if ($usr->get("code_usager") != $codeUsagerOrig) {
				$u = new Usager($log, $dbh);
				if ($u->getUsagerParCodeUsager($usr->get("code_usager"))) {

					// Erreur le code usager existe déjà
					$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_177 . HTML_LISTE_ERREUR_FIN;
				}
			}

			// Obtenir le mode (ajout ou modification)
			$mode = Web::getParam('mode');
			$pageInfos['mode'] = $mode;
			
			// Vérifier les champs du profil
			$erreurs .= $usr->verifierProfil();
			
			// Vérifier les champs du mot de passe au besoin (seulement si spécifié ou nouvel utilisateur)
			if (Web::getParam('usager_mdp_nouv') != "" || Web::getParam('usager_mdp_conf') != "" || $mode == "ajout") {
				$erreurs .= $usr->verifierChoixNouveauMDP();
			}
			
			if ($erreurs == "") {
				
				// Détecter une approbation
				if ($statutOrig == Usager::STATUT_A_APPROUVER && $usr->get("statut") == Usager::STATUT_ACTIF) {

					// Modifier le statut de l'utilisateur
					$usr->set("statut", USAGER::STATUT_ACTIF);
					
					// Envoyer un courriel de confirmation
					$usr->envoiCourrielCompteApprobation();
				}	
								
				
				// Détecter un refus d'accès
				if ($statutOrig == Usager::STATUT_A_APPROUVER && $usr->get("statut") == Usager::STATUT_REFUSE) {
				
					// Modifier le statut de l'utilisateur
					$usr->set("statut", USAGER::STATUT_REFUSE);
					
					// Envoyer un courriel de confirmation
					$usr->envoiCourrielCompteRefus();
				}
				
				// Si le statut était à incomplet, le passer à actif
				if ($statutOrig == Usager::STATUT_INCOMPLET) {
					$usr->set("statut", USAGER::STATUT_ACTIF);
				}
				
				// Enregistrer les informations de l'utilisateur
				$usr->enregistrer();
				
				// Message de confirmation
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);

				// Enlever le mode ajout
				$pageInfos["mode"] = "modif";
				
				// Charger les infos de l'utilisateur
				$usr->getUsagerParIdUsager($idUsager);
					
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
			}
			
			// Obtenir la liste des projets
			$listeIDProjetsActifs = $projetActif->getListeProjetsUtilisateur($idUsager, $log, $dbh);
				
			foreach ($listeIDProjetsActifs as $p) {
				$idProj = $p->get("id_projet");

				// Charger le projet
				$prj = new Projet($log, $dbh);
				$prj->getProjetParId($idProj);
					
				// Ajouter aux résultats de recherche
				array_push($listeProjetsActifsUtilisateur, $prj);
			}			
			
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
				
			// Obtenir la liste des langues de publication
			$lang = new Langue($log, $dbh);
			$listeLanguesPublication = $lang->getListeLangues($idProjetActif);			
			
			// Réafficher le gabarit
			$gabarit = "admin-utilisateur-modifier.php";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Liste des utilisateurs
		// ----------------------------------------------------------------------------------------
		if ($demande == "utilisateurs_liste") {
		
			$u = new Usager($log, $dbh);

			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "liste") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}

			// Obtenir le filtre par statut
			$filtreStatut = $u->getFiltreStatut();
				
			// Déterminer si on affiche la liste des utilisateurs
			if ($session->get("usagers_recherche_chaine") == "") {
				$listeTousUsagers = $u->getListeUsagers(true, $filtreStatut);
			} else {
				
				// ou rechercher les usagers
				$chaine = $session->get("usagers_recherche_chaine");
				$listeTousUsagers = $u->recherche($chaine, $filtreStatut, Usager::STATUT_LISTE_USAGERS);
			}			
				
			$pagination = new Pagination($listeTousUsagers, $usager, $log, $dbh);
				
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
		
					// Obtenir la liste des usagers
					$u = $listeTousUsagers[$i];
		
					// Ajouter aux résultats de recherche
					array_push($listeUsagers, $u);
				}
			}
				
			// Préparer l'affichage
			$usager->preparerAffichageListe();
				
			// Filtre statut pour les utilisateurs
			$filtreStatut = $session->get("pref_filtre_statut");
			if ($filtreStatut == "") {
				$filtreStatut = "tous";
			}
			
			$gabarit = "admin-utilisateurs-liste.php";
				
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
				$log->debug("admin.php:   Chargement du projet '$idProjetActif'");
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
			$gabarit = "admin-corbeille.php";
		}		

		
		// ----------------------------------------------------------------------------------------
		
		//                        É D I T I O N    D E S    T E X T E S 
		
		// ----------------------------------------------------------------------------------------
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder les textes
		// ----------------------------------------------------------------------------------------
		if ($demande == "textes_sauvegarder") {
		
			$textes = new Texte($log, $dbh);
			
			// Obtenir les informations à partir de la requête
			$textes->getDonneesRequete();

			// Sauvegarde
			$textes->enregistrer();
			
			// Message de confirmation
			$messages = new Messages(MSG_001, Messages::CONFIRMATION);
			
			// Réafficher la page
			$demande = "textes_modifier";
		}
		
		// ----------------------------------------------------------------------------------------
		// Afficher l'écran pour modifier les textes
		// ----------------------------------------------------------------------------------------
		if ($demande == "textes_modifier") {
		
			// Obtenir la langue
			$langTexteSel = Web::getParam("texte_langue");
				
			if ($langTexteSel == "") {
				$langTexteSel = LANGUE_DEFAUT;
			}
				
			// Obtenir les textes
			$texte = new Texte($log, $dbh);
			$texte->getTexte($langTexteSel);
				
			// Obtenir la liste des langues de l'interface
			$listeLanguesInterface = LangueInterface::getListeLanguesInterfaces($log);
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, '0', 'ADMIN_TEXTES', '');
				
			// Régler le gabarit à utiliser
			$gabarit = "admin-textes-modifier.php";
		}		

		
		// ----------------------------------------------------------------------------------------
		
		//                          O U T I L S   D E   G E S T I O N
		
		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Afficher l'écran des outils
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils") {
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
			
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}
			
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
			
		}


		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des catégories
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_categories") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
			
			// Indexation
			$cat = new Categorie($logTmp, $dbh);
			$nbIdxCategorie = $cat->reindexer();
			$log->debug("admin.php: Indexation des catégories: '$nbIdxCategorie'");
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}

		
		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_collections") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
				
			// Indexation
			$col = new Collection($logTmp, $dbh);
			$nbIdxCollection = $col->reindexer();
			$log->debug("admin.php: Indexation des collection : '$nbIdxCollection'");
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des items
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_items") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
			
			// Indexation
			$i = new Item($logTmp, $dbh);
			$nbIdxItem = $i->reindexer(); 
			$log->debug("admin.php: Indexation des items : '$nbIdxItem'n");
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}		

		
		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_langues") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
			
			// Indexation
			$l = new Langue($logTmp, $dbh);
			$nbIdxLangue = $l->reindexer();
			$log->debug("admin.php: Indexation des langues : '$nbIdxLangue'");
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}
				
		
		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des médias
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_medias") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
			
			// Indexation
			$m = new Media($logTmp, $dbh);
			$nbIdxMedia = $m->reindexer();
			$log->debug("admin.php: Indexation des médias : '$nbIdxMedia'");		
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des projets
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_projets") {
			
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
				
			// Indexation
			$p = new Projet($logTmp, $dbh);
			$nbIdxProjet = $p->reindexer();
			$log->debug("admin.php: Indexation des projets: '$nbIdxProjet'");
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}
		

		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des questionnaires
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_questionnaires") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
				
			// Indexation
			$q = new Questionnaire($logTmp, $dbh);
			$nbIdxQuest = $q->reindexer();
			$log->debug("admin.php: Indexation des questionnaires: '$nbIdxQuest'");
		
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
		
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Effectuer l'indexation des utilisateurs
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_indexation_utilisateurs") {
		
			// Désactiver la journalisation durant l'indexation
			$logTmp = new Log($logFN, LOG::AUCUN);
				
			// Indexation
			$u = new Usager($logTmp, $dbh);
			$nbIdxUsager = $u->reindexer();
			$log->debug("admin.php: Indexation des utilisateurs : '$nbIdxUsager'");
				
			/// Message de confirmation
			$messages = new Messages(MSG_033, Messages::CONFIRMATION);
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}			
				
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}
		
		// ----------------------------------------------------------------------------------------
		// Rendre l'application disponible
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_acces_ouvrir") {
			
			// Rendre l'application disponible
			$maintenance->setApplicationDisponible();
			
			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}
			
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}		

		// ----------------------------------------------------------------------------------------
		// Rendre l'application non disponible
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_acces_fermer") {
						
			// Rendre l'application non disponible
			$maintenance->setApplicationNonDisponible();

			// Vérifier si une mise à jour est disponible
			$maintenance = new Maintenance($log, $dbh);
			$msg = $maintenance->verifierNouvelleVersionDisponible($session);
				
			$messageMAJ = "";
			if ($msg != "") {
				$messageMAJ = CONSTANT($msg);
			}
			
			// Message de confirmation
			$messages = new Messages(MSG_046, Messages::AVERTISSEMENT);
			
			// Régler le gabarit à utiliser
			$gabarit = "admin-outils.php";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Effectuer la sauvegarde des données
		// ----------------------------------------------------------------------------------------
		if ($demande == "outils_sauvegarde") {
			
			$maintenance = new Maintenance($log, $dbh);
			$maintenance->sauvegardeBD($usager);
			
		}
		
		// ----------------------------------------------------------------------------------------
		// Préparer les chaînes de recherche
		// ----------------------------------------------------------------------------------------
		$chaineRechCorbeille = $session->get("corbeille_recherche_chaine");
		$chaineRechProjets = $session->get("projets_recherche_chaine");
		$chaineRechProjetsAdmin = $session->get("projets_admin_recherche_chaine");
		$chaineRechUsagers = $session->get("usagers_recherche_chaine");
		
		// ----------------------------------------------------------------------------------------
		// Préparer affichage de la liste des projets
		// ----------------------------------------------------------------------------------------
		$projet->preparerAffichageAdmin();		
		$usr->preparerAffichage();
		$pageInfos["responsable"] = $session->get("pref_filtre_responsable_admin");
		
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
		Erreur::erreurFatal('006', "[admin.php] Demande incorrecte : '$demande'", $log);
	}

	// Terminer
	$log->debug("admin.php: Fin");

} catch (Exception $e) {
	Erreur::erreurFatal('018', "admin.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}