<?php
	
/** 
 * Classe Securite
 * 
 * Méthodes de sécurité
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Securite {
		
		/**
		 * Créer un grain de sel
		 * @return string
		 */
		public static function creerGrainDeSel() {
			$string = sha1(uniqid(rand(), true));
			return substr($string, 0, 16); 
		}

		/**
		 * Générer un hash du mot de passe
		 * @param String mot de passe
		 * @param String grain de sel secret
		 * @return string
		 */
		public static function getHashMotPasse( $motPasse , $gdsSecret) {
			return sha1($motPasse . $gdsSecret);
		}

		/**
		 * Vérifier si la demande est permise
		 * @param String demande
		 * @param array demandes permises
		 * @return bool
		 */
		public static function verifierDemande( $demande , $listeDemande) {
			return in_array($demande, $listeDemande);
		}
		
		/**
		 * Vérifier si la valeur est permise
		 * @param String valeur
		 * @param array liste de valeurs
		 * @return bool
		 */
		public static function verifierValeur( $valeur , $listeValeurs) {
			
			$trouve = false;

			if (in_array($valeur, $listeValeurs)) {
				$trouve = true;
			}
			
			return $trouve;
		}		
		
		
		/**
		 * Chiffrer une chaîne
		 * @param $chaîne
		 * @return string
		 * 
		 */
		public static function encrypter($value){
		   if(!$value){return false;}
		   $text = $value;
		   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, substr(CHAINE_SECRETE,0,32), $text, MCRYPT_MODE_ECB, $iv);
		   return trim(base64_encode($crypttext)); //encode for cookie
		}

		/**
		 * Déchiffrer une chaîne
		 * @param $chaîne
		 * @return string
		 * 
		 */
		public static function decrypter($value){
		   if(!$value){return false;}
		   $crypttext = base64_decode($value); //decode cookie
		   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, substr(CHAINE_SECRETE,0,32), $crypttext, MCRYPT_MODE_ECB, $iv);
		   return trim($decrypttext);
		}
		
		/**
		 * Obtenir le code utilisateur à partir du cookie
		 * @return string
		 * 
		 */
		public static function getCodeUtilisateurDuCookie() {
			
			$codeUtilisateur = "";
			
			// Obtenir la valeur du cookie
			$cookie = Web::getCookie(COOKIE_CODE_UTILISATEUR);
			
			$codeUtilisateurCookie = Securite::decrypter($cookie, CHAINE_SECRETE);
			
			// Vérifier si la valeur est valide
			$chaineValidation = substr($codeUtilisateurCookie, 0, strlen(COOKIE_CODE_UTILISATEUR));
			if ( $chaineValidation == COOKIE_CODE_UTILISATEUR) {
				$codeUtilisateur = substr($codeUtilisateurCookie, strlen(COOKIE_CODE_UTILISATEUR));
			}  
			
			return $codeUtilisateur;
				
		}
		
		/**
		 * Nettoyer le nom du fichier
		 * @param String nom de fichier
		 * @return string
		 */
		public static function nettoyerNomfichier($fichier) {
			
			// Enlever les accents
			$fichier = Web::supprimerAccents($fichier);
			
			return preg_replace("/[^a-zA-Z0-9\-\_]/","", $fichier); 
		}
		
		/**
		 * Nettoyer le nom du fichier et remplacer par des tirets
		 * @param String nom de fichier
		 * @return string
		 */
		public static function nettoyerNomfichierTirets($fichier) {
				
			// Enlever les accents
			$fichier = Web::supprimerAccents($fichier);
				
			return preg_replace("/[^a-zA-Z0-9\-\_]/","-", $fichier);
		}
		
		/**
		* Générer une chaîne aléatoire
		* @param int $longueur
		* @return string
		*/
		public static function genererChaineAleatoire($longueur = 8) {
		    // 35 caractères alloués
		    $caracteresPermis = "abcdefghijklmnopqrstuvwxyz0123456789";
		 
		    $chaineAleatoire = '';
		 
		    for($i = 1; $i <= $longueur; $i++) {
		        $chaineAleatoire .= $caracteresPermis[rand(0, 35)];
		    }
		 
		    return $chaineAleatoire;
		}
		
		
		/**
		* Vérifier si le mot de passe est valide
		* @param mot de passe
		* @return 1 si valide
		*/
		public static function isMotPasseValide($motPasse) {

			$erreurs = 0;
			
			// Enlever les espaces avant ou après
			$mdp = trim($motPasse);
			
			// Vérifier la longueur minimale
			if (strlen($mdp) < SECURITE_MOTPASSE_LONGUEUR_MIN) {
				$erreurs++;
			}
			
			// Vérifier le nombre de lettre(s)
			$nbLettres = preg_match_all( "/[a-zA-Z]/", $mdp, $lettres );
			if ($nbLettres < SECURITE_MOTPASSE_LETTRE_MIN) {
				$erreurs++;
			}
			
			// Vérifier le nombre de chiffre(s)
			$nbChiffres = preg_match_all( "/[0-9]/", $mdp, $chiffres );
			if ($nbChiffres < SECURITE_MOTPASSE_CHIFFRE_MIN) {
				$erreurs++;
			}
						
			return ($erreurs == 0);
		}
		
	}
?>