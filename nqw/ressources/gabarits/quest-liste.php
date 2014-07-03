<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>	
	
	<script type="text/javascript">

		function changerPage(page, pageCour) {

			// Vérifier si on peut changer la page
			pageCour = "<?php echo $pagination->getPageCour() ?>";

			if (page != pageCour) {
				document.frm.pagination_page_dest.value=page;
				document.frm.submit();
			}
		}
		
		function verifierSelection() {
			// Vérifier si un des checkbox est sélectionné			
			var nbSel = $('input:checkbox:checked.selectionQuest').map(function () { 
				  return this.value; 
			}).size();

			// Si aucune sélection, désactiver tous les choix
			if (nbSel == 0) {
				$("#menuModifier").addClass("inactif");
				$("#menuDupliquer").addClass("inactif");
				$("#menuSuivi").addClass("inactif");
				$("#menuApercu").addClass("inactif");
				$("#menuExporter").addClass("inactif");
				$("#menuTelecharger").addClass("inactif");
				$("#menuImprimer").addClass("inactif");
				$("#menuCorbeille").addClass("inactif");
			}

			// Si une sélection, activer tous les choix
			if (nbSel == 1) {
				$("#menuModifier").removeClass("inactif");
				$("#menuDupliquer").removeClass("inactif");
				$("#menuSuivi").removeClass("inactif");
				$("#menuApercu").removeClass("inactif");
				$("#menuExporter").removeClass("inactif");
				$("#menuTelecharger").removeClass("inactif");
				$("#menuImprimer").removeClass("inactif");
				$("#menuCorbeille").removeClass("inactif");
			}				
			
			// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
			if (nbSel > 1) {
				$("#menuModifier").addClass("inactif");
				$("#menuDupliquer").removeClass("inactif");
				$("#menuSuivi").removeClass("inactif");
				$("#menuApercu").addClass("inactif");
				$("#menuExporter").addClass("inactif");
				$("#menuTelecharger").addClass("inactif");
				$("#menuImprimer").addClass("inactif");
				$("#menuCorbeille").removeClass("inactif");
			}
		}

		
		$(document).ready(function () {

			// Afficher aperçu au besoin
			afficherApercu('<?php echo $pageInfos['apercu'] ?>');
			
			// Activer le clic sur les boîtes de sélection 
			$(".selectionQuest").click(function() {
				verifierSelection();
			});

			// Activer la sélection/déselection de tous les checkboxes
		    $('#selectall').change(function () { 
			    if ($(this).attr("checked")) { 
		            $('.selectionQuest').prop('checked', true);
		        } else {
		            $('.selectionQuest').prop('checked', false);
		        }
	          	verifierSelection();		        
		    });

			// Désactiver certaines fonctions selon le statut du questionnaire
			if ('<?php echo $quest->get("statut")?>' == '1') {
				$("#questionnaireVoir").addClass("inactif");
				$("#questionnaireDesactiver").addClass("inactif");
			}
		});
	</script>
	
</head>

<body id="bQuestionnaires" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-quest1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-rech.php' ?> 
						<div id="contenu">
						  <div id="contenuPrincipal">
								<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_MES_QUESTIONNAIRES ?>" /><?php echo TXT_MES_QUESTIONNAIRES ?><span><a class="tools" href="questionnaires.php?demande=questionnaire_ajouter"><?php echo TXT_AJOUTER_NOUVEAU_QUESTIONNAIRE ?></a></span></h2></div>
								
								<!--  Messages -->
								<?php include '../ressources/includes/message_tableaux.php' ?>
								<!--  /Messages -->
								
								<form id="frm" name="frm" action="questionnaires.php">
									<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
									<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
									<input type="hidden" name="pagination_page_dest" value="" />
									<input type="hidden" name="tri" value="" />
									<input type="hidden" name="demande" value="" />
									
									<table class="tblListe tblListQuest">
										<tr class="tblNav">
											<td class="alCe">
												<div class="menuContexte">
													<img src="../images/ic-tools.png" alt="" />
													<?php include '../ressources/includes/menu-contexte-quest-liste.php' ?>
												</div>
											</td>
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
											<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()" /></th>
											<th class="cCode"><a href="#" class="<?php echo $quest->get('tri_id_questionnaire') ?>" onclick="changerTri('id_questionnaire')"><?php echo TXT_CODE ?></a></th>
											<th class="c3"><a href="#" class="<?php echo $quest->get('tri_titre') ?>" onclick="changerTri('titre')"><?php echo TXT_TITRE ?></a></th>
											<th class="c4"><a href="#" class="<?php echo $quest->get('tri_nb_items') ?>" onclick="changerTri('nb_items')"><?php echo TXT_ITEMS ?></a></th>
											<th class="c5"><a href="#" class="<?php echo $quest->get('tri_collection') ?>" onclick="changerTri('collection')"><?php echo TXT_COLLECTION ?></a></th>
											<th class="c6"><a href="#" class="<?php echo $quest->get('tri_statut') ?>" onclick="changerTri('statut')"><?php echo TXT_STATUT ?></a></th>
											<th class="c7"><a href="#" class="<?php echo $quest->get('tri_remarque') ?>" onclick="changerTri('remarque')"><?php echo TXT_REMARQUE ?></a></th>
											<th class="c8"><a href="#" class="<?php echo $quest->get('tri_date_modification') ?>" onclick="changerTri('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
											<th class="c9 last"><a href="#" class="<?php echo $quest->get('tri_suivi') ?>" onclick="changerTri('suivi')"><img src="../images/ic-star-gris.png" alt="<?php echo TXT_ACTIVER_SUIVI ?>" /></a></th>
										</tr>
	
										<?php foreach($listeQuestionnaires as $questionnaire){ ?> 
										
											<tr>
												<td class="cCheck"><input class="noBord selectionQuest" type="checkbox" name="questionnaires_selection_<?php echo $questionnaire->get("id_questionnaire")?>" value="<?php echo $questionnaire->get("id_questionnaire")?>" /></td>
												<td>Q<?php echo $questionnaire->get('id_questionnaire') ?></td>
												<td><a href="#" onclick="modifierQuestionnaire('<?php echo $questionnaire->get('id_questionnaire') ?>')"><?php echo $questionnaire->get('titre') ?></a></td>
												<td class="alCe"><?php echo $questionnaire->get('nb_items')+0; ?></td>
												<td><?php echo $questionnaire->get('collection'); ?></td>
												<td><?php echo $questionnaire->getStatutTxt() ?></td>
												<td><?php echo $questionnaire->get('remarque') ?></td>
												<td><?php echo $questionnaire->get('date_modification') ?></td>
												<td class="c9 last">
												  <?php if ($questionnaire->get('suivi') == "1" ) { ?>
													  <a href="questionnaires.php?demande=questionnaire_suivi_desactiver&questionnaire_id_questionnaire=<?php echo $questionnaire->get('id_questionnaire') ?>">
														<img src="../images/ic-star-jaune.png" alt="" />
													  </a>
												  <?php } else { ?>
													  <a href="questionnaires.php?demande=questionnaire_suivi_activer&questionnaire_id_questionnaire=<?php echo $questionnaire->get('id_questionnaire') ?>">
														<img src="../images/ic-star-gris.png" alt="" />
													  </a>
												  <?php } ?>
												</td>
											</tr>
										
										<?php }?>
										
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
