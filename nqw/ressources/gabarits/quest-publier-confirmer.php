<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ITEMS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

	<style type="text/css">

		html, body { height: 100%; background:#FFFFFF; }

	</style>	
	
	<script type="text/javascript">

		// Bouton annuler
		function fermer() {
			// Fermer la fenêtre
			window.close();
		}

		function ouvrirFenetreQuestionnaire(url) {
			NewWindow(url,'apercuquestionnaire','980','600','yes','yes','yes');
			window.close();
		}
		
		$(document).ready(function () {
			window.opener.remplacerStatut('<?php echo $quest->get("statut") ?>','<?php echo $quest->getStatutTxt() ?>');
		});

		$(document).ready(function () {
			width = 650;
			height = 500
			window.resizeTo(width, height);
			window.moveTo((screen.availWidth-width)/2, (screen.availHeight-height)/2);
		});
		
	</script>
	
</head>

<body>

	<div class="boxStdWindow" style="min-width:350px; min-height:200px; ">
	
		<div class="boxTitre"><p><?php echo TXT_PUBLIER_LE_QUESTIONNAIRE ?> - <?php echo $quest->get("titre") ?></p></div>

			<div class="boxContenu">
			<div class="boxPrincipal" >
					<?php if (isset($messages)) { ?>

						<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1" style="width:99%; ">
							<p><?php echo $messages->getMessages(); ?></p>
						</div>

					<?php } else { ?>

						<div class="boxMsgOk margTop20">
							<p><?php echo TXT_QUESTIONNAIRE_PUBLIE_AVEC_SUCCES?></p>
						</div>
						
						<p><?php echo TXT_ADRESSE_DU_DOSSIER_EN_LIGNE_EST?>&nbsp;:&nbsp;<a href="<?php echo URL_DOMAINE . URL_PUBLICATION . $pageInfos['repertoire_projet'] . $pageInfos['repertoire_publication'] . "/" ?>" onclick="parent.ouvrirFenetreQuestionnaire('<?php echo URL_DOMAINE . URL_PUBLICATION . $pageInfos['repertoire_projet'] . $pageInfos['repertoire_publication'] ?>', 'questionnaire_apercu')"><?php echo URL_DOMAINE . URL_PUBLICATION . $pageInfos['repertoire_projet'] . $pageInfos['repertoire_publication'] . "/" ?></a></p>

					<?php } ?>
			</div>
		</div>
	
		<div class="boxBottom"><input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="fermer()" value="<?php echo TXT_FERMER ?>"  /></div>
	
	</div>
	
</body>
</html>
