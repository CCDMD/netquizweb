<?php

/** 
 * Classe Terme
 * 
 * Représente un terme du lexique
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Terme {
	
	protected $dbh;
	protected $log;
	
	protected $listeChamps = "id_terme, id_projet, terme, variantes, type_definition, texte, url, media_image, media_son, media_video, remarque, statut, date_creation, date_modification";
							  
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
	 * Sauvegarder les informations dans la base de données - ajout d'un terme
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Terme::ajouter() Début");
		
		try {
			// Obtenir le prochain id pour un terme
			$projet = new Projet($this->log, $this->dbh);
			$projet->getProjetParId($this->get("id_projet"));
			$idTerme = $projet->genererIdTerme();
			$this->set("id_terme", $idTerme);
			
			// Vérifier le terme : s'il est vide, utiliser la valeur par défaut
			if ( trim($this->get("terme")) == "") {
				$this->set("titre", TXT_NOUVEAU_TERME);
			}
			
			// Préparer ajout
			$stmt = $this->dbh->prepare("insert into tterme (id_terme, id_projet, terme, variantes, type_definition, texte, url, media_image, media_son, media_video, remarque, statut, date_creation, date_modification) 
										 values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, now(),now() )");
	
			// Statut par défaut = brouillon
			$this->set("statut", "1");
			
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_terme'),
								 $this->get('id_projet'), 
								 $this->get('terme'),
								 $this->get('variantes'),
								 $this->get('type_definition'),
								 $this->get('texte'),
								 $this->get('url'),
								 $this->get('media_image'),
								 $this->get('media_son'),
								 $this->get('media_video'),
								 $this->get('remarque'),
								 ));
			
			$this->log->debug("Terme::ajouter() Nouveau terme créée (id = '" . $this->get('id_terme') . "')");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Terme::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Terme::ajouter() Fin");
		
		return;
	}	

	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un terme
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Terme::enregistrer() Début");

				// Vérifier le terme : s'il est vide, utiliser la valeur par défaut
			if ( trim($this->get("terme")) == "") {
				$this->set("titre", TXT_NOUVEAU_TERME);
			}
		
		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update tterme
										 set terme = ?,
											 variantes = ?,
											 type_definition = ?,						 
											 texte = ?,
											 url = ?,
											 media_image = ?,
											 media_son = ?,
											 media_video = ?,
										 	 remarque = ?,
										 	 statut = ?,
								  		 	 date_modification = now()										
										 where id_terme = ? 
										 and id_projet = ?
											");
	
			// MAJ d'une ligne
			$stmt->execute( array(   $this->get('terme'),
									 $this->get('variantes'),
									 $this->get('type_definition'),
									 $this->get('texte'),
									 $this->get('url'),
									 $this->get('media_image'),
									 $this->get('media_son'),
									 $this->get('media_video'),
									 $this->get('remarque'),
									 $this->get('statut'),
									 $this->get('id_terme'),
									 $this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Terme::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
										
		$this->log->debug("Terme::enregistrer() Fin");
										
		return;
	}		

	/**
	 * 
	 * Charger le terme à partir de la base de données
	 * 
	 * @param String idTerme
	 * @param String idProjet
	 * 
	 */
	public function getTermeParId($idTerme, $idProjet) {

		$this->log->debug("Terme::getTermeParId() Début idTerme = '$idTerme'  idProjet = '$idProjet'");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select " . $this->listeChamps . " 
					from 
					  tterme 
					where 
					  id_terme = ? 
					  and id_projet = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idTerme, $idProjet));
			
			// Vérifier qu'on a trouvé au moins un terme
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun terme trouvé pour l'id '$idTerme'");
			}
			
			// Vérifier qu'un seul terme est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour le terme id '$idTerme' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour le terme
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
		        
		        // Indiquer qu'un seul terme a été trouvée
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Terme::getTermeParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Préparer le terme pour le menu
		$titreMenu = Web::tronquer($this->get("terme"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Préparer la liste des médias
		$this->set("media_image_txt", Media::getMediaIdTitre($this->get("media_image"), $this->get("id_projet"), $this->log, $this->dbh));
		$this->set("media_son_txt", Media::getMediaIdTitre($this->get("media_son"), $this->get("id_projet"), $this->log, $this->dbh));
		$this->set("media_video_txt", Media::getMediaIdTitre($this->get("media_video"), $this->get("id_projet"), $this->log, $this->dbh));
		
		// Terminé
		$this->log->debug("Terme::getTermeParId() Trouve = '$trouve'");
		$this->log->debug("Terme::getTermeParId() Fin");
		return $trouve;		
	}

	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des termes
	 * 
	 */
	public function getTri() {
		
		$this->log->debug("Terme::getTri() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("terme_pref_tri_champ");
		$triSessionOrdre = $session->get("terme_pref_tri_ordre");
		$this->log->debug("Terme::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Terme::getTri() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_terme", "terme", "variantes", "remarque", "date_modification");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_terme";
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
			$triParamChamp = "id_terme";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		$session->set("terme_pref_tri_champ", $triParamChamp);
		$session->set("terme_pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Terme::getTri() Fin");
		
		return $triParamChamp . " " . $triParamOrdre;
	}	
	
	
	/**
	 * 
	 * Obtenir les valeurs du terme à partir de la requête web
	 * 
	 */
	public function getDonneesRequete() {

		$this->log->debug("Terme::getDonneesRequete() Début");
		
		// Obtenir les paramètres
		$params = Web::getListeParam("terme_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'";
		}
		
		// Traitement spécial pour les variantes... une chaîne par ligne, pas d'espace avant ou après
		$listeVariantes = explode("\n", $this->get("variantes"));
		
		// Pour chaque variante, enlever les espaces avant/après la chaîne et ajouter à une nouvelle chaîne
		$variantes = "";
		foreach ($listeVariantes as $var) {
			if ($variantes != "") {
				$variantes .= "\n";
			}
			$variantes .= trim($var);
		}
		
		// Conserver les nouvelles valeurs
		$this->set("variantes", $variantes);
		
		$this->log->debug("Terme::getDonneesRequete() Fin");
		return;
	}		
		

	/**
	 *
	 * Obtenir les variantes comme liste séparées par des virgules
	 * 
	 */
	public function getListeVariantes() {
	
		$this->log->debug("Terme::getListeVariantes() Début");

		$variantes = "";
		
		// Obtenir la liste des variantes
		$listeVariantes = explode("\n", $this->get("variantes"));
		
		// Produire une nouvelle liste séparées par des virgules
		foreach ($listeVariantes as $var) {
			if ($variantes != "") {
				$variantes .= ", ";
			}
			$variantes .= trim($var);
		}
		
		$this->log->debug("Terme::getListeVariantes() Fin variantes = '$variantes'");
		
		return $variantes;
	}
	
	
	/**
	 *
	 * Obtenir la définition pour affichage dans la liste
	 *
	 */
	public function getDefinitionPourAffichage() {
	
		$this->log->debug("Terme::getDefinitionPourAffichage() Début");
	
		$definition = "";

		if ($this->get("type_definition") == "texte") {
			$definition = $this->get("texte");
		} elseif ($this->get("type_definition") == "url") {
			$definition = $this->get("url");
		} elseif ($this->get("type_definition") == "media_image") {
			$definition = $this->get("media_image");
		} elseif ($this->get("type_definition") == "media_son") {
			$definition = $this->get("media_son");
		} elseif ($this->get("type_definition") == "media_video") {
			$definition = $this->get("media_video");					
		}
		
		$this->log->debug("Terme::getDefinitionPourAffichage() Fin definition = '$definition'");
	
		return $definition;
	}
	
	
	/**
	 *
	 * Obtenir la liste des termes du projet
	 * 
	 * @param String idProjet
	 * @param String tri
	 *
	 */
	public function getListeIdTermesDuProjet($idProjet, $tri) {
	
		$this->log->debug("Terme::getListeIdTermesDuProjet() Début");
		$listeTermes = array();
	
		try {
			$sql = "select id_terme from tterme where id_projet = ? order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
				
			// Vérifier qu'on a trouvé au moins un terme
			if ($sth->rowCount() == 0) {
				$this->log->info("Terme::getListeIdTermesDuProjet() Aucun terme trouvé pour le projet '$idProjet'");
			}
			else {
				// Récupérer les ids des termes
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_terme'];
					array_push($listeTermes, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Terme::getListeIdTermesDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_termes", $listeTermes);
	
		// Terminé
		$this->log->debug("Terme::getListeIdTermesDuProjet() Fin");
		return $listeTermes;
	}	

	
	/**
	 * 
	 * Dupliquer le terme
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Terme::dupliquer() Début");
	
		// Retirer l'id initial
		$this->set("id_terme", "");
		
		// Ajouter un astérisque devant le terme
		$terme = "*" . $this->get("terme");
		$this->set("terme", $terme);
		
		// Ajouter le nouveau terme
		$this->ajouter();
		
		$this->log->debug("Terme::dupliquer() Fin");
	}	
	
	/**
	 * 
	 * Supprimer un terme
	 * 
	 */
	public function supprimer() {
		
		$this->log->debug("Terme::supprimer() Début");
	
		try {
			// Supprimer le terme des questionnaires
			$this->log->debug("Terme::supprimer() Supprimer le terme '" . $this->get("id_terme") . "' des questionnaires du projet '" . $this->get("id_projet") . "'");
			$sql = "delete from rprojet_questionnaire_terme where id_projet = ? and id_terme= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_terme")));
			
			// Supprimer le terme de la table
			$this->log->debug("Terme::supprimer() Supprimer le terme '" . $this->get("id_terme") . "' du projet '" . $this->get("id_projet") . "'");
			$sql = "delete from tterme where id_projet = ? and id_terme= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_terme")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Terme::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Terme::supprimer() Fin");
	}		

	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Terme::preparerAffichageListe() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("terme_pref_tri_champ");
		$tri_ordre = $session->get("terme_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}
		
		$this->log->debug("Terme::preparerAffichageListe() Fin");		
		
		return;
	}	

	/**
	 * 
	 * Obtenir l'id du terme à partir de la page demandée
	 * 
	 *  @param String $page
	 *
	 */
	public function getIdTermeParPage($page) {

		$this->log->debug("Terme::getIdTermeParPage() Début");

		$idTerme = "";
		$pageCour = $page - 1;
		
		// Obtenir la position de l'item dans les résultats
		$session = new Session();
		$listeTermes = $session->get("liste_termes");
	
		// Obtenir le nombre total de termes
		$pageTotal = count($listeTermes);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er terme trouvé
			$idTerme = $listeTermes[0];		
		} else {
			$idTerme = $listeTermes[$pageCour];
		}
		
		$this->log->debug("Terme::getIdTermeParPage() Fin");
		
		return $idTerme;
	}	
	

	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Terme::preparerAffichage() Début");
		
		// Obtenir la position du terme dans les résultats
		$session = new Session();
		$listeTermes = $session->get("liste_termes");
		if ( is_array($listeTermes) ) { 
			$pageCour = array_search($this->get("id_terme"), $listeTermes);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de termes
		$pageTotal = count($listeTermes);
		
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
			
		// Préparer la liste des médias
		$this->set("media_image_txt", Media::getMediaIdTitre($this->get("media_image"), $this->get("id_projet"), $this->log, $this->dbh));
		$this->set("media_son_txt", Media::getMediaIdTitre($this->get("media_son"), $this->get("id_projet"), $this->log, $this->dbh));
		$this->set("media_video_txt", Media::getMediaIdTitre($this->get("media_video"), $this->get("id_projet"), $this->log, $this->dbh));
		
		$this->log->debug("Terme::preparerAffichage() Fin");
	}	
	
	
	/**
	 *
	 * Exporter un terme en format XML
	 * 
	 * @param string répertoire destination
	 *
	 */
	public function exporterXML($repertoireDestination) {
	
		$this->log->debug("Terme::exporterXML() Début");
	
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination);
		
		// Récupérer le gabarit pour publier un terme
		$contenu = Fichiers::getContenuTerme(REPERTOIRE_GABARITS_EXPORTATION . "terme.php", $this);
	
		$this->log->debug("ItemAssociations::exporterXML() Fin");
	
		return $contenu;
	}

	
	/**
	 *
	 * Exporter un terme en format XML
	 * @param Projet projet
	 * @param Usager usager
	 * @param array Liste de termes
	 *
	 */
	public function exporterListeTermesXML($projet, $usager, $listeTermes) {
	
		$this->log->debug("Terme::exporterXML() Début");
	
		$succes = 0;
		$urlFichierZip = "";
		$contenu = "";
		$listeMedias = array();
	
		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomBase = FICHIER_EXPORTATION_XML_TERMES;
	
		// Vérifier le nombre de termes à exporter et ajouter un "s" au besoin
		if (count($listeTermes) > 1) {
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
				
			$this->log->debug("Terme::exporterXML() Exportation du terme");
	
			// Exporter le terme
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes) {
				// Créer le répertoire média
				$succes = $publication->creerRepertoireMedia($repertoireDestination);
			}
	
			// Entête XML
			$contenu .= XML_ENTETE . "\n";
			$contenu .= XML_NQW_DEBUT . "\n";
				
			// Obtenir la liste des termes en XML
			foreach ($listeTermes as $idTerme) {
				$t = new Terme($this->log, $this->dbh);
				$t->getTermeParId($idTerme, $projet->get("id_projet"));
	
				// Obtenir le contenu XML
				$contenu .= $t->exporterXML($repertoireDestination);
	
				// Obtenir la liste des médias
				$listeMedias = $t->ajouterMediaListeExportation($listeMedias);
			}
	
			// Préparer la listes des médias
			$media = new Media($this->log, $this->dbh);
			$contenuMedia = $media->exporterListeMediasXML($listeMedias, $projet->get("id_projet"));

			// Ajouter les médias
			$contenu .= $contenuMedia;
				
			// Fin du fichier XML
			$contenu .= XML_NQW_FIN . "\n";
				
			// Écrire le contenu dans un fichier XML
			$publication->ecrireFichier($repertoireDestination . FICHIER_EXPORTATION_XML, $contenu);
				
		} else {
			$this->log->debug("Terme::exporterXML() Impossible de publier le terme - le répertoire existe déjà");
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
			$this->log->debug("Terme::exporterXML() Suppression du répertoire '$repertoireSourceZip'");
			Fichiers::rmdirr($repertoireSourceZip);
		} else {
			$urlFichierZip = "";
		}
	
		$this->log->debug("Terme::exporterXML() Fin");
		return $urlFichierZip;
	}	
	

	/**
	 *
	 * ajouterMediaListeExportation()
	 * Ajouter les médias d'un terme à la liste d'exportation
	 * 
	 * @param array Liste médias à exporter
	 *
	 */
	public function ajouterMediaListeExportation($listeMedias) {
	
		$this->log->debug("Terme::ajouterMediaListeExportation() Début");
	
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
	
		// Ajouter les médias qui sont dans les zones de texte (rich text)
		$cles = array_keys($this->donnees);
			
		// Parcourir la liste de clés
		foreach ($cles as $cle) {
			$contenu = $this->get($cle);
			$matches = array();
	
			// Effectuer la recherche
			preg_match_all("/\[M(\d+?)]/i", $contenu, $matches, PREG_SET_ORDER);
				
			foreach ($matches as $val) {
	
				// Obtenir le média trouvé
				$idMedia = $val[1];
	
				// L'ajouter à la liste
				if ($idMedia != "" && $idMedia != "0") {
					array_push($listeMedias, $idMedia);
				}
			}
		}
	
		$this->log->debug("Terme::ajouterMediaListeExportation() Fin");
	
		return $listeMedias;
	}
	
	
	/**
	 *
	 * Exporter la relation entre le questionnaire et le terme
	 * 
	 * @param String répertoire destination
	 *
	 */
	public function exporterXMLQuestTerme($repertoireDestination) {
	
		// Récupérer le gabarit pour publier le XML
		$contenu = Fichiers::getContenuTerme(REPERTOIRE_GABARITS_EXPORTATION . "questionnaire-terme.php", $this);
	
		return $contenu;
	}	
	
	
	/**
	 *
	 * Préparer les données pour publication
	 * 
	 * @param String Répertoire Destination
	 *
	 */
	public function preparerPublication($repertoireDestination) {
	
		$this->log->debug("Terme::preparerPublication() Début");
		
		$variantes = "";
		
		// --------------------------------------------------------
		// Obtenir la liste des variantes
		// --------------------------------------------------------
		$listeVariantes = explode("\n", $this->get("variantes"));
		
		// Produire une nouvelle liste séparées par des virgules
		foreach ($listeVariantes as $var) {
			if ($variantes != "") {
				$variantes .= ", ";
			}
			$variantes .= trim($var);
		}
		$this->set("liste_variantes", $variantes);

		// --------------------------------------------------------
		// Fichier image - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierImage = "";
		if ($this->get("media_image") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierImage = $media->getNomFichierMedia($this->get("media_image"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
				
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_image_fichier_source", $source);
		}
		$this->set("media_image_fichier", $fichierImage);
		
		// --------------------------------------------------------
		// Fichier son - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierSon = "";
		if ($this->get("media_son") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierSon = $media->getNomFichierMedia($this->get("media_son"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
		
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_son_fichier_source", $source);
				
		}
		$this->set("media_son_fichier", $fichierSon);
		
		// --------------------------------------------------------
		// Fichier video - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierVideo = "";
		if ($this->get("media_video") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierVideo = $media->getNomFichierMedia($this->get("media_video"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
				
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_video_fichier_source", $source);
		}
		$this->set("media_video_fichier", $fichierVideo);		

		// --------------------------------------------------------
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
		
		
		$this->log->debug("Terme::preparerPublication() Fin");
		
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
	 * Obtenir une valeur pour du XML
	 * @param String $valeur
	 * 
	 */
	public function getXML( $valeur ) {
		return Web::nettoyerChainePourXML($this->get($valeur));
	}
	
	
}

?>
