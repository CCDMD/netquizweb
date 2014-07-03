<?php

/**
 * Aiguilleur : questionnaires.php
 *
 * Aiguillage des demandes pour les questionnaires
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {

	// Configuration et démarrage
	$aiguilleur = "questionnaires";	
	require_once 'init.php';
	
	$demandesPermises = array("liste", "recherche", "recherche_initialiser", "questionnaire_ajouter", "questionnaire_modifier", "questionnaire_sauvegarder", "questionnaire_dupliquer", "questionnaire_suivi",
			"questionnaire_imprimer", "questionnaire_accueil_imprimer", "questionnaire_fin_imprimer", "questionnaire_suivi_activer", "questionnaire_suivi_activer_ajax", "questionnaire_suivi_desactiver",
			"questionnaire_supprimer", "questionnaire_corbeille", "questionnaire_apercu", "questionnaire_apercu_messages", "questionnaire_publier_valider", "questionnaire_desactiver",
			"questionnaire_publier_formulaire", "questionnaire_publier_envoi", "questionnaire_publier_voir", "questionnaire_selectionner", "questionnaire_selectionner_recherche",
			"questionnaire_selectionner_recherche_initialiser", "questionnaire_selectionner_suivi_activer", "questionnaire_selectionner_suivi_desactiver",
			"questionnaire_telecharger", "questionnaire_exporter", "menu_modifier", "theme_apercu",
			"corbeille", "corbeille_recherche", "corbeille_recherche_initialiser", "corbeille_recuperer", "corbeille_supprimer",
			"accueil_modifier", "accueil_sauvegarder", "fin_modifier", "fin_sauvegarder", "item_ajouter", "item_modifier", "item_sauvegarder", "item_supprimer_classeur_elements",
			"item_apercu", "item_dupliquer", "item_imprimer", "item_suivi", "item_suivi_activer", "item_supprimer",
			"item_modifier_ajouter_couleur", "item_modifier_supprimer_couleur", "item_changer_section", "item_corbeille",
			"items_selectionner_demarrer", "items_selectionner", "items_selectionner_recherche", "items_selectionner_recherche_initialiser", "items_selectionner_suivi_desactiver",
			"items_selectionner_suivi_activer", "items_selectionner_sauvegarder", "item_modifier_ajouter_element", "item_modifier_supprimer_element",
			"item_modifier_ajouter_couleur", "item_modifier_supprimer_couleur",
			"item_modifier_type_element", "item_modifier_media", "item_modifier_type",
			"item_modifier_type_element_classeur", "item_modifier_ajouter_classeur", "item_modifier_supprimer_classeur", "item_ajouter_classeur_element", "item_supprimer_classeur_element", "item_ajouter_classeur_elements",
			"item_ajouter_lacune", "item_maj_lacunes", "item_supprimer_lacunes", "item_modifier_ajouter_lacune_reponse", "item_modifier_supprimer_lacune_reponse", "item_modifier_type_lacune",
			"elements_importer", "elements_importer_formulaire", "elements_importer_envoi", "elements_importer_resultats",
			"aide_apropos_intro", "aide_apropos_droits", "aide_apropos_generique", "aide_apropos_commentaires", "aide", "projet_creer", "session_verifier", "session_message"
	);
	
	$log->debug("questionnaires.php: Début");

	// ----------------------------------------------------------------------------------------
	// Initialisation
	// ----------------------------------------------------------------------------------------
	$gabarit = "";
	$listeThemes = "";
	$session = new Session();
	$corbeille = new Corbeille($log, $dbh);
	$terme = new Terme($log, $dbh);
	$listeCorbeille = array();
	$demandeRetour = ""; // En cas de rafaîchissement demande à recharger

	// ----------------------------------------------------------------------------------------
	// Obtenir la demande
	// ----------------------------------------------------------------------------------------
	$demande = Web::getParam('demande');
	if ($demande == "") {
		$demande = "liste";
	}

	$log->debug("questionnaires.php:   --------------------------- Aiguillage de la demande '$demande' ---------------------------");

	// Prendre note de la demande originale
	$demandeOrig = $demande;

	// ----------------------------------------------------------------------------------------
	// Obtenir l'id du questionnaire
	// ----------------------------------------------------------------------------------------
	$idQuest = Web::getParamNum('questionnaire_id_questionnaire');
	$log->debug("questionnaires.php:   idQuest = '" . $idQuest . "'");

	// ----------------------------------------------------------------------------------------
	// Obtenir la page à afficher
	// ----------------------------------------------------------------------------------------
	$page = Web::getParamNum('questionnaire_page');

	// ----------------------------------------------------------------------------------------
	// Obtenir l'id de la lacune
	// ----------------------------------------------------------------------------------------
	$idLacune = Web::getParam("lacune");

	// ----------------------------------------------------------------------------------------
	// Instancier le questionnaire et récupérer les infos
	// ----------------------------------------------------------------------------------------
	$quest = new Questionnaire($log, $dbh);

	// ----------------------------------------------------------------------------------------
	// Obtenir l'id de l'item et le type d'item
	// ----------------------------------------------------------------------------------------
	$idItem = Web::getParamNum('item_id_item');
	$log->debug("questionnaires.php:   idItem = '" . $idItem . "'");
	$typeItem = Web::getParam('item_type_item');
	$log->debug("questionnaires.php:   typeItem = '" . $typeItem . "'");

	// Vérifier si l'item est valide
	if ($typeItem != "" && ! Item::isTypeItemValide($typeItem) ) {
		$log->debug("questionnaires.php:   Le type d'item spécifié est invalide '" . $typeItem . "'");
		Erreur::erreurFatal("009", "Le type d'item spécifié est invalide '" . $typeItem . "'", $log);
	}

	// ----------------------------------------------------------------------------------------
	// Vérifier si l'id du questionnaire est disponible via un changement de page
	// ----------------------------------------------------------------------------------------
	if ($idQuest == "" && $page != "") {
		$idQuest = $quest->getIdQuestionnaireParPage($page);
		$idItem = "";
	}

	// Charger le questionnaire si un id est spécifié
	if ($idQuest != "") {
		$quest->getQuestionnaireParId($idQuest, $idProjetActif);

		// Vérifier si le questionnaire existe
		if ($quest->get("id_questionnaire") == "") {
			$log->debug("init.php:   Impossible de récupérer les données pour le questionnaire '" . $idQuest . "' pour le projet'" . $idProjetActif . "'");
			Erreur::erreurFatal("008", "Impossible de récupérer les données pour le questionnaire ('" . $idQuest . "')", $log);
		}
	}

	// ----------------------------------------------------------------------------------------
	// Instancier l'item et récupérer les infos
	// ----------------------------------------------------------------------------------------
	$item = new Item($log, $dbh);
	if ($idItem != "" || $typeItem != "") {
		$itemFactory = new Item($log, $dbh);
		$item = $itemFactory->instancierItemParType($typeItem, $idProjetActif, $idItem);
		
		// Ajouter la langue du questionnaire
		$item->set("id_langue_questionnaire", $quest->get("id_langue_questionnaire"));
	}

	// ----------------------------------------------------------------------------------------
	// Obtenir l'onglet sélectionné (doit être 1, 2, 3, 4 ou "")
	// ----------------------------------------------------------------------------------------
	$onglet = Web::getParamNum("onglet");
	if ($onglet != "" && $onglet != "1" && $onglet != "2" && $onglet != "3") {
		$onglet = 1;
	}

	// ----------------------------------------------------------------------------------------
	// Obtenir la section ouverte
	// ----------------------------------------------------------------------------------------
	$section = Web::getParam("section");
	if ($section != "" && $section != "couleurs" && $section != "texte" && $section != "classeurs" && $section != "contenu") {
		$section = "";
	}

	// ----------------------------------------------------------------------------------------
	// Obtenir l'id de l'élément ouvert en édition (Classement)
	// ----------------------------------------------------------------------------------------
	$elementEditeur = Web::getParam("elementEditeur");

	// ----------------------------------------------------------------------------------------
	// Obtenir la collection et catégorie
	// ----------------------------------------------------------------------------------------
	$idCollection = Web::getParamNum("collection");
	$idCategorie = Web::getParamNum("categorie_id_categorie");

	// ----------------------------------------------------------------------------------------
	// Obtenir la liste des collections de l'utilisateur
	// ----------------------------------------------------------------------------------------
	if ($idProjetActif != "") {
		$collection = new Collection($log, $dbh);
		$listeCollection = $collection->getListeCollections($idProjetActif);
		$categorie = new Categorie($log, $dbh);
		$listeCategorie = $categorie->getListeCategories($idProjetActif);
	}

	// ----------------------------------------------------------------------------------------
	// Vérifier si on doit ouvrir une fenêtre pour sélectionner des items
	// ----------------------------------------------------------------------------------------
	$selectionItems = Web::getParamNum("items_selectionner");
	if ($selectionItems == "1") {
		$quest->set("selectionItems","1");
	}

	// ----------------------------------------------------------------------------------------
	// Préparer les informations pour la page
	// ----------------------------------------------------------------------------------------
	$pageInfos = array();
	$pageInfos['apercu'] = ""; // Défaut
	$pageInfos["flagModifications"] = "";

	if ($idProjetActif != "") {
		$pageInfos['repertoire_projet'] = Securite::nettoyerNomfichier($projetActif->get("repertoire")) . "/";
	}

	// ----------------------------------------------------------------------------------------
	// Obtenir le filtre par type d'item
	// ----------------------------------------------------------------------------------------
	$filtreTypeItem = Web::getParam("filtre_type_item");
	
	// Sinon de la session
	if ($filtreTypeItem == "") {
		$filtreTypeItem = $session->get("pref_filtre_type_item");
	}


	// ----------------------------------------------------------------------------------------
	// Vérifier et traiter la demande
	// ----------------------------------------------------------------------------------------
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {

		$log->debug("questionnaires.php:   Traiter la demande '$demande'");


		// ----------------------------------------------------------------------------------------

		//       D E M A N D E S   P R I O R I T A I R E S   P O U R   A I G U I L L A G E

		// ----------------------------------------------------------------------------------------

		
		// ----------------------------------------------------------------------------------------
		// Désactiver la publication d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_desactiver") {
		
			// Désactiver la publication
			$quest->desactiverPublication($projetActif);
		
			// Message de confirmation
			$messages = new Messages(MSG_007, Messages::CONFIRMATION);
		
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "liste";
			}

		}
		
		
		// ----------------------------------------------------------------------------------------
		// Préparer la publication d'un questionnaire - Valider le questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_publier_valider") {
				
			if ($quest->valider($idProjetActif)) {

				// Afficher le formulaire pour publier
				$urlApercu = "questionnaires.php?demande=questionnaire_publier_formulaire&questionnaire_id_questionnaire=" . $idQuest;
				$pageInfos['apercu'] = $urlApercu;

			} else {

				// Afficher message d'erreur
				$urlApercu = "questionnaires.php?demande=questionnaire_apercu_messages";
				$pageInfos['apercu'] = $urlApercu;
			}
				
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "liste";
			}
		}

		// ----------------------------------------------------------------------------------------
		// Aperçu d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_apercu") {
				
			$chaineAleatoire = Securite::genererChaineAleatoire(8);
			$prefixRepertoire = Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" . REPERTOIRE_PREFIX_APERCU . $chaineAleatoire . "/";

			// Vérifier le flag de modifications passer en paramètre
			$flagModif = Web::getParamNum("flagModifications");
			if ($flagModif == "1") {
				$pageInfos["flagModifications"] = 1;
			}
			
			// Obtenir les données du formulaire pour l'aperçu
			$quest->getDonneesRequete();
			
			// Vérifier si la langue par défaut doit être employée
			if ($quest->get("id_langue_questionnaire") == "0") {
				$log->debug("init.php:  questionnaire_apercu utiliser la langue par défaut de l'usager : '" . $usager->get("pref_apercu_langue") . "'");
				$quest->set("id_langue_questionnaire", $usager->get("pref_apercu_langue"));
			}
			
			// Valider le questionnaire
			if ($quest->valider($idProjetActif)) {

				// Générer l'apercu
				$rc = $quest->genererApercu($projetActif, $usager, $chaineAleatoire);

				if ($rc < 0) {
					$messages = new Messages(ERR_012, Messages::ERREUR);
				} elseif ($rc == 1) {

					// Préparer l'URL pour l'aperçu
					$urlApercu = URL_PUBLICATION . $prefixRepertoire . FICHIER_INDEX_HTML;
					$pageInfos['apercu'] = $urlApercu;
				}
			} else {

				// Afficher message d'erreur
				$urlApercu = "questionnaires.php?demande=questionnaire_apercu_messages";
				$pageInfos['apercu'] = $urlApercu;
			}
				
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "liste";
			}
		}


		// ----------------------------------------------------------------------------------------
		// Télécharger un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_telecharger") {
				
			if ($quest->valider($idProjetActif)) {

				// Générer l'apercu
				$urlFichierZip = $quest->genererApercuZip($usager);

				if ($urlFichierZip == "") {
					$messages = new Messages(ERR_012, Messages::ERREUR);
				} else {
					header("Location: $urlFichierZip");
				}
			} else {

				// Afficher message d'erreur
				$urlApercu = "questionnaires.php?demande=questionnaire_apercu_messages";
				$pageInfos['apercu'] = $urlApercu;
			}
				
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "liste";
			}
		}

		// ----------------------------------------------------------------------------------------
		// Exporter un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_exporter") {
				
			// Générer l'apercu
			$urlFichierZip = $quest->exporterXML($usager);
				
			if ($urlFichierZip == "") {
				$messages = new Messages(ERR_012, Messages::ERREUR);
			} else {
				header("Location: $urlFichierZip");
			}
				
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "liste";
			}
		}


		// ----------------------------------------------------------------------------------------

		//                          A C C U E I L   Q U E S T I O N N A I R E

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour modifier l'accueil d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "accueil_modifier") {
				
			// Régler l'item à accueil pour le menu
			$item->set("id_item", "accueil");
				
			// Titre pour ajout de média
			$session->set("item_titre_menu", TXT_PAGE_ACCUEIL);
				
			// Vérifier l'état du questionnaire publié
			$quest->verifierQuestionnairePublie($projetActif);
				
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire"), TXT_PREFIX_ITEM . $item->get("id_item"));
			
			// Régler le gabarit à utiliser et la demande de retour
			$demandeRetour = "accueil_modifier";
			$gabarit = "quest-accueil.php";
		}

		// ----------------------------------------------------------------------------------------
		// Sauvegarder les modifications à l'accueil du formulaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "accueil_sauvegarder") {

			if (!$selectionItems) {
					
				// Obtenir les informations à partir de la requête
				$quest->getDonneesRequete();

				// Mettre à jour un questionnaire existant
				$quest->enregistrer();

				// Recharger les données les plus récentes
				$quest->getQuestionnaireParId($idQuest, $idProjetActif);

				// Message de confirmation
				$log->debug("questionnaires.php: Accueil Sauvegarde complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);

				// Régler l'item à accueil pour le menu
				$item->set("id_item", "accueil");
			}

			// Régler le gabarit à utiliser et la demande de retour
			$demandeRetour = "accueil_modifier";
			$gabarit = "quest-accueil.php";
		}

		
		// ----------------------------------------------------------------------------------------
		
		//                                      P R O J E T S 
		
		// ----------------------------------------------------------------------------------------
		

		// ----------------------------------------------------------------------------------------
		// Projet - Créer un nouveau projet
		// ----------------------------------------------------------------------------------------
		if ($demande == "projet_creer") {
		
			// Obtenir les informations du projet
			$nouvProjet = new Projet($log, $dbh);
			$nouvProjet->getDonneesRequete();
				
			// Vérifier les champs
			$erreurs = $nouvProjet->verifierChampsProjet(true);
				
			// Vérifier si des erreurs ont été détectées
			if ($erreurs == "") {
		
				// Statut actif par défaut
				$nouvProjet->set("statut", "1");
		
				// Enregistrer les informations du projet
				$nouvProjet->ajouter();
		
				// Ajouter le rôle administrateur à l'usager pour le projet
				$nouvProjet->ajouterRole($idUsager, Projet::ROLE_RESPONSABLE);
				$messages = new Messages(MSG_015, Messages::CONFIRMATION);
				
				// Obtenir la liste des projets pour l'utilisateur
				$listeProjets = Projet::getListeProjetsUtilisateur($idUsager, $log, $dbh);
				
				// Obtenir le nouveau projet courant
				$projet = new Projet($log, $dbh);
				$idProjetActif = $projet->getIdProjetCourant($usager);
				$log->debug("init.php:   Projet '$idProjetActif' sélectionné");
				
				// Charger le projet
				if ($idProjetActif != "0") {
					$log->debug("init.php:   Chargement du projet '$idProjetActif'");
					$projet->getProjetParId($idProjetActif);
				}
				
				// Effectuer une redirection afin d'éviter le rafraîchissement de la page qui pourrait créer un 2e projet (erreur sur identifiant unique)
				$url = "questionnaires.php";
				header("Location: $url");
		
			} else {
				$messages = new Messages($erreurs, Messages::ERREUR);
					
				// Retour à la page d'identification
				include(REPERTOIRE_GABARITS . 'identification-nouveau-projet.php');
			}
				
		}		
		
		
		// ----------------------------------------------------------------------------------------

		//                                  C O R B E I L L E

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans la corbeille
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille_recherche") {
				
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
				
			// Mettre en session
			$session->set("corbeille_recherche_chaine", $chaine);
				
			// Afficher la liste des questionnaires correspondants
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
				$log->debug("init.php:   Chargement du projet '$idProjetActif'");
				$projetActif->getProjetParId($idProjetActif);
			}			
			
			// Réafficher la liste des questionnaires
			$demande = "corbeille";
		}

		// ----------------------------------------------------------------------------------------
		// Corbeille supprimer des éléments
		// ----------------------------------------------------------------------------------------
		if ($demande == "corbeille_supprimer") {

			// Obtenir le(s) formulaire(s) à supprimer
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

			// Déterminer si on affiche la liste des questionnaires
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
			$gabarit = "quest-corbeille.php";
		}


		// ----------------------------------------------------------------------------------------

		//                                      I T E M S

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Aperçu d'un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_apercu") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);

			$chaineAleatoire = Securite::genererChaineAleatoire(8);
			$prefixRepertoire = Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" . REPERTOIRE_PREFIX_APERCU . $chaineAleatoire . "/";
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);
				
			// Préparer les valeurs
			$item->preparerValeursApresChargement($idProjetActif);
				
			// Préparer les valeurs pour le panneau paramètres (valeurs pour cet item seulement)
			$item->preparerValeursPanneauParametres();

			// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
			$item->preparerValeursPanneauMessages();
				
			// Vérifier le flag de modifications passer en paramètre
			$flagModif = Web::getParamNum("flagModifications");
			if ($flagModif == "1") {
				$pageInfos["flagModifications"] = 1;
			}
				
			// Valider l'item
			if ($item->valider($quest)) {

				// Générer l'apercu
				$rc = $item->genererApercu($projetActif, $usager, $item, $chaineAleatoire, $quest);
				if ($rc < 0) {
					$messages = new Messages(ERR_012, Messages::CONFIRMATION);
				} elseif ($rc == 1) {

					// Préparer l'URL pour l'aperçu
					$urlApercu = URL_PUBLICATION . $prefixRepertoire . FICHIER_MAIN_HTML;
					$pageInfos['apercu'] = $urlApercu;
				}
					
			} else {

				// Afficher message d'erreur
				$urlApercu = "bibliotheque.php?demande=item_apercu_messages";
				$pageInfos['apercu'] = $urlApercu;
			}

			// Obtenir les informations "valeur pour ce questionnaire seulement"
			if ($demandeOrig != "item_apercu") {
				$item->getValeursPourQuestionnaire($idQuest, $idProjetActif);
			}
				
			// Mettre en session le titre du questionnaire et de l'item pour la
			// fenêtre de sélection des fichiers
			$session->set("questionnaire_titre_menu", $quest->get("titre_menu"));
			$session->set("item_titre_menu", $item->get("titre_menu"));

			// Récupérer la langue du questionnaire
			$quest->set("langue_txt", LANGUE::getTitre($quest->get("id_langue_questionnaire"), $idProjetActif, $log, $dbh));
				
			// Obtenir la langue de l'item
			$langueItem = $quest->getLangueApercuObj();

			// Analyser les éléments avant affichage
			$item->analyserElements();
				
			// Régler la demande de retour
			$demandeRetour = "item_modifier";
				
			// Afficher le formulaire de modification
			$gabarit = "quest-item-modifier.php";
		}

		// ----------------------------------------------------------------------------------------
		// Afficher les messages d'erreurs reliés à l'apercu
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_apercu_messages") {
				
			// Obtenir le message
			$msg = $session->get("item_apercu_messages");
			$item->set("item_apercu_messages", $msg);
			$session->delete("item_apercu_messages");

			// Régler le gabarit
			$gabarit = "validation/item-page.php";
		}

		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour ajouter un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_ajouter") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Préparer les valeurs initiales selon le type d'item
			$item->preparerValeursInitiales($typeItem, $projetActif, $usager);
			$item->set("id_questionnaire", $idQuest);
			$item->set("id_projet", $idProjetActif);
				
			// Ajouter l'item
			$item->ajouter();

			// Mettre à jour le numéro d'item courant
			$idItem = $item->get("id_item");
				
			// Ajouter l'item au bon endroit dans le menu
			$menu = new Menu($log, $dbh);
			$posItem = $menu->insererItem($idItem, $typeItem, $idQuest, $idProjetActif);
				
			// Mettre à jour le nombre d'items pour le questionnaire
			$quest->updateNombreItems($idQuest, $idProjetActif);

			// Récupérer la langue du questionnaire
			$quest->set("langue_txt", LANGUE::getTitre($quest->get("id_langue_questionnaire"), $idProjetActif, $log, $dbh));
				
			// Analyser les éléments avant affichage
			$item->analyserElements();
				
			// Obtenir la langue de l'item
			$langueItem = $quest->getLangueApercuObj();
				
			// Se souvenir de la section crée pour ajout subséquent
			if ($typeItem == 15) {
				$session->set("idItem", $idItem);
				$session->set("typeItem", $item->get("type_item"));
			}
				
			// Régler le gabarit à utiliser
			if ($typeItem == 15) {
				$gabarit = "quest-section.php";
			} else {
				$gabarit = "quest-item-modifier.php";
			}
		}

		// ----------------------------------------------------------------------------------------
		// Modifier le type d'item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_type") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir le type d'item original
			$typeItemOrig = $item->get("type_item");
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);
				
			// Obtenir le nouveau type d'item
			$typeItemNouv = $item->get("type_item");
				
			// Vérifier le nouveau type d'item
			$log->debug("questionnaires.php: Changer le type d'un item de '$typeItemOrig' à '$typeItemNouv'\n");
				
			// Préparer les valeurs intiales
			$item->preparerValeursInitiales($typeItemNouv, $projetActif, $usager);

			// Analyser les éléments avant affichage
			$item->analyserElements();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Modifier le type d'élément pour l'item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_type_element") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Analyser les éléments avant affichage
			$item->analyserElements();
				
			// Changer de type d'éléments 1
			if ($item->get("type_elements1") != $item->get("type_elements1_orig")) {
				$item->changerTypeElements1();
			}

			// Changer de type d'éléments 2
			if ($item->get("type_elements2") != $item->get("type_elements2_orig")) {
				$item->changerTypeElements2();
			}
				
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Modifier le type d'item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_type_element_classeur") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Changer de type d'éléments 1
			if ($item->get("type_elements1") != $item->get("type_elements1_orig")) {
				$item->changerTypeElements1();
			}

			// Changer de type d'éléments 2
			if ($item->get("type_elements2") != $item->get("type_elements2_orig")) {
				$item->changerTypeElements2();
			}

			// Analyser les classeurs
			$item->analyserClasseurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Modifier le type de lacune
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_type_lacune") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Changer de type de lacune
			if ($item->get("type_lacune") != $item->get("type_lacune_orig")) {
				$item->set("type_elements1", $item->get("type_lacune"));
			}

			// Analyser les lacunes
			$item->analyserLacunes($idLacune);

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Ajouter une lacune
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_ajouter_lacune") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);

			// Analyser les lacunes
			$item->analyserLacunes($idLacune);

			// Obtenir l'id de la lacune
			$idLacune = $item->get("nouvelle_lacune");

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"] = 1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "contenu";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer une lacune
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_supprimer_lacunes") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);
				
			// Analyser les lacunes
			$item->analyserLacunes($idLacune, true);
				
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"] = 1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "contenu";
		}

		// ----------------------------------------------------------------------------------------
		// Mettre à jour les lacunes
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_maj_lacunes") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);
				
			// Analyser les lacunes
			$item->analyserLacunes();
				
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"] = 1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "contenu";
		}

		// ----------------------------------------------------------------------------------------
		// Ajouter une réponse à une lacune
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_ajouter_lacune_reponse") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Obtenir la position de l'élément
			$element = Web::getParam("element");

			// Obtenir la lacune et l'index de la réponse
			if (preg_match("/lacune_(\d+?)_reponse_(\d+?)/i", $element, $matches)) {

				$lacune = $matches[1];
				$reponse = $matches[2];

				// Ajouter l'élément
				$item->ajouterReponse($lacune, $reponse);

				// Ouvrir la lacune dans l'éditeur
				$idLacune = "lacune_" . $lacune;
			}
				
			// Analyser les lacunes
			$item->analyserLacunes();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer une réponse à une lacune
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_supprimer_lacune_reponse") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();
				
			// Obtenir la position de l'élément
			$element = Web::getParam("element");
				
			// Obtenir la lacune et l'index de la réponse
			if (preg_match("/lacune_(\d+?)_reponse_(\d+?)/i", $element, $matches)) {

				$lacune = $matches[1];
				$reponse = $matches[2];

				// Supprimer la réponse
				$item->supprimerReponse($lacune, $reponse);
					
				// Ouvrir la lacune dans l'éditeur
				$idLacune = "lacune_" . $lacune;
			}

			// Analyser les lacunes
			$item->analyserLacunes();
				
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Ajouter un element au formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_ajouter_element") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();
				
			// Obtenir la position de l'élément
			$element = Web::getParamNum("element");

			// Ajouter l'élément
			$item->ajouterElement($element);
				
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer un element au formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_supprimer_element") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);
				
			// Obtenir la position de l'élément
			$element = Web::getParamNum("element");

			// Supprimer l'élément
			$item->supprimerElement($element);

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Ajouter une couleur au formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_ajouter_couleur") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Obtenir la position de la couleur ($i)
			$couleur = Web::getParamNum("couleur");
				
			// Ajouter la couleur
			$item->ajouterCouleur($couleur);
				
			// Analyser les couleurs
			$item->analyserCouleurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "couleurs";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer une couleur au formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_supprimer_couleur") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);

			// Obtenir la position de la couleur ($i)
			$couleur = Web::getParamNum("couleur");
				
			// Supprimer la couleur
			$item->supprimerCouleur($couleur);
				
			// Analyser les couleurs
			$item->analyserCouleurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "couleurs";
		}

		// ----------------------------------------------------------------------------------------
		// Ajouter un classeur au formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_ajouter_classeur") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Obtenir la position du classeur ($i)
			$classeur = Web::getParamNum("classeur");

			// Ajouter le classeur
			$item->ajouterClasseurDonnees($classeur);

			// Analyser les classeurs
			$item->analyserClasseurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "classeurs";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer une classeur au formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_supprimer_classeur") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);

			// Obtenir la position du classeur ($i)
			$classeur = Web::getParamNum("classeur");

			// Supprimer la classeur
			$item->supprimerClasseurDonnees($classeur);

			// Analyser les classeurs
			$item->analyserClasseurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "classeurs";
		}

		// ----------------------------------------------------------------------------------------
		// Ajouter un élément à un classeur
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_ajouter_classeur_element") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);

			// Obtenir la position du classeur ($i)
			$idClasseur = Web::getParamNum("classeur");

			// Ajouter un nouvel élément
			$item->ajouterElementAuxDonnees($idClasseur);

			// Analyser les classeurs
			$item->analyserClasseurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "contenu";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer un ou plusieurs éléments à un classeur
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_supprimer_classeur_elements") {
			
			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);

			// Obtenir la position du classeur ($i)
			$idClasseur = Web::getParamNum("classeur");

			// Vérifier si une sélection d'éléments est disponible
			$listeElements = Web::getListeParam("item_selection_");

			if (! empty($listeElements) && $idClasseur != "" ) {
				foreach ($listeElements as $element) {
					$item->deleteByPrefix($element);
				}
			}

			// Analyser les classeurs
			$item->analyserClasseurs();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "contenu";
		}

		// ----------------------------------------------------------------------------------------
		// Supprimer un élément dans un classeur
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_supprimer_classeur_element") {
		
			$pagination = new Pagination(array(), $usager, $log, $dbh);
		
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();
		
			// Obtenir la position du classeur ($i)
			$idClasseur = Web::getParamNum("classeur");
		
			// Obtenir l'élément à supprimer
			$idElement = Web::getParamNum("element");
		
			if ($idClasseur != "" && $idElement != "") {
				$prefixElement = "classeur_" . $idClasseur . "_element_" . $idElement;
				$item->deleteByPrefix($prefixElement);
			}
		
			// Analyser les classeurs
			$item->analyserClasseurs();
		
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"]=1;
		
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
			$section = "contenu";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Changer de section
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_changer_section") {

			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			$pageInfos["flagModifications"] = 1;

			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Modifier un média
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_media") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete($usager);

			// Préparer les champs
			$item->preparerValeursApresChargement($idProjetActif);
				
			// Préparer les valeurs pour le panneau paramètres (valeurs pour cet item seulement)
			$item->preparerValeursPanneauParametres();
				
			// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
			$item->preparerValeursPanneauMessages();
				
			// Analyser les éléments avant affichage
			$item->analyserElements();
			
			// Si le média est vide, vider les coordonnées des zones
			if ($item->get("image") == "") {
				for ($i = 1; $i < NB_MAX_CHOIX_REPONSES; $i++) {
					if ($item->get("reponse_" . $i . "_statut") == "1" ) {
						$item->set("reponse_" . $i . "_coordonnee_x", "");
						$item->set("reponse_" . $i . "_coordonnee_y", "");
					}
				}
			}			
				
			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			if ($demande == "item_modifier_media") {
				$pageInfos["flagModifications"] = 1;
			}
				
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}

		
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_corbeille") {
		
			// Mettre à la corbeille l'item actuel
			$item->desactiver();

			// Vider l'item mis à la corbeille
			$item = new Item($log, $dbh);
			$id_item = "";
			
			// Réafficher la liste des items
			$demande = "questionnaire_modifier";
		}		

		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier") {
			
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Obtenir la liste des formulaires qui utilisent cet item
			$item->getListeQuestionnairesUtilisantItem();
				
			// Obtenir les informations "valeur pour ce questionnaire seulement"
			if ($demandeOrig != "item_apercu") {
				$item->getValeursPourQuestionnaire($idQuest, $idProjetActif);
			}
				
			// Mettre en session le titre du questionnaire et de l'item pour la
			// fenêtre de sélection des fichiers
			$session->set("questionnaire_titre_menu", $quest->get("titre_menu"));
			$session->set("item_titre_menu", $item->get("titre_menu"));

			// Récupérer la langue du questionnaire
			$quest->set("langue_txt", LANGUE::getTitre($quest->get("id_langue_questionnaire"), $idProjetActif, $log, $dbh));
				
			// Obtenir la langue de l'item
			$langueItem = $quest->getLangueApercuObj();

			// Préparer les valeurs pour le panneau paramètres (valeurs pour cet item seulement)
			$item->preparerValeursPanneauParametres();

			// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
			$item->preparerValeursPanneauMessages();
				
			// Analyser les éléments avant affichage
			$item->analyserElements();
				
			// Vérifier l'état du questionnaire publié
			$quest->verifierQuestionnairePublie($projetActif);
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire"), TXT_PREFIX_ITEM . $item->get("id_item"));
				
			// Régler le gabarit à utiliser
			if ($item->get("type_item") == 15) {
				$gabarit = "quest-section.php";
			} else {
				$gabarit = "quest-item-modifier.php";
			}
				
			// Régler la demande de retour
			$demandeRetour = "item_modifier";
		}

		// ----------------------------------------------------------------------------------------
		// Sauvegarder un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_sauvegarder") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Déterminer si un changement de questionnaire a été demandé
			$pageDest = Web::getParamNum("pagination_page_dest");
				
			if ( $pageDest != "") {

				// Obtenir la page de destination
				$idQuest = $quest->getIdQuestionnaireParPage($pageDest);

				// Obtenir le questionnaire correspondant
				$quest->getQuestionnaireParId($idQuest, $idProjetActif);

				// Ne pas sélectionner d'item
				$idItem = "";
				$item = new Item($log, $dbh);

				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);

				// Afficher la page par défaut pour modifier un questionnaire
				$demande = "questionnaire_modifier";
			} else {

				if (!$selectionItems) {

					// Obtenir les informations à partir de la requête
					$item->getDonneesRequete($usager);
						
					// Ajouter l'id du questionnaire et projet pour l'indexation par questionnaire
					$item->set("id_questionnaire", $idQuest);
					$item->set("id_projet", $idProjetActif);
						
					// Au besoin, enregistrer une nouvelle catégorie
					$item->enregistrerNouvelleCategorie();

					// Rafraîchir la liste des categories
					$listeCategorie = $categorie->getListeCategories($idProjetActif);
						
					// Enregistrer les informations d'un item existant
					$item->enregistrer();
						
					// Obtenir l'id de l'item
					$idItem = $item->get("id_item");
						
					// Mettre à jour le nombre d'items pour le questionnaire
					$quest->updateNombreItems($idQuest, $idProjetActif);
						
					// Charger les données à nouveau pour obtenir le bon ordre des éléments
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $idItem);
						
					// Obtenir les informations "valeur pour ce questionnaire seulement
					$item->getValeursPourQuestionnaire($idQuest, $idProjetActif);
						
					// Récupérer la langue du questionnaire
					$quest->set("langue_txt", LANGUE::getTitre($quest->get("id_langue_questionnaire"), $idProjetActif, $log, $dbh));
						
					// Message de confirmation
					$log->debug("questionnaires.php: Sauvegarde de l'item complétée");
					$messages = new Messages(MSG_001, Messages::CONFIRMATION);
						
				}

				// Préparer les valeurs
				$item->preparerValeursApresChargement($idProjetActif);
					
				// Préparer les valeurs pour le panneau paramètres (valeurs pour cet item seulement)
				$item->preparerValeursPanneauParametres();

				// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
				$item->preparerValeursPanneauMessages();

				// Analyser les éléments avant affichage
				$item->analyserElements();
					
				// Obtenir la langue de l'item
				$langueItem = $quest->getLangueApercuObj();

				// Régler le gabarit à utiliser
				if ($item->get("type_item") == 15) {
					$gabarit = "quest-section.php";
				} else {
					$gabarit = "quest-item-modifier.php";
				}
			}

			// Régler la demande de retour
			$demandeRetour = "item_modifier";
				
			// Obtenir la liste des formulaires qui utilisent cet item
			$item->getListeQuestionnairesUtilisantItem();
		}


		// ----------------------------------------------------------------------------------------
		// Impression d'un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_imprimer") {

			// Obtenir les données
			$item->getDonneesRequete();
				
			// Obtenir le contenu de l'item pour impression
			$contenu = $item->imprimer($quest);
				
			// Obtenir le gabarit à utiliser pour l'impression
			$gabarit = $item->get("gabarit_impression");
		}


		// ----------------------------------------------------------------------------------------
		// Dupliquer un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_dupliquer") {
				
			$pagination = new Pagination(array(), $usager, $log, $dbh);
				
			// Modifier certaines valeurs avant de dupliquer
			$item->set("id_questionnaire", $idQuest);
			$item->set("id_item", "");
			$item->set("id_projet", $idProjetActif);
			$titre = TXT_PREFIX_DUPLIQUER . $item->get("titre");
			$item->set('titre', $titre);

			// Ajouter un nouvel item
			$item->ajouter();

			// Analyser les éléments
			$item->analyserElements();
				
			// Mettre à jour le numéro d'item courant
			$idItem = $item->get("id_item");
				
			// Ajouter l'item au bon endroit dans le menu
			$menu = new Menu($log, $dbh);
			$posItem = $menu->insererItem($idItem, $typeItem, $idQuest, $idProjetActif);
				
			// Mettre à jour le nombre d'items pour le questionnaire
			$quest->updateNombreItems($idQuest, $idProjetActif);

			// Obtenir la langue de l'item
			$langueItem = $quest->getLangueApercuObj();
			
			// Déterminer le gabarit
			if ($item->get("type_item") == '15') {
				// Régler le gabarit à utiliser
				$gabarit = "quest-section.php";
			} else {
				// Régler le gabarit à utiliser
				$gabarit = "quest-item-modifier.php";
			}
		}


		// ----------------------------------------------------------------------------------------
		// Activer ou désactiver le suivi sur un item (requête AJAX) selon l'état actuel
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_suivi") {

			if ($item->get("suivi") == "1") {
				// Désactiver le suivi
				$item->desactiverSuivi();
				echo "0";
			} else {
				// Activer le suivi
				$item->activerSuivi();
				echo "1";
			}
		}

		// ----------------------------------------------------------------------------------------
		// Activer le suivi sur un item (requête AJAX)
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_suivi_activer") {

			// Activer le suivi
			$item->activerSuivi();
			echo "1";
		}


		// ----------------------------------------------------------------------------------------
		// Supprimer un item du questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_supprimer") {

			$menu = new Menu($log, $dbh);
				
			// S'il s'agit d'une section, supprimer d'abord les items contenus dans la section
			if ($item->get("type_item") == '15') {
				$item->supprimerItemsSection($idItem, $idQuest, $idProjetActif);
			}

			// Supprimer l'item
			$item->supprimerItemQuestionnaire($idItem, $idQuest, $idProjetActif);

			// Mettre à jour le nombre d'items pour le questionnaire
			$quest->updateNombreItems($idQuest, $idProjetActif);
				
			// Après suppression, rediriger l'usager à la page de modifications du questionnaire
			$demande = "questionnaire_modifier";
			$item->set("id_item", "");
		}

			
		// ----------------------------------------------------------------------------------------
		// Activer le suivi sur un item dans la liste
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_selectionner_suivi_activer") {

			// Activer le suivi
			$item->activerSuivi();
			$demande = "items_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Désactiver le suivi sur un item dans la liste
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_selectionner_suivi_desactiver") {

			// Activer le suivi
			$item->desactiverSuivi();
			$demande = "items_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les items à sélectionner
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_selectionner_recherche") {
				
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
				
			// Mettre en session
			$session->set("itemsselect_recherche_chaine", $chaine);
				
			// Afficher la liste des questionnaires correspondants
			$demande = "items_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche des items à sélectionner
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_selectionner_recherche_initialiser") {
				
			// Mettre en session
			$session->set("itemsselect_recherche_chaine", "");
				
			// Afficher la liste des questionnaires correspondants
			$demande = "items_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Sauvegarder les items sélectionnés
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_selectionner_sauvegarder") {

			$listeItemsRejetes = 0;
			$listeItemsAcceptes = 0;
				
			// Rediriger l'utilisateur vers le 1er item ajouté
			$idItemDest = 0;

			// Obtenir la liste des items pour un questionnaire
			$listeItems = $quest->getListeItems();
				
			// Vérifier si une sélection d'items est disponible
			$listeElements = array_reverse(Web::getListeParam("items_selection_"));
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {

					// Vérifier si on peut ajouter l'item (pas déjà dans le questionnaire)
					if ( !in_array($element, $listeItems) ) {

						$listeItemsAcceptes++;

						// Prendre note du dernier item traité
						$idItemDest = $element;

						// Instancier l'item
						$itemFactory = new Item($log, $dbh);
						$item = $itemFactory->instancierItemParType('', $idProjetActif, $element);

						// Ajouter l'item au questionnaire
						$item->set("id_questionnaire", $idQuest);
						$item->set("id_projet", $idProjetActif);
						$item->ajouterLienQuestionnaireItem();

						// Mettre à jour le nombre de liens pour l'item
						$item->updateLiensItem();

						// Ajouter l'item au bon endroit dans le menu
						$menu = new Menu($log, $dbh);
						$menu->insererItem($item->get("id_item"), $item->get("type_item"), $idQuest, $idProjetActif);
					} else {
						// Item rejeté
						$listeItemsRejetes++;
						$log->debug("questionnaires.php: élément '$element' rejeté");
					}
				}

				// Mettre à jour le nombre d'items dans le questionnaire
				$quest->updateNombreItems($idQuest, $idProjetActif);

				// Ajouter la liste des éléments rejetés à la session
				if ( ($listeItemsAcceptes == 0) && ($listeItemsRejetes > 0) ) {
					// Aucun accepté, tous rejetés
					$session->set("message_erreur", ERR_020);
				} elseif ( ($listeItemsAcceptes > 0) && ($listeItemsRejetes > 0) ) {
					// Mixe acceptés et rejetés
					$session->set("message_erreur", ERR_019);
				} else {
					if ($listeItemsAcceptes == 1) {
						$messages = new Messages(MSG_014, Messages::CONFIRMATION);
					} else {
						$messages = new Messages(MSG_004, Messages::CONFIRMATION);
					}
				}
			}
				
			// Réafficher la liste des items
			$gabarit = "quest-items-selectionner-fermer.php";
		}

		// ----------------------------------------------------------------------------------------
		// Sélectionner un ou plusieurs items
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_selectionner") {

			$listeItems = array();

			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "items_selectionnner" || $demandeOrig == "items_selectionner_recherche") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
				
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdItem = array();
			if ($session->get("itemsselect_recherche_chaine") == "") {
				$listeIdItem = $item->getListeItems($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("itemsselect_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdItem = $item->rechercheItems($chaine, $idProjetActif, "1", $log, $dbh);
			}
				
			// Appliquer la pagination
			$pagination = new Pagination($listeIdItem, $usager, $log, $dbh, "popup");
				
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {

					$idItem = $listeIdItem[$i];
						
					// Obtenir les informations de cet item
					$element = new Item($log, $dbh);
					$element->getItemParId($idItem, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_ITEM . $element->get("id_item"));
					$element->getListeQuestionnairesUtilisantItem();
						
					// Ajouter aux résultats de recherche
					array_push($listeItems, $element);
				}
			}
				
			// Préparer affichage de la liste
			$item->preparerAffichageListe();
				
			// Obtenir la liste des types d'items
			$listeTypesItems = $item->getListeTypesItemsTrieParType();
				
			// Régler le gabarit à utiliser
			$gabarit = "quest-items-selectionner.php";
		}

		// ----------------------------------------------------------------------------------------

		//                             F I N   Q U E S T I O N N A I R E

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour modifier la fin d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "fin_modifier") {

			//Régler l'item à accueil pour le menu
			$item->set("id_item", "fin_questionnaire");
				
			// Vérifier l'état du questionnaire publié
			$quest->verifierQuestionnairePublie($projetActif);

			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire"), TXT_PREFIX_ITEM . $item->get("id_item"));
			
			// Régler le gabarit à utiliser et la demande de retour
			$demandeRetour = "fin_modifier";
			$gabarit = "quest-fin.php";
		}

		// ----------------------------------------------------------------------------------------
		// Sauvegarder les modifications à la page de fin
		// ----------------------------------------------------------------------------------------
		if ($demande == "fin_sauvegarder") {

			if (! $selectionItems) {

				// Obtenir les informations à partir de la requête
				$quest->getDonneesRequete();
					
				// Mettre à jour un questionnaire existant
				$quest->enregistrer();
					
				// Message de confirmation
				$log->debug("questionnaires.php: Fin Sauvegarde complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);

				//Régler l'item à accueil pour le menu
				$item->set("id_item", "fin_questionnaire");
			}
				
			// Régler le gabarit à utiliser
			$gabarit = "quest-fin.php";
		}

		// ----------------------------------------------------------------------------------------

		//                                      M E N U

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Mettre à jour le menu
		// ----------------------------------------------------------------------------------------
		if ($demande == "menu_modifier") {
				
			// Obtenir les informations sur l'ordre des éléments du menu
			$elements = Web::getParam('menu');
				
			// Modifier l'ordre dans la BD
			$menu = new Menu($log, $dbh);
			$menu->enregistrerOrdreItems($idProjetActif, $idQuest, $elements);
				
			// Code de retour
			echo "1";
		}


		// ----------------------------------------------------------------------------------------

		//                               Q U E S T I O N N A I R E S

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les questionnaires
		// ----------------------------------------------------------------------------------------
		if ($demande == "recherche") {
				
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
				
			// Mettre en session
			$session->set("recherche_chaine", $chaine);
				
			// Afficher la liste des questionnaires correspondants
			$demande = "liste";
		}

		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche
		// ----------------------------------------------------------------------------------------
		if ($demande == "recherche_initialiser") {
				
			// Mettre en session
			$session->set("recherche_chaine", "");
				
			// Afficher la liste des questionnaires correspondants
			$demande = "liste";
		}

		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les questionnaires
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_selectionner_recherche") {
				
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
				
			// Mettre en session
			$session->set("questionnaire_selectionner_recherche_chaine", $chaine);
				
			// Afficher la liste des questionnaires correspondants
			$demande = "questionnaire_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_selectionner_recherche_initialiser") {
				
			// Mettre en session
			$session->set("questionnaire_selectionner_recherche_chaine", "");
				
			// Afficher la liste des questionnaires correspondants
			$demande = "questionnaire_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Activer le suivi sur un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_selectionner_suivi_activer") {

			// Activer ou désactiver le suivi sur le questionnaire actuel
			$quest->activerSuivi();
				
			// Réafficher la liste des questionnaires
			$demande = "questionnaire_selectionner";
		}


		// ----------------------------------------------------------------------------------------
		// Désactiver le suivi sur un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_selectionner_suivi_desactiver") {

			// Désactiver le suivi
			$quest->desactiverSuivi();
				
			// Réafficher la liste des questionnaires
			$demande = "questionnaire_selectionner";
		}

		// ----------------------------------------------------------------------------------------
		// Afficher la fenêtre pour choisir un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_selectionner") {

			$listeQuestionnaires = array();
				
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "questionnaire_selectionner") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
				
			// Obtenir la liste des questionnaires
			$questListe = new Questionnaire($log, $dbh);
				
			// Déterminer si on affiche la liste des questionnaires
			if ($session->get("questionnaire_selectionner_recherche_chaine") == "") {
				$listeIdQuestionnaire = $questListe->getListeQuestionnaire($idProjetActif);
			} else {
				$chaine = $session->get("questionnaire_selectionner_recherche_chaine");
				$listeIdQuestionnaire = $questListe->recherche($chaine, $idProjetActif);
			}

			// Appliquer la pagination
			$pagination = new Pagination($listeIdQuestionnaire, $usager, $log, $dbh, "popup");
				
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {

					$idQuest = $listeIdQuestionnaire[$i];
						
					// Obtenir les informations de ce questionnaire
					$q = new Questionnaire($log, $dbh);
					$q->getQuestionnaireParId($idQuest, $idProjetActif);
						
					// Obtenir le libellé de la collection
					if (isset($listeCollection[$q->get("id_collection")])) {
						$q->set("collection", $listeCollection[$q->get("id_collection")]);
					} else {
						$q->set("collection", "");
					}
						
					// Obtenir le nombre d'items
					//$nbItems = $q->getNombreItems($idQuest, $idProjetActif);
					//$q->set("nombreItems", $nbItems);
						
					// Ajouter aux résultats de recherche
					array_push($listeQuestionnaires, $q);
				}
			}
				
			// Préparer l'affichage de la page
			$quest->preparerListeQuestionnaire();
				
			// Régler le gabarit à utiliser
			$gabarit = "biblio-items-quest-selectionner.php";
		}

		// ----------------------------------------------------------------------------------------
		// Afficher les messages d'erreurs reliés à l'apercu
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_apercu_messages") {
				
			// Obtenir les messages à partir de la session et les mettre dans le questionnaire
			$titre = $session->get("questionnaire_apercu_titre");
			$idQuestMsg = $session->get("questionnaire_id_questionnaire");
			$msgEntete = $session->get("questionnaire_apercu_messages_entete");
			$msgDetails = $session->get("questionnaire_apercu_messages_details");
				
			$quest->set("questionnaire_id_questionnaire", $idQuestMsg);
			$quest->set("questionnaire_apercu_titre", $titre);
			$quest->set("questionnaire_apercu_messages_entete", $msgEntete);
			$quest->set("questionnaire_apercu_messages_details", $msgDetails);
				
			$session->delete("questionnaire_id_questionnaire");
			$session->delete("questionnaire_apercu_titre");
			$session->delete("questionnaire_apercu_messages_entete");
			$session->delete("questionnaire_apercu_messages_details");

			// Régler le gabarit
			$gabarit = "validation/questionnaire-page.php";
		}

		// ----------------------------------------------------------------------------------------
		// Préparer la publication d'un questionnaire - Afficher le formulaire et publication
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_publier_formulaire" || $demande == "questionnaire_publier_envoi") {
				
			$succes = 0;
				
			// Déterminer le nom de répertoire pour l'utilisateur et le répertoire de publication
			$pageInfos['repertoire_publication'] = Web::getParam("repertoire_publication");
				
			// Vérifier si le nom du répertoire est disponible via le questionnaire dans le cas d'une re-publication
			if ($pageInfos['repertoire_publication'] == "") {
				if ($quest->get("publication_repertoire") != "") {
					$pageInfos['repertoire_publication'] = $quest->get("publication_repertoire");
				}
			}
				
			if ($demande == "questionnaire_publier_envoi") {

				// Vérifier le nom du répertoire de publication (ne doit pas contenir de caractères spéciaux et maximum X caractères)
				if ($pageInfos['repertoire_publication'] == Securite::nettoyerNomfichier($pageInfos['repertoire_publication']) &&
				strlen($pageInfos['repertoire_publication']) < SECURITE_LONGMAX_REPERTOIRE &&
				$pageInfos['repertoire_publication'] != "" &&
				$pageInfos['repertoire_publication'] != "apercu") {

					if ($quest->publier($projetActif, $pageInfos['repertoire_publication'])) {
						$succes = 1;
					} else {
						$log->debug("questionnaires.php Erreur lors de la publication du questionnaire - répertoire de destination déjà utilisé?");
						$messages = new Messages(ERR_040, Messages::ERREUR);
					}

				} else {
					$log->debug("questionnaires.php Le nom de répertoire pour la publication n'est pas valide");
					$messages = new Messages(ERR_039, Messages::ERREUR);
				}
			}
				
			// Régler le gabarit
			if ($succes) {
				$gabarit = "quest-publier-confirmer.php";
			} else {
				$gabarit = "quest-publier-formulaire.php";
			}
		}

		// ----------------------------------------------------------------------------------------
		// Voir le questionnaire publié
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_publier_voir") {

			// Redirection pour obtenir le chemin valide
			$url = URL_PUBLICATION . $pageInfos['repertoire_projet'] . $quest->get("publication_repertoire") . "/";
			header("Location: $url");
		}

		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour ajouter un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_ajouter") {
				
			// Créer un questionnaire avec les informations de base
			$quest->set("titre", TXT_NOUVEAU_QUESTIONNAIRE);
			$quest->set("id_projet", $idProjetActif);
			$quest->set("theme",FICHIER_THEME_DEFAUT);
			//$item->set("id_langue_questionnaire", $usager->get("langue_publication"));
			$quest->set("id_langue_questionnaire", "1");
			$quest->ajouter();

			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
				
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh);
			$listeLangues = $lang->getListeLangues($idProjetActif);

			// Obtenir le nombre de section dans le questionnaire
			$quest->set("nbSections", $quest->getNombreSections($idQuest, $idProjetActif));

			// Mettre à jour la liste des questionnaires
			$listeIdQuest = $quest->getListeQuestionnaire($idProjetActif);
			
			// Obtenir la liste des termes
			$listeTermes = array();
			$listeIdTermes = $terme->getListeIdTermesDuProjet($idProjetActif, "terme");
			
			foreach ($listeIdTermes as $idElement) {
			
				// Obtenir les informations de ce terme
				$element = new Terme($log, $dbh);
				$element->getTermeParId($idElement, $idProjetActif);
				$element->set("id_prefix", TXT_PREFIX_TERME . $element->get("id_collection"));
			
				// Ajouter aux résultats de recherche
				array_push($listeTermes, $element);
			}			
			
			// Régler le gabarit à utiliser
			$gabarit = "quest-modifier.php";
		}
			
		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour modifier un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_modifier") {
				
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
				
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh);
			$listeLangues = $lang->getListeLangues($idProjetActif);
				
			// Obtenir le nombre de section dans le questionnaire
			$quest->set("nbSections", $quest->getNombreSections($idQuest, $idProjetActif));
				
			// Vérifier l'état du questionnaire publié
			$quest->verifierQuestionnairePublie($projetActif);
				
			// Vérifier si le thème sélectionné est présent
			if (! $quest->verifierThemeSelectionne() ) {
				$messages = new Messages(ERR_172, Messages::ERREUR);
			}

			// Obtenir la liste des termes
			$listeTermes = array();
			$listeIdTermes = $terme->getListeIdTermesDuProjet($idProjetActif, "terme");

			foreach ($listeIdTermes as $idElement) {

				// Obtenir les informations de ce terme
				$element = new Terme($log, $dbh);
				$element->getTermeParId($idElement, $idProjetActif);
				$element->set("id_prefix", TXT_PREFIX_TERME . $element->get("id_collection"));
		
				// Ajouter aux résultats de recherche
				array_push($listeTermes, $element);
			}
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire"), TXT_PREFIX_ITEM . $item->get("id_item"));
			
			// Régler le gabarit à utiliser et url de retour
			$demandeRetour = "questionnaire_modifier";
			$gabarit = "quest-modifier.php";
		}

		// ----------------------------------------------------------------------------------------
		// Sauvegarder les données d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_sauvegarder") {

			if (!$selectionItems) {
					
				// Obtenir les informations à partir de la requête
				$quest->getDonneesRequete();

				// Au besoin, enregistrer une nouvelle collection
				$quest->enregistrerNouvelleCollection();

				// Rafraîchir la liste des collections
				$listeCollection = $collection->getListeCollections($idProjetActif);
					
				// Mettre à jour un questionnaire existant
				$quest->enregistrer();

				// Message de confirmation
				$log->debug("questionnaires.php: Sauvegarde du questionnaire '" . $quest->get("id_questionnaire") . "' complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
			}

			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
				
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh);
			$listeLangues = $lang->getListeLangues($idProjetActif);
				
			// Obtenir le nombre de section dans le questionnaire
			$quest->set("nbSections", $quest->getNombreSections($idQuest, $idProjetActif));

			// Obtenir la liste des termes pour afficher la liste complète
			$listeTermes = array();
			$listeIdTermes = $terme->getListeIdTermesDuProjet($idProjetActif, "terme");
			
			foreach ($listeIdTermes as $idElement) {
			
				// Obtenir les informations de ce terme
				$element = new Terme($log, $dbh);
				$element->getTermeParId($idElement, $idProjetActif);
				$element->set("id_prefix", TXT_PREFIX_TERME . $element->get("id_collection"));
			
				// Ajouter aux résultats de recherche
				array_push($listeTermes, $element);
			}
			
			// Régler le gabarit à utiliser et url de retour
			$demandeRetour = "questionnaire_modifier";
			$gabarit = "quest-modifier.php";
		}

		// ----------------------------------------------------------------------------------------
		// Imprimer un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_imprimer") {
				
			// Obtenir les données
			$quest->getDonneesRequete();
				
			// Obtenir la liste des items pour impression
			$listeItems = $quest->getListeItems();

			// Régler le gabarit à utiliser
			$gabarit = IMPRESSION_GABARIT_QUESTIONNAIRE;
		}

		// ----------------------------------------------------------------------------------------
		// Imprimer l'accueil d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_accueil_imprimer") {
				
			// Obtenir les données
			$quest->getDonneesRequete();
				
			// Régler le gabarit à utiliser
			$gabarit = IMPRESSION_GABARIT_QUESTIONNAIRE_ACCUEIL;
		}

		// ----------------------------------------------------------------------------------------
		// Imprimer la fin d'un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_fin_imprimer") {
				
			// Obtenir les données
			$quest->getDonneesRequete();
				
			// Régler le gabarit à utiliser
			$gabarit = IMPRESSION_GABARIT_QUESTIONNAIRE_FIN;
		}

		// ----------------------------------------------------------------------------------------
		// Activer ou désactiver le suivi sur un questionnaire (requête AJAX)
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_suivi") {
				
			if ($quest->get("suivi") == "1") {
				// Désactiver le suivi
				$quest->desactiverSuivi();
				echo "0";
			} else {
				// Activer le suivi
				$quest->activerSuivi();
				echo "1";
			}
		}

		// ----------------------------------------------------------------------------------------
		// Activer ou désactiver le suivi sur un questionnaire (requête AJAX)
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_suivi_activer_ajax") {
				
			// Activer le suivi
			$quest->activerSuivi();
			echo "1";

		}

		// ----------------------------------------------------------------------------------------
		// Activer le suivi sur un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_suivi_activer") {

			// Vérifier si une sélection de questionnaire est transmises
			$listeQuest = Web::getListeParamGet("questionnaires_selection_");
			if (! empty($listeQuest)) {
				foreach ($listeQuest as $questId) {
					$q = new Questionnaire($log, $dbh);
					$q->getQuestionnaireParId($questId, $idProjetActif);
					$q->activerSuivi();
					$q->enregistrer();
				}
			} else {
					
				// Activer ou désactiver le suivi sur le questionnaire actuel
				if ($quest->get("suivi") == "1") {
					$quest->desactiverSuivi();
				} else {
					$quest->activerSuivi();
				}
			}
				
			// Réafficher la liste des questionnaires
			$demande = "liste";
		}

		// ----------------------------------------------------------------------------------------
		// Désactiver le suivi sur un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_suivi_desactiver") {
				
			// Désactiver le suivi
			$quest->desactiverSuivi();
				
			// Réafficher la liste des questionnaires
			$demande = "liste";
		}

		// ----------------------------------------------------------------------------------------
		// Dupliquer un questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_dupliquer") {

			// Obtenir le(s) formulaire(s) à dupliquer
			$listeQuest = Web::getListeParamGet("questionnaires_selection_");
			if (! empty($listeQuest) ) {
				foreach ($listeQuest as $questId) {
					$q = new Questionnaire($log, $dbh);
					$q->getQuestionnaireParId($questId, $idProjetActif);
					$q->dupliquer();
				}
			} else {
				// Dupliquer le questionnaire courant
				$quest->dupliquer();
			}

			// Réafficher la liste des questionnaires
			$demande = "liste";
		}

		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille
		// ----------------------------------------------------------------------------------------
		if ($demande == "questionnaire_corbeille") {

			// Obtenir le(s) formulaire(s) à dupliquer
			$listeQuest = Web::getListeParamGet("questionnaires_selection_");
			if (! empty($listeQuest)) {

				foreach ($listeQuest as $questId) {
					$q = new Questionnaire($log, $dbh);
					$q->getQuestionnaireParId($questId, $idProjetActif);
					$q->desactiver($questId, $projetActif);
				}
			} else {
				// Désactiver le questionnaire courant
				$quest->desactiver($idQuest, $projetActif);
			}
				
			// Réafficher la liste des questionnaires
			$demande = "liste";
		}

		// ----------------------------------------------------------------------------------------
		// Afficher la liste des questionnaires
		// ----------------------------------------------------------------------------------------
		if ($demande == "liste") {
				
			$listeQuestionnaires = array();
				
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "liste") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
				
			// Obtenir la liste des questionnaires
			$questListe = new Questionnaire($log, $dbh);
				
			// Déterminer si on affiche la liste des questionnaires
			if ($session->get("recherche_chaine") == "") {
				$listeIdQuestionnaire = $questListe->getListeQuestionnaire($idProjetActif);
			} else {
				$chaine = $session->get("recherche_chaine");
				$listeIdQuestionnaire = $questListe->recherche($chaine, $idProjetActif);
			}

			// Appliquer la pagination
			$pagination = new Pagination($listeIdQuestionnaire, $usager, $log, $dbh);
				
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {

					$idQuest = $listeIdQuestionnaire[$i];
						
					// Obtenir les informations de ce questionnaire
					$q = new Questionnaire($log, $dbh);
					$q->getQuestionnaireParId($idQuest, $idProjetActif);
						
					// Obtenir le libellé de la collection
					if (isset($listeCollection[$q->get("id_collection")])) {
						$q->set("collection", $listeCollection[$q->get("id_collection")]);
					} else {
						$q->set("collection", "");
					}
						
					// Ajouter aux résultats de recherche
					array_push($listeQuestionnaires, $q);
				}
			}
				
			// Préparer l'affichage de la page
			$quest->preparerListeQuestionnaire();
				
			// Régler le gabarit à utiliser
			$gabarit = "quest-liste.php";
		}

		// ----------------------------------------------------------------------------------------

		//                                       T H È M E

		// ----------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------
		// Afficher l'apercu d'un thème
		// ----------------------------------------------------------------------------------------
		if ($demande == "theme_apercu") {
				
			// Déterminer le fichier à utiliser
			$theme = Web::getParam("theme");
			$theme = Securite::nettoyerNomfichier($theme);
				
			$fichier = REPERTOIRE_THEMES . $theme . "/" . REPERTOIRE_PREFIX_THEME . "/" . FICHIER_THEME_APERCU;
			$log->debug("questionnaires.php Chargement de l'aperçu du thème '$fichier'");
				
			// Obtenir le type de fichier
			$mimeType = Fichiers::get_mime_type($fichier);

			// Obtenir le contenu du fichier
			$data = file_get_contents($fichier);

			// Envoi
			header("Content-type: image/jpeg");
			print $data;
		}

		// ----------------------------------------------------------------------------------------

		//                                 I M P O R T A T I O N

		// ----------------------------------------------------------------------------------------


		// ----------------------------------------------------------------------------------------
		// Formulaire pour importer du XML
		// ----------------------------------------------------------------------------------------
		if ($demande == "elements_importer_formulaire") {

			// Vérifier si le projet a un répertoire (obligatoire)
			if ($projetActif->get("repertoire") == "") {
				Erreur::erreurFatal('018', "questionnaires.php - L'identifiant unique du projet '" . $projetActif->get("id_projet") . "' est absent.  Impossible d'importer.", $log);
			}
			
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh);
			$listeLangues = $lang->getListeLangues($idProjetActif);

			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
				
			// Régler le gabarit à utiliser
			$gabarit = "quest-elements-importer-formulaire.php";
		}

		// ----------------------------------------------------------------------------------------
		// Réception et traitement du fichier XML
		// ----------------------------------------------------------------------------------------
		if ($demande == "elements_importer_envoi") {

			$importation = new Importation($usager, $projetActif, $log, $dbh);
			$messages = $importation->importerXML();
			
			// Conserver les messages en session
			$session->set("importation_messages", $messages);

			// Redirection pour l'affichage des résultats
			$url = "questionnaires.php?demande=elements_importer_resultats";
			header("Location: $url");
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Afficher les résultats
		// ----------------------------------------------------------------------------------------
		if ($demande == "elements_importer_resultats") {

			// Obtenir le message de la session
			$messages = $session->get("importation_messages");
			
			// Message par défaut
			if ($messages == "") {
				$messages = TXT_AUCUNE_INFORMATION;
			}
		
			// Régler le gabarit à utiliser
			$gabarit = "quest-elements-importer-resultats.php";
		}
		
			
		// ----------------------------------------------------------------------------------------
		
		//                            G E S T I O N    S E S S I O N 
		
		// ----------------------------------------------------------------------------------------
				
		// ----------------------------------------------------------------------------------------
		// Vérifier si la session existe
		// ----------------------------------------------------------------------------------------
		if ($demande == "session_verifier") {

			// Initialisation
			$verrou = new Verrou($log, $dbh);
			$idProjetCour = $session->get("idProjetCourant");
			$idUsagerCour = $session->get("idUsager");
			
			// Vérifier si la session est active
			$isSessionActive = $session->isSessionActive();

			// Obtenir l'élément en édition
			$verrouIdProjet = Web::getParam("verrou_id_projet");
			$verrouIdElement1 = Web::getParam("verrou_id_element1");
			$verrouIdElement2 = Web::getParam("verrou_id_element2");
			$log->debug("questionnaires.php: Verrou idProjet : '$verrouIdProjet' idElement1 : '$verrouIdElement1' idElement2 : '$verrouIdElement2'");
			
			// Vérifier si les éléments sont verrouillés et obtenir la liste des personnes qui ont un verrou
			$listePersonnes = "";
			if ($isSessionActive) {
				$listePersonnes1 =  $verrou->isElementVerrouilleAutrePersonne($idUsagerCour, $verrouIdProjet, $verrouIdElement1);
				$listePersonnes2 =  $verrou->isElementVerrouilleAutrePersonne($idUsagerCour, $verrouIdProjet, $verrouIdElement2);
				$listePersonnes = implode(",",array_unique(array_merge($listePersonnes1, $listePersonnes2)));
			}
		
			// Vérifier la session est active et si c'est le projet courant
			if ($isSessionActive == 0 && ($verrouIdProjet == $idProjetCour || $verrouIdProjet == "0") ) {

				if ($verrouIdElement1 != "") {
					$verrou->ajouterVerrou($idUsagerCour, $verrouIdProjet, $verrouIdElement1);
					$log->debug("questionnaires.php:Appliqué verrou : Projet = '$idProjetCour' verrouIdProjet = '$verrouIdProjet' verrouIdElement1 : '$verrouIdElement1'");
				}
				
				if ($verrouIdElement2 != "") {
					$verrou->ajouterVerrou($idUsagerCour, $verrouIdProjet, $verrouIdElement2);
					$log->debug("questionnaires.php:Appliqué verrou : Projet = '$idProjetCour' verrouIdProjet = '$verrouIdProjet' verrouIdElement2 : '$verrouIdElement2'");
				}
				
			} else {
				$log->debug("questionnaires.php: Impossible d'appliquer un verrou");
			}
			
			// Envoi des informations sur les verrous
			print json_encode(array("sessionActive" => $isSessionActive, "verrouListe" => $listePersonnes));
			
		}	

		// ----------------------------------------------------------------------------------------
		// Afficher message session expirée
		// ----------------------------------------------------------------------------------------
		if ($demande == "session_message") {
			
			// Déterminer le message a afficher
			$sessionStatut = $session->isSessionActive();
			
			// Régler le gabarit à utiliser
			$gabarit = "message-session.php";				
		}		
		

		// ----------------------------------------------------------------------------------------

		//                                       A I D E

		// ----------------------------------------------------------------------------------------


		// ----------------------------------------------------------------------------------------
		// Aide - À propos
		// ----------------------------------------------------------------------------------------
		if ($demande == "aide_apropos_intro") {
				
			// Régler le gabarit à utiliser
			$gabarit = "aide/apropos-intro.php";
		}

		// ----------------------------------------------------------------------------------------
		// Aide - Droits
		// ----------------------------------------------------------------------------------------
		if ($demande == "aide_apropos_droits") {

			// Régler le gabarit à utiliser
			$gabarit = "aide/apropos-droits.php";
		}

		// ----------------------------------------------------------------------------------------
		// Aide - Générique
		// ----------------------------------------------------------------------------------------
		if ($demande == "aide_apropos_generique") {

			// Régler le gabarit à utiliser
			$gabarit = "aide/apropos-generique.php";
		}

		// ----------------------------------------------------------------------------------------
		// Aide - Commentaires
		// ----------------------------------------------------------------------------------------
		if ($demande == "aide_apropos_commentaires") {

			// Régler le gabarit à utiliser
			$gabarit = "aide/apropos-commentaires.php";
		}
		
		// ----------------------------------------------------------------------------------------
		// Préparer les options (select) pour le questionnaire, items et termes
		// ----------------------------------------------------------------------------------------
		$quest->preparerAffichage();
		if ($idItem != "" || $typeItem != "") {
			$item->preparerAffichage();
		}

		// ----------------------------------------------------------------------------------------
		// Conserver les informations sur le questionnaire et item traités
		// ----------------------------------------------------------------------------------------
		$session = new Session;
		$session->set("idQuest", $idQuest);

		if ($demandeOrig == "item_modifier" ||
		$demandeOrig == "accueil_modifier" ||
		$demandeOrig == "fin_modifier" ) {
			$session->set("idItem", $idItem);
			$session->set("typeItem", $item->get("type_item"));
		}

		// ----------------------------------------------------------------------------------------
		// Préparer les chaînes de recherche
		// ----------------------------------------------------------------------------------------
		$chaineRech = $session->get("recherche_chaine");
		$chaineRechCorbeille = $session->get("corbeille_recherche_chaine");
		$chaineRechItemsSel = $session->get("itemsselect_recherche_chaine");
		$chaineRechQuestSel = $session->get("questionnaire_selectionner_recherche_chaine");

		// ----------------------------------------------------------------------------------------
		// Préparer le menu
		// ----------------------------------------------------------------------------------------
		if ($quest->get("id_questionnaire") != "" ) {
			$menuObj = new Menu($log, $dbh);
			$menu = $menuObj->getMenu($quest->get("id_questionnaire"), $idProjetActif);
		}

		// ----------------------------------------------------------------------------------------
		// Préparer les champs de type filtre
		// ----------------------------------------------------------------------------------------
		$pageInfos['idCollection'] = $session->get("pref_filtre_collection");

		// ----------------------------------------------------------------------------------------
		// Traitement des messages d'erreurs en attente (priorité)
		// ----------------------------------------------------------------------------------------
		if ($demandeOrig != "items_selectionner_sauvegarder") {
				
			if ($session->get("message_erreur") != "") {
				$messages = new Messages($session->get("message_erreur"), Messages::ERREUR);
				$session->delete("message_erreur");
			}
			if ($session->get("message_confirmation") != "") {
				$messages = new Messages($session->get("message_confirmation"), Messages::CONFIRMATION);
				$session->delete("message_confirmation");
			}
			if ($session->get("message_avertissement") != "") {
				$messages = new Messages($session->get("message_confirmation"), Messages::AVERTISSEMENT);
				$session->delete("message_avertissement");
			}
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
		Erreur::erreurFatal('006', "[questionnaires.php] Demande incorrecte : '$demande'", $log);
	}

	// Terminer
	$log->debug("questionnaires.php: Fin");

} catch (Exception $e) {
	Erreur::erreurFatal('018', "questionnaires.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}