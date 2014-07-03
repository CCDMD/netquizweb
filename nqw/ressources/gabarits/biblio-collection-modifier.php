<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_COLLECTIONS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">

	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $collection->get("page_courante") ?>";

		if (page != pageCour) {
			document.frm.pagination_page_dest.value=page;
			document.frm.demande.value="collection_sauvegarder";
			document.frm.submit();
		}
	}

	</script>
	
</head>

<body id="bBCollections" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-collections.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<form id="frm" name="frm" action="bibliotheque.php" method="post">
								<input type="hidden" name="demande" value="collection_sauvegarder" />
								<input type="hidden" name="collection_id_collection" value="<?php echo $collection->get("id_collection") ?>" />
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
								<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_COLLECTION . $collection->get("id_collection") ?>" />
								<input type="hidden" name="verrou_id_element2" value="" />
	
									<div class="filAriane"><h2><img src="../images/ic-collection.png" alt="<?php echo TXT_MES_COLLECTIONS ?>" /><a href="bibliotheque.php?demande=collection_liste"><?php echo TXT_MES_COLLECTIONS ?></a><span class="sep">&gt;</span><?php echo $collection->get("titre")?> <span class="id">(<?php echo TXT_PREFIX_COLLECTION . $collection->get("id_collection")?>)</span></h2></div>
									<div class="flDr menuContexteGa">
										<img src="../images/ic-tools.png" alt="" />
										<?php include '../ressources/includes/menu-contexte-collections.php' ?>
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
	
												<p><label for="collection_titre"><?php echo TXT_TITRE_DE_LA_COLLECTION?></label>
													<input class="w250 suiviModif" type="text" id="collection_titre" name="collection_titre" value="<?php echo $collection->get("titre")?>"/></p>
												<p><label for="collection_remarque"><?php echo TXT_REMARQUE ?></label>
													<textarea id="collection_remarque" name="collection_remarque" rows="5" cols="200" class="wmax suiviModif" placeholder="<?php echo TXT_INSCRIRE_UNE_REMARQUE_UTILE ?>"><?php echo $collection->get("remarque")?></textarea></p>
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
</body>
</html>