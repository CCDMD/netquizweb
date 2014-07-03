<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css'  href='../css/netquiz-print.css' media="print" />
</head>

<body id="bErreur">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-print.php' ?>
		
		<div id="corps">
			
			<div class="boxMsgErr" style="margin:10px 0px;"><p><?php echo TXT_ERREURS_DETECTEES_QUESTIONNAIRE . " " . $quest->get("questionnaire_apercu_titre") . " (" . TXT_PREFIX_QUESTIONNAIRE . $quest->get("questionnaire_id_questionnaire") . ")" ?> </p></div>
			
			<?php if ($quest->get("questionnaire_apercu_messages_entete") != "") { ?>
			<ul>
				<?php echo $quest->get("questionnaire_apercu_messages_entete") ?>
			</ul>
			<?php } ?>

			<?php echo $quest->get("questionnaire_apercu_messages_details") ?>
			
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>
