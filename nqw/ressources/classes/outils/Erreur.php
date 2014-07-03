<?php
	
/** 
 * Classe Erreur
 * 
 * Gestion des erreurs applicatives
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Erreur {
		
		/** 
		 * Traiter une erreur fatale
		 * 
		 * @param string $codeErreur
		 * @param string $messageErreur
		 * @param Log $log
		 */
		public static function erreurFatal( $codeErreur, $messageErreur, $log ) {
			
			// Journaliser l'erreur
			if ($log != NULL) {
				$log->fatal("ERREUR CRITIQUE : $codeErreur ('$messageErreur')");
			}
			
			// Obtenir le code utilisateur si disponible
			$session = new Session();
			$codeUsager = $session->get('codeUsager');
			
			// Obtenir le projet si disponible
			$projet = $session->get("projetActif");
			
			// Envoi de l'erreur par courriel
			if (SUPPORT_ACTIF == 1) {
				// Envoi de l'erreur par courriel
				$message = "";
				
				$courriel = new Courriel($log);
				$ip = $_SERVER['REMOTE_ADDR'];
				$sujet = "Erreur technique - " . ID_INSTALLATION;
				$message .= "Une erreur technique est survenue.  Voici les informations :\r\n";
				$message .= "DATE ET HEURE : " . date("Y-m-d G:i:s") . "\r\n";
				$message .= "IP CLIENT : " . $ip . "\r\n";
				$message .= "CODE USAGER : " . $codeUsager . "\r\n";
				$message .= "PROJET ACTIF : " . $projet . "\r\n";
				$message .= "SERVEUR : " . ID_INSTALLATION . "\r\n";
				$message .= "URL : " . URL_DOMAINE . "\r\n";
				$message .= "CODE D'ERREUR : " . $codeErreur . "\r\n";
				$message .= "MESSAGE D'ERREUR : " . constant("ERR_" . $codeErreur) . "\r\n";
				$message .= "MESSAGE D'ERREUR TECHNIQUE : " . $messageErreur . "\r\n";
				$courriel->envoiCourriel(SUPPORT_COURRIEL, $sujet, $message);
			}
			
			// Effectuer une redirection javascript à la page d'erreur
			$url_redirect = URL_ERREUR . "?erreur=$codeErreur";
			include("../ressources/gabarits/erreur-redirect.php"); 
			exit;
		}
		
		/**
		 * 
		 * Traiter une erreur d'initialisation

		 */
		public static function erreurInit() {

			print "Un probl&egrave;me de configuration avec le r&eacute;pertoire de base de l'application emp&ecirc;che le fonctionnement de Netquiz Web.  Veuillez SVP contacter l'administrateur du serveur.\n";
			exit;
		}
		
		
	}

?>
