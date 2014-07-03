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

<body id="bIndex">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-index.php' ?>
		
		<div id="corps">
			<div id="colG">
			
				<h1>
				<?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB1 . " "?>
				<a class="lnkVersionNqw" href="<?php echo URL_AIDE_NQW . Securite::nettoyerNomfichierTirets(VERSION_NQW) ?>" target="_blank"><?php echo VERSION_NQW ?></a><?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB2 ?>
				</h1>
				
				<h2>Mise à jour de l'application</h2>
				
				<p>Confirmation de la mise à jour</p>
				
				<p>La mise à jour de Netquiz Web est maintenant complétée et la version <?php echo VERSION_NQW ?> a été installée.</p>
				
				<p>Vous pouvez maintenant débuter l'utilisation de l'application.</p>
				
				<form action="questionnaires.php" method="get">
			
				<p><input type="submit" value="Continuer"/></p>
				
				</form>
				
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>
