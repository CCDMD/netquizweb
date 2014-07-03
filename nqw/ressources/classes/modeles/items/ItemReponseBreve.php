<?php

require_once '../ressources/classes/outils/Session.php';

/** 
 * Classe Item - Réponse brève
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class ItemReponseBreve extends Item  {
	
	protected $dbh;
	protected $log;
	
	protected $listeChampsItemReponseBreve = "id_item_reponse, id_item, id_projet, reponse, element, retroaction, ordre, date_creation, date_modification";
							  
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

		$log->debug("ItemReponseBreve::__construct() Début");
		$log->debug("ItemReponseBreve::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		$log->debug("ItemReponseBreve::__construct() Fin");
		
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
	
		$this->log->debug("ItemReponseBreve::instancierTypeItem() Début typeItem = '$typeItem'");
	
		// Préparation globale
		parent::preparerValeursInitiales($typeItem, $projet, $usager);
	
		// Si des valeurs existent déjà pour les réponses brèves, enlever l'encodage HTML 
		// car les réponses des réponses brèves ne sont pas en texte enrichi
		for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
				
			// Obtenir les valeurs
			$val = $this->get("reponse_" . $i . "_element");
			
			// Enlever l'encodage HTML
			$val = html_entity_decode($val, ENT_QUOTES, "UTF-8");
			
			// Remettre la valeur
			$this->set("reponse_" . $i . "_element", $val);
		}
			
		$this->log->debug("ItemReponseBreve::instancierTypeItem() Fin");
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

		$this->log->debug("ItemReponseBreve::getItemsParId() Début");
		$trouve = false;

		try {
			$sql = "SELECT " . $this->listeChampsItemReponseBreve . " from titem_reponse where id_item = ? and id_projet = ? order by ordre";
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
			Erreur::erreurFatal('018', "ItemReponseBreve::getItemParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}					

		parent::getItemParId($idItem, $idProjet);
		
		// Terminé
		$this->log->debug("ItemReponseBreve::getItemParId() Trouve = '$trouve'");
		$this->log->debug("ItemReponseBreve::getItemParId() Fin");
		return $trouve;		
	}	

	
	/**
	 * 
	 * Supprimer les informations sur les items
	 * @param String idItem
	 * @param String idUsager
	 *
	 */
	public function supprimerReponseBreve($idItem, $idProjet) {

		$this->log->debug("ItemReponseBreve::supprimer() Début  (idItem = '" . $idItem . "' idProjet = '" . $idProjet . "')");

		try {
			$sql = "delete from titem_reponse where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemReponseBreve::supprimerReponseBreve() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}		

		$this->log->debug("ItemReponseBreve::supprimer() Fin");
		return;
	}		
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Ajout d'un item
	 *
	 */
	public function ajouter() {

		$this->log->debug("ItemReponseBreve::ajouter() Début");

		// Enregistrement des informations de base
		parent::ajouter();

		// Obtenir la réponse
		$idxReponse = $this->get("reponse_choix");
		
		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into titem_reponse (id_item, id_projet, reponse, element, ordre, retroaction, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, now(), now()) ");
				
		// Obtenir la liste des éléments
		$ordre = 0;
		for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
			
			// Obtenir les valeurs
			$val = $this->get("reponse_" . $i . "_element");
			$retro = $this->get("reponse_" . $i . "_retroaction");
			if ($val != "") {
				
				// Incrémenter le compteur pour l'ordre d'affichage
				$ordre++;
				
				// Vérifier si on a la bonne réponse pour l'élément
				$reponse = "0";
				if ($i == $idxReponse) {
					$reponse = "1"; 
				}
				
				$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet"), $reponse, $val, $ordre, $retro));
			}
			
		}

		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemReponseBreve::ajouter() Fin");
		
		return;
	}		
	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un item
	 *
	 */
	public function enregistrer() {

		$this->log->debug("ItemReponseBreve::enregistrer() Début");

		// Enregistrement des informations de base
		parent::enregistrer();
				
		// Supprimer les informations d'item
		$this->supprimerReponseBreve($this->get("id_item"), $this->get("id_projet"));
		
		// Obtenir la réponse
		$idxReponse = $this->get("reponse_choix");
		
		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into titem_reponse (id_item, id_projet, reponse, element, ordre, retroaction, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, now(), now()) ");
				
		// Obtenir la liste des éléments
		$ordre = 0;
		for ($i = 0; $i < NB_MAX_CHOIX_REPONSES; $i++) {
			
			// Obtenir les valeurs
			$reponse = $this->get("reponse_" . $i . "_reponse");
			$val = $this->get("reponse_" . $i . "_element");
			$retro = $this->get("reponse_" . $i . "_retroaction");
			
			if ($val != "" || $retro != "") {
				
				// Incrémenter le compteur pour l'ordre d'affichage
				$ordre++;
				
				$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet"), $reponse, $val, $ordre, $retro));
			}
		}
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemReponseBreve::enregistrer() Fin");
								
		return;
	}	
	

	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemReponseBreve: indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();
		
		// Éléments propres aux réponses
		$nbReponses = $this->get("reponse_total");
		if ($nbReponses > 0) {
		
			for ($i=1; $i <= $nbReponses; $i++ ) {
				$index .= $this->get("reponse_" . $i . "_element") . " ";
				$index .= $this->get("reponse_" . $i . "_retroaction") . " ";
			}
		}				
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemReponseBreve: indexer() Fin");
	}	

	
	/**
	 * 
	 * Valider l'item
	 * @param Questionnaire $quest
	 * 
	 */
	public function valider($quest) {

		$this->log->debug("ItemReponseBreve::valider() Début");

		$messages = "";
		$succes = 0;
		$nbReponses = 0;
		$nbBonnesReponses = 0;
		$nbReponsesValides = 0;
	
		// Parcourir les choix de réponses et effectuer la validation
		$nbReponses = $this->get("reponse_total");
		
		if ($nbReponses > 0) {
		
			for ($i =1; $i <= $nbReponses; $i++ ) {
				// Déterminer si c'est une bonne réponse
				if ($this->get("reponse_" . $i . "_reponse") == 1) {
					$nbBonnesReponses++;
				}
				
				// Déterminer si la réponse est valide
				$element = trim($this->get("reponse_" . $i . "_element"));
				if ($element != "") {
					$nbReponsesValides++;
				}
			}
		}
		
		// Vérifier le champ énoncé
		if (trim($this->get("enonce")) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_149 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier si aucune réponse n'est sélectionnée ou si aucune réponse n'est valide
		if ($nbBonnesReponses == 0 || $nbReponsesValides < 1 ) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_150 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le champ rétro pour les réponses imprévues
		if (trim($this->get("retroaction_reponse_imprevue")) == "") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_148 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier la pondération (doit être numérique)
		$messages .= parent::verifierPonderation();
		
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
		
		$this->log->debug("ItemReponseBreve::valider() Fin");
		
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
		
		$this->log->debug("ItemReponseBreve::publier() Début");

		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier un item
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-reponse-breve.php", $this, $langue);

		$this->log->debug("ItemReponseBreve::publier() Fin");
				
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

		$this->log->debug("ItemReponseBreve::exporterXML() Début");
		
		// Si un id questionnaire est passé en paramètre, charger les données "pour ce questionnaire seulement"
		if ($quest != null)  {
			$this->getValeursPourQuestionnaire($quest->get("id_questionnaire"), $this->get("id_projet"));
		}
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);		
		
		// Récupérer le gabarit pour publier un item à choix multiples
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-reponse-breve.php", $this, $langue);
		
		$this->log->debug("ItemReponseBreve::exporterXML() Fin");
		
		return $contenu;
	}	
	
}

?>
