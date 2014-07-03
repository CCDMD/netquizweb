<?php

/** 
 * Classe TermePublication
 * 
 * Représente un terme du lexique pour la publication
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class TermePublication {
	
	protected $donnees;
	
	/**
	 * 
	 * Constructeur
	 * 
	 */
	public function __construct() {

		$donnees = array();
		
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
	 * Régler une valeur
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
	 * Obtenir une valeur pour javascript
	 * 
	 * @param String valeur
	 *
	 */
	public function getJS( $valeur ) {
	
		// Par défaut utiliser la clé demandée
		$val = $valeur;
	
		// Vérifier si une version pour publication est disponible (avec des médias),
		// si oui, utiliser cette version
		$valeurPub = $valeur . "_pub";
	
		if (isset($this->donnees[$valeurPub])) {
			$val = $valeurPub;
		}
	
		return Web::nettoyerChainePourJs($this->get($val));
	}	
	
	
}

?>
