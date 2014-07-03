<?php

require_once '../ressources/classes/outils/Session.php';
require_once '../ressources/classes/modeles/Usager.php';

/** 
 * Classe Authentification
 * 
 * Permet d'identifier et authentifier l'utilisateur
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	class Authentification {

		protected $redirect;
		protected $log;
		protected $dbh; 
		
		/**
		 * Constructeur
		 * 
		 * @param string $redirect
		 * @param Log $log
		 * @param PDO $dbh
		 */
		public function __construct( $redirect, Log $log, PDO $dbh ) {
			$log->debug("Authentification::construct()");
			
			$this->redirect = $redirect;
			$this->log = $log;
			$this->dbh = $dbh;

			return;
		}		

		/**
		 * 
		 * Authentification d'un usager
		 * 
		 * @param string $codeUsager
		 * @param string $motPasse
		 * @return boolean true = authentifié
		 */ 
		public function authentifier($codeUsager, $motPasse) {

			$this->log->debug("Authentification::authentifier() Début");
			$authentification = false;
			$erreur = "";
			
			// Vérifier si le code usager et mot de passe sont présents
			if ($codeUsager != "" && $motPasse != "") {

				// Instancier un objet usager
				$usager = new Usager($this->log, $this->dbh);
				
				// Vérifier le code usager et mdp
				if (! $usager->authentifier($codeUsager, $motPasse) ) {
					
					// Message erreur authentification
					$this->log->debug("Authentification::authentifier() Erreur d'authentification - code usager ou mot de passe incorrect");
					$erreur = ERR_005;
					
					// Cas spécial : Compte verrouillé
					if ($usager->get("statut") == "1") {
						$this->log->debug("Authentification::authentifier()Erreur d'authentification - le compte usager est verrouillé");
						$erreur = ERR_180;
					}
					
				} else {
				
					// Vérifier le statut de l'utilisateur
					
					// Authentification complétée, ajouter le code usager en mémoire
					if ($usager->get("statut") == "0") {
						$session = new Session();
						$session->set('codeUsager', $usager->get("code_usager"));
						$session->set('idUsager', $usager->get("id_usager"));
						$authentification = true;
						$this->log->debug("Authentification::authentifier() Authentification complétée avec succès");
						
						// Mettre à jour la date de dernière authentification
						$usager->enregistrerAuthentificationReussie();
					}
					
					// Compte verrouillé
					if ($usager->get("statut") == USAGER::STATUT_INACTIF) {
						$this->log->debug("Authentification::authentifier() Erreur d'authentification - le compte usager est verrouillé");
						$erreur = ERR_180;
					}
					
					// Compte pas encore approuvé
					if ($usager->get("statut") == USAGER::STATUT_A_APPROUVER) {
						$this->log->debug("Authentification::authentifier() Erreur d'authentification - le compte usager n'a pas été approuvé");
						$erreur = ERR_179;
					}
					
					// Compte refusé
					if ($usager->get("statut") == USAGER::STATUT_REFUSE) {
						$this->log->debug("Authentification::authentifier() Erreur d'authentification - le compte usager est refusé");
						$erreur = ERR_189;
					}
					
					// Compte supprimé
					if ($usager->get("statut") == USAGER::STATUT_SUPPRIME) {
						$this->log->debug("Authentification::authentifier() Erreur d'authentification - le compte usager est supprimé");
						$erreur = ERR_213;
					}

					// Compte incomplet
					if ($usager->get("statut") == USAGER::STATUT_INCOMPLET) {
						$this->log->debug("Authentification::authentifier() Erreur d'authentification - le compte usager est incomplet");
						$erreur = ERR_203;
					}
						
				}
				
			} else {
				// Code usager ou mot de passe absent
				$erreur = ERR_004;	
			}
			
			// Traiter une erreur d'authentification
			if ($erreur != "") {
				
					// Obtenir les textes pour la langue d'interface
					$langueInterface = new LangueInterface($this->log);
					$textes = new Texte($this->log, $this->dbh);
					$textes->getTexte($langueInterface->getLangue());
					
					$this->log->debug("Authentification::Erreur d'authentification ('$erreur')");
					$messages = new Messages($erreur,Messages::ERREUR);
				
					// Erreur de connexion, retour à la page d'acceuil
					include(REPERTOIRE_GABARITS . 'identification.php');
			}
			
			$this->log->debug("Authentification::authentifier() Fin");
			
			return $authentification;	
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
