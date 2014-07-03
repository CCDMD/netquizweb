<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include '../ressources/includes/librairies-avec-editeur.php' ?>
	
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
	
	function changerPage(page) {

		// Verifier si on peut changer la page
		pageCour = "<?php echo $quest->get("page_courante") ?>";

		if (page != pageCour) {
			urlDest = "questionnaires.php?demande=questionnaire_modifier&questionnaire_page=" + page;
			document.location=urlDest;
		}
	}
	
	// Démarrage
	$(document).ready(function() {

		// Activer le flag des modifications au besoin
		activerSuiviModifications('<?php echo $pageInfos["flagModifications"] ?>');
	 	
	 	// Activer la fenêtre jaillissante
	 	if ("<?php echo $quest->get("selectionItems") ?>" == "1") {
	 		$(".fenetreSelItems").trigger('click');
	 	}

		// Afficher aperçu au besoin
		afficherApercu('<?php echo $pageInfos['apercu'] ?>');

		// Désactiver certaines fonctions selon le statut du questionnaire
		if ('<?php echo $quest->get("statut")?>' == '1') {
			$("#questionnaireVoir").addClass("inactif");
			$("#questionnaireDesactiver").addClass("inactif");
		}
		
		// Initialiser les panneaux
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
								<div class="flDr statut">
									<div class="displayInline"><span class="txtTitre"><?php echo TXT_STATUT ?>&nbsp;:&nbsp;</span></div>
									<div class="menuContexteGa displayInline">
										<a class="tools" href="#"><span class="txtStatut" id="statutQuestionnaire"><?php echo $quest->getStatutTxt() ?></span>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
										<?php include '../ressources/includes/menu-contexte-quest-publier.php' ?>
									</div>
								</div>
								<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_MES_QUESTIONNAIRES ?>" /><a href="questionnaires.php"><?php echo TXT_MES_QUESTIONNAIRES ?></a><span class="sep">&gt;</span><a href="questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>"><?php echo $quest->get("titre") ?></a> <span class="id">(<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire")?>)</span><span class="sep">&gt;</span><?php echo TXT_FIN_DU_QUESTIONNAIRE ?></h2></div>
								<form id="frm" name="frm" action="questionnaires.php" method="post">
									<input type="hidden" name="demande" value="fin_sauvegarder" />
									<input type="hidden" name="demandeRetour" value="" />
									<input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />
									<input type="hidden" name="item_id_item" value="" />
									<input type="hidden" name="items_selectionner" value="" />
									<input type="hidden" name="flagModifications" value="" />
									
									<div class="onglets">
										<div id="onglet1" class="ongletActif"><div><a href="#" onclick="javascript:showSection('onglet','section',1);return false;"><?php echo TXT_CONTENU ?></a></div></div>
										<div class="tools menuContexteGa itemTools">
											<img src="../images/ic-tools.png" alt="" />
											<?php include '../ressources/includes/menu-contexte-quest-fin.php' ?>
										</div>
									</div>
	
									<div class="detail">
										<div class="detailTop"><div>
											<input class="btnApercu" name="btnApercu" id="btnApercu1" type="button" onclick="apercuQuestionnaire('questionnaire_apercu', 'fin_modifier')" value="<?php echo TXT_APERCU ?>"  />
											<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annulerQuestionnaire('fin_modifier')" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" />
										</div></div>
	
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
	
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
	
												<p><label for="texte_fin"><?php echo TXT_TEXTE ?></label>
													<textarea name="questionnaire_texte_fin" id="questionnaire_texte_fin" class="wmax editeur suiviModif" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $quest->get("texte_fin") ?></textarea></p>
											</div>						
										</div>						
	
										<div class="detailBot"><div>
											<input class="btnApercu" name="btnApercu" id="btnApercu2" type="button" onclick="apercuQuestionnaire('questionnaire_apercu', 'fin_modifier')" value="<?php echo TXT_APERCU ?>"  />
											<input class="btnReset" name="btnReset" id="btnReset2" type="button" onclick="annulerQuestionnaire('fin_modifier')" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
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

	<!--  Lien pour fenêtre jaillissante pour l'ajout de média nqw -->
	<a class="fenetreEditeurMedia" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=image"></a>
		
	<!--  Lien pour fenêtre jaillissante servant à l'importation d'items -->
	<a class="fenetreSelItems" href="questionnaires.php?demande=items_selectionner&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>"></a>
	
</body>
</html>
