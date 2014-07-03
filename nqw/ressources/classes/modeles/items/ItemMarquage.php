<?php

require_once '../ressources/classes/outils/Session.php';

/** 
 * Classe Item - Marquage
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

class ItemMarquage extends Item  {
	
	protected $dbh;
	protected $log;
						  
	protected $donnees;
	public $listeMarques;
	
	protected $listeChampsItemCouleurs = "couleur, titre, retroaction, retroaction_negative, retroaction_incomplete, ordre";
	
	/**
	 * 
	 * Constructeur
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 * 
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$log->debug("ItemMarquage::__construct() Début");
		$log->debug("ItemMarquage::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		
		$this->listeMarques = array();
		
		$log->debug("ItemMarquage::__construct() Fin");
				
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
	
		$this->log->debug("ItemMarquage::instancierTypeItem() Début typeItem = '$typeItem'");
	
		// Préparation globale
		parent::preparerValeursInitiales($typeItem, $projet, $usager);
	
		$this->set("points_retranches", MARQUAGE_NB_POINTS_RETRANCHES);
			
		$this->log->debug("ItemMarquage::instancierTypeItem() Fin");
	}	
	
	
	/**
	 *
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {
	
		$this->log->debug("ItemMarquage::getDonneesRequete() Début");
	
		// Supprimer l'information sur les classeurs
		$this->supprimerCouleursDonnees();
	
		// Supprimer l'information sur les rétroactions des marques (si applicable)
		$this->supprimerMarquesRetrosDonnees();
		
		// Appel méthode parent
		parent::getDonneesRequete();
	
		// Analyse des classeurs
		$this->analyserCouleurs();
	
		$this->log->debug("Item::getDonneesRequete() Fin");
	
		return;
	}	

	
	/**
	 *
	 * Supprimer les couleurs dans les données en transit
	 *
	 */
	protected function supprimerCouleursDonnees() {
	
		$this->log->debug("Item::supprimerCouleursDonnees() Début");
	
		$cles = array_keys($this->donnees);
			
		foreach ($cles as $cle) {
	
			// Obtenir chaque champ
			if (substr($cle, 0, 7) == "couleur") {
				unset($this->donnees[$cle]);
			}
		}
	
		$this->log->debug("Item::supprimerCouleursDonnees() Fin");
	
		return;
	}
	
	/**
	 *
	 * Supprimer les rétros sur les marques
	 *
	 */
	protected function supprimerMarquesRetrosDonnees() {
	
		$this->log->debug("Item::supprimerMarquesRetrosDonnees() Début");
	
		$cles = array_keys($this->donnees);
			
		foreach ($cles as $cle) {
	
			// Obtenir chaque champ
			if (substr($cle, 0, 7) == "marque_") {
				unset($this->donnees[$cle]);
			}
		}
	
		$this->log->debug("Item::supprimerMarquesRetrosDonnees() Fin");
	
		return;
	}	
	
	/**
	 * 
	 * Obtenir les informations sur l'item
	 * 
	 * @param String idQuestionnaire
	 * @param String idProjet
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("ItemMarquage::getItemsParId() Début");
		
		// Obtenir les couleurs
		$this->getItemCouleursParIdItem($idItem, $idProjet);
				
		// Analyser les couleurs
		$this->analyserCouleurs();
		
		// Obtenir les marques
		$this->getMarqueParIdItem($idItem, $idProjet);
		
		// Préparer le texte
		$this->preparerTexteAvecMarques();
		
		$this->log->debug("ItemMarquage::getItemParId() Fin");
		return;		
	}	

	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Ajout d'un item
	 *
	 */
	public function ajouter() {
	
		$this->log->debug("ItemMarquage::ajouter() Début");
	
		// Enregistrement des informations de base
		parent::ajouter();
	
		// Enregistrer les couleurs
		$this->ajouterItemCouleurs();
	
		// Analyser le texte et obtenir les marques
		$this->enregisterMarquesCouleursPositions();		
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemMarquage::ajouter() Fin");
	
		return;
	}	
	
	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un item
	 *
	 */
	public function enregistrer() {
	
		$this->log->debug("ItemMarquage::enregistrer() Début");
	
		// Supprimer les couleurs
		$this->supprimerCouleurs();
		
		// Enregistrer les couleurs pour l'item
		$this->ajouterItemCouleurs();
		
		// Analyser le texte et obtenir les marques
		$this->enregisterMarquesCouleursPositions();
		
		// Au besoin remplacer la , par un .
		$pointsRetranches = str_replace(',', '.', $this->get("points_retranches"));
		$pointsRetranches = floatval($pointsRetranches);
		
		// Vérifier qu'une valeur est spécifiée pour le nombre de points retranchés
		if ($pointsRetranches == "") {
			$pointsRetranches = MARQUAGE_NB_POINTS_RETRANCHES;
		}
		$this->set("points_retranches", $pointsRetranches);		
		
		// Enregistrement des informations de base
		parent::enregistrer();
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemMarquage::enregistrer() Fin");
		
		return;
	}

	
	/**
	 *
	 * Enregistrer les marques (couleur et position)
	 *
	 */
	public function enregisterMarquesCouleursPositions() {
	
		$this->log->debug("ItemMarquage::enregisterMarquesCouleursPositions() Début");

		// Supprimer les marques existantes
		$this->supprimerMarques();
		
		// Analyser les marques et rétros
		$this->analyserMarques();
		
		try {
		
			// Préparer le SQL
			$sthMarque = $this->dbh->prepare("insert into titem_marque (id_item, id_projet, couleur, texte, position_debut, position_fin, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, now(), now()) ");
			
			// Préparer le SQL
			$sthRetro = $this->dbh->prepare("insert into titem_marque_retro (id_marque, id_projet, couleur, retroaction, date_creation, date_modification) VALUES (?, ?, ?, ?, now(), now()) ");

			// Traiter chacune des marques
			foreach ($this->listeMarques as $marque) {
		
				// Insérer la marque
				$sthMarque->execute(array($this->get("id_item"), $this->get("id_projet"), $marque->get("couleur"), $marque->get("texte"), $marque->get("position_debut"), $marque->get("position_fin")));
				$marque->set("id_marque", $this->dbh->lastInsertId('id_marque'));
				
				foreach ($marque->listeRetros as $retro) {
				
					$this->log->debug("ItemMarquage::enregisterMarquesRetro() idMarque : '" . $marque->get("id_marque") . "' Couleur : '" . $retro->get("couleur") . "'  Retro : '" . $retro->get("retro") . "'");
						
					// Insérer la rétroaction pour cette couleur
					$sthRetro->execute(array($marque->get("id_marque"), $this->get("id_projet"), $retro->get("couleur"), $retro->get("retro")));
				}
			}
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemMarquage::enregisterMarquesCouleursPositions() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
				
		$this->log->debug("ItemMarquage::enregisterMarquesCouleursPositions() Fin");
	
		return;
	}	
	
	
	
	/**
	 *
	 * Analyser les marques
	 *
	 */
	public function analyserMarques() {
	
		$this->log->debug("ItemMarquage::analyserMarques() Début");
	
		// Obtenir le texte avec les marques / correctif - double désencodage
		$html = html_entity_decode($this->get("texte"), ENT_COMPAT, "UTF-8");
		$html = html_entity_decode($html, ENT_COMPAT, "UTF-8");
		
		// Remplacer les <br /> par <br>
		$html = preg_replace("/<br \/>/",'<br>', $html);
		$this->log->debug("ItemMarquage::analyserMarques() html : '$html'");
		
		// Préparer le texte
		$texte = $html;
		
		// Vider la liste actuelle des marques
		unset($this->listeMarques);
		$this->listeMarques = array();
		
		// Localiser les marques
		preg_match_all('/<span id="(.*?)".*?background-color: #(.+?)".*?>(.+?)<\/span>/', $html, $matches, PREG_SET_ORDER);
		
		// Traiter chacune des marques
		$idx = 1;
		$conversionMarqueAPosition = array();
		foreach ($matches as $marqueInfos) {
		
			$marque = new Marque($this->log, $this->dbh);
			$this->log->debug("ItemMarquage::analyserMarques() Créer une nouvelle marque");
			
			// Traiter la marque
			$match = $marqueInfos[0];
			$marque->set("id_marque", $marqueInfos[1]);
			$marque->set("couleur", $marqueInfos[2]);
			$marque->set("texte", $marqueInfos[3]);
		
			// Obtenir les positions
			$marque->set("position_debut", mb_strpos($texte, $match, 0 , "UTF-8"));
			$marque->set("position_fin", $marque->get("position_debut") + mb_strlen($marque->get("texte"), "UTF-8") - 1);
		
			// Remplacer la chaîne dans le texte
			$texte = str_replace($match, $marque->get("texte"), $texte);

			// Obtenir la liste des rétros
			$listeRetros = $this->getListeRetros();
			foreach ($listeRetros as $key => $contenu) {

				// Obtenir l'id de la marque et l'id de la retro
				preg_match('/(marque_\d+?)_retro_(.+)/', $key, $matches);
				$idMarque = $matches[1];
				$couleur =  $matches[2];
					
				if ($idMarque == $marque->get("id_marque")) {

					$marqueRetro = new MarqueRetro($this->log, $this->dbh);
					$marqueRetro->set("id_marque", $marque->get("id_marque"));
					$marqueRetro->set("couleur", $couleur);
					$marqueRetro->set("retro", $contenu);
					$marqueRetro->set("position_debut", $marque->get("position_debut"));
					$marqueRetro->set("position_fin", $marque->get("position_fin"));
					
					// Ajouter à la liste des rétros pour cette marque
					array_push($marque->listeRetros, $marqueRetro);
				}

			}
			
			// Ajouter à la liste des marques
			array_push($this->listeMarques, $marque);
			$this->log->debug("ItemMarquage::analyserMarques() Ajouter la marque '" . $marque->get("id_marque") . "'");
		}

		// Escaper les caractères
		//$texte = htmlspecialchars($texte, ENT_QUOTES);
		
		// Mettre à jour le texte
		$this->set("solution", $texte);
		
		$this->log->debug("ItemMarquage::analyserMarques() Fin");
	}
								
							
	/**
	 *
	 * Supprimer les informations sur les marques de cet item
	 *
	 */
	public function supprimerMarques() {
	
		$this->log->debug("ItemMarquage::supprimerMarques() Début ");
	
		try {
			$sql = "delete from titem_marque where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemMarquage::supprimerMarques() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("ItemMarquage::supprimerMarques() Fin ");
		
		return;
	}	
	
	
	/**
	 *
	 * Obtenir les informations sur les couleurs pour l'item
	 * 
	 * @param String idItem
	 * @param String idProjet
	 */
	private function getItemCouleursParIdItem($idItem, $idProjet) {
	
		$this->log->debug("ItemMarquage::getItemCouleursParIdItem() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT " . $this->listeChampsItemCouleurs . " from titem_couleur where id_item = ? and id_projet = ? order by ordre";
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
						$cleComplete = "couleur_" . $idx . "_$cle";
						$this->donnees[$cleComplete] = $row[$cle];
						//echo "load Cle : '$cle' Clé complète : '$cleComplete'  Valeur = '" . $row[$cle] . "'\n";
					}
				}
			}

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemMarquage::getItemCouleursParIdItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		parent::getItemParId($idItem, $idProjet);

		// Terminé
		$this->log->debug("ItemMarquage::getItemCouleursParIdItem() Fin");
		return;
	}	

	
	/**
	 *
	 * Obtenir les informations sur les marques pour l'item
	 * 
	 * @param String idItem
	 * @param String idProjet
	 */
	private function getMarqueParIdItem($idItem, $idProjet) {
	
		$this->log->debug("ItemMarquage::getMarqueParIdItem() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT * from titem_marque where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idItem, $idProjet));
	
			// Récupérer les informations pour l'item
			$result = $sth->fetchAll();
			
			$idx = 1;
			
			foreach($result as $row) {
	
				$idMarque = $row['id_marque'];
				$couleur = $row['couleur'];
				$texte = $row['texte'];
				$positionDebut = $row['position_debut'];
				$positionFin = $row['position_fin'];
				
				// Couleur
				$cle = "marque_" . $idMarque . "_couleur";
				$this->donnees[$cle] = $couleur;
				$this->log->debug("ItemMarquage::getMarqueParIdItem() Cle : '$cle' Couleur = '" . $couleur . "'");
				
				// Texte
				$cle = "marque_" . $idMarque . "_texte";
				$this->donnees[$cle] = $texte;
				$this->log->debug("ItemMarquage::getMarqueParIdItem() Cle : '$cle' Texte = '" . $texte . "'");
				
				// Position début
				$cle = "marque_" . $idMarque . "_position_debut";
				$this->donnees[$cle] = $positionDebut;
				$this->log->debug("ItemMarquage::getMarqueParIdItem() Cle : '$cle' Position début = '" . $positionDebut . "'");
				
				// Position fin
				$cle = "marque_" . $idMarque . "_position_fin";
				$this->donnees[$cle] = $positionFin;
				$this->log->debug("ItemMarquage::getMarqueParIdItem() Cle : '$cle' Position fin = '" . $positionFin . "'");
				
				// Obtenir les rétros pour la marque
				$this->getRetroParIdMarque($idMarque, $idProjet, $idx);
				
				$idx++;
			}
	
		} catch (Exception $e) {
		Erreur::erreurFatal('018', "ItemMarquage::getMarqueParIdItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("ItemMarquage::getMarqueParIdItem() Fin");
		return;
	}
	
	
	/**
	 *
	 * Obtenir les informations sur les retros pour une marque
	 * 
	 * @param String idMarque
	 * @param String idProjet
	 * @param String index
	 */
	private function getRetroParIdMarque($idMarque, $idProjet, $idx) {
	
		$this->log->debug("ItemMarquage::getRetroParIdMarque() Début");
		$trouve = false;
	
		try {
			$sql = "SELECT * from titem_marque_retro where id_marque = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idMarque, $idProjet));
	
			// Récupérer les informations pour l'item
			$result = $sth->fetchAll();
			
			foreach($result as $row) {

				$idMarque = $row['id_marque'];
				$couleur = $row['couleur'];
				$retro = $row['retroaction'];
				
				$this->log->debug("ItemMarquage::getRetroParIdMarque() idMarque : '$idMarque'  couleur : '$couleur'  retro : '$retro'");
				
				$cle = "marque_" . $idx . "_retro_" . $couleur;
				$this->donnees[$cle] = $retro;
				$this->log->debug("ItemMarquage::getRetroParIdMarque() Cle : '$cle' Retro = '" . $retro . "'");
			}
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemMarquage::getRetroParIdMarque() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("ItemMarquage::getRetroParIdMarque() Fin");
		return;
	}	
	
	
	/**
	 *
	 * Obtenir une liste des rétroactions
	 *  
	 */
	public function getListeRetros() {
		
		
		$this->log->debug("ItemMarquage::getListeRetros() Début");
		
		$listeRetros = array();

		// Obtenir la liste des rétros
		foreach ($this->donnees as $key => $value) {
			if (preg_match("/marque_\d+?_retro_\w{6}/i", $key)) {
				$listeRetros[$key] = html_entity_decode($value, ENT_QUOTES);
			}
		}
		
		$this->log->debug("ItemMarquage::getListeRetros() Fin");
		
		return $listeRetros;
	}
	

	/**
	 *
	 * Préparer le texte avec les marques
	 *
	 */
	public function preparerTexteAvecMarques() {
	
		$this->log->debug("ItemMarquage::preparerTexteAvecMarques() Début");
				
		$this->set("texte", $this->get("solution"));
		
		// Obtenir la liste des marques dans un tableau
		$listeMarques = array();
		
		$nbMarques = 0;
		foreach ($this->donnees as $key => $value) {
			if (preg_match("/marque_(\d+)_texte?/i", $key, $matches)) {
				
				$listeMarques[$key] = $matches[1];;
				$nbMarques++;
				
			}
		}
		$this->log->debug("ItemMarquage::preparerTexteAvecMarques() nbMarques : '$nbMarques'");
				
		// Inverser l'ordre de la liste des marques
		$listeMarquesInv = array_reverse($listeMarques);
		
		// Parcourir la liste des marques de la fin vers le début afin
		// de ne pas interférer avec les positions précédentes en insérant une marque
		$idx = $nbMarques;
		foreach ($listeMarquesInv as $key => $value) {
			
			$idMarque = $value;
			$this->log->debug("ItemMarquage::preparerTexteAvecMarques() KEY : '$key' id = '" . $idMarque . "'");
			
			// Obtenir les infos
			$texte = $this->get("marque_" . $idMarque . "_texte");
			$couleur = $this->get("marque_" . $idMarque . "_couleur");
			$positionDebut = $this->get("marque_" . $idMarque . "_position_debut");
			$positionFin = $this->get("marque_" . $idMarque . "_position_fin");
			$this->log->debug("ItemMarquage::preparerTexteAvecMarques() texte : '$texte' couleur : '$couleur' positionDebut : '$positionDebut' positionFin : '$positionFin'");
			
			// Placer la marque dans le texte
			$this->placerMarque($texte, $couleur, $positionDebut, $positionFin, $idx);
			$idx--;
		}
		
		$this->log->debug("ItemMarquage::preparerTexteAvecMarques() Fin");
		
		return;
	}	
	

	/**
	 *
	 * Placer une marque dans le texte
	 * 
	 * @param String texte
	 * @param String couleur
	 * @param String positionDebut
	 * @param String positionFin
	 * @param String index de la marque
	 *
	 */
	public function placerMarque($texte, $couleur, $positionDebut, $positionFin, $index) {
	
		$this->log->debug("ItemMarquage::placerMarque() Début texte : '$texte'  couleur : '$couleur' positionDebut : '$positionDebut'  positionFin : '$positionFin'  index : '$index'");
		
		// Obtenir le html
		$html = $this->get("texte");
		
		// Préparer la marque
		$marque = '<span id="marque_' . $index . '" class="marque" style="background-color: #' . $couleur . '">' . $texte . '</span>';
		$this->log->debug("ItemMarquage::placerMarque() Marque : '$marque'");
		
		// Calculer les positions
		$pos1 = $positionDebut;
		if ($pos1 < 0) {
			$pos1 = 0;
		}
		
		$pos2 = $positionFin + 1;
		if ($pos2 > mb_strlen($html, "UTF-8")) {
			$pos2 = mb_strlen($html, "UTF-8");
		}
		$this->log->debug("ItemMarquage::placerMarque() Pos1 : '$pos1'  Pos2 : '$pos2'");
		
		// Placer la marque dans le texte		
		$debut = mb_substr($html, 0, $pos1, "UTF-8");
		$fin = mb_substr($html, $pos2, mb_strlen($html), "UTF-8");
		
		$this->log->debug("ItemMarquage::placerMarque() HTML AVANT : '$html'");
		$this->log->debug("ItemMarquage::placerMarque() Début : '$debut' Marque : '$marque'  Fin : '$fin'");	

		$html = $debut . $marque . $fin;
		
		$this->log->debug("ItemMarquage::placerMarque() HTML APRÈS : '$html'");
		
		// Mettre à jour le texte
		$this->set("texte", $html);
		
		$this->log->debug("ItemMarquage::placerMarque() Fin");
		
	}
	
	
	/**
	 * 
	 * Supprimer les informations sur les couleurs de cet item
	 *
	 */
	public function supprimerCouleurs() {

		$this->log->debug("ItemMarquage::supprimerCouleurs() Début ");

		try {
			$sql = "delete from titem_couleur where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemMarquage::supprimerCouleurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
		
		$this->log->debug("ItemMarquage::supprimerCouleurs() Fin ");
		
		return;
	}		

	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Ajout la liste des couleurs pour un item
	 *
	 */
	public function ajouterItemCouleurs() {
	
		$this->log->debug("ItemMarquage::ajouterItemCouleurs() Début");
	
		try {
		
			// Enregistrer les informations
			$sth = $this->dbh->prepare("insert into titem_couleur (id_item, id_projet, couleur, titre, retroaction, retroaction_negative, retroaction_incomplete, ordre, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now()) ");
			
			// Obtenir la liste des éléments
			$ordre = 0;
			for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
		
				// Obtenir les valeurs
				$couleur = $this->get("couleur_" . $i . "_couleur");
				$titre = $this->get("couleur_" . $i . "_titre");
				$retro = $this->get("couleur_" . $i . "_retroaction");
				$retroNeg = $this->get("couleur_" . $i . "_retroaction_negative");
				$retroIncomp = $this->get("couleur_" . $i . "_retroaction_incomplete");
				
				if ($titre != "" || $retro) {
		
					// Incrémenter le compteur pour l'ordre d'affichage
					$ordre++;
		
					$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet"), $couleur, $titre, $retro, $retroNeg, $retroIncomp, $ordre));
				}
		
			}
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemMarquage::ajouterItemCouleurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			

		$this->log->debug("ItemMarquage::ajouterItemCouleurs() Fin");		
		
		return;
	}	
	

	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 * @param String chaine
	 * @param String idProjet
	 */
	public function indexer() {
		
		$this->log->debug("ItemMarquage: indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();

		// Traiter chacune des marques
		foreach ($this->listeMarques as $marque) {
			
			// Insérer la marque
			$index .= $marque->get("couleur") . " ";
			$index .= $marque->get("texte") . " ";
		
			foreach ($marque->listeRetros as $retro) {
				$index .= $retro->get("couleur") . " ";
				$index .= $retro->get("retro") . " ";
			}
		}
		
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemMarquage: indexer() Fin");
	}	

	
	/**
	 *
	 * Analyser les couleurs
	 *
	 */
	public function analyserCouleurs() {
	
		$this->log->debug("Item::analyserCouleurs()");
	
		// Obtenir la liste des éléments à choix multiples
		$nbElements = 0;
		$valeursExistent = 0;
		$couleursActives = array();
		$doublonCouleurs = 0;
	
		for ($i = NB_MAX_COULEURS; $i >= 1; $i--) {
	
			$nbElements++;
	
			// Obtenir les valeurs
			$couleur = trim($this->get("couleur_" . $i . "_couleur"));
			$titre = trim($this->get("couleur_" . $i . "_titre"));
			$retro = trim($this->get("couleur_" . $i . "_retroaction"));
			$retroNeg = trim($this->get("couleur_" . $i . "_retroaction_negative"));
			$retroIncomp = trim($this->get("couleur_" . $i . "_retroaction_incomplete"));
			$statut = trim($this->get("couleur_" . $i . "_statut"));
			
			// Ajouter la couleur à la liste des couleurs
			$couleurMaj = strtoupper($couleur);
			if ($couleur != "") {
				if (in_array($couleurMaj, $couleursActives)) {
					$doublonCouleurs = 1;
				} else {
					array_push($couleursActives, $couleurMaj);
				}
			}
	
			// Vérifier si l'item est actif
			if ($couleur != "" || $titre != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $statut == 1 || $valeursExistent == 1) {
				$statut = 1;
				$valeursExistent = 1;
			}
			if ($statut != "") {
				$this->set("couleur_" . $i . "_statut", $statut);
			}
		}
	
		// Déterminer s'il y a des doublons de couleurs
		$this->log->debug("Item::analyserCouleurs() Doublon couleurs = '$doublonCouleurs");
		$this->set("doublons_couleurs", $doublonCouleurs);
		
		$this->log->debug("Item::analyserCouleurs() Fin");
		return;
	}
	
	
	/**
	 *
	 * Ajouter une couleur
	 * 
	 * @param string position
	 *
	 */
	public function ajouterCouleur($position) {
	
		$this->log->debug("Item::ajouterCouleur() Début position : '$position'");
	
		// Obtenir la liste des éléments
		$idx = NB_MAX_COULEURS + 1;
		$valeursExistent = 0;
	
		for ($i = NB_MAX_COULEURS; $i >= 1; $i--) {
				
			// Ajouter l'élément
			if ($position == $i) {
				$this->set("couleur_" . $idx . "_couleur", "");
				$this->set("couleur_" . $idx . "_titre", "");
				$this->set("couleur_" . $idx . "_retroaction", "");
				$this->set("couleur_" . $idx . "_retroaction_negative", "");
				$this->set("couleur_" . $idx . "_retroaction_incomplete", "");
				$this->set("couleur_" . $idx . "_statut", 1);
				$idx--;
			}
	
			// Obtenir les valeurs
			$couleur = trim($this->get("couleur_" . $i . "_couleur"));
			$titre = trim($this->get("couleur_" . $i . "_titre"));
			$retro = trim($this->get("couleur_" . $i . "_retroaction"));
			$retroNeg = trim($this->get("couleur_" . $i . "_retroaction_negative"));
			$retroIncomp = trim($this->get("couleur_" . $i . "_retroaction_incomplete"));
	
			// Vérifier si l'item est actif
			$statut = 0;
			if ($couleur != "" || $titre != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "" || $valeursExistent) {
				$statut = 1;
				$valeursExistent = 1;
			}
	
			if ($statut == 1) {
				// Nouvelles valeurs
				$this->set("couleur_" . $idx . "_couleur", $couleur);
				$this->set("couleur_" . $idx . "_titre", $titre);
				$this->set("couleur_" . $idx . "_retroaction", $retro);
				$this->set("couleur_" . $idx . "_retroaction_negative", $retroNeg);
				$this->set("couleur_" . $idx . "_retroaction_incomplete", $retroIncomp);
				$this->set("couleur_" . $idx . "_statut", $statut);
			} else {
				$this->delete("couleur_" . $idx . "_couleur");
				$this->delete("couleur_" . $idx . "_titre");
				$this->delete("couleur_" . $idx . "_retroaction");
				$this->delete("couleur_" . $idx . "_retroaction_negative");
				$this->delete("couleur_" . $idx . "_retroaction_incomplete");
				$this->delete("couleur_" . $idx . "_statut");
			}
			$idx--;
		}
	
		$this->log->debug("Item::ajouterCouleur() Fin");
		return;
	}
	
	
	/**
	 *
	 * Supprimer une couleur
	 * 
	 * @param string position
	 *
	 */
	public function supprimerCouleur($position) {
	
		$this->log->debug("Item::supprimerCouleur() Début position : '$position'");
	
		// Obtenir la liste des éléments à choix multiples
		$idx = 1;
		$ordre = 1;
	
		for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
	
			// Supprimer l'élément
			if ($position != $i) {
					
				// Obtenir les valeurs
				$couleur = trim($this->get("couleur_" . $i . "_couleur"));
				$titre = trim($this->get("couleur_" . $i . "_titre"));
				$retro = trim($this->get("couleur_" . $i . "_retroaction"));
				$retroNeg = trim($this->get("couleur_" . $i . "_retroaction_negative"));
				$retroIncomp = trim($this->get("couleur_" . $i . "_retroaction_incomplete"));
				$statut = trim($this->get("couleur_" . $i . "_statut"));
	
				// Vérifier si l'item est actif
				if ($couleur != "" || $titre != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "") {
					$statut = 1;
				}
	
				if ($statut == 1) {
	
					// Nouvelles valeurs
					$this->set("couleur_" . $idx . "_couleur", $couleur);
					$this->set("couleur_" . $idx . "_titre", $titre);
					$this->set("couleur_" . $idx . "_retroaction", $retro);
					$this->set("couleur_" . $idx . "_retroaction_negative", $retroNeg);
					$this->set("couleur_" . $idx . "_retroaction_incomplete", $retroIncomp);
					$this->set("couleur_" . $idx . "_statut", $statut);
					$this->set("couleur_" . $idx . "_ordre", $ordre);
						
					// Incrémenter le compteur pour l'ordre
					$ordre++;
						
				} else {
	
					$this->delete("couleur_" . $idx . "_couleur");
					$this->delete("couleur_" . $idx . "_titre");
					$this->delete("couleur_" . $idx . "_retroaction");
					$this->delete("couleur_" . $idx . "_retroaction_negative");
					$this->delete("couleur_" . $idx . "_retroaction_incomplete");
					$this->delete("couleur_" . $idx . "_statut");
					$this->delete("couleur_" . $idx . "_ordre");
				}
				$idx++;
			}
		}
	
		$this->log->debug("Item::supprimerCouleur() Fin");
		return;
	}
		
	
	/**
	 * 
	 * Valider l'item
	 * 
	 * @param Questionnaire $quest
	 *
	 */
	public function valider($quest) {

		$this->log->debug("ItemMarquage::valider() Début");
		
		$messages = "";
		$succes = 0;
		$nbReponses = 0;
		$nbBonnesReponses = 0;
		$nbReponsesValides = 0;

		// Analyser les couleurs
		$this->analyserCouleurs();
		
		// Vérifier la pondération (doit être numérique)
		$messages .= parent::verifierPonderation();
		
		// Vérifier les points retranchés
		$messages .= parent::verifierPointsRetranches();
		
		// Vérifier si le thème est valide
		$messages .= parent::verifierTheme($quest);
				
		// Vérifier si une couleur de marquage est utilisée plus d'une fois
		$couleursVues = array();
		$libellesVus = array();
		$doublonCouleur = 0;
		$doublonLibelle = 0;
		$libelleVide = 0;
		
		for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
		
			// Obtenir les valeurs
			$couleur = $this->get("couleur_" . $i . "_couleur");
			$libelle = trim($this->get("couleur_" . $i . "_titre"));
			$retro = $this->get("couleur_" . $i . "_retroaction");
			$retroNeg = $this->get("couleur_" . $i . "_retroaction_negative");
			$retroIncomp = $this->get("couleur_" . $i . "_retroaction_incomplete");

			if ($couleur != "" || $libelle != "" || $retro != "" || $retroNeg != "" || $retroIncomp != "") {
			
				// Vérifier les doublons pour les couleurs
				if (in_array($couleur, $couleursVues)) {
					$doublonCouleur++;
				} else {
					array_push($couleursVues, $couleur);
				}
				
				// Vérifier les doublons pour les libellés
				if (in_array($libelle, $libellesVus)) {
					$doublonLibelle++;
				} else {
					array_push($libellesVus, $libelle);
				}
				
				// Vérifier les libellés vides
				if ($libelle == "") {
					$libelleVide++;
				}
			}
		}
		
		// Vérifier si un doublon de couleur existe
		if ($doublonCouleur > 0) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_151 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier si un doublon de libellé existe
		if ($doublonLibelle > 0) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_153 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier si un libellé vide existe
		if ($libelleVide > 0) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_152 . HTML_LISTE_ERREUR_FIN;
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
		
		$this->log->debug("ItemMarquage::valider() Fin");
		
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
		
		$this->log->debug("ItemMarquage::publier() Début");
		
		$this->analyserMarques();
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-marquage.php", $this, $langue);
		
		$this->log->debug("ItemMarquage::publier() Fin");
		
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
		
		$this->log->debug("ItemMarquage::exporterXML() Début");

		// Si un id questionnaire est passé en paramètre, charger les données "pour ce questionnaire seulement"
		if ($quest != null)  {
			$this->getValeursPourQuestionnaire($quest->get("id_questionnaire"), $this->get("id_projet"));
		}
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);
		
		// Obtenir les marques courantes + rétros
		$this->analyserMarques();
		
		// Récupérer le gabarit pour publier un item
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-marquage.php", $this, $langue);
		
		$this->log->debug("ItemMarquage::exporterXML() Fin");
		
		return $contenu;
	}	
	
}

?>
