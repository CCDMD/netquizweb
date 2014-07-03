<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_COMPTE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
</head>

<body id="bCMdp" onload="resizePanels();">

	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-compte.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-rech-compte.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<form name="frmCompte" id="frmCompte" action="compte.php" method="post">
							  	<input type="hidden" name="demande" value="compte_mdp_sauvegarder" />
	
							  	<div class="filAriane">
									<h2><img src="../images/ic-mot-de-passe.png" alt="<?php echo TXT_MON_MOT_DE_PASSE ?>" /><?php echo TXT_MON_MOT_DE_PASSE ?></h2>
								</div>
								
	                            <div class="detail">
	                                <div class="detailTop"><div>
	                                    <input class="btnReset annuler" name="btnReset" id="btnReset1" type="button" value="<?php echo TXT_ANNULER ?>"  />
	                                    <input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
	                                </div>
	                                
	                                <div id="section1" class="detailContenant">
	                                    <div class="detailContenu">
	                                    
	                   
											<!--  Messages -->
											<div class="zoneMsgListe">
											<?php if (isset($messages)) { 
											
												 if ($messages->getTypeMessage() == Messages::CONFIRMATION) { ?>
													<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1">
														<ul> 
															<?php echo $messages->getMessages() ?>
														</ul>
													</div>
												<?php } else { ?>
													<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1">
													    
														<ul> 
															<?php echo $messages->getMessages() ?>
														</ul>
													</div>
												<?php }?>
												
											<?php } ?>
											</div>
											<!--  /Messages -->
	                                       
	                                        <p><label for="usager_mdp_actuel"><?php echo TXT_MOT_PASSE_ACTUEL ?><abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
	                                        <input type="password" id="usager_mdp_actuel" class="suiviModif" name="usager_mdp_actuel" size="50" maxlength="150" autocomplete="off"/></p>
	                                
	                                        <p><label for="usager_mdp_nouv"><?php echo TXT_NOUVEAU_MOT_PASSE ?><abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr> <span class="aide"><?php echo TXT_MDP_INSTRUCTIONS ?></span></label>
	                                        <input type="password" id="usager_mdp_nouv" class="suiviModif" name="usager_mdp_nouv" size="50" maxlength="150" /></p>
	                                
	                                        <p><label for="usager_mdp_conf"><?php echo TXT_NOUVEAU_MOT_PASSE_CONF ?><abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
	                                        <input type="password" id="usager_mdp_conf" class="suiviModif" name="usager_mdp_conf" size="50" maxlength="150" /></p>
	
	                                    </div>						
	                                </div>
	                                                        
	                                <div class="detailBot">
	                                	<div>
	                                    <input class="btnReset annuler" name="btnReset" id="btnReset2" type="reset" value="<?php echo TXT_ANNULER ?>"  />
	                                    <input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="submit" value="<?php echo TXT_ENREGISTRER?>" />
	                                	</div>
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
