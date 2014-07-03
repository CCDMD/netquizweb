<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_MEDIAS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

	<script type="text/javascript">
	
	// Changer de page
	function changerPage(page) {

		// Véfier si on peut changer la page
		pageCour = "<?php echo $pagination->getPageCour() ?>";

		if (page != pageCour) {
			document.frm.demande.value="media_liste";
			document.frm.pagination_page_dest.value=page;
			document.frm.submit();
		}
	}

	// Vérifier les options disponnibles dans le menu contextuel
	function verifierSelection() {

		// Vérifier si un des checkbox est sélectionné		
		var nbSel = $('input:checkbox:checked.selectionElement').map(function () { 
			  return this.value; 
		}).size();
	
		// Si aucune sélection, désactiver tous les choix
		if (nbSel == 0) {
			$("#menuModifier").addClass("inactif");
			$("#menuSuivi").addClass("inactif");
			$("#menuImprimer").addClass("inactif");
			$("#menuCorbeille").addClass("inactif");
		}
	
		// Si une sélection, activer tous les choix
		if (nbSel == 1) {
			$("#menuModifier").removeClass("inactif");
			$("#menuSuivi").removeClass("inactif");
			$("#menuImprimer").removeClass("inactif");
			$("#menuCorbeille").removeClass("inactif");
		}				
		
		// Désactiver les items du menu qui ne s'appliquent pas à plusieurs questionnaires
		if (nbSel > 1) {
			$("#menuModifier").addClass("inactif");
			$("#menuSuivi").removeClass("inactif");
			$("#menuImprimer").addClass("inactif");
			$("#menuCorbeille").removeClass("inactif");
		}
	}	
	
	// Démarrage
	$(document).ready(function() {

		// Activer le clic sur les bo��s de séction 
		$(".selectionElement").click(function() {
			verifierSelection();
		});
		
		// Activer la sélection/déselection de tous les checkboxes
	    $('#selectall').change(function () { 
		    if ($(this).attr("checked")) { 
	            $('.selectionElement').prop('checked', true);
	        } else {
	            $('.selectionElement').prop('checked', false);
	        }
		    verifierSelection();
	    });
	    		
		// Resize des fenêtres inital pour affichage plus rapide de l'interface
		resizePanels();

	}); 
	
	</script>
	
</head>

<body id="bBMedias">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-rech-biblio-medias.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
								<div class="filAriane"><h2><img src="../images/ic-medias.png" alt="<?php echo TXT_MES_MEDIAS ?>" /><?php echo TXT_MES_MEDIAS ?> <span><a class="tools" href="media.php?demande=media_ajouter"><?php echo TXT_AJOUTER_UN_NOUVEAU_MEDIA?></a></span></h2></div>
								
								<form id="frm" name="frm" action="media.php" method="post">
									<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
									<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
									<input type="hidden" name="pagination_page_dest" value="" />
									<input type="hidden" name="tri" value="" />
									<input type="hidden" name="demande" value="media_liste" />
									<table class="tblListe tblListeMedias">
										<tr class="tblNav">
											<td class="alCe">
												<div class="menuContexte">
													<img src="../images/ic-tools.png" alt="" />
													<?php include '../ressources/includes/menu-contexte-medias-liste.php' ?>
												</div>
											</td>
											<td colspan="8">
												<div class="flGa">
													<select name="filtre_type_media" onchange="soumettre()">
														<option value="tous"><?php echo TXT_AFFICHER_TOUS_LES_TYPES ?></option>
														<option value="image" <?php if ($filtreTypeMedia == "image") { echo "selected"; } ?> ><?php echo TXT_IMAGE ?></option>
														<option value="video" <?php if ($filtreTypeMedia == "video") { echo "selected"; } ?>><?php echo TXT_VIDEO ?></option>
														<option value="son" <?php if ($filtreTypeMedia == "son") { echo "selected"; } ?>><?php echo TXT_SON ?></option>
													</select>
												</div>
												<div class="flDr">
													<?php include '../ressources/includes/table-nav-haut.php' ?>
												</div>
											</td>
										</tr>
										<tr>
											<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()" /></th>
											<th class="cCode"><a href="#" class="<?php echo $media->get('tri_id_media') ?>" onclick="changerTri('id_media')"><?php echo TXT_CODE ?></a></th>
											<th class="c3"><a href="#" class="<?php echo $media->get('tri_titre') ?>" onclick="changerTri('titre')"><?php echo TXT_TITRE ?></a></th>
											<th class="c4"><a href="#" class="<?php echo $media->get('type') ?>" onclick="changerTri('type')"><?php echo TXT_TYPE?></a></th>
											<th class="c5"><a href="#" class="<?php echo $media->get('description') ?>" onclick="changerTri('description')"><?php echo TXT_DESCRIPTION?></a></th>
											<th class="c6"><a href="#" class="<?php echo $media->get('remarque') ?>" onclick="changerTri('remarque')"><?php echo TXT_REMARQUE ?></a></th>
											<th class="c7"><a href="#" class="<?php echo $media->get('date_modification') ?>" onclick="changerTri('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
											<th class="c8"><a href="#" class="<?php echo $media->get('suivi') ?>" onclick="changerTri('suivi')"><img src="../images/ic-star-gris.png" alt="" /></a></th>
											<th class="cLink last"><img src="../images/ic-link-2.png" alt="" /></th>
										</tr>
										
								<?php foreach($listeMedia as $element){ ?> 										
										<tr>
											<td class="cCheck"><input class="noBord selectionElement" type="checkbox" name="medias_selection_<?php echo $element->get("id_media")?>" value="<?php echo $element->get("id_media")?>" /></td>
											<td><?php echo $element->get("id_prefix")?></td>
											<td class="alGa"><a href="media.php?demande=media_modifier&media_id_media=<?php echo $element->get("id_media") ?>"><?php echo $element->get("titre")?></a></td>
											<td><?php echo $element->get("type_txt") ?></td>
											<td class="alGa"><?php echo $element->get("description") ?></td>
											<td class="alGa"><?php echo $element->get("remarque") ?></td>
											<td><?php echo $element->get("date_modification") ?></td>
											<td class="c8">
											  <?php if ($element->get('suivi') == "1" ) { ?>
												  <a href="media.php?demande=media_liste_suivi_desactiver&media_id_media=<?php echo $element->get('id_media') ?>">
													<img src="../images/ic-star-jaune.png" alt="" />
												  </a>
											  <?php } else { ?>
												  <a href="media.php?demande=media_liste_suivi_activer&media_id_media=<?php echo $element->get('id_media') ?>">
													<img src="../images/ic-star-gris.png" alt="" />
												  </a>
											  <?php } ?>
											</td>
	
											  <?php 
											  		$liens = "";
	
													// Liste des items pour ce média
													$listeLiensItems = $element->get("liste_liens_items");
													if (! empty($listeLiensItems) ) {
														 foreach ($listeLiensItems as $lien) { $liens .= $lien . "<br />"; }
													}
											  		
											  		// Listes des questionnaires pour ce média
											  		$listeLiensQuest = $element->get("liste_liens_questionnaires");
													if (! empty($listeLiensQuest) ) {
														 foreach ($listeLiensQuest as $lien) { $liens .= $lien . "<br />"; }
													}
	
													// Liste des langues pour ce média
													$listeLiensLangues = $element->get("liste_liens_langues");
													if (! empty($listeLiensLangues) ) {
														 foreach ($listeLiensLangues as $lien) { $liens .= $lien . "<br />"; }
													}
											  ?>
	
											<td class="cLink last"><a class="infobulle infobulleGa" href="#"><?php if ($liens != "") { ?><img src="../images/ic-link.png" alt="" /><span><?php echo $liens ?></span></a><?php } ?></td>										
										</tr>
								<?php } ?>
	
										<tr class="lgLast tblNav">
											<td colspan="9" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
										</tr>
									</table>
								</form>
								
							</div> <!-- /contenuPrincipal -->							
						</div> <!-- /contenu -->
					</div> <!-- /zoneContenu -->
				</div> <!-- /colD -->
			</div> <!-- /jqxSplitter -->
		</div> <!-- /corps -->
	
		<?php include '../ressources/includes/piedpage.php' ?>
	</div> <!-- /bodyContenu -->
</body>
</html>
