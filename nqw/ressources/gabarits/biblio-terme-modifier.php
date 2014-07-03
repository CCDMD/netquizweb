<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_TERMES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies-avec-editeur.php' ?>    
	
	<script type="text/javascript">

	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $terme->get("page_courante") ?>";

		if (page != pageCour) {
			document.frm.pagination_page_dest.value=page;
			document.frm.demande.value="terme_sauvegarder";
			document.frm.submit();
		}
	}

	$(document).ready(function () {

		// Activer la sélection automatique du radio button pour le type de définition
	    $('.suiviRadioDefinition').keyup(function () {
	    	idTerme = $(this).attr('id').substring(6);
	    	selectionnerTypeDefinition(idTerme);
	    });

	});	

	</script>
	
</head>

<body id="bBTermes" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-termes.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<form id="frm" name="frm" action="bibliotheque.php" method="post">
								<input type="hidden" name="demande" value="terme_sauvegarder" />
								<input type="hidden" name="demandeRetour" value="" />
								<input type="hidden" name="terme_id_terme" value="<?php echo $terme->get("id_terme") ?>" />
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
								<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_TERME . $terme->get("id_terme") ?>" />
								<input type="hidden" name="verrou_id_element2" value="" />
	
									<div class="filAriane"><h2><img src="../images/ic-termes.png" alt="<?php echo TXT_MES_TERMES ?>" /><a href="bibliotheque.php?demande=terme_liste"><?php echo TXT_MES_TERMES ?></a><span class="sep">&gt;</span><?php echo $terme->get("terme")?> <span class="id">(<?php echo TXT_PREFIX_TERME . $terme->get("id_terme")?>)</span></h2></div>
									<div class="flDr menuContexteGa">
										<img src="../images/ic-tools.png" alt="" />
										<?php include '../ressources/includes/menu-contexte-termes.php' ?>
									</div>
	
									<div class="detail">
										<div class="detailTop"><div>
											<input class="btnReset annuler" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
										</div>
	
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
	
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
	
												<p><label for="terme_terme"><?php echo TXT_TERME ?></label>
													<input class="wmax suiviModif" type="text" id="terme_terme" name="terme_terme" value="<?php echo $terme->get("terme")?>" onclick="fermerEditeurs()"/></p>

												<p><label for="terme_variantes"><?php echo TXT_VARIANTES ?></label>(<?php echo TXT_SEPAREES_PAR_DES_RETOUR ?>)
													<textarea id="terme_variantes" name="terme_variantes" rows="2" cols="200" class="wmax suiviModif" placeholder="<?php echo TXT_INSCRIRE_VARIANTES ?>" onclick="fermerEditeurs()"><?php echo $terme->get("variantes")?></textarea></p>

												<p><label for="terme_texte"><?php echo TXT_DEFINITION ?></label></p>
												
												<div>
													<input class="btnRadioTermes" type="radio" name="terme_type_definition" value="texte" <?php if ($terme->get("type_definition") == "" || $terme->get("type_definition") == "texte") { echo "checked"; } ?>/><?php echo TXT_AJOUTER_UN_TEXTE ?>
													<textarea id="terme_texte" name="terme_texte" rows="2" cols="200" class="wmax editeur suiviModif suiviRadioDefinition" placeholder="<?php echo TXT_INSCRIRE_DEFINITION ?>"><?php echo $terme->get("texte")?></textarea>
												</div>
												
												<div class="padTo10">
													<input class="btnRadioTermes" type="radio" name="terme_type_definition" value="url" <?php if ($terme->get("type_definition") == "url") { echo "checked"; } ?>/><?php echo TXT_INSERER_UNE_ADRESSE_URL ?><br />
													<input class="wmax suiviModif suiviRadioDefinition" type="text" id="terme_url" name="terme_url" value="<?php echo $terme->get("url")?>" placeholder="<?php echo TXT_INSERER_UNE_ADRESSE_URL_VERS_UN_DOCUMENT ?>"/>
												</div>
												
													
												<!--  Ajouter une image -->
												
												<div class="padTo10">
														<div class="menuContexte displayInline">
                                                    <input class="btnRadioTermes" type="radio" name="terme_type_definition" value="media_image" <?php if ($terme->get("type_definition") == "media_image") { echo "checked"; } ?> class="suiviRadioDefinition"/>
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_IMAGE ?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=image" onclick="ouvrirSelectionMediaLien('terme_media_image')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=image" onclick="ouvrirImportMediaLien('terme_media_image')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													<input type="hidden" id="terme_media_image" name="terme_media_image" value="<?php echo $terme->get("media_image") ?>" />
													<p id="terme_media_image_lien">
														
															<?php if ($terme->get("media_image") == 0) { 
																echo TXT_AUCUNE_SELECTION;  
															} else { 
																echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $terme->get("media_image") ?>" target="media_<?php echo $terme->get("media_image") ?>"><?php echo $terme->get("media_image_txt") ?></a>
															<?php }?>
														
														<span id="terme_media_image_supp" <?php if ($terme->get("media_image") == 0) { ?> style="display: none;" <?php } ?>>
															<a href="#" onclick="viderChampMedia('terme_media_image','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
													
												</div>
													
						
												<!--  Ajouter un son -->
												
												<div class="padTo10">
														<div class="menuContexte displayInline">
                                                        <input class="btnRadioTermes" type="radio" name="terme_type_definition" value="media_son" <?php if ($terme->get("type_definition") == "media_son") { echo "checked"; } ?>/>
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_SON?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=son" onclick="ouvrirSelectionMediaLien('terme_media_son')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=son" onclick="ouvrirImportMediaLien('terme_media_son')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													<input type="hidden" id="terme_media_son" name="terme_media_son" value="<?php echo $terme->get("media_son") ?>" />
													<p id="terme_media_son_lien">
														
															<?php if ($terme->get("media_son") == 0) { 
																echo TXT_AUCUNE_SELECTION;  
															} else { 
																echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $terme->get("media_son") ?>" target="media_<?php echo $terme->get("media_son") ?>"><?php echo $terme->get("media_son_txt") ?></a>
															<?php }?>
														
														<span id="terme_media_son_supp" <?php if ($terme->get("media_son") == 0) { ?> style="display: none;" <?php } ?>>
															<a href="#" onclick="viderChampMedia('terme_media_son','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
												</div>
												
																				
												<!--  Ajouter une video -->
												
												<div class="padTo10">
														<div class="menuContexte displayInline">
                                                        <input class="btnRadioTermes" type="radio" name="terme_type_definition" value="media_video" <?php if ($terme->get("type_definition") == "media_video") { echo "checked"; } ?>/>
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UNE_VIDEO?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=video" onclick="ouvrirSelectionMediaLien('terme_media_video')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer&filtre_type_media=video" onclick="ouvrirImportMediaLien('terme_media_video')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													<input type="hidden" id="terme_media_video" name="terme_media_video" value="<?php echo $terme->get("media_video") ?>" />
													<p id="terme_media_video_lien">
														
															<?php if ($terme->get("media_video") == 0) { 
																echo TXT_AUCUNE_SELECTION;  
															} else { 
																echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $terme->get("media_video") ?>" target="media_<?php echo $terme->get("media_video") ?>"><?php echo $terme->get("media_video_txt") ?></a>
															<?php }?>
														
														<span id="terme_media_video_supp" <?php if ($terme->get("media_video") == 0) { ?> style="display: none;" <?php } ?>>
															<a href="#" onclick="viderChampMedia('terme_media_video','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
												</div>													
																			
												<div class="wmax100 clear padTo15"><hr /></div>
													
												<p><label for="terme_remarque"><?php echo TXT_REMARQUE ?></label>
													<textarea id="terme_remarque" name="terme_remarque" rows="5" cols="200" class="wmax suiviModif" placeholder="<?php echo TXT_INSCRIRE_UNE_REMARQUE_UTILE ?>"><?php echo $terme->get("remarque")?></textarea></p>
											</div>						
										</div>						
	
										<div class="detailBot"><div>
											<input class="btnReset annuler" name="btnReset" id="btnReset2" type="reset" value="<?php echo TXT_ANNULER ?>"  />
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
	<a class="fenetreEditeurMedia" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=image"></a>
</body>
</html>