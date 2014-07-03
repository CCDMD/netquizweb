<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_MEDIAS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript" src="../jwplayer/jwplayer.js"></script>
	<script type="text/javascript">

	// Changer de page
	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $media->get("page_courante") ?>";

		if (page != pageCour) {
			document.frm.pagination_page_dest.value=page;
			document.frm.demande.value="media_sauvegarder";
			document.frm.submit();
		}
	}

	// Activer suivi
	function activerSuivi() {
		envoiSuiviMedia('media.php', 'media_suivi', '<?php echo $media->get("id_media") ?>');
	}

	// Sélection automatique de la source
	function selectionSource(src) {
		
		if (src == "mediaFichier") {
			$('input:radio[name="media_source"]').filter('[value="fichier"]').attr('checked', true);
		}
		if (src == "mediaWeb") {
			$('input:radio[name="media_source"]').filter('[value="web"]').attr('checked', true);
		}
	}

	// Supprimer fichier
	function supprimerFichier() {

		// Cacher les sections relatives au fichier
		$('#fichierActuel').hide();
		$('#apercuMedia').hide();

		// Indiquer la suppression du fichier
		document.frm.media_fichier_supprimer.value = "1";
	}


	// Démarrage
	$(document).ready(function() {

		// Resize des fenêtres inital pour affichage plus rapide de l'interface
		resizePanels();

		// Activer le clic sur l'étoile pour le suivi
		$("#icone-etoile").click(function() {
			envoiSuiviMedia('media.php', 'media_suivi', '<?php echo $media->get("id_media") ?>');
		});
		
		// Afficher aperçu au besoin
		afficherApercu('<?php echo $media->get("apercu")?>');
		
	}); 

	$(window).load(function() {

	    // Redimensionner l'image
		$("image_media").redimensionner();

	  // Redimensionner à nouveau au cas où l'image n'est pas été chargée à temps (FIX pour IE 11)
		setTimeout(function(){

			// Redimensionner l'image
			$("image_media").redimensionner();
			  
		},500);

	});
	
	</script>
	
</head>

<body id="bBMedias" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-medias.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
								<form id="frm" name="frm" action="media.php" method="post" enctype="multipart/form-data">
								<input type="hidden" name="demande" value="media_sauvegarder" />
								<input type="hidden" name="media_id_media" value="<?php echo $media->get("id_media") ?>" />
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								<input type="hidden" name="media_fichier_supprimer" value="" />
								<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
								<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_MEDIA . $media->get("id_media") ?>" />								
								<input type="hidden" name="verrou_id_element2" value="" />
								
								
								<div class="filAriane"><h2><img src="../images/ic-medias.png" alt="<?php echo TXT_MES_MEDIAS ?>" /><a href="media.php?demande=media_liste"><?php echo TXT_MES_MEDIAS ?></a><span class="sep">&gt;</span><?php echo $media->get("titre") ?>  <span class="id">(<?php echo TXT_PREFIX_MEDIA . $media->get("id_media")?>)</span></h2></div>
								<div class="flDr menuContexteGa top-16">
									<img src="../images/ic-tools.png" alt="" />
									<?php include '../ressources/includes/menu-contexte-medias.php' ?>
								</div>
								
									<div class="detail">
										<div class="detailTop"><div>
											<div class="flGa">
												<?php if ($media->get("suivi") == "1") {?>
													<img id="icone-etoile" src="../images/ic-star-jaune.png" alt="" />
												<?php } else { ?>
													<img id="icone-etoile" src="../images/ic-star-gris.png" alt="" />
												<?php }?>
												
												
		  										<?php 
											  		$liens = "";
	
													// Liste des items pour ce média
													$listeLiensItems = $media->get("liste_liens_items");
													if (! empty($listeLiensItems) ) {
														 foreach ($listeLiensItems as $lien) { $liens .= $lien . "<br />"; }
													}
											  		
											  		// Listes des questionnaires pour ce média
											  		$listeLiensQuest = $media->get("liste_liens_questionnaires");
													if (! empty($listeLiensQuest) ) {
														 foreach ($listeLiensQuest as $lien) { $liens .= $lien . "<br />"; }
													}
	
													// Liste des langues pour ce média
													$listeLiensLangues = $media->get("liste_liens_langues");
													if (! empty($listeLiensLangues) ) {
														 foreach ($listeLiensLangues as $lien) { $liens .= $lien . "<br />"; }
													}
											  
													if (! empty($liens) ) { ?>
													
													<a href="#" class="infobulle"><img src="../images/ic-link.png" alt="" /><span><?php echo $liens ?></span></a>
													
												<?php } ?>
																							
											</div>
											<input class="btnReset annuler" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER?>" /></div>
										</div>
	
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
											
												<p><label for="media_titre"><?php echo TXT_TITRE_DU_MEDIA?></label>
													<input class="wmax suiviModif" type="text" id="media_titre" name="media_titre" value="<?php echo $media->get("titre")?>"/>
												</p>
	
	
												<div class="wdemiGa">
													<p class="btnRadioMedia"><input class="w15" type="radio" name="media_source" value="fichier" id="mediaFichier" <?php echo $media->get("source_fichier") ?>/>&nbsp;<span class="gras"><?php echo TXT_FICHIER_MEDIA ?></span></p>
													<?php if ($media->get("fichier_usager") != "") { ?>
														<div id="fichierActuel" class="padGa25"><?php echo TXT_FICHIER_ACTUEL, "&nbsp;:&nbsp;", $media->get("fichier_usager"); ?> <a href="#" onclick="supprimerFichier()"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a></div>
													<?php }?>
													<p class="padGa25">
														<?php if ($media->get("fichier_usager") != "") echo TXT_NOUVEAU_FICHIER . " :" ?> <input type="file" name="media_fichier_nouveau"  onchange="selectionSource('mediaFichier')"/>
														<a href="#" onclick="document.frm.media_fichier_nouveau.value=''"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
													</p>
	
													<p class="btnRadioMedia"><input class="w15" type="radio" name="media_source" value="web" id="mediaWeb" <?php echo $media->get("source_web") ?> />&nbsp;<span class="gras"><?php echo TXT_MEDIA_PROVENANT_AUTRE_SITE ?></span></p>
													<p class="padGa25">
														<textarea name="media_url" rows="5" cols="200" class="wmax" onchange="selectionSource('mediaWeb')" placeholder="<?php echo TXT_COPIER_COLLER_CODE_HTML_OU_URL_DU_MEDIA ?>"><?php echo $media->get("url") ?></textarea>
													</p>
												</div>
												
												<div id="apercuMedia" class="wdemiDr padTo20">
												
													<?php if ($media->get("source") == "fichier" && $media->get("fichier") != "") {
														
															if ($media->get("type") == "image") { ?>
																<div><a href="media.php?demande=media_presenter&media_id_media=<?php echo $media->get("id_media")?>" target="apercu"><img id="image_media" src="media.php?demande=media_afficher&media_id_media=<?php echo $media->get("id_media") ?>" alt="" /></a></div>
																
													<?php 	} elseif( $media->get("type") == "video") { ?>
																								
																<div><div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?>...</div></div>
																<script type="text/javascript">
																	jwplayer("container").setup({
																		'flashplayer': "../jwplayer/player.swf",
																		'controlbar': 'bottom',
																		'file': 'media.php?demande=media_afficher&media_id_media=<?php echo $media->get("id_media") ?>',
																		'provider': 'video',
																		'height': 200,
																		'width': 355
																	});
																</script>
		
													<?php  	} elseif( $media->get("type") == "son") { ?>
													
																<div><div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?>...</div></div>
																<script type="text/javascript">
																	jwplayer("container").setup({
																		'flashplayer': "../jwplayer/player.swf",
																		'controlbar': 'bottom',
																		'file': 'media.php?demande=media_afficher&media_id_media=<?php echo $media->get("id_media") ?>',
																		'provider': 'audio',
																		'height': 22,
																		'width': 355
																	});
																</script>
													
													<?php 	}
														 } ?>
											
													<?php if ($media->get("source") == "web" && $media->get("url") != "") {
													
															if ($media->get("type") == "image") { ?>
													
																<div><a href="media.php?demande=media_presenter&media_id_media=<?php echo $media->get("id_media")?>" target="apercu"><img id="image_media" src="<?php echo $media->get("url") ?>" width="400" alt="" /></a></div>
														
													<?php 	} elseif( $media->get("type") == "video") { ?>
													
																	<a href="media.php?demande=media_presenter&media_id_media=<?php echo $media->get("id_media")?>" target="apercu"><img src="../images/media-video.png" alt=""></a>
																
													<?php	} elseif( $media->get("type") == "son") { ?>
													
																<div><div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?>...</div></div>
																<script type="text/javascript">
																	jwplayer("container").setup({
																		'flashplayer': "../jwplayer/player.swf",
																		'controlbar': 'bottom',
																		'file': 'http://<?php echo $media->get("url") ?>',
																		'provider': 'audio',
																		'height': 22,
																		'width': 355
																	});
																</script>
														
													<?php 	}
														 } ?>
	
												</div>
												
												
												<div class="clear">
													<p><label for="media_description"><?php echo TXT_DESCRIPTION ?></label>
														<textarea id="media_description" name="media_description" class="wmax suiviModif" rows="5" cols="200" placeholder="<?php echo TXT_INSCRIRE_UNE_DESCRIPTION ?>"><?php echo $media->get("description")?></textarea>
													</p>
													<br /><hr />
													<p><label for="media_remarque"><?php echo TXT_REMARQUE ?></label>
														<textarea id="media_remarque" name="media_remarque" class="wmax suiviModif" rows="5" cols="200"  placeholder="<?php echo TXT_INSCRIRE_UNE_REMARQUE_UTILE ?>"><?php echo $media->get("remarque")?></textarea>
													</p>
												</div>						
											
											</div>						
										</div>						
	
										<div class="detailBot"><div>
											<input class="btnReset annnuler" name="btnReset" id="btnReset2" type="reset" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="submit" value="<?php echo TXT_ENREGISTRER?>" /></div>
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
</body>
</html>
