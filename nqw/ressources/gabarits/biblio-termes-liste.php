<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_TERMES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

	<script type="text/javascript">
	
	// Changer de page
	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $pagination->getPageCour() ?>";

		if (page != pageCour) {
			document.frm.demande.value="terme_liste";
			document.frm.pagination_page_dest.value=page;
			document.frm.submit();
		}
	}

	function verifierSelection() {

		// Vérifier si un des checkbox est sélectionné		
		var nbSel = $('input:checkbox:checked.selectionElement').map(function () { 
			  return this.value; 
		}).size();
	
		// Si aucune sélection, désactiver tous les choix
		if (nbSel == 0) {
			$("#menuModifier").addClass("inactif");
			$("#menuDupliquer").addClass("inactif");
			$("#menuExporter").addClass("inactif");
			$("#menuImprimer").addClass("inactif");
			$("#menuCorbeille").addClass("inactif");
		}
	
		// Si une sélection, activer tous les choix
		if (nbSel == 1) {
			$("#menuModifier").removeClass("inactif");
			$("#menuDupliquer").removeClass("inactif");
			$("#menuExporter").removeClass("inactif");
			$("#menuImprimer").removeClass("inactif");
			$("#menuCorbeille").removeClass("inactif");
		}				
		
		// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
		if (nbSel > 1) {
			$("#menuModifier").addClass("inactif");
			$("#menuDupliquer").removeClass("inactif");
			$("#menuExporter").removeClass("inactif");
			$("#menuImprimer").removeClass("inactif");
			$("#menuCorbeille").removeClass("inactif");
		}
	}

	function supprimerListeTermes() {

		if (confirm('<?php echo TXT_VOULEZ_VOUS_SUPPRIMER_CES_TERMES ?>')) {
			soumettreDemande('terme_supprimer');
		}
	}
	
	// Démarrage
	$(document).ready(function() {

		// Resize des fenêtres inital pour affichage plus rapide de l'interface
		resizePanels();

		// Activer le clic sur les boîtes de sélection 
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

		// Afficher aperçu au besoin
		afficherApercu('<?php echo $item->get("apercu")?>');
		
	}); 
	
</script>
	
</head>

<body id="bBTermes" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-vide.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<div class="filAriane"><h2><img src="../images/ic-termes.png" alt="<?php echo TXT_MES_TERMES ?>" /><?php echo TXT_MES_TERMES ?><span><a class="tools" href="bibliotheque.php?demande=terme_ajouter"><?php echo TXT_AJOUTER_UN_NOUVEAU_TERME ?></a></span></h2></div>
								
								<form id="frm" name="frm" action="bibliotheque.php" method="post">
									<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
									<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
									<input type="hidden" name="pagination_page_dest" value="" />
									<input type="hidden" name="tri" value="" />
									<input type="hidden" name="demande" value="terme_liste" />
									
									<table class="tblListe">
										<tr class="tblNav">
											<td class="alCe">
												<div class="menuContexte">
													<img src="../images/ic-tools.png" alt="" />
													<?php include '../ressources/includes/menu-contexte-termes-liste.php' ?>
												</div>
											</td>
											<td colspan="7">
												<div class="flDr">
													<?php include '../ressources/includes/table-nav-haut.php' ?>
												</div>
											</td>
										</tr>
										
										<tr>
											<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()" /></th>
											<th class="cCode"><a href="#" class="<?php echo $terme->get('tri_id_terme') ?>" onclick="changerTriTermes('id_terme')"><?php echo TXT_CODE ?></a></th>
											<th class="c3"><a href="#" class="<?php echo $terme->get('tri_terme') ?>" onclick="changerTriTermes('terme')"><?php echo TXT_TERME ?></a></th>
											<th class="c4"><a href="#" class="<?php echo $terme->get('tri_variantes') ?>" onclick="changerTriTermes('variantes')"><?php echo TXT_VARIANTES ?></a></th>
											<th class="c5"><?php echo TXT_DEFINITION ?></th>
											<th class="c6"><a href="#" class="<?php echo $terme->get('tri_remarque') ?>" onclick="changerTriTermes('remarque')"><?php echo TXT_REMARQUE ?></a></th>
											<th class="c7 last"><a href="#" class="<?php echo $terme->get('tri_date_modification') ?>" onclick="changerTriTermes('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
										</tr>
									<?php foreach($listeTermes as $element){ ?> 									
										<tr>
											<td class="cCheck"><input class="noBord selectionElement" type="checkbox" name="termes_selection_<?php echo $element->get("id_terme")?>" value="<?php echo $element->get("id_terme")?>" /></td>
											<td><?php echo $element->get("id_prefix") . $element->get("id_terme") ?></td>
											<td class="alGa"><a href="bibliotheque.php?demande=terme_modifier&terme_id_terme=<?php echo $element->get("id_terme") ?>"><?php echo $element->get("terme")?></a></td>
											<td class="alGa"><?php echo $element->getListeVariantes() ?></td>
											<td class="alGa">
											
											<?php   if ($element->get("type_definition") == "texte") { 
														echo strip_tags(html_entity_decode($element->get("texte"), ENT_QUOTES, "UTF-8"));
													} elseif ($element->get("type_definition") == "url") {
														echo $element->get("url");
													} elseif ($element->get("type_definition") == "media_image") { ?>
											
													<a href="media.php?demande=media_presenter&media_id_media=<?php echo $element->get("media_image") ?>" target="media_<?php echo $element->get("media_image") ?>"><?php echo $element->get("media_image_txt") ?></a>		
													
											<?php	} elseif ($element->get("type_definition") == "media_son") { ?>
						
													<a href="media.php?demande=media_presenter&media_id_media=<?php echo $element->get("media_son") ?>" target="media_<?php echo $element->get("media_son") ?>"><?php echo $element->get("media_son_txt") ?></a>					
											
											<?php	} elseif ($element->get("type_definition") == "media_video") { ?> 
					
													<a href="media.php?demande=media_presenter&media_id_media=<?php echo $element->get("media_video") ?>" target="media_<?php echo $element->get("media_video") ?>"><?php echo $element->get("media_video_txt") ?></a>
					
											<?php 	} ?>
											
											</td>
											
											
											<td class="alGa"><?php echo $element->get("remarque")?></td>
											<td class="last"><?php echo $element->get("date_modification")?></td>
										</tr>
									<?php } ?>
										<tr class="lgLast tblNav">
											<td colspan="7" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
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
