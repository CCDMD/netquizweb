<?php

require_once '../ressources/classes/outils/Session.php';

/** 
 * Classe Item
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

class Item {
	
	protected $dbh;
	protected $log;
	
	protected $listeChampsItem = "id_item, id_projet, titre, enonce, info_comp1_titre, info_comp1_texte, info_comp2_titre, info_comp2_texte, 
								  media_titre, media_texte, media_image, media_son, media_video, solution, retroaction_positive, retroaction_negative, 
								  retroaction_reponse_imprevue, couleur_element, couleur_element_associe, afficher_masque, type_item, id_categorie, suivi, ponderation, 
								  ordre_presentation, type_etiquettes, type_champs, afficher_solution, 
								  points_retranches, majmin, ponctuation, type_elements1, type_elements2, demarrer_media, 
								  reponse_bonne_message, reponse_bonne_media, reponse_mauvaise_message, reponse_mauvaise_media, reponse_incomplete_message, 
								  reponse_incomplete_media, remarque, liens, type_bonnesreponses, orientation_elements, statut, image, couleur_zones, 
								  date_modification, date_creation";
							  
	protected $donnees;
	
	/**
	 *  
	 * Constructeur
	 * @param Log $log
	 * @param PDO $dbh
	 * 
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$log->debug("Item::__construct() Début");
		$this->dbh = $dbh;
		$this->log = $log;
		
		$donnees = array();
		$log->debug("Item::__construct() Fin");
				
		return;
	}


	/**
	 *
	 * Obtenir la liste des clés
	 *
	 */
	
	public function getListeCles() {
		
		$this->log->debug("Item::getListeCles() Début");
		
		return array_keys($this->donnees);
		
		$this->log->debug("Item::getListeCles() Fin");
	}
	
	
	/**
	 * 
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {

		$this->log->debug("Item::getDonneesRequete() Début");
		
		// Supprimer les informations sur les items afin de traiter
		// adéquatement la suppression d'un choix de réponse
		$this->supprimerChoixReponses();
		
		// Obtenir les paramètres
		$params = Web::getListeParam("item_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'\n";
		}

		// Traiter les données pour le panneau paramètres
		$this->getDonneesPanneauParametres();
		
		// Traiter les données pour le panneau messages
		$this->getDonneesPanneauMessages();

		// Traiter les choix de réponses
		$this->getDonneesChoixReponses();
		
		$this->log->debug("Item::getDonneesRequete() Fin");
		
		return;
	}	

	/**
	 * 
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	protected function getDonneesPanneauParametres() {

		$this->log->debug("Item::getDonneesPanneauParametres() Début");

		// Si le flag de suppression des données pour le panneau
		// "modifier les valeurs "pour ce questionnaire seulement - paramètres"
		// est activé, supprimer les valeurs.
		
		$paramPanneauParametres = Web::getParamNum("vider_panneau_parametres");
		
		$this->log->debug("Item::getDonneesPanneauParametres() Vider Parametres : '$paramPanneauParametres'"); 
		
		if ($paramPanneauParametres == "1") {
			$this->set("ponderation_quest", "");
			$this->set("demarrer_media_quest", "");
			$this->set("afficher_solution_quest", "");
			$this->set("ordre_presentation_quest", "");
			$this->set("type_etiquettes_quest", "");
			$this->set("type_bonnesreponses_quest", "");
			$this->set("orientation_elements_quest", "");
			$this->set("couleur_element_quest", "");
			$this->set("couleur_element_associe_quest", "");
			$this->set("afficher_masque_quest", "");
			$this->set("points_retranches_quest", "");
			$this->set("majmin_quest", "");
			$this->set("ponctuation_quest", "");
		}

		// Ne pas conserver les valeurs "pour ce questionnaire seulement" qui 
		// répète la configuration actuelle de l'item
		if ($this->get("ponderation_quest") == $this->get("ponderation")) {
			$this->set("ponderation_quest","");
		}
		if ($this->get("demarrer_media_quest") == $this->get("demarrer_media")) {
			$this->set("demarrer_media_quest","");
		}
		if ($this->get("afficher_solution_quest") == $this->get("afficher_solution")) {
			$this->set("afficher_solution_quest","");
		}
		if ($this->get("ordre_presentation_quest") == $this->get("ordre_presentation")) {
			$this->set("ordre_presentation_quest","");
		}
		if ($this->get("type_etiquettes_quest") == $this->get("type_etiquettes")) {
			$this->set("type_etiquettes_quest","");
		}
		if ($this->get("type_bonnesreponses_quest") == $this->get("type_bonnesreponses")) {
			$this->set("type_bonnesreponses_quest","");
		}
		if ($this->get("orientation_elements_quest") == $this->get("orientation_elements")) {
			$this->set("orientation_elements_quest","");
		}
		if ($this->get("couleur_element_quest") == $this->get("couleur_element")) {
			$this->set("couleur_element_quest","");
		}
		if ($this->get("couleur_element_associe_quest") == $this->get("couleur_element_associe")) {
			$this->set("couleur_element_associe_quest","");
		}
		if ($this->get("afficher_masque_quest") == $this->get("afficher_masque")) {
			$this->set("afficher_masque_quest","");
		}
		if ($this->get("points_retranches_quest") == $this->get("points_retranches")) {
			$this->set("points_retranches_quest","");
		}
		if ($this->get("majmin_quest") == $this->get("majmin")) {
			$this->set("majmin_quest","");
		}
		if ($this->get("ponctuation_quest") == $this->get("ponctuation")) {
			$this->set("ponctuation_quest","");
		}
		
		$this->log->debug("Item::getDonneesPanneauParametres() Fin");
	}
	
	
	/**
	 * 
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	protected function getDonneesPanneauMessages() {

		$this->log->debug("Item::getDonneesPanneauMessages() Début");	
		
		// Obtenir la langue
		$langueItem = $this->getLangueApercuObj();
		
		// Si le flag de suppression des données pour le panneau
		// "modifier les valeurs pour ce questionnaire seulement - paramètres"
		// est activé, supprimer les valeurs.
		$paramPanneauMessages = Web::getParamNum("vider_panneau_messages");
		
		$this->log->debug("Item::getDonneesPanneauParametres() Vider Messages : '$paramPanneauMessages'");
		
		if ($paramPanneauMessages == "1") {
			$this->set("reponse_bonne_message", "");
			$this->set("reponse_mauvaise_message", "");
			$this->set("reponse_incomplete_message", "");
			$this->set("reponse_bonne_media", "");
			$this->set("reponse_mauvaise_media", "");
			$this->set("reponse_incomplete_media", "");
			$this->set("reponse_bonne_media_txt", "");
			$this->set("reponse_mauvaise_media_txt", "");
			$this->set("reponse_incomplete_media_txt", "");
		} else {
			// Cas spéciaux pour les médias du panneau messages
			if ($this->get("reponse_bonne_media") == "" && $langueItem->get("media_bonnereponse") != 0) {
				$this->set("reponse_bonne_media", -1);		
			}
			if ($this->get("reponse_mauvaise_media") == "" && $langueItem->get("media_mauvaisereponse") != 0) {
				$this->set("reponse_mauvaise_media", -1);		
			}
			if ($this->get("reponse_incomplete_media") == "" && $langueItem->get("media_reponseincomplete") != 0) {
				$this->set("reponse_incomplete_media", -1);		
			}
		}
		
		// Ne pas conserver les valeurs "pour cet item seulement" qui 
		// répète la configuration actuelle de l'item
		if ($this->get("reponse_bonne_message") == $langueItem->get("message_bonnereponse")) {
			$this->set("reponse_bonne_message","");
		}
		if ($this->get("reponse_mauvaise_message") == $langueItem->get("message_mauvaisereponse")) {
			$this->set("reponse_mauvaise_message","");
		}
		if ($this->get("reponse_incomplete_message") == $langueItem->get("message_reponseincomplete")) {
			$this->set("reponse_incomplete_message","");
		}
		if ($this->get("reponse_bonne_media") == $langueItem->get("media_bonnereponse")) {
			$this->set("reponse_bonne_media","");
			$this->set("reponse_bonne_media_txt","");
		}
		if ($this->get("reponse_mauvaise_media") == $langueItem->get("media_mauvaisereponse")) {
			$this->set("reponse_mauvaise_media","");
			$this->set("reponse_mauvaise_media_txt","");
		}
		if ($this->get("reponse_incomplete_media") == $langueItem->get("media_reponseincomplete")) {
			$this->set("reponse_incomplete_media","");
			$this->set("reponse_incomplete_media_txt","");
		}
		
		$this->log->debug("Item::getDonneesPanneauMessages() Fin");
	}
	
	/**
	 * 
	 * Obtenir les valeurs des choix de réponses
	 * 
	 */
	protected function getDonneesChoixReponses() {

		$this->log->debug("Item::getDonneesChoixReponses() Début");	

		$idx = 0;
		
		for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			
			// Obtenir les éléments et réponses
			$element = $this->get("reponse_" . $i . "_element");
			$elementAssocie = $this->get("reponse_" . $i . "_element_associe");
			$masque = $this->get("reponse_" . $i . "_masque");
			$retro = $this->get("reponse_" . $i . "_retroaction");
			$retroNeg = $this->get("reponse_" . $i . "_retroaction_negative");
			$retroIncomp = $this->get("reponse_" . $i . "_retroaction_incomplete");

			if ($element != "" || $elementAssocie != "" || $masque != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "") {
				$idx = $i;
			}
		}
		
		// Noter le nombre de choix multiples
		$this->set("reponse_total", $idx);
		
		$this->log->debug("Item::getDonneesChoixReponses() Fin");
	}
	
	
	/**
	 * 
	 * Charger l'item à partir de la base de données
	 * @param String idQuestionnaire
	 * @param String idProjet
	 * 
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("Item::getItemParId() Début idItem = '$idItem'  idProjet = '$idProjet'");
		$trouve = false;

		try {
			$sql = "SELECT " . $this->listeChampsItem . " from titem where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
			
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun item trouvé pour l'id '$idItem'");
			}
			
			// Vérifier qu'un seul item est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour l'item id '$idItem' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour l'item
				$result = $sth->fetchAll();
			
			    foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->donnees[$cle] = $row[$cle];
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
		        
		        // Indiquer qu'un et un seul item a été trouvé
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::getItemParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Préparer les valeurs après chargement
		$this->preparerValeursApresChargement($idProjet);
						
		// Terminé
		$this->log->debug("Item::getItemParId() Trouve = '$trouve'");
		$this->log->debug("Item::getItemParId() Fin");
		return $trouve;		
	}	


	/**
	 * 
	 * Préparer les informations après chargement
	 * @param String idProjet
	 *  
	 */
	public function preparerValeursApresChargement($idProjet) {
		
		$this->log->debug("Item::preparerValeursApresChargement() Début");

		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Préparer la liste des médias
		$this->set("media_image_txt", Media::getMediaIdTitre($this->get("media_image"), $idProjet, $this->log, $this->dbh));
		$this->set("media_son_txt", Media::getMediaIdTitre($this->get("media_son"), $idProjet, $this->log, $this->dbh));
		$this->set("media_video_txt", Media::getMediaIdTitre($this->get("media_video"), $idProjet, $this->log, $this->dbh));
		$this->set("reponse_bonne_media_txt", Media::getMediaIdTitre($this->get("reponse_bonne_media"), $idProjet, $this->log, $this->dbh));
		$this->set("reponse_mauvaise_media_txt", Media::getMediaIdTitre($this->get("reponse_mauvaise_media"), $idProjet, $this->log, $this->dbh));
		$this->set("reponse_incomplete_media_txt", Media::getMediaIdTitre($this->get("reponse_incomplete_media"), $idProjet, $this->log, $this->dbh));
		
		// Défaut pour le type d'élément 1
		if ($this->get("type_elements1") == "") {
			$this->set("type_elements1", "texte");
		}
		
		// Défaut pour le type d'élément 2
		if ($this->get("type_elements2") == "") {
			$this->set("type_elements2", "texte");
		}
		
		$this->log->debug("Item::preparerValeursApresChargement() Fin");
	}	
	

	/**
	 * 
	 * Charger les informations pour ce questionnaire seulement 
	 * @param String idQuestionnaire
	 * @param String idProjet
	 * 
	 */
	public function getValeursPourQuestionnaire($idQuest, $idProjet) {
		
		$idItem = $this->get("id_item");
		
		$this->log->debug("Item::getValeursPourQuestionnaire() Début idItem : '$idItem' idQuest : '$idQuest' idProjet : '$idProjet'");
	
		try {
			$sql = "select ponderation_quest, demarrer_media_quest, afficher_solution_quest, ordre_presentation_quest, type_etiquettes_quest, type_bonnesreponses_quest, orientation_elements_quest, 
						   couleur_element_quest, couleur_element_associe_quest, afficher_masque_quest, points_retranches_quest, majmin_quest, ponctuation_quest 
					from tquestionnaire_item 
					where id_questionnaire = ? and id_item = ? and id_projet = ? and statut != 0";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idQuest, $idItem, $idProjet));
			
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() == 0) {
				$this->log->info("Item::getValeursPourQuestionnaire() Aucune valeur trouvée pour idItem : '$idItem' idQuest : '$idQuest' idProjet : '$idProjet'");
			}
			
			// Vérifier qu'un seul item est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "Item::getValeursPourQuestionnaire() La recherche pour '$idItem' idQuest : '$idQuest' idProjet : '$idProjet' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour l'item
				$result = $sth->fetchAll();
			
			    foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->set($cle,$row[$cle]);
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
			}
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::getValeursPourQuestionnaire() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		$this->log->debug("Item::getValeursPourQuestionnaire() Fin");
	}

	/**
	 * 
	 * Préparer affichage panneau paramètres  
	 * 
	 */
	public function preparerValeursPanneauParametres() {
		
		$this->log->debug("Item::preparerValeursPanneauParametres() Début");
		
		// Préparer le panneau "valeurs pour ce questionnaire seulement - paramètres"
		$nbRemplacement = 0;
		if ($this->get("ponderation_quest") == "") {
			$this->set("ponderation_quest", $this->get("ponderation"));
			$nbRemplacement++;
		}
		if ($this->get("demarrer_media_quest") == "") {
			$this->set("demarrer_media_quest", $this->get("demarrer_media"));
			$nbRemplacement++;
		}
		if ($this->get("afficher_solution_quest") == "") {
			$this->set("afficher_solution_quest", $this->get("afficher_solution"));
			$nbRemplacement++;
		}
		if ($this->get("ordre_presentation_quest") == "") {
			$this->set("ordre_presentation_quest", $this->get("ordre_presentation"));
			$nbRemplacement++;
		}
		if ($this->get("type_etiquettes_quest") == "") {
			$this->set("type_etiquettes_quest", $this->get("type_etiquettes"));
			$nbRemplacement++;
		}

		if ($this->get("type_bonnesreponses_quest") == "") {
			$this->set("type_bonnesreponses_quest", $this->get("type_bonnesreponses"));
			$nbRemplacement++;
		}
		
		if ($this->get("orientation_elements_quest") == "") {
			$this->set("orientation_elements_quest", $this->get("orientation_elements"));
			$nbRemplacement++;
		}

		if ($this->get("couleur_element_quest") == "") {
			$this->set("couleur_element_quest", $this->get("couleur_element"));
			$nbRemplacement++;
		}

		if ($this->get("couleur_element_associe_quest") == "") {
			$this->set("couleur_element_associe_quest", $this->get("couleur_element_associe"));
			$nbRemplacement++;
		}

		if ($this->get("afficher_masque_quest") == "") {
			$this->set("afficher_masque_quest", $this->get("afficher_masque"));
			$nbRemplacement++;
		}		
		
		if ($this->get("points_retranches_quest") == "") {
			$this->set("points_retranches_quest", $this->get("points_retranches"));
			$nbRemplacement++;
		}
		
		if ($this->get("majmin_quest") == "") {
			$this->set("majmin_quest", $this->get("majmin"));
			$nbRemplacement++;
		}
		
		if ($this->get("ponctuation_quest") == "") {
			$this->set("ponctuation_quest", $this->get("ponctuation"));
			$nbRemplacement++;
		}
		
		// Forcer l'ouverture du panneau si au moins une valeur est spécifiée
		$ouvrirPanneauParametres = 0;
		if ($nbRemplacement < 13) {
			$ouvrirPanneauParametres = 1;	
		}
		$this->set("ouvrirPanneauParametres", $ouvrirPanneauParametres);
		
		$this->log->debug("Item::preparerValeursPanneauParametres() Fin");
		
	}
	
	/**
	 * 
	 * Préparer affichage panneau messages  
	 * 
	 */
	public function preparerValeursPanneauMessages() {
		
		$this->log->debug("Item::preparerValeursPanneauMessages() Début");
		
		// Obtenir la langue
		$langueItem = $this->getLangueApercuObj();
		
		// Préparer le panneau "valeurs pour ce questionnaire seulement - messages"
		$nbRemplacement = 0;
		if ($this->get("reponse_bonne_message") == "") {
			$this->set("reponse_bonne_message", $langueItem->get("message_bonnereponse"));
			$nbRemplacement++;
		}
		if ($this->get("reponse_mauvaise_message") == "") {
			$this->set("reponse_mauvaise_message", $langueItem->get("message_mauvaisereponse"));
			$nbRemplacement++;
		}
		if ($this->get("reponse_incomplete_message") == "") {
			$this->set("reponse_incomplete_message", $langueItem->get("message_reponseincomplete"));
			$nbRemplacement++;
		}
		if ($this->get("reponse_bonne_media") == 0) {
			$this->set("reponse_bonne_media", $langueItem->get("media_bonnereponse"));
			$nbRemplacement++;
		}
		if ($this->get("reponse_mauvaise_media") == 0) {
			$this->set("reponse_mauvaise_media", $langueItem->get("media_mauvaisereponse"));
			$nbRemplacement++;
		}
		if ($this->get("reponse_incomplete_media") == 0) {
			$this->set("reponse_incomplete_media", $langueItem->get("media_reponseincomplete"));
			$nbRemplacement++;
		}	

		// Obtenir les noms de fichiers
		$this->set("reponse_bonne_media_txt", Media::getMediaIdTitre($this->get("reponse_bonne_media"), $this->get("id_projet"), $this->log, $this->dbh));
		$this->set("reponse_mauvaise_media_txt", Media::getMediaIdTitre($this->get("reponse_mauvaise_media"), $this->get("id_projet"), $this->log, $this->dbh));
		$this->set("reponse_incomplete_media_txt", Media::getMediaIdTitre($this->get("reponse_incomplete_media"), $this->get("id_projet"), $this->log, $this->dbh));
		
		// Forcer l'ouverture du panneau si au moins une valeur est spécifiée
		$ouvrirPanneauMessages = 0;
		if ($nbRemplacement < 6) {
			$ouvrirPanneauMessages = 1;	
		}
		$this->set("ouvrirPanneauMessages", $ouvrirPanneauMessages);
		
		$this->log->debug("Item::preparerValeursPanneauMessages() Fin");
		
	}	
	
	/**
	 * 
	 * Changer le type d'éléments 1
	 * 
	 */
	public function changerTypeElements1() {

		$this->log->debug("Item::changerTypeElements1() Début");
		
		// Vider les champs lors du changement de type 
		for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			$this->set("reponse_" . $i . "_element","");
		}
		
		// Vider les champs lors du changement de type
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
			$this->set("classeur_" . $i . "_titre","");
		}
		
		$this->log->debug("Item::changerTypeElements1() Fin");
	}
	
	/**
	 * 
	 * Changer le type d'éléments 2
	 * 
	 */
	public function changerTypeElements2() {

		$this->log->debug("Item::changerTypeElements2() Début");
		
		// Vider les champs lors du changement de type 
		for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			$this->set("reponse_" . $i . "_element_associe","");
		}
		
		
		$this->log->debug("Item::changerTypeElements2() Fin");
	}
	
	
	/**
	 * 
	 * Obtenir la langue de l'item
	 * 
	 */
	public function getLangueApercuObj() {

		$this->log->debug("Item::getLangueApercuObj() Début");

		$langueItem = new Langue($this->log, $this->dbh);		
		
		// Obtenir l'id de la langue pour apercu
		$idLangueItem = 0;
		$idLangueItem = $this->get("id_langue_questionnaire");
		
		if ($idLangueItem == "" || $idLangueItem == "0") {
			// Obtenir l'id de la langue pour apercu 
			$idLangueItem = $this->get("apercu_langue");
		}

		// Instancier la langue
		$langueItem->getLangueParId($idLangueItem, $this->get("id_projet"));
	
		$this->log->debug("Item::getLangueApercuObj() Fin");
		return $langueItem;
	}
	
	/**
	 * 
	 * Obtenir la liste des items pour l'usager
	 * @param String idProjet
	 * 
	 */
	public function getListeItems($idProjet) {

		$this->log->debug("Item::getListeItem() Début");
		$listeItems = array(); 
				
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		// Obtenir le type de question (filtre)
		$filtreTypeItem = $this->getFiltreTypeItem();
		
		try {
			// SQL de base
			$sql = "select id_item, titre, type_item, 
					case 
						when type_item = 1 then ?
						when type_item = 2 then ?
						when type_item = 3 then ?
						when type_item = 4 then ?
						when type_item = 5 then ?
						when type_item = 6 then ?
						when type_item = 7 then ?
						when type_item = 8 then ?
						when type_item = 9 then ?
						when type_item = 10 then ?
						when type_item = 11 then ?
						when type_item = 12 then ?
						when type_item = 13 then ?
						when type_item = 14 then ?
					end				
					as type, 
					
					date_modification, liens, statut 
					from titem
					where id_projet = ? 
					and statut != 0
					and type_item != 15 
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array(TXT_ASSOCIATIONS, 
										TXT_CHOIX_MULTIPLES,
										TXT_CLASSEMENT,
										TXT_DAMIER,
										TXT_DEVELOPPEMENT,
										TXT_DICTEE,
										TXT_MARQUAGE,
										TXT_MISE_EN_ORDRE,
										TXT_REPONSE_BREVE,
										TXT_REPONSES_MULTIPLES,
										TXT_TEXTE_LACUNAIRE,
										TXT_VRAI_OU_FAUX,
										TXT_ZONES_A_IDENTIFIER,
										TXT_PAGE,
										$idProjet));
										
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun item trouvé pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des items
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour le type d'item
					if ($filtreTypeItem != "" && $row['type_item'] != $filtreTypeItem) {
						continue;
					}				
					
					$id = $row['id_item'];
					array_push($listeItems, $id);
				}
			}

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::getListeItems() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_items", $listeItems);
		
		// Terminé
		$this->log->debug("Item::getListeItem() Fin");
		return $listeItems;		
	}	

	
	/**
	 * 
	 * Effectuer une recherche dans les items seulement
	 * @param String chaine
	 * @param String idProjet
	 * @param String statut
	 * @param Log log
	 * @param PDO dbh
	 * 
	 */
	public function rechercheItems($chaine, $idProjet, $statut, $log, $dbh) {
	
		$log->debug("Item::rechercheItems() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeItems = array(); 

		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		// Obtenir le type de question (filtre)
		$filtreTypeItem = $this->getFiltreTypeItem();
		
		// Prefixer le champ tri, sauf pour "type"
		if (substr($tri, 0, 4) != "type") {
			$tri = "titem." . $tri;
		}

		try {
			// SQL de recherche
			$sql = "select titem_index.id_item, titem.titre, titem.type_item, 
					case 
						when type_item = 1 then ?
						when type_item = 2 then ?
						when type_item = 3 then ?
						when type_item = 4 then ?
						when type_item = 5 then ?
						when type_item = 6 then ?
						when type_item = 7 then ?
						when type_item = 8 then ?
						when type_item = 9 then ?
						when type_item = 10 then ?
						when type_item = 11 then ?
						when type_item = 12 then ?
						when type_item = 13 then ?
						when type_item = 14 then ?
					end				
					as type, 
					titem.date_modification, titem.liens, titem.statut	
					from titem_index, titem
					left join tcategorie_index on tcategorie_index.id_categorie = titem.id_categorie and tcategorie_index.id_projet = titem.id_projet
					where titem_index.id_projet = ? 
					and (titem_index.texte like ? or tcategorie_index.texte like ?)
					and titem.id_item = titem_index.id_item
					and titem.id_projet = titem_index.id_projet
					and titem.statut = ?
					and titem.type_item != 15
					order by $tri";
			
			$sth = $dbh->prepare($sql);
			$rows = $sth->execute(array(TXT_ASSOCIATIONS, 
										TXT_CHOIX_MULTIPLES,
										TXT_CLASSEMENT,
										TXT_DAMIER,
										TXT_DEVELOPPEMENT,
										TXT_DICTEE,
										TXT_MARQUAGE,
										TXT_MISE_EN_ORDRE,
										TXT_REPONSE_BREVE,
										TXT_REPONSES_MULTIPLES,
										TXT_TEXTE_LACUNAIRE,
										TXT_VRAI_OU_FAUX,
										TXT_ZONES_A_IDENTIFIER,
										TXT_PAGE,
										$idProjet, 
										$chaine,
										$chaine,
										$statut
										));
			
			// Vérifier qu'on a trouvé au moins un item	
			if ($sth->rowCount() == 0) {
				$log->info("Item::recherche() Aucun item trouvé pour l'usager '$idProjet' et la recherche '$chaine'");
			}
			else {
				// Récupérer les ids des items
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					
					// Appliquer le filter pour le type d'item
					if ($filtreTypeItem != "" && $row['type_item'] != $filtreTypeItem) {
						continue;
					}			
					
					$id = $row['id_item'];
					array_push($listeItems, $id);
				}
			}
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::rechercheItems() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_items", $listeItems);
		
		$log->debug("Item::rechercheItems() Fin");		
		return $listeItems;
	}
	
	/**
	 *
	 * Obtenir la liste des items pour suppression
	 * @param String idProjet
	 *
	 */
	public function getListeIdItemsDuProjet($idProjet) {
	
		$this->log->debug("Media::getListeIdItemsDuProjet() Début");
		$listeItems = array();
	
		try {
			$sql = "select id_item from titem where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
	
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::getListeIdItemsDuProjet() Aucun item trouvé pour le projet '$idProjet'");
			}
			else {
				// Récupérer les ids des items
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					$id = $row['id_item'];
					array_push($listeItems, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeIdItemsDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Media::getListeIdItemsDuProjet() Fin");
		return $listeItems;
	}	
	
	
	/**
	 * 
	 * Effectuer une recherche dans les items seulement et retourner les ids des questionnaires contenant ces items
	 * @param String chaine
	 * @param String idProjet
	 * @param Log log
	 * @param PDO dbh
	 * 
	 */
	public static function rechercheQuestionnairesContenantItems($chaine, $idProjet, $log, $dbh) {
	
		$log->debug("Item::rechercheQuestionnairesContenantItems() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeItems = array(); 
				
		try {
			// SQL de recherche
			$sql = "select id_questionnaire
					from titem_index, tquestionnaire_item, titem
					left join tcategorie_index on tcategorie_index.id_categorie = titem.id_categorie
					where titem.id_item = titem_index.id_item 
					and titem_index.id_projet = ?
					and (titem_index.texte like ? or tcategorie_index.texte like ?)
					and tquestionnaire_item.id_item = titem_index.id_item
					and tquestionnaire_item.id_projet = titem_index.id_projet
					and tquestionnaire_item.statut = 1
					";
			
			$sth = $dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $chaine, $chaine));
			
			// Vérifier qu'on a trouvé au moins un item	
			if ($sth->rowCount() == 0) {
				$log->info("Item::rechercheQuestionnairesContenantItems() Aucun item trouvé pour l'usager '$idProjet' et la recherche '$chaine'");
			}
			else {
				// Récupérer les ids des items
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_questionnaire'];
					array_push($listeItems, $id);
				}
			}
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::rechercheQuestionnairesContenantItems() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
		}
		
		$log->debug("Item::rechercheQuestionnairesContenantItems() Fin");		
		return $listeItems;
	}	
		
	
	/**
	 * 
	 * Obtenir le filtre pour le type d'item
	 * 
	 */
	public function getFiltreTypeItem() {
		
		$this->log->debug("Item::getFiltreTypeItem() Début");
		
		$session = new Session();
		
		// Vérifier le mode (fenêtre ou plein écran)
		$mode = Web::getMode();
		
		// Vérifier si un filtre est spécifié dans la session
		$filtreTypeItem = $session->get("pref_filtre_type_item");
		
		// Vérifier si un filtre est passé en paramètre
		$filtreTypeItemParam = Web::getParam("filtre_type_item");
		
		// Déterminer si on utilise la valeur passé en paramètre
		if ($filtreTypeItemParam != "") {
		
			// Si l'utilisateur veut voir tous les types de questions enlever le filtre
			if ($filtreTypeItemParam == "tous") {
				$session->delete("pref_filtre_type_item");
				$filtreTypeItem = "";
			} else {
			
				// Obtenir la liste des types de question
				$listeTypesItems = $this->getListeTypesItems();
	
				// Vérifier si la collection demandée est disponible pour l'utilisateur
				if ($listeTypesItems[$filtreTypeItemParam] != "") {			
	
					// Stocker le tri dans la session
					$session->set("pref_filtre_type_item", $filtreTypeItemParam);
					$filtreTypeItem = $filtreTypeItemParam;
				}
			}
		}
		
		$this->log->debug("Item::getFiltreTypeItem() Fin");
		
		return $filtreTypeItem;
	}	
		
	
	/**
	 * 
	 * Préparer sauvegarde
	 * 
	 */
	protected function preparerSauvegarde() {
	
		$this->log->debug("Item::preparerSauvegarde() Début");

		// Traitement de media_image
		$pos = strpos($this->get("media_image"), '-');
		if ($pos > 0) {
			$this->set("media_image", substr($this->get("media_image"), 1, $pos - 1));
		}
		
		// Traitement de media_son
		$pos = strpos($this->get("media_son"), '-');
		if ($pos > 0) {
			$this->set("media_son", substr($this->get("media_son"), 1, $pos - 1));
		}

		// Traitement de media_video
		$pos = strpos($this->get("media_video"), '-');
		if ($pos > 0) {
			$this->set("media_video", substr($this->get("media_video"), 1, $pos - 1));
		}
			
		// Traitement de pondération (remplace virgule par point)
		$this->set("ponderation", str_replace(",", ".", $this->get("ponderation")) );
		$this->set("ponderation_quest", str_replace(",", ".", $this->get("ponderation_quest")) );
		
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVEL_ITEM);
		}		
		
		$this->log->debug("Item::preparerSauvegarde() Fin");
	}
		
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'un item
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Item::ajouter() Début");

		// Obtenir le prochain id item
		$projet = new Projet($this->log, $this->dbh);
		$projet->getProjetParId($this->get("id_projet"));
		$idItem = $projet->genererIdItem();
		$this->set("id_item", $idItem);		
		
		// Préparer sauvegarde
		$this->preparerSauvegarde();
		
		// Préparer ajout
		try {
			$stmt = $this->dbh->prepare("insert into titem (id_item, id_projet, titre, enonce, info_comp1_titre, info_comp1_texte, info_comp2_titre, info_comp2_texte, 
															media_titre, media_texte, media_image, media_son, media_video, solution, retroaction_positive, retroaction_negative, 
															retroaction_reponse_imprevue, couleur_element, couleur_element_associe, afficher_masque, type_item, id_categorie, suivi, ponderation, ordre_presentation, 
															type_etiquettes, type_champs, afficher_solution, points_retranches, majmin, ponctuation, type_elements1, type_elements2, 
															demarrer_media, reponse_bonne_message, reponse_bonne_media, reponse_mauvaise_message, 
															reponse_mauvaise_media, reponse_incomplete_message, reponse_incomplete_media, remarque, liens, type_bonnesreponses, orientation_elements, 
															statut, image, couleur_zones, date_creation, date_modification) 
										 values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(),now() )");
			
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_item'), 
								 $this->get('id_projet'), 
								 $this->get('titre'),
								 $this->get('enonce'),
								 $this->get('info_comp1_titre'),
								 $this->get('info_comp1_texte'),
								 $this->get('info_comp2_titre'),
								 $this->get('info_comp2_texte'),
								 $this->get('media_titre'),
								 $this->get('media_texte'),
								 (int)$this->get('media_image'),
								 (int)$this->get('media_son'),
								 (int)$this->get('media_video'),
								 $this->get('solution'),
								 $this->get('retroaction_positive'),
								 $this->get('retroaction_negative'),
								 $this->get('retroaction_reponse_imprevue'),
								 $this->get('couleur_element'),
								 $this->get('couleur_element_associe'),
								 $this->get('afficher_masque'),
								 (int)$this->get('type_item'),
								 (int)$this->get('id_categorie'),
								 $this->get('suivi'),
								 $this->get('ponderation'),
								 $this->get('ordre_presentation'),
								 $this->get('type_etiquettes'),
								 $this->get('type_champs'),
								 $this->get('afficher_solution'),
								 $this->get('points_retranches'),
								 $this->get('majmin'),
								 $this->get('ponctuation'),
								 $this->get('type_elements1'),
								 $this->get('type_elements2'),
								 $this->get('demarrer_media'),
								 $this->get('reponse_bonne_message'),
								 (int)$this->get('reponse_bonne_media'),
								 $this->get('reponse_mauvaise_message'),
								 (int)$this->get('reponse_mauvaise_media'),
								 $this->get('reponse_incomplete_message'),
								 (int)$this->get('reponse_incomplete_media'),
								 $this->get('remarque'),
								 (int)$this->get('liens'),
								 $this->get('type_bonnesreponses'),
								 $this->get('orientation_elements'),
								 (int)$this->get('statut'),
								 $this->get('image'),
								 $this->get('couleur_zones')
								 ));
			
			$this->log->debug("Item::ajouter() Nouveau item créé (id = '" . $this->get('id_item') . "')");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Ajouter lien entre questionnaire et item au besoin (contexte questionnaire)
		if ($this->get('id_questionnaire') != '') {
			$this->ajouterLienQuestionnaireItem();
		}

		// Mettre à jour le nombre de liens pour l'item
		$this->updateLiensItem();
		
		$this->log->debug("Item::ajouter() Fin");
		
		return;
	}
	

	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un item
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Item::enregistrer() Début");
		
		// Préparer sauvegarde
		$this->preparerSauvegarde();
		
		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update titem 
										 set titre=?,
										 	 enonce=?,
										 	 info_comp1_titre=?,
										 	 info_comp1_texte=?,
										 	 info_comp2_titre=?,
										 	 info_comp2_texte=?,
										 	 media_titre=?,
										 	 media_texte=?,
										 	 media_image=?,
										 	 media_son=?,
										 	 media_video=?,
										 	 solution=?,
										 	 retroaction_positive=?,
										 	 retroaction_negative=?,
											 retroaction_reponse_imprevue=?,
											 couleur_element=?,
											 couleur_element_associe=?,
											 afficher_masque=?,
										 	 type_item=?,
										 	 id_categorie=?,
										 	 suivi=?,
										 	 ponderation=?, 
										 	 ordre_presentation=?, 
										 	 type_etiquettes=?,
											 type_champs=?, 
										 	 afficher_solution=?, 
										 	 points_retranches=?,
										 	 majmin=?,
										 	 ponctuation=?,
										 	 type_elements1=?,
										 	 type_elements2=?,
											 demarrer_media=?,
											 reponse_bonne_message=?, 
											 reponse_bonne_media=?, 
											 reponse_mauvaise_message=?, 
											 reponse_mauvaise_media=?, 
											 reponse_incomplete_message=?, 
											 reponse_incomplete_media=?,
											 remarque=?,
											 liens=?,
											 type_bonnesreponses=?,
											 orientation_elements=?,
											 statut=?,
											 image=?,
											 couleur_zones=?,
								  		 	 date_modification=now()										
										 where id_item = ? 
										 and id_projet= ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('titre'),
									$this->get('enonce'),
									$this->get('info_comp1_titre'),
									$this->get('info_comp1_texte'),
									$this->get('info_comp2_titre'),
									$this->get('info_comp2_texte'),
									$this->get('media_titre'),
									$this->get('media_texte'),
									(int)$this->get('media_image'),
									(int)$this->get('media_son'),
									(int)$this->get('media_video'),
									$this->get('solution'),
									$this->get('retroaction_positive'),
									$this->get('retroaction_negative'),
									$this->get('retroaction_reponse_imprevue'),
									$this->get('couleur_element'),
									$this->get('couleur_element_associe'),
									$this->get('afficher_masque'),					
									$this->get('type_item'),
									$this->get('id_categorie'),
									$this->get('suivi'),
									$this->get('ponderation'),
									$this->get('ordre_presentation'),
									$this->get('type_etiquettes'),
									$this->get('type_champs'),
									$this->get('afficher_solution'),
									$this->get('points_retranches'),
								 	$this->get('majmin'),
								 	$this->get('ponctuation'),
								 	$this->get('type_elements1'),
								 	$this->get('type_elements2'),
									$this->get('demarrer_media'),
									$this->get('reponse_bonne_message'),
									$this->get('reponse_bonne_media'),
									$this->get('reponse_mauvaise_message'),
									$this->get('reponse_mauvaise_media'),
									$this->get('reponse_incomplete_message'),
									$this->get('reponse_incomplete_media'),
									$this->get('remarque'),
									$this->get('liens'),
									$this->get('type_bonnesreponses'),
									$this->get('orientation_elements'),
									$this->get('statut'),
									$this->get('image'),
									$this->get('couleur_zones'),
									$this->get('id_item'),
									$this->get('id_projet')
									) );
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		// Mettre à jouter l'information pour le lien entre l'item et le questionnaire
		$this->enregistrerLienQuestionnaireItem();
						
		// Mettre à jour le nombre de liens pour l'item
		$this->updateLiensItem();

		$this->log->debug("Item::enregistrer() Fin");		
		
		return;
	}	

	
	
	/**
	 * 
	 * Obtenir la liste des questionnaires qui utilisent l'item
	 *
	 */
	public function getListeQuestionnairesUtilisantItem() {	
	
		$this->log->debug("Item::getListeQuestionnairesUtilisantItem() Début");
		
		$listeQuestionnaires = array();
		
		try {
			// SQL de base
			$sql = "select tquestionnaire.id_questionnaire, tquestionnaire.titre
					from tquestionnaire_item, tquestionnaire
					where tquestionnaire_item.id_projet = ? 
					and tquestionnaire_item.id_item = ?
					and tquestionnaire.id_questionnaire = tquestionnaire_item.id_questionnaire
					and tquestionnaire.id_projet = tquestionnaire_item.id_projet
					and tquestionnaire.statut > 0";
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($this->get("id_projet"),
										$this->get("id_item")
										));
										
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Item::getListeQuestionnairesUtilisantItem() Aucun questionnaire trouvé pour l'usager '" . $this->get("id_projet") . "' et l'item '" . $this->get("id_item") . "'");
			}
			else {
				// Récupérer les ids de questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					
					$info = $row['titre'] . " (" . TXT_PREFIX_QUESTIONNAIRE . $row['id_questionnaire'] . ")";
					array_push($listeQuestionnaires, $info);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::getListeQuestionnairesUtilisantItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
	
		# Ajouter à l'item
		$this->set("liste_liens_questionnaires", $listeQuestionnaires);
		
		$this->log->debug("Item::getListeQuestionnairesUtilisantItem() Fin");
		
		return;
	}
	

	/**
	 * 
	 * Obtenir la liste des questionnaires qui utilisent l'item
	 *
	 */
	public function getListeIDQuestionnairesUtilisantItem() {	
	
		$this->log->debug("Item::getListeIDQuestionnairesUtilisantItem() Début");
		
		$listeQuestionnaires = array();
		
		try {
			// SQL de base
			$sql = "select tquestionnaire.id_questionnaire, tquestionnaire.titre
					from tquestionnaire_item, tquestionnaire
					where tquestionnaire_item.id_projet = ? 
					and tquestionnaire_item.id_item = ?
					and tquestionnaire.id_questionnaire = tquestionnaire_item.id_questionnaire
					and tquestionnaire.id_projet = tquestionnaire_item.id_projet
					and tquestionnaire.statut > 0";
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($this->get("id_projet"),
										$this->get("id_item")
										));
										
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Item::getListeIDQuestionnairesUtilisantItem() Aucun questionnaire trouvé pour l'usager '" . $this->get("id_projet") . "' et l'item '" . $this->get("id_item") . "'");
			}
			else {
				// Récupérer les ids de questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					array_push($listeQuestionnaires, $row['id_questionnaire']);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::getListeIDQuestionnairesUtilisantItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
		
		$this->log->debug("Item::getListeIDQuestionnairesUtilisantItem() Fin");
		
		return $listeQuestionnaires;
	}	

	/**
	 * 
	 * Dupliquer l'item
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Item::dupliquer() Début");
	
		// Retirer l'id initial
		$idQuestSource = $this->get("id_item");
		$this->set("id_item", "");
		
		// Ajouter un astérisque devant le titre
		$titre = "*" . $this->get("titre");
		$this->set("titre", $titre);
		
		// Ajouter le nouvel item
		$this->ajouter();
		
		// Analyser les éléments
		$this->analyserElements();
		
		$this->log->debug("Item::dupliquer() Fin");
	}	
	
	
	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des items
	 */
	public function getTri() {
		
		$this->log->debug("Item::getTri() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("item_pref_tri_champ");
		$triSessionOrdre = $session->get("item_pref_tri_ordre");
		$this->log->debug("Item::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Item::getTri() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_item", "type", "titre", "id_categorie", "remarque", "date_modification", "suivi", "liens");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_item";
			} else {
				// Déterminer si on doit inverser le tri ou non
				if ($triSessionChamp == "" || $triSessionChamp != $triParamChamp) {
					// Aucune valeur en session, on tri selon le champ demandé en mode croissant
					$triParamOrdre .= "asc";
				} else {
						// Inverser l'ordre de tri
						if ($triSessionOrdre == "asc") {
							$triParamOrdre = "desc";
						} else {
							$triParamOrdre = "asc";
						}
				}
			}
		}
		
		// Si aucun tri spécifié, utilisé celui de la session
		if ($triParamChamp == "") {
			$triParamChamp = $triSessionChamp;
			$triParamOrdre = $triSessionOrdre;
		}
		
		// Si aucun tri en session, utilisé celui par défaut
		if ($triParamChamp == "") {
			$triParamChamp = "id_item";
			$triParamOrdre = "asc";			
		}
		
		// Pour la corbeille, échanger id_element par id_item
		if ($triParamChamp == "id_element") {
			$triParamChamp = "id_item";
		}
		
		// Stocker le tri dans la session
		$session->set("item_pref_tri_champ", $triParamChamp);
		$session->set("item_pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Item::getTri() Fin");
		
		return $triParamChamp . " " . $triParamOrdre;
	}	

	
	/**
	 * 
	 * Obtenir l'id de l'item à partir de la page demandée
	 * @param String page demandée 
	 *
	 */
	public function getIdItemParPage($page) {

		$this->log->debug("Item::getIdItemParPage() Début");

		$idItem = "";
		$pageCour = $page - 1;
		
		// Obtenir la position de l'item dans les résultats
		$session = new Session();
		$listeItems = $session->get("liste_items");
	
		// Obtenir le nombre total d'items
		$pageTotal = count($listeItems);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er item trouvé
			$idItem = $listeItems[0];		
		} else {
			$idItem = $listeItems[$pageCour];
		}
		
		return $idItem;
	}
			
	
	/**
	 * 
	 * Obtenir la liste des types d'items
	 */
	public function getListeTypesItems() {

		$this->log->debug("Item::getListeTypesItems() Début");
		 		
		$listeTypesItems = array(	1 => TXT_ASSOCIATIONS, 
									2 => TXT_CHOIX_MULTIPLES,
									3 => TXT_CLASSEMENT,
									4 => TXT_DAMIER,
									5 => TXT_DEVELOPPEMENT,
									6 => TXT_DICTEE,
									7 => TXT_MARQUAGE,
									8 => TXT_MISE_EN_ORDRE,
									9 => TXT_REPONSE_BREVE,
									10 => TXT_REPONSES_MULTIPLES,
									11 => TXT_TEXTE_LACUNAIRE,
									12 => TXT_VRAI_OU_FAUX,
									13 => TXT_ZONES_A_IDENTIFIER,
									14 => TXT_PAGE
								);	
								
		$this->log->debug("Item::getListeTypesItems() Fin");
		
		return $listeTypesItems;
	}

	/**
	 * 
	 * Enregistrer une nouvelle catégorie au besoin
	 */
	public function enregistrerNouvelleCategorie() {
		
		$this->log->debug("Item::enregistrerNouvelleCategorie() Début");
		
		$titre = $this->get("categorie_ajouter");
		
		if ($titre != '') {

			// Préparer la nouvelle catégorie
			$categorie = new Categorie($this->log, $this->dbh);
			$categorie->set("titre", $titre);
			$categorie->set("id_projet", $this->get("id_projet"));
			$categorie->set("remarque", "");
			
			// Ajouter la catégorie
			$categorie->ajouter();
			
			// Régler l'id de la catégorie de l'item sur l'id de la nouvelle catégorie
			$this->set("id_categorie", $categorie->get("id_categorie"));
		}
		
		$this->log->debug("Item::enregistrerNouvelleCategorie() Fin");
		
	}	
	
	
	/**
	 * 
	 * Obtenir la liste des types d'items trié par type
	 */
	public function getListeTypesItemsTrieParType() {

		$this->log->debug("Item::getListeTypesItemsTrieParType() Début");

		$listeTypesItems = $this->getListeTypesItems();
		asort($listeTypesItems);
		
	 	$this->log->debug("Item::getListeTypesItemsTrieParType() Fin");

	 	return $listeTypesItems;
	 	
	}	
	
	/**
	 * 
	 * Préparer les données pour affichage sur le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Item::preparerAffichage() Début");
		
		// Select
		$this->set('ordre_presentation_' . $this->get('ordre_presentation'), HTML_SELECTED);
		$this->set('type_etiquettes_' . $this->get('type_etiquettes'), HTML_SELECTED);
		$this->set('type_champs_' . $this->get('type_champs'), HTML_SELECTED);
		$this->set('type_bonnesreponses_' . $this->get('type_bonnesreponses'), HTML_CHECKED);
		$this->set('orientation_elements_' . $this->get('orientation_elements'), HTML_SELECTED);
		$this->set('afficher_solution_' . $this->get('afficher_solution'), HTML_SELECTED);
		$this->set('afficher_masque_' . $this->get('afficher_masque'), HTML_SELECTED);
		$this->set('demarrer_media_' . $this->get('demarrer_media'), HTML_SELECTED);
		$this->set('majmin_' . $this->get('majmin'), HTML_CHECKED);
		$this->set('ponctuation_' . $this->get('ponctuation'), HTML_CHECKED);
		
		$this->set('ordre_presentation_quest_' . $this->get('ordre_presentation_quest'), HTML_SELECTED);
		$this->set('type_etiquettes_quest_' . $this->get('type_etiquettes_quest'), HTML_SELECTED);
		$this->set('type_bonnesreponses_quest_' . $this->get('type_bonnesreponses_quest'), HTML_CHECKED);
		$this->set('orientation_elements_quest_' . $this->get('orientation_elements_quest'), HTML_SELECTED);
		$this->set('afficher_solution_quest_' . $this->get('afficher_solution_quest'), HTML_SELECTED);
		$this->set('afficher_masque_quest_' . $this->get('afficher_masque_quest'), HTML_SELECTED);
		$this->set('demarrer_media_quest_' . $this->get('demarrer_media_quest'), HTML_SELECTED);
		$this->set('majmin_quest_' . $this->get('majmin_quest'), HTML_CHECKED);
		$this->set('ponctuation_quest_' . $this->get('ponctuation_quest'), HTML_CHECKED);
		
		// Obtenir la position du questionnaire dans les résultats
		$session = new Session();
		$listeItems = $session->get("liste_items");
		if ( is_array($listeItems) ) { 
			$pageCour = array_search($this->get("id_item"), $listeItems);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total d'items
		$pageTotal = count($listeItems);
		
		// Obtenir la page suivante
		$pageSuiv = $pageCour + 1;
		if ($pageSuiv > $pageTotal) {
			$pageSuiv = $pageTotal;
		}
		
		// Obtenir la page précédente
		$pagePrec = $pageCour - 1;
		if ($pagePrec < 1) {
			$pagePrec = 1;
		}
		
		$this->set("pages_total", $pageTotal);
		$this->set("page_suivante", $pageSuiv );
		$this->set("page_precedente", $pagePrec );
		$this->set("page_courante", $pageCour );
			
		$this->log->debug("Item::preparerAffichage() Fin");
	}

	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Item::preparerAffichageListe() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("item_pref_tri_champ");
		$tri_ordre = $session->get("item_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Item::preparerAffichageListe() Fin");		
		
		return;
	}		

	/**
	 * 
	 * Préparer les données pour publication 
	 * @param string Répertoire Destination
	 * @param Questionnaire Questionnaire courant
	 *
	 */
	public function preparerPublication($repertoireDestination, $quest) {

		$this->log->debug("Item::preparerPublication() Début");

		// --------------------------------------------------------
		// Texte médias en entête
		// --------------------------------------------------------
		$this->set("media_titre_texte", Publication::preparerChampTitreTexte($this->get("media_titre"), $this->get("media_texte"), $this->log)); 
		
		// --------------------------------------------------------
		// Fichier image - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierImage = "";
		if ($this->get("media_image") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierImage = $media->getNomFichierMedia($this->get("media_image"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
			
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_image_fichier_source", $source);
		}
		$this->set("media_image_fichier", $fichierImage);

		// --------------------------------------------------------
		// Fichier son - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierSon = "";
		if ($this->get("media_son") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierSon = $media->getNomFichierMedia($this->get("media_son"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);

			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_son_fichier_source", $source);
			
		}
		$this->set("media_son_fichier", $fichierSon);

		// --------------------------------------------------------
		// Fichier video - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierVideo = "";
		if ($this->get("media_video") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierVideo = $media->getNomFichierMedia($this->get("media_video"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
			
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_video_fichier_source", $source);
		}
		$this->set("media_video_fichier", $fichierVideo);
		
		// --------------------------------------------------------
		// Fichier image (zones à identifier)
		// --------------------------------------------------------
		$fichierImg = "";
		if ($this->get("image") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierImg = $media->getNomFichierMedia($this->get("image"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
				
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("image_fichier_source", $source);
		}
		$this->set("image_fichier", $fichierImg);		
		
		// --------------------------------------------------------
		// Démarrage auto des médias
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Démarrer média questionnaire : '" . $quest->get("demarrage_media_type") . "'");
		$this->log->debug("Item::preparerPublication() Démarrer média pour ce questionnaire seulement: '" .  $this->get("demarrer_media_quest") . "'");
		$this->log->debug("Item::preparerPublication() Démarrer média item : '" . $this->get("demarrer_media") . "'");
		
		// Le questionnaire a priorité
		$demarrageMedia = $quest->get("demarrage_media_type");
		
		// Sauf si l'utilisateur a choisi selon les paramètres de l'item ou on est dans un contexte sans questionnaire
		if ($quest->get("demarrage_media_type") == "item" || $demarrageMedia == "") {
			
			// Si une valeur existe pour ce questionnaire seulement, l'utilisé
			if ($this->get("demarrer_media_quest") != "") {
				$demarrageMedia = $this->get("demarrer_media_quest");
			} else {
				// Sinon par défaut on prend le réglage de l'item
				$demarrageMedia = $this->get("demarrer_media");
			}
		}
		$this->log->debug("Item::preparerPublication() Démarrer média final : '$demarrageMedia'");
		
		// --------------------------------------------------------
		// Démarrage auto du vidéo ou son
		// --------------------------------------------------------
		if ($demarrageMedia == "video") {
			$this->set("demarrage_video", "true");
			$this->set("demarrage_audio", "false");
		} 
		elseif ($demarrageMedia == "audio") {
			$this->set("demarrage_video", "false");
			$this->set("demarrage_audio", "true");
		}
		elseif ($demarrageMedia == "aucun") {
			$this->set("demarrage_video", "false");
			$this->set("demarrage_audio", "false");
		}
		
		// --------------------------------------------------------
		// Pondération
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Pondération pour ce questionnaire seulement: '" .  $this->get("ponderation_quest") . "'");
		$this->log->debug("Item::preparerPublication() Pondération item : '" . $this->get("ponderation") . "'");
		
		if ($this->get("ponderation_quest") != "") {
			// Source : Pondération pour ce questionnaire seulement
			$ponderation = $this->get("ponderation_quest");
		} else {
			// Source : Pondération item
			$ponderation = $this->get("ponderation");
		}
		
		// Au besoin remplacer la , par un .
		$ponderation = str_replace(',', '.', $ponderation);
		$ponderation = floatval($ponderation);
		$this->set("ponderation_calculee", $ponderation);
		
		// --------------------------------------------------------
		// Type d'étiquettes
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Type étiquettes pour ce questionnaire seulement : '" . $this->get("type_etiquettes_quest") . "'");
		$this->log->debug("Item::preparerPublication() Type étiquettes pour cet item : '" . $this->get("type_etiquettes") . "'");
		
		if ($this->get("type_etiquettes_quest") != "") {
			// Source : Ce questionnaire seulement
			$this->set("type_etiquettes_calcule", $this->get("type_etiquettes_quest"));
		} else {
			// Source : Pondération item
			$this->set("type_etiquettes_calcule", $this->get("type_etiquettes"));
		}
		
		$this->log->debug("Item::preparerPublication() Type étiquette final : '" . $this->get("type_etiquettes_calcule") . "'\n");
		
		$typeEtiquettes = 0; // Aucune
		if ($this->get("type_etiquettes_calcule") == "alphabetique") {
			$typeEtiquettes = 1;
		} elseif  ($this->get("type_etiquettes_calcule") == "numerique") {
			$typeEtiquettes = 2;
		}
		$this->set("type_etiquettes_pub", $typeEtiquettes);		
		
		// --------------------------------------------------------
		// Type bonnes réponses
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Type bonnesreponses pour ce questionnaire seulement : '" . $this->get("type_bonnesreponses_quest") . "'");
		$this->log->debug("Item::preparerPublication() Type bonnesreponses pour cet item : '" . $this->get("type_bonnesreponses") . "'");
		
		if ($this->get("type_bonnesreponses_quest") != "") {
			// Source : Ce questionnaire seulement
			$this->set("type_bonnesreponses_calcule", $this->get("type_bonnesreponses_quest"));
		} else {
			// Source : Pondération item
			$this->set("type_bonnesreponses_calcule", $this->get("type_bonnesreponses"));
		}
		
		$this->log->debug("Item::preparerPublication() Bonnes reponses final : '" . $this->get("type_bonnesreponses_calcule") . "'\n");
		
		$typeBonnesReponses = "true"; // Toutes
		if ($this->get("type_bonnesreponses_calcule") == "une") {
			$typeBonnesReponses = "false";
		}

		$this->set("type_bonnesreponses_pub", $typeBonnesReponses);
		
		
		// --------------------------------------------------------
		// Afficher la solution
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Affichage de la solution questionnaire : '" .  $quest->get("affichage_resultats_type") . "'");
		$this->log->debug("Item::preparerPublication() Affichage de la solution pour ce questionnaire seulement: '" .  $this->get("afficher_solution_quest") . "'");
		$this->log->debug("Item::preparerPublication() Affichage de la solution item : '" . $this->get("afficher_solution") . "'");

		// Le questionnaire a priorité
		$affichageResultats = $quest->get("affichage_resultats_type");
		
		// Sauf si l'utilisateur a choisi selon les paramètres de l'item ou on est dans un contexte sans questionnaire
		if ($quest->get("affichage_resultats_type") == "item" || $affichageResultats == "") {
			
			// Si une valeur existe pour ce questionnaire seulement, l'utiliser
			if ($this->get("afficher_solution_quest") != "") {
				$affichageResultats = $this->get("afficher_solution_quest");
			} else {
				// Sinon par défaut on prend le réglage de l'item
				$affichageResultats = $this->get("afficher_solution");
			}
		}
		$this->log->debug("Item::preparerPublication() Affichage résultats final : '$affichageResultats'");
		if ($affichageResultats == "oui") {		
			$this->set("affichage_resultats", "true");
		} else {
			$this->set("affichage_resultats", "false");
		}

		// --------------------------------------------------------
		// Points retranchés
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Points retranchés pour ce questionnaire seulement: '" .  $this->get("points_retranches_quest") . "'");
		$this->log->debug("Item::preparerPublication() Points retranchés item : '" . $this->get("points_retranches") . "'");

		$pointsRetranches = "";
		
		// Si une valeur existe pour ce questionnaire seulement, l'utiliser
		if ($this->get("points_retranches_quest") != "") {
			$pointsRetranches = $this->get("points_retranches_quest");
		} else {
			// Sinon par défaut on prend le réglage de l'item
			$pointsRetranches = $this->get("points_retranches");
		}
		
		// Au besoin remplacer la , par un .
		$pointsRetranches = str_replace(',', '.', $pointsRetranches);
		
		// Valeur par défaut
		if ($pointsRetranches == "") {
			$pointsRetranches = "0";
		} 
		
		$this->log->debug("Item::preparerPublication() Points retranchés final : '$pointsRetranches'");
		$this->set("points_retranches_pub", $pointsRetranches);

		// --------------------------------------------------------
		// Correction... majmin
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Correction majmin pour ce questionnaire seulement: '" .  $this->get("majmin_quest") . "'");
		$this->log->debug("Item::preparerPublication() Correction majmin item : '" . $this->get("majmin") . "'");

		$majmin = "";
		
		// Si une valeur existe pour ce questionnaire seulement, l'utiliser
		if ($this->get("majmin_quest") != "" && $quest->get("id_questionnaire") != "") {
			$majmin = $this->get("majmin_quest");
		} else {
			// Sinon par défaut on prend le réglage de l'item
			$majmin = $this->get("majmin");
		}

		$this->log->debug("Item::preparerPublication() Correction majmin final : '$majmin'");
		
		$majminPub = "false";
		if ($majmin == "1") {
			$majminPub = "true";
		}
		
		$this->set("majmin_pub", $majminPub);

		// --------------------------------------------------------
		// Correction...  ponctuation
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Correction ponctuation pour ce questionnaire seulement: '" .  $this->get("ponctuation_quest") . "'");
		$this->log->debug("Item::preparerPublication() Correction ponctuation item : '" . $this->get("ponctuation") . "'");

		$ponctuation = "";
		
		// Si une valeur existe pour ce questionnaire seulement, l'utiliser
		if ($this->get("ponctuation_quest") != "" && $quest->get("id_questionnaire") != "") {
			$ponctuation = $this->get("ponctuation_quest");
		} else {
			// Sinon par défaut on prend le réglage de l'item
			$ponctuation = $this->get("ponctuation");
		}

		$ponctuationPub = "false";
		if ($ponctuation == "1") {
			$ponctuationPub = "true";	
		}
		
		$this->log->debug("Item::preparerPublication() Correction ponctuation final : $ponctuation");
		$this->set("ponctuation_pub", $ponctuationPub);


		// --------------------------------------------------------
		// Couleur zones à identifier
		// --------------------------------------------------------
		$this->log->debug("Item::preparerPublication() Couleur zones à identifier item : '" . $this->get("couleur_zones") . "'");
		
		// Par défaut on prend le réglage de l'item
		$couleurZones = $this->get("couleur_zones");
		
		// Vérifier si la couleur par défaut doit être utilisée
		if ($couleurZones == "") {
			$couleurZones = COULEUR_ZONES_DEFAUT;
		}
		
		$this->log->debug("Item::preparerPublication() couleurZones final : $couleurZones");
		$this->set("couleur_zones_pub", $couleurZones);
		
				
		// --------------------------------------------------------
		// Réponses
		// --------------------------------------------------------
		
		// Régler le type d'éléments à images
		for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			$typeElements1 = "0"; // Défaut - texte
			$typeElements2 = "0"; // Défaut - texte
			
			// Elements
		 	if ($this->get("type_elements1") == "texte")  {
				
				// Traiter un élément de type texte
				$typeElements1 = "0"; // texte	
				
				// Déterminer la source de l'image
				$this->set("reponse_" . $i . "_element_source_image", -1);
				
			} elseif ($this->get("type_elements1") == "image") {
				
				// Traiter un élément de type image
				$typeElements1 = "1";
				
				// Obtenir l'id du média
				$idMedia = $this->get("reponse_" . $i . "_element");
	
				// Obtenir le nom du fichier 
				$media = new Media($this->log, $this->dbh);
				$fichierImage = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
								
				// Copier le fichier
				$media->copierFichierMedia($repertoireDestination);

				// Cas où l'image est absente
				if ($fichierImage == "") {
					$fichierImage = IMAGE_ABSENTE_DEFAUT_NOM_FICHIER;
				}
				
				// Préparer le nom du fichier dans les données
				$this->set("reponse_" . $i . "_element_fichier", $fichierImage);
				
				// Déterminer la source de l'image
				if ($media->get("source") != "web") {
					$this->set("reponse_" . $i . "_element_source_image", 1);
				} elseif ($media->get("source") == "web") {
					$this->set("reponse_" . $i . "_element_source_image", 2);
				}
			}
			
			// Elements associés
		 	if ($this->get("type_elements2") == "texte")  {
				
				// Traiter un élément de type texte
				$typeElements2 = "0"; // texte	
				
				// Déterminer la source de l'image
				$this->set("reponse_" . $i . "_element_associe_source_image", -1);
				
			} elseif ($this->get("type_elements2") == "image") {
				
				// Traiter un élément de type image
				$typeElements2 = "1";
				
				// Obtenir l'id du média
				$idMedia = $this->get("reponse_" . $i . "_element_associe");
	
				// Obtenir le nom du fichier 
				$media = new Media($this->log, $this->dbh);
				$fichierImage = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
								
				// Copier le fichier
				$media->copierFichierMedia($repertoireDestination);
	
				// Préparer le nom du fichier dans les données
				$this->set("reponse_" . $i . "_element_associe_fichier", $fichierImage);
				
				// Déterminer la source de l'image
				if ($media->get("source") != "web") {
					$this->set("reponse_" . $i . "_element_associe_source_image", 1);
				} elseif ($media->get("source") == "web") {
					$this->set("reponse_" . $i . "_element_associe_source_image", 2);
				}
			}			
			
			// Attributs communs
			$reponse = 'false';
			if ($this->getJS("reponse_" . $i . "_reponse") == 1 || $this->getJS("reponse_choix") == $i) {
	   			$reponse = 'true';
			}
			$this->set("reponse_" . $i . "_reponse_pub", $reponse);
			
			
			// Masque si l'item est un damier
			if ($this->get("type_item") == "4" && $this->get("reponse_" . $i . "_element") != "") {

				// Obtenir l'id du média
				$idMedia = $this->get("reponse_" . $i . "_masque");
				
				// Traiter un média régulier
				if ($idMedia > 0) {
				
					// Obtenir le nom du fichier
					$media = new Media($this->log, $this->dbh);
					$fichierImage = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
					
					// Copier le fichier
					$media->copierFichierMedia($repertoireDestination);
					
					// Préparer le nom du fichier dans les données
					$this->set("reponse_" . $i . "_masque_fichier", $fichierImage);
					
					// Déterminer la source de l'image
					if ($media->get("source") != "web") {
						$this->set("reponse_" . $i . "_masque_source_image", 1);
					} elseif ($media->get("source") == "web") {
						$this->set("reponse_" . $i . "_masque_source_image", 2);
					}
				} else {
					
					// Fichier par défaut
					$this->set("reponse_" . $i . "_masque_fichier", DAMIER_IMAGE_DEFAUT);
					
					// Source locale
					$this->set("reponse_" . $i . "_masque_source_image", 1);
					
					// Copier le fichier
					$source = IMAGE_PAIR_FICHIER;
					$dest = $repertoireDestination . REPERTOIRE_PREFIX_MEDIAS . DAMIER_IMAGE_DEFAUT;
					copy($source, $dest);
				}
			}
	
						
		}
		
		// Prendre note du type d'éléments 1
		$this->set("type_elements1_pub", $typeElements1);
		
		// Prendre note du type d'éléments 2
		$this->set("type_elements2_pub", $typeElements2);
		
		// Obtenir la langue de publication du questionnaire
		$langue = $this->getLangueApercuObj();

		// --------------------------------------------------------
		// Message pour bonne réponse
		// --------------------------------------------------------
		if ($this->get("reponse_bonne_message") != "") {
			$this->set("message_bonne_reponse", $this->get("reponse_bonne_message"));
		} else {
			$this->set("message_bonne_reponse", $langue->get("message_bonnereponse"));
		}
		
		// --------------------------------------------------------
		// Message pour mauvaise réponse
		// --------------------------------------------------------
		if ($this->get("reponse_mauvaise_message") != "") {
			$this->set("message_mauvaise_reponse", $this->get("reponse_mauvaise_message"));
		} else {
			$this->set("message_mauvaise_reponse", $langue->get("message_mauvaisereponse"));
		}

		// --------------------------------------------------------
		// Message pour réponse incomplete
		// --------------------------------------------------------
		if ($this->get("reponse_incomplete_message") != "") {
			$this->set("message_reponse_incomplete", $this->get("reponse_incomplete_message"));
		} else {
			$this->set("message_reponse_incomplete", $langue->get("message_reponseincomplete"));
		}
		
		// --------------------------------------------------------
		// Obtenir les ids des médias
		// --------------------------------------------------------
		$reponseBonneMedia = 0;
		if ($this->get("reponse_bonne_media") > 0) {
			$reponseBonneMedia = $this->get("reponse_bonne_media");
		} else {
			$reponseBonneMedia = $langue->get("media_bonnereponse");
		}
		$this->log->debug("Item::preparerPublication() reponseBonneMedia : '$reponseBonneMedia'");

		$reponseMauvaiseMedia = 0;
		if ($this->get("reponse_mauvaise_media") > 0) {
			$reponseMauvaiseMedia = $this->get("reponse_mauvaise_media");
		} else {
			$reponseMauvaiseMedia = $langue->get("media_mauvaisereponse");
		}
		$this->log->debug("Item::preparerPublication() reponseMauvaiseMedia : '$reponseMauvaiseMedia'");		
		
		$reponseIncompleteMedia = 0;
		if ($this->get("reponse_incomplete_media") > 0) {
			$reponseIncompleteMedia = $this->get("reponse_incomplete_media");
		} else {
			$reponseIncompleteMedia = $langue->get("media_reponseincomplete");
		}
		$this->log->debug("Item::preparerPublication() reponseIncompleteMedia : '$reponseIncompleteMedia'");
		
		// --------------------------------------------------------
		// Fichier bonne réponse
		// --------------------------------------------------------
		$media = new Media($this->log, $this->dbh);
		$nomFichier = $media->getNomFichierMedia($reponseBonneMedia, $this->get("id_projet"));
		
		// Fichier bonne réponse - type, démarrage et controleur
		$type = "0";
		$demarrage = "-1";
		$controleur = "-1";
		
		if ($media->get("type") == "image") {
			$type = "1";
		}
		elseif ($media->get("type") == "son") {
			$type = "2";
			$demarrage = $this->get("demarrage_audio");
			$controleur = "true";
		}
		elseif ($media->get("type") == "video") {
			$type = "3";
			$demarrage = $this->get("demarrage_video");
			$controleur = "true";
		}
		
		// Fichier bonne réponse - source
		$source = "0";
		if ($media->get("source") != "web") {
			$source = "1";
		}
		elseif ($media->get("source") == "web") {
			$source = "2";
		}
		
		// Fichier bonne réponse - Préparer les informations
		$this->set("reponse_bonne_media", $reponseBonneMedia);
		$this->set("reponse_bonne_media_fichier", $nomFichier);		
		$this->set("reponse_bonne_media_type", $type);
		$this->set("reponse_bonne_media_source", $source);
		$this->set("reponse_bonne_media_demarrage", $demarrage);
		$this->set("reponse_bonne_media_controleur", $controleur);
		
		// Fichier bonne réponse - Copier le fichier média
		$media->copierFichierMedia($repertoireDestination);

		// --------------------------------------------------------
		// Fichier mauvaise réponse
		// --------------------------------------------------------
		$media = new Media($this->log, $this->dbh);
		$nomFichier = $media->getNomFichierMedia($reponseMauvaiseMedia, $this->get("id_projet"));
		
		// Fichier mauvaise réponse - type, démarrage et controleur
		$type = "0";
		$demarrage = "-1";
		$controleur = "-1";
		
		if ($media->get("type") == "image") {
			$type = "1";
		}
		elseif ($media->get("type") == "son") {
			$type = "2";
			$demarrage = $this->get("demarrage_audio");
			$controleur = "true";
		}
		elseif ($media->get("type") == "video") {
			$type = "3";
			$demarrage = $this->get("demarrage_video");
			$controleur = "true";
		}
		
		// Fichier mauvaise réponse - source
		$source = "0";
		if ($media->get("source") != "web") {
			$source = "1";
		}
		elseif ($media->get("source") == "web") {
			$source = "2";
		}
		
		// Fichier mauvaise réponse - Préparer les informations
		$this->set("reponse_mauvaise_media", $reponseMauvaiseMedia);
		$this->set("reponse_mauvaise_media_fichier", $nomFichier);		
		$this->set("reponse_mauvaise_media_type", $type);
		$this->set("reponse_mauvaise_media_source", $source);
		$this->set("reponse_mauvaise_media_demarrage", $demarrage);
		$this->set("reponse_mauvaise_media_controleur", $controleur);
		
		// Fichier mauvaise réponse - Copier le fichier média
		$media->copierFichierMedia($repertoireDestination);

		// --------------------------------------------------------
		// Fichier réponse incomplète
		// --------------------------------------------------------
		$media = new Media($this->log, $this->dbh);
		$nomFichier = $media->getNomFichierMedia($reponseIncompleteMedia, $this->get("id_projet"));
		
		// Fichier incomplete réponse - type, démarrage et controleur
		$type = "0";
		$demarrage = "-1";
		$controleur = "-1";
		
		if ($media->get("type") == "image") {
			$type = "1";
		}
		elseif ($media->get("type") == "son") {
			$type = "2";
			$demarrage = $this->get("demarrage_audio");
			$controleur = "true";
		}
		elseif ($media->get("type") == "video") {
			$type = "3";
			$demarrage = $this->get("demarrage_video");
			$controleur = "true";
		}
		
		// Fichier incomplete réponse - source
		$source = "0";
		if ($media->get("source") != "web") {
			$source = "1";
		}
		elseif ($media->get("source") == "web") {
			$source = "2";
		}
		
		// Fichier incomplete réponse - Préparer les informations
		$this->set("reponse_incomplete_media", $reponseIncompleteMedia);	
		$this->set("reponse_incomplete_media_fichier", $nomFichier);		
		$this->set("reponse_incomplete_media_type", $type);
		$this->set("reponse_incomplete_media_source", $source);
		$this->set("reponse_incomplete_media_demarrage", $demarrage);
		$this->set("reponse_incomplete_media_controleur", $controleur);
		
		// Fichier incomplete réponse - Copier le fichier média
		$media->copierFichierMedia($repertoireDestination);		
		
		// --------------------------------------------------------
		// Obtenir la liste des médias dans les champs rich text
		// --------------------------------------------------------
		$cles = array_keys($this->donnees);
		 
		// Parcourir la liste de clés
		foreach ($cles as $cle) {
			$contenu = $this->get($cle);
			$matches = array();
			
			// Effectuer la recherche
			preg_match_all("/\[M(\d+?)]/i", $contenu, $matches, PREG_SET_ORDER);
					
			foreach ($matches as $val) {

				// Obtenir le texte trouvé
				$matchMedia = $val[0];
				$idMedia = $val[1];
				
				// Obtenir le média
				$media = new Media($this->log, $this->dbh);
				$nomFichier = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
				
				// Préparer le HTML selon le type de media
				$html = "";
				if ($media->get("type") == "image") {
					$gabApercu = HTML_APERCU_IMAGE;
					$html = str_replace("::IMAGE::", $nomFichier, $gabApercu);
				}
				
				$contenu = str_replace($matchMedia, $html, $contenu);
				
				// Préparer le contenu pour publication
				$clePub = $cle . "_pub";
				$this->set($clePub, $contenu);
				
				// Copier le fichier média
				$media->copierFichierMedia($repertoireDestination);
			}
		}
		
		// --------------------------------------------------------
		// Solution
		// --------------------------------------------------------
		// Transformer les fins de ligne en <br>
		$this->set("solution_pub", web::convertirFinDeLigneVersBR($this->get("solution")) );
		
		$this->log->debug("Item::preparerPublication() Fin");
	}
		

	/**
	 *
	 * Retirer les médias de la langue par défaut
	 * @param Langue Langue par défaut
	 * 
	 */
	public function retirerMediasLangueParDefaut($langue) {
	
		$this->log->debug("Item::retirerMediasLangueParDefaut() Début");
	
		// Enlever les réponses par défaut - Bonne réponse
		if ($this->get("reponse_bonne_media") > 0 && $this->get("reponse_bonne_media") == $langue->get("media_bonnereponse")) {
			$this->set("reponse_bonne_media", "");
		}
		
		// Enlever les réponses par défaut - Réponse mauvaise média
		if ($this->get("reponse_mauvaise_media") > 0 && $this->get("reponse_mauvaise_media") == $langue->get("media_mauvaisereponse")) {
			$this->set("reponse_mauvaise_media", "");
		}
		
		// Enlever les réponses par défaut - Réponse incomplète média
		if ($this->get("reponse_incomplete_media") > 0 && $this->get("reponse_incomplete_media") == $langue->get("media_reponseincomplete")) {
			$this->set("reponse_incomplete_media", "");
		}	
	
		$this->log->debug("Item::retirerMediasLangueParDefaut() Fin");
		
	}
		
		
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'une relation questionnaire -> item
	 * 
	 */
	public function ajouterLienQuestionnaireItem() {

		$this->log->debug("Item::ajouterLienQuestionnaireItem() Début");
				
		$stmt = $this->dbh->prepare("insert into tquestionnaire_item(id_questionnaire, id_item, id_projet, statut,  
									ponderation_quest, demarrer_media_quest, afficher_solution_quest, ordre_presentation_quest, type_etiquettes_quest, type_bonnesreponses_quest, orientation_elements_quest, points_retranches_quest, 
									majmin_quest, ponctuation_quest, couleur_element_quest, couleur_element_associe_quest, afficher_masque_quest, date_creation, date_modification) 
									 values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now() )");

		try {
			// Insertion d'un enregistrement
			$stmt->execute(array(	$this->get('id_questionnaire'),
									$this->get('id_item'),
									$this->get('id_projet'),
									1,
									$this->get('ponderation_quest'),
									$this->get('demarrer_media_quest'),
									$this->get('afficher_solution_quest'),
									$this->get('ordre_presentation_quest'),
									$this->get('type_etiquettes_quest'),
									$this->get('type_bonnesreponses_quest'),
									$this->get('orientation_elements_quest'),
									$this->get('points_retranches_quest'),
									$this->get('points_majmin_quest'),
									$this->get('points_ponctuation_quest'),
									$this->get('couleur_element_quest'),
									$this->get('couleur_element_associe_quest'),
									$this->get('afficher_masque_quest')
								 ));
			
			// Obtenir l'ID
			$this->donnees['id_questionnaire_item'] = $this->dbh->lastInsertId('id_questionnaire_item');
			$this->log->debug("Item::ajouter() Nouveau lien questionnaire -> item créé (id = '" . $this->get('id_questionnaire_item') . "')");
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::ajouterLienQuestionnaireItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		$this->log->debug("Item::ajouterLienQuestionnaireItem() Fin");
		return;
	}	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'une relation questionnaire -> item
	 * 
	 */
	public function ajouterLienQuestionnaireItemOrdreSection() {

		$this->log->debug("Item::ajouterLienQuestionnaireItemOrdreSection() Début");
				
		$stmt = $this->dbh->prepare("insert into tquestionnaire_item(id_questionnaire, id_item, id_projet, statut,  
									ponderation_quest, demarrer_media_quest, afficher_solution_quest, ordre_presentation_quest, type_etiquettes_quest, type_bonnesreponses_quest, orientation_elements_quest, points_retranches_quest, 
									majmin_quest, ponctuation_quest, couleur_element_quest, couleur_element_associe_quest, afficher_masque_quest, section, ordre, date_creation, date_modification) 
									 values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now() )");

		try {
			// Insertion d'un enregistrement
			$stmt->execute(array(	$this->get('id_questionnaire'),
									$this->get('id_item'),
									$this->get('id_projet'),
									1,
									$this->get('ponderation_quest'),
									$this->get('demarrer_media_quest'),
									$this->get('afficher_solution_quest'),
									$this->get('ordre_presentation_quest'),
									$this->get('type_etiquettes_quest'),
									$this->get('type_bonnesreponses_quest'),
									$this->get('orientation_elements_quest'),
									$this->get('points_retranches_quest'),
									$this->get('majmin_quest'),
									$this->get('ponctuation_quest'),
									$this->get('couleur_element_quest'),
									$this->get('couleur_element_associe_quest'),
									$this->get('afficher_masque_quest'),
									$this->get('section'),
									$this->get('ordre')
								 ));
			
			// Obtenir l'ID
			$this->donnees['id_questionnaire_item'] = $this->dbh->lastInsertId('id_questionnaire_item');
			$this->log->debug("Item::ajouter() Nouveau lien questionnaire -> item créé (id = '" . $this->get('id_questionnaire_item') . "')");
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::ajouterLienQuestionnaireItemOrdreSection() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		$this->log->debug("Item::ajouterLienQuestionnaireItemOrdreSection() Fin");
		return;
	}	
	

	/**
	 * 
	 * Mettre à jour le nombre de liens pour l'item
	 *
	 */
	public function updateLiensItem() {
		
		$this->log->debug("Item::updateLiensItem() Début");
		
		try {
			$sql = "select count(*) from tquestionnaire_item where id_projet = ? and id_item = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($this->get("id_projet"), $this->get("id_item")));
			
			// Obtenir le nombre de liens
			$total = $sth->fetchColumn();
			$this->set("liens", $total);
	
			// Mettre à jour le nombre de liens
			$stmt = $this->dbh->prepare("update titem 
								  		 set liens = ?										
										 where id_item = ? 
										 and id_projet= ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $total,
									$this->get('id_item'),
									$this->get('id_projet')
									) );

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::updateLiensItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}											
		
		$this->log->debug("Item::updateLiensItem() Fin");
		
	}

	
	/**
	 * 
	 * Mettre à jour le nombre de liens pour l'item
	 *
	 */
	public static function updateLiensItemStatic($idProjet, $idItem, $log, $dbh) {
		
		$log->debug("Item::updateLiensItem() Début");
		
		try {
			$sql = "select count(*) from tquestionnaire_item where id_projet = ? and id_item = ?";
			$sth = $dbh->prepare($sql);
			$sth->execute(array($idProjet, $idItem));
			
			// Obtenir le nombre de liens
			$total = $sth->fetchColumn();
			$this->set("liens", $total);
	
			// Mettre à jour le nombre de liens
			$stmt = $this->dbh->prepare("update titem 
								  		 set liens = ?										
										 where id_item = ? 
										 and id_projet= ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $total,
									$idProjet,
									$idItem
									) );
									
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::updateLiensItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}											
		
		$log->debug("Item::updateLiensItem() Fin");
	}
	
	
	/**
	 * 
	 * Instancier un objet item selon le type d'item
	 *
	 */
	public function instancierItemParType($typeItem, $idProjet, $idItem) {

		$this->log->debug("Item::instancierTypeItem() Début typeItem = '$typeItem'  idProjet = '$idProjet'  idItem = '$idItem'");
		
		$instance = new Item($this->log, $this->dbh);
		
		// Déterminer le type d'item - au besoin récupérer de la bd
		if ($typeItem == "") {
			$typeItem = $this->getTypeItem($idProjet, $idItem);
		}
		
		$this->log->debug("Item::instancierTypeItem() type d'item détecter : '$typeItem'");

		// Instancier selon le type 
		if ($typeItem == 1) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 1 - Associations");
			$instance = new ItemAssociations($this->log, $this->dbh);
		} elseif ($typeItem == 2) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 2 - Choix multiples");
			$instance = new ItemChoixMultiples($this->log, $this->dbh);
		} elseif ($typeItem == 3) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 3 - Classement");
			$instance = new ItemClassement($this->log, $this->dbh);
		} elseif ($typeItem == 4) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 4 - Damier");
			$instance = new ItemDamier($this->log, $this->dbh);
		} elseif ($typeItem == 5) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 5 - Développement");
			$instance = new ItemDeveloppement($this->log, $this->dbh);
		} elseif ($typeItem == 6) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 6 - Dictée");
			$instance = new ItemDictee($this->log, $this->dbh);
		} elseif ($typeItem == 7) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 7 - Marquage");
			$instance = new ItemMarquage($this->log, $this->dbh);
		} elseif ($typeItem == 8) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 8 - Mise en ordre");
			$instance = new ItemMiseOrdre($this->log, $this->dbh);
		} elseif ($typeItem == 9) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 9 - Réponse brève");
			$instance = new ItemReponseBreve($this->log, $this->dbh);
		} elseif ($typeItem == 10) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 10 - Réponses multiples");
			$instance = new ItemReponsesMultiples($this->log, $this->dbh);
		} elseif ($typeItem == 11) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 11 - Texte lacunaire");
			$instance = new ItemTexteLacunaire($this->log, $this->dbh);
		} elseif ($typeItem == 12) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 12 - Vrai ou faux");
			$instance = new ItemVraiFaux($this->log, $this->dbh);
		} elseif ($typeItem == 13) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 13 - Zone à identifier");
			$instance = new ItemZonesIdentifier($this->log, $this->dbh);
		} elseif ($typeItem == 14) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 14 - Page");
			$instance = new ItemPage($this->log, $this->dbh);
		} elseif ($typeItem == 15) {
			$this->log->debug("Item::instancierTypeItem() Instantier un item de type 15 - Section");
			$instance = new ItemSection($this->log, $this->dbh);
		} elseif ($idItem != "" && $typeItem != "") {
			Erreur::erreurFatal("128", "Impossible d'instancier un item avec le type : '" . $typeItem . "'  (id_item:'" . $idItem . "')", $this->log);
		}
		
		// Ajouter le type d'item aux données
		$instance->set("type_item", $typeItem);
		
		// Charger les données
		$this->log->debug("Item::instancierTypeItem() Vérifier si on doit charger les données");
		if ($idItem != "") {
			$this->log->debug("Item::instancierTypeItem() Chargement des données idItem = '$idItem'  idProjet = '$idProjet'");
			$instance->getItemParId($idItem, $idProjet);
		}
		
		$this->log->debug("Item::instancierTypeItem() Fin");
		
		return $instance;
	}		

	
	/**
	 * 
	 * Configurer les valeurs par défaut selon le type d'item
	 * @param string typeItem
	 * @param Projet projet
	 * @param Usager usager
	 *
	 */
	public function preparerValeursInitiales($typeItem, $projet, $usager) {

		$this->log->debug("Item::instancierTypeItem() Début typeItem = '$typeItem'");	

		// Valider pour tous les types sauf section
		if ($typeItem != 15) {

			// Valeurs par défaut
			if ($this->get("titre") == "") {
				$this->set("titre", TXT_NOUVEL_ITEM);
				$this->set("titre_menu", TXT_NOUVEL_ITEM);
			}
			$this->set("type_elements1", "texte");
		}
	
		// Régler la pondération à 1 pour tous les items sauf développement et page
		if ($typeItem != 5 && $typeItem != 14) {
			$this->set("ponderation", PONDERATION_DEFAULT);
		} 
		
		// Valeur par défaut pour tous les types
		$this->set("id_projet", $projet->get("id_projet"));
		$this->set("statut", "1");
		
		$this->log->debug("Item::instancierTypeItem() Fin");
	}
	
	
	/**
	 * 
	 * Obtenir le type d'item
	 *
	 */
	public function getTypeItem($idProjet, $idItem) {

		$this->log->debug("Item::getTypeItem() Début");
		
		$sql = "select type_item from titem where id_projet = ? and titem.id_item = ?";
		$sth = $this->dbh->prepare($sql);
		$sth->execute(array($idProjet, $idItem));
		
		// Vérifier qu'on a trouvé au moins un type d'item
		$type_item = $sth->fetchColumn();
	
		$this->log->debug("Item::getTypeItem() Fin");
		
		return $type_item;
	}



	/**
	 * 
	 * Générer l'aperçu d'un item
	 * @param Projet projet
	 * @param Usager usager
	 * @param Item item
	 * @param string titreQuest
	 * @param string chaineAleatoire
	 * @param Questionnaire quest
	 *
	 */
	public function genererApercu($projet, $usager, $item, $chaineAleatoire, $quest) {

		$this->log->debug("Item::genererApercu() Début  repertoire : '" . $projet->get("repertoire") . "' idProjet = '" . $projet->get("id_projet") . "' idItem = '" . $item->get("id_item") . "'  titre = '" . $item->get("titre") . "' chaineAleatoire = '$chaineAleatoire'");

		// Si on est dans un questionnaire, utiliser le thème du questionnaire, sinon celui de l'item
		$theme = "";
		if ($quest != null) {
			$theme = $quest->get("theme");
		} else {
			$theme = $this->get("apercu_theme"); 
		}
		
		// Ajouter la langue du questionnaire si applicable
		if ($quest != null) {
			$this->set("id_langue_questionnaire", $quest->get("id_langue_questionnaire"));
		}
		
		// Utiliser le thème par défaut au besoin
		if ($theme == "") {
			$theme = FICHIER_THEME_DEFAUT;
		}		
		
		$repertoireSource = REPERTOIRE_THEMES . $theme . "/";
		
		// Déterminer les répertoires sources et de publication de l'aperçu
		$repertoireDestinationProjet = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/";
		$repertoireDestinationApercu = $repertoireDestinationProjet . REPERTOIRE_PREFIX_APERCU;
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" . REPERTOIRE_PREFIX_APERCU . $chaineAleatoire . "/";
		
		// Vérifier si le répertoire projet existe sinon le créé
		if (!is_dir($repertoireDestinationProjet)) { 
			mkdir($repertoireDestinationProjet);
			$this->log->debug("Item::genererApercu() Le répertoire '$repertoireDestinationProjet' a été créé");
		}
		
		// Publier l'aperçu	
		$publication = new Publication($this->log, $this->dbh);
		$succes = $publication->preparerRepertoire($repertoireDestinationApercu, $repertoireDestination);
		if ($succes == 1) {
			$succes = $publication->copierTheme($repertoireSource, $repertoireDestination);
		}
		
		// Préparer le fichier de configuration du quiz (main.js)
		$contenu = $this->publierItemUnique($repertoireDestination, $quest);
		//$contenu = _entity_decode(utf8_decode($contenu), ENT_QUOTES);
		
		// Écrire la configuration du Quiz (main.js)
		$publication->ecrireFichier($repertoireDestination . FICHIER_MAIN_JS, $contenu);
				
		// Préparer le fichier lexique.js
		if ($quest != null) {
			$contenu = $quest->publierLexique($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_LEXIQUE_JS, $contenu);
		}

			
		$this->log->debug("Item::genererApercu() Fin");
		return $succes;
		
	}	
	
	
	/**
	 * 
	 * ajouterMediaListeExportation()
	 * Ajouter les médias d'un item à la liste d'exportation
	 * @param array Liste médias à exporter
	 *
	 */
	public function ajouterMediaListeExportation($listeMedias) {
	
		$this->log->debug("Item::ajouterMediaListeExportation() Début");
	
		// Image
		if ($this->get("media_image") > 0 && !in_array($this->get("media_image"), $listeMedias)) {
			array_push($listeMedias, $this->get("media_image"));
		}
		
		// Son
		if ($this->get("media_son") > 0 && !in_array($this->get("media_son"), $listeMedias)) {
			array_push($listeMedias, $this->get("media_son"));
		}

		// Video
		if ($this->get("media_video") > 0 && !in_array($this->get("media_video"), $listeMedias)) {
			array_push($listeMedias, $this->get("media_video"));
		}

		// Réponse bonne média
		if ($this->get("reponse_bonne_media") > 0 && !in_array($this->get("reponse_bonne_media"), $listeMedias)) {
			array_push($listeMedias, $this->get("reponse_bonne_media"));
		}
		
		// Réponse mauvaise média
		if ($this->get("reponse_mauvaise_media") > 0 && !in_array($this->get("reponse_mauvaise_media"), $listeMedias)) {
			array_push($listeMedias, $this->get("reponse_mauvaise_media"));
		}

		// Réponse incomplète média
		if ($this->get("reponse_incomplete_media") > 0 && !in_array($this->get("reponse_incomplete_media"), $listeMedias)) {
			array_push($listeMedias, $this->get("reponse_incomplete_media"));
		}

		// Image
		if ($this->get("image") > 0 && !in_array($this->get("image"), $listeMedias)) {
			array_push($listeMedias, $this->get("image"));
		}		
		
		// Ajouter les médias des réponses au besoin - element
		if ($this->get("type_elements1") == "image") {
			
			for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			
				// Obtenir les éléments et réponses
				$element = $this->get("reponse_" . $i . "_element");
				if ($element  > 0 && !in_array($element, $listeMedias)) {
					array_push($listeMedias, $element);
				}
			}
		}

		// Ajouter les médias des réponses au besoin - element associe
		if ($this->get("type_elements2") == "image") {
			
			for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			
				// Obtenir les éléments et réponses
				$elementAssocie = $this->get("reponse_" . $i . "_element_associe");
				if ($elementAssocie  > 0 && !in_array($element, $listeMedias)) {
					array_push($listeMedias, $elementAssocie);
				}
			}
		}
		
		// Ajouter les médias pour les masques au besoin
		for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
				
			// Obtenir les éléments et réponses
			$element = $this->get("reponse_" . $i . "_masque");
			if ($element  > 0 && !in_array($element, $listeMedias)) {
				array_push($listeMedias, $element);
			}
		}
	
		// Ajouter les médias qui sont dans les zones de texte (rich text)
		$cles = array_keys($this->donnees);
			
		// Parcourir la liste de clés
		foreach ($cles as $cle) {
			$contenu = $this->get($cle);
			$matches = array();
				
			// Effectuer la recherche
			preg_match_all("/\[M(\d+?)]/i", $contenu, $matches, PREG_SET_ORDER);
			
			foreach ($matches as $val) {
		
				// Obtenir le média trouvé
				$idMedia = $val[1];

				// L'ajouter à la liste
				if ($idMedia != "" && $idMedia != "0") {
					array_push($listeMedias, $idMedia);
				}
			}
		}
				
		$this->log->debug("Item::ajouterMediaListeExportation() Fin");
		
		return $listeMedias;
	}


	/**
	 * 
	 * Imprimer un item
	 *
	 */
	public function imprimer($quest) {
	
		$this->log->debug("Item::imprimer() Début");
	
		// Déterminer gabarit d'impression
		$gabaritImpression = REPERTOIRE_GABARITS_IMPRESSION . "item-" . constant('ITEM_' . $this->get("type_item")) . ".php";
		
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritImpression)) {
			$this->log->erreur("Le gabarit d'impression '$gabaritImpression' ne peut être localisé.");
		}
		
		// Obtenir le contenu pour impression
		$contenu = Fichiers::getContenuQuestItem($gabaritImpression , $quest, $this);

		// L'ajouter à l'item
		$this->set("contenu", $contenu);
		
		// Déterminer le gabarit à utiliser pour l'impression
		if ($this->get("type_item") == 15) {
			// Régler le gabarit à utiliser
			$this->set("gabarit_impression", IMPRESSION_GABARIT_SECTION);
		} else {
			// Régler le gabarit à utiliser
			$this->set("gabarit_impression", IMPRESSION_GABARIT_ITEM);
		}
		
		$this->log->debug("Item::imprimer() Fin");
		
		return $contenu;
	}		

	
	/**
	 * 
	 * Supprimer un item d'un questionnaire
	 * @param String idItem
	 * @param String idQuest
	 * @param String idProjet
	 */
	public function supprimerItemQuestionnaire($idItem, $idQuest, $idProjet) {	
		
		$this->log->debug("Item::supprimerItemQuestionnaire() Début idItem = '$idItem'  idQuest = '$idQuest'  idProjet = '$idProjet'");
		
		try {
			// Préparer la mise à jour
			$stmt = $this->dbh->prepare("delete from tquestionnaire_item 
										 where id_item = ?
										 and id_questionnaire = ?
										 and id_projet = ?
										");
			
			$stmt->execute( array( $idItem,
								   $idQuest,
								   $idProjet
								 ) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::supprimerItemQuestionnaire() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}								 		
		
		// Mettre à jour le nombre de liens pour l'item
		$this->updateLiensItem();							 
							 
		$this->log->debug("Item::supprimerItemQuestionnaire() Fin");
	}		

	
	/**
	 * 
	 * Supprimer un item d'un questionnaire
	 * 
	 */
	public function supprimer() {	
		
		$this->log->debug("Item::supprimer() Début");
		
		try {
			// Supprimer les classeurs au besoin
			$stmt = $this->dbh->prepare("delete from titem_classeur where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
						
			// Supprimer les couleurs au besoin
			$stmt = $this->dbh->prepare("delete from titem_couleur where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
			
			// Supprimer les index au besoin
			$stmt = $this->dbh->prepare("delete from titem_index where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
				
			// Supprimer les lacunes au besoin
			$stmt = $this->dbh->prepare("delete from titem_lacune where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );

			// Supprimer les réponses lacunes au besoin
			$stmt = $this->dbh->prepare("delete from titem_lacune_reponse where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
			
			// Supprimer les marques au besoin
			$stmt = $this->dbh->prepare("delete from titem_marque where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
			
			// Supprimer les réponses au besoin
			$stmt = $this->dbh->prepare("delete from titem_reponse where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
			
			// Supprimer les section au besoin
			$stmt = $this->dbh->prepare("delete from titem_section where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
						
			// Supprimer l'item
			$stmt = $this->dbh->prepare("delete from titem where id_item = ? and id_projet = ?");
			$stmt->execute( array( $this->get("id_item"), $this->get("id_projet") ) );
					
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}		
		$this->log->debug("Item::supprimer() Fin");
	}			
	
	/**
	 * 
	 * Supprimer une section et ses items du menu
	 * @param String idItem
	 * @param String idQuest
	 * @param String idProjet
	 * 
	 */
	public function supprimerItemsSection($idSection, $idQuest, $idProjet) {	
		
		$this->log->debug("Item::supprimerItemsSection() Début");
		
		// Obtenir la liste des items pour la section
		$section = new ItemSection($this->log, $this->dbh);
		$listeItems = $section->getItemsSection($idSection, $idQuest, $idProjet);

		try {
			// Préparer la mise à jour
			$stmt = $this->dbh->prepare("delete from tquestionnaire_item 
										 where id_item = ?
										 and id_questionnaire = ?
										 and id_projet = ?
										");
			
			foreach ($listeItems as $idItem) {
			
				$stmt->execute( array( $idItem,
									   $idQuest,
									   $idProjet
									 ) );		
	
				// Mettre à jour le nombre de liens pour l'item
				$this->updateLiensItem();								 
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::supprimerItemsSection() - Erreur technique détectée durant la suppression des items : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Supprimer les informations sur la section
		try {
			// Requête
			$this->log->debug("Item::supprimerItemsSection() Suppression de la section '$idSection' pour le projet '$idProjet'");
			$stmt = $this->dbh->prepare("delete from titem_section
										 where id_item = ?
										 and id_projet = ?
										");
				
			$stmt->execute( array( $idSection, $idProjet) );
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::supprimerItemsSection() - Erreur technique détectée durant la suppression de la section '$idSection' : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
				
		
		$this->log->debug("Item::supprimerItemsSection() Fin");
		
	}	
			

	/**
	 * 
	 * Préparer l'index de recherche
	 * @param String chaine
	 * @param String idProjet
	 */
	protected function preparerIndex() {
		
		$this->log->debug("Item: preparerIndex() Début");
		
		$index = "";
		$index .= TXT_PREFIX_ITEM . $this->get("id_item") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("enonce") . " ";
		$index .= $this->get("info_comp1_titre") . " ";
		$index .= $this->get("info_comp1_texte") . " ";
		$index .= $this->get("info_comp2_titre") . " ";
		$index .= $this->get("info_comp2_texte") . " ";
		$index .= $this->get("media_titre") . " ";
		$index .= $this->get("media_texte") . " ";
		$index .= $this->get("solution") . " ";
		$index .= $this->get("retroaction_positive") . " ";
		$index .= $this->get("retroaction_negative") . " ";
		$index .= $this->get("retroaction_reponse_imprevue") . " ";
		$index .= $this->get("reponse_bonne_message") . " ";
		$index .= $this->get("reponse_bonne_media") . " ";
		$index .= $this->get("reponse_mauvaise_message") . " ";
		$index .= $this->get("reponse_mauvaise_media") . " ";
		$index .= $this->get("reponse_incomplete_message") . " ";
		$index .= $this->get("reponse_incomplete_media") . " ";
		$index .= $this->get("remarque") . " ";

		$this->log->debug("Item: preparerIndex() Fin");
		
		return $index;
	}

	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idProjet
	 */
	protected function updateIndex($index) {
		
		$this->log->debug("Item: updateIndex() Début  index = '$index'");
		
		// Nettoyer la chaîne de recherche
		$index = Web::nettoyerChaineRech($index);
		
		try {
		
			// Supprimer l'index existant au besoin
			$sql = "delete from titem_index where id_projet = ? and id_item = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_item")));
			$this->log->debug("Item: updateIndex() Suppression des données d'index pour : idProjet = '" . $this->get("id_projet") . "' id_item = '" . $this->get("id_item") . "'");
			$this->log->debug("Item: updateIndex() Suppression complétée");
			
			// Insérer l'index
			$this->log->debug("Item: updateIndex() Ajout des données d'index pour : idProjet = '" . $this->get("id_projet") . "' id_item = '" . $this->get("id_item") . "'");
			$sql = "insert into titem_index (id_projet, id_item, texte, date_creation, date_modification)
					values (?, ?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_item"), $index));
			$this->log->debug("Item: updateIndex() Ajout complété");
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Item: updateIndex() Fin");
	}		


	
	/**
	 * 
	 * Mettre à jour le nombre d'items au niveau des questionnaires
	 *
	 */
	public function updateNombreItemsQuest() {
		
		$this->log->debug("Item::updateNombreItemsQuest()");
		
		// Déterminer si l'item se retrouve dans un questionnaire
		$listeQuest = array();
		$listeQuest = $this->getListeIDQuestionnairesUtilisantItem();
		
		// Mettre à jour le nombre d'items pour chaque questionnaire
		foreach ($listeQuest as $idQuest) {

			$quest = new Questionnaire($this->log, $this->dbh);
			$quest->getQuestionnaireParId($idQuest, $this->get("id_projet"));
			$quest->updateNombreItems($idQuest, $quest->get("id_projet"));
			$this->log->debug("Item::desactiver() Mise à jour du nombre d'items pour id_quest : '" . $idQuest . "'  id_projet : '" . $quest->get("id_projet") . "'");
		}
		
		$this->log->debug("Item::updateNombreItemsQuest() Fin");
	}		
	
	/**
	 * 
	 * Désactiver l'item (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
		
		$this->log->debug("Item::desactiver()");
		
		// Modifier le statut de l'item
		$this->set("statut","0");
		$this->enregistrer();
		
		// Mettre à jour les questionnaires (nombre d'items)
		$this->updateNombreItemsQuest();
		
		$this->log->debug("Item::desactiver() Fin");
	}	
	
	
	/**
	 * 
	 * Activer le suivi d'un item
	 *
	 */
	public function activerSuivi() {
		
		// Activer le suivi
		$this->set("suivi", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
	}
	
	/**
	 * 
	 * Désactiver le suivi d'un item
	 *
	 */
	public function desactiverSuivi() {
		
		// Activer le suivi
		$this->set("suivi", "0");
		
		// Sauvegarder les données
		$this->enregistrer();
	}	
	

	/**
	 * 
	 * Activer l'item
	 *
	 */
	public function activer() {
		
		// Activer l'item
		$this->set("statut", "1");
		$this->enregistrer();

		// Mettre à jour les questionnaires (nombre d'items)
		$this->updateNombreItemsQuest();		
	}	
	
	
	/**
	 * 
	 * Vérifie si le type d'item spécifié est valide
	 * @param Item $item
	 */
	public static function isTypeItemValide($item) {

		$val = "";
		try {
			$val = constant('ITEM_' . $item);
		} catch (Exception $e) {
			
		}
		
		return ( $val != "");
	}
		
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - mise à jour d'une relation questionnaire -> item
	 * 
	 */
	public function enregistrerLienQuestionnaireItem() {

		$this->log->debug("Item::enregistrerLienQuestionnaireItem() Début");
		
		try {
			$stmt = $this->dbh->prepare("update tquestionnaire_item 
								  		 set 
								  		   	date_modification = now(),
								  		   	ponderation_quest = ?, 
								  		   	demarrer_media_quest = ?, 
								  		   	afficher_solution_quest = ?,
								  		   	ordre_presentation_quest = ?,
								  		   	type_etiquettes_quest = ?,
								  		   	type_bonnesreponses_quest = ?,
											orientation_elements_quest = ?,
								  		   	points_retranches_quest = ?,
								  		   	majmin_quest = ?,
								  		   	ponctuation_quest = ?,
											couleur_element_quest = ?,
											couleur_element_associe_quest = ?,
											afficher_masque_quest = ?
										 where 
										   	id_questionnaire = ?
										 	and id_item = ? 
										 	and id_projet= ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(	$this->get('ponderation_quest'),
									$this->get('demarrer_media_quest'),
									$this->get('afficher_solution_quest'),  
									$this->get('ordre_presentation_quest'),
									$this->get('type_etiquettes_quest'),
									$this->get('type_bonnesreponses_quest'),
									$this->get('orientation_elements_quest'),
									$this->get('points_retranches_quest'),
									$this->get('majmin_quest'),
									$this->get('ponctuation_quest'),
									$this->get('couleur_element_quest'),
									$this->get('couleur_element_associe_quest'),
									$this->get('afficher_masque_quest'),					
									$this->get('id_questionnaire'),
									$this->get('id_item'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::enregistrerLienQuestionnaireItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}									
		
		$this->log->debug("Item::enregistrerLienQuestionnaireItem() Fin");
									
		return;
	}		

	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - mise à jour d'une relation questionnaire -> item
	 * 
	 */
	public function enregistrerLienQuestionnaireItemOrdreSection() {

		$this->log->debug("Item::enregistrerLienQuestionnaireItemOrdreSection() Début");

		try {
			$stmt = $this->dbh->prepare("update tquestionnaire_item 
								  		 set 
								  		   	date_modification = now(),
								  		   	ponderation_quest = ?, 
								  		   	demarrer_media_quest = ?, 
								  		   	afficher_solution_quest = ?,
								  		   	ordre_presentation_quest = ?,
								  		   	type_etiquettes_quest = ?,
								  		   	type_bonnesreponses_quest = ?,
											orientation_elements_quest = ?,
								  		   	points_retranches_quest = ?,
								  		   	majmin_quest = ?,
								  		   	ponctuation_quest = ?,
											couleur_element_quest = ?,
											couleur_element_associe_quest = ?,
											afficher_masque_quest = ?,
								  		   	section = ?,
								  		   	ordre = ?	
										 where 
										   	id_questionnaire = ?
										 	and id_item = ? 
										 	and id_projet= ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(	$this->get('ponderation_quest'),
									$this->get('demarrer_media_quest'),
									$this->get('afficher_solution_quest'),  
									$this->get('ordre_presentation_quest'),
									$this->get('type_etiquettes_quest'),
									$this->get('type_bonnesreponses_quest'),
									$this->get('orientation_elements_quest'),
									$this->get('points_retranches_quest'),
									$this->get('majmin_quest'),
									$this->get('ponctuation_quest'),
									$this->get('couleur_element_quest'),
									$this->get('couleur_element_associe_quest'),
									$this->get('afficher_masque_quest'),
									$this->get('section'),
									$this->get('ordre'),
									$this->get('id_questionnaire'),
									$this->get('id_item'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::enregistrerLienQuestionnaireItemOrdreSection() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}									
		
		$this->log->debug("Item::enregistrerLienQuestionnaireItemOrdreSection() Fin");
									
		return;
	}		
	
	/**
	 * 
	 * Supprimer les choix de réponses
	 *
	 */
	public function supprimerChoixReponses() {

		$this->log->debug("Item::supprimerChoixReponses() Début");

		// Supprimer les valeurs pour choix multiples
		$this->deleteByPrefix("reponse");
    	
    	// Supprimer les checkboxes
    	$this->set("majmin", "-1");
		$this->set("ponctuation", "-1");
		$this->set("majmin_quest", "-1");
		$this->set("ponctuation_quest", "-1");
    	    	
		$this->log->debug("Item::supprimerChoixReponses() Fin");
		
		return;
	}		
	
	
	/**
	 * 
	 * Publier un item seulement
	 * @param string Répertoire de destination
	 * @param Questionnaire quest
	 *
	 */
	protected function publierItemUnique($repertoireDestination, $quest) {

		$this->log->debug("Item::publierItemUnique() Début");
		
		$contenu = "";

		// Dans un contexte de questionnaire, utiliser la langue du questionnaire
		$idLangue = 0;
		if ($quest != null) {
			$idLangue = $quest->get("id_langue_questionnaire");
		} else {
			// Sinon utiliser la langue disponible au niveau de l'item (qui provient des préférences de l'utilisateur)
			$idLangue = $this->get("apercu_langue");
		}
		
		// Si la langue n'est pas réglée utilisée celle par défaut
		if ($idLangue == 0) {
			$idLangue = LANGUE_DEFAUT_ID;
		}

		// Charger la langue
		$langue = new Langue($this->log, $this->dbh);
		$langue->getLangueParId($idLangue, $this->get("id_projet"));
		
		// Récupérer le gabarit pour publier un seul item - début
		$contenu .= Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-unique-debut.php", $this, $langue);

		if ($quest == null) {
			// Utiliser un questionnaire vide
			$quest = new Questionnaire($this->log, $this->dbh);
		}
		
		// Préparer l'index de publication
		$this->set("index_publication", 0);
		
		// Récupérer la configuration particulière à l'item
		$contenu .= $this->publier($langue, $repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier un seul item - fin
		$contenu .= Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-unique-fin.php", $this, $langue);
		
		$this->log->debug("Item::publierItemUnique() Fin");
		
		return $contenu;
	}	
		

	/**
	 *
	 * Validation commune à tous les items
	 * Questionnaire $quest
	 * 
	 */
	protected function verifierTheme($quest) {
	
		$this->log->debug("Item::verifierTheme() Début");

		$messages = "";
		
		$theme = "";
		if ($quest != null) {
			$theme = $quest->get("theme");
		} else {
			$theme = $this->get("apercu_theme");
		}
		
		// Utiliser le thème par défaut au besoin
		if ($theme == "") {
			$theme = FICHIER_THEME_DEFAUT;
		}		
		
		// Utiliser le thème par défaut au besoin
		if ($theme == "") {
			$theme = FICHIER_THEME_DEFAUT;
		}
		
		// Vérifier que le thème existe
		$repertoireSourceTheme = REPERTOIRE_THEMES . $theme . "/";
		$this->log->debug("Questionnaire::verifierTheme() Vérifier que le thème sélectionné existe ('$repertoireSourceTheme')");
		if (!is_dir($repertoireSourceTheme)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_042 . HTML_LISTE_ERREUR_FIN;
			$this->log->debug("Questionnaire::valider() Problème détecté : Le répertoire du thème sélectionné n'existe pas (theme = '$theme')");
		}
		
		$this->log->debug("Item::verifierTheme() Fin");
	
		return $messages;
	}
			
	
	/**
	 * 
	 * Analyser les éléments
	 *
	 */
	public function analyserElements() {

		$this->log->debug("Item::analyserElements()");
				
		// Obtenir la liste des éléments à choix multiples
		$nbElements = 0;
		$valeursExistent = 0;
		
		for ($i = NB_MAX_CHOIX_REPONSES; $i >= 1; $i--) {
			
			$nbElements++;
			
			// Obtenir les valeurs
			$element = trim($this->get("reponse_" . $i . "_element"));
			$elementAssocie = trim($this->get("reponse_" . $i . "_element_associe"));
			$marque = trim($this->get("reponse_" . $i . "_masque"));
			$retro = trim($this->get("reponse_" . $i . "_retroaction"));
			$retroNeg = trim($this->get("reponse_" . $i . "_retroaction_negative"));
			$retroIncomp = trim($this->get("reponse_" . $i . "_retroaction_incomplete"));
			$reponse = trim($this->get("reponse_" . $i . "_reponse"));
			$statut = trim($this->get("reponse_" . $i . "_statut"));
			
			// Vérifier si l'item est actif
			if ($element != "" || $elementAssocie != "" || $marque != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $reponse != "" || $i <= 2 || $statut == 1 || $valeursExistent == 1) {
				$statut = 1;
				$valeursExistent = 1;
			}
			if ($statut != "") {
				$this->set("reponse_" . $i . "_statut", $statut);
			}
		}
		
		$this->log->debug("Item::analyserElements() Fin");
		return;
	}			
	
	
	
	/**
	 * 
	 * Ajouter un élément
	 * @param string position
	 *
	 */
	public function ajouterElement($position) {

		$this->log->debug("Item::ajouterElement() Début position : '$position'");
				
		// Obtenir la liste des éléments à choix multiples
		$idx = NB_MAX_CHOIX_REPONSES + 1;
		$valeursExistent = 0;
	
		for ($i = NB_MAX_CHOIX_REPONSES; $i >= 1; $i--) {
			
			// Ajouter l'élément
			if ($position == $i) {
				$this->set("reponse_" . $idx . "_element", "");
				$this->set("reponse_" . $idx . "_element_associe", "");
				$this->set("reponse_" . $idx . "_masque", "0");
				$this->set("reponse_" . $idx . "_retroaction", "");
				$this->set("reponse_" . $idx . "_retroaction_negative", "");
				$this->set("reponse_" . $idx . "_retroaction_incomplete", "");
				$this->set("reponse_" . $idx . "_coordonnee_x", "");
				$this->set("reponse_" . $idx . "_coordonnee_y", "");
				$this->set("reponse_" . $idx . "_statut", 1);
				$this->set("reponse_" . $idx . "_reponse", "");
				$idx--;
			}
			
			// Obtenir les valeurs
			$element = trim($this->get("reponse_" . $i . "_element"));
			$elementAssocie = trim($this->get("reponse_" . $i . "_element_associe"));
			$masque = trim($this->get("reponse_" . $i . "_masque"));
			$retro = trim($this->get("reponse_" . $i . "_retroaction"));
			$retroNeg = trim($this->get("reponse_" . $i . "_retroaction_negative"));
			$retroIncomp = trim($this->get("reponse_" . $i . "_retroaction_incomplete"));
			$reponse = trim($this->get("reponse_" . $i . "_reponse"));
			$coordX = trim($this->get("reponse_" . $i . "_coordonnee_x"));
			$coordY = trim($this->get("reponse_" . $i . "_coordonnee_y"));
			
			// Vérifier si l'item est actif
			$statut = 0;
			if ($element != "" || $elementAssocie != "" || $masque != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $coordX != "" || $coordY != "" || $valeursExistent) {
				$statut = 1;
				$valeursExistent = 1;
			}
			
			if ($statut == 1) {
				// Nouvelles valeurs
				$this->set("reponse_" . $idx . "_element", $element);
				$this->set("reponse_" . $idx . "_element_associe", $elementAssocie);
				$this->set("reponse_" . $idx . "_masque", $masque);
				$this->set("reponse_" . $idx . "_retroaction", $retro);
				$this->set("reponse_" . $idx . "_retroaction_negative", $retroNeg);
				$this->set("reponse_" . $idx . "_retroaction_incomplete", $retroIncomp);
				$this->set("reponse_" . $idx . "_coordonnee_x", $coordX);
				$this->set("reponse_" . $idx . "_coordonnee_y", $coordY);
				$this->set("reponse_" . $idx . "_statut", $statut);
				$this->set("reponse_" . $idx . "_reponse", $reponse);
			} else {
				$this->delete("reponse_" . $idx . "_element");
				$this->delete("reponse_" . $idx . "_element_associe");
				$this->delete("reponse_" . $idx . "_masque");
				$this->delete("reponse_" . $idx . "_retroaction");
				$this->delete("reponse_" . $idx . "_retroaction_negative");
				$this->delete("reponse_" . $idx . "_retroaction_incomplete");
				$this->delete("reponse_" . $idx . "_coordonnee_x");
				$this->delete("reponse_" . $idx . "_coordonnee_y");
				$this->delete("reponse_" . $idx . "_statut");
				$this->delete("reponse_" . $idx . "_reponse");
			}
			$idx--;
		}
		
		// Ajuster la réponse pour type d'item choix multiple
		if ($this->get("type_item") == "2") {
			$reponse = $this->get("reponse_choix");
			if ($reponse > $position) {
				$reponse++;
				$this->set("reponse_choix", $reponse);
			}
		}
		
		$this->log->debug("Item::ajouterElement() Fin");
		return;
	}			
	
	
	/**
	 * 
	 * Supprimer un élément
	 * @param string position
	 *
	 */
	public function supprimerElement($position) {

		$this->log->debug("Item::supprimerElement() Début position : '$position'");
		
		// Obtenir la liste des éléments à choix multiples
		$idx = 1;
		
		for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
			
			// Supprimer l'élément
			if ($position != $i) {
			
				// Obtenir les valeurs
				$element = trim($this->get("reponse_" . $i . "_element"));
				$elementAssocie = trim($this->get("reponse_" . $i . "_element_associe"));
				$masque = trim($this->get("reponse_" . $i . "_masque"));
				$retro = trim($this->get("reponse_" . $i . "_retroaction"));
				$retroNeg = trim($this->get("reponse_" . $i . "_retroaction_negative"));
				$retroIncomp = trim($this->get("reponse_" . $i . "_retroaction_incomplete"));
				$coordX = trim($this->get("reponse_" . $i . "_coordonnee_x"));
				$coordY = trim($this->get("reponse_" . $i . "_coordonnee_y"));
				$statut = trim($this->get("reponse_" . $i . "_statut"));
				$reponse = trim($this->get("reponse_" . $i . "_reponse"));
				
				// Vérifier si l'item est actif
				if ($element != "" || $elementAssocie != "" || $masque != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $coordX != "" || $coordY != "") {
					$statut = 1;
				}
				
				if ($statut == 1) {
					// Nouvelles valeurs
					$this->set("reponse_" . $idx . "_element", $element);
					$this->set("reponse_" . $idx . "_element_associe", $elementAssocie);
					$this->set("reponse_" . $idx . "_masque", $masque);
					$this->set("reponse_" . $idx . "_retroaction", $retro);
					$this->set("reponse_" . $idx . "_retroaction_negative", $retroNeg);
					$this->set("reponse_" . $idx . "_retroaction_incomplete", $retroIncomp);
					$this->set("reponse_" . $idx . "_coordonnee_x", $coordX);
					$this->set("reponse_" . $idx . "_coordonnee_y", $coordY);
					$this->set("reponse_" . $idx . "_statut", $statut);
					$this->set("reponse_" . $idx . "_reponse", $reponse);
				} else {
					$this->delete("reponse_" . $idx . "_element");
					$this->delete("reponse_" . $idx . "_element_associe");
					$this->delete("reponse_" . $idx . "_masque");
					$this->delete("reponse_" . $idx . "_retroaction");
					$this->delete("reponse_" . $idx . "_retroaction_negative");
					$this->delete("reponse_" . $idx . "_retroaction_incomplete");
					$this->delete("reponse_" . $idx . "_coordonnee_x");
					$this->delete("reponse_" . $idx . "_coordonnee_y");
					$this->delete("reponse_" . $idx . "_statut");
					$this->delete("reponse_" . $idx . "_reponse");
				}
				$idx++;
			}
		}
		
		// Ajuster la réponse
		$reponse = $this->get("reponse_choix");
		if ($reponse == $position) {
			$this->set("reponse_choix", "");
		}
		
		if ($reponse > $position) {
			$reponse--;
			$this->set("reponse_choix", $reponse);
		}
		
		$this->log->debug("Item::supprimerElement() Fin");
		return;
	}			

	
	/**
	 * 
	 * Obtenir le type d'item sous forme textuel
	 *
	 */
	public function getTypeItemTxt() {

		$this->log->debug("Item::getTypeItemTxt() Début");
		$txt = "";
		
		if ($this->get("type_item") == "1") {
			$txt = TXT_ASSOCIATIONS;
		} elseif ($this->get("type_item") == "2") {
			$txt = TXT_CHOIX_MULTIPLES;
		} elseif ($this->get("type_item") == "3") {
			$txt = TXT_CLASSEMENT;
		} elseif ($this->get("type_item") == "4") {
			$txt = TXT_DAMIER;
		} elseif ($this->get("type_item") == "5") {
			$txt = TXT_DEVELOPPEMENT;
		} elseif ($this->get("type_item") == "6") {
			$txt = TXT_DICTEE;
		} elseif ($this->get("type_item") == "7") {
			$txt = TXT_MARQUAGE;
		} elseif ($this->get("type_item") == "8") {
			$txt = TXT_MISE_EN_ORDRE;
		} elseif ($this->get("type_item") == "9") {
			$txt = TXT_REPONSE_BREVE;
		} elseif ($this->get("type_item") == "10") {
			$txt = TXT_REPONSES_MULTIPLES;
		} elseif ($this->get("type_item") == "11") {
			$txt = TXT_TEXTE_LACUNAIRE;
		} elseif ($this->get("type_item") == "12") {
			$txt = TXT_VRAI_OU_FAUX;
		} elseif ($this->get("type_item") == "13") {
			$txt = TXT_ZONES_A_IDENTIFIER;
		} elseif ($this->get("type_item") == "14") {
			$txt = TXT_PAGE;
		} elseif ($this->get("type_item") == "15") {
			$txt = TXT_SECTION;
		}
		
		$this->log->debug("Item::getTypeItemTxt() Fin");
		
		return $txt;
	}		

	/**
	 * 
	 * Obtenir le type d'éléments 1 sous forme textuel
	 *
	 */
	public function getTypeElements1Txt() {

		$this->log->debug("Item::getTypeElements1Txt() Début");
		$txt = "";
		
		if ($this->get("type_elements1") == "texte") {
			$txt = TXT_TEXTE;
		} elseif ($this->get("type_elements1") == "image") {
			$txt = TXT_IMAGE;
		}
		
		$this->log->debug("Item::getTypeElements1Txt() Fin");
		
		return $txt;
	}

	/**
	 * 
	 * Obtenir le type d'éléments 2 sous forme textuel
	 *
	 */
	public function getTypeElements2Txt() {

		$this->log->debug("Item::getTypeElements2Txt() Début");
		$txt = "";
		
		if ($this->get("type_elements2") == "texte") {
			$txt = TXT_TEXTE;
		} elseif ($this->get("type_elements2") == "image") {
			$txt = TXT_IMAGE;
		}
		
		$this->log->debug("Item::getTypeElements2Txt() Fin");
		
		return $txt;
	}		
	
	/**
	 * 
	 * Exporter un item en format XML
	 * @param Projet projet
	 * @param Usager usager
	 * @param array Liste d'items
	 *
	 */
	public function exporterItemXML($projet, $usager, $listeItems) {

		$this->log->debug("Item::exporterXML() Début");
		
		$succes = 0;
		$urlFichierZip = "";
		$contenu = "";
		$listeMedias = array();

		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomBase = FICHIER_EXPORTATION_XML_ITEMS;
		
		// Vérifier le nombre d'items à exporter et ajouter un "s" au besoin
		if (count($listeItems) > 1) {
			$nomBase =  $nomBase . "s";
		}
		
		$nomRepertoireZip = Securite::nettoyerNomfichier($nomBase) . "_" . $ts . "_xml"; 
		$nomFichierZip = $nomRepertoireZip . ".zip";
		$urlFichierZip = URL_PUBLICATION . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomFichierZip;
		
		// Déterminer le répertoire de publication du XML
		$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/";
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";

		// Questionnaire par défaut
		$quest = new Questionnaire($this->log, $this->dbh);
		
		// Vérifier que le répertoire de destination n'existe pas
		if (!is_dir($repertoireDestination)) {
			
			$this->log->debug("Questionnaire:exporterXML() Exportation de l'item");

			// Exporter l'item	
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes) {
				// Créer le répertoire média
				$succes = $publication->creerRepertoireMedia($repertoireDestination);
			}
	
			// Entête XML
			$contenu .= XML_ENTETE . "\n";
			$contenu .= XML_NQW_DEBUT . "\n";
			
			// Obtenir la liste des items en XML
			foreach ($listeItems as $idItem) {
				$itemFactory = new Item($this->log, $this->dbh);
				$item = $itemFactory->instancierItemParType('', $projet->get("id_projet"), $idItem);

				// Déterminer la langue pour l'aperçu
				$langue = $item->getLangueApercuObj();
				
				// Obtenir le contenu XML
				$contenu .= $item->exporterXML($langue, $repertoireDestination, $quest);
								
				// Obtenir la liste des médias
				$listeMedias = $item->ajouterMediaListeExportation($listeMedias);
			}

			// Préparer la listes des médias
			$media = new Media($this->log, $this->dbh);
			$contenuMedia = $media->exporterListeMediasXML($listeMedias, $projet->get("id_projet"));
			
			// Ajouter les médias
			$contenu .= $contenuMedia;
			
			// Fin du fichier XML
			$contenu .= XML_NQW_FIN . "\n";
			
			// Écrire le contenu dans un fichier XML
			$publication->ecrireFichier($repertoireDestination . FICHIER_EXPORTATION_XML, $contenu);
			
		} else {
			$this->log->debug("Item::exporterXML() Impossible de publier l'item - le répertoire existe déjà");
		}		
			
		if ($succes == 1) {
			// Déterminer le répertoire source
			$repertoireSourceZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";
			$repertoireDestinationZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU;
					
			// Préparer le fichier zip
			$fichierZip = $repertoireDestinationZip . $nomFichierZip;
			
			// Zip des fichiers
			Fichiers::Zip($repertoireSourceZip, $fichierZip);
			
			// Supprimer le répertoire temporaire
			$this->log->debug("Item::exporterXML() Suppression du répertoire '$repertoireSourceZip'");
			Fichiers::rmdirr($repertoireSourceZip);
		} else {
			$urlFichierZip = "";
		}
		
		$this->log->debug("Item::exporterXML() Fin");
		return $urlFichierZip;
	}		

	/**
	 * 
	 * Exporter les valeurs pour ce questionnaires seulement en format XML
	 * @param string langue
	 * @param string répertoire destination
	 * @param Questionnaire questionnaire courant si disponible 
	 *
	 */
	public function exporterXMLQuestItem($langue, $repertoireDestination, $quest) {
		
		// Récupérer le gabarit pour publier une Developpement dans un questionnaire
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "questionnaire-item.php", $this, $langue);
		
		return $contenu;
	}		
	
	
	/**
	 * 
	 * Vérifier le champ pondération
	 * 
	 */
	public function verifierPonderation() {
	
		$this->log->debug("Item::verifierPonderation() Début");
		
		$messages = "";
		$errPonderationItem = 0;
		
		// Vérifier que le champ pondération du questionnaire est numérique
		$ponderationQuest = $this->get("ponderation_quest");
		$ponderationQuest = str_replace(',', '.', $ponderationQuest);

		// Vérifier que le champ pondération de l'item est numérique
		$ponderationItem = $this->get("ponderation");
		$ponderationItem = str_replace(',', '.', $ponderationItem);

		if ($ponderationItem != "" && ! is_numeric($ponderationItem)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_037 . HTML_LISTE_ERREUR_FIN;
			$errPonderationItem++;
		}
				
		if ($errPonderationItem == 0 || $ponderationQuest != $ponderationItem ) {
			
			if ($ponderationQuest != "" && ! is_numeric($ponderationQuest)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_038 . HTML_LISTE_ERREUR_FIN;
			}
		}
		
		$this->log->debug("Item::verifierPonderation() Début");
		
		return $messages;
	}
			
	
	/**
	 * 
	 * Vérifier le champ Points retranchés
	 * 
	 */
	public function verifierPointsRetranches() {
	
		$this->log->debug("Item::verifierPointsRetranches() Début");
		
		$messages = "";
		$errPointsRetranchesItem = 0;
		
		// Vérifier que le champ points retranchés du questionnaire est numérique
		$pointsRetranchesQuest = $this->get("points_retranches_quest");
		$pointsRetranchesQuest = str_replace(',', '.', $pointsRetranchesQuest);

		// Vérifier que le champ points retranchés de l'item est numérique
		$pointsRetranchesItem = $this->get("points_retranches");
		$pointsRetranchesItem = str_replace(',', '.', $pointsRetranchesItem);
		
		if ($pointsRetranchesItem != "" && ! is_numeric($pointsRetranchesItem)) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_140 . HTML_LISTE_ERREUR_FIN;
			$errPointsRetranchesItem++;
		}		
		
		if ($errPointsRetranchesItem == 0 || $pointsRetranchesQuest != $pointsRetranchesItem ) {
			if ($pointsRetranchesQuest != "" && $pointsRetranchesQuest != $pointsRetranchesItem && ! is_numeric($pointsRetranchesQuest)) {
				$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_142 . HTML_LISTE_ERREUR_FIN;
			}
		}
	
		// Vérifier le champ Pondération à utiliser pour la vérification
		$ponderation = "";
		if ($this->get("ponderation_quest") != "") {
			$ponderation = $this->get("ponderation_quest");
		} else {
			$ponderation = $this->get("ponderation");
		}
		
		// Vérifier le champ points retranchés à utiliser
		$pointsRetranches = "";
		if ($this->get("points_retranches_quest") != "") {
			$pointsRetranches = $this->get("points_retranches_quest");
		} else {
			$pointsRetranches = $this->get("points_retranches");
		}		
		
		// Vérifier que le nombre de points retranchés est > pondération
		if ($pointsRetranches != "" && is_numeric($pointsRetranches) && $pointsRetranches > $ponderation) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_141 . HTML_LISTE_ERREUR_FIN;
		}
		
		$this->log->debug("Item::verifierPointsRetranches() Début");
		
		return $messages;
	}	
	
	
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Item::reindexer() Début ");
	
		$nbMAJ = 0;
		
		try {
			$sql = "SELECT 	id_item, id_projet
					FROM 	titem";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() == 0) {
				$this->log->info("Item::reindexer()  Aucun item localisé");
			} else {
	
				// Récupérer les ids des items et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Récupérer l'id de l'item
					$idProjet = $row['id_projet'];
					$idItem = $row['id_item'];
	
					// Obtenir l'item
					$item = new Item($this->log, $this->dbh);
					$i = $item->instancierItemParType('', $idProjet, $idItem);
					
					// Réindexer
					$this->log->info("Item::reindexer()  Indexation pour l'item '$idItem' et projet '$idProjet'");
					$i->indexer();
					$this->log->info("Item::reindexer()  Indexation complétée pour l'item '$idItem'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Item::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Item::reindexer() Fin");
		return $nbMAJ;
	}	
	
	/**
	 *
	 * Obtenir le titre de la catégorie
	 * 
	 */
	public function getCategorieTitre() {
	
		$this->log->debug("Item::getCategorieTitre() Début");
		
		// Obtenir le titre de la catégorie
		$categorie = new Categorie($this->log, $this->dbh);
		$categorie->getCategorieParId($this->get("id_categorie"), $this->get("id_projet"));
		
		$this->log->debug("Item::getCategorieTitre() Fin");

		return $categorie->get("titre");
	}	
	
	/**
	 * 
	 * Obtenir une valeur
	 * @param String valeur
	 * 
	 */
	public function get( $valeur ) {
		
		$val = "";
		if (isset($this->donnees[$valeur])) {
			$val = $this->donnees[$valeur];
		}
		
		return $val;
	}
	
	/**
	 * 
	 * Écrire une valeur
	 * @param String libelle
	 * @param String valeur
	 * 
	 */
	public function set( $libelle, $valeur ) {
		$this->donnees[$libelle] = $valeur;
	}

	/**
	 * 
	 * Supprimer une valeur
	 * @param String libelle
	 * 
	 */
	public function delete( $libelle ) {
		unset ($this->donnees[$libelle]);
	}	
	
	/**
	 *
	 * Supprimer les valeurs qui débutent par un prefix
	 * @param String libellé
	 *
	 */
	public function deleteByPrefix( $libelle ) {
		
		// Obtenir les clés
		$cles = array_keys($this->donnees);
		 
		// Parcourir la liste de clés
		foreach ($cles as $cle) {
		
			// Supprimer la clé si match
			if (substr($cle, 0, strlen($libelle)) == $libelle) {
				unset($this->donnees[$cle]);
			}
		}
	}	
	
	/**
	 * 
	 * Obtenir une valeur pour impression
	 * @param String valeur
	 * @param String nbLigne
	 * 
	 */
	public function getImpression( $valeur, $nbLigne = 2 ) {
		
		$val = "";
		
		if (isset($this->donnees[$valeur]) && $this->donnees[$valeur] != "") {
			if ($nbLigne == 1) {
				$val = IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . html_entity_decode($this->donnees[$valeur], ENT_QUOTES, "UTF-8") . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;
			} elseif ($nbLigne == 2) {
				$val = IMPRESSION_HTML_PREFIX_VALEUR_DEUX_LIGNES . html_entity_decode($this->donnees[$valeur], ENT_QUOTES, "UTF-8") . IMPRESSION_HTML_SUFFIXE_VALEUR_DEUX_LIGNES;
			}
		} else {
			$val = IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . IMPRESSION_HTML_AUCUNE_VALEUR . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;
		}
		
		return $val;
	}	

	/**
	 * 
	 * Obtenir une valeur pour javascript
	 * @param String valeur
	 * 
	 */
	public function getJS( $valeur ) {
		
		// Par défaut utiliser la clé demandée
		$val = $valeur;
		
		// Vérifier si une version pour publication est disponible (avec des médias),
		// si oui, utiliser cette version
		$valeurPub = $valeur . "_pub";
		
		if (isset($this->donnees[$valeurPub])) {
			$val = $valeurPub;
		}
		
		return Web::nettoyerChainePourJs($this->get($val));
	}
	
	/**
	 * 
	 * Obtenir une valeur pour du XML
	 * @param String valeur
	 *  
	 */
	public function getXML( $valeur ) {
		return Web::nettoyerChainePourXML($this->get($valeur));
	}	
	
}

?>
