
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - DAMIER
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionCheckerBoard(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_damier") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php if ($item->getJS("afficher_masque_pub") == "1") echo $langue->getJS("consignes_damier_masquees"); else echo $langue->getJS("consignes_damier_nonmasquees"); ?>';

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

// Type d'affichage des éléments 
page.question.setAffichage(<?php echo $item->getJS("afficher_masque_pub") ?>);

// Couleur de fond de l'élément 
page.question.setBkgA('<?php echo $item->getJS("couleur_element_pub") ?>');

// Couleur de fond de l'élément à associer 
page.question.setBkgB('<?php echo $item->getJS("couleur_element_associe_pub") ?>');

// Type d'éléments à apparier dans l'onglet Contenu de l'item.
page.question.iType = <?php echo $item->getJS("type_elements_pub") ?>;

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
			
			// Élément associé
			if ($item->get("type_elements2") == "texte") {
				echo "'" . $item->getJS("reponse_" . $i . "_element_associe") . "', ";
			} else {
				echo "'" . $item->getJS("reponse_" . $i . "_element_associe_fichier") . "', ";
			}

			// Masque
			echo "'" . $item->getJS("reponse_" . $i . "_masque_fichier") . "', ";

			// Rétroaction positive
			echo "'" . $item->getJS("reponse_" . $i . "_retroaction") . "', ";
			
			// Source de l'élément 
			echo "'" . $item->getJS("reponse_" . $i . "_element_source_image") . "', ";
			
			// Source de l'élément associé
			echo "'" . $item->getJS("reponse_" . $i . "_element_associe_source_image") . "', ";

			// Source du masque
			echo "'" . $item->getJS("reponse_" . $i . "_masque_source_image") . "'";
			
			echo ");\n";
		}
?>


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>