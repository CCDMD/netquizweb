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

				<h1><?php echo TXT_MOT_PASSE_OU_NOM_UTILISATEUR_OUBLIE ?></h1>
				
				<!--  Messages -->
	<?php if (isset($messages)) { ?>

	<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1"><p><?php echo $messages->getMessages(); ?> </p></div>
		
	<?php } ?>
				<!--  /Messages -->
				
				<p class="padTo10"><?php echo TXT_SAISISSEZ_VOTRE_ADRESSE_COURRIEL ?></p>
				
				<form name="frmLogin" id="frmLogin" action="identification.php" method="post">
					<input type="hidden" name="demande" value="rappel_envoi" />
					<p><label for="courriel"><?php echo TXT_COURRIEL ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					<input  class="w75pc" type="text" name="courriel" maxlength="150" /></p>
					<p><input class="btnSubmit" type="submit" name="btnSubmit" id="btnSubmit" value="<?php echo TXT_ENVOYER ?>" /></p>
					<p><a href="<?php echo URL_ACCUEIL ?>" >Retour &agrave; la connexion</a></p>
				</form>	

			</div> <!-- /colG -->
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>