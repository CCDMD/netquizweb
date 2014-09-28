<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB_ADMIN ?> - <?php echo TXT_ADMINISTRATION ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">
	
		function changerPage(page) {
	
			// Verifier si on peut changer la page
			pageCour = "<?php echo $usr->get("page_courante") ?>";
	
			if (page != pageCour) {
				urlDest = "admin.php?demande=utilisateur_modifier&usager_page=" + page;
				document.location=urlDest;
			}
		}

	</script>	
	
	
</head>

<body id="bAUtilisateurs" onload="resizePanels();">
	
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-admin.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-admin-utilisateurs.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
							<div class="filAriane flGa"><h2><img src="../images/ic-utilisateur.png" alt="<?php echo TXT_TOUS_LES_UTILISATEURS ?>" /><a href="admin.php?demande=utilisateurs_liste"><?php echo TXT_TOUS_LES_UTILISATEURS ?></a><span class="sep">&gt;</span> 
							<?php echo $usr->getNomPrenom() ?> <span class="id">(<?php echo TXT_PREFIX_USAGER . $usr->get("id_usager") ?>)</span></h2></div>
                            <div class="tools menuContexteGa flDr padTo15">
                                <img src="../images/ic-tools.png" alt="" />
                                <?php include '../ressources/includes/menu-contexte-admin-utilisateur-modifier.php' ?>
                            </div>
							
							<form id="frm" name="frm" action="admin.php" method="post">
							<input type="hidden" name="demande" value="utilisateur_modifier_sauvegarder" />
							<input type="hidden" name="admin" value="1" />
							<input type="hidden" name="usager_id_usager" value="<?php echo $usr->get("id_usager") ?>" />
							<input type="hidden" name="flagModifications" value="" />
							<input type="hidden" name="mode" value="<?php echo $pageInfos['mode']?>" />
							<input type="hidden" name="verrou_id_projet" value="0" />
							<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_USAGER . $usr->get("id_usager") ?>" />
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

                                            
                                            <p class="txtUpCase gras"><?php echo TXT_INFORMATION ?></p>
											<?php if ($pageInfos['mode'] == "ajout" ) { ?>
	                                        	<p><label for="usager_code"><?php echo TXT_NOM_UTILISATEUR ?><span class="aide"><?php echo TXT_CODE_USAGER_INSTRUCTIONS ?></span></label>
	                                        	<input type="text" id="usager_code_usager" name="usager_code_usager"  class="suiviModif" size="50" maxlength="150"  value="<?php echo $usr->get("code_usager") ?>"/></p>
	                                        <?php } else { ?>
	                                        	<p><label for="usager_code"><?php echo TXT_NOM_UTILISATEUR ?></label>
	                                        	<?php echo $usr->get("code_usager") ?>
	                                        <?php } ?>
                                            
	                                        <p><label for="usager_code"><?php echo TXT_DATE_DE_CREATION ?></label>
	                                        <?php echo $usr->get("date_creation") ?></p>
	                                        
	                                        <p><label for="usager_code"><?php echo TXT_DERNIER_ACCES ?></label>
	                                        <?php echo $usr->get("date_dern_authentification") ?></p>
                                            
	                                        <p><label for="usager_courriel"><?php echo TXT_COURRIEL ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>"> *</abbr></label>
	                                        <input type="text" id="usager_courriel" name="usager_courriel" class="suiviModif" size="50" maxlength="150"   value="<?php echo $usr->get("courriel") ?>" /></p>
											
	                                        <p><label for="usager_prenom"><?php echo TXT_PRENOM ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
	                                        <input type="text" id="usager_prenom" name="usager_prenom"  class="suiviModif" size="50" maxlength="150"  value="<?php echo $usr->get("prenom") ?>"/></p>

															
	                                        <p><label for="usager_nom"><?php echo TXT_NOM ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
	                                        <input type="text" id="usager_nom" name="usager_nom" class="suiviModif" size="50" maxlength="150" value="<?php echo $usr->get("nom") ?>"/></p>
	                                        
											<?php if ($pageInfos['mode'] == "ajout" ) { ?>
											
		                                        <p><label for="usager_mdp"><?php echo TXT_MOT_DE_PASSE ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr><span class="aide"><?php echo TXT_MDP_INSTRUCTIONS ?></span></label>
		                                        <input type="password" id="usager_mdp" name="usager_mdp_nouv" size="50" maxlength="150" autocomplete="off"/></p>
														
		                                        <p><label for="usager_mdp_conf"><?php echo TXT_CONFIRMER_LE_MOT_DE_PASSE ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
		                                        <input type="password" id="usager_mdp_conf" name="usager_mdp_conf" size="50" maxlength="150"  autocomplete="off" /></p>
											
											<?php } else { ?>
											
		                                        <p><label for="usager_mdp"><?php echo TXT_MOT_DE_PASSE ?><span class="aide"><?php echo TXT_MDP_INSTRUCTIONS ?></span></label>
		                                        <input type="password" id="usager_mdp" name="usager_mdp_nouv" class="suiviModif" placeholder="<?php echo TXT_INSCRIRE_NOUVEAU_MOT_PASSE_SI_DESIRE ?>" size="50" maxlength="150" autocomplete="off"/></p>
														
		                                        <p><label for="usager_mdp_conf"><?php echo TXT_CONFIRMER_LE_MOT_DE_PASSE ?></label>
		                                        <input type="password" id="usager_mdp_conf" name="usager_mdp_conf" class="suiviModif" placeholder="<?php echo TXT_CONFIRMER_LE_MOT_DE_PASSE ?>" size="50" maxlength="150" autocomplete="off" /></p>
											
											<?php } ?>
										
											<p><label for="utilisateur"><?php echo TXT_ROLE ?></label>
												<input type="radio" name="usager_role" value="0" class="suiviModif" <?php if ($usr->get("role") == 0) { echo "checked"; } ?>/>&nbsp;<?php echo TXT_UTILISATEUR ?>
												<input class="padGa20 suiviModif" type="radio" name="usager_role" value="1" <?php if ($usr->get("role") == 1) { echo "checked"; } ?>/>&nbsp;<?php echo TXT_ADMINISTRATEUR ?>
											</p>											
											
											<p><label for="utilisateur"><?php echo TXT_STATUT ?></label>
												<input class="suiviModif" type="radio" name="usager_statut" value="0" <?php if ($usr->get("statut") == 0) { echo "checked"; } ?>/>&nbsp;<?php echo USAGER_STATUT_0 ?>
												<input class="padGa20 suiviModif" type="radio" name="usager_statut" value="1"  <?php if ($usr->get("statut") == 1) { echo "checked"; } ?>/>&nbsp;<?php echo USAGER_STATUT_1 ?>
												<input class="padGa20 suiviModif" type="radio" name="usager_statut" value="2"  <?php if ($usr->get("statut") == 2) { echo "checked"; } ?>/>&nbsp;<?php echo USAGER_STATUT_2 ?>
												<input class="padGa20 suiviModif" type="radio" name="usager_statut" value="3"  <?php if ($usr->get("statut") == 3) { echo "checked"; } ?>/>&nbsp;<?php echo USAGER_STATUT_3 ?>
												<input class="padGa20 suiviModif" type="radio" name="usager_statut" value="4"  <?php if ($usr->get("statut") == 4) { echo "checked"; } else { echo "disabled"; } ?>/>&nbsp;<?php echo USAGER_STATUT_4 ?>
											</p>
											
											<?php if ($pageInfos['mode'] != "ajout" ) { ?>
												<p><label for="utilisateur"><?php echo TXT_NB_MAUVAIS_ESSAIS_MDP ?><span class="aide">(<?php echo TXT_COMPTE_VERROUILLE . " " . SECURITE_NB_MAUVAIS_ESSAIS_VERROUILLAGE . " " . TXT_ESSAIS . ")" ?></span></label>
													<input type="text" id="usager_nb_mauvais_essais" name="usager_nb_mauvais_essais" class="suiviModif" size="10" maxlength="5" value="<?php echo $usr->get("nb_mauvais_essais") ?>"/></p>
												</p>
											
	                                        	<p class="padBot0"><label><?php echo TXT_LISTE_PROJETS_DE_CET_UTILISATEUR ?></label></p>
												
	                                            <table class="tblListe tblListeUserProjets">
														<tr>
															<th class="cCode"><?php echo TXT_CODE ?></th>
		                                                    <th><?php echo TXT_TITRE_DU_PROJET ?></th>
		                                                    <th><?php echo TXT_RESPONSABLE ?></th>
		                                                    <th><?php echo TXT_STATUT ?></th>
	    	                                                <th class="last"><?php echo TXT_DERNIER_ACCES ?></th>
														</tr>
														
														
														<?php foreach ($listeProjetsActifsUtilisateur as $proj) { ?>
														
															<tr>
																<td class="cCode"><?php echo TXT_PREFIX_PROJET . $proj->get("id_projet") ?></th>
			                                                    <td><?php echo $proj->get("titre") ?></th>
			                                                    <td><?php echo $proj->getResponsableProjet() ?></th>
			                                                    <td><?php echo $proj->getStatutTxt() ?></th>
		    	                                                <td class="last"><?php echo $proj->get('date_modification') ?></th>
															</tr>
															
														<?php } ?>
														
													</table>					
												

											<?php } ?>												

                                            <hr class="t25b25" />
											
                                            <p class="txtUpCase gras"><?php echo TXT_PREFERENCES ?></p>
	                                        <p><label for="usager_langue"><?php echo TXT_LANGUE_INTERFACE ?></label>
												<select class="w250 suiviModif" id="usager_langue_interface" name="usager_langue_interface">
	
													<?php foreach ($listeLanguesInterface as $codeLangue => $titreLangue) { ?>
							                    	
							                    		<option value="<?php echo $codeLangue ?>" <?php if ($usr->get("langue_interface") == $codeLangue) { echo "selected"; }?>><?php echo $titreLangue ?></option>
							                    		
							                        <?php } ?>											
												
												</select>
																						
	                                        </p>
	                                        
											<?php if ($pageInfos['mode'] != "ajout" ) { ?>	                                        
		                                        <p><label for="usager_pref_projet"><?php echo TXT_PROJET_COURANT_A_LA_CONNEXION ?></label>
		                                        
													<select class="w250 suiviModif" id="usager_pref_projet" name="usager_pref_projet">
														
														<option value="">Aucun</option>
														
								                    	<?php foreach ($listeProjetsActifsUtilisateur as $proj) { ?>
								
															
								                    		<option value="<?php echo $proj->get("id_projet") ?>" <?php if ($usr->get("pref_projet") == $proj->get("id_projet") ) { echo "selected"; }?>><?php echo $proj->get("titre") . " (" .  TXT_PREFIX_PROJET . $proj->get("id_projet") . ")" ?></option>
								                        
								                        <?php } ?>
														
													</select>										
		
												</p>
											<?php } ?>
												
	                                        <p><label for="usager_pref_apercu_theme"><?php echo TXT_THEME_DEFAUT ?></label>
	                                        
												<select class="w250 suiviModif" id="usager_pref_apercu_theme" name="usager_pref_apercu_theme">
					
													<?php foreach ($listeThemes as $theme) { ?>
							                    	
							                    		<option value="<?php echo $theme ?>" <?php if ($usr->get("pref_apercu_theme") == $theme) { echo "selected"; }?>><?php echo $theme ?></option>
							                    		
							                        <?php } ?>											
												
												</select>										
	
											</p>
											
											<p><label for="usager_pref_apercu_langue"><?php echo TXT_LANGUE_DEFAUT ?></label>
	                                        
												<select class="w250 suiviModif" id="usager_pref_apercu_langue" name="usager_pref_apercu_langue">
					
													<?php foreach ($listeLanguesPublication as $codeLangue => $titreLangue) { ?>
							                    	
							                    		<option value="<?php echo $codeLangue ?>" <?php if ($usr->get("pref_apercu_langue") == $codeLangue) { echo "selected"; }?>><?php echo $titreLangue ?></option>
							                    		
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
</body>
</html>
