<?php

require_once '../ressources/classes/outils/Session.php';

/** 
 * Classe Item - TexteLacunaire
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

class ItemTexteLacunaire extends Item  {
	
	protected $dbh;
	protected $log;
						  
	protected $donnees;
	public $listeLacunes;
	
	/**
	 * 
	 * Constructeur
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 * 
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$log->debug("ItemTexteLacunaire::__construct() Début");
		$log->debug("ItemTexteLacunaire::__construct() Appel du constructeur parent");
		parent::__construct($log, $dbh);
		
		$this->listeLacunes = array();
		
		$log->debug("ItemTexteLacunaire::__construct() Fin");
				
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
		$this->set("type_elements1", "glisser-deposer");
			
		$this->log->debug("ItemDamier::instancierTypeItem() Fin");
	}	
	
	/**
	 *
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {
	
		$this->log->debug("ItemTexteLacunaire::getDonneesRequete() Début");
	
		// Supprimer l'information sur les rétroactions des lacunes (si applicable)
		$this->supprimerLacunesRetrosDonnees();
		
		// Appel méthode parent
		parent::getDonneesRequete();
	
		$this->log->debug("Item::getDonneesRequete() Fin");
	
		return;
	}	

	
	/**
	 *
	 * Supprimer les rétros sur les lacunes
	 *
	 */
	protected function supprimerLacunesRetrosDonnees() {
	
		$this->log->debug("Item::supprimerLacunesRetrosDonnees() Début");
	
		$cles = array_keys($this->donnees);
			
		foreach ($cles as $cle) {
	
			// Obtenir chaque champ
			if (substr($cle, 0, 7) == "lacune_") {
				unset($this->donnees[$cle]);
			}
		}
	
		$this->log->debug("Item::supprimerLacunesRetrosDonnees() Fin");
	
		return;
	}	
	
	/**
	 * 
	 * Obtenir les informations sur l'item
	 * 
	 * @param String idItem
	 * @param String idProjet
	 */
	public function getItemParId($idItem, $idProjet) {

		$this->log->debug("ItemTexteLacunaire::getItemsParId() Début");
		
		parent::getItemParId($idItem, $idProjet);
		
		// Obtenir les lacunes
		$this->getLacunes();
		
		// Obtenir les réponses
		$this->getReponses();
		
		// Analyser les lacunes
		$this->analyserLacunes();
		
		// Type de lacune
		$this->set("type_lacune", $this->get("type_elements1"));
		
		$this->log->debug("ItemTexteLacunaire::getItemParId() Fin");
		return;		
	}	
	
	
	/**
	 *
	 * Obtenir les informations sur les lacunes
	 * 
	 */
	public function getLacunes() {
	
		$this->log->debug("ItemTexteLacunaire::getLacunes() Début");
		$trouve = false;
	
		try {
			$sql = "select id_projet, id_item, idx_lacune, retro, date_modification from titem_lacune where id_item = ? and id_projet = ? order by idx_lacune desc";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
	
			// Vérifier qu'on a trouvé au moins une lacune
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune lacune trouvée pour l'item id '" . $this->get("id_item") . "'");
			}
	
			// Vérifier qu'au moins un item est retourné, sinon erreur
			else {
				// Récupérer les informations pour l'item
				$result = $sth->fetchAll();
					
				$idx = 0;
				foreach($result as $row) {
					$idx++;
					$this->set("lacune_" . $idx . "_retro", $row['retro']);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemTexteLacunaire::getLacunes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("ItemTexteLacunaire::getLacunes() Fin");
		
	}
	
	
	
	/**
	 *
	 * Obtenir les informations sur les réponses
	 * 
	 */
	public function getReponses() {
	
		$this->log->debug("ItemTexteLacunaire::getReponses() Début");
		$trouve = false;
	
		try {
			$sql = "select id_projet, id_item, idx_lacune, reponse, element, ordre, retroaction, date_creation, date_modification from titem_lacune_reponse where id_item = ? and id_projet = ? order by idx_lacune, ordre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
				
			// Vérifier qu'on a trouvé au moins une réponse
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune réponse trouvée pour l'item id '" . $this->get("id_item") . "'");
			}
				
			// Vérifier qu'au moins un item est retourné, sinon erreur
			else {
				// Récupérer les informations pour la réponse
				$result = $sth->fetchAll();
					
				$idx = 0;
				$lacuneCourante = 0;
				foreach($result as $row) {

					// Obtenir les infos
					$lacune = $row['idx_lacune'];
					$reponse = $row['reponse'];
					$element = $row['element'];
					$retro = $row['retroaction'];
					
					// Si on change de lacune, remettre le compteur de réponse à 0
					if ($lacune != $lacuneCourante) {
						$idx = 1;
						$lacuneCourante = $lacune;
					} else {
						$idx++;
					}
					
					$cle = "lacune_" . $lacune . "_reponse_" . $idx;
					
					$this->set($cle . "_element", $element);
					$this->set($cle . "_retroaction", $retro);
					$this->set($cle . "_reponse", $reponse);
					
					// Vérifier le statut
					if ($this->get($cle . "_reponse") != "" || $this->get($cle . "_element") != "" || $this->get($cle . "_retroaction") != "" ) {
						$this->set($cle . "_statut", "1");
					}
				}
				
		        // Indiquer qu'un et un seul item a été trouvé
				$trouve = true;
			}
	
			} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemTexteLacunaire::getReponses() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
	
			// Terminé
			$this->log->debug("ItemTexteLacunaire::getReponses() Trouve = '$trouve'");
			$this->log->debug("ItemTexteLacunaire::getReponses() Fin");
			
			return $trouve;
	}	
	
	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Ajout d'un item
	 *
	 */
	public function ajouter() {
	
		$this->log->debug("ItemTexteLacunaire::ajouter() Début");
	
		// Enregistrement des informations de base
		parent::ajouter();
		
		// Analyser les lacunes
		$this->analyserLacunes();
		
		// Enregistrer les lacunes
		$this->supprimerLacunes();
		$this->enregistrerLacunes();		
		
		// Enregistrer les retros
		$this->analyserReponses();
		$this->supprimerReponses();
		$this->enregistrerReponses();
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemTexteLacunaire::ajouter() Fin");
	
		return;
	}	
	
	
	/**
	 *
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un item
	 *
	 */
	public function enregistrer() {
	
		$this->log->debug("ItemTexteLacunaire::enregistrer() Début");
		
		// Enregistrement des informations de base
		parent::enregistrer();

		// Analyser les lacunes
		$this->analyserLacunes();		
		
		// Enregistrer les lacunes
		$this->supprimerLacunes();
		$this->enregistrerLacunes();
		
		// Enregistrer les réponses
		$this->analyserReponses();
		$this->supprimerReponses();
		$this->enregistrerReponses();
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("ItemTexteLacunaire::enregistrer() Fin");
		
		return;
	}
	
	
	/**
	 *
	 * Analyser les informations sur les réponses
	 *
	 */
	public function analyserReponses() {
	
		$this->log->debug("ItemTexteLacunaire::analyserReponses() Début");
		
		// Parcourir les lacunes
		foreach ($this->listeLacunes as $lacune) {
				
			// Obtenir la liste des réponses
			for ($j = 1; $j <= NB_MAX_CHOIX_REPONSES; $j++) {
				
				$cle = "lacune_" . $lacune->get("idx_lacune") . "_reponse_" . $j; 
				
				$element = $this->get($cle . "_element");
				$retro = $this->get($cle . "_retroaction");
				$reponse = $this->get($cle . "_reponse");
				
				if ($element != "" || $retro != "") {
					$lacuneReponse = new LacuneReponse($this->log, $this->dbh);
					$lacuneReponse->set("idx_lacune", $lacune->get("idx_lacune"));
					$lacuneReponse->set("element", $element);
					$lacuneReponse->set("retro", $retro);
					$lacuneReponse->set("reponse", $reponse);
						
					// Ajouter à la liste des réponses pour cette lacune
					array_push($lacune->listeReponses, $lacuneReponse);
				}
			}
		}
	
		$this->log->debug("ItemTexteLacunaire::analyserReponses() Début");
		
	}
	
	/**
	 *
	 * Supprimer les informations sur les lacunes
	 *
	 */
	public function supprimerLacunes() {
	
		$this->log->debug("ItemTexteLacunaire::supprimerLacunes() Début");
	
		try {
			$sql = "delete from titem_lacune where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemTexteLacunaire::supprimerLacunes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("ItemTexteLacunaire::supprimerLacunes() Fin");
		return;
	}
	
	
	/**
	 *
	 * Sauvegarder les informations sur les lacunes
	 *
	 */
	public function enregistrerLacunes() {
	
		$this->log->debug("ItemTexteLacunaire::enregistrerLacunes() Début");
	
		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into titem_lacune (id_projet, id_item, idx_lacune, retro, date_creation, date_modification) VALUES (?, ?, ?, ?, now(), now()) ");
	
		try{
	
			// Parcourir les lacunes
			$idx = 0;
			foreach ($this->listeLacunes as $lacune) {
				$idx++;
				$rows = $sth->execute(array($this->get("id_projet"),  $this->get("id_item"), $idx, $lacune->get("retro")));
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemTexteLacunaire::enregistrerLacunes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("ItemTexteLacunaire::enregistrerLacunes() Début");
	}	
	
	
	/**
	 *
	 * Supprimer les informations sur les réponses
	 *
	 */
	public function supprimerReponses() {
	
		$this->log->debug("ItemTexteLacunaire::supprimerReponses() Début");
	
		try {
			$sql = "delete from titem_lacune_reponse where id_item = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_item"), $this->get("id_projet")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemTexteLacunaire::supprimerReponses() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("ItemTexteLacunaire::supprimerReponses() Fin");
		return;
	}	
	
	
	/**
	 *
	 * Sauvegarder les informations sur les réponses
	 *
	 */
	public function enregistrerReponses() {
	
		$this->log->debug("ItemTexteLacunaire::enregistrerRetros() Début");

		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into titem_lacune_reponse (id_projet, id_item, idx_lacune, reponse, element, ordre, retroaction, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, ?, ?, now(), now()) ");
		
		try{ 
				
			// Parcourir les lacunes
			foreach ($this->listeLacunes as $lacune) {
		
				// Parcourir la liste des réponses
				$ordre = 0;
				foreach ($lacune->listeReponses as $reponse) {
					
					$ordre++;
					$rows = $sth->execute(array($this->get("id_projet"),  $this->get("id_item"), $reponse->get("idx_lacune"), $reponse->get("reponse"), $reponse->get("element"), $ordre, $reponse->get("retro")));
					
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "ItemTexteLacunaire::enregistrerReponses() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("ItemTexteLacunaire::enregistrerRetros() Début");
	}	
		
	
	/**
	 *
	 * Analyser les lacunes
	 * @param String idLacuneSel Lacune sélectionné originalement
	 * @param bool $traiterSuppression
	 *
	 */
	public function analyserLacunes($idLacuneSel = 0, $traiterSuppression = false) {
	
		$this->log->debug("ItemTexteLacunaire::analyserLacunes() Début");
	
		// Obtenir le texte avec les lacunes
		$htmlIn = html_entity_decode($this->get("solution"), ENT_QUOTES, "UTF-8");
		
		// Remplacer les <br /> par <br>
		$htmlIn = preg_replace("/<br \/>/",'<br>', $htmlIn);
		$htmlOut = $htmlIn;
		$xmlOut = $htmlIn;
		$this->log->debug("ItemTexteLacunaire::analyserLacunes() html : '$htmlIn'");
		
		// Vider la liste actuelle des lacunes
		unset($this->listeLacunes);
		$this->listeLacunes = array();
		
		// Localiser les lacunes
		preg_match_all('/<span id="(.*?)".*?>(.+?)<\/span>/', $htmlIn, $matches, PREG_SET_ORDER);
		
		// Traiter chacune des lacunes
		$idx = sizeof($matches);
		if ($traiterSuppression) {
			$idx = 0;
		}
		
		$nbLacunes = 0;
		
		if (! $traiterSuppression) {
			$matches = array_reverse($matches);
		}
		foreach ($matches as $lacuneInfos) {

			if ($traiterSuppression) {
				$idx++;
			}
			
			$lacune = new Lacune($this->log, $this->dbh);
			$this->log->debug("ItemTexteLacunaire::analyserLacunes() Créer une nouvelle lacune");
			
			// Traiter la lacune
			$match = $lacuneInfos[0];
			$idLacuneSrc = $lacuneInfos[1];
			$lacune->set("idx_lacune", $idx);
			$lacune->set("texte", $lacuneInfos[2]);
			
			// Obtenir la rétro pour cette lacune
			$retro = $this->get($idLacuneSrc . "_retro");
			$lacune->set("retro", $retro);
			
			// Préparer la nouvelle lacune
			$lacuneHTML = '<span id="lacune_' . $idx . '" class="lacune lacune-' . $this->get("type_elements1") . ' mceNonEditable">' . TXT_LACUNE . " " . $idx . '</span>'; 
			
			// Déplacer les réponses
			$idLacuneDest = "lacune_" . $idx;
			$this->deplacerReponses($idLacuneSrc, $idLacuneDest);
			
			// Déplacer les lacunes
			$this->deplacerLacunes($idLacuneSrc, $idLacuneDest);
			
			// Prendre note de la position d'une nouvelle lacune
			if ($idLacuneSrc == "lacune_9999") {
				$this->set("nouvelle_lacune", $idLacuneDest);

				// Ajouter la réponse pour la nouvelle lacune
				$rep = Web::getParam("lacune_texte");
				$cleRep = $idLacuneDest . "_reponse_1_element";
				$this->set($cleRep, $rep);
			}
			
			// Remplacer dans le texte
			$htmlOut = str_replace($match, $lacuneHTML, $htmlOut);
			
			// Remplacer dans le XML
			$lacuneXML = "<lacune_" . $idx . ">" . TXT_LACUNE . " " . $idx . "</lacune_" . $idx . ">";
			$xmlOut = str_replace($match, $lacuneXML, $xmlOut);
						
			// Ajouter à la liste des lacunes
			array_push($this->listeLacunes, $lacune);
			$this->log->debug("ItemTexteLacunaire::analyserLacunes() Ajouter la lacune '" . $lacune->get("idx_lacune") . "'");
			
			// Compteurs
			$nbLacunes++;
			
			if (! $traiterSuppression) {
				$idx--;
			}
				
		}
		
		// Nombre total de lacunes
		$this->set("nb_lacunes", $nbLacunes);
		
		// Mettre à jour le texte
		$this->set("solution", $htmlOut);
		$this->set("solution_xml", $xmlOut);

		$this->log->debug("ItemTexteLacunaire::analyserLacunes() Fin");
	}

	
	/**
	 *
	 * Déplacer les lacunes
	 * @param String idLacuneSrc
	 * @param String idLacuneDest
	 *
	 */
	protected function deplacerLacunes($idLacuneSrc, $idLacuneDest) {
	
		$this->log->debug("ItemTexteLacunaire::DeplacerLacunes() Début ");
	
		if ($idLacuneSrc != $idLacuneDest) {
			$this->set($idLacuneDest . "_retro", $this->get($idLacuneSrc . "_retro"));
			$this->set($idLacuneSrc . "_retro", "");
		}
	
		$this->log->debug("ItemTexteLacunaire::DeplacerReponses() Fin ");
	}
	

	/**
	 *
	 * Déplacer les réponses pour une lacune
	 * @param String idLacuneSrc
	 * @param String idLacuneDest
	 *
	 */
	protected function deplacerReponses($idLacuneSrc, $idLacuneDest) {
	
		$this->log->debug("ItemTexteLacunaire::DeplacerReponses() Début ");	
		
		if ($idLacuneSrc != $idLacuneDest) {

			// Parcourir les réponses et les déplacer au besoin
			for ($i = 1; $i <= NB_MAX_CHOIX_REPONSES; $i++) {
					
				$cleSource = $idLacuneSrc . "_reponse_" . $i;
				$cleDest = $idLacuneDest . "_reponse_" . $i;
			
				// Vérifier si les données existent
				if ($this->get($cleSource . "_reponse") != "" || $this->get($cleSource . "_element") != "" || $this->get($cleSource . "_retroaction") != "" ) {
					
					// Copie des données
					$this->set($cleDest . "_reponse", $this->get($cleSource . "_reponse") );
					$this->set($cleDest . "_element", $this->get($cleSource . "_element") );
					$this->set($cleDest . "_retroaction", $this->get($cleSource . "_retroaction") );
					$this->set($cleDest . "_statut", "1" );
				
					// Suppresion des données originales
					$this->set($cleSource . "_reponse", "" );
					$this->set($cleSource . "_element", "" );
					$this->set($cleSource . "_retroaction", "" );
					$this->set($cleSource . "_statut", "" );
				}
			}
		}		
		
		$this->log->debug("ItemTexteLacunaire::DeplacerReponses() Fin ");
	}


	/**
	 *
	 * Préparer le texte avec les lacunes
	 *
	 */
	public function preparerTexteAvecLacunes() {
	
		$this->log->debug("ItemTexteLacunaire::preparerTexteAvecLacunes() Début");
	
		$this->set("texte", $this->get("solution"));
				
		// Obtenir la liste des lacunes dans un tableau
		$listeLacunes = array();
		
		$nbLacunes = 0;
		foreach ($this->donnees as $key => $value) {
			if (preg_match("/lacune_(\d+)_texte?/i", $key, $matches)) {
				
				$listeLacunes[$key] = $matches[1];
				$nbLacunes++;
				
			}
		}
		$this->log->debug("ItemTexteLacunaire::preparerTexteAvecLacunes() nbLacunes : '$nbLacunes'");
				
		// Inverser l'ordre de la liste des lacunes
		$listeLacunesInv = array_reverse($listeLacunes);
		
		// Parcourir la liste des lacunes de la fin vers le début afin
		// de ne pas interférer avec les positions précédentes en insérant une lacune
		$idx = $nbLacunes;
		foreach ($listeLacunesInv as $key => $value) {
			
			$idLacune = $value;
			$this->log->debug("ItemTexteLacunaire::preparerTexteAvecLacunes() KEY : '$key' id = '" . $idLacune . "'");
			
			// Obtenir les infos
			$texte = $this->get("lacune_" . $idLacune . "_texte");
			$couleur = $this->get("lacune_" . $idLacune . "_couleur");
			$positionDebut = $this->get("lacune_" . $idLacune . "_position_debut");
			$positionFin = $this->get("lacune_" . $idLacune . "_position_fin");
			$this->log->debug("ItemTexteLacunaire::preparerTexteAvecLacunes() texte : '$texte' couleur : '$couleur' positionDebut : '$positionDebut' positionFin : '$positionFin'");
			
			// Placer la lacune dans le texte
			$this->placerLacune($texte, $couleur, $positionDebut, $positionFin, $idx);
			$idx--;
		}
	
		$this->log->debug("ItemTexteLacunaire::preparerTexteAvecLacunes() Fin");
	
		return;
	}	
	

	/**
	 *
	 * Placer une lacune dans le texte
	 * 
	 * @param String texte
	 * @param String couleur
	 * @param String positionDebut
	 * @param String positionFin
	 * @param String index de la lacune
	 *
	 */
	public function placerLacune($texte, $couleur, $positionDebut, $positionFin, $index) {
	
		$this->log->debug("ItemTexteLacunaire::placerLacune() Début texte : '$texte'  couleur : '$couleur' positionDebut : '$positionDebut'  positionFin : '$positionFin'  index : '$index'");
		
		// Obtenir le html
		$html = $this->get("texte");
		
		// Préparer la lacune
		$lacune = '<span id="lacune_' . $index . '" class="lacune" style="background-color: #' . $couleur . '">' . $texte . '</span>';
		$this->log->debug("ItemTexteLacunaire::placerLacune() Lacune : '$lacune'");
		
		// Calculer les positions
		$pos1 = $positionDebut;
		if ($pos1 < 0) {
			$pos1 = 0;
		}
		
		$pos2 = $positionFin + 1;
		if ($pos2 > mb_strlen($html, "UTF-8")) {
			$pos2 = mb_strlen($html , "UTF-8");
		}
		$this->log->debug("ItemTexteLacunaire::placerLacune() Pos1 : '$pos1'  Pos2 : '$pos2'");
		
		// Placer la lacune dans le texte		
		$debut = mb_substr($html, 0, $pos1, "UTF-8");
		$fin = mb_substr($html, $pos2, mb_strlen($html), "UTF-8");
		
		$this->log->debug("ItemTexteLacunaire::placerLacune() HTML AVANT : '$html'");
		$this->log->debug("ItemTexteLacunaire::placerLacune() Début : '$debut' Lacune : '$lacune'  Fin : '$fin'");	

		$html = $debut . $lacune . $fin;
		
		$this->log->debug("ItemTexteLacunaire::placerLacune() HTML APRÈS : '$html'");
		
		// Mettre à jour le texte
		$this->set("texte", $html);
		
		$this->log->debug("ItemTexteLacunaire::placerLacune() Fin");
		
	}
	

	/**
	 *
	 * Ajouter réponses aux données
	 * 
	 * @param String lacune
	 * @param String reponse
	 *
	 */
	public function ajouterReponse($lacune, $reponse) {
	
		$this->log->debug("ItemTexteLacunaire::ajouterReponseDonnees() Début");	
			
		// Parcourir les réponses et les déplacer au besoin
		for ($i = NB_MAX_CHOIX_REPONSES; $i > 0; $i--) {
			
			$cleSource = "lacune_" . $lacune . "_reponse_" . $i;
			$idxSuiv = $i + 1;
			$cleDest = "lacune_" . $lacune . "_reponse_" . $idxSuiv;
			
			if ($i > $reponse) {
				
				// Vérifier si les données existent
				if ($this->get($cleSource . "_reponse") != "" || $this->get($cleSource . "_element") != "" || $this->get($cleSource . "_retroaction") != "" ) {
					
					// Copie des données
					$this->set($cleDest . "_reponse", $this->get($cleSource . "_reponse") );
					$this->set($cleDest . "_element", $this->get($cleSource . "_element") );
					$this->set($cleDest . "_retroaction", $this->get($cleSource . "_retroaction") );
					$this->set($cleDest . "_statut", "1" );

					// Suppresion des données originales
					$this->set($cleSource . "_reponse", "" );
					$this->set($cleSource . "_element", "" );
					$this->set($cleSource . "_retroaction", "" );
					$this->set($cleSource . "_statut", "" );
					
				}
				
			} elseif ($i == $reponse) { 
			
				// Copie des données
				$this->set($cleDest . "_reponse", "" );
				$this->set($cleDest . "_element", "" );
				$this->set($cleDest . "_retroaction", "" );
				$this->set($cleDest . "_statut", "1" );
				
			}
		}
		
		$this->log->debug("ItemTexteLacunaire::ajouterReponseDonnees() Fin");
	}
	
	
	/**
	 *
	 * Supprimer réponse des données
	 * 
	 * @param String lacune
	 * @param String reponse 
	 *
	 */
	public function supprimerReponse($lacune, $reponse) {
	
		$this->log->debug("ItemTexteLacunaire::supprimerReponse() Début");
	
		// Parcourir les réponses et les déplacer au besoin
		for ($i = 1; $i < NB_MAX_CHOIX_REPONSES; $i++) {
				
			$cleSource = "lacune_" . $lacune . "_reponse_" . $i;
			$idxPrec = $i - 1;
			$cleDest = "lacune_" . $lacune . "_reponse_" . $idxPrec;
			
			if ($i > $reponse) {
	
				// Vérifier si les données existent
				if ($this->get($cleSource . "_reponse") != "" || $this->get($cleSource . "_element") != "" || $this->get($cleSource . "_retroaction") != ""  || $idxPrec == $reponse ) {
					
					// Copie des données
					$this->set($cleDest . "_reponse", $this->get($cleSource . "_reponse") );
					$this->set($cleDest . "_element", $this->get($cleSource . "_element") );
					$this->set($cleDest . "_retroaction", $this->get($cleSource . "_retroaction") );
					$this->set($cleDest . "_statut", "1" );
					
					// Suppression des données originales
					$this->set($cleSource . "_reponse", "" );
					$this->set($cleSource . "_element", "" );
					$this->set($cleSource . "_retroaction", "" );
					$this->set($cleSource . "_statut", "" );
				}
			} 
		}
	
		$this->log->debug("ItemTexteLacunaire::supprimerReponse() Fin");
	}
		
	
	/**
	 *
	 * Obtenir le type de lacune sous forme textuel
	 *
	 */
	public function getTypeLacuneTxt() {
	
		$this->log->debug("Item::getTypeLacuneTxt() Début");
		$txt = "";
		
		if ($this->get("type_elements1") == "glisser-deposer") {
			$txt = TXT_GLISSER_DEPOSER;
		} elseif ($this->get("type_elements1") == "menu-deroulant") {
			$txt = TXT_MENU_DEROULANT;
		} elseif ($this->get("type_elements1") == "reponse-breve") {
			$txt = TXT_REPONSE_BREVE;
		}
	
		$this->log->debug("Item::getTypeLacuneTxt() Fin");
	
		return $txt;
	}
	
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("ItemTexteLacunaire: indexer() Début");
		
		// Éléments communs aux items
		$index = parent::preparerIndex();

		// Ajouter le texte 
		$index .= $this->get("solution") . " ";
		
		// Traiter chacune des lacunes
		foreach ($this->listeLacunes as $lacune) {
			
			// Obtenir la rétro
			$index .= $lacune->get("retro") . " ";
		
			// Obtenir les champs de chaque réponse
			foreach ($lacune->listeReponses as $reponse) {
				$index .= $reponse->get("element") . " ";
				$index .= $reponse->get("retro") . " ";
			}
		}
		
		// Mettre à jour l'index
		parent::updateIndex($index);
		
		$this->log->debug("ItemTexteLacunaire: indexer() Fin");
	}	
	
	
	/**
	 * 
	 * Valider l'item
	 * 
	 * @param Questionnaire $quest
	 *
	 */
	public function valider($quest) {

		$this->log->debug("ItemTexteLacunaire::valider() Début");
		
		$messages = "";
		$succes = 0;
		$nbReponses = 0;
		$nbBonnesReponses = 0;
		$nbReponsesValides = 0;
		
		// Analyser les lacunes
		$this->analyserLacunes();
		
		// Vérifier que le champ texte contient du texte et vérifier que le champ texte contient au moins une lacune
		if ($this->get("solution") == "" || $this->get("nb_lacunes") == "0") {
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_164 . HTML_LISTE_ERREUR_FIN;
		}

		// Parcourir la liste des lacunes
		$erreurs = array();
		foreach ($this->listeLacunes as $lacune) {

			// Obtenir la liste des réponses
			$nbReponses = 0;
			$nbBonnesReponses = 0;
			for ($j = 1; $j <= NB_MAX_CHOIX_REPONSES; $j++) {
			
				$cle = "lacune_" . $lacune->get("idx_lacune") . "_reponse_" . $j;
			
				$element = $this->get($cle . "_element");
				$retro = $this->get($cle . "_retroaction");
				$reponse = $this->get($cle . "_reponse");
			
				if ($element != "" || $retro != "") {
					$nbReponses++;
					
					// Vérifier si bonne reponse
					if ($reponse != "" && $reponse != "0") {
						$nbBonnesReponses++;
					}
				}
			}
			
			if ($this->get("type_lacune") == "glisser-deposer") {

				// Doit comporter au moins une réponse
				if ($nbReponses < 1) {
					array_push($erreurs, ERR_162);
				}
				
				// Doit comporter au moins une bonne réponse
				if ($nbBonnesReponses < 1) {
					array_push($erreurs, ERR_159);
				}
				
			} else if ($this->get("type_lacune") == "menu-deroulant") {
				
				// Doit comporter au moins deux réponses
				if ($nbReponses < 2) {
					array_push($erreurs, ERR_163);
				}
				
				// Doit comporter au moins une bonne réponse
				if ($nbBonnesReponses < 1) {
					array_push($erreurs, ERR_159);
				}
				
				// Toutes les réponses sont de bonnes réponses
				if ($nbReponses == $nbBonnesReponses) {
					array_push($erreurs, ERR_161);
				}
			
			} else if ($this->get("type_lacune") == "reponse-breve") {
				
				// Doit comporter au moins une réponse
				if ($nbReponses < 1) {
					array_push($erreurs, ERR_162);
				}
				
				// Doit comporter au moins une bonne réponse
				if ($nbBonnesReponses < 1) {
					array_push($erreurs, ERR_159);
				}				
				
				// Champ rétro vide?
				if ($lacune->get("retro") == "") {
					array_push($erreurs, ERR_160);
				}
			}
		}
		
		// Obtenir la listes des erreurs
		$erreurs = array_unique($erreurs);
		foreach ($erreurs as $erreur) {
			$messages .= HTML_LISTE_ERREUR_DEBUT . $erreur . HTML_LISTE_ERREUR_FIN;
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
		
		$this->log->debug("ItemTexteLacunaire::valider() Fin");
		
		return $succes;
	}
	

	/**
	 *
	 * Obtenir le texte pour publication
	 *
	 */
	public function getTextePourPublication() {
	
		$this->log->debug("ItemTexteLacunaire::getTextePourPublication() Début");
		
		$contenu = $this->get("solution");
		
		// Remplacer les fins de ligne
		$contenu = preg_replace("/\r\n|\r|\n/",'<br>', $contenu);
		$contenu = preg_replace("/<br \/>/",'<br>', $contenu);
		
		// Localiser les lacunes
		preg_match_all('/<span id="(.*?)".*?>(.+?)<\/span>/', $contenu, $matches, PREG_SET_ORDER);
		
		// Traiter chacune des lacunes
		$idx = 0;
		$nbLacunes = 0;
		
		foreach ($matches as $lacuneInfos) {
			$idx++;
			
			$match = $lacuneInfos[0];
			$idLacuneSrc = $lacuneInfos[1];
			
			$blankTag = "%blank" . $idx;
			
			// Remplacer dans le texte
			$contenu = str_replace($match, $blankTag, $contenu);
		}		
		
		$this->log->debug("ItemTexteLacunaire::getTextePourPublication() Début");

		// Enlever caractère html et UTF8
		$contenu = html_entity_decode($contenu, ENT_NOQUOTES, "UTF-8");
		
		// Escape pour Javascript
		$contenu = Web::nettoyerChainePourJS($contenu);
		
		return $contenu;
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
		
		$this->log->debug("ItemTexteLacunaire::publier() Début");
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Récupérer le gabarit pour publier
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_PUBLICATION . "item-texte-lacunaire.php", $this, $langue);
		
		$this->log->debug("ItemTexteLacunaire::publier() Fin");
		
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
		
		$this->log->debug("ItemTexteLacunaire::exporterXML() Début");

		// Si un id questionnaire est passé en paramètre, charger les données "pour ce questionnaire seulement"
		if ($quest != null)  {
			$this->getValeursPourQuestionnaire($quest->get("id_questionnaire"), $this->get("id_projet"));
		}
		
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination, $quest);
		
		// Retirer les médias de la langue par défaut
		$this->retirerMediasLangueParDefaut($langue);
		
		// Obtenir les lacunes courantes + réponses
		$this->analyserLacunes();
		
		// Récupérer le gabarit pour publier un item
		$contenu = Fichiers::getContenuItemLangue(REPERTOIRE_GABARITS_EXPORTATION . "item-texte-lacunaire.php", $this, $langue);
		
		$this->log->debug("ItemTexteLacunaire::exporterXML() Fin");
		
		return $contenu;
	}	
	
}

?>
