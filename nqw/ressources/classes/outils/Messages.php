<?php

/** 
 * Classe Messages
 * 
 * Gestion de l'affichage des messages d'erreurs et de confirmation
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Messages {

		const AVERTISSEMENT = "Warn";
		const ERREUR = "Err";
		const CONFIRMATION = "Ok"; 
		
		protected $messages;
		protected $type;
		
		// Constructeur
		public function __construct( $message, $type) {
			
			$this->messages = $message;
			$this->type = $type;

			return;
		}
		
		public function getMessages() {
			return $this->messages;
		}
		
		public function getTypeMessage() {
			return $this->type;
		}
		
	}

?>
