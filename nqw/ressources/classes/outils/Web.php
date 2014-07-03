<?php
	
/** 
 * Classe Web
 * 
 * Méthodes utilisées pour le traitement des requêtes et données
 * dans un contexte web.
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Web {
		
		/**
		 * 
		 * Obtenir un paramètre passé en GET ou POST
		 * @param string $param
		 * 
		 */
		public static function getParam($param) {
			
			$val = "";
			
			if ( isset($_POST[$param]) ) {
				$val = $_POST[$param];
			} elseif ( isset($_GET[$param]) ) {
				$val = $_GET[$param];
			}
			
			// Nettoyer les caractères
			$val = Web::nettoyerChaineWeb($val);	
			
			// Enlever les espaces avant ou après
			$val = trim($val);
			
			return $val;
		}


		/**
		 * 
		 * Obtenir un paramètre numérique passé en GET ou POST
		 * @param string $param
		 * 
		 */
		public static function getParamNum($param) {
			
			$val = "";
			
			if ( isset($_POST[$param]) ) {
				$val = $_POST[$param];
			} elseif ( isset($_GET[$param]) ) {
				$val = $_GET[$param];
			}
			
			// Conserver seulement les chiffres
			$val = preg_replace('[\D]', '', $val);	
			
			return $val;
		}
		
		
		/**
		 * 
		 * Obtenir les valeurs qui débutent par un certain préfixe
		 * à partir des informations du Post
		 * @param array $param
		 * 
		 */
		public static function getListeParamPost($prefix) {

			$listeParams = array();
			
			// Obtenir les variables du POST
			$listeCles = array_keys($_POST);
			
			foreach ($listeCles as $cle){
				$str = strstr($cle, $prefix);
				
				if ($str != "") {
					$cleCourte = substr($str, strlen($prefix));
					$valeur = Web::nettoyerChaineWeb($_POST[$cle]);
					$listeParams[$cleCourte] = $valeur;
					//echo "getListeParamPost cle : '$cle'  prefix : '$prefix' str: '$str' value POST='" . $_POST[$cle] . "'  valeur = '$valeur'\n";
				}
			}

			return $listeParams;
		}
		

		/**
		 *
		 * Obtenir les valeurs qui débutent par un certain préfixe
		 * à partir des informations du post ou du get
		 * @param array $param
		 * 
		 */
		public static function getListeParam($prefix) {
		
			$listeParamsPost = Web::getListeParamPost($prefix);
			$listeParamsGet = Web::getListeParamGet($prefix);
			
			// Merge des résultats
			$listeParams = array_merge($listeParamsPost, $listeParamsGet);
			
			return $listeParams;
		}		
		

		/**
		 * 
		 * Obtenir le mode : fenêtre ou normal
		 * 
		 */
		public static function getMode() {
			
			$mode = "";
			
			$modeParam = Web::getParam("mode");
			if ($modeParam != "fenetre") {
				$mode = "normal";
			} else {
				$mode = "fenetre";
			}
			
			return $mode;
		}		
		
		/**
		 * 
		 * Obtenir les valeurs qui débutent par un certain préfixe
		 * à partir des informations du Get
		 * @param array $param
		 * 
		 */
		public static function getListeParamGet($prefix) {

			$listeParams = array();
			
			// Obtenir les variables du GET
			$listeCles = array_keys($_GET);
			
			foreach ($listeCles as $cle){
				
				$str = strstr($cle, $prefix);
				if ($str != "") {
					$cleCourte = substr($str, strlen($prefix));
					$listeParams[$cleCourte] =  Web::nettoyerChaineWeb($_GET[$cle]);
				}
			}
			return $listeParams;
		}

		
		/**
		 * 
		 * Nettoyer la chaîne provenant du web (caractères UTF-8 dans IE)
		 * @param string $param
		 * 
		 */
		public static function nettoyerChaineWeb($chaine) {
			
			// Enlever les placeholder
			$chaine = preg_replace("/<!-- PHSTART -->.*<!-- PHSTOP -->/", "", $chaine);
			
			if(get_magic_quotes_gpc()){
				$chaine = stripslashes($chaine);
			}
			
			$chaine = htmlspecialchars($chaine); 
				
			$chaine = trim($chaine);

			return $chaine;
		}		
		

		/**
		 * 
		 * Nettoyer la chaîne provenant du web pour obtenir un nom de fichier sécuritaire
		 * @param string $param
		 * 
		 */
		public static function nettoyerChaineNomFichier($chaine) {
		
			// Enlever les entités HTML pour ne pas double encode
			$chaine = html_entity_decode($chaine);
			$chaine = preg_replace("/[^a-zA-Z\d\-\_]/", "", $chaine);

			return $chaine;
		}				
		

		/**
		 * 
		 * Nettoyer la chaîne pour publication JS
		 * @param string $param
		 * 
		 */
		public static function nettoyerChainePourJS($chaine) {
						
			// Enlever les sauts de ligne
			$chaine = str_replace(array("\r\n", "\r", "\n"), ' ', $chaine);

			// Escaper les caractères HTML
			$chaine = html_entity_decode($chaine, ENT_QUOTES, "UTF-8");
			
			// Prévenir les problèmes d'apostrophes et guillemets
			$chaine = addslashes($chaine);
			
			return $chaine;
		}			
		
		/**
		 * 
		 * Nettoyer la chaîne pour publication XML
		 * @param string $param
		 * 
		 */
		public static function nettoyerChainePourXML($chaine) {
						
			// Enlever les sauts de ligne
			$chaine = str_replace(array("\r\n", "\r", "\n"), ' ', $chaine);

			// Desescaper les caractères HTML
			$chaine = html_entity_decode($chaine, ENT_QUOTES, "UTF-8");
			$chaine = htmlentities($chaine, ENT_NOQUOTES, "UTF-8");

			// Caractères particuliers à XML
			$chaine = str_replace(array("&", "<", ">", "\"", "'"), array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), $chaine);
			
			return $chaine;
		}	

		/**
		 * 
		 * Nettoyer la chaîne reçu du XML
		 * @param string $param
		 * 
		 */
		public static function nettoyerChaineProvenantDuXML($chaine) {

			// Caractères particuliers à XML
			$chaine = str_replace(array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), array("&", "<", ">", "\"", "'") , $chaine);

			// Desescaper les caractères HTML
			$chaine = html_entity_decode($chaine, ENT_QUOTES, 'UTF-8');
			
			// Prévenir les problèmes d'apostrophes et guillemets
			$chaine = stripslashes($chaine);
			
			return $chaine;
		}		
		
		/**
		 * 
		 * Nettoyer la chaône pour une recherche
		 * @param string $param
		 * 
		 */
		public static function nettoyerChaineRech($chaine) {

			// Convertir les caractères HTML
			$chaine = html_entity_decode($chaine, ENT_QUOTES, "UTF-8");
			
			// Double décodage au cas ou...
			$chaine = html_entity_decode($chaine, ENT_QUOTES, "UTF-8");

			$chaine = Web::supprimerAccents($chaine);
			
			// Enlever les tags HTML
			$chaine = strip_tags($chaine);			
			
			return $chaine;	
		}

		
		/**
		 *
		 * Supprimer les accents de la chaîne
		 * @param String chaine
		 *
		 */
		public static function supprimerAccents($cc) {
			$cc = str_replace(	array(
									'à', 'â', 'ä', 'á', 'ã', 'å',
									'î', 'ï', 'ì', 'í', 
									'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
									'ù', 'û', 'ü', 'ú', 
									'é', 'è', 'ê', 'ë', 
									'ç', 'ÿ', 'ñ', 'ý',
								),
								array(
									'a', 'a', 'a', 'a', 'a', 'a', 
									'i', 'i', 'i', 'i', 
									'o', 'o', 'o', 'o', 'o', 'o', 
									'u', 'u', 'u', 'u', 
									'e', 'e', 'e', 'e', 
									'c', 'y', 'n', 'y',
								),
								$cc
							);
			$cc = str_replace(	array(
									'À', 'Â', 'Ä', 'Á', 'Ã', 'Å',
									'Î', 'Ï', 'Ì', 'Í', 
									'Ô', 'Ö', 'Ò', 'Ó', 'Õ', 'Ø', 
									'Ù', 'Û', 'Ü', 'Ú', 
									'É', 'È', 'Ê', 'Ë', 
									'Ç', 'Ÿ', 'Ñ', 'Ý',
								),
								array(
									'A', 'A', 'A', 'A', 'A', 'A', 
									'I', 'I', 'I', 'I', 
									'O', 'O', 'O', 'O', 'O', 'O', 
									'U', 'U', 'U', 'U', 
									'E', 'E', 'E', 'E', 
									'C', 'Y', 'N', 'Y',
								),
								$cc
							);
	
			return $cc;				
		}		

		
		/**
		 * 
		 * Tronquer une chaîne à une certaine longueur et ajouter ...
		 * @param String $param
		 * @param int longueur
		 * 
		 */
		public static function tronquer($string, $length) {
		 
		    if (strlen($string) > $length) {
		        $string = substr($string,0,($length -3));
	            $string = substr($string,0,strrpos($string,' ')) . ' ...';
		    }
		    
		    return $string;
		}
		
		
		/**
		 * 
		 * Obtenir un paramètre passé par COOKIE
		 * @param string $param
		 * 
		 */
		public static function getCookie($param) {
			
			$val = "";
			
			if ( isset($_COOKIE[$param]) ) {
				$val = $_COOKIE[$param];
			}
			
			//TODO Validation de sécurité au besoin
			return $val;	
		}
		
		/**
		 *
		 * Convertir les <br> en fin de ligne
		 * @param String txt
		 *
		 */
		public static function convertirBRVersFinDeLigne($txt) {

			$txt = preg_replace("/<br\>/", "\n", $txt);
			$txt = preg_replace("/<br \>/", "\n", $txt);
			$txt = preg_replace("/<br>/", "\n", $txt);
			$txt = preg_replace("/<br >/", "\n", $txt);
			
			return $txt;
		}	

		/**
		 *
		 * Convertir les fins de ligne en <br>
		 * @param String chaine
		 *
		 */
		public static function convertirFinDeLigneVersBR($txt) {

			$txt = preg_replace("/\r\n/",'<br>', $txt);
			$txt = preg_replace("/\n\r/",'<br>', $txt);
			$txt = preg_replace("/\n/",'<br>', $txt);
			$txt = preg_replace("/\r/",'<br>', $txt);
			$txt = preg_replace("/<br \/>/",'<br>', $txt);
				
			return $txt;
		}		
		
	}

?>
