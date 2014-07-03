<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_MEDIAS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

	<script type="text/javascript">

	// Bouton annuler
	function annuler() {
		// Fermer la fenêtre
		parent.$.fancybox.close();
	}
	
	// Bouton ajouter
	function ajouter() {

		// Obtenir le média séctionné		
		titre = document.frm.media_titre.value;
		val = unescape("<?php echo $media->get("id_media")?> - " + titre);
		
		if (val !== undefined) {

			// Soumettre les modifs
			document.frm.submit();
			
			// Appel de la fonction parent			
			//parent.modifierChampMedia(val);
		}
	}

	// Sélection automatique de la source
	function selectionSource(src) {
		
		if (src == "mediaFichier") {
			$('input:radio[name="media_source"]').filter('[value="fichier"]').attr('checked', true);
		}
		if (src == "mediaWeb") {
			$('input:radio[name="media_source"]').filter('[value="web"]').attr('checked', true);
		}
	}	
	
	// Démarrage
	$(document).ready(function() {

		// Resize des fenêtres inital pour affichage plus rapide de l'interface
		resizePanels();

	}); 
	
	</script>
	
</head>

<body>

	<div class="boxStd">
	
		<form id="frm" name="frm" action="media.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="demande" value="media_importer_sauvegarder" />
			<input type="hidden" name="media_id_media" value="<?php echo $media->get("id_media") ?>" />
			<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
			<input type="hidden" name="pagination_page_dest" value="" />
	
			<div class="boxTitre">
				<p><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?>
		
					<?php 	
					if ($media->get("questionnaire_titre_menu") != "") {
						echo "&mdash; " . $media->get("questionnaire_titre_menu");	
					}
				
					if ($media->get("item_titre_menu") != "") {
						echo " &gt; " . $media->get("item_titre_menu");	
					}
					?>
				
				</p>
			</div>
		
			<div class="boxContenu">
			
				<div class="boxPrincipal">
		
					<!--  Messages -->
					<?php include '../ressources/includes/message_onglet1.php' ?>
					<!--  /Messages -->
		
					<p><label for="media_titre"><?php echo TXT_TITRE_DU_MEDIA?></label>
						<input class="wmax" type="text" id="media_titre" name="media_titre" value="<?php echo $media->get("titre")?>"/>
					</p>

					<div class="wdemiGa">
						<p class="btnRadioMedia"><input class="w15" type="radio" name="media_source" value="fichier" id="mediaFichier" <?php echo $media->get("source_fichier") ?>/>&nbsp;<span class="gras"><?php echo TXT_FICHIER_MEDIA ?></span></p>
						<?php if ($media->get("fichier_usager") != "") { ?>
							<p id="fichierActuel" class="padGa25"><?php echo TXT_FICHIER_ACTUEL, "&nbsp;:&nbsp;", $media->get("fichier_usager"); ?> <a href="#" onclick="supprimerFichier()"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a></p>
						<?php }?>
						<p class="padGa25">
							<?php if ($media->get("fichier_usager") != "") echo TXT_NOUVEAU_FICHIER . " :" ?> <input type="file" name="media_fichier_nouveau"  onchange="selectionSource('mediaFichier')"/>
							<a href="#" onclick="document.frm.media_fichier_nouveau.value=''"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
						</p>

						<p class="btnRadioMedia"><input class="w15" type="radio" name="media_source" value="web" id="mediaWeb" <?php echo $media->get("source_web") ?> />&nbsp;<span class="gras"><?php echo TXT_MEDIA_PROVENANT_AUTRE_SITE ?></span></p>
						<p class="padGa25">
							<textarea name="media_url" rows="5" cols="200" class="wmax" onchange="selectionSource('mediaWeb')" placeholder="<?php echo TXT_COPIER_COLLER_CODE_HTML_OU_URL_DU_MEDIA ?>"><?php echo $media->get("url") ?></textarea>
						</p>
					</div>

					<div class="clear">
						<p><label for="media_description"><?php echo TXT_DESCRIPTION ?></label>
							<textarea id="media_description" name="media_description" rows="5" cols="200" class="wmax" placeholder="<?php echo TXT_INSCRIRE_UNE_DESCRIPTION ?>"><?php echo $media->get("description")?></textarea>
						</p>
						<hr />
						<p><label for="media_remarque"><?php echo TXT_REMARQUE ?></label>
							<textarea id="media_remarque" name="media_remarque" rows="5" cols="200" class="wmax" placeholder="<?php echo TXT_INSCRIRE_UN_COMMENTAIRE_UTILE_POUR_GESTION_ITEMS ?>"><?php echo $media->get("remarque")?></textarea>
						</p>
					</div>						
				
				</div>
			</div>
			
			<div class="boxBottom">
				<input class="btnReset" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>" onclick="annuler()" />
				<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="button" onclick="ajouter()" value="<?php echo TXT_AJOUTER ?>" />
			</div>
	
		</form>
	
	</div>						
						
</body>
</html>