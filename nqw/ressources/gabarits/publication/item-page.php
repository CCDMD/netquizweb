
// ----------------------------------------------------------------------------------------------------------
// ITEM <?php echo $item->getJS("item_index") ?> - PAGE
// ----------------------------------------------------------------------------------------------------------

// Initialisation de la page
page = gNQ4.newPage();

// Champ Titre de la section dans laquelle se trouve l'item dans un questionnaire
page.title = '<?php echo $item->getJS("titreSection") ?>';

// Champ Titre de l'item
page.itemTitle = '<?php echo $item->getJS("titre") ?>';
	
// Champ Texte de l'item
page.statement = '<?php echo $item->getJS("enonce") ?>';


// COMPLÉMENTS (onglet Compléments - sans informations complémentaires)
// ----------------------------------------------------------------------------------------------------------

// MÉDIAS EN ENTÊTE
<?php if ($item->getJS("media_titre_texte") != "") { ?>

// Texte (champ Ajouter un texte : titre + texte)
page.textGuideline = '<?php echo $item->getJS("media_titre_texte") ?>';
<?php } ?>

<?php if ($item->getJS("media_image_fichier") != "" ) {?>
// Image (champ Ajouter une image)
page.imagePath = '<?php echo $item->getJS("media_image_fichier") ?>';
page.imageCategory = '<?php echo $item->getJS("media_image_fichier_source") ?>';
<?php } ?>

<?php if ($item->getJS("media_son_fichier") != "" ) {?>
// Son (champ Ajouter un son)
page.soundPath = '<?php echo $item->getJS("media_son_fichier") ?>';
page.soundCategory = '<?php echo $item->getJS("media_son_fichier_source") ?>';
page.autoplaySound = <?php echo $item->get("demarrage_audio") ?>;
page.showSoundController = true;
<?php } ?>

<?php if ($item->getJS("media_video_fichier") != "" ) {?>
// Vidéo (champ Ajouter une vidéo)
page.videoPath = '<?php echo $item->getJS("media_video_fichier") ?>';
page.videoCategory = '<?php echo $item->getJS("media_video_fichier_source") ?>';
page.autoplayVideo = <?php echo $item->get("demarrage_video") ?>;
page.showVideoController = true;
<?php } ?>

// MESSAGES GÉNÉRAUX DE RÉTROACTION
// Champ Message pour bonne réponse
page.goodAnswerLabel = '<?php echo $item->getJS("message_bonne_reponse") ?>';
<?php if ($item->getJS("reponse_bonne_media_fichier") != "") { ?>
page.goodAnswerMedia = page.addFeedbackMedia(<?php echo $item->getJS("reponse_bonne_media_type") ?>, <?php echo $item->getJS("reponse_bonne_media_source") ?>, '<?php echo $item->getJS("reponse_bonne_media_fichier") ?>', <?php echo $item->getJS("reponse_bonne_media_demarrage") ?>, <?php echo $item->getJS("reponse_bonne_media_controleur") ?>);
<?php } ?>

// Champ Message pour mauvaise réponse
page.wrongAnswerLabel = '<?php echo $item->getJS("message_mauvaise_reponse") ?>';
<?php if ($item->getJS("reponse_mauvaise_media_fichier") != "") { ?>
page.wrongAnswerMedia = page.addFeedbackMedia(<?php echo $item->getJS("reponse_mauvaise_media_type") ?>, <?php echo $item->getJS("reponse_mauvaise_media_source") ?>, '<?php echo $item->getJS("reponse_mauvaise_media_fichier") ?>', <?php echo $item->getJS("reponse_mauvaise_media_demarrage") ?>, <?php echo $item->getJS("reponse_mauvaise_media_controleur") ?>);
<?php } ?>

// Champ Message pou réponse incomplète
page.incompleteAnswerLabel = '<?php echo $item->getJS("message_reponse_incomplete") ?>';
<?php if ($item->getJS("reponse_incomplete_media_fichier") != "") { ?>
page.incompleteAnswerMedia = page.addFeedbackMedia(<?php echo $item->getJS("reponse_incomplete_media_type") ?>, <?php echo $item->getJS("reponse_incomplete_media_source") ?>, '<?php echo $item->getJS("reponse_incomplete_media_fichier") ?>', <?php echo $item->getJS("reponse_incomplete_media_demarrage") ?>, <?php echo $item->getJS("reponse_incomplete_media_controleur") ?>);
<?php } ?>