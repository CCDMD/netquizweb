
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - ASSOCIATIONS
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionAssociation(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_association") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php echo $langue->getJS("consignes_association") ?>';

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
			
			// Élément
			if ($item->get("type_elements1") == "texte") {
				echo "'" . $item->getJS("reponse_" . $i . "_element") . "', ";
			} else {
				echo "'" . $item->getJS("reponse_" . $i . "_element_fichier") . "', ";
			} 
			echo $item->get("type_elements1_pub") . ", ";
			
			// Élément associé
			if ($item->get("type_elements2") == "texte") {
				echo "'" . $item->getJS("reponse_" . $i . "_element_associe") . "', ";
			} else {
				echo "'" . $item->getJS("reponse_" . $i . "_element_associe_fichier") . "', ";
			} 
			echo $item->get("type_elements2_pub") . ", ";

			// Rétroaction positive
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction") . "', ";
			
			// Rétroaction négative			
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction_negative") . "', ";
			
			// Toujours 0
			echo "0, ";
			
			// Source de l'élément 
			echo $item->getJS("reponse_" . $i . "_element_source_image") . ", ";
			
			// Source de l'élément associé
			echo $item->getJS("reponse_" . $i . "_element_associe_source_image");
			echo ");\n";
		}
?>


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>