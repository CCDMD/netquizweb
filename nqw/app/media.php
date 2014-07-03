<?php

/** 
 * Aiguilleur : media.php
 * 
 * Aiguillage des demandes pour les médias
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {

	// Configuration et démarrage
	$aiguilleur = "media";
	require_once 'init.php';
	
	$demandesPermises = array(	
						      "liste", "media_liste", "media_ajouter", "media_sauvegarder", "media_modifier", "media_recherche", "media_recherche_initialiser", 
						      "media_dupliquer", "media_imprimer_liste","media_corbeille_liste", "media_imprimer", "media_corbeille",
							  "media_liste_suivi_activer", "media_liste_suivi_desactiver", "media_suivi_activer", "media_suivi", "media_afficher",
							  "media_importer", "media_importer_sauvegarder", "media_selectionner", "media_selectionner_recherche", "media_selectionner_recherche_initialiser",
							  "media_presenter"
							  );
							  
	$log->debug("media.php: Début");
	
	// ----------------------------------------------------------------------------------------
	// Initialisation
	// ----------------------------------------------------------------------------------------
	$gabarit = "";
	$session = new Session();
	$pagination = new Pagination(array(), $usager, $log, $dbh);
	
	// ----------------------------------------------------------------------------------------
	// Répertoires par défaut
	// ----------------------------------------------------------------------------------------
	$prefixRepertoireMedia = Securite::nettoyerNomfichier($projetActif->get("repertoire")) . "/";
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la demande
	// ----------------------------------------------------------------------------------------
	$demande = Web::getParam('demande');
	if ($demande == "" || $demande == "liste") {
		$demande = "media_liste";	
	}
	$log->debug("media.php:   --------------------------- Aiguillage de la demande '$demande' ---------------------------");
	
	// Prendre note de la demande originale
	$demandeOrig = $demande;
	
	// ----------------------------------------------------------------------------------------
	// Obtenir les ids des objets
	// ----------------------------------------------------------------------------------------
	$idMedia = Web::getParam("media_id_media");
	
	// ----------------------------------------------------------------------------------------
	// Obtenir la page à afficher
	// ----------------------------------------------------------------------------------------
	$pageMedia = Web::getParam('media_page');
	
	// ----------------------------------------------------------------------------------------
	// Instancier les objets de base
	// ----------------------------------------------------------------------------------------
	$media = new Media($log, $dbh);
	
	// ----------------------------------------------------------------------------------------
	// Préparer les informations pour la page
	// ----------------------------------------------------------------------------------------
	$pageInfos = array();	
	
	// ----------------------------------------------------------------------------------------
	// Vérifier si un changement de page est requis
	// ----------------------------------------------------------------------------------------
	if ($pageMedia != "") {
		$idMedia = $media->getIdMediaParPage($pageMedia);
	}
	
	// ----------------------------------------------------------------------------------------
	// Obtenir le filtre par type 
	// ----------------------------------------------------------------------------------------
	$filtreTypeMedia = Web::getParam("filtre_type_media");
	if ($filtreTypeMedia == "") {
		// Vérifier si un filtre est spécifié dans la session
		$filtreTypeMedia = $session->get("pref_filtre_type_media");
	}
	$pageInfos['filtre_type_media'] = $filtreTypeMedia;
		
	// ----------------------------------------------------------------------------------------
	// Instancier les objets au besoin
	// ----------------------------------------------------------------------------------------
	if ($idMedia != "") {
		$media->getMediaParId($idMedia, $idProjetActif);	
	}
	
	// ----------------------------------------------------------------------------------------
	// Obtenir l'onglet sélectionné (doit être 1, 2, 3, 4 ou "")
	// ----------------------------------------------------------------------------------------
	$onglet = Web::getParam("onglet");
	if ($onglet != "" && $onglet != "1" && $onglet != "2" && $onglet != "3") {
		$onglet = 1;
	}
	$chaineRech = "";
	
	// Vérifier la demande
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {
		
		$log->debug("media.php:   Traiter la demande '$demande'");
	
		// ----------------------------------------------------------------------------------------
		// Afficher un media
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_afficher") {
	
			if ($media->get("source") != "web") {
				
				// Déterminer la source à utiliser
				$fichier = REPERTOIRE_MEDIA . $prefixRepertoireMedia . $media->get("fichier");

				// Vérifier si le fichier existe et le statut du média
				if (! file_exists($fichier) || $media->get("fichier") == "" || $media->get("statut") != MEDIA::STATUT_ACTIF) {
					// Sinon utiliser l'image par défaut
					$fichier = IMAGE_ABSENTE_DEFAUT_CHEMIN_COMPLET;
				}
				
				// Obtenir le type de fichier
				$mimeType = Fichiers::get_mime_type($fichier);
				
				// Obtenir le contenu du fichier
				$data = file_get_contents($fichier);

				// Envoi
				header("Content-type: " . $mimeType);
				print $data;
			} else {
				header("Location: http://" . $media->get("url"));
			}
		}
	
		// ----------------------------------------------------------------------------------------
		// Présenter un média (seul sur la page)
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_presenter") {
			
			if ( ($media->get("source") != "web" && ($media->get("type") == "image" || $media->get("type") == "") ) ) {

				// Déterminer la source à utiliser
				$fichier = REPERTOIRE_MEDIA . $prefixRepertoireMedia . $media->get("fichier");
				
				// Vérifier si le fichier existe
				if (! file_exists($fichier) || $media->get("fichier") == "") {
					
					// Sinon utiliser l'image par défaut
					$media->set("fichier", IMAGE_ABSENTE_DEFAUT_CHEMIN_COMPLET);
				}
				
			} 
	
			$gabarit = "biblio-media-presenter.php";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les medias pour la sélection
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_selectionner_recherche") {
			
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("media_selectionner_recherche_chaine", $chaine);
			
			// Afficher la liste des medias correspondantes
			$demande = "media_selectionner";
		}
	
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche dans les medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_selectionner_recherche_initialiser") {
			
			// Mettre en session
			$session->set("media_selectionner_recherche_chaine", "");
			
			// Afficher la liste des medias
			$demande = "media_selectionner";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Sélectionner un media
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_selectionner") {
	
			$listeMedia = array();
			
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "media_selectionner") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
			}
			
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdMedia = array();
			if ($session->get("media_selectionner_recherche_chaine") == "") {
				$listeIdMedia = $media->getListeIdMedias($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("media_selectionner_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdMedia = $media->rechercheMedias($chaine, $idProjetActif, $log, $dbh);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdMedia, $usager, $log, $dbh);
			
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
				
					$idElement = $listeIdMedia[$i];
	
					// Obtenir les informations de cet item
					$element = new Media($log, $dbh);
					$element->getMediaParId($idElement, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_MEDIA . $element->get("id_media"));
					
					// Ajouter aux résultats de recherche
					array_push($listeMedia, $element);
				}
			}
			
			$media->preparerAffichageListe();		
			
			// Obtenir le titre du questionnaire et de l'item pour afficher en 
			// "fil d'Ariane" dans l'entête de la fenêtre de sélection des fichiers
			$media->set("questionnaire_titre_menu", $session->get("questionnaire_titre_menu"));
			$media->set("item_titre_menu", $session->get("item_titre_menu"));
			
			$gabarit = "biblio-media-selectionner.php";		
		}		
	
		// ----------------------------------------------------------------------------------------
		// Importer un media
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_importer") {
	
			// Titre par défaut			
			$media->set("id_projet", $idProjetActif);
			$media->set("titre", TXT_NOUVEAU_MEDIA);
			$media->set("titre_menu", TXT_NOUVEAU_MEDIA);
			$media->ajouter();	
	
			// Mettre à jour l'identifiant de la media
			$idMedia = $media->get("id_media");
			
			// Obtenir le titre du questionnaire et de l'item pour afficher en 
			// "fil d'Ariane" dans l'entête de la fenêtre de sélection des fichiers
			$media->set("questionnaire_titre_menu", $session->get("questionnaire_titre_menu"));
			$media->set("item_titre_menu", $session->get("item_titre_menu"));
					
			// Régler le gabarit à utiliser
			$gabarit = "biblio-media-importer.php";
		}		
		
		
		// ----------------------------------------------------------------------------------------
		// Ajouter un media
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_ajouter") {
	
			// Titre par défaut			
			$media->set("id_projet", $idProjetActif);
			$media->set("titre", TXT_NOUVEAU_MEDIA);
			$media->set("titre_menu", TXT_NOUVEAU_MEDIA);
			$media->ajouter();	
	
			// Mettre à jour l'identifiant de la media
			$idMedia = $media->get("id_media");
			
			// Mettre à jour la liste des médias
			$listeIdMedia = $media->getListeIdMedias($idProjetActif);
	
			// Régler le gabarit à utiliser
			$gabarit = "biblio-media-modifier.php";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Sauvegarder un média
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_sauvegarder" || $demande == "media_importer_sauvegarder") {
	
			// Déterminer si un changement d'item est demandé
			$pageDest = Web::getParam("pagination_page_dest");
			
			if ( $pageDest != "") {
				
				// Obtenir la page de destination
				$idMedia = $media->getIdMediaParPage($pageDest);
				
				// Obtenir l'item correspondant
				$media->getMediaParId($idMedia, $idProjetActif);
				
				// Mettre à jour la page courante dans la session
				$session->set("pagination_page_cour", $pageDest);
				
				// Afficher la page par défaut pour modifier un questionnaire
				$demande = "media_modifier";
				
			} else {
			
				// Obtenir les informations à partir de la requête
				$media->getDonneesRequete();
				
				// Transfert du fichier
				$statut = $media->transfertFichier();
				
				// Analyse de l'URL
				$media->analyseURLMedia();
								
				// Déterminer si le fichier correspond au type de fichier attendu pour le transfert (filtre par type de fichier)
				if ($filtreTypeMedia != "" && $media->get("type") != $filtreTypeMedia) {
					$statut = -4;
					
					// Remettre le nom de fichier initial
					$media->set("titre", TXT_NOUVEAU_MEDIA);
				}
	
				if ($statut == 0) {
				
					// Enregistrer les informations d'un média existante
					$media->enregistrer();
			
					// Message de confirmation
					$log->debug("bibliotheque.php: Sauvegarde de la media complétée");
					$messages = new Messages(MSG_001, Messages::CONFIRMATION);
					
				} else {
					
					// Erreur
					if ($statut == -1) {
						// Impossible de créer le répertoire pour les médias
						$messages = new Messages(ERR_013, Messages::ERREUR);
					} elseif( $statut == -2) {
						// Type de fichier non supporté
						$messages = new Messages(ERR_014, Messages::ERREUR);
					} elseif( $statut == -3) {
						// Erreur de transfert 
						$messages = new Messages(ERR_015, Messages::ERREUR);
					} elseif( $statut == -4) {
						// Le type de fichier n'est pas celui attendu
						$messages = new Messages(ERR_236, Messages::ERREUR);
					}
				}

				// Obtenir les liens vers les éléments qui utilisent le média
				$media->getListeQuestionnairesUtilisantMedia();
				$media->getListeItemsUtilisantMedia();
				$media->getListeLanguesUtilisantMedia();
				
				// Régler le gabarit à utiliser
				if ($demande == "media_importer_sauvegarder") {
					if ($statut < 0) {
						// Erreur
						$gabarit = "biblio-media-importer.php";
					} else {
						$gabarit = "biblio-media-importer-fermer.php";
					}
				} else {
					$gabarit = "biblio-media-modifier.php";
				}
			}
		}
		
		// ----------------------------------------------------------------------------------------
		// Modifier un média
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_modifier") {
	
			// Obtenir les liens vers les éléments qui utilisent le média
			$media->getListeQuestionnairesUtilisantMedia();
			$media->getListeItemsUtilisantMedia();
			$media->getListeLanguesUtilisantMedia();
			
			// Vérifier les verrous
			$verrou = new Verrou($log, $dbh);
			$messages = $verrou->getMessageVerrous($idUsager, $idProjetActif, TXT_PREFIX_MEDIA . $media->get("id_media"), '');
			
			$gabarit = "biblio-media-modifier.php";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Effectuer une recherche dans les medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_recherche") {
			
			// Récupérer la chaîne de recherche
			$chaine = Web::getParam("chaine");
			
			// Mettre en session
			$session->set("media_recherche_chaine", $chaine);
			
			// Afficher la liste des medias correspondantes
			$demande = "media_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Réinitialiser la recherche dans les medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_recherche_initialiser") {
			
			// Mettre en session
			$session->set("media_recherche_chaine", "");
			
			// Afficher la liste des medias
			$demande = "media_liste";
		}
	
		// ----------------------------------------------------------------------------------------
		// Dupliquer une ou plusieurs medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_dupliquer") {
	
			// Obtenir le(s) formulaire(s) à dupliquer
			$listeElements = Web::getListeParam("medias_selection_");
	
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$media = new Media($log, $dbh);
					$media->getMediaParId($element, $idProjetActif);
					$media->dupliquer();
				}
			} else {
				// Dupliquer la media courante
				$media->dupliquer();
			}
				
			// Réafficher la liste des medias
			$demande = "media_liste";
		}	
	
		// ----------------------------------------------------------------------------------------
		// Impression d'une ou plusieurs medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_imprimer_liste" || $demande == "media_imprimer") {
			
			$contenu = "";
			
			// Vérifier si une sélection de medias est disponible
			$listeElements = Web::getListeParam("medias_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$media = new Media($log, $dbh);
					$media->getMediaParId($element, $idProjetActif);
					$contenu .= $media->imprimer('');
				}
			} else {
				// Obtenir les données de la requête
				$media->getDonneesRequete();
				
				// Obtenir le contenu de la media pour impression
				$contenu = $media->imprimer('');
			}
	
			// Passer le contenu via la media
			$media->set("contenu", $contenu);
			
			// Obtenir le gabarit à utiliser pour l'impression
			$gabarit = $media->get("gabarit_impression");
		}
	
		// ----------------------------------------------------------------------------------------
		// Mettre à la corbeille une ou plusieurs medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_corbeille_liste" || $demande == "media_corbeille") {
	
			// Vérifier si une sélection de medias est disponible
			$listeElements = Web::getListeParam("medias_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$media = new Media($log, $dbh);
					$media->getMediaParId($element, $idProjetActif);
					$media->desactiver();
				}
			} else {
				// Mettre à la corbeille la media actuelle
				$media->desactiver();
			}
			
			// Réafficher la liste des items
			$demande = "media_liste";
		}
		
		// ----------------------------------------------------------------------------------------
		// Activer le suivi sur un média dans la liste 
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_liste_suivi_activer") {
	
			// Activer le suivi
			$media->activerSuivi();
			
			$demande = "media_liste";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Désactiver le suivi sur un média dans la liste 
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_liste_suivi_desactiver") {
	
			// Activer le suivi
			$media->desactiverSuivi();
			
			$demande = "media_liste";
		}	
		
		// ----------------------------------------------------------------------------------------
		// Activer ou désactiver le suivi sur un média (requête AJAX) 
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_suivi") {
	
			if ($media->get("suivi") == "1") {
				// Désactiver le suivi
				$media->desactiverSuivi();
				echo "0";
			} else {
				// Activer le suivi
				$media->activerSuivi();
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
		// Activer le suivi sur un ou plusieurs medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_suivi_activer") {
	
			// Vérifier si une sélection de medias est disponible
			$listeElements = Web::getListeParam("medias_selection_");
			if (! empty($listeElements) ) {
				foreach ($listeElements as $element) {
					$media = new Media($log, $dbh);
					$media->getMediaParId($element, $idProjetActif);
					$media->activerSuivi();
				}
			} else {
			
				// Activer le suivi sur l'media actuel
				$media->activerSuivi();
			}
			
			// Réafficher la liste des medias
			$demande = "media_liste";
		}	
				
		// ----------------------------------------------------------------------------------------
		// Afficher la liste des medias
		// ----------------------------------------------------------------------------------------
		if ($demande == "media_liste") {
			
			$listeMedia = array();
			
			// Déterminer si la pagination doit être remise à la page 1
			if ($demandeOrig == "media_liste" || $demandeOrig == "media_dupliquer") {
				$session = new Session;
				$session->set("pagination_page_cour", "1");
				//$session->delete("pref_filtre_type_media");
			}
			
			// Déterminer si on affiche la liste des items ou la recherche
			$listeIdMedia = array();
			if ($session->get("media_recherche_chaine") == "") {
				$listeIdMedia = $media->getListeIdMedias($idProjetActif);
			} else {
				// Effectuer une recherche
				$chaine = $session->get("media_recherche_chaine");
				if ($chaine != '') {
					$chaine = '%' . $chaine . '%';
				}
				$listeIdMedia = $media->rechercheMedias($chaine, $idProjetActif, $log, $dbh);
			}
			
			// Appliquer la pagination
			$pagination = new Pagination($listeIdMedia, $usager, $log, $dbh);
			
			if ($pagination->getNbResultats() > 0) {
				for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
				
					$idElement = $listeIdMedia[$i];
	
					// Obtenir les informations de cet item
					$element = new Media($log, $dbh);
					$element->getMediaParId($idElement, $idProjetActif);
					$element->set("id_prefix", TXT_PREFIX_MEDIA . $element->get("id_media"));
					$element->getListeQuestionnairesUtilisantMedia();
					$element->getListeItemsUtilisantMedia();
					$element->getListeLanguesUtilisantMedia();
					
					// Ajouter aux résultats de recherche
					array_push($listeMedia, $element);
				}
			}
			
			$media->preparerAffichageListe();		
			
			$gabarit = "biblio-medias-liste.php";
		}
		
		// ---------------------------------------------------------------------------------------- 
	
		//                        T R A I T E M E N T S   C O M M U N S 
		
		// ----------------------------------------------------------------------------------------			
					
		// ----------------------------------------------------------------------------------------
		// Préparer les options (select) pour les différents objets
		// ----------------------------------------------------------------------------------------
		$media->preparerAffichage();
	
		// ----------------------------------------------------------------------------------------
		// Préparer les chaînes de recherche
		// ----------------------------------------------------------------------------------------	
		$chaineRechMedia = $session->get("media_recherche_chaine");
		$chaineRechMediaSel = $session->get("media_selectionner_recherche_chaine");
			
		// ----------------------------------------------------------------------------------------
		// Traitement du gabarit
		// ----------------------------------------------------------------------------------------
		if ($gabarit != "") {
			include(REPERTOIRE_GABARITS . $gabarit);
		}
		
	} else {
		// Erreur: la demande est incorrecte
		Erreur::erreurFatal('006', "[media.php] Demande incorrecte : '$demande'", $log);
	}
	
	// Terminer
	$log->debug("media.php: Fin");
	
} catch (Exception $e) {
	Erreur::erreurFatal('018', "media.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}