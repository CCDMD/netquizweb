<?php

/** 
 * Classe Langue
 * 
 * Représente une langue, modifiable par l'utilisateur et utilisée
 * lors de la publication pour afficher les valeurs du côté client.
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Langue {
	
	protected $dbh;
	protected $log;
							  
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
	 * Sauvegarder les informations dans la base de donnes - ajout d'une langue
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Langue::ajouter() Début");
		
		// Obtenir le prochain id pour une langue
		$projet = new Projet($this->log, $this->dbh);
		$projet->getProjetParId($this->get("id_projet"));
		$idLangue = $projet->genererIdLangue();
		$this->set("id_langue", $idLangue);
		
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVELLE_LANGUE);
		}
		
		try {
			// Préparer ajout
			$stmt = $this->dbh->prepare("insert into tlangue (
										 id_langue, id_projet, titre, delimiteur, boutons_annuler, boutons_ok, consignes_association, consignes_choixmultiples, 
										 consignes_classement, consignes_damier_masquees, consignes_damier_nonmasquees, consignes_developpement, consignes_dictee_debut, 
										 consignes_dictee_majuscules, consignes_dictee_ponctuation, consignes_marquage, consignes_ordre, consignes_reponsebreve_debut, 
										 consignes_reponsebreve_majuscules, consignes_reponsebreve_ponctuation, consignes_reponsesmultiples_unereponse, 
										 consignes_reponsesmultiples_toutes, consignes_lacunaire_menu, consignes_lacunaire_glisser, consignes_lacunaire_reponsebreve_debut, 
										 consignes_lacunaire_reponsebreve_majuscules, consignes_lacunaire_reponsebreve_ponctuation, 
										 consignes_vraifaux, consignes_zones, fenetre_renseignements, fenetre_nom, fenetre_prenom, fenetre_matricule, fenetre_groupe, fenetre_courriel, 
										 fenetre_autre, fenetre_envoi, fenetre_courriel_destinataire, fonctionnalites_commencer, fonctionnalites_effacer, fonctionnalites_courriel, 
										 fonctionnalites_imprimer, fonctionnalites_recommencer, fonctionnalites_reprendre, fonctionnalites_resultats, fonctionnalites_lexique, fonctionnalites_questionnaire, 
										 fonctionnalites_solution, fonctionnalites_valider, navigation_page, navigation_de, message_bonnereponse, message_mauvaisereponse, message_reponseincomplete,
										 media_bonnereponse, media_mauvaisereponse, media_reponseincomplete,										 
										 message_point, message_points, message_sanstitre, conjonction_et, 
										 message_dictee_motsentrop, message_dictee_orthographe, message_dictee_motsmanquants, message_reponsesuggeree, resultats_afaire, 
										 resultats_areprendre, resultats_confirmation, resultats_accueil, resultats_nbessais, resultats_points, resultats_reussi, resultats_sansobjet, resultats_statut, 
										 resultats_tempsdereponse, item_association, item_choixmultiples, item_classement, item_damier, item_developpement, item_dictee, item_marquage, 
										 item_miseenordre, item_reponsebreve, item_reponsesmultiples, item_textelacunaire, item_vraioufaux, item_zonesaidentifier, remarque, 
										 message_libelle_solution, resultats_objet_courriel, resultats_message_courriel_succes, resultats_message_courriel_erreur,
										 statut, date_creation, date_modification) 
										 values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
										 		 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
										 		 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
										 		 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, now(),now() )");
	
			// Statut par défaut = brouillon
			$this->set("statut", "1");
			
			// Insertion d'un enregistrement
			$stmt->execute(array(   $this->get('id_langue'),
									$this->get('id_projet'), 
								    $this->get('titre'),
								    $this->get('delimiteur'),
									$this->get('boutons_annuler'),
									$this->get('boutons_ok'),
									$this->get('consignes_association'),
									$this->get('consignes_choixmultiples'),
									$this->get('consignes_classement'),
									$this->get('consignes_damier_masquees'),
									$this->get('consignes_damier_nonmasquees'),
									$this->get('consignes_developpement'),
									$this->get('consignes_dictee_debut'),
									$this->get('consignes_dictee_majuscules'),
									$this->get('consignes_dictee_ponctuation'),
									$this->get('consignes_marquage'),
									$this->get('consignes_ordre'),
									$this->get('consignes_reponsebreve_debut'),
									$this->get('consignes_reponsebreve_majuscules'),
									$this->get('consignes_reponsebreve_ponctuation'),
									$this->get('consignes_reponsesmultiples_unereponse'),
									$this->get('consignes_reponsesmultiples_toutes'),
									$this->get('consignes_lacunaire_menu'),
									$this->get('consignes_lacunaire_glisser'),
									$this->get('consignes_lacunaire_reponsebreve_debut'),
									$this->get('consignes_lacunaire_reponsebreve_majuscules'),
									$this->get('consignes_lacunaire_reponsebreve_ponctuation'),
									$this->get('consignes_vraifaux'),
									$this->get('consignes_zones'),
									$this->get('fenetre_renseignements'), 
									$this->get('fenetre_nom'), 
									$this->get('fenetre_prenom'), 
									$this->get('fenetre_matricule'), 
									$this->get('fenetre_groupe'), 
									$this->get('fenetre_courriel'), 
									$this->get('fenetre_autre'), 
									$this->get('fenetre_envoi'), 
									$this->get('fenetre_courriel_destinataire'), 
									$this->get('fonctionnalites_commencer'), 
									$this->get('fonctionnalites_effacer'), 
									$this->get('fonctionnalites_courriel'), 
									$this->get('fonctionnalites_imprimer'), 
									$this->get('fonctionnalites_recommencer'), 
									$this->get('fonctionnalites_reprendre'), 
									$this->get('fonctionnalites_resultats'),
									$this->get('fonctionnalites_lexique'),
									$this->get('fonctionnalites_questionnaire'),
									$this->get('fonctionnalites_solution'), 
									$this->get('fonctionnalites_valider'), 
									$this->get('navigation_page'), 
									$this->get('navigation_de'), 
									$this->get('message_bonnereponse'), 
									$this->get('message_mauvaisereponse'), 
									$this->get('message_reponseincomplete'), 
									(int)$this->get('media_bonnereponse'), 
									(int)$this->get('media_mauvaisereponse'), 
									(int)$this->get('media_reponseincomplete'),
									$this->get('message_point'),
									$this->get('message_points'),
									$this->get('message_sanstitre'),
									$this->get('conjonction_et'),  
									$this->get('message_dictee_motsentrop'), 
									$this->get('message_dictee_orthographe'), 
									$this->get('message_dictee_motsmanquants'), 
									$this->get('message_reponsesuggeree'), 
									$this->get('resultats_afaire'), 
									$this->get('resultats_areprendre'), 
									$this->get('resultats_confirmation'), 
									$this->get('resultats_accueil'),
									$this->get('resultats_nbessais'), 
									$this->get('resultats_points'), 
									$this->get('resultats_reussi'), 
									$this->get('resultats_sansobjet'), 
									$this->get('resultats_statut'), 
									$this->get('resultats_tempsdereponse'), 
									$this->get('item_association'), 
									$this->get('item_choixmultiples'), 
									$this->get('item_classement'), 
									$this->get('item_damier'), 
									$this->get('item_developpement'), 
									$this->get('item_dictee'), 
									$this->get('item_marquage'), 
									$this->get('item_miseenordre'), 
									$this->get('item_reponsebreve'), 
									$this->get('item_reponsesmultiples'), 
									$this->get('item_textelacunaire'), 
									$this->get('item_vraioufaux'),
									$this->get('item_zonesaidentifier'),								
									$this->get('remarque'),
									$this->get('message_libelle_solution'),
									$this->get('resultats_objet_courriel'),
									$this->get('resultats_message_courriel_succes'),
									$this->get('resultats_message_courriel_erreur')
								 ));
			
			$this->log->debug("Langue::ajouter() Nouvelle langue créée (id = '" . $this->get('id_langue') . "')");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Mettre à jour l'index
		$this->indexer();			
		
		// TODO : Vérifier qu'un id est retourné sinon erreur
		$this->log->debug("Langue::ajouter() Fin");
		
		return;
	}	

	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'une langue
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Langue::enregistrer() Début");

		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVELLE_LANGUE);
		}
		
		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update tlangue 
										 set titre = ?,
										 	 delimiteur = ?,
										 	 boutons_annuler = ?,
										 	 boutons_ok = ?,
										 	 consignes_association = ?, 
										 	 consignes_choixmultiples = ?, 
										 	 consignes_classement = ?, 
										 	 consignes_damier_masquees = ?, 
										 	 consignes_damier_nonmasquees = ?, 
										 	 consignes_developpement = ?, 
										 	 consignes_dictee_debut = ?, 
										 	 consignes_dictee_majuscules = ?, 
										 	 consignes_dictee_ponctuation = ?, 
										 	 consignes_marquage = ?, 
										 	 consignes_ordre = ?, 
										 	 consignes_reponsebreve_debut = ?, 
										 	 consignes_reponsebreve_majuscules = ?, 
										 	 consignes_reponsebreve_ponctuation = ?, 
										 	 consignes_reponsesmultiples_unereponse = ?, 
										 	 consignes_reponsesmultiples_toutes = ?, 
										 	 consignes_lacunaire_menu = ?, 
										 	 consignes_lacunaire_glisser = ?, 
										 	 consignes_lacunaire_reponsebreve_debut = ?, 
										 	 consignes_lacunaire_reponsebreve_majuscules = ?, 
										 	 consignes_lacunaire_reponsebreve_ponctuation = ?, 
										 	 consignes_vraifaux = ?, 
										 	 consignes_zones = ?,
											 fenetre_renseignements= ?, 
											 fenetre_nom= ?, 
											 fenetre_prenom= ?, 
											 fenetre_matricule= ?, 
											 fenetre_groupe= ?, 
											 fenetre_courriel= ?, 
											 fenetre_autre= ?, 
											 fenetre_envoi= ?, 
											 fenetre_courriel_destinataire= ?, 
											 fonctionnalites_commencer= ?, 
											 fonctionnalites_effacer= ?, 
											 fonctionnalites_courriel= ?, 
											 fonctionnalites_imprimer= ?, 
											 fonctionnalites_recommencer= ?, 
											 fonctionnalites_reprendre= ?, 
											 fonctionnalites_resultats= ?,
											 fonctionnalites_lexique= ?, 
											 fonctionnalites_questionnaire= ?,  
											 fonctionnalites_solution= ?, 
											 fonctionnalites_valider= ?, 
											 navigation_page= ?, 
											 navigation_de= ?, 
											 message_bonnereponse= ?, 
											 message_mauvaisereponse= ?, 
											 message_reponseincomplete= ?, 
											 media_bonnereponse= ?, 
											 media_mauvaisereponse= ?, 
											 media_reponseincomplete= ?,
											 message_libelle_solution= ?,
											 message_point= ?, 
											 message_points= ?,
											 message_sanstitre= ?,
											 conjonction_et= ?, 
											 message_dictee_motsentrop= ?, 
											 message_dictee_orthographe= ?, 
											 message_dictee_motsmanquants= ?, 
											 message_reponsesuggeree= ?, 
											 resultats_afaire= ?, 
											 resultats_areprendre= ?, 
											 resultats_confirmation= ?,
											 resultats_accueil= ?,  
											 resultats_nbessais= ?, 
											 resultats_points= ?, 
											 resultats_reussi= ?, 
											 resultats_sansobjet= ?, 
											 resultats_statut= ?, 
											 resultats_tempsdereponse= ?, 
											 resultats_objet_courriel= ?,
											 resultats_message_courriel_succes =?, 
											 resultats_message_courriel_erreur =?,
											 item_association= ?, 
											 item_choixmultiples= ?, 
											 item_classement= ?, 
											 item_damier= ?, 
											 item_developpement= ?, 
											 item_dictee= ?, 
											 item_marquage= ?, 
											 item_miseenordre= ?, 
											 item_reponsebreve= ?, 
											 item_reponsesmultiples= ?, 
											 item_textelacunaire= ?, 
											 item_vraioufaux= ?,
											 item_zonesaidentifier= ?,									 	 
										 	 remarque = ?,
										 	 statut = ?,
								  		 	 date_modification = now()										
										 where id_langue = ? 
										 and id_projet = ?
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('titre'),
									$this->get('delimiteur'),
									$this->get('boutons_annuler'),
									$this->get('boutons_ok'),
									$this->get('consignes_association'),
									$this->get('consignes_choixmultiples'),
									$this->get('consignes_classement'),
									$this->get('consignes_damier_masquees'),
									$this->get('consignes_damier_nonmasquees'),
									$this->get('consignes_developpement'),
									$this->get('consignes_dictee_debut'),
									$this->get('consignes_dictee_majuscules'),
									$this->get('consignes_dictee_ponctuation'),
									$this->get('consignes_marquage'),
									$this->get('consignes_ordre'),
									$this->get('consignes_reponsebreve_debut'),
									$this->get('consignes_reponsebreve_majuscules'),
									$this->get('consignes_reponsebreve_ponctuation'),
									$this->get('consignes_reponsesmultiples_unereponse'),
									$this->get('consignes_reponsesmultiples_toutes'),
									$this->get('consignes_lacunaire_menu'),
									$this->get('consignes_lacunaire_glisser'),
									$this->get('consignes_lacunaire_reponsebreve_debut'),
									$this->get('consignes_lacunaire_reponsebreve_majuscules'),
									$this->get('consignes_lacunaire_reponsebreve_ponctuation'),
									$this->get('consignes_vraifaux'),
									$this->get('consignes_zones'),
									$this->get('fenetre_renseignements'), 
									$this->get('fenetre_nom'), 
									$this->get('fenetre_prenom'), 
									$this->get('fenetre_matricule'), 
									$this->get('fenetre_groupe'), 
									$this->get('fenetre_courriel'), 
									$this->get('fenetre_autre'), 
									$this->get('fenetre_envoi'), 
									$this->get('fenetre_courriel_destinataire'), 
									$this->get('fonctionnalites_commencer'), 
									$this->get('fonctionnalites_effacer'), 
									$this->get('fonctionnalites_courriel'), 
									$this->get('fonctionnalites_imprimer'), 
									$this->get('fonctionnalites_recommencer'), 
									$this->get('fonctionnalites_reprendre'), 
									$this->get('fonctionnalites_resultats'),
									$this->get('fonctionnalites_lexique'),
									$this->get('fonctionnalites_questionnaire'),
									$this->get('fonctionnalites_solution'), 
									$this->get('fonctionnalites_valider'), 
									$this->get('navigation_page'), 
									$this->get('navigation_de'), 
									$this->get('message_bonnereponse'), 
									$this->get('message_mauvaisereponse'), 
									$this->get('message_reponseincomplete'),
									(int)$this->get('media_bonnereponse'), 
									(int)$this->get('media_mauvaisereponse'), 
									(int)$this->get('media_reponseincomplete'),
									$this->get('message_libelle_solution'),
									$this->get('message_point'),   
									$this->get('message_points'),
									$this->get('message_sanstitre'),
									$this->get('conjonction_et'),   
									$this->get('message_dictee_motsentrop'), 
									$this->get('message_dictee_orthographe'), 
									$this->get('message_dictee_motsmanquants'), 
									$this->get('message_reponsesuggeree'), 
									$this->get('resultats_afaire'), 
									$this->get('resultats_areprendre'), 
									$this->get('resultats_confirmation'),
									$this->get('resultats_accueil'),
									$this->get('resultats_nbessais'), 
									$this->get('resultats_points'), 
									$this->get('resultats_reussi'), 
									$this->get('resultats_sansobjet'), 
									$this->get('resultats_statut'), 
									$this->get('resultats_tempsdereponse'),
									$this->get('resultats_objet_courriel'),
									$this->get('resultats_message_courriel_succes'),
									$this->get('resultats_message_courriel_erreur'),
									$this->get('item_association'), 
									$this->get('item_choixmultiples'), 
									$this->get('item_classement'), 
									$this->get('item_damier'), 
									$this->get('item_developpement'), 
									$this->get('item_dictee'), 
									$this->get('item_marquage'), 
									$this->get('item_miseenordre'), 
									$this->get('item_reponsebreve'), 
									$this->get('item_reponsesmultiples'), 
									$this->get('item_textelacunaire'), 
									$this->get('item_vraioufaux'),
									$this->get('item_zonesaidentifier'),								
									$this->get('remarque'),
									(int)$this->get('statut'),
									$this->get('id_langue'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
									
		// Mettre à jour l'index
		$this->indexer();									
								
		$this->log->debug("Langue::enregistrer() Fin");
										
		return;
	}		

	
	/**
	 *
	 * Valider les champs de la langue avant publication
	 * 
	 */
	public function validerAvantPublication() {
	
		$this->log->debug("Langue::validerAvantPublication() Début");

		$totChampsVides = 0;
		
		$cles = array_keys($this->donnees);
		
		// Déterminer si certains champs sont vides et ne devraient pas l'être
		foreach ($cles as $cle) {

			// Vérifier la clé si elle n'est pas "reconnue" pour être vide 
			if ($cle != "remarque" && $cle != "media_bonnereponse_txt" && $cle != "media_mauvaisereponse_txt" && $cle != "media_reponseincomplete_txt") {
				if (trim($this->get($cle)) == "") {
					$this->log->debug("Langue::validerAvantPublication() Le champ '$cle' est vide!");
					$totChampsVides++;
				}
			}
		}
	
		$this->log->debug("Langue::validerAvantPublication() Nombre total de champ vide : '$totChampsVides'");
		
		$this->log->debug("Langue::validerAvantPublication() Fin");
		
		return ($totChampsVides == 0);
	}
	
	
	
	/**
	 * 
	 * Charger la langue à partir de la base de données
	 * @param String idLangue
	 * @param String idProjet
	 */
	public function getLangueParId($idLangue, $idProjet) {

		$this->log->debug("Langue::getLangueParId() Début idLangue = '$idLangue'  idProjet = '$idProjet'");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select * 
					from 
					  tlangue 
					where 
					  id_langue = ? 
					  and id_projet = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idLangue, $idProjet));
			
			// Vérifier qu'on a trouvé au moins une langue
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune langue trouvée pour l'id '$idLangue'");
			}
			
			// Vérifier qu'une seule langue est retournée, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour la langue id '$idLangue' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour la langue
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
		        
		        // Indiquer qu'une seule langue a été trouvée
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
			
		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Préparer la liste des médias
		$this->set("media_bonnereponse_txt", Media::getMediaIdTitre($this->get("media_bonnereponse"), $idProjet, $this->log, $this->dbh));
		$this->set("media_mauvaisereponse_txt", Media::getMediaIdTitre($this->get("media_mauvaisereponse"), $idProjet, $this->log, $this->dbh));
		$this->set("media_reponseincomplete_txt", Media::getMediaIdTitre($this->get("media_reponseincomplete"), $idProjet, $this->log, $this->dbh));
		
		// Terminer
		$this->log->debug("Langue::getLangueParId() Trouve = '$trouve'");
		$this->log->debug("Langue::getLangueParId() Fin");
		return $trouve;		
	}

	/**
	 * 
	 * Obtenir l'ordre de tri de la liste des langues
	 */
	public function getTri() {
		
		$this->log->debug("Langue::getTri() Début");
		
		$session = new Session();
		
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("langue_pref_tri_champ");
		$triSessionOrdre = $session->get("langue_pref_tri_ordre");
		$this->log->debug("Langue::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Langue::getTri() triSessionOrdre = '$triSessionOrdre'");
		
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_langue", "titre", "remarque", "date_modification");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_langue";
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
			$triParamChamp = "id_langue";
			$triParamOrdre = "asc";			
		}
		
		// Stocker le tri dans la session
		$session->set("langue_pref_tri_champ", $triParamChamp);
		$session->set("langue_pref_tri_ordre", $triParamOrdre);
		
		$this->log->debug("Langue::getTri() Fin");
		
		return $triParamChamp . " " . $triParamOrdre;
	}	
	
	
	/**
	 * 
	 * Obtenir les valeurs de la langue à partir de la requête web
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {

		// Obtenir les paramètres
		$params = Web::getListeParam("langue_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'\n";
		}
		return;
	}		
	
	
	/**
	 * 
	 * Obtenir la liste des langues
	 * @param String idProjet
	 */
	public function getListeLangues($idProjet) {

		$this->log->debug("Langue::getListeLangues() Début");
		$langues = array(); 
		
		try {
			$sql = "select id_langue, titre from tlangue where id_projet = ? and statut != 0 order by titre";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins une langue	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucune langue trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des langues
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_langue'];
	  				// $titre = htmlspecialchars(utf8_decode($row['titre']));
	  				$titre = $row['titre'];
	  				$langues[$id] = $titre;	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::getListeLangues() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Terminer
		$this->log->debug("Langue::getListeLangues() Fin");
		return $langues;		
	}

	
	/**
	 * 
	 * Obtenir la liste des langues
	 * @param String idProjet
	 * 
	 */
	public function getListeIdLangues($idProjet) {

		$this->log->debug("Langue::getListeIdLangues() Début");
		$listeLangues = array(); 

		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		try {
			$sql = "select id_langue from tlangue where id_projet = ? and statut != 0 order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins une langue	
			if ($sth->rowCount() == 0) {
				$this->log->info("Langue::getListeIdLangues() Aucune langue trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des langues
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_langue'];
	  				array_push($listeLangues, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::getListeIdLangues() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			

		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_langues", $listeLangues);
		
		// Terminer
		$this->log->debug("Langue::getListeIdLangues() Fin");
		return $listeLangues;		
	}	


	/**
	 *
	 * Obtenir la liste des langues du projet
	 * @param String idProjet
	 *
	 */
	public function getListeIdLanguesDuProjet($idProjet) {
	
		$this->log->debug("Langue::getListeIdLanguesDuProjet() Début");
		$listeLangues = array();
	
		try {
			$sql = "select id_langue from tlangue where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
				
			// Vérifier qu'on a trouvé au moins une langue
			if ($sth->rowCount() == 0) {
				$this->log->info("Langue::getListeIdLanguesDuProjet() Aucune langue trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des langues
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$id = $row['id_langue'];
					array_push($listeLangues, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::getListeIdLanguesDuProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminer
		$this->log->debug("Langue::getListeIdLanguesDuProjet() Fin");
		return $listeLangues;
	}	
	
	/**
	 * 
	 * Obtenir le libellé d'une langue
	 * 
	 */
	static public function getTitre($idLangue, $idProjet, $log, $dbh) {
		
		$log->debug("Langue: getTitre() Début  idLangue : '$idLangue'  idProjet : '$idProjet'");
		
		$titre = "";
		
		// Charger la langue
		$langue = new Langue($log, $dbh);
		$langue->getLangueParId($idLangue, $idProjet);
		
		// Retourner le titre
		$titre = $langue->get("titre");
		$log->debug("Langue: getTitre() Fin titre : '$titre'");
		
		return $titre;
	}	
	
	/**
	 * 
	 * Préparer l'index de recherche
	 * 
	 */
	protected function preparerIndex() {
		
		$this->log->debug("Langue: preparerIndex() Début");
		
		$index = "";
		$index .= TXT_PREFIX_LANGUE . $this->get("id_langue") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("remarque") . " ";

		$this->log->debug("Langue: preparerIndex() Fin");
		
		return $index;
	}

	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String index
	 * 
	 */
	protected function updateIndex($index) {
		
		$this->log->debug("Langue: updateIndex() Début  index = '$index'");
		
		// Nettoyer la chaîne de recherche
		$index = Web::nettoyerChaineRech($index);
		
		try {
			// Supprimer l'index existant
			$sql = "delete from tlangue_index where id_projet = ? and id_langue = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_langue")));
			$this->log->debug("Langue: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_langue = '" . $this->get("id_langue") . "'");
			$this->log->debug("Langue: updateIndex() Suppression complétée");
			
			// Insérer l'index
			$this->log->debug("Langue: updateIndex() Ajout des données d'index pour : idProjet = '" . $this->get("id_projet") . "'  id_langue = '" . $this->get("id_langue") . "'");
			$sql = "insert into tlangue_index (id_projet, id_langue, texte, date_creation, date_modification)
					values (?, ?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_langue"), $index));
			$this->log->debug("Langue: updateIndex() Ajout complété");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Langue: updateIndex() Fin");
	}		
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
		
		$this->log->debug("Langue: indexer() Début");
		
		// Préparer l'index
		$index = $this->preparerIndex();
		
		// Mettre à jour l'index
		$this->updateIndex($index);
		
		$this->log->debug("Langue: indexer() Fin");
	}		
		
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Langue::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_langue, id_projet
					FROM 	tlangue";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins une langue
			if ($sth->rowCount() == 0) {
				$this->log->info("Langue::reindexer()  Aucune langue localisée");
			} else {
	
				// Récupérer les ids des langues et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Récupérer l'id de la langue
					$idProjet = $row['id_projet'];
					$idLangue = $row['id_langue'];
	
					// Obtenir la langue
					$l = new Langue($this->log, $this->dbh);
					$l->getLangueParId($idLangue, $idProjet);
						
					// Réindexer
					$this->log->info("Langue::reindexer()  Indexation pour la langue '$idLangue' et projet '$idProjet'");
					$l->indexer();
					$this->log->info("Langue::reindexer()  Indexation complétée pour la langue '$idLangue'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Langue::reindexer() Fin");
		return $nbMAJ;
	}	
	
	
	/**
	 * 
	 * Effectuer une recherche dans les langues seulement
	 * @param String chaine
	 * @param String idProjet
	 * @param Log log
	 * @param PDO dbh
	 */
	public function rechercheLangues($chaine, $idProjet, $log, $dbh) {
	
		$log->debug("Langue::rechercheLangues() Début chaine = '$chaine'  idProjet = '$idProjet'");

		$listeLangues = array(); 

		// Préparer la chaîne de recherche
		$chaine = Web::nettoyerChaineRech($chaine);
		
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
		
		try {
			$sql = "select 
					  	tlangue_index.id_langue, tlangue.date_modification as date_modification 
					from 
						tlangue_index,
						tlangue 
					where 
						tlangue_index.id_projet = ? and
						tlangue_index.texte like ? and
						tlangue.id_langue = tlangue_index.id_langue and
						tlangue.id_projet = tlangue_index.id_projet
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet, $chaine));
			
			// Vérifier qu'on a trouvé au moins une langue	
			if ($sth->rowCount() == 0) {
				$this->log->info("Langue::getListeIdLangues() Aucune langue trouvée pour l'usager '$idProjet'");
			}
			else {
				// Récupérer les ids des langues
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	  				$id = $row['id_langue'];
	  				array_push($listeLangues, $id);	
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::rechercheLangues() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_langues", $listeLangues);
				
		// Terminer
		$log->debug("Langue::rechercheLangues() Fin");		
		return $listeLangues;
	}		
		
	/**
	 * 
	 * Dupliquer la langue
	 *
	 */
	public function dupliquer() {

		$this->log->debug("Langue::dupliquer() Début");
	
		// Retirer l'id initial
		$this->set("id_langue", "");
		
		// Ajouter un astérisque devant le titre
		$titre = "*" . $this->get("titre");
		$this->set("titre", $titre);

		// Ajouter la nouvelle langue
		$this->ajouter();
		
		$this->log->debug("Langue::dupliquer() Fin");
	}	
	
	/**
	 * 
	 * Imprimer une langue
	 *
	 */
	public function imprimer() {
	
		$this->log->debug("Langue::imprimer() Début");
	
		// Déterminer gabarit d'impression
		$gabaritImpression = REPERTOIRE_GABARITS_IMPRESSION . "langue-details.php";
		
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritImpression)) {
			$this->log->erreur("Le gabarit d'impression '$gabaritImpression' ne peut étre localisé.");
		}
		
		// Obtenir le contenu pour impression
		$contenu = Fichiers::getContenuElement($gabaritImpression , $this);

		// L'ajouter à la langue
		$this->set("contenu", $contenu);
		
		// Déterminer le gabarit à utiliser pour l'impression
		$this->set("gabarit_impression", IMPRESSION_GABARIT_LANGUE);

		$this->log->debug("Langue::imprimer() Fin");
		
		return $contenu;
	}		
	
	/**
	 * 
	 * Désactiver la langue (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
		
		$this->log->debug("Langue::desactiver() Début");
		
		$confirmation = 0;
		
		// Me pas mettre à la corbeille les langues par défaut
		$listeLanguesProtegees = explode(",", LISTE_LANGUES_PROTEGEES);
		if (!in_array($this->get("id_langue"), $listeLanguesProtegees)) {
			$this->set("statut","0");
			$this->enregistrer();
			$confirmation = 1;
		}
		
		$this->log->debug("Langue::desactiver() Fin");
		return $confirmation;
	}		
	

	/**
	 * 
	 * Activer la langue
	 *
	 */
	public function activer() {
		
		$this->log->debug("Langue::activer() Début");
		
		// Activer le suivi
		$this->set("statut", "1");
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Langue::activer() Fin");
	}	

	/**
	 * 
	 * Supprimer une langue
	 */
	public function supprimer() {
		
		$this->log->debug("Langue::supprimer() Début");
	
		try {
			// Supprimer l'index existant
			$sql = "delete from tlangue_index where id_projet = ? and id_langue = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_langue")));
			$this->log->debug("Langue: updateIndex() Suppression des données d'index pour : id_projet = '" . $this->get("id_projet") . "'  id_langue = '" . $this->get("id_langue") . "'");
			$this->log->debug("Langue: updateIndex() Suppression complétée");
			
			// Supprimer la langue de la table
			$this->log->debug("Langue::supprimer() Supprimer la langue '" . $this->get("id_langue") . "' de l'usager '" . $this->get("id_projet") . "'");
			$sql = "delete from tlangue where id_projet = ? and id_langue= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $this->get("id_langue")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Langue::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("Langue::supprimer() Fin");
	}		
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Langue::preparerAffichageListe() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("langue_pref_tri_champ");
		$tri_ordre = $session->get("langue_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Langue::preparerAffichageListe() Fin");		
		
		return;
	}	

	/**
	 * 
	 * Obtenir l'id de la langue à partir de la page demandée 
	 *
	 */
	public function getIdLangueParPage($page) {

		$this->log->debug("Langue::getIdLangueParPage() Début");

		$idLangue = "";
		$pageCour = $page - 1;
		
		// Obtenir la position de l'item dans les résultats
		$session = new Session();
		$listeLangues = $session->get("liste_langues");
	
		// Obtenir le nombre total de langues
		$pageTotal = count($listeLangues);

		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er item trouvé
			$idLangue = $listeLangues[0];		
		} else {
			$idLangue = $listeLangues[$pageCour];
		}
		
		$this->log->debug("Langue::getIdLangueParPage() Fin");
			
		return $idLangue;
	}	
	

	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Langue::preparerAffichage() Début");
		
		// Select
		$this->set('delimiteur_' . $this->get('delimiteur'), HTML_SELECTED);
		
		// Obtenir la position de la langue
		$session = new Session();
		$listeLangues = $session->get("liste_langues");
		if ( is_array($listeLangues) ) { 
			$pageCour = array_search($this->get("id_langue"), $listeLangues);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de langue
		$pageTotal = count($listeLangues);
		
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
			
		$this->log->debug("Langue::preparerAffichage() Fin");
	}	
		
	
	/**
	 *
	 * Préparer les données pour publication
	 * @param string Répertoire Destination
	 *
	 */
	public function preparerPublication($repertoireDestination) {
	
		$this->log->debug("Langue::preparerPublication() Début");	
	
		// --------------------------------------------------------
		// Bonne réponse - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierBonneReponse = "";
		if ($this->get("media_bonnereponse") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierBonneReponse = $media->getNomFichierMedia($this->get("media_bonnereponse"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
		
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_bonnereponse_fichier_source", $source);
		}
		$this->set("media_bonnereponse_fichier", $fichierBonneReponse);
		
		// --------------------------------------------------------
		// Mauvaise réponse - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierMauvaiseReponse = "";
		if ($this->get("media_mauvaisereponse") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierMauvaiseReponse = $media->getNomFichierMedia($this->get("media_mauvaisereponse"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
		
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_mauvaisereponse_fichier_source", $source);
		
		}
		$this->set("media_mauvaisereponse_fichier", $fichierMauvaiseReponse);
		
		// --------------------------------------------------------
		// Réponse incomplète - Obtenir le nom et copier le fichier
		// --------------------------------------------------------
		$fichierReponseIncomplete = "";
		if ($this->get("media_reponseincomplete") > 0) {
			$media = new Media($this->log, $this->dbh);
			$fichierReponseIncomplete = $media->getNomFichierMedia($this->get("media_reponseincomplete"), $this->get("id_projet"));
			$media->copierFichierMedia($repertoireDestination);
		
			// Déterminer si le fichier est local ou web
			$source = 1; // local par défaut
			if ($media->get("source") == "web") {
				$source = 2;
			}
			$this->set("media_reponseincomplete_fichier_source", $source);
		}
		$this->set("media_reponseincomplete_fichier", $fichierReponseIncomplete);	
		
		$this->log->debug("Langue::preparerPublication() Fin");
	}
	
	
	
	/**
	 *
	 * Exporter une langue en format XML
	 * @param Projet projet
	 * @param Usager usager
	 * @param array Liste de langues
	 *
	 */
	public function exporterListeLanguesXML($projet, $usager, $listeLangues) {
	
		$this->log->debug("Langue::exporterXML() Début");
	
		$succes = 0;
		$urlFichierZip = "";
		$contenu = "";
		$listeMedias = array();
	
		// Déterminer le nom du fichier zip
		$ts = date( "Y-m-d_H-i-s" );
		$nomBase = FICHIER_EXPORTATION_XML_LANGUES;
	
		// Vérifier le nombre de langues à exporter et ajouter un "s" au besoin
		if (count($listeLangues) > 1) {
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
	
			$this->log->debug("Langue() Exportation d'une langue");
	
			// Exporter le Langue
			$publication = new Publication($this->log, $this->dbh);
			$succes = $publication->preparerRepertoire($repertoireDestinationUsager, $repertoireDestination);
			if ($succes) {
				// Créer le répertoire média
				$succes = $publication->creerRepertoireMedia($repertoireDestination);
			}			
	
			// Entête XML
			$contenu .= XML_ENTETE . "\n";
			$contenu .= XML_NQW_DEBUT . "\n";
	
			// Obtenir la liste des Langues en XML
			foreach ($listeLangues as $idLangue) {
				$l = new Langue($this->log, $this->dbh);
				$l->getLangueParId($idLangue, $projet->get("id_projet"));
	
				// Obtenir le contenu XML
				$contenu .= $l->exporterXML($repertoireDestination);
			}
	
			// Fin du fichier XML
			$contenu .= XML_NQW_FIN . "\n";
	
			// Écrire le contenu dans un fichier XML
			$publication->ecrireFichier($repertoireDestination . FICHIER_EXPORTATION_XML, $contenu);
	
		} else {
			$this->log->debug("Langue::exporterXML() Impossible de publier le Langue - le répertoire existe déjà");
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
			$this->log->debug("Langue::exporterXML() Suppression du répertoire '$repertoireSourceZip'");
			Fichiers::rmdirr($repertoireSourceZip);
		} else {
			$urlFichierZip = "";
		}
	
		$this->log->debug("Langue::exporterXML() Fin");
		return $urlFichierZip;
	}	
	
	
	/**
	 *
	 * Exporter une langue en format XML
	 * @param string répertoire destination
	 *
	 */
	public function exporterXML($repertoireDestination) {
	
		$this->log->debug("Langue::exporterXML() Début");
	
		// Préparer l'information pour la publication
		$this->preparerPublication($repertoireDestination);
	
		// Récupérer le gabarit pour publier un terme
		$contenu = Fichiers::getContenuLangue(REPERTOIRE_GABARITS_EXPORTATION . "langue.php", $this);
	
		$this->log->debug("Langue::exporterXML() Fin");
	
		return $contenu;
	}	
	
	/**
	 * 
	 * Obtenir une valeur
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
	 */
	public function set( $libelle, $valeur ) {
		$this->donnees[$libelle] = $valeur;
	}

	/**
	 * 
	 * Obtenir une valeur pour impression
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
	 */
	public function getJS( $valeur ) {
		return Web::nettoyerChainePourJs($this->get($valeur));
	}
	
	
	/**
	 *
	 * Obtenir une valeur pour du XML
	 */
	public function getXML( $valeur ) {
		return Web::nettoyerChainePourXML($this->get($valeur));
	}	
	
}

?>
