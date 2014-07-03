<?php

/** 
 * Classe Item - Zones à identifier
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

class ItemZonesIdentifier extends Item  {
	
	protected $dbh;
	protected $log;
						  
	protected $listeChampsItemZonesIdentifier = "id_item_reponse, id_item, id_projet, element, retroaction, retroaction_negative, coordonnee_x, coordonnee_y, ordre, date_creation, date_modification";
	
	protected $donnees;
	
	/**
	 * 
	 * Constructeur
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 * 
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$log->debug("ItemZonesIdentifier::__construct() Début");
		$log->debug("ItemZonesIdentifier::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		
		$this->listeLacunes = array();
		
		$log->debug("ItemZonesIdentifier::__construct() Fin");
				
		return;
	}

	/**
	 *
	 * Configurer les valeurs par défaut
	 * 
	 * @param string typeItem
	 * @param Projet projet
	 * @param Usager usager
	 *
	 */
	public function preparerValeursInitiales($typeItem, $projet, $usager) {
	
		$this->log->debug("ItemDamier::instancierTypeItem() Début typeItem = '$typeItem'");
	
		// Préparation globale
		parent::preparerValeursInitiales($typeItem, $projet, $usager);
	
		// Préparation pour cet item
			
		$this->log->debug("ItemDamier::instancierTypeItem() Fin");
	}	
	
	/**
	 *
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {
	
		$this->log->debug("ItemZonesIdentifier::getDonneesRequete() Début");
	
		// Appel méthode parent
		parent::getDonneesRequete();
	
		$this->log->debug("Item::getDonneesRequete() Fin");
	
		return;
	}	

	
	/**
	 *
	 * Obtenir les informations sur l'item
	 * 
	 * @param String idItem
	 * @param String idProjet
	 *
	 */
	public function getItemParId($idItem, $idProjet) {
	
		$this->log->debug("ItemZonesIdentifier::getItemsParId() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT " . $this->listeChampsItemZonesIdentifier . " from titem_reponse where id_item = ? and id_projet = ? order by ordre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
				
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun item trouvé pour l'id '$idItem'");
			}
				
			// Vérifier qu'au moins un item est retourné, sinon erreur
			else {
				// Récupérer les informations pour l'item
				$result = $sth->fetchAll();
					
				$idx = 0;
				foreach($result as $row) {
					$idx++;
					$cles = array_keys($row);
	
					foreach ($cles as $cle) {
						 
						// Obtenir chaque champ
						if (! is_numeric($cle) ) {
							$cleComplete = "reponse_" . $idx . "_$cle";
							$this->donnees[$cleComplete] = $row[$cle];
							//echo "load Cle : '$cle' Clé complète : '$cleComplete'  Valeur = '" . $row[$cle] . "'\n";
	
							// Noter la réponse
							if ($cle == "reponse" && $row[$cle] == 1) {
								$this->set("reponse_choix", $idx);
							}
	
						}
					}
				}
	

				// Noter le nombre de choix
				$this->set("reponse_total", $idx);				
				
				// Indiquer qu'un et un seul item a été trouvé
				$trouve = true;
			}
	
			} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemZonesIdentifier::getItemParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		parent::getItemParId($idItem, $idProjet);
		
		$this->verifierImage();
	
		// Terminé
		$this->log->debug("ItemZonesIdentifier::getItemParId() Trouve = '$trouve'");
		$this->log->debug("ItemZonesIdentifier::getItemParId() Fin");
		return $trouve;
	}	
	
	
	/**
	 *
	 * Vérifier que l'image existe toujours, tant dans la bd que sur disque
	 *
	 */
	public function verifierImage() {	
		
		$this->log->debug("ItemZonesIdentifier::verifierImage() Début");
		
		$trouve = false;
		
		// Obtenir l'id de l'image
		$idMedia = $this->get("image");
		$this->log->debug("ItemZonesIdentifier::verifierImage() idMedia = '$idMedia'");
		
		// Vérifier qu'une image est sélectionnée
		if ($idMedia != 0) {

			// Vérifier si l'image existe dans la BD
			$m = new Media($this->log, $this->dbh);
			$m->getMediaParId($idMedia, $this->get("id_projet"));
			
			// Vérifier si le média est actif
			if ($m->get("statut") == "1") {
			
				// Obtenir le nom de fichier
				$fichier = $m->get("fichier");
				$this->log->debug("ItemZonesIdentifier::verifierImage() fichier : '$fichier'");
				
				// Vérifier que le nom de fichier n'est pas vide
				if ($fichier != "") {
					
					// Obtenir le répertoire du projet
					$p = new Projet($this->log, $this->dbh);
					$p->getProjetParId($this->get("id_projet"));
					$repertoireProjet = $p->get("repertoire");
					$this->log->debug("ItemZonesIdentifier::verifierImage() Répertoire Projet : '$repertoireProjet'");
					
					// Obtenir le répertoire complet pour le fichier
					$fichierComplet = REPERTOIRE_MEDIA . $repertoireProjet . "/" . $fichier;
					
					// Vérifier que le fichier existe
					$this->log->debug("ItemZonesIdentifier::verifierImage() Vérifier que le fichier '$fichierComplet' existe");
					if (file_exists($fichierComplet)) {
						$trouve = true;
						$this->log->debug("ItemZonesIdentifier::verifierImage() Le fichier existe");
					} else {
						$this->log->debug("ItemZonesIdentifier::verifierImage() Le fichier n'existe pas");
					}
				}
			} else {
				$this->log->debug("ItemZonesIdentifier::verifierImage() Le média existe mais n'est pas actif");
			}
		}
		
		// Si le fichier n'a pas été trouvé, réinitialiser le fichier et les positions des zones
		if (! $trouve) {
			
			// Supprimer l'image
			$this->set("image", "0");
		
			// Supprimer les positions des zones
			for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
			
				// Obtenir les valeurs
				$coordX = $this->get("reponse_" . $i . "_coordonnee_x");
				$coordY = $this->get("reponse_" . $i . "_coordonnee_y");

				if ($coordX != "" || $coordX != "0") {
					$this->set("reponse_" . $i . "_coordonnee_x", "");
				}
				
				if ($coordY != "" || $coordY != "0") {
					$this->set("reponse_" . $i . "_coordonnee_y", "");
				}
			}
		}
		
		$this->log->debug("ItemZonesIdentifier::verifierImage() Fin");
		
	}
	
	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Ajout d'un item
	 *
	 */
	public function ajouter() {
	
		$this->log->debug("ItemZonesIdentifier::ajouter() Début");
	
		// Enregistrement des informations de base
		parent::ajouter();
	
		// Obtenir la réponse
		$idxReponse = $this->get("reponse_choix");
	
		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into titem_reponse (id_item, id_projet, element, ordre, retroaction, retroaction_negative, coordonnee_x, coordonnee_y, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now()) ");
	
		// Obtenir la liste des éléments
		$ordre = 0;
		for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
				
			// Obtenir les valeurs
			$val = $this->get("reponse_" . $i . "_element");
			$retroaction = $this->get("reponse_" . $i . "_retroaction");
			$retroactionNegative = $this->get("reponse_" . $i . "_retroaction");
			$coordX = $this->get("reponse_" . $i . "_coordonnee_x");
			$coordY = $this->get("reponse_" . $i . "_coordonnee_y");
			if ($val != "") {
	
				// Incrémenter le compteur pour l'ordre d'affichage
				$ordre++;
	
				// Ajouter l'élément à la BD
				$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet"), $val, $ordre, $retroaction, $retroactionNegative, $coordX, $coordY));
			}
				
		}
	
		// Mettre à jour l'index
		$this->indexer();
	
		$this->log->debug("ItemZonesIdentifier::ajouter() Fin");
	
		return;
	}
			
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un item
	 *
	 */
	public function enregistrer() {

		$this->log->debug("ItemZonesIdentifier::enregistrer() Début");

		// Enregistrement des informations de base
		parent::enregistrer();
				
		// Supprimer les informations d'item
		$this->supprimerReponsesZonesIdentifier($this->get("id_item"), $this->get("id_projet"));
		
		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into titem_reponse (id_item, id_projet, element, ordre, retroaction, retroaction_negative, coordonnee_x, coordonnee_y, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now()) ");
				
		
		// Obtenir la liste des éléments
		$ordre = 0;
		for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
			
			// Obtenir les valeurs
			$val = $this->get("reponse_" . $i . "_element");
			$retroaction = $this->get("reponse_" . $i . "_retroaction");
			$retroaction_negative = $this->get("reponse_" . $i . "_retroaction_negative");
			$coordX = $this->get("reponse_" . $i . "_coordonnee_x");
			$coordY = $this->get("reponse_" . $i . "_coordonnee_y");
			
			if ($val != "" || $retroaction != "" || $coordX != "" || $coordY != "") {
				
				// Incrémenter le compteur pour l'ordre d'affichage
				$ordre++;
				
				$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet"), $val, $ordre, $retroaction, $retroaction_negative, $coordX, $coordY));
			}
		}
		
		// Mettre à jour l'index
		$this->indexer();		
								
		return;
	}	
	
	
	/**
	 *
	 * Supprimer les informations sur les réponses
	 *
	 */
	public function supprimerReponsesZonesIdentifier($idItem, $idProjet) {
	
		$this->log->debug("ItemZonesIdentifier::supprimer() Début  (idItem = '" . $idItem . "' idProjet = '" . $idProjet . "')");
	
		try {
			$sql = "delete from titem_reponse where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemZonesIdentifier::supprimerReponsesMultiples() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("ItemZonesIdentifier::supprimer() Fin");
		return;
	}	
	
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemZonesIdentifier: indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();

		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemZonesIdentifier: indexer() Fin");
	}	
	
	
	/**
	 * 
	 * Valider l'item
	 * 
	 * @param Questionnaire $quest 
	 *
	 */
	public function valider($quest) {

		$this->log->debug("ItemZonesIdentifier::valider() Début");
		
		$messages = "";
		$succes = 0;
		
		// Vérifier si le thème est valide
		$messages .= parent::verifierTheme($quest);
		
		// Vérifier si une image est présente
		if ($this->get("image") == 0) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_173 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Parcourir la liste des zones
		$totalZones = 0;
		$totalZonesNonPositionnes = 0;
		$positionsZones = array();
		$totalCollisionsPositions = 0;

		for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
				
			// Obtenir les valeurs
			$val = $this->get("reponse_" . $i . "_element");
			$retroaction = $this->get("reponse_" . $i . "_retroaction");
			$retroaction_negative = $this->get("reponse_" . $i . "_retroaction_negative");
			$coordX = $this->get("reponse_" . $i . "_coordonnee_x");
			$coordY = $this->get("reponse_" . $i . "_coordonnee_y");
			
			if (($val != "" && $val != "0") || $retroaction != "" || $retroaction_negative != "" ) {
				$totalZones++;
				
				// Vérifier les zones non positionnées
				if (($coordX == "0" && $coordY == "0") || ($coordX == "" && $coordY == "")) {
					$totalZonesNonPositionnes++;
				}
				
				// Vérifier les collisions de positions
				$positions = $coordX . "," . $coordY;
				
				// Vérifier collision
				if (in_array($positions, $positionsZones)) {
					$totalCollisionsPositions++;
				} else {
					array_push($positionsZones, $positions);
				}
			}
		}

		// Vérifier qu'au moins 2 zones sont présentes
		if ($totalZones < 2) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_174 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier les zones en collisions
		if ($totalCollisionsPositions > 0) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_175 . HTML_LISTE_ERREUR_FIN;
		}
		
		if ($messages != "") {
			
			// Ajouter les messages
			$this->set("apercu_messages", $messages);
					
			// Déterminer gabarit de validation
			$gabaritValidation = REPERTOIRE_GABARITS_VALIDATION . "item-details.php";
			
			// Vérifier si le fichier existe, sinon erreur
			if (!file_exists($gabaritValidation)) {
				$this->log->erreur("Le gabarit de validation '$gabaritValidation' ne peut être localisé.");
			}
			
			// Obtenir le contenu pour validation
			$contenu = Fichiers::getContenuItem($gabaritValidation, $this);
			
			// Ajouter le contenu à la session
			$session = new Session();
			$session->set("apercu_messages", $contenu);
		} else {
			// Tous les critères sont respectés
			$succes = 1;
		}
		
		$this->log->debug("ItemZonesIdentifier::valider() Fin");
		
		return $succes;
	}
	
	
	/**
	 * 
	 * Publier un item
	 * 
	 * @param string langue
	 * @param string répertoire destination
	 * @param Questionnaire questionnaire courant si disponible
	 *
	 */
	public function publier($langue, $repertoireDestination, $quest) {
		
		$this->log->debug("ItemZonesIdentifier::publier() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-zones-identifier.php", $this, $langue);
		
		$this->log->debug("ItemZonesIdentifier::publier() Fin");
		
		return $contenu;
	}

	/**
	 * 
	 * Exporter un item en format XML
	 * 
	 * @param string langue
	 * @param string répertoire destination
	 * @param Questionnaire questionnaire courant si disponible 
	 *
	 */
	public function exporterXML($langue, $repertoireDestination, $quest) {
		
		$this->log->debug("ItemZonesIdentifier::exporterXML() Début");

		// Si un id questionnaire est passé en paramètre, charger les données "pour ce questionnaire seulement"
		if ($quest != null)  {
			$this->getValeursPourQuestionnaire($quest->get("id_questionnaire"), $this->get("id_projet"));
		}
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);
		
		// Récupérer le gabarit pour publier un item
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-zones-identifier.php", $this, $langue);
		
		$this->log->debug("ItemZonesIdentifier::exporterXML() Fin");
		
		return $contenu;
	}	
	
}

?>
