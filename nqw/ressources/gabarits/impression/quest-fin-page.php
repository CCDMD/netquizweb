<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css'  href='../css/netquiz-print.css' media="print" />
</head>

<body id="bImprime" onload="//window.print();window.close()">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-print.php' ?>
		
		<div id="corps">
		
			<h1><?php echo TXT_QUESTIONNAIRE ?>&nbsp;<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire")?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $quest->get("titre") ?></h1>

			<p><span class="champTitre"><?php echo TXT_DATE_DE_CREATION ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("date_creation",1) ?></span>
				<span class="champTitre padGa25"><?php echo TXT_DATE_DE_MODIFICATION ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("date_modification",1) ?></span></p>

			<?php include '../ressources/gabarits/impression/quest-fin-details.php' ?>

		</div> <!-- /corps -->
	
	</div> <!-- /bodyContenu -->
</body>
</html>
