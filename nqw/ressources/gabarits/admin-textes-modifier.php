<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB_ADMIN ?> - <?php echo TXT_ADMINISTRATION ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include '../ressources/includes/librairies-avec-editeur.php' ?>
	
    <script type="text/javascript">
	function changerLangue() {
		
		document.frm.demande.value="textes_modifier";
		document.frm.submit();
	}
	</script>
	
</head>

<body id="bATextes" onload="resizePanels();">
	
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
							<div class="filAriane flGa"><h2><img src="../images/ic-textes.png" alt="<?php echo TXT_TEXTES ?>" /><?php echo TXT_TEXTES ?></h2></div>
                            
							<form id="frm" name="frm" action="admin.php" method="post">
							
								<input type="hidden" name="demande" value="textes_sauvegarder" >
								<input type="hidden" name="verrou_id_projet" value="0" />
								<input type="hidden" name="verrou_id_element1" value="ADMIN_TEXTES" />
								<input type="hidden" name="verrou_id_element2" value="" />
							
								<div class="detail">
                                    <div class="detailTop"><div>
										<input class="btnReset annuler" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" />
                                    </div></div>
									
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
														$('#message1').hide(); // pour IE7 et IE8, apr�s le fadeout du texte, on �teint la boite
													  });
														
													}, 21000);
												</script> 
												
											<?php } ?>
                                            </div>
											<!--  /Messages -->

	                                        <p><label for="langue"><?php echo TXT_LANGUE ?></label>
												<select class="w250" id="texte_langue" name="texte_langue" onchange="changerLangue()">
							                    		
													<?php foreach ($listeLanguesInterface as $codeLangue => $titreLangue) { ?>
											
							                    		<option value="<?php echo $codeLangue ?>" <?php if ($langTexteSel == $codeLangue) { echo "selected"; }?>><?php echo $titreLangue ?></option>
													
													<?php } ?>
														
												</select>
	                                        </p>

											<p><label for="texte_message_avertissement"><?php echo TXT_MESSAGE_AVERTISSEMENT_TEMPORAIRE ?></label>
												<textarea class="wmax editeur suiviModif" id="texte_message_temporaire" name="texte_message_avertissement" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_MESSAGE_AVERTISSEMENT_TEMPORAIRE ?>"><?php echo $texte->get("message_avertissement") ?></textarea></p>

											<p><label for="texte_message_bienvenue"><?php echo TXT_TEXTE_SUPPLEMENTAIRE_MOT_BIENVENUE ?></label>
												<textarea class="wmax editeur suiviModif" id="texte_message_bienvenue" name="texte_message_bienvenue" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_TEXTE_SUPPLEMENTAIRE_MOT_BIENVENUE ?>"><?php echo $texte->get("message_bienvenue") ?></textarea></p>


										</div>						
									</div>
															
									<div class="detailBot"><div>
										<input class="btnReset annuler" name="btnReset" id="btnReset2" type="reset" value="<?php echo TXT_ANNULER ?>"  />
										<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="submit" value="<?php echo TXT_ENREGISTRER ?>" />
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
