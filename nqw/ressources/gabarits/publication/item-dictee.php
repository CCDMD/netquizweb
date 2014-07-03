
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - DICTÉE
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionDictee(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_dictee") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php 
echo $langue->getJS("consignes_dictee_debut");

if ( $item->getJS("majmin_pub") == "true" && $item->getJS("ponctuation_pub") == "true" ) {
	echo ", ";
}

if ($item->getJS("majmin_pub") == "true" && $item->getJS("ponctuation_pub") == "false") {
	echo " " . $langue->getJS("conjonction_et");
}

if ($item->getJS("majmin_pub") == "true") {
	echo " " . $langue->getJS("consignes_dictee_majuscules");
}

if ($item->getJS("ponctuation_pub") == "true") {
	echo " " . $langue->getJS("conjonction_et");
}

if ($item->getJS("ponctuation_pub") == "true") {
	echo " " . $langue->getJS("consignes_dictee_ponctuation");
}

echo "."; ?>';

// Champ Pondération dans Paramètres de l'item (défaut pour choix multiples = 1.0)
page.question.ponderation = <?php if ($item->getJS("ponderation_calculee") != "") echo $item->getJS("ponderation_calculee"); else echo "1.0"; ?>;

// Champ Affichage de la solution (paramètre de l'item ou selon le questionnaire)
page.showNavBSolution = <?php echo $item->getJS("affichage_resultats") ?>;

// Champ Titre de la section dans laquelle se trouve l'item dans un questionnaire
page.title = '<?php echo $item->getJS("titreSection") ?>';

// Champ Titre de l'item
page.itemTitle = '<?php echo $item->getJS("titre") ?>';
	
// Champ Énoncé de l'item
page.statement = '<?php echo $item->getJS("enonce") ?>';

// Points retranchés par faute
page.question.iFautePond = '<?php echo $item->getJS("points_retranches_pub") ?>';

// Pour la correction, tenir compte... des majuscules/minuscules 
page.question.bCaseSens = <?php echo $item->getJS("majmin_pub") ?>;

// Pour la correction, tenir compte... de la ponctuation 
page.question.bPoncCompte = <?php echo $item->getJS("ponctuation_pub") ?>;

// Rétroaction globale  / Rétroaction positive 
page.question.sRetroPos = '<?php echo $item->getJS("retroaction_positive") ?>';

// Rétroaction globale  / Rétroaction négative 
page.question.sRetroNeg = '<?php echo $item->getJS("retroaction_negative") ?>';

// Solution 
page.question.sBRep = '<?php echo $item->getJS("solution") ?>';

// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>