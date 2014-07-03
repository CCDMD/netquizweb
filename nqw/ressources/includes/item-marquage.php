								<div class="detail">
									<div class="detailTop"><div>
										<div class="flGa">
											<?php if ($item->get("suivi") == "1") {?>
												<img id="icone-etoile" src="../images/ic-star-jaune.png" alt="" />
											<?php } else { ?>
												<img id="icone-etoile" src="../images/ic-star-gris.png" alt="" />
											<?php }?>
											
											<?php $listeLiens = $item->get("liste_liens_questionnaires"); if (! empty($listeLiens) ) { ?>
												<a href="#" class="infobulle"><img src="../images/ic-link.png" alt="" /><span><?php foreach ($listeLiens as $lien) { echo $lien . "<br />"; }?></span></a>
											<?php } ?>
										</div>

										<input class="btnApercu" name="btnApercu" id="btnApercu1" type="button" onclick="preparerMarqueRetroPourEnregistrement();genererApercuItem()" value="<?php echo TXT_APERCU ?>"  />
										<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annuler()" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="button" onclick="preparerMarqueRetroPourEnregistrement();enregistrer('item_sauvegarder')" value="<?php echo TXT_ENREGISTRER ?>" />
									</div></div>
									
									<div id="section1" class="detailContenant">
										<div class="detailContenu">
									
											<!--  Messages -->
											<?php include '../ressources/includes/message_onglet1.php' ?>
											<!--  /Messages -->
											
											<div class="itemType">
												<div class="displayInline"><span class="txtTitre"><?php echo TXT_TYPE_ITEM ?>&nbsp;:&nbsp;</span></div>
												<div class="menuContexteGa displayInline">
													<a class="tools" href="#"><span class="txtType"><img class="alTexBot" src="../images/ic-marquage.png" alt="<?php echo TXT_MARQUAGE ?>" />&nbsp;<?php echo TXT_MARQUAGE ?><img src="../images/ic-tools-2.png" alt="" /></span></a>
													<?php include '../ressources/includes/menu-contexte-items-type-changer.php' ?>
												</div>
											</div>

											<p class="item_titre"><label for="item_titre"><?php echo TXT_TITRE_ITEM?></label>
												<input class="wmax suiviModif" type="text" name="item_titre" id="item_titre" onclick="fermerEditeurs()" value="<?php echo $item->get("titre") ?>" /></p>
											
											<p><label for="item_enonce"><?php echo TXT_ENONCE ?></label>
												<textarea class="wmax editeur suiviModif" id="item_enonce" name="item_enonce" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_ENONCE?>"><?php echo $item->get("enonce") ?></textarea></p>
											
											<div id="couleursFerme">
												<p class="padBotZero"><a class="classeurLibelle"  onclick="preparerMarqueRetroPourEnregistrement();changerSection('couleurs')"><img src="../images/ic-fleche.png" alt="" /><label class="inline"><?php echo TXT_DEFINITION_DES_COULEURS_DE_MARQUAGE ?></label></a></p>
											</div>
											
											<div id="couleursOuvert" style="display:none;">
											
												<p class="classeurLibelle"><img src="../images/ic-fleche-bas.png" alt="" /><label class="inline"><?php echo TXT_DEFINITION_DES_COULEURS_DE_MARQUAGE ?></label></p>
												<table class="tblItemDesc" id="">
												
												<?php
													for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
													if ($item->get("couleur_" . $i . "_statut") == "1" || $i <= 1) { ?>
												
													<tr>
														<td colspan="3" class="noPadBot">
															<table>
																<tr>
																	<td class="colCarre padTopZero">
																		<div class="menuContexte menuContexteCouleurs displayInline" >
																			<?php $couleur = $i . "_couleur"; ?>
																		    <input type="hidden" name="item_couleur_<?php echo $couleur ?>" id="item_couleur_<?php echo $couleur ?>" value="<?php if ($item->get("couleur_" . $couleur) == "") echo MARQUAGE_COULEUR_DEFAUT; else echo $item->get("couleur_" . $couleur)?>" />
																		    <a name="lien_couleur_<?php echo $couleur ?>" />
																		    <a class="tools gras" href="#item_couleur_<?php echo $couleur ?>"><span class="carre" style="background: #<?php if ($item->get("couleur_" . $couleur) == "") echo MARQUAGE_COULEUR_DEFAUT; else echo $item->get("couleur_" . $couleur)?>" id="couleur_<?php echo $couleur ?>"></span></a>
																			<?php include '../ressources/includes/menu-contexte-couleurs.php' ?>
																		</div>
																	</td>
																	<td class="padTopZero"><input class="wmax suiviModif" type="text" name="item_couleur_<?php echo $i ?>_titre" id="item_couleur_titre" onclick="fermerEditeurs()" value="<?php echo $item->get("couleur_" . $i . "_titre") ?>" /></td>
																</tr>
															</table>

														</td>
                                                        <td>&nbsp;</td>
													</tr>
													<tr class="titre">
														<td class="padBotZero padTopZero"><label><?php echo TXT_RETROACTION_POSITIVE ?></label></td>
														<td class="padBotZero padTopZero"><label><?php echo TXT_RETROACTION_NEGATIVE ?></label></td>
														<td class="padBotZero padTopZero"><label><?php echo TXT_RETROACTION_POUR_REPONSE_INCOMPLETE ?></label></td>
														<td class="padBotZero padTopZero">&nbsp;</td>
													</tr>
													<tr class="" id="">
														<td class="padTopZero padBot50"><textarea id="item_couleur_<?php echo $i ?>_retroaction" class="wmax editeur suiviModif" name="item_couleur_<?php echo $i ?>_retroaction" rows="3" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_POSITIVE_MARQUAGE ?>"><?php echo $item->get("couleur_" . $i . "_retroaction") ?></textarea></td>
														<td class="padTopZero padBot50"><textarea id="item_couleur_<?php echo $i ?>_retroaction_negative" class="wmax editeur suiviModif" name="item_couleur_<?php echo $i ?>_retroaction_negative" rows="3" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_NEGATIVE_MARQUAGE ?>"><?php echo $item->get("couleur_" . $i . "_retroaction_negative") ?></textarea></td>
														<td class="padTopZero padBot50"><textarea id="item_couleur_<?php echo $i ?>_retroaction_incomplete" class="wmax editeur suiviModif" name="item_couleur_<?php echo $i ?>_retroaction_incomplete" rows="3" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_REPONSE_INCOMPLETE_MARQUAGE ?>"><?php echo $item->get("couleur_" . $i . "_retroaction_incomplete") ?></textarea></td>
														<td class="icDelAdd padTopZero padBot50">
															<a id="" class="" onclick="preparerMarqueRetroPourEnregistrement();supprimerCouleur('<?php echo $i ?>')" href="#"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
															<a id="" class="" onclick="preparerMarqueRetroPourEnregistrement();ajouterCouleur('<?php echo $i ?>')" href="#"><img class="icAdd" src="../images/ic-add.png" alt="<?php echo TXT_AJOUTER ?>" /></a>
														</td>
													</tr>
													
													
												<?php }
													}?>
													
												</table>
											</div>

											<div class="wmax100 clear padTo15"><hr /></div>

											<div id="texteFerme">
												<div><a class="classeurLibelle" onclick="preparerMarqueRetroPourEnregistrement();changerSection('texte')" ><img src="../images/ic-fleche.png" alt="" /><label class="inline"><?php echo TXT_EDITION_DU_TEXTE_ET_DES_MARQUES ?></label></a></div>
											</div>
											
											<div id="texteOuvert" style="display:none;">
											
												<div class="classeurLibelle flGa"><img src="../images/ic-fleche-bas.png" alt="" /><label class="inline"><?php echo TXT_EDITION_DU_TEXTE_ET_DES_MARQUES ?></label></div>
											
												<div class="flDr" style="margin-top:7px; ">
													<a id="" class="padDr15" onclick="supprimerMarques();return false;" href=""><img class="icDelete icDeleteVAlign" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER_LES_MARQUES_SELECTIONNEES ?>" /><?php echo TXT_SUPPRIMER_LES_MARQUES_SELECTIONNEES ?></a>
													<a id="" class="" onclick="ajouterMarque();return false;" href=""><img class="icAdd icAddVAlign" src="../images/ic-add.png" alt="<?php echo TXT_AJOUTER_UNE_MARQUE ?>" /><?php echo TXT_AJOUTER_UNE_MARQUE ?></a>
												</div>
												<div class="clear"><textarea id="item_texte" class="wmax editeur_marquage suiviModif" name="item_texte" rows="10" cols="200"><?php echo $item->get("texte")?></textarea></div>
												
												<div class="cadre" id="cadreEditeurMarques" >
													<p class="cadreTitre padTo15"><label class="ocre"><?php echo TXT_MARQUE ?> <span id="nav_position" class="ocre gras"></span> </label></p>
													<div class="flDr" style="margin:-25px 0px 5px 0px; ">
													
															<a class="padDr15" onclick="supprimerMarque()"><img class="icDelete icDeleteVAlign" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER_CETTE_MARQUE ?>" /><?php echo TXT_SUPPRIMER_CETTE_MARQUE ?></a>
													
															<span class="txt"><?php echo TXT_MARQUE ?></span>
															<input class="btnSubmit btnPrev" type="button" onclick="modifierMarquePrec()" name="" value="" />
															<span id="nav_position2"></span> <?php echo TXT_DE ?> <span id="nav_total"></span>
															<input class="btnSubmit btnNext" type="button" onclick="modifierMarqueSuiv()" name="" value="" />
													</div>	

													<table class="tblItemMarquage">
														<tr>
															<td class="cadreContenu">
																<div class="padTo10">
																
																	<p>
                                                                    	<label style="margin-top:0px;"><?php echo TXT_TEXTE_DE_LA_MARQUE ?>&nbsp;</label>
																		<span id="marque_titre" ></span>
                                                                    </p>
																</div>
																
																<div class="padTo10">
                                                                    <label class="inline" style="margin-top:0px;"><?php echo TXT_COULEUR_DE_CETTE_MARQUE ?>&nbsp;</label>
																	<div class="menuContexte menuContexteCouleurs displayInline">
																		
																		<?php $couleur = "marque_couleur"; ?>
																	    <input type="hidden" name="item_<?php echo $couleur ?>" id="item_couleur_<?php echo $couleur ?>" value="<?php if ($item->get("couleur_" . $couleur) == "") echo $item->get("couleur_1_couleur"); else echo $item->get("couleur_" . $couleur)?>" />
																	    <a name="lien_couleur_<?php echo $couleur ?>" />
																	    
																	    <a class="tools gras" href="#item_couleur_<?php echo $couleur ?>"></a>
																	    <span class="carre" style="vertical-align:middle; background: #<?php if ($item->get("couleur_" . $couleur) == "") echo $item->get("couleur_1_couleur"); else echo $item->get("couleur_" . $couleur)?>" id="couleur_<?php echo $couleur ?>"></span> 
																	    
																	    <!-- Titre de la couleur choisie -->
																	    
																	    <?php
																	    	for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
																					if ($item->get("couleur_" . $i . "_statut") == "1" || $i <= 1) {
																		?>																			
																						
																				<span style="vertical-align:middle;" id="titre<?php echo strtoupper($item->get("couleur_" . $i . "_couleur")) ?>" class="couleurTitre"><?php echo $item->get("couleur_" . $i . "_titre") ?></span>
																																						
																		<?php }
																			} ?>
																			
																				<!--  Mauvaise réponse - titre de la couleur -->
																				<span style="vertical-align:middle;" id="titre<?php echo COULEUR_MAUVAISE_REPONSE ?>" class="couleurTitre"><?php echo TXT_MAUVAISE_REPONSE ?></span>
																				<!--  /Mauvaise réponse - titre de la couleur -->																				
																	    
																	    </a>
																																	
																		<div class="menuDeroul">
																			<p class="menuTitre"><?php echo TXT_CHOISIR_UNE_COULEUR ?></p>
																			<ul>
																				<?php 
																				for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
																					if ($item->get("couleur_" . $i . "_statut") == "1" || $i <= 1) {
																				?>																			
																																						
																					<li class="nowrap">
																						<a onclick="marqueChoisirCouleur('<?php echo $item->get("couleur_" . $i . "_couleur") ?>');choisirCouleur('<?php echo $couleur ?>','<?php echo $item->get("couleur_" . $i . "_couleur") ?>');return false">
																							<span class="carre" style="background: #<?php echo $item->get("couleur_" . $i . "_couleur") ?>">&nbsp;</span><?php echo $item->get("couleur_" . $i . "_titre") ?>
																						</a>
																					</li>
																				
																				<?php }
																				} ?>
																				
																				<li class="nowrap">
																					<a onclick="marqueChoisirCouleur('<?php echo ITEM_MARQUAGE_COULEUR_MAUVAISE_REPONSE ?>');choisirCouleur('marque_couleur','<?php echo ITEM_MARQUAGE_COULEUR_MAUVAISE_REPONSE ?>');return false">
																						<span class="carre" style="background: #<?php echo ITEM_MARQUAGE_COULEUR_MAUVAISE_REPONSE ?>">&nbsp;</span><?php echo TXT_MAUVAISE_REPONSE ?>
																					</a>
																				</li>
																																								
																				
																			</ul>
																		</div>
																	</div>
																</div>
																<div class="padTo10">
																	<label><?php echo TXT_RETROACTIONS_POUR_CHAQUE_COULEUR_DE_MARQUAGE ?> </label>
																</div>
																
																<table>
																																
																	<?php 
																	$couleursUtilisees = array();
																	
																	for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {

																		// Obtenir la couleur
																		$couleur = $item->get("couleur_" . $i . "_couleur");

																		// Ajouter la couleur si : 
																		// 1- Elle est active
																		// 2- Pour en avoir au moins une
																		// 3- Si elle n'a pas été déjà affichée
																		if ( ($item->get("couleur_" . $i . "_statut") == "1" || $i <= 1 ) && !in_array($couleur, $couleursUtilisees))  {
		
																			// Prendre note de la couleur
																			array_push($couleursUtilisees, $couleur);
																	?>																			
																																			
																			<tr>
																				<td style="max-width:25px;"><span class="carre" style="background: #<?php echo $couleur ?>">&nbsp;</span></td>
																				<td class="alGa"><?php echo $item->get("couleur_" . $i . "_titre") ?></td>
																				<td>
																				  <textarea id="retro_<?php echo $couleur ?>" class="wmax editeur suiviModif marqueRetro" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_POSITIVE_MARQUAGE ?>">
																				  <?php echo $item->get("marque_" . $couleur . "_retroaction")?>
																				  </textarea>
																				</td>
																			</tr>
																	
																	<?php }
																	} ?>																
																																		
																
																</table>

															</td>
														</tr>
													</table>
												
												</div>

											</div>

										</div>						
									</div>
															
									<div id="section2" class="detailContenant nod">
										<div class="detailContenu">

											<!--  Messages -->
											<?php include '../ressources/includes/message_onglet2.php' ?>
											<!--  /Messages -->

											<?php include '../ressources/includes/item-complements.php' ?>
										</div>
									</div>						

									<div id="section3" class="detailContenant nod">
										<div class="detailContenu">
											
											<!--  Messages -->
											<?php include '../ressources/includes/message_onglet3.php' ?>
											<!--  /Messages -->

											<?php include '../ressources/includes/item-parametres-categorie-apercu.php' ?>
											
											<div class="wmax100 clear padTo15"><hr /></div>

											<div class="wdemiGa">
												<p><label><?php echo TXT_PARAMETRES_DE_ITEM ?></label></p>
												<p><label for="item_ponderation"><?php echo TXT_PONDERATION ?></label>
													<input class="suiviModif" type="text" id="item_ponderation" name="item_ponderation" value="<?php echo $item->get("ponderation")?>" size="5" maxlength="10" />
												</p>
												<p><label for="item_demarrer_media"><?php echo TXT_DEMARRAGE_AUTOMATIQUE_DES_MEDIAS ?></label>
													<select class="w250 suiviModif" id="item_demarrer_media" name="item_demarrer_media">
														<option value="aucun" <?php echo $item->get('demarrer_media_aucun') ?> ><?php echo TXT_NE_PAS_DEMARRER_DE_MEDIA ?></option>
														<option value="audio" <?php echo $item->get('demarrer_media_audio') ?> ><?php echo TXT_DEMARRER_LE_MEDIA_AUDIO ?></option>
														<option value="video" <?php echo $item->get('demarrer_media_video') ?> ><?php echo TXT_DEMARRER_LE_MEDIA_VIDEO?></option>
													</select>
												</p>
												<p><label for="item_afficher_solution"><?php echo TXT_AFFICHAGE_DE_LA_SOLUTION ?></label>
													<select class="w250 suiviModif" id="item_afficher_solution" name="item_afficher_solution">
														<option value="oui" <?php echo $item->get('afficher_solution_oui') ?> ><?php echo TXT_AFFICHER ?></option>
														<option value="non" <?php echo $item->get('afficher_solution_non') ?> ><?php echo TXT_NE_PAS_AFFICHER ?></option>
													</select>
												</p>
												<p><label for="item_points_retranches"><?php echo TXT_POINTS_RETRANCHES_PAR_MAUVAISE_REPONSE ?></label>
													<input class="suiviModif" type="text" id="item_points_retranches" name="item_points_retranches" value="<?php echo $item->get("points_retranches")?>" size="5" maxlength="10" />
												</p>

												
											</div>
											<?php if ($aiguilleur == 'questionnaires') {?>
											<div class="wdemiDr">
											
												<p class="lnkCadre" id="lnkCadre1"><a onclick="activerModifierValeursParametres()"><?php echo TXT_MODIFIER_CES_VALEURS_POUR_CE_QUESTIONNAIRE_SEULEMENT ?></a></p>

												<div id="cadre1" class="cadre" style="display:none;">
													<p class="cadreTitre"><label class="ocre"><?php echo TXT_VALEURS_MODIFIEES_POUR_CE_QUESTIONNAIRE_SEULEMENT ?></label></p>
													<table class="tblItemParam">
														<tr>
															<td class="cadreContenu">
																<p><label for="item_ponderation_quest"><?php echo TXT_PONDERATION ?></label>
																<input class=" suiviModif" type="text" id="item_ponderation_quest" name="item_ponderation_quest" value="<?php echo $item->get("ponderation_quest")?>" size="5" maxlength="10" />
																</p>
			
																<p><label for="item_demarrer_media_quest"><?php echo TXT_DEMARRAGE_AUTOMATIQUE_DES_MEDIAS ?></label>
																	<select class="w250 suiviModif" id="item_demarrer_media_quest" name="item_demarrer_media_quest">
																		<option value="aucun" <?php echo $item->get('demarrer_media_quest_aucun') ?> ><?php echo TXT_NE_PAS_DEMARRER_DE_MEDIA ?></option>
																		<option value="audio" <?php echo $item->get('demarrer_media_quest_audio') ?> ><?php echo TXT_DEMARRER_LE_MEDIA_AUDIO ?></option>
																		<option value="video" <?php echo $item->get('demarrer_media_quest_video') ?> ><?php echo TXT_DEMARRER_LE_MEDIA_VIDEO?></option>
																	</select>
																</p>
			
																<p><label for="item_afficher_solution_quest"><?php echo TXT_AFFICHAGE_DE_LA_SOLUTION ?></label>
																	<select class="w250 suiviModif" id="item_afficher_solution_quest" name="item_afficher_solution_quest">
																		<option value="oui" <?php echo $item->get('afficher_solution_quest_oui') ?> ><?php echo TXT_AFFICHER ?></option>
																		<option value="non" <?php echo $item->get('afficher_solution_quest_non') ?> ><?php echo TXT_NE_PAS_AFFICHER ?></option>
																	</select>
																</p>
																<p><label for="item_points_retranches"><?php echo TXT_POINTS_RETRANCHES_PAR_MAUVAISE_REPONSE ?></label>
																	<input class="suiviModif" type="text" id="item_points_retranches_quest" name="item_points_retranches_quest" value="<?php echo $item->get("points_retranches_quest")?>" size="5" maxlength="10" />
																</p>
																
															</td>
															<td class="cadreLnkDisable"><a onclick="desactiverModifierValeursParametres()"><img class="icDelete" src="../images/ic-delete.png" alt="" /></a></td>
														</tr>
													</table>
												
												</div>
											</div>
											<?php } ?>
											
											<div class="wmax100 clear padTo15"><hr /></div>
											
											<?php include '../ressources/includes/item-parametres-messages.php' ?>

											<div class="wmax100 clear padTo15"><hr /></div>

											<div class="wmax">
												<p><label for="item_remarque"><?php echo TXT_REMARQUE ?></label>
													<textarea class="wmax suiviModif" id="item_remarque" name="item_remarque" rows="5" cols="200" placeholder="<?php echo TXT_INSCRIRE_UN_COMMENTAIRE_UTILE_POUR_GESTION_ITEMS ?>"><?php echo $item->get("remarque") ?></textarea></p>
											</div>

										</div>
									</div>						
									<div class="detailBot"><div>
										<input class="btnApercu" name="btnApercu" id="btnApercu2" onclick="preparerMarqueRetroPourEnregistrement();;genererApercuItem()"  type="button" value="<?php echo TXT_APERCU ?>"  />
										<input class="btnReset" name="btnReset" id="btnReset2" type="button" onclick="annuler()" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="button" onclick="preparerMarqueRetroPourEnregistrement();enregistrer('item_sauvegarder')" value="<?php echo TXT_ENREGISTRER ?>" />
									</div></div>
								</div>					
