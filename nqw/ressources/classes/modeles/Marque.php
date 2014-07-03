<?php

/** 
 * Classe Marque
 * 
 * Représente une marque pour l'item Marquage
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Marque {
	
	protected $dbh;
	protected $log;
							  
	protected $donnees;
	public $listeRetros;
	
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
		$this->listeRetros = array();
		
		return;
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
	 * Obtenir une valeur pour impression
	 * @param String $valeur 
	 * @param String $nbLigne
	 * 
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

	/**
	 *
	 * Obtenir une valeur pour du XML
	 * @param String valeur
	 *
	 */
	public function getXML( $valeur ) {
		return Web::nettoyerChainePourXML($this->get($valeur));
	}	
	
	
}

?>
