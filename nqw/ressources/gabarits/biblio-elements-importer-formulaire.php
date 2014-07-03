<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_IMPORTER_ELEMENTS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">

		// Bouton importer
		function importer() {

			if (document.frm.fichier_import.value != "") {
				document.frm.submit();
			}
		}

	</script>

</head>

<body id="bBItems" onload="resizePanels();">

	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-vide.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
                                <form name="frm" id="frm" action="bibliotheque.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="demande" value="elements_importer_envoi" />

								<div class="filAriane">
									<h2><img src="../images/ic-items.png" alt="<?php echo TXT_MES_ITEMS ?>" /><?php echo TXT_MES_ITEMS?> <span class="sep">&gt;</span> <?php echo TXT_IMPORTER_ELEMENTS ?></h2>
								</div>
								
                                <div class="detail">
									<div class="detailTop"><div>
										<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annuler()" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="button" onclick="importer()" value="<?php echo TXT_IMPORTER ?>" />
									</div></div>
                                    
									<div id="section1" class="detailContenant">
										<div class="detailContenu">

                                            <!--  Messages -->
                                            <?php include '../ressources/includes/message_onglet1.php' ?>
                                            <!--  /Messages -->

                                            
                                            <p>
                                                <label><?php echo TXT_FICHIER_A_IMPORTER ?></label>
                                                <input type="file" name="fichier_import"  />
                                                <a href="#" onclick="document.frm.fichier_import.value=''"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
                                            </p>

                                            
                                        </div>						
                                    </div>						
	
                                    <div class="detailBot"><div>
                                        <input class="btnReset" name="btnReset" id="btnReset2" type="reset" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit" name="btnSubmit" id="btnSubmit2" type="button" onclick="importer()" value="<?php echo TXT_IMPORTER ?>" />
									</div></div>		

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

	<!--  Lien pour fenêtre jaillissante servant à l'ajout d'items dans un questionnaire -->
	<a class="fenetreSelQuest" href="questionnaires.php?demande=questionnaire_selectionner"></a>
	
</body>
</html>
