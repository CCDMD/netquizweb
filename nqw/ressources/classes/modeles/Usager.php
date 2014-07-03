<?php

/** 
 * Classe Usager
 * 
 * Représente un utilisateur de l'application
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Usager {
	
	const STATUT_ACTIF = "0";
	const STATUT_INACTIF = "1";
	const STATUT_A_APPROUVER = "2";
	const STATUT_REFUSE = "3";
	const STATUT_INCOMPLET = "4";
	const STATUT_SUPPRIME = "9";
	const STATUT_LISTE_USAGERS = "0,1,2,3,4";
	const ROLE_USAGER = "0";
	const ROLE_ADMINISTRATEUR = "1";
	
	protected $dbh;
	protected $log;
	protected $listeChamps = "id_usager, nom, prenom, courriel, code_usager, mot_passe, gds_secret, nb_mauvais_essais, langue_interface,
							  dern_nouv_consultee, pref_message, pref_nb_elem_page, pref_projet, pref_apercu_langue, pref_apercu_theme, code_rappel, 
							  role, statut, date_creation, date_modification, date_dern_authentification";
	
	protected $donnees;
	
	/**
	 * 
	 * Constructeur
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function __construct( Log $log, PDO $dbh ) {

		$this->dbh = $dbh;
		$this->log = $log;
		
		return;
	}
	

	/**
	 * 
	 * Authentifier l'usager par code usager / courriel et mot de passe
	 * 
	 * @param string $codeUsager
	 * @param string $motPasse
	 * 
	 */
	public function authentifier($codeUsager, $motPasse) {
		$this->log->info("Usager::authentifier() Début");
		
		$isAuthentifie = false;
		
		// Vérifier si un courriel est passé en paramètre
		if ($codeUsager != "" && $motPasse != "") {
	
			// Déterminer si on authentifie par courriel ou usager
			if( strpos($codeUsager, '@') > 0 ) {
				// Obtenir l'usager par courriel				
				$this->getUsagerParCourriel($codeUsager);
			} else {
				// Obtenir l'usager par code usager 
				$this->getUsagerParCodeUsager($codeUsager);
			}
			
			// Vérifier le mot de passe si un usager a été trouvé
			$this->log->info("Usager::authentifier() Vérifier le hash du mot de passe");
			if ( $this->get("id_usager") != "") {
				if ( $this->get("mot_passe") == Securite::getHashMotPasse($motPasse, $this->get("gds_secret")) ) {
					$isAuthentifie = true;
				} else {
					// Mécanique à confirmer
					$this->enregistrerMauvaisEssaiMDP();	
				}
			}
		} else {
			$this->log->info("Usager::authentifier() Code usager vide");
		}
		
		$this->log->info("Usager::authentifier() Fin");
		return $isAuthentifie;	
	}
	
	
	/**
	 * 
	 * Obtenir les informations d'un usager en effectuant une recherche sur le code usager
	 * 
	 * @param String $codeUsager
	 * 
	 */
	public function getUsagerParCodeUsager( $codeUsager) {

		$this->log->debug("Usager::getUsagerParCodeUsager() Début  codeUsager : '$codeUsager'");
		$trouve = false;
		
		try {
			$sql = "SELECT " . $this->listeChamps . " from tusager where code_usager = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($codeUsager));
			
			// Vérifier qu'on a trouvé au moins un usager	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun usager trouvé pour le code usager '$codeUsager'");
			}
			
			// Vérifier qu'un seul usager est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('003', "La recherche pour le code usager '$codeUsager' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour l'usager
				$result = $sth->fetchAll();
			
				foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->donnees[$cle] = $row[$cle];
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
				
		        // Indiquer qu'un et un seul usager a été trouvé
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getUsagerParCodeUsager() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Nom et prénom
		$this->set("nom_prenom", $this->get("nom") . " " . $this->get("prenom"));
		
		// Terminé
		$this->log->debug("Usager::getUsagerParCodeUsager() Trouve = '$trouve'");
		$this->log->debug("Usager::getUsagerParCodeUsager() Fin");
		return $trouve;		
	}
	

	/**
	 *
	 * Obtenir la liste des codes utilisateurs à approuver
	 *
	 */
	public function getListeUsagerApprouver() {
	
		$this->log->debug("Usager::getListeUsagerApprouver() Début ");
		$trouve = false;
		$listeUsagers = array();
	
		try {
			$sql = "SELECT " . $this->listeChamps . " from tusager where statut = 2";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
				
			// Vérifier qu'on a trouvé au moins un usager
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun utilisateur à approuver");
			} else {
				// Récupérer les informations pour l'usager
				$result = $sth->fetchAll();
					
				foreach($result as $row) {
	
					$u = new Usager($this->log, $this->dbh);
					$cles = array_keys($row);
	
					foreach ($cles as $cle) {

						// Obtenir chaque champ
						if (! is_numeric($cle) ) {
							$u->donnees[$cle] = $row[$cle];
							//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
						}
					}
					
					// Ajouter à la liste
					array_push($listeUsagers, $u);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getListeUsagerApprouver() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Usager::getListeUsagerApprouver() Fin");
		return $listeUsagers;
	}	

	
	/**
	 *
	 * Obtenir la liste des administrateurs
	 *
	 */
	public function getListeAdministrateurs() {
	
		$this->log->debug("Usager::getListeAdministrateurs() Début ");
		$trouve = false;
		$listeUsagers = array();
	
		try {
			$sql = "SELECT " . $this->listeChamps . " from tusager where role = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array(Usager::ROLE_ADMINISTRATEUR));
	
			// Vérifier qu'on a trouvé au moins un usager
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun utilisateur à approuver");
			} else {
				// Récupérer les informations pour l'usager
				$result = $sth->fetchAll();
					
				foreach($result as $row) {
	
					$u = new Usager($this->log, $this->dbh);
					$cles = array_keys($row);
	
					foreach ($cles as $cle) {
	
						// Obtenir chaque champ
						if (! is_numeric($cle) ) {
							$u->donnees[$cle] = $row[$cle];
							//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
						}
					}
						
					// Ajouter à la liste
					array_push($listeUsagers, $u);
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getListeAdministrateurs() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Usager::getListeAdministrateurs() Fin");
		return $listeUsagers;
	}
	
	
	/**
	 *
	 * Obtenir le nombre d'admin
	 *
	 */
	public function getNombreAdmin() {
	
		$this->log->debug("Usager::getNombreAdmin() Début");
	
		$total = 0;
	
		try {
			$sql = "select count(*) from tusager where role = ? and statut = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array(USAGER::ROLE_ADMINISTRATEUR,USAGER::STATUT_ACTIF));
				
			// Obtenir le nombre d'administrateurs actifs
			$total = $sth->fetchColumn();
				
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getNombreAdmin() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Usager::getNombreAdmin() Fin total = '$total'");
	
		return $total;
	}	
	
	
	/**
	 *
	 * Obtenir la liste des id usagers
	 *
	 */
	public function getListeUsagers($filtreActif = false, $filtreStatut) {
	
		$this->log->debug("Usager::getListeUsagers() Début ");
		$trouve = false;
		$listeUsagers = array();
		$listeIdUsagers = array();
	
		// Obtenir le tri
		$tri = $this->getTri();
		
		try {
			$sql = "SELECT 	id_usager, concat(nom, ', ', prenom) as nom_prenom, courriel, role, 
					 		(select count(id_projet) from rprojet_usager_role where rprojet_usager_role.id_usager = tusager.id_usager) as nb_projets,
							statut, date_dern_authentification 
					FROM 	tusager
					WHERE   statut != 9
					ORDER BY $tri";
			
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un usager
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun usager localisé");
			} else {
				
				// Récupérer les ids des projets
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					
					// Appliquer le filtre pour administrateurs
					if ($filtreStatut == "admin" && $row['role'] != "1") {
						continue;
					}	
					
					// Appliquer le filtre par statut
					if ($filtreStatut != "admin" && $filtreActif && $filtreStatut != "" && $row['statut'] != $filtreStatut) {
						continue;
					}
					
					$u = new Usager($this->log, $this->dbh);
					$u->set("id_usager", $row['id_usager']);
					$u->set("nom_prenom", $row['nom_prenom']);
					$u->set("courriel", $row['courriel']);
					$u->set("nb_projets", $row['nb_projets']);
					$u->set("statut", $row['statut']);
					$u->set("date_dern_authentification", $row['date_dern_authentification']);
					array_push($listeUsagers, $u);
					array_push($listeIdUsagers, $u->get("id_usager"));
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getListeUsagers() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		// Sauvegarder la liste des ids en session
		$session = new Session();
		$session->set("liste_usagers", $listeIdUsagers);
		
		// Terminé
		$this->log->debug("Usager::getListeUsagers() Fin");
		return $listeUsagers;
	}	

	/**
	 * 
	 * Obtenir les informations d'un usager en effectuant une recherche sur l'id usager
	 * @param String $codeUsager
	 */
	public function getUsagerParIdUsager($idUsager) {

		$this->log->debug("Usager::getUsagerParIdUsager() Début  idUsager : '$idUsager'");
		$trouve = false;
		
		try {
			$sql = "SELECT " . $this->listeChamps . " from tusager where id_usager = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($idUsager));
			
			// Vérifier qu'on a trouvé au moins un usager	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun usager trouvé pour l'id usager '$idUsager'");
			}
			
			// Vérifier qu'un seul usager est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('003', "La recherche pour l'id usager '$idUsager' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour l'usager
				$result = $sth->fetchAll();
			
				foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->donnees[$cle] = $row[$cle];
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
				
		        // Indiquer qu'un et un seul usager a été trouvé
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getUsagerParIdUsager() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}			
		
		// Nom et prénom
		$this->set("nom_prenom", $this->get("nom") . " " . $this->get("prenom"));
		
		// Préparer le titre, utilisé dans la corbeille
		$this->set("titre", $this->get("nom") . ", " . $this->get("prenom"));
		
		// Dernier accès par défaut
		if ($this->get("date_dern_authentification") == "") {
			$this->set("date_dern_authentification","-");
		}
		
		// Terminé
		$this->log->debug("Usager::getUsagerParIdUsager() Trouve = '$trouve'");
		$this->log->debug("Usager::getUsagerParIdUsager() Fin");
		return $trouve;		
	}	
	
	/**
	 * 
	 * Obtenir les informations d'un usager en effectuant une recherche sur le code usager
	 * @param $courriel
	 */
	public function getUsagerParCourriel($courriel) {

		$this->log->debug("Usager::getUsagerParCourriel() Début  courriel : '$courriel'");
		$trouve = false;
		
		try {
			$sql = "SELECT " . $this->listeChamps . " from tusager where courriel = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($courriel));
			
			// Vérifier qu'on a trouvé au moins un usager	
			if ($sth->rowCount() == 0) {
				$this->log->info("Aucun usager trouvé pour le courriel '$courriel'");
			}
			
			// Vérifier qu'un seul usager est retourné, sinon erreur
			elseif ($sth->rowCount() > 1) {
				Erreur::erreurFatal('003', "La recherche pour le courriel '$courriel' a retourné plus d'un résultat", $this->log);			
			}
			
			else {
				// Récupérer les informations pour l'usager
				$result = $sth->fetchAll();
			
				foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {
				    	// Obtenir chaque champ
				    	if (! is_numeric($cle) ) {
				    		$this->donnees[$cle] = $row[$cle];
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    	}
			    	}
		        }
		        
		        // Indiquer qu'un et un seul usager a été trouvé
		        $trouve = true;
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::getUsagerParCourriel() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}	
		
		// Nom et prénom
		$this->set("nom_prenom", $this->get("nom") . " " . $this->get("prenom"));
				
		// Terminé
		$this->log->debug("Usager::getUsagerParCourriel() Trouve = '$trouve'");
		$this->log->debug("Usager::getUsagerParCourriel() Fin");
		return $trouve;		
	}	
	
	
	
	/**
	 *
	 * Obtenir l'ordre de tri de la liste des Projets
	 */
	public function getTri() {
	
		$this->log->debug("Usager::getTri() Début");
	
		$session = new Session();
	
		// Vérifier si un tri est spécifié dans la session
		$triSessionChamp = $session->get("usager_pref_tri_champ");
		$triSessionOrdre = $session->get("usager_pref_tri_ordre");
		$this->log->debug("Usager::getTri() triSessionChamp = '$triSessionChamp'");
		$this->log->debug("Usager::getTri() triSessionOrdre = '$triSessionOrdre'");
	
		// Vérifier si l'ordre de tri désiré est passé en paramètre
		$triParamChamp = Web::getParam("tri");
		$triParamOrdre = "";
	
		// Vérifier si l'ordre demandé est disponible
		if ($triParamChamp != "") {
			$listeValeurs = array("id_usager", "nom_prenom", "courriel", "nb_projets", "statut", "date_dern_authentification");
			if ( !Securite::verifierValeur( $triParamChamp, $listeValeurs) ) {
				$triParamChamp = "id_usager";
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
			$triParamChamp = "id_usager";
			$triParamOrdre = "asc";
		}
	
		// Stocker le tri dans la session
		$session->set("usager_pref_tri_champ", $triParamChamp);
		$session->set("usager_pref_tri_ordre", $triParamOrdre);
	
		$this->log->debug("Usager::getTri() Fin");
	
		return $triParamChamp . " " . $triParamOrdre;
	}	
	
	/**
	 *
	 * Ajouter un usager
	 * 
	 */
	public function ajouter() {
	
		$this->log->debug("Usager::ajouter() Début");
	
		try {
			
			$stmt = $this->dbh->prepare("insert into tusager (nom, prenom, courriel, code_usager, mot_passe, gds_secret, nb_mauvais_essais, langue_interface,
										 dern_nouv_consultee, pref_message, pref_nb_elem_page, pref_projet, pref_apercu_langue, pref_apercu_theme, code_rappel, role, statut, date_creation, date_modification)
										 values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now() )");
				
			// Insertion d'un usager
			$stmt->execute( array(  $this->get("nom"),
					$this->get("prenom"),
					$this->get("courriel"),
					$this->get("code_usager"),
					$this->get("mot_passe"),
					$this->get("gds_secret"),
					(int)$this->get("nb_mauvais_essais"),
					$this->get("langue_interface"),
					(int)$this->get("dern_nouv_consultee"),
					$this->get("pref_message"),
					(int)$this->get("pref_nb_elem_page"),
					$this->get("pref_projet"),
					(int)$this->get("pref_apercu_langue"),
					$this->get("pref_apercu_theme"),
					$this->get("code_rappel"),
					$this->get("role"),
					(int)$this->get("statut"),
			) );
			
			// Obtenir l'ID
			$this->donnees['id_usager'] = $this->dbh->lastInsertId('id_usager');
			$this->log->debug("Usager::ajouter() Nouveal usager créé (id = '" . $this->get('id_usager') . "')");
				
			
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}

		// Mettre à jour l'index
		$this->indexer();		
		
		$this->log->debug("Usager::ajouter() Fin");
		
		return;
	}
		
	
	/**
	 *
	 * Enregistrer l'usager
	 * 
	 */
	public function enregistrer() {

		$this->log->debug("Usager::enregistrer() Début");
				
		try {
			$stmt = $this->dbh->prepare("update tusager set 
										nom=?,
										prenom=?,
										courriel=?,
										code_usager=?,
										mot_passe=?,
										gds_secret=?,
										nb_mauvais_essais=?,
										langue_interface=?,
								  		dern_nouv_consultee=?,
								  		pref_message=?,
								  		pref_nb_elem_page=?,
										pref_projet=?,
										pref_apercu_langue=?,
										pref_apercu_theme=?,
								  		code_rappel=?,
										role=?,
								  		statut=?,
								  		date_creation=?,
								  		date_dern_authentification=?,
										date_modification=now()										
										where id_usager = ?");
			
			// insertion d'une ligne
			$stmt->execute( array(  $this->get("nom"),
									$this->get("prenom"),
									$this->get("courriel"),
									$this->get("code_usager"),
									$this->get("mot_passe"),
									$this->get("gds_secret"),
									(int)$this->get("nb_mauvais_essais"),
									$this->get("langue_interface"),
									(int)$this->get("dern_nouv_consultee"),
									$this->get("pref_message"),
									(int)$this->get("pref_nb_elem_page"),
									$this->get("pref_projet"),
									(int)$this->get("pref_apercu_langue"),
									$this->get("pref_apercu_theme"),
									$this->get("code_rappel"),
									$this->get("role"),
									(int)$this->get("statut"),
									$this->get("date_creation"),
									$this->get("date_dern_authentification"),
									$this->get("id_usager")
									) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}									

		// Mettre à jour l'index
		$this->indexer();		
		
		$this->log->debug("Usager::enregistrer() Fin");								
								
		return;
	}	
	
	
	/**
	 *
	 * Mettre à jour l'index de recherche
	 * 
	 */
	public function indexer() {
	
		$this->log->debug("Usager::indexer() Début");
	
		// Préparer l'index
		$index = $this->preparerIndex();
	
		// Mettre à jour l'index
		$this->updateIndex($index);
	
		$this->log->debug("Usager::indexer() Fin");
	}
		

	/**
	 *
	 * Préparer l'index de recherche
	 */
	protected function preparerIndex() {
	
		$this->log->debug("Usager::preparerIndex() Début");
	
		$index = "";
		$index .= TXT_PREFIX_USAGER . $this->get("id_usager") . " ";
		$index .= $this->get("nom") . " ";
		$index .= $this->get("prenom") . " ";
		$index .= $this->get("courriel") . " ";
		$index .= $this->get("code_usager") . " ";
	
		$this->log->debug("Usager::preparerIndex() Fin");
	
		return $index;
	}	
	
	
	/**
	 *
	 * Mettre à jour l'index de recherche
	 * 
	 * @param String $index
	 */
	protected function updateIndex($index) {
	
		$this->log->debug("Usager::updateIndex() Début  index = '$index'");
	
		// Nettoyer la chaîne de recherche
		$index = Web::nettoyerChaineRech($index);
	
		try {
			// Supprimer l'index existant
			$sql = "delete from tusager_index where id_usager = ?";
			$sth = $this->dbh->prepare($sql);
			$this->log->debug("Usager::updateIndex() Suppression des données d'index pour id_usager = '" . $this->get("id_usager") . "'");
			$rows = $sth->execute(array($this->get("id_usager")));
			$this->log->debug("Usager::updateIndex() Suppression complétée");
				
			// Insérer l'index
			$this->log->debug("Usager::updateIndex() Ajout des données d'index pour id_usager = '" . $this->get("id_usager") . "'");
			$sql = "insert into tusager_index (id_usager, texte, date_creation, date_modification)
					values (?, ?, now(), now())";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_usager"), $index));
			$this->log->debug("Usager::updateIndex() Ajout complété");
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Projet::updateIndex() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Usager::updateIndex() Fin");
	}	
	

	/**
	 *
	 * Mettre à jour les index
	 *
	 */
	public function reindexer() {
	
		$this->log->debug("Usager::reindexer() Début ");
	
		$nbMAJ = 0;
	
		try {
			$sql = "SELECT 	id_usager
					FROM 	tusager";
				
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute();
	
			// Vérifier qu'on a trouvé au moins un usager
			if ($sth->rowCount() == 0) {
				$this->log->info("Usager::reindexer()  Aucun usager localisé");
			} else {
	
				// Récupérer les ids des utilisateurs et réindexer les données
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
					
					// Récupérer l'usager
					$idUsager = $row['id_usager'];
					$u = new Usager($this->log, $this->dbh);
					$u->getUsagerParIdUsager($idUsager);
					
					// Réindexer
					$this->log->info("Usager::reindexer()  Indexation pour l'utilisateur '$idUsager'");
					$u->indexer();
					$this->log->info("Usager::reindexer()  Indexation complétée pour l'utilisateur '$idUsager'");
					$nbMAJ++;
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::reindexer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Terminé
		$this->log->debug("Usager::reindexer() Fin");
		return $nbMAJ;
	}	
	
	
	/**
	 *
	 * Effectuer une recherche dans les usagers
	 * 
	 * @param String chaine
	 * @param String filtreStatut
	 * @param String statut
	 */
	public function recherche($chaine, $filtreStatut, $statut) {
	
		$this->log->debug("Media::rechercheMedias() Début chaine = '$chaine'");
	
		$listeUsagers = array();
		$listeIdUsagers = array();
	
		// Préparer la chaîne de recherche
		$rech = '%' . Web::nettoyerChaineRech($chaine) . '%';
	
		// Obtenir le tri à utiliser
		$tri = $this->getTri();
	
		try {
			$sql = "SELECT 	tusager.id_usager, concat(tusager.nom, ', ', tusager.prenom) as nom_prenom, tusager.courriel, tusager.role, 
					 		(select count(rprojet_usager_role.id_usager) from rprojet_usager_role where rprojet_usager_role.id_usager = tusager.id_usager and id_role = 1) as nb_projets,
							tusager.statut, tusager.date_dern_authentification 
					FROM 	tusager, tusager_index
					WHERE   statut in ($statut)
					AND		tusager.id_usager = tusager_index.id_usager
					AND		tusager_index.texte like '$rech'
					ORDER BY $tri";
			
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($chaine));
				
			// Vérifier qu'on a trouvé au moins un usager
			if ($sth->rowCount() == 0) {
				$this->log->info("Usager::recherche() Aucune usager trouvé pour la recherche '$rech'");
			}
			else {
				// Récupérer les ids des medias
				while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		
					// Appliquer le filtre pour administrateurs
					if ($filtreStatut == "admin" && $row['role'] != "1") {
						continue;
					}	
					
					// Appliquer le filtre par statut
					if ($filtreStatut != "admin" && $filtreActif && $filtreStatut != "" && $row['statut'] != $filtreStatut) {
						continue;
					}
							
					$id = $row['id_usager'];
					
					$u = new Usager($this->log, $this->dbh);
					$u->set("id_usager", $row['id_usager']);
					$u->set("nom_prenom", $row['nom_prenom']);
					$u->set("courriel", $row['courriel']);
					$u->set("nb_projets", $row['nb_projets']);
					$u->set("statut", $row['statut']);
					$u->set("date_dern_authentification", $row['date_dern_authentification']);
					array_push($listeUsagers, $u);
					array_push($listeIdUsagers, $u->get("id_usager"));					
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::recherche() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		// Conserver la liste en session
		$session = new Session();
		$session->set("liste_usagers", $listeIdUsagers);

		// Terminé
		$this->log->debug("Usager::recherche() Fin");
		
		return $listeUsagers;
	}	
	
	/**
	 * 
	 * Obtenir les valeurs de l'utilisateur à partir de la requête web
	 * 
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {

		// Obtenir les paramètres
		$params = Web::getListeParam("usager_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'";
		}
		return;
	}	
	
	
	/**
	 *
	 * Activer le projet
	 *
	 */
	public function activer() {
	
		$this->log->debug("Usager::activer() Début");
	
		// Activer le suivi
		$this->set("statut", USAGER::STATUT_ACTIF);
	
		// Sauvegarder les données
		$this->enregistrer();
	
		$this->log->debug("Usager::activer() Fin");
	}	
	
	
	/**
	 *
	 * Désactiver l'utilisateur (mettre à la corbeille)
	 *
	 */
	public function desactiver() {
	
		$this->log->debug("Usager::desactiver()");
	
		$desactiver = 0;
		
		// Vérifier le nombre d'administrateur actif
		$nbAdminActif = $this->getNombreAdmin();
		
		if ($this->isAdmin()) {

			// Admin
			if ($nbAdminActif > 1) {
				$desactiver = 1;
			}
			
		} else {
			// Utilisateur normal, on désactive
			$desactiver = 1;
		}
		
		if ($desactiver == 1) {
			$this->set("statut", Usager::STATUT_SUPPRIME);
			$this->enregistrer();
			$conf = 1;
		}
	
		$this->log->debug("Usager::desactiver() Fin");
		
		return $desactiver;
	}
		
	
	/**
	 *
	 * Supprimer un usager
	 * 
	 */
	public function supprimer() {
	
		$this->log->debug("Usager::supprimer() Début");
	
		try {
	
			// Supprimer le répertoire de publication de l'usager
			$repertoireDestinationUsager = REPERTOIRE_PUB . Securite::nettoyerNomfichier($this->get("code_usager")) . "/";
			if (is_dir($repertoireDestinationUsager)) {
				Fichiers::rmdirr($repertoireDestinationUsager);				
			}
			
			// Supprimer l'index existant pour l'utilisateur
			$this->log->debug("Usager::updateIndex() Suppression des données d'index pour id_usager = '" . $this->get("id_usager") . "'");
			$sql = "delete from tusager_index where id_usager = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_usager")));
			$this->log->debug("Usager::updateIndex() Suppression de l'index complétée");
				
			// Supprimer l'utilisateur
			$this->log->debug("Usager::supprimer() Supprimer l'utilisateur '" . $this->get("id_usager") . "'");
			$sql = "delete from tusager where id_usager = ?";
			$sth = $this->dbh->prepare($sql);
			$rows = $sth->execute(array($this->get("id_usager")));
			$this->log->debug("Projet::supprimer() Suppression de l'utilisateur complété");
				
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::supprimer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Usager::supprimer() Fin");
	}	
	
	
	/**
	 * 
	 * Vérifier les informations du profil
	 * 
	 */
	public function verifierProfil() {
		
		$this->log->debug("Usager::verifierProfil() Début");
		
		$erreurs = "";
		
		// Raccourcis pour les champs
		$code = trim($this->get("code_usager"));
		$nom = trim($this->get("nom"));
		$prenom = trim($this->get("prenom"));
		$courriel = trim($this->get("courriel"));

		// Vérifier le code usager
		if ($code == "" || strlen($code) > 150 || !$this->verifierCodeUsager() ) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_176 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le prénom
		if ($prenom == "" || strlen($prenom) > 150) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_022 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le nom
		if ($nom == "" || strlen($nom) > 150) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_021 . HTML_LISTE_ERREUR_FIN;
		}

		// Vérifier le courriel
		if ($courriel == "" || strlen($courriel) > 250 || !filter_var($courriel, FILTER_VALIDATE_EMAIL) ) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_023 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Conserver les versions trim
		$this->set("nom", $nom);
		$this->set("prenom", $prenom);
		$this->set("courriel", $courriel);
		
		$this->log->debug("Usager::verifierProfil() Fin");
		
		return $erreurs;
		
	}	
	
	/**
	 * 
	 * Vérifier les informations des mots de passe
	 * 
	 */
	public function verifierChangementMDP() {
		
		$this->log->debug("Usager::verifierMDP() Début");
		
		$erreurs = "";
		
		// Raccourcis pour les champs
		$mdpActuel = trim($this->get("mdp_actuel"));
		$mdpNouv = trim($this->get("mdp_nouv"));
		$mdpConf = trim($this->get("mdp_conf"));
		
		// Vérifier le mot de passe actuel
		if ($mdpActuel == "" || strlen($mdpActuel) > 250 || ! $this->authentifier($this->get("code_usager"), $mdpActuel) ) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_024 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier le nouveau mot de passe
		if ($mdpNouv == "" || strlen($mdpNouv) > 250 || ! Securite::isMotPasseValide($mdpNouv)) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_025 . HTML_LISTE_ERREUR_FIN;
		}
		
		// Vérifier que le nouveau mot de passe est différent de l'ancien seulement si l'usager connait le mot de passe actuel (pour éviter des attaques
		if ($erreurs == "") {
			$mdpNouvHash = Securite::getHashMotPasse($mdpNouv, $this->get("gds_secret")); 
			if ($mdpNouvHash == $this->get("mot_passe")) {
				$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_027 . HTML_LISTE_ERREUR_FIN;
			}
		}

		// Vérifier la confirmation du mot de passe
		if ($mdpConf == "" || strlen($mdpConf) > 250 || $mdpNouv != $mdpConf) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_026 . HTML_LISTE_ERREUR_FIN;
		}		

		// Si aucune erreur, remplacer le mot de passe actuel par le nouveau
		if ($erreurs == "") {
			$this->set("mot_passe", $mdpNouvHash);
		}
		
		$this->log->debug("Usager::verifierMDP() Fin");
		
		return $erreurs;
		
	}		
	
	/**
	 *
	 * Vérifier les informations pour le choix d'un nouveau de mot de passe
	 *
	 */
	public function verifierChoixNouveauMDP() {
	
		$this->log->debug("Usager::verifierNouveauMDP() Début");
	
		$erreurs = "";
	
		// Raccourcis pour les champs
		$mdpNouv = trim($this->get("mdp_nouv"));
		$mdpConf = trim($this->get("mdp_conf"));
	
		// Vérifier le nouveau mot de passe
		if ($mdpNouv == "" || strlen($mdpNouv) > 250 || ! Securite::isMotPasseValide($mdpNouv)) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_025 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Vérifier la confirmation du mot de passe
		if ($mdpConf == "" || strlen($mdpConf) > 250 || $mdpNouv != $mdpConf) {
			$erreurs .= HTML_LISTE_ERREUR_DEBUT . ERR_026 . HTML_LISTE_ERREUR_FIN;
		}
	
		// Si aucune erreur, remplacer le mot de passe actuel par le nouveau
		if ($erreurs == "") {
			$mdpNouvHash = Securite::getHashMotPasse($mdpNouv, $this->get("gds_secret"));
			$this->set("mot_passe", $mdpNouvHash);
				
			// Supprimer le code de rappel
			$this->set("code_rappel","");
		}
	
		$this->log->debug("Usager::verifierNouveauMDP() Fin");
	
		return $erreurs;
	}	
	
	
	/**
	 *
	 * Vérifier le format du code usager
	 *
	 */
	public function verifierCodeUsager() {
	
		$this->log->debug("Usager::verifierCodeUsager() Début");
		
		$valide = true;

		// Vérifier la longueur minimale
		if (strlen($this->get("code_usager")) < SECURITE_CODEUSAGER_LONGUEUR_MIN) {
			$valide = false;
		}
		
		if (!preg_match('/^[A-Za-z0-9_\-]+$/u', $this->get("code_usager"))) {
			$valide = false;
		}
				
		$this->log->debug("Usager::verifierCodeUsager() Fin");
		
		return $valide;
	}
	
	
		
	
	/**
	 * 
	 * Envoi rappel code utilisateur + lien mdp
	 * 
	 */
	public function envoiCourrielRappel() {
		
		$this->log->debug("Usager::envoiCourrielRappel() Début");
		
		$succes = 0;
		
		// Préparer le code de rappel pour l'utilisateur
		$this->set("code_rappel", Securite::genererChaineAleatoire(16));
		$this->enregistrer();
		
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "identification-rappel.php";
		
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
		
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
		
		// Envoi du courriel
		$courriel = new Courriel($this->log);
		$succes = $courriel->envoiCourriel($this->get("courriel"), TXT_COURRIEL_RAPPEL_OBJET, $contenu);
				 
		$this->log->debug("Usager::envoiCourrielRappel() Début");
		
		return $succes;
	}
	

	/**
	 *
	 * Envoi d'un message de confirmation pour l'accès au compte
	 *
	 */
	public function envoiCourrielCompteApprobation() {
	
		$this->log->debug("Usager::envoiCourrielCompteApprobation() Début");
	
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "compte-approbation.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courriel = new Courriel($this->log);
		$succes = $courriel->envoiCourriel($this->get("courriel"), TXT_COURRIEL_APPROBATION_OBJET, $contenu);
			
		$this->log->debug("Usager::envoiCourrielCompteApprobation() Début");
	
		return $succes;
	}	
	
	/**
	 *
	 * Envoi d'un message de refus pour l'accès au compte
	 *
	 */
	public function envoiCourrielCompteRefus() {
	
		$this->log->debug("Usager::envoiCourrielCompteRefus() Début");
	
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "compte-refus.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courriel = new Courriel($this->log);
		$succes = $courriel->envoiCourriel($this->get("courriel"), TXT_COURRIEL_REFUS_OBJET, $contenu);
			
		$this->log->debug("Usager::envoiCourrielCompteRefus() Début");
	
		return $succes;
	}	
	
	
	/**
	 *
	 * Envoi d'un message de confirmation pour la création d'un compte admin
	 *
	 */
	public function envoiCourrielCreationCompteAdmin() {
	
		$this->log->debug("Usager::envoiCourrielCreationCompteAdmin() Début");
	
		// Préparer le courriel
		$gabaritCourriel = REPERTOIRE_GABARITS_COURRIELS . "compte-admin.php";
	
		// Vérifier si le fichier existe, sinon erreur
		if (!file_exists($gabaritCourriel)) {
			$this->log->erreur("Le gabarit du courriel '$gabaritCourriel' ne peut être localisé.");
		}
	
		// Obtenir le contenu
		$contenu = Fichiers::getContenuElement($gabaritCourriel , $this);
	
		// Envoi du courriel
		$courriel = new Courriel($this->log);
		$succes = $courriel->envoiCourriel($this->get("courriel"), TXT_COURRIEL_CREATION_COMPTE_ADMIN, $contenu);
			
		$this->log->debug("Usager::envoiCourrielCreationCompteAdmin() Début");
	
		return $succes;
	}	
	
	/**
	 * 
	 * Enregistrer un mauvais essai pour le mdp
	 * 
	 */
	public function enregistrerMauvaisEssaiMDP() {
		
		$this->log->debug("Usager::enregistrerMauvaisEssaiMDP() Début");
		
		// Incrémenter le compteur
		$nbEssais = $this->get("nb_mauvais_essais");
		$nbEssais++;
		$this->set("nb_mauvais_essais", $nbEssais);
		
		// Si plus du nombre d'essais maximum verrouiller le compte
		if ($nbEssais > SECURITE_NB_MAUVAIS_ESSAIS_VERROUILLAGE) {
			$this->set("statut", 1);	
		}
		
		// Enregistrer
		$this->enregistrer();
				
		$this->log->debug("Usager::enregistrerMauvaisEssaiMDP() Fin");
	}

	
	/**
	 *
	 * Enregistrer une authentification réussie
	 *
	 */
	public function enregistrerAuthentificationReussie() {
	
		$this->log->debug("Usager::enregistrerAuthentificationReussie() Début");
		
		try {
			$stmt = $this->dbh->prepare("update tusager set
										nb_mauvais_essais=0, 
								  		date_dern_authentification=now()
					
										where id_usager = ?");
			
			// insertion d'une ligne
			$stmt->execute( array(  $this->get("id_usager") ) );
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Usager::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}										
		$this->log->debug("Usager::enregistrerAuthentificationReussie() Fin");
	}	
	
	
	/**
	 *
	 * Obtenir l'id de l<utilisateur à partir de la page demandée pour tous les utilisateurs
	 *
	 */
	public function getIdUsagerParPage($page) {
	
		$this->log->debug("Projet::getIdUsagerParPageTous() Début");
	
		$idUsager = "";
		$pageCour = $page - 1;
	
		// Obtenir la position du projet dans les résultats
		$session = new Session();
		$listeUsagers = $session->get("liste_usagers");
		
		// Obtenir le nombre total d'utilisateurs
		$pageTotal = count($listeUsagers);
	
		// Vérifier l'intervalle
		if ($pageCour < 1 || $pageCour >= $pageTotal) {
			// Par défaut retourner le 1er utilisateur
			$idUsager = $listeUsagers[0];
		} else {
			$idUsager = $listeUsagers[$pageCour];
		}
	
		$this->log->debug("Projet::getIdUsagerParPageTous() Fin idUsager = '" . $idUsager . "'");
		return $idUsager;
	}	
	
	
	/**
	 *
	 * Obtenir le filtre pour le statut
	 *
	 */
	public function getFiltreStatut() {
	
		$this->log->debug("Projet::getFiltreStatut() Début");
	
		$session = new Session();
	
		// Vérifier si un filtre est spécifié dans la session
		$filtreStatut = $session->get("pref_filtre_statut");
	
		// Vérifier si un filtre est passé en paramètre
		$filtreStatutParam = Web::getParam("statut");
		
		// Déterminer si on utilise la valeur passé en paramètre
		if ($filtreStatutParam != "") {
			
			// Si l'utilisateur veut voir toutes les collections enlever le filtre
			if ($filtreStatutParam == "tous") {
				$session->delete("pref_filtre_statut");
				$filtreStatut = "";
			} else {
				// Stocker le tri dans la session
				$session->set("pref_filtre_statut", $filtreStatutParam);
				$filtreStatut = $filtreStatutParam;
			}
		}
	
		$this->log->debug("Projet::getFiltreStatut() Fin");
		
		return $filtreStatut;
	}	
	
	/**
	 *
	 * Préparer les données pour le web
	 *
	 */
	public function preparerAffichageListe() {
	
		$this->log->debug("Usager::preparerAffichageListe() Début");
	
		// Préparer les classes pour le tri
		$session = new Session();
		$tri_champ = $session->get("usager_pref_tri_champ");
		$tri_ordre = $session->get("usager_pref_tri_ordre");
			
		if ($tri_ordre == "asc") {
			$this->set('tri_' . $tri_champ,  "triAsc");
		} elseif ($tri_ordre = "desc") {
			$this->set('tri_' . $tri_champ,  "triDesc");
		}
	
		$this->log->debug("Usager::preparerAffichageListe() Fin");
	
		return;
	}	
	
	
	/**
	 *
	 * Préparer les données pour la navigation
	 *
	 */
	public function preparerAffichage() {
	
		$this->log->debug("Projet::preparerAffichage() Début");
	
		// Obtenir la position de l'utilisateur dans les résultats
		$session = new Session();
		$listeUsagers = $session->get("liste_usagers");
	
		if ( is_array($listeUsagers) ) {
			$pageCour = array_search($this->get("id_usager"), $listeUsagers);
		} else {
			$pageCour = 1;
		}
	
		// Ajouter 1 car l'index commence à 0
		$pageCour += 1;
	
		// Obtenir le nombre total d'utilisateurs
		$pageTotal = count($listeUsagers);
	
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
	 * Obtenir le statut dans la langue de l'utilisateur
	 * 
	 */
	public function getStatutTxt() {
	
		$this->log->debug("Usager::getStatutTxt() Début");
		
		$val = "";
	
		if ($this->get("statut") != "") {
			// Obtenir la chaîne à récupérer
			$str ="USAGER_STATUT_" . strtoupper($this->get('statut'));
	
			// Obtenir la valeur à partir du fichier des langues
			$val = constant($str);
		}
		
		$this->log->debug("Usager::getStatutTxt() Fin");
	
		return $val;
	}	
	
	
	/**
	 *
	 * Obtenir le nom et prénom
	 * 
	 */
	public function getNomPrenom() {
	
		$this->log->debug("Usager::getNomPrenom() Début");
		
		$val = $this->get("nom_prenom");
		
		// Nouvel utilisateur
		if ($val == "" ||  $val == ", ") {
			$val = TXT_NOUVEL_UTILISATEUR_SANS_NOM;
		}	
		
		$this->log->debug("Usager::getNomPrenom() Fin");
	
		return $val;
	}	
	
	/**
	 * 
	 * Enregistrer une connexion
	 * 
	 */
	public function enregistrerConnexion() {
		
		$this->log->debug("Usager::enregistrerConnexion() Début");

		$this->set("nb_mauvais_essais", 0);
		
		$this->log->debug("Usager::enregistrerConnexion() Fin");
		
	}
	
	/**
	 * 
	 * Obtenir l'id Usager
	 */
	public function getIdUsager() {
		return $this->get("id_usager");
	}
	
	
	/**
	 * 
	 * Vérifier si l'utilisateur est admin
	 * 
	 */
	public function isAdmin() {
		return ($this->get("role") == "1");
	}	
	
	/**
	 * 
	 * Régler l'id Usager
	 */
	protected function setIdUsager( $idUsager ) {
		$this->set("id_usager", $idUsager);
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
	
}

?>
