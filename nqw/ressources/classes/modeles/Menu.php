<?php

/** 
 * Classe Menu
 * 
 * Gestion du menu et de l'arborescence d'un questionnaire
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Menu {
	
	protected $dbh;
	protected $log;
						  
	protected $donnees;
	
	/**
	 * 
	 * Constructeur
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$this->dbh = $dbh;
		$this->log = $log;
		
		$donnees = array();
		
		return;
	}
	
	
	/**
	 * 
	 * Sauvegarder l'ordre des items dans la base de données
	 * 
	 * @param String $idProjet
	 * @param String $idQuest
	 * @param String $elements
	 * 
	 */
	public function enregistrerOrdreItems($idProjet, $idQuest, $elements) {

		$this->log->debug("Menu::enregistrerOrdreItems() Début");

		//$this->log->debug("Menu::enregistrerOrdreItems() elements : '$elements'");

		try {
			$stmt = $this->dbh->prepare("update tquestionnaire_item 
								  		 set ordre = ?, section = ?									
										 where id_projet = ?
										 and id_questionnaire = ?
										 and id_item = ? 
										");
			$ordre = 0;
			$idItem = "";
			$idParent = "";
			
			// Décoder les caractères HTML
			$elements = html_entity_decode($elements);
			
			foreach(preg_split("/(\r?\n)/", $elements) as $ligne){
		    
				// Enlever les caractères non voulus
				$ligne = str_replace("\\", "", $ligne);
				
				// Détecter le début d'un nouvel élément
				$indicateur = substr($ligne, -3);
				$this->log->debug("Menu::enregistrerOrdreItems() ligne : '$ligne'");
				$this->log->debug("Menu::enregistrerOrdreItems() indicateur : '$indicateur'");
				
				// Traiter un nouvel élément
				if ($indicateur == "..." || $ligne == "") {
					if ($idItem != "") {
						$this->log->debug("Menu::enregistrerOrdreItems() Traiter élément '$idItem'");
						
						$this->log->debug("********** idItem : '$idItem'  idParent : '$idParent'");
						
						// Enregistrer dans la BD
			    		if ($idItem != "") {
							// insertion d'une ligne
							$stmt->execute( array(  $ordre,
													$idParent,
													$idProjet,
													$idQuest,
													$idItem
													) );		
				    			
			    			$this->log->debug("Menu::enregistrerOrdreItems() idProjet : '$idProjet'  idQuest : '$idQuest'  idItem : '$idItem'  idParent = '$idParent' ordre = '$ordre'");
			    			$ordre++;
			    		}
						
						$idItem = "";
						$idParent = "";
					} else {
						$this->log->debug(" idItem VIDE!!!\n");
					
					}
				
				} else {
				
					// Traiter un nouveau item_id
					if (preg_match('/item_id/',$ligne)) {
						$this->log->debug("try to MATCH idItem");
						$pattern = '/"(\d*)"/';
			    		preg_match($pattern, $ligne, $matches);
			    		if (isset($matches[1])) {
				    		$idItem = $matches[1];
				    		$this->log->debug("MATCH idItem : '$idItem'");
			    		}
					}
					
					// Traiter un nouveau parent_id
					if (preg_match('/parent_id/',$ligne)) {
						$this->log->debug("try to MATCH idParent");
						$pattern = '/"(\d*)"/';
			    		preg_match($pattern, $ligne, $matches);
			    		if (isset($matches[1])) {
				    		$idParent = $matches[1];
				    		$this->log->debug("MATCH idParent : '$idParent'");
			    		}
					}
				
				}
			}
			
			
		if ($idItem != "") {
			$this->log->debug("Menu::enregistrerOrdreItems() Traiter élément FIN '$idItem'");
			
			$this->log->debug("********** idItem : '$idItem'  idParent : '$idParent'");
			
			// Enregistrer dans la BD
    		if ($idItem != "") {
				// insertion d'une ligne
				$stmt->execute( array(  $ordre,
										$idParent,
										$idProjet,
										$idQuest,
										$idItem
										) );		
	    			
    			$this->log->debug("Menu::enregistrerOrdreItems() idProjet : '$idProjet'  idQuest : '$idQuest'  idItem : '$idItem'  idParent = '$idParent' ordre = '$ordre'");
    			$ordre++;
    		}
			
			$idItem = "";
			$idParent = "";
		} else {
			$this->log->debug(" idItem VIDE!!!\n");
		
		}			
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Menu::enregistrerOrdreItems() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}		
		
		$this->log->debug("Menu::enregistrerOrdreItems() Fin");
										
		return;
	}		
	

	/**
	 * 
	 * Obtenir le menu
	 * 
	 * @param String idQuest
	 * @param String idProjet
	 */
	public function getMenu($idQuest, $idProjet) {

		$this->log->debug("Menu::getMenu() Début idQuest : '$idQuest'  idProjet : '$idProjet'");
		$structMenu = array();
		
		try {
			// Obtenir la liste des questionnaires pour l'utilisateur
			$sql = "select tquestionnaire.id_questionnaire, tquestionnaire.titre 
					from tquestionnaire 
					where tquestionnaire.id_projet = ?
					and tquestionnaire.id_questionnaire = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $idQuest));
			
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() > 0) {
				
				// Récupérer les informations des questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$questId = $row['id_questionnaire'];
	  				$questTitre = $row['titre'];
	  				
					// Préparer un élément de menu pour le questionnaire
					$menuItem = new MenuItem();
					$menuItem->setId($questId);
					$menuItem->setType("questionnaire");
					$menuItem->setNiveau("0");
					$menuItem->setLibelle($questTitre);
					array_push($structMenu, $menuItem);
	
					// Charger le questionnaire et obtenir le nombre d'items
	  				$quest = new Questionnaire($this->log, $this->dbh);
					$menuItem->setNbSousItem($quest->getNombreItems($idQuest, $idProjet));
					
					// Obtenir la liste des items
					$sql = "select tquestionnaire_item.id_questionnaire_item, titem.id_item, titem.type_item, tquestionnaire_item.section, titem.titre  
							from tquestionnaire_item, titem
							where tquestionnaire_item.id_questionnaire = ?  
							and tquestionnaire_item.id_projet = ?
							and titem.id_item = tquestionnaire_item.id_item
							and titem.id_projet = tquestionnaire_item.id_projet
							and tquestionnaire_item.statut != 0
							and titem.statut != 0 
							order by ordre";
					$sth2 = $this->dbh->prepare($sql);
					$rows = $sth2->execute(array($questId, $idProjet));
			
					// Vérifier qu'on a trouvé au moins un item	
					if ($sth2->rowCount() > 0) {
	
						// Récupérer les informations de l'item
						while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC)) {

							
			  				$itemId = $row2['id_item'];
			  				$itemType = $row2['type_item'];
			  				$idQuestionnaireItem = $row2['id_questionnaire_item'];
			  				
			  				$this->log->debug("Menu::getMenu() Item trouvé - itemId : '$itemId'  itemType : '$itemType'");
			  				
			  				$idSection = $row2['section'];
			  				$itemTitre = $row2['titre'];
	
			  				// Préparer un élément de menu pour l'item
			  				//echo "Traitement de l'item '" . $itemId . "' type d'item : '" . $itemType . "'<br/>\n";
			  				$menuItem = new MenuItem();
							$menuItem->setId($itemId);
							$menuItem->setIdSection($idSection);
							$menuItem->setType("item_" . $itemType);
							$menuItem->setNiveau("1");
							$menuItem->setLibelle($itemTitre);
							$menuItem->setIdQuestionnaireItem($idQuestionnaireItem);
							array_push($structMenu, $menuItem);
						}
					}
	  			}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Menu::getMenu() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
			
		$this->log->debug("Menu::getMenu() Fin");
		
		return $structMenu;		
	}
		
		
	/**
	 * 
	 * Insérer les items au bon endroit
	 * 
	 * @param String idItem
	 * @param String typeItem
	 * @param String idQuest
	 * @param String idProjet
	 */
	public function insererItem($idItem, $typeItem, $idQuest, $idProjet) {		
		
		$this->log->debug("Menu::getPositionInsertionItem() Début idItem : '$idItem'  typeItem : '$typeItem'  idQuest : '$idQuest'  idProjet : '$idProjet'");
		
		// Obtenir les informations sur le dernier item traité
		$session = new Session;
		$idItemPrec = $session->get("idItem");
		$typeItemPrec = $session->get("typeItem");
		$this->log->debug("Menu::insererItem() idItemPrec : '$idItemPrec'  typeItemPrec : '$typeItemPrec'");
		
		try {
			// Préparer la mise à jour
			$stmt = $this->dbh->prepare("update tquestionnaire_item 
								  		 set ordre = ?, section = ?									
										 where id_projet = ?
										 and id_questionnaire = ?
										 and id_item = ? 
										");
			
			// Obtenir la liste des items en ordre
			$sql = "select id_item, ordre, section 
							from tquestionnaire_item
							where id_questionnaire = ?  
							and id_projet = ?
							order by ordre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idQuest, $idProjet));
			
			// Vérifier qu'on a trouvé au moins un item	
			if ($sth->rowCount() > 0) {
	
				$ordre = 0;
				$insererSection = false;
				
				// Récupérer les informations des items existants
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$itemId = $row['id_item'];
	  				$itemOrdre = $row['ordre'];
	  				$itemSection = $row['section'];
	
					// Traiter les items de la section courante d'abord et insérer la section après
					if ($typeItem == '15' && $insererSection) {
	
	  					if ($itemSection == '0') {
	  						$insererSection = false;
	  						$this->log->debug("Menu::insererItem() Flag insertion a false ");
	
	  						$stmt->execute( array(  $ordre,
													$itemSection,
													$idProjet,
													$idQuest,
													$idItem
												) );		
			    			
			    			$this->log->debug("Menu::insererItem() *Nouvelle section  idProjet : '$idProjet'  idQuest : '$idQuest'  idItem : '$itemId'  itemSection = '$itemSection'  ordre = '$ordre'  typeItemPrec = '$typeItemPrec'");
		    				$ordre++;	
	  					}
	  				}
	
	 				// Si on insère une section, ne pas permettre de la mettre dans une autre section
	  				if ($itemId == $idItemPrec && $typeItem == '15' && !$insererSection ) {
						$insererSection = true;
	 					$this->log->debug("Menu::insererItem() Flag insertion a true ");
					}  				
	  				
	  				// Mettre à jour l'ordre pour chaque item actuel
	  				$stmt->execute( array(  $ordre,
											$itemSection,
											$idProjet,
											$idQuest,
											$itemId
											) );		
		    			
	    			$this->log->debug("Menu::insererItem() idProjet : '$idProjet'  idQuest : '$idQuest'  idItem : '$itemId'  itemSection = '$itemSection' ordre = '$ordre'");
	    			$ordre++;
	    			
	  				// Insérer le nouvel item après l'item courant de la dernière requête ou dans le cas où on se trouve dans une section, après la section
	  				if ($typeItem != '15' && $itemId == $idItemPrec ) {
	  					
	  					// Traiter le cas d'insertion d'un item dans une section
		  				if ($typeItemPrec == '15') {
		  					$itemSection = $idItemPrec;
		  				}
	  					
	  					$stmt->execute( array(  $ordre,
												$itemSection,
												$idProjet,
												$idQuest,
												$idItem
												) );		
			    			
		    			$this->log->debug("Menu::insererItem() *Nouvel item  idProjet : '$idProjet'  idQuest : '$idQuest'  idItem : '$itemId'  itemSection = '$itemSection'  ordre = '$ordre'  typeItemPrec = '$typeItemPrec'");
		    			$ordre++;	
	  				}
	  				
	    			
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Menu::getPositionInsertionItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Terminé
		$this->log->debug("Menu::getPositionInsertionItem() Fin");
	}
	
}

?>
