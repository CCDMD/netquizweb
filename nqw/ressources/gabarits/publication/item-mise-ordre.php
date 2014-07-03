
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - MISE EN ORDRE
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionRanking(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_miseenordre") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php echo $langue->getJS("consignes_ordre") ?>';

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


// LISTE DES CHOIX DE RÉPONSES
// ----------------------------------------------------------------------------------------------------------
<?php for ( $i = 1; $i <= $item->getJS("reponse_total"); $i++ ) {
			// Ajouter l'information pour chaque choix de réponse
			echo "page.question.addChoice(";
			if ($item->get("type_elements1") == "texte") {
				echo "'" . $item->getJS("reponse_" . $i . "_element") . "', ";
			} else {
				echo "'" . $item->getJS("reponse_" . $i . "_element_fichier") . "', ";
			} 
			echo $item->get("type_elements1_pub") . ", ";
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction") . "', ";
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction_negative") . "', ";
			echo "'0', ";
			echo "'" . $item->getJS("reponse_" . $i . "_element_source_image") . "'";
			echo ");\n";
		}
?>

// Ordre aléatoire toujours requis
page.question.shuffle();


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>