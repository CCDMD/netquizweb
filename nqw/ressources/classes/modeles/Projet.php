<?php

/** 
 * Classe Projet
 * 
 * Un projet regroupe les questionnaires, items, médias, Projets, catégories, médias, ...
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Projet {
	
	
	const ROLE_RESPONSABLE = "1";
	const ROLE_COLLABORATEUR = "2";
	
	const STATUT_INACTIF = "0";
	const STATUT_ACTIF = "1";
	const STATUT_SUPPRIME = "2";
	const STATUT_VERROUILLE = "3";
	
	protected $dbh;
	protected $log;
	
	protected $listeChamps = "id_projet, titre, description, repertoire, notification, statut, date_modification, date_creation";
							  
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
	 * Sauvegarder les informations dans la base de données - ajout d'un projet
	 * 
	 */
	public function ajouter() {

		$this->log->debug("Projet::ajouter() Début");
		
		try {
		// Préparer ajout
			$stmt = $this->dbh->prepare("insert into tprojet (titre, description, repertoire, notification, dernier_id_langue, statut, date_creation, date_modification) 
										 values (?, ?, ?, ?, ?, ?, now(),now() )");
	
			// Statut par défaut = brouillon
			$this->set("statut", "1");
			
			// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
			if ( trim($this->get("titre")) == "") {
				$this->set("titre", TXT_NOUVEAU_PROJET);
			}
			
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('titre'),
								 $this->get('description'),
								 $this->get('repertoire'),
								 $this->get('notification'),
								 NB_LANGUES_PAR_DEFAUT,
								 $this->get('statut')
								 ));
			
			// Obtenir l'ID
			$this->donnees['id_projet'] = $this->dbh->lastInsertId('id_projet');
			$this->log->debug("Projet::ajouter() Nouveau projet créé (id = '" . $this->get('id_projet') . "')");
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Mettre à jour l'index
		$this->indexer();
		
		// Ajouter les langues par défaut
		$this->ajouterLanguesParDefaut();
		
		$this->log->debug("Projet::ajouter() Fin");
		
		return;
	}	

	
	/**
	 *
	 * Ajouter les langues par défaut
	 *
	 */
	public function ajouterLanguesParDefaut() {
	
		$this->log->debug("Projet::ajouterLanguesParDefaut() Début");
	
		try {
			// Préparer ajout
			$stmt = $this->dbh->prepare("
										
					insert into tlangue (
										 id_langue, id_projet, titre, delimiteur, boutons_annuler, boutons_ok, consignes_association, consignes_choixmultiples, 
										 consignes_classement, consignes_damier_masquees, consignes_damier_nonmasquees, consignes_developpement, consignes_dictee_debut, 
										 consignes_dictee_majuscules, consignes_dictee_ponctuation, consignes_marquage, consignes_ordre, consignes_reponsebreve_debut, 
										 consignes_reponsebreve_majuscules, consignes_reponsebreve_ponctuation, consignes_reponsesmultiples_unereponse, 
										 consignes_reponsesmultiples_toutes, consignes_lacunaire_menu, consignes_lacunaire_glisser, consignes_lacunaire_reponsebreve_debut, 
										 consignes_lacunaire_reponsebreve_majuscules, consignes_lacunaire_reponsebreve_ponctuation, 
										 consignes_vraifaux, consignes_zones, fenetre_renseignements, fenetre_nom, fenetre_prenom, fenetre_matricule, fenetre_groupe, fenetre_courriel, 
										 fenetre_autre, fenetre_envoi, fenetre_courriel_destinataire, fonctionnalites_commencer, fonctionnalites_effacer, fonctionnalites_courriel, 
										 fonctionnalites_imprimer, fonctionnalites_lexique, fonctionnalites_questionnaire, fonctionnalites_recommencer, fonctionnalites_reprendre, 
										 fonctionnalites_resultats, fonctionnalites_solution, fonctionnalites_valider, navigation_page, navigation_de, message_bonnereponse, 
										 message_mauvaisereponse, message_reponseincomplete, media_bonnereponse, media_mauvaisereponse, media_reponseincomplete,										 
										 message_point, message_points, message_sanstitre, conjonction_et, 
										 message_dictee_motsentrop, message_dictee_orthographe, message_dictee_motsmanquants, message_reponsesuggeree, resultats_afaire, 
										 resultats_areprendre, resultats_confirmation, resultats_nbessais, resultats_points, resultats_reussi, resultats_sansobjet, resultats_statut, 
										 resultats_tempsdereponse, item_association, item_choixmultiples, item_classement, item_damier, item_developpement, item_dictee, item_marquage, 
										 item_miseenordre, item_reponsebreve, item_reponsesmultiples, item_textelacunaire, item_vraioufaux, item_zonesaidentifier, remarque, 
										 message_libelle_solution, resultats_accueil, resultats_objet_courriel, resultats_message_courriel_succes, resultats_message_courriel_erreur,
										 statut, date_modification, date_creation) 
										 
					select				 id_langue, ?, titre, delimiteur, boutons_annuler, boutons_ok, consignes_association, consignes_choixmultiples, 
										 consignes_classement, consignes_damier_masquees, consignes_damier_nonmasquees, consignes_developpement, consignes_dictee_debut, 
										 consignes_dictee_majuscules, consignes_dictee_ponctuation, consignes_marquage, consignes_ordre, consignes_reponsebreve_debut, 
										 consignes_reponsebreve_majuscules, consignes_reponsebreve_ponctuation, consignes_reponsesmultiples_unereponse, 
										 consignes_reponsesmultiples_toutes, consignes_lacunaire_menu, consignes_lacunaire_glisser, consignes_lacunaire_reponsebreve_debut, 
										 consignes_lacunaire_reponsebreve_majuscules, consignes_lacunaire_reponsebreve_ponctuation, 
										 consignes_vraifaux, consignes_zones, fenetre_renseignements, fenetre_nom, fenetre_prenom, fenetre_matricule, fenetre_groupe, fenetre_courriel, 
										 fenetre_autre, fenetre_envoi, fenetre_courriel_destinataire, fonctionnalites_commencer, fonctionnalites_effacer, fonctionnalites_courriel, 
										 fonctionnalites_imprimer, fonctionnalites_lexique, fonctionnalites_questionnaire, fonctionnalites_recommencer, fonctionnalites_reprendre, 
										 fonctionnalites_resultats, fonctionnalites_solution, fonctionnalites_valider, navigation_page, navigation_de, message_bonnereponse, 
										 message_mauvaisereponse, message_reponseincomplete, media_bonnereponse, media_mauvaisereponse, media_reponseincomplete,										 
										 message_point, message_points, message_sanstitre, conjonction_et, 
										 message_dictee_motsentrop, message_dictee_orthographe, message_dictee_motsmanquants, message_reponsesuggeree, resultats_afaire, 
										 resultats_areprendre, resultats_confirmation, resultats_nbessais, resultats_points, resultats_reussi, resultats_sansobjet, resultats_statut, 
										 resultats_tempsdereponse, item_association, item_choixmultiples, item_classement, item_damier, item_developpement, item_dictee, item_marquage, 
										 item_miseenordre, item_reponsebreve, item_reponsesmultiples, item_textelacunaire, item_vraioufaux, item_zonesaidentifier, remarque, 
										 message_libelle_solution, resultats_accueil, resultats_objet_courriel, resultats_message_courriel_succes, resultats_message_courriel_erreur,
										 statut, now(), now()
					from tlangue_defaut
									
					");
	
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_projet')));
				
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::ajouterLanguesParDefaut() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Mettre à jour l'index
		$this->indexer();
	
		$this->log->debug("Projet::ajouterLanguesParDefaut() Fin");
	
		return;
	}	
	
	
	
	/**
	 *
	 * Ajouter un rôle pour un utilisateur dans le projet courant
	 * @param string idUsager
	 * @param string role
	 *
	 */
	public function ajouterRole($idUsager, $role) {
	
		$this->log->debug("Projet::ajouterRole() Début");
		
		$this->log->debug("Projet::ajouterRole() Ajout du role '$role' pour l'usager '$idUsager'");
	
		try {
			// Préparer ajout
			$stmt = $this->dbh->prepare("insert into rprojet_usager_role (id_projet, id_usager, id_role)
										 values (?, ?, ?)");
				
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_projet'),
					$idUsager,
					$role
			));

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::ajouterRole() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::ajouterRole() Fin");
	
		return;
	}	
	
	/**
	 *
	 * Modifier un rôle pour un utilisateur dans le projet courant
	 * 
	 * @param String idUsager
	 * @param String $roleOrig
	 * @param String $roleNouv
	 *
	 */
	public function modifierRole($idUsager, $roleOrig, $roleNouv) {
	
		$this->log->debug("Projet::modifierRole() Début");
	
		$this->log->debug("Projet::modifierRole() Modifier le rôle '$roleOrig' pour '$roleNouv' pour l'usager '$idUsager'");
	
		try {
			// Préparer ajout
			$stmt = $this->dbh->prepare("update rprojet_usager_role 
										 set id_role = ?
										 where id_projet = ?
										 and id_usager = ?
										 and id_role = ?");
	
			// Insertion d'un enregistrement
			$stmt->execute(array($roleNouv,
								 $this->get('id_projet'),
								 $idUsager,
								 $roleOrig
			));
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::modifierRole() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::modifierRole() Fin");
	
		return;
	}
		
	
	/**
	 *
	 * Supprimer un rôle pour un utilisateur dans le projet courant
	 * 
	 * @param String idUsager
	 * @param Sstring role
	 *
	 */
	public function supprimerRole($idUsager, $role) {
	
		$this->log->debug("Projet::supprimerRole() Début");
	
		$this->log->debug("Projet::supprimerRole() Supprimer le role '$role' pour l'usager '$idUsager'");
	
		try {
			// Préparer sql
			$stmt = $this->dbh->prepare("delete from rprojet_usager_role 
										 where id_projet = ? 
										 and id_usager = ?
										 and id_role = ?");
	
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get('id_projet'),
								 $idUsager,
								 $role));
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::supprimerRole() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::supprimerRole() Fin");
	
		return;
	}	
	
	/**
	 * 
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un projet
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Projet::enregistrer() Début");
		
		// Vérifier le titre : s'il est vide, utiliser la valeur par défaut
		if ( trim($this->get("titre")) == "") {
			$this->set("titre", TXT_NOUVEAU_PROJET);
		}
		
		try {
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("update tprojet 
										 set titre = ?,
										 	 description = ?,
											 repertoire = ?,
											 notification = ?,
										 	 statut = ?,
								  		 	 date_modification = now()										
										 where id_projet = ? 
											");
	
			// insertion d'une ligne
			$stmt->execute( array(  $this->get('titre'),
									$this->get('description'),
									$this->get('repertoire'),
									$this->get('notification'),
									$this->get('statut'),
									$this->get('id_projet')
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Mettre à jour l'index
		$this->indexer();
										
		$this->log->debug("Projet::enregistrer() Fin");
										
		return;
	}		
	
	
	/**
	 *
	 * Obtenir les collaborateurs actuels
	 *
	 */
	public function getCollaborateursActuels() {
	
		$this->log->debug("Projet::getCollaborateursActuels() Début");
		
		$listeCollaborateurs = array();
	
		try {
			// Préparer le SQL
			$sql = "select tusager.id_usager, tusager.prenom, tusager.nom, tusager.courriel
					from tusager, rprojet_usager_role
					where rprojet_usager_role.id_projet = ?
					and rprojet_usager_role.id_role = ?
					and tusager.id_usager = rprojet_usager_role.id_usager";
				
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), PROJET::ROLE_COLLABORATEUR));
				
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun collaborateur actuel trouvé pour le projet id '" . $this->get("id_projet") . "'");
			} else {
				// Récupérer les informations pour le projet
				$result = $sth->fetchAll();
					
				foreach($result as $row) {
					array_push($listeCollaborateurs, $row);
				}
		
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getCollaborateursActuels() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("Projet::getCollaborateursActuels() Fin");
		return $listeCollaborateurs;
	}
		
	
	/**
	 *
	 * Obtenir les collaborateurs invités
	 *
	 */
	public function getCollaborateursInvites() {
	
		$this->log->debug("Projet::getCollaborateursInvites() Début");
	
		$listeCollaborateurs = array();
	
		try {
			// Préparer le SQL
			$sql = "select collaborateur_courriel, date_creation
					from tcollaborateur
					where id_projet = ?";
	
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
	
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun collaborateur invité trouvé pour le projet id '" . $this->get("id_projet") . "'");
			} else {
				// Récupérer les informations pour le projet
				$result = $sth->fetchAll();
					
				foreach($result as $row) {
					array_push($listeCollaborateurs, $row);
				}
	
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getCollaborateursInvites() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::getCollaborateursInvites() Fin");
		return $listeCollaborateurs;
	}	
	

	/**
	 *
	 * Sauvegarder les informations sur les collaborateurs
	 *	 
	 */
	public function enregistrerCollaborateurs() {
	
		$this->log->debug("Projet::enregistrerCollaborateurs() Début");
	
		// Enregistrer les informations sur les choix de réponses
		$sth = $this->dbh->prepare("insert into tcollaborateur (id_projet, collaborateur_courriel, date_creation, date_modification) VALUES (?, ?, now(), now()) ");
	
		try{
			// Parcourir les collaborateurs
			for ($i = 0; $i <= NB_MAX_COLLABORATEURS; $i++) {
				
				$courriel = trim($this->get("collaborateur_" . $i . "_courriel"));
				
				if ($courriel != "") {
				
					$rows = $sth->execute(array($this->get("id_projet"),  $courriel ));
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::enregistrerCollaborateurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::enregistrerCollaborateurs() Début");
	}
	
	
	/**
	 *
	 * Supprimer les informations sur les collaborateurs
	 *
	 */
	public function supprimerCollaborateurs() {
	
		$this->log->debug("Projet::supprimerCollaborateurs() Début");
	
		try {
			$sql = "delete from tcollaborateur where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::supprimerCollaborateurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::supprimerCollaborateurs() Fin");
		return;
	}	
	
	
	/**
	 *
	 * Supprimer l'accès d'un collaborateur au projet
	 * @param string idUsager
	 *
	 */
	public function supprimerCollaborateur($idUsager) {
	
		$this->log->debug("Projet::supprimerCollaborateur() Début idUsager : '$idUsager'");
	
		try {
			$sql = "delete from rprojet_usager_role
					where id_projet = ?
					and id_usager = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $idUsager));
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::supprimerCollaborateurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Obtenir les informations de la personne
		$u = new Usager($this->log, $this->dbh);
		$u->getUsagerParIdUsager($idUsager);
		
		// Envoi d'un courriel pour aviser l'utilisateur du retrait des accès
		$this->envoiCourrielRetraitCollaborateur($u);
		
		$this->log->debug("Projet::supprimerCollaborateur() Fin");
		return;
	}	
	
	
	/**
	 *
	 * Vérifier que la personne est collaborateur au projet
	 * 
	 * @param string $idProjet
	 * @param string $listeProjetsActifs
	 *
	 */
	public function isRoleCollaborateurProjet($idProjet, $listeProjetsActifs) {
	
		$this->log->debug("Projet::isRoleCollaborateurProjet() Début idProjet = '$idProjet'");
	
		$trouve = in_array($idProjet, $listeProjetsActifs);
	
		$this->log->debug("Projet::isRoleCollaborateurProjet() Fin Trouve = '$trouve'");
		return $trouve;
	}	
	
	
	/**
	 *
	 * Vérifier que la personne est responsable du projet
	 *
	 * @param string $idUsager
	 * @param string $idProjet
	 *
	 */
	public function isRoleResponsableProjet($idUsager, $idProjet) {
	
		$this->log->debug("Projet::isRoleResponsableProjet() Début idUsager : '$idUsager' idProjet : '$idProjet'\n");
		$isResponsable = false;
		
		try {
			// SQL de la requête
			$sql = "select id_usager 
					from rprojet_usager_role
					where id_projet = ?
					and id_role = 1";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
				
			// Obtenir l'id du responsable
			$idUsagerResponsable = $sth->fetchColumn();
			
			// Vérifier
			if ($idUsager == $idUsagerResponsable) {
				$isResponsable = true;
			}
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getResponsableProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::isRoleResponsableProjet() Fin isResponsable = '$isResponsable'");
		return $isResponsable;
	}	
	
	/**
	 * 
	 * Charger le projet à partir de la base de données
	 * @param String idProjet
	 * 
	 */
	public function getProjetParId($idProjet) {

		$this->log->debug("Projet::getProjetParId() Début idProjet = '$idProjet'");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select " . $this->listeChamps . " 
					from 
					  tprojet
					where 
					  id_projet = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idProjet));
			
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun projet trouvé pour l'id '$idProjet'");
			}
			
			// Vérifier qu'une seul projet est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('008', "La recherche pour le projet id '$idProjet' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour le projet
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
		        
		        // Indiquer qu'un seul projet a été trouvé
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getProjetParId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Préparer le titre du menu
		$titreMenu = Web::tronquer($this->get("titre"), 45);
		$this->set("titre_menu", $titreMenu);
		
		// Terminé
		$this->log->debug("Projet::getProjetParId() Trouve = '$trouve'");
		$this->log->debug("Projet::getProjetParId() Fin");
		return $trouve;		
	}

	
	/**
	 *
	 * Obtenir le prochain ID pour l'élément demandé
	 * string element
	 *
	 */
	public function genererId($element) {
	
		$this->log->debug("Projet::genererId() Début élément = '$element'");
	
		$id = "";
		
		$dernierElement = "dernier_" . $element;
		
		try {
	
			// Débuter transaction
			$this->dbh->beginTransaction();
				
			// Select pour update
			$sql = "select " . $dernierElement . " from tprojet where id_projet = ? for update";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($this->get("id_projet")));
				
			// Update
			$sql2 = "update tprojet set " . $dernierElement . " = (@cur_" . $dernierElement . " := " . $dernierElement . ") + 1 where id_projet = ?";
			$sth2= $this->dbh->prepare($sql2);
			$sth2->execute(array($this->get("id_projet")));
	
			// Select pour update
			$sql3 = "select " . $dernierElement . " from tprojet where id_projet = ?";
			$sth3 = $this->dbh->prepare($sql3);
			$sth3->execute(array($this->get("id_projet")));
				
			$this->dbh->commit();
				
			// Récupérer l'id
			$id = $sth3->fetchColumn();

				
		} catch (Exception $e) {
		// En cas d'erreur rollback
			$this->dbh->rollBack();
				
			// Log de l'erreur
			Erreur::erreurFatal('018', "Projet::genererId() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
	
		// Terminé
		$this->log->debug("Projet::genererId() Fin id retourné : '$id'");
		return $id;
	}	
	
	

	/**
	 *
	 * Obtenir le prochain ID pour un questionnaire
	 *
	 */
	public function genererIdQuestionnaire() {
	
		$this->log->debug("Projet::genererIdQuestionnaire() Début");
		
		$id = $this->genererId("id_questionnaire");
	
		$this->log->debug("Projet::genererIdQuestionnaire() Fin");
		return $id;
	}	
	
	
	/**
	 *
	 * Obtenir le prochain ID pour une langue
	 *
	 */
	public function genererIdLangue() {
	
		$this->log->debug("Projet::genererIdLangue() Début");
	
		$id = $this->genererId("id_langue");
	
		$this->log->debug("Projet::genererIdLangue() Fin");
		return $id;
	}

	
	/**
	 *
	 * Obtenir le prochain ID pour un item
	 *
	 */
	public function genererIdItem() {
	
		$this->log->debug("Projet::genererIdItem() Début");
	
		$id = $this->genererId("id_item");
	
		$this->log->debug("Projet::genererIdItem() Fin");
		return $id;
	}

	
	/**
	 *
	 * Obtenir le prochain ID pour une section
	 *
	 */
	public function genererIdSection() {
	
		$this->log->debug("Projet::genererIdSection() Début");
	
		$id = $this->genererId("id_section");
	
		$this->log->debug("Projet::genererIdSection() Fin");
		return $id;
	}
	
	

	/**
	 *
	 * Obtenir le prochain ID pour un media
	 *
	 */
	public function genererIdMedia() {
	
		$this->log->debug("Projet::genererIdMedia() Début");
	
		$id = $this->genererId("id_media");
	
		$this->log->debug("Projet::genererIdMedia() Fin");
		return $id;
	}

	
	/**
	 *
	 * Obtenir le prochain ID pour une catégorie
	 *
	 */
	public function genererIdCategorie() {
	
		$this->log->debug("Projet::genererIdCategorie() Début");
	
		$id = $this->genererId("id_categorie");
	
		$this->log->debug("Projet::genererIdCategorie() Fin");
		return $id;
	}
	

	/**
	 *
	 * Obtenir le prochain ID pour une collection
	 *
	 */
	public function genererIdCollection() {
	
		$this->log->debug("Projet::genererIdCollection() Début");
	
		$id = $this->genererId("id_collection");
	
		$this->log->debug("Projet::genererIdCollection() Fin");
		return $id;
	}	
	
	
	/**
	 *
	 * Obtenir le prochain ID pour un terme
	 *
	 */
	public function genererIdTerme() {
	
		$this->log->debug("Projet::genererIdTerme() Début");
	
		$id = $this->genererId("id_terme");
	
		$this->log->debug("Projet::genererIdTerme() Fin");
		return $id;
	}	
	
	/**
	 *
	 * Obtenir la liste des projets pour un utilisateur
	 * 
	 * @param String idUsager 
	 * @param Log log
	 * @param PDO dbh
	 *
	 */
	static public function getListeProjetsUtilisateur($idUsager, $log, $dbh) {
	
		$log->debug("Projet::getListeProjetsUtilisateur() Début - idUsager = '$idUsager'");
	
		$listeProjets = array();
		
		try {
			// SQL de recherche
			$sql = "select tprojet.id_projet, tprojet.titre, tprojet.description, tprojet.repertoire 
					from tprojet, rprojet_usager_role 
					where tprojet.id_projet = rprojet_usager_role.id_projet 
					and rprojet_usager_role.id_usager = ? and tprojet.statut = 1
					order by tprojet.titre";
			$sth = $dbh->prepare($sql);
			$rows = $sth->execute(array($idUsager));

			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$log->info("Projet::getListeProjetsUtilisateur() Aucun projet trouvé pour l'usager '$idUsager'");
			}
			else {
				// Récupérer les ids des questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$proj = new Projet($log, $dbh);
					
					$proj->set("id_projet", $row['id_projet']); 
					$proj->set("titre", $row['titre']);
					$proj->set("description", $row['description']);
					$proj->set("repertoire", $row['repertoire']);
					
					array_push($listeProjets, $proj);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getListeProjetsUtilisateur() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
		}
	
		$log->debug("Projet::getListeProjetsUtilisateur() Fin");
		return $listeProjets;
	}	
	
	
	/**
	 *
	 * Obtenir la liste de tous les projets actifs
	 * 
	 * @param Log log
	 * @param PDO dbh
	 *
	 */
	static public function getListeProjetsActifs($log, $dbh) {
	
		$log->debug("Projet::getListeProjetsActifs() Début");
	
		$listeProjets = array();
	
		try {
			// SQL de recherche
			$sql = "select tprojet.id_projet, tprojet.titre, tprojet.description, tprojet.repertoire
					from tprojet
					where tprojet.statut = 1
					order by tprojet.titre";
			$sth = $dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$log->info("Projet::getListeProjetsActifs() Aucun projet actif trouvé");
			}
			else {
				// Récupérer les ids des questionnaires
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					$proj = new Projet($log, $dbh);
						
					$proj->set("id_projet", $row['id_projet']);
					$proj->set("titre", $row['titre']);
					$proj->set("description", $row['description']);
					$proj->set("repertoire", $row['repertoire']);
						
					array_push($listeProjets, $proj);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getListeProjetsActifs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
		}
	
		$log->debug("Projet::getListeProjetsActifs() Fin");
		return $listeProjets;
	}	
	
	
	/**
	 *
	 * Obtenir la liste de tous les projets
	 * @param String idUsager
	 * @param String Tri
	 * @param Boolean $filtreActif
	 * @param String $filtreResponsable
	 *
	 */
	public function getListeTousProjets($tri, $filtreActif = false, $filtreResponsable) {
	
		$this->log->debug("Projet::getListeTousProjets() Début");
		$listeProjets = array();
	
		try {
			// SQL de base
			$sql = "select tprojet.id_projet, tprojet.titre, tprojet.statut, tprojet.date_modification, 
						(
						select concat(prenom, ' ', nom) 
						from tusager, rprojet_usager_role as pur
						where pur.id_projet = tprojet.id_projet
						and tusager.id_usager = pur.id_usager
					    and pur.id_role = 1
						) as responsable
					from tprojet
					where tprojet.statut in (0, 1) 
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
				
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$this->log->info("Projet::getListeTousProjets() Aucun projet trouvé pour l'usager '$idUsager'");
			}
			else {
				// Récupérer les ids des projets
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour le responsable
					if ($filtreActif && $filtreResponsable != "" && $row['responsable'] != $filtreResponsable) {
						continue;
					}	
					$id = $row['id_projet'];
					array_push($listeProjets, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getListeTousProjets() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Conserver la liste en session
		if ($filtreActif) {
			$session = new Session();
			$session->set("liste_projets_tous", $listeProjets);
		}
	
		// Terminé
		$this->log->debug("Projet::getListeTousProjets() Fin");
		return $listeProjets;
	}


	/**
	 *
	 * Obtenir la liste des projets pour un utilisateur
	 * @param String idUsager
	 * @param String Tri
	 * @param Boolean $filtreActif
	 * @param String $filtreResponsable
	 *
	 */
	public function getListeProjets($idUsager, $tri, $filtreActif = false, $filtreResponsable) {
	
		$this->log->debug("Projet::getListeProjets() Début");
		$listeProjets = array();
	
		try {
			// SQL de base
			$sql = "select tprojet.id_projet, tprojet.titre, tprojet.statut, tprojet.date_modification, 
						(
						select concat(prenom, ' ', nom) 
						from tusager, rprojet_usager_role as pur
						where pur.id_projet = tprojet.id_projet
						and tusager.id_usager = pur.id_usager
					        and pur.id_role = 1
						) as responsable
					from tprojet, rprojet_usager_role
					where rprojet_usager_role.id_usager = ?
					and tprojet.id_projet = rprojet_usager_role.id_projet
					and tprojet.statut in (0, 1) 
					order by $tri";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idUsager));
				
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
			$this->log->info("Projet::getListeProjets() Aucun projet trouvé pour l'usager '$idUsager'");
			}
			else {
				// Récupérer les ids des projets
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour le responsable
					if ($filtreActif && $filtreResponsable != "" && $row['responsable'] != $filtreResponsable) {
						continue;
					}	
					$id = $row['id_projet'];
					array_push($listeProjets, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getListeProjets() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Conserver la liste en session
		if ($filtreActif) {
			$session = new Session();
			$session->set("liste_projets", $listeProjets);
		}		
	
		// Terminé
		$this->log->debug("Projet::getListeProjets() Fin");
		return $listeProjets;
	}
	
	
	/**
	 *
	 * Effectuer une recherche dans les projets et les éléments des projets
	 * @param String chaine
	 * @param String idUsager
	 * @param String statut
	 * @param String idUsager
	 * @param String filtreResponsable
	 * @param bool modeAdmin
	 *
	 */
	public function recherche($chaine, $tri, $statut, $idUsager, $filtreResponsable, $modeAdmin = false) {
	
		$this->log->debug("Projet::recherche() Début chaine = '$chaine'  tri : '$tri' statut = '$statut'  idUsager = '$idUsager'  filtreResponsable = '$filtreResponsable'");
		$listeTousProjets = array();
		$listeProjets = array();
	
		// Préparer la chaîne de recherche
		$rech = '%' . Web::nettoyerChaineRech($chaine) . '%';
	
		try {
			
			// Obtenir la liste de tous les projets qui correspondent à la recherche
			$sql = "select distinct id_projet 
					from tprojet_index
					where tprojet_index.texte like '$rech'
					
					union
					
					select distinct id_projet 
					from tquestionnaire_index
					where tquestionnaire_index.texte like '$rech'
					
					union
					
					select distinct id_projet 
					from titem_index
					where titem_index.texte like '$rech'
					
					union
					
					select distinct id_projet 
					from tmedia_index
					where tmedia_index.texte like '$rech'
					
					union
					
					select distinct id_projet 
					from tcategorie_index
					where tcategorie_index.texte like '$rech'
					
					union
					
					select distinct id_projet 
					from tcollection_index
					where tcollection_index.texte like '$rech'
					
					union
					
					select distinct id_projet 
					from tlangue_index
					where tlangue_index.texte like '$rech'";
			
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idUsager, $rech));
			
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$this->log->info("Projet::recherche() Aucun projet trouvé pour les critères de recherche");
			} else {
			
				$listeStatuts = explode(",", $statut);
				
				// Récupérer les ids des projets
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				
					$id = $row['id_projet'];
					
					// Vérifier le statut du projet
					$p = new Projet($this->log, $this->dbh);
					$p->getProjetParId($id);
					
					$statut = $p->get("statut");
					
					$this->log->info("Projet::recherche() Vérifier le statut '$statut' du projet '$id' avec la liste des statuts permis : '$statut'");
					if (in_array($statut, $listeStatuts)) {
						$this->log->info("Projet::recherche() Projet ajouté aux résultats de recherche");
						array_push($listeTousProjets, $id);
					} else {
						$this->log->info("Projet::recherche() Projet rejeté car le statut n'est pas valide");
					}
					
				}
			}
			
			// Transformer la liste
			$listeTousProjetsSQL = implode(",", $listeTousProjets);
			
			// Cas liste vide
			if ($listeTousProjetsSQL == "") {
				$listeTousProjetsSQL = "-1";
			}
			
			// Déterminer si on est en mode admin
			$session = new Session();
			$codeUsagerSession = $session->get('codeUsager');
			$u = new Usager($this->log, $this->dbh);
			$u->getUsagerParCodeUsager($codeUsagerSession);
			
			if ($modeAdmin && $u->isAdmin()) {
				
				// SQL de base
				$sql2 = "select id_projet, titre, statut, date_modification,
					(
						select concat(prenom, ' ', nom)
						from tusager, rprojet_usager_role as pur
						where pur.id_projet = tprojet.id_projet
						and tusager.id_usager = pur.id_usager
						and pur.id_role = 1
					) as responsable
					
				from tprojet
				where id_projet in ( $listeTousProjetsSQL )
				order by $tri";
				$sth2 = $this->dbh->prepare($sql2);
				$rows2 = $sth2->execute(array());
				
			} else { 
			
				// SQL de base
				$sql2 = "select tprojet.id_projet, tprojet.titre, tprojet.statut, tprojet.date_modification,
							(
							select concat(prenom, ' ', nom)
							from tusager, rprojet_usager_role as pur
							where pur.id_projet = tprojet.id_projet
							and tusager.id_usager = pur.id_usager
							and pur.id_role = 1
							) as responsable
						from tprojet, rprojet_usager_role
						where rprojet_usager_role.id_projet in ( $listeTousProjetsSQL )
						and rprojet_usager_role.id_usager = ?
						and rprojet_usager_role.id_role in (1, 2) 
						and tprojet.id_projet = rprojet_usager_role.id_projet
						and tprojet.statut in ($statut)
						order by $tri";
				$sth2 = $this->dbh->prepare($sql2);
				$rows2 = $sth2->execute(array($idUsager));
				
			}
			
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth2->rowCount() == 0) {
				$this->log->info("Projet::recherche() Aucun projet trouvé pour l'usager '$idUsager'");
			} else {
				// Récupérer les ids des projets
				while ($row = $sth2->fetch(PDO::FETCH_ASSOC)) {
	
					// Appliquer le filter pour le responsable
					if ($filtreResponsable != "" && $row['responsable'] != $filtreResponsable) {
						continue;
					}
					$id = $row['id_projet'];
					array_push($listeProjets, $id);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::recherche() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_projets", $listeProjets);
	
		// Terminé
		$this->log->debug("Projet::recherche() Fin");
		return $listeProjets;
	
	}	
	
	/**
	 *
	 * Obtenir l'ordre de tri de la liste des Projets
	 */
	public function getTri() {
	
		$this->log->debug("Projet::getTri() Début");
	
		$session = new Session();
	
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("projet_pref_tri_champ");
		$triSessionOrdre = $session->get("projet_pref_tri_ordre");
		$this->log->debug("Projet::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Projet::getTri() triSessionOrdre = '$triSessionOrdre'");
	
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_projet", "titre", "responsable", "statut", "repertoire", "date_modification");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_projet";
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
			$triParamChamp = "id_projet";
			$triParamOrdre = "asc";
		}
	
		// Stocker le tri dans la session
		$session->set("projet_pref_tri_champ", $triParamChamp);
		$session->set("projet_pref_tri_ordre", $triParamOrdre);
	
		$this->log->debug("Projet::getTri() Fin");
	
		return $triParamChamp . " " . $triParamOrdre;
	}
		
	
	/**
	 *
	 * Obtenir le filtre pour le responsable
	 * 
	 */
	public function getFiltreResponsable() {
	
		$this->log->debug("Projet::getFiltreResponsable() Début");
	
		$session = new Session();
	
		// Vérifier si un filtre est spécifié dans la session
		$filtreResponsable = $session->get("pref_filtre_responsable");
	
		// Vérifier si un filtre est passé en paramètre
		$filtreResponsableParam = Web::getParam("responsable");
	
		// Déterminer si on utilise la valeur passé en paramètre
		if ($filtreResponsableParam != "") {
	
			// Si l'utilisateur veut voir toutes les collections enlever le filtre
			if ($filtreResponsableParam == "tous") {
				$session->delete("pref_filtre_responsable");
				$filtreResponsable = "";
			} else {
				// Stocker le tri dans la session
				$session->set("pref_filtre_responsable", $filtreResponsableParam);
				$filtreResponsable = $filtreResponsableParam;
			}
		}
	
		$this->log->debug("Projet::getFiltreResponsable() Fin");
	
		return $filtreResponsable;
	}	
	

	/**
	 *
	 * Obtenir le filtre pour le responsable - admin
	 *
	 */
	public function getFiltreResponsableAdmin() {
	
		$this->log->debug("Projet::getFiltreResponsableAdmin() Début");
	
		$session = new Session();
	
		// Vérifier si un filtre est spécifié dans la session
		$filtreResponsable = $session->get("pref_filtre_responsable_admin");
	
		// Vérifier si un filtre est passé en paramètre
		$filtreResponsableParam = Web::getParam("responsable");
	
		// Déterminer si on utilise la valeur passé en paramètre
		if ($filtreResponsableParam != "") {
	
			// Si l'utilisateur veut voir toutes les collections enlever le filtre
			if ($filtreResponsableParam == "tous") {
				$session->delete("pref_filtre_responsable_admin");
				$filtreResponsable = "";
			} else {
				// Stocker le tri dans la session
				$session->set("pref_filtre_responsable_admin", $filtreResponsableParam);
				$filtreResponsable = $filtreResponsableParam;
			}
		}
	
		$this->log->debug("Projet::getFiltreResponsableAdmin() Fin");
	
		return $filtreResponsable;
	}
		
		
	/**
	 *
	 * Obtenir le prénom et nom de la personne responsable du projet
	 *
	 */
	public function getResponsableProjet() {
		
		$this->log->debug("Projet::getResonsableProjet() Début obtenir le responsable du projet '" . $this->get("id_projet") . "'");
		
		try {
			// SQL de la requête
			$sql = "select concat(prenom, ' ', nom) as responsable
					from tusager, rprojet_usager_role as pur
					where pur.id_projet = ?
					and tusager.id_usager = pur.id_usager
					and pur.id_role = 1";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
			
			// Obtenir le prénom et nom du responsable
			$responsable = $sth->fetchColumn();
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getResponsableProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		
		$this->log->debug("Projet::getResonsableProjet() Fin responsable = '$responsable'");

		return $responsable;
	}
	
	
	/**
	 *
	 * Obtenir le prénom et nom de la personne responsable du projet
	 *
	 */
	public function getResponsableProjetAvecCourriel() {
	
		$this->log->debug("Projet::getResponsableProjetAvecCourriel() Début obtenir le responsable du projet '" . $this->get("id_projet") . "'");
	
		try {
			// SQL de la requête
			$sql = "select concat(prenom, ' ', nom , ' (' , courriel , ')') as responsable
					from tusager, rprojet_usager_role as pur
					where pur.id_projet = ?
					and tusager.id_usager = pur.id_usager
					and pur.id_role = 1";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
				
			// Obtenir le prénom et nom du responsable
			$responsable = $sth->fetchColumn();
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getResponsableProjetAvecCourriel() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
	
		$this->log->debug("Projet::getResponsableProjetAvecCourriel() Fin responsable = '$responsable'");
	
		return $responsable;
	}


	/**
	 *
	 * Obtenir l'id de la personne responsable du projet
	 *
	 */
	public function getIdResponsableProjet() {
	
		$this->log->debug("Projet::getIdResponsableProjet() Début obtenir le responsable du projet '" . $this->get("id_projet") . "'");
	
		$idResp = 0;
		
		try {
			// SQL de la requête
			$sql = "select id_usager
					from rprojet_usager_role
					where id_projet = ?
					and id_role = 1";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
				
			// Obtenir le prénom et nom du responsable
			$idResp = $sth->fetchColumn();
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getIdResponsableProjet() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
	
		$this->log->debug("Projet::getIdResponsableProjet() Fin responsable = '$idResp'");
	
		return $idResp;
	}	
	
	/**
	 *
	 * Remplacer le responsable du projet
	 * @param String id responsable original
	 * @param String id nouveau responsable
	 *
	 */
	public function remplacerResponsable($idRespOrig, $idRespNouv) {
	
		$this->log->debug("Projet::remplacerResponsable() Début id du responsable original '$idRespOrig'  id nouveau responsable : '$idRespNouv'");

		// Ajouter le nouveau rôle de responsable au collaborateur
		$this->modifierRole($idRespOrig, Projet::ROLE_RESPONSABLE, Projet::ROLE_COLLABORATEUR);
		
		// Enlever le rôle de responsable original et le mettre collaborateur
		$this->modifierRole($idRespNouv, Projet::ROLE_COLLABORATEUR, Projet::ROLE_RESPONSABLE);
			
		// Envoi courriels aux collaborateurs
		$this->envoiCourrielCollaborateursModifications();

		// Envoi courriel au nouveau responsable
		$u = new Usager($this->log, $this->dbh);
		$u->getUsagerParIdUsager($idRespNouv);
		
		// Ajouter le prénom et nom pour le courriel
		$this->set("prenom", $u->get("prenom"));
		$this->set("nom", $u->get("nom"));
		
		$this->envoiCourrielNouveauResponsable($u->get("courriel"));
		
		$this->log->debug("Projet::remplacerResponsable() Fin'");
	
		return;
	}	
	
	
	/**
	 *
	 * Obtenir la liste des responsables de projets
	 *
	 */
	public function getListeResponsablesProjets() {
	
		$this->log->debug("Projet::getListeResponsablesProjets()");
		
		$listeResponsables = array();
	
		try {
			// SQL de la requête
			$sql = "select tusager.id_usager, concat(prenom, ' ', nom) as responsable
					from tusager, rprojet_usager_role as pur
					where tusager.id_usager = pur.id_usager
					and pur.id_role = 1";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array());
	
			// Récupérer les informations
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$id = $row['id_usager'];
				$responsable = $row['responsable'];
			}	
			

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getListeResponsablesProjets() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}		
	
		$this->log->debug("Projet::getListeResponsablesProjets() Fin");
		return $listeResponsables;
	}	
	
	
	/**
	 *
	 * Obtenir la liste des responsables de projets pour des projets spécifiques
	 * 
	 * param array $listeProjets
	 *
	 */
	public function getListeResponsablesProjetsPourProjetsSpecifiques($listeProjet) {
	
		$this->log->debug("Projet::getListeResponsablesProjetsPourProjetsSpecifiques() Début");
	
		$listeResponsables = array();
	
		$listeId = implode(",", $listeProjet);
		
		// Cas particulier liste vide
		if ($listeId == "") {
			$listeId = "-1";
		}
		
		try {
			// SQL de la requête
			$sql = "select tusager.id_usager, concat(prenom, ' ', nom) as responsable
					from tusager, rprojet_usager_role as pur
					where pur.id_projet in ( " . $listeId . ")
					and tusager.id_usager = pur.id_usager
					and pur.id_role = 1";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array());
	
			// Récupérer les informations
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
				$id = $row['id_usager'];
				$responsable = $row['responsable'];
				$listeResponsables[$id] = $responsable;
			}
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::getListeResponsablesProjetsPourProjetsSpecifiques() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		// Rendre les valeurs uniques
		$listeResponsables = array_unique($listeResponsables);
		
		$this->log->debug("Projet::getListeResponsablesProjetsPourProjetsSpecifiques() Fin");
		return $listeResponsables;
	}
		
	
	/**
	 *
	 * Vérifier que le projet est valide
	 *
	 */
	public function verifierProjet() {
	
		$this->log->debug("Projet::verifierProjet() Début");
		
		// Vérifier que le projet a un identifiant unique, sinon le générer
		$this->log->debug("Projet::verifierProjet() Valider qu'un identifiant unique existe");
		if ($this->get("repertoire") == "") {
			$this->log->debug("Projet::verifierProjet() Projet sans identifiant (répertoire) unique... mesure corrective déclenchée");
			$nouvIdProjet = PREFIX_IDENTIFIANT_PROJET_DEFAUT . $this->get("id_projet");
			$this->log->debug("Projet::verifierProjet() Nouvel identifiant assigné automatiquement : '" + $nouvIdProjet + "'");
			$this->set("repertoire", $nouvIdProjet);
			$this->enregistrer();
		}
		
		$this->log->debug("Projet::verifierProjet() Fin");
	}
	
	/**
	 *
	 * Vérifier les informations du projet
	 * 
	 * boolean verifierRepertoire
	 *
	 */
	public function verifierChampsProjet($verifierRepertoire = false) {
	
		$this->log->debug("Projet::verifierChampsProjet() Début");
	
		$erreurs = "";
	
		// Raccourcis pour les champs
		$idProjet = $this->get("id_projet");
		$titre = trim($this->get("titre"));
		$description = trim($this->get("description"));
		$repertoire = trim($this->get("repertoire"));
	
		// Vérifier le titre
		if ($titre == "" || strlen($titre) > 300 ) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_181 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier la description
		if (strlen($description) > 3000) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_182 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le nom de répertoire si c'est un nouveau projet
		if ($verifierRepertoire) {
			
			// Vérifier le format du nom de répertoire
			if ($repertoire == "" || strlen($repertoire) > 80 || ! $this->verifierNomRepertoire()) {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_183 . HTML_LISTE_ERREUR_FIN;
				$this->set("erreur_repertoire", "1");
			}
			
			// Vérifier que le nom de répertoire n'est pas déjà utilisé si c'est un nouveau projet
			if ($verifierRepertoire && $repertoire != "" && $this->verifierNomRepertoireUtilise()) {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_185 . HTML_LISTE_ERREUR_FIN;
				$this->set("erreur_repertoire", "1");
			}
	
			// Vérifier que le nom de répertoire contient au moins une lettre
			preg_match_all( "/[a-z]/", $this->get("repertoire"), $matches, PREG_SET_ORDER );
			
			if (empty($matches)) {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_186 . HTML_LISTE_ERREUR_FIN;
				$this->set("erreur_repertoire", "1");
			}
						
		}
		
		// Conserver les versions trim
		$this->set("titre", $titre);
		$this->set("description", $description);
		$this->set("repertoire", $repertoire);
	
		$this->log->debug("Projet::verifierChampsProjet() Fin");
	
		return $erreurs;
	}

	
	/**
	 *
	 * Vérifier le format du nom du répertoire
	 *
	 */
	public function verifierNomRepertoire() {
	
		$this->log->debug("Projet::verifierRepertoire() Début");
	
		$valide = true;
	
		// Vérifier la longueur minimale
		if (strlen($this->get("repertoire")) < SECURITE_REPERTOIRE_LONGUEUR_MIN) {
			$valide = false;
		}
		
		if (!preg_match('/^[a-z0-9_\-]+$/u', $this->get("repertoire"))) {
			$valide = false;
		}
	
		$this->log->debug("Projet::verifierRepertoire() Fin");
	
		return $valide;
	}
	

	/**
	 *
	 * Vérifier si le nom du répertoire est utilisé
	 *
	 */
	public function verifierNomRepertoireUtilise() {
	
		$this->log->debug("Projet::verifierNomRepertoireUtilise() Début");
		$utilise = false;
	
		try {
			// SQL 
			$sql = "select tprojet.id_projet from tprojet where repertoire = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("repertoire")));
			
			// Vérifier qu'on a trouvé au moins un item
			if ($sth->rowCount() != 0) {
				$this->log->info("Projet::verifierNomRepertoireUtilise() Le répertoire '" . $this->get("repertoire") . "' est déjà utilisé");
				$utilise = true;
			} else {
				$this->log->info("Projet::verifierNomRepertoireUtilise() Le répertoire '" . $this->get("repertoire") . "' n'est pas utilisé");
				$utilise = false;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::verifierNomRepertoireUtilise() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
		}
	
		$this->log->debug("Projet::verifierNomRepertoireUtilise() Fin");
	
		return $utilise;
	}	
		

	/**
	 *
	 * Régler le projet courant
	 * 
	 * @param string idProjet
	 *
	 */
	public function setProjetCourant($idProjet) {
	
		$this->log->debug("Projet::setProjetCourant() Début - idProjet : '$idProjet'");

		// Conserver en session
		$session = new Session();
		$session->set("idProjetCourant", $idProjet);
	
		$this->log->debug("Projet::setProjetCourant() Fin");
	
		return;
	}	
	

	/**
	 *
	 * Obtenir l'id du projet courant
	 * 
	 * @param Usager usagers
	 * @return string idProjet
	 *
	 */
	public function getIdProjetCourant($usager) {
	
		$this->log->debug("Projet::getProjetCourant() Début");
		
		$idProjet = 0;
	
		// Obtenir la valeur la session
		$session = new Session();
		$idProjet = $session->get("idProjetCourant");
		$this->log->debug("Projet::getProjetCourant() IDProjet de la session : '$idProjet'");

		// Sinon vérifier la valeur du profil utilisateur
		if ($idProjet == 0) {
			$idProjet = $usager->get("pref_projet");
			$this->log->debug("Projet::getProjetCourant() idProjet du profil utilisateur (BD) : '$idProjet'");
		}
		
		// Vérifier que le projet est dans la liste des projets actifs
		$listeProjets = $this->getListeProjetsUtilisateur($usager->get("id_usager"), $this->log, $this->dbh);
		
		$projetTrouve = false;
		foreach ($listeProjets as $proj) {
			if ($proj->get("id_projet") == $idProjet) {
				$projetTrouve = true;
			}
		}
		
		// Projet non-trouvé, prendre le premier projet si disponible
		if (!$projetTrouve) {
			if (! empty($listeProjets)) {
				// Utiliser le 1er projet
				$proj = $listeProjets[0];
				$idProjet = $proj->get("id_projet");
			} else {
				// Aucun projet
				$idProjet = 0;
			}
		}
	
		$this->log->debug("Projet::getProjetCourant() Fin - idProjet : '$idProjet'");
	
		return $idProjet;
	}	
	
	
	/**
	 * 
	 * Obtenir les valeurs du questionnaire à partir de la requête web
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {

		$this->log->debug("Projet::getDonneesRequete() Début");
		
		// Obtenir les paramètres
		$params = Web::getListeParam("projet_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'";
		}
		
		$this->log->debug("Projet::getDonneesRequete() Fin");
		return;
	}		
	
	
	/**
	 *
	 * Obtenir le statut dans la langue de l'utilisateur
	 */
	public function getStatutTxt() {
	
		$val = "";
		
		if ($this->get("statut") != "") {
			// Obtenir la chaîne à récupérer
			$str ="PROJET_STATUT_" . strtoupper($this->get('statut'));
		
			// Obtenir la valeur à partir du fichier des langues
			$val = constant($str);
		}
	
		return $val;
	}
	
	
	/**
	 * 
	 * Désactiver le projet (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
		
		$this->log->debug("Projet::desactiver()");
		
		// Obtenir la liste des questionnaires du projet
		$quest = new Questionnaire($this->log, $this->dbh);
		$listeQuest = $quest->getListeQuestionnaire($this->get("id_projet"));
		
		foreach ($listeQuest as $idQuest) {
			$q = new Questionnaire($this->log, $this->dbh);
			$q->getQuestionnaireParId($idQuest, $this->get("id_projet"));
			$q->desactiverPublication($this);
		}
		
		$this->set("statut",Projet::STATUT_SUPPRIME);
		$this->enregistrer();
		
		$this->log->debug("Projet::desactiver() Fin");
	}		
	

	/**
	 * 
	 * Activer le projet
	 *
	 */
	public function activer() {
		
		$this->log->debug("Projet::activer() Début");
		
		// Activer le suivi
		$this->set("statut", PROJET::STATUT_ACTIF);
		
		// Sauvegarder les données
		$this->enregistrer();
		
		$this->log->debug("Projet::activer() Fin");
	}	


	/**
	 *
	 * Supprimer les médias du projet
	 * 
	 */
	private function supprimerMedias() {
	
		$this->log->debug("Projet::supprimerMedias() Début");
		
		// Obtenir la liste des médias du projet
		$media = new Media($this->log, $this->dbh);
		$listeMedias = $media->getListeIdMediasDuProjet($this->get("id_projet"));

		// Parcourir la liste des médias
		foreach ($listeMedias as $idMedia) {
			
			// Charger et supprimer le média
			$this->log->debug("Projet::supprimerMedias() Suppression du média '$idMedia'");
			$m = new Media($this->log, $this->dbh);
			$m->getMediaParId($idMedia, $this->get("id_projet"));
			$m->supprimer();
		}
		
		$this->log->debug("Projet::supprimerMedias() Fin");
	}
	
	/**
	 *
	 * Supprimer les items du projet
	 *
	 */
	private function supprimerItems() {
	
		$this->log->debug("Projet::supprimerItems() Début");
	
		// Obtenir la liste des items du projet
		$item = new Item($this->log, $this->dbh);
		$listeItems = $item->getListeIdItemsDuProjet($this->get("id_projet"));
	
		// Parcourir la liste des items
		foreach ($listeItems as $idItem) {
				
			// Charger et supprimer l'item
			$this->log->debug("Projet::supprimerItems() Suppression de l'item '$idItem'");
			$i = new Item($this->log, $this->dbh);
			$i->getItemParId($idItem, $this->get("id_projet"));
			$i->supprimer();
		}
	
		$this->log->debug("Projet::supprimerItems() Fin");
	}	
	
	/**
	 *
	 * Supprimer les catégories du projet
	 *
	 */
	private function supprimerCategories() {
	
		$this->log->debug("Projet::supprimerCategories() Début");
	
		// Obtenir la liste des catégories du projet
		$categorie = new Categorie($this->log, $this->dbh);
		$listeCategories = $categorie->getListeIdCategoriesDuProjet($this->get("id_projet"));
		
		// Parcourir la liste des catégories
		foreach ($listeCategories as $idCategorie) {
		
			// Charger et supprimer la catégorie
			$this->log->debug("Projet::supprimerItems() Suppression de la catégorie '$idCategorie'");
			$c = new Categorie($this->log, $this->dbh);
			$c->getCategorieParId($idCategorie, $this->get("id_projet"));
			$c->supprimer();
		}
	
		$this->log->debug("Projet::supprimerCategories() Fin");
	}	
	
	
	/**
	 *
	 * Supprimer les collections du projet
	 *
	 */
	private function supprimerCollections() {
	
		$this->log->debug("Projet::supprimerCollections() Début");
	
		// Obtenir la liste des collections du projet
		$collection = new Collection($this->log, $this->dbh);
		$listeCollections = $collection->getListeIdCollectionsDuProjet($this->get("id_projet"));
		
		// Parcourir la liste des collections
		foreach ($listeCollections as $idCollection) {
		
			// Charger et supprimer la collection
			$this->log->debug("Projet::supprimerCollections() Suppression de la collection '$idCollection'");
			$c = new Collection($this->log, $this->dbh);
			$c->getCollectionParId($idCollection, $this->get("id_projet"));
			$c->supprimer();
		}
	
		$this->log->debug("Projet::supprimerCollections() Fin");
	}	

	
	/**
	 *
	 * Supprimer les questionnaires du projet
	 *
	 */
	private function supprimerQuestionnaires() {
	
		$this->log->debug("Projet::supprimerQuestionnaires() Début");
	
		// Obtenir la liste des questionnaires du projet
		$questionnaire = new Questionnaire($this->log, $this->dbh);
		$listeQuestionnaires = $questionnaire->getListeIdQuestionnairesDuProjet($this->get("id_projet"));
		
		// Parcourir la liste des questionnaires
		foreach ($listeQuestionnaires as $idQuest) {
		
			// Charger et supprimer le questionnaire
			$this->log->debug("Projet::supprimerQuestionnaires() Suppression du questionnaire '$idQuest'");
			$q = new Questionnaire($this->log, $this->dbh);
			$q->getQuestionnaireParId($idQuest, $this->get("id_projet"));
			$q->supprimer();
		}
	
		$this->log->debug("Projet::supprimerQuestionnaires() Fin");
	}	
	

	/**
	 *
	 * Supprimer les langues du projet
	 *
	 */
	private function supprimerLangues() {
	
		$this->log->debug("Projet::supprimerLangues() Début");
	
		// Obtenir la liste des langues du projet
		$langue = new Langue($this->log, $this->dbh);
		$listeLangues = $langue->getListeIdLanguesDuProjet($this->get("id_projet"));
		
		// Parcourir la liste des langues
		foreach ($listeLangues as $idLangue) {
		
			// Charger et supprimer la langue
			$this->log->debug("Projet::supprimerLangues() Suppression de la langue '$idLangue'");
			$l = new Langue($this->log, $this->dbh);
			$l->getLangueParId($idLangue, $this->get("id_projet"));
			$l->supprimer();
		}
	
		$this->log->debug("Projet::supprimerLangues() Fin");
	}
		
	
	/**
	 * 
	 * Supprimer une projet
	 */
	public function supprimer() {
		
		$this->log->debug("Projet::supprimer() Début");
	
		try {
			
			// Supprimer tous les médias
			$this->log->debug("Projet::supprimer() Supprimer les médias du projet");
			$this->supprimerMedias();
			
			// Supprimer tous les items
			$this->log->debug("Projet::supprimer() Supprimer les items du projet");
			$this->supprimerItems();
			
			// Supprimer toutes les catégories
			$this->log->debug("Projet::supprimer() Supprimer les catégories du projet");
			$this->supprimerCategories();
			
			// Supprimer toutes les collections
			$this->log->debug("Projet::supprimer() Supprimer les collections du projet");
			$this->supprimerCollections();
			
			// Supprimer tous les questionnaires
			$this->log->debug("Projet::supprimer() Supprimer les questionnaires du projet");
			$this->supprimerQuestionnaires();
			
			// Supprimer les langues
			$this->log->debug("Projet::supprimer() Supprimer les langues du projet");
			$this->supprimerLangues();

			// Supprimer l'index existant pour le projet
			$this->log->debug("Projet::supprimer() Suppression des données d'index pour id_projet = '" . $this->get("id_projet") . "'");
			$sql = "delete from tprojet_index where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
			$this->log->debug("Projet::supprimer() Suppression de l'index complétée");
			
			// Supprimer le projet
			$this->log->debug("Projet::supprimer() Supprimer le projet '" . $this->get("id_projet") . "'");
			$sql = "delete from tprojet where id_projet= ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
			$this->log->debug("Projet::supprimer() Suppression du projet complété");
			
			// Supprimer le répertoire du projet s'il existe
			if ($this->get("repertoire") != "") {
				$repertoire = REPERTOIRE_PUB . $this->get("repertoire");
				$this->log->debug("Projet::supprimer() Suppression du répertoire de publication '$repertoire'");
				Fichiers::rmdirr($repertoire);
			} else	{
				$this->log->debug("Projet::supprimer() Aucun répertoire de publication à supprimer");
			}
			
			// Supprimer le répertoire média s'il existe
			if ($this->get("repertoire") != "") {
				$repertoire = REPERTOIRE_MEDIA . $this->get("repertoire");
				$this->log->debug("Projet::supprimer() Suppression du répertoire '$repertoire'");
				Fichiers::rmdirr($repertoire);
			} else	{
				$this->log->debug("Projet::supprimer() Aucun répertoire média à supprimer");
			}
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Projet::supprimer() Fin");
	}		


	/**
	 *
	 * Ajouter un collaborateur
	 * @param string courriel
	 *
	 */
	public function ajouterCollaborateur($courriel) {
	
		$this->log->debug("Projet::ajouterCollaborateur() Début Courriel : '$courriel'");
		
		// Préparer un jeton unique
		$this->set("jeton", substr(Securite::getHashMotPasse($courriel, Securite::genererChaineAleatoire()), 0, 16));
		
		try {
			
			// L'utilisateur n'a pas de compte dans nqw, enregistré la demande de collaboration et l'inviter
			
			// Préparer ajout
			$stmt = $this->dbh->prepare("insert into tcollaborateur (id_projet, collaborateur_courriel, jeton, date_creation, date_modification)
										 values (?, ?, ?, now(),now() )");
		
			// Insertion d'un enregistrement
			$stmt->execute(array($this->get("id_projet"), $courriel, $this->get("jeton")));
			
			// Envoi d'une invitation à l'utilisateur
			$this->envoiCourrielInvitationNouveauCollaborateur($courriel);
			
				
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::ajouterCollaborateur() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("Projet::ajouterCollaborateur() Fin");
	}					
	
	
	/**
	 *
	 * Envoi d'un message pour inviter un nouveau collaborateur
	 * @param String courriel
	 *
	 */
	public function envoiCourrielInvitationNouveauCollaborateur($courriel) {
	
		$this->log->debug("Projet::envoiCourrielInvitationNouveauCollaborateur() Début  courriel : '$courriel'");
	
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "collaborateur-nouveau-invitation.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courrielObj = new Courriel($this->log);
		$succes = $courrielObj->envoiCourriel($courriel, TXT_COURRIEL_COLLABORATEUR_INVITATION_OBJET, $contenu);
			
		$this->log->debug("Projet::envoiCourrielInvitationNouveauCollaborateur() Début");
	
		return $succes;
	}	
	
	/**
	 *
	 * Envoi d'un message pour inviter un collaborateur déjà inscrit dans nqw
	 * @param String courriel
	 *
	 */
	public function envoiCourrielInvitationCollaborateur($courriel) {
	
		$this->log->debug("Projet::envoiCourrielInvitationCollaborateur() Début  courriel : '$courriel'");
	
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "collaborateur-invitation.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courrielObj = new Courriel($this->log);
		$succes = $courrielObj->envoiCourriel($courriel, TXT_COURRIEL_COLLABORATEUR_INVITATION_OBJET, $contenu);
			
		$this->log->debug("Projet::envoiCourrielInvitationCollaborateur() Début");
	
		return $succes;
	}	
	
	
	/**
	 *
	 * Envoi d'un message pour retirer un collaborateur à un projet
	 * @param Usager objet 
	 *
	 */
	public function envoiCourrielRetraitCollaborateur($utilisateur) {
	
		$this->log->debug("Projet::envoiCourrielRetraitCollaborateur() Début  courriel : '" . $utilisateur->get("courriel") . "'");
	
		// Ajouter le prénom et nom pour le courriel
		$this->set("prenom", $utilisateur->get("prenom"));
		$this->set("nom", $utilisateur->get("nom"));
		
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "collaborateur-retrait.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courrielObj = new Courriel($this->log);
		$succes = $courrielObj->envoiCourriel($utilisateur->get("courriel"), TXT_COURRIEL_COLLABORATEUR_RETRAIT_OBJET, $contenu);
			
		$this->log->debug("Projet::envoiCourrielRetraitCollaborateur() Début");
	
		return $succes;
	}	
	
	
	/**
	 *
	 * Envoi d'un message pour informer un nouveau responsable
	 * @param String courriel
	 *
	 */
	public function envoiCourrielNouveauResponsable($courriel) {
	
		$this->log->debug("Projet::envoiCourrielNouveauResponsable() Début  courriel : '$courriel'");
	
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "projet-nouveau-responsable.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courrielObj = new Courriel($this->log);
		$succes = $courrielObj->envoiCourriel($courriel, TXT_COURRIEL_NOUVEAU_RESPONSABLE_OBJET, $contenu);
			
		$this->log->debug("Projet::envoiCourrielNouveauResponsable() Début");
	
		return $succes;
	}	
	

	/**
	 *
	 * Informer les collaborateurs des modifications effectuées
	 *
	 */
	public function envoiCourrielCollaborateursModifications() {
	
		$this->log->debug("Projet::envoiCourrielsCollaborateursModifications() Début");
	
		$modifications = "";
		
		// Vérifier quels champs ont été modifiés
		if ($this->get("titre") != $this->get("titreOrig")) {
			$this->log->debug("Projet::envoiCourrielsCollaborateursModifications() Titre modifié!");
			$modifications .= TXT_COURRIEL_PROJET_MODIFICATIONS_TITRE1 . $this->get("titreOrig") . TXT_COURRIEL_PROJET_MODIFICATIONS_TITRE2 . $this->get("titre") . TXT_COURRIEL_PROJET_MODIFICATIONS_TITRE3 . "\n";
		}
		
		if ($this->getResponsableProjet() != $this->get("responsableOrig")) {
			$this->log->debug("Projet::envoiCourrielsCollaborateursModifications() Responsable modifié!");
			$modifications .= TXT_COURRIEL_PROJET_MODIFICATIONS_RESPONSABLE1 . $this->get("responsableOrig") . TXT_COURRIEL_PROJET_MODIFICATIONS_RESPONSABLE2 . $this->getResponsableProjet() . TXT_COURRIEL_PROJET_MODIFICATIONS_RESPONSABLE3 . "\n";
		}
		
		if ($this->get("statut") != $this->get("statutOrig")) {
			$this->log->debug("Projet::envoiCourrielsCollaborateursModifications() Statut modifié!");
			if ($this->get("statut") == "1") {
				$modifications .= TXT_COURRIEL_PROJET_MODIFICATIONS_STATUT_ACTIF . "\n"; 
			} else {
				$modifications .= TXT_COURRIEL_PROJET_MODIFICATIONS_STATUT_INACTIF . "\n";
			}
		}

		// Vérifier si on doit informer les collaborateurs des modifications
		if ($modifications != "") {		
			// Ajouter la liste des modifications
			$this->set("modifications", $modifications);
			
			// Obtenir la liste des courriels des collaborateurs
			$listeCollaborateurs = $this->getCollaborateursActuels();
	
			// Envoi d'un courriel à chaque personne
			foreach ($listeCollaborateurs as $collaborateur) {
				
				// Ajouter le prénom et nom pour le courriel
				$this->set("prenom", $collaborateur['prenom']);
				$this->set("nom", $collaborateur['nom']);
				
				// Préparer le courriel
				$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "projet-modifications.php";
				
				// Vérifier si le fichier existe, sinon erreur
				if (!file_exists($gabaritCourriel)) {
					$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
				}
				
				// Obtenir le contenu
				$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
				
				// Envoi du courriel
				$courrielObj = new Courriel($this->log);
				$succes = $courrielObj->envoiCourriel($collaborateur['courriel'], TXT_COURRIEL_PROJET_MODIFICATIONS_OBJET, $contenu);
				
				$this->log->debug("Projet::envoiCourrielsCollaborateursModifications() Envoi d'un courriel pour avisé le collaborateur à l'adresse de courriel : '" . $collaborateur['courriel'] . "'");
			}
		}
		

		
		$this->log->debug("Projet::envoiCourrielsCollaborateursModifications() Fin");
		
	}
	
	
	/**
	 *
	 * Vérifier si la personne est un collaborateur invité
	 * @param string courriel
	 *
	 */
	public function isCollaborateurInvite($courriel) {
	
		$this->log->debug("Projet::isCollaborateurInvite() Début Courriel : '$courriel'");
		$trouve = false;
	
		try {
			
			$sql = "select collaborateur_courriel 
					from tcollaborateur 
					where id_projet = ?
					and collaborateur_courriel = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $courriel));
			
			// Vérifier qu'on a trouvé au moins un collaborateur
			if ($sth->rowCount() != 0) {
				$this->log->info("Projet::isCollaborateurInvite() Le courriel '$courriel' a déjà été invité");
				$trouve = true;
			} else {
				$this->log->info("Projet::isCollaborateurInvite() Le courriel '$courriel' n'a pas déjà été invité");
				$trouve = false;
			}
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::ajouterCollaborateur() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::isCollaborateurInvite() Fin");
		
		return $trouve;
	}	
	
	
	/**
	 *
	 * Vérifier le jeton et le projet
	 * @param string jeton
	 *
	 */
	public function verifierCollaborateurInvitationParJeton($jeton) {
	
		$this->log->debug("Projet::verifierCollaborateurInvitationParJeton() Début Jeton : '$jeton'");
		
		$reponse = -1;
		$idProjet = "";
	
		try {
							
			// Obtenir le projet auquel le collaborateur est invité
			$sql = "select id_projet
					from tcollaborateur
					where jeton = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($jeton));
				
			// Vérifier qu'on a trouvé au moins un jeton
			if ($sth->rowCount() == 1) {
				$this->log->info("Projet::verifierCollaborateurInvitationParJeton() Le jeton '$jeton' a été localisé");
				$trouve = true;
			} else {
				$reponse = 0;
			}
			
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$idProjet = $row['id_projet'];
			}

			// Si le projet existe, obtenir le statut
			if ($idProjet != "") {
				$prj = new Projet($this->log, $this->dbh);
				$prj->getProjetParId($idProjet);
				
				if ($prj->get("statut") == "1") {
					$reponse = 1;
				} else {
					$reponse = 2;
				}
			}
			
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::verifierCollaborateurInvitationParJeton() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::verifierCollaborateurInvitationParJeton() Fin");
	
		return $reponse;
	}
	
	
	/**
	 *
	 * Traiter la demande via le jeton
	 * @param string jeton
	 *
	 */
	public function traiterCollaborateurInvitationParJeton($idUsager, $jeton) {
	
		$this->log->debug("Projet::traiterCollaborateurInvitationParJeton() Début idUsager : '$idUsager' Jeton : '$jeton'");
	
		$trouve = false;
		$courriel = "";
	
		try {
				
			// Obtenir le courriel du collaborateur invité
			$sql = "select collaborateur_courriel
					from tcollaborateur
					where jeton = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($jeton));
	
			// Vérifier qu'on a trouvé au moins un jeton
			if ($sth->rowCount() == 1) {
				$this->log->info("Projet::traiterCollaborateurInvitationParJeton() Le jeton '$jeton' a été localisé");
				$trouve = true;
			} else {
				$this->log->info("Projet::traiterCollaborateurInvitationParJeton() Le jeton '$jeton' ne peut être localisé");
				$trouve = false;
			}
				
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$courriel = $row['collaborateur_courriel'];
			}
	
			if ($courriel != "") {
				// Obtenir la liste des projets pour la personne avec ce courriel
				$sql2 = "select id_projet, jeton
						from tcollaborateur
						where collaborateur_courriel = ?";
				$sth2= $this->dbh->prepare($sql2);
				$rows2 = $sth2->execute(array($courriel));
					
				while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC)) {
					$idProjet = $row2['id_projet'];
					$jeton = $row2['jeton'];
						
					// Charger le projet
					$prj = new Projet($this->log, $this->dbh);
					$prj->getProjetParId($idProjet);
	
					// Ajouter un rôle de collaborateur au projet
					$prj->ajouterRole($idUsager, Projet::ROLE_COLLABORATEUR);
	
					// Retirer l'invitation puisqu'elle a été acceptée
					$prj->supprimerCollaborateurInvitationParJeton($jeton);
				}
			}
				
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::traiterCollaborateurInvitationParJeton() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::traiterCollaborateurInvitationParJeton() Fin");
	
		return $courriel;
	}	
	
	/**
	 *
	 * Supprimer l'invitation pour le jeton X
	 * @param string jeton
	 *
	 */
	public function supprimerCollaborateurInvitationParJeton($jeton) {
	
		$this->log->debug("Projet::supprimerCollaborateurInvitationParJeton() Début Jeton : '$jeton'");

		try {
	
			$sql = "delete from tcollaborateur where jeton = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($jeton));
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::supprimerCollaborateurInvitationParJeton() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		$this->log->debug("Projet::supprimerCollaborateurInvitationParJeton() Fin");
	
		return;
	}	
		
	
	/**
	 *
	 * Vérifier si la personne est un collaborateur actuel
	 * @param string courriel
	 *
	 */
	public function isCollaborateurActuel($courriel) {
	
		$this->log->debug("Projet::isCollaborateurActuel() Début Courriel : '$courriel'");
		$trouve = false;
	
		try {

			// Préparer le SQL
			$sql = "select tusager.courriel
				from tusager, rprojet_usager_role
				where rprojet_usager_role.id_projet = ?
				and rprojet_usager_role.id_role = ?
				and tusager.id_usager = rprojet_usager_role.id_usager
		 		and tusager.courriel = ?";
		
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), PROJET::ROLE_COLLABORATEUR, $courriel));
		
			// Vérifier qu'on a trouvé au moins un collaborateur
			if ($sth->rowCount() != 0) {
				$this->log->info("Projet::isCollaborateurInvite() Le courriel '$courriel' a déjà été invité");
				$trouve = true;
			} else {
				$this->log->info("Projet::isCollaborateurInvite() Le courriel '$courriel' n'a pas déjà été invité");
				$trouve = false;
			}
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::ajouterCollaborateur() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Projet::isCollaborateurActuel() Fin");
	
		return $trouve;
	}	
	
	/**
	 *
	 * Supprimer une invitation à un collaborateur
	 * @param string courriel
	 *
	 */
	public function supprimerCollaborateurInvitation($courriel) {
	
		$this->log->debug("Projet::supprimerCollaborateurInvitation() Suppression de l'invitation pour le courriel : '$courriel' et projet : '" . $this->get("id_projet") . "'");
	
		try {
		
			// Préparer le SQL
			$sql = "delete from tcollaborateur
					where id_projet = ?
					and collaborateur_courriel = ?";
		
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $courriel));
		
		
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::supprimerCollaborateurInvitation() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}		
	
		$this->log->debug("Projet::supprimerCollaborateurInvitation() Fin");
		return;
	}
	
	
	
	/**
	 * 
	 * Préparer l'index de recherche
	 */
	protected function preparerIndex() {
		
		$this->log->debug("Projet::preparerIndex() Début");
		
		$index = "";
		$index .= TXT_PREFIX_PROJET . $this->get("id_projet") . " ";
		$index .= $this->get("titre") . " ";
		$index .= $this->get("description") . " ";

		$this->log->debug("Projet::preparerIndex() Fin");
		
		return $index;
	}

	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idUsager
	 */
	protected function updateIndex($index) {
		
		$this->log->debug("Projet::updateIndex() Début  index = '$index'");
		
		// Nettoyer la chaîne de recherche
		$index = Web::nettoyerChaineRech($index);
		
		try {
			// Supprimer l'index existant
			$sql = "delete from tprojet_index where id_projet = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet")));
			$this->log->debug("Projet::updateIndex() Suppression des données d'index pour id_projet = '" . $this->get("id_projet") . "'");
			$this->log->debug("Projet::updateIndex() Suppression complétée");
			
			// Insérer l'index
			$this->log->debug("Projet::updateIndex() Ajout des données d'index pour id_projet = '" . $this->get("id_projet") . "'");
			$sql = "insert into tprojet_index (id_projet, texte, date_creation, date_modification)
					values (?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_projet"), $index));
			$this->log->debug("Projet::updateIndex() Ajout complété");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		$this->log->debug("Projet::updateIndex() Fin");
	}		
	
	/**
	 * 
	 * Mettre à jour l'index de recherche
	 * @param String chaine
	 * @param String idUsager
	 */
	public function indexer() {
		
		$this->log->debug("Projet::indexer() Début");
		
		// Préparer l'index
		$index = $this->preparerIndex();
		
		// Mettre à jour l'index
		$this->updateIndex($index);
		
		$this->log->debug("Projet::indexer() Fin");
	}	
	
	
	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Projet::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_projet
					FROM 	tprojet";
	
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un projet
			if ($sth->rowCount() == 0) {
				$this->log->info("Projet::reindexer()  Aucun projet localisé");
			} else {
	
				// Récupérer les ids des projets et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
					// Récupérer l'id du projet
					$idProjet = $row['id_projet'];
						
					// Obtenir le projet
					$q = new Projet($this->log, $this->dbh);
					$q->getProjetParId($idProjet);
	
					// Réindexer
					$this->log->info("Projet::reindexer()  Indexation pour le projet '$idProjet'");
					$q->indexer();
					$this->log->info("Projet::reindexer()  Indexation complétée pour le projet '$idProjet'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Projet::reindexer() Fin");
		return $nbMAJ;
	}	
	
	
	/**
	 *
	 * Obtenir l'id du questionnaire à partir de la page demandée
	 * 
	 * @param String $page
	 *
	 */
	public function getIdProjetParPage($page) {
	
		$this->log->debug("Projet::getIdProjetParPage() Début");
	
		$idProjet = "";
		$pageCour = $page - 1;
	
		// Obtenir la position du projet dans les résultats
		$session = new Session();
		$listeProjets = $session->get("liste_projets");
	
		// Obtenir le nombre total de projets
		$pageTotal = count($listeProjets);
	
		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er projet
			$idProjet = $listeProjets[0];
		} else {
			$idProjet = $listeProjets[$pageCour];
		}
	
		$this->log->debug("Projet::getIdProjetParPage() Fin idProjet = '" . $idProjet . "'");
		return $idProjet;
	}	
	
	
	/**
	 *
	 * Obtenir l'id du questionnaire à partir de la page demandée pour tous les projets
	 * 
	 * @param String $page
	 *
	 */
	public function getIdProjetParPageTous($page) {
	
		$this->log->debug("Projet::getIdProjetParPage() Début");
	
		$idProjet = "";
		$pageCour = $page - 1;
	
		// Obtenir la position du projet dans les résultats
		$session = new Session();
		$listeProjets = $session->get("liste_projets_tous");
	
		// Obtenir le nombre total de projets
		$pageTotal = count($listeProjets);
	
		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er projet
			$idProjet = $listeProjets[0];
		} else {
			$idProjet = $listeProjets[$pageCour];
		}
	
		$this->log->debug("Projet::getIdProjetParPage() Fin idProjet = '" . $idProjet . "'");
		return $idProjet;
	}	
	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichageListe() {

		$this->log->debug("Projet::preparerAffichageListe() Début");

		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("projet_pref_tri_champ");
		$tri_ordre = $session->get("projet_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
				$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}

		$this->log->debug("Projet::preparerAffichageListe() Fin");		
		
		return;
	}	

	
	/**
	 * 
	 * Préparer les données pour le web 
	 *
	 */
	public function preparerAffichage() {

		$this->log->debug("Projet::preparerAffichage() Début");
		
		// Obtenir la position du projet dans les résultats
		$session = new Session();
		$listeProjets = $session->get("liste_projets");
		
		if ( is_array($listeProjets) ) { 
			$pageCour = array_search($this->get("id_projet"), $listeProjets);
		} else {
			$pageCour = 1;
		}
		
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
		
		// Obtenir le nombre total de projets
		$pageTotal = count($listeProjets);
		
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
			
		$this->log->debug("Projet::preparerAffichage() Fin");
	}	
	
	/**
	 *
	 * Préparer les données pour le web - affichage admin
	 *
	 */
	public function preparerAffichageAdmin() {
	
		$this->log->debug("Projet::preparerAffichageAdmin() Début");
	
		// Obtenir la position du projet dans les résultats
		$session = new Session();
		$listeProjets = $session->get("liste_projets_tous");
		
		if ( is_array($listeProjets) ) {
			$pageCour = array_search($this->get("id_projet"), $listeProjets);
		} else {
			$pageCour = 1;
		}
	
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
	
		// Obtenir le nombre total de projets
		$pageTotal = count($listeProjets);
	
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
			
		$this->log->debug("Projet::preparerAffichageAdmin() Fin");
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
	 * 
	 */
	public function set( $libelle, $valeur ) {
		$this->donnees[$libelle] = $valeur;
	}
	
	/**
	 *
	 * Supprimer une valeur
	 * 
	 * @param String $libelle
	 *
	 */
	public function delete( $libelle ) {
		unset ($this->donnees[$libelle]);
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
	
	
}

?>
