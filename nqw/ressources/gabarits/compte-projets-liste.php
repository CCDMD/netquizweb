<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_COMPTE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

    <script type="text/javascript">

	function changerPage(page, pageCour) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $pagination->getPageCour() ?>";

		if (page != pageCour) {
			document.frm.pagination_page_dest.value=page;
			document.frm.submit();
		}
	}
    
	function changerTri(tri) {
		document.frm.tri.value = tri;
		document.frm.submit();
	}

	function verifierSelection() {
		// Vérifier si un des checkbox est sélectionné			
		var nbSel = $('input:checkbox:checked.selectionElement').map(function () { 
			  return this.value; 
		}).size();

		// Si aucune sélection, désactiver tous les choix
		if (nbSel == 0) {
			$("#menuModifier").addClass("inactif");
			$("#menuTerminerCollaboration").addClass("inactif");
			$("#menuCorbeille").addClass("inactif");
			
		}

		// Si une sélection, activer tous les choix
		if (nbSel == 1) {
			$("#menuModifier").removeClass("inactif");
			$("#menuTerminerCollaboration").removeClass("inactif");
			$("#menuCorbeille").removeClass("inactif");
		}				
		
		// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
		if (nbSel > 1) {
			$("#menuModifier").addClass("inactif");
			$("#menuTerminerCollaboration").removeClass("inactif");
			$("#menuCorbeille").removeClass("inactif");
		}
	}


	function mettreProjetsCorbeille() {

		nbProjetRoleCollaborteur = 0;

		// Obtenir les éléments sélectionnés
		$('input:checkbox:checked.selectionElement').each(function() {
			sel = $(this).attr('name');
			cle = "#" + sel + "_role";
			role = $(cle).val();

			if (role == "collaborateur") {
				nbProjetRoleCollaborteur++;
			} 
		});

		// Vérifier si l'utilisateur tente de mettre à la corbeille des projets dont il n'est pas propriétaire
		if (nbProjetRoleCollaborteur > 0) {
			alert("<?php echo MSG_026 ?>");
		} else {
			// Confirmer avec l'utilisateur qu'il veut mettre ces projets à la corbeille
			if (confirm("<?php echo TXT_CONFIRMER_MISE_CORBEILLE_PROJET?>")) {
				soumettreDemande('projet_corbeille');
			}
		}
	}

	
	function terminerCollaborationProjetSelectionne() {

		nbProjetRoleResponsable = 0;

		// Obtenir les éléments sélectionnés
		$('input:checkbox:checked.selectionElement').each(function() {
			sel = $(this).attr('name');
			cle = "#" + sel + "_role";
			role = $(cle).val();

			if (role == "responsable") {
				nbProjetRoleResponsable++;
			} 
		});

		// Vérifier si l'utilisateur tente par erreur de se retirer comme collaborateur de ses propres projets
		if (nbProjetRoleResponsable > 0) {
			alert("<?php echo MSG_025 ?>");
		} else {
			// Confirmer avec l'utilisateur qu'il veut mettre fin à la collaboration des projets sélectionnés
			if (confirm("<?php echo TXT_CONFIRMER_RETRAIT_DE_LA_LISTE_DES_COLLABORATEURS ?>")) {
				soumettreDemande('projet_terminer_collaboration');
			} 
		}
	}
	
	$(document).ready(function () {

		// Activer le clic sur les boîtes de sélection 
		$(".selectionElement").click(function() {
			verifierSelection();
		});
		
		// Activer la sélection/déselection de tous les checkboxes
	    $('#selectall').change(function () { 
		    if ($(this).attr("checked")) { 
	            $('.selectionElement').prop('checked', true);
	        } else {
	            $('.selectionElement').prop('checked', false);
	        }
		    verifierSelection();
	    });

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
						<?php include '../ressources/includes/barre-rech-compte-projets.php' ?> 
						<div id="contenu">
							<div id="contenuPrincipal">
								<div class="filAriane"><h2><img src="../images/ic-projet.png" alt="<?php echo TXT_MES_PROJETS ?>" /><?php echo TXT_MES_PROJETS ?><span><a class="tools" href="compte.php?demande=projet_ajouter"><?php echo TXT_AJOUTER_UN_PROJET ?></a></span></h2></div>
	
								<!--  Messages -->
								<?php include '../ressources/includes/message_tableaux.php' ?>
								<!--  /Messages -->							
								
								<form id="frm" name="frm" action="compte.php">
								<input type="hidden" name="demande" value="projets_liste" />
								<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								<input type="hidden" name="projet_id_projet" value="" />
								<input type="hidden" name="tri" value="" />
								
									
									<table class="tblListe tblListeProjets">
										<tr class="tblNav">
											<td class="alCe">
												<div class="menuContexte">
													<img src="../images/ic-tools.png" alt="" />
													<?php include '../ressources/includes/menu-contexte-compte-projets-liste.php' ?>
												</div>
											</td>
											<td colspan="6">
												<div class="flGa">
													<select name="responsable" onchange="soumettre()">
														<option value="tous"><?php echo TXT_AFFICHER_TOUS_LES_RESPONSABLES ?></option>
														<?php foreach ($listeResponsables as $id_responsable => $responsable) {
															if ($id_responsable != 0) { ?>
																												
																<option value="<?php echo $responsable ?>" <?php if ($pageInfos['responsable'] == $responsable) { echo "selected"; } ?> ><?php echo $responsable ?></option>
														
														<?php  }
														} ?>													
													</select>
												</div>
												<div class="flDr">
													<?php include '../ressources/includes/table-nav-haut.php' ?>
												</div>
											</td>
										</tr>
										
										<tr>
											<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()" /></th>
											<th class="cCode"><a href="#" class="<?php echo $projet->get('tri_id_projet') ?>" onclick="changerTri('id_projet')"><?php echo TXT_CODE ?></a></th>
											<th class="c3"><a href="#" class="<?php echo $projet->get('tri_titre') ?>" onclick="changerTri('titre')"><?php echo TXT_TITRE_DU_PROJET ?></a></th>
											<th class="c4"><a href="#" class="<?php echo $projet->get('tri_responsable') ?>" onclick="changerTri('responsable')"><?php echo TXT_RESPONSABLE ?></a></th>
											<th class="c5"><a href="#" class="<?php echo $projet->get('tri_repertoire') ?>" onclick="changerTri('repertoire')"><?php echo TXT_IDENTIFIANT ?></a></th>
											<th class="c6"><a href="#" class="<?php echo $projet->get('tri_statut') ?>" onclick="changerTri('statut')"><?php echo TXT_STATUT ?></a></th>
											<th class="c7 last"><a href="#" class="<?php echo $projet->get('tri_date_modification') ?>" onclick="changerTri('date_modification')"><?php echo TXT_DATE_DE_MODIFICATION ?></a></th>
										</tr>
	
										<?php foreach($listeProj as $proj){ ?> 
										
											<input type="hidden" id="projets_selection_<?php echo $proj->get("id_projet")?>_role" value="<?php if ($projet->isRoleResponsableProjet($idUsager, $proj->get("id_projet"))) { echo "responsable"; } else { echo "collaborateur"; } ?>" />
										
											<tr>
												<td class="cCheck"><input class="noBord selectionElement" type="checkbox" name="projets_selection_<?php echo $proj->get("id_projet")?>" value="<?php echo $proj->get("id_projet")?>" /></td>
												<td><?php echo TXT_PREFIX_PROJET ?><?php echo $proj->get('id_projet') ?></td>
												<td><a href="compte.php?demande=projet_modifier&projet_id_projet=<?php echo $proj->get('id_projet') ?>"><?php echo $proj->get('titre') ?></a></td>
												<td><?php echo $proj->getResponsableProjet()?></td>
												<td><?php echo $proj->get('repertoire'); ?></td>
												<td><?php echo $proj->getStatutTxt() ?></td>
												<td class="last"><?php echo $proj->get('date_modification') ?></td>
											</tr>
										
										<?php } ?>
										
										<tr class="lgLast tblNav">
											<td colspan="7" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
										</tr>
									</table>
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
