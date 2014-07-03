											<table class="tblCategorieApercu">
												<tr>
													<td class="c1">
														<p><label for="item_id_categorie"><img src="../images/ic-categorie.png" alt="" /><?php echo TXT_CATEGORIE ?></label>
		
															<select class="w250 margBot5 suiviModif" id="item_id_categorie" name="item_id_categorie" onchange="validerCategorie()">
																<option value="" disabled="disabled" selected="selected" class="champPlaceholder"><?php echo TXT_CHOISIR_UNE_CATEGORIE ?></option>
															<?php foreach ($listeCategorie as $id_categorie => $libelle) {
																if ($id_categorie != 0) { ?>
																													
																	<option value="<?php echo $id_categorie ?>" <?php if ($item->get('id_categorie') == $id_categorie) { echo "selected='selected'"; } ?> ><?php echo $libelle ?></option>
															
															<?php }
															} ?>
															</select>
															<input class="w250"  type="text" id="categorieText" name="item_categorie_ajouter" placeholder="<?php echo TXT_INSCRIRE_NOUVELLE_CATEGORIE_AU_BESOIN ?>" />
														</p>
													</td>

												</tr>
											</table>