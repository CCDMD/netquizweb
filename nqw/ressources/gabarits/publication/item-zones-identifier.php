
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - ZONES À IDENTIFIER
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionImagePart(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_zonesaidentifier") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php echo $langue->getJS("consignes_zones") ?>';

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

// Couleur du contour des zones 
page.question.theDropZoneBorder = '<?php echo $item->getJS("couleur_zones") ?>'; 

// Champ Image
page.question.addImage('<?php echo $item->getJS("image_fichier") ?>', <?php echo $item->getJS("image_fichier_source") ?>);

// LISTE DES CHOIX DE RÉPONSES
// ----------------------------------------------------------------------------------------------------------
<?php for ( $i = 1; $i <= $item->getJS("reponse_total"); $i++ ) {
			// Ajouter l'information pour chaque choix de réponse
			echo "page.question.addChoice(";
			if ($item->get("type_elements1") == "texte") {
				echo "'" . $item->getJS("reponse_" . $i . "_element") . "', ";
				echo "'0', ";
			} else {
				echo "'" . $item->getJS("reponse_" . $i . "_element_fichier") . "', ";
				echo "'1', ";
			} 
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction") . "', ";
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction_negative") . "', ";
			echo $i . ", ";
			echo $item->getJS("reponse_" . $i . "_element_source_image");
			echo ");\n";
			
			// Forcer une valeur numérique (ex: 0)
			$x = $item->getJS("reponse_" . $i . "_coordonnee_x") + 0;
			$y = $item->getJS("reponse_" . $i . "_coordonnee_y") + 0;
			
			echo "page.question.addImagePart(" . $y . ", " . $x . ");\n";
		}
?>

// Champ Ordre de présentation des éléments dans Paramètre de l'item
<?php if ($item->getJS("ordre_presentation") == "aleatoire") { ?>
page.question.shuffle();
<?php } ?>


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>