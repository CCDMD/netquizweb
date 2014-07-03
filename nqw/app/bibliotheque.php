<?php

/** 
 * Aiguilleur : bibliotheque.php
 * 
 * Aiguillage des demandes pour la bibliothèque
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {
		
	// Configuration et démarrage
	$aiguilleur = "bibliotheque";
	require_once 'init.php';
	$demandesPermises = array("liste", "item_ajouter","item_modifier","item_sauvegarder","items_choix", "item_apercu", "item_apercu_liste",
							  "item_suivi", "item_suivi_activer","item_liste_suivi_activer", "item_liste_suivi_desactiver", "item_corbeille", "item_corbeille_liste",
						      "item_dupliquer", "item_imprimer", "item_imprimer_liste", "item_recherche", "item_recherche_initialiser", "item_apercu_messages", 
							  "item_ajouter_questionnaire", "item_ajouter_questionnaire_item", "item_modifier_ajouter_element", "item_modifier_supprimer_element", "item_modifier_type_element",
							  "item_modifier_type_element_classeur", "item_modifier_type_lacune", "item_modifier_ajouter_couleur", "item_modifier_supprimer_couleur", "item_changer_section",
							  "item_modifier_ajouter_classeur", "item_modifier_supprimer_classeur", "item_ajouter_classeur_element",  "item_supprimer_classeur_elements", "item_supprimer_classeur_element",
							  "item_modifier_media", "item_modifier_langue_apercu", "item_modifier_theme_apercu", "item_exporter", "item_modifier_type",
						      "item_ajouter_lacune", "item_maj_lacunes", "item_supprimer_lacunes", "item_modifier_ajouter_lacune_reponse", "item_modifier_supprimer_lacune_reponse",
							  "langue_liste", "langue_ajouter", "langue_sauvegarder", "langue_modifier", "langue_recherche", "langue_recherche_initialiser", 
							  "langue_dupliquer", "langue_imprimer_liste","langue_corbeille_liste", "langue_imprimer", "langue_corbeille", "langue_exporter",
							  "terme_liste", "terme_ajouter", "terme_sauvegarder", "terme_modifier", "terme_dupliquer", "terme_supprimer", "terme_exporter",
						      "collection_liste", "collection_ajouter", "collection_sauvegarder", "collection_modifier", "collection_recherche", "collection_recherche_initialiser", 
						      "collection_dupliquer", "collection_imprimer_liste","collection_corbeille_liste", "collection_imprimer", "collection_corbeille", "collection_exporter",
						      "categorie_liste", "categorie_ajouter", "categorie_sauvegarder", "categorie_modifier", "categorie_recherche", "categorie_recherche_initialiser", 
						      "categorie_dupliquer", "categorie_imprimer_liste","categorie_corbeille_liste", "categorie_imprimer", "categorie_corbeille", "categorie_exporter",
							  "elements_importer_formulaire", "elements_importer_envoi", "elements_importer_resultats",
							  "corbeille_recherche", "corbeille_recherche_initialiser", "corbeille_recuperer", "corbeille_supprimer", "corbeille"
							  );
	$log->debug("bibliotheque.php: Début");
	
	// ----------------------------------------------------------------------------------------
	// Initialisation
	// ----------------------------------------------------------------------------------------
	$gabarit = "";
	$chaineRech = "";
	$session = new Session();
	$corbeille = new Corbeille($log, $dbh);
	$pagination = new Pagination(array(), $usager, $log, $dbh);
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la demande
	// ----------------------------------------------------------------------------------------
	$demande = Web::getParam('demande');
	if ($demande == "") {
		$demande = "liste";	
	}
	$log->debug("bibliotheque.php:   --------------------------- Aiguillage de la demande '$demande' ---------------------------");
	
	// Prendre note de la demande originale
	$demandeOrig = $demande;
	
	// ----------------------------------------------------------------------------------------
	// Obtenir les ids des objets
	// ----------------------------------------------------------------------------------------
	$idItem = Web::getParamNum("item_id_item");
	$idTerme = Web::getParamNum("terme_id_terme");
	$idCollection = Web::getParamNum("collection_id_collection");
	$idCategorie = Web::getParamNum("categorie_id_categorie");
	$idLangue = Web::getParamNum("langue_id_langue");
	$idLacune = Web::getParam("lacune");
	
	// ----------------------------------------------------------------------------------------
	// Obtenir le type de l'item
	// ----------------------------------------------------------------------------------------
	$typeItem = Web::getParam('item_type_item');
	
	// Vérifier si l'item est valide
	if ($typeItem != "" && ! Item::isTypeItemValide($typeItem) ) {
		$log->debug("questionnaires.php:   Le type d'item spécifié est invalide '" . $typeItem . "'");
		Erreur::erreurFatal("009", "Le type d'item spécifié est invalide '" . $typeItem . "'", $log);
	}
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la page à afficher
	// ----------------------------------------------------------------------------------------
	$pageItem = Web::getParamNum('item_page');
	$pageTerme = Web::getParamNum('terme_page');
	$pageCollection = Web::getParamNum('collection_page');
	$pageCategorie = Web::getParamNum('categorie_page');
	$pageLangue = Web::getParamNum('langue_page');
	
	// ----------------------------------------------------------------------------------------
	// Instancier les objets de base
	// ----------------------------------------------------------------------------------------
	$item = new Item($log, $dbh);
	$terme = new Terme($log, $dbh);
	$collection = new Collection($log, $dbh);
	$categorie = new Categorie($log, $dbh);
	$langue = new Langue($log, $dbh);
	
	// ----------------------------------------------------------------------------------------
	// Vérifier si un changement de page est requis
	// ----------------------------------------------------------------------------------------
	if ($pageItem != "") {
		$idItem = $item->getIdItemParPage($pageItem);
	}
	if ($pageCategorie != "") {
		$idCategorie = $categorie->getIdCategorieParPage($pageCategorie);
	}
	if ($pageLangue != "") {
		$idLangue = $langue->getIdLangueParPage($pageLangue);
	}
	if ($pageTerme != "") {
		$idTerme = $terme->getIdTermeParPage($pageTerme);
	}	
	
	// ----------------------------------------------------------------------------------------
	// Instancier l'item et récupérer les infos
	// ----------------------------------------------------------------------------------------
	$itemFactory = new Item($log, $dbh);
	$item = $itemFactory->instancierItemParType($typeItem, $idProjetActif, $idItem);
	
	// ----------------------------------------------------------------------------------------
	// Préparer les informations pour la page
	// ----------------------------------------------------------------------------------------
	$pageInfos = array();
	$pageInfos['apercu'] = ""; // Défaut
	$pageInfos["flagModifications"] = "";
	
	// ----------------------------------------------------------------------------------------
	// Obtenir le filtre par type d'item
	// ----------------------------------------------------------------------------------------
	$filtreTypeItem = Web::getParam("filtre_type_item");
	if ($filtreTypeItem == "") {
		// Vérifier si un filtre est spécifié dans la session
		$filtreTypeItem = $session->get("pref_filtre_type_item");
	}	
	
	// ----------------------------------------------------------------------------------------
	// Instancier les objets au besoin
	// ----------------------------------------------------------------------------------------
	if ($idCollection != "") {
		$collection->getCollectionParId($idCollection, $idProjetActif);	
	}
	if ($idCategorie != "") {
		$categorie->getCategorieParId($idCategorie, $idProjetActif);	
	}
	
	if ($idLangue != "") {
		$langue->getLangueParId($idLangue, $idProjetActif);	
	}
	if ($idTerme != "") {
		$terme->getTermeParId($idTerme, $idProjetActif);
	}
	
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la liste des catégories pour l'utilisateur
	// ----------------------------------------------------------------------------------------
	$listeCategorie = $categorie->getListeCategories($idProjetActif);
	
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
			
	
	// Vérifier la demande
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {
		
		$log->debug("bibliotheque.php:   Traiter la demande '$demande'");
		
		
		// ---------------------------------------------------------------------------------------- 
	
		//                                 I M P O R T A T I O N  
		
		// ----------------------------------------------------------------------------------------				

		// ----------------------------------------------------------------------------------------
		// Formulaire pour importer du XML
		// ----------------------------------------------------------------------------------------
		if ($demande == "elements_importer_formulaire") {

			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh); 
			$listeLangues = $lang->getListeLangues($idProjetActif);

			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();
			
			// Régler le gabarit à utiliser
			$gabarit = "biblio-elements-importer-formulaire.php";
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
			$url = "bibliotheque.php?demande=elements_importer_resultats";
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
			$gabarit = "biblio-elements-importer-resultats.php";
		}
				
		
		// ---------------------------------------------------------------------------------------- 
	
		//                                        I T E M S  
		
		// ----------------------------------------------------------------------------------------			
	
		
		// ----------------------------------------------------------------------------------------
		// Ajouter les items sélectionnés au questionnaire
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_ajouter_questionnaire" || $demande == "item_ajouter_questionnaire_item" ) {
			
			$log->debug("questionnaires.php:item_ajouter_questionnaire Début");
			$listeItemsRejetes = 0;
			$listeItemsAcceptes = 0;
			
			// Obtenir le questionnaire de destination
			$idQuest = Web::getParamNum("questionnaire_dest");
			$log->debug("questionnaires.php:item_ajouter_questionnaire Ajouter l'item au questionnaire : '" . $idQuest . "'");
			
			// Obtenir la liste des items pour un questionnaire
			$quest = new Questionnaire($log, $dbh);
			$quest->getQuestionnaireParId($idQuest, $idProjetActif);
			$listeItems = $quest->getListeItems();
			
			// Obtenir les infos du questionnaire
			$quest = new Questionnaire($log, $dbh);
			$quest->getQuestionnaireParId($idQuest, $idProjetActif);

			// Vérifier si une sélection d'items est disponible
			$log->debug("questionnaires.php:item_ajouter_questionnaire Vérifier si une sélection d'item est disponible");
			$listeElements = array_reverse(Web::getListeParam("items_selection_"));

			if (! empty($listeElements) && $idQuest != "" ) {
				
				$log->debug("questionnaires.php:item_ajouter_questionnaire Une liste d'item est disponible");
				foreach ($listeElements as $element) {
					
					// Vérifier si on peut ajouter l'item (pas déjà dans le questionnaire)
					$log->debug("questionnaires.php:item_ajouter_questionnaire Vérifier si on peut ajouter l'item");
					if ( !in_array($element, $listeItems) ) { 

						$listeItemsAcceptes++;

						// Instancier l'item
						$log->debug("questionnaires.php:item_ajouter_questionnaire Instancier item '$element'");
						$itemFactory = new Item($log, $dbh);
						$item = $itemFactory->instancierItemParType('', $idProjetActif, $element);
						
						// Ajouter l'item au questionnaire
						$item->set("id_questionnaire", $idQuest);
						$item->set("id_projet", $idProjetActif);
						$log->debug("questionnaires.php:item_ajouter_questionnaire Ajouter un lien entre l'item et le questionnaire");
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

				// Ajouter la liste des éléments rejetés à la session
				if ( ($listeItemsAcceptes == 0) && ($listeItemsRejetes > 0) ) {
					// Aucun accepté, tous rejetés
					$messages = new Messages(ERR_020, Messages::ERREUR);
				} elseif ( ($listeItemsAcceptes > 0) && ($listeItemsRejetes > 0) ) {
					// Mixe acceptés et rejetés
					$messages = new Messages(ERR_019, Messages::ERREUR);
				} else {
					// Tous acceptés - Déterminer le nombre...
					if ($listeItemsAcceptes == 1) {
						// 1 seul
						$messages = new Messages(MSG_014, Messages::CONFIRMATION);
					} else {
						// Plusieurs
						$messages = new Messages(MSG_004, Messages::CONFIRMATION);
					}
				}
			} else {
				$log->debug("questionnaires.php:item_ajouter_questionnaire Impossible de localiser une liste d'item");
			}

			// Mettre à jour le nombre d'items pour le questionnaire
			$quest->updateNombreItems($idQuest, $idProjetActif);	
			
			if ($demande == "item_ajouter_questionnaire_item") {
				$demande = "item_modifier";
			} else {
				// Réafficher la liste des items
				$demande = "liste";
			}
		}
		
	
		// ----------------------------------------------------------------------------------------
		// Sauvegarder un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_sauvegarder") {
			
			// Déterminer si un changement d'item est demandé
			$pageDest = Web::getParamNum("pagination_page_dest");
			
			if ( $pageDest != "") {
				
				// Obtenir la page de destination
				$idItem = $item->getIdItemParPage($pageDest);
				
				// Obtenir l'item correspondant
				$item = $item->instancierItemParType('', $idProjetActif, $idItem);
				
				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);
				
				// Afficher la page par défaut pour modifier un questionnaire
				$demande = "item_modifier";
				
			} else {
	
				// Obtenir les informations à partir de la requête
				$item->getDonneesRequete();
				
				// Au besoin, enregistrer une nouvelle catégorie
				$item->enregistrerNouvelleCategorie();
			
				// Rafraîchir la liste des categories
				$listeCategorie = $categorie->getListeCategories($idProjetActif);			
				
				// Enregistrer les informations d'un item existant
				$item->enregistrer();
				
				// Obtenir l'id de l'item
				$idItem = $item->get("id_item");
				
				// Recharger les informations complètes pour affichage
				$item = $item->instancierItemParType('', $idProjetActif, $idItem);
				
				// Obtenir la liste des langues
				$lang = new Langue($log, $dbh); 
				$listeLangues = $lang->getListeLangues($idProjetActif);

				// Obtenir la liste des thèmes
				$theme = new Theme($log, $dbh);
				$listeThemes = $theme->getListeThemes();

				// Obtenir la langue de l'item
				$langueItem = $item->getLangueApercuObj();
				
				// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
				$item->preparerValeursPanneauMessages();			
				
				// Analyser les éléments avant affichage
				$item->analyserElements();	
				
				// Message de confirmation
				$log->debug("questionnaires.php: Sauvegarde de l'item complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
		
				// Régler le gabarit à utiliser
				$gabarit = "biblio-item-modifier.php";
			}
		}			
		
		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_ajouter") {

			// Préparer les valeurs initiales selon le type d'item
			$item->preparerValeursInitiales($typeItem, $projetActif, $usager);
			
			// Ajouter à la base de données
			$item->ajouter();	
	
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh); 
			$listeLangues = $lang->getListeLangues($idProjetActif);

			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();			
			
			// Obtenir la langue de l'item
			$langueItem = $item->getLangueApercuObj();

			// Analyser les éléments avant affichage
			$item->analyserElements();			
			
			// Mettre à jour le numéro d'item courant
			$idItem = $item->get("id_item");
			
			// Mettre à jour la liste des items
			$listeIdItem = $item->getListeItems($idProjetActif);
			
			$gabarit = "biblio-item-modifier.php";
		}
		
		// ----------------------------------------------------------------------------------------
		// Modifier un média
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_media" || $demande == "item_modifier_langue_apercu" || $demande == "item_modifier_theme_apercu") {
			
			$pagination = new Pagination(array(), $usager, $log, $dbh);
			
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();

			// Préparer les champs
			$item->preparerValeursApresChargement($idProjetActif);
			
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
			
			if ($demande == "item_modifier_langue_apercu" || $demande == "item_modifier_theme_apercu" ) {
				// Vérifier le flag de modifications passer en paramètre
				$flagModif = Web::getParamNum("flagModifications");
				if ($flagModif == "1") {
					$pageInfos["flagModifications"] = 1;
				}
			}
			
			// Afficher le formulaire de modification et régler la demande de retour
			$demande = "item_modifier";
			$demandeRetour = "item_modifier";
		}						
		
		// ----------------------------------------------------------------------------------------
		// Modifier le type d'item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier_type") {
			
			$pagination = new Pagination(array(), $usager, $log, $dbh);

			// Obtenir le type d'item original
			$typeItemOrig = $item->get("type_item");
			
			// Obtenir les informations à partir de la requête
			$item->getDonneesRequete();
			
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
		// Modifier le type d'item
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
			$item->getDonneesRequete();
				
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
			$item->getDonneesRequete();
		
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
			$item->getDonneesRequete();
			
			// Obtenir la position de l'élément
			$element = Web::getParamNum("element");

			// Ajouter l'élément
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
			$item->getDonneesRequete();
		
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
			$item->getDonneesRequete();
		
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
			$item->getDonneesRequete();
			
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
			$item->getDonneesRequete();
		
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
		// Afficher les choix d'items 
		// ----------------------------------------------------------------------------------------
		if ($demande == "items_choix") {
			
			$gabarit = "biblio-items-choix.php";
		}
		
		// ----------------------------------------------------------------------------------------
		// Exporter les items sélectionnés
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_exporter") {

			$urlFichierZip = "";

			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("items_selection_");
			$listeItems = array();
			//header();
			if (! empty($listeElements) ) {
				
				// Préparer la liste des items
				foreach ($listeElements as $element) {
					array_push($listeItems, $element);					
				}
				
				// Générer le XML pour exportation
				if (! empty($listeItems)) {
					$itemFactory = new Item($log, $dbh);
					$urlFichierZip = $item->exporterItemXML($projetActif, $usager, $listeItems);
				}
								
			} else {
				
				// Un seul item à traiter
				array_push($listeItems, $idItem);
				
				// Générer le XML pour exportation
				if (! empty($listeItems)) {
					$itemFactory = new Item($log, $dbh);
					$urlFichierZip = $item->exporterItemXML($projetActif, $usager, $listeItems);
				}
			}

			// Réafficher la liste des items
			$demande = "liste";
			
			if ($urlFichierZip == "") {
				$messages = new Messages(ERR_043, Messages::ERREUR);
			} else {
				header("Location: $urlFichierZip");			
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
		// Activer le suivi sur un ou plusieurs items
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_suivi_activer") {
	
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("items_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $element);
					$item->activerSuivi();
				}
				
				// Réafficher la liste des items
				$demande = "liste";
				
			} else {
			
				// Activer le suivi sur l'item actuel
				$item->activerSuivi();
				
				// Réafficher la liste des items
				$demande = "modifier";
			}
		}
			
		// ----------------------------------------------------------------------------------------
		// Activer le suivi sur un item dans la liste 
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_liste_suivi_activer") {
	
			// Activer le suivi
			$item->activerSuivi();
			$demande = "liste";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Désactiver le suivi sur un item dans la liste 
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_liste_suivi_desactiver") {
	
			// Activer le suivi
			$item->desactiverSuivi();
			$demande = "liste";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les items
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_recherche") {
			
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("item_recherche_chaine", $chaine);
			
			// Afficher la liste des questionnaires correspondants
			$demande = "liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche des items
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_recherche_initialiser") {
			
			// Mettre en session
			$session->set("item_recherche_chaine", "");
			
			// Afficher la liste des questionnaires correspondants
			$demande = "liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Dupliquer un ou plusieurs items
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_dupliquer") {
	
			// Obtenir le(s) formulaire(s) à dupliquer
			$listeElements = Web::getListeParam("items_selection_");
	
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $element);
					$item->dupliquer();
				}
			} else {
				// Dupliquer l'item courant
				$item->dupliquer();
			}
				
			// Réafficher la liste des items
			$demande = "liste";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Aperçu d'un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_apercu_liste" || $demande == "item_apercu") {
			
			$chaineAleatoire = Securite::genererChaineAleatoire(8);
			$prefixRepertoire = Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" . REPERTOIRE_PREFIX_APERCU . $chaineAleatoire . "/";

			// Marquer le questionnaire comme modifié (pour flag si l'utilisateur quitte la page)
			if ($demande == "item_modifier_media") {
				$pageInfos["flagModifications"] = 1;
			}
			
			if ($demande == "item_apercu" ) {

				// Obtenir les informations à partir de la requête
				$item->getDonneesRequete();
				
				// Vérifier le flag de modifications passer en paramètre
				$flagModif = Web::getParamNum("flagModifications");
				if ($flagModif == "1") {
					$pageInfos["flagModifications"] = 1;
				}
			}

			// Valider l'item
			if ($item->valider(null)) {
				
				// Régler la langue et le thème pour l'aperçu
				$item->set("apercu_langue", $usager->get("pref_apercu_langue"));
				$item->set("apercu_theme", $usager->get("pref_apercu_theme"));
				
				// Générer l'apercu
				$rc = $item->genererApercu($projetActif, $usager, $item, $chaineAleatoire, null);
	
				if ($rc < 0) {
					$messages = new Messages(ERR_012, Messages::CONFIRMATION);
				} elseif ($rc == 1) {
				
					// Préparer l'URL pour l'aperçu
					$urlApercu = URL_PUBLICATION . $prefixRepertoire . FICHIER_MAIN_HTML;
					$item->set("apercu", $urlApercu);
				}
				
			} else {

				// Afficher message d'erreur
				$urlApercu = "bibliotheque.php?demande=item_apercu_messages";
				$item->set("apercu", $urlApercu);
			} 

			// Préparer les champs
			$item->preparerValeursApresChargement($idProjetActif);

			// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
			$item->preparerValeursPanneauMessages();
			
			// Analyser les éléments avant affichage
			$item->analyserElements();
			
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh); 
			$listeLangues = $lang->getListeLangues($idProjetActif);
			
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();

			// Obtenir la langue de l'item
			$langueItem = $item->getLangueApercuObj();
			
			// Analyser les éléments avant affichage
			$item->analyserElements();

			// Titre pour ajout de média
			$session->set("questionnaire_titre_menu", $item->get("titre"));
			$session->set("item_titre_menu", "");			
						
			// Obtenir la liste des formulaires qui utilisent cet item
			$item->getListeQuestionnairesUtilisantItem();
			
			// Réafficher la liste des items
			if ($demande == "item_apercu_liste") {
				$demande = "liste";
			} elseif ($demande == "item_apercu") {
				$gabarit = "biblio-item-modifier.php";
			}
			
		}	
		
		// ----------------------------------------------------------------------------------------
		// Afficher les messages d'erreurs reliés à l'apercu
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_apercu_messages") {
			
			// Obtenir le message
			$msg = $session->get("apercu_messages");
			$item->set("item_apercu_messages", $msg);
			$session->delete("item_apercu_messages");

			// Régler le gabarit
			$gabarit = "validation/item-page.php";
		}		
		
		// ----------------------------------------------------------------------------------------
		// Impression d'un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_imprimer_liste" || $demande == "item_imprimer") {
			
			$contenu = "";
			
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("items_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $element);
					$contenu .= $item->imprimer('');
				}
			} else {
				
				// Obtenir les données
				$item->getDonneesRequete();
				
				// Obtenir le contenu de l'item pour impression
				$contenu = $item->imprimer('');
			}
	
			// Passer le contenu via l'item
			$item->set("contenu", $contenu);
			
			// Obtenir le gabarit à utiliser pour l'impression
			$gabarit = $item->get("gabarit_impression");
		}
		
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille un ou plusieurs items
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_corbeille_liste" || $demande == "item_corbeille") {
	
			// Vérifier si une sélection d'items est disponible
			$listeElements = Web::getListeParam("items_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $element);
					$item->desactiver();
				}
			} else {
				// Mettre à la corbeille l'item actuel
				$item->desactiver();
			}
			
			// Réafficher la liste des items
			$demande = "liste";
		}
		
		// ----------------------------------------------------------------------------------------
		// Afficher le formulaire pour modifier un item
		// ----------------------------------------------------------------------------------------
		if ($demande == "item_modifier") {
	
			// Obtenir la liste des formulaires qui utilisent cet item
			$item->getListeQuestionnairesUtilisantItem();
			
			// Obtenir la liste des langues
			$lang = new Langue($log, $dbh); 
			$listeLangues = $lang->getListeLangues($idProjetActif);
			
			// Obtenir la liste des thèmes
			$theme = new Theme($log, $dbh);
			$listeThemes = $theme->getListeThemes();

			// Obtenir la langue de l'item
			$langueItem = $item->getLangueApercuObj();
			
			// Préparer les valeurs pour le panneau messages (valeurs pour cet item seulement)
			$item->preparerValeursPanneauMessages();			
			
			// Analyser les éléments avant affichage
			$item->analyserElements();

			// Titre pour ajout de média
			$session->set("questionnaire_titre_menu", $item->get("titre"));
			$session->set("item_titre_menu", "");
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_QUESTIONNAIRE . "0", TXT_PREFIX_ITEM . $item->get("id_item"));

			$gabarit = "biblio-item-modifier.php";
		}
	
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des items
		// ----------------------------------------------------------------------------------------
		if ($demande == "liste") {
			
			$listeItems = array();
			
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "liste") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
			
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdItem = array();
			if ($session->get("item_recherche_chaine") == "") {
				$listeIdItem = $item->getListeItems($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("item_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdItem = $item->rechercheItems($chaine, $idProjetActif, "1", $log, $dbh);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdItem, $usager, $log, $dbh);
			
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
			$gabarit = "biblio-items-liste.php";
		}		
		
		
		// ---------------------------------------------------------------------------------------- 
	
		//                                   L A N G U E S 
		
		// ----------------------------------------------------------------------------------------			
			
		// ----------------------------------------------------------------------------------------
		// Ajouter une langue
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_ajouter") {
	
			// Titre par défaut			
			$langue->set("titre", TXT_NOUVELLE_LANGUE);
			$langue->set("titre_menu", TXT_NOUVELLE_LANGUE);
			$langue->set("id_projet", $idProjetActif);
			$langue->ajouter();	
	
			// Mettre à jour l'identifiant de la langue
			$idLangue = $langue->get("id_langue");
			
			// Mettre à jour la liste des langues
			$listeIdLangue = $langue->getListeIdLangues($idProjetActif);
	
			// Régler le gabarit à utiliser
			$gabarit = "biblio-langue-modifier.php";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder une langue
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_sauvegarder") {
	
			// Déterminer si un changement d'item est demandé
			$pageDest = Web::getParamNum("pagination_page_dest");
			
			if ( $pageDest != "") {
				
				// Obtenir la page de destination
				$idLangue = $langue->getIdLangueParPage($pageDest);
				
				// Obtenir l'item correspondant
				$langue->getLangueParId($idLangue, $idProjetActif);
				
				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);
				
				// Afficher la page par défaut pour modifier un questionnaire
				$demande = "langue_modifier";
				
			} else {
			
				// Obtenir les informations à partir de la requête
				$langue->getDonneesRequete();
				
				// Enregistrer les informations d'une langue existante
				$langue->enregistrer();
		
				// Recharger la langue pour obtenir les valeurs les plus récentes
				$langue->getLangueParId($idLangue, $idProjetActif);
				
				// Message de confirmation
				$log->debug("bibliotheque.php: Sauvegarde de la langue complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
		
				// Régler le gabarit à utiliser
				$gabarit = "biblio-langue-modifier.php";
			}
		}
		
		// ----------------------------------------------------------------------------------------
		// Modifier une langue
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_modifier") {
	
			// Titre pour ajout de média
			$session->set("questionnaire_titre_menu", LANGUE::getTitre($idLangue, $idProjetActif, $log, $dbh));
			$session->set("item_titre_menu", "");
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_LANGUE . $langue->get("id_langue"), '');
			
			$gabarit = "biblio-langue-modifier.php";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_recherche") {
			
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("langue_recherche_chaine", $chaine);
			
			// Afficher la liste des langues correspondantes
			$demande = "langue_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche dans les langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_recherche_initialiser") {
			
			// Mettre en session
			$session->set("langue_recherche_chaine", "");
			
			// Afficher la liste des langues
			$demande = "langue_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Dupliquer une ou plusieurs langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_dupliquer") {
	
			// Obtenir le(s) formulaire(s) à dupliquer
			$listeElements = Web::getListeParam("langues_selection_");
	
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$langue = new Langue($log, $dbh);
					$langue->getLangueParId($element, $idProjetActif);
					$langue->dupliquer();
				}
			} else {
				// Dupliquer la langue courante
				$langue->dupliquer();
			}
				
			// Réafficher la liste des langues
			$demande = "langue_liste";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Impression d'une ou plusieurs langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_imprimer_liste" || $demande == "langue_imprimer") {
			
			$contenu = "";
			
			// Vérifier si une sélection de langues est disponible
			$listeElements = Web::getListeParam("langues_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$langue = new Langue($log, $dbh);
					$langue->getLangueParId($element, $idProjetActif);
					$contenu .= $langue->imprimer('');
				}
			} else {
				// Obtenir les données de la requête
				$langue->getDonneesRequete();
				
				// Obtenir le contenu de la langue pour impression
				$contenu = $langue->imprimer('');
			}
	
			// Passer le contenu via la langue
			$langue->set("contenu", $contenu);
			
			// Obtenir le gabarit à utiliser pour l'impression
			$gabarit = $langue->get("gabarit_impression");
		}
	
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille une ou plusieurs langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_corbeille_liste" || $demande == "langue_corbeille") {
	
			$languesProtegees = 0;
			
			// Vérifier si une sélection de langues est disponible
			$listeElements = Web::getListeParam("langues_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$langue = new Langue($log, $dbh);
					$langue->getLangueParId($element, $idProjetActif);
					$conf = $langue->desactiver();
					
					if ($conf == "0") {
						$languesProtegees++;
					}
				}
			} else {
				// Mettre à la corbeille la langue actuelle
				$conf = $langue->desactiver();
				
				if ($conf == "0") {
					$languesProtegees++;
				}
			}
			
			// Message s'il n'est pas possible de supprimer certaines langues (systèmes) protégées
			if ($languesProtegees > 0) {
				$messages = new Messages(MSG_035, Messages::AVERTISSEMENT);
			} 
			
			// Réafficher la liste des items
			$demande = "langue_liste";
		}
			

		// ----------------------------------------------------------------------------------------
		// Exporter les langues sélectionnées
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_exporter") {
		
			$urlFichierZip = "";
		
			// Vérifier si une sélection de langues est disponible
			$listeElements = Web::getListeParam("langues_selection_");
			$listelangues = array();
		
			if (! empty($listeElements) ) {
		
				// Préparer la liste des langues
				foreach ($listeElements as $element) {
					array_push($listelangues, $element);
				}
		
				// Générer le XML pour exportation
				if (! empty($listelangues)) {
					$l = new Langue($log, $dbh);
					$urlFichierZip = $l->exporterListelanguesXML($projetActif, $usager, $listelangues);
				}
		
			} else {
		
				// Une seule langue à traiter
				array_push($listelangues, $idLangue);
		
				// Générer le XML pour exportation
				if (! empty($listelangues)) {
					$l = new Langue($log, $dbh);
					$urlFichierZip = $l->exporterListelanguesXML($projetActif, $usager, $listelangues);
				}
			}
		
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "langue_liste";
			}
		
			if ($urlFichierZip == "") {
				$messages = new Messages(ERR_043, Messages::ERREUR);
			} else {
				header("Location: $urlFichierZip");
			}
		}
				
		
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des langues
		// ----------------------------------------------------------------------------------------
		if ($demande == "langue_liste") {
			
			$listeLangue = array();
			
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "langue_liste" || $demandeOrig == "langue_dupliquer") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
			
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdLangue = array();
			if ($session->get("langue_recherche_chaine") == "") {
				$listeIdLangue = $langue->getListeIdLangues($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("langue_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdLangue = $langue->rechercheLangues($chaine, $idProjetActif, $log, $dbh);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdLangue, $usager, $log, $dbh);
			
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
				
					$idElement = $listeIdLangue[$i];
	
					// Obtenir les informations de cet item
					$element = new Langue($log, $dbh);
					$element->getLangueParId($idElement, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_LANGUE . $element->get("id_langue"));
					
					// Ajouter aux résultats de recherche
					array_push($listeLangue, $element);
				}
			}
			
			$langue->preparerAffichageListe();		
			
			$gabarit = "biblio-langues-liste.php";
		}
			
		
		// ----------------------------------------------------------------------------------------
		
		//                                     T E R M E S 
		
		// ----------------------------------------------------------------------------------------
		
		
		// ----------------------------------------------------------------------------------------
		// Dupliquer un ou plusieurs termes
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_dupliquer") {
		
			// Obtenir le(s) terme(s) à dupliquer
			$listeElements = Web::getListeParam("termes_selection_");
		
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$terme = new Terme($log, $dbh);
					$terme->getTermeParId($element, $idProjetActif);
					$terme->dupliquer();
				}
			} else {
				// Dupliquer le terme courant
				$terme->dupliquer();
			}
		
			// Réafficher la liste des termes
			$demande = "terme_liste";
		}
		
		// ----------------------------------------------------------------------------------------
		// Supprimer un terme
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_supprimer") {
			
			// Vérifier si une sélection de termes est disponible
			$listeElements = Web::getListeParam("termes_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$terme = new Terme($log, $dbh);
					$terme->getTermeParId($element, $idProjetActif);
					$terme->supprimer();
				}
			} else {
				// Sinon supprimer le terme courant
				$terme->supprimer();
			}			
		
			// Réafficher la liste des termes
			$demande = "terme_liste";
		}
		
	
		// ----------------------------------------------------------------------------------------
		// Exporter les termes sélectionnés
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_exporter") {
		
			$urlFichierZip = "";
		
			// Vérifier si une sélection de termes est disponible
			$listeElements = Web::getListeParam("termes_selection_");
			$listeTermes = array();

			if (! empty($listeElements) ) {
		
				// Préparer la liste des termes
				foreach ($listeElements as $element) {
					array_push($listeTermes, $element);
				}
		
				// Générer le XML pour exportation
				if (! empty($listeTermes)) {
					$t = new Terme($log, $dbh);
					$urlFichierZip = $t->exporterListeTermesXML($projetActif, $usager, $listeTermes);
				}
		
			} else {
		
				// Un seul item à traiter
				array_push($listeTermes, $idTerme);
		
				// Générer le XML pour exportation
				if (! empty($listeTermes)) {
					$t = new Terme($log, $dbh);
					$urlFichierZip = $t->exporterListeTermesXML($projetActif, $usager, $listeTermes);
				}
			}
		
			// Réafficher la liste des termes
			$demande = "terme_liste";
				
			if ($urlFichierZip == "") {
				$messages = new Messages(ERR_043, Messages::ERREUR);
			} else {
				header("Location: $urlFichierZip");
			}
		}		
		
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des termes
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_liste") {
				
			$listeTermes = array();
				
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "terme_liste" || $demandeOrig == "terme_dupliquer") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
				
			// Obtenir le tri à utiliser
			$tri = $terme->getTri();

			// Obtenir la liste des ids des termes
			$listeIdTermes = $terme->getListeIdTermesDuProjet($idProjetActif, $tri);
				
			// Appliquer la pagination
			$pagination = new Pagination($listeIdTermes, $usager, $log, $dbh);
				
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
		
					$idElement = $listeIdTermes[$i];
		
					// Obtenir les informations de ce terme
					$element = new Terme($log, $dbh);
					$element->getTermeParId($idElement, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_TERME . $element->get("id_collection"));
						
					// Ajouter aux résultats de recherche
					array_push($listeTermes, $element);
				}
			}
				
			$terme->preparerAffichageListe();
				
			$gabarit = "biblio-termes-liste.php";
		}
				
		
		// ----------------------------------------------------------------------------------------
		// Ajouter un terme
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_ajouter") {
		
			// Titre par défaut
			$terme->set("terme", TXT_NOUVEAU_TERME);
			$terme->set("titre_menu", TXT_NOUVEAU_TERME);
			$terme->set("id_projet", $idProjetActif);
			$terme->ajouter();
		
			// Mettre à jour l'identifiant du terme
			$idTerme = $terme->get("id_terme");
				
			// Obtenir le tri à utiliser
			$tri = $terme->getTri();
			
			// Mettre à jour la liste des termes
			$listeIdTermes = $terme->getListeIdTermesDuProjet($idProjetActif, $tri);
		
			// Régler le gabarit à utiliser
			$gabarit = "biblio-terme-modifier.php";
		}	


		// ----------------------------------------------------------------------------------------
		// Sauvegarder un terme
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_sauvegarder") {
		
			// Déterminer si un changement de terme est demandé
			$pageDest = Web::getParamNum("pagination_page_dest");
		
			if ( $pageDest != "") {
		
				// Obtenir la page de destination
				$idTerme = $terme->getIdTermeParPage($pageDest);
		
				// Obtenir le terme correspondant
				$terme->getTermeParId($idTerme, $idProjetActif);
		
				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);
		
				// Afficher la page par défaut pour modifier terme
				$demande = "terme_modifier";
		
			} else {
			
				// Obtenir les informations à partir de la requête
				$terme->getDonneesRequete();
				
				// Enregistrer les informations d'un terme existant
				$terme->enregistrer();

				// Message de confirmation
				$log->debug("bibliotheque.php: Sauvegarde du terme complété");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
		
				// Régler le gabarit à utiliser
				$gabarit = "biblio-terme-modifier.php";
			}
		}
		

		
		
		// ----------------------------------------------------------------------------------------
		// Modifier un terme
		// ----------------------------------------------------------------------------------------
		if ($demande == "terme_modifier") {
		
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_TERME . $terme->get("id_terme"), '');
			
			$gabarit = "biblio-terme-modifier.php";
		}
		
	
		// ---------------------------------------------------------------------------------------- 
	
		//                                C O L L E C T I O N S 
		
		// ----------------------------------------------------------------------------------------			
		
		// ----------------------------------------------------------------------------------------
		// Ajouter une collection
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_ajouter") {
	
			// Titre par défaut			
			$collection->set("titre", TXT_NOUVELLE_COLLECTION);
			$collection->set("titre_menu", TXT_NOUVELLE_COLLECTION);
			$collection->set("id_projet", $idProjetActif);
			$collection->ajouter();	
	
			// Mettre à jour l'identifiant de la collection
			$idCollection = $collection->get("id_collection");
			
			// Mettre à jour la liste des collections
			$listeIdCollection = $collection->getListeIdCollections($idProjetActif);
	
			// Régler le gabarit à utiliser
			$gabarit = "biblio-collection-modifier.php";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder une collection
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_sauvegarder") {
	
			// Déterminer si un changement d'item est demandé
			$pageDest = Web::getParamNum("pagination_page_dest");
			
			if ( $pageDest != "") {
				
				// Obtenir la page de destination
				$idCollection = $collection->getIdCollectionParPage($pageDest);
				
				// Obtenir l'item correspondant
				$collection->getCollectionParId($idCollection, $idProjetActif);
				
				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);
				
				// Afficher la page par défaut pour modifier un questionnaire
				$demande = "collection_modifier";
				
			} else {
			
				// Obtenir les informations à partir de la requête
				$collection->getDonneesRequete();
				
				// Enregistrer les informations d'une collection existante
				$collection->enregistrer();
		
				// Message de confirmation
				$log->debug("bibliotheque.php: Sauvegarde de la collection complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
		
				// Régler le gabarit à utiliser
				$gabarit = "biblio-collection-modifier.php";
			}
		}
		
		// ----------------------------------------------------------------------------------------
		// Modifier une collection
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_modifier") {
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_COLLECTION . $collection->get("id_collection"), '');
	
			$gabarit = "biblio-collection-modifier.php";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_recherche") {
			
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("collection_recherche_chaine", $chaine);
			
			// Afficher la liste des collections correspondantes
			$demande = "collection_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche dans les collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_recherche_initialiser") {
			
			// Mettre en session
			$session->set("collection_recherche_chaine", "");
			
			// Afficher la liste des collections
			$demande = "collection_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Dupliquer une ou plusieurs collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_dupliquer") {
	
			// Obtenir le(s) formulaire(s) à dupliquer
			$listeElements = Web::getListeParam("collections_selection_");
	
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$collection = new Collection($log, $dbh);
					$collection->getCollectionParId($element, $idProjetActif);
					$collection->dupliquer();
				}
			} else {
				// Dupliquer la collection courante
				$collection->dupliquer();
			}
				
			// Réafficher la liste des collections
			$demande = "collection_liste";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Impression d'une ou plusieurs collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_imprimer_liste" || $demande == "collection_imprimer") {
			
			$contenu = "";
			
			// Vérifier si une sélection de collections est disponible
			$listeElements = Web::getListeParam("collections_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$collection = new Collection($log, $dbh);
					$collection->getCollectionParId($element, $idProjetActif);
					$contenu .= $collection->imprimer('');
				}
			} else {
				// Obtenir les données de la requête
				$collection->getDonneesRequete();				
				
				// Obtenir le contenu de la collection pour impression
				$contenu = $collection->imprimer('');
			}
	
			// Passer le contenu via la collection
			$collection->set("contenu", $contenu);
			
			// Obtenir le gabarit à utiliser pour l'impression
			$gabarit = $collection->get("gabarit_impression");
		}
	
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille une ou plusieurs collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_corbeille_liste" || $demande == "collection_corbeille") {
	
			// Vérifier si une sélection de collections est disponible
			$listeElements = Web::getListeParam("collections_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$collection = new Collection($log, $dbh);
					$collection->getCollectionParId($element, $idProjetActif);
					$collection->desactiver();
				}
			} else {
				// Mettre à la corbeille la collection actuelle
				$collection->desactiver();
			}
			
			// Réafficher la liste des items
			$demande = "collection_liste";
		}
			
		// ----------------------------------------------------------------------------------------
		// Exporter les collections sélectionnées
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_exporter") {
		
			$urlFichierZip = "";
		
			// Vérifier si une sélection de collections est disponible
			$listeElements = Web::getListeParam("collections_selection_");
			$listeCollections = array();
		
			if (! empty($listeElements) ) {
		
				// Préparer la liste des collections
				foreach ($listeElements as $element) {
					array_push($listeCollections, $element);
				}
		
				// Générer le XML pour exportation
				if (! empty($listeCollections)) {
					$t = new Collection($log, $dbh);
					$urlFichierZip = $t->exporterListeCollectionsXML($projetActif, $usager, $listeCollections);
				}
		
			} else {
		
				// Une seule collection à traiter
				array_push($listeCollections, $idCollection);
		
				// Générer le XML pour exportation
				if (! empty($listeCollections)) {
					$t = new Collection($log, $dbh);
					$urlFichierZip = $t->exporterListeCollectionsXML($projetActif, $usager, $listeCollections);
				}
			}
		
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "collection_liste";
			}			
		
			if ($urlFichierZip == "") {
				$messages = new Messages(ERR_043, Messages::ERREUR);
			} else {
				header("Location: $urlFichierZip");
			}
		}		
		
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des collections
		// ----------------------------------------------------------------------------------------
		if ($demande == "collection_liste") {
			
			$listeCollection = array();
			
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "collection_liste" || $demandeOrig == "collection_dupliquer") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
			
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdCollection = array();
			if ($session->get("collection_recherche_chaine") == "") {
				$listeIdCollection = $collection->getListeIdCollections($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("collection_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdCollection = $collection->rechercheCollections($chaine, $idProjetActif, $log, $dbh);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdCollection, $usager, $log, $dbh);
			
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
				
					$idElement = $listeIdCollection[$i];
	
					// Obtenir les informations de cet item
					$element = new Collection($log, $dbh);
					$element->getCollectionParId($idElement, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_COLLECTION . $element->get("id_collection"));
					
					// Ajouter aux résultats de recherche
					array_push($listeCollection, $element);
				}
			}
			
			$collection->preparerAffichageListe();		
			
			$gabarit = "biblio-collections-liste.php";
		}
	
		// ---------------------------------------------------------------------------------------- 
	
		//                              C A T É G O R I E S
		
		// ----------------------------------------------------------------------------------------		
		
		// ----------------------------------------------------------------------------------------
		// Ajouter une categorie
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_ajouter") {
	
			// Titre par défaut			
			$categorie->set("titre", TXT_NOUVELLE_CATEGORIE);
			$categorie->set("titre_menu", TXT_NOUVELLE_CATEGORIE);
			$categorie->set("id_projet", $idProjetActif);
			$categorie->ajouter();	
	
			// Mettre à jour l'identifiant de la categorie
			$idCategorie = $categorie->get("id_categorie");
			
			// Mettre à jour la liste des catégories
			$listeIdCategorie = $categorie->getListeIdCategories($idProjetActif);
	
			// Régler le gabarit à utiliser
			$gabarit = "biblio-categorie-modifier.php";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder une categorie
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_sauvegarder") {
	
			// Déterminer si un changement d'item est demandé
			$pageDest = Web::getParamNum("pagination_page_dest");
			
			if ( $pageDest != "") {
				
				// Obtenir la page de destination
				$idCategorie = $categorie->getIdCategorieParPage($pageDest);
				
				// Obtenir l'item correspondant
				$categorie->getCategorieParId($idCategorie, $idProjetActif);
				
				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);
				
				// Afficher la page par défaut pour modifier un questionnaire
				$demande = "categorie_modifier";
				
			} else {
			
				// Obtenir les informations à partir de la requête
				$categorie->getDonneesRequete();
				
				// Enregistrer les informations d'une categorie existante
				$categorie->enregistrer();
		
				// Message de confirmation
				$log->debug("bibliotheque.php: Sauvegarde de la categorie complétée");
				$messages = new Messages(MSG_001, Messages::CONFIRMATION);
		
				// Régler le gabarit à utiliser
				$gabarit = "biblio-categorie-modifier.php";
			}
		}
		
		// ----------------------------------------------------------------------------------------
		// Modifier une categorie
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_modifier") {
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_CATEGORIE . $categorie->get("id_categorie"), '');
	
			$gabarit = "biblio-categorie-modifier.php";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les categories
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_recherche") {
			
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("categorie_recherche_chaine", $chaine);
			
			// Afficher la liste des categories correspondantes
			$demande = "categorie_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche dans les categories
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_recherche_initialiser") {
			
			// Mettre en session
			$session->set("categorie_recherche_chaine", "");
			
			// Afficher la liste des categories
			$demande = "categorie_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Dupliquer une ou plusieurs categories
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_dupliquer") {
	
			// Obtenir le(s) formulaire(s) à dupliquer
			$listeElements = Web::getListeParam("categories_selection_");
	
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$categorie = new Categorie($log, $dbh);
					$categorie->getCategorieParId($element, $idProjetActif);
					$categorie->dupliquer();
				}
			} else {
				// Dupliquer la categorie courante
				$categorie->dupliquer();
			}
				
			// Réafficher la liste des categories
			$demande = "categorie_liste";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Impression d'une ou plusieurs categories
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_imprimer_liste" || $demande == "categorie_imprimer") {
			
			$contenu = "";
			
			// Vérifier si une sélection de categories est disponible
			$listeElements = Web::getListeParam("categories_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$categorie = new Categorie($log, $dbh);
					$categorie->getCategorieParId($element, $idProjetActif);
					$contenu .= $categorie->imprimer('');
				}
			} else {
				// Obtenir les données de la requête
				$categorie->getDonneesRequete();
								
				// Obtenir le contenu de la categorie pour impression
				$contenu = $categorie->imprimer('');
			}
	
			// Passer le contenu via la categorie
			$categorie->set("contenu", $contenu);
			
			// Obtenir le gabarit à utiliser pour l'impression
			$gabarit = $categorie->get("gabarit_impression");
		}
	
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille une ou plusieurs categories
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_corbeille_liste" || $demande == "categorie_corbeille") {
	
			// Vérifier si une sélection de categories est disponible
			$listeElements = Web::getListeParam("categories_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$categorie = new Categorie($log, $dbh);
					$categorie->getCategorieParId($element, $idProjetActif);
					$categorie->desactiver();
				}
			} else {
				// Mettre à la corbeille la categorie actuelle
				$categorie->desactiver();
			}
			
			// Réafficher la liste des items
			$demande = "categorie_liste";
		}
			
		
		// ----------------------------------------------------------------------------------------
		// Exporter les categories sélectionnées
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_exporter") {
		
			$urlFichierZip = "";
		
			// Vérifier si une sélection de categories est disponible
			$listeElements = Web::getListeParam("categories_selection_");
			$listeCategories = array();
		
			if (! empty($listeElements) ) {
		
				// Préparer la liste des catégories
				foreach ($listeElements as $element) {
					array_push($listeCategories, $element);
				}
		
				// Générer le XML pour exportation
				if (! empty($listeCategories)) {
					$t = new Categorie($log, $dbh);
					$urlFichierZip = $t->exporterListeCategoriesXML($projetActif, $usager, $listeCategories);
				}
		
			} else {
		
				// Une seule categorie à traiter
				array_push($listeCategories, $idCategorie);
		
				// Générer le XML pour exportation
				if (! empty($listeCategories)) {
				$t = new Categorie($log, $dbh);
				$urlFichierZip = $t->exporterListeCategoriesXML($projetActif, $usager, $listeCategories);
				}
			}
		
			// Déterminer la demande de retour
			$demandeRetourParam = Web::getParam("demandeRetour");
			if ($demandeRetourParam != "") {
				$demande = $demandeRetourParam;
			} else {
				// Défaut
				$demande = "categorie_liste";
			}
		
			if ($urlFichierZip == "") {
				$messages = new Messages(ERR_043, Messages::ERREUR);
			} else {
				header("Location: $urlFichierZip");
			}
		}
		
		
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des categories
		// ----------------------------------------------------------------------------------------
		if ($demande == "categorie_liste") {
			
			$listeCategorie = array();
			
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "categorie_liste" || $demandeOrig == "categorie_dupliquer") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
			
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdCategorie = array();
			if ($session->get("categorie_recherche_chaine") == "") {
				$listeIdCategorie = $categorie->getListeIdCategories($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("categorie_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdCategorie = $categorie->rechercheCategories($chaine, $idProjetActif, $log, $dbh);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdCategorie, $usager, $log, $dbh);
			
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
				
					$idElement = $listeIdCategorie[$i];
	
					// Obtenir les informations de cet item
					$element = new Categorie($log, $dbh);
					$element->getCategorieParId($idElement, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_CATEGORIE . $element->get("id_categorie"));
					
					// Ajouter aux résultats de recherche
					array_push($listeCategorie, $element);
				}
			}
			
			$categorie->preparerAffichageListe();		
			
			$gabarit = "biblio-categories-liste.php";
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
		// Corbeille récupérer
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
		// Corbeille supprimer
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
			$gabarit = "biblio-corbeille.php";
		}
	
		// ----------------------------------------------------------------------------------------
		// Importer
		// ----------------------------------------------------------------------------------------
		if ($demande == "medias_importer") {
	
			$gabarit = "biblio-media-importer.php";
		}
		
		
		// ---------------------------------------------------------------------------------------- 
	
		//                        T R A I T E M E N T S   C O M M U N S 
		
		// ----------------------------------------------------------------------------------------			
		
		// ----------------------------------------------------------------------------------------
		// Préparer les options (select) pour les différents objets
		// ----------------------------------------------------------------------------------------
		$item->preparerAffichage();
		$collection->preparerAffichage();
		$categorie->preparerAffichage();
		$langue->preparerAffichage();
		$terme->preparerAffichage();
	
		// ----------------------------------------------------------------------------------------
		// Conserver les informations sur l'item traité
		// ----------------------------------------------------------------------------------------
		$session = new Session;
		$session->set("idItem", $idItem);
		$session->set("typeItem", $item->get("type_item"));
	
		// ----------------------------------------------------------------------------------------
		// Préparer les chaînes de recherche
		// ----------------------------------------------------------------------------------------	
		$chaineRech = $session->get("item_recherche_chaine");
		$chaineRechCorbeille = $session->get("corbeille_recherche_chaine");
		$chaineRechCollection = $session->get("collection_recherche_chaine");
		$chaineRechCategorie = $session->get("categorie_recherche_chaine");
		$chaineRechLangue = $session->get("langue_recherche_chaine");
			
		// ----------------------------------------------------------------------------------------
		// Traitement du gabarit
		// ----------------------------------------------------------------------------------------
		if ($gabarit != "") {
			include(REPERTOIRE_GABARITS . $gabarit);
		}
		
		
	} else {
		// Erreur: la demande est incorrecte
		Erreur::erreurFatal('006', "[bibliotheque.php] Demande incorrecte : '$demande'", $log);
	}
	
	// Terminer
	$log->debug("bibliotheque.php: Fin");
	
} catch (Exception $e) {
	Erreur::erreurFatal('018', "bibliotheque.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}