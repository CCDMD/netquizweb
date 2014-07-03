<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_MEDIAS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

	<script type="text/javascript">
	
	// Changer de page
	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $pagination->getPageCour() ?>";

		if (page != pageCour) {
			document.frm.demande.value="media_selectionner";
			document.frm.pagination_page_dest.value=page;
			document.frm.submit();
		}
	}

	// Bouton ajouter
	function ajouter() {

		// Obtenir le média sélectionné		
		val = $('input[name=medias_selection]:checked').val();
		
		if (val !== undefined) {
			// Appel de la fonction parent
			parent.modifierChampMediaFermer(val);
		}
	}

	// Bouton annuler
	function annuler() {
		// Fermer la fenêtre
		parent.$.fancybox.close();
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
		<div class="boxTitre">
			<p>
				<?php 
				
				// Présenter les médias demandés ...
				if ($filtreTypeMedia == "image") {
					echo TXT_CHOISIR_UNE_IMAGE_DE_LA_BIBLIOTHEQUE;
				} elseif ($filtreTypeMedia == "video") {
					echo TXT_CHOISIR_UNE_VIDEO_DE_LA_BIBLIOTHEQUE;
				} elseif ($filtreTypeMedia == "son") {
					echo TXT_CHOISIR_UN_SON_DE_LA_BIBLIOTHEQUE;
				} else { 
					echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE;
				}
					
				if ($media->get("questionnaire_titre_menu") != "") {
					echo "&nbsp;&mdash; " . $media->get("questionnaire_titre_menu");	
				}
			
				if ($media->get("item_titre_menu") != "") {
					echo " &gt; " . $media->get("item_titre_menu");	
				}
				
				?>
			</p>
		</div>
	
		<div class="boxContenu">
			<?php include '../ressources/includes/barre-rech-biblio-medias-selectionner.php' ?>
			<div class="boxPrincipal">
				<div class="filAriane"><h2><img src="../images/ic-medias.png" alt="<?php echo TXT_MES_MEDIAS ?>" /><?php echo TXT_MES_MEDIAS ?></h2></div>
														
				<form id="frm" name="frm" action="media.php" method="post">
					<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
					<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
					<input type="hidden" name="pagination_page_dest" value="" />
					<input type="hidden" name="tri" value="" />
					<input type="hidden" name="mode" value="fenetre" />
					<input type="hidden" name="demande" value="media_selectionner" />
					<input type="hidden" name="filtre_type_media" value="<?php echo $pageInfos['filtre_type_media'] ?>" />

					<table class="tblListe tblListeMedias">
						<tr class="tblNav">
							<td>
							</td>
							<td colspan="8">
								<div class="flDr">
									<?php include '../ressources/includes/table-nav-haut.php' ?>
								</div>
							</td>
						</tr>
						<tr>
							<th class="cCheck"></th>
							<th class="cCode"><a href="#" class="<?php echo $media->get('tri_id_media') ?>" onclick="changerTriMediaSel('id_media')"><?php echo TXT_CODE ?></a></th>
							<th class="c3"><a href="#" class="<?php echo $media->get('tri_titre') ?>" onclick="changerTriMediaSel('titre')"><?php echo TXT_TITRE ?></a></th>
							<th class="c4"><a href="#" class="<?php echo $media->get('tri_type') ?>" onclick="changerTriMediaSel('type')"><?php echo TXT_TYPE?></a></th>
							<th class="c5"><a href="#" class="<?php echo $media->get('tri_description') ?>" onclick="changerTriMediaSel('description')"><?php echo TXT_DESCRIPTION?></a></th>
							<th class="c6"><a href="#" class="<?php echo $media->get('tri_remarque') ?>" onclick="changerTriMediaSel('remarque')"><?php echo TXT_REMARQUE ?></a></th>
							<th class="c7"><a href="#" class="<?php echo $media->get('tri_date_modification') ?>" onclick="changerTriMediaSel('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
							<th class="c8 last"><a href="#" class="<?php echo $media->get('tri_suivi') ?>" onclick="changerTriMediaSel('suivi')"><img src="../images/ic-star-gris.png" alt="" /></a></th>
						</tr>
						
					<?php foreach($listeMedia as $element){ ?> 										
						<tr>
							<td><input class="noBord selectionElement" type="radio" name="medias_selection" value="<?php echo $element->get("id_media") . " - " . $element->get("titre") ?>" /></td>
							<td><?php echo $element->get("id_prefix")?></td>
							<td class="alGa"><a href="media.php?demande=media_presenter&media_id_media=<?php echo $element->get("id_media") ?>" target="media_<?php echo $element->get("id_media") ?>"><?php echo $element->get("titre")?></a></td>
							<td><?php echo $element->get("type_txt") ?></td>
							<td class="alGa"><?php echo $element->get("description") ?></td>
							<td class="alGa"><?php echo $element->get("remarque") ?></td>
							<td><?php echo $element->get("date_modification") ?></td>
							<td class="c8 last">
							  <?php if ($element->get('suivi') == "1" ) { ?>
									<img src="../images/ic-star-jaune.png" alt="" />
							  <?php } else { ?>
									<img src="../images/ic-star-gris.png" alt="" />
							  <?php } ?>
							</td>
						</tr>
					<?php } ?>

						<tr class="lgLast tblNav">
							<td colspan="9" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
						</tr>

					</table>
				</form>
			</div>
		</div>
		
		<div class="boxBottom">
			<input class="btnReset" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>" onclick="annuler()"  />
			<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_AJOUTER ?>" onclick="ajouter()" />
		</div>
	
	</div>						
						
</body>
</html>