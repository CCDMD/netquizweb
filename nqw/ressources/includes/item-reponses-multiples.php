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
													<a class="tools" href="#"><span class="txtType"><img class="alTexBot" src="../images/ic-reponses-multiples.png" alt="<?php echo TXT_REPONSES_MULTIPLES ?>" />&nbsp;<?php echo TXT_REPONSES_MULTIPLES ?><img src="../images/ic-tools-2.png" alt="" /></span></a>
													<?php include '../ressources/includes/menu-contexte-items-type-changer.php' ?>
												</div>
											</div>

											<p class="item_titre"><label for="item_titre"><?php echo TXT_TITRE_ITEM?></label>
												<input class="wmax suiviModif" type="text" name="item_titre" id="item_titre" onclick="fermerEditeurs()" value="<?php echo $item->get("titre") ?>" /></p>
											
											<p><label for="item_enonce"><?php echo TXT_ENONCE ?></label>
												<textarea class="wmax editeur suiviModif" id="item_enonce" name="item_enonce" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_ENONCE?>"><?php echo $item->get("enonce") ?></textarea></p>
											
											<table class="tblItemDesc margTop20" id="liste-elements">
												<tr class="titre">
													<td colspan="2" style="width:6%; "><label><?php echo TXT_BONNE_REPONSE ?>&nbsp;?</label></td>
													<td><label class="displayInline"><?php echo TXT_REPONSE ?> :</label> 
														<div class="menuContexte displayInline">
															<a class="tools" href="#"><?php echo $item->getTypeElements1Txt() ?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<?php include '../ressources/includes/menu-contexte-element1-texte-image.php' ?>
														</div>
													</td>
													<td><label><?php echo TXT_RETROACTION ?></label></td>
													<td>&nbsp;</td>
												</tr>

												<?php for ($i = 1; $i < NB_MAX_CHOIX_REPONSES; $i++) { 
													if ($item->get("reponse_" . $i . "_statut") == "1" ) {	?>
													
													<tr class="item_choix_mult" id="element<?php echo $i ?>">
														<td style="width:2%;"><p class="id-element ocre gras"><?php echo $i ?></p></td>
														<td>
															<input type="hidden" name="reponse_<?php echo $i ?>_statut" value="<?php echo $item->get("reponse_" . $i . "_statut") ?>" />
															<input type="checkbox" name="item_reponse_<?php echo $i ?>_reponse" class="suiviModif" value="1" <?php if ($item->get("reponse_" . $i . "_reponse") == "1") echo "checked='checked'" ?>  onclick="fermerEditeurs()" />
														</td>
														
													<?php if ($item->get("type_elements1") != "image") { ?>
														<td><textarea id="reponse_<?php echo $i ?>_element" class="wmax element-texte editeur suiviModif" name="item_reponse_<?php echo $i ?>_element"  placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>" rows="4" cols="200"><?php echo $item->get("reponse_" . $i . "_element") ?></textarea></td>
													<?php } ?>
													
													<?php if ($item->get("type_elements1") == "image") { ?>
														<td>
																
																<?php
																  // Préparer les champs
																  $libelle = "reponse_" . $i . "_element"; 
																  $img = $item->get($libelle);
																 ?>
																  
																	<input type="hidden" id="item_<?php echo $libelle ?>" name="item_<?php echo $libelle ?>" value="<?php echo $img ?>" /> 
														
																	<!--  Ajouter une image -->
																	
																	<?php if ($img == "") { ?>
																	
																	<!--  Afficher la boîte de sélection -->
																	<div id="item_<?php echo $libelle ?>_selection">
																		<!--  Afficher le menu de sélection d'une image -->
																		<div class="menuContexte displayInline">
																			<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_IMAGE?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																			<?php include '../ressources/includes/menu-contexte-ajouter-image.php' ?>
																		</div>
																	</div>
																	<!--  / Afficher la boîte de sélection -->
																
																<?php } else {

																	
																		  // Charger le média pour obtenir le titre et les infos
																		  $media = new Media($log, $dbh);
																		  $media->getMediaParId($img, $item->get("id_projet"));
																	?>	
																	
																	<!--  Afficher l'image -->
																	<div id="item_<?php echo $libelle ?>_affichage">
																		<p class="elementImage">
																			<a href="media.php?demande=media_presenter&media_id_media=<?php echo $img ?>" target="media_<?php echo $img ?>" title="<?php echo $media->get("titre") . " (" . TXT_PREFIX_MEDIA . $img . ")" ?>"><img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="<?php echo $media->get("titre") . " (" . TXT_PREFIX_MEDIA . $img . ")" ?>" /></a>																	
																			
																			<span class="icBtnDel" id="item_<?php echo $libelle ?>_supp" <?php if ($img == 0) { ?> style="display: none;" <?php } ?>>
																				<a href="#" onclick="viderChampImage('item_<?php echo $libelle ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="" /></a>
																			</span>
																		</p>
																	</div>
																	<!-- / Afficher l'image -->
																	
																<?php } ?>
																
														</td>
													<?php } ?>
													
													
														<td><textarea id="reponse_<?php echo $i ?>_retroaction" class="wmax element-retro editeur suiviModif" name="item_reponse_<?php echo $i ?>_retroaction"  placeholder="<?php echo TXT_INSCRIRE_VOTRE_RETROACTION ?>" rows="4" cols="200"><?php echo $item->get("reponse_" . $i . "_retroaction") ?></textarea></td>
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

												<p><label for="item_ordre_presentation"><?php echo TXT_ORDRE_DE_PRESENTATION_DES_ELEMENTS ?></label>
													<select class="w250 suiviModif" id="item_ordre_presentation" name="item_ordre_presentation">
														<option value="predetermine" <?php echo $item->get('ordre_presentation_predetermine') ?> ><?php echo TXT_PREDETERMINE ?></option>
														<option value="aleatoire" <?php echo $item->get('ordre_presentation_aleatoire') ?> ><?php echo TXT_ALEATOIRE ?></option>
													</select>
												</p>
												<p><label for="item_type_etiquettes"><?php echo TXT_ETIQUETTE_DES_ELEMENTS ?></label>
													<select class="w250 suiviModif" id="item_type_etiquettes" name="item_type_etiquettes">
														<option value="numerique" <?php echo $item->get('type_etiquettes_numerique') ?> ><?php echo TXT_NUMERIQUE ?></option>
														<option value="alphabetique" <?php echo $item->get('type_etiquettes_alphabetique') ?> ><?php echo TXT_ALPHABETIQUE?></option>
														<option value="aucun" <?php echo $item->get('type_etiquettes_aucun') ?> ><?php echo TXT_AUCUN ?></option>
													</select>
												</p>
												
												<p><label for="item_type_bonnesreponses"><?php echo TXT_UTILISATEUR_DOIT_DONNER ?></label>
													<input type="radio" name="item_type_bonnesreponses" value="toutes" <?php echo $item->get('type_bonnesreponses_toutes') ?> />&nbsp;<?php echo TXT_TOUTES_LES_BONNES_REPONSES ?><br />
													<input type="radio" name="item_type_bonnesreponses" value="une" <?php echo $item->get('type_bonnesreponses_une') ?> />&nbsp;<?php echo TXT_AU_MOINS_UNE_BONNE_REPONSE ?><br />
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
																
																<p><label for="item_ordre_presentation_quest"><?php echo TXT_ORDRE_DE_PRESENTATION_DES_ELEMENTS ?></label>
																	<select class="w250 suiviModif" id="item_ordre_presentation_quest" name="item_ordre_presentation_quest">
																		<option value="predetermine" <?php echo $item->get('ordre_presentation_quest_predetermine') ?> ><?php echo TXT_PREDETERMINE ?></option>
																		<option value="aleatoire" <?php echo $item->get('ordre_presentation_quest_aleatoire') ?> ><?php echo TXT_ALEATOIRE ?></option>
																	</select>
																</p>
																<p><label for="item_type_etiquettes_quest"><?php echo TXT_ETIQUETTE_DES_ELEMENTS ?></label>
																	<select class="w250 suiviModif" id="item_type_etiquettes_quest" name="item_type_etiquettes_quest">
																		<option value="numerique" <?php echo $item->get('type_etiquettes_quest_numerique') ?> ><?php echo TXT_NUMERIQUE ?></option>
																		<option value="alphabetique" <?php echo $item->get('type_etiquettes_quest_alphabetique') ?> ><?php echo TXT_ALPHABETIQUE?></option>
																		<option value="aucun" <?php echo $item->get('type_etiquettes_quest_aucun') ?> ><?php echo TXT_AUCUN ?></option>
																	</select>
																</p>
																
																<p><label for="item_type_bonnesreponses_quest"><?php echo TXT_UTILISATEUR_DOIT_DONNER ?></label>
																	<input type="radio" name="item_type_bonnesreponses_quest" value="toutes" <?php echo $item->get('type_bonnesreponses_quest_toutes') ?> />&nbsp;<?php echo TXT_TOUTES_LES_BONNES_REPONSES ?><br />
																	<input type="radio" name="item_type_bonnesreponses_quest" value="une" <?php echo $item->get('type_bonnesreponses_quest_une') ?> />&nbsp;<?php echo TXT_AU_MOINS_UNE_BONNE_REPONSE ?><br />
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
