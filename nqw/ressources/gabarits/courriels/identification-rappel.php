<?php echo TXT_COURRIEL_RAPPEL_BONJOUR ?> <?php echo html_entity_decode($element->get("prenom"),ENT_QUOTES, "UTF-8") . " " . html_entity_decode($element->get("nom"),ENT_QUOTES, "UTF-8")?>,

<?php echo TXT_COURRIEL_RAPPEL_LIGNE1 ?> 

<?php echo URL_DOMAINE . URL_BASE ?>app/mdp.php?demande=mdp&id=<?php echo $element->get("id_usager") ?>&conf=<?php echo $element->get("code_rappel")?>


<?php echo TXT_COURRIEL_RAPPEL_LIGNE2 ?>


<?php echo TXT_COURRIEL_RAPPEL_LIGNE3 ?>


<?php echo TXT_COURRIEL_RAPPEL_MERCI ?>

