<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB_ADMIN?> - <?php echo TXT_TOUS_LES_UTILISATEURS ?></title>
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


		function verifierSelection() {

			// Vérifier si un des checkbox est sélectionné			
			var nbSel = $('input:checkbox:checked.selectionElement').map(function () { 
				  return this.value; 
			}).size();

			// Si aucune sélection, désactiver tous les choix
			if (nbSel == 0) {
				$("#menuModifier").addClass("inactif");
				$("#menuApprouver").addClass("inactif");
				$("#menuCourriel").addClass("inactif");
				$("#menuCorbeille").addClass("inactif");
				
			}

			// Si une sélection, activer tous les choix
			if (nbSel == 1) {
				$("#menuModifier").removeClass("inactif");
				$("#menuApprouver").removeClass("inactif");
				$("#menuCourriel").removeClass("inactif");
				$("#menuCorbeille").removeClass("inactif");
			}				
			
			// Désactiver les items du menu qui ne s'appliquent pas plusieurs utilisateurs
			if (nbSel > 1) {			
				$("#menuModifier").addClass("inactif");
				$("#menuApprouver").removeClass("inactif");
				$("#menuCourriel").removeClass("inactif");
				$("#menuCorbeille").removeClass("inactif");
			}
		}


		function approuverUtilisateurs() {

			nbUtilisateursErr = 0;

			// Obtenir les éléments sélectionnés
			$('input:checkbox:checked.selectionElement').each(function() {
				sel = $(this).attr('name');
				cle = "#" + sel + "_statut";
				statut = $(cle).val();
						
				if (statut != "<?php echo Usager::STATUT_A_APPROUVER ?>") {
					nbUtilisateursErr++;
				} 
			});

			// Vérifier si l'admin tente par erreur d'approuver des utilisateurs qui ne sont pas à approuver
			if (nbUtilisateursErr > 0) {
				alert("<?php echo MSG_031 ?>");
			} else {
				// Confirmer avec l'utilisateur qu'il veut approuver tous ces utilisateurs
				if (confirm("<?php echo TXT_CONFIRMER_APPROBATION ?>")) {
					soumettreDemande('utilisateurs_approuver');
				} 
			}
		}

		
		function envoiCourrielUtilisateurs() {

			// Déterminer le séparateur
			separateur = ""; 
			var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
			if (isMacLike) {
				separateur = ", ";
			} else {
				separateur = "; ";
			}
			
			chaine = "mailto:" + '<?php echo $usager->get("courriel") ?>' + "?bcc=";
			
			// Obtenir les éléments sélectionnés
			$('input:checkbox:checked.selectionElement').each(function() {
				sel = $(this).attr('name');
				cle = "#" + sel + "_courriel";
				courriel = $(cle).val();

				chaine += courriel + separateur;
			});

			document.location = chaine;
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
						<?php include '../ressources/includes/barre-rech-admin-utilisateurs.php' ?> 
						<div id="contenu">
							<div id="contenuPrincipal">
								<div class="filAriane">
	                            	<h2><img src="../images/ic-utilisateur.png" alt="<?php echo TXT_TOUS_LES_UTILISATEURS ?>" /><?php echo TXT_TOUS_LES_UTILISATEURS ?><span><a class="tools" href="admin.php?demande=utilisateur_ajouter"><?php echo TXT_AJOUTER_UN_UTILISATEUR ?></a></span></h2>
	                            </div>
								
								<!--  Messages -->
								<?php include '../ressources/includes/message_tableaux.php' ?>
								<!--  /Messages -->
																
								<form id="frm" name="frm" action="admin.php">
									<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
									<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
									<input type="hidden" name="pagination_page_dest" value="" />
									<input type="hidden" name="tri" value="" />
									<input type="hidden" name="demande" value="utilisateurs_liste" />
									
									<table class="tblListe">
										<tr class="tblNav">
											<td class="alCe">
												<div class="menuContexte">
													<img src="../images/ic-tools.png" alt="" />
													<?php include '../ressources/includes/menu-contexte-admin-utilisateurs-liste.php' ?>
												</div>
											</td>
											<td colspan="7">
												<div class="flGa">
													<select name="statut" onchange="soumettre()">
														<option value="tous" <?php if ($filtreStatut == "tous") { echo "selected"; } ?>><?php echo TXT_AFFICHER_TOUS_LES_UTILISATEURS ?></option>
														<option value="0" <?php if ($filtreStatut == "0") { echo "selected"; } ?>><?php echo TXT_AFFICHER_TOUS_LES_UTILISATEURS_ACTIFS ?></option>
														<option value="1" <?php if ($filtreStatut == "1") { echo "selected"; } ?>><?php echo TXT_AFFICHER_TOUS_LES_UTILISATEURS_VERROUILLES ?></option>
														<option value="2" <?php if ($filtreStatut == "2") { echo "selected"; } ?>><?php echo TXT_AFFICHER_TOUS_LES_UTILISATEURS_EN_ATTENTE_APPROBATION ?></option>
														<option value="3" <?php if ($filtreStatut == "3") { echo "selected"; } ?>><?php echo TXT_AFFICHER_TOUS_LES_UTILISATEURS_AVEC_ACCES_REFUSES ?></option>
														<option value="admin" <?php if ($filtreStatut == "admin") { echo "selected"; } ?>><?php echo TXT_AFFICHER_TOUS_LES_ADMINISTRATEURS ?></option>
													</select>
												</div>
												<div class="flDr">
													<?php include '../ressources/includes/table-nav-haut.php' ?>
												</div>
											</td>
										</tr>
										<tr>
											<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()" /></th>
											<th class="cCode"><a href="#" class="<?php echo $usager->get('tri_id_usager') ?>" onclick="changerTriUsager('id_usager')"><?php echo TXT_CODE ?></a></th>
											<th class="c3"><a href="#" class="<?php echo $usager->get('tri_nom_prenom') ?>" onclick="changerTriUsager('nom_prenom')"><?php echo TXT_NOM.', '.TXT_PRENOM ?></a></th>
											<th class="c4"><a href="#" class="<?php echo $usager->get('tri_courriel') ?>" onclick="changerTriUsager('courriel')"><?php echo TXT_COURRIEL ?></a></th>
											<th class="c5"><a href="#" class="<?php echo $usager->get('tri_nb_projet') ?>" onclick="changerTriUsager('nb_projets')"><?php echo TXT_TOUS_LES_PROJETS ?></a></th>
											<th class="c7"><a href="#" class="<?php echo $usager->get('tri_statut') ?>" onclick="changerTriUsager('statut')"><?php echo TXT_STATUT ?></a></th>
											<th class="c8 last"><a href="#" class="<?php echo $usager->get('tri_date_dern_authentification') ?>" onclick="changerTriUsager('date_dern_authentification')"><?php echo TXT_DERNIER_ACCES ?></a></th>
										</tr>
	
										<?php foreach($listeUsagers as $usager){ ?> 
										<input type="hidden" id="utilisateurs_selection_<?php echo $usager->get('id_usager') ?>_statut" value="<?php echo $usager->get("statut") ?>" />
										<input type="hidden" id="utilisateurs_selection_<?php echo $usager->get('id_usager') ?>_courriel" value="<?php echo $usager->get("courriel") ?>" />
										
											<tr>
												<td class="cCheck"><input class="noBord selectionElement" type="checkbox" name="utilisateurs_selection_<?php echo $usager->get("id_usager")?>" value="<?php echo $usager->get("id_usager")?>" /></td>
												<td><?php echo TXT_PREFIX_USAGER . $usager->get('id_usager') ?></td>
												<td><a href="admin.php?demande=utilisateur_modifier&usager_id_usager=<?php echo $usager->get('id_usager') ?>"><?php echo $usager->getNomPrenom() ?></a></td>
												<td><?php echo $usager->get("courriel") ?></td>
												<td><?php echo $usager->get('nb_projets'); ?></td>
												<td><?php echo $usager->getStatutTxt() ?></td>
												<td class="c7 last"><?php echo $usager->get('date_dern_authentification') ?></td>
											</tr>
										
										<?php }?>
										
										<tr class="lgLast tblNav">
											<td colspan="8" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
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
