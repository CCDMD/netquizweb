<?php

/** 
 * Classe Importation
 * 
 * Gestion de l'importation de données dans l'application
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
class Importation {

	protected $log;
	protected $dbh; 
	protected $repertoireImport;
	protected $repertoireXML;
	protected $xml;
	protected $usager;
	protected $projet;
	protected $fichierXML;
	protected $conversionMedias;
	protected $conversionItems;
	protected $conversionSections;
	protected $conversionTermes;
	protected $msgConf;
	protected $questLangueDefaut;
	protected $itemLangueApercuDefaut;
	protected $itemThemeApercuDefaut;
	protected $listeItemsVerif; 
	
	
	/**
	 * Constructeur
	 * 
	 * @param usager usager
	 * @param projet projet
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function __construct( $usager, $projet, Log $log, PDO $dbh ) {
		$log->debug("Importation::construct()");
		
		$this->log = $log;
		$this->dbh = $dbh;
		$this->usager = $usager;
		$this->projet = $projet;
		$this->repertoireImport = "";
		$this->repertoireXML = "";
		$this->xml = "";
		$this->conversionMedias = array();
		$this->conversionItems = array();
		$this->conversionQuest = array();
		$this->msgConf = "";
		
		// Obtenir la langue par défaut du profil utilisateur
		$this->questLangueDefaut = $usager->get("pref_apercu_langue");
		$this->itemLangueApercuDefaut = $usager->get("pref_apercu_langue");
		
		// Obtenir le thème par défaut du profil utilisateur
		$this->itemThemeApercuDefaut = $usager->get("pref_apercu_theme");
		
		$this->listeItemsVerif = array();

		return;
	}		

	/**
	 * 
	 * Importation d'un fichier XML
	 * 
	 */ 
	public function importerXML() {

		$this->log->debug("Importation::importerXML() Début");
		
		$messages = "";
		$fichier = "";
		$fichierNom = "";
		$fichierExtension = "";
		$this->repertoireXML = "";
		
		$this->log->debug("Importation::importerXML() Importer un fichier XML");
		$this->log->debug("Importation::importerXML() Valeur par défaut : questLangueDefaut : '" . $this->questLangueDefaut . "'  itemLangueApercuDefaut : '" . $this->itemLangueApercuDefaut . "' itemThemeApercuDefaut : '" . $this->itemThemeApercuDefaut . "'");
		
		// Transfert du fichier
		$messages .= $this->transfertFichier();
		
		// Charger le XML
		if ($messages == "") {
			$this->log->debug("Importation::importerXML() Chargement du fichier XML '" . $this->fichierXML . "'");
			$this->xml = simplexml_load_file($this->fichierXML);
			
			if ($this->xml == "") {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_158 . HTML_LISTE_ERREUR_FIN;
			}
		}			
		
		// Vérifier le XML
		if ($messages == "") {		
			$messages .= $this->verifierXML();
		}
		
		// Importer les médias
		if ($messages == "") {
			$messages .= $this->importerMedias();
		}

		// Importer les langues
		if ($messages == "") {
			$messages .= $this->importerLangues();
		}		
		
		// Importer les catégories
		if ($messages == "") {
			$messages .= $this->importerCategories();
		}		
		
		// Importer les collections
		if ($messages == "") {
			$messages .= $this->importerCollections();
		}
		
		// Importer les termes
		if ($messages == "") {
			$messages .= $this->importerTermes();
		}		
		
		// Importer les items
		if ($messages == "") {
			$messages .= $this->importerItems();
		}
		
		// Importer les questionnaires
		if ($messages == "") {
			$messages .= $this->importerQuestionnaires();
		}		
		
		// Messages de confirmation
		if ($messages == "") {
			$messages = $this->msgConf;
		}
		
		// Supprimer le fichier
		$this->supprimerFichiers();
		
		$this->log->debug("Importation::importerXML() Fin");
		
		return $messages;	
	}

	
	/**
	 * 
	 * Importer les questionnaires
	 * 
	 */ 
	public function importerQuestionnaires() {

		$this->log->debug("Importation::importerQuestionnaires() Début");
		
		$listeCollections = array();

		$messages = "";
		
		// Traiter chacun des questionnaires
		foreach ($this->xml->questionnaire as $questXML) {
			
			// Préparer un nouveau questionnaire
			$quest = new Questionnaire($this->log, $this->dbh);
			
			$quest->set("id_projet", $this->projet->get("id_projet"));
			$quest->set("titre", Web::nettoyerChaineProvenantDuXML((string)$questXML->titre));
			$quest->set("titre_long", Web::nettoyerChaineProvenantDuXML((string)$questXML->titre_long));
			$quest->set("suivi", Web::nettoyerChaineProvenantDuXML((string)$questXML->suivi));
			$quest->set("temps_reponse_calculer", Web::nettoyerChaineProvenantDuXML((string)$questXML->temps_reponse_calculer));
			$quest->set("generation_question_type", Web::nettoyerChaineProvenantDuXML((string)$questXML->generation_question_type));
			$quest->set("generation_question_nb", Web::nettoyerChaineProvenantDuXML((string)$questXML->generation_question_nb));
			$quest->set("temps_passation_type", Web::nettoyerChaineProvenantDuXML((string)$questXML->temps_passation_type));
			$quest->set("temps_passation_heures", Web::nettoyerChaineProvenantDuXML((string)$questXML->temps_passation_heures));
			$quest->set("temps_passation_minutes", Web::nettoyerChaineProvenantDuXML((string)$questXML->temps_passation_minutes));
			$quest->set("essais_repondre_type", Web::nettoyerChaineProvenantDuXML((string)$questXML->essais_repondre_type));
			$quest->set("essais_repondre_nb", Web::nettoyerChaineProvenantDuXML((string)$questXML->essais_repondre_nb));
			$quest->set("affichage_resultats_type", Web::nettoyerChaineProvenantDuXML((string)$questXML->affichage_resultats_type));
			$quest->set("demarrage_media_type", Web::nettoyerChaineProvenantDuXML((string)$questXML->demarrage_media_type));
			$quest->set("id_langue_questionnaire", Web::nettoyerChaineProvenantDuXML((string)$questXML->id_langue_questionnaire));
			$quest->set("theme", Web::nettoyerChaineProvenantDuXML((string)$questXML->theme));
			$quest->set("mot_bienvenue", Web::nettoyerChaineProvenantDuXML((string)$questXML->mot_bienvenue));
			$quest->set("note", Web::nettoyerChaineProvenantDuXML((string)$questXML->note));
			$quest->set("generique", Web::nettoyerChaineProvenantDuXML((string)$questXML->generique));
			$quest->set("media_titre", Web::nettoyerChaineProvenantDuXML((string)$questXML->media_titre));
			$quest->set("media_texte", Web::nettoyerChaineProvenantDuXML((string)$questXML->media_texte));
			$quest->set("texte_fin", Web::nettoyerChaineProvenantDuXML((string)$questXML->texte_fin));
			$quest->set("remarque", Web::nettoyerChaineProvenantDuXML((string)$questXML->remarque));
			$quest->set("id_langue_questionnaire", $this->questLangueDefaut);
			
			if ($questXML->media_image != "" && (int)$questXML->media_image > 0) {
				$quest->set("media_image", $this->conversionMedias[(int)$questXML->media_image]);
			}
			
			if ($questXML->media_son != "" && (int)$questXML->media_son > 0) {
				$quest->set("media_son", $this->conversionMedias[(int)$questXML->media_son]);
			}
			
			if ($questXML->media_video != "" && (int)$questXML->media_video > 0) {
				$quest->set("media_video", $this->conversionMedias[(int)$questXML->media_video]);
			}		
			
			// Vérifier le thème
			$repertoireSourceTheme = REPERTOIRE_THEMES . $quest->get("theme") . "/";
			$this->log->debug("Importation::verifierTheme() Vérifier que le thème sélectionné existe ('$repertoireSourceTheme')");
			if (!is_dir($repertoireSourceTheme)) {
				$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . ERR_204 . HTML_LISTE_ERREUR_FIN;
				$this->log->debug("Importation::importerQuestionnaires() Problème détecté : Le répertoire du thème sélectionné n'existe pas (theme = '$theme'). Le thème par défaut sera utilisé");
				$quest->set("theme", FICHIER_THEME_DEFAUT);
			}

			// Importer une collection seulement si l'id du projet et domaine sont les mêmes
			$idProjetImport = Web::nettoyerChaineProvenantDuXML((string)$questXML->id_projet);
			$domaineImport = Web::nettoyerChaineProvenantDuXML((string)$questXML->url_domaine);
			if (URL_DOMAINE == $domaineImport && $this->projet->get("id_projet") == $idProjetImport) {
				$quest->set("id_collection", Web::nettoyerChaineProvenantDuXML((string)$questXML->id_collection));
			}			
			
			// Ajouter une collection provenant de Netquiz Pro 4 au besoin
			$collectionNQPRO = trim(Web::nettoyerChaineProvenantDuXML((string)$questXML->collection_nqpro));

			if ($collectionNQPRO != "") {

				// Vérifier si la collection existe
				if (array_key_exists($collectionNQPRO, $listeCollections)) {
					$quest->set("id_collection", $listeCollections[$collectionNQPRO]);
				} else {
					// Créer la collection
					$collection = new Collection($this->log, $this->dbh);
					$collection->set("id_projet", $this->projet->get("id_projet"));
					$collection->set("titre", $collectionNQPRO);
					
					// Ajouter la collection
					$collection->ajouter();

					// Ajouter à la liste
					$listeCollections[$collectionNQPRO] = $collection->get("id_collection");
				}
				
				// Régler l'id de la collection au niveau du questionnaire
				$quest->set("id_collection", $collection->get("id_collection"));
			}
			
			
			// Remplacer les médias dans les rich textes
			$this->remplacerIDMediaTextes($quest);
							
			// Sauvegarder le questionnaire
			$quest->ajouter();
			$this->log->debug("Importation::importerQuestionnaires() Nouveau questionnaire ajouté : '" . $quest->get("id_questionnaire") . "'");
			
			// Ajouter les sections
			foreach ($questXML->section as $sectionXML) {
				
				// Déterminer l'id et le type d'item
				$typeItem = "15";

				// Préparer objet item
				$itemFactory = new Item($this->log, $this->dbh); 
				$item = $itemFactory->instancierItemParType($typeItem, $this->projet->get("id_projet"), '');
			
				$item->set("id_projet", $this->projet->get("id_projet"));
				$item->set("id_questionnaire", $quest->get("id_questionnaire"));
				$item->set("titre", Web::nettoyerChaineProvenantDuXML((string)$sectionXML->titre));
				$item->set("generation_question_type", Web::nettoyerChaineProvenantDuXML((string)$sectionXML->generation_question_type));
				$item->set("statut", "1");
				
				// Ajout
				$item->ajouter();
				$this->log->debug("Importation::importerQuestionnaires() Section ajoutée : '" . $item->get("id_item") . "'");
				
				// Conversions
				$this->conversionSections[(int)$sectionXML->id_section] = $item->get("id_item");
			}
			
			// Ajouter les items
			$menu = new Menu($this->log, $this->dbh);
			$ordre = 0;
			
			// Traiter chacun des items
			foreach ($questXML->questionnaire_item as $questItemXML) {
				
				
				$type = $questItemXML->type;

				if ($type == "item") {
				
					// Obtenir l'id de l'item 
					$idItem = $this->conversionItems[(int)$questItemXML->id_item];
					
					$this->log->debug("Importation::importerQuestionnaires() idSection Orig : '" . $questItemXML->id_section . "'");
					
					// Section si applicable
					$idSection = 0;
					if ($questItemXML->id_section != "" && (int)$questItemXML->id_section > 0) {
						$idSection = $this->conversionSections[(int)$questItemXML->id_section];
					}
					$this->log->debug("Importation::importerQuestionnaires() idSection Nouv : '$idSection'");
					
					// Charger l'item
					$this->log->debug("Importation::importerQuestionnaires() Charger l'item '$idItem' avec section '$idSection'");
					$itemFactory = new Item($this->log, $this->dbh); 
					$item = $itemFactory->instancierItemParType('', $this->projet->get("id_projet"), $idItem);
				
					// Ajouter les informations complémentaires
					$item->set("id_questionnaire", $quest->get("id_questionnaire"));
					$item->set("id_item", $idItem);
					$item->set("id_projet", $quest->get("id_projet"));
					$item->set("section", $idSection);
					$item->set("ordre", $ordre);
					$item->set("ponderation_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->ponderation_quest));
					$item->set("demarrer_media_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->demarrer_media_quest));
					$item->set("afficher_solution_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->afficher_solution_quest));
					$item->set("ordre_presentation_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->ordre_presentation_quest));
					$item->set("type_etiquettes_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->type_etiquettes_quest));
					$item->set("type_bonnesreponses_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->type_bonnesreponses_quest));
					$item->set("points_retranches_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->points_retranches_quest));
					$item->set("majmin_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->majmin_quest));
					$item->set("ponctuation_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->ponctuation_quest));
					$item->set("orientation_elements_quest", Web::nettoyerChaineProvenantDuXML((string)$questItemXML->orientation_elements_quest));
	
					$this->log->debug("Importation::importerQuestionnaires() Enregistrer le lien pour le questionnaire '" . $quest->get("id_questionnaire") . "' et l'item '" . $idItem . "'");
					$item->ajouterLienQuestionnaireItemOrdreSection();
				
				} elseif ($type == "section") {
					
					$idSection = $this->conversionSections[(int)$questItemXML->id_section];
					
					// Charger la section
					$this->log->debug("Importation::importerQuestionnaires() Charger la section '$idSection'");
					$itemFactory = new Item($this->log, $this->dbh); 
					$item = $itemFactory->instancierItemParType('', $this->projet->get("id_projet"), $idSection);					
					
					// Ajouter l'item au bon endroit dans le menu
					$item->set("ordre", $ordre);
					$item->set("id_questionnaire", $quest->get("id_questionnaire"));
					$this->log->debug("Importation::importerQuestionnaires() Enregistrer un lien au menu pour id_questionnaire : '" . $quest->get("id_questionnaire") . "' et ordre = '" . $item->get("ordre") . "'");
					$item->enregistrerLienQuestionnaireItemOrdreSection();
					
				}
			
				$ordre++;
			}

			// Mettre à jour le nombre d'items pour le questionnaire
			$quest->updateNombreItems($quest->get("id_questionnaire"), $quest->get("id_projet"));
			
			// Traiter chacun des termes
			foreach ($questXML->questionnaire_terme as $termeXML) {
			
				// Obtenir l'id du terme convertit
				$idTerme = $this->conversionTermes[(int)$termeXML->id_terme];
			
				// Ajouter le lien entre le questionnaire et le terme
				$this->log->debug("Importation::importerQuestionnaires() Ajouter le terme : '" . $idTerme . "' au questionnaire id_questionnaire : '" . $quest->get("id_questionnaire") . "'");
				$quest->ajouterTerme($idTerme);
			}
			
			$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_011 . " <a href=\"" . URL_QUESTIONNAIRE_MODIFIER . $quest->get("id_questionnaire") . "\">" . $quest->get("titre") . " (" . TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire") . ")</a>" . HTML_LISTE_ERREUR_FIN;
			
		}
		
		$this->log->debug("Importation::importerQuestionnaires() Fin");
		
	}

	
	/**
	 * 
	 * Importer les items
	 * 
	 */ 
	public function importerItems() {

		$this->log->debug("Importation::importerItems() Début");

		$messages = "";
		$listeCategories = array();
		
		// Traiter chacun des items
		foreach ($this->xml->item as $itemXML) {
			
			$this->log->debug("Importation::importerItems() idItem : '" . $itemXML->id_item . "'");
			
			// Déterminer l'id et le type d'item
			$typeItem = Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_item);
	
			// Préparer objet item
			$itemFactory = new Item($this->log, $this->dbh); 
			$item = $itemFactory->instancierItemParType($typeItem, $this->projet->get("id_projet"), '');
			
			$item->set("id_projet", $this->projet->get("id_projet"));
			$item->set("titre", Web::nettoyerChaineProvenantDuXML((string)$itemXML->titre));
			$item->set("enonce", Web::nettoyerChaineProvenantDuXML((string)$itemXML->enonce));
			$item->set("solution", Web::nettoyerChaineProvenantDuXML((string)$itemXML->solution));
			$item->set("retroaction_positive", Web::nettoyerChaineProvenantDuXML((string)$itemXML->retroaction_positive));
			$item->set("retroaction_negative", Web::nettoyerChaineProvenantDuXML((string)$itemXML->retroaction_negative));
			$item->set("info_comp1_titre", Web::nettoyerChaineProvenantDuXML((string)$itemXML->info_comp1_titre));			
			$item->set("info_comp1_texte", Web::nettoyerChaineProvenantDuXML((string)$itemXML->info_comp1_texte));
			$item->set("info_comp2_titre", Web::nettoyerChaineProvenantDuXML((string)$itemXML->info_comp2_titre));
			$item->set("info_comp2_texte", Web::nettoyerChaineProvenantDuXML((string)$itemXML->info_comp2_texte));
			$item->set("media_titre", Web::nettoyerChaineProvenantDuXML((string)$itemXML->media_titre));
			$item->set("media_texte", Web::nettoyerChaineProvenantDuXML((string)$itemXML->media_texte));
			$item->set("type_item", Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_item));
			
			$item->set("suivi", Web::nettoyerChaineProvenantDuXML((string)$itemXML->suivi));
			$item->set("retroaction_reponse_imprevue", Web::nettoyerChaineProvenantDuXML((string)$itemXML->retroaction_reponse_imprevue));
			$item->set("ponderation", Web::nettoyerChaineProvenantDuXML((string)$itemXML->ponderation));
			$item->set("ordre_presentation", Web::nettoyerChaineProvenantDuXML((string)$itemXML->ordre_presentation));
			$item->set("type_etiquettes", Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_etiquettes));
			$item->set("type_bonnesreponses", Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_bonnesreponses));
			$item->set("type_champs", Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_champs));
			$item->set("afficher_solution", Web::nettoyerChaineProvenantDuXML((string)$itemXML->afficher_solution));
			$item->set("points_retranches", Web::nettoyerChaineProvenantDuXML((string)$itemXML->points_retranches));
			$item->set("majmin", Web::nettoyerChaineProvenantDuXML((string)$itemXML->majmin));
			$item->set("ponctuation", Web::nettoyerChaineProvenantDuXML((string)$itemXML->ponctuation));
			$item->set("orientation_elements", Web::nettoyerChaineProvenantDuXML((string)$itemXML->orientation_elements));
			$item->set("couleur_element", Web::nettoyerChaineProvenantDuXML((string)$itemXML->couleur_element));
			$item->set("couleur_element_associe", Web::nettoyerChaineProvenantDuXML((string)$itemXML->couleur_element_associe));
			$item->set("afficher_masque", Web::nettoyerChaineProvenantDuXML((string)$itemXML->afficher_masque));
			$item->set("type_elements1", Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_elements1));
			$item->set("type_elements2", Web::nettoyerChaineProvenantDuXML((string)$itemXML->type_elements2));
			$item->set("demarrer_media", Web::nettoyerChaineProvenantDuXML((string)$itemXML->demarrer_media));
			$item->set("reponse_bonne_message", Web::nettoyerChaineProvenantDuXML((string)$itemXML->reponse_bonne_message));
			$item->set("reponse_mauvaise_message", Web::nettoyerChaineProvenantDuXML((string)$itemXML->reponse_mauvaise_message));
			$item->set("reponse_incomplete_message", Web::nettoyerChaineProvenantDuXML((string)$itemXML->reponse_incomplete_message));
			$item->set("remarque", (string)$itemXML->remarque);
			$item->set("statut", "1");
			$item->set("apercu_langue", $this->itemLangueApercuDefaut);
			$item->set("apercu_theme", $this->itemThemeApercuDefaut);
			$imgIdConvert = (int)$itemXML->image;
			if ($imgIdConvert > 0) {
				$item->set("image", $this->conversionMedias[$imgIdConvert]);
			}
			$item->set("couleur_zones", Web::nettoyerChaineProvenantDuXML((string)$itemXML->couleur_zones));
			
			if ($itemXML->media_image != "" && (int)$itemXML->media_image > 0) {
				$item->set("media_image", $this->conversionMedias[(int)$itemXML->media_image]);
			}
			if ($itemXML->media_son != "" && (int)$itemXML->media_son > 0) {
				$item->set("media_son", $this->conversionMedias[(int)$itemXML->media_son]);
			}
			if ($itemXML->media_video != "" && (int)$itemXML->media_video > 0) {
				$item->set("media_video", $this->conversionMedias[(int)$itemXML->media_video]);	
			}
			if ($itemXML->reponse_bonne_media != "" && (int)$itemXML->reponse_bonne_media > 0) {
				$item->set("reponse_bonne_media", $this->conversionMedias[(int)$itemXML->reponse_bonne_media]);
			}
			if ($itemXML->reponse_mauvaise_media != "" && (int)$itemXML->reponse_mauvaise_media > 0) {
				$item->set("reponse_mauvaise_media", $this->conversionMedias[(int)$itemXML->reponse_mauvaise_media]);
			}
			if ($itemXML->reponse_incomplete_media != "" && (int)$itemXML->reponse_incomplete_media > 0) {
				$item->set("reponse_incomplete_media", $this->conversionMedias[(int)$itemXML->reponse_incomplete_media]);
			}

			// Importer une catégorie seulement si l'id projet et domaine sont les mêmes
			$idProjetImport = Web::nettoyerChaineProvenantDuXML((string)$itemXML->id_projet);
			$domaineImport = Web::nettoyerChaineProvenantDuXML((string)$itemXML->url_domaine);
			if (URL_DOMAINE == $domaineImport && $this->projet->get("id_projet") == $idProjetImport) {
					$item->set("id_categorie", Web::nettoyerChaineProvenantDuXML((string)$itemXML->id_categorie));
			}
			
			// Ajouter une catégorie provenant de Netquiz pro 4 au besoin
			$categorieNQPRO = Web::nettoyerChaineProvenantDuXML((string)$itemXML->categorie_nqpro);
			if ($categorieNQPRO != "") {
				
				// Vérifier si la catégorie existe
				if (array_key_exists($categorieNQPRO, $listeCategories)) {
					$item->set("id_categorie", $listeCategories[$categorieNQPRO]);
				} else {
					// Créer la categorie
					$categorie = new Categorie($this->log, $this->dbh);
					$categorie->set("id_projet", $this->projet->get("id_projet"));
					$categorie->set("titre", $categorieNQPRO);
						
					// Ajouter la categorie
					$categorie->ajouter();
					
					// Régler l'id de la catégorie au niveau du questionnaire
					$item->set("id_categorie", $categorie->get("id_categorie"));
					
					// Ajouter à la liste
					$listeCategories[$categorieNQPRO] = $categorie->get("id_categorie");
				}
			}
						
			// Sauvegarder dans la base de données
			$item->ajouter();
			
			// Préparer les choix de réponses
			if ($item->get("type_item") == "1" || $item->get("type_item") == "2" || $item->get("type_item") == "4" || $item->get("type_item") == "8" || $item->get("type_item") == "9" ||  
				$item->get("type_item") == "10" || $item->get("type_item") == "12" || $item->get("type_item") == "13") {
				$this->log->debug("Importation::importerItems() Préparer choix de réponses");
				
				$idxReponse = 1;
				foreach ($itemXML->reponse as $reponseXML) {
					
					if ($item->get("type_elements1") == "texte") {
						$item->set("reponse_" . $idxReponse ."_element", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->element));
					} elseif ($item->get("type_elements1") == "image") {
						$imgAvant = (int)$reponseXML->element;
						$imgApres = $this->conversionMedias[(int)$reponseXML->element];
						$item->set("reponse_" . $idxReponse ."_element", $this->conversionMedias[(int)$reponseXML->element]);
					}
					if ($item->get("type_elements2") == "texte") {
						$item->set("reponse_" . $idxReponse ."_element_associe", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->element_associe));
					} elseif ($item->get("type_elements2") == "image") {
						$imgAvant = (int)$reponseXML->element_associe;
						$imgApres = $this->conversionMedias[(int)$reponseXML->element_associe];
						$item->set("reponse_" . $idxReponse ."_element_associe", $this->conversionMedias[(int)$reponseXML->element_associe]);
					}
					
					$item->set("reponse_" . $idxReponse ."_retroaction", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->retroaction));
					$item->set("reponse_" . $idxReponse ."_retroaction_negative", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->retroaction_negative));
					$item->set("reponse_" . $idxReponse ."_retroaction_incomplete", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->retroaction_incomplete));
					if ($reponseXML->masque != "") {
						$item->set("reponse_" . $idxReponse ."_masque", $this->conversionMedias[(int)$reponseXML->masque]);
					}
					$item->set("reponse_" . $idxReponse ."_reponse", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->bonne_reponse));
					$item->set("reponse_" . $idxReponse ."_coordonnee_x", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->coordonnee_x));
					$item->set("reponse_" . $idxReponse ."_coordonnee_y", Web::nettoyerChaineProvenantDuXML((string)$reponseXML->coordonnee_y));

					// Noter la réponse
					if ($item->get("reponse_" . $idxReponse ."_reponse") == "1") {
						$item->set("reponse_choix", $idxReponse);
					}
					
					$idxReponse++;
				}
			}
			
			
			// Traitement spécifique pour un item de type Classement
			if ($item->get("type_item") == "3") {
			
				$i = 1;
				// Importer les classeurs
				foreach ($itemXML->classeur as $classeurXML) {

					// Selon le type d'éléments, utiliser le texte ou la valeur convertie pour le média
					if ($item->get("type_elements1") == "texte") {
						$item->set("classeur_" . $i . "_titre", (string)$classeurXML->titre);
					} elseif ($item->get("type_elements1") == "image") {
						$imgApres = $this->conversionMedias[(int)$classeurXML->titre];
						$item->set("classeur_" . $i . "_titre", $imgApres);
					}
					
					$item->set("classeur_" . $i . "_retroaction", (string)$classeurXML->retroaction);
					$item->set("classeur_" . $i . "_retroaction_positive", (string)$classeurXML->retroaction_positive);
					$item->set("classeur_" . $i . "_retroaction_negative", (string)$classeurXML->retroaction_negative);
					$item->set("classeur_" . $i . "_retroaction_incomplete", (string)$classeurXML->retroaction_incomplete);
						
					$j = 1;
					foreach ($classeurXML->element as $elementXML) {
						
						// Selon le type d'éléments, utiliser le texte ou la valeur convertie pour le média
						if ($item->get("type_elements2") == "texte") {
							$item->set("classeur_" . $i . "_element_" . $j . "_texte", (string)$elementXML->texte);
						} elseif ($item->get("type_elements2") == "image") {
							$imgAvant = (string)$elementXML->texte;
							$imgApres = $this->conversionMedias[(int)$elementXML->texte];
							$item->set("classeur_" . $i . "_element_" . $j . "_texte", $imgApres);
						}
			
						$k = 1;
						foreach ($elementXML->retroaction as $retroXML) {
							$item->set("classeur_" . $i . "_element_" . $j . "_retro_" . $k, (string)$retroXML->texte);
							$k++;
						}
						$j++;
					}
					$i++;
				}
			}			
			
			// Traitement spécifique pour un item de type Dictée
			if ($item->get("type_item") == "6") {
				
				// Remplacer les <br> par des fins de ligne
				$item->set("solution", Web::convertirBRVersFinDeLigne($item->get("solution")) );
			}		
			
			// Traitement spécifique pour un item de type Marquage
			if ($item->get("type_item") == "7") {

				// Obtenir la liste des couleurs
				$idx = 1;
				foreach ($itemXML->couleur_marquage as $couleurMarquage) {
					
					$item->set("couleur_" . $idx . "_couleur", (string)$couleurMarquage->couleur);
					$item->set("couleur_" . $idx . "_titre", (string)$couleurMarquage->titre);
					$item->set("couleur_" . $idx . "_retroaction", (string)$couleurMarquage->retroaction);
					$item->set("couleur_" . $idx . "_retroaction_negative", (string)$couleurMarquage->retroaction_negative);
					$item->set("couleur_" . $idx . "_retroaction_incomplete", (string)$couleurMarquage->retroaction_incomplete);
					$item->set("couleur_" . $idx . "_ordre", (string)$idx);
					
					$idx++;
				}
				
				// Importer le texte avec marques
				$item->set("solution", html_entity_decode((string)$itemXML->solution, ENT_QUOTES, "UTF-8"));
				
				// Importer les marques
				$idx = 1;
				foreach ($itemXML->marque as $marque) {
					
					$item->set("marque_" . $idx . "_couleur", (string)$marque->couleur);
					$item->set("marque_" . $idx . "_texte", (string)$marque->texte);
					$item->set("marque_" . $idx . "_position_debut", (int) $marque->position_debut);
					$item->set("marque_" . $idx . "_position_fin", (int) $marque->position_fin);
					
					// Importer les rétros
					foreach ($marque->retroaction as $retro) {
						
						$txt = html_entity_decode((string)$retro->texte, ENT_QUOTES, "UTF-8");
						$this->log->debug("Importation::importerItems() ************** Texte : '" . (string)$retro->texte . "'  Txt : '$txt'");
						$item->set("marque_" . $idx . "_retro_" . (string)$retro->couleur, $txt );
					}
					$idx++;
				}
				
				// Reconstruire le texte avec marques
				$item->preparerTexteAvecMarques();
			}
		
			// Traitement spécifique pour un item de type Texte lacunaire
			if ($item->get("type_item") == "11") {
					
				// Localiser les lacunes
				preg_match_all('/<lacune_(.*?)>(.+?)<\/lacune_.*?>/', $item->get("solution"), $matches, PREG_SET_ORDER);
				
				$idx = 0;
				$htmlOut = $item->get("solution");
				foreach ($matches as $lacuneInfos) {
					$idx++;
					$match = $lacuneInfos[0];
					$idLacuneSrc = $lacuneInfos[2];
					$lacuneHTML = "<span id=\"lacune_" . $idx . "\" class=\"lacune lacune-" . $item->get("type_elements1") . " mceNonEditable\">" . TXT_LACUNE . " " . $idx . "</span>";
					
					// Remplacer dans le html
					$htmlOut = str_replace($match, $lacuneHTML, $htmlOut);
				}
				$item->set("solution", $htmlOut);
				
				$i = 1;
				// Importer les lacunes
				foreach ($itemXML->lacune as $lacuneXML) {
					$item->set("lacune_" . $i . "_retro", (string)$lacuneXML->retroaction);
			
					$j = 1;
					foreach ($lacuneXML->reponse as $reponseXML) {
						$item->set("lacune_" . $i . "_reponse_" . $j . "_element", html_entity_decode((string)$reponseXML->element, ENT_NOQUOTES, "UTF-8"));
						$item->set("lacune_" . $i . "_reponse_" . $j . "_retroaction", html_entity_decode((string)$reponseXML->retroaction, ENT_NOQUOTES, "UTF-8"));
						$item->set("lacune_" . $i . "_reponse_" . $j . "_reponse", html_entity_decode((string)$reponseXML->bonne_reponse, ENT_NOQUOTES, "UTF-8"));
						$j++;
					}
					$i++;
				}
			}			

			// Remplacer les médias dans les rich textes
			$this->remplacerIDMediaTextes($item);
			
			// Enregistrer les informations supplémentaires
			$item->enregistrer();
			$this->log->debug("Importation::importerItems() Nouvel item : id_item : '" . $item->get("id_item") . "'");
			
			// Message de confirmation
			$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_010 . " <a href=\"" . URL_ITEM_MODIFIER . $item->get("id_item") . "\">" . $item->get("titre") . " (" . TXT_PREFIX_ITEM . $item->get("id_item") . ")</a>" . HTML_LISTE_ERREUR_FIN;
			
			// Ajouter à la matrice de conversion
			$this->conversionItems[(int)$itemXML->id_item] = $item->get("id_item");
		}
		
		$this->log->debug("Importation::importerItems() Fin");
			
		return $messages;
	}	
	

	/**
	 *
	 * Importer les collections
	 *
	 */
	public function importerCollections() {
	
		$this->log->debug("Importation::importerCollections() Début");
	
		$messages = "";
	
		// Traiter chacune des collections
		foreach ($this->xml->collection as $collectionXML) {
			$this->log->debug("Importation::importerCollections() Importer la collection idCollection : '" . $collectionXML->id_collection . "'");
	
			// Préparer objet collection
			$collection = new Collection($this->log, $this->dbh);
			$collection->set("id_projet", $this->projet->get("id_projet"));
			$collection->set("titre", Web::nettoyerChaineProvenantDuXML((string)$collectionXML->titre));
			$collection->set("remarque", Web::nettoyerChaineProvenantDuXML((string)$collectionXML->remarque));
				
			// Sauvegarder dans la base de données
			$collection->ajouter();
			$this->log->debug("Importation::importerCollections() Nouvelle collection - id_collection : '" . $collection->get("id_collection") . "'");
			
			// Message de confirmation
			$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_037 . " <a href=\"" . URL_COLLECTION_MODIFIER . $collection->get("id_collection") . "\">" . $collection->get("titre") . " (" . TXT_PREFIX_COLLECTION . $collection->get("id_collection") . ")</a>" . HTML_LISTE_ERREUR_FIN;
				
		}
	
		$this->log->debug("Importation::importerCollections() Fin");
	
		return $messages;
	}
	

	/**
	 *
	 * Importer les catégories
	 *
	 */
	public function importerCategories() {
	
		$this->log->debug("Importation::importerCategories() Début");
	
		$messages = "";
	
		// Traiter chacune des collections
		foreach ($this->xml->categorie as $categorieXML) {
			$this->log->debug("Importation::importerCategories() Importer la categorie idCategorie : '" . $categorieXML->id_categorie . "'");
	
			// Préparer objet catégorie
			$categorie = new Categorie($this->log, $this->dbh);
			$categorie->set("id_projet", $this->projet->get("id_projet"));
			$categorie->set("titre", Web::nettoyerChaineProvenantDuXML((string)$categorieXML->titre));
			$categorie->set("remarque", Web::nettoyerChaineProvenantDuXML((string)$categorieXML->remarque));
	
			// Sauvegarder dans la base de données
			$categorie->ajouter();
			$this->log->debug("Importation::importerCategories() Nouvelle catégorie - id_categorie : '" . $categorie->get("id_categorie") . "'");
				
			// Message de confirmation
			$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_038 . " <a href=\"" . URL_CATEGORIE_MODIFIER . $categorie->get("id_categorie") . "\">" . $categorie->get("titre") . " (" . TXT_PREFIX_CATEGORIE . $categorie->get("id_categorie") . ")</a>" . HTML_LISTE_ERREUR_FIN;
		}
	
		$this->log->debug("Importation::importerCategories() Fin");
	
		return $messages;
	}	
	
	
	/**
	 * 
	 * Importer les médias
	 * 
	 */ 
	public function importerMedias() {

		$this->log->debug("Importation::importerMedias() Début");
		
		$messages = "";

		// Traiter chacun des médias
		foreach ($this->xml->media as $mediaXML) {
			$this->log->debug("Importation::importerMedias() Importer le média idMedia : '" . $mediaXML->id_media . "'");
	
			// Préparer objet média
			$media = new Media($this->log, $this->dbh);
			$media->set("id_projet", $this->projet->get("id_projet"));
			$media->set("titre", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->titre));
			$media->set("type", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->type ));
			$media->set("description", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->description));
			$media->set("source", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->source));
			$media->set("fichier", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->fichier) );
			$media->set("url", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->url));
			$media->set("suivi", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->suivi));
			$media->set("liens", 0);
			$media->set("remarque", Web::nettoyerChaineProvenantDuXML((string)$mediaXML->remarque));
			
			// Sauvegarder dans la base de données
			$media->ajouter();
			$this->log->debug("Importation::importerMedias() Nouveau media : id_media : '" . $media->get("id_media") . "'");
			
			// Ajouter à la matrice de conversion
			$this->conversionMedias[(int)$mediaXML->id_media] = $media->get("id_media");

			// Copier le fichier média au besoin
			if ($media->get("source") == "fichier") {
				
				// Fichier original
				$fnOrig = $this->repertoireXML . REPERTOIRE_PREFIX_MEDIAS . $mediaXML->fichier;
				
				// Préparer le nouveau fichier - enlever tout préfix
				$fichier = preg_replace('/^\d+_/', '', $mediaXML->fichier);

				// Obtenir le nouveau nom de fichier + extension
	   			$info = pathinfo($fichier);
	   			if (isset($info['extension'])) {
			    	$fichierExtension = strtolower($info['extension']);
			    	$fichierNom =  basename($fichier,'.'.$info['extension']);
		    		$fichierNom = Web::nettoyerChaineNomFichier($fichierNom);
					$fichierDest = $media->get("id_media") . "_" . $fichierNom . "." . $fichierExtension;
					$repDest = REPERTOIRE_MEDIA . $media->getPrefixRepertoireMedia($this->projet->get("id_projet"));
					$fnDest =  $repDest . $fichierDest;
					
					// Vérifier si le répertoire existe, sinon le créé
					if( !file_exists($repDest) ) {
						mkdir($repDest);
						$this->log->debug("Importation::importerMedias() Le répertoire '$repDest' a été créé");
					} else {
						$this->log->debug("Importation::importerMedias() Le répertoire '$repDest' existe déjà");
					}
				
					// Copier le fichier
					copy($fnOrig, $fnDest);
					$this->log->debug("Importation::importerMedias() Copie du fichier '$fnOrig' vers '$fnDest");
				
					// Ajouter le nom du fichier à l'information dans la bd
					$media->set("fichier", $fichierDest);
					$media->enregistrer();

					// Message de confirmation
					$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_009 . " <a href=\"" . URL_MEDIA_MODIFIER . $media->get("id_media") . "\">" . $media->get("titre") . " (" . TXT_PREFIX_MEDIA . $media->get("id_media") . ")</a>" . HTML_LISTE_ERREUR_FIN;
					
	   			} else {
	   				$messages = HTML_LISTE_ERREUR_DEBUT . ERR_035 . HTML_LISTE_ERREUR_FIN;
	   			}
				
			} 
		}
		
		$this->log->debug("Importation::importerMedias() Fin");
		
		return $messages;
	}	

	
	/**
	 *
	 * Importer les termes
	 *
	 */
	public function importerTermes() {
	
		$this->log->debug("Importation::importerTermes() Début");
	
		$messages = "";
	
		// Traiter chacun des termes
		foreach ($this->xml->terme as $termeXML) {
			$this->log->debug("Importation::importerTermes() Importer le terme idTerme : '" . $termeXML->id_terme . "'");
	
			// Préparer objet terme
			$terme = new Terme($this->log, $this->dbh);
			$terme->set("id_projet", $this->projet->get("id_projet"));
			$terme->set("terme", Web::nettoyerChaineProvenantDuXML((string)$termeXML->terme));
			
			$variantesXML = Web::nettoyerChaineProvenantDuXML((string)$termeXML->variantes);
			
			// Traitement spécial pour les variantes... une chaîne par ligne, pas d'espace avant ou après
			$listeVariantes = explode(",", $variantesXML);
			
			// Pour chaque variante, enlever les espaces avant/après la chaîne et ajouter à une nouvelle chaîne
			$variantes = "";
			foreach ($listeVariantes as $var) {
				if ($variantes != "") {
					$variantes .= "\n";
				}
				$variantes .= trim($var);
			}
			
			$terme->set("variantes", $variantes);
			
			$terme->set("type_definition", Web::nettoyerChaineProvenantDuXML((string)$termeXML->type_definition));
			$terme->set("texte", Web::nettoyerChaineProvenantDuXML((string)$termeXML->texte));
			$terme->set("url", Web::nettoyerChaineProvenantDuXML((string)$termeXML->url));
			$terme->set("remarque", Web::nettoyerChaineProvenantDuXML((string)$termeXML->remarque));

			if ($termeXML->media_image != "" && (int)$termeXML->media_image > 0) {
				$terme->set("media_image", $this->conversionMedias[(int)$termeXML->media_image]);
			}
			if ($termeXML->media_son != "" && (int)$termeXML->media_son > 0) {
				$terme->set("media_son", $this->conversionMedias[(int)$termeXML->media_son]);
			}
			if ($termeXML->media_video != "" && (int)$termeXML->media_video > 0) {
				$terme->set("media_video", $this->conversionMedias[(int)$termeXML->media_video]);
			}			
			
			// Sauvegarder dans la base de données
			$terme->ajouter();
			$this->log->debug("Importation::importerTermes() Nouveau terme : id_terme : '" . $terme->get("id_terme") . "'");
	
			// Ajouter à la matrice de conversion
			$this->conversionTermes[(int)$termeXML->id_terme] = $terme->get("id_terme");
	
			// Message de confirmation
			$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_040 . " <a href=\"" . URL_TERME_MODIFIER . $terme->get("id_terme") . "\">" . $terme->get("terme") . " (" . TXT_PREFIX_TERME . $terme->get("id_terme") . ")</a>" . HTML_LISTE_ERREUR_FIN;
			
		}
	
		$this->log->debug("Importation::importerTermes() Fin");
	
		return $messages;
	}
		


	/**
	 *
	 * Importer les langues
	 *
	 */
	public function importerLangues() {
	
		$this->log->debug("Importation::importerLangues() Début");
	
		$messages = "";
	
		// Traiter chacune des langues
		foreach ($this->xml->langue as $langueXML) {
			$this->log->debug("Importation::importerLangues() Importer la langue idlangue : '" . $langueXML->id_langue . "'");
	
			// Préparer objet langue
			$langue = new Langue($this->log, $this->dbh);
			$langue->set("id_projet", $this->projet->get("id_projet"));
			$langue->set("titre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->titre));
			$langue->set("delimiteur", Web::nettoyerChaineProvenantDuXML((string)$langueXML->delimiteur));
			$langue->set("boutons_annuler", Web::nettoyerChaineProvenantDuXML((string)$langueXML->boutons_annuler));
			$langue->set("boutons_ok", Web::nettoyerChaineProvenantDuXML((string)$langueXML->boutons_ok));
			$langue->set("consignes_association", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_association));
			$langue->set("consignes_choixmultiples", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_choixmultiples));
			$langue->set("consignes_classement", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_classement));
			$langue->set("consignes_damier_masquees", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_damier_masquees));
			$langue->set("consignes_damier_nonmasquees", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_damier_nonmasquees));
			$langue->set("consignes_developpement", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_developpement));
			$langue->set("consignes_dictee_debut", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_dictee_debut));
			$langue->set("consignes_dictee_majuscules", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_dictee_majuscules));
			$langue->set("consignes_dictee_ponctuation", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_dictee_ponctuation));
			$langue->set("consignes_marquage", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_marquage));
			$langue->set("consignes_ordre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_ordre));
			$langue->set("consignes_reponsebreve_debut", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_reponsebreve_debut));
			$langue->set("consignes_reponsebreve_majuscules", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_reponsebreve_majuscules));
			$langue->set("consignes_reponsebreve_ponctuation", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_reponsebreve_ponctuation));
			$langue->set("consignes_reponsesmultiples_unereponse", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_reponsesmultiples_unereponse));
			$langue->set("consignes_reponsesmultiples_toutes", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_reponsesmultiples_toutes));
			$langue->set("consignes_lacunaire_menu", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_lacunaire_menu));
			$langue->set("consignes_lacunaire_glisser", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_lacunaire_glisser));
			$langue->set("consignes_lacunaire_reponsebreve_debut", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_lacunaire_reponsebreve_debut));
			$langue->set("consignes_lacunaire_reponsebreve_majuscules", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_lacunaire_reponsebreve_majuscules));
			$langue->set("consignes_lacunaire_reponsebreve_ponctuation", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_lacunaire_reponsebreve_ponctuation));
			$langue->set("consignes_vraifaux", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_vraifaux));
			$langue->set("consignes_zones", Web::nettoyerChaineProvenantDuXML((string)$langueXML->consignes_zones));
			$langue->set("fenetre_renseignements", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_renseignements));
			$langue->set("fenetre_nom", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_nom));
			$langue->set("fenetre_prenom", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_prenom));
			$langue->set("fenetre_matricule", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_matricule));
			$langue->set("fenetre_groupe", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_groupe));
			$langue->set("fenetre_courriel", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_courriel));
			$langue->set("fenetre_autre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_autre));
			$langue->set("fenetre_envoi", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_envoi));
			$langue->set("fenetre_courriel_destinataire", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fenetre_courriel_destinataire));
			$langue->set("fonctionnalites_commencer", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_commencer));
			$langue->set("fonctionnalites_effacer", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_effacer));
			$langue->set("fonctionnalites_courriel", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_courriel));
			$langue->set("fonctionnalites_imprimer", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_imprimer));
			$langue->set("fonctionnalites_recommencer", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_recommencer));
			$langue->set("fonctionnalites_reprendre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_reprendre));
			$langue->set("fonctionnalites_resultats", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_resultats));
			$langue->set("fonctionnalites_lexique", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_lexique));
			$langue->set("fonctionnalites_questionnaire", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_questionnaire));
			$langue->set("fonctionnalites_solution", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_solution));
			$langue->set("fonctionnalites_valider", Web::nettoyerChaineProvenantDuXML((string)$langueXML->fonctionnalites_valider));
			$langue->set("navigation_page", Web::nettoyerChaineProvenantDuXML((string)$langueXML->navigation_page));
			$langue->set("navigation_de", Web::nettoyerChaineProvenantDuXML((string)$langueXML->navigation_de));
			$langue->set("message_bonnereponse", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_bonnereponse));
			$langue->set("message_mauvaisereponse", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_mauvaisereponse));
			$langue->set("message_reponseincomplete", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_reponseincomplete));
			$langue->set("message_libelle_solution", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_libelle_solution));
			$langue->set("message_point", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_point));
			$langue->set("message_points", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_points));
			$langue->set("message_sanstitre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_sanstitre));
			$langue->set("conjonction_et", Web::nettoyerChaineProvenantDuXML((string)$langueXML->conjonction_et));
			$langue->set("message_dictee_motsentrop", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_dictee_motsentrop));
			$langue->set("message_dictee_orthographe", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_dictee_orthographe));
			$langue->set("message_dictee_motsmanquants", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_dictee_motsmanquants));
			$langue->set("message_reponsesuggeree", Web::nettoyerChaineProvenantDuXML((string)$langueXML->message_reponsesuggeree));
			$langue->set("resultats_afaire", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_afaire));
			$langue->set("resultats_areprendre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_areprendre));
			$langue->set("resultats_message_courriel_succes", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_message_courriel_succes));
			$langue->set("resultats_message_courriel_erreur", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_message_courriel_erreur));
			$langue->set("resultats_objet_courriel", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_objet_courriel));
			$langue->set("resultats_confirmation", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_confirmation));
			$langue->set("resultats_accueil", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_accueil));
			$langue->set("resultats_nbessais", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_nbessais));
			$langue->set("resultats_points", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_points));
			$langue->set("resultats_reussi", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_reussi));
			$langue->set("resultats_sansobjet", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_sansobjet));
			$langue->set("resultats_statut", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_statut));
			$langue->set("resultats_tempsdereponse", Web::nettoyerChaineProvenantDuXML((string)$langueXML->resultats_tempsdereponse));
			$langue->set("item_association", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_association));
			$langue->set("item_choixmultiples", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_choixmultiples));
			$langue->set("item_classement", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_classement));
			$langue->set("item_damier", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_damier));
			$langue->set("item_developpement", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_developpement));
			$langue->set("item_dictee", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_dictee));
			$langue->set("item_marquage", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_marquage));
			$langue->set("item_miseenordre", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_miseenordre));
			$langue->set("item_reponsebreve", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_reponsebreve));
			$langue->set("item_reponsesmultiples", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_reponsesmultiples));
			$langue->set("item_textelacunaire", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_textelacunaire));
			$langue->set("item_vraioufaux", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_vraioufaux));
			$langue->set("item_zonesaidentifier", Web::nettoyerChaineProvenantDuXML((string)$langueXML->item_zonesaidentifier));
			$langue->set("remarque", Web::nettoyerChaineProvenantDuXML((string)$langueXML->remarque));
			$langue->set("statut", Web::nettoyerChaineProvenantDuXML((string)$langueXML->statut));
			
			if ($langueXML->media_mauvaisereponse != "" && (int)$langueXML->media_mauvaisereponse > 0) {
				$langue->set("media_mauvaisereponse", $this->conversionMedias[(int)$langueXML->media_mauvaisereponse]);
			}
			if ($langueXML->media_mauvaisereponse != "" && (int)$langueXML->media_mauvaisereponse > 0) {
				$langue->set("media_son", $this->conversionMedias[(int)$langueXML->media_mauvaisereponse]);
			}
			if ($langueXML->media_reponseincomplete != "" && (int)$langueXML->media_reponseincomplete > 0) {
				$langue->set("media_video", $this->conversionMedias[(int)$langueXML->media_reponseincomplete]);
			}
				
			// Sauvegarder dans la base de données
			$langue->ajouter();
			$this->log->debug("Importation::importerLangues() Nouveau langue : id_langue : '" . $langue->get("id_langue") . "'");
	
			// Message de confirmation
			$this->msgConf .= HTML_LISTE_ERREUR_DEBUT . MSG_039 . " <a href=\"" . URL_LANGUE_MODIFIER . $langue->get("id_langue") . "\">" . $langue->get("titre") . " (" . TXT_PREFIX_LANGUE . $langue->get("id_langue") . ")</a>" . HTML_LISTE_ERREUR_FIN;
				
		}
	
		$this->log->debug("Importation::importerLangues() Fin");
	
		return $messages;
	}
	
	
	/**
	 * 
	 * Transfert du fichier d'import
	 * 
	 */ 
	public function transfertFichier() {

		$this->log->debug("Importation::transfertFichier() Début");
		
		$messages = "";
		$erreur = false;		
		
		// Obtenir le répertoire temporaire
		$this->repertoireImport = $this->getTempDir();
		
		// Supprimer le répertoire s'il existe
		if (is_dir($this->repertoireImport)) {
			$this->log->debug("Importation::transfertFichier() Suppression du répertoire '" . $this->repertoireImport . "'");
			Fichiers::rmdirr($this->repertoireImport);
		}
			
		// Créer le répertoire
		mkdir($this->repertoireImport);
		
		// Vérifier que le répertoire existe
		if (! is_dir($this->repertoireImport)) {
			$this->log->debug("Importation::transfertFichier() Impossible de créer le répertoire '$this->repertoireImport'");
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_035 . HTML_LISTE_ERREUR_FIN;
			$erreur = true;
		}
		
		// Obtenir le nom du fichier
		if (! $erreur && isset($_FILES['fichier_import'])) {
			$fichier = basename($_FILES['fichier_import']['name']);
			$this->log->debug("Importation::transfertFichier()  Fichier détecté : '$fichier'");				
		} else {
			$this->log->debug("Importation::transfertFichier() Impossible de créer le répertoire '$this->repertoireImport'");
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_045 . HTML_LISTE_ERREUR_FIN;
			$erreur = true;
		}
			 
		// Obtenir et vérifier l'extension et rendre sécuritaire le nom de fichier
		if (! $erreur) {
   			$info = pathinfo($fichier);
	    	$fichierExtension = strtolower($info['extension']);
	    	$fichierNom =  basename($fichier,'.'.$info['extension']);
	    	$fichierNom = Web::nettoyerChaineNomFichier($fichierNom);
	    	$this->log->debug("Importation::transfertFichier() Nom : '$fichierNom'  extension : '$fichierExtension'");
		    	if (strtolower($fichierExtension) != "zip") {
				$this->log->debug("Importation::transfertFichier() Le fichier transmit n'est pas un zip : '$fichierExtension'");
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_046 . HTML_LISTE_ERREUR_FIN;
				$erreur = true;
	    	}				
		}

		// Effectuer le transfer du fichier
		if (! $erreur) {
			$this->log->debug("Importation::transfertFichier() Transfert du fichier");
			$fichierComplet = $this->repertoireImport  . "/" . $fichier;
			$this->log->debug("Importation::transfertFichier() Transfert du fichier vers '$fichierComplet'");
		    if(move_uploaded_file($_FILES['fichier_import']['tmp_name'], $fichierComplet)) {
				$this->log->debug("Importation::transfertFichier() Fichier transféré avec succès vers '$fichierComplet'");
		    } else {
		    	$this->log->debug("Importation::transfertFichier() Erreur lors du transfert du fichier vers '$fichierComplet'");
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_047 . HTML_LISTE_ERREUR_FIN;
				$erreur = true;
		    }
		}
		
		// Extraire les fichiers
		if (! $erreur) {
		     $zip = new ZipArchive;
		     $res = $zip->open($fichierComplet);
		     if ($res === TRUE) {
		        $zip->extractTo($this->repertoireImport);
		        $zip->close();
		        $this->log->debug("Importation::transfertFichier() Fichiers extraits du zip");
		     } else {
		        $this->log->debug("Importation::transfertFichier() Erreur lors du transfert du fichier vers '$fichierComplet'");
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_048 . HTML_LISTE_ERREUR_FIN;
				$erreur = true;
		     }
		}
		
		// Vérifier que le fichier XML est disponible
		if (! $erreur) {
			//$this->repertoireXML  = $this->repertoireImport  . "/" . $fichierNom . "/";
			$this->repertoireXML  = $this->repertoireImport  . "/";  
			$this->fichierXML = $this->repertoireXML . FICHIER_EXPORTATION_XML;
			$this->log->debug("Importation::transfertFichier() Vérifier que le fichier '$this->fichierXML' existe");
			if (file_exists($this->fichierXML)) {
				$this->log->debug("Importation::transfertFichier() Le fichier XML a été localisé : '$this->fichierXML'");					
			} else {
				$this->log->debug("Importation::transfertFichier() Impossible de localiser le fichier XML '$this->fichierXML'");
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_049 . HTML_LISTE_ERREUR_FIN;
				$erreur = true;					
			}
		}
		
		$this->log->debug("Importation::transfertFichier() Fin");
		
		return $messages;
	}


	/**
	 * 
	 * Supprimer les fichiers et répertoires
	 * 
	 */ 
	public function supprimerFichiers() {

		$this->log->debug("Importation::supprimerFichiers() Début");	

		$this->log->debug("Importation::supprimerFichiers() Suppression du répertoire d'import : '" . $this->repertoireImport . "'");
		Fichiers::rmdirr($this->repertoireImport);
		
		$this->log->debug("Importation::supprimerFichiers() Fin");
		
	}
	
	
		
	/**
	 * 
	 * Vérifier le XML
	 * @param string xml
	 * @param string répertoire xml
	 *  
	 */ 
	protected function verifierXML() {
		
		$this->log->debug("Importation::verifierXML() Début");

		$contenu = "";
		
		// 1. Vérifier si une section QUESTIONNAIRE existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier si des questionnaires existent");
		if (isset($this->xml->questionnaire)) {

			$this->log->debug("Importation::verifierXML() Questionnaires localisés à vérifier");

			$messages = "";
			$idxQuestionnaire = 0;
			$questionnairesTraites = array();
			
			// Traiter chacun des questionnaires
			foreach ($this->xml->questionnaire as $quest) {
				
				$idQuestionnaire = preg_replace('[\D]', '', $quest->id_questionnaire);
				$this->log->debug("Importation::verifierXML() Traitement d'un questionnaire (idQuestionnaire = '$idQuestionnaire'");
				
				// Vérifier que l'id du questionnaire est unique
				if ($idQuestionnaire != "" && in_array( $idQuestionnaire, $questionnairesTraites) ) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_208 . $idQuestionnaire . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($questionnairesTraites, $idQuestionnaire);
				}
						
				$messages .= $this->verifierQuestionnaireXML($quest, $idxQuestionnaire);
				$idxQuestionnaire++;

				// Vérifier le média pour media_image
				if (isset($quest->media_image) && $quest->media_image != "" && $quest->media_image != "0") {
					$messages .= $this->verifierMediaDisponible($quest->media_image);
				}

				// Vérifier le média pour media_son
				if (isset($quest->media_son) && $quest->media_son != "" && $quest->media_son != "0") {
					$messages .= $this->verifierMediaDisponible($quest->media_son);
				}
				
				// Vérifier le média pour media_video
				if (isset($quest->media_video) && $quest->media_video != "" && $quest->media_video != "0") {
					$messages .= $this->verifierMediaDisponible($quest->media_video);
				}
				
				if ($messages != "") {
					
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour le questionnaire '$idQuestionnaire'");
					$quest = new Questionnaire($this->log, $this->dbh);
					
					if ($idQuestionnaire == "") { 
						$idQuestionnaire = IMPORTATION_VALEUR_INCONNUE;
					}
					
					$quest->set("id_questionnaire", $idQuestionnaire);
					$quest->set("idx_questionnaire", $idxQuestionnaire);
					
					// Ajouter les messages
					$quest->set("messages", $messages);
							
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "quest-details.php";
					
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
					
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuQuest($gabarit, $quest);
				}		
			}
		}		
		
		
		// 2. Vérifier si une section ITEMS existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier si des items existent");
		if (isset($this->xml->item)) {
			$this->log->debug("Importation::verifierXML() Items localisés à vérifier");
			
			$messages = "";
			$idxItem = 0;
			
			// Traiter chacun des items
			foreach ($this->xml->item as $itemXML) {
				
				$idItem = preg_replace('[\D]', '', $itemXML->id_item);
				$this->log->debug("Importation::verifierXML() Traitement d'un item (idItem = '$idItem')");
						
				$messages .= $this->verifierItemXML($itemXML, $idxItem);
				$idxItem++;

				// Vérifier le média pour media_image
				if (isset($itemXML->media_image) && $itemXML->media_image != "" && $itemXML->media_image != "0") {
					$messages .= $this->verifierMediaDisponible($itemXML->media_image);
				}

				// Vérifier le média pour media_son
				if (isset($itemXML->media_son) && $itemXML->media_son != "" && $itemXML->media_son != "0") {
					$messages .= $this->verifierMediaDisponible($itemXML->media_son);
				}
				
				// Vérifier le média pour media_video
				if (isset($itemXML->media_video) && $itemXML->media_video != "" && $itemXML->media_video != "0") {
					$messages .= $this->verifierMediaDisponible($itemXML->media_video);
				}
				
				if ($messages != "") {
					
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour l'item '$idItem'");
					$item = new Item($this->log, $this->dbh);
					
					if ($idItem == "") { 
						$idItem = IMPORTATION_VALEUR_INCONNUE;
					}
					
					$item->set("id_item", $idItem);
					$item->set("idx_item", $idxItem);
					
					// Ajouter les messages
					$item->set("messages", $messages);
							
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "item-details.php";
					
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
					
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuItem($gabarit, $item);
				}		
			}
		}
		
		// 3. Vérifier si une section MEDIA existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier les médias");
		
		if (isset($this->xml->media)) {
			
			$this->log->debug("Importation::verifierXML() Médias localisés à vérifier");
			
			$messages = "";
			$idxMedia = 0;
			$mediasTraites = array();
						
			$this->log->debug("Importation::verifierXML() Media : '" . $this->xml->media->asXML() . "'");
			
			// Traiter chacun des médias			
			foreach ($this->xml->media as $mediaXML) {
				
				$idxMedia++;
				
				$this->log->debug("Importation::verifierXML() MediaXML : '$mediaXML'");

				// Obtenir l'id media
				$idMedia = preg_replace('[\D]', '', $mediaXML->id_media);
				if ($idMedia == "") { 
					$idMedia = IMPORTATION_VALEUR_INCONNUE;
				}
				
				// Vérifier que l'id du média est unique
				if ($idMedia != "" && in_array( $idMedia, $mediasTraites) ) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_207 . $idMedia . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($mediasTraites, $idMedia);
				}
				
				$this->log->debug("Importation::verifierXML() Préparation de la validation d'un média (idMedia='" . $idMedia . "')");
				
				$messages .= $this->verifierMediaXML($mediaXML);
				
				if ($messages != "") {
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour le média '$idMedia' : '$messages'");
					
					$media = new Media($this->log, $this->dbh);
					
					$media->set("id_media", $idMedia);
					$media->set("idx_media", $idxMedia);
					
					// Ajouter les messages
					$media->set("messages", $messages);
							
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "media-details.php";
					
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
					
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuMedia($gabarit, $media);
				}
			}		
		}
		
		// 4. Vérifier si une section LANGUE existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier les langues");
		
		if (isset($this->xml->langue)) {
		
			$this->log->debug("Importation::verifierXML() Langues localisées à vérifier");
		
			$messages = "";
			$idxLangue = 0;
			$languesTraites = array();
		
			$this->log->debug("Importation::verifierXML() Langue : '" . $this->xml->langue->asXML() . "'");
		
			// Traiter chacune des langues
			foreach ($this->xml->langue as $langueXML) {
		
				$idxLangue++;
		
				$this->log->debug("Importation::verifierXML() LangueXML : '$langueXML'");
		
				// Obtenir l'id de la langue
				$idLangue = preg_replace('[\D]', '', $langueXML->id_langue);
				if ($idLangue == "") {
					$idLangue = IMPORTATION_VALEUR_INCONNUE;
				}
		
				// Vérifier que l'id de la langue est unique
				if ($idLangue != "" && in_array( $idLangue, $languesTraites) ) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_227 . $idLangue . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($languesTraites, $idLangue);
				}
		
				$this->log->debug("Importation::verifierXML() Préparation de la validation d'une langue (idLangue='" . $idLangue . "')");
		
				$messages .= $this->verifierLangueXML($langueXML);
		
				if ($messages != "") {
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour la langue '$idLangue' : '$messages'");
		
					$langue = new Langue($this->log, $this->dbh);
		
					$langue->set("id_langue", $idLangue);
					$langue->set("idx_langue", $idxLangue);
		
					// Ajouter les messages
					$langue->set("messages", $messages);
		
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "langue-details.php";
		
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
		
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuLangue($gabarit, $langue);
				}
			}
		}
		
		
		// 5. Vérifier si une section CATEGORIE existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier les categories");
		
		if (isset($this->xml->categorie)) {
		
			$this->log->debug("Importation::verifierXML() Catégories localisées à vérifier");
		
			$messages = "";
			$idxCategorie = 0;
			$categoriesTraites = array();
		
			$this->log->debug("Importation::verifierXML() Catégorie : '" . $this->xml->categorie->asXML() . "'");
		
			// Traiter chacune des categories
			foreach ($this->xml->categorie as $categorieXML) {
		
				$idxCategorie++;
		
				$this->log->debug("Importation::verifierXML() CategorieXML : '$categorieXML'");
		
				// Obtenir l'id de la catégorie
				$idCategorie = preg_replace('[\D]', '', $categorieXML->id_categorie);
				if ($idCategorie == "") {
					$idCategorie = IMPORTATION_VALEUR_INCONNUE;
				}
		
				// Vérifier que l'id de la catégorie est unique
				if ($idCategorie != "" && in_array( $idCategorie, $categoriesTraites) ) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_227 . $idCategorie . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($categoriesTraites, $idCategorie);
				}
		
				$this->log->debug("Importation::verifierXML() Préparation de la validation d'une categorie (idCategorie='" . $idCategorie . "')");
		
				$messages .= $this->verifierCategorieXML($categorieXML);
		
				if ($messages != "") {
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour la categorie '$idCategorie' : '$messages'");
		
					$categorie = new Categorie($this->log, $this->dbh);
		
					$categorie->set("id_categorie", $idCategorie);
					$categorie->set("idx_categorie", $idxCategorie);
		
					// Ajouter les messages
					$categorie->set("messages", $messages);
		
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "categorie-details.php";
		
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
		
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuCategorie($gabarit, $categorie);
				}
			}
		}	

		
		// 6. Vérifier si une section COLLECTION existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier les collections");
		
		if (isset($this->xml->collection)) {
		
			$this->log->debug("Importation::verifierXML() Collections localisées à vérifier");
		
			$messages = "";
			$idxCollection = 0;
			$collectionsTraites = array();
		
			$this->log->debug("Importation::verifierXML() Collection : '" . $this->xml->collection->asXML() . "'");
		
			// Traiter chacune des collections
			foreach ($this->xml->collection as $collectionXML) {
		
				$idxCollection++;
		
				$this->log->debug("Importation::verifierXML() CollectionXML : '$collectionXML'");
		
				// Obtenir l'id de la collection
				$idCollection = preg_replace('[\D]', '', $collectionXML->id_collection);
				if ($idCollection == "") {
					$idCollection = IMPORTATION_VALEUR_INCONNUE;
				}
		
				// Vérifier que l'id de la collection est unique
				if ($idCollection != "" && in_array( $idCollection, $collectionsTraites) ) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_227 . $idCollection . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($collectionsTraites, $idCollection);
				}
		
				$this->log->debug("Importation::verifierXML() Préparation de la validation d'une collection (idCollection='" . $idCollection . "')");
		
				$messages .= $this->verifierCollectionXML($collectionXML);
		
				if ($messages != "") {
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour la collection '$idCollection' : '$messages'");
		
					$collection = new Collection($this->log, $this->dbh);
		
					$collection->set("id_collection", $idCollection);
					$collection->set("idx_collection", $idxCollection);
		
					// Ajouter les messages
					$collection->set("messages", $messages);
		
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "collection-details.php";
		
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
		
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuCollection($gabarit, $collection);
				}
			}
		}		
		
		// 7. Vérifier si une section TERME existe, si oui vérifier les éléments
		$this->log->debug("Importation::verifierXML() Vérifier les termes");
		
		if (isset($this->xml->terme)) {
		
			$this->log->debug("Importation::verifierXML() Termes localisés à vérifier");
		
			$messages = "";
			$idxTerme = 0;
			$termesTraites = array();
		
			$this->log->debug("Importation::verifierXML() Terme : '" . $this->xml->terme->asXML() . "'");
		
			// Traiter chacun des termes
			foreach ($this->xml->terme as $termeXML) {
		
				$idxTerme++;
		
				$this->log->debug("Importation::verifierXML() TermeXML : '$termeXML'");
		
				// Obtenir l'id du terme
				$idTerme = preg_replace('[\D]', '', $termeXML->id_terme);
				if ($idTerme == "") {
					$idTerme = IMPORTATION_VALEUR_INCONNUE;
				}
		
				// Vérifier que l'id du terme est unique
				if ($idTerme != "" && in_array( $idTerme, $termesTraites) ) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_227 . $idTerme . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($termesTraites, $idTerme);
				}
		
				$this->log->debug("Importation::verifierXML() Préparation de la validation d'un terme (idTerme='" . $idTerme . "')");
		
				$messages .= $this->verifierTermeXML($termeXML);
		
				if ($messages != "") {
					$this->log->debug("Importation::verifierXML() Erreurs détectées pour le terme '$idTerme' : '$messages'");
		
					$terme = new Terme($this->log, $this->dbh);
		
					$terme->set("id_terme", $idTerme);
					$terme->set("idx_terme", $idxTerme);
		
					// Ajouter les messages
					$terme->set("messages", $messages);
		
					// Déterminer gabarit d'importation (messages)
					$gabarit = REPERTOIRE_GABARITS_IMPORTATION . "terme-details.php";
		
					// Vérifier si le fichier existe, sinon erreur
					if (!file_exists($gabarit)) {
						$this->log->erreur("Le gabarit '$gabarit' ne peut être localisé.");
					}
		
					// Obtenir le contenu pour validation
					$contenu .= Fichiers::getContenuTerme($gabarit, $terme);
				}
			}
		}
		
		$this->log->debug("Importation::verifierXML() Fin");
		
		return $contenu;
	}		
		

	/**
	 * 
	 * Verifier le XML pour un questionnaire
	 * @param array questionnaire XML
	 * @param string index questionanire
	 *
	 */
	public function verifierQuestionnaireXML($questXML, $idxQuest) {

		$this->log->debug("Importation::validerQuestXML() Début ($idxQuest = '$idxQuest')");
		
		$messages = "";
		
		// Vérifier le champ id_questionnaire
		if (preg_replace('[\D]', '', $questXML->id_questionnaire) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_050 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ titre
		if (!isset($questXML->titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_051 . HTML_LISTE_ERREUR_FIN;
		}		
		
		// Vérifier le champ titre_long
		if (!isset($questXML->titre_long)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_092 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ suivi
		if (!isset($questXML->suivi)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_061 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ generation_question_type
		if (!isset($questXML->generation_question_type)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_093 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ generation_question_nb
		if (!isset($questXML->generation_question_nb)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_094 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ temps_reponse_calculer
		if (!isset($questXML->temps_reponse_calculer)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_096 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ essais_repondre_type
		if (!isset($questXML->essais_repondre_type)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_097 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ essais_repondre_nb
		if (!isset($questXML->essais_repondre_nb)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_098 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ affichage_resultats_type
		if (!isset($questXML->affichage_resultats_type)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_099 . HTML_LISTE_ERREUR_FIN;
		}			

		// Vérifier le champ demarrage_media_type
		if (!isset($questXML->demarrage_media_type)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_100 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ id_langue_questionnaire
		if (!isset($questXML->id_langue_questionnaire)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_101 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ theme
		if (!isset($questXML->theme)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_102 . HTML_LISTE_ERREUR_FIN;
		} else {
			// Vérifier que si un thème est spécifié, qu'il existe
			$theme = (string)$questXML->theme;
			$repertoireSourceTheme = REPERTOIRE_THEMES . $theme . "/";
			$this->log->debug("Importation::verifierTheme() Vérifier que le thème sélectionné existe ('$repertoireSourceTheme')");
			if (!is_dir($repertoireSourceTheme)) {
				//$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_204 . HTML_LISTE_ERREUR_FIN;
				$this->log->debug("Importation::verifierTheme() Problème détecté : Le répertoire du thème sélectionné n'existe pas (theme = '$theme'). Le thème par défaut sera utilisé");
			}
		}	

		// Vérifier le champ mot_bienvenue
		if (!isset($questXML->mot_bienvenue)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_103 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ note
		if (!isset($questXML->note)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_104 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ generique
		if (!isset($questXML->generique)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_105 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ media_titre
		if (!isset($questXML->media_titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_106 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ media_texte
		if (!isset($questXML->media_texte)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_107 . HTML_LISTE_ERREUR_FIN;
		}			
		
		// Vérifier le champ media_image
		if (!isset($questXML->media_image)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_108 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ media_son
		if (!isset($questXML->media_son)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_109 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ media_video
		if (!isset($questXML->media_video)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_110 . HTML_LISTE_ERREUR_FIN;
		}	

		// Vérifier le champ texte_fin
		if (!isset($questXML->texte_fin)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_111 . HTML_LISTE_ERREUR_FIN;
		}
					
		// Vérifier la section questionnaire-item
		$messages .= $this->verifierQuestionnaireItemXML($questXML);
		
		// Vérifier la section questionnaire-section
		$messages .= $this->verifierQuestionnaireSectionXML($questXML);
		
		// Vérifier le XML de toutes les sections
		foreach ($questXML->section as $sectionXML) {
			$messages .= $this->verifierSectionXML($sectionXML);
		}
		
		$this->log->debug("Importation::validerQuestXML() Fin");
		
		return $messages;
	}
	

	/**
	 * 
	 * Vérifier le XML pour la section questionnaire_item
	 * @param obj questionnaire XML
	 *
	 */
	public function verifierQuestionnaireItemXML($questXML) {
	
		$this->log->debug("Importation::verifierQuestionnaireItemXML() Début");
		
		$messages = "";
		$itemsElementTraites = array();
		
		// Obtenir la liste des items existants en prévision de la vérification
		// Localiser l'item
		foreach ($this->xml->item as $item) {
			$idItemTemp = (string)$item->id_item;
			$this->log->debug("Importation::verifierQuestionnaireItemXML() idItem : '" . $idItemTemp . "'");
			
			// Vérifier si on a déjà traité cet id (élément de type item)
			if ($idItemTemp != "" && in_array( $idItemTemp, $itemsElementTraites) ) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_206 . $idItemTemp . HTML_LISTE_ERREUR_FIN;
				$this->log->debug("Importation::verifierQuestionnaireItemXML() ***** DUPLICATE idItemTemp : '" . $idItemTemp . "'");
			} else {
				$this->log->debug("Importation::verifierQuestionnaireItemXML() ***** AJOUT idItemTemp : '" . $idItemTemp . "'");
				array_push($itemsElementTraites, $idItemTemp);
			}
			array_push($this->listeItemsVerif, $idItemTemp);
				
		}

		// Items vérifiés
		$itemsTraites = array();
		
		// Vérifier chacun des items
		foreach ($questXML->questionnaire_item as $questItemXML) {
			
			$type = $questItemXML->type;

			if ($type == "item") {
				$idItem = (string)$questItemXML->id_item;
				if ($idItem == "") {
					$idItem = IMPORTATION_VALEUR_INCONNUE;
				}
				
				$this->log->debug("Importation::verifierQuestionnaireItemXML() Vérification du questionnaire_item '$idItem'");
				
				// Vérifier le champ id_item
				if (!isset($questItemXML->id_item)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_112 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier si on a déjà traité cet id (item dans un questionnaire)
				$this->log->debug("Importation::verifierQuestionnaireItemXML() Vérifier '$idItem'");
				if ($idItem != "" && in_array( $idItem, $itemsTraites) ) {
					$this->log->debug("Importation::verifierQuestionnaireItemXML() **** ERREUR ****");
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_205 . $idItem . HTML_LISTE_ERREUR_FIN;
				} else {
					array_push($itemsTraites, $idItem);
				}
				
				// Vérifier le champ id_section
				if (!isset($questItemXML->id_section)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_113 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ ponderation_quest
				if (!isset($questItemXML->ponderation_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_114 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ demarrer_media_quest
				if (!isset($questItemXML->demarrer_media_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_115 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
	
				// Vérifier le champ ordre_presentation_quest
				if (!isset($questItemXML->ordre_presentation_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_116 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ type_etiquettes_quest
				if (!isset($questItemXML->type_etiquettes_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_117 . $idItem . HTML_LISTE_ERREUR_FIN;
				}

				// Vérifier le champ type_bonnesreponses_quest
				if (!isset($questItemXML->type_bonnesreponses_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_122 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ points_retranches_quest
				if (!isset($questItemXML->points_retranches_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_124 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ majmin_quest
				if (!isset($questItemXML->majmin_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_125 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ ponctuation_quest
				if (!isset($questItemXML->ponctuation_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_134 . $idItem . HTML_LISTE_ERREUR_FIN;
				}

				// Vérifier le champ orientation_elements_quest
				if (!isset($questItemXML->orientation_elements_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_156 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ couleur_element_quest
				if (!isset($questItemXML->couleur_element_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_166 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ couleur_element_associe_quest
				if (!isset($questItemXML->couleur_element_associe_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_167 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ afficher_masque_quest
				if (!isset($questItemXML->afficher_masque_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_168 . $idItem . HTML_LISTE_ERREUR_FIN;
				}

				// Vérifier le champ type_champs_quest
				if (!isset($questItemXML->type_champs_quest)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_169 . $idItem . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier que la section existe au besoin
				if (isset($questItemXML->id_section) && ($questItemXML->id_section > 0)) { 
					$messages .= $this->verifierSectionDisponible($questItemXML->id_section, $questXML);
				}
	
				// Vérifier que l'item existe au besoin
				if (isset($questItemXML->id_item) && ($questItemXML->id_item > 0)) { 
					$messages .= $this->verifierItemDisponible($questItemXML->id_item);
				}
			} elseif ($type == "section") {
				
				// TODO : Compléter la validation pour une section au besoin
				
			}
			
		}
		
		$this->log->debug("Importation::verifierQuestionnaireItemXML() Fin");
		
		return $messages;
	}
	
	
	/**
	 * 
	 * Vérifier le XML pour la sectiionquestionnaire_section
	 * @param obj questionnaire XML
	 *
	 */
	public function verifierQuestionnaireSectionXML($questXML) {
	
		$this->log->debug("Section::verifierQuestionnaireSectionXML() Début");
		
		$messages = "";
		
		foreach ($questXML->questionnaire_section as $questSectionXML) {
			
			$idSection = (string)$questSectionXML->id_section;
			if ($idSection == "") {
				$idSection = IMPORTATION_VALEUR_INCONNUE;
			}
			
			$this->log->debug("Section::verifierQuestionnaireSectionXML() Vérification du questionnaire_section '$idSection'");
			
			// Vérifier le champ id_section
			if (preg_replace('[\D]', '', $questSectionXML->id_section) == "") {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_117 . HTML_LISTE_ERREUR_FIN;
			}			

			// Vérifier que la section existe au besoin
			if (isset($questSectionXML->id_section) && ($questSectionXML->id_section > 0)) { 
				$messages .= $this->verifierSectionDisponible($questSectionXML->id_section, $questXML);
			}
			
		}
		
		$this->log->debug("Section::verifierQuestionnaireSectionXML() Fin");
		
		return $messages;
	}
		
	
	/**
	 * 
	 * Verifier le XML pour un item
	 * @param array item XML
	 * @param string index item
	 *
	 */
	public function verifierItemXML($itemXML, $idxItem) {

		$this->log->debug("Importation::verifierXML() Début (idxItem = '$idxItem')");

		$messages = "";
		$contenu = "";

		if ($itemXML->type == "item") {
		
			$typeItem = preg_replace('[\D]', '', $itemXML->type_item);
			
			// Vérifier le champ id_item
			if (preg_replace('[\D]', '', $itemXML->id_item) == "") {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_050 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ titre
			if (!isset($itemXML->titre)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_051 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ enonce
			if (!isset($itemXML->enonce)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_082 . HTML_LISTE_ERREUR_FIN;
			}

			// Vérifier le champ solution
			if (!isset($itemXML->solution)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_135 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ retroaction_positive
			if (!isset($itemXML->retroaction_positive)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_136 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ retroaction_negative
			if (!isset($itemXML->retroaction_negative)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_137 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ info_comp1_titre
			if (!isset($itemXML->info_comp1_titre)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_052 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ info_comp1_texte
			if (!isset($itemXML->info_comp1_texte)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_053 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ info_comp2_titre
			if (!isset($itemXML->info_comp2_titre)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_054 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ info_comp2_texte
			if (!isset($itemXML->info_comp2_texte)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_055 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ media_titre
			if (!isset($itemXML->media_titre)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_056 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ media_texte
			if (!isset($itemXML->media_texte)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_057 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ media_image
			if (!isset($itemXML->media_image)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_058 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ media_son
			if (!isset($itemXML->media_son)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_059 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ media_video
			if (!isset($itemXML->media_video)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_060 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ suivi
			if (!isset($itemXML->suivi)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_061 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ ponderation
			if (!isset($itemXML->ponderation)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_062 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ ordre_presentation
			if (!isset($itemXML->ordre_presentation)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_063 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ type_etiquettes
			if (!isset($itemXML->type_etiquettes)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_064 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ afficher_solution
			if (!isset($itemXML->afficher_solution)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_065 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ points retranchés
			if (!isset($itemXML->points_retranches)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_066 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ majmin
			if (!isset($itemXML->majmin)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_067 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ ponctuation
			if (!isset($itemXML->ponctuation)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_068 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ couleur_zone
			if (!isset($itemXML->couleur_zone)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_171 . HTML_LISTE_ERREUR_FIN;
			}
				

			// Vérifier le champ type_elements1
			if (!isset($itemXML->type_elements1)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_069 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ demarrer_media
			if (!isset($itemXML->demarrer_media)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_070 . HTML_LISTE_ERREUR_FIN;
			}		
			
			// Vérifier le champ reponse_bonne_message
			if (!isset($itemXML->reponse_bonne_message)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_071 . HTML_LISTE_ERREUR_FIN;
			}
	
			// Vérifier le champ reponse_mauvaise_message
			if (!isset($itemXML->reponse_mauvaise_message)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_072 . HTML_LISTE_ERREUR_FIN;
			}		
	
			// Vérifier le champ reponse_incomplete_message
			if (!isset($itemXML->reponse_incomplete_message)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_073 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ reponse_bonne_media
			if (!isset($itemXML->reponse_bonne_media)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_074 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ reponse_mauvaise_media
			if (!isset($itemXML->reponse_mauvaise_media)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_075 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ reponse_incomplete_media
			if (!isset($itemXML->reponse_incomplete_media)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_076 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ remarque
			if (!isset($itemXML->remarque)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_077 . HTML_LISTE_ERREUR_FIN;
			}
			
			// Vérifier le champ suivi
			if (!isset($itemXML->suivi) || ($itemXML->suivi != "1" && $itemXML->suivi != "0" && $itemXML->suivi != "")) {		
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_087 . HTML_LISTE_ERREUR_FIN;
			}		
			
			// Champ pour réponse brève
			if ($typeItem == 3) {
					
				// Vérifier le champ orientation_elements
				if (!isset($itemXML->orientation_elements)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_157 . HTML_LISTE_ERREUR_FIN;
				}
			}			

			// Champ pour marquage
			if ($typeItem == 7) {
				// Vérifier le champ retroaction_reponse_imprevue
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_151 . HTML_LISTE_ERREUR_FIN;
				if (!isset($itemXML->retroaction_reponse_imprevue)) {
				}
			}
			
			// Champ pour réponse brève
			if ($typeItem == 9) {
				// Vérifier le champ retroaction_reponse_imprevue
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_151 . HTML_LISTE_ERREUR_FIN;
				if (!isset($itemXML->retroaction_reponse_imprevue)) {
				}
				
				// Vérifier le champ couleur_element
				if (!isset($itemXML->couleur_element)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_152 . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ couleur_element_associe
				if (!isset($itemXML->couleur_element_associe)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_153 . HTML_LISTE_ERREUR_FIN;
				}
	
				// Vérifier le champ afficher_masque
				if (!isset($itemXML->afficher_masque)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_154 . HTML_LISTE_ERREUR_FIN;
				}
			}
						
			// Vérifier le type d'item
			if (!isset($typeItem) || $typeItem < 1 || $typeItem > 15 ) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_078 . HTML_LISTE_ERREUR_FIN;
			}		
	
			// Vérifier les réponses
			$messages .= $this->verifierReponsesXML($typeItem, $itemXML);

		}
			
		$this->log->debug("Importation::verifierXML() Fin");
		
		return $messages;
	}					


	/**
	 * 
	 * Vérifier le XML pour une section
	 * @param array section XML
	 *
	 */
	public function verifierSectionXML($sectionXML) {
	
		$this->log->debug("Importation::verifierSectionXML() Début");
		
		$messages = "";
		$trouve = false;
		
		// Vérifier le champ id_section
		if (preg_replace('[\D]', '', $sectionXML->id_section) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_118 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Obtenir l'identifiant de la section
		$idSection = preg_replace('[\D]', '', $sectionXML->id_section);
		if ($idSection == "") {
			$idSection = IMPORTATION_VALEUR_INCONNUE;
		}

		// Vérifier le champ titre
		if (!isset($sectionXML->titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_119 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ generation_question_type
		if (!isset($sectionXML->generation_question_type)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_120 . HTML_LISTE_ERREUR_FIN;
		}	
		
		$this->log->debug("Importation::verifierSectionXML() Fin");
		
	}
	
	
	/**
	 * 
	 * Vérifier le XML pour un média
	 * @param array item XML
	 *
	 */
	public function verifierMediaXML($mediaXML) {
	
		$this->log->debug("Importation::verifierMediaXML() Début");
		
		$messages = "";
		$trouve = false;

		$repertoireMedia = $this->repertoireXML . REPERTOIRE_PREFIX_MEDIAS;
		$this->log->debug("Importation::verifierMediaXML() RepertoireMedia : '$repertoireMedia'");

		
		// Vérifier le champ id_media
		if (preg_replace('[\D]', '', $mediaXML->id_media) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_081 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ titre
		if (!isset($mediaXML->titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_051 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ remarque
		if (!isset($mediaXML->remarque)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_077 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ description
		if (!isset($mediaXML->description)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_083 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ type
		if (!isset($mediaXML->type) || ($mediaXML->type != "son" && $mediaXML->type != "image" && $mediaXML->type != "video" && $mediaXML->type != "") ) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_084 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ source
		if (!isset($mediaXML->source) || ($mediaXML->source != "web" && $mediaXML->source != "fichier" && $mediaXML->source != "")) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_085 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ url
		if (!isset($mediaXML->url)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_086 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ suivi
		if (!isset($mediaXML->suivi) || ($mediaXML->suivi != "1" && $mediaXML->suivi != "0" && $mediaXML->suivi != "")) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_087 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Si un fichier est requis, vérifier qu'il est présent
		
		if ($mediaXML->source == "fichier") {
			$this->log->debug("Importation::verifierMediaXML() fichier : '" . $mediaXML->fichier . "'");
			
			// Vérifier si le fichier existe dans le répertoire média
			$fnMedia = $repertoireMedia . $mediaXML->fichier;
			$this->log->debug("Importation::verifierMediaXML() Vérifier le fichier média : '" . $fnMedia . "'");
			if (! file_exists($fnMedia)) {
				$this->log->debug("Importation::verifierMediaXML() Impossible de localiser le fichier '$fnMedia'");
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_079 . $mediaXML->fichier .  HTML_LISTE_ERREUR_FIN;
			} else {
				$this->log->debug("Importation::verifierMediaXML() Le fichier '$fnMedia' a été vérifié avec succès.");
			} 
		}
		
		$this->log->debug("Importation::verifierMediaXML() Fin");
		return $messages;
	}
	
	
	/**
	 *
	 * Vérifier le XML pour une langue
	 * @param array langue XML
	 *
	 */
	public function verifierLangueXML($langueXML) {
	
		$this->log->debug("Importation::verifierLangueXML() Début");
	
		$messages = "";
		$trouve = false;
	
		// Vérifier le champ id_langue
		if (preg_replace('[\D]', '', $langueXML->id_langue) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_228 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ titre
		if (!isset($langueXML->titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_051 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ remarque
		if (!isset($langueXML->remarque)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_077 . HTML_LISTE_ERREUR_FIN;
		}
	
			
		$this->log->debug("Importation::verifierLangueXML() Fin");
		return $messages;
	}
		
	
	/**
	 *
	 * Vérifier le XML pour une collection
	 * @param array collection XML
	 *
	 */
	public function verifierCollectionXML($collectionXML) {
	
		$this->log->debug("Importation::verifierCollectionXML() Début");
	
		$messages = "";
		$trouve = false;
	
		// Vérifier le champ id_collection
		if (preg_replace('[\D]', '', $collectionXML->id_collection) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_229 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ titre
		if (!isset($collectionXML->titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_051 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ remarque
		if (!isset($collectionXML->remarque)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_077 . HTML_LISTE_ERREUR_FIN;
		}
	
			
		$this->log->debug("Importation::verifiercollectionXML() Fin");
		return $messages;
	}
		
	
	/**
	 *
	 * Vérifier le XML pour une categorie
	 * @param array categorie XML
	 *
	 */
	public function verifierCategorieXML($categorieXML) {
	
		$this->log->debug("Importation::verifierCategorieXML() Début");
	
		$messages = "";
		$trouve = false;
	
		// Vérifier le champ id_categorie
		if (preg_replace('[\D]', '', $categorieXML->id_categorie) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_230 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ titre
		if (!isset($categorieXML->titre)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_051 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ remarque
		if (!isset($categorieXML->remarque)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_077 . HTML_LISTE_ERREUR_FIN;
		}
	
			
		$this->log->debug("Importation::verifiercategorieXML() Fin");
		return $messages;
	}
	
	
	/**
	 *
	 * Vérifier le XML pour un terme
	 * @param array terme XML
	 *
	 */
	public function verifierTermeXML($termeXML) {
	
		$this->log->debug("Importation::verifierTermeXML() Début");
	
		$messages = "";
		$trouve = false;
	
		// Vérifier le champ id_terme
		if (preg_replace('[\D]', '', $termeXML->id_terme) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_170 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier le champ terme
		if (!isset($termeXML->terme)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_231 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ liste_variantes
		if (!isset($termeXML->variantes)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_232 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ type_definition
		if (!isset($termeXML->type_definition)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_233 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ texte
		if (!isset($termeXML->texte)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_234 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ url
		if (!isset($termeXML->url)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_235 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ media_image
		if (!isset($termeXML->media_image)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_058 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le champ media_son
		if (!isset($termeXML->media_son)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_059 . HTML_LISTE_ERREUR_FIN;
		}
				
		// Vérifier le champ media_video
		if (!isset($termeXML->media_video)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_060 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ remarque
		if (!isset($termeXML->remarque)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_077 . HTML_LISTE_ERREUR_FIN;
		}
	
			
		$this->log->debug("Importation::verifierTermeXML() Fin");
		return $messages;
	}	
		
	
	/**
	 * 
	 * Vérifier le XML pour les choix de réponses
	 * @param String type d'item
	 * @param Item item XML
	 *
	 */
	public function verifierReponsesXML($typeItem, $itemXML) {
	
		$this->log->debug("Importation::verifierReponsesXML() Début");
		
		$messages = "";
		
		
		if ($typeItem == "2" || $typeItem == "8" || $typeItem == "10") {

			// Déterminer si on doit vérifier les médias
			$typeElements1 = (string)$itemXML->type_elements1;
			
			foreach ($itemXML->reponse as $reponseXML) {
				
				$this->log->debug("Importation::verifierReponsesXML() Vérification de la réponse '" . $reponseXML->id_reponse . "'");
				
				// Vérifier le champ id_item_reponse
				if (!isset($reponseXML->id_item_reponse)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_088 . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ bonne_reponse
				if (!isset($reponseXML->bonne_reponse)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_089 . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ element
				if (!isset($reponseXML->element)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_090 . HTML_LISTE_ERREUR_FIN;
				}
				
				// Vérifier le champ retroaction
				if (!isset($reponseXML->retroaction)) {
					$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_091 . HTML_LISTE_ERREUR_FIN;
				}

				if ($typeItem == "4") {
					// Vérifier le champ masque
					if (!isset($reponseXML->masque)) {
						$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_147 . HTML_LISTE_ERREUR_FIN;
					}
				}				
				
				if ($typeItem == "8") {
					// Vérifier le champ retroaction_negative
					if (!isset($reponseXML->retroaction_negative)) {
						$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_123 . HTML_LISTE_ERREUR_FIN;
					}
				}
				
				// Si la réponse est une image, vérifier que le média est présent
				if ($typeElements1 == "image") {
					$this->log->debug("Importation::verifierReponsesXML() Vérifier que le média '" . $reponseXML->element . "' existe");
					$messages .= $this->verifierMediaDisponible($reponseXML->element);
				}
				
			}
		}	
		
		$this->log->debug("Importation::verifierReponsesXML() Fin");
		
		return $messages;
	}


	/**
	 * 
	 * Vérifier que l'item existe
	 * @param string id Item
	 *
	 */
	public function verifierItemDisponible($idItem) {
	
		$this->log->debug("Importation::verifierItemDisponible() Début (idItem='$idItem')");
		
		$messages = "";
		
		if (! in_array($idItem, $this->listeItemsVerif)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_126 . $idItem .  HTML_LISTE_ERREUR_FIN;
		}
		
		$this->log->debug("Importation::verifierItemDisponible() Fin");
		return $messages;
	}	
	
	
	/**
	 * 
	 * Vérifier que la section existe
	 * @param string id Section
	 * @param string questionnaire XML
	 *
	 */
	public function verifierSectionDisponible($idSection, $questXML) {
	
		$this->log->debug("Importation::verifierSectionDisponible() Début (idSection='$idSection')");
		
		$messages = "";
		$trouve = false;
		
		$this->log->debug("Importation::verifierSectionDisponible() Vérifier section : '$idSection'");

		// Localiser la section
		foreach ($questXML->section as $section) {
			$this->log->debug("Importation::verifierSectionDisponible() idSection : '" . $section->id_section . "'  Section recherchée : '$idSection'");
			
			if ((string)$section->id_section == (string)$idSection) {
				
				// Trouvé!
				$trouve = true;
			}
		}
		
		if (!$trouve) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_095 . $idSection .  HTML_LISTE_ERREUR_FIN;
		}
		
		$this->log->debug("Importation::verifierSectionDisponible() Fin");
		return $messages;
	}	
	
	
	/**
	 * 
	 * Vérifier que le média existe
	 * @param string id item
	 *
	 */
	public function verifierMediaDisponible($idMedia) {
	
		$this->log->debug("Importation::verifierMediaDisponible() Début (idMedia='$idMedia')");
		
		$messages = "";
		$trouve = false;
		
		$this->log->debug("Importation::verifierMediaDisponible() Vérifier média : '$idMedia'");
		$repertoireMedia = $this->repertoireXML . REPERTOIRE_PREFIX_MEDIAS;
		$this->log->debug("Importation::verifierMediaDisponible() RepertoireMedia : '$repertoireMedia'");

		// Localiser le média
		foreach ($this->xml->media as $media) {
			$this->log->debug("Importation::verifierMediaDisponible() IDMEDIA : '" . $media->id_media . "'  Media recherché : '$idMedia'");
			
			if ((string)$media->id_media == (string)$idMedia) {
				$this->log->debug("Importation::verifierMediaDisponible() source: '" . $media->source . "'");
				
				// Trouvé!
				$trouve = true;
			}
		}
		
		if (!$trouve) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_080 . $idMedia .  HTML_LISTE_ERREUR_FIN;
		}
		
		$this->log->debug("Importation::verifierMediaDisponible() Fin");
		return $messages;
	}
		
	
	/**
	 * 
	 * Obtenir le chemin du répertoire temporaire
	 * 
	 */ 
	protected function getTempDir() {
		
		$this->log->debug("Importation::getTempDir() Début");
		
		// Obtenir un suffixe à partir de la session pour prévenir les collisions
		$session = new Session();
		$suffixe = substr($session->getSessionIdSecuritaire(), 0, 10);
		
		$prefixUsager = Securite::nettoyerNomfichier(REPERTOIRE_PREFIX_IMPORT . "_" . $this->usager->get("code_usager") . "_" . $suffixe );
		$this->log->debug("Importation::getTempDir() '$prefixUsager'");
		$fn = sys_get_temp_dir() . "/" . $prefixUsager;

		$this->log->debug("Importation::getTempDir() Fin");
		
		return $fn;
	}
	
	
	

	/**
	 *
	 * Remplacer les ids des médias dans les rich text au besoin
	 * @param obj
	 *
	 */
	protected function remplacerIDMediaTextes($obj) {
		
	$this->log->debug("Importation::remplacerIDMediaTextes() Début - Vérifier les médias dans les textes");
		
	// Ajouter les médias qui sont dans les zones de texte (rich text)
	$cles = $obj->getListeCles();
		
	// Parcourir la liste de clés
	foreach ($cles as $cle) {
	
		$this->log->debug("Importation::importerItems() Clé : '$cle'");
	
		$contenu = $obj->get($cle);
		$matches = array();
			
		// Effectuer la recherche
		preg_match_all("/\[M(\d+?)]/i", $contenu, $matches, PREG_SET_ORDER);
			
		foreach ($matches as $val) {
				
			// Obtenir le média trouvé
			$matchMedia = $val[0];
			$idMedia = $val[1];
				
			// Remplacer l'id du média
			$idMediaConv = $this->conversionMedias[(int)$idMedia];
			$this->log->debug("Importation::remplacerIDMediaTextes() Remplacer Média : '$idMedia' par '$idMediaConv'");
				
			// Préparer le remplacement
			$txtMedia = "[" . TXT_PREFIX_MEDIA . $idMediaConv . "]";
			$contenu = str_replace($matchMedia, $txtMedia, $contenu);
				
			// Modifier avec la clé
			$obj->set($cle, $contenu);
		}
	}	
	
	$this->log->debug("Importation::remplacerIDMediaTextes() Fin - Vérifier les médias dans les textes");
	
	}
	
	
}
?>