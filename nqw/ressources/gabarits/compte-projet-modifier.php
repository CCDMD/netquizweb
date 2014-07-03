<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_COMPTE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>	
	
	<script type="text/javascript">
	function envoiInvitation() {
		document.frm.demande.value="projet_modifier_ajouter_collaborateur";

		if (flagModifications) {
			document.frm.flagModifications.value = "1";
		} else {
			document.frm.flagModifications.value = "0";
		}

		flagModifications = false;
		
		document.frm.submit();
	}

	function verifierSelectionActuels() {
		
		// Vérifier si un des checkbox est sélectionné			
		var nbSel = $('input:checkbox:checked.selectionElementActuels').map(function () { 
			  return this.value; 
		}).size();

		// Si aucune sélection, désactiver tous les choix
		if (nbSel == 0) {
			$("#menuActuelsCourriel").addClass("inactif");
			$("#menuActuelsResponsable").addClass("inactif");
			$("#menuActuelsSupprimer").addClass("inactif");
		} else if (nbSel == 1) {
			$("#menuActuelsCourriel").removeClass("inactif");
			$("#menuActuelsSupprimer").removeClass("inactif");
			$("#menuActuelsResponsable").removeClass("inactif");
		} else if (nbSel >= 1) {
			// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
			$("#menuActuelsCourriel").removeClass("inactif");
			$("#menuActuelsResponsable").addClass("inactif");
			$("#menuActuelsSupprimer").removeClass("inactif");
		}
	}		
	
	function verifierSelectionInvites() {
		
		// Vérifier si un des checkbox est sélectionné			
		var nbSel = $('input:checkbox:checked.selectionElementInvites').map(function () { 
			  return this.value; 
		}).size();

		// Si aucune sélection, désactiver tous les choix
		if (nbSel == 0) {
			$("#menuInvitesCourriel").addClass("inactif");
			$("#menuInvitesSupprimer").addClass("inactif");
		}
		
		// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
		if (nbSel >= 1) {
			$("#menuInvitesCourriel").removeClass("inactif");
			$("#menuInvitesSupprimer").removeClass("inactif");
		}
	}		

	function envoiCourrielCollaborateursActuels(element) {
		
		// Obtenir la liste des courriels sélectionnés			
		listeCourriels = "";
		separateur = "";
	
		// Déterminer le séparateur 
		var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
		if (isMacLike) {
			separateur = ", ";
		} else {
			separateur = "; ";
		}
		
		$('input:checkbox:checked.selectionElementActuels').each(function() {
	
			   cle = '#courriel_' + this.value;
			   courriel = $(cle).val();
			
			   if (listeCourriels == "") {
				   listeCourriels = courriel
			   } else {
				   listeCourriels += separateur +  courriel;
			   }
		});

		flagModifications = false;
	
		document.location = "mailto:" + listeCourriels;
	}

	
	function envoiCourrielCollaborateursInvites(element) {

		// Désactiver le clic si la classe "inactif" est présente sur le li parent
		if ($(element).parent().hasClass("inactif")) {
			return false;
		}		

		// Obtenir la liste des courriels sélectionnés			
		listeCourriels = "";
		separateur = "";

		// Déterminer le séparateur 
		var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
		if (isMacLike) {
			separateur = ", ";
		} else {
			separateur = "; ";
		}
		
		$('input:checkbox:checked.selectionElementInvites').each(function() {
			   if (listeCourriels == "") {
				   listeCourriels = this.value;
			   } else {
				   listeCourriels += separateur +  this.value;
			   }
		});

		document.location = "mailto:" + listeCourriels;
		
	}


	function remplacerResponsable(element) {

		// Désactiver le clic si la classe "inactif" est présente sur le li parent
		if ($(element).parent().hasClass("inactif")) {
			return false;
		}		

		// Vérifier si certains champs ont été modifiés, si oui, forcer l'enregistrement
		if (flagModifications) {
			alert("<?php echo MSG_029 ?>");
		} else {
			// Aucune modification en attente, on peut soumettre 
			if (confirm('<?php echo TXT_NOMMER_RESPONSABLE_AVERTISSEMENT ?>')) {
				soumettreDemande('collaborateur_remplacer_responsable');
			}
		}
	}

	function retirerCollaborateursActuels(element) {

		// Désactiver le clic si la classe "inactif" est présente sur le li parent
		if ($(element).parent().hasClass("inactif")) {
			return false;
		}	

		soumettreDemande('collaborateur_supprimer_acces');
	}

	function supprimerInvitationCollaborateur(element) {

		// Désactiver le clic si la classe "inactif" est présente sur le li parent
		if ($(element).parent().hasClass("inactif")) {
			return false;
		}	

		flagModifications = false;
		
		soumettreDemande('collaborateur_supprimer_invitation');
	}

	function changerPage(page) {

		// Verifier si on peut changer la page
		pageCour = "<?php echo $projet->get("page_courante") ?>";

		if (page != pageCour) {
			urlDest = "compte.php?demande=projet_modifier&projet_page=" + page;
			document.location=urlDest;
		}
	}		
	
	// Au chargement de la page
	$(document).ready(function () {

		// Activer le clic sur les boîtes de sélection des collaborateurs actuels 
		$(".selectionElementActuels").click(function() {
			verifierSelectionActuels();
		});

		// Activer le clic sur les boîtes de sélection des collaborateurs invités 
		$(".selectionElementInvites").click(function() {
			verifierSelectionInvites();
		});
		
		
		// Activer la sélection/déselection de tous les checkboxes pour les collaborateurs actuels
	    $('#selectAllActuels').change(function () { 
		    if ($(this).attr("checked")) { 
	            $('.selectionElementActuels').prop('checked', true);
	        } else {
	            $('.selectionElementActuels').prop('checked', false);
	        }
		    verifierSelectionActuels();
	    });

		// Activer la sélection/déselection de tous les checkboxes pour les collaborateurs invités
	    $('#selectAllInvites').change(function () { 
		    if ($(this).attr("checked")) { 
	            $('.selectionElementInvites').prop('checked', true);
	        } else {
	            $('.selectionElementInvites').prop('checked', false);
	        }
		    verifierSelectionInvites();
	    });

		// Activer le flag des modifications au besoin
		activerSuiviModifications('<?php echo $pageInfos["flagModifications"] ?>');
	    
	});

	
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
							<input type="hidden" name="flagModifications" value="" />
							<input type="hidden" name="verrou_id_projet" value="0" />
							<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_PROJET . $projet->get("id_projet") ?>" />
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
													$('#message1').hide(); // pour IE7 et IE8, après le fadeout du texte, on éteint la boite
												  });
													
												}, 21000);
											</script> 
												
											<?php } ?>
                                            </div>
											<!--  /Messages -->

                                            <p class="txtUpCase gras"><?php echo TXT_INFORMATION ?></p>
											<p><label for="projet_titre"><?php echo TXT_TITRE_DU_PROJET?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
												<input class="wmax suiviModif" type="text" id="projet_titre" name="projet_titre" value="<?php echo $projet->get("titre") ?>" /></p>

											<p><label for="projet_description"><?php echo TXT_DESCRIPTION ?></label>
												<textarea class="wmax editeur suiviModif" id="projet_description" name="projet_description" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_UNE_DESCRIPTION ?>"><?php echo $projet->get("description") ?></textarea></p>

											<?php if ($projet->get("repertoire") == "" || $projet->get("erreur_repertoire") == "1") { ?>
											
												<p><label for="projet_repertoire"><?php echo TXT_IDENTIFIANT_UNIQUE_DU_PROJET ?>  <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr> <span class="aide">(<?php echo TXT_IDENTIFIANT_UNIQUE_DU_PROJET_CONTRAINTES ?>)</span></label>
												<input class="wmax suiviModif" type="text" id="projet_repertoire" name="projet_repertoire" value="<?php echo $projet->get("repertoire") ?>"  placeholder="<?php echo TXT_INSCRIRE_UN_IDENTIFIANT_UNIQUE ?>" /></p>
											
											<?php } else { ?>
											
												<p><label for="projet_repertoire"><?php echo TXT_IDENTIFIANT_UNIQUE_DU_PROJET ?></label>
													<?php echo $projet->get("repertoire") ?></p>
													<input type="hidden" name="projet_repertoire" value="<?php echo $projet->get("repertoire") ?>" />
													
											<?php } ?>


											<p><label for="projet_statut"><?php echo TXT_STATUT_DU_PROJET ?></label>
												<input type="radio" name="projet_statut" value="1" <?php if ($projet->get("statut") == "1") { echo "checked"; }?> />&nbsp;<?php echo TXT_ACTIF ?>
												<input class="padGa20" type="radio" name="projet_statut" value="0" <?php if ($projet->get("statut") == "0") { echo "checked"; }?>/>&nbsp;<?php echo TXT_INACTIF ?>
											</p>											

											<p><label for="projetResponsable"><?php echo TXT_RESPONSABLE_DU_PROJET?></label>
												<?php echo $projet->getResponsableProjetAvecCourriel($idProjet)?></p>

											<hr class="t25b25" />
                                            
                                            <p class="txtUpCase gras"><?php echo TXT_COLLABORATION ?></p>
											<p class="padBot0"><label for="projetCollaborateurs"><?php echo TXT_COLLABORATEURS_ACTUELS_DE_CE_PROJET?></label></p>
											
                                            <table class="tblListe tblListeCollaborateurs">
													<tr class="tblNav">
														<td class="alCe">
															<div class="menuContexte">
																<img src="../images/ic-tools.png" alt="" />
																<?php include '../ressources/includes/menu-contexte-compte-collaborateurs-actuels.php' ?>
															</div>
														</td>
                                                        <td colspan="3"></td>
													</tr>
													<tr>
														<th class="cCheck"><input class="noBord" id="selectAllActuels" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelectionActuels()" /></th>
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
															<td class="cCheck"><input class="noBord selectionElementActuels" type="checkbox" name="collaborateurs_actuels_selection_<?php echo $idx ?>" value="<?php echo $collaborateur['id_usager'] ?>" /></td>
															<td><?php echo TXT_PREFIX_USAGER . $collaborateur['id_usager'] ?></td>
															<td><?php echo $collaborateur['nom'] . ", " . $collaborateur['prenom'] ?></td>
                                                        <td class="last"><?php echo $collaborateur['courriel'] ?></td>
														</tr>
													
													<?php } ?>
												</table>											
										
                                        	<p class="padBot0"><label for="projetCollaborateurs"><?php echo TXT_COLLABORATEURS_INVITES_A_CE_PROJET?></label></p>
											
                                            <table class="tblListe tblListeCollaborateurs">
													<tr class="tblNav">
														<td class="alCe">
															<div class="menuContexte">
																<img src="../images/ic-tools.png" alt="" />
																<?php include '../ressources/includes/menu-contexte-compte-collaborateurs-invites.php' ?>
															</div>
														</td>
                                                    <td colspan="2"></td>
													</tr>
													<tr>
														<th class="cCheck"><input class="noBord" id="selectAllInvites" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelectionInvites()" /></th>
                                                    <th class="w44pc"><?php echo TXT_COURRIEL ?></th>
                                                    <th class="last"><?php echo TXT_DATE_ENVOI_INVITATION ?></th>
													</tr>
				
													<?php
														$idx = 0; 
														foreach ($listeCollaborateursInvites as $collaborateur) { 
															$idx++; ?> 
													
															<tr>
																<td class="cCheck"><input class="noBord selectionElementInvites" type="checkbox" name="collaborateurs_invites_selection_<?php echo $idx  ?>" value="<?php echo $collaborateur['collaborateur_courriel']  ?>"/></td>
                                                            <td><?php echo $collaborateur['collaborateur_courriel'] ?></td>
																<td class="last"><?php echo $collaborateur['date_creation'] ?></td>
															</tr>
													
													<?php } ?>
													
												</table>					
											
											<p><label for="projet_collaborateur_courriel"><?php echo TXT_INVITER_NOUVEAU_COLLABORATEUR ?></label>
											<input class="w60pc suiviModif" type="text" id="projet_collaborateur_courriel" name="projet_collaborateur_courriel" value="<?php echo $projet->get("collaborateur_courriel") ?>" placeholder="<?php echo TXT_COLLABORATEURS_INSTRUCTIONS2 ?>" />
											<input class="btnSubmit" type="button" value="<?php echo TXT_ENVOI ?>" onclick="envoiInvitation()" />

											</p>
											
                                            <p class="margTop20"><?php echo TXT_COLLABORATEUR_NOTE ?></p>
											
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
