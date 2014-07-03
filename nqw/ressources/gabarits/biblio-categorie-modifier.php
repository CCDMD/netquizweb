<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_CATEGORIES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">

	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $categorie->get("page_courante") ?>";

		if (page != pageCour) {
			document.frm.pagination_page_dest.value=page;
			document.frm.demande.value="categorie_sauvegarder";
			document.frm.submit();
		}
	}

	</script>
	
</head>

<body id="bBCategories" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-categories.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<form id="frm" name="frm" action="bibliotheque.php" method="post">
								<input type="hidden" name="demande" value="categorie_sauvegarder" />
								<input type="hidden" name="categorie_id_categorie" value="<?php echo $categorie->get("id_categorie") ?>" />
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
								<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_CATEGORIE . $categorie->get("id_categorie") ?>" />
								<input type="hidden" name="verrou_id_element2" value="" />
	
									<div class="filAriane"><h2><img src="../images/ic-categorie.png" alt="<?php echo TXT_MES_CATEGORIES ?>" /><a href="bibliotheque.php?demande=categorie_liste"><?php echo TXT_MES_CATEGORIES ?></a><span class="sep">&gt;</span><?php echo $categorie->get("titre")?> <span class="id">(<?php echo TXT_PREFIX_CATEGORIE . $categorie->get("id_categorie")?>)</span></h2></div>
	
									<div class="flDr menuContexteGa">
										<img src="../images/ic-tools.png" alt="" />
										<?php include '../ressources/includes/menu-contexte-categories.php' ?>
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
	
												<p><label for="categorie_titre"><?php echo TXT_TITRE_DE_LA_CATEGORIE ?></label>
													<input class="w250 suiviModif" type="text" id="categorie_titre" name="categorie_titre" value="<?php echo $categorie->get("titre")?>"/></p>
												<p><label for="categorie_remarque"><?php echo TXT_REMARQUE ?></label>
													<textarea id="categorie_remarque" name="categorie_remarque" rows="5" cols="200" class="wmax suiviModif" placeholder="<?php echo TXT_INSCRIRE_UNE_REMARQUE_UTILE ?>"><?php echo $categorie->get("remarque")?></textarea></p>
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
