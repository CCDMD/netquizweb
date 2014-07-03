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

			<?php if (isset($messages)) { ?>
				
				<ul> 
				<?php echo $messages->getMessages() ?>
				</ul>
				
			<?php } ?>

				<h1><?php echo TXT_REINITIALISER_VOTRE_MOT_PASSE ?></h1>

				<p class="padTo20"><?php echo TXT_MOT_PASSE_MODIFIE_SUCCES ?>.</p>
				<p><?php echo TXT_VEUILLEZ ?> <a href="<?php echo URL_ACCUEIL ?>"><?php echo TXT_VOUS_CONNECTER ?></a> <?php echo TXT_POUR_CONTINUER ?>.</p>						

						
			</div> <!-- /colG -->
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>