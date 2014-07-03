<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ADMINISTRATION ?></title>
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
				$("#actionRecuperer").addClass("inactif");
				$("#actionSupprimer").addClass("inactif");
			}
			
			// Désactiver les items du menu qui ne s'appliquent pas plusieurs questionnaires
			if (nbSel >= 1) {
				$("#actionRecuperer").removeClass("inactif");
				$("#actionSupprimer").removeClass("inactif");
			}
		}		

		function supprimerElementsCorbeille() {

			if (confirm("<?php echo TXT_CONFIRMER_SUPPRESSION_ELEMENTS ?>")) {
				soumettreDemande('corbeille_supprimer');
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

<body id="bACorbeille" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-admin.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-rech-admin-corbeille.php' ?> 
						<div id="contenu">
							<div id="contenuPrincipal">
							
								<div class="filAriane"><h2><img src="../images/ic-corbeille.png" alt="<?php echo TXT_CORBEILLE ?>" /><?php echo TXT_CORBEILLE ?></h2></div>
								
								<div>
									<form id="frm" name="frm" action="admin.php" method="post">
										<input type="hidden" name="pagination_nb_elements" value="<?php echo $pagination->getNbElemParPage()?>" />
										<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
										<input type="hidden" name="pagination_page_dest" value="" />
										<input type="hidden" name="tri" value="" />
										<input type="hidden" name="demande" value="corbeille" />
                                        
										<table class="tblListe tblListCorb">
											<?php include '../ressources/includes/corbeille.php' ?>
										</table>
									</form>
								</div>
								
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
