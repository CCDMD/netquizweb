
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - CLASSEMENT
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionClassement(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_classement") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
page.consigne = '<?php echo $langue->getJS("consignes_classement") ?>';

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

// Orientation
page.question.orientation = '<?php echo $item->getJS("orientation_elements_pub") ?>';

// Type d'onglet des classeurs 
page.question.tagType = <?php echo $item->getJS("type_elements1_pub") ?>;

// Type l'éléments des classeurs
page.question.elementType = <?php echo $item->getJS("type_elements2_pub") ?>;

// Contenu des classeurs, éléments et rétros

<?php

// Parcourir les classeurs
for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
	if ($item->get("classeur_" . $i . "_statut") == 1) {

		if ($item->get("type_elements1") == "texte") {
?>			
			page.question.addContainer('<?php echo $item->getJS("classeur_" . $i . "_titre") ?>', <?php echo $i ?>, <?php echo $item->getJS("classeur_" . $i . "_source_image") ?>);
<?php
		} else  {
?>
			page.question.addContainer('<?php echo $item->getJS("classeur_" . $i . "_fichier") ?>', <?php echo $i ?>, <?php echo $item->getJS("classeur_" . $i . "_source_image") ?>);
<?php	}
?>
		page.question.setGoodAnswer('<?php echo $item->getJS("classeur_" . $i . "_retroaction") ?>', <?php echo $i ?>);
		page.question.setWrongAnswer('<?php echo $item->getJS("classeur_" . $i . "_retroaction_negative") ?>', <?php echo $i ?>);
		page.question.setIncompleteAnswer('<?php echo $item->getJS("classeur_" . $i . "_retroaction_incomplete") ?>', <?php echo $i ?>);

<?php 
		// Parcourir les éléments
		for ($j = 1; $j < NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
			if ($item->get("classeur_" . $i . "_element_" . $j . "_statut") == 1 && $item->get("classeur_" . $i . "_element_" . $j . "_texte") != TXT_ELEMENT_SANS_TEXTE && $item->get("classeur_" . $i . "_element_" . $j . "_texte") != "") {

				// Préparer les rétroactions par classeurs
				$retros = array();
				for ($k = 1; $k < NB_MAX_CLASSEURS; $k++) {
					if ($item->get("classeur_" . $k . "_statut") == "1" ) {
						array_push($retros, "'" . $item->getJS("classeur_" . $i . "_element_" . $j . "_retro_" . $k ) . "'");
					} 
				}
				$listeRetros = "[" . implode(', ', $retros) . "]";
				
				if ($item->get("type_elements2") == "texte") {
?>
					page.question.addChoice('<?php echo $item->getJS("classeur_" . $i . "_element_" . $j . "_texte") ?>', <?php echo $i ?>, '-1' , <?php echo $listeRetros ?>);
					
<?php 			} else {
?>
					page.question.addChoice('<?php echo $item->getJS("classeur_" . $i . "_element_" . $j . "_fichier") ?>', <?php echo $i ?>, <?php echo $item->getJS("classeur_" . $i . "_element_" . $j . "_source_image") ?> , <?php echo $listeRetros ?>);
<?php 			}			
			}
		} 
	}
}
?>		

// Ordre aléatoire - requis pour classement				
page.question.shuffle();


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>