<?php

/** 
 * Classe Item - Dictee
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class ItemDictee extends Item  {
	
	protected $dbh;
	protected $log;
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

		$log->debug("ItemDictee::__construct() Début");
		$log->debug("ItemDictee::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		$log->debug("ItemDictee::__construct() Fin");
		
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
	
		$this->log->debug("ItemDictee::instancierTypeItem() Début typeItem = '$typeItem'");

		// Préparation globale
		parent::preparerValeursInitiales($typeItem, $projet, $usager);
		
		$this->set("points_retranches", DICTEE_NB_POINTS_RETRANCHES);
			
		$this->log->debug("ItemDictee::instancierTypeItem() Fin");
	}	
	
	
	/**
	 * 
	 * Charger la dictée à partir de la base de données
	 * 
	 * @param String idItem
	 * @param String idProjet
	 * 
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("ItemDictee::getItemParId() Début idItem = '$idItem'  idProjet = '$idProjet'");

		// Récupérer les données communes
		$trouve = parent::getItemParId($idItem, $idProjet);
		
		// Terminé
		$this->log->debug("ItemDictee::getItemParId() Trouve = '$trouve'");
		$this->log->debug("ItemDictee::getItemParId() Fin");
		return $trouve;		
	}

	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'une Dictee
	 * 
	 */
	public function ajouter() {

		$this->log->debug("ItemDictee::ajouter() Début");

		// Enregistrement des informations de base
		parent::ajouter();
		
		// Mettre à jour l'index
		$this->indexer();
				
		return;
	}
	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une Dictee
	 *
	 */
	public function enregistrer() {

		$this->log->debug("ItemDictee::enregistrer() Début");

		
		// Au besoin remplacer la , par un .
		$pointsRetranches = str_replace(',', '.', $this->get("points_retranches"));
		$pointsRetranches = floatval($pointsRetranches);
		
		// Vérifier qu'une valeur est spécifiée pour le nombre de points retranchés
		if ($pointsRetranches == "") { 
			$pointsRetranches = DICTEE_NB_POINTS_RETRANCHES;
		}
		$this->set("points_retranches", $pointsRetranches);
		
		// Enregistrement des informations de base
		parent::enregistrer();		
		
		// Mettre à jour l'index
		$this->indexer();
				
		$this->log->debug("ItemDictee::enregistrer() Fin");
								
		return;
	}	

	/**
	 * 
	 * Valider l'item
	 * @param Questionnaire $quest
	 *
	 */
	public function valider($quest) {
		
		$this->log->debug("ItemDictee::valider() Début");

		$succes = 0;
		$messages = "";

		// Vérifier que le champ solution n'est pas vide
		if (trim($this->get("solution")) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_139 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier la pondération (doit être numérique)
		$messages .= parent::verifierPonderation();

		// Vérifier les points retranchés
		$messages .= parent::verifierPointsRetranches();
		
		// Vérifier si le thème est valide
		$messages .= parent::verifierTheme($quest);
		
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
						
		$this->log->debug("ItemDictee::valider() Fin");
		
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

		$this->log->debug("ItemDictee::publier() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier un item à choix multiples
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-dictee.php", $this, $langue);
		
		$this->log->debug("ItemDictee::publier() Fin");
		
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
				
		$this->log->debug("ItemDictee::exporterXML() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);
		
		// Récupérer le gabarit pour publier un item à choix multiples
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-dictee.php", $this, $langue);
		
		$this->log->debug("ItemDictee::exporterXML() Fin");
		
		return $contenu;
	}	
	
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemDictee::indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();
		
		// Éléments propres à une dictée
		$index .= $this->get("titre") . " ";
		$index .= $this->get("enonce"). " ";
		$index .= $this->get("solution"). " ";
		$index .= $this->get("retroaction_positive"). " ";
		$index .= $this->get("retroaction_negative"). " ";
		
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemDictee::indexer() Fin");
	}		
}

?>
