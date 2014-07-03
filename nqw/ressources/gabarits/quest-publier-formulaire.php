<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_PUBLIER_LE_QUESTIONNAIRE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>

	<style type="text/css">

		html, body { height: 100%; background:#FFFFFF; }

	</style>	
	
	<script type="text/javascript">

		// Bouton annuler
		function annuler() {
			// Fermer la fenêtre
			window.close();
		}
		
		$(document).ready(function () {
			width = 650;
			height = 500;
			window.resizeTo(width, height);
			window.moveTo((screen.availWidth-width)/2, (screen.availHeight-height)/2);
		});
	</script>
	
</head>

<body>

	<div class="boxStdWindow" style="min-width:350px; min-height:200px; ">
	
		<div class="boxTitre"><p><?php echo TXT_PUBLIER_LE_QUESTIONNAIRE ?> - <?php echo $quest->get("titre") ?></p></div>
		
		<form name="frmPublier" id="frmPublier" action="questionnaires.php" method="post">
			<input type="hidden" name="demande" value="questionnaire_publier_envoi" />
			<input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />

			<div class="boxContenu">
				<div class="boxPrincipal">

					<?php if (isset($messages)) { ?>

						<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1" style="width:99%; ">
							<p><?php echo $messages->getMessages(); ?></p>
						</div>

					<?php } ?>
					
					<p class="padTo15"><?php echo TXT_CONSIGNE_PUBLICATION1?></p>
					
					<p><?php echo TXT_CONSIGNE_PUBLICATION2?></p>
					
					<p class="padTo15">
						<label for="repertoire_publication"><?php echo TXT_ADRESSE_DU_DOSSIER_EN_LIGNE ?></label>
						<span><?php echo URL_DOMAINE . URL_PUBLICATION . $pageInfos['repertoire_projet'] ?> </span>
						<input type="text" id="repertoire_publication" name="repertoire_publication" size="50" maxlength="64" value="<?php echo $pageInfos['repertoire_publication'] ?>"/>
					</p>
				</div>
			</div>
			<div class="boxBottom">
				<input class="btnReset" name="btnReset" id="btnReset1" type="button" onclick="annuler()" value="<?php echo TXT_ANNULER ?>"  />
				<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_PUBLIER ?>" />
			</div>
		
		</form>		  
	</div>
		
</body>
</html>
