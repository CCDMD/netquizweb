
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - RÉPONSE BRÈVE
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionShortText(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_reponsebreve") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php 
echo $langue->getJS("consignes_reponsebreve_debut");

if ( $item->getJS("majmin_pub") == "true" && $item->getJS("ponctuation_pub") == "true" ) {
	echo ", ";
}

if ($item->getJS("majmin_pub") == "true" && $item->getJS("ponctuation_pub") == "false") {
	echo " " . $langue->getJS("conjonction_et");
}

if ($item->getJS("majmin_pub") == "true") {
	echo " " . $langue->getJS("consignes_reponsebreve_majuscules");
}

if ($item->getJS("ponctuation_pub") == "true") {
	echo " " . $langue->getJS("conjonction_et");
}

if ($item->getJS("ponctuation_pub") == "true") {
	echo " " . $langue->getJS("consignes_reponsebreve_ponctuation");
}

echo "."; ?>';

// Champ Pondération dans Paramètres de l'item (défaut pour réponses multiples = 1.0)
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

// Pour la correction, tenir compte... des majuscules/minuscules 
page.question.bCaseSens = <?php echo $item->getJS("majmin_pub") ?>;

// Pour la correction, tenir compte... de la ponctuation 
page.question.bPoncCompte = <?php echo $item->getJS("ponctuation_pub") ?>;

// LISTE DES CHOIX DE RÉPONSES
// ----------------------------------------------------------------------------------------------------------
<?php for ( $i = 1; $i <= $item->getJS("reponse_total"); $i++ ) {

			// Ajouter l'information pour chaque choix de réponse
			
			// Ajouter une bonne ou mauvaise réponse
			if ($item->get("reponse_" . $i . "_reponse_pub") == "true") {
				echo "page.question.setGoodAnswer(";
			} else {
				echo "page.question.addWrongAnswer(";
			}
			
			// Élément
			echo "'" . html_entity_decode($item->getJS("reponse_" . $i . "_element"), ENT_QUOTES, "UTF-8") . "', ";
			
			// Rétroaction
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction") . "'";
			
			echo ");\n";
		}
?>

// Champ Rétroaction pour toutes les réponses non prévues dans l'onglet Contenu de l'item
page.question.setOtherAnswersFeedback('<?php echo $item->getJS("retroaction_reponse_imprevue") ?>'); 


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>