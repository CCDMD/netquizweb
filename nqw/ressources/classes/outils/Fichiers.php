<?php

/** 
 * Classe Fichiers
 * 
 * Gestion des opérations sur les fichiers et répertoires
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

	
	class Fichiers {
		
		/**
		 * Copy a file, or recursively copy a folder and its contents
		 *
		 * @author      Aidan Lister <aidan@php.net>
		 * @version     1.0.1
		 * @link        http://aidanlister.com/repos/v/function.copyr.php
		 * @param       string   $source            Source path
		 * @param       string   $dest              Destination path
		 * @return      bool     Returns TRUE on success, FALSE on failure (CD: not sure this is true)
		 *
		 * adapted by Chris Dickinson 2010-02-01
		 * modifié par Richard Dumoulin 2012-08-22
		 */
		static function copyr($source, $dest) {

			// Check for symlinks
		    if (is_link($source)) {
		        return symlink(readlink($source), $dest);
		    }
		
		    // Simple copy for a file
		    if (is_file($source)) {
		        return copy($source, $dest);
		    }
		
		    $return = TRUE; // CD: $return needs to be initialized to TRUE here for multiple uses below
		    
		    // Make destination directory
		    if (!is_dir($dest)) {
		        $return = mkdir($dest);
		        chmod($dest, 0700); // RD : Sécurité strict
		    }
		    
		    // Vérifier que le répertoire source existe, sinon exception
		    if (!is_dir($source)) {
		    	throw new ErrorException("Impossible de copier les fichiers à partir du répertoire source '$source'. Vérifier le répertoire source.");
		    }
		    
		    // Loop through the folder
		    $dir = dir($source);
		    while (false !== $entry = $dir->read()) {
		        // Skip pointers
		        if ($entry == '.' || $entry == '..') {
		            continue;
		        }
		
		        // Deep copy directories
		        $return = Fichiers::copyr("$source/$entry", "$dest/$entry") && $return; // CD: added return value here
		    }
		
		    // Clean up
		    $dir->close();
		    
		    return $return; // CD: $return in stead of TRUE
		}

		
		/**
		 * Delete a file, or a folder and its contents (recursive algorithm)
		 *
		 * @author      Aidan Lister &lt;aidan@php.net&gt;
		 * @version     1.0.3
		 * @link        http://aidanlister.com/2004/04/recursively-deleting-a-folder-in-php/
		 * @param       string   $dirname    Directory to delete
		 * @return      bool     Returns TRUE on success, FALSE on failure
		 */
		static function rmdirr($dirname)
		{
		    // Sanity check
		    if (!file_exists($dirname)) {
		        return false;
		    }
		 
		    // Simple delete for a file
		    if (is_file($dirname) || is_link($dirname)) {
		        return unlink($dirname);
		    }
		 
		    // Loop through the folder
		    $dir = dir($dirname);
		    while (false !== $entry = $dir->read()) {
		        // Skip pointers
		        if ($entry == '.' || $entry == '..') {
		            continue;
		        }
		 
		        // Recurse
		        Fichiers::rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		    }
		 
		    // Clean up
		    $dir->close();
		    return rmdir($dirname);
		}

		
		/**
		 * 
		 * Remplacer la valeur dans le fichier
		 * @param String Fichier
		 * @param String Tag
		 * @param String Valeur
		 * 
		 */
		static public function remplacerValeur($fichier, $tag, $valeur) {
		
			// Modifier la valeur dans le fichier
			$contenu = file_get_contents($fichier);
			$contenuMAJ = str_replace($tag, $valeur, $contenu);
			file_put_contents($fichier, $contenuMAJ);
		}				
		

		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Questionnaire Le questionnaire
		 * @param Item
		 * 
		 */
		static public function getContenuQuestItem($filename, $quest, $item) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}

		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Questionnaire Le questionnaire
		 * @param Élément
		 * 
		 */
		static public function getContenuQuestElement($filename, $quest, $element) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}


		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Objet X L'élément
		 * 
		 */
		static public function getContenuElement($filename, $element) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}

		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Questionnaire questionnaire
		 * 
		 */
		static public function getContenuQuest($filename, $quest) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}		
		
		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Item  L'item
		 * 
		 */
		static public function getContenuItem($filename, $item) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}		

		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Item  L'item
		 * @param Langue langue
		 * 
		 */
		static public function getContenuItemLangue($filename, $item, $langue) {
			

		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    } else {
		    	// TODO : remonter ce type de problème ou le journaliser
		    }
		    return false;
		}			
		
		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Media Le média
		 * 
		 */
		static public function getContenuMedia($filename, $media) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}	

		/**
		 *
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Terme
		 *
		 */
		static public function getContenuTerme($filename, $terme) {
				
			if (is_file($filename)) {
				ob_start();
				include $filename;
				return ob_get_clean();
			}
			return false;
		}		
		
		/**
		 *
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Langue langue
		 *
		 */
		static public function getContenuLangue($filename, $langue) {
		
			if (is_file($filename)) {
				ob_start();
				include $filename;
				return ob_get_clean();
			}
			return false;
		}

		/**
		 *
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Collection collection
		 *
		 */
		static public function getContenuCollection($filename, $collection) {
		
			if (is_file($filename)) {
				ob_start();
				include $filename;
				return ob_get_clean();
			}
			return false;
		}

		/**
		 *
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Categorie categorie
		 *
		 */
		static public function getContenuCategorie($filename, $categorie) {
		
			if (is_file($filename)) {
				ob_start();
				include $filename;
				return ob_get_clean();
			}
			return false;
		}		
		
		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Questionnaire Le questionnaire
		 * @param Item L'item
		 * @param Langue La langue
		 * 
		 */
		static public function getContenuQuestItemLangue($filename, $quest, $item, $langue) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}

		/**
		 * 
		 * Obtenir le contenu d'un fichier et le conserver dans une variable
		 * @param String Nom de fichier du gabarit
		 * @param Questionnaire Le questionnaire
		 * @param Langue La langue
		 * 
		 */
		static public function getContenuQuestLangue($filename, $quest, $langue) {
			
		    if (is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}			
		
		/**
		 * 
		 * Obtenir le mime type selon le nom de fichier
		 * @param String Nom de fichier
		 * 
		 */
		static public function get_mime_type($fichier) {
	
	        // Liste des types disponibles
	        $mime_types = array(
	                "pdf"=>"application/pdf"
	                ,"exe"=>"application/octet-stream"
	                ,"zip"=>"application/zip"
	                ,"docx"=>"application/msword"
	                ,"doc"=>"application/msword"
	                ,"xls"=>"application/vnd.ms-excel"
	                ,"ppt"=>"application/vnd.ms-powerpoint"
	                ,"gif"=>"image/gif"
	                ,"png"=>"image/png"
	                ,"jpeg"=>"image/jpg"
	                ,"jpg"=>"image/jpg"
	                ,"mp3"=>"audio/mpeg"
	                ,"wav"=>"audio/x-wav"
	                ,"mp4"=>"video/mp4"
	                ,"mpeg"=>"video/mpeg"
	                ,"mpg"=>"video/mpeg"
	                ,"mpe"=>"video/mpeg"
	                ,"mov"=>"video/quicktime"
	                ,"avi"=>"video/x-msvideo"
	                ,"3gp"=>"video/3gpp"
	                ,"css"=>"text/css"
	                ,"jsc"=>"application/javascript"
	                ,"js"=>"application/javascript"
	                ,"php"=>"text/html"
	                ,"htm"=>"text/html"
	                ,"html"=>"text/html"
	        );
	
			$extension = strtolower(end(explode('.',$fichier)));
	
			return $mime_types[$extension];
		}
		
		
			
		/**
		 * 
		 * Zipper le contenu d'un répertoire
		 * @param String Chemin complet du répertoire source
		 * @param String Nom de fichier zip de destination
		 * 
		 */
		
		static public function Zip($source, $destination) {
	
			// Conserver le répertoire initial
			$cwd = getcwd();
			
			// Changer au répertoire à zipper
			chdir($source);
			$source = ".";
			
		    if (!extension_loaded('zip') || !file_exists($source)) {
		        return false;
		    }
		
		    $zip = new ZipArchive();
		    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		        return false;
		    }
		
		    $source = str_replace('\\', '/', $source);
		
		    if (is_dir($source) === true)
		    {
		        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
		
		        foreach ($files as $file)
		        {
		            $file = str_replace('\\', '/', $file);
		
		            // Ignorer "." et ".." folders
		            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
		                continue;

	                
		                
		            if (is_dir($file) === true)
		            {
		                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
		            }
		            else if (is_file($file) === true)
		            {
		                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
		            }
		        }
		    }
		    else if (is_file($source) === true)
		    {
		        $zip->addFromString(basename($source), file_get_contents($source));
		    }

		   	// Retour au répertoire de base
			chdir($cwd);
		    
		    return $zip->close();
		}
			
		
	}
