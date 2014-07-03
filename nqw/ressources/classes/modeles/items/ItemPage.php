<?php

/** 
 * Classe Item - Page
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class ItemPage extends Item  {
	
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

		$log->debug("ItemPage::__construct() Début");
		$log->debug("ItemPage::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		$log->debug("ItemPage::__construct() Fin");
		
		return;
	}

	
	/**
	 * 
	 * Charger la Page à partir de la base de données
	 * 
	 * @param String idItem
	 * @param String idProjet
	 * 
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("ItemPage::getItemParId() Début idItem = '$idItem'  idProjet = '$idProjet'");

		// Récupérer les données communes
		$trouve = parent::getItemParId($idItem, $idProjet);
		
		// Terminé
		$this->log->debug("ItemPage::getItemParId() Trouve = '$trouve'");
		$this->log->debug("ItemPage::getItemParId() Fin");
		return $trouve;		
	}

	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'une Page
	 * 
	 */
	public function ajouter() {

		$this->log->debug("ItemPage::ajouter() Début");

		// Enregistrement des informations de base
		parent::ajouter();
		
		// Mettre à jour l'index
		$this->indexer();
				
		return;
	}
	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une Page
	 *
	 */
	public function enregistrer() {

		$this->log->debug("ItemPage::enregistrer() Début");

		// Enregistrement des informations de base
		parent::enregistrer();		
		
		// Mettre à jour l'index
		$this->indexer();
				
		$this->log->debug("ItemPage::enregistrer() Fin");
								
		return;
	}	

	/**
	 * 
	 * Valider la page
	 * @param Questionnaire $quest
	 * 
	 */
	public function valider($quest) {

		$this->log->debug("ItemPage::valider() Début");

		$succes = 0;
		$messages = "";

		// Vérifier que le texte n'est pas vide
		if (trim($this->get("enonce")) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_127 . HTML_LISTE_ERREUR_FIN;
		}
		
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

		$this->log->debug("ItemPage::valider() Fin");
		
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

		
		$this->log->debug("ItemPage::publier() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier un item à choix multiples
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-page.php", $this, $langue);
		
		$this->log->debug("ItemPage::publier() Fin");
		
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
				
		$this->log->debug("ItemPage::exporterXML() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);		
		
		// Récupérer le gabarit pour publier un item à choix multiples
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-page.php", $this, $langue);
		
		$this->log->debug("ItemPage::exporterXML() Fin");
		
		return $contenu;
	}	
	
	
	/**
	 * 
	 * Exporter les valeurs pour ce questionnaires seulement en format XML
	 * 
	 * @param string langue
	 * @param string répertoire destination
	 * @param Questionnaire questionnaire courant si disponible 
	 *
	 */
	public function exporterXMLQuestItem($langue, $repertoireDestination, $quest) {
		
		$this->log->debug("ItemPage::exporterXMLQuestItem() Début");
		
		// Récupérer le gabarit pour publier une Page dans un questionnaire
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "questionnaire-page.php", $this, $langue);
		
		$this->log->debug("ItemPage::exporterXMLQuestItem() Fin");
		
		return $contenu;
	}	
		
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemPage::indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();
		
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemPage::indexer() Fin");
	}		
	
}

?>
