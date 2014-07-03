<?php

require_once '../ressources/classes/outils/Session.php';
require_once '../ressources/classes/modeles/Usager.php';

/** 
 * Classe Verrou
 * 
 * Permet de mettre un verrou lors de l'édition d'éléments afin d'éviter les collisions
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	class Verrou {

		protected $log;
		protected $dbh; 
		
		/**
		 * Constructeur
		 * @param Log $log
		 * @param PDO $dbh
		 */
		public function __construct( Log $log, PDO $dbh ) {
			$log->debug("Verrou::construct()");
			
			$this->log = $log;
			$this->dbh = $dbh;

			return;
		}	

		
		/**
		 *
		 * Ajouter un verrou
		 * 
		 * @param String idUsager
		 * @param String idProjet
		 * @param String idElement
		 *
		 */
		public function ajouterVerrou($idUsager, $idProjet, $idElement) {
		
			$this->log->debug("Verrou::ajouterVerrou() Début");
			
			// Ajouter le verrou seulement s'il n'est pas déjà existant
			if (! $this->isVerrouActif($idUsager, $idProjet, $idElement)) {
				
				// Ajout du verrou
				$this->log->debug("Verrou::ajouterVerrou() Ajout d'un verrou");
				try {
					$sql = "insert into tverrou (id_usager, id_projet, id_element, date_modification) values (?, ?, ?, now())";
					$sth = $this->dbh->prepare($sql);
					$sth->execute(array($idUsager, $idProjet, $idElement));
						
				} catch (Exception $e) {
					Erreur::erreurFatal('018', "Verrou::ajouterVerrou() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
				}
				
			} else {
				
				// Mise à jour du verrou
				$this->log->debug("Verrou::ajouterVerrou() Mise à jour d'un verrou");
				try {
					$sql = "update tverrou set date_modification = now()
							where id_usager = ?
							and id_projet = ?
							and id_element =?";
					$sth = $this->dbh->prepare($sql);
					$sth->execute(array($idUsager, $idProjet, $idElement));
				
				} catch (Exception $e) {
					Erreur::erreurFatal('018', "Verrou::ajouterVerrou() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
				}
			}
		
			$this->log->debug("Verrou::ajouterVerrou()");
		
			return;
		}		
				
		
		/**
		 *
		 * Vérifier si déjà verrouillé
		 * @param String idUsager
		 * @param String idProjet
		 * @param String idElement 
		 *
		 */
		public function isVerrouActif($idUsager, $idProjet, $idElement) {
		
			$this->log->debug("Verrou::isVerrouActif() Début");
				
			$total = 0;
		
			try {
				$sql = "select count(*) 
						from tverrou 
						where id_usager=?
						and id_projet = ?
						and id_element = ?";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($idUsager, $idProjet, $idElement));
					
				// Obtenir le nombre de verrou
				$total = $sth->fetchColumn();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Verrou::isVerrouActif() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
				
			$this->log->debug("Verrou::isVerrouActif() Fin total = '$total'");
			
			$verrouActif = 0;
			if ($total > 0) {
				$verrouActif = 1;
			}
			
			$this->log->debug("Verrou::isVerrouActif() Fin total = '$verrouActif'");
			
			return $verrouActif;
		}
		
		
		/**
		 *
		 * Vérifier si l'élément est verrouillé par un autre utilisateur
		 * @param String idUsager
		 * @param String idProjet
		 * @param String idElement
		 * 
		 */
		public function isElementVerrouilleAutrePersonne($idUsager, $idProjet, $idElement) {
		
			$this->log->debug("Verrou::isElementVerrouilleAutrePersonne() Début");
		
			$listePersonnes = array();
		
			try {
				$sql = "select concat(tusager.prenom, ' ', tusager.nom) as prenomnom
						from tverrou, tusager
						where tverrou.id_usager != ?
						and tverrou.id_projet = ? 
						and tverrou.id_element = ?
						and tverrou.date_modification >= current_timestamp - interval ? second
						and tusager.id_usager = tverrou.id_usager
						";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($idUsager, $idProjet, $idElement, DUREE_VERROU_EXPIRATION));
					
				// Récupérer les informations des personnes
				$result = $sth->fetchAll();
			
				foreach($result as $row) {
			    	
			    	$cles = array_keys($row);
			    	
			    	foreach ($cles as $cle) {

				    	// Obtenir chaque personne
				    	if (! is_numeric($cle) ) {
				    		//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
				    		array_push($listePersonnes, $row[$cle]);
				    	}
			    	}
		        }
									
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Verrou::isElementVerrouilleAutrePersonne() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Verrou::isElementVerrouilleAutrePersonne() Fin");
				
			return $listePersonnes;
		}		
		

		/**
		 *
		 * Vérifier si l'élément est verrouillé par un autre utilisateur si oui, retourner un message
		 * @param String idUsager
		 * @param String idProjet
		 * @param String idElement1
		 * @param String idElement2
		 *
		 */
		public function getMessageVerrous($idUsager, $idProjet, $idElement1, $idElement2) {
		
			$this->log->debug("Verrou::getMessageVerrous() Début idUsager = '$idUsager' idProjet = '$idProjet'  idElement1 = '$idElement1'  idElement2 = '$idElement2'");
		
			$listePersonnes = array();
		
			try {
				$sql = "select concat(tusager.prenom, ' ', tusager.nom) as prenomnom
						from tverrou, tusager
						where tverrou.id_usager != ?
						and tverrou.id_projet = ?
						and (tverrou.id_element = ? or tverrou.id_element = ?)
						and tusager.id_usager = tverrou.id_usager
						";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($idUsager, $idProjet, $idElement1, $idElement2));
					
				// Récupérer les informations des personnes
				$result = $sth->fetchAll();
					
				foreach($result as $row) {
		
					$cles = array_keys($row);
		
					foreach ($cles as $cle) {
		
						// Obtenir chaque personne
						if (! is_numeric($cle) ) {
							//echo "[Récupérer de la bd] Cle : '$cle'  Valeur = '" . $row[$cle] . "'\n";
							array_push($listePersonnes, $row[$cle]);
						}
					}
				}
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Verrou::getMessageVerrous() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Verrou::getMessageVerrous() Fin");
		
			$message = null;
			if (! empty($listePersonnes)) {
				// Créer la liste
				$listePersonnesTxt = implode(", ", array_unique($listePersonnes));
				$message = new Messages(ERR_237 . $listePersonnesTxt, Messages::AVERTISSEMENT);
			}
			
			return $message;
		}		
		

		/**
		 *
		 * Supprimer les verrous expirés
		 * 
		 */
		public function nettoyage() {
		
			$this->log->debug("Verrou::nettoyage() Début");
		
			try {
				// Préparer la suppression
				$stmt = $this->dbh->prepare("delete from tverrou
											 where date_modification < current_timestamp - interval ? second
											");
					
				$stmt->execute( array( DUREE_VERROU_EXPIRATION ) );
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Verrou::supprimerVerrou() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Verrou::nettoyage() Fin");
		}		
		
	}

?>
