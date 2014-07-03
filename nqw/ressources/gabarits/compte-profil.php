<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_COMPTE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
</head>
<body id="bCProfil" onload="resizePanels();">

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
						
							    <form action="compte.php" method="post">
	                            <input type="hidden" name="demande" value="compte_profil_sauvegarder" />
							
								<div class="filAriane">
									<h2><img src="../images/ic-utilisateur.png" alt="<?php echo TXT_MON_PROFIL ?>" /><?php echo TXT_MON_PROFIL ?></h2>
								</div>
								
	                            <div class="detail">
	                                <div class="detailTop"><div>
	                                    <input class="btnReset annuler" name="btnReset" id="btnReset1" type="button" value="<?php echo TXT_ANNULER ?>"  />
	                                    <input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
	                                </div>
	                                
	                                <div id="section1" class="detailContenant">
	                                    <div class="detailContenu">
	                                    
	                                        <!--  Messages -->
	                                        <?php include '../ressources/includes/message_liste.php' ?>
	                                        <!--  /Messages -->
	                                       
	                                        <p class="txtUpCase gras"><?php echo TXT_INFORMATION?></p>
	                                        
	                                        <p><label class="inline"><?php echo TXT_NOM_UTILISATEUR?></label><br />
		                                        <?php echo $usager->get("code_usager") ?></p>
	                                    
	                                        <p><label for="usager_prenom"><?php echo TXT_PRENOM ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
	                                        <input type="text" id="usager_prenom" name="usager_prenom" class="suiviModif" size="50" maxlength="150"  value="<?php echo $usager->get("prenom") ?>"/></p>
	                            
	                                        <p><label for="usager_nom"><?php echo TXT_NOM ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
	                                        <input type="text" id="usager_nom" name="usager_nom" class="suiviModif" size="50" maxlength="150" value="<?php echo $usager->get("nom") ?>"/></p>
	                                        
	                                        <p><label for="usager_courriel"><?php echo TXT_COURRIEL ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>"> *</abbr></label>
	                                        <input type="text" id="usager_courriel" name="usager_courriel" class="suiviModif" size="50" maxlength="150"   value="<?php echo $usager->get("courriel") ?>" /></p>
	                                       
	                                        <hr class="t25b25" />
	                                        <p class="txtUpCase gras"><?php echo TXT_PREFERENCES?></p>
	                                        <p><label for="usager_langue"><?php echo TXT_LANGUE_INTERFACE ?></label>
												<select class="w250 suiviModif" id="usager_langue_interface" name="usager_langue_interface">
	
													<?php foreach ($listeLanguesInterface as $codeLangue => $titreLangue) { ?>
							                    	
							                    		<option value="<?php echo $codeLangue ?>" <?php if ($usager->get("langue_interface") == $codeLangue) { echo "selected"; }?>><?php echo $titreLangue ?></option>
							                    		
							                        <?php } ?>											
												
												</select>
																						
	                                        </p>
	                                        <p><label for="usager_pref_projet"><?php echo TXT_PROJET_COURANT_A_LA_CONNEXION ?></label>
	                                        
												<select class="w250 suiviModif" id="usager_pref_projet" name="usager_pref_projet">
													
							                    	<?php foreach ($listeProjetsActifs as $proj) { ?>
							
							                    		<option value="<?php echo $proj->get("id_projet") ?>" <?php if ($usager->get("pref_projet") == $proj->get("id_projet") ) { echo "selected"; }?>><?php echo $proj->get("titre") . " (" .  TXT_PREFIX_PROJET . $proj->get("id_projet") . ")" ?></option>
							                        
							                        <?php } ?>
													
												</select>										
	
											</p>
											
	                                        <p><label for="usager_pref_apercu_theme"><?php echo TXT_THEME_DEFAUT ?></label>
	                                        
												<select class="w250 suiviModif" id="usager_pref_apercu_theme" name="usager_pref_apercu_theme">
					
													<?php foreach ($listeThemes as $theme) { ?>
							                    	
							                    		<option value="<?php echo $theme ?>" <?php if ($usager->get("pref_apercu_theme") == $theme) { echo "selected"; }?>><?php echo $theme ?></option>
							                    		
							                        <?php } ?>											
												
												</select>										
	
											</p>
											
											<p><label for="usager_pref_apercu_langue"><?php echo TXT_LANGUE_DEFAUT ?></label>
	                                        
												<select class="w250 suiviModif" id="usager_pref_apercu_langue" name="usager_pref_apercu_langue">
					
													<?php foreach ($listeLanguesPublication as $codeLangue => $titreLangue) { ?>
							                    	
							                    		<option value="<?php echo $codeLangue ?>" <?php if ($usager->get("pref_apercu_langue") == $codeLangue) { echo "selected"; }?>><?php echo $titreLangue ?></option>
							                    		
							                        <?php } ?>											
												
												</select>										
	
											</p>
	
											
											
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

	<!--  Lien pour fenêtre jaillissante servant à l'ajout d'items dans un questionnaire -->
	<a class="fenetreSelQuest" href="questionnaires.php?demande=questionnaire_selectionner"></a>
	
</body>
</html>
