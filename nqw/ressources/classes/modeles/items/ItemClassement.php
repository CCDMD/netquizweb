<?php

require_once '../ressources/classes/outils/Session.php';

/** 
 * Classe Item - Classement
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

class ItemClassement extends Item  {
	
	protected $dbh;
	protected $log;
						  
	protected $donnees;
	
	protected $listeChampsItemClasseurs = "id_classeur, titre, retroaction, retroaction_negative, retroaction_incomplete, ordre";
	
	/**
	 * 
	 * Constructeur
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 * 
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$log->debug("ItemClassement::__construct() Début");
		$log->debug("ItemClassement::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		$log->debug("ItemClassement::__construct() Fin");
		
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
	
		$this->log->debug("ItemClassement::instancierTypeItem() Début typeItem = '$typeItem'");
	
		// Préparation globale
		parent::preparerValeursInitiales($typeItem, $projet, $usager);
	
		// Préparation pour cet item
		$this->set("type_elements1", "texte");
		$this->set("type_elements2", "texte");
			
		$this->log->debug("ItemClassement::instancierTypeItem() Fin");
	}	
	
	/**
	 *
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {
	
		$this->log->debug("ItemClassement::getDonneesRequete() Début");
	
		// Supprimer l'information sur les classeurs
		$this->supprimerClasseursDonnees();

		// Appel méthode parent
		parent::getDonneesRequete();
		
		// Analyse des classeurs
		$this->analyserClasseurs();
	
		$this->log->debug("ItemClassement::getDonneesRequete() Fin");
	
		return;
	}	
	

	/**
	 *
	 * Supprimer les classeurs
	 *
	 */
	protected function supprimerClasseursDonnees() {
	
		$this->log->debug("ItemClassement::supprimerClasseursDonnees() Début");
	
		$cles = array_keys($this->donnees);
			
		foreach ($cles as $cle) {
	
			// Obtenir chaque champ
			if (substr($cle, 0, 8) == "classeur") {
				unset($this->donnees[$cle]);
			}
		}
	
		$this->log->debug("ItemClassement::supprimerClasseursDonnees() Fin");
	
		return;
	}	
	
	
	/**
	 * 
	 * Obtenir les informations sur l'item
	 * @param String idItem
	 * @param String idProjet
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("ItemClassement::getItemsParId() Début");
		
		// Obtenir les classeurs
		$this->getClasseursParIdItem($idItem, $idProjet);
				
		// Analyser les classeurs
		$this->analyserClasseurs();
		
		$this->log->debug("ItemClassement::getItemParId() Fin");
		return;		
	}	
	

	/**
	 *
	 * Sauvegarder les informations dans la base de données - Ajout d'un item
	 *
	 */
	public function ajouter() {
	
		$this->log->debug("ItemClassement::ajouter() Début");
	
		// Enregistrement des informations de base
		parent::ajouter();
	
		// Enregistrer les classeurs
		$this->ajouterClasseurs();
	
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemClassement::ajouter() Fin");
	
		return;
	}	
	
	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un item
	 *
	 */
	public function enregistrer() {
	
		$this->log->debug("ItemClassement::enregistrer() Début");
	
		// Enregistrement des informations de base
		parent::enregistrer();
	
		// Supprimer les classeurs
		$this->supprimerClasseurs();
		
		// Enregistrer les claseurs pour l'item
		$this->ajouterClasseurs();
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemClassement::enregistrer() Fin");
		
		return;
	}
		
	
	/**
	 *
	 * Obtenir les informations sur les classeurs pour l'item
	 * 
	 * @param String idItem
	 * @param String idProjet
	 * 
	 */
	private function getClasseursParIdItem($idItem, $idProjet) {
	
		$this->log->debug("ItemClassement::getClasseursParIdItem() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT " . $this->listeChampsItemClasseurs . " from titem_classeur where id_item = ? and id_projet = ? order by ordre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
				
			// Récupérer les informations pour l'item
			$result = $sth->fetchAll();
	
			$idx = 0;
			foreach($result as $row) {
				$idx++;
				$cles = array_keys($row);
			  
				foreach ($cles as $cle) {
	
					// Obtenir chaque champ
					if (! is_numeric($cle) ) {
						$cleComplete = "classeur_" . $idx . "_$cle";
						$this->donnees[$cleComplete] = $row[$cle];
					}
					
					// Obtenir les éléments pour ce classeur
					if ($cle == "id_classeur") {
						$this->getElementParIdClasseur($row[$cle], $idProjet, $idx);
					}
				}
			}

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemClassement::getClasseursParIdItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		parent::getItemParId($idItem, $idProjet);

		// Terminé
		$this->log->debug("ItemClassement::getClasseursParIdItem() Fin");
		return;
	}	


	/**
	 *
	 * Obtenir les informations sur les éléments
	 * 
	 * @param String idClasseur
	 * @param String idProjet
	 * @param String idClasseur
	 * 
	 */
	private function getElementParIdClasseur($idClasseur, $idProjet, $idxClasseur) {
	
		$this->log->debug("ItemClassement::getElementParIdClasseur() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT * from titem_classeur_element where id_classeur = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idClasseur, $idProjet));
	
			// Récupérer les informations
			$result = $sth->fetchAll();

			// Récupérer chaque élément
			$idxElement = 1;
			foreach($result as $row) {
	
				$idElement = $row['id_element'];
				$texte = $row['texte'];
	
				$this->log->debug("ItemClassement::getElementParIdClasseur() idClasseur : '$idElement'  texte : '$texte'");
	
				$cle = "classeur_" . $idxClasseur . "_element_" . $idxElement;
				$this->set($cle . "_texte", $texte);
				$this->set($cle . "_statut", "1");
				
				// Récupérer les rétros
				$this->getRetrosParIdElement($idElement, $idProjet, $idxClasseur, $idxElement);
				
				$idxElement++;
			}
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemClassement::getElementParIdClasseur() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("ItemClassement::getElementParIdClasseur() Fin");
		return;
	}

	
	/**
	 *
	 * Obtenir les informations sur les rétroactions
	 * 
	 * @param String idElement
	 * @param String idProjet
	 * @param String indexClasseur
	 * @param String indexElement
	 */
	private function getRetrosParIdElement($idElement, $idProjet, $idxClasseur, $idxElement) {
	
		$this->log->debug("ItemClassement::getRetrosParIdElement() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT * from titem_classeur_element_retro where id_element = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idElement, $idProjet));
	
			// Récupérer les informations
			$result = $sth->fetchAll();
	
			$idxRetro = 1;
			foreach($result as $row) {
	
				$idRetro = $row['id_retro'];
				$retro = $row['retroaction'];
	
				if ($retro != "") {
				
					$this->log->debug("ItemClassement::getRetrosParIdElement() idRetro : '$idRetro'  retro : '$retro'");
		
					$cle = "classeur_" . $idxClasseur . "_element_" . $idxElement . "_retro_" . $idxRetro;
					$this->set($cle . "", $retro);
					$this->log->debug("ItemClassement::getRetrosParIdElement() Cle : '$cle' Retro = '" . $retro . "'\n");
				}
		
				$idxRetro++;
			}
	
			} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemClassement::getRetrosParIdElement() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
	
			// Terminé
			$this->log->debug("ItemClassement::getRetrosParIdElement() Fin");
		return;
	}	
	
	
	/**
	 * 
	 * Supprimer les informations sur les classeurs de cet item
	 *
	 */
	public function supprimerClasseurs() {

		$this->log->debug("ItemClassement::supprimerClasseurs() Début ");

		try {
			$sql = "delete from titem_classeur where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemClassement::supprimerClasseurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		return;
		
		$this->log->debug("ItemClassement::supprimerClasseurs() Fin ");
	}		
	
	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Ajout la liste des classeurs pour un item
	 *
	 */
	public function ajouterClasseurs() {
	
		$this->log->debug("ItemClassement::ajouterClasseurs() Début");
	
		// SQL
		$sthClasseur = $this->dbh->prepare("insert into titem_classeur (id_item, id_projet, titre, retroaction, retroaction_negative, retroaction_incomplete, ordre, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, ?, now(), now()) ");
		$sthElement = $this->dbh->prepare("insert into titem_classeur_element (id_classeur, id_projet, texte, date_creation, date_modification) VALUES (?, ?, ?, now(), now()) ");
		$sthRetro = $this->dbh->prepare("insert into titem_classeur_element_retro (id_element, id_projet, id_classeur, retroaction, date_creation, date_modification) VALUES (?, ?, ?, ?, now(), now()) ");
		
		
		try {
		
			$this->dbh->beginTransaction();
		
			// Parcourir les classeurs
			$ordre = 0;
			for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
		
				// Obtenir les valeurs
				$titre = $this->get("classeur_" . $i . "_titre");
				$retro = $this->get("classeur_" . $i . "_retroaction");
				$retroNeg = $this->get("classeur_" . $i . "_retroaction_negative");
				$retroIncomp = $this->get("classeur_" . $i . "_retroaction_incomplete");
				
				$idClasseur = 0;
				if ($titre != "" || $retro) {
					// Incrémenter le compteur pour l'ordre d'affichage
					$ordre++;
					$rows = $sthClasseur->execute(array($this->get("id_item"), $this->get("id_projet"), $titre, $retro, $retroNeg, $retroIncomp, $ordre));
					
					// Obtenir l'id du classeur
					$idClasseur = $this->dbh->lastInsertId('id_marque');
				}
				
				// Parcourir les éléments
				for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
	
					$idElement = 0;
					
					// Obtenir les valeurs
					$cle = "classeur_" . $i . "_element_" . $j . "_texte";
					$texte = $this->get($cle);
						
					if ($texte != "") {
						$rows = $sthElement->execute(array($idClasseur, $this->get("id_projet"), $texte));
						
						// Obtenir l'id de l'élément
						$idElement = $this->dbh->lastInsertId('id_element');
					
						// Parcourir les rétros
						for ($k = 1; $k <= NB_MAX_CLASSEURS; $k++) {
							
							$cleRetro = "classeur_" . $i . "_element_" . $j . "_retro_" . $k;
							$retro = $this->get($cleRetro);
		
							$rows = $sthRetro->execute(array($idElement, $this->get("id_projet"), $k, $retro));
		
						}
					}
				}			
			}
			
			$this->dbh->commit();
			
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemClassement::ajouterClasseurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
				
	
		$this->log->debug("ItemClassement::ajouterClasseurs() Fin");		
		
		return;
	}	
	

	/**
	 *
	 * Analyser les classeurs
	 *
	 */
	public function analyserClasseurs() {
	
		$this->log->debug("ItemClassement::analyserClasseurs()");
	
		// Obtenir la liste des éléments à choix multiples
		$nbElements = 0;
		$valeursExistent = 0;
	
		for ($i = NB_MAX_CLASSEURS; $i >= 1; $i--) {
	
			$nbElements++;
	
			// Obtenir les valeurs
			$titre = trim($this->get("classeur_" . $i . "_titre"));
			$retro = trim($this->get("classeur_" . $i . "_retroaction"));
			$retroNeg = trim($this->get("classeur_" . $i . "_retroaction_negative"));
			$retroIncomp = trim($this->get("classeur_" . $i . "_retroaction_incomplete"));
			$statut = trim($this->get("classeur_" . $i . "_statut"));
	
			// Vérifier si l'item est actif
			if ($titre != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $statut == 1 || $valeursExistent == 1) {
				$statut = 1;
				$valeursExistent = 1;
			}
			if ($statut != "") {
				$this->set("classeur_" . $i . "_statut", $statut);
			}
		}
	
		$this->log->debug("ItemClassement::analyserClasseurs() Fin");
		return;
	}
	
	
	/**
	 *
	 * Ajouter une classeur
	 * 
	 * @param String position
	 *
	 */
	public function ajouterClasseurDonnees($position) {
	
		$this->log->debug("ItemClassement::ajouterClasseurDonnees() Début position : '$position'");
	
		// Obtenir la liste des éléments
		$idx = NB_MAX_CLASSEURS + 1;
		$valeursExistent = 0;
	
		for ($i = NB_MAX_CLASSEURS; $i >= 1; $i--) {
			
			// Ajouter l'élément
			if ($position == $i) {
				
				$this->deleteByPrefix("classeur_" . $idx . "_");
				
				$this->set("classeur_" . $idx . "_titre", "");
				$this->set("classeur_" . $idx . "_retroaction", "");
				$this->set("classeur_" . $idx . "_retroaction_negative", "");
				$this->set("classeur_" . $idx . "_retroaction_incomplete", "");
				$this->set("classeur_" . $idx . "_statut", 1);
				$idx--;
			}
	
			// Obtenir les valeurs
			$titre = trim($this->get("classeur_" . $i . "_titre"));
			$retro = trim($this->get("classeur_" . $i . "_retroaction"));
			$retroNeg = trim($this->get("classeur_" . $i . "_retroaction_negative"));
			$retroIncomp = trim($this->get("classeur_" . $i . "_retroaction_incomplete"));
	
			// Vérifier si l'item est actif
			$statut = 0;
			if ($titre != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $valeursExistent) {
				$statut = 1;
				$valeursExistent = 1;
			}
	
			if ($statut == 1) {
			
				$prefixClasseur1 = "classeur_$i";
				$prefixClasseur2 = "classeur_$idx";
					
				$prefixRetro1 = "retro_$i";
				$prefixRetro2 = "retro_$idx";
					
				$cles = array_keys($this->donnees);
			
				foreach ($cles as $cle) {
			
					// Obtenir chaque champ et le déplacer
					if (substr($cle, 0, strlen($prefixClasseur1)) == $prefixClasseur1) {
						$nouvelleCle = str_replace($prefixClasseur1, $prefixClasseur2, $cle);
						$valeur = $this->get($cle);
						
						// Supprimer l'ancienne clé
						$this->delete($cle);
							
						// Déplacer les rétros
						if (preg_match("/$prefixRetro1/i", $cle)) {
							$nouvelleCle = str_replace($prefixRetro1, $prefixRetro2, $nouvelleCle);
						}
							
						// Copier la valeur
						$this->set($nouvelleCle, $valeur);
					} else {

						// Déplacer seulement les rétros
						if (preg_match("/$prefixRetro1/i", $cle)) {
							
							$nouvelleCle = str_replace($prefixRetro1, $prefixRetro2, $cle);
							$valeur = $this->get($cle);
							
							// Supprimer l'ancienne clé
							$this->delete($cle);
							
							// Copier la valeur
							$this->set($nouvelleCle, $valeur);
						}
					}
				}
			
			} else {
				$this->deleteByPrefix("classeur_" . $idx . "_");
			}
			$idx--;
		}
		$this->log->debug("ItemClassement::ajouterClasseurDonnees() Fin");
		return;
	}
	
	
	/**
	 *
	 * Supprimer une classeur
	 * 
	 * @param string position
	 *
	 */
	public function supprimerClasseurDonnees($position) {
	
		$this->log->debug("ItemClassement::supprimerClasseurDonnees() Début position : '$position'");
		
		// Obtenir la liste des éléments à choix multiples
		$idx = 1;
		$ordre = 1;
		
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
	
			// Supprimer l'élément
			if ($position != $i) {
					
				// Obtenir les valeurs
				$titre = trim($this->get("classeur_" . $i . "_titre"));
				$retro = trim($this->get("classeur_" . $i . "_retroaction"));
				$retroNeg = trim($this->get("classeur_" . $i . "_retroaction_negative"));
				$retroIncomp = trim($this->get("classeur_" . $i . "_retroaction_incomplete"));
				$statut = trim($this->get("classeur_" . $i . "_statut"));
	
				// Vérifier si l'item est actif
				if ($titre != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "") {
					$statut = 1;
				}
	
				if ($statut == 1) {
	
					$prefixClasseur1 = "classeur_$i";
					$prefixClasseur2 = "classeur_$idx";
					
					$prefixRetro1 = "retro_$i";
					$prefixRetro2 = "retro_$idx";
					
					$cles = array_keys($this->donnees);
						
					foreach ($cles as $cle) {
						
						// Obtenir chaque champ et le déplacer
						if (substr($cle, 0, strlen($prefixClasseur1)) == $prefixClasseur1 && $prefixClasseur1 != $prefixClasseur2) {
							$nouvelleCle = str_replace($prefixClasseur1, $prefixClasseur2, $cle);
							$valeur = $this->get($cle);
							
							// Supprimer l'ancienne clé
							$this->delete($cle);
							
							// Vérifier les rétros
							if (preg_match("/$prefixRetro1/i", $cle)) {
								$nouvelleCle = str_replace($prefixRetro1, $prefixRetro2, $nouvelleCle);
							}
							
							// Copier la valeur
							$this->set($nouvelleCle, $valeur);
						} else {
							
							// Déplacer seulement les rétros
							if (preg_match("/$prefixRetro1/i", $cle) && $prefixRetro1 != $prefixRetro2) {
									
								$nouvelleCle = str_replace($prefixRetro1, $prefixRetro2, $cle);
								$valeur = $this->get($cle);
									
								// Supprimer l'ancienne clé
								$this->delete($cle);
									
								// Copier la valeur
								$this->set($nouvelleCle, $valeur);
							}
						}
					}
	
				} else {
					$this->deleteByPrefix("classeur_" . $idx . "_");
				}
				$idx++;
			}
		}
	
		$this->log->debug("ItemClassement::supprimerClasseurDonnees() Fin");
		return;
	}	
	


	/**
	 *
	 * Ajouter un élément aux données
	 * 
	 * @param String idClasseur
	 *
	 */
	public function ajouterElementAuxDonnees($idClasseur) {
	
		$this->log->debug("Element::ajouterElementAuxDonnees() Début");
	
		// Localiser le prochain index disponible
		for ($i = 1; $i <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $i++) {
			
			$statut = $this->get("classeur_" . $idClasseur . "_element_" . $i . "_statut");

			if ($statut != '1') {
				$this->set("classeur_" . $idClasseur . "_element_" . $i . "_texte", TXT_ELEMENT_SANS_TEXTE);
				$this->set("classeur_" . $idClasseur . "_element_" . $i . "_statut", "1");
				break;
			}
		}
	
		$this->log->debug("Element::ajouterElementAuxDonnees() Fin");
	
	}	
	
	
	/**
	 *
	 * Changer le type d'éléments 1
	 *
	 */
	public function changerTypeElements1() {
	
		$this->log->debug("ItemClassement::changerTypeElements1() Début");
	
		// Vider les champs lors du changement de type
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
			$this->set("classeur_" . $i . "_titre","");
		}
		
		$this->log->debug("ItemClassement::changerTypeElements1() Fin");
	}
	
	/**
	 *
	 * Changer le type d'éléments 2
	 *
	 */
	public function changerTypeElements2() {
	
		$this->log->debug("ItemClassement::changerTypeElements2() Début");
	
		// Vider les champs lors du changement de type
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
			
			// Parcourir les éléments
			for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
			
				// Obtenir les valeurs
				$cle = "classeur_" . $i . "_element_" . $j . "_texte";
				if ($this->get($cle) != "") {
					$this->set($cle,"");
				}
			}
		}					
		
		$this->log->debug("ItemClassement::changerTypeElements2() Fin");
	}	
	
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemClassement: indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();
		
		// Informations propres à l'item
		$nbReponses = $this->get("reponse_total");
		if ($nbReponses > 0) {
		
			for ($i=1; $i <= $nbReponses; $i++ ) {
				$index .= $this->get("reponse_" . $i . "_element") . " ";
				$index .= $this->get("reponse_" . $i . "_retroaction") . " ";
			}
		}		
		
		// Parcourir les classeurs
		$ordre = 0;
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
		
			// Obtenir les valeurs
			$index .= $this->get("classeur_" . $i . "_titre") . " ";
			$index .= $this->get("classeur_" . $i . "_retroaction") . " ";
			$index .= $this->get("classeur_" . $i . "_retroaction_negative") . " ";
			$index .= $this->get("classeur_" . $i . "_retroaction_incomplete") . " ";

			// Parcourir les éléments
			for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
		
				// Obtenir les valeurs
				$index .= $this->get("classeur_" . $i . "_element_" . $j . "_texte") . " ";
					
				// Parcourir les rétros
				for ($k = 1; $k <= NB_MAX_CLASSEURS; $k++) {

					$index .= $this->get("classeur_" . $i . "_element_" . $j . "_retro_" . $k) . " ";
				}
			}
		}		
		
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemClassement: indexer() Fin");
	}	

	
	/**
	 * 
	 * Valider l'item
	 * @param Questionnaire $quest
	 * 
	 */
	public function valider($quest) {

		$this->log->debug("ItemClassement::valider() Début");

		$messages = "";
		$succes = 0;
		$nbReponses = 0;
		$nbBonnesReponses = 0;
		$nbReponsesValides = 0;
		
		// Analyser les classeurs
		$this->analyserClasseurs();

		// Parcourir la liste des classeurs
		$nbClasseurs = 0;
		$nbClasseursSansOnglet = 0;
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
			
			if ($this->get("classeur_" . $i . "_statut") == 1) {
				
				// Vérifier l'onglet
				if ($this->get("classeur_" . $i . "_titre") == "") {
					$nbClasseursSansOnglet++;
				} else {
					$nbClasseurs++;
				}
			}
		}		
		
		// Vérifier qu'au moins 2 classeurs existent
		if ($nbClasseurs < 2) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_154 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier si des classeurs sans onglet existent
		if ($nbClasseursSansOnglet > 0) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_155 . HTML_LISTE_ERREUR_FIN;
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
		
		$this->log->debug("ItemClassement::valider() Fin");
		
		return $succes;
	}
	
	
	/**
	 *
	 * Préparer les données pour publication
	 * 
	 * @param String Répertoire Destination
	 * @param Questionnaire Questionnaire courant
	 *
	 */
	public function preparerPublication($repertoireDestination, $quest) {
		
		$this->log->debug("ItemClassement::preparerPublication() Début");
		
		parent::preparerPublication($repertoireDestination, $quest);
		
		// --------------------------------------------------------
		// Orientation éléments
		// --------------------------------------------------------
		$this->log->debug("ItemClassement::preparerPublication() Orientation éléments pour ce questionnaire seulement : '" . $this->get("orientation_elements_quest") . "'");
		$this->log->debug("ItemClassement::preparerPublication() Orientation éléments pour cet item : '" . $this->get("orientation_elements") . "'");
		
		$orientation = "";
		if ($this->get("orientation_elements_quest") != "") {
			// Source : Ce questionnaire seulement
			$orientation = $this->get("orientation_elements_quest");
		} else {
			// Source : Pondération item
			$orientation = $this->get("orientation_elements");
		}
		
		$orientationPub = 0;
		if ($orientation == "verticale") {
				$orientationPub = 1;
		}
		$this->set("orientation_elements_pub", $orientationPub);
		$this->log->debug("ItemClassement::preparerPublication() Orientation éléments final : '" . $this->get("orientation_elements_pub") . "'\n");
		
		// --------------------------------------------------------
		// Préparer les fichiers pour les classeurs
		// --------------------------------------------------------
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
			$typeElements1 = "0"; // Défaut - texte
				
			// Elements
			if ($this->get("type_elements1") == "texte")  {
		
				// Déterminer la source de l'image
				$this->set("classeur_" . $i . "_source_image", -1);
		
			} elseif ($this->get("type_elements1") == "image") {
		
				// Traiter un élément de type image
				$typeElements1 = "1";
		
				// Obtenir l'id du média
				$idMedia = $this->get("classeur_" . $i . "_titre");
		
				// Préparer le fichier et les informations
				if ($idMedia != "") {
					
					// Obtenir le nom du fichier
					$this->log->debug("ItemClassement::preparerPublication() Charger média pour classeur $i '$idMedia'");

					$media = new Media($this->log, $this->dbh);
					$fichierImage = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
		
					// Copier le fichier
					$media->copierFichierMedia($repertoireDestination);
		
					// Préparer le nom du fichier dans les données
					$this->set("classeur_" . $i . "_fichier", $fichierImage);
		
					// Déterminer la source de l'image
					if ($media->get("source") == "fichier") {
						$this->set("classeur_" . $i . "_source_image", 1);
					} elseif ($media->get("source") == "web") {
						$this->set("classeur_" . $i . "_source_image", 2);
					}
				}
			}
		}

		// --------------------------------------------------------
		// Préparer les fichiers pour les éléments
		// --------------------------------------------------------
		for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
			
			for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
				
				// Obtenir l'id du média
				$idMedia = $this->get("classeur_" . $i . "_element_" . $j . "_texte");
				
				// Préparer le fichier et les informations
				if ($idMedia != "") {
						
					// Obtenir le nom du fichier
					$this->log->debug("ItemClassement::preparerPublication() Charger média pour classeur $i élément $j '$idMedia'");
				
					$media = new Media($this->log, $this->dbh);
					$fichierImage = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
				
					// Copier le fichier
					$media->copierFichierMedia($repertoireDestination);
				
					// Préparer le nom du fichier dans les données
					$this->set("classeur_" . $i . "_element_" . $j ."_fichier", $fichierImage);
				
					// Déterminer la source de l'image
					if ($media->get("source") == "fichier") {
						$this->set("classeur_" . $i . "_element_" . $j . "_source_image", 1);
					} elseif ($media->get("source") == "web") {
						$this->set("classeur_" . $i . "_element_" . $j. "_source_image", 2);
					}
				}				
			}
		}
		
		$this->log->debug("ItemClassement::preparerPublication() Fin");
	}	
	

	/**
	 *
	 * ajouterMediaListeExportation()
	 * Ajouter les médias d'un item à la liste d'exportation
	 * 
	 * @param array Liste médias à exporter
	 *
	 */
	public function ajouterMediaListeExportation($listeMedias) {
	
		$this->log->debug("ItemClassement::ajouterMediaListeExportation() Début");
	
		$listeMedias = parent::ajouterMediaListeExportation($listeMedias);
	
		// Ajouter les médias des classeurs au besoin
		if ($this->get("type_elements1") == "image") {
				
			for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
					
				// Obtenir les éléments et réponses
				$media = $this->get("classeur_" . $i . "_titre");
				if ($media  > 0 && !in_array($media, $listeMedias)) {
					array_push($listeMedias, $media);
				}
			}
		}
	
		// Ajouter les médias des éléments au besoin
		if ($this->get("type_elements2") == "image") {
				
			for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
				for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
					
					// Obtenir les éléments et réponses
					$media = $this->get("classeur_" . $i . "_element_" . $j . "_texte");
					if ($media  > 0 && !in_array($media, $listeMedias)) {
						array_push($listeMedias, $media);
					}
				}
			}
		}
	
		$this->log->debug("ItemClassement::ajouterMediaListeExportation() Fin");
	
		return $listeMedias;
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
		
		$this->log->debug("ItemClassement::publier() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-classement.php", $this, $langue);
		
		
		$this->log->debug("ItemClassement::publier() Fin");
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
		
		$this->log->debug("ItemClassement::exporterXML() Début");
		
		// Si un id questionnaire est passé en paramètre, charger les données "pour ce questionnaire seulement"
		if ($quest != null)  {
			$this->getValeursPourQuestionnaire($quest->get("id_questionnaire"), $this->get("id_projet"));
		}
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);
		
		// Récupérer le gabarit pour publier un item
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-classement.php", $this, $langue);
				
		$this->log->debug("ItemClassement::exporterXML() Fin");
		return $contenu;
	}	
	
}

?>
