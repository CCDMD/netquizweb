<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ACCUEIL ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- NetquizWeb -->
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css' />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css'  href='../css/netquiz-print.css' media="print" />
</head>

<body id="bIndexOptions">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-index.php' ?>
		
		<div id="corps">
			<div id="colG">

				<h1><?php echo TXT_REINITIALISER_VOTRE_MOT_PASSE ?></h1>

			<?php if (isset($messages)) { ?>
				
				<ul class="errProfil"> 
				<?php echo $messages->getMessages() ?>
				</ul>
				
			<?php } ?>				
				
				<form name="frmLogin" id="frmLogin" action="mdp.php" method="post">
				  <input type="hidden" name="demande" value="mdp_enregistrer" />
				  <input type="hidden" name="id" value="<?php echo $idUsager ?>" />
				  <input type="hidden" name="conf" value="<?php echo $codeRappel ?>" />
					
					<p><span class="gras"><?php echo TXT_NOM_UTILISATEUR?> : <?php echo $usager->get("code_usager") ?></span><br />
					<span class="gras"><?php echo TXT_COURRIEL ?> : </span><?php echo $usager->get("courriel")?></p>
									
					<p><label for="usager_mdp_nouv"><?php echo TXT_NOUVEAU_MOT_PASSE ?><abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr> <?php echo TXT_MDP_INSTRUCTIONS ?></label>
					<input class="w75pc" type="password" id="usager_mdp_nouv"  name="usager_mdp_nouv" maxlength="150" /></p>
			
					<p><label for="usager_mdp_conf"><?php echo TXT_NOUVEAU_MOT_PASSE_CONF ?><abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					<input class="w75pc" type="password" id="usager_mdp_conf"  name="usager_mdp_conf" maxlength="150" /></p>
						
					<p><input class="btnSubmit btnEnregistrer" type="submit" name="btnSubmit" id="btnSubmit" value="<?php echo TXT_ENREGISTRER?>" /></p>
					<p><a href="<?php echo URL_ACCUEIL ?>" ><?php echo TXT_RETOUR_A_LA_CONNEXION ?></a></p>
				</form>		  
			</div> <!-- /colG -->
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>
