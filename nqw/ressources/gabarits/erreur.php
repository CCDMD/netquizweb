<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ERREUR ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- JQuery + UI -->
	<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>

	<!-- NetquizWeb -->
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css' />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css' href='../css/netquiz-print.css' media="print" />
</head>

<script type="text/javascript" language="javascript">
$(document).ready(function () {
	
	var modeFenetre = self != top;

	if (modeFenetre) {
		$("#modeNormal").hide();
		$("#modeFenetre").show();
	} else {
		$("#modeNormal").show();
		$("#modeFenetre").hide();
	}
});
</script>

<body id="bErreur">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-index.php' ?>
		
		<div id="corps">
			<!--[if lte IE 7]><div class="boxMsgOldBrowser"><p><?php echo TXT_MESSAGE_NAVIGATEURS_NON_SUPPORTES ?></p></div><![endif]-->
			<div id="colG">
				<h2>Erreur</h2>
				<div class="boxMsgErr"><p><?php echo $messageErreur ?></p></div>
				
				<br />
				<div id="modeNormal">
					<a href="<?php echo URL_BASE?>">Retour</a>
				</div>
				<div id="modeFenetre">
					<a href="#" onclick="parent.$.fancybox.close();">Fermer</a>
				</div>
			</div> <!-- /colG -->
			
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>
