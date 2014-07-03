<?php

require_once '../ressources/classes/outils/Session.php';
require_once '../ressources/classes/modeles/Usager.php';

/** 
 * Classe Maintenance
 * 
 * Permet d'effectuer les tâches de maintenance
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	class Maintenance {

		protected $log;
		protected $dbh; 
		
		/**
		 * Constructeur
		 * @param Log $log
		 * @param PDO $dbh
		 */
		public function __construct( Log $log, PDO $dbh ) {
			$log->debug("Maintenance::construct()");
			
			$this->log = $log;
			$this->dbh = $dbh;

			return;
		}	

		
		/**
		 *
		 * Sauvegarde du contenu de la BD
		 * param Usager $usager
		 *
		 */
		public function sauvegardeBD($usager) {
		
			$this->log->debug("Maintenance::sauvegardeBD() Début");

			$chaineAleatoire = Securite::genererChaineAleatoire(8);
			$prefixRepertoireSauvegarde = Securite::nettoyerNomfichier($usager->get("code_usager")) . "/" . REPERTOIRE_PREFIX_SAUVEGARDE;
			$prefixRepertoireAleatoire =  $prefixRepertoireSauvegarde . $chaineAleatoire . "/";
			
			$repertoireSauvegarde = REPERTOIRE_PUB . $prefixRepertoireSauvegarde;
			$repertoireAleatoire = REPERTOIRE_PUB . $prefixRepertoireAleatoire;
			
			$ts = date( "Y-m-d_H-i-s" );
			$nomFichierZip = "netquizweb_" . $ts . ".zip";
						
			$urlFichierZip = URL_PUBLICATION . $prefixRepertoireAleatoire . $nomFichierZip;
			$cheminCompletFichierZip = REPERTOIRE_PUB . $prefixRepertoireAleatoire . $nomFichierZip;
			
			// Vérifier si le répertoire sauvegarde existe sinon le créer
			if (!is_dir($repertoireSauvegarde)) {
				mkdir($repertoireSauvegarde);
				$this->log->debug("Maintenance::sauvegardeBD() Le répertoire '$repertoireSauvegarde' a été créé");
			}
			
			// Vérifier si le répertoire aléatoire existe sinon le créer
			if (!is_dir($repertoireAleatoire)) {
				mkdir($repertoireAleatoire);
				$this->log->debug("Maintenance::sauvegardeBD() Le répertoire '$repertoireAleatoire' a été créé");
			}
			
			// Obtenir le répertoire de base de la BD
			$repertoireBD = $this->getRepertoireBaseBD();
			$this->log->debug("Maintenance::sauvegardeBD() répertoireBD =  '$repertoireBD' a été créé");
			
			// Exécuter la commande en spécifiant le host
			$cmd = $repertoireBD . EXEC_MYSQLDUMP . " --user " . DB_USER . " --password=" . DB_PASSWORD . " --host="  . DB_HOST . " " . DB_NAME;
			$this->log->debug("Maintenance::sauvegardeBD() CMD =  '$cmd'");
				
			// Récupérer le SQL
			$output = array();
			
			// TODO : Vérifier le code de retour
			$rc = exec( $cmd, $output);
			$sql = implode("\n",$output);
				
			// Créer un fichier ZIP
			$zip = new ZipArchive;
			$res = $zip->open($cheminCompletFichierZip, ZipArchive::CREATE);
			if ($res === TRUE) {
				$this->log->debug("Maintenance::sauvegardeBD() Ajout des données au zip");
				$zip->addFromString('netquizweb.sql', $sql);
				$zip->close();
				
				// Obtenir le contenu du fichier
				$data = file_get_contents($cheminCompletFichierZip);
					
				// Envoi
				header( "Content-Type: application/x-gzip");
				header( 'Content-Disposition: attachment; filename="' . $nomFichierZip . '"' );
				print $data;
				
				// Supprimer le fichier et les répertoires
				Fichiers::rmdirr($repertoireSauvegarde);
				
			} else {
				$this->log->debug("Maintenance::sauvegardeBD() Erreur lors de l'ouverture du fichier zip");
			}
			
			$this->log->debug("Maintenance::sauvegardeBD() Fin");
				
			return;
		}
		
		
		/**
		 *
		 * Exécuter un script SQL
		 * param String nom du fichier
		 *
		 */
		public function executerSQL($fichierSQL) {
		
			$this->log->debug("Maintenance::executerSQL() Début");

			// Vérifier si le fichier SQL existe
			if (file_exists($fichierSQL)) {
			
				// Obtenir le répertoire de base de la BD
				$repertoireBD = $this->getRepertoireBaseBD();
				$this->log->debug("Maintenance::executerSQL() répertoireBD =  '$repertoireBD' a été créé");
							
				// Exécuter la commande en spécifiant le host
				$cmd = $repertoireBD . EXEC_MYSQL . " --user=" . DB_USER . " --password=" . DB_PASSWORD . " --host="  . DB_HOST . " --database=" . DB_NAME . " < ";
				$this->log->debug("Maintenance::executerSQL() cmd =  '$cmd'");
			
				// Récupérer le résultat
				$output = shell_exec($cmd . $fichierSQL);
				$this->log->debug("Maintenance::executerSQL() output =  '$output'");
			} else {
				$this->log->debug("Maintenance::executerSQL() Le fichier SQL '$fichierSQL' n'existe pas");
			}
				
			$this->log->debug("Maintenance::executerSQL() Fin");
		
			return;
		}		
		
		
		/**
		 *
		 * Obtenir le nombre de tables dans la BD
		 *
		 */
		public function getNombreTables() {
		
			$this->log->debug("Maintenance::getNombreTables() Début");
			
			$total = 0;
		
			try {
				$sql = "select count(*) from information_schema.tables where table_schema=?";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array(DB_NAME));
					
				// Obtenir le nombre de tables
				$total = $sth->fetchColumn();
			
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::getNombreTables() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
					
			$this->log->debug("Maintenance::getNombreTables() Fin total = '$total'");
		
			return $total;
		}

		
		/**
		 *
		 * Obtenir le nombre d'admin
		 *
		 */
		public function getNombreAdmin() {
		
			$this->log->debug("Maintenance::getNombreAdmin() Début");
				
			$total = 0;
		
			try {
				$sql = "select count(*) from tusager where role = ?";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array(USAGER::ROLE_ADMINISTRATEUR));
					
				// Obtenir le nombre d'administrateurs actifs
				$total = $sth->fetchColumn();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::getNombreAdmin() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
				
			$this->log->debug("Maintenance::getNombreAdmin() Fin total = '$total'");
		
			return $total;
		}		
		
		
		/**
		 *
		 * Obtenir la version courante de la BD
		 *
		 */
		public function getVersionBD() {
		
			$this->log->debug("Maintenance::getVersionBD() Début");
		
			$version = 0;
		
			try {
				$sql = "select version from tconfig";
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
					
				// Obtenir la version de la bd
				$version = $sth->fetchColumn();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::getVersionBD() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Maintenance::getVersionBD() Version = '$version'");
		
			return $version;
		}
		
		/**
		 *
		 * Vérifier si l'accès utilisateurs est permis
		 *
		 */
		public function isApplicationDisponible() {
		
			$this->log->debug("Maintenance::isApplicationDisponible() Début");
		
			$appDispo = 0;
		
			try {
				$sql = "select application_disponible from tconfig";
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
					
				// Obtenir la valeur de la bd
				$appDispo = $sth->fetchColumn();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::isApplicationDisponible() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Maintenance::isApplicationDisponible() isAppDispo = '$appDispo'");
		
			return $appDispo;
		}		
		
		
		/**
		 *
		 * Rendre l'application disponible
		 *
		 */
		public function setApplicationDisponible() {
		
			$this->log->debug("Maintenance::setApplicationDisponible() Début");
		
			try {
				$sql = "update tconfig set application_disponible = 1";
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::setApplicationDisponible() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Maintenance::setApplicationDisponible() Fin");
		
			return;
		}	

		/**
		 *
		 * Rendre l'application disponible
		 *
		 */
		public function setApplicationNonDisponible() {
		
			$this->log->debug("Maintenance::setApplicationNonDisponible() Début");
		
			try {
				$sql = "update tconfig set application_disponible = 0";
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::setApplicationNonDisponible() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Maintenance::setApplicationNonDisponible() Fin");
		
			return;
		}		

		
		/**
		 *
		 * Ajouter la version courante de la BD
		 * @param string version
		 *
		 */
		public function ajouterVersionBD($version) {
		
			$this->log->debug("Maintenance::ajouterVersionBD() Début - version : '$version'");
			
			try {
				$sql = "insert into tconfig (version,date_modification) values (?, now())";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($version));
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::ajouterVersionBD() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Maintenance::ajouterVersionBD()");
		
			return $version;
		}		
		
		
		/**
		 *
		 * Modifier la version courante de la BD
		 * @param string version
		 *
		 */
		public function modifierVersionBD($version) {
		
			$this->log->debug("Maintenance::modifierVersionBD() Début - version : '$version'");
		
			try {
				$sql = "update tconfig set version = ?";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($version));
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::modifierVersionBD() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
		
			$this->log->debug("Maintenance::modifierVersionBD() Version");
		
			return $version;
		}		
		
		
		/**
		 *
		 * Obtenir le répertoire de base de la BD
		 *
		 */
		public function getRepertoireBaseBD() {
		
			$this->log->debug("Maintenance::getRepertoireBaseBD() Début");
				
			$repertoire = "";
		
			try {
				$sql = "select @@basedir";
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
					
				// Obtenir le répertoire
				$repertoire = $sth->fetchColumn();
					
			} catch (Exception $e) {
				Erreur::erreurFatal('018', "Maintenance::getRepertoireBaseBD() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
			}
				
			$this->log->debug("Maintenance::getRepertoireBaseBD() Fin répertoire = '$repertoire'");
		
			return $repertoire;
		}		
		
		
		/**
		 *
		 * Vérifier si l'installation est complétée
		 *
		 */
		public function isInstallationComplete() {
		
			$this->log->debug("Maintenance::isInstallationComplete() Début");

			$installationComplete = false;
			$nbTables = 0;
			$nbAdmins = 0 ;
			$versionBD = "";
			
			// Obtenir le nombre de tables
			$nbTables = $this->getNombreTables();
			
			// Obtenir le nombre d'admin
			if ($nbTables > 0) {
				$nbAdmins = $this->getNombreAdmin();

				// Obtenir la version de l'applicatin dans la bd
				$versionBD = $this->getVersionBD();
			}
			
			
			// Si des tables existent ET au moins un admin ET que ce n'est pas une mise à jour, l'installation est complété
			if ($nbTables > 0 &&  $nbAdmins > 0 && !$this->isMiseAJourEnAttente()) {
				$installationComplete = true;
			}
					
			$this->log->debug("Maintenance::getNombreTables() Fin");
			
			return $installationComplete;
			
		}	

		
		/**
		 *
		 * Vérifier si une mise à jour est en attente
		 *
		 */
		public function isMiseAJourEnAttente() {
		
			$this->log->debug("Maintenance::isMiseAJourEnAttente() Début");

			$majEnAttente = false;
			$versionBD = $this->getVersionBD();
			
			$this->log->debug("Maintenance::isMiseAJourEnAttente() Version BD : '$versionBD'  Version code : '" . VERSION_NQW . "'");
			
			if ($versionBD != VERSION_NQW) {
				$majEnAttente = true;
			}
		
			$this->log->debug("Maintenance::isMiseAJourEnAttente() Fin");
				
			return $majEnAttente;
		}
				
		/**
		 *
		 * Vérifier si une nouvelle version est disponible
		 * @param Session $session
		 * @return boolean true = authentifié
		 * 
		 */
		public function verifierNouvelleVersionDisponible($session) {
			
			$this->log->debug("Maintenance::verifierNouvelleVersionDisponible() Début");
			
			$msg = "";
			
			// Vérifier si l'information est en session
			$flag = $session->get("maintenance_flag");
			$msg = $session->get("maintenance_msg");
			if ($flag == "1") {
				
				$msg = $session->get("maintenance_msg");
				
			} else {
			
				// Récupérer le fichier XML
				if ($xml = @simplexml_load_file(URL_NQW_VERSIONS_XML) ) {
				
					// Obtenir la version courante disponible
					$versionDispo = $xml->versioncourante;
					
					// Vérifier si la version installée est la version disponible
					$this->log->debug("Maintenance::verifierNouvelleVersionDisponible() Version actuelle : '" . VERSION_NQW . "' Version DISPO : '" . $versionDispo . "'\n");
					if (VERSION_NQW != $versionDispo) {
						// Vérifier si un message est disponible
						$cle = 'version-' . VERSION_NQW;
						$msg = $xml->messages->$cle;
						
						// Message par défaut
						if ($msg == "") {
							$msg = "MSG_041";
						}
					}
					
				} else {
					$msg = "MSG_045";
				}
			}
				
				// Sauvegarder en session
				$session->set('maintenance_flag', '1');
				$session->set("maintenance_msg", $msg);
			
			$this->log->debug("Maintenance::verifierNouvelleVersionDisponible() Fin");
			
			return $msg;
		}
		
		/**
		 * 
		 * Vérifie qu'un usager est authentifié
		 * @return boolean true = authentifié
		 */
		public function verifierAuthentification() {
			$session = new Session();
			return $session->get('codeUsager');
		}

		
		/**
		 * 
		 * Déconnexion de l'usager
		 * 
		 */
		public static function deconnexion() {
			
			// Supprimer les informations de la session
			$session = new Session();
			$session->delete('codeUsager');
			$session->delete('pagination_page_cour');
		}		
		
	}

?>
