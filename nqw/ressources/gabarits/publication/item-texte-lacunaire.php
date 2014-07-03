
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - TEXTE LACUNAIRE
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Type d'item et numéro de page si prédéterminé
page.initQuestionBlankText(<?php echo $item->getJS("item_index") ?>);

// Champ dans Types d’items de la langue du questionnaire
page.readableType = '<?php echo $langue->getJS("item_textelacunaire") ?>';

// Consigne selon la langue du questionnaire ou de l'aperçu
<?php if ($item->get("type_lacune") == "menu-deroulant") { ?>
	page.consigne = '<?php echo $langue->getJS("consignes_lacunaire_menu") ?>';
<?php } else if ($item->get("type_lacune") == "glisser-deposer") { ?>
	page.consigne = '<?php echo $langue->getJS("consignes_lacunaire_glisser") ?>';
<?php } else if ($item->get("type_lacune") == "reponse-breve") { 

echo "page.consigne = '";
	
echo $langue->getJS("consignes_lacunaire_reponsebreve_debut");

if ( $item->getJS("majmin_pub") == "true" && $item->getJS("ponctuation_pub") == "true" ) {
	echo ", ";
}

if ($item->getJS("majmin_pub") == "true" && $item->getJS("ponctuation_pub") == "false") {
	echo " " . $langue->getJS("conjonction_et");
}

if ($item->getJS("majmin_pub") == "true") {
	echo " " . $langue->getJS("consignes_lacunaire_reponsebreve_majuscules");
}

if ($item->getJS("ponctuation_pub") == "true") {
	echo " " . $langue->getJS("conjonction_et");
}

if ($item->getJS("ponctuation_pub") == "true") {
	echo " " . $langue->getJS("consignes_lacunaire_reponsebreve_ponctuation");
}

echo ".'"; 

} ?>


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

// Type de lacune
<?php if ($item->get("type_lacune") == "glisser-deposer") { ?>
page.question.questionType = 1;
<?php } else if ($item->get("type_lacune") == "reponse-breve") { ?>
page.question.questionType = 2;
<?php } else if ($item->get("type_lacune") == "menu-deroulant") { ?>
page.question.questionType = 3;
<?php } ?>

<?php if ($item->get("type_lacune") == "reponse-breve") { ?>
	// Pour la correction, tenir compte... des majuscules/minuscules 
	page.question.bCaseSens = <?php echo $item->getJS("majmin_pub") ?>;
	
	// Pour la correction, tenir compte... de la ponctuation 
	page.question.bPoncCompte = <?php echo $item->getJS("ponctuation_pub") ?>;
	
	// Taille du champ
	<?php if ($item->get("type_champs") == "petit") { ?>
	page.question.inputSize = '100';
	<?php } else if ($item->get("type_champs") == "moyen") { ?>
	page.question.inputSize = '250';
	<?php } else if ($item->get("type_champs") == "grand") { ?>
	page.question.inputSize = '400';
	<?php } ?> 
<?php } ?>

// Liste des lacunes si de type Glisser-déposer ou Réponse brève 
<?php 
if ($item->get("type_lacune") == "glisser-deposer" || $item->get("type_lacune") == "reponse-breve") { 

	// Parcourir la liste des lacunes
	foreach($item->listeLacunes as $lacune) {
		
		// Parcourir les réponses
		for ($j = 1; $j <= NB_MAX_CHOIX_REPONSES; $j++) {
		
			$cle = "lacune_" . $lacune->get("idx_lacune") . "_reponse_" . $j;
				
			$element = $item->getJS($cle . "_element");
			$retro = $item->getJS($cle . "_retroaction");
			$reponse = $item->getJS($cle . "_reponse");
				
			// Vérifier que la réponse est valide
			if ($element != "" || $retro != "") {
				
				// Ajouter une réponse 
				if ($reponse == "1") {
					// Bonne réponse
					print "page.question.addGoodAnswer('" . $element . "', '" . $retro . "', " . $lacune->get("idx_lacune") . ");\n";
				} else {
					// Mauvaise réponse
					print "page.question.addWrongAnswer('" . $element . "', '" . $retro . "', " . $lacune->get("idx_lacune") . ");\n";
				}
			}
		}
		
		// Rétro pour la lacune
		print "page.question.setOtherAnswersFeedback('" . $lacune->getJS("retro") . "', " . $lacune->get("idx_lacune") . ");\n";
	}
} 
?>


// Liste des lacunes si de type Menu déroulant 
<?php 
if ($item->get("type_lacune") == "menu-deroulant") { 

	// Parcourir la liste des lacunes
	foreach($item->listeLacunes as $lacune) {
		
		// Parcourir les réponses
		for ($j = 1; $j <= NB_MAX_CHOIX_REPONSES; $j++) {
		
			$cle = "lacune_" . $lacune->get("idx_lacune") . "_reponse_" . $j;
				
			$element = $item->getJS($cle . "_element");
			$retro = $item->getJS($cle . "_retroaction");
			$reponse = $item->getJS($cle . "_reponse");
				
			// Vérifier que la réponse est valide
			if ($element != "" || $retro != "") {

				// Bonne réponse: true or false
				$bonneReponse = "true";
				if ($reponse != "1") {
					$bonneReponse = "false";
				}

				print "page.question.createSelect('" . $element . "', '" . $retro . "', " . $bonneReponse . ", " . $lacune->get("idx_lacune") . ");\n";
			}
		}
	}
} 
?>

// Afficher l'ordre au hasard en tout temps
page.question.shuffle();

// Texte principal
page.question.addMainText('<?php echo $item->getTextePourPublication() ?>');


// COMPLÉMENTS (onglet Compléments)
// ----------------------------------------------------------------------------------------------------------
<?php include "item-complements.php" ?>