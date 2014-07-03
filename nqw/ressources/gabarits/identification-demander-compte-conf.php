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

				<h1><?php echo TXT_DEMANDER_UN_COMPTE ?></h1>
				
				<!--  Messages -->
				<?php if (isset($messages)) { ?>
			
				<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1"><p><?php echo $messages->getMessages(); ?> </p></div>
					
				<?php } ?>
				<!--  /Messages -->
				
				<p class="padTo10"><?php echo TXT_DEMANDE_TRANSMISE ?></p>
								
				<p><a href="<?php echo URL_ACCUEIL ?>"><?php echo TXT_RETOUR_A_LA_CONNEXION ?></a></p>
				
			</div> <!-- /colG -->
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>