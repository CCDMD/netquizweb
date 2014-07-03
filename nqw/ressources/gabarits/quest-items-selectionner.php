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
				document.frm.demande.value="items_selectionner";
				document.frm.pagination_page_dest.value=page;
				document.frm.submit();
			}
		}

		// Bouton ajouter
		function ajouter() {

			var nbSel = $('input:checkbox:checked.selectionElement').map(function () { 
				  return this.value; 
			}).size();

			if (nbSel > 0) {
				document.frm.demande.value="items_selectionner_sauvegarder";
				document.frm.submit();
			}
		}
		
		// Bouton annuler
		function annuler() {
			// Fermer la fenêtre
			parent.$.fancybox.close();
		}

		$(document).ready(function () {

			// Activer la sélection/déselection de tous les checkboxes
		    $('#selectall').change(function () { 
			    if ($(this).attr("checked")) { 
		            $('.selectionElement').prop('checked', true);
		        } else {
		            $('.selectionElement').prop('checked', false);
		        }
		    });
			
		});
		
	</script>
	
</head>

<body>
	<div class="boxStd">
			<div class="boxTitre">
				<p><?php echo TXT_CHOISIR_DES_ITEMS_DE_LA_BIBLIOTHEQUE ?></p>
			</div>
	
		<div class="boxContenu">
			<?php include '../ressources/includes/barre-rech-quest-items-selectionner.php' ?>
								
			<div class="boxPrincipal">
				<div class="filAriane"><h2><img src="../images/ic-items.png" alt="<?php echo TXT_MES_ITEMS ?>" /><?php echo TXT_MES_ITEMS ?> </h2></div>

				<form id="frm" name="frm" action="questionnaires.php" method="post">
					<input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />
					<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
					<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
					<input type="hidden" name="pagination_page_dest" value="" />
					<input type="hidden" name="tri" value="" />
					<input type="hidden" name="demande" value="items_selectionner" />
					<table class="tblListe tblListeItems">
					
						<tr class="tblNav">
							<td></td>
							<td colspan="8">
								<div class="flGa">
									<select name="filtre_type_item" onchange="soumettreDemandeCourante()">
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
							<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" /></th>
							<th class="cCode"><a href="#" class="<?php echo $item->get('tri_id_item') ?>" onclick="changerTriItemSel('id_item')"><?php echo TXT_CODE ?></a></th>
							<th class="c3"><a href="#" class="<?php echo $item->get('tri_titre') ?>" onclick="changerTriItemSel('titre')"><?php echo TXT_TITRE ?></a></th>
							<th class="c4"><a href="#" class="<?php echo $item->get('type') ?>" onclick="changerTriItemSel('type')"><?php echo TXT_TYPE?></a></th>
							<th class="c5"><a href="#" class="<?php echo $item->get('categorie') ?>" onclick="changerTriItemSel('id_categorie')"><?php echo TXT_CATEGORIE ?></a></th>
							<th class="c6"><a href="#" class="<?php echo $item->get('remarque') ?>" onclick="changerTriItemSel('remarque')"><?php echo TXT_REMARQUE ?></a></th>
							<th class="c7"><a href="#" class="<?php echo $item->get('date_modification') ?>" onclick="changerTriItemSel('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
							<th class="c8 last"><a href="#" class="<?php echo $item->get('suivi') ?>" onclick="changerTriItemSel('suivi')"><img src="../images/ic-star-gris.png" alt="" /></a></th>
						</tr>
						
						<?php foreach($listeItems as $element){ 
							?>
						 									
						<tr>
							<td><input class="noBord selectionElement" type="checkbox" name="items_selection_<?php echo $element->get("id_item")?>" value="<?php echo $element->get("id_item")?>" /></td>
							<td><?php echo $element->get("id_prefix") ?></td>
							<td class="alGa"><?php echo $element->get("titre")?></td>
							<td><?php echo $element->getTypeItemTxt() ?></td>
							<td><?php echo $element->getCategorieTitre() ?></td>
							<td class="alGa"><?php echo $element->get("remarque")?></td>
							<td><?php echo $element->get("date_modification") ?></td>
							<td class="c8 last">
							  <?php if ($element->get('suivi') == "1" ) { ?>
								  <a href="questionnaires.php?demande=items_selectionner_suivi_desactiver&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>&item_id_item=<?php echo $element->get('id_item') ?>">
									<img src="../images/ic-star-jaune.png" alt="" />
								  </a>
							  <?php } else { ?>
								  <a href="questionnaires.php?demande=items_selectionner_suivi_activer&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>&item_id_item=<?php echo $element->get('id_item') ?>">
									<img src="../images/ic-star-gris.png" alt="" />
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
			</div> <!-- /contenu -->

		</div> <!-- /boxContenu -->

		<div class="boxBottom">
			<input class="btnReset" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>" onclick="annuler()"  />
			<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_AJOUTER ?>" onclick="ajouter()" />
		</div>

	</div> <!-- /boxStd -->
</body>
</html>
