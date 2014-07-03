<?php

/**
 * messages_js.php
 *
 * Préparation des messages d'erreurs JS selon la langue de l'utilisateur
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

header("Content-type: text/javascript");

ob_start();
require_once '../env.inc.php';
require_once '../config.inc.php';
require_once '../defaults.inc.php';
require_once '../ressources/classes/outils/Session.php';
ob_end_clean();

// Obtenir la langue de la session
$session = new Session();
$sessionLangue = $session->get("langue");

// Valeur par défaut au besoin
if ($sessionLangue == "") {
	$sessionLangue = LANGUE_DEFAUT;
}

// Récupérer le fichier de langue
$fichierLangue = REPERTOIRE_LANGUES . "nqw_" . $sessionLangue . ".php";
$fichierLangueLocal = REPERTOIRE_LANGUES . "local_" . $sessionLangue . ".php";
	
// Récupérer le fichier de langue local à l'installation si il existe
if (file_exists($fichierLangueLocal)) {
	require_once $fichierLangueLocal;
} else {
	require_once $fichierLangue;
}

// Préparer les messages JS ci-dessous

?>

var TXT_AVERTISSEMENT_ANNULER = "<?php echo trim(TXT_AVERTISSEMENT_ANNULER) ?>";
var TXT_AVERTISSEMENT_NOUVEAU_VERROU = "<?php echo trim(TXT_AVERTISSEMENT_NOUVEAU_VERROU) ?>";
