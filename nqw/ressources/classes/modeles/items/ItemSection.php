<?php

/** 
 * Classe Item - Section
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class ItemSection extends Item  {
	
	protected $dbh;
	protected $log;
	
	protected $listeChampsSection = "id_item_section, id_item, id_projet, generation_question_type, date_creation, date_modification";
							  
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

		$log->debug("ItemSection::__construct() Début");
		$log->debug("ItemSection::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		$log->debug("ItemSection::__construct() Fin");
		
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
	
		$this->log->debug("ItemSection::instancierTypeItem() Début typeItem = '$typeItem'");
	
		// Préparation globale
		parent::preparerValeursInitiales($typeItem, $projet, $usager);
	
		// Préparation pour cet item
		$this->set("titre", TXT_NOUVELLE_SECTION);
			
		$this->log->debug("ItemSection::instancierTypeItem() Fin");
	}	
	
	/**
	 * 
	 * Charger la section à partir de la base de données
	 * 
	 * @param String idItem
	 * @param String idProjet
	 * 
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("ItemSection::getItemParId() Début idItem = '$idItem'  idProjet = '$idProjet'");
		$trouve = false;

		try {
			$sql = "SELECT " . $this->listeChampsSection . " from titem_section where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
			
			// Vérifier qu'on a trouvé au moins une section	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune section trouvée pour l'item '$idItem'");
			}
			
			// Vérifier qu'une seule section est retournée, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('011', "La recherche pour la section id '$idItem' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour la section
				$result = $sth->fetchAll();
			
			    foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->donnees[$cle] = $row[$cle];
				    		//echo "Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
		        
		        // Indiquer qu'une et une seule section a été trouvée
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemSection::getItemParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Récupérer les données communes
		parent::getItemParId($idItem, $idProjet);
		
		// Terminé
		$this->log->debug("ItemSection::getItemParId() Trouve = '$trouve'");
		$this->log->debug("ItemSection::getItemParId() Fin");
		return $trouve;		
	}

	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'une section
	 * 
	 */
	public function ajouter() {

		$this->log->debug("ItemSection::ajouter() Début");

		// Obtenir le prochain id de section
		$projet = new Projet($this->log, $this->dbh);
		$projet->getProjetParId($this->get("id_projet"));
		$idItemSection = $projet->genererIdSection();
		$this->set("id_item_section", $idItemSection);
		
		// Enregistrement des informations de base
		parent::ajouter();
		
		try {
			$stmt = $this->dbh->prepare("insert into titem_section (id_item_section, id_item, id_projet, generation_question_type, date_creation, date_modification) 
										 values (?,?,?,?,now(),now() )");
	
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_item_section'),
								 $this->get('id_item'),
								 $this->get('id_projet'),
								 $this->get('generation_question_type') 
								 ));

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemSection::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Mettre à jour l'index
		$this->indexer();	

		$this->log->debug("ItemSection::ajouter() Fin");		
		
		return;
	}
	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une section
	 *
	 */
	public function enregistrer() {

		$this->log->debug("ItemSection::enregistrer() Début");

		// Enregistrement des informations de base
		parent::enregistrer();		
		
		try {
			$stmt = $this->dbh->prepare("update titem_section
										set 
											generation_question_type=?, 
								  		 	date_modification=now()										
											
											where id_item_section = ?									 
											and id_projet = ?");
	
			// insertion d'une ligne
			$stmt->execute( array(	$this->get('generation_question_type'), 
									$this->get('id_item_section'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemSection::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}									

		// Mettre à jour l'index
		$this->indexer();									
								
		$this->log->debug("ItemSection::enregistrer() Fin");
								
		return;
	}	

	/**
	 * 
	 * Valider la section
	 * 
	 * @param Questionnaire $quest
	 *
	 */
	public function valider($quest) {

		$this->log->debug("ItemSection::valider() Début");

		$messages = "";

		// Vérifier que le titre n'est pas vide
		if (trim($this->get("titre")) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_033 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier si le thème est valide
		$messages .= parent::verifierTheme($quest);		
		
		return $messages;
	}	
	
	/**
	 * 
	 * Obtenir la liste des items d'une section
	 * 
	 * @param String idSection
	 * @param String idQuestionnaire
	 * @param String idProjet
	 * 
	 */
	public function getItemsSection($idSection, $idQuestionnaire, $idProjet) {

		$this->log->debug("ItemSection::getItemsSection() Début  idSection = '$idSection'  idQuestionnaire = '$idQuestionnaire'  idProjet = '$idProjet'");
		$trouve = false;
		$listeItems = array();

		try {
			$sql = "SELECT id_item from tquestionnaire_item where section = ? and id_questionnaire = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idSection, $idQuestionnaire, $idProjet));
			
			// Vérifier qu'on a trouvé au moins une section	
			if ($sth->rowCount() > 0) {
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				array_push($listeItems, $row['id_item']);	
					}		
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemSection::getItemsSection() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Terminé
		$this->log->debug("ItemSection::getSectionParId() Fin");
		return $listeItems;		
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
		
		$this->log->debug("ItemSection::exporterXML() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);		
		
		// Récupérer le gabarit pour publier un item à choix multiples
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-section.php", $this, $langue);
		
		$this->log->debug("ItemSection::exporterXML() Fin");
		
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
		
		$this->log->debug("ItemSection::exporterXMLQuestItem() Début");
		
		// Récupérer le gabarit pour publier une section dans un questionnaire
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "questionnaire-section.php", $this, $langue);
		
		$this->log->debug("ItemSection::exporterXMLQuestItem() Fin");
		
		return $contenu;
	}	
	
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemSection::indexer() Début");
		
		// Éléments propres à une section
		$index = $this->get("titre");
		
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemSection::indexer() Fin");
	}		
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("ItemSection::preparerAffichage() Début");
		
		// Appel de la méthode parent
		parent::preparerAffichage();

		// Select
		$this->set('generation_question_type_' . $this->get('generation_question_type'), HTML_SELECTED);
			
		$this->log->debug("ItemSection::preparerAffichage() Fin");
	}	
	
	
	/**
	 * 
	 * Obtenir le type de génération de question en format texte 
	 *
	 */
	public function getGenerationQuestionTxt() {

		$this->log->debug("ItemSection::getGenerationQuestionTxt() Début");

		$txt = "";
		
		if ($this->get("generation_question_type") == "section" || $this->get("generation_question_type") == "") {
			$txt = TXT_SELON_PARAMETRE_SECTION;
		} elseif ($this->get("generation_question_type") == "aleatoire") {
			$txt = TXT_ORDRE_ALEATOIRE;
		} elseif ($this->get("generation_question_type") == "predetermine") {
			$txt = TXT_ORDRE_PREDETERMINE;
		}
			
		$this->log->debug("ItemSection::getGenerationQuestionTxt() Fin");
		
		return $txt;
	}	
	
}

?>
