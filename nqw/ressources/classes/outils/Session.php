<?php

/** 
 * Classe Session
 * 
 * "Wrapper" pour l'accès à l'objet session
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Session {
	
	/**
	 * 
	 * Constructeur
	 * Démarre la session avec un session_start()
	 * @access public
	 * 
	 */
	function Session() {
		if(!isset($_SESSION)) {
			session_start();
		}  
	}
	
	/**
	 * 
	 * Ajouter une variable à la session
	 * @param string nom de la variable
	 * @param mix valeur de la variable
	 * @return void
	 * @access public
	 * 
	 */
	function set($name, $value)	{
		$_SESSION[$name] = $value;
	}
	
	/**
	 * 
	 * Récupère une variable de la session
	 * @param string nom de la variable
	 * @return mix valeur de la variable
	 * @access public
	 * 
	 */
	function get($name)
	{
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * Supprimer une variable de la session
	 * @param string nom de la variable
	 * @return void
	 * @access public
	 * 
	 */
	function delete($name) {
		unset($_SESSION[$name]);
	}
	
	/**
	 * 
	 * Détruire la session
	 * @return void
	 * @access public
	 * 
	 */
	function destroy() {
		$_SESSION = array();
		session_destroy();
	}
	
	/**
	 * 
	 * Obtenir le session id securitaire pour nom de répertoire
	 * 
	 */
	function getSessionIdSecuritaire() {
		$sid = session_id();
		$ssid = Securite::getHashMotPasse($sid, CHAINE_SECRETE . "NQW2013");
		return $ssid;
	}
	
	/**
	 *
	 * Régler la dernière visite
	 *
	 */
	function setDerniereVisite() {
		$this->set("derniere_visite", time() );
	}

	
	/**
	 *
	 * Vérifier si la session est active
	 *
	 */
	function verifierSessionActive() {
		$ts = time();
		$dv = $this->get("derniere_visite");
		
		if ($dv != "" && ($ts - $dv) > SESSION_DUREE) {

			// Vider la session
			$this->destroy();
				
		}
	}
	
	/**
	 *
	 * Déterminer si la session est active
	 *
	 */
	function isSessionActive() {
		
		$codeRetour = "0";
		
		$ts = time();
		$dv = $this->get("derniere_visite");
		
		// Déterminer si on est dans la période d'avertissement
		if (($ts - $dv) > SESSION_DUREE_AVERTISSEMENT) {
			$codeRetour = 1;
		}

		// Timeout passé
		if (($ts - $dv) > SESSION_DUREE) {
			$codeRetour = 2;
		}
		
		return $codeRetour;
	}
	
	/**
	 *
	 * Déterminer si la session est pré-timeout
	 *
	 */
	function isSessionPreTimeout() {
		$ts = time();
		$dv = $this->get("derniere_visite");
	
		if (($ts - $dv) > SESSION_DUREE_AVERTISSEMENT) {
			return 0;
		}
		return 1;
	}	
	
}
?>