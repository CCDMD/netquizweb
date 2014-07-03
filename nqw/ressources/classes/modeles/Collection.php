<?php

/** 
 * Classe Collection
 * 
 * Permet à l'usager de regrouper des questionnaires
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Collection {
	
	protected $dbh;
	protected $log;
	
	protected $listeChamps = "id_collection, id_projet, titre, remarque, statut, date_creation, date_modification";
							  
	protected $donnees;
	
	/**
	 * 
	 * Constructeur
	 * @param Log $log
	 * @param PDO $dbh
	 * 
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$this->dbh = $dbh;
		$this->log = $log;
		
		$donnees = array();
		
		return;
	}


	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'une collection
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Collection::ajouter() Début");
		
		try {
			// Obtenir le prochain id pour une collection
			$projet = new Projet($this->log, $this->dbh);
			$projet->getProjetParId($this->get("id_projet"));
			$idCollection = $projet->genererIdCollection();
			$this->set("id_collection", $idCollection);
			
			// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
			if ( trim($this->get("titre")) == "") {
				$this->set("titre", TXT_NOUVELLE_COLLECTION);
			}
			
			// Préparer ajout
			$stmt = $this->dbh->prepare("insert into tcollection (id_collection, id_projet, titre, remarque, statut, date_creation, date_modification) 
										 values (?, ?, ?, ?, 1, now(),now() )");
	
			// Statut par défaut = brouillon
			$this->set("statut", "1");
			
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_collection'),
								 $this->get('id_projet'), 
								 $this->get('titre'),
								 $this->get('remarque'),
								 ));
			
			$this->log->debug("Collection::ajouter() Nouvelle collection créée (id = '" . $this->get('id_collection') . "')");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		$this->log->debug("Collection::ajouter() Fin");
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("Collection::ajouter() Fin");
		
		return;
	}	

	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une collection
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Collection::enregistrer() Début");

		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVELLE_COLLECTION);
		}
		
		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update tcollection 
										 set titre = ?,
										 	 remarque = ?,
										 	 statut = ?,
								  		 	 date_modification = now()										
										 where id_collection = ? 
										 and id_projet = ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('titre'),
									$this->get('remarque'),
									$this->get('statut'),
									$this->get('id_collection'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
								
		// Mettre à jour l'index
		$this->indexer();
										
		$this->log->debug("Collection::enregistrer() Fin");
										
		return;
	}		

	/**
	 * 
	 * Charger la collection à partir de la base de données
	 * @param String idCollection
	 * @param String idProjet
	 * 
	 */
	public function getCollectionParId($idCollection, $idProjet) {

		$this->log->debug("Collection::getCollectionParId() Début idCollection = '$idCollection'  idProjet = '$idProjet'");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select " . $this->listeChamps . " 
					from 
					  tcollection 
					where 
					  id_collection = ? 
					  and id_projet = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idCollection, $idProjet));
			
			// Vérifier qu'on a trouvé au moins une collection
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune collection trouvée pour l'id '$idCollection'");
			}
			
			// Vérifier qu'une seule collection est retournée, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour la collection id '$idCollection' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour la collection
				$result = $sth->fetchAll();
			
			    foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->set($cle,$row[$cle]);
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
		        
		        // Indiquer qu'une seule collection a été trouvée
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::getCollectionParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Terminé
		$this->log->debug("Collection::getCollectionParId() Trouve = '$trouve'");
		$this->log->debug("Collection::getCollectionParId() Fin");
		return $trouve;		
	}

	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des collections
	 * 
	 */
	public function getTri() {
		
		$this->log->debug("Collection::getTri() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("collection_pref_tri_champ");
		$triSessionOrdre = $session->get("collection_pref_tri_ordre");
		$this->log->debug("Collection::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Collection::getTri() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_collection", "titre", "remarque", "date_modification");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_collection";
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
			$triParamChamp = "id_collection";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		$session->set("collection_pref_tri_champ", $triParamChamp);
		$session->set("collection_pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Collection::getTri() Fin");
		
		return $triParamChamp . " " . $triParamOrdre;
	}	
	
	
	/**
	 * 
	 * Obtenir les valeurs de la collection à partir de la requête web
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {

		$this->log->debug("Collection::getDonneesRequete() Début");
		
		// Obtenir les paramètres
		$params = Web::getListeParam("collection_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'";
		}
		
		$this->log->debug("Collection::getDonneesRequete() Fin");
		return;
	}		
	
	
	/**
	 * 
	 * Obtenir la liste des collections
	 * @param String idProjet
	 */
	public function getListeCollections($idProjet) {

		$this->log->debug("Collection::getListeCollections() Début");
		$collections = array(); 
		
		// Ajouter la collection null par défaut
		$collections[0] = "";

		try {
			$sql = "select id_collection, titre from tcollection where id_projet = ? and statut != 0 order by titre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins une collection	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune collection trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des collections
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_collection'];
	  				// $titre = htmlspecialchars(utf8_decode($row['titre']));
	  				$titre = $row['titre'];
	  				$collections[$id] = $titre;	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::getListeCollections() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Terminé
		$this->log->debug("Collection::getListeCollections() Fin");
		return $collections;		
	}

	
	/**
	 * 
	 * Obtenir la liste des collections
	 * @param String idProjet
	 * 
	 */
	public function getListeIdCollections($idProjet) {

		$this->log->debug("Collection::getListeIdCollections() Début");
		$listeCollections = array(); 

		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		try {
			$sql = "select id_collection from tcollection where id_projet = ? and statut != 0 order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins une collection	
			if ($sth->rowCount() == 0) {
				$this->log->info("Collection::getListeIdCollections() Aucune collection trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des collections
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_collection'];
	  				array_push($listeCollections, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::getListeIdCollections() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			

		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_collections", $listeCollections);
		
		// Terminé
		$this->log->debug("Collection::getListeIdCollections() Fin");
		return $listeCollections;		
	}	
	
	
	/**
	 *
	 * Obtenir la liste des collections du projet
	 * @param String idProjet
	 *
	 */
	public function getListeIdCollectionsDuProjet($idProjet) {
	
		$this->log->debug("Collection::getListeIdCollectionsDuProjet() Début");
		$listeCollections = array();
	
		try {
			$sql = "select id_collection from tcollection where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
				
			// Vérifier qu'on a trouvé au moins une collection
			if ($sth->rowCount() == 0) {
				$this->log->info("Collection::getListeIdCollectionsDuProjet() Aucune collection trouvée pour le projet '$idProjet'");
			}
			else {
				// Récupérer les ids des collections
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_collection'];
					array_push($listeCollections, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::getListeIdCollectionsDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_collections", $listeCollections);
	
		// Terminé
		$this->log->debug("Collection::getListeIdCollectionsDuProjet() Fin");
		return $listeCollections;
	}	

	
	/**
	 * 
	 * Effectuer une recherche dans les collections seulement
	 * @param String chaine
	 * @param String idProjet
	 * @param Log log
	 * @param PDO dbh
	 * 
	 */
	public function rechercheCollections($chaine, $idProjet, $log, $dbh) {
	
		$log->debug("Collection::rechercheCollections() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeCollections = array(); 

		// Préparer la chaîne de recherche
		$chaine = Web::nettoyerChaineRech($chaine);
		
		try {
			// Obtenir le tri à utiliser
			$tri = $this->getTri();
			
			$sql = "select 
					  	tcollection_index.id_collection 
					from 
						tcollection_index,
						tcollection 
					where 
						tcollection_index.id_projet = ? and
						tcollection_index.texte like ? and
						tcollection.id_collection = tcollection_index.id_collection and 
						tcollection.id_projet = tcollection_index.id_projet
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $chaine));
			
			// Vérifier qu'on a trouvé au moins une collection
			if ($sth->rowCount() == 0) {
				$this->log->info("Collection::getListeIdCollections() Aucune collection trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des collections
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_collection'];
	  				array_push($listeCollections, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::rechercheCollections() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_collections", $listeCollections);
				
		// Terminé
		$log->debug("Collection::rechercheCollections() Fin");		
		return $listeCollections;
	}	
		
	
	/**
	 * 
	 * Dupliquer la collection
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Collection::dupliquer() Début");
	
		// Retirer l'id initial
		$this->set("id_collection", "");
		
		// Ajouter un astérisque devant le titre
		$titre = "*" . $this->get("titre");
		$this->set("titre", $titre);
		
		// Ajouter la nouvelle collection
		$this->ajouter();
		
		$this->log->debug("Collection::dupliquer() Fin");
	}	
	
	/**
	 * 
	 * Imprimer une collection
	 *
	 */
	public function imprimer() {
	
		$this->log->debug("Collection::imprimer() Début");
	
		// Déterminer gabarit d'impression
		$gabaritImpression = REPERTOIRE_GABARITS_IMPRESSION . "collection-details.php";
		
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritImpression)) {
			$this->log->erreur("Le gabarit d'impression '$gabaritImpression' ne peut être localisé.");
		}
		
		// Obtenir le contenu pour impression
		$contenu = Fichiers::getContenuElement($gabaritImpression , $this);

		// L'ajouter à la collection
		$this->set("contenu", $contenu);
		
		// Déterminer le gabarit à utiliser pour l'impression
		$this->set("gabarit_impression", IMPRESSION_GABARIT_COLLECTION);

		$this->log->debug("Collection::imprimer() Fin");
		
		return $contenu;
	}		
	
	/**
	 * 
	 * Désactiver la collection (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
		
		$this->log->debug("Collection::desactiver()");
		
		$this->set("statut","0");
		$this->enregistrer();
		
		$this->log->debug("Collection::desactiver() Fin");
	}		
	

	/**
	 * 
	 * Activer la collection
	 *
	 */
	public function activer() {
		
		$this->log->debug("Collection::activer() Début");
		
		// Activer le suivi
		$this->set("statut", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Collection::activer() Fin");
	}	

	/**
	 * 
	 * Supprimer une collection
	 */
	public function supprimer() {
		
		$this->log->debug("Collection::supprimer() Début");
	
		try {
			// Supprimer l'index existant
			$sql = "delete from tcollection_index where id_projet = ? and id_collection = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_collection")));
			$this->log->debug("Collection: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_collection = '" . $this->get("id_collection") . "'");
			$this->log->debug("Collection: updateIndex() Suppression complétée");			
			
			// Supprimer la collection de la table
			$this->log->debug("Collection::supprimer() Supprimer la collection '" . $this->get("id_collection") . "' de l'usager '" . $this->get("id_projet") . "'");
			$sql = "delete from tcollection where id_projet = ? and id_collection= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_collection")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Collection::supprimer() Fin");
	}		

	
	/**
	 * 
	 * Préparer l'index de recherche
	 * 
	 */
	protected function preparerIndex() {
		
		$this->log->debug("Collection: preparerIndex() Début");
		
		$index = "";
		$index .= TXT_PREFIX_COLLECTION . $this->get("id_Collection") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("remarque") . " ";

		$this->log->debug("Collection: preparerIndex() Fin");
		
		return $index;
	}

	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String index
	 * 
	 */
	protected function updateIndex($index) {
		
		$this->log->debug("Collection: updateIndex() Début  index = '$index'");
		
		// Nettoyer la chaîne de recherche
		$index = Web::nettoyerChaineRech($index);
		
		try {
			// Supprimer l'index existant au besoin
			$sql = "delete from tcollection_index where id_projet = ? and id_collection = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_collection")));
			$this->log->debug("Collection: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_collection = '" . $this->get("id_collection") . "'");
			$this->log->debug("Collection: updateIndex() Suppression complétée");
			
			// Insérer l'index
			$this->log->debug("Collection: updateIndex() Ajout des données d'index pour : idProjet = '" . $this->get("id_projet") . "'  id_collection = '" . $this->get("id_collection") . "'");
			$sql = "insert into tcollection_index (id_projet, id_collection, texte, date_creation, date_modification)
					values (?, ?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_collection"), $index));
			$this->log->debug("Collection: updateIndex() Ajout complété");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Collection: updateIndex() Fin");
	}		
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("Collection: indexer() Début");
		
		// Préparer l'index
		$index = $this->preparerIndex();
		
		// Mettre à jour l'index
		$this->updateIndex($index);
		
		$this->log->debug("Collection: indexer() Fin");
	}	
	
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Collection::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_collection, id_projet
					FROM 	tcollection";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins une collection
			if ($sth->rowCount() == 0) {
				$this->log->info("Collection::reindexer()  Aucune collection localisée");
			} else {
	
				// Récupérer les ids des collection et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Récupérer l'id de la collection
					$idProjet = $row['id_projet'];
					$idCollection = $row['id_collection'];
	
					// Obtenir la collection
					$q = new Collection($this->log, $this->dbh);
					$q->getCollectionParId($idCollection, $idProjet);
	
					// Réindexer
					$this->log->info("Collection::reindexer()  Indexation pour la collection '$idCollection' et projet '$idProjet'");
					$q->indexer();
					$this->log->info("Collection::reindexer()  Indexation complétée pour la collection '$idCollection'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Collection::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Collection::reindexer() Fin");
		return $nbMAJ;
	}	
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Collection::preparerAffichageListe() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("collection_pref_tri_champ");
		$tri_ordre = $session->get("collection_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Collection::preparerAffichageListe() Fin");		
		
		return;
	}	

	/**
	 * 
	 * Obtenir l'id de la collection à partir de la page demandée 
	 * @param String page
	 *
	 */
	public function getIdCollectionParPage($page) {

		$this->log->debug("Collection::getIdCollectionParPage() Début");

		$idCollection = "";
		$pageCour = $page - 1;
		
		// Obtenir la position de l'item dans les résultats
		$session = new Session();
		$listeCollections = $session->get("liste_collections");
	
		// Obtenir le nombre total de collections
		$pageTotal = count($listeCollections);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er item trouvé
			$idCollection = $listeCollections[0];		
		} else {
			$idCollection = $listeCollections[$pageCour];
		}
		
		$this->log->debug("Collection::getIdCollectionParPage() Fin");
			
		return $idCollection;
	}	
	

	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Collection::preparerAffichage() Début");
		
		// Obtenir la position de la collection dans les résultats
		$session = new Session();
		$listeCollections = $session->get("liste_collections");
		if ( is_array($listeCollections) ) { 
			$pageCour = array_search($this->get("id_collection"), $listeCollections);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de collection
		$pageTotal = count($listeCollections);
		
		// Obtenir la page suivante
		$pageSuiv = $pageCour + 1;
		if ($pageSuiv > $pageTotal) {
			$pageSuiv = $pageTotal;
		}
		
		// Obtenir la page précédente
		$pagePrec = $pageCour - 1;
		if ($pagePrec < 1) {
			$pagePrec = 1;
		}
		
		$this->set("pages_total", $pageTotal);
		$this->set("page_suivante", $pageSuiv );
		$this->set("page_precedente", $pagePrec );
		$this->set("page_courante", $pageCour );
			
		$this->log->debug("Collection::preparerAffichage() Fin");
	}	
	
	/**
	 *
	 * Exporter une collection en format XML
	 * @param Projet projet
	 * @param Usager usager
	 * @param array Liste de collections
	 *
	 */
	public function exporterListeCollectionsXML($projet, $usager, $listeCollections) {
	
		$this->log->debug("Collection::exporterXML() Début");
	
		$succes = 0;
		$urlFichierZip = "";
		$contenu = "";
		$listeMedias = array();
	
		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomBase = FICHIER_EXPORTATION_XML_COLLECTIONS;
	
		// Vérifier le nombre de collections à exporter et ajouter un "s" au besoin
		if (count($listeCollections) > 1) {
			$nomBase =  $nomBase . "s";
		}
	
		$nomRepertoireZip = Securite::nettoyerNomfichier($nomBase) . "_" . $ts . "_xml";
		$nomFichierZip = $nomRepertoireZip . ".zip";
		$urlFichierZip = URL_PUBLICATION . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomFichierZip;
	
		// Déterminer le répertoire de publication du XML
		$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/";
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";
	
		// Vérifier que le répertoire de destination n'existe pas
		if (!is_dir($repertoireDestination)) {
	
			$this->log->debug("Collection::exporterXML() Exportation d'une collection");
	
			// Exporter le Collection
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
	
			// Entête XML
			$contenu .= XML_ENTETE . "\n";
			$contenu .= XML_NQW_DEBUT . "\n";
	
			// Obtenir la liste des Collections en XML
			foreach ($listeCollections as $idCollection) {
				$c = new Collection($this->log, $this->dbh);
				$c->getCollectionParId($idCollection, $projet->get("id_projet"));
	
				// Obtenir le contenu XML
				$contenu .= $c->exporterXML($repertoireDestination);
			}
	
			// Fin du fichier XML
			$contenu .= XML_NQW_FIN . "\n";
	
			// Écrire le contenu dans un fichier XML
			$publication->ecrireFichier($repertoireDestination . FICHIER_EXPORTATION_XML, $contenu);
	
		} else {
			$this->log->debug("Collection::exporterXML() Impossible de publier le Collection - le répertoire existe déjà");
		}
			
		if ($succes == 1) {
			// Déterminer le répertoire source
			$repertoireSourceZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";
			$repertoireDestinationZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU;
	
			// Préparer le fichier zip
			$fichierZip = $repertoireDestinationZip . $nomFichierZip;
	
			// Zip des fichiers
			Fichiers::Zip($repertoireSourceZip, $fichierZip);
	
			// Supprimer le répertoire temporaire
			$this->log->debug("Collection::exporterXML() Suppression du répertoire '$repertoireSourceZip'");
			Fichiers::rmdirr($repertoireSourceZip);
		} else {
			$urlFichierZip = "";
		}
	
		$this->log->debug("Collection::exporterXML() Fin");
		return $urlFichierZip;
	}	
	
	/**
	 *
	 * Exporter une collection en format XML
	 * 
	 * @param string répertoire destination
	 *
	 */
	public function exporterXML($repertoireDestination) {
	
		$this->log->debug("Collection::exporterXML() Début");
	
		// Récupérer le gabarit pour publier une collection
		$contenu = Fichiers::getContenuCollection(REPERTOIRE_GABARITS_EXPORTATION . "collection.php", $this);
	
		$this->log->debug("Collection::exporterXML() Fin");
	
		return $contenu;
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
	
	/**
	 * 
	 * Obtenir une valeur pour impression
	 * 
	 */
	public function getImpression( $valeur, $nbLigne = 2 ) {
		
		$val = "";
		if (isset($this->donnees[$valeur]) && $this->donnees[$valeur] != "") {
			if ($nbLigne == 1) {
				$val = IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . html_entity_decode($this->donnees[$valeur], ENT_QUOTES, "UTF-8") . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;
			} elseif ($nbLigne == 2) {
				$val = IMPRESSION_HTML_PREFIX_VALEUR_DEUX_LIGNES . html_entity_decode($this->donnees[$valeur], ENT_QUOTES, "UTF-8") . IMPRESSION_HTML_SUFFIXE_VALEUR_DEUX_LIGNES;
			}
		} else {
			$val = IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . IMPRESSION_HTML_AUCUNE_VALEUR . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;
		}
		
		return $val;
	}
	
	/**
	 *
	 * Obtenir une valeur pour du XML
	 * 
	 */
	public function getXML( $valeur ) {
		return Web::nettoyerChainePourXML($this->get($valeur));
	}
	
	
}

?>
