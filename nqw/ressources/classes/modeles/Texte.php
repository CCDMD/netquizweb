<?php

/** 
 * Classe Texte
 * 
 * Représente une chaîne de texte, modifiable par l'administrateur
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */


class Texte {
	
	protected $dbh;
	protected $log;
							  
	protected $donnees;
	
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
	 * Sauvegarder les informations dans la base de données - Mise à jour d'un Texte
	 *
	 */
	public function enregistrer() {

		$this->log->debug("Texte::enregistrer() Début");
		
		try {
			
			// Supprimer auparavant
			$stmtSupp = $this->dbh->prepare("delete from ttexte
											 where id_texte = ?
										 	 and langue_interface = ?
											");
			
			// Effectuer suppression
			$stmtSupp->execute( array( 'message_avertissement', $this->get('langue') ) );
			$stmtSupp->execute( array( 'message_bienvenue', $this->get('langue') ) );
			
			
			// Préparer enregistrement
			$stmt = $this->dbh->prepare("insert into ttexte (texte, id_texte, langue_interface, date_modification)
										 values (?,?,?,now()) 
										");
	
			// Insérer les données
			$stmt->execute( array( $this->get('message_avertissement'), 'message_avertissement', $this->get('langue') ) );
			$stmt->execute( array( $this->get('message_bienvenue'), 'message_bienvenue', $this->get('langue') ) );

		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Texte::enregistrer() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
									
		$this->log->debug("Texte::enregistrer() Fin");
										
		return;
	}		

	
	/**
	 * 
	 * Charger la Texte à partir de la base de données
	 * 
	 * @param String langue
	 * 
	 */
	public function getTexte($langue) {

		$this->log->debug("Texte::getTexte() Début");
		$trouve = false;
		
		try {
			// Préparer le SQL
			$sql = "select * 
					from ttexte 
					where langue_interface = ?";
			
			// Exécuter la requête
			$sth = $this->dbh->prepare($sql);
			$row = $sth->execute(array($langue));
			
			// Récupérer les informations pour les textes
			$result = $sth->fetchAll();
			
			foreach($result as $row) {
			
				$cles = array_keys($row);
			
				foreach ($cles as $cle) {
					
					// Obtenir chaque champ
					if (! is_numeric($cle) && $cle == "id_texte" ) {
						
						$idTexte = $row[$cle];
						
						$this->donnees[$idTexte] = $row['texte'];
						//echo "[Récupérer de la bd] idTexte : '$idTexte'  Valeur = '" . $row['texte'] . "'\n";
					}
				}
			}
		} catch (Exception $e) {
			Erreur::erreurFatal('018', "Texte::ajouter() - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $this->log);
		}
		
		// Terminer
		$this->log->debug("Texte::getTexte() Fin");
		return;		
	}
		
	/**
	 * 
	 * Obtenir les valeurs du texte à partir de la requête web
	 * @param Log $log
	 * @param PDO $dbh
	 */
	public function getDonneesRequete() {

		// Obtenir les paramètres
		$params = Web::getListeParam("texte_");
		
		// Ajouter les informations de la requête aux variables de l'instance de l'objet
		foreach ($params as $cle => $valeur) {
			$this->donnees[$cle] = $valeur;
			//echo "[Requête] cle : '$cle'  valeur : '$valeur'\n";
		}
		return;
	}		
	
	
	/**
	 * 
	 * Obtenir une valeur
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
	 */
	public function set( $libelle, $valeur ) {
		$this->donnees[$libelle] = $valeur;
	}
	
}

?>
