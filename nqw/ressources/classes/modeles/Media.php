<?php

/** 
 * Classe Media
 * 
 * Représente une image, un son ou un vidéo
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Media {
	
	const STATUT_INACTIF = "0";
	const STATUT_ACTIF = "1";
	
	protected $dbh;
	protected $log;
	
	protected $listeChamps = "id_media, id_projet, titre, remarque, type, description, source, fichier, url, suivi, liens, statut, date_creation, date_modification";
							  
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
	 * Sauvegarder les informations dans la base de données - ajout d'une media
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Media::ajouter() Début");
		
		// Obtenir le prochain id pour un média
		$projet = new Projet($this->log, $this->dbh);
		$projet->getProjetParId($this->get("id_projet"));
		$idMedia = $projet->genererIdMedia();
		$this->set("id_media", $idMedia);		
		
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVEAU_MEDIA);
		}
		
		// Préparer ajout
		$stmt = $this->dbh->prepare("insert into tmedia (id_media, id_projet, titre, type, description, source, fichier, url, suivi, liens, remarque, statut, date_creation, date_modification) 
									 values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, now(),now() )");

		// Statut par défaut = brouillon
		$this->set("statut", "1");
		
		try {
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_media'),
								 $this->get('id_projet'), 
								 $this->get('titre'),
								 $this->get('type'),
								 $this->get('description'),
								 $this->get('source'),
								 $this->get('fichier'),
								 $this->get('url'),
								 $this->get('suivi'),
								 (int)$this->get('liens'),
								 $this->get('remarque'),
								 ));
			
			$this->log->debug("Media::ajouter() Nouvelle media créée (id = '" . $this->get('id_media') . "')");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		
		// Mettre à jour l'index
		$this->indexer();
		
		$this->log->debug("Media::ajouter() Fin");
		
		return;
	}	
	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une media
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Media::enregistrer() Début");

		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVEAU_MEDIA);
		}
		
		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update tmedia 
										 set titre = ?,
										 	 type = ?,
										 	 description = ?,
										 	 source = ?,
										 	 fichier = ?,
										 	 url = ?,
										 	 suivi = ?,
										 	 liens = ?,
										 	 remarque = ?,
										 	 statut = ?,
								  		 	 date_modification = now()										
										 where id_media = ? 
										 and id_projet = ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('titre'),
									$this->get('type'),
									$this->get('description'),
									$this->get('source'),
								 	$this->get('fichier'),
								 	$this->get('url'),
									$this->get('suivi'),
									(int)$this->get('liens'),
									$this->get('remarque'),
									(int)$this->get('statut'),
									$this->get('id_media'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
								
		// Mettre à jour l'index
		$this->indexer();
										
		$this->log->debug("Media::enregistrer() Fin");
										
		return;
	}		

		

	/**
	 * 
	 * Obtenir le prefix pour le répertoire média
	 *
	 */
	public function getPrefixRepertoireMedia($idProjet) {
		
		$this->log->debug("Media::getPrefixRepertoireMedia() Début");
		
		// Obtenir les infos sur le projet
		$projet = new Projet($this->log, $this->dbh);
		$projet->getProjetParId($idProjet);

		// Déterminer le préfix 
		$prefixRepertoireMedia = Securite::nettoyerNomfichier($projet->get("repertoire")) . "/";
		
		$this->log->debug("Media::getPrefixRepertoireMedia() Fin");
		
		return $prefixRepertoireMedia;
	}


	
	/**
	 * 
	 * Transfert du fichier
	 *
	 */
	public function transfertFichier() {
		
		$this->log->debug("Media::transfertFichier() Début");
		
		$prefixRepertoire = $this->getPrefixRepertoireMedia($this->get("id_projet"));
		
		// Vérifier que le prefix n'est pas vide
		if ($prefixRepertoire == "" || $prefixRepertoire == "/") {
			Erreur::erreurFatal('210', "Media::transfertFichier() - Erreur technique détectée.  L'identifiant unique du projet '" . TXT_PREFIX_PROJET . $this->get("id_projet") . "' ne peut être vide.  Vérifier la configuration du projet.", $this->log);
		}
		
		$statut = 0;
				
		// Forcer suppression du fichier à la demande de l'utilisateur
		if ($this->get("fichier_supprimer") == "1") {
		
			// Vérifier si un fichier existait déjà, si oui, le supprimer
			$this->supprimerFichier();
				
			// Vider la valeur
			$this->set("fichier", "");
		}		
		
		// Transfert du fichier si présent
		if(isset($_FILES['media_fichier_nouveau'])) {
			
			// Obtenir les informations sur le fichier à récupérer
		    $dossier = REPERTOIRE_MEDIA . $prefixRepertoire;
		    $fichier = basename($_FILES['media_fichier_nouveau']['name']);
		     
		    if ($fichier != "") {
		    	
		     	// Vérifier si le répertoire existe, sinon le créé
				if( !file_exists($dossier) ) { 
					mkdir($dossier); 
					$this->log->debug("Media::transfertFichier() Le répertoire '$dossier' a été créé");
				} else { 
					$this->log->debug("Media::transfertFichier() Le répertoire '$dossier' existe déjà");
				}
				
				if( file_exists($dossier) ) {

					// Obtenir et vérifier l'extension et rendre sécuritaire le nom de fichier
				    $info = pathinfo($fichier);
				    $fichierExtension = strtolower($info['extension']);
				    $fichierNom =  basename($fichier,'.'.$info['extension']);
				    $fichierNom = Web::nettoyerChaineNomFichier($fichierNom);
				    
				    // Déterminer le type de média
				   	$typeFichier = $this->getTypeMedia($fichierExtension);
				    				    
				    // Vérifier que le type du fichier est permis
				    if ($typeFichier != "") {
				    
				    	// Déterminer le nom de répertoire + identifiant + nom de fichier + extension
				    	$fichier = $this->get("id_media") . "_" . $fichierNom . "." . $fichierExtension;
				    	$fichierComplet = $dossier . $fichier;
				    	$this->log->debug("Media::transfertFichier() Nouveau fichier à importer : '$fichierComplet'");
				    					
						// Vérifier si un fichier existait déjà, si oui, le supprimer
						$this->supprimerFichier();
				    	
						// Stocker le fichier
					    if(move_uploaded_file($_FILES['media_fichier_nouveau']['tmp_name'], $fichierComplet)) {
					         $this->log->debug("Media::transfertFichier() Transfert du fichier '$fichierComplet' effectué avec succès");
					         
					         // Mettre à jour l'information sur le média
					         $this->set("fichier", $fichier);
					         
					         // Type de média
					         $this->set("type", $typeFichier);

					         // Obtenir le nom de fichier pour affichage à l'utilisateur
							$this->set("fichier_usager", preg_replace('/^\d+_/', '', $this->get("fichier")) );
					         
					         // Mettre à jour le titre s'il est vide
					         if ($this->get("titre") == html_entity_decode(TXT_NOUVEAU_MEDIA, ENT_QUOTES, "UTF-8")) {
					         	$this->set("titre", $fichierNom . "." . $fichierExtension);
					         }
					    }
					    else {
					         $this->log->erreur("Media::transfertFichier() Échec du transfert du fichier '$fichier'");
					         $statut = -3;
					    }
				    } else {
				    	$statut = -2;	
				    
				    }
				} else {
					$this->log->erreur("Media::transfertFichier() Tentative de transfert d'un fichier avec un type de fichier non-supporté : '$fichierExtension'");
					$statut = -1;
				}
		    }
		}
		
		$this->log->debug("Media::transfertFichier() Fin");
		return $statut;
	}	

	
	/**
	 * 
	 * Exporter un média en format XML
	 *
	 */
	public function exporterXML() {

		$this->log->debug("Media::exporterXML() Début");
		
		// Récupérer le gabarit pour publier un média en format xml
		$contenu = Fichiers::getContenuMedia(REPERTOIRE_GABARITS_EXPORTATION . "media.php", $this);
				
		$this->log->debug("Media::exporterXML() Fin");
		
		return $contenu;
	}		
	
	/**
	 * 
	 * Exporter un média en format XML
	 * @param array Liste des médias
	 * @param string id_projet
	 *
	 */
	public function exporterListeMediasXML($listeMedias, $idProjet) {

		$this->log->debug("Media::exporterListeMediasXML() Début");
		
		$contenu = "";
		
		// Conserver seulement les ids uniques
		$listeMediasUnique = array_unique($listeMedias);
		
		foreach ($listeMediasUnique as $idMedia) {
			
			if ($idMedia != "0" && $idMedia != "") {
				$media = new Media($this->log, $this->dbh);
				$media->getMediaParId($idMedia, $idProjet);
				
				if ($media->get("id_media") != "") {
					$contenu .= $media->exporterXML();
				}
			}
		}
		
		$this->log->debug("Media::exporterListeMediasXML() Fin");
		
		return $contenu;
	}	
	
	/**
	 * 
	 * Utiliser un média via URL
	 *
	 */
	public function analyseURLMedia() {
		
		$this->log->debug("Media::AnalyseURLMedia() Début");
		
		// Obtenir le nom du fichier
		$fichier = $this->get("url");
		if ($fichier != "") {
			
			$fichierNom = "";
			$typeFichier = "";
			
			// Détecter les iframes et embeds
			if (strpos( strtolower($this->get("url")), "iframe") > 0 || 
				strpos( strtolower($this->get("url")), "embed") > 0 ||
				strpos( strtolower($this->get("url")), "youtu.be") > 0 ||
				strpos( strtolower($this->get("url")), "vimeo") > 0	) {
				$typeFichier = "video";
			} else {
			
				// Traitement de l'url
				// $this->set("url", preg_replace('#^[^:/.]*[:/]+#i', '', $this->get("url")) ); 
				
				// Déterminer le nom du fichier et l'extension
				$info = pathinfo($fichier);
				if (isset($info['extension'])) {
					$fichierExtension = strtolower($info['extension']);
					$fichierNom =  basename($fichier,'.'.$info['extension']);

					// Déterminer le type de média
		    		$typeFichier = $this->getTypeMedia($fichierExtension);
					
				} else {
					$fichierNom = $fichier;
					$typeFichier = "video";
				}
				$fichierNom = Web::nettoyerChaineNomFichier($fichierNom);
				
			}
		
			// Mettre à jour le titre s'il est vide et si le nom de fichier est disponible
			if ($this->get("titre") == TXT_NOUVEAU_MEDIA && $fichierNom != "" && $fichierExtension != "") {
				$this->set("titre", $fichierNom . "." . $fichierExtension);
			}
			
		    $this->set("type", $typeFichier);
		}
					
		$this->log->debug("Media::AnalyseURLMedia() Fin");
	}

	
	/**
	 * 
	 * Obtenir le filtre pour le type de média
	 * 
	 */
	public function getFiltreTypeMedia() {
		
		$this->log->debug("Media::getFiltreTypeMedia() Début");
		
		$session = new Session();
		
		// Obtenir le mode - fenêtre ou normal... en mode fenêtre ne pas sauvegarder le filtre en session
		$mode = Web::getMode();
		
		// Vérifier si un filtre est spécifié dans la session
		$filtreTypeMedia = "";
		if ($mode == "normal") {
			$filtreTypeMedia = $session->get("pref_filtre_type_media");
		}
		
		// Vérifier si un filtre est passé en paramètre
		$filtreTypeMediaParam = Web::getParam("filtre_type_media");
		
		// Déterminer si on utilise la valeur passé en paramètre
		if ($filtreTypeMediaParam != "") {
		
			// Si l'utilisateur veut voir tous les types de questions enlever le filtre
			if ($filtreTypeMediaParam == "tous") {
				if ($mode == "normal") {
					$session->delete("pref_filtre_type_media");
				}
				$filtreTypeMedia = "";
			} else {
			
				// Obtenir la liste des types de médias
				$listeTypesMedias = $this->getListeTypesMedias();
	
				// Vérifier si la collection demandée est disponible pour l'utilisateur
				if ($listeTypesMedias[$filtreTypeMediaParam] != "") {			
	
					// Stocker le filtre dans la session
					if ($mode == "normal") {
						$session->set("pref_filtre_type_media", $filtreTypeMediaParam);
					}
					$filtreTypeMedia = $filtreTypeMediaParam;
				}
			}
		}
		
		$this->log->debug("Media::getFiltreTypeMedia() Fin");
		
		return $filtreTypeMedia;
	}	
			
	
	/**
	 * 
	 * Obtenir la liste des types de médias
	 */
	public function getListeTypesMedias() {

		$this->log->debug("Media::getListeTypesMedias() Début");
		 		
		$listeTypesMedias = array(	'image' => TXT_IMAGE, 
									'video' => TXT_VIDEO,
									'son' => TXT_SON
								);	
								
		$this->log->debug("Media::getListeTypesMedias() Fin");
		
		return $listeTypesMedias;
	}
	
	/**
	 * 
	 * Déterminer le type de média
	 * @param String idMedia
	 * @param String idProjet
	 */
	private function getTypeMedia($fichierExtension) {
		
		$this->log->debug("Media::getTypeMedia() Début fichierExtension = '$fichierExtension'");
		
	 	$typeFichier = "";

	    if (in_array($fichierExtension, explode(",", TYPE_FICHIER_IMAGE))) {
	    	$typeFichier = "image";
	    }
	    if (in_array($fichierExtension, explode(",", TYPE_FICHIER_VIDEO))) {
	    	$typeFichier = "video";
	    }
	    if (in_array($fichierExtension, explode(",", TYPE_FICHIER_SON))) {
	    	$typeFichier = "son";
	    }
	    
		$this->log->debug("Media::getTypeMedia() Fin  typeFichier = '$typeFichier'");
		
		return $typeFichier;
	}
	
	
	/**
	 * 
	 * Charger la media à partir de la base de données
	 * @param String idMedia
	 * @param String idProjet
	 */
	public function getMediaParId($idMedia, $idProjet) {

		$this->log->debug("Media::getMediaParId() Début idMedia = '$idMedia'  idProjet = '$idProjet'");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select " . $this->listeChamps . " 
					from 
					  tmedia 
					where 
					  id_media = ? 
					  and id_projet = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idMedia, $idProjet));
			
			// Vérifier qu'on a trouvé au moins une media
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune media trouvée pour l'id '$idMedia'");
			}
			
			// Vérifier qu'une seule media est retournée, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour la media id '$idMedia' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour la media
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
		        
		        // Indiquer qu'une seule media a été trouvée
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getMediaParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Obtenir le type d'élément
		if ($this->get("type") == "image") {
			$this->set("type_txt", TXT_IMAGE);
		} elseif ($this->get("type") == "video") {
			$this->set("type_txt", TXT_VIDEO);
		} elseif ($this->get("type") == "son") {
			$this->set("type_txt", TXT_SON);
		}
		
		// Obtenir les dimensions de l'image
		if ($this->get("type") == "image" && $this->get("fichier") != "") {
			$prefixRepertoire = $this->getPrefixRepertoireMedia($this->get("id_projet"));
			$dossier = REPERTOIRE_MEDIA . $prefixRepertoire;
			$fichierComplet = $dossier . $this->get("fichier");

			$largeur = "0";
			$hauteur = "0";
			if (file_exists($fichierComplet)) {
				list($largeur, $hauteur) = getimagesize($fichierComplet);
			}
				$this->set("media_largeur", $largeur);
				$this->set("media_hauteur", $hauteur);
		}
		
		// Obtenir le nom de fichier pour affichage à l'utilisateur
		$this->set("fichier_usager", preg_replace('/^\d+_/', '', $this->get("fichier")) );
		
		// Terminé
		$this->log->debug("Media::getMediaParId() Trouve = '$trouve'");
		$this->log->debug("Media::getMediaParId() Fin");
		return $trouve;		
	}


	/**
	 * 
	 * Obtenir le nom de fichier du média
	 * 
	 */
	public function getNomFichierMedia($idMedia, $idProjet) {
		
		$this->log->debug("Media::getNomFichierMedia() Début");
	
		$nomFichier = "";
		
		if ($idMedia > 0) {
			$this->getMediaParId($idMedia, $idProjet);
			
			if ($this->get("source") == "fichier") {
				$nomFichier = $this->get("fichier");
			} 
			if ($this->get("source") == "web") {
				$nomFichier = $this->get("url");
			}
		}
		
		$this->log->debug("Media::getNomFichierMedia() Fin");
		
		return $nomFichier;
	}
			

	/**
	 * 
	 * Copier le média vers le questionnaire
	 * @param string Répertoire Destination
	 */
	public function copierFichierMedia($repertoireDestination) {
		
		$this->log->debug("Media::copierFichierMedia() Début");
	
		if ($this->get("source") != "web") {
			
			// Copier le fichier de la bibliothèque vers le questionnaire
			$fichierOrig = REPERTOIRE_MEDIA . $this->getPrefixRepertoireMedia($this->get("id_projet")) . $this->get("fichier");
			$repertoireDestMedia = $repertoireDestination . REPERTOIRE_PREFIX_MEDIAS;
			$fichierDest =  $repertoireDestMedia . $this->get("fichier");
			
			// Vérifier que le répertoire existe avant de copier le fichier
			if (! is_dir ( $repertoireDestMedia )) {
				Erreur::erreurFatal("013", "Impossible de créer le répertoire pour les médias '$repertoireDestMedia'", $this->log);
			}
			$this->log->debug("Media::copierFichierMedia() Copie du fichierOrig : '$fichierOrig' vers fichierDest : '$fichierDest'");
			
			// Cas où le nom de fichier est vide
			if ($this->get("fichier") == "") {
				$fichierOrig = IMAGE_ABSENTE_DEFAUT_CHEMIN_COMPLET;
				$fichierDest =  $repertoireDestMedia . IMAGE_ABSENTE_DEFAUT_NOM_FICHIER;
			} else if (! file_exists($fichierOrig)) {
				$fichierOrig = IMAGE_ABSENTE_DEFAUT_CHEMIN_COMPLET;
			} 

			// Copie du fichier
			copy($fichierOrig, $fichierDest);
		}
		
		$this->log->debug("Media::copierFichierMedia() Fin");
		
		return;
	}	
	
	
	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des medias
	 */
	public function getTri() {
		
		$this->log->debug("Media::getTri() Début");
		
		$session = new Session();
		
		// Obtenir le mode - fenêtre ou normal...
		$mode = Web::getMode();
				
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = "";
		$triSessionOrdre = "";
		
		if ($mode == "normal") {
			$triSessionChamp = $session->get("media_pref_tri_champ");
			$triSessionOrdre = $session->get("media_pref_tri_ordre");
		} else {
			$triSessionChamp = $session->get("fenetre_media_pref_tri_champ");
			$triSessionOrdre = $session->get("fenetre_media_pref_tri_ordre");
		}
		$this->log->debug("Media::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Media::getTri() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_media", "titre", "type", "description", "remarque", "date_modification", "suivi", "lien");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_media";
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
			$triParamChamp = "id_media";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		if ($mode == "normal") {
			$session->set("media_pref_tri_champ", $triParamChamp);
			$session->set("media_pref_tri_ordre", $triParamOrdre);
		} else {
			$session->set("fenetre_media_pref_tri_champ", $triParamChamp);
			$session->set("fenetre_media_pref_tri_ordre", $triParamOrdre);
		}
		
		$tri = "tmedia." .  $triParamChamp . " " . $triParamOrdre;
		
		$this->log->debug("Media::getTri() Fin");
		
		return $tri;
	}	
	
	
	/**
	 * 
	 * Obtenir les valeurs du média à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {

		// Obtenir les paramètres
		$params = Web::getListeParam("media_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'";
		}
		return;
	}		
	
	
	/**
	 * 
	 * Obtenir la liste des medias
	 * @param String idProjet
	 */
	public function getListeMedias($idProjet) {

		$this->log->debug("Media::getListeMedias() Début");
		$medias = array(); 
		
		// Ajouter la media null par défaut
		$medias[0] = "";
				
		try {
			$sql = "select id_media, titre from tmedia where id_projet = ? and statut != 0 order by titre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins un média	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune media trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des medias
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_media'];
	  				// $titre = htmlspecialchars(utf8_decode($row['titre']));
	  				$titre = $row['titre'];
	  				$medias[$id] = $titre;	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeMedias() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Terminé
		$this->log->debug("Media::getListeMedias() Fin");
		return $medias;		
	}

	

	/**
	 * 
	 * Obtenir la liste des medias
	 * @param String idProjet
	 * 
	 */
	public function getListeIdMedias($idProjet) {

		$this->log->debug("Media::getListeIdMedias() Début");
		$listeMedias = array(); 

		// Obtenir le tri à utiliser
		$tri = $this->getTri();

		// Obtenir le type de média (filtre)
		$filtreTypeMedia = $this->getFiltreTypeMedia();
		
		try {
			$sql = "select id_media, type from tmedia where id_projet = ? and statut != 0 order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins un média	
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::getListeIdMedias() Aucune media trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des medias
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour le type de média
					if ($filtreTypeMedia != "" && $row['type'] != $filtreTypeMedia) {
						continue;
					}	
					
	  				$id = $row['id_media'];
	  				array_push($listeMedias, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeIdMedias() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			

		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_medias", $listeMedias);
		
		// Terminé
		$this->log->debug("Media::getListeIdMedias() Fin");
		return $listeMedias;		
	}	

	
	/**
	 *
	 * Obtenir la liste des medias pour le projet
	 * @param String idProjet
	 *
	 */
	public function getListeIdMediasDuProjet($idProjet) {
	
		$this->log->debug("Media::getListeIdMediasDuProjet() Début");
		$listeMedias = array();
	
		try {
			$sql = "select id_media from tmedia where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
				
			// Vérifier qu'on a trouvé au moins un média
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::getListeIdMediasDuProjet() Aucun media trouvé pour le projet '$idProjet'");
			}
			else {
				// Récupérer les ids des medias
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
						
					$id = $row['id_media'];
					array_push($listeMedias, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeIdMediasDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Media::getListeIdMediasDuProjet() Fin");
		return $listeMedias;
	}	
	
	/**
	 * 
	 * Effectuer une recherche dans les medias seulement
	 * @param String chaine
	 * @param String idProjet
	 * @param Log log
	 * @param PDO dbh
	 */
	public function rechercheMedias($chaine, $idProjet, $log, $dbh) {
	
		$log->debug("Media::rechercheMedias() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeMedias = array(); 

		// Préparer la chaîne de recherche
		$chaine = Web::nettoyerChaineRech($chaine);
		
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		// Obtenir le type de média (filtre)
		$filtreTypeMedia = $this->getFiltreTypeMedia();

		try {
			$sql = "select 
					  	tmedia_index.id_media, type 
					from 
						tmedia_index,
						tmedia 
					where 
						tmedia_index.id_projet = ? and
						tmedia_index.texte like ? and
						tmedia.id_media = tmedia_index.id_media and
						tmedia.id_projet = tmedia_index.id_projet and
						tmedia.statut = 1 
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $chaine));
			
			// Vérifier qu'on a trouvé au moins un média	
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::rechercheMedias() Aucune media trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des medias
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour le type de média
					if ($filtreTypeMedia != "" && $row['type'] != $filtreTypeMedia) {
						continue;
					}	
									
	  				$id = $row['id_media'];
	  				array_push($listeMedias, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::rechercheMedias() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_medias", $listeMedias);
				
		// Terminé
		$log->debug("Media::rechercheMedias() Fin");		
		return $listeMedias;
	}	
		
	
	/**
	 * 
	 * Dupliquer un media  * pas utilisé pour l'instant *
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Media::dupliquer() Début");
	
		// Retirer l'id initial
		$this->set("id_media", "");
		
		// Ajouter un astérisque devant le titre
		$titre = "*" . $this->get("titre");
		$this->set("titre", $titre);
		
		// Ajouter le nouveau media
		$this->ajouter();
		
		// Effectuer une copie du média avec le nouvel identifiant
		$prefixRepertoire = $this->getPrefixRepertoireMedia($this->get("id_projet"));

		// Obtenir les informations sur les fichiers
		$dossier = REPERTOIRE_MEDIA . $prefixRepertoire;
		$fichierOrigComplet = $dossier . $this->get("fichier");

		// Déterminer le fichier de destination
		$fichierDest = $this->get("fichier");
		$fichierDest = preg_replace('/^\d+_/', '', $fichierDest);
		
		// Préfixer avec l'identifiant du nouveau média
		$fichierDest = $this->get("id_media") . "_" . $fichierDest;
		$fichierDestComplet = $dossier . $fichierDest;

		// Copier le fichier
		copy($fichierOrig, $fichierDest);
		
		$this->log->debug("Media::dupliquer() Fin");
	}	
	
	/**
	 * 
	 * Imprimer une media
	 *
	 */
	public function imprimer() {
	
		$this->log->debug("Media::imprimer() Début");
	
		// Déterminer gabarit d'impression
		$gabaritImpression = REPERTOIRE_GABARITS_IMPRESSION . "media-details.php";
		
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritImpression)) {
			$this->log->erreur("Le gabarit d'impression '$gabaritImpression' ne peut être localisé.");
		}
		
		// Obtenir le contenu pour impression
		$contenu = Fichiers::getContenuElement($gabaritImpression , $this);

		// L'ajouter à la media
		$this->set("contenu", $contenu);
		
		// Déterminer le gabarit à utiliser pour l'impression
		$this->set("gabarit_impression", IMPRESSION_GABARIT_MEDIA);

		$this->log->debug("Media::imprimer() Fin");
		
		return $contenu;
	}		
	
	/**
	 * 
	 * Désactiver la media (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
		
		$this->log->debug("Media::desactiver() Début");
		
		$this->set("statut","0");
		$this->enregistrer();
		
		$this->log->debug("Media::desactiver() Fin");
	}		

	/**
	 * 
	 * Activer le suivi d'un item
	 *
	 */
	public function activerSuivi() {
		
		$this->log->debug("Media::activerSuivi() Début");
		
		// Activer le suivi
		$this->set("suivi", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Media::activerSuivi() Fin");
	}
	
	/**
	 * 
	 * Désactiver le suivi d'un item
	 *
	 */
	public function desactiverSuivi() {
		
		$this->log->debug("Media::desactiverSuivi() Début");
		
		// Activer le suivi
		$this->set("suivi", "0");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Media::desactiverSuivi() Fin");
	}	
	
	/**
	 * 
	 * Activer la media
	 *
	 */
	public function activer() {
		
		$this->log->debug("Media::activer() Début");
		
		// Activer le suivi
		$this->set("statut", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Media::activer() Fin");
	}	

	
	/**
	 * 
	 * Supprimer le fichier media
	 */
	public function supprimerFichier() {	
	
		$this->log->debug("Media::supprimerFichier() Début");
				
		// Obtenir le préfix du répertoire média de l'utilisateur
		$prefixRepertoireMedia = $this->getPrefixRepertoireMedia($this->get("id_projet"));
		
		// Déterminer le dossier média
		$dossier = REPERTOIRE_MEDIA . $prefixRepertoireMedia;
		
		// Vérifier si un fichier existait déjà, si oui, le supprimer
		$ancienFichier = $this->get("fichier");
		$ancienFichierComplet = $dossier . $ancienFichier;

		if ( $ancienFichier != "" && file_exists($ancienFichierComplet) ) {
			$this->log->debug("Media::transfertFichier() Suppression de l'ancien fichier '$ancienFichierComplet'");
			unlink($ancienFichierComplet);
			$this->log->debug("Media::supprimerFichier() Fichier supprimé");
		}

		// Vider le nom du fichier usager
		$this->set("fichier_usager", "");
		
		$this->log->debug("Media::supprimerFichier() Fin");
	}
		
	
	/**
	 * 
	 * Supprimer un media
	 */
	public function supprimer() {
		
		$this->log->debug("Media::supprimer() Début");
								
		// Supprimer le fichier média
		$this->supprimerFichier();
		
		try {
			// Supprimer l'index existant
			$sql = "delete from tmedia_index where id_projet = ? and id_media = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_media")));
			$this->log->debug("Media: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_media = '" . $this->get("id_media") . "'");
			$this->log->debug("Media: updateIndex() Suppression complétée");			
			
			// Supprimer la media de la table
			$this->log->debug("Media::supprimer() Supprimer la media '" . $this->get("id_media") . "' de l'usager '" . $this->get("id_projet") . "'");
			$sql = "delete from tmedia where id_projet = ? and id_media= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_media")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Media::supprimer() Fin");
	}		

	
	/**
	 * 
	 * Préparer l'index de recherche
	 */
	protected function preparerIndex() {
		
		$this->log->debug("Media: preparerIndex() Début");
		
		$index = "";
		$index .= TXT_PREFIX_MEDIA . $this->get("id_media") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("remarque") . " ";

		$this->log->debug("Media: preparerIndex() Fin");
		
		return $index;
	}

	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idProjet
	 */
	protected function updateIndex($index) {
		
		$this->log->debug("Media: updateIndex() Début  index = '$index'");
		
		// Nettoyer la chaîne de recherche
		$index = utf8_encode(Web::nettoyerChaineRech($index));
		
		try {
			// Supprimer l'index existant
			$sql = "delete from tmedia_index where id_projet = ? and id_media = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_media")));
			$this->log->debug("Media: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_media = '" . $this->get("id_media") . "'");
			$this->log->debug("Media: updateIndex() Suppression complétée");
			
			// Insérer l'index
			$this->log->debug("Media: updateIndex() Ajout des données d'index pour : idProjet = '" . $this->get("id_projet") . "'  id_media = '" . $this->get("id_media") . "'");
			$sql = "insert into tmedia_index (id_projet, id_media, texte, date_creation, date_modification)
					values (?, ?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_media"), $index));
			$this->log->debug("Media: updateIndex() Ajout complété");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Media: updateIndex() Fin");
	}		
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idProjet
	 */
	public function indexer() {
		
		$this->log->debug("Media: indexer() Début");
		
		// Préparer l'index
		$index = $this->preparerIndex();
		
		// Mettre à jour l'index
		$this->updateIndex($index);
		
		$this->log->debug("Media: indexer() Fin");
	}	
	
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Media::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_media, id_projet
					FROM 	tmedia";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un média
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::reindexer()  Aucun média localisée");
			} else {
	
				// Récupérer les ids des médias et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Récupérer l'id du média
					$idProjet = $row['id_projet'];
					$idMedia = $row['id_media'];
	
					// Obtenir le média
					$m = new Media($this->log, $this->dbh);
					$m->getMediaParId($idMedia, $idProjet);
	
					// Réindexer
					$this->log->info("Media::reindexer()  Indexation pour le média '$idMedia' et projet '$idProjet'");
					$m->indexer();
					$this->log->info("Media::reindexer()  Indexation complétée pour le média '$idMedia'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Media::reindexer() Fin");
		return $nbMAJ;
	}	
	
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Media::preparerAffichageListe() Début");

		// Obtenir le mode - fenêtre ou normal... 
		$mode = Web::getMode();
				
		// Vérifier si un tri est spécifié dans la session
		$tri_champ = "";
		$tri_ordre = "";
				
		$session = new Session();

		// Préparer les classes pour le tri
		if ($mode == "normal") {
			$tri_champ = $session->get("media_pref_tri_champ");
			$tri_ordre = $session->get("media_pref_tri_ordre");
		} else {
			$tri_champ = $session->get("fenetre_media_pref_tri_champ");
			$tri_ordre = $session->get("fenetre_media_pref_tri_ordre");
		}		
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Media::preparerAffichageListe() Fin");		
		
		return;
	}	

	/**
	 * 
	 * Obtenir l'id de la media à partir de la page demandée 
	 *
	 */
	public function getIdMediaParPage($page) {

		$this->log->debug("Media::getIdMediaParPage() Début");

		$idMedia = "";
		$pageCour = $page - 1;
		
		// Obtenir la position de l'item dans les résultats
		$session = new Session();
		$listeMedias = $session->get("liste_medias");
	
		// Obtenir le nombre total de medias
		$pageTotal = count($listeMedias);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er item trouvé
			$idMedia = $listeMedias[0];		
		} else {
			$idMedia = $listeMedias[$pageCour];
		}
		
		$this->log->debug("Media::getIdMediaParPage() Fin");
			
		return $idMedia;
	}	
	
	/**
	 * 
	 * Obtenir l'id et le titre du média
	 * @param String idItem
	 * @param String idProjet
	 * @param Log log
	 * @param PDO dbh
	 * 
	 */
	static public function getMediaIdTitre($idMedia, $idProjet, $log, $dbh) {

		$log->debug("Media::getMediaIdTitre() Début idMedia = '$idMedia'  idProjet = '$idProjet'");

		// Cas ou media = 0
		if ($idMedia == 0 || $idMedia == '') {
			return "";
		}
		
		$info = "";
	
		// Récupérer le média
		$media = new Media($log, $dbh);
		$media->getMediaParId($idMedia, $idProjet);
		
		// Obtenir le titre
		$titre = $media->get("titre");

		// Si on trouve un titre l'info est valide, sinon ne rien retourner
		if ($titre != "") {
			$info = TXT_PREFIX_MEDIA . $idMedia . " - " . $titre;
		}
		
		$log->debug("Media::getMediaIdTitre() Fin");
		
		return $info;
	}

		
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Media::preparerAffichage() Début");
		
		// Select
		$this->set('source_' . $this->get('source'), HTML_CHECKED);
		
		// Obtenir la position du média dans les résultats
		$session = new Session();
		$listeMedias = $session->get("liste_medias");
		if ( is_array($listeMedias) ) { 
			$pageCour = array_search($this->get("id_media"), $listeMedias);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de media
		$pageTotal = count($listeMedias);
		
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
			
		$this->log->debug("Media::preparerAffichage() Fin");
	}	
	
	
	/**
	 * 
	 * Obtenir la liste des questionnaires qui utilisent ce média
	 * 
	 */
	public function getListeQuestionnairesUtilisantMedia() {	
	
		$this->log->debug("Media::getListeQuestionnairesUtilisantMedia() Début");
		
		$listeQuestionnaires = array();
		
		try {
			// SQL de base
			$sql = "select tquestionnaire.id_questionnaire, tquestionnaire.titre
					from tquestionnaire
					where tquestionnaire.id_projet = ?
					and tquestionnaire.statut > 0
					and ( tquestionnaire.media_image = ? or tquestionnaire.media_son = ? or tquestionnaire.media_video = ? )";
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($this->get("id_projet"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media")
										));
										
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::getListeQuestionnairesUtilisantMedia() Aucun questionnaire trouvé pour l'usager '" . $this->get("id_projet") . "' et le média '" . $this->get("id_media") . "'");
			} else {
				// Récupérer les ids des questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$info = $row['titre'] . " (" . TXT_PREFIX_QUESTIONNAIRE . $row['id_questionnaire'] . ")";
					array_push($listeQuestionnaires, $info);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeQuestionnairesUtilisantMedia() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
	
		# Ajouter au média
		$this->set("liste_liens_questionnaires", $listeQuestionnaires);
		
		$this->log->debug("Media::getListeQuestionnairesUtilisantMedia() Fin");
		
		return;
	}	
	

	/**
	 * 
	 * Obtenir la liste des items qui utilisent ce média
	 * 
	 */
	public function getListeItemsUtilisantMedia() {	
	
		$this->log->debug("Media::getListeItemsUtilisantMedia() Début");
		
		$listeItems = array();
		
		try {
			// SQL de base
			$sql = "( 
					  select titem.id_item, titem.titre
					  from titem
					  where titem.id_projet = ? 
					  and titem.statut > 0
					  and ( titem.image = ? or titem.media_image = ? or titem.media_son = ? or titem.media_video = ? or reponse_bonne_media = ? or reponse_mauvaise_media = ? or reponse_incomplete_media = ? )
					)
					union
					( 
					  select titem.id_item, titem.titre
					  from titem, titem_reponse
					  where titem.id_projet = ?
					  and titem.statut > 0 
					  and titem_reponse.id_item = titem.id_item
					  and titem_reponse.id_projet = titem.id_projet
					  and (titem_reponse.element = ? or titem_reponse.element_associe = ?)
					)
					union
					( 
					  select titem.id_item, titem.titre
					  from titem, titem_classeur
					  where titem.id_projet = ?
					  and titem.statut > 0
					  and titem_classeur.id_item = titem.id_item
					  and titem_classeur.id_projet = titem.id_projet
					  and titem_classeur.titre = ?
					)
					union
					( 
					  select titem.id_item, titem.titre
					  from titem, titem_classeur, titem_classeur_element
					  where titem.id_projet = ?
					  and titem.statut > 0
					  and titem_classeur.id_item = titem.id_item
					  and titem_classeur.id_projet = titem.id_projet
					  and titem_classeur_element.id_classeur = titem_classeur.id_classeur
					  and titem_classeur_element.id_projet = titem_classeur.id_projet
					  and titem_classeur_element.texte = ?
					)
					
					";
			
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($this->get("id_projet"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_projet"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_projet"),
										$this->get("id_media"),
										$this->get("id_projet"),
										$this->get("id_media")
										));
										
			// Vérifier qu'on a trouvé au moins un item	
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::getListeItemsUtilisantMedia() Aucun item trouvé pour l'usager '" . $this->get("id_projet") . "' et le média '" . $this->get("id_media") . "'");
			} else {
				// Récupérer les ids des items
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$info = $row['titre'] . " (" . TXT_PREFIX_ITEM . $row['id_item'] . ")";
					array_push($listeItems, $info);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeQuestionnairesUtilisantMedia() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
	
		# Ajouter au média
		$this->set("liste_liens_items", $listeItems);
		
		$this->log->debug("Media::getListeItemsUtilisantMedia() Fin");
		
		return;
	}	

	
	/**
	 * 
	 * Obtenir la liste des langues qui utilisent ce média
	 * 
	 */
	public function getListeLanguesUtilisantMedia() {	
	
		$this->log->debug("Media::getListeLanguesUtilisantMedia() Début");
		
		$listeLangues = array();
		
		try {
			// SQL de base
			$sql = "select tlangue.id_langue, tlangue.titre
					from tlangue
					where tlangue.id_projet = ? 
					and tlangue.statut > 0
					and ( tlangue.media_bonnereponse = ? or tlangue.media_mauvaisereponse = ? or tlangue.media_reponseincomplete = ? )";
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($this->get("id_projet"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media")										
										));
										
			// Vérifier qu'on a trouvé au moins un questionnaire	
			if ($sth->rowCount() == 0) {
				$this->log->info("Media::getListeLanguesUtilisantMedia() Aucun item trouvé pour l'usager '" . $this->get("id_projet") . "' et le média '" . $this->get("id_media") . "'");
			} else {
				// Récupérer les ids des langues
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$info = $row['titre'] . " (" . TXT_PREFIX_LANGUE . $row['id_langue'] . ")";
					array_push($listeLangues, $info);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::getListeLanguesUtilisantMedia() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
	
		# Ajouter au média
		$this->set("liste_liens_langues", $listeLangues);
		
		$this->log->debug("Media::getListeLanguesUtilisantMedia() Fin");
		
		return;
	}		

	
	/**
	 * 
	 * Mettre à jour le nombre de liens pour le média
	 * - Non utilisé pour l'instant et à tester en phase 2 -
	 *
	 */
	public function updateLiensMedia() {
		
		$this->log->debug("Media::updateLiensItem() Début");
		
		try {
			// SQL de base
			$sql = "select count(*)
					from tquestionnaire
					where tquestionnaire.id_projet = ? 
					and ( tquestionnaire.media_image = ? or tquestionnaire.media_son = ? or tquestionnaire.media_video = ? )";
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($this->get("id_projet"),
										$this->get("id_media"),
										$this->get("id_media"),
										$this->get("id_media")
										));			
			
			// Obtenir le nombre de liens
			$total = $sth->fetchColumn();
			$this->set("liens", $total);
	
			// Mettre à jour le nombre de liens
			$stmt = $this->dbh->prepare("update tmedia 
								  		 set liens = ?										
										 where id_media = ? 
										 and id_projet= ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $total,
									$this->get('id_media'),
									$this->get('id_projet')
									) );

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::updateLiensMedia() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}											
		
		$this->log->debug("Media::updateLiensMedia() Fin");
	}

	
	/**
	 * 
	 * Mettre à jour le nombre de liens pour le média de manière statique
	 * - Non utilisé pour l'instant et à tester en phase 2 -
	 *
	 */
		
	public static function updateLiensMediaStatic($idProjet, $idMedia, $log, $dbh) {
		$log->debug("Media::updateLiensItemStatic() Début");
		
		try {
			// SQL de base
			$sql = "select count(*)
					from tquestionnaire
					where tquestionnaire.id_projet = ? 
					and ( tquestionnaire.media_image = ? or tquestionnaire.media_son = ? or tquestionnaire.media_video = ? )";
			
			$sth = $this->dbh->prepare($sql);
			
			// Effectuer la requête
			$rows = $sth->execute(array($idProjet,
										$idMedia,
										$idMedia,
										$idMedia
										));			
			
			// Obtenir le nombre de liens
			$total = $sth->fetchColumn();
	
			// Mettre à jour le nombre de liens
			$stmt = $this->dbh->prepare("update tmedia 
								  		 set liens = ?										
										 where id_media = ? 
										 and id_projet= ?
										");
	
			// insertion d'une ligne
			$stmt->execute( array(  $total,
									$idMedia,
									$idProjet
									) );

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Media::updateLiensMediaStatic() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
		}											
		
		$log->debug("Media::updateLiensMediaStatic() Fin");
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
