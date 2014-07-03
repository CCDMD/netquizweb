<?php

/**
 * Préparation de l'environnement pour Netquiz Web
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

// ** Répertoire de base de l'application ** //
$scriptFilename = $_SERVER['SCRIPT_FILENAME'];
$posX = strpos($scriptFilename, "/app/");

if ($posX == "") {
	$posX = strrpos($scriptFilename, "/");
}

// Si on ne trouve pas le répertoire /app/ enlever le nom du script de l'URL
if ($posX > 0) {
	$repertoireFichier = substr($scriptFilename, 0, $posX+1);
} 
define('REPERTOIRE_BASE', $repertoireFichier);

// ** URL - Serveur - où l'application s'exécute ** //
$httpHost = $_SERVER['HTTP_HOST'];
define('URL_DOMAINE', 'http://' . $httpHost);

// ** URL - Répertoire - où l'application s'exécute ** //
$requestUri = $_SERVER['REQUEST_URI'];
$posX = strpos($requestUri, "/app/");
$repertoireWeb = substr($requestUri, 0, $posX+1);
define('URL_BASE', $repertoireWeb);

// ** Chaîne secrète pour le chiffrement ** //
$chaineSecrete = sha1(URL_DOMAINE);
define('CHAINE_SECRETE', $chaineSecrete);

?>