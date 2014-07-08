<?php

require_once '../ressources/classes/outils/Erreur.php';

/** 
 * Classe Log
 * 
 * Gestion de la journalisation applicative
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Log {
		
		const DEBUG 		= 5;	// Journaliser tous les détails 
		const INFO 			= 4;	// ...
		const AVERTISSEMENT = 3;	// ...
		const ERREUR 		= 2;	// ...
		const CRITIQUE 		= 1;	// Évenements critiques
		const AUCUN 		= 0;	// Ne rien journalisé
		
		protected  $fichier;
		protected  $formatDate	= "Y-m-d G:i:s";
		protected  $priorite = Log::AVERTISSEMENT;
		protected  $fh;

		// Constructeur
		public function __construct( $fichier, $priorite ) {
			// RD20140706 Correctif pour permettre l'écritre des logs en cas d'exception
			//if ( $priorite == Log::AUCUN ) return;
			
			$this->fichier = $fichier;
			$this->priorite = $priorite;
			
			if ( file_exists( $this->fichier ) ) {
				if ( !is_writable($this->fichier) )	{
					$erreur = new Erreur('002', 'Le fichier journal ne peut être ouvert en écriture', NULL);
					return;
				}
			}
			
			if ( ! $this->fh = fopen( $this->fichier , "a" ) ) {
				$erreur = new Erreur('002', 'Le fichier journal ne peut être ouvert pour ajout', NULL);
				return;
			}
			
			return;
		}
		
		public function __destruct() {
			if ( $this->fh )
				fclose( $this->fh );
		}
		
		public function info($contenu) {
			$this->logChaine( $contenu , Log::INFO );
		}
		
		public function debug($contenu) {
			$this->logChaine( $contenu , Log::DEBUG );
		}
		
		public function avertissement($contenu) {
			$this->logChaine( $contenu , Log::AVERTISSEMENT );	
		}
		
		public function erreur($contenu) {
			$this->logChaine( $contenu , Log::ERREUR );		
		}

		public function fatal($contenu) {
			$this->logChaine( $contenu , Log::CRITIQUE );
		}
		
		protected function logChaine($contenu, $priorite) {
			if ( $priorite <= $this->priorite ) {
				$prefix = $this->getPrefix( $priorite );
				$this->EcrireLog ( "$prefix $contenu \n" );
			}
		}
		
		/**
		 * Écrire l'information dans le journal
		 * @param String contenu
		 * 
		 */
		public function EcrireLog( $contenu ) {
		    if (fwrite( $this->fh , $contenu ) === false) {
		        // TODO : Traiter erreur écriture log
		    }
		}
		
		/**
		 * Obtenir le préfix pour la ligne dans le journal
		 * @param String niveau
		 * @return string prefix
		 * 
		 */
		private function getPrefix( $niveau ) {
			$time = date( $this->formatDate );
			$ip = $_SERVER['REMOTE_ADDR'];
			$sessionId = substr(session_id(),-5);
		
			switch( $niveau ) {
				case Log::INFO:
					return "$time | $ip | $sessionId | INFO  -->";
				case Log::AVERTISSEMENT:
					return "$time | $ip | $sessionId | AVERTISSEMENT  -->";				
				case Log::DEBUG:
					return "$time | $ip | $sessionId | DEBUG -->";				
				case Log::ERREUR:
					return "$time | $ip | $sessionId | ERREUR -->";
				case Log::CRITIQUE:
					return "$time | $ip | $sessionId | CRITIQUE -->";
				default:
					return "$time | $ip | $sessionId | LOG   -->";
			}
		}
	}

?>
