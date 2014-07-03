<?php

/** 
 * Classe MenuItem
 * 
 * Représente un item du menu ou de l'arborescence d'un questionnaire
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class MenuItem {
	
	protected $dbh;
	protected $log;

	protected $id;
	protected $idSection;
	protected $type;
	protected $libelle;
	protected $niveau;
	protected $nbSousItem;
	protected $idQuestionnaireItem;
	
	/**
	 * 
	 * Constructeur
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function __construct( ) {
		
		return;
	}


	/**
	 * Obtenir l'id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Régler l'id
	 * @param String id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Obtenir l'id de Section
	 */
	public function getIdSection() {
		return $this->idSection;
	}
	
	/**
	 * Régler l'id de la section
	 * @param String idSection
	 */
	public function setIdSection($idSection) {
		$this->idSection = $idSection;
	}

	/**
	 * Obtenir l'id unique questionnaire-item
	 */
	public function getIdQuestionnaireItem() {
		return $this->idQuestionnaireItem;
	}
	
	/**
	 * Régler l'id du questionnaire-item
	 * @param String idQuestionnaireItem
	 */
	public function setIdQuestionnaireItem($idQuestionnaireItem) {
		$this->idQuestionnaireItem = $idQuestionnaireItem;
	}	
	
	/**
	 * Obtenir le type d'item
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Obtenir le type en format texte/libellé
	 */
	public function getTypeTxt() {
		
		$itemType = strtoupper($this->type);
		$typeTxt = constant($itemType);

		return $typeTxt;
	}	
	
	/**
	 * Régler le type
	 * @param String Type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Obtenir le libellé
	 */
	public function getLibelle() {
		return $this->libelle;
	}

	/**
	 * Régler le libellé
	 * @param String Libellé
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
	}
	
	/**
	 * Obtenir le niveau
	 */
	public function getNiveau() {
		return $this>niveau;
	}

	/**
	 * Régler le niveau
	 * @param String Niveau
	 */
	public function setNiveau($niveau) {
		$this->niveau = $niveau;
	}

	/**
	 * Obtenir le nombre de sous-items
	 */
	public function getNbSousItem() {
		return $this->nbSousItem;
	}

	/**
	 * Régler le nombre de sous-items
	 * @param String nbSousItem
	 */
	public function setNbSousItem($nbSousItem) {
		$this->nbSousItem = $nbSousItem;
	}
	
}

?>
