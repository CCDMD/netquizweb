<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">
	$(document).ready(function() {
		
		
		// Afficher les champs heures et minutes pour le temps de passation SI le type est limité - désactivé pour phase I		
		// $('#tempsPassationType').bind('change load', function(event) {
		//	var v = $('#tempsPassationType').val();
		//   if( v =="limite") { 
		//    	$('#tempsPassation').show(); 
		//    } else { 
		//    	$('#tempsPassation').hide(); 
	    //    } 
		// });

		// Afficher le champ pour le nombre d'essais si le type est limité		
		$('#essaisRepondreType').bind('change load', function(event) {
			var v = $('#essaisRepondreType').val();
		    if( v =="limite") { 
		    	$('#essaisRepondre').show(); 
		    } else { 
		    	$('#essaisRepondre').hide(); 
	        } 
		 });

		// Afficher le champ pour choisir le nombre d'item si l'ordre est aléatoire		
		$('#generationQuestionType').bind('change load', function(event) {
			var v = $('#generationQuestionType').val();
		    if( v == "aleatoire" && '<?php echo $quest->get("nbSections")?>' == 0) { 
		    	$('#selectionXItems').show(); 
		    } else { 
		    	$('#selectionXItems').hide(); 
	        } 
		 });

		 // Activer la fonctionnalites champs au chargement de la page
		 // $('#tempsPassationType').trigger("change");
		 $('#essaisRepondreType').trigger("change");
		 $('#generationQuestionType').trigger("change");

		 // Mettre le flag de détection des modifications faux au chargement de la page
		 desactiverSuiviModifications();
		 
		// Activer la sélection/déselection de tous les checkboxes
	    $('#selectall').change(function () { 
		    if ($(this).attr("checked")) { 
	            $('.selectionElement').prop('checked', true);
	        } else {
	            $('.selectionElement').prop('checked', false);
	        }
		    verifierSelection();
	    });
		 
		 
	});

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

	function validerCollection() {

		sel = $("#collectionSelect").val();
		if (sel != "") {
			$("#collectionText").val("");
		} 
	}

	
	$(document).ready(function () {

		// Ajuster les panneaux
		resizePanels();

		// Sélectionner l'onglet
		selectionnerOnglet("<?php echo $onglet ?>");
		
		// Changer le style du theme selectionne
		$("input[name='questionnaire_theme']").click(function () {
			var $currentId = $(this).attr('id');
			$("label[for^=theme]").removeClass("themeSelected");
			$("label[for='" + $currentId + "']").addClass("themeSelected");
		 });

		// Activer le clic sur l'étoile pour le suivi
		$("#icone-etoile").click(function() {
			envoiSuiviQuestionnaire('questionnaires.php', 'questionnaire_suivi', '<?php echo $quest->get("id_questionnaire") ?>');
		});

		// Activer le flag des modifications au besoin
		activerSuiviModifications('<?php echo $pageInfos["flagModifications"] ?>');
		
		// Au chargement de la page si un thème est sélectionné ajouter la classe
		themeSel = '<?php echo $quest->get("theme") ?>';
		if (themeSel != "") {
			themeSel = 'theme_' + themeSel;
			$("label[for='" + themeSel + "']").addClass("themeSelected");
		}

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
						
	});
 	</script>
	
</head>

<body id="bQuestionnaire">

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
									<input type="hidden" name="demande" value="questionnaire_sauvegarder" />
									<input type="hidden" name="demandeRetour" value="" />
									<input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />
									<input type="hidden" name="item_id_item" value="" />
									<input type="hidden" name="maj_lexique" value="1" />
									<input type="hidden" name="onglet" value="<?php echo $onglet ?>" />
									<input type="hidden" name="items_selectionner" value="" />
									<input type="hidden" name="flagModifications" value="" />
									<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
									<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire") ?>" />
									<input type="hidden" name="verrou_id_element2" value="" />
								
									<div class="flDr statut">
										<div class="displayInline"><span class="txtTitre"><?php echo TXT_STATUT ?>&nbsp;:&nbsp;</span></div>
										<div class="menuContexteGa displayInline">
											<a class="tools" href="#"><span class="txtStatut" id="statutQuestionnaire"><?php echo $quest->getStatutTxt() ?></span><img src="../images/ic-tools-2.png" alt="" /></a>
											<?php include '../ressources/includes/menu-contexte-quest-publier.php' ?>
										</div>
									</div>
									<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_MES_QUESTIONNAIRES ?>" /><a href="questionnaires.php"><?php echo TXT_MES_QUESTIONNAIRES ?></a><span class="sep">&gt;</span><?php echo $quest->get("titre") ?> <span class="id">(<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire")?>)</span></h2></div>
									<div class="onglets">
										<div id="onglet1" class="ongletActif"><div><a href="#" onclick="changerOnglet('1');return false;"><?php echo TXT_PARAMETRES ?></a></div></div>
										<div id="onglet2" class="ongletInactif"><div><a href="#" onclick="changerOnglet('2');return false;"><?php echo TXT_THEME ?></a></div></div>
										<div id="onglet3" class="ongletInactif"><div><a href="#" onclick="javascript:changerOnglet('3');return false;"><?php echo TXT_LEXIQUE ?></a></div></div>
										<div class="tools menuContexteGa itemTools">
											<img src="../images/ic-tools.png" alt="" />
											<?php include '../ressources/includes/menu-contexte-quest.php' ?>
										</div>
									</div>
									
									<div class="detail">
										<div class="detailTop"><div>
											<div class="flGa">
												<?php if ($quest->get("suivi") == "1") {?>
													<img id="icone-etoile" src="../images/ic-star-jaune.png" alt="" />
												<?php } else { ?>
													<img id="icone-etoile" src="../images/ic-star-gris.png" alt="" />
												<?php }?>
											</div>
											<input class="btnApercu" name="btnApercu" id="btnApercu1" type="button" onclick="apercuQuestionnaire('questionnaire_apercu', 'questionnaire_modifier')" value="<?php echo TXT_APERCU ?>"  />
											<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annulerQuestionnaire('questionnaire_modifier')" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
										</div>
										
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
												
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
											
												<div>
													<p><label for="collectionSelect"><img src="../images/ic-collection.png" alt="<?php echo TXT_COLLECTION ?>" /><?php echo TXT_COLLECTION ?></label>
														
														<select class="w250 suiviModif" id="collectionSelect" name="questionnaire_id_collection" onchange="validerCollection()">
															<option value="" class="champPlaceholder" disabled="disabled" selected="selected"><?php echo TXT_CHOISIR_UNE_COLLECTION ?></option>
															
															<?php foreach ($listeCollection as $id_collection => $libelle) {
																if ($id_collection != 0) { ?>
																													
																	<option value="<?php echo $id_collection ?>" <?php if ($quest->get('id_collection') == $id_collection) { echo "selected='selected'"; } ?> ><?php echo $libelle ?></option>
															
															<?php }
															} ?>
															
														</select>
														<input class="w250" type="text" id="collectionText" name="questionnaire_collection_ajouter" placeholder="<?php echo TXT_INSCRIRE_NOUVELLE_COLLECTION_AU_BESOIN ?>" />
													</p>
													<hr />
													
													
												</div>
	
												<p><label for="questionnaire_titre"><?php echo TXT_TITRE_DU_QUESTIONNAIRE ?></label>
													<input class="wmax suiviModif" type="text" name="questionnaire_titre" id="questionnaire_titre" maxlength="300" value="<?php echo $quest->get("titre") ?>"/>
												</p>
												
												<p><label for="questionnaire_generation_question_type"><?php echo TXT_GENERATION_DES_ITEMS ?></label>
													<select class="w250 suiviModif" name="questionnaire_generation_question_type" id="generationQuestionType">
														<option value="section" <?php echo $quest->get('generation_question_type_section'); ?> ><?php echo TXT_SELON_PARAMETRE_SECTION ?></option>
														<option value="aleatoire" <?php echo $quest->get('generation_question_type_aleatoire'); ?> ><?php echo TXT_ORDRE_ALEATOIRE ?></option>
														<option value="predetermine" <?php echo $quest->get('generation_question_type_predetermine'); ?> ><?php echo TXT_ORDRE_PREDETERMINE ?></option>
													</select>
													<span id="selectionXItems" class="optionsArial"><?php echo TXT_INCLURE ?> <input type="text" class="suiviModif" size="5" maxlength="10" name="questionnaire_generation_question_nb" value="<?php if ($quest->get('generation_question_nb') > 0) { echo $quest->get('generation_question_nb'); } else { echo $quest->get('nb_items'); } ?>" /> <?php echo TXT_ITEMS_SUR_X_ITEMS . " " . $quest->get("nb_items") . " " . TXT_ITEMS ?></span>
												</p>
												
												
												<p><label for="tempsReponseCalculer"><?php echo TXT_TEMPS_DE_REPONSE ?></label>
													<select class="w250 suiviModif" name="questionnaire_temps_reponse_calculer" id="tempsReponseCalculer">
														<option value="non" <?php echo $quest->get('temps_reponse_calculer_non'); ?> ><?php echo TXT_NE_PAS_CALCULER ?></option>
														<option value="oui" <?php echo $quest->get('temps_reponse_calculer_oui'); ?> ><?php echo TXT_CALCULER ?></option>
													</select>
												</p>
												
												<!-- Désactivé pour phase I
												<p><label for="tempsPassationType"><?php echo TXT_TEMPS_DE_PASSATION ?></label>
													<select class="w250 suiviModif" name="tempsPassationType" id="tempsPassationType">
														<option value="illimite" <?php echo $quest->get('temps_passation_type_illimite'); ?> ><?php echo TXT_ILLIMITE ?></option>
														<option value="limite" <?php echo $quest->get('temps_passation_type_limite'); ?> ><?php echo TXT_LIMITE ?></option>
													</select>
													<span id="tempsPassation" class="optionsArial">
														&agrave; <input type="text" class="suiviModif" name="questionnaire_temps_passation_heures" value="<?php echo $quest->get('temps_passation_heures'); ?>" size="5" maxlength="10" /> <?php echo TXT_HEURES ?> 
														<input class="suiviModif" name="questionnaire_temps_passation_minutes" value="<?php echo $quest->get('temps_passation_minutes'); ?>" type="text" size="5" maxlength="10" /> <?php echo TXT_MINUTES ?>
													</span>
												</p>
												 -->
	
												<p><label for="questionnaire_affichage_resultats_type"><?php echo TXT_AFFICHAGE_DE_LA_SOLUTION ?></label>
													<select class="w250 suiviModif" name="questionnaire_affichage_resultats_type" id="questionnaire_affichage_resultats_type">
														<option value="item" <?php echo $quest->get('affichage_resultats_type_item'); ?> ><?php echo TXT_SELON_PARAMETRE_ITEM ?></option>
														<option value="oui" <?php echo $quest->get('affichage_resultats_type_oui'); ?> ><?php echo TXT_TOUJOURS_AFFICHER ?></option>
														<option value="non" <?php echo $quest->get('affichage_resultats_type_non'); ?> ><?php echo TXT_JAMAIS_AFFICHER ?></option>																										
													</select>
												</p>
													
												<!-- Désactivé pour phase I	
												<p><label for="essaisRepondreType"><?php echo TXT_NOMBRE_ESSAI_POUR_REPONDRE ?></label>
													<select class="w250 suiviModif" name="essaisRepondreType" id="essaisRepondreType">
														<option value="illimite" <?php echo $quest->get('essais_repondre_type_illimite'); ?> ><?php echo TXT_ILLIMITE ?></option>
														<option value="limite" <?php echo $quest->get('essais_repondre_type_limite'); ?> ><?php echo TXT_LIMITE ?></option>
													</select>
													<span id="essaisRepondre" class="optionsArial">
														<?php echo TXT_A ?> <input type="text" class="suiviModif" name="questionnaire_essais_repondre_nb" value="<?php echo $quest->get('essais_repondre_nb'); ?>" size="5" maxlength="10" /> <?php echo TXT_ESSAIS ?>
													</span>
												</p>
												-->
												
												<p><label for="questionnaire_demarrage_media_type"><?php echo TXT_DEMARRAGE_AUTOMATIQUE_DES_MEDIAS ?></label>
													<select class="w250 suiviModif" name="questionnaire_demarrage_media_type" id="questionnaire_demarrage_media_type">
														<option value="item" <?php echo $quest->get('demarrage_media_type_item'); ?> ><?php echo TXT_SELON_PARAMETRE_ITEM ?></option>
														<option value="aucun" <?php echo $quest->get('demarrage_media_type_aucun'); ?> ><?php echo TXT_NE_JAMAIS_DEMARRER_DE_MEDIAS ?></option>
														<option value="audio" <?php echo $quest->get('demarrage_media_type_audio'); ?> ><?php echo TXT_TOUJOURS_DEMARRER_MEDIA_AUDIO?></option>
														<option value="video" <?php echo $quest->get('demarrage_media_type_video'); ?> ><?php echo TXT_TOUJOURS_DEMARRER_MEDIA_VIDEO?></option>																																							
													</select>
												</p>
												
												<p><label for="questionnaire_id_langue_questionnaire"><?php echo TXT_LANGUE_DU_QUESTIONNAIRE_PUBLIE ?></label>
													<select class="w250 suiviModif" name="questionnaire_id_langue_questionnaire" id="questionnaire_id_langue_questionnaire">
														<?php foreach ($listeLangues as $idLangue => $descLangue) { 
															if ($descLangue == "") { continue; }
															?>
															<option value="<?php echo $idLangue ?>" <?php echo $quest->get('id_langue_questionnaire_' . $idLangue ); ?> ><?php echo $descLangue ?></option>
														<?php } ?>
													</select>
												</p>
												
												<div class="wmax100 clear padTo15"><hr /></div>
	
												<div class="wmax">
													<p><label for="questionnaire_remarque"><?php echo TXT_REMARQUE ?></label>
														<textarea class="wmax suiviModif" id="questionnaire_remarque" name="questionnaire_remarque" rows="5" cols="200" placeholder="<?php echo TXT_INSCRIRE_UN_COMMENTAIRE_UTILE_POUR_GESTION_QUESTIONNAIRES ?>"><?php echo $quest->get("remarque") ?></textarea></p>
												</div>
												
											</div>
										</div>						
										
										<div id="section2" class="detailContenant nod">
											<div class="detailContenu">
											
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet2.php' ?>
												<!--  /Messages -->
												
												<div>
													<?php foreach ($listeThemes as $theme) { ?>
														<div class="displayInline nowrap">
															<input type="radio" name="questionnaire_theme" class="suiviModif" value="<?php echo $theme ?>" id="theme_<?php echo $theme ?>" <?php echo $quest->get('theme_' . $theme); ?> />
															<label class="inline theme" for="theme_<?php echo $theme ?>">
															    <a href="questionnaires.php?demande=theme_apercu&theme=<?php echo $theme ?>" target="<?php echo $theme ?>">
																	<img src="questionnaires.php?demande=theme_apercu&theme=<?php echo $theme ?>" width="210" onclick="document.getElementById('theme_' + <?php echo $theme ?>).checked=true; " alt="<?php echo TXT_THEME ?>" />
																</a>
																<br /><?php echo $theme ?>
															</label>
														</div>
													<?php } ?>
												</div>
											</div>						
										</div>
										
										<div id="section3" class="detailContenant nod">
											<div class="detailContenu">
											
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet3.php' ?>
												<!--  /Messages -->
												
												<div>
												
                                                    <p class="gras"><?php echo TXT_TERMES_A_INCLURE ?></p>
												
												<table class="tblListe tblListeLexique">
													
													<tr>
														<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()" /></th>
														<th class="cCode"><?php echo TXT_CODE ?></th>
														<th class="c3"><?php echo TXT_TERME ?></th>
														<th class="c4"><?php echo TXT_VARIANTES ?></th>
														<th class="c5 last"><?php echo TXT_DEFINITION ?></th>
													</tr>
												<?php foreach($listeTermes as $element){ ?> 									
													<tr>
														<td class="cCheck">
															<input class="noBord selectionElement" type="checkbox" name="questionnaire_terme_selection_<?php echo $element->get("id_terme")?>" value="<?php echo $element->get("id_terme")?>" <?php if ($quest->get("terme_selection_" . $element->get("id_terme")) != "" ) { echo "checked"; } ?>/>
														</td>
														<td><?php echo $element->get("id_prefix") . $element->get("id_terme") ?></td>
														<td class="alGa"><a href="bibliotheque.php?demande=terme_modifier&terme_id_terme=<?php echo $element->get("id_terme") ?>"><?php echo $element->get("terme")?></a></td>
														<td class="alGa"><?php echo $element->getListeVariantes() ?></td>
														<td class="alGa last">
														
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
														
													</tr>
												<?php } ?>

												</table>												
															
												</div>
											</div>						
										</div>			
										
										<div class="detailBot"><div>
											<input class="btnApercu" name="btnApercu" id="btnApercu2" type="button" onclick="apercuQuestionnaire('questionnaire_apercu', 'questionnaire_modifier')" value="<?php echo TXT_APERCU ?>"  />
											<input class="btnReset" name="btnReset" id="btnReset2" type="button" onclick="annulerQuestionnaire('questionnaire_modifier')" value="<?php echo TXT_ANNULER ?>"  />
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

	<!--  Lien pour fenêtre jaillissante servant à l'importation d'items -->
	<a class="fenetreSelItems" href="questionnaires.php?demande=items_selectionner&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>"></a>
</body>
</html>
