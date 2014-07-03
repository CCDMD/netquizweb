<?php

/** 
 * Classe Courriel
 * 
 * Outil(s) pour l'envoi de courriels
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */
	
	class Courriel {
		
		protected $log;

		/**
		 * 
		 * Constructeur
		 * @param Log $log
		 */
		public function __construct( Log $log ) {
	
			$this->log = $log;
			
			return;
		}			

		
		/**
		 * 
		 * Envoi d'un courriel
		 * 
		 * @param string $dest Destination
		 * @param string $sujet Sujet du courriel
		 * @param string $message Message à envoyer
		 */
		
		public function envoiCourriel($dest, $sujet, $message) {

			$this->log->info("Usager::envoiCourriel() Début");
			
			$succes = 0;
			
			// Préparation des champs
			$sujet = utf8_decode($sujet);
			$message = utf8_decode($message);
			
			// Préparation des entêtes
			$entetes = "From: \"Netquiz Web\" <" . EMAIL_FROM . ">\n"; 
			$entetes .= "Reply-To: " . EMAIL_FROM > "\n"; 
			$entetes .= "Content-Type: text/plain; charset=\"UTF-8\"\n"; 
			$entetes .= "Content-Transfer-Encoding: 8bit"; 
			
			// Envoi du courriel
			try {
				mb_language("uni"); 
				if (mb_send_mail($dest, $sujet, $message, $entetes)) {
					$succes = 1;
				}
			} catch (Exception $e) {
				$this->log->erreur("Courriel::envoiCourriel Impossible d'envoyer un courriel à '$dest'. Erreur : '" . $e->getMessage() . "'");
			}
			
			$this->log->info("Usager::envoiCourriel() Fin");
			
			return $succes;
		}
		
		
	}

?>
