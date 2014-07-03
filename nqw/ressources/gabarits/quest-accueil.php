<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include '../ressources/includes/librairies-avec-editeur.php' ?>
    
	<script type="text/javascript">

	// Fermer la fenêtre jaillissante pour sélectionner des items
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

		// Sélectionner l'onglet
		selectionnerOnglet("<?php echo $onglet ?>");

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
			 			
	});
	
	</script>	
	
</head>

<body id="bQuestionnaire">

<!--  Lien pour fenêtre jaillissante servant à l'importation d'items -->
<a class="fenetreSelItems" href="questionnaires.php?demande=items_selectionner&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>"></a>


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
								<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_MES_QUESTIONNAIRES ?>" /><a href="questionnaires.php"><?php echo TXT_MES_QUESTIONNAIRES ?></a><span class="sep">&gt;</span><a href="questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>"><?php echo $quest->get("titre") ?></a><span class="id"> (<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire")?>)</span><span class="sep">&gt;</span><?php echo TXT_PAGE_ACCUEIL ?></h2></div>
								
								<form id="frm" name="frm" action="questionnaires.php" method="post">
									<input type="hidden" name="demande" value="accueil_sauvegarder" />
									<input type="hidden" name="demandeRetour" value="" />
									<input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />
									<input type="hidden" name="item_id_item" value="" />
									<input type="hidden" name="onglet" value="" />
									<input type="hidden" name="items_selectionner" value="" />
									<input type="hidden" name="flagModifications" value="" />
									
									<div class="onglets">
										<div id="onglet1" class="ongletActif"><div><a href="#" onclick="javascript:changerOnglet('1');return false;"><?php echo TXT_CONTENU ?></a></div></div>
										<div id="onglet2" class="ongletInactif"><div><a href="#" onclick="javascript:changerOnglet('2');return false;"><?php echo TXT_COMPLEMENTS ?></a></div></div>
										<div class="tools menuContexteGa itemTools">
											<img src="../images/ic-tools.png" alt="" />
											<?php include '../ressources/includes/menu-contexte-quest-accueil.php' ?>
										</div>
									</div>
	
									<div class="detail">
										<div class="detailTop"><div>
											<input class="btnApercu" name="btnApercu" id="btnApercu1" type="button" onclick="apercuQuestionnaire('questionnaire_apercu', 'accueil_modifier')" value="<?php echo TXT_APERCU ?>"  />
											<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annulerQuestionnaire('accueil_modifier')" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" />
										</div></div>
	
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
	
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
	
												<p><label for="questionnaire_titre_long"><?php echo TXT_TITRE_QUESTIONNAIRE_PAGE_ACCUEIL ?></label>
													<input class="wmax suiviModif" type="text" name="questionnaire_titre_long" value="<?php echo $quest->get("titre_long") ?>" onclick="fermerEditeurs()"/></p>
												<p><label for="questionnaire_mot_bienvenue"><?php echo TXT_MOT_DE_BIENVENUE ?></label>
													<textarea id="mot_bienvenue" class="wmax editeur suiviModif" name="questionnaire_mot_bienvenue" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $quest->get("mot_bienvenue") ?></textarea></p>
												<div style="width:48%; float:left;"><p><label for="questionnaire_note"><?php echo TXT_NOTE ?></label>
													<textarea id="note" class="wmax editeur suiviModif" name="questionnaire_note" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $quest->get("note") ?></textarea></p></div>
												<div style="width:48%; float:right;"><p><label for="questionnaire_generique"><?php echo TXT_GENERIQUE ?></label>
													<textarea  id="generique" class="wmax editeur suiviModif" name="questionnaire_generique" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $quest->get("generique") ?></textarea></p></div>
												<div class="clear">&nbsp;</div>
											</div>						
										</div>						
	
										<div id="section2" class="detailContenant nod">
											<div class="detailContenu">
	
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet2.php' ?>
												<!--  /Messages -->
	
												<p><label><?php echo TXT_MEDIA_ACCUEIL ?></label></p>
												<p><label class="niv2" for="questionnaire_media_titre"><?php echo TXT_AJOUTER_UN_TEXTE ?></label>
													<input class="wmax suiviModif" type="text" name="questionnaire_media_titre" value="<?php echo $quest->get("media_titre") ?>" onclick="fermerEditeurs()" placeholder="<?php echo TXT_INSCRIRE_LE_TITRE ?>" /></p>
												<p><textarea id="media_texte" class="wmax editeur suiviModif" name="questionnaire_media_texte" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_LE_TEXTE ?>" ><?php echo $quest->get("media_texte") ?></textarea></p>
	
	
	
												<!--  Ajouter une image -->
												<div class="padTo10">
													<div>
														
														<div class="menuContexte displayInline">
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_IMAGE?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=image" onclick="ouvrirSelectionMediaLien('questionnaire_media_image')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer" onclick="ouvrirImportMediaLien('questionnaire_media_image')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													</div>
													<input type="hidden" id="questionnaire_media_image" name="questionnaire_media_image" value="<?php echo $quest->get("media_image") ?>" />
													<p id="questionnaire_media_image_lien">
														
															<?php if ($quest->get("media_image") == 0) { 
																echo TXT_AUCUNE_SELECTION;  
															} else { 
																echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $quest->get("media_image") ?>" target="media_<?php echo $quest->get("media_image") ?>"><?php echo $quest->get("media_image_txt") ?></a>
															<?php }?>
														
														<span id="questionnaire_media_image_supp" <?php if ($quest->get("media_image") == 0) { ?> style="display: none;" <?php } ?>>
															<a href="#" onclick="viderChampMedia('questionnaire_media_image','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
													
												</div>
													
						
												<!--  Ajouter un son -->
												<div class="padTo10">
													<div>
														
														<div class="menuContexte displayInline">
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_SON?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=son" onclick="ouvrirSelectionMediaLien('questionnaire_media_son')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer" onclick="ouvrirImportMediaLien('questionnaire_media_son')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													</div>
													<input type="hidden" id="questionnaire_media_son" name="questionnaire_media_son" value="<?php echo $quest->get("media_son") ?>" />
													<p id="questionnaire_media_son_lien">
														
															<?php if ($quest->get("media_son") == 0) { 
																echo TXT_AUCUNE_SELECTION;  
															} else { 
																echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $quest->get("media_son") ?>" target="media_<?php echo $quest->get("media_son") ?>"><?php echo $quest->get("media_son_txt") ?></a>
															<?php }?>
														
														<span id="questionnaire_media_son_supp" <?php if ($quest->get("media_son") == 0) { ?> style="display: none;" <?php } ?>>
															<a href="#" onclick="viderChampMedia('questionnaire_media_son','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
												</div>
												
																				
												<!--  Ajouter une video -->
												<div class="padTo10">
													<div>
														
														<div class="menuContexte displayInline">
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_VIDEO?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=video" onclick="ouvrirSelectionMediaLien('questionnaire_media_video')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer" onclick="ouvrirImportMediaLien('questionnaire_media_video')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													</div>
													<input type="hidden" id="questionnaire_media_video" name="questionnaire_media_video" value="<?php echo $quest->get("media_video") ?>" />
													<p id="questionnaire_media_video_lien">
														
															<?php if ($quest->get("media_video") == 0) { 
																echo TXT_AUCUNE_SELECTION;  
															} else { 
																echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $quest->get("media_video") ?>" target="media_<?php echo $quest->get("media_video") ?>"><?php echo $quest->get("media_video_txt") ?></a>
															<?php }?>
														
														<span id="questionnaire_media_video_supp" <?php if ($quest->get("media_video") == 0) { ?> style="display: none;" <?php } ?>>
															<a href="#" onclick="viderChampMedia('questionnaire_media_video','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
												</div>
	
	
											</div>
										</div>						
	
										<div class="detailBot"><div>
											<input class="btnApercu" name="btnApercu" id="btnApercu2" type="button" onclick="apercuQuestionnaire('questionnaire_apercu', 'accueil_modifier')" value="<?php echo TXT_APERCU ?>"  />
											<input class="btnReset" name="btnReset" id="btnReset2" type="button" onclick="annulerQuestionnaire('accueil_modifier')" value="<?php echo TXT_ANNULER ?>"  />
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
	<a class="fenetreSelItems" href="questionnaires.php?demande=items_selectionner&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>" ></a>
	
</body>
</html>
