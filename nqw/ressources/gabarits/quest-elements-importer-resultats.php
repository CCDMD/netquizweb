<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_IMPORTER_ELEMENTS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">

		// Bouton recommencer
		function recommencer() {
			document.location="questionnaires.php?demande=elements_importer_formulaire";
		}
		
	</script>
		
</head>

<body id="bQuestionnaires" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-quest1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-vide.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<form name="frm" id="frm" action="questionnaires.php" method="post" enctype="multipart/form-data">

								<div class="filAriane"><h2><img src="../images/ic-questionnaires.png" alt="<?php echo TXT_QUESTIONNAIRES ?>" /><?php echo TXT_MES_QUESTIONNAIRES ?> <span class="sep">&gt;</span> <?php echo TXT_IMPORTER_ELEMENTS ?></h2></div>

                                <div class="detail">
									<div class="detailTop"><div>
										<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="recommencer()" value="<?php echo TXT_NOUVELLE_IMPORTATION ?>"  />
									</div></div>
                                    
									<div id="section1" class="detailContenant">
										<div class="detailContenu">

											<h3 class="noir14"><?php echo TXT_RESULTATS_DE_L_IMPORTATION ?></h3>
											
											<ul><?php echo $messages ?></ul>
										</div>
                                    </div>
											
                                    <div class="detailBot"><div>
                                        <input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="recommencer()" value="<?php echo TXT_NOUVELLE_IMPORTATION ?>"  />
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
</body>
</html>
