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
				document.frm.demande.value="questionnaire_selectionner";
				document.frm.pagination_page_dest.value=page;
				document.frm.submit();
			}
		}

		// Bouton ajouter
		function ajouter() {
			// Obtenir le questionnaire sélectionné
			selection = $("input[name=questionnaire_selection]:checked").val();

			// Si aucune sélection, désactiver
			if (typeof selection != 'undefined') {

				// Transmettre à l'appellant et soumettre
				parent.ajouterItemsAuQuestionnaire(selection);
	
				// Fermer la fenêtre
				parent.$.fancybox.close();
			}
		}
		
		// Bouton annuler
		function annuler() {
			// Fermer la fenêtre
			parent.$.fancybox.close();
		}
		
		$(document).ready(function () {

		});
		
	</script>
	
</head>

<body>

	<div class="boxStd">
		<div class="boxTitre">
			<p><?php echo TXT_CHOISIR_UN_QUESTIONNAIRE ?></p>
		</div>

		<div class="boxContenu">

			<?php include '../ressources/includes/barre-rech-items-quest-selectionner.php' ?>

			<div class="boxPrincipal">
				<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_MES_QUESTIONNAIRES ?>" /><?php echo TXT_MES_QUESTIONNAIRES ?></h2></div>
					
				<form id="frm" name="frm" action="questionnaires.php">
					<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
					<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
					<input type="hidden" name="pagination_page_dest" value="" />
					<input type="hidden" name="tri" value="" />
					<input type="hidden" name="demande" value="questionnaire_selectionner" />

					<table class="tblListe tblListQuest">
							
						<tr class="tblNav">
							<td colspan="8">
								<div class="flGa">
									<select name="collection" onchange="soumettre()">
										<option value="tous"><?php echo TXT_AFFICHER_TOUTES_LES_COLLECTIONS ?></option>
										<?php foreach ($listeCollection as $id_collection => $libelle) {
											if ($id_collection != 0) { ?>
																								
												<option value="<?php echo $id_collection ?>" <?php if ($pageInfos['idCollection'] == $id_collection) { echo "selected"; } ?> ><?php echo $libelle ?></option>
										
										<?php }
										} ?>													
									</select>
								</div>
								<div class="flDr">
									<?php include '../ressources/includes/table-nav-haut.php' ?>
								</div>
							</td>
						</tr>
						<tr>
							<th class="cCheck"></th>
							<th class="cCode"><a href="#" class="<?php echo $quest->get('tri_id_questionnaire') ?>" onclick="changerTriSelQuest('id_questionnaire')"><?php echo TXT_CODE ?></a></th>
							<th class="c3"><a href="#" class="<?php echo $quest->get('tri_titre') ?>" onclick="changerTriSelQuest('titre')"><?php echo TXT_TITRE ?></a></th>
							<th class="c4"><a href="#" class="<?php echo $quest->get('tri_nb_items') ?>" onclick="changerTriSelQuest('nb_items')"><?php echo TXT_ITEMS ?></a></th>
							<th class="c5"><a href="#" class="<?php echo $quest->get('tri_collection') ?>" onclick="changerTriSelQuest('collection')"><?php echo TXT_COLLECTION ?></a></th>
							<th class="c6"><a href="#" class="<?php echo $quest->get('tri_statut') ?>" onclick="changerTriSelQuest('statut')"><?php echo TXT_STATUT ?></a></th>
							<th class="c7"><a href="#" class="<?php echo $quest->get('tri_date_modification') ?>" onclick="changerTriSelQuest('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
							<th class="c8 last"><a href="#" class="<?php echo $quest->get('tri_suivi') ?>" onclick="changerTriSelQuest('suivi')"><img src="../images/ic-star-gris.png" alt="<?php echo TXT_ACTIVER_SUIVI ?>" /></a></th>
						</tr>

						<?php foreach($listeQuestionnaires as $questionnaire){ ?> 
						
							<tr>
								<td><input class="noBord selectionQuest" type="radio" name="questionnaire_selection" value="<?php echo $questionnaire->get("id_questionnaire")?>" /></td>
								<td>Q<?php echo $questionnaire->get('id_questionnaire') ?></td>
								<td class="alGa"><a href="#" onclick="modifierQuestionnaire('<?php echo $questionnaire->get('id_questionnaire') ?>')"><?php echo $questionnaire->get('titre') ?></a></td>
								<td><?php echo $questionnaire->get('nb_items')+0; ?></td>
								<td><?php echo $questionnaire->get('collection'); ?></td>
								<td><?php echo $questionnaire->getStatutTxt() ?></td>
								<td><?php echo $questionnaire->get('date_modification') ?></td>
								<td class="c8 last">
								  <?php if ($questionnaire->get('suivi') == "1" ) { ?>
									  <a href="questionnaires.php?demande=questionnaire_selectionner_suivi_desactiver&questionnaire_id_questionnaire=<?php echo $questionnaire->get('id_questionnaire') ?>">
										<img src="../images/ic-star-jaune.png" alt="" />
									  </a>
								  <?php } else { ?>
									  <a href="questionnaires.php?demande=questionnaire_selectionner_suivi_activer&questionnaire_id_questionnaire=<?php echo $questionnaire->get('id_questionnaire') ?>">
										<img src="../images/ic-star-gris.png" alt="" />
									  </a>
								  <?php } ?>
								</td>
							</tr>
						
						<?php }?>
						
						<tr class="lgLast tblNav">
							<td colspan="8" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
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
