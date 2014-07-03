<?php

/** 
 * Aiguilleur : erreur.php
 * 
 * Aiguillage des demandes pour le traitement des erreurs
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

ob_start();
require_once '../env.inc.php';
require_once '../config.inc.php';
require_once '../defaults.inc.php';

require_once REPERTOIRE_CLASSES_OUTILS . 'LangueInterface.php';
require_once REPERTOIRE_CLASSES_OUTILS . 'Log.php';
require_once REPERTOIRE_CLASSES_OUTILS . 'Session.php';
require_once REPERTOIRE_CLASSES_OUTILS . 'Web.php';
ob_end_clean();

// Ouvrir le log
// Démarrage de la journalisation
$aujourdhui = date( "Y-m-d" );
$logFN = JOURNALISATION_FICHIER . "-" . $aujourdhui . ".log"; 
$log = new Log($logFN, JOURNALISATION_NIVEAU);
$log->debug("init.php: Début");

// Obtenir les informations de connection
try {
	$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';';
	//$dbh = new PDO($dsn, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
	$dbh = new PDO($dsn, DB_USER, DB_PASSWORD );
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->exec('SET NAMES utf8');
	
} catch (Exception $e) {
}

// Obtenir la langue
$langueInterface = new LangueInterface($log);
$langueInterface->chargerLangue('');

// Vérifier si une erreur est passée en paramètre
$errId = '000';
if (isset($_GET['erreur'])) {
	$errId = preg_replace('/[^0-9]/', '', $_GET['erreur']);
}

// Obtenir le texte du message d'erreur	à partir du fichier de langue
$errMsg = "ERR_" . $errId;
$messageErreur = constant($errMsg); 

// Afficher la page d'erreur
include(REPERTOIRE_GABARITS . 'erreur.php');
