											<div class="wdemiGa">
												<p>
												  <label>
													<?php if ($aiguilleur == 'questionnaires') {
														echo TXT_MESSAGES_GENERAUX_DE_LA_LANGUE_DU_QUESTIONNAIRE;
													} else {
														echo TXT_MESSAGES_GENERAUX_DE_LA_LANGUE_DE_APERCU;
													}
													?>
												  </label>
												 </p>
											
												<p><label><?php echo TXT_MESSAGE_POUR_BONNE_REPONSE ?></label>
												   <?php echo $langueItem->get("message_bonnereponse")?></p>

												<p><?php echo TXT_FICHIER_ACTUEL ?> :&nbsp;
													<?php if ($langueItem->get("media_bonnereponse") == 0) { 
															echo TXT_AUCUNE_SELECTION;  
														} else { ?>
															<a href="media.php?demande=media_presenter&media_id_media=<?php echo $langueItem->get("media_bonnereponse") ?>" target="media_<?php echo $langueItem->get("media_bonnereponse") ?>"><?php echo $langueItem->get("media_bonnereponse_txt") ?></a>
														<?php }?>
												</p>

												<p><label><?php echo TXT_MESSAGE_POUR_MAUVAISE_REPONSE ?></label>
												   <?php echo $langueItem->get("message_mauvaisereponse")?></p>

												<p><?php echo TXT_FICHIER_ACTUEL ?> :&nbsp;
													<?php if ($langueItem->get("media_mauvaisereponse") == 0) { 
															echo TXT_AUCUNE_SELECTION;  
														} else { ?>
															<a href="media.php?demande=media_presenter&media_id_media=<?php echo $langueItem->get("media_mauvaisereponse") ?>" target="media_<?php echo $langueItem->get("media_mauvaisereponse") ?>"><?php echo $langueItem->get("media_mauvaisereponse_txt") ?></a>
														<?php }?>
												</p>

												<p><label><?php echo TXT_MESSAGE_POUR_REPONSE_INCOMPLETE ?></label>
												   <?php echo $langueItem->get("message_reponseincomplete")?></p>

												<p><?php echo TXT_FICHIER_ACTUEL ?> :&nbsp;
													<?php if ($langueItem->get("media_reponseincomplete") == 0) { 
															echo TXT_AUCUNE_SELECTION;  
														} else { ?>
															<a href="media.php?demande=media_presenter&media_id_media=<?php echo $langueItem->get("media_reponseincomplete") ?>" target="media_<?php echo $langueItem->get("media_reponseincomplete") ?>"><?php echo $langueItem->get("media_reponseincomplete_txt") ?></a>
														<?php }?>
												</p>
											</div>

											<div class="wdemiDr">
												<p class="lnkCadre" id="lnkCadre2"><a onclick="activerModifierValeursMessages()"><?php echo TXT_MODIFIER_CES_VALEURS_POUR_CET_ITEM_SEULEMENT ?></a></p>

												<div class="cadre" id="cadre2" style="display:none;">
													<p class="cadreTitre"><label class="ocre"><?php echo TXT_VALEURS_MODIFIEES_POUR_CET_ITEM_SEULEMENT ?></label></p>
													<table class="tblItemParam">
														<tr>
															<td class="cadreContenu">
																<p><label for="item_reponse_bonne_message"><?php echo TXT_MESSAGE_POUR_BONNE_REPONSE ?></label>
																	<textarea class="w90pc suiviModif" id="item_reponse_bonne_message" name="item_reponse_bonne_message" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_UN_MESSAGE_PERSONNALISE_POUR_CET_ITEM_SINON_ETC ?>" ><?php echo $item->get("reponse_bonne_message")?></textarea></p>
		
																<!--  Bonne réponse - Ajouter un média -->
																<div>
																	<div class="menuContexte displayInline">
																		<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_MEDIA?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																		<div class="menuDeroul">
																			<ul class="sansTitre">
																				<li><a class="fenetreStd" href="media.php?demande=media_selectionner&filtre_type_media=tous" onclick="ouvrirSelectionMediaLien('item_reponse_bonne_media')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																				<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=tousr" onclick="ouvrirImportMediaLien('item_reponse_bonne_media')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																			</ul>
																		</div>
																	</div>
																</div>
																<input type="hidden" id="item_reponse_bonne_media" name="item_reponse_bonne_media" value="<?php echo $item->get("reponse_bonne_media") ?>" />
																<p id="item_reponse_bonne_media_lien">
																	
																		<?php if ($item->get("reponse_bonne_media") <= 0) { 
																			echo TXT_AUCUNE_SELECTION;  
																		} else { 
																			echo TXT_FICHIER_ACTUEL;
																		?>
																			:&nbsp;<a href="media.php?demande=media_presenter&media_id_media=<?php echo $item->get("reponse_bonne_media") ?>" target="media_<?php echo $item->get("reponse_bonne_media") ?>"><?php echo $item->get("reponse_bonne_media_txt") ?></a>
																		<?php }?>
																	
																	<span id="item_reponse_bonne_media_supp" <?php if ($item->get("reponse_bonne_media") <= 0) { ?> style="display: none;" <?php } ?>>
																		<a onclick="viderChampMedia('item_reponse_bonne_media','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
																	</span>
																</p>
		
																<p><label for="item_reponse_mauvaise_message"><?php echo TXT_MESSAGE_POUR_MAUVAISE_REPONSE ?></label>
																	<textarea class="w90pc suiviModif" id="item_reponse_mauvaise_message" name="item_reponse_mauvaise_message" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_UN_MESSAGE_PERSONNALISE_POUR_CET_ITEM_SINON_ETC ?>" ><?php echo $item->get("reponse_mauvaise_message")?></textarea></p>
		
																<!-- Mauvaise réponse - Ajouter un média -->
																<div>
																	<div class="menuContexte displayInline">
																		<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_MEDIA?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																		<div class="menuDeroul">
																			<ul class="sansTitre">
																				<li><a class="fenetreStd" href="media.php?demande=media_selectionner&filtre_type_media=tous" onclick="ouvrirSelectionMediaLien('item_reponse_mauvaise_media')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																				<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=tous" onclick="ouvrirImportMediaLien('item_reponse_mauvaise_media')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																			</ul>
																		</div>
																	</div>
																</div>
																<input type="hidden" id="item_reponse_mauvaise_media" name="item_reponse_mauvaise_media" value="<?php echo $item->get("reponse_mauvaise_media") ?>" />
																<p id="item_reponse_mauvaise_media_lien">
																	
																		<?php if ($item->get("reponse_mauvaise_media") <= 0) { 
																			echo TXT_AUCUNE_SELECTION;  
																		} else { 
																			echo TXT_FICHIER_ACTUEL;
																		?>
																			:&nbsp;<a href="media.php?demande=media_presenter&media_id_media=<?php echo $item->get("reponse_mauvaise_media") ?>" target="media_<?php echo $item->get("reponse_mauvaise_media") ?>"><?php echo $item->get("reponse_mauvaise_media_txt") ?></a>
																		<?php }?>
																	
																	<span id="item_reponse_mauvaise_media_supp" <?php if ($item->get("reponse_mauvaise_media") <= 0) { ?> style="display: none;" <?php } ?>>
																		<a onclick="viderChampMedia('item_reponse_mauvaise_media','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
																	</span>
																</p>
		
																<p><label for="item_reponse_incomplete_message"><?php echo TXT_MESSAGE_POUR_REPONSE_INCOMPLETE ?></label>
																	<textarea class="w90pc suiviModif" id="item_reponse_incomplete_message" name="item_reponse_incomplete_message" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_UN_MESSAGE_PERSONNALISE_POUR_CET_ITEM_SINON_ETC ?>" ><?php echo $item->get("reponse_incomplete_message")?></textarea></p>
		
																<!-- Réponse incomplète - Ajouter un média -->
																<div>
																	
																	<div class="menuContexte displayInline">
																		<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_MEDIA?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
																		<div class="menuDeroul">
																			<ul class="sansTitre">
																				<li><a class="fenetreStd" href="media.php?demande=media_selectionner&filtre_type_media=tous" onclick="ouvrirSelectionMediaLien('item_reponse_incomplete_media')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																				<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=tous" onclick="ouvrirImportMediaLien('item_reponse_incomplete_media')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																			</ul>
																		</div>
																	</div>
																</div>
																<input type="hidden" id="item_reponse_incomplete_media" name="item_reponse_incomplete_media" value="<?php echo $item->get("reponse_incomplete_media") ?>" />
																<p id="item_reponse_incomplete_media_lien">
																	
																		<?php if ($item->get("reponse_incomplete_media") <= 0) { 
																			echo TXT_AUCUNE_SELECTION;  
																		} else { 
																			echo TXT_FICHIER_ACTUEL;
																		?>
																			:&nbsp;<a href="media.php?demande=media_presenter&media_id_media=<?php echo $item->get("reponse_incomplete_media") ?>" target="media_<?php echo $item->get("reponse_incomplete_media") ?>"><?php echo $item->get("reponse_incomplete_media_txt") ?></a>
																		<?php }?>
																	
																	<span id="item_reponse_incomplete_media_supp" <?php if ($item->get("reponse_incomplete_media") <= 0) { ?> style="display: none;" <?php } ?>>
																		<a onclick="viderChampMedia('item_reponse_incomplete_media','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
																	</span>
																</p>
															</td>
															<td class="cadreLnkDisable"><a onclick="desactiverModifierValeursMessages()"><img class="icDelete" src="../images/ic-delete.png" alt="" /></a></td>
														</tr>
													</table>

												</div>
											</div>