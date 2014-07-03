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
													<a class="tools" href="#"><span class="txtType"><img class="alTexBot" src="../images/ic-classement.png" alt="<?php echo TXT_CLASSEMENT ?>" />&nbsp;<?php echo TXT_CLASSEMENT ?><img src="../images/ic-tools-2.png" alt="" /></span></a>
													<?php include '../ressources/includes/menu-contexte-items-type-changer.php' ?>
												</div>
											</div>

											<p class="item_titre"><label for="item_titre"><?php echo TXT_TITRE_ITEM?></label>
												<input class="wmax suiviModif" type="text" name="item_titre" id="item_titre" onclick="fermerEditeurs()" value="<?php echo $item->get("titre") ?>" /></p>
											
											<p><label for="item_enonce"><?php echo TXT_ENONCE ?></label>
												<textarea class="wmax editeur suiviModif" id="item_enonce" name="item_enonce" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_ENONCE?>"><?php echo $item->get("enonce") ?></textarea></p>
											
											<div id="classeursFerme">
												<p class="padBotZero"><a class="classeurLibelle" onclick="changerSection('classeurs')"><img src="../images/ic-fleche.png" alt="" /><label class="inline"><?php echo TXT_DEFINITION_DES_CLASSEURS ?></label></a></p>
											</div>
											
											<div id="classeursOuvert" style="display:none;">
											
												<p class="classeurLibelle"><img src="../images/ic-fleche-bas.png" alt="" /><label class="inline"><?php echo TXT_DEFINITION_DES_CLASSEURS ?></label></p>
												<table class="tblItemDesc" id="">
													<tr class="titre">
														<td colspan="3" class="padTopZero">
															<div><label class="displayInline"><?php echo TXT_TYPE_DE_CLASSEUR_ONGLET_ELEMENT ?> : </label>
																	<div class="menuContexte displayInline">
																		<a class="tools gras" href="#"><?php echo $item->getTypeElements1Txt() ?> / <?php echo $item->getTypeElements2Txt() ?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																		<?php include '../ressources/includes/menu-contexte-classement-texte-image.php' ?>
																</div>
															</div>
														</td>
														<td class="padTopZero">&nbsp;</td>
													</tr>
												
												<?php
													for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {

														if ($item->get("classeur_" . $i . "_statut") == "1" || $i <= 2) { ?>
												
															<input type="hidden" name="item_classeur_<?php echo $i ?>_id_classeur" value="<?php echo $item->get("classeur_" . $i . "_id_classeur") ?>" />
												
												
															<?php if ( $item->get("type_elements1") == "texte" ) { ?>
																<tr>
																	<td class="padBotZero" colspan="3"><input id="item_classeur_<?php echo $i ?>_titre" name="item_classeur_<?php echo $i ?>_titre" class="wmax" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>" value="<?php echo $item->get("classeur_" . $i . "_titre") ?>" /></td>
																	<td class="padBotZero">&nbsp;</td>
																</tr>
															
															<?php } else { 
																
																  // Preparer les champs
																  $libelle = "classeur_" . $i . "_titre"; 
																  $img = $item->get($libelle);
																 ?>
															
																<input type="hidden" id="item_<?php echo $libelle ?>" name="item_<?php echo $libelle ?>" value="<?php echo $img ?>" /> 
																														
																<?php if ($img == "") { ?>
																<tr>
																	<td colspan="4" class="padBotZero">
																		<!--  Afficher la boite de selection -->
																		<div id="item_<?php echo $libelle ?>_selection">
																			<!--  Afficher le menu de selection d'une image -->
																			<div class="menuContexte displayInline">
																				<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_IMAGE?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																				<?php include '../ressources/includes/menu-contexte-ajouter-image.php' ?>
																			</div>
																		</div>
																		<!--  / Afficher la boite de selection -->
																	</td>
																</tr>
																<?php } else {  
																
																		// Charger le media pour obtenir le titre et les infos
																		$media = new Media($log, $dbh);
																		$media->getMediaParId($img, $item->get("id_projet"));
																		
																		?>
																		<tr>
																			<td colspan="4" class="padBotZero">
																				<!--  Afficher l'image -->
																				<div id="item_<?php echo $libelle ?>_affichage">
																					<p class="elementImage">
																						<a href="media.php?demande=media_presenter&media_id_media=<?php echo $img ?>" target="media_<?php echo $img ?>" title="<?php echo $media->get("titre") . " (" . TXT_PREFIX_MEDIA . $img . ")" ?>">
																							<img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="<?php echo $media->get("titre") . " (" . TXT_PREFIX_MEDIA . $img . ")" ?>" />
																						</a>												
																						
																						<span class="icBtnDel" id="item_<?php echo $libelle ?>_supp" <?php if ($img == 0) { ?> style="display: none;" <?php } ?>>
																							<a href="#" onclick="viderChampImage('item_<?php echo $libelle ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="" /></a>
																						</span>
																					</p>
																				</div>
																				<!-- / Afficher l'image -->
																			</td>
																		</tr>
					
																<?php } ?>
																
															<?php } ?>
																											

															<tr class="titre">
																<td class="padBotZero padTopZero"><label><?php echo TXT_RETROACTION_POSITIVE ?></label></td>
																<td class="padBotZero padTopZero"><label><?php echo TXT_RETROACTION_NEGATIVE ?></label></td>
																<td class="padBotZero padTopZero"><label><?php echo TXT_RETROACTION_POUR_REPONSE_INCOMPLETE ?></label></td>
																<td class="padBotZero padTopZero">&nbsp;</td>
															</tr>
															<tr>
																<td class="padTopZero padBot50" style="width:32%;"><textarea id="item_classeur_<?php echo $i?>_retroaction" name="item_classeur_<?php echo $i?>_retroaction" class=" editeur suiviModif wmax" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_POSITIVE_CLASSEUR ?>" ><?php echo $item->get("classeur_" . $i . "_retroaction") ?></textarea></td>
																<td class="padTopZero padBot50" style="width:32%;"><textarea id="item_classeur_<?php echo $i?>_retroaction_negative" name="item_classeur_<?php echo $i?>_retroaction_negative" class=" editeur suiviModif wmax" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_NEGATIVE_CLASSEUR ?>" ><?php echo $item->get("classeur_" . $i . "_retroaction_negative") ?></textarea></td>
																<td class="padTopZero padBot50" style="width:32%;"><textarea id="item_classeur_<?php echo $i?>_retroaction_incomplete" name="item_classeur_<?php echo $i?>_retroaction_incomplete" class=" editeur suiviModif wmax" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_RETROACTION_POUR_REPONSE_INCOMPLETE_CLASSEUR ?>" ><?php echo $item->get("classeur_" . $i . "_retroaction_incomplete") ?></textarea></td>
																<td class="padTopZero padBot50 icDelAdd">
																	<a id="supprimerElement<?php echo $i ?>" class="supprimerElement" onclick="supprimerClasseur('<?php echo $i ?>');return false;" href="#"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
																	<a id="ajouterElement<?php echo $i ?>" class="ajouterElement" onclick="ajouterClasseur('<?php echo $i ?>');return false;" href="#"><img class="icAdd" src="../images/ic-add.png" alt="<?php echo TXT_AJOUTER ?>" /></a>
																</td>
															</tr>

														<?php
															}
														} ?>
														
													
													
												</table>
												
											</div>

											<div class="wmax100 clear padTo15"><hr /></div>

											<div id="contenuFerme">
												<p><a class="classeurLibelle" onclick="changerSection('contenu')" ><img src="../images/ic-fleche.png" alt="" /><label class="inline"><?php echo TXT_EDITION_DU_CONTENU_DES_CLASSEURS ?></label></a></p>
											</div>
											
											<div id="contenuOuvert" style="display:none;">
											
												<p class="classeurLibelle"><img src="../images/ic-fleche-bas.png" alt="" /><label class="inline"><?php echo TXT_EDITION_DU_CONTENU_DES_CLASSEURS ?></label></p>

												
												<!--  -----------------------------------------------------------------  -->
												<!--  Liste des classeurs - Début                                      -->
												<!--  -----------------------------------------------------------------  -->
												
												
												
										<?php 	for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {

													$nbElements = array();
												
													// Initialiser le nombre d'élément
													$nbElements[$i] = 0;
											
													if ($item->get("classeur_" . $i . "_statut") == "1" ) {

										?> 												
													
														<!--  -----------------------------------------------------------------  -->
														<!--  Afficher entête du classeur - Début                                -->
														<!--  -----------------------------------------------------------------  -->
													
										<?php 			if ( $item->get("type_elements1") == "texte" )  {
										?>
										
															<div style="margin-right:375px;" class="fauxOnglet <?php if ( $i > 1 ) { echo "margTop20"; } ?>" style="min-height:16px;"><?php echo $item->get("classeur_" . $i . "_titre" ) ?></div>
															
										<?php 			} else { 
															$libelle = "classeur_" . $i . "_titre";
															$img = $item->get($libelle);
										?>
										
															<div style="margin-right:375px;" class="fauxOnglet <?php if ( $i > 1 ) { echo "margTop20"; } ?>" style="min-height:80px;"><img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="<?php echo TXT_ONGLET_SANS_IMAGE ?>" /></div>
															
										<?php 			} 			
										?>
														<!--  -----------------------------------------------------------------  -->
														<!--  Afficher entête du classeur - Fin                                  -->
														<!--  -----------------------------------------------------------------  -->
																								
                                                
                                                        <div style="width:375px; text-align:right; margin-top:-20px" class="flDr">
                                                                    
                                                            <a id="" class="padDr15" onclick="supprimerClasseurElements('<?php echo $i ?>');return false;" href="#"><img class="icDelete icDeleteVAlign" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER_LES_ELEMENTS_SELECTIONNES ?>" /><?php echo TXT_SUPPRIMER_LES_ELEMENTS_SELECTIONNES ?></a>
                                                            <a id="" class="" onclick="ajouterClasseurElement('<?php echo $i ?>');return false;" href="#"><img class="icAdd icAddVAlign" src="../images/ic-add.png" alt="<?php echo TXT_AJOUTER_UN_ELEMENT ?>" /><?php echo TXT_AJOUTER_UN_ELEMENT ?></a>
                                                        </div>
														
														<table class="tblItemClasseurs clear">
														
														<!--  -----------------------------------------------------------------  -->
														<!--  ÉLÉMENT : Afficher les éléments du classeur - Début                -->
														<!--  -----------------------------------------------------------------  -->
													
														
														<?php 
																$nbElementsTotal = 0;
																for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {

																	$cle = "classeur_" . $i . "_element_" . $j;
																	$cleStatut = $cle . "_statut";
																	$cleTexte = $cle . "_texte";
																	$statut = $item->get($cleStatut);
																	$texte = $item->get($cleTexte);
																	$img = $texte;
																	
																	if ($texte == "") {
																		$texte = TXT_ELEMENT_SANS_TEXTE;
																	}
																	
																	if ( $statut == '1') {

																		$nbElementsTotal++;
	
																		// Prendre note du nombre d'élément
																		$nbElements[$i] = $nbElements[$i] + 1;
														?>
																	
																		<input type="hidden" name="item_<?php echo $cleStatut ?>" value="<?php echo $statut ?>" ?>
																		

																		<!--  -----------------------------------------------------------------  -->
																		<!--  ÉLÉMENT : Afficher le champ texte ou la sélection d'une image      -->
																		<!--  -----------------------------------------------------------------  -->
																		
																		
														<?php 			if ( $item->get("type_elements2") == "texte" )  { ?>																	
																			
																			<tr id="ligne_classeur_<?php echo $i ?>_element_<?php echo $j ?>" name="ligne_classeur">
																			  <td>
																			    <input name="item_selection_<?php echo $cleTexte ?>" type="checkbox" value="<?php echo $cle ?>" />&nbsp;<a href="#" id="lien_classeur_<?php echo $i ?>_element_<?php echo $j ?>" onclick="afficherEditeurElement('classeur_<?php echo $i ?>_element_<?php echo $j ?>');return false;"><?php echo $texte ?></a>
																			  </td>
																			</tr>
														<?php	
															 			} else {

																			// Traiter les éléments de type image

																			if ($texte == TXT_ELEMENT_SANS_TEXTE || $texte == "") {
														?>
																				<tr>
																					<td>
																						<!--  Image vide pour représenter un élément -->
																						<input name="item_selection_<?php echo $cleTexte ?>" type="checkbox" value="<?php echo $cle ?>" />
																						&nbsp;
																						<a href="#" id="lien_classeur_<?php echo $i ?>_element_<?php echo $j ?>" onclick="afficherEditeurElement('classeur_<?php echo $i ?>_element_<?php echo $j ?>');return false;">
																							[ IMAGE VIDE ICI ]
																						</a>
																					</td>
																				</tr>

														<?php				} else { 
														?>
															
																				<!--  Afficher l'image d'un élément -->				
																				<tr>
																				  <td>
																				    <input name="item_selection_<?php echo $cleTexte ?>" type="checkbox" value="<?php echo $cle ?>" />
																				    &nbsp;
																				    <a href="#" id="lien_classeur_<?php echo $i ?>_element_<?php echo $j ?>" onclick="afficherEditeurElement('classeur_<?php echo $i ?>_element_<?php echo $j ?>');return false;">
																				    	<img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="" />
																				    </a>
																				  </td>
																				</tr>
																				
														<?php 				}								
																		}
																	} 
																}
																
																
																// Situation où il y a aucun élément
																if ($nbElementsTotal == 0) {
														?>
																	<tr><td><br /></td></tr>						
														<?php 
																  
																}
														?>
														
														<!--  -----------------------------------------------------------------  -->
														<!--  ÉLÉMENT : Afficher les éléments du classeur - FIN                  -->
														<!--  -----------------------------------------------------------------  -->

														</table>
														
														<!--  -----------------------------------------------------------------  -->
														<!--  ÉDITEUR : Afficher l'éditeur pour un élément - Début               -->
														<!--  -----------------------------------------------------------------  -->
														
														<?php 
																for ($j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {

																// Infos
																$cle = "classeur_" . $i . "_element_" . $j;
																$cleStatut = $cle . "_statut";
																$cleTexte = $cle . "_texte";
																$statut = $item->get($cleStatut);
																$texte = $item->get($cleTexte);

																// Navigation
																$elementPrec = $j - 1;
																$elementSuiv = $j + 1;
																if ($elementPrec < 1) {
																	$elementPrec = 1;
																}
																if ($elementSuiv > $nbElements[$i] ) {
																	$elementSuiv = $nbElements[$i];
																}
																
																if ( $statut == '1') { 
														?>
	
																	<!-- Début de l'éditeur -->
				
																	<div class="cadre cadreEditeur margGa30 margBot20" id="editeur_classeur_<?php echo $i ?>_element_<?php echo $j ?>">
																		<p class="cadreTitre padTo15">
																			<label class="ocre"><?php echo TXT_ELEMENT ?> <?php echo $j ?> </label>
																		</p>
																		<div class="flDr" style="margin:-25px 0px 5px 0px; ">
																		
																		<a class="padDr15" onclick="supprimerClasseurElement('<?php echo $i ?>','<?php echo $j ?>');return false;"><img class="icDelete icDeleteVAlign" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER_CET_ELEMENT ?>" /><?php echo TXT_SUPPRIMER_CET_ELEMENT ?></a>
																		
																				<span class="txt"><?php echo TXT_ELEMENT ?></span>
																				<input class="btnSubmit btnPrev" type="button" onclick="afficherEditeurElement('classeur_<?php echo $i ?>_element_<?php echo $elementPrec ?>');return false;" name="" value="" />
																				<?php echo $j ?> <?php echo TXT_DE ?> <?php echo $nbElements[$i] ?>
																				<input class="btnSubmit btnNext" type="button" onclick="afficherEditeurElement('classeur_<?php echo $i ?>_element_<?php echo $elementSuiv ?>');return false;" name="" value="" />
																		</div>	
																		<table class="tblItemClassement">
																			<tr>
																				<td class="cadreContenu">
																					
																					<!-- Afficher le informations sur l'élément courant -->
																					
																	<?php 			if ( $item->get("type_elements2") == "texte" )  { ?>	
																					
																					    <!-- Élément texte - titre -->
																						<p class="padBot0"><label for=""><?php echo TXT_TEXTE_ELEMENT ?></label></p>
																					
																						<!--  Afficher un champ texte dans l'éditeur -->
																						<textarea id="classeur_<?php echo $i ?>_element_<?php echo $j ?>_texte" name="item_classeur_<?php echo $i ?>_element_<?php echo $j ?>_texte" class="wmax" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>" onblur="majTexteElement('classeur_<?php echo $i ?>_element_<?php echo $j ?>',true)"><?php echo $texte ?></textarea>
																						
																						
																	<?php 			} else { 
																					 	 // Preparer les champs
																					  	$libelle = "classeur_" . $i . "_element_" . $j . "_texte"; 
																					  	$img = $item->get($libelle);
																	 ?>
																	 
																	 					<!-- Élément image - titre -->
																						<p class="padBot0"><label for=""><?php echo TXT_IMAGE_ELEMENT ?></label></p>
															
																						<input type="hidden" id="item_<?php echo $libelle ?>" name="item_<?php echo $libelle ?>" value="<?php echo $img ?>" /> 
																																				
																	<?php				if ($texte == TXT_ELEMENT_SANS_TEXTE || $texte == "") {
																	?>
																									<!--  Afficher la boite de selection -->
																									<div id="item_<?php echo $libelle ?>_selection">
																										<!--  Afficher le menu de selection d'une image -->
																										<div class="menuContexte displayInline">
																											<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_IMAGE?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																											<?php include '../ressources/includes/menu-contexte-ajouter-image.php' ?>
																										</div>
																									</div>
																									<!--  / Afficher la boite de selection -->
																	<?php 				} else {  
																							
																	?>
																									<!--  Afficher l'image -->
																									<div id="item_<?php echo $libelle ?>_affichage">
																										<p class="elementImage">
																											<a href="media.php?demande=media_presenter&media_id_media=<?php echo $img ?>" target="media_<?php echo $img ?>" title="">
																												<img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="<?php echo TXT_PREFIX_MEDIA . $img ?>" />
																											</a>												
																											
																											<span class="icBtnDel" id="item_<?php echo $libelle ?>_supp" <?php if ($img == 0) { ?> style="display: none;" <?php } ?>>
																												<a href="#" onclick="viderChampImage('item_<?php echo $libelle ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="" /></a>
																											</span>
																										</p>
																									</div>
																									<!-- / Afficher l'image -->
										
																		<?php 				} 
																					
																					} 
																		?>
																					
																					
																					
																					<p class="padBot0"><label for=""><?php echo TXT_RETROACTIONS_POUR_CHAQUE_CLASSEUR ?></label></p>
																					
																					<table>
												
																						<!--  Liste des rétros pour chaque élément - Début -->																															
																							
																						<?php 	for ($k = 1; $k <= NB_MAX_CLASSEURS; $k++) {
					
																								if ($item->get("classeur_" . $k . "_statut") == "1" ) { ?> 		
																							
																									<tr>
																										<td style="width:20%;">
																										
																							<?php 			if ( $item->get("type_elements1") == "texte" )  { ?>	
																							
																												<!-- Afficher le titre du classeur pour la rétro -->																										
																												<?php echo $item->get("classeur_" . $k . "_titre" ) ?>
																												
																							<?php 			} else { 					
																												$libelle = "classeur_" . $k . "_titre";
																												$img = $item->get($libelle);
																							?>
																									
																												<!-- Afficher l'image du classeur pour la rétro -->
																												<a href="media.php?demande=media_presenter&media_id_media=<?php echo $img ?>" target="media_<?php echo $img ?>" title="">
																													<img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="" />
																												</a>
																											
																							<?php 			}
																							?>
																											
																										</td>
																										<td>
																											<textarea id="classeur_<?php echo $i ?>_element_<?php echo $j ?>_retro_<?php echo $k?>" name="item_classeur_<?php echo $i ?>_element_<?php echo $j ?>_retro_<?php echo $k?>" class="editeur wmax" rows="2" cols="200" placeholder="<?php if ($i == $k) echo TXT_INSCRIRE_VOTRE_RETROACTION_POSITIVE; else echo TXT_INSCRIRE_VOTRE_RETROACTION_NEGATIVE; ?>" ><?php echo $item->get("classeur_" . $i . "_element_" . $j . "_retro_" . $k) ?></textarea></td>
																									</tr>
																								
																						<?php 		} 
																								} 
																						?>
																						
																						<!--  Liste des rétros pour chaque élément - Fin -->
																						
																					</table>
																					
																				</td>
																			</tr>
																		</table>
																	</div>
																	
																	<!--  -----------------------------------------------------------------  -->
																	<!--  ÉDITEUR : Afficher l'éditeur pour un élément - Fin                 -->
																	<!--  -----------------------------------------------------------------  -->
																	
													<?php 		}
															}
															
														}
													?>
										
										<br />
													
										<?php 													
												}
										?>

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

												<p><label for="item_orientation_elements"><?php echo TXT_ORIENTATION_DES_ELEMENTS_A_CLASSER ?></label>
													<select class="w250 suiviModif" id="item_orientation_elements" name="item_orientation_elements">
														<option value="horizontale" <?php echo $item->get('orientation_elements_horizontale') ?> ><?php echo TXT_HORIZONTALE ?></option>
														<option value="verticale" <?php echo $item->get('orientation_elements_verticale') ?> ><?php echo TXT_VERTICALE ?></option>
													</select>
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
																	
																	<p><label for="item_orientation_elements_quest"><?php echo TXT_ORIENTATION_DES_ELEMENTS_A_CLASSER ?></label>
																		<select class="w250 suiviModif" id="item_orientation_elements_quest" name="item_orientation_elements_quest">
																			<option value="horizontale" <?php echo $item->get('orientation_elements_quest_horizontale') ?> ><?php echo TXT_HORIZONTALE ?></option>
																			<option value="verticale" <?php echo $item->get('orientation_elements_quest_verticale') ?> ><?php echo TXT_VERTICALE ?></option>
																		</select>
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
