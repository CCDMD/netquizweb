<?php

/** 
 * Classe Theme
 * 
 * Gestion des thèmes pour l'affichage du "look" côté client
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Theme {
	
	protected $dbh;
	protected $log;

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
		
		return;
	}

	
	/**
	 * 
	 * Obtenir la liste des thèmes
	 * 
	 */

	public function getListeThemes() {
		
		$this->log->debug("Theme::getListeThemes() Début");
		
		$listeThemes = array();

		// Obtenir les thèmes disponibles
		$listeFichiers = scandir(REPERTOIRE_THEMES);
		
		// Enlever . et .. de la liste des fichiers
		foreach ($listeFichiers as $fichier) {
		    if ($fichier != '.' && $fichier != '..') {
			    array_push($listeThemes, utf8_encode(Securite::nettoyerNomfichier($fichier)));
		    }
		}
		
		$this->log->debug("Theme::getListeThemes() Fin");
		
		return $listeThemes;
	}
		
}

?>
