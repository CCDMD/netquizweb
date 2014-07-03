					<div class="wdemiGa">
						<p><label for="item_info_comp1_titre"><?php echo TXT_INFORMATION_COMPLEMENTAIRE ?> 1</label>
							<input class="wmax suiviModif" type="text" id="item_info_comp1_titre" name="item_info_comp1_titre" value="<?php echo $item->get("info_comp1_titre") ?>" onclick="" placeholder="<?php echo TXT_INSCRIRE_LE_TITRE_DETAILS?>" /></p>
						<p><textarea id="item_info_comp1_texte" class="wmax editeur" name="item_info_comp1_texte" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_LE_TEXTE ?>"><?php echo $item->get("info_comp1_texte") ?></textarea></p>
					</div>
					<div class="wdemiDr">
						<p><label for="item_info_comp2_titre"><?php echo TXT_INFORMATION_COMPLEMENTAIRE ?> 2</label>
							<input class="wmax suiviModif" type="text" id="item_info_comp2_titre" name="item_info_comp2_titre" value="<?php echo $item->get("info_comp2_titre") ?>" onclick="" placeholder="<?php echo TXT_INSCRIRE_LE_TITRE_DETAILS?>" /></p>
						<p><textarea id="item_info_comp2_texte" class="wmax editeur suiviModif" name="item_info_comp2_texte" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_LE_TEXTE ?>"><?php echo $item->get("info_comp2_texte") ?></textarea></p>
					</div>
					<div class="wmax clear">
						<br /><hr />
						<p><label><?php echo TXT_MEDIA_EN_ENTETE ?></label></p>
						<p><label class="niv2" for="item_media_titre"><?php echo TXT_AJOUTER_UN_TEXTE ?></label>
							<input class="wmax suiviModif" type="text" id="item_media_titre" name="item_media_titre" value="<?php  echo $item->get("media_titre") ?>" onclick="fermerEditeurs()" placeholder="<?php echo TXT_INSCRIRE_LE_TITRE ?>" /></p>
						<p><textarea id="media_texte" class="wmax editeur suiviModif" name="item_media_texte" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_LE_TEXTE ?>"><?php  echo $item->get("media_texte") ?></textarea></p>						

						<!--  Ajouter une image -->
						<div class="padTo10">
							<div>
								<div class="menuContexte displayInline">
									<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_IMAGE ?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
									<div class="menuDeroul">
										<ul class="sansTitre">
											<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=image" onclick="ouvrirSelectionMediaLien('item_media_image')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
											<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=image" onclick="ouvrirImportMediaLien('item_media_image')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
										</ul>
									</div>
								</div>
							</div>
							<input type="hidden" id="item_media_image" name="item_media_image" value="<?php echo $item->get("media_image") ?>" />
							<p id="item_media_image_lien">
								
									<?php if ($item->get("media_image") == 0) { 
										echo TXT_AUCUNE_SELECTION;  
									} else { 
										echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
										<a href="media.php?demande=media_presenter&media_id_media=<?php echo $item->get("media_image") ?>" target="media_<?php echo $item->get("media_image") ?>"><?php echo $item->get("media_image_txt") ?></a>
									<?php }?>
								
								<span id="item_media_image_supp" <?php if ($item->get("media_image") == 0) { ?> style="display: none;" <?php } ?>>
									<a href="#" onclick="viderChampMedia('item_media_image','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
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
											<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=son" onclick="ouvrirSelectionMediaLien('item_media_son')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
											<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=son" onclick="ouvrirImportMediaLien('item_media_son')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
										</ul>
									</div>
								</div>
							</div>
							<input type="hidden" id="item_media_son" name="item_media_son" value="<?php echo $item->get("media_son") ?>" />
							<p id="item_media_son_lien">
								
									<?php if ($item->get("media_son") == 0) { 
										echo TXT_AUCUNE_SELECTION;  
									} else { 
										echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
										<a href="media.php?demande=media_presenter&media_id_media=<?php echo $item->get("media_son") ?>" target="media_<?php echo $item->get("media_son") ?>"><?php echo $item->get("media_son_txt") ?></a>
									<?php }?>
								
								<span id="item_media_son_supp" <?php if ($item->get("media_son") == 0) { ?> style="display: none;" <?php } ?>>
									<a href="#" onclick="viderChampMedia('item_media_son','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
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
											<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=video" onclick="ouvrirSelectionMediaLien('item_media_video')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
											<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=video" onclick="ouvrirImportMediaLien('item_media_video')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
										</ul>
									</div>
								</div>
							</div>
							<input type="hidden" id="item_media_video" name="item_media_video" value="<?php echo $item->get("media_video") ?>" />
							<p id="item_media_video_lien">
								
									<?php if ($item->get("media_video") == 0) { 
										echo TXT_AUCUNE_SELECTION;  
									} else { 
										echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
										<a href="media.php?demande=media_presenter&media_id_media=<?php echo $item->get("media_video") ?>" target="media_<?php echo $item->get("media_video") ?>"><?php echo $item->get("media_video_txt") ?></a>
									<?php }?>
								
								<span id="item_media_video_supp" <?php if ($item->get("media_video") == 0) { ?> style="display: none;" <?php } ?>>
									<a href="#" onclick="viderChampMedia('item_media_video','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
								</span>
							</p>
						</div>
						
					</div>