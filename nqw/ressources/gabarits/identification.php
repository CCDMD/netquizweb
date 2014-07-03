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
    
	<script type="text/javascript" language="javascript">
	$(document).ready(function () {

		// Ne pas ouvrir la page de connexion dans une fenêtre
		var modeFenetre = self != top;
	
		if (modeFenetre) {
			opener.location = "<?php echo URL_ERREUR ?>" + "?erreur=131";
		}
	});
	</script>    
    
	<script type="text/javascript">
		function setFocus(){
			if(document.frmLogin.codeUtilisateur.value.length==0) {
			document.frmLogin.codeUtilisateur.focus();
			}
		}
	</script>
</head>

<body id="bIndex" onload="setFocus();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-index.php' ?>
		
		<div id="corps">
			<div id="colG">
				<h1>
				<?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB1 . " "?>
				<a class="lnkVersionNqw" href="<?php echo URL_AIDE_NQW . Securite::nettoyerNomfichierTirets(VERSION_NQW) ?>" target="_blank"><?php echo VERSION_NQW ?></a>
				<?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB2 ?></h1>
				
				<?php if ($textes->get("message_avertissement") != '') { ?>
					<div class="boxMsgWarn boxMsgMaintenance" style="width:100%;"><p><?php echo html_entity_decode($textes->get("message_avertissement"), ENT_QUOTES, "UTF-8") ?></p></div>
				<?php } ?>
				<p><?php echo TXT_IDENTIFICATION_LIGNE1?></p>
				
				<?php if ($textes->get("message_bienvenue") != '') { ?>
					<p><?php echo html_entity_decode($textes->get("message_bienvenue"), ENT_QUOTES, "UTF-8") ?></p>
				<?php } ?>
			
				<h1><?php echo TXT_CONNEXION ?></h1>
				<p><?php echo TXT_ENTREZ_NOM_UTILISATEUR_ET_MOT_DE_PASSE?></p>
				<p><?php echo TXT_IDENTIFICATION_CONSULTEZ_AIDE?></p>
			
				<!--[if lte IE 7]><div class="boxMsgOldBrowser"><p><?php echo TXT_MESSAGE_NAVIGATEURS_NON_SUPPORTES ?></p></div><![endif]-->
				
				<?php 
					// Afficher message d'erreur
					if (isset($messages)) { 
						echo "<div class=\"boxMsg" . $messages->getTypeMessage() . "\"><p class=\"msgErr\">" . $messages->getMessages() . "</p></div>";
					}
  				?>

				<form name="frmLogin" id="frmLogin" action="identification.php" method="post">
				    <input type="hidden" name="demande" value="authentification" />
					<div class="boxCadre"><div class="box">
					
						<p><label for="codeUtilisateur"><?php echo TXT_COURRIEL_OU_NOM_UTILISATEUR ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
							<input type="text" name="codeUtilisateur" id="codeUtilisateur" size="50" maxlength="150" value="<?php if (isset($codeUtilisateurRappel)) { echo $codeUtilisateurRappel; } ?>"/></p>
						<p><label for="motPasse"><?php echo TXT_MOT_DE_PASSE ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
							<input type="password" name="motPasse" id="motPasse" size="50" maxlength="150" /></p>
						<p><input class="chk" type="checkbox" name="connexionActive" value="1" <?php if (isset($codeUtilisateurRappel)) { echo "checked"; } ?>/> <?php echo TXT_SE_SOUVENIR_DE_MOI ?></p>
					</div></div>
					<div class="boxAfter">
						<!--[if lte IE 7]>
							<p><input class="btnSubmit" type="submit" name="btnSubmit" id="btnSubmit" value="<?php echo TXT_ME_CONNECTER ?>" disabled="disabled" /></p>
						<![endif]-->
						<!--[if gt IE 7]>
							<p><input class="btnSubmit" type="submit" name="btnSubmit" id="btnSubmit" value="<?php echo TXT_ME_CONNECTER ?>" /></p>
							<p><a href="identification.php?demande=rappel"><?php echo TXT_MOT_DE_PASSE_OU_UTILISATEUR_OUBLIE ?></a><br />
                            	<a href="identification.php?demande=demander_compte"><?php echo TXT_DEMANDER_UN_COMPTE ?></a></p>
						<![endif]-->
						<!--[if !IE]> -->
							<p><input class="btnSubmit" type="submit" name="btnSubmit" id="btnSubmit" value="<?php echo TXT_ME_CONNECTER ?>" /></p>
							<p><a href="identification.php?demande=rappel"><?php echo TXT_MOT_DE_PASSE_OU_UTILISATEUR_OUBLIE ?></a><br />
                            	<a href="identification.php?demande=demander_compte"><?php echo TXT_DEMANDER_UN_COMPTE ?></a></p>
						<!-- <![endif]-->						
					</div>
				</form>		  
			</div> <!-- /colG -->
			
			<!-- Pour usage futur, Pour les nouveautes
            <div id="colD">
				<h1><a href="#"><img class="flDr" src="../images/ic-fil-rss.jpg" alt="Fil RSS" /></a>< ?php echo TXT_NOUVEAUTE ? ></h1>
				<div id="contenu">
					<div class="news">
						<p>Lecteur de flux qui affiche les nouveaut&eacute;s, nouvelles, mises &agrave; jour de NetquizWeb selon la langue.</p>
					</div>
				</div>
			</div> <!-- /colD -->
			
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>
