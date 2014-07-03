<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB_ADMIN?> - <?php echo TXT_TOUS_LES_UTILISATEURS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">

	function approuver() {
		document.frm.demande.value="demande_compte_approuver";
		document.frm.submit();
	} 

	function refuser() {

		if (confirm("<?php echo TXT_CONFIRMER_REFUS_COMPTE ?>") ) {
			document.frm.demande.value="demande_compte_refuser";
			document.frm.submit();
		}
	} 
	
	</script>
	
</head>

<body id="bADemandes" onload="resizePanels();">

	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-admin.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-rech-admin.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
						
								<form method="post" name="frm" action="admin.php">
								<input type="hidden" name="demande" value="usager_approuver" />
								<input type="hidden" name="id_usager_app" value="<?php echo $usagerApp->get("id_usager") ?>" />
							
								<div class="filAriane">&nbsp;</div>
								
	                            <div class="detail">
	                                <div class="detailTop"><div>&nbsp;</div></div>
	                                
	                                <div id="section1" class="detailContenant">
	                                    <div class="detailContenu">
	                                    
	                                     	<h2>Demande de compte</h2>
	                                    
	                                        <!--  Messages -->
	                                        <?php include '../ressources/includes/message_onglet1.php' ?>
	                                        <!--  /Messages -->
	
	                                        
	                                        
	                                        Code Utilisateur : <?php echo $usagerApp->get("code_usager") ?> <br /><br />
	                                        Prénom : <?php echo $usagerApp->get("prenom") ?> <br /><br />
	                                        Nom: <?php echo $usagerApp->get("nom") ?> <br /><br />
	                                        Courriel : <?php echo $usagerApp->get("courriel") ?><br /><br />
	                                        Langue : <?php echo $usagerApp->get("langue_interface") ?><br /><br />
	                                        Date de la demande : <?php echo $usagerApp->get("date_creation") ?><br /><br />
	                                        
	                                        L'utilisateur est déjà assigné comme collaborateur pour les projets suivants : <br/> 
											<?php foreach ($listeProjets as $p) {
	                                        
	                                        	echo TXT_PREFIX_PROJET . $p->get("id_projet") . " - " . $p->get("titre") . "<br />";
	                                        
	                                        } ?>                                        
	                                        
	                                        <br />
	                                        
	                                        
	                                        
	                                        Ajouter l'utilisateur comme collaborateur à un projet existant * :<br /> 
	                                        <select name="id_projet">
	                                        <option value="">Aucun projet</option>
	                                        <?php foreach ($listeProjetsActifs as $p) { ?>
	                                        
	                                        	<option value="<?php echo $p->get("id_projet") ?>"><?php echo TXT_PREFIX_PROJET . $p->get("id_projet") . " - " . $p->get("titre")?></option>
	                                        
	                                        <?php } ?>
	                                        </select>
	                                        <br /><br /><br />
	                                        
	                                        <input type="button" value="Refuser la demande" onclick="refuser()" />
	                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                                        <input type="button" value="Approuver la demande" onclick="approuver()" />
	                                        
	                                        <br /><br /><br /><br />
	                                        * Si l'utilisateur n'est pas assigné comme collaborateur à un projet existant, celui-ci sera invité à créer son propre projet lors de la connexion initiale
	                                        <br /><br />
	                                        
	
	                                    </div>						
	                                </div>
	                                
	                                </form>
	                                                        
	                                <div class="detailBot"><div>&nbsp;</div></div>
	                            </div>
	
	
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





























                                        
