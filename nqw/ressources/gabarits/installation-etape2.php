<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ACCUEIL ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- JQuery + UI -->
	<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>

	<!-- NetquizWeb -->
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css' />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css'  href='../css/netquiz-print.css' media="print" />
</head>

<body id="bInstall">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-installation.php' ?>
		
		<div id="corps">
			<div id="colG">
			
				<h1>
				<?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB1 . " "?>
				<a class="lnkVersionNqw" href="<?php echo URL_AIDE_NQW . Securite::nettoyerNomfichierTirets(VERSION_NQW) ?>" target="_blank"><?php echo VERSION_NQW ?></a><?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB2 ?>
				</h1>
				
				<h2><?php echo TXT_INSTALLATION_NETQUIZ_WEB ?></h2>
				
				<p><?php echo TXT_ETAPE_2_DE_3 ?></p>
				
				<!--  Messages -->
				<?php if (isset($messages)) { ?>
			
				<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1">
					<ul> 
						<?php echo $messages->getMessages() ?>
					</ul>
				</div>
					
				<?php } ?>
				<!--  /Messages -->				
								
				<p><?php echo TXT_SAISIR_INFOS_ADMIN ?> : </p>

				<form action="install.php" method="post">
				<input type="hidden" name="demande" value="installation_etape3" />
				
				
					<p><label for="usager_code_usager"><?php echo TXT_NOM_UTILISATEUR ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr><span class="aide"><?php echo TXT_CODE_USAGER_INSTRUCTIONS ?></span></label>
					<input class="w75pc" type="text" id="usager_code_usager" name="usager_code_usager" size="65" maxlength="150"  value="<?php echo $nouvUsager->get("code_usager") ?>"/></p>
										
					<p><label for="usager_prenom"><?php echo TXT_PRENOM ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					<input class="w75pc" type="text" id="usager_prenom" name="usager_prenom" maxlength="150"  value="<?php echo $nouvUsager->get("prenom") ?>"/></p>
		
					<p><label for="usager_nom"><?php echo TXT_NOM ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					<input  class="w75pc" type="text" id="usager_nom" name="usager_nom" maxlength="150" value="<?php echo $nouvUsager->get("nom") ?>"/></p>
					
					<p><label for="usager_courriel"><?php echo TXT_COURRIEL ?><abbr title="<?php echo TXT_CHAMPS_REQUIS ?>"> *</abbr></label>
					<input  class="w75pc" type="text" id="usager_courriel" name="usager_courriel" maxlength="150"   value="<?php echo $nouvUsager->get("courriel") ?>" /></p>					
					
					<p><label for="usager_langue"><?php echo TXT_LANGUE ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					
						<?php foreach ($listeLanguesInterface as $codeLangue => $titreLangue) { ?>
						
							<input type="radio" name="usager_langue" value="<?php echo $codeLangue ?>" id="langue<?php echo $codeLangue ?>" <?php if ($nouvUsager->get("langue") == $codeLangue || ( $nouvUsager->get("langue") == "" && $codeLangue == LANGUE_DEFAUT ))  { echo "checked"; } ?> />&nbsp;<?php echo $titreLangue ?><br />
						
						<?php } ?>
					</p>
					
					<p><label for="usager_mdp_nouv"><?php echo TXT_MOT_DE_PASSE ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr><span class="aide"><?php echo TXT_MDP_INSTRUCTIONS ?></span></label>
					<input  class="w75pc" type="password" name="usager_mdp_nouv" maxlength="150" /></p>
					<p><label for="usager_mdp_conf"><?php echo TXT_CONFIRMER_LE_MOT_DE_PASSE ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					<input  class="w75pc" type="password" name="usager_mdp_conf" maxlength="150" /></p>				
			
				<p><input type="submit" value="<?php echo TXT_CONTINUER ?>"/></p>
				
				</form>
				
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>
