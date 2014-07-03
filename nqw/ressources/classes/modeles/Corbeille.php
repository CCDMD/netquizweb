<?php

/** 
 * 
 * Classe Corbeille
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

class Corbeille {
	
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

		$this->log = $log;
		$this->dbh = $dbh;
		
		$donnees = array();
		
		return;
	}
	

	/**
	 *
	 * Obtenir la liste des éléments pour un utilisateur 
	 * @param String idProjet
	 *
	 */
	public function getListeElements($idProjet) {
	
		$this->log->debug("Corbeille::getListeElements() Début");
		
		$listeElements;
		
		// Obtenir le code utilisateur en session
		$session = new Session();
		$cu = $session->get('codeUsager');
		
		// Vérifier si l'usager est admin
		$u = new Usager($this->log, $this->dbh);
		$u->getUsagerParCodeUsager($cu);
		
		// Utiliser la bonne méthode selon les accès
		if ( $u->isAdmin() ) {
			$listeElements = $this->getListeElementsAdmin($idProjet);
		} else {
			$listeElements = $this->getListeElementsUtilisateur($idProjet);
		}
		
		$this->log->debug("Corbeille::getListeElements() Fin");
		
		return $listeElements;
		
	}
		
	
	/**
	 * 
	 * Obtenir la liste des éléments pour un utilisateur (ids seulements)
	 * @param String idProjet
	 * 
	 */
	public function getListeElementsUtilisateur($idProjet) {

		$this->log->debug("Corbeille::getListeElementsUtilisateur() Début");
		
		$listeElements = array(); 
		
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		try {
			// Obtenir la liste des éléments pour l'utilisateur
			$sql = "select concat(?, id_questionnaire) as id_element, ? as type, titre 
						from tquestionnaire 
						where id_projet = ?
						and statut = ?
					union
					select concat(?, id_item) as id_element, ? as type, titre
						from titem
						where id_projet = ?
						and statut = ?
					union
					select concat(?, id_collection) as id_element, ? as type, titre
						from tcollection
						where id_projet = ?
						and statut = ?
					union
					select concat(?, id_categorie) as id_element, ? as type, titre
						from tcategorie
						where id_projet = ?
						and statut = ?
					union
					select concat(?, id_langue) as id_element, ? as type, titre
						from tlangue
						where id_projet = ?
						and statut = ?					
					union
					select concat(?, id_media) as id_element, ? as type, titre
						from tmedia
						where id_projet = ?
						and statut = ?
					union					
					select concat(?, id_projet) as id_element, ? as type, titre
						from tprojet
						where statut = ?
						
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array(
										TXT_PREFIX_QUESTIONNAIRE, TXT_QUESTIONNAIRE, $idProjet, '0', 
										TXT_PREFIX_ITEM, TXT_ITEM, $idProjet, '0', 
										TXT_PREFIX_COLLECTION, TXT_COLLECTION, $idProjet, '0', 
										TXT_PREFIX_CATEGORIE, TXT_CATEGORIE, $idProjet, '0',
										TXT_PREFIX_LANGUE, TXT_LANGUE, $idProjet, '0',
										TXT_PREFIX_MEDIA, TXT_MEDIA, $idProjet, '0',
										TXT_PREFIX_PROJET, TXT_PROJET, '2',
										));
			
			// Vérifier qu'on a trouvé au moins un élément	
			if ($sth->rowCount() > 0) {
				
				// Récupérer les ids
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$element = array();
					
					$id = substr($row['id_element'],1);
					$type = $row['type'];
					$titre = $row['titre'];
					
					array_push($listeElements, $type . "_" . $id);
				}
				
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Corbeille::getListeElementsUtilisateur() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			

		$this->log->debug("Corbeille::getListeElementsUtilisateur() Fin");		
		
		return $listeElements;		
	}


	/**
	 *
	 * Obtenir la liste des éléments du côté admin(ids seulements)
	 * @param String idProjet
	 *
	 */
	public function getListeElementsAdmin($idProjet) {
	
		$this->log->debug("Corbeille::getListeElementsAdmin() Début");
	
		$listeElements = array();
	
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
	
		try {
			// Obtenir la liste des éléments pour l'utilisateur
			$sql = "select concat(?, id_questionnaire) as id_element, ? as type, titre
			from tquestionnaire
			where id_projet = ?
			and statut = ?
			union
			select concat(?, id_item) as id_element, ? as type, titre
			from titem
			where id_projet = ?
			and statut = ?
			union
			select concat(?, id_collection) as id_element, ? as type, titre
			from tcollection
			where id_projet = ?
			and statut = ?
			union
			select concat(?, id_categorie) as id_element, ? as type, titre
			from tcategorie
			where id_projet = ?
			and statut = ?
			union
			select concat(?, id_langue) as id_element, ? as type, titre
			from tlangue
			where id_projet = ?
			and statut = ?
			union
			select concat(?, id_media) as id_element, ? as type, titre
			from tmedia
			where id_projet = ?
			and statut = ?
			union
			select concat(?, id_projet) as id_element, ? as type, titre
			from tprojet
			where statut = ?
			union
			select concat(?, id_usager) as id_element, ? as type, concat(tusager.nom, ', ', tusager.prenom) as titre
			from tusager
			where statut = ?
	
			order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array(
			TXT_PREFIX_QUESTIONNAIRE, TXT_QUESTIONNAIRE, $idProjet, '0',
			TXT_PREFIX_ITEM, TXT_ITEM, $idProjet, '0',
			TXT_PREFIX_COLLECTION, TXT_COLLECTION, $idProjet, '0',
			TXT_PREFIX_CATEGORIE, TXT_CATEGORIE, $idProjet, '0',
					TXT_PREFIX_LANGUE, TXT_LANGUE, $idProjet, '0',
					TXT_PREFIX_MEDIA, TXT_MEDIA, $idProjet, '0',
					TXT_PREFIX_PROJET, TXT_PROJET, '2',
					TXT_PREFIX_USAGER, TXT_UTILISATEUR, USAGER::STATUT_SUPPRIME
					));
						
					// Vérifier qu'on a trouvé au moins un élément
					if ($sth->rowCount() > 0) {
	
					// Récupérer les ids
					while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$element = array();
						
					$id = substr($row['id_element'],1);
			$type = $row['type'];
			$titre = $row['titre'];
				
			array_push($listeElements, $type . "_" . $id);
			}
	
			}
			} catch (Exception $e) {
			Erreur::erreurFatal('018', "Corbeille::getListeElementsAdmin() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
	
			$this->log->debug("Corbeille::getListeElementsAdmin() Fin");
	
			return $listeElements;
			}	
	
	
	/**
	 * 
	 * Obtenir la liste des éléments (objets)
	 * @param Array Liste des ids éléments
	 * @param Projet instance projet
	 * @param Pagination obj pagination
	 */
	public function getListeElementsObjets($listeElements, $projet, $pagination) {	

		$this->log->debug("Corbeille::getListeElementsObjets() Début");
		
		$listeCorbeille = array();
		
		if ($pagination->getNbResultats() > 0) {
			for ($i = $pagination->getIndexDebut() ; $i <= $pagination->getIndexFin() ; $i++ ) {
			
				// Récupérer le type et l'id
				$identifiant = $listeElements[$i];
				$infos = explode("_", $identifiant);
				$type = $infos[0];
				$id = $infos[1];
				
				// Obtenir les informations de cet élément
				if ($type == TXT_QUESTIONNAIRE) {
					$element = new Questionnaire($this->log, $this->dbh);
					$element->getQuestionnaireParId($id, $projet->get("id_projet"));
					$element->set("id_element", $element->get("id_questionnaire"));
					$element->set("id_prefix", TXT_PREFIX_QUESTIONNAIRE . $element->get("id_element")); 
				} elseif ($type == TXT_ITEM) {
					$factory = new Item($this->log, $this->dbh);
					$element = $factory->instancierItemParType('', $projet->get("id_projet"), $id);
					$element->set("id_element", $element->get("id_item"));
					$element->set("id_prefix", TXT_PREFIX_ITEM . $element->get("id_element")); 
				} elseif ($type == TXT_COLLECTION) {
					$element = new Collection($this->log, $this->dbh);
					$element->getCollectionParId($id, $projet->get("id_projet"));
					$element->set("id_element", $element->get("id_collection"));
					$element->set("id_prefix", TXT_PREFIX_COLLECTION . $element->get("id_element"));
				} elseif ($type == TXT_CATEGORIE) {
					$element = new Categorie($this->log, $this->dbh);
					$element->getCategorieParId($id, $projet->get("id_projet"));
					$element->set("id_element", $element->get("id_categorie"));
					$element->set("id_prefix", TXT_PREFIX_CATEGORIE . $element->get("id_element"));
 				} elseif ($type == TXT_LANGUE) {
					$element = new Langue($this->log, $this->dbh);
					$element->getLangueParId($id, $projet->get("id_projet"));
					$element->set("id_element", $element->get("id_langue"));
					$element->set("id_prefix", TXT_PREFIX_LANGUE . $element->get("id_element"));
 				} elseif ($type == TXT_MEDIA) {
					$element = new Media($this->log, $this->dbh);
					$element->getMediaParId($id, $projet->get("id_projet"));
					$element->set("id_element", $element->get("id_media"));
					$element->set("id_prefix", TXT_PREFIX_MEDIA . $element->get("id_element"));
				} elseif ($type == TXT_PROJET) {
					$element = new Projet($this->log, $this->dbh);
					$element->getProjetParId($id);
					$element->set("id_element", $element->get("id_projet"));
					$element->set("id_prefix", TXT_PREFIX_PROJET . $element->get("id_element"));
				} elseif ($type == TXT_UTILISATEUR) {
					$element = new Usager($this->log, $this->dbh);
					$element->getUsagerParIdUsager($id);
					$element->set("id_element", $element->get("id_usager"));
					$element->set("id_prefix", TXT_PREFIX_USAGER . $element->get("id_element"));					
				}
					
				$element->set("type", $type);
					
				// Ajouter aux résultats de recherche
				array_push($listeCorbeille, $element);
			}
		}
				
		$this->log->debug("Corbeille::getListeElementsObjets() Fin");
		
		return $listeCorbeille;
	}
	
	/**
	 * 
	 * Effectuer une recherche dans les éléments qui sont à la corbeille
	 * @param String chaine
	 * @param String idProjet
	 * @param String idUsager
	 */
	public function recherche($chaine, $idProjet, $idUsager) {

		$this->log->debug("Corbeille::recherche() Début chaine = '$chaine'  idProjet = '$idProjet'  idUsager = '$idUsager'");
		$listeElements = array(); 
				
		// Obtenir le code utilisateur en session
		$session = new Session();
		$cu = $session->get('codeUsager');
		
		// Obtenir les infos de l'usager afin de vérifier s'il est admin
		$u = new Usager($this->log, $this->dbh);
		$u->getUsagerParCodeUsager($cu);
		
		// Préparer la chaîne de recherche
		$rech = '%' . Web::nettoyerChaineRech($chaine) . '%';

		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		// QUESTIONNAIRES ET ITEMS (1 de 2) Obtenir les ids des questionnaires qui contiennent les termes de recherche
		$quest = new Questionnaire($this->log, $this->dbh);
		$listeIdQuest = $quest->rechercheQuestionnnaire($rech, $idProjet);

		// QUESTIONNAIRES ET ITEMS (2 de 2) Obtenir les ids des items qui contiennent les termes de recherche
		$listeIdQuestItems = Item::rechercheQuestionnairesContenantItems($rech, $idProjet, $this->log, $this->dbh);	
		
		// Fusionner les ids des questionnaires trouvés
		$listeQuest = "";
		if (empty($listeIdQuest)) {
			$listeIdTous = $listeIdQuestItems;
		}
		
		if (empty($listeIdQuestItems)) {
			$listeIdTous = $listeIdQuest;
		}
		
		if (! empty($listeIdQuest) && ! empty($listeIdQuestItems) ) {
			$listeTmp = array_merge($listeIdQuest, $listeIdQuestItems);
			$listeIdTous = array_unique($listeTmp);
		}
		
		$listeQuest = implode(",", $listeIdTous);
		
		// ITEMS Obtenir les ids des items qui contiennent les termes de recherche -- ITEMS SEULEMENT --
		$item = new Item($this->log, $this->dbh);
		$listeItems = implode(",", $item->rechercheItems($rech, $idProjet, "*", $this->log, $this->dbh));

		// COLLECTIONS Obtenir les ids des collections qui contiennent les termes de recherche
		$collection = new Collection($this->log, $this->dbh);
		$listeCollections = implode(",", $collection->rechercheCollections($rech, $idProjet, $this->log, $this->dbh));

		// CATÉGORIES Obtenir les ids des catégories qui contiennent les termes de recherche
		$categorie = new Categorie($this->log, $this->dbh);
		$listeCategories = implode(",", $categorie->rechercheCategories($rech, $idProjet, $this->log, $this->dbh));

		// LANGUES Obtenir les ids des langues qui contiennent les termes de recherche
		$langue = new Langue($this->log, $this->dbh);
		$listeLangues = implode(",", $langue->rechercheLangues($rech, $idProjet, $this->log, $this->dbh));

		// MÉDIAS Obtenir les ids des médias qui contiennent les termes de recherche
		$media = new Media($this->log, $this->dbh);
		$listeMedias = implode(",", $media->rechercheMedias($rech, $idProjet, $this->log, $this->dbh));
		
		// PROJETS Obtenir les ids des projets qui contiennent les termes de recherche
		$projet = new Projet($this->log, $this->dbh);
		
		if ($u->isAdmin()) {
			$listeProjets = implode(",", $projet->recherche($rech, "id_projet", PROJET::STATUT_SUPPRIME,  $idUsager, "", true));
		} else {
			$listeProjets = implode(",", $projet->recherche($rech, "id_projet", PROJET::STATUT_SUPPRIME,  $idUsager, "", false));
		}
		
		// UTILISATEURS -- Recherche permise pour les admins seulement
		$listeUsagers = "";
		
		if ($u->isAdmin()) {
			$usr = new Usager($this->log, $this->dbh);
			$listeTousUsagers = $usr->recherche($rech, USAGER::STATUT_SUPPRIME, USAGER::STATUT_SUPPRIME);
			
			// Obtenir la liste des usagers
			$listeIdUsagers = array();
			foreach ($listeTousUsagers as $u) {
				// Ajouter aux résultats de recherche
				array_push($listeIdUsagers, $u->get("id_usager"));
			}
			
			$listeUsagers = implode(",", $listeIdUsagers);
		}
		
		// Cas limite où les listes sont vides
		if ($listeQuest == '') {
			$listeQuest = "-1";
		}
		if ($listeItems == '') {
			$listeItems = "-1";
		}
		if ($listeCollections == '') {
			$listeCollections = "-1";
		}
		if ($listeCategories == '') {
			$listeCategories = "-1";
		}
		if ($listeLangues == '') {
			$listeLangues = "-1";
		}
		if ($listeMedias == '') {
			$listeMedias = "-1";
		}
		if ($listeProjets == '') {
			$listeProjets = "-1";
		}	
		if ($listeUsagers == '') {
			$listeUsagers = "-1";
		}
						
		if ($listeQuest != "" || $listeItems != "" || $listeCollections != "" || $listeCategories != "" || $listeLangues != "" || $listeMedias != "" || $listeProjets != "" ) {
			
			try {
				// Obtenir la liste des éléments pour l'utilisateur
				$sql = "select concat(?, id_questionnaire) as id_element, ? as type, titre 
							from tquestionnaire 
							where id_projet = ?
							and id_questionnaire in ( " . $listeQuest . ")
							and statut = ?
						union 
							select concat(?, id_item) as id_element, ? as type, titre
								from titem
								where id_projet = ?
								and id_item in ( " . $listeItems . ")
								and statut = ?
						union 
							select concat(?, id_collection) as id_element, ? as type, titre
								from tcollection
								where id_projet = ?
								and id_collection in ( " . $listeCollections . ")
								and statut = ?	
						union 
							select concat(?, id_categorie) as id_element, ? as type, titre
								from tcategorie
								where id_projet = ?
								and id_categorie in ( " . $listeCategories . ")
								and statut = ?
						union 
							select concat(?, id_langue) as id_element, ? as type, titre
								from tlangue
								where id_projet = ?
								and id_langue in ( " . $listeLangues . ")
								and statut = ?							
						union 
							select concat(?, id_media) as id_element, ? as type, titre
								from tmedia
								where id_projet = ?
								and id_media in ( " . $listeMedias . ")
								and statut = ?							

						union 
							select concat(?, id_projet) as id_element, ? as type, titre
								from tprojet
								where id_projet in ( " . $listeProjets . ")
								and statut = ?
						union 
							select concat(?, id_usager) as id_element, ? as type, concat(nom, ', ', prenom) as titre
								from tusager
								where id_usager in ( " . $listeUsagers . ")
								and statut = ?									
								
						order by $tri";
				$sth = $this->dbh->prepare($sql);
				$rows = $sth->execute(array( TXT_PREFIX_QUESTIONNAIRE, TXT_QUESTIONNAIRE, $idProjet, '0', 
											 TXT_PREFIX_ITEM, TXT_ITEM, $idProjet, '0',
											 TXT_PREFIX_COLLECTION, TXT_COLLECTION, $idProjet, '0',
											 TXT_PREFIX_CATEGORIE, TXT_CATEGORIE, $idProjet, '0',
											 TXT_PREFIX_LANGUE, TXT_LANGUE, $idProjet, '0',
											 TXT_PREFIX_MEDIA, TXT_MEDIA, $idProjet, '0',
											 TXT_PREFIX_PROJET, TXT_PROJET, PROJET::STATUT_SUPPRIME,
											 TXT_PREFIX_USAGER, TXT_UTILISATEUR, USAGER::STATUT_SUPPRIME
											));
				
				// Vérifier qu'on a trouvé au moins un élément	
				if ($sth->rowCount() > 0) {
				
					// Récupérer les ids
					while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
						$element = array();
						
						$id = substr($row['id_element'],1);
						$type = $row['type'];
						$titre = $row['titre'];
						
						array_push($listeElements, $type . "_" . $id);
					}
				}
			} catch (Exception $e) {
			Erreur::erreurFatal('018', "Corbeille::recherche() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}				
		}
				
		// Terminé
		$this->log->debug("Corbeille::recherche() Fin");
		return $listeElements;		
	}	
	
	
	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des éléments
	 * 
	 */
	public function getTri() {
		
		$this->log->debug("Corbeille::getTri() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("corbeille_pref_tri_champ");
		$triSessionOrdre = $session->get("corbeille_pref_tri_ordre");
		$this->log->debug("Corbeille::getTriQuestionnaire() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Corbeille::getTriQuestionnaire() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_element", "type", "titre");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_element";
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
			$triParamChamp = "id_element";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		$session->set("corbeille_pref_tri_champ", $triParamChamp);
		$session->set("corbeille_pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Corbeille::getTri() Fin");
				
		return $triParamChamp . " " . $triParamOrdre;
	}	

	
	/**
	 * 
	 * Récupérer un ou plusieurs éléments 
	 * @param String listeElements
	 * @param String idProjet
	 *
	 */
	public function recupererListeElements($listeElements, $idProjet) {	
		
		$this->log->debug("Corbeille::recupererListeElements() Début");
		
		if (! empty($listeElements) ) {
			foreach ($listeElements as $element) {
				
				// Déterminer le type d'élément
				$type = substr($element,0,1);
				$id = substr($element,1);

				if ($type == TXT_PREFIX_QUESTIONNAIRE) {
					$q = new Questionnaire($this->log, $this->dbh);
					$q->getQuestionnaireParId($id, $idProjet);
					$q->activer();
				} elseif ($type == TXT_PREFIX_ITEM) {
					$factory = new Item($this->log, $this->dbh);
					$i = $factory->instancierItemParType('', $idProjet, $id);
					$i->activer();
				} elseif ($type == TXT_PREFIX_COLLECTION) {
					$c = new Collection($this->log, $this->dbh);
					$c->getCollectionParId($id, $idProjet);
					$c->activer();
				} elseif ($type == TXT_PREFIX_CATEGORIE) {
					$c = new Categorie($this->log, $this->dbh);
					$c->getCategorieParId($id, $idProjet);
					$c->activer();
				} elseif ($type == TXT_PREFIX_LANGUE) {
					$c = new Langue($this->log, $this->dbh);
					$c->getLangueParId($id, $idProjet);
					$c->activer();
				} elseif ($type == TXT_PREFIX_MEDIA) {
					$m = new Media($this->log, $this->dbh);
					$m->getMediaParId($id, $idProjet);
					$m->activer();
				} elseif ($type == TXT_PREFIX_PROJET) {
					$m = new Projet($this->log, $this->dbh);
					$m->getProjetParId($id);
					$m->activer();
				} elseif ($type == TXT_PREFIX_USAGER) {
					$m = new Usager($this->log, $this->dbh);
					$m->getUsagerParIdUsager($id);
					$m->activer();
				}
			}
		}
		
		$this->log->debug("Corbeille::recupererListeElements() Fin");
	}
	
	
	/**
	 * 
	 * Supprimer un ou plusieurs éléments de la corbeille 
	 * 
	 * @param String listeElements
	 * @param String idProjet
	 *
	 */
	public function supprimerListeElements($listeElements, $idProjet) {	
		
		$this->log->debug("Corbeille::supprimerListeElements() Début");
	
		if (! empty($listeElements) ) {
			foreach ($listeElements as $element) {
				
				// Déterminer le type d'élément
				$type = substr($element,0,1);
				$id = substr($element,1);

				if ($type == TXT_PREFIX_QUESTIONNAIRE) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer questionnaire '$id'");
					$q = new Questionnaire($this->log, $this->dbh);
					$q->getQuestionnaireParId($id, $idProjet);
					$q->supprimer();
				} elseif ($type == TXT_PREFIX_ITEM) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer item '$id'");
					$factory = new Item($this->log, $this->dbh);
					$i = $factory->instancierItemParType('', $idProjet, $id);
					$i->supprimer($idProjet, $id);
				} elseif ($type == TXT_PREFIX_COLLECTION) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer collection '$id'");
					$col = new Collection($this->log, $this->dbh);
					$col->getCollectionParId($id, $idProjet);
					$col->supprimer();
				} elseif ($type == TXT_PREFIX_CATEGORIE) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer categorie '$id'");
					$cat = new Categorie($this->log, $this->dbh);
					$cat->getCategorieParId($id, $idProjet);
					$cat->supprimer();
				} elseif ($type == TXT_PREFIX_LANGUE) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer langue '$id'");
					$l = new Langue($this->log, $this->dbh);
					$l->getLangueParId($id, $idProjet);
					$l->supprimer();
				} elseif ($type == TXT_PREFIX_MEDIA) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer media '$id'");
					$m = new Media($this->log, $this->dbh);
					$m->getMediaParId($id, $idProjet);
					$m->supprimer();
				} elseif ($type == TXT_PREFIX_PROJET) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer projet '$id'");
					$m = new Projet($this->log, $this->dbh);
					$m->getProjetParId($id);
					$m->supprimer();
				} elseif ($type == TXT_PREFIX_USAGER) {
					$this->log->debug("Corbeille::supprimerListeElements() Supprimer usager '$id'");
					$m = new Usager($this->log, $this->dbh);
					$m->getUsagerParIdUsager($id);
					$m->supprimer();
				}
			}
		}
		
		$this->log->debug("Corbeille::supprimerListeElements() Fin");
	}	
	
	/**
	 * 
	 * Préparer les données pour le web 
	 * 
	 *
	 */
	public function preparerAffichageWeb() {

		$this->log->debug("Corbeille::preparerAffichageWeb() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("corbeille_pref_tri_champ");
		$tri_ordre = $session->get("corbeille_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Corbeille::preparerAffichageWeb() Fin");		
		
		return;
	}	

	
	/**
	 * 
	 * Obtenir une valeur
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
	 * 
	 */
	public function set( $libelle, $valeur ) {
		$this->donnees[$libelle] = $valeur;
	}	
	
}

?>
