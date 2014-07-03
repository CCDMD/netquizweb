<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include '../ressources/includes/librairies.php' ?>
    
	<script type="text/javascript">

		// Fermer la fenêtre jaillissante pour sélectioner des items
		function fermerSelectionItems(idItemDest) {
	
			// Fermer la fenêtre fancybox
			$.fancybox.close();
			
			// Si un idItem de destination est disponible, rediriger cet item
			if (idItemDest != "" && idItemDest != 0) {
				document.frm.demande.value="item_modifier";
				document.frm.item_id_item.value=idItemDest;
			} 
			document.frm.submit();
		}
	
		// Changer de page
		function changerPage(page) {
	
			// Vérifier si on peut changer la page
			pageCour = "<?php echo $pagination->getPageCour() ?>";
	
			if (page != pageCour) {
				document.frm.pagination_page_dest.value=page;
				document.frm.item_id_item.value="";
				document.frm.submit();
			}
		}
	
	
		// Démarrage
		$(document).ready(function() {

			// Afficher aperçu au besoin
			afficherApercu('<?php echo $pageInfos['apercu'] ?>');
			
		 	// Activer la fenêtre jaillissante
		 	if ("<?php echo $quest->get("selectionItems") ?>" == "1") {
		 		$(".fenetreSelItems").trigger('click');
		 	}
		 	
			// Désactiver certaines fonctions selon le statut du questionnaire
			if ('<?php echo $quest->get("statut")?>' == '1') {
				$("#questionnaireVoir").addClass("inactif");
				$("#questionnaireDesactiver").addClass("inactif");
			}	

			// Resize des fenêtres inital pour affichage plus rapide de l'interface
			resizePanels();
			
		});

	</script>     
	
</head>

<body id="bQuestionnaire" onload="resizePanels();">

	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">
			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-quest1.php' ?>
					<?php include '../ressources/includes/ss-menu-quest2.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-questionnaires.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
								<form id="frm" name="frm" action="questionnaires.php" method="post">
									<input type="hidden" name="demande" value="item_sauvegarder" />
									<input type="hidden" name="item_id_item_section" value="<?php echo $item->get("id_item_section") ?>" />
									<input type="hidden" name="item_id_item" value="<?php echo $item->get("id_item") ?>" />
									<input type="hidden" name="item_type_item" value="<?php echo $item->get("type_item") ?>" />
									<input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />
									<input type="hidden" name="pagination_page_dest" value="" />
									<input type="hidden" name="items_selectionner" value="" />
									
									<div class="flDr statut">
										<div class="displayInline"><span class="txtTitre"><?php echo TXT_STATUT ?>&nbsp;:&nbsp;</span></div>
										<div class="menuContexteGa displayInline">
											<a class="tools" href="#"><span class="txtStatut" id="statutQuestionnaire"><?php echo $quest->getStatutTxt() ?></span><img src="../images/ic-tools-2.png" alt="" /></a>
											<?php include '../ressources/includes/menu-contexte-quest-publier.php' ?>
										</div>
									</div>
									
									<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_MES_QUESTIONNAIRES ?>" /><a href="questionnaires.php"><?php echo TXT_MES_QUESTIONNAIRES ?></a><span class="sep">&gt;</span><a href=questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>"><?php echo $quest->get("titre") ?></a> <span class="id">(<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire")?>)</span><span class="sep">&gt;</span><?php echo $item->get("titre")?></h2></div>
	
									<div class="onglets">
										<div id="onglet1" class="ongletActif"><div><a href="#" onclick="javascript:showSection('onglet','section',1);return false;"><?php echo TXT_PARAMETRES ?></a></div></div>
										<div class="tools  menuContexteGa itemTools">
											<img src="../images/ic-tools.png" alt="" />
											<?php include '../ressources/includes/menu-contexte-quest-section.php' ?>
										</div>
									</div>
	
									<div class="detail">
										<div class="detailTop"><div>
											<input class="btnReset annuler" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" />
										</div></div>
	
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
	
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
	
												<p><label for="item_titre"><?php echo TXT_TITRE_DE_LA_SECTION ?></label>
													<input class="wmax suiviModif" type="text" name="item_titre" id="item_titre" value="<?php echo $item->get("titre") ?>" /></p>
												<p><label for="item_generation_question_type"><?php echo TXT_GENERATION_DES_ITEMS ?></label>
	
													<select class="w250 suiviModif" name="item_generation_question_type" id="item_generation_question_type">
														<option value="aleatoire" <?php echo $item->get('generation_question_type_aleatoire'); ?> ><?php echo TXT_ORDRE_ALEATOIRE ?></option>
														<option value="predetermine" <?php echo $item->get('generation_question_type_predetermine'); ?> ><?php echo TXT_ORDRE_PREDETERMINE ?></option>
													</select>
												</p>
											</div>						
										</div>						
	
										<div class="detailBot"><div>
											<input class="btnReset annuler" name="btnReset" id="btnReset2" type="reset" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="submit" value="<?php echo TXT_ENREGISTRER?>" /></div>
										</div>		
									</div>					
								</form>
							</div> <!-- /contenuPrincipal -->
						</div> <!-- /contenu -->
					</div> <!-- /zoneContenu -->
				</div> <!-- /colD -->
			
			</div> <!-- /jqxSplitter -->
		</div> <!-- /corps -->
	
		<?php include '../ressources/includes/piedpage.php' ?>
	</div> <!-- /bodyContenu -->

<!--  Lien pour fenêtre jaillissante servant à l'importation d'items -->
<a class="fenetreSelItems" href="questionnaires.php?demande=items_selectionner&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>"></a>
</body>
</html>
