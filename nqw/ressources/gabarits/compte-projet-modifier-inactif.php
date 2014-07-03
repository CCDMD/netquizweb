<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_COMPTE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
   	<script type="text/javascript">
	function changerPage(page) {

		// Verifier si on peut changer la page
		pageCour = "<?php echo $projet->get("page_courante") ?>";

		if (page != pageCour) {
			urlDest = "compte.php?demande=projet_modifier&projet_page=" + page;
			document.location=urlDest;
		}
	}
	
	</script>    
    
</head>

<body id="bCProjets" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-compte.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-compte-projets.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
							
								<div class="filAriane flGa"><h2><img src="../images/ic-projet.png" alt="<?php echo TXT_MES_PROJETS ?>" /><a href="compte.php?demande=projets_liste"><?php echo TXT_MES_PROJETS ?></a><span class="sep">&gt;</span> <?php echo $projet->get("titre") ?> <span class="id">(<?php echo TXT_PREFIX_PROJET . $projet->get("id_projet")?>)</span></h2></div>
	                            <div class="tools menuContexteGa projetTools flDr padTo15">
	                                <img src="../images/ic-tools.png" alt="" />
	                                <?php include '../ressources/includes/menu-contexte-compte-projet-modifier.php' ?>
	                            </div>
								
								<form id="frm" name="frm" action="compte.php" method="post">
								<input type="hidden" name="demande" value="projet_modifier_sauvegarder" />
								<input type="hidden" name="projet_id_projet" value="<?php echo $projet->get("id_projet") ?>" />
								<input type="hidden" name="projet_collaborateur" value="" />
								
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
	                                                    
	                                                <?php } ?>
												</div>
												<!--  /Messages -->
												
                                           		<p class="txtUpCase gras"><?php echo TXT_INFORMATION ?></p>
	                                            <p><label for="projet_titre"><?php echo TXT_TITRE_DU_PROJET?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
													<?php echo $projet->get("titre") ?></p>
												
												<p><label for="projet_description"><?php echo TXT_DESCRIPTION ?></label>
													<?php echo $projet->get("description") ?></p>
												
												<p><label for="projet_repertoire"><?php echo TXT_IDENTIFIANT_UNIQUE_DU_PROJET ?></label>
													<?php echo $projet->get("repertoire") ?></p>
													
												<p><label for="projet_repertoire"><?php echo TXT_STATUT_DU_PROJET ?></label>
													<input type="radio" name="projet_statut" value="1" <?php if ($projet->get("statut") == "1") { echo "checked"; }?> />&nbsp;<?php echo TXT_ACTIF ?>
													<input class="padGa20" type="radio" name="projet_statut" value="0" <?php if ($projet->get("statut") == "0") { echo "checked"; }?>/>&nbsp;<?php echo TXT_INACTIF ?>
												</p>												
																								
												<p><label for="projetResponsable"><?php echo TXT_RESPONSABLE_DU_PROJET?></label>
													<?php echo $projet->getResponsableProjetAvecCourriel($idProjet)?></p>
													
                                                <hr class="t25b25" />
                                                
                                                <p class="txtUpCase gras"><?php echo TXT_COLLABORATION ?></p>
												<p class="padBot0"><label for="projetCollaborateurs"><?php echo TXT_COLLABORATEURS_ACTUELS_DE_CE_PROJET?></label></p>
												
	                                            <table class="tblListe tblListeCollaborateurs">
	                                                <tr>
	                                                    <th class="cCode"><?php echo TXT_CODE ?></th>
	                                                    <th class="w40pc"><?php echo TXT_NOM . ', ' . TXT_PRENOM ?></th>
	                                                    <th class="last"><?php echo TXT_COURRIEL ?></th>
	                                                </tr>
	            
	                                                <?php
	                                                    $idx = 0; 
	                                                    foreach ($listeCollaborateursActifs as $collaborateur) { 
	                                                      $idx++;?> 
	                                                
	                                                    <input type="hidden" id="courriel_<?php echo $collaborateur['id_usager'] ?>" value="<?php echo $collaborateur['courriel'] ?>" />
	                                                    <tr>
	                                                        <td><?php echo TXT_PREFIX_USAGER . $collaborateur['id_usager'] ?></td>
	                                                        <td><?php echo $collaborateur['nom'] . ", " . $collaborateur['prenom'] ?></td>
	                                                        <td class="last"><?php echo $collaborateur['courriel'] ?></td>
	                                                    </tr>
	                                                
	                                                <?php } ?>
	                                                
	                                            </table>											
								                                                
												<p class="padBot0"><label for="projetCollaborateurs"><?php echo TXT_COLLABORATEURS_INVITES_A_CE_PROJET?></label></p>
												
	                                            <table class="tblListe tblListeCollaborateurs">
	                                                <tr>
	                                                    <th class="w44pc"><?php echo TXT_COURRIEL ?></th>
	                                                    <th class="last"><?php echo TXT_DATE_ENVOI_INVITATION ?></th>
	                                                </tr>
	            
	                                                <?php
	                                                    $idx = 0; 
	                                                    foreach ($listeCollaborateursInvites as $collaborateur) { 
	                                                        $idx++; ?> 
	                                                
	                                                        <tr>
	                                                            <td><?php echo $collaborateur['collaborateur_courriel'] ?></td>
	                                                            <td class="last"><?php echo $collaborateur['date_creation'] ?></td>
	                                                        </tr>
	                                                
	                                                <?php } ?>
	
	                                            </table>					
												
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
