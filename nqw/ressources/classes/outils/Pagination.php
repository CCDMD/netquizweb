<?php
	
/** 
 * Classe Pagination
 * 
 * Gestion de la pagination des listes
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Pagination {

		protected $dbh;
		protected $log;
		protected $liste;
		protected $nbElemParPage;
		protected $nbResultats;
		protected $usager;
		protected $nbPages;
		protected $indexDebut;
		protected $indexFin;
		protected $pageCour;
		protected $pageSuiv;
		protected $pagePrec;
		protected $prefixSession;
	
		/**
		 * 
		 * Constructeur
		 * @param Log $log
		 * @param PDO $dbh
		 * 
		 */
		public function __construct( Array $liste, Usager $usager, Log $log, PDO $dbh, $prefixSession = null) {
	
			$this->dbh = $dbh;
			$this->log = $log;
			$this->liste = $liste;
			$this->usager = $usager;
			$this->prefixSession = "pagination_" .$prefixSession;
			$this->nbElemParPage = $this->calculerNbElemParPage();
			$this->nbResultats = count($liste); 
			
			// Calculer le nombre de pages
			$this->calculerPagination();
			
			return;
		}


		public function getNbElemParPage() {
			return $this->nbElemParPage;
		}

		public function getNbResultats() {
			return $this->nbResultats;
		}
		
		public function getNbPages() {
			return $this->nbPages;
		}
		
		public function getIndexDebut() {
			return $this->indexDebut;
		}

		public function getIndexFin() {
			return $this->indexFin;
		}

		public function getPageCour() {
			return $this->pageCour;
		}

		public function getPageSuiv() {
			return $this->pageSuiv;
		}

		public function getPagePrec() {
			return $this->pagePrec;
		}
		
		
		/**
		 * 
		 * Calculer le nombre d'éléments par page
		 * 
		 */
		private function calculerNbElemParPage() {
			
			$this->log->debug("Pagination::calculerNbElemParPage() Début");
			
			$session = new Session();
			
			// Valeur par défaut du fichier config
			$nbElem = NB_ELEMENT_PAR_PAGE;
			
			// Vérifier si une valeur en session est disponible
			if ($session->get($this->prefixSession . "nbElemParPage") != "") {
				$nbElem = $session->get($this->prefixSession . "nbElemParPage");
				$this->log->debug("Pagination.php::calculerNbElemParPage : Valeur provenant de la session : '$nbElem'");
			} 
			
			// Sinon vérifier dans la BD
			elseif ($this->usager->get("pref_nb_elem_page") != "") {
				$nbElem = $this->usager->get("pref_nb_elem_page");
				$this->log->debug("Pagination.php::calculerNbElemParPage : Valeur provenant de la BD : '$nbElem'");
			}

			// Vérifier si l'utilisateur a modifié la valeur
			$nbElemParPageParam = $this->getNbElemParPageParam($nbElem);
			if ($nbElemParPageParam > 0 ) {
				$nbElem = $nbElemParPageParam;
				$this->log->debug("Pagination.php::calculerNbElemParPage : Valeur provenant du web : '$nbElem'");
			}

			// Validation de l'intégrité - ne devrait jamais être 0
			if ($nbElem == 0) {
				$nbElem = NB_ELEMENT_PAR_PAGE;
			} 
			
			// Mettre en session
			$session->set($this->prefixSession . "nbElemParPage", $nbElem);
			$this->log->debug("Pagination.php::calculerNbElemParPage : Valeur finale : '$nbElem'");
			
			// Mettre à jour les préférences utilisateurs au besoin
			if ($this->usager->get("pref_nb_elem_page") != $nbElem) {
				$this->usager->set("pref_nb_elem_page", $nbElem);
				$this->usager->enregistrer();				
			}			
			
			$this->log->debug("Pagination::calculerNbElemParPage() Fin");
			
			return $nbElem;
		}

		
		/**
		 * 
		 * Calculer la pagination
		 * 
		 */
		private function calculerPagination() {
			
			$this->log->debug("Pagination::calculerPagination() Début");
						
			$session = new Session();
			
			// Nombre de pages totales
			$this->nbPages = ceil($this->getNbResultats() / $this->getNbElemParPage());
			
			// Cas spécial 0 page
			if ($this->nbPages == 0) {
				$this->nbPages = 1;
			}
			
			$this->log->debug("Pagination::calculerPagination() nbPages = '$this->nbPages'");
			$this->log->debug("Pagination::calculerPagination() nbResultats= '" . $this->getNbResultats() . "'");
			
			// Page courante par défaut
			$this->pageCour = "1";
			
			// Vérifier si on peut obtenir la page courante de la session
			if ($session->get("$this->prefixSession . page_cour") != "") {
				$this->pageCour = $session->get($this->prefixSession . "page_cour");
			}
			
			// Obtenir la page initiale à partir des paramètres
			$pageInitiale = "1";
			if ( is_numeric($pageInitiale = Web::getParam("pagination_page")) && Web::getParam("pagination_page") > 1 && Web::getParam("pagination_page") < $this->nbPages) {
				$pageInitiale = Web::getParam("pagination_page");
			}
			
			// Vérifier si l'utilisateur demande une autre page
			$pageDest = "";
			
			if (Web::getParam("pagination_page_dest") != "" && is_numeric(Web::getParam("pagination_page_dest")) && Web::getParam("pagination_page_dest") != $pageInitiale) {
				$pageDest = Web::getParam("pagination_page_dest");
			} elseif (Web::getParam("pagination_page_haut") != "" && is_numeric(Web::getParam("pagination_page_haut")) && Web::getParam("pagination_page_haut") != $pageInitiale) {
				$pageDest = Web::getParam("pagination_page_haut");
			} elseif (Web::getParam("pagination_page_bas") != "" && is_numeric(Web::getParam("pagination_page_bas")) && Web::getParam("pagination_page_bas") != $pageInitiale) {
				$pageDest = Web::getParam("pagination_page_bas");
			} else {
				$pageDest = $pageInitiale;
			}
			
			// Valider la page demandée
			if ( $pageDest != "" ) {
				// La page doit être dans l'intervalle permis
				if ($pageDest < 1) {
					$pageDest = 1;
				} elseif ( $pageDest > $this->nbPages) {
					$pageDest = $this->nbPages;
				}
				
				$this->pageCour = $pageDest;
			}

			// Pages suivante et précédentes
			$this->pageSuiv = $this->pageCour + 1;
			$this->pagePrec = $this->pageCour - 1;
			
			// Vérifier que la limite maximale n'a pas été atteinte pour la page suivante
			if ($this->pageSuiv > $this->nbPages) {
				$this->pageSuiv = $this->nbPages;
			}

			// Vérifier que la limite minimale (1) n'a pas été atteinte pour la page précédente
			if ($this->pagePrec < 1 ) {
				$this->pagePrec = 1;
			}
			
			// Enregistrer la page courante dans la session
			$session->set($this->prefixSession . "page_cour", $this->pageCour);
			
			// Index de début
			$this->indexDebut = ($this->pageCour -1) * $this->getNbElemParPage();
			$this->log->debug("Pagination::calculerPagination() indexDebut = '$this->indexDebut'  nbElemParPage : '" . $this->getNbElemParPage() . "'  PageCour : '" . $this->pageCour . "'");
			
			// Index de fin
			$this->indexFin = ( ($this->pageCour) * $this->getNbElemParPage()) - 1;

			
			// Ne pas dépasser le nombre d'éléments
			if ( $this->indexFin + 1 > $this->getNbResultats() ) {
				$this->indexFin = $this->getNbResultats() - 1 ;				
			}
			
			// Cas spécial - aucun résultat
			if ( $this->getNbResultats() == 0) {
				$this->indexDebut = 0;
				$this->indexFin = 0;
			}
			
			$this->log->debug("Pagination::calculerPagination() indexFin = '$this->indexFin'");
			
			$this->log->debug("Pagination::calculerPagination() Fin");
		}
		
		/**
		 * 
		 * Obtenir le nombre d'éléments par page
		 * 
		 */
		private function getNbElemParPageParam($initial) {

			$nbElemParPage = 0;
			
			// Déterminer si l'utilisateur a effectuée une modification dans la page 
			if ( Web::getParam("pagination_nb_elements_haut") != "" && (Web::getParam("pagination_nb_elements_haut") != Web::getParam("pagination_nb_elements")) ) {
				$nbElemParPage = Web::getParam("pagination_nb_elements_haut");
			}
			if ( Web::getParam("pagination_nb_elements_bas") != "" && (Web::getParam("pagination_nb_elements_bas") != Web::getParam("pagination_nb_elements")) ) {
				$nbElemParPage = Web::getParam("pagination_nb_elements_bas");
			}

			// Vérifier que la valeur est permise
			$listeValeurs = array("5", "15", "30", "60");
			if ( !Securite::verifierValeur( $nbElemParPage, $listeValeurs) ) {
				$nbElemParPage = 0;
			}
				
			return $nbElemParPage;
		}
		
		
		
	}
?>
