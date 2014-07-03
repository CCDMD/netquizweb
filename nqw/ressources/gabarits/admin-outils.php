<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB_ADMIN ?> - <?php echo TXT_ADMINISTRATION ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
    
    <script type="text/javascript">

    function indexation(type, desc) {

    	if (confirm('<?php echo TXT_JS_CONFIRMER_INDEXATION1 ?>' + desc.toLowerCase() + '<?php echo TXT_JS_CONFIRMER_INDEXATION2 ?>') ) {
        	document.location = 'admin.php?demande=outils_indexation_' + type;
        }
    }

    function sauvegarde() {
       	document.location = 'admin.php?demande=outils_sauvegarde';
    }

    function ouvrirAcces() {
       	document.location = 'admin.php?demande=outils_acces_ouvrir';
    }

    function fermerAcces() {
       	document.location = 'admin.php?demande=outils_acces_fermer';
    }
    

    </script>
	
</head>

<body id="bAOutils" onload="resizePanels();">
	
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-admin.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-vide.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
							<div class="filAriane flGa"><h2><img src="../images/ic-outils.png" alt="<?php echo TXT_MAINTENANCE ?>" /><?php echo TXT_MAINTENANCE ?></h2></div>
                            
							<form id="frm" name="frm" action="admin.php" method="post">
							
								<div class="detail">
                                    <div class="detailTop"><div>&nbsp;</div></div>
									
									<div id="section1" class="detailContenant">
										<div class="detailContenu">
										
											<!--  Messages -->
											<div class="zoneMsgListe">
											<?php if (isset($messages)) { ?>
										
												<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1">
													<ul> 
														<?php echo $messages->getMessages() ?>
													</ul>
												</div>
												
												<script type="text/javascript">
													setTimeout(function() {
														$('#message1').animate({
														opacity: 0.0
													  }, 20000, function() {
														// Animation complete.
														$('#message1').hide(); // pour IE7 et IE8, apr�s le fadeout du texte, on éteint la boite
													  });
														
													}, 21000);
												</script> 
												
											<?php } ?>
                                            </div>
											<!--  /Messages -->

	                                        <p class="w600"><?php echo TXT_MAINTENANCE_LIGNE1 ?></p>
                                            
                                            <div class="flGa">
                                                <p class="padTo20 padBot0"><label><?php echo TXT_VERSION_NQW ?></label></p>
                                                <p>
                                                 <a class="lnkVersionNqw" href="<?php echo URL_AIDE_NQW . Securite::nettoyerNomfichierTirets(VERSION_NQW) ?>" target="_blank"><?php echo VERSION_NQW ?></a>
                                                </p>
                                            </div>
                                            
                                            <div class="flGa padTo20 margGa30">
                                                <!--  Messages -->
                                                <div class="zoneMsg">
                                                
                                                	<?php if ($messageMAJ != "") { ?>
                                                    	<div class="boxMsgWarn"><p><?php echo $messageMAJ ?></p></div>
                                                    <?php } ?>
                                                
                                                </div>
												<!--  /Messages -->
                                            </div>
	                                        
	                                        <p class="clear padTo20 padBot0"><label><?php echo TXT_MAINTENANCE_DU_SITE ?></label></p>
	                                        <p><?php echo TXT_MODE_MAINTENANCE ?></p>
                                            <p>
                                            <?php 
                                            // Déterminer si l'application est ouverte aux utilisateurs
                                            $maintenance = new Maintenance($log, $dbh);
                                            if ($maintenance->isApplicationDisponible()) {
                                            ?>
                                            	<span class="displayInline w200"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit3" type="button" onClick="fermerAcces()" value="<?php echo TXT_FERMER_ACCES ?>" /></span>
                                            	
                                            <?php } else { ?>
                                            
                                             	<span class="displayInline w200"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit3" type="button" onClick="ouvrirAcces()" value="<?php echo TXT_PERMETTRE_ACCES ?>" /></span>
                                             	
                                            <?php }?>
	                                        </p>

	                                        <p class="padTo20 padBot0"><label><?php echo TXT_SAUVEGARDE_DE_LA_BASE_DE_DONNEES ?></label></p>
	                                        <p class="w600"><?php echo TXT_NOTE_SAUVEGARDE ?></p>
                                            <p>
                                                <span class="displayInline w200"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit3" type="button" onClick="sauvegarde()" value="<?php echo TXT_EXPORTER_FORMAT_SQL ?>" /></span>
	                                        </p>
	                                        
	                                        <p class="padTo20 padBot0"><label><?php echo TXT_INDEXATION ?></label></p>
                                            <p class="w600"><?php echo TXT_NOTE_INDEXATION1 ?></p>
                                            <p class="w600"><?php echo TXT_NOTE_INDEXATION2 ?></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('categories', '<?php echo TXT_CATEGORIE ?>')" value="<?php echo TXT_INDEXER_CATEGORIES ?>" /></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('collections', '<?php echo TXT_COLLECTION ?>')" value="<?php echo TXT_INDEXER_COLLECTIONS ?>" /></p>
											<p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('items', '<?php echo TXT_ITEM ?>')" value="<?php echo TXT_INDEXER_ITEMS ?>" /></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('langues', '<?php echo TXT_LANGUE ?>')" value="<?php echo TXT_INDEXER_LANGUES ?>" /></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('medias', '<?php echo TXT_MEDIA ?>')" value="<?php echo TXT_INDEXER_MEDIAS ?>" /></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('projets', '<?php echo TXT_PROJET ?>')" value="<?php echo TXT_INDEXER_PROJETS ?>" /></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('questionnaires', '<?php echo TXT_QUESTIONNAIRE ?>')" value="<?php echo TXT_INDEXER_QUESTIONNAIRES ?>" /></p>
                                            <p class="padBot0"><input class="btnSubmit w175" name="btnSubmit" id="btnSubmit2" type="button" onClick="indexation('utilisateurs', '<?php echo TXT_UTILISATEUR ?>')" value="<?php echo TXT_INDEXER_UTILISATEURS ?>" /></p>

										</div>						
									</div>
															
									<div class="detailBot"><div>&nbsp;</div></div>
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
