<?php

/** 
 * Classe Publication
 * 
 * Gestion de la publication et des aperçus
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Publication {
		
		
		protected $dbh;
		protected $log;
		protected $repertoireSource;
		protected $repertoireDestination;
		
		/**
		 * 
		 * Constructeur
		 * @param Log $log
		 * @param PDO $dbh
		 */
		public function __construct( Log $log, PDO $dbh ) {
	
			$this->dbh = $dbh;
			$this->log = $log;
			
		}		
		

		/**
		 * 
		 * preparerRepertoire
		 * @param String Répertoire Destination Usager
		 * @param String Répertoire Destination
		 * 
		 */
		public function preparerRepertoire($repertoireDestinationUsager, $repertoireDestination) {

			$this->log->debug("Publication::preparerRepertoire() Début repertoireDestinationUsager : '$repertoireDestinationUsager'  repertoireDestination : '$repertoireDestination'");
			
			$succes = 0;
			
			// Créer au besoin le répertoire utilisateur
			if( !is_dir($repertoireDestinationUsager) ) {
				@mkdir($repertoireDestinationUsager);
				
				// Vérifier si le répertoire a été créé
				if (!is_dir($repertoireDestinationUsager)) {
					Erreur::erreurFatal("012", "Impossible de créer le répertoire usager '$repertoireDestinationUsager'", $this->log);
				} else { 
					$this->log->debug("Publication::preparerRepertoire() Le répertoire usager '$repertoireDestinationUsager' a été créé");
				}
			} else {
				$this->log->debug("Publication::preparerRepertoire() Le répertoire usager '$repertoireDestinationUsager' existe déjà");
			}
			
			// Créer les les répertoires de destination au besoin
			$chaineRep = str_replace($repertoireDestinationUsager, "", $repertoireDestination);
			$listeRep = explode("/", $chaineRep);
			$repertoireBase = $repertoireDestinationUsager;
			
			foreach ($listeRep as $repertoire) {
				if ($repertoire != "") {
					
					$repertoireCreer = $repertoireBase . $repertoire . "/";
					$repertoireBase = $repertoireCreer;
			
					if( !file_exists($repertoireCreer) ) {

						try { 
							@mkdir($repertoireCreer);
						} catch (Exception $e) {
							Erreur::erreurFatal("012", "Impossible de créer le répertoire '$repertoireCreer'", $this->log);
						} 
						$this->log->debug("Publication::preparerRepertoire() Le répertoire '$repertoireCreer' a été créé");
					} else {
						$this->log->debug("Publication::preparerRepertoire() Le répertoire '$repertoireCreer' existe déjà");
					}
				}
			}
			
			// Vérifier que le répertoire final existe, sinon erreur
			if( is_dir($repertoireDestination) ) {
				$this->log->debug("Publication::preparerRepertoire() Le répertoire '$repertoireDestination' a été créé");
				$succes = 1;
			} else {
				Erreur::erreurFatal("012", "Impossible de créer le répertoire '$repertoireDestinationUsager'", $this->log);
			}
			
			$this->log->debug("Publication::preparerRepertoire() Fin  (succes = '$succes')");
			
			return $succes;
		}
		
		
		/**
		 * 
		 * copierTheme()
		 * Copier le répertoire thème
		 * @param String Répertoire Source
		 * @param String Répertoire Destination
		 * 
		 */
		public function copierTheme($repertoireSource, $repertoireDestination) {

			$this->log->debug("Publication::copierTheme() Début  repertoireSource : '$repertoireSource' repertoireDestination : '$repertoireDestination'");
			
			$succes = 0;
			
			// Vérifier que le répertoire final existe, sinon erreur
			if( file_exists($repertoireDestination) ) {
				// Copier les fichiers sources pour l'aperçu			
				$this->log->debug("Publication::copierTheme() Copier les fichiers du répertoire '$repertoireSource' vers '$repertoireDestination'");
				Fichiers::copyr($repertoireSource, $repertoireDestination);
				$succes = 1;
			} else {
				$succes = -1;
			}
			
			if (ACTIVER_CHANGEMENT_PERMISSIONS_FICHIERS_APERCUS == "1") {
				// Changer les permissions
				Fichiers::chmodRecursif($repertoireDestination);
			}
			
			$this->log->debug("Publication::copierTheme() Fin  (succes = '$succes')");
			
			return $succes;
		}
		
		
		/**
		 * 
		 * creerRepertoireMedia()
		 * Créer le répertoire média 
		 * @param String Répertoire Destination
		 * 
		 */
		public function creerRepertoireMedia($repertoireDestination) {

			$this->log->debug("Publication::creerRepertoireMedia() Début  repertoireDestination : '$repertoireDestination'");
			
			$succes = 0;

			$repertoireDestinationMedia = $repertoireDestination . REPERTOIRE_PREFIX_MEDIAS;
 
			mkdir($repertoireDestinationMedia); 
			$this->log->debug("Publication::creerRepertoireMedia() Le répertoire '$repertoireDestinationMedia' a été créé");
			
			// Vérifier que le répertoire final existe, sinon erreur
			if( file_exists($repertoireDestinationMedia) ) {
				$succes = 1;
			} else {
				$succes = -1;
			}
			
			$this->log->debug("Publication::creerRepertoireMedia() Fin  (succes = '$succes')");
			
			return $succes;
		}		
		
		
		/**
		 * 
		 * nettoyerRepertoireUsager()
		 * @param String Répertoire Destination Usager
		 * 
		 */
		public function nettoyerRepertoireUsager($repertoireDestinationUsager) {

			$this->log->debug("Publication::nettoyerRepertoireUsager() Début");
			
			$nbSupp = 0;
			$tsCourant = time();
			
			$this->log->debug("Publication::nettoyerRepertoireUsager() Vérification du répertoire : '$repertoireDestinationUsager'");
			// Vérifier si le répertoire existe
			if (is_dir($repertoireDestinationUsager)) {
			
				// Obtenir la liste des fichiers/répertoires
				$fichiers = scandir($repertoireDestinationUsager);
				foreach ($fichiers as $f) {
					if ($f == "." || $f == "..") {
						continue;
					}
					$fn = $repertoireDestinationUsager . $f;
					$tsFichier = (int)filemtime($fn);
					$diff = $tsCourant - $tsFichier;
					
					$this->log->debug("Publication::nettoyerRepertoireUsager() Fichier : '$fn' tsFichier : '$tsFichier'  tsCourant '$tsCourant'  diff : '$diff'");
					
					// Si le répertoire dépasse le délai permit, supprimer le répertoire
					if ($diff > SECURITE_NETTOYAGE_FICHIERS_TEMPORAIRES) {
						$this->log->debug("Publication::nettoyerRepertoireUsager() Suppression du répertoire '$fn'");
						Fichiers::rmdirr($fn);
						$nbSupp++;					
					}
				}
			}
			
			if ($nbSupp > 0) {
				$this->log->debug("Publication::nettoyerRepertoireUsager() $nbSupp Fichier(s) supprimé(s)");
			} else {
				$this->log->debug("Publication::nettoyerRepertoireUsager() Aucun fichier à supprimer");
			}

			$this->log->debug("Publication::nettoyerRepertoireUsager() Fin");
		}

		
		/**
		 * 
		 * Écrire le contenu dans le fichier spécifié
		 * @param String Chemin complet fichier main.js
		 * @param String Contenu
		 * 
		 */
		public function ecrireFichier($fichier, $contenu) {
		
			$this->log->debug("Publication::configurerQuiz() Début  fichier = '$fichier'");
			
			$idxItem = 0;
			$config = "";
			
			// Remplacer dans le fichier JS
			file_put_contents($fichier, $contenu);

			$this->log->debug("Publication::configurerQuiz() Fin");
		}		
		
		/**
		 * 
		 * Formater le titre et le texte
		 * @param String Titre
		 * @param String Texte
		 * 
		 */
		static public function preparerChampTitreTexte($titre, $texte, $log) {
		
			$log->debug("Publication::preparerChampTitreTexte() Début  titre = '$titre'  texte : '$texte'");
		
			$contenu = "";
			
			// Cas 1. Seulement le titre
			if ($titre != "" && $texte == "" ) {
				$contenu = HTML_GRAS_DEBUT . $titre . HTML_GRAS_FIN;
			} 
			// Cas 2. Seulement le texte
			elseif ($titre == "" && $texte != "" ) {
				$contenu = $texte;
			}
			// Cas 3. Titre et texte
			elseif ($titre != "" && $texte != "" ) {
				$contenu = HTML_GRAS_DEBUT . $titre . HTML_GRAS_FIN . HTML_SAUT_LIGNE_PUBLICATION . $texte; 
			}
			// Cas 4. Vide
			else {
				$contenu = ""; 
			}
			
			$log->debug("Publication::preparerChampTitreTexte() Fin  contenu = '$contenu'");
			
			return $contenu;
		}	
		
		
	}
?>
