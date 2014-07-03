<?php

/** 
 * Aiguilleur : mdp.php
 * 
 * Aiguillage des demandes pour les mots de passes
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

try {
	
	// Configuration et démarrage
	$aiguilleur = "mdp";
	require_once 'init.php';
	$demandesPermises = array("mdp", "mdp_enregistrer");
	$log->debug("mdp.php: Début");
	
	// Obtenir la demande
	$demande = Web::getParam('demande');
	if ($demande == "") {
		$demande = "mdp";
	} 

	// Récupérer les paramètres
	$idUsager = filter_var(Web::getParam("id"), FILTER_SANITIZE_NUMBER_INT);
	$codeRappel = filter_var(Web::getParam("conf"), FILTER_SANITIZE_STRING);
	
	// Vérifier demande
	if ( Securite::verifierDemande($demande, $demandesPermises) ) {
	
		// Aiguillage de la demande
		$log->debug("mdp.php:   Traitement de la demande '$demande'");
		
		// ----------------------------------------------------------------------------------------
		// Afficher la page de modification de mdp
		// ----------------------------------------------------------------------------------------		
		if ($demande == "mdp") {
			
			$erreurs = 0;

			// Récupérer l'usager basé sur l'id
			$usager = new Usager($log, $dbh);
			
			if ($usager->getUsagerParIdUsager($idUsager)) {
				
				// Vérifier le code de rappel
				if ($codeRappel != $usager->get("code_rappel")) {
					$log->debug("mdp.php Erreur avec le code de rappel '$codeRappel'");
					
					// Incrémenter le nombre de mauvais essais
					$usager->enregistrerMauvaisEssaiMDP();
					
					$erreurs++;
				}
				 
			} else {
				$erreurs++;
			}
			
			// Vérifier erreurs
			if ($erreurs > 0) {
				Erreur::erreurFatal('030', "mdp.php - Erreur de réinitialisation du mot de passe'", $log);
			} 
			
			// Afficher la page pour modifier le mot de passe
			include(REPERTOIRE_GABARITS . 'identification-modifier-mdp.php');
		}

			
		// ----------------------------------------------------------------------------------------
		// Traiter une demande de modification de mdp
		// ----------------------------------------------------------------------------------------		
		if ($demande == "mdp_enregistrer") {
			
			$erreurs = "";
			$succes = 0;
			
			// Récupérer l'usager basé sur l'id
			$usager = new Usager($log, $dbh);
			
			if (!$usager->getUsagerParIdUsager($idUsager)) {
				$log->debug("mdp.php Erreur de récupération de l'usager '$idUsager'");
				Erreur::erreurFatal('030', "mdp.php - Erreur de réinitialisation du mot de passe'", $log);
			}
				
			// Vérifier le code de rappel
			if ($erreurs == "" && $codeRappel != $usager->get("code_rappel")) {
				$log->debug("mdp.php Erreur avec le code de rappel '$codeRappel'");
				Erreur::erreurFatal('030', "mdp.php - Erreur de réinitialisation du mot de passe'", $log);
			}
				
			// Si aucune erreur, procéder au changement de mot de passe
			if ($erreurs == "") {
				
				// Obtenir les informations à partir de la requête
				$usager->getDonneesRequete();				
				
				// Vérifier les champs mots de passe
				$erreurs = $usager->verifierChoixNouveauMDP();
				
				if ($erreurs == "") {
					// Enregistrer les informations d'une collection existante
					$usager->enregistrer();
					$succes = 1;
					
				} else {
					$messages = new Messages($erreurs, Messages::ERREUR);
				}
			}

			// Afficher la page
			if ($succes) {
				// Afficher la page de confirmation
				include(REPERTOIRE_GABARITS . 'identification-modifier-mdp-conf.php');
			} else {
				// Afficher la page pour modifier le mot de passe
				include(REPERTOIRE_GABARITS . 'identification-modifier-mdp.php');
			}
		}
	}
	 
	// Terminer
	$log->debug("mdp.php: Fin");
	
} catch (Exception $e) {
	Erreur::erreurFatal('018', "mdp.php - Erreur technique détectée : '" . $e->getMessage() . $e->getTraceAsString() . "'", $log);
}	