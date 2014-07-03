
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - MARQUAGE
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionMarquage(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_marquage") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php echo $langue->getJS("consignes_marquage") ?>';

// Champ Pondération dans Paramètres de l'item (défaut pour choix multiples = 1.0)
page.question.ponderation = <?php if ($item->getJS("ponderation_calculee") != "") echo $item->getJS("ponderation_calculee"); else echo "1.0"; ?>;

// Champ Affichage de la solution (paramètre de l'item ou selon le questionnaire)
page.showNavBSolution = <?php echo $item->getJS("affichage_resultats") ?>;

// Champ Étiquette des éléments (0 = Aucune étiquette, 1 = Alphabétique, 2 = Numérique)
page.question.labelType = '<?php echo $item->getJS("type_etiquettes_pub") ?>';

// Champ Titre de la section dans laquelle se trouve l'item dans un questionnaire
page.title = '<?php echo $item->getJS("titreSection") ?>';

// Champ Titre de l'item
page.itemTitle = '<?php echo $item->getJS("titre") ?>';
	
// Champ Énoncé de l'item
page.statement = '<?php echo $item->getJS("enonce") ?>';

// Points retranchés par faute
page.question.iFautePond = '<?php echo $item->getJS("points_retranches_pub") ?>';

// Champ texte
page.question.addMainText('<?php echo $item->getJS("solution") ?>');

// Choix de couleurs
<?php for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
	if ($item->get("couleur_" . $i . "_statut") == "1") {	?>
	
		page.question.addColor('<?php echo strtoupper($item->get("couleur_" . $i . "_couleur")) ?>', 
							   '<?php echo $item->getJS("couleur_" . $i . "_titre") ?>', 
							   '<?php echo $item->getJS("couleur_" . $i . "_retroaction") ?>', 
							   '<?php echo $item->getJS("couleur_" . $i . "_retroaction_negative") ?>', 
							   '<?php echo $item->getJS("couleur_" . $i . "_retroaction_incomplete") ?>');    	
	
<?php } 	
	} ?>								


// Liste des marques
<?php 
	// Obtenir les marques courantes + rétros
	$item->analyserMarques();

	// Traiter chacune des marques et rétros
	foreach ($item->listeMarques as $marque) {
	
		$couleur = strtoupper(str_replace(";", "", $marque->get("couleur")));
		// Pas de addChoice pour les mauvaises réponses
		if ($couleur != ITEM_MARQUAGE_COULEUR_MAUVAISE_REPONSE) {
?>
			page.question.addChoice(<?php echo $marque->get("position_debut") ?>, <?php echo $marque->get("position_fin") ?>, '<?php echo strtoupper($couleur) ?>');

<?php  	}	
	
		// Liste des rétros pour cette marque
		foreach ($marque->listeRetros as $retro) {
?>
			page.question.addRetro(<?php echo $retro->get("position_debut") ?>, <?php echo $retro->get("position_fin") ?>, '<?php echo strtoupper($retro->get("couleur")) ?>', '<?php echo $retro->getJS("retro") ?>');

<?php 	}
	} 
?>


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>