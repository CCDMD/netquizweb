<?php

require_once '../ressources/classes/outils/Session.php';

/** 
 * Classe Questionnaire
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 * 
 */

class Questionnaire {
	
	protected $dbh;
	protected $log;
	
	protected $listeChamps = "id_questionnaire, id_projet, titre, titre_long, suivi, generation_question_type, generation_question_nb, temps_reponse_calculer, temps_passation_type, temps_passation_heures, temps_passation_minutes,
							  essais_repondre_type, essais_repondre_nb, affichage_resultats_type, demarrage_media_type, id_langue_questionnaire, id_collection, theme, mot_bienvenue, note, generique, media_titre, media_texte, 
							  media_image, media_son, media_video, texte_fin, publication_repertoire, publication_date, nb_items, remarque, statut, date_creation, date_modification";
							  
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
	 * Obtenir la liste des clés
	 *
	 */
	
	public function getListeCles() {
	
		$this->log->debug("Item::getListeCles() Début");
	
		return array_keys($this->donnees);
	
		$this->log->debug("Item::getListeCles() Fin");
	}

	/**
	 * 
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {

		// Supprimer les termes existants au besoin
		if (Web::getParamNum("maj_lexique") == "1") {
			$this->deleteByPrefix("terme_selection_");
		}
		
		$params = Web::getListeParam("questionnaire_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
		}
		
		return;
	}

	/**
	 * 
	 * Charger le questionnaire à partir de la base de données
	 * 
	 * @param String idQuestionnaire
	 * @param String idProjet
	 */
	public function getQuestionnaireParId($idQuestionnaire, $idProjet) {

		$this->log->debug("Questionnaire::getQuestionnaireParId() Début idQuestionnaire : '$idQuestionnaire'  idProjet : '$idProjet'");
		$trouve = false;

		try {
			$sql = "SELECT " . $this->listeChamps . " from tquestionnaire where id_questionnaire = ? and id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idQuestionnaire, $idProjet));
			
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun questionnaire trouvé pour l'id '$idQuestionnaire'");
			}
			
			// Vérifier qu'un seul questionnaire est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour le questionnaire id '$idQuestionnaire' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour le questionnaire
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
		        
		        // Indiquer qu'un et un seul questionnaire a été trouvé
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::getQuestionnaireParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Valeur par défaut
		if ($this->donnees['statut'] == "") {
			$this->donnees['statut'] = "brouillon";
		}
		
		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), '45');
		$this->donnees['titre_menu']= $titreMenu;
		
		// Préparer la liste des médias
		$this->set("media_image_txt", Media::getMediaIdTitre($this->get("media_image"), $idProjet, $this->log, $this->dbh));
		$this->set("media_son_txt", Media::getMediaIdTitre($this->get("media_son"), $idProjet, $this->log, $this->dbh));
		$this->set("media_video_txt", Media::getMediaIdTitre($this->get("media_video"), $idProjet, $this->log, $this->dbh));

		// Récupérer la liste des termes pour le questionnaire
		$this->set("liste_termes", $this->getTermes());
		
		// Terminé
		$this->log->debug("Questionnaire::getQuestionnaireParId() Trouve = '$trouve'");
		$this->log->debug("Questionnaire::getQuestionnaireParId() Fin");
		return $trouve;		
	}


	/**
	 * 
	 * Obtenir le nombre d'items pour le questionnaire
	 * 
	 * @param String idQuestionnaire
	 * @param String idProjet
	 * 
	 */
	public function getNombreItems($idQuestionnaire, $idProjet) {

		$this->log->debug("Questionnaire::getNombreItems() Début");
		$this->log->debug("Questionnaire::getNombreItems() idQuestionnaire = '$idQuestionnaire'  idProjet = '$idProjet'");

		try {
			$sql = "select count(*) 
					from tquestionnaire_item, titem 
					where tquestionnaire_item.id_questionnaire = ? 
					and tquestionnaire_item.id_projet = ? 
					and titem.id_item = tquestionnaire_item.id_item
					and titem.id_projet = tquestionnaire_item.id_projet 
					and tquestionnaire_item.statut != 0 
					and titem.type_item != 15
					and titem.statut != 0";
			//$sql = "select count(*) from tquestionnaire_item where tquestionnaire_item.id_questionnaire = ? and tquestionnaire_item.id_projet = ? and tquestionnaire_item.statut != 0";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($idQuestionnaire, $idProjet));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::getNombreItems() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Vérifier qu'on a trouvé au moins un questionnaire
		$total = $sth->fetchColumn();
		$this->log->debug("Questionnaire::getNombreItems() Total : '$total'");
		$this->log->debug("Questionnaire::getNombreItems() Fin");
		return $total;
	}
	
	
	/**
	 * 
	 * Obtenir le nombre de section pour le questionnaire
	 * 
	 * @param String idQuestionnaire
	 * @param String idProjet
	 * 
	 */
	public function getNombreSections($idQuestionnaire, $idProjet) {

		$this->log->debug("Questionnaire::getNombreSections() Début");
		$this->log->debug("Questionnaire::getNombreSections() idQuestionnaire = '$idQuestionnaire'  idProjet = '$idProjet'");

		try {
			$sql = "select count(*) 
					from tquestionnaire_item, titem 
					where tquestionnaire_item.id_questionnaire = ? 
					and tquestionnaire_item.id_projet = ? 
					and titem.id_item = tquestionnaire_item.id_item
					and titem.id_projet = tquestionnaire_item.id_projet 
					and tquestionnaire_item.statut != 0 
					and titem.type_item = 15";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($idQuestionnaire, $idProjet));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::getNombreSections() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Obtenir le nombre de sections
		$total = $sth->fetchColumn();
		$this->log->debug("Questionnaire::getNombreSections() Total : '$total'");
		$this->log->debug("Questionnaire::getNombreSections() Fin");
		return $total;
	}	

	
	/**
	 * 
	 * Mettre à jour le nombre d'item au niveau du questionnaire
	 * 
	 * @param String idQuestionnaire
	 * @param String idProjet
	 * 
	 */
	public function updateNombreItems($idQuestionnaire, $idProjet) {

		$this->log->debug("Questionnaire::updateNombreItems() Début");
		$this->log->debug("Questionnaire::updateNombreItems() idQuestionnaire = '$idQuestionnaire'  idProjet = '$idProjet'");

		// Obtenir le nombre d'items
		$nbItems = $this->getNombreItems($idQuestionnaire, $idProjet);
		$this->set("nb_items", $nbItems);
		
		// Sauvegarder l'information
		$this->enregistrer();
		
		// Vérifier qu'on a trouvé au moins un questionnaire
		$this->log->debug("Questionnaire::updateNombreItems() Fin");
		return;
	}	
		
	
	/**
	 * 
	 * Obtenir la liste des questionnaires
	 * 
	 * @param String idProjet
	 * 
	 */
	public function getListeQuestionnaire($idProjet) {

		$this->log->debug("Questionnaire::getListeQuestionnaire() Début");
		$listeQuestionnaires = array(); 
				
		// Obtenir le tri à utiliser
		$tri = $this->getTriQuestionnaire();
		
		// Obtenir la collection (filtre)
		$filtreCollection = $this->getFiltreCollection($idProjet);
		
		try {
			// SQL de base
			$sql = "select tquestionnaire.id_questionnaire, tquestionnaire.titre, tquestionnaire.id_collection, tcollection.titre as collection, tquestionnaire.date_modification, tquestionnaire.statut, tquestionnaire.remarque
					from tquestionnaire 
					left join tcollection on tquestionnaire.id_collection = tcollection.id_collection and tquestionnaire.id_projet = tcollection.id_projet 
					where tquestionnaire.id_projet = ? and tquestionnaire.statut != 0 order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun questionnaire trouvé pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids de questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour la collection
					//echo "Vérifier filtre collection ['$filtreCollection'] et la valeur : '" . $row['id_collection'] . "'\n";
					if ($filtreCollection != "" && $row['id_collection'] != $filtreCollection) {
						continue;
					}
					$id = $row['id_questionnaire'];
					array_push($listeQuestionnaires, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::getListeQuestionnaire() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_questionnaires", $listeQuestionnaires);
		
		// Terminé
		$this->log->debug("Questionnaire::getListeQuestionnaire() Fin");
		return $listeQuestionnaires;		
	}
	
	
	/**
	 *
	 * Obtenir la liste des questionnaires du projet
	 * 
	 * @param String idProjet
	 *
	 */
	public function getListeIdQuestionnairesDuProjet($idProjet) {
	
		$this->log->debug("Categorie::getListeIdQuestionnairesDuProjet() Début");
		$listeQuestionnaires = array();
	
		try {
	
			$sql = "select id_questionnaire from tquestionnaire where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
	
			// Vérifier qu'on a trouvé au moins un questionnaire
			if ($sth->rowCount() == 0) {
				$this->log->info("Categorie::getListgetListeIdQuestionnairesDuProjeteIdCategoriesDuProjet() Aucun questionnaire trouvé pour le projet '$idProjet'");
			}
			else {
				// Récupérer les ids des questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_questionnaire'];
					array_push($listeQuestionnaires, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Categorie::getListeIdQuestionnairesDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Categorie::getListeIdQuestionnairesDuProjet() Fin");
		return $listeQuestionnaires;
	}	
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("Questionnaire::indexer() Début");
		
		$index = "";
		$index .= TXT_PREFIX_QUESTIONNAIRE . $this->get("id_questionnaire") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("titre_long") . " ";
		$index .= $this->get("mot_bienvenue") . " ";
		$index .= $this->get("note") . " ";
		$index .= $this->get("generique") . " ";
		$index .= $this->get("media_titre") . " ";
		$index .= $this->get("media_texte") . " ";
		$index .= $this->get("media_image") . " ";
		$index .= $this->get("media_son") . " ";
		$index .= $this->get("media_video") . " ";
		$index .= $this->get("texte_fin") . " ";
		$index .= $this->get("date_creation") . " ";
		$index .= $this->get("date_modification") . " ";
		
		// Enlever le html
		$index = Web::nettoyerChaineRech($index);
		
		try {
			// Supprimer l'index existant au besoin
			$sql = "delete from tquestionnaire_index where id_projet = ? and id_questionnaire = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_questionnaire")));
			
			// Insérer l'index
			$sql = "insert into tquestionnaire_index (id_questionnaire, id_projet, texte, date_creation, date_modification)
					values (?,?,?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_questionnaire"), $this->get("id_projet"), $index));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::indexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Questionnaire::indexer() Fin");
	}

	
	
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Questionnaire::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_questionnaire, id_projet
					FROM 	tquestionnaire";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un questionnaire
			if ($sth->rowCount() == 0) {
				$this->log->info("Questionnaire::reindexer()  Aucun questionnaire localisé");
			} else {
	
				// Récupérer les ids des questionnaires et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
						
					// Récupérer l'id du questionnaire et du projet
					$idQuest = $row['id_questionnaire'];
					$idProjet = $row['id_projet'];
					
					// Vérifier si le projet existe
					$p = new Projet($this->log, $this->dbh);
					
					// Obtenir le questionnaire
					$q = new Questionnaire($this->log, $this->dbh);
					$q->getQuestionnaireParId($idQuest, $idProjet);
						
					// Réindexer
					$this->log->info("Questionnaire::reindexer()  Indexation pour le questionnaire '$idQuest' et projet '$idProjet'");
					$q->indexer();
					$this->log->info("Questionnaire::reindexer()  Indexation complétée pour le questionnaire '$idQuest'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Questionnaire::reindexer() Fin");
		return $nbMAJ;
	}	
	
	/**
	 * 
	 * Supprimer un questionnaire
	 * 
	 */
	public function supprimer() {
		
		$this->log->debug("Questionnaire::supprimer() Début");
	
		try {
			// Supprimer l'index existant au besoin
			$this->log->debug("Questionnaire::supprimer() Supprimer le questionnaire '" . $this->get("id_questionnaire") . "' de le projet '" . $this->get("id_projet") . "'");
			$sql = "delete from tquestionnaire where id_projet = ? and id_questionnaire= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_questionnaire")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Questionnaire::supprimer() Fin");
	}	
	
	
	/**
	 * 
	 * Effectuer une recherche dans les questionnaires seulement
	 * 
	 * @param String chaine
	 * @param String idProjet
	 */
	public function rechercheQuestionnnaire($chaine, $idProjet) {
	
		$this->log->debug("Questionnaire::recherche() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeQuestionnaires = array(); 
				
		try {
			// SQL de recherche
			$sql = "select tquestionnaire.id_questionnaire 
					from tquestionnaire_index, tquestionnaire
					left join tcollection_index on tcollection_index.id_collection = tquestionnaire.id_collection and tcollection_index.id_projet = tquestionnaire.id_projet
					where tquestionnaire.id_projet = ?
					and tquestionnaire.id_questionnaire = tquestionnaire_index.id_questionnaire
					and tquestionnaire.id_projet = tquestionnaire_index.id_projet
					and (tquestionnaire_index.texte like ? or tcollection_index.texte like ?)";
			
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $chaine, $chaine));
			
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun questionnaire trouvé pour l'usager '$idProjet' et la recherche '$chaine'");
			}
			else {
				// Récupérer les ids de questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_questionnaire'];
					array_push($listeQuestionnaires, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::recherche() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Questionnaire::recherche() Fin");		
		return $listeQuestionnaires;
	}
	
	
	/**
	 * 
	 * Effectuer une recherche dans les questionnaires et les items
	 * 
	 * @param String chaine
	 * @param String idProjet
	 * 
	 */
	public function recherche($chaine, $idProjet) {

		$this->log->debug("Questionnaire::recherche() Début chaine = '$chaine'  idProjet = '$idProjet'");
		$listeQuestionnaires = array(); 
				
		// Préparer la chaîne de recherche
		$rech = '%' . Web::nettoyerChaineRech($chaine) . '%';
		
		// Obtenir le tri à utiliser
		$tri = $this->getTriQuestionnaire();
		
		// Obtenir la collection (filtre)
		$filtreCollection = $this->getFiltreCollection($idProjet);
		
		// Obtenir les ids des questionnaires qui contiennent les termes de recherche
		$listeIdQuest = $this->rechercheQuestionnnaire($rech, $idProjet);

		// Obtenir les ids des items qui contiennent les termes de recherche
		$listeIdItems = Item::rechercheQuestionnairesContenantItems($rech, $idProjet, $this->log, $this->dbh);
		
		// Fusionner les ids des questionnaires trouvés
		$listeIdTous = array_merge($listeIdQuest, $listeIdItems);
		$listeIdTousTrie = array_unique($listeIdTous);
		$listeId = implode(",", $listeIdTousTrie);
		
		if ($listeId != "") {
			try {
				// SQL de recherche
				$sql = "select tquestionnaire.id_questionnaire, tquestionnaire.titre, tquestionnaire.id_collection, tcollection.titre as collection, tquestionnaire.date_modification, tquestionnaire.statut, tquestionnaire.remarque 
						from tquestionnaire 
						left join tcollection on tquestionnaire.id_collection = tcollection.id_collection and tquestionnaire.id_projet = tcollection.id_projet 
						where tquestionnaire.id_projet = ? and tquestionnaire.statut != 0 
						and tquestionnaire.id_questionnaire in ( " . $listeId . ")				
						order by $tri";
				$sth = $this->dbh->prepare($sql);
				$rows = $sth->execute(array($idProjet));
				
				// Vérifier qu'on a trouvé au moins un questionnaire	
				if ($sth->rowCount() == 0) {
					$this->log->info("Aucun questionnaire trouvé pour l'usager '$idProjet'");
				}
				else {
					// Récupérer les ids de questionnaires
					while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		
						// Appliquer le filter pour la collection
						if ($filtreCollection != "" && $row['id_collection'] != $filtreCollection) {
							continue;
						}
						$id = $row['id_questionnaire'];
						array_push($listeQuestionnaires, $id);
					}
				}
			} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::recherche() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}				
		}
				
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_questionnaires", $listeQuestionnaires);
		
		// Terminé
		$this->log->debug("Questionnaire::getListeQuestionnaire() Fin");
		return $listeQuestionnaires;		
	}

	
	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des questionnaires
	 * 
	 */
	public function getTriQuestionnaire() {
		
		$this->log->debug("Questionnaire::getTriQuestionnaire() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("pref_tri_champ");
		$triSessionOrdre = $session->get("pref_tri_ordre");
		$this->log->debug("Questionnaire::getTriQuestionnaire() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Questionnaire::getTriQuestionnaire() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
		
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_questionnaire", "titre", "nb_items", "remarque", "collection", "statut", "date_modification", "suivi");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_questionnaire";
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
			$triParamChamp = "id_questionnaire";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		$session->set("pref_tri_champ", $triParamChamp);
		$session->set("pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Questionnaire::getTriQuestionnaire() Fin");
		
		return $triParamChamp . " " . $triParamOrdre;
	}
	

	/**
	 * 
	 * Obtenir le filtre pour la collection
	 * 
	 * @param String idProjet
	 */
	public function getFiltreCollection($idProjet) {
		
		$this->log->debug("Questionnaire::getFiltreCollection() Début");
		
		$session = new Session();
		
		// Vérifier si un filtre est spécifié dans la session
		$filtreCollection = $session->get("pref_filtre_collection");
		
		// Vérifier si un filtre est passé en paramètre
		$filtreCollectionParam = Web::getParam("collection");
		
		// Déterminer si on utilise la valeur passé en paramètre
		if ($filtreCollectionParam != "") {
		
			// Si l'utilisateur veut voir toutes les collections enlever le filtre
			if ($filtreCollectionParam == "tous") {
				$session->delete("pref_filtre_collection");
				$filtreCollection = "";
			} else {
			
				// Obtenir la liste de collection
				$collection = new Collection($this->log, $this->dbh);
				$listeCollections = $collection->getListeCollections($idProjet);
	
				// Vérifier si la collection demandée est disponible pour l'utilisateur
				if ($listeCollections[$filtreCollectionParam] != "") {			
	
					// Stocker le tri dans la session
					$session->set("pref_filtre_collection", $filtreCollectionParam);
					$filtreCollection = $filtreCollectionParam;
				}
			}
		}
		
		$this->log->debug("Questionnaire::getFiltreCollection() Fin");
		
		return $filtreCollection;
	}	
	

	/**
	 * 
	 * Obtenir la liste des items pour ce questionnaire

	 */
	public function getListeItems() {

		$this->log->debug("Questionnaire::getListeItems() Début");
		$listeItems = array(); 
		
		$idProjet = $this->get("id_projet");
		$idQuest = $this->get("id_questionnaire");
		$this->log->debug("Questionnaire::getListeItems() Recherche des items pour le projet '$idProjet' et le questionnaire '$idQuest'");
		
		try {
			// SQL de base
			$sql = "select id_item 
					from tquestionnaire_item 
					where id_projet = ? and id_questionnaire= ? and statut != 0 order by ordre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $idQuest));
			
			// Vérifier qu'on a trouvé au moins un item	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun item trouvé pour l'usager '$idProjet' et le questionnaire '$idQuest'");
			}
			else {
				// Récupérer les ids
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$idItem = $row['id_item'];
					array_push($listeItems, $idItem);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::getListeItem() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Terminé
		$this->log->debug("Questionnaire::getListeItems() Fin");
		return $listeItems;
	}
	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - ajout d'un questionnaire
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Questionnaire::ajouter() Début");
				
		try {
			
			// Obtenir le prochain id questionnaire
			$projet = new Projet($this->log, $this->dbh);
			$projet->getProjetParId($this->get("id_projet"));
			$idQuest = $projet->genererIdQuestionnaire();
			$this->set("id_questionnaire", $idQuest);
			
			// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
			if ( trim($this->get("titre")) == "") {
				$this->set("titre", TXT_NOUVEAU_QUESTIONNAIRE);
			}
			
			$stmt = $this->dbh->prepare("insert into tquestionnaire (id_questionnaire, id_projet, titre, titre_long, suivi, generation_question_type, generation_question_nb, temps_reponse_calculer, 
										temps_passation_type, temps_passation_heures, temps_passation_minutes, essais_repondre_type, essais_repondre_nb, affichage_resultats_type, demarrage_media_type, 
										 id_langue_questionnaire, id_collection, theme, mot_bienvenue, note, generique, media_titre, media_texte, media_image, media_son, media_video, 
										 texte_fin, publication_repertoire, remarque, statut, date_creation, date_modification) 
										 values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,now(),now() )");
	
			// Valeurs par défaut pour un nouveau questionnaire
			$this->set('statut', '1');

			$this->log->debug("Questionnaire::ajouter() Ajouter un nouveau questionnaire (idQuest : '$idQuest', idProjet : '" . $this->get("id_projet") . "', idProjet : '" . $this->get("id_projet"));
			
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get("id_questionnaire"),
								 $this->get("id_projet"),
								 $this->get('titre'), 
								 $this->get('titre_long'),
								 $this->get('suivi'),
								 $this->get('temps_reponse_calculer'),  
								 $this->get('generation_question_type'), 
								 (int)$this->get('generation_question_nb'),
								 $this->get('temps_passation_type'),
								 $this->get('temps_passation_heures'),
								 $this->get('temps_passation_minutes'),
								 $this->get('essais_repondre_type'),
								 (int)$this->get('essais_repondre_nb'),
								 $this->get('affichage_resultats_type'),
								 $this->get('demarrage_media_type'),
								 (int)$this->get('id_langue_questionnaire'),
								 (int)$this->get('id_collection'),
								 $this->get('theme'),
								 $this->get('mot_bienvenue'),
								 $this->get('note'),
								 $this->get('generique'),
								 $this->get('media_titre'),
								 $this->get('media_texte'),
								 $this->get('media_image'),
								 $this->get('media_son'),
								 $this->get('media_video'),
								 $this->get('texte_fin'),
								 $this->get('publication_repertoire'),
								 $this->get('remarque'),
								 (int)$this->get('statut'),
								 ));
			
			$this->log->debug("Questionnaire::ajouter() Nouveau questionnaire créé (id = '" . $this->get('id_questionnaire') . "')");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		
		// Mettre à jour l'index
		$this->indexer();

		return;
	}

	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un questionnaire
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Questionnaire::enregistrer() Début");
				
		// Mettre à jour le nombre d'items
		$nbItems = $this->getNombreItems($this->get("id_questionnaire"), $this->get("id_projet"));
		$this->set("nb_items", $nbItems);
				
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVEAU_QUESTIONNAIRE);
		}		
		
		try {
			// Enregistrer les informations
			$stmt = $this->dbh->prepare("update tquestionnaire 
										set id_projet=?, 
											titre=?,
											titre_long=?,
											suivi=?,
											generation_question_type=?, 
											generation_question_nb=?,
											temps_reponse_calculer=?, 
											temps_passation_type=?, 
											temps_passation_heures=?,
											temps_passation_minutes=?,
											essais_repondre_type=?,
											essais_repondre_nb=?,
											affichage_resultats_type=?,
											demarrage_media_type=?, 
											id_langue_questionnaire=?, 
											id_collection=?, 
											theme=?, 
											mot_bienvenue=?, 
											note=?, 
											generique=?, 
											media_titre=?, 
											media_texte=?,
								  		 	media_image=?, 
								  		 	media_son=?, 
								  		 	media_video=?, 
								  		 	texte_fin=?,
								  		 	publication_repertoire=?,
								  		 	publication_date=?,
								  		 	nb_items=?,
											remarque=?,
								  		 	statut=?, 
								  		 	date_modification=now()										
											
											where id_questionnaire = ?
											and id_projet = ?");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('id_projet'),
									$this->get('titre'), 
									$this->get('titre_long'),
								 	$this->get('suivi'),
									$this->get('generation_question_type'), 
									(int)$this->get('generation_question_nb'),
									$this->get('temps_reponse_calculer'),
									$this->get('temps_passation_type'),
									$this->get('temps_passation_heures'),
									$this->get('temps_passation_minutes'),
								    $this->get('essais_repondre_type'),
								    (int)$this->get('essais_repondre_nb'),
								    $this->get('affichage_resultats_type'),
									$this->get('demarrage_media_type'),
									(int)$this->get('id_langue_questionnaire'),
									(int)$this->get('id_collection'),
									$this->get('theme'),
									$this->get('mot_bienvenue'),
									$this->get('note'),
									$this->get('generique'),
									$this->get('media_titre'),
									$this->get('media_texte'),
									$this->get('media_image'),
									$this->get('media_son'),
									$this->get('media_video'),
									$this->get('texte_fin'),
									$this->get('publication_repertoire'),
									$this->get('publication_date'),
									(int)$this->get('nb_items'),
									$this->get('remarque'),
									(int)$this->get('statut'),
									$this->get('id_questionnaire'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}									

		// Supprimer les termes précédents et enregistrer les termes courants
		$this->supprimerTermes();
		$this->enregistrerTermes();
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("Questionnaire::enregistrer() Fin");
							
		return;
	}
	
	
	/**
	 *
	 * Sauvegarder les informations sur les termes
	 *
	 */
	public function enregistrerTermes() {
	
		$this->log->debug("Questionnaire::enregistrerTermes() Début");
		
		try {
			// Préparer SQL insertion
			$sth = $this->dbh->prepare("insert into rprojet_questionnaire_terme (id_projet, id_questionnaire, id_terme) values (?,?,?)");
		
			// Obtenir les variables du questionnaire courant
			$listeCles = array_keys($this->donnees);
			$prefix = "terme_selection_";
				
			// Obtenir la liste des termes
			foreach ($listeCles as $cle){
			
				$str = strstr($cle, $prefix);
				if ($str != "") {
					$idTerme = substr($str, strlen($prefix));
					
					// Insertion dans la BD
					$this->log->debug("Questionnaire::enregistrerTermes() Enregistrer la relation pour id_projet = '" . $this->get("id_projet") . "' id_questionnaire = '" . $this->get("id_questionnaire") . "' idTerme = '" . $idTerme . "'\n");
					$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_questionnaire"), $idTerme));
				}
			}	

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::enregistrerTermes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("Questionnaire::enregistrerTermes() Fin");
	}
	
	
	/**
	 *
	 * Ajouter un terme au questionnaire
	 * String $idTerme
	 *
	 */
	public function ajouterTerme($idTerme) {
	
		$this->log->debug("Questionnaire::ajouterTerme() Début");
	
		try {
			// Préparer SQL insertion
			$sth = $this->dbh->prepare("insert into rprojet_questionnaire_terme (id_projet, id_questionnaire, id_terme) values (?,?,?)");
	
			// Insertion dans la BD
			$this->log->debug("Questionnaire::ajouterTerme() Enregistrer la relation pour id_projet = '" . $this->get("id_projet") . "' id_questionnaire = '" . $this->get("id_questionnaire") . "' idTerme = '" . $idTerme . "'\n");
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_questionnaire"), $idTerme));
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::enregistrerTermes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Questionnaire::ajouterTerme() Fin");
	}	
		
	
	/**
	 *
	 * Supprimer les termes pour un questionnaire
	 *
	 */
	public function supprimerTermes() {
	
		$this->log->debug("Questionnaire::supprimerTermes() Début");
	
		try {
			// Supprimer les termes pour le questionnaire courant
			$this->log->debug("Questionnaire::supprimerTermes() Supprimer les termes pour le projet '" . $this->get("id_projet") . "' et le questionnaire '" . $this->get("id_questionnaire") . "'");
			$sql = "delete from rprojet_questionnaire_terme where id_projet = ? and id_questionnaire = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($this->get("id_projet"), $this->get("id_questionnaire")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::supprimerTermes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Questionnaire::supprimerTermes() Fin");
	}	
	
	
	/**
	 *
	 * Obtenir la liste des termes pour ce questionnaire
	 * 
	 */
	public function getTermes() {
	
		$this->log->debug("Questionnaire::getTermes() Début");
		
		// Obtenir la liste de termes active au projet
		$terme = new Terme($this->log, $this->dbh);
		$listeTermesActifs = $terme->getListeIdTermesDuProjet($this->get("id_projet"), "id_terme");
		
		$idProjet = $this->get("id_projet");
		$idQuest = $this->get("id_questionnaire");
		$this->log->debug("Questionnaire::getTermes() Recherche de termes pour le projet '$idProjet' et le questionnaire '$idQuest'");
	
		try {
			// SQL de base
			$sql = "select id_terme
					from rprojet_questionnaire_terme
					where id_projet = ? and id_questionnaire= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $idQuest));
				
			// Vérifier qu'on a trouvé au moins un terme
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun item trouvé pour le projet '$idProjet' et le questionnaire '$idQuest'");
			}
			else {
				// Récupérer les ids
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$idTerme = $row['id_terme'];
					
					// Ajouter le terme s'il est actif au niveau du projet
					if (in_array($idTerme, $listeTermesActifs)) {
						$this->set("terme_selection_" . $idTerme, $idTerme);
					}
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::getTermes() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Questionnaire::getTermes() Fin");
		return;
	}
		
		
	/**
	 * 
	 * Enregistrer une nouvelle collection au besoin
	 */
	public function enregistrerNouvelleCollection() {
		
		$this->log->debug("Questionnaire::enregistrerNouvelleCollection() Début");
		
		$titre = $this->get("collection_ajouter");
		
		if ($titre != '') {

			// Préparer la nouvelle collection
			$collection = new Collection($this->log, $this->dbh);
			$collection->set("titre", $titre);
			$collection->set("id_projet", $this->get("id_projet"));
			$collection->set("remarque", "");
			
			// Ajouter la collection
			$collection->ajouter();
			
			// Régler l'id de collection du questionnaire sur l'id de la nouvelle collection
			$this->set("id_collection", $collection->get("id_collection"));
		}
		
		$this->log->debug("Questionnaire::enregistrerNouvelleCollection() Fin");
		
	}	


	/**
	 * 
	 * Copier les items d'un questionnaire
	 *
	 */
	public function dupliquerItems($idQuestSource, $idQuestDest, $idProjet) {

		$this->log->debug("Questionnaire::dupliquerItems() Début  idQuestSource = '$idQuestSource'  idQuestDest = '$idQuestDest'");

		try {
			// Préparer SQL insertion
			$stmtInsert = $this->dbh->prepare("insert into tquestionnaire_item(id_questionnaire, id_item, id_projet, ordre, section, ponderation_quest, afficher_solution_quest, ordre_presentation_quest, type_etiquettes_quest,
											   type_bonnesreponses_quest, demarrer_media_quest, points_retranches_quest, majmin_quest, ponctuation_quest, statut, 
											   date_creation, date_modification) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now() )");
			
			
			// Obtenir la liste des items en ordre
			$sql = "select id_item, id_projet, section, ponderation_quest, afficher_solution_quest, ordre_presentation_quest, type_etiquettes_quest, type_bonnesreponses_quest, demarrer_media_quest, points_retranches_quest,
						   majmin_quest, ponctuation_quest, statut 
					from tquestionnaire_item
					where id_questionnaire = ?  
					and id_projet = ?
					and statut = 1
					order by ordre";
			
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idQuestSource, $idProjet));
		
			// Vérifier qu'on a trouvé au moins un item	
			if ($sth->rowCount() > 0) {
	
				$ordre = 0;
				
				// Récupérer les informations des items existants
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	
					// Insertion d'un enregistrement
					$stmtInsert->execute(array(	$idQuestDest,
											$row['id_item'],
											$idProjet,
											$ordre,
											$row['section'],
											$row['ponderation_quest'],
											$row['afficher_solution_quest'],
											$row['ordre_presentation_quest'],
											$row['type_etiquettes_quest'],
											$row['type_bonnesreponses_quest'],
											$row['demarrer_media_quest'],
											$row['points_retranches_quest'],
											$row['majmin_quest'],
											$row['ponctuation_quest'],
											$row['statut'],
										 ));
	
					// Garder l'ordre en place
					$ordre++;				 
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Questionnaire::dupliquerItems() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		$this->log->debug("Questionnaire::dupliquerItems() Fin");
	}
	  				
	
	/**
	 * 
	 * Dupliquer le questionnaire
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Questionnaire::dupliquer() Début");
	
		// Retirer l'id initial
		$idQuestSource = $this->get("id_questionnaire");
		$this->set("id_questionnaire", "");
		
		// Ajouter un astérisque devant le titre
		$titre = "*" . $this->get("titre");
		$this->set("titre", $titre);
		
		// Enlever les informations de publication 
		$this->set("publication_repertoire", "");
		$this->set("publication_date", "");
		
		// Modifier le statut
		$this->set("statut", "1");
		
		// Ajouter le nouveau questionnaire
		$this->ajouter();
		
		// Dupliquer les items
		$this->dupliquerItems($idQuestSource, $this->get("id_questionnaire"), $this->get("id_projet") );
		
		// Mettre à jour le nombre d'item
		$this->updateNombreItems($this->get("id_questionnaire"), $this->get("id_projet"));
		
		$this->log->debug("Questionnaire::dupliquer() Fin");
	}


	/**
	 * 
	 * Activer le questionnaire
	 *
	 */
	public function activer() {
		
		$this->log->debug("Questionnaire::activer() Début");

		$this->set("statut","1");
		$this->enregistrer();
		
		$this->log->debug("Questionnaire::activer() Fin");
	}	
	
	/**
	 * 
	 * Désactiver le questionnaire (mettre à la corbeille)
	 * @param String idQuest
	 * @param Objet Projet
	 *
	 */
	public function desactiver($idQuest, $projet) {
		
		$this->log->debug("Questionnaire::desactiver() Début idQuest = '$idQuest'  idProjet = '" . $projet->get("id_projet") . "'");

		// Régler le projet
		$this->set("id_projet", $projet->get("id_projet"));
		
		// Désactiver la publication
		$this->desactiverPublication($projet);
		
		// Modifier le statut
		$this->set("statut","0");
		$this->enregistrer();
		
		$this->log->debug("Questionnaire::desactiver() Fin");
	}
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Questionnaire::preparerAffichage() Début");

		// Select
		$this->set('generation_question_type_' . $this->get('generation_question_type'), HTML_SELECTED);
		$this->set('temps_reponse_calculer_' . $this->get('temps_reponse_calculer'), HTML_SELECTED); 
		$this->set('temps_passation_type_' . $this->get('temps_passation_type'), HTML_SELECTED);
		$this->set('essais_repondre_type_' . $this->get('essais_repondre_type'), HTML_SELECTED);
		$this->set('affichage_resultats_type_' . $this->get('affichage_resultats_type'), HTML_SELECTED);
		$this->set('demarrage_media_type_' . $this->get('demarrage_media_type'), HTML_SELECTED);
		$this->set('id_langue_questionnaire_' . $this->get('id_langue_questionnaire'), HTML_SELECTED);
		
		// Radio
		$this->set('theme_' . $this->get('theme'), HTML_CHECKED);
		
		// Obtenir la position du questionnaire dans les résultats
		$session = new Session();
		$listeQuestionnaires = $session->get("liste_questionnaires");
		$pageCour = 1;
		if (isset($listeQuestionnaires) && ! empty($listeQuestionnaires)) {
			$pageCour = array_search($this->get("id_questionnaire"), $listeQuestionnaires);
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de questionnaires
		$pageTotal = count($listeQuestionnaires);
		
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
		
		$this->log->debug("Questionnaire::preparerAffichage() Fin");
		
		return;
	}
	

	/**
	 * 
	 * Obtenir l'id du questionnaire à partir de la page demandée 
	 *
	 */
	public function getIdQuestionnaireParPage($page) {

		$this->log->debug("Questionnaire::getIdQuestionnaireParPage() Début");

		$idQuest = "";
		$pageCour = $page - 1;
		
		// Obtenir la position du questionnaire dans les résultats
		$session = new Session();
		$listeQuestionnaires = $session->get("liste_questionnaires");
	
		// Obtenir le nombre total de questionnaires
		$pageTotal = count($listeQuestionnaires);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er formulaire trouvé
			$idQuest = $listeQuestionnaires[0];		
		} else {
			$idQuest = $listeQuestionnaires[$pageCour];
		}
		
		return $idQuest;
	}
	
	

	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerListeQuestionnaire() {

		$this->log->debug("Questionnaire::preparerListeQuestionnaire() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("pref_tri_champ");
		$tri_ordre = $session->get("pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}
		
		$this->log->debug("Questionnaire::preparerListeQuestionnaire() Fin");		
		
		return;
	}
	
	
	
	/**
	 * 
	 * Valider le questionnaire 
	 * 
	 * String idProjet
	 *
	 */
	public function valider($idProjet) {

		$this->log->debug("Questionnaire::valider() Début");

		$session = new Session();
		$succes = 0;
		$erreursTot = 0;
		$contenu = "";
		$messages = "";
		$sectionCourante = 0;
		$sectionNbItems = 0;
		$sectionNbItemsValides = 0;
		
		// Déterminer la langue pour l'aperçu
		$idLangue = $this->get("id_langue_questionnaire");
		$this->log->debug("Questionnaire::publierRepertoire() Langue du questionnaire : '$idLangue'");
		
		// Charger la langue
		$langue = new Langue($this->log, $this->dbh);
		$langue->getLangueParId($idLangue, $this->get("id_projet"));
		
		// Vérifier la langue
		if ($langue->validerAvantPublication()) {
			$this->log->debug("Questionnaire::publierRepertoire() Tous les champs de la langue '$idLangue' sont présents. Validation OK!");
		} else {
			$erreursTot++;
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_211 . HTML_LISTE_ERREUR_FIN;
				
			$this->log->debug("Questionnaire::publierRepertoire() Un ou plusieurs champs de la langue '$idLangue' sont absents. Validation incorrecte!");
		}
				
		
		// Valider la page d'accueil
		$this->log->debug("Questionnaire::valider() Vérifier le titre long : '" . $this->get("titre_long") . "'");
		if (trim($this->get("titre_long")) == "") {
			$erreursTot++;
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_035 . HTML_LISTE_ERREUR_FIN;
			$this->log->debug("Questionnaire::valider() Problème détecté : Titre long absent");
		}
		
		$this->log->debug("Questionnaire::valider() Vérifier qu'un thème est sélectionné");
		if ($this->get("theme") == "") {
			$erreursTot++;
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_041 . HTML_LISTE_ERREUR_FIN;
			$this->log->debug("Questionnaire::valider() Problème détecté : Aucun thème sélectionné");
		}
		
		// Vérifier que le thème existe
		$repertoireSourceTheme = REPERTOIRE_THEMES . $this->get("theme") . "/";
		$this->log->debug("Questionnaire::valider() Vérifier que le thème sélectionné existe ('$repertoireSourceTheme')");
		if (!is_dir($repertoireSourceTheme)) {
			$erreursTot++;
			$messages .= HTML_LISTE_ERREUR_DEBUT . ERR_042 . HTML_LISTE_ERREUR_FIN;
			$this->log->debug("Questionnaire::valider() Problème détecté : Le répertoire du thème sélectionné n'existe pas");
		}
		
		$session->set("questionnaire_apercu_messages_entete", $messages);
				
		// Obtenir les items dans l'ordre du menu
		$menu = new Menu($this->log, $this->dbh);
		$listeItems = $menu->getMenu($this->get("id_questionnaire"), $idProjet);
		
		// Traiter la liste des items
		$nbItems = 0;
		foreach ($listeItems as $element) {
		
			$idItem = $element->getId();
			$typeItem = $element->getType();
			$idSection = $element->getIdSection();

			// Instancier l'item
			$itemFactory = new Item($this->log, $this->dbh);
			$item = $itemFactory->instancierItemParType('', $idProjet, $idItem);
			
			// Charger les valeurs "Pour ce questionnaire seulement"
			$item->getValeursPourQuestionnaire($this->get("id_questionnaire"), $idProjet);
			
			$this->log->debug("Questionnaire::valider() Traiter l'item : '$idItem'  typeItem : '$typeItem'  idSection = '$idSection'");
			
			// Traitement d'une fin de section
			if ($sectionCourante != $idSection && $sectionCourante > 0) {
				
				$this->log->debug("Questionnaire::valider() Fin de section détectée. Nombre d'item traité dans la section : '$sectionNbItems'  Nombre d'items valides : '$sectionNbItemsValides'");
				
				// Message si aucun item dans la section
				if ($sectionCourante >= 0 && $sectionNbItems == 0) {
					$this->log->debug("Questionnaire::valider() Message : Aucun item dans la section");
					$contenu .= HTML_LISTE_ERREUR_DEBUT . ERR_036 . HTML_LISTE_ERREUR_FIN;
				}
				
				// Message si tous les items sont valides
				if ($sectionNbItems > 0 && $sectionNbItemsValides == $sectionNbItems) {
					$this->log->debug("Questionnaire::valider() Message : tous les items sont valides");
					$contenu .= HTML_LISTE_ERREUR_DEBUT . MSG_006 . HTML_LISTE_ERREUR_FIN;
				}

				// Ajouter le gabarit de fin de section
				$contenu .= Fichiers::getContenuElement(REPERTOIRE_GABARITS_VALIDATION . "section-details-fin.php", $element);
				
				$sectionCourante = -1;
				
			}
			
			// Traitement du début d'une section
			if ($typeItem == "item_15") {
	
				$this->log->debug("Questionnaire::valider() Traitement d'une section");
				
				// Début d'une section
				$sectionNbItems = 0;
				$sectionNbItemsValides = 0;
				$sectionCourante = $idItem;
				
				// Ajouter le gabarit de début de section
				$this->log->debug("Questionnaire::valider() Début de section - Section courante maintenant à '$sectionCourante' - idSection : '$idSection'");
				$contenu .= Fichiers::getContenuElement(REPERTOIRE_GABARITS_VALIDATION . "section-details-debut.php", $element);
				
				// Valider la section
				$validationSection = $item->valider($this);
				$contenu .= $validationSection;
			} else {
				if ($typeItem != "questionnaire") {
					$nbItems++;
				}
			} 
			
			if ($typeItem != "item_15" && $typeItem != "questionnaire" && $typeItem != "") {
				$this->log->debug("Questionnaire::valider() Afficher l'item '$idItem'"); 
							
				// Valider l'item
				if ($item->valider($this)) {
					$this->log->debug("Questionnaire::valider() Item '$idItem' valide");
					$sectionNbItemsValides++;				
				} else {
					$this->log->debug("Questionnaire::valider() Item '$idItem' invalide");
					$erreursTot++;
					$contenu .= $session->get("apercu_messages");
				}
				
				// Incrémenter le nombre d'item pour la section
				if ($sectionCourante > 0) {
					$sectionNbItems++;
				}
				
			}

			$this->log->debug("Questionnaire::valider() Id : '$idItem'  Type : '$typeItem'  idSection : '$idSection'  Section courante : '$sectionCourante' ");
		}
		
		// Fin du traitement
		if ($sectionCourante > 0) {
			
			$this->log->debug("Questionnaire::valider() Fin de section détectée. Nombre d'item traité dans la section : '$sectionNbItems'  Nombre d'items valides : '$sectionNbItemsValides'");
				
				$sectionCourante = -1;

				// Message si aucun item dans la section
				if ($sectionNbItems == 0) {
					$this->log->debug("Questionnaire::valider() Message : Aucun item dans la section");
					$contenu .= HTML_LISTE_ERREUR_DEBUT . ERR_036 . HTML_LISTE_ERREUR_FIN;
				}
				
				// Message si tous les items sont valides
				if ($sectionNbItems > 0 && $sectionNbItemsValides == $sectionNbItems) {
					$this->log->debug("Questionnaire::valider() Message : tous les items sont valides");
					$contenu .= HTML_LISTE_ERREUR_DEBUT . MSG_006 . HTML_LISTE_ERREUR_FIN;
				}
								
				// Ajouter le gabarit de fin de section
				$this->log->debug("Questionnaire::valider() Ajouter le gabarit de fin de section");
				$contenu .= Fichiers::getContenuElement(REPERTOIRE_GABARITS_VALIDATION . "section-details-fin.php", $element);
		}	
		
		// Si la présentation des questions est par ordre aléatoire, vérifier que le nombre choisi ne dépasse pas le max.
		if ($this->get("generation_question_type") == "aleatoire" && $this->get("generation_question_nb") > $nbItems) {
			$this->log->debug("Questionnaire::valider() Erreur : Le nombre d'items aléatoires spécifiés est plus grand que le nombre d'item");
			$contenu .= HTML_LISTE_ERREUR_DEBUT . ERR_202 . HTML_LISTE_ERREUR_FIN;
			$erreursTot++;
		}
		
		$this->log->debug("Questionnaire::preparerListeQuestionnaire() Fin");
				
		// Placer les valeurs dans la session pour l'affichage dans une 2e requête
		$session->set("questionnaire_id_questionnaire", $this->get("id_questionnaire"));
		if ($erreursTot > 0) {
			$session->set("questionnaire_apercu_titre", $this->get("titre"));
			$session->set("questionnaire_apercu_messages_details", $contenu);
		}  else {
			$session->set("questionnaire_apercu_titre", "");
			$session->set("questionnaire_apercu_messages_details", "");
		}
		
		if ($erreursTot == 0) {
			$succes = 1;
		}
 
		$this->log->debug("Questionnaire::valider() Fin  erreursTot = '$erreursTot' succes = '$succes'");
		
		return $succes;
	}
	
	
	/**
	 * 
	 * Aperçu d'un questionnaire
	 * @param Projet Objet projet
	 * @param Usager Objet usager
	 * @param string Chaine aléatoire
	 *
	 */
	public function genererApercu($projet, $usager, $chaineAleatoire) {

		$this->log->debug("Questionnaire::genererApercu() Début chaineAleatoire = '$chaineAleatoire'");
		
		$succes = 0;
		
		// Déterminer le répertoire source selon le thème
		$theme = $this->get("theme");
		if ($theme == "") {
			$theme = FICHIER_THEME_DEFAUT;
		}
		$repertoireSource = REPERTOIRE_THEMES . $theme . "/";
		
		// Déterminer le répertoire de publication de l'aperçu
		$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/";
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $chaineAleatoire . "/";
		
		// Vérifier que le répertoire de destination n'existe pas
		if (!is_dir($repertoireDestination)) {
			
			$this->log->debug("Questionnaire:genererApercu() Publication du questionnaire");

			// Publier le questionnaire	
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes == 1) {
				$succes = $publication->copierTheme($repertoireSource, $repertoireDestination);
			}
	
			// Préparer le fichier de configuration du quiz (main.js)
			$contenu = $this->publierContenu($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_MAIN_JS, $contenu);
			
			// Préparer le fichier lexique.js
			$contenu = $this->publierLexique($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_LEXIQUE_JS, $contenu);
			
		} else {
			$this->log->debug("Questionnaire::genererApercu() Impossible de publier le questionnaire - le répertoire existe déjà");
		}		
			
		$this->log->debug("Questionnaire::genererApercu() Fin");
		return $succes;
	}		
	
	
	/**
	 * 
	 * Exporter un questionnaire en format XML (+zip)
	 * @param usager Objet usager
	 *
	 */
	public function exporterXML($usager) {

		$this->log->debug("Questionnaire::exporterXML() Début");
		
		$succes = 0;
		$urlFichierZip = "";

		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomRepertoireZip = Securite::nettoyerNomfichier($this->get("titre")) . "_" . $ts . "_xml"; 
		$nomFichierZip = $nomRepertoireZip . ".zip";
		$urlFichierZip = URL_PUBLICATION . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomFichierZip;
		
		// Déterminer le répertoire de publication de l'aperçu
		$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/";
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";

		// Vérifier que le répertoire de destination n'existe pas
		if (!is_dir($repertoireDestination)) {
			
			$this->log->debug("Questionnaire:exporterXML() Exportation du questionnaire");

			// Exporter le questionnaire	
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes) {
				// Créer le répertoire média
				$succes = $publication->creerRepertoireMedia($repertoireDestination);
			}
	
			// Exporter le contenu en format XML
			$contenu = $this->exporterXMLContenu($repertoireDestination);
			
			// Écrire le contenu dans un fichier XML
			$publication->ecrireFichier($repertoireDestination . FICHIER_EXPORTATION_XML, $contenu);
			
		} else {
			$this->log->debug("Questionnaire::exporterXML() Impossible de publier le questionnaire - le répertoire existe déjà");
		}		
			
		if ($succes == 1) {
			// Déterminer le répertoire source
			$repertoireSourceZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU ;
					
			// Préparer le fichier zip
			$fichierZip = $repertoireSourceZip . $nomFichierZip;
			
			// Zip des fichiers
			Fichiers::Zip($repertoireDestination, $fichierZip);
			
			// Supprimer le répertoire temporaire
			$this->log->debug("Questionnaire::exporterXML() Suppression du répertoire '$repertoireDestination'");
			Fichiers::rmdirr($repertoireDestination);
		} else {
			$urlFichierZip = "";
		}
		
		$this->log->debug("Questionnaire::exporterXML() Fin");
		return $urlFichierZip;
	}		
	

	/**
	 * 
	 * Aperçu d'un questionnaire transmis en fichier zip
	 * @param usager Objet usager
	 *
	 */
	public function genererApercuZip($usager) {

		$this->log->debug("Questionnaire::genererApercuZip() Début");
		
		$succes = 0;
		$urlFichierZip = "";

		// Déterminer le répertoire source selon le thème
		$theme = $this->get("theme");
		if ($theme == "") {
			$theme = FICHIER_THEME_DEFAUT;
		}
		$repertoireSource = REPERTOIRE_THEMES . $theme . "/";

		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomRepertoireZip = Securite::nettoyerNomfichier($this->get("titre")) . "_" . $ts; 
		$nomFichierZip = $nomRepertoireZip . ".zip";
		$urlFichierZip = URL_PUBLICATION . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomFichierZip;
		
		// Déterminer le répertoire de publication de l'aperçu
		$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/";
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";
		
		// Vérifier que le répertoire de destination n'existe pas
		if (!is_dir($repertoireDestination)) {
			
			$this->log->debug("Questionnaire:genererApercu() Publication du questionnaire");

			// Publier le questionnaire	
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes == 1) {
				$succes = $publication->copierTheme($repertoireSource, $repertoireDestination);
			}
	
			// Préparer le fichier de configuration du quiz (main.js)
			$contenu = $this->publierContenu($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_MAIN_JS, $contenu);
			
			// Préparer le fichier lexique.js
			$contenu = $this->publierLexique($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_LEXIQUE_JS, $contenu);			
			
		} else {
			$this->log->debug("Questionnaire::genererApercu() Impossible de publier le questionnaire - le répertoire existe déjà");
		}		
			
		if ($succes == 1) {
			// Déterminer le répertoire source et destination
			$repertoireSourceZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . $nomRepertoireZip . "/";
			$repertoireDestinationZip = REPERTOIRE_PUB . Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" .  REPERTOIRE_PREFIX_APERCU . "/";
			
			// Préparer le fichier zip
			$fichierZip = $repertoireDestinationZip . $nomFichierZip;
			
			// Zip des fichiers
			Fichiers::Zip($repertoireSourceZip, $fichierZip);
			
			// Supprimer les fichiers temporaires
			$this->log->debug("Questionnaire::genererApercuZip() Suppression du répertoire '$repertoireSourceZip'");
			Fichiers::rmdirr($repertoireSourceZip);
			
		} else {
			$urlFichierZip = "";
		}
		
		$this->log->debug("Questionnaire::genererApercuZip() Fin");
		return $urlFichierZip;
	}		
	
	
	
	/**
	 * 
	 * Publier un questionnaire
	 * 
	 * @param Projet projet
	 * @param string Répertoire Destination
	 *
	 */
	public function publier($projet, $repertoirePublication) {

		$this->log->debug("Questionnaire::publier() Début");
		
		$succes = 0;
			
		// Au besoin, désactiver la publication
		if ($this->get("publication_repertoire") != "") {
			$this->desactiverPublication($projet);
		}
		
		// Déterminer le répertoire source selon le thème
		$theme = $this->get("theme");
		if ($theme == "") {
			$theme = FICHIER_THEME_DEFAUT;
		}
		$repertoireSource = REPERTOIRE_THEMES . $theme . "/";
		
		// Déterminer les répertoires sources et de publication de l'aperçu
		$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($projet->get("repertoire")) . "/";
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($projet->get("repertoire")) . "/" . $repertoirePublication . "/";
		
		// Vérifier que le répertoire de destination n'existe pas
		if (!is_dir($repertoireDestination)) {
			
			$this->log->debug("Questionnaire::publier() Publication du questionnaire");

			// Publier le questionnaire	
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes == 1) {
				$succes = $publication->copierTheme($repertoireSource, $repertoireDestination);
			}
	
			// Préparer le fichier de configuration du quiz (main.js)
			$contenu = $this->publierContenu($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_MAIN_JS, $contenu);
			
			// Préparer le fichier lexique.js
			$contenu = $this->publierLexique($repertoireDestination);
			$publication->ecrireFichier($repertoireDestination . FICHIER_LEXIQUE_JS, $contenu);
			
			// Enregistrer les informations de publication
			$this->set("publication_repertoire", $repertoirePublication);
			$this->set("publication_date", date("Y-m-d H:i:s", time()));
			$this->set("statut", "2");

			$this->enregistrer();
		} else {
			$this->log->debug("Questionnaire::publier() Impossible de publier le questionnaire - le répertoire existe déjà");
		}
		
		$this->log->debug("Questionnaire::publier() Fin");
		
		return $succes;
	}
	
	/**
	 * 
	 * Préparer la publication d'un questionnaire
	 * 
	 * @param string repertoireDestination
	 *
	 */
	protected function preparerPublication($repertoireDestination) {

		$this->log->debug("Questionnaire::preparerPublication() Début");	

		// Préparer le champ texte du média d'accueil
		$info = Publication::preparerChampTitreTexte($this->get("media_titre"), $this->get("media_texte"), $this->log);
		$this->set("media_titre_texte", Publication::preparerChampTitreTexte($this->get("media_titre"), $this->get("media_texte"), $this->log));
				
		// Fichier image - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierImage = "";
		$sourceImage = "";
		if ($this->get("media_image") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierImage = $media->getNomFichierMedia($this->get("media_image"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
			
			// Déterminer si le fichier est local ou web
			$sourceImage = 1; // local par défaut
			if ($media->get("source") == "web") {
				$sourceImage = 2;
			}
		}
		$this->set("media_image_fichier", $fichierImage);
		$this->set("media_image_fichier_source", $sourceImage);

		// Fichier son - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierSon = "";
		$sourceSon = "";
		if ($this->get("media_son") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierSon = $media->getNomFichierMedia($this->get("media_son"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
			
			// Déterminer si le fichier est local ou web
			$sourceSon = 1; // local par défaut
			if ($media->get("source") == "web") {
				$sourceSon = 2;
			}			
		}
		$this->set("media_son_fichier", $fichierSon);
		$this->set("media_son_fichier_source", $sourceSon);

		// Fichier video - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierVideo = "";
		$sourceVideo = "";
		if ($this->get("media_video") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierVideo = $media->getNomFichierMedia($this->get("media_video"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
			
			// Déterminer si le fichier est local ou web
			$sourceVideo = 1; // local par défaut
			if ($media->get("source") == "web") {
				$sourceVideo = 2;
			}			
		}
		$this->set("media_video_fichier", $fichierVideo);
		$this->set("media_video_fichier_source", $sourceVideo);	
		
		// Démarrage auto des médias
		// --------------------------------------------------------
		$this->log->debug("Questionnaire::preparerPublication() Démarrer média questionnaire : '" . $this->get("demarrage_media_type") . "'");
		$demarrageMedia = $this->get("demarrage_media_type");
		$this->log->debug("Item::preparerPublication() Démarrer média : '$demarrageMedia'");
		
		// Démarrage auto du vidéo ou son
		// --------------------------------------------------------
		if ($demarrageMedia == "video") {
			$this->set("demarrage_video", "true");
			$this->set("demarrage_audio", "false");
		} 
		elseif ($demarrageMedia == "audio") {
			$this->set("demarrage_video", "false");
			$this->set("demarrage_audio", "true");
		}
		elseif ($demarrageMedia == "aucun") {
			$this->set("demarrage_video", "false");
			$this->set("demarrage_audio", "false");
		}		

		// Ordre de présentation
		// --------------------------------------------------------
		if ($demarrageMedia == "video") {
			$this->set("demarrage_video", "true");
			$this->set("demarrage_audio", "false");
		} 
		elseif ($demarrageMedia == "audio") {
			$this->set("demarrage_video", "false");
			$this->set("demarrage_audio", "true");
		}
		elseif ($demarrageMedia == "aucun") {
			$this->set("demarrage_video", "false");
			$this->set("demarrage_audio", "false");
		}		

		// Temps réponse
		// --------------------------------------------------------
		$tempsReponse = "false";
		if ($this->get("temps_reponse_calculer") == "oui") {
			$tempsReponse = "true";
		}
		$this->set("temps_reponse", $tempsReponse);
		
		$this->log->debug("Questionnaire::preparerPublication() Fin");

		
		// Obtenir la liste des médias dans les champs rich text
		// --------------------------------------------------------
		$cles = array_keys($this->donnees);
			
		// Parcourir la liste de clés
		foreach ($cles as $cle) {
			$contenu = $this->get($cle);
			$matches = array();
				
			// Effectuer la recherche
			preg_match_all("/\[M(\d+?)]/i", $contenu, $matches, PREG_SET_ORDER);
				
			foreach ($matches as $val) {
		
				// Obtenir le texte trouvé
				$matchMedia = $val[0];
				$idMedia = $val[1];
		
				// Obtenir le média
				$media = new Media($this->log, $this->dbh);
				$nomFichier = $media->getNomFichierMedia($idMedia, $this->get("id_projet"));
		
				// Préparer le HTML selon le type de media
				$html = "";
				if ($media->get("type") == "image") {
					$gabApercu = HTML_APERCU_IMAGE;
					$html = str_replace("::IMAGE::", $nomFichier, $gabApercu);
				}
		
				$contenu = str_replace($matchMedia, $html, $contenu);
		
				// Préparer le contenu pour publication
				$clePub = $cle . "_pub";
				$this->set($clePub, $contenu);
		
				// Copier le fichier média
				$media->copierFichierMedia($repertoireDestination);
			}
		}		
		
	}

	
	/**
	 * 
	 * Publier un questionnaire
	 * @param string Répertoire Destination
	 *
	 */
	protected function publierContenu($repertoireDestination) {

		$this->log->debug("Questionnaire::publierContenu() Début");
		
		$contenu = "";
		$contenuDetails = "";
		$indexItems = 0;
		
		// Préparer les champs pour la publication
		$this->preparerPublication($repertoireDestination);
		
		// Déterminer la langue pour l'aperçu
		$idLangue = $this->get("id_langue_questionnaire");
		$this->log->debug("Questionnaire::publierRepertoire() Langue du questionnaire : '$idLangue'");
		
		// Charger la langue
		$langue = new Langue($this->log, $this->dbh);
		$langue->getLangueParId($idLangue, $this->get("id_projet"));

		// Obtenir les items dans l'ordre du menu
		$menu = new Menu($this->log, $this->dbh);
		$listeItems = $menu->getMenu($this->get("id_questionnaire"), $this->get("id_projet"));

		// Obtenir le paramètre pour l'ordre de présentation
		$ordrePresentationQuest = $this->get("generation_question_type");
		$this->log->debug("Questionnaire::publierRepertoire() ordre de présentation du questionnaire : '$ordrePresentationQuest'\n");
		
		// Traiter la liste des items
		$idSectionActuelle = "";
		$avantPremiereSection = 1;
		$indexElement = 0;
		$indexDebut = 0;
		$indexFin = 0; 
		$listeItemsAleatoire = array();
		
		foreach ($listeItems as $element) {
		
			$idItem = $element->getId();
			$typeItem = $element->getType();
			$idSection = $element->getIdSection();

			$this->log->debug("Questionnaire::publierRepertoire() indexElement : '$indexElement'  idItem : '$idItem'  typeItem : '$typeItem'  idSection : '$idSection'\n");
			
			if ($typeItem != "item_15" && $typeItem != "questionnaire" && $typeItem != "") {
			
				// Changement de section
				if ($idSection != $idSectionActuelle) {

					if ($idSectionActuelle != "" && $idSectionActuelle != "0") {
						$indexFin = $indexElement - 1;
						$this->log->debug("Questionnaire::publierRepertoire() Générer changement de section!\n");
						$this->log->debug("Questionnaire::publierRepertoire()   Changement de section : idSection : '$idSection'  idSectionActuelle : '$idSectionActuelle'\n");
						$this->log->debug("Questionnaire::publierRepertoire()   indexDebut : '$indexDebut'  indexFin : '$indexFin'\n");
						
						// Obtenir les informations sur la section au besoin
						$ordrePresentationSect = "";
						if ($ordrePresentationQuest == "section") {
							$this->log->debug("Questionnaire::publierRepertoire() Obtenir l'ordre de présentation pour la section '$idSectionActuelle'\n");
							$itemSection = new ItemSection($this->log, $this->dbh);
							$itemSection->getItemParId($idSectionActuelle, $this->get("id_projet"));
							$ordrePresentationSect = $itemSection->get("generation_question_type");
						}
						
						// Tenir compte de l'ordre aélatoire
						if ($ordrePresentationQuest == "aleatoire" || $ordrePresentationSect == "aleatoire") {
							$contenuDetails .= HTML_ITEM_ALEATOIRE_DEBUT. $indexDebut . HTML_ITEM_ALEATOIRE_SEPARATEUR . $indexFin . HTML_ITEM_ALEATOIRE_FIN . "\n";
						}
					}
					
					// Initialisation pour la suite
					$indexDebut = $indexElement;
					$idSectionActuelle = $idSection;
				}

				// Prendre note des items en dehors des sections
				if ($idSection == 0) {
					$this->log->debug("Questionnaire::publierRepertoire() Item seul (dans aucune section) : '$idItem'\n");
					array_push($listeItemsAleatoire, $indexElement);
				}
				
				// Incrément idx élément
				$indexElement++;				
			}

			// Instancier l'item
			$itemFactory = new Item($this->log, $this->dbh);
			$item = $itemFactory->instancierItemParType('', $this->get("id_projet"), $idItem);
			
			// Obtenir le nom de la section
			$titreSection = "";
			if ($idSection > 0) {
				$section = new ItemSection($this->log, $this->dbh);
				$section->getItemParId($idSection, $this->get("id_projet"));
				$titreSection = $section->get("titre");
			} 
			$item->set("titreSection", $titreSection);
			
			// Ajouter la langue du questionnaire
			$item->set("id_langue_questionnaire", $idLangue);

			// Préparer l'index de publication (début à 0)
			$item->set("item_index", $indexItems);
			
			// Traitement d'un item
			if ($typeItem != "item_15" && $typeItem != "questionnaire" && $typeItem != "") {
				
				// Si un id questionnaire est passé en paramètre, charger les données "pour ce questionnaire seulement"
				$item->getValeursPourQuestionnaire($this->get("id_questionnaire"), $this->get("id_projet"));
				
				// Publier l'item
				$contenuDetails .= $item->publier($langue, $repertoireDestination, $this);
				$indexItems++;						
			}
		}

		// Traiter le dernier changement de section
		$this->log->debug("Questionnaire::publierRepertoire() Changement fin de section : idSection : '$idSection'  idSectionActuelle : '$idSectionActuelle'\n");
		if ($idSection != "") {

			if ($idSectionActuelle != "" && $idSectionActuelle != "0") {
				$indexFin = $indexElement - 1;
				$this->log->debug("Questionnaire::publierRepertoire() Générer changement de section!\n");
				$this->log->debug("Questionnaire::publierRepertoire()    Changement de section : idSection : '$idSection'  idSectionActuelle : '$idSectionActuelle'\n");
				$this->log->debug("Questionnaire::publierRepertoire()    indexDebut1 : '$indexDebut'  indexFin1 : '$indexFin'\n");
				
				if ($ordrePresentationQuest == "aleatoire") {
					$contenuDetails .= HTML_ITEM_ALEATOIRE_DEBUT. $indexDebut . HTML_ITEM_ALEATOIRE_SEPARATEUR . $indexFin . HTML_ITEM_ALEATOIRE_FIN . "\n";
				}
			}
			
			// Initialisation pour la suite
			$indexDebut = $indexElement;
			$idSectionActuelle = $idSection;
		}		

		// Produire la liste aléatoire des items sans section
		if ($ordrePresentationQuest == "aleatoire") {
			$listeItemsSansSection = implode(HTML_ITEM_HORS_SECTION_ALEATOIRE_SEPARATEUR, $listeItemsAleatoire);
			$contenuDetails .= HTML_ITEM_HORS_SECTION_ALEATOIRE_DEBUT . $listeItemsSansSection . HTML_ITEM_HORS_SECTION_ALEATOIRE_FIN;
		}
		
		// Conserver le nombre d'items
		$this->set("nb_items_total", $indexItems);

		// Récupérer le gabarit pour publier un seul item - début
		$contenu .= Fichiers::getContenuQuestLangue(REPERTOIRE_GABARITS_PUBLICATION . "questionnaire-debut.php", $this, $langue);

		// Ajouter les détails
		$contenu .= $contenuDetails;
		
		// Récupérer le gabarit pour publier un seul item - fin
		$contenu .= Fichiers::getContenuQuestLangue(REPERTOIRE_GABARITS_PUBLICATION . "questionnaire-fin.php", $this, $langue);
		
		$this->log->debug("Questionnaire::publierContenu() Fin");
		
		return $contenu;
	}		
	

	/**
	 *
	 * Publier lexique
	 * @param String répertoire de destination
	 *
	 */
	public function publierLexique($repertoireDestination) {
	
		$this->log->debug("Questionnaire::publierLexique() Début");
		
		$listeTermes = array();
		
		// Obtenir les variables du questionnaire courant
		$listeCles = array_keys($this->donnees);
		$prefix = "terme_selection_";
		
		// Obtenir la liste des termes
		foreach ($listeCles as $cle){
				
			$str = strstr($cle, $prefix);
			if ($str != "") {
				
				// Terme à traiter
				$idTerme = substr($str, strlen($prefix));
				
				// Obtenir les infos sur le terme de la BD
				$t = new Terme($this->log, $this->dbh);
				$t->getTermeParId($idTerme, $this->get("id_projet"));
				
				// Préparer les informations du termes
				$tp = new TermePublication();
				$tp->set("expression",$t->get("terme"));
				
				// Régler correctement les valeurs des champs pour correspondre aux valeurs attendues par le JS du Quiz
				
				// ****************************************************** TEXTE *********************************************************
				if ($t->get("type_definition") == "texte" || $t->get("type_definition") == "" ) {
					$tp->set("type", "texte");
					$tp->set("localisation", "1");
					$tp->set("contenu", $t->get("texte"));
				}
				
				// ****************************************************** URL *********************************************************
				if ($t->get("type_definition") == "url") {
					$tp->set("type", "lien");
					$tp->set("localisation", "2");
					$tp->set("contenu", $t->get("url"));
				}				
				
				// ****************************************************** IMAGE *********************************************************
				if ($t->get("type_definition") == "media_image") {
					
					// Obtenir le média
					$fichierImage = "";
					$sourceImage = "";
					if ($t->get("media_image") > 0) {
						$media = new Media($this->log, $this->dbh);
						$fichierImage = $media->getNomFichierMedia($t->get("media_image"), $t->get("id_projet"));
						$media->copierFichierMedia($repertoireDestination);
							
						// Déterminer si le fichier est local ou web
						$sourceImage = 1; // local par défaut
						if ($media->get("source") == "web") {
							$sourceImage = 2;
						}
					}					
					
					$tp->set("type", "image");
					$tp->set("localisation", $sourceImage);
					$tp->set("contenu", $fichierImage);
				}
				
				
				// ****************************************************** SON *********************************************************
				if ($t->get("type_definition") == "media_son") {
				
					// Fichier son - Obtenir le nom et copier le fichier
					$fichierSon = "";
					$sourceSon = "";
					
					if ($t->get("media_son") > 0) {
						$media = new Media($this->log, $this->dbh);
						$fichierSon = $media->getNomFichierMedia($t->get("media_son"), $t->get("id_projet"));
						$media->copierFichierMedia($repertoireDestination);
							
						// Déterminer si le fichier est local ou web
						$sourceSon = 1; // local par défaut
						if ($media->get("source") == "web") {
							$sourceSon = 2;
						}
					}

					// Réglages pour le côté "client"
					$tp->set("type", "son");
					$tp->set("localisation", $sourceSon);
					$tp->set("contenu", $fichierSon);
				}
				
				
				// ****************************************************** VIDÉO *********************************************************
				if ($t->get("type_definition") == "media_video") {
								
					// Fichier video - Obtenir le nom et copier le fichier
					$fichierVideo = "";
					$sourceVideo = "";
					
					if ($t->get("media_video") > 0) {
						$media = new Media($this->log, $this->dbh);
						$fichierVideo = $media->getNomFichierMedia($t->get("media_video"), $t->get("id_projet"));
						$media->copierFichierMedia($repertoireDestination);
							
						// Déterminer si le fichier est local ou web
						$sourceVideo = 1; // local par défaut
						if ($media->get("source") == "web") {
							$sourceVideo = 2;
						}
					}
					
					// Réglages pour le côté "client"
					$tp->set("type", "video");
					$tp->set("localisation", $sourceVideo);
					$tp->set("contenu", $fichierVideo);					
				}			
				
				// Préparer les variantes
				$listeVariantes = explode("\n", $t->get("variantes"));
				$tp->set("variantes", implode(PUBLICATION_SEPARATEUR_VARIANTES, $listeVariantes));
				
				// Ajouter à la liste des termes
				array_push($listeTermes, $tp);
				
			}
		}

		// Ajouter la liste des termes au questionnaire
		$this->set("listeTermes", $listeTermes);
		
		// Publier avec le bon gabarit
		if (count($listeTermes) > 0) {
			// Récupérer le gabarit pour publier le lexique
			$contenu = Fichiers::getContenuQuest(REPERTOIRE_GABARITS_PUBLICATION . "lexique.php", $this);
		} else {
			// Récupérer le gabarit pour publier le lexique
			$contenu = Fichiers::getContenuQuest(REPERTOIRE_GABARITS_PUBLICATION . "lexique-vide.php", $this);
		}
		
		$this->log->debug("Questionnaire::publierLexique() Fin");
		return $contenu;
	}
	
	
	/**
	 * 
	 * Exporter un questionnaire en format XML étape par étape
	 * @param string Répertoire Destination
	 *
	 */
	protected function exporterXMLContenu($repertoireDestination) {

		$this->log->debug("Questionnaire::exporterXMLContenu() Début");
		
		$contenu = "";
		$contenuItems = "";
		$contenuSections = "";
		$contenuDetails = "";
		$contenuTermes = "";
		
		// Préparer les champs pour la publication
		$this->preparerPublication($repertoireDestination);
		
		// Déterminer la langue pour l'aperçu
		$idLangue = $this->get("id_langue_questionnaire");
		$this->log->debug("Questionnaire::exporterXMLContenu() Langue du questionnaire : '$idLangue'");
		
		// Charger la langue
		$langue = new Langue($this->log, $this->dbh);
		$langue->getLangueParId($idLangue, $this->get("id_projet"));

		// Obtenir les items dans l'ordre du menu
		$menu = new Menu($this->log, $this->dbh);
		$listeItems = $menu->getMenu($this->get("id_questionnaire"), $this->get("id_projet"));

		// Obtenir le paramètre pour l'ordre de présentation
		$ordrePresentationQuest = $this->get("generation_question_type");
		$this->log->debug("Questionnaire::exporterXMLContenu() ordre de présentation du questionnaire : '$ordrePresentationQuest'\n");
		
		// Traiter la liste des items
		$nbItems = 0;
		$listeMedias = array();
		foreach ($listeItems as $element) {
		
			$idItem = $element->getId();
			$typeItem = $element->getType();
			$idSection = $element->getIdSection();

			$this->log->debug("Questionnaire::exporterXMLContenu() idItem : '$idItem'  typeItem : '$typeItem'  idSection : '$idSection'\n");

			// Instancier l'item
			if ($typeItem != "questionnaire") {
				$itemFactory = new Item($this->log, $this->dbh);
				
				$item = $itemFactory->instancierItemParType('', $this->get("id_projet"), $idItem);
				
				// Ajouter l'id de section
				$item->set("id_section", $idSection);

				if ($typeItem != "item_15" && $typeItem != "") {
					// Exporter les informations d'un item
					$contenuItems .= $item->exporterXML($langue, $repertoireDestination, $this);
				} else {
					// Exporter les informations d'une section
					$contenuSections .= $item->exporterXML($langue, $repertoireDestination, $this);
				}
				
				// Exporter les valeurs pour ce questionnaire seulement
				$contenuDetails .= $item->exporterXMLQuestItem($langue, $repertoireDestination, $this);
				
				// Obtenir la liste des médias
				$listeMedias = $item->ajouterMediaListeExportation($listeMedias);
			}
			$nbItems++;						
		}
		
		// Traiter la liste des termes
		$listeCles = array_keys($this->donnees);
		$prefix = "terme_selection_";
		
		// Obtenir la liste des termes
		foreach ($listeCles as $cle){
			$str = strstr($cle, $prefix);
			if ($str != "") {
				$idTerme = substr($str, strlen($prefix));
				$t = new Terme($this->log, $this->dbh);
				$t->getTermeParId($idTerme, $this->get("id_projet"));
				$contenuTermes .= $t->exporterXML($repertoireDestination);
				
				// Ajouter les médias des termes à la liste des médias à exporter
				$listeMedias = $t->ajouterMediaListeExportation($listeMedias);
				
				// Ajouter la relation questionnaire > terme
				$contenuDetails .= $t->exporterXMLQuestTerme($repertoireDestination);
			}
		}
		
		// Ajouter les médias du questionnaire à la liste des médias à exporter
		$listeMedias = $this->ajouterMediaListeExportation($listeMedias);
		
		// Préparer la listes des médias
		$media = new Media($this->log, $this->dbh);
		$contenuMedia = $media->exporterListeMediasXML($listeMedias, $this->get("id_projet"));
		
		// Conserver le nombre d'items
		$this->set("nb_items_total", $nbItems);

		// Entête XML
		$contenu .= XML_ENTETE . "\n";
		$contenu .= XML_NQW_DEBUT . "\n";
		
		// Récupérer le gabarit pour publier un questionnaire - début
		$contenu .= Fichiers::getContenuQuestLangue(REPERTOIRE_GABARITS_EXPORTATION . "questionnaire-debut.php", $this, $langue);

		// Valeurs pour ce questionnaire seulement
		$contenu .= $contenuDetails;
		
		// Ajouter les sections
		$contenu .= $contenuSections;
		
		// Récupérer le gabarit pour publier un questionnaire - fin
		$contenu .= Fichiers::getContenuQuestLangue(REPERTOIRE_GABARITS_EXPORTATION . "questionnaire-fin.php", $this, $langue);
		
		// Ajouter les items
		$contenu .= $contenuItems;
		
		// Ajouter les termes
		$contenu .= $contenuTermes;
				
		// Ajouter les médias
		$contenu .= $contenuMedia;
		
		// Fin du fichier XML
		$contenu .= XML_NQW_FIN . "\n";
		
		$this->log->debug("Questionnaire::exporterXMLContenu() Fin");
		
		return $contenu;
	}			
	

	/**
	 * 
	 * ajouterMediaListeExportation()
	 * Ajouter les médias d'un item à la liste d'exportation
	 * @param array Liste médias à exporter
	 *
	 */
	public function ajouterMediaListeExportation($listeMedias) {
	
		$this->log->debug("Questionnaire::ajouterMediaListeExportation() Début");
	
		// Image
		if ($this->get("media_image") > 0 && !in_array($this->get("media_image"), $listeMedias)) {
			array_push($listeMedias, $this->get("media_image"));
		}
		
		// Son
		if ($this->get("media_son") > 0 && !in_array($this->get("media_son"), $listeMedias)) {
			array_push($listeMedias, $this->get("media_son"));
		}

		// Video
		if ($this->get("media_video") > 0 && !in_array($this->get("media_video"), $listeMedias)) {
			array_push($listeMedias, $this->get("media_video"));
		}
		
		$this->log->debug("Questionnaire::ajouterMediaListeExportation() Fin");
		
		return $listeMedias;
	}	
	
	
	/**
	 * 
	 * Obtenir la langue du questionnaire
	 * 
	 */
	public function getLangueApercuObj() {

		$this->log->debug("Questionnaire::getLangueApercuObj() Début");

		$langueItem = new Langue($this->log, $this->dbh);		
		
		// Obtenir l'id de la langue pour apercu
		$idLangueItem = $this->get("id_langue_questionnaire");
		
		// Instancier la langue
		$langueItem->getLangueParId($idLangueItem, $this->get("id_projet"));
	
		$this->log->debug("Questionnaire::getLangueApercuObj() Fin");
		return $langueItem;
	}	
	
	
	/**
	 * 
	 * Désactiver un questionnaire
	 * @param Projet
	 *
	 */
	public function desactiverPublication($projet) {

		$this->log->debug("Questionnaire::desactiverPublication() Début");
			
		$succes = 0;
		
		// Déterminer les répertoires sources et de publication de l'aperçu
		$repertoireDestination = REPERTOIRE_PUB . Securite::nettoyerNomfichier($projet->get("repertoire")) . "/" . $this->get("publication_repertoire") . "/";
		
		// Supprimer les fichiers du questionnaire
		$this->log->debug("Questionnaire::desactiverPublication() Suppression du répertoire du questionnaire publié : '$repertoireDestination'");
		if ($this->get("publication_repertoire")!= "" && is_dir($repertoireDestination)) {
			$this->log->debug("Questionnaire::desactiverPublication() Suppression du répertoire '$repertoireDestination'");
			Fichiers::rmdirr($repertoireDestination);
		}
	
		// Enregistrer les informations de publication
		$this->set("publication_repertoire", "");
		$this->set("publication_date", date("d/m/y H:i:s", time()));
		$this->set("statut", "1");
		$this->enregistrer();
		
		$this->log->debug("Questionnaire::desactiverPublication() Fin");
		
		return $succes;
	}	

	
	/**
	 *
	 * Vérifier que le questionnaire publié existe toujours
	 * @param Projet projet
	 *
	 */
	public function verifierQuestionnairePublie($projet) {
	
		$this->log->debug("Questionnaire::verifierQuestionnairePublie() Début");	
	
		// Si le questionnaire a un statut publié, vérifier que le contenu existe
		if ($this->get("statut") == "2") {
			$repertoire = $this->get("publication_repertoire");
			
			// Si le répertoire a été défini, vérifier qu'il existe
			if ($repertoire != "") {
				$repertoireDest = REPERTOIRE_PUB . Securite::nettoyerNomfichier($projet->get("repertoire")) . "/" . $this->get("publication_repertoire") . "/";
				
				// Si le répertoire n'existe pas physiquement sur disque, désactiver le projet
				if (!is_dir($repertoireDest)) {
					// Désactiver publication
					$this->desactiverPublication($projet);
				}
			}
		}
		
		$this->log->debug("Questionnaire::verifierQuestionnairePublie() Fin");
	}
	

	/**
	 *
	 * Vérifier que le thème sélectionné pour le questionnaire existe
	 *
	 */
	public function verifierThemeSelectionne() {
	
		$this->log->debug("Questionnaire::verifierThemeSelectionne() Début");

		$repertoireExiste = false;
		
		// Vérifier que le thème existe
		$repertoireSourceTheme = REPERTOIRE_THEMES . $this->get("theme") . "/";
		$this->log->debug("Questionnaire::verifierThemeSelectionne() Vérifier que le thème sélectionné existe ('$repertoireSourceTheme')");
		if (is_dir($repertoireSourceTheme)) {
			$repertoireExiste = true;
			$this->log->debug("Questionnaire::verifierThemeSelectionne() Problème détecté : Le répertoire du thème sélectionné n'existe pas");
		}
	
		$this->log->debug("Questionnaire::verifierThemeSelectionne() Fin");
		
		return $repertoireExiste;
	}
	
	
	/**
	 * 
	 * Activer le suivi d'un questionnaire
	 *
	 */
	public function activerSuivi() {
		
		$this->log->debug("Questionnaire::activerSuivi() Début");
		
		// Activer le suivi
		$this->set("suivi", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Questionnaire::activerSuivi() Fin");
	}

	/**
	 * 
	 * Désactiver le suivi d'un questionnaire
	 *
	 */
	public function desactiverSuivi() {
		
		$this->log->debug("Questionnaire::desactiverSuivi() Début");
		
		// Activer le suivi
		$this->set("suivi", "0");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Questionnaire::desactiverSuivi() Fin");
	}	

	/**
	 * 
	 * Obtenir le statut dans la langue de l'utilisateur
	 */
	public function getStatutTxt() {
	
		// Obtenir la chaîne à récupérer
		$str ="QUESTIONNAIRE_STATUT_" . strtoupper($this->get('statut'));
		
		// Obtenir la valeur à partir du fichier des langues
		$val = constant($str);
		
		return $val;
	}	
	
	
	/**
	 * 
	 * Obtenir une valeur
	 * 
	 * @param String $valeur
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
	 * @param String $libelle
	 * @param String $valeur 
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
	 * Obtenir une valeur pour javascript
	 * 
	 * @param String valeur
	 * 
	 */
	public function getJS( $valeur ) {
		
		// Par défaut utiliser la clé demandée
		$val = $valeur;
		
		// Vérifier si une version pour publication est disponible (avec des médias),
		// si oui, utiliser cette version
		$valeurPub = $valeur . "_pub";
		
		if (isset($this->donnees[$valeurPub])) {
			$val = $valeurPub;
		}
		
		return Web::nettoyerChainePourJs($this->get($val));
	}
	
	
	/**
	 * 
	 * Obtenir une valeur pour du XML
	 * 
	 */
	public function getXML( $valeur ) {
		return Web::nettoyerChainePourXML($this->get($valeur));
	}
	
	/**
	 *
	 * Supprimer les valeurs qui débutent par un prefix
	 * 
	 * @param String libellé
	 *
	 */
	public function deleteByPrefix( $libelle ) {
	
		// Obtenir les clés
		$cles = array_keys($this->donnees);
			
		// Parcourir la liste de clés
		foreach ($cles as $cle) {
	
			// Supprimer la clé si match
			if (substr($cle, 0, strlen($libelle)) == $libelle) {
				unset($this->donnees[$cle]);
			}
		}
	}
	
	/**
	 *
	 * Supprimer une valeur
	 *
	 */
	public function delete( $libelle ) {
		unset ($this->donnees[$libelle]);
	}	
	
}

?>
