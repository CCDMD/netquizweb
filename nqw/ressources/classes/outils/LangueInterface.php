<?php
	
/** 
 * Classe LangueInterface
 * 
 * Gestion de l'affichage des libellés dans l'application
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */
	
	class LangueInterface {

		protected $langue = "";
		protected $log;
		
		// Constructeur
		public function __construct( Log $log ) {
			
			// Régler les variables
			$this->log = $log;
						
			$this->log->debug("LangueInterface::__construct");
		}

		/**
		 *
		 * Obtenir la langue de l'utilisateur
		 *
		 */
		public function chargerLangue($usager) {
			
			$this->log->debug("LangueInterface::chargerLangue() Début");
			
			// Obtenir la liste des langues disponibles
			$listeLanguesInterface = $this->getListeLanguesInterfaces($this->log);
		
			// Obtenir la langue de la session
			$session = new Session();
			$sessionLang = $session->get("langue");
			$this->log->debug("LangueInterface::chargerLangue() Langue en session : '$sessionLang' ");
			
			// Obtenir la langue du profile usager
			$langueProfil = "";
			if ($usager != '') {
				$langueProfil = $usager->get("langue_interface");
				$this->log->debug("LangueInterface::chargerLangue() Langue du profil : '$langueProfil' ");
			}
						
			// Vérifier pour un paramètre "lang"
			$paramLangue = Web::getParam("lang");
			$this->log->debug("LangueInterface::chargerLangue() Langue passée en paramètre : '$paramLangue'");
			if ($paramLangue != "") {
				$this->log->debug("LangueInterface::chargerLangue() Utiliser la langue à partir des paramètres");
				$this->langue = $paramLangue;
			}
						
			// Vérifier pour un paramètre "langue" du profil utilisateur sauf en mode admin
			if ( Web::getParam("admin") != "1") {
				$paramLangueUsager = Web::getParam("usager_langue_interface");
				$this->log->debug("LangueInterface::chargerLangue() Langue passée en paramètre via profil utilisateur : '$paramLangueUsager'");
				if ($this->langue == '' && $paramLangueUsager != '') {
					$this->log->debug("LangueInterface::chargerLangueUsager() Utiliser la langue à partir du profil utilisateur");
					$this->langue = $paramLangueUsager;
				}
			}
			
			// Sinon vérifier dans la session
			if ($this->langue == '' && $sessionLang != '') {
				$this->log->debug("LangueInterface::chargerLangue() Utiliser la langue à partir de la valeur en session");
				$this->langue = $sessionLang;
			}
			
			// Si aucune préférence de langue trouvée ou si la langue sélectionnée n'est pas disponible, utiliser la langue par défaut
			if ($this->langue == '' || ! isset($listeLanguesInterface[$this->langue])) {
				$this->log->debug("LangueInterface::chargerLangue() Utiliser la langue par défaut");
				$this->langue = LANGUE_DEFAUT;
			}
			
			// Mettre en session la langue choisie au besoin
			if ($this->langue != $sessionLang) {
				$this->log->debug("LangueInterface::chargerLangue() Mettre la langue à jour le paramètre de langue dans la session");
				$session->set("langue", $this->langue);
			}
			
			// Récupérer le fichier de langue
			$fichierLangue = REPERTOIRE_LANGUES . "nqw_" . $this->langue . ".php"; 
			$fichierLangueLocal = REPERTOIRE_LANGUES . "local_" . $this->langue . ".php";
			
			$this->log->debug("LangueInterface::chargerLangue() Langue utilisée pour la requête : '" . $this->langue . "'");
			
			// Récupérer le fichier de langue local à l'installation si il existe
			$this->log->debug("LangueInterface::chargerLangue() Récupérer le fichier de langue local : '$fichierLangueLocal");
			if (file_exists($fichierLangueLocal)) {
				require_once $fichierLangueLocal;
			} else {
				$this->log->debug("LangueInterface::chargerLangue() Récupérer le fichier de langue de l'application : '$fichierLangue'");
				require_once $fichierLangue;
			}

			// Mettre dans le profil la langue choisie au besoin
			if ($usager != '' && $this->langue != $langueProfil) {
				$this->log->debug("LangueInterface::chargerLangue() Mettre la langue à jour le paramètre de langue dans la BD");
				$usager->set("langue_interface", $this->langue);
				$usager->enregistrer();
			}			
			
			$this->log->debug("LangueInterface::chargerLangue() Fin");			
			return;
		}
		
		/**
		 *
		 * Obtenir la langue courante
		 *
		 */
		
		public function getLangue() {
			return $this->langue;	
		}
		
		/**
		 *
		 * Obtenir les langues disponibles
		 * @param Log $log
		 * @return Array Liste des langues
		 *
		 */
		static public function getListeLanguesInterfaces($log) {
		
			$log->debug("LangueInterface::getListeLanguesInterfaces() Début");
			
			// Obtenir la liste à partir des langues installées
			
			$listeLangues = array();
			
			// Obtenir les thèmes disponibles
			$listeFichiers = scandir(REPERTOIRE_LANGUES);
			
			// Enlever . et .. de la liste des fichiers
			foreach ($listeFichiers as $fichier) {
				if ($fichier != '.' && $fichier != '..') {
					
					// Enlever le .php
					$codeLangue = $fichier;
					$codeLangue= str_replace(".php", "", $codeLangue);
					
					// Enlever local et nqw
					$codeLangue = str_replace("local_", "", $codeLangue);
					$codeLangue = str_replace("nqw_", "", $codeLangue);
					
					// Obtenir le code de langue
					$codeLangue = ($codeLangue);
					
					// Obtenir le titre de la langue à partir du fichier
					$titreLangue = "";
					$contenu = file_get_contents(REPERTOIRE_LANGUES . $fichier);
					
					preg_match_all('/\'LANGUE_INTERFACE_TITRE\',\'(.*)\'/', $contenu, $matches, PREG_SET_ORDER );
					if (!empty($matches)) {
						$titreLangue = $matches[0][1];
					}
					
					if ($titreLangue != "") {
						$listeLangues[$codeLangue] = $titreLangue;
					}
				}
			}			
			
			// Retourner les valeurs uniques
			$listeLangues = array_unique($listeLangues);
			
			$log->debug("LangueInterface::getListeLanguesInterfaces() Fin");

			return $listeLangues;
		}
		
	}

?>
