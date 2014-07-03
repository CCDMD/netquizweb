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

										<input class="btnApercu" name="btnApercu" id="btnApercu1" type="button" onclick="genererApercuItem()" value="<?php echo TXT_APERCU ?>"  />
										<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annuler()" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="button" onclick="enregistrer('item_sauvegarder')" value="<?php echo TXT_ENREGISTRER ?>" />
									</div></div>
									
									<div id="section1" class="detailContenant">
										<div class="detailContenu">
									
											<!--  Messages -->
											<?php include '../ressources/includes/message_onglet1.php' ?>
											<!--  /Messages -->
											
											<div class="itemType">
												<div class="displayInline"><span class="txtTitre"><?php echo TXT_TYPE_ITEM ?>&nbsp;:&nbsp;</span></div>
												<div class="menuContexteGa displayInline">
													<a class="tools" href="#"><span class="txtType"><img class="alTexBot" src="../images/ic-reponse-breve.png" alt="<?php echo TXT_REPONSE_BREVE ?>" />&nbsp;<?php echo TXT_REPONSE_BREVE ?><img src="../images/ic-tools-2.png" alt="" /></span></a>
													<?php include '../ressources/includes/menu-contexte-items-type-changer.php' ?>
												</div>
											</div>

											<p class="item_titre"><label for="item_titre"><?php echo TXT_TITRE_ITEM?></label>
												<input class="wmax suiviModif" type="text" name="item_titre" id="item_titre" onclick="fermerEditeurs()" value="<?php echo $item->get("titre") ?>" /></p>
											
											<p><label for="item_enonce"><?php echo TXT_ENONCE ?></label>
												<textarea class="wmax editeur suiviModif" id="item_enonce" name="item_enonce" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_ENONCE?>"><?php echo $item->get("enonce") ?></textarea></p>
											
											<table class="tblItemDesc margTop20" id="liste-elements">
												<tr class="titre" >
													<td style="width:6%; "><label><?php echo TXT_BONNE_REPONSE ?>&nbsp;?</label></td>
													<td style="width:45%; "><label><?php echo TXT_REPONSE_PREVUE ?></label> </td>
													<td style="width:45%; "><label><?php echo TXT_RETROACTION ?></label></td>
													<td>&nbsp;</td>
												</tr>

												<?php for ($i = 1; $i < NB_MAX_CHOIX_REPONSES; $i++) { 
													if ($item->get("reponse_" . $i . "_statut") == "1" ) {	?>
												
													<tr class="item_choix_mult" id="element<?php echo $i ?>">
														<td><input type="checkbox" name="item_reponse_<?php echo $i ?>_reponse" class="suiviModif" value="1" <?php if ($item->get("reponse_" . $i . "_reponse") == "1") echo "checked='checked'" ?>  onclick="fermerEditeurs()" /></td>
														
														<td><textarea id="reponse_<?php echo $i ?>_element" class="wmax element-texte suiviModif" name="item_reponse_<?php echo $i ?>_element" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $item->get("reponse_" . $i . "_element") ?></textarea></td>
															
														<td><textarea id="reponse_<?php echo $i ?>_retroaction" class="wmax element-texte editeur suiviModif" name="item_reponse_<?php echo $i ?>_retroaction" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_RETROACTION_POSITIVE_NEGATIVE ?>"><?php echo $item->get("reponse_" . $i . "_retroaction") ?></textarea></td>
														
														<td class="icDelAdd">
															<a id="supprimerElement<?php echo $i ?>" class="supprimerElement" onclick="supprimerElement('<?php echo $i ?>')" href="#"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
															<a id="ajouterElement<?php echo $i ?>" class="ajouterElement" onclick="ajouterElement('<?php echo $i ?>')" href="#"><img class="icAdd" src="../images/ic-add.png" alt="<?php echo TXT_AJOUTER ?>" /></a>
														</td>
													</tr>
												<?php 
													} 
												} 
											?>
											
											</table>
										

											<p><label for="item_enonce"><?php echo TXT_RETROACTIONS_POUR_TOUTES_REPONSES_NON_PREVUES ?></label>
												<textarea class="wmax editeur suiviModif" id="item_retroaction_reponse_imprevue" name="item_retroaction_reponse_imprevue" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTIONS_POUR_TOUTES_REPONSES_NON_PREVUES?>"><?php echo $item->get("retroaction_reponse_imprevue") ?></textarea></p>
											
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
												<p><label for="item_majmin"><?php echo TXT_POUR_LA_CORRECTION_TENIR_COMPTE ?>...</label>
													 <input type="checkbox" name="item_majmin" value="1" <?php echo $item->get('majmin_1') ?> />&nbsp;<?php echo TXT_DES_MAJUSCULES_MINUSCULES ?><br />
													 <input type="checkbox" name="item_ponctuation" value="1" <?php echo $item->get('ponctuation_1') ?> />&nbsp;<?php echo TXT_DE_LA_PONCTUATION ?> 
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
																				
																<p><label for="item_majmin"><?php echo TXT_POUR_LA_CORRECTION_TENIR_COMPTE ?>...</label>
																	 <input type="checkbox" name="item_majmin_quest" value="1" <?php echo $item->get('majmin_quest_1') ?> />&nbsp;<?php echo TXT_DES_MAJUSCULES_MINUSCULES ?><br />
																	 <input type="checkbox" name="item_ponctuation_quest" value="1" <?php echo $item->get('ponctuation_quest_1') ?> />&nbsp;<?php echo TXT_DE_LA_PONCTUATION ?> 
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
										<input class="btnApercu" name="btnApercu" id="btnApercu2" onclick="genererApercuItem()"  type="button" value="<?php echo TXT_APERCU ?>"  />
										<input class="btnReset" name="btnReset" id="btnReset2" type="button" onclick="annuler()" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="button" onclick="enregistrer('item_sauvegarder')" value="<?php echo TXT_ENREGISTRER ?>" />
									</div></div>
								</div>					
