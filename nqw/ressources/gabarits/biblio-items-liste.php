<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ITEMS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">

		function changerPage(page, pageCour) {

			// Vérifier si on peut changer la page
			pageCour = "<?php echo $pagination->getPageCour() ?>";

			if (page != pageCour) {
				document.frm.demande.value="liste";
				document.frm.pagination_page_dest.value=page;
				document.frm.submit();
			}
		}

		// Enregistrer la sélection du questionnaire et soumettre
		function ajouterItemsAuQuestionnaire(idQuest) {
			document.frm.questionnaire_dest.value = idQuest;
			document.frm.demande.value="item_ajouter_questionnaire";
			document.frm.submit();
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
				$("#menuSuivi").addClass("inactif");
				$("#menuApercu").addClass("inactif");
				$("#menuAjouter").addClass("inactif");
				$("#menuExporter").addClass("inactif");
				$("#menuImprimer").addClass("inactif");
				$("#menuCorbeille").addClass("inactif");
			}

			// Si une sélection, activer tous les choix
			if (nbSel == 1) {
				$("#menuModifier").removeClass("inactif");
				$("#menuDupliquer").removeClass("inactif");
				$("#menuSuivi").removeClass("inactif");
				$("#menuApercu").removeClass("inactif");
				$("#menuAjouter").removeClass("inactif");
				$("#menuExporter").removeClass("inactif");
				$("#menuImprimer").removeClass("inactif");
				$("#menuCorbeille").removeClass("inactif");
			}				
			
			// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
			if (nbSel > 1) {
				$("#menuModifier").addClass("inactif");
				$("#menuDupliquer").removeClass("inactif");
				$("#menuSuivi").removeClass("inactif");
				$("#menuApercu").addClass("inactif");
				$("#menuAjouter").removeClass("inactif");
				$("#menuExporter").removeClass("inactif");
				$("#menuImprimer").addClass("actif");
				$("#menuCorbeille").removeClass("inactif");
			}
		}

		
		$(document).ready(function () {

			// Afficher aperçu au besoin
			afficherApercu('<?php echo $pageInfos['apercu'] ?>');
			
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

<body id="bBItems" onload="resizePanels();">

	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-rech-biblio-items.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<div class="filAriane">
									<h2><img src="../images/ic-items.png" alt="<?php echo TXT_MES_ITEMS ?>" /><?php echo TXT_MES_ITEMS ?> 
									<!--<span class="tools"><a>Ajouter un nouvel item <img src="../images/ic-tools-2.png" alt="" /></a></span>-->
										<div class="menuContexte displayInline">
											<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_NOUVEL_ITEM ?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a><?php include '../ressources/includes/menu-contexte-items-ajouter-biblio.php' ?>
										</div>
									</h2>
								</div>
								<!--  Messages -->
								<?php include '../ressources/includes/message_tableaux.php' ?>
								<!--  /Messages -->
								
								<form id="frm" name="frm" action="bibliotheque.php" method="get">
									<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
									<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
									<input type="hidden" name="pagination_page_dest" value="" />
									<input type="hidden" name="tri" value="" />
									<input type="hidden" name="demande" value="liste" />
									<input type="hidden" name="questionnaire_selectionner" value="" />
									<input type="hidden" name="questionnaire_dest" value="" />
									<table class="tblListe tblListeItems">
										
										<tr class="tblNav">
											<td class="alCe">
												<div class="menuContexte">
													<img src="../images/ic-tools.png" alt="" />
													<?php include '../ressources/includes/menu-contexte-items-liste.php' ?>
												</div>
											</td>
											<td colspan="8">
												<div class="flGa">
													<select name="filtre_type_item" onchange="soumettreDemandeListe()">
														<option value="tous"><?php echo TXT_AFFICHER_TOUS_LES_TYPES ?></option>
														
														<?php foreach ($listeTypesItems as $type_id => $type_libelle) { ?>
														
															<option value="<?php echo $type_id ?>" <?php if ($type_id == $filtreTypeItem) { echo "selected"; } ?>><?php echo $type_libelle?></option>
														
														<?php } ?>
														
													</select>
												</div>
												<div class="flDr">
													<?php include '../ressources/includes/table-nav-haut.php' ?>
												</div>
											</td>
										</tr>
										<tr>
											<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox"  onclick="verifierSelection()" /></th>
											<th class="cCode"><a href="#" class="<?php echo $item->get('tri_id_item') ?>" onclick="changerTri('id_item')"><?php echo TXT_CODE ?></a></th>
											<th class="c3"><a href="#" class="<?php echo $item->get('tri_titre') ?>" onclick="changerTri('titre')"><?php echo TXT_TITRE ?></a></th>
											<th class="c4"><a href="#" class="<?php echo $item->get('tri_type') ?>" onclick="changerTri('type')"><?php echo TXT_TYPE?></a></th>
											<th class="c5"><a href="#" class="<?php echo $item->get('tri_id_categorie') ?>" onclick="changerTri('id_categorie')"><?php echo TXT_CATEGORIE ?></a></th>
											<th class="c6"><a href="#" class="<?php echo $item->get('tri_remarque') ?>" onclick="changerTri('remarque')"><?php echo TXT_REMARQUE ?></a></th>
											<th class="c7"><a href="#" class="<?php echo $item->get('tri_date_modification') ?>" onclick="changerTri('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
											<th class="c8"><a href="#" class="<?php echo $item->get('tri_suivi') ?>" onclick="changerTri('suivi')"><img src="../images/ic-star-gris.png" alt="" /></a></th>
											<th class="cLink last"><img src="../images/ic-link-2.png" alt="" /></th>
										</tr>
										
										<?php foreach($listeItems as $element){ ?>
																			
										<tr>
											<td class="cCheck"><input class="noBord selectionElement" type="checkbox" name="items_selection_<?php echo $element->get("id_item")?>" value="<?php echo $element->get("id_item")?>" /></td>
											<td><?php echo $element->get("id_prefix") ?></td>
											<td class="alGa"><a href="bibliotheque.php?demande=item_modifier&item_id_item=<?php echo $element->get("id_item") ?>"><?php echo $element->get("titre")?></a></td>
											<td><?php echo $element->getTypeItemTxt() ?></td>
											<td><?php echo $element->getCategorieTitre() ?></td>
											<td class="alGa"><?php echo $element->get("remarque")?></td>
											<td><?php echo $element->get("date_modification") ?></td>
											<td class="c8">
											  <?php if ($element->get('suivi') == "1" ) { ?>
												  <a href="bibliotheque.php?demande=item_liste_suivi_desactiver&item_id_item=<?php echo $element->get('id_item') ?>">
													<img src="../images/ic-star-jaune.png" alt="" />
												  </a>
											  <?php } else { ?>
												  <a href="bibliotheque.php?demande=item_liste_suivi_activer&item_id_item=<?php echo $element->get('id_item') ?>">
													<img src="../images/ic-star-gris.png" alt="" />
												  </a>
											  <?php } ?>
											</td>
											<td class="cLink last">
												<?php $listeLiens = $element->get("liste_liens_questionnaires"); if (! empty($listeLiens) ) { ?>
													<a class="infobulle infobulleGa" href="#"><img src="../images/ic-link.png" alt="" /><span>
													<?php foreach ($listeLiens as $lien) { echo $lien . "<br />"; }?></span>
													</a>
												<?php } ?>
											</td>
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

	<!--  Lien pour fenêtre jaillissante servant à l'ajout d'items dans un questionnaire -->
	<a class="fenetreSelQuest" href="questionnaires.php?demande=questionnaire_selectionner"></a>
	
</body>
</html>
