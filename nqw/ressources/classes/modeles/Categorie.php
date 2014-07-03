<?php

/** 
 * Classe Categorie
 * 
 * Permet de regrouper des items
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Categorie {
	
	protected $dbh;
	protected $log;
	
	protected $listeChamps = "id_categorie, id_projet, titre, remarque, statut, date_creation, date_modification";
							  
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
	 * Sauvegarder les informations dans la base de données - ajout d'une categorie
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Categorie::ajouter() Début");
		
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVELLE_CATEGORIE);
		}		
		
		try {
			
			// Obtenir le prochain id pour une catégorie
			$projet = new Projet($this->log, $this->dbh);
			$projet->getProjetParId($this->get("id_projet"));
			$idCategorie = $projet->genererIdCategorie();
			$this->set("id_categorie", $idCategorie);			
			
			// Préparer ajout
			$stmt = $this->dbh->prepare("insert into tcategorie (id_categorie, id_projet, titre, remarque, statut, date_creation, date_modification) 
										 values (?, ?, ?, ?, 1, now(),now() )");
	
			// Statut par défaut = brouillon
			$this->set("statut", "1");
			
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_categorie'),
								 $this->get('id_projet'), 
								 $this->get('titre'),
								 $this->get('remarque'),
								 ));
			
			$this->log->debug("Categorie::ajouter() Nouvelle categorie créée (id = '" . $this->get('id_categorie') . "')");
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		$this->log->debug("Categorie::ajouter() Fin");
		
		// Mettre à jour l'index
		$this->indexer();
		
		return;
	}	

	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une categorie
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Categorie::enregistrer() Début");
		
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVELLE_CATEGORIE);
		}		

		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update tcategorie 
										 set titre = ?,
										 	 remarque = ?,
										 	 statut = ?,
								  		 	 date_modification = now()										
										 where id_categorie = ? 
										 and id_projet = ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('titre'),
									$this->get('remarque'),
									$this->get('statut'),
									$this->get('id_categorie'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}									

		// Mettre à jour l'index
		$this->indexer();								
								
		$this->log->debug("Categorie::enregistrer() Fin");
										
		return;
	}		

	/**
	 * 
	 * Charger la categorie à partir de la base de données
	 * @param String idCategorie
	 * @param String idProjet
	 * 
	 */
	public function getCategorieParId($idCategorie, $idProjet) {

		$this->log->debug("Categorie::getCategorieParId() Début idCategorie = '$idCategorie'  idProjet = '$idProjet'");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select " . $this->listeChamps . " 
					from 
					  tcategorie 
					where 
					  id_categorie = ? 
					  and id_projet = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idCategorie, $idProjet));
			
			// Vérifier qu'on a trouvé au moins une categorie
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune categorie trouvée pour l'id '$idCategorie'");
			}
			
			// Vérifier qu'une seule categorie est retournée, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour la categorie id '$idCategorie' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour la categorie
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
		        
		        // Indiquer qu'une seule categorie a été trouvée
		        $trouve = true;
		        
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::getCategorieParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Terminé
		$this->log->debug("Categorie::getCategorieParId() Trouve = '$trouve'");
		$this->log->debug("Categorie::getCategorieParId() Fin");
		return $trouve;		
	}

	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des categories
	 * 
	 */
	public function getTri() {
		
		$this->log->debug("Categorie::getTri() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("categorie_pref_tri_champ");
		$triSessionOrdre = $session->get("categorie_pref_tri_ordre");
		$this->log->debug("Categorie::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Categorie::getTri() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_categorie", "titre", "remarque", "date_modification");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_categorie";
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
			$triParamChamp = "id_categorie";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		$session->set("categorie_pref_tri_champ", $triParamChamp);
		$session->set("categorie_pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Categorie::getTri() Fin");
		
		return $triParamChamp . " " . $triParamOrdre;
	}	
	
	
	/**
	 * 
	 * Obtenir les valeurs de la catégorie à partir de la requête web
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {
		
		$this->log->debug("Categorie::getDonneesRequete() Début");

		// Obtenir les paramètres
		$params = Web::getListeParam("categorie_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'";
		}
		
		$this->log->debug("Categorie::getDonneesRequete() Fin");
		return;
	}		
	
	
	/**
	 * 
	 * Obtenir la liste des categories
	 * @param String idProjet
	 * 
	 */
	public function getListeCategories($idProjet) {

		$this->log->debug("Categorie::getListeCategories() Début");
		$categories = array(); 
		
		// Ajouter la categorie null par défaut
		$categories[0] = "";
				
		try {
			$sql = "select id_categorie, titre from tcategorie where id_projet = ? and statut != 0 order by titre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins une catégorie	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune categorie trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des categories
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_categorie'];
	  				$titre = $row['titre'];
	  				$categories[$id] = $titre;	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::getListeCategories() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Terminé
		$this->log->debug("Categorie::getListeCategories() Fin");
		return $categories;		
	}

	
	/**
	 * 
	 * Obtenir la liste des categories
	 * @param String idProjet
	 * 
	 */
	public function getListeIdCategories($idProjet) {

		$this->log->debug("Categorie::getListeIdCategories() Début");
		$listeCategories = array(); 

		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		try {
		
			$sql = "select id_categorie from tcategorie where id_projet = ? and statut != 0 order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins une catégorie	
			if ($sth->rowCount() == 0) {
				$this->log->info("Categorie::getListeIdCategories() Aucune categorie trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des categories
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_categorie'];
	  				array_push($listeCategories, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::getListeIdCategories() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_categories", $listeCategories);
		
		// Terminé
		$this->log->debug("Categorie::getListeIdCategories() Fin");
		return $listeCategories;		
	}	

	
	/**
	 *
	 * Obtenir la liste des categories du projet
	 * @param String idProjet
	 *
	 */
	public function getListeIdCategoriesDuProjet($idProjet) {
	
		$this->log->debug("Categorie::getListeIdCategoriesDuProjet() Début");
		$listeCategories = array();
	
		try {
	
			$sql = "select id_categorie from tcategorie where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
				
			// Vérifier qu'on a trouvé au moins une catégorie
			if ($sth->rowCount() == 0) {
				$this->log->info("Categorie::getListeIdCategoriesDuProjet() Aucune categorie trouvée pour le projet '$idProjet'");
			}
			else {
				// Récupérer les ids des categories
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_categorie'];
					array_push($listeCategories, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::getListeIdCategoriesDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Categorie::getListeIdCategoriesDuProjet() Fin");
		return $listeCategories;
	}	
	
	/**
	 * 
	 * Préparer l'index de recherche
	 * 
	 */
	protected function preparerIndex() {
		
		$this->log->debug("Categorie: preparerIndex() Début");
		
		$index = "";
		$index .= TXT_PREFIX_CATEGORIE . $this->get("id_categorie") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("remarque") . " ";

		$this->log->debug("Categorie: preparerIndex() Fin");
		
		return $index;
	}

	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idProjet
	 * 
	 */
	protected function updateIndex($index) {
		
		$this->log->debug("Categorie: updateIndex() Début  index = '$index'");
		
		// Nettoyer la chaîne de recherche
		$index = utf8_encode(Web::nettoyerChaineRech($index));

		try {
			// Supprimer l'index existant au besoin
			$sql = "delete from tcategorie_index where id_projet = ? and id_categorie = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_categorie")));
			$this->log->debug("Categorie: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_categorie = '" . $this->get("id_categorie") . "'");
			$this->log->debug("Categorie: updateIndex() Suppression complétée");
			
			// Insérer l'index
			$this->log->debug("Categorie: updateIndex() Ajout des données d'index pour : idProjet = '" . $this->get("id_projet") . "'  id_categorie = '" . $this->get("id_categorie") . "'");
			$sql = "insert into tcategorie_index (id_projet, id_categorie, texte, date_creation, date_modification)
					values (?, ?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_categorie"), $index));
			$this->log->debug("Categorie: updateIndex() Ajout complété");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Categorie: updateIndex() Fin");
	}		
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idProjet
	 */
	public function indexer() {
		
		$this->log->debug("Categorie: indexer() Début");
		
		// Préparer l'index
		$index = $this->preparerIndex();
		
		// Mettre à jour l'index
		$this->updateIndex($index);
		
		$this->log->debug("Categorie: indexer() Fin");
	}		
	
	
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Categorie::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_categorie, id_projet
					FROM 	tcategorie";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins une catégorie
			if ($sth->rowCount() == 0) {
				$this->log->info("Categorie::reindexer()  Aucune catégorie localisée");
			} else {
	
				// Récupérer les ids des catégories et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Récupérer l'id de la catégorie
					$idProjet = $row['id_projet'];
					$idCategorie = $row['id_categorie'];
						
					// Vérifier si le projet existe
					$p = new Categorie($this->log, $this->dbh);
						
					// Obtenir la catégorie
					$q = new Categorie($this->log, $this->dbh);
					$q->getCategorieParId($idCategorie, $idProjet);
	
					// Réindexer
					$this->log->info("Categorie::reindexer()  Indexation pour la catégorie '$idCategorie' et projet '$idProjet'");
					$q->indexer();
					$this->log->info("Categorie::reindexer()  Indexation complétée pour la catégorie '$idCategorie'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Categorie::reindexer() Fin");
		return $nbMAJ;
	}
		
	
	/**
	 * 
	 * Effectuer une recherche dans les categories seulement
	 * @param String chaine
	 * @param String idProjet
	 */
	public function rechercheCategories($chaine, $idProjet, $log, $dbh) {
	
		$log->debug("Categorie::rechercheCategories() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeCategories = array(); 

		// Préparer la chaîne de recherche
		$chaine = Web::nettoyerChaineRech($chaine);
		
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		try {
			$sql = "select 
					  	tcategorie_index.id_categorie 
					from 
						tcategorie_index,
						tcategorie 
					where 
						tcategorie_index.id_projet = ? and
						tcategorie_index.texte like ? and
						tcategorie.id_categorie = tcategorie_index.id_categorie and
						tcategorie.id_projet = tcategorie_index.id_projet
						 
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $chaine));
			
			// Vérifier qu'on a trouvé au moins une catégorie	
			if ($sth->rowCount() == 0) {
				$this->log->info("Categorie::getListeIdCategories() Aucune categorie trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des categories
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_categorie'];
	  				array_push($listeCategories, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::rechercheCategories() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_categories", $listeCategories);
				
		// Terminé
		$log->debug("Categorie::rechercheCategories() Fin");		
		return $listeCategories;
	}		
	
	/**
	 * 
	 * Dupliquer la categorie
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Categorie::dupliquer() Début");
	
		// Retirer l'id initial
		$this->set("id_categorie", "");
		
		// Ajouter un astérisque devant le titre
		$titre = "*" . $this->get("titre");
		$this->set("titre", $titre);
		
		// Ajouter la nouvelle categorie
		$this->ajouter();
		
		$this->log->debug("Categorie::dupliquer() Fin");
	}	
	
	/**
	 * 
	 * Imprimer une categorie
	 *
	 */
	public function imprimer() {
	
		$this->log->debug("Categorie::imprimer() Début");
	
		// Déterminer gabarit d'impression
		$gabaritImpression = REPERTOIRE_GABARITS_IMPRESSION . "categorie-details.php";
		
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritImpression)) {
			$this->log->erreur("Le gabarit d'impression '$gabaritImpression' ne peut être localisé.");
		}
		
		// Obtenir le contenu pour impression
		$contenu = Fichiers::getContenuElement($gabaritImpression , $this);

		// L'ajouter à la categorie
		$this->set("contenu", $contenu);
		
		// Déterminer le gabarit à utiliser pour l'impression
		$this->set("gabarit_impression", IMPRESSION_GABARIT_CATEGORIE);

		$this->log->debug("Categorie::imprimer() Fin");
		
		return $contenu;
	}		
	
	/**
	 * 
	 * Désactiver la categorie (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
		
		$this->log->debug("Categorie::desactiver() Début");
		
		$this->set("statut","0");
		$this->enregistrer();
		
		$this->log->debug("Categorie::desactiver() Fin");
	}		
	

	/**
	 * 
	 * Activer la categorie
	 *
	 */
	public function activer() {
		
		$this->log->debug("Categorie::activer() Début");
		
		// Activer le suivi
		$this->set("statut", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Categorie::activer() Fin");
	}	

	/**
	 * 
	 * Supprimer une categorie
	 */
	public function supprimer() {
		
		$this->log->debug("Categorie::supprimer() Début");
	
		try {

			// Supprimer l'index existant
			$sql = "delete from tcategorie_index where id_projet = ? and id_categorie = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_categorie")));
			$this->log->debug("Categorie: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_categorie = '" . $this->get("id_categorie") . "'");
			$this->log->debug("Categorie: updateIndex() Suppression complétée");
			
			// Supprimer la categorie de la table
			$this->log->debug("Categorie::supprimer() Supprimer la categorie '" . $this->get("id_categorie") . "' de l'usager '" . $this->get("id_projet") . "'");
			$sql = "delete from tcategorie where id_projet = ? and id_categorie= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_categorie")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Categorie::supprimer() Fin");
	}		
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Categorie::preparerAffichageListe() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("categorie_pref_tri_champ");
		$tri_ordre = $session->get("categorie_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Categorie::preparerAffichageListe() Fin");		
		
		return;
	}	

	/**
	 * 
	 * Obtenir l'id de la categorie à partir de la page demandée 
	 *
	 */
	public function getIdCategorieParPage($page) {

		$this->log->debug("Categorie::getIdCategorieParPage() Début");

		$idCategorie = "";
		$pageCour = $page - 1;
		
		// Obtenir la position de la catégorie dans les résultats
		$session = new Session();
		$listeCategories = $session->get("liste_categories");
	
		// Obtenir le nombre total de categories
		$pageTotal = count($listeCategories);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner la 1ere catégorie trouvée
			$idCategorie = $listeCategories[0];		
		} else {
			$idCategorie = $listeCategories[$pageCour];
		}
		
		$this->log->debug("Categorie::getIdCategorieParPage() Fin");
			
		return $idCategorie;
	}	
	

	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Categorie::preparerAffichage() Début");
		
		// Obtenir la position de la catégorie dans les résultats
		$session = new Session();
		$listeCategories = $session->get("liste_categories");
		if ( is_array($listeCategories) ) { 
			$pageCour = array_search($this->get("id_categorie"), $listeCategories);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de categorie
		$pageTotal = count($listeCategories);
		
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
			
		$this->log->debug("Categorie::preparerAffichage() Fin");
	}	
	
	/**
	 *
	 * Exporter une categorie en format XML
	 * 
	 * @param Projet projet
	 * @param Usager usager
	 * @param array Liste de categories
	 *
	 */
	public function exporterListeCategoriesXML($projet, $usager, $listeCategories) {
	
		$this->log->debug("Categorie::exporterXML() Début");
	
		$succes = 0;
		$urlFichierZip = "";
		$contenu = "";
		$listeMedias = array();
	
		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomBase = FICHIER_EXPORTATION_XML_CATEGORIES;
	
		// Vérifier le nombre de categories à exporter et ajouter un "s" au besoin
		if (count($listeCategories) > 1) {
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
	
			$this->log->debug("Categorie::exporterXML() Exportation d'une catégorie");
	
			// Exporter le catégorie
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
	
			// Entête XML
			$contenu .= XML_ENTETE . "\n";
			$contenu .= XML_NQW_DEBUT . "\n";
	
			// Obtenir la liste des categories en XML
			foreach ($listeCategories as $idCategorie) {
				$c = new Categorie($this->log, $this->dbh);
				$c->getCategorieParId($idCategorie, $projet->get("id_projet"));
	
				// Obtenir le contenu XML
				$contenu .= $c->exporterXML($repertoireDestination);
			}
	
			// Fin du fichier XML
			$contenu .= XML_NQW_FIN . "\n";
	
			// Écrire le contenu dans un fichier XML
			$publication->ecrireFichier($repertoireDestination . FICHIER_EXPORTATION_XML, $contenu);
	
		} else {
			$this->log->debug("Categorie::exporterXML() Impossible de publier le catégorie - le répertoire existe déjà");
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
			$this->log->debug("Categorie::exporterXML() Suppression du répertoire '$repertoireSourceZip'");
			Fichiers::rmdirr($repertoireSourceZip);
		} else {
			$urlFichierZip = "";
		}
	
		$this->log->debug("Categorie::exporterXML() Fin");
		return $urlFichierZip;
	}	
	
	/**
	 *
	 * Exporter une catégorie en format XML
	 * @param string répertoire destination
	 *
	 */
	public function exporterXML($repertoireDestination) {
	
		$this->log->debug("Categorie::exporterXML() Début");
	
		// Récupérer le gabarit pour publier un terme
		$contenu = Fichiers::getContenuCategorie(REPERTOIRE_GABARITS_EXPORTATION . "categorie.php", $this);
	
		$this->log->debug("Categorie::exporterXML() Fin");
	
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
