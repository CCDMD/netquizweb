<?php

/** 
 * Classe Element
 * 
 * Représente une marque pour l'item Marquage
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Element {
	
	protected $dbh;
	protected $log;
							  
	protected $donnees;
	public $listeRetros;
	
	/**
	 * 
	 * Constructeur
	 * @param Log $log
	 * @param PDO $dbh
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
	 * Ajouter un élément dans la BD
	 *
	 */
	public function ajouter() {
	
		$this->log->debug("Element::ajouter() Début ajouter un élément au classeur");
	
		try {
			// Enregistrer les informations
			$sth = $this->dbh->prepare("insert into titem_classeur_element (id_classeur, id_projet, titre, date_creation, date_modification) VALUES (?, ?, ?, now(), now()) ");
			$rows = $sth->execute(array($this->get("id_classeur"), $this->get("id_projet"), $this->get("titre")));
	
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Element::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
	
		$this->log->debug("Element::ajouter() Fin");
	
		return;
	}	

	
	
	/**
	 * 
	 * Obtenir une valeur
	 * 
	 * @param String valeur
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
	 * @param String libelle
	 * @param String valeur
	 * 
	 */
	public function set( $libelle, $valeur ) {
		$this->donnees[$libelle] = $valeur;
	}
	
	/**
	 * 
	 * Obtenir une valeur pour impression
	 * 
	 * @param String valeur
	 * @param String nbLigne
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
	
	
}

?>
