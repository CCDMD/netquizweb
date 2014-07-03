	<div id="barreNav">
		<form name="frmRech" id="frmRech" action="questionnaires.php" method="get">
		        <input type="hidden" name="demande" value="questionnaire_selectionner_recherche" />
		        <input type="hidden" name="questionnaire_id_questionnaire" value="<?php echo $quest->get("id_questionnaire") ?>" />
				<input type="text" name="chaine" id="rechMots" size="50" maxlength="150"  value="<?php echo $chaineRechQuestSel ?>" placeholder="<?php echo TXT_INSCRIRE_MOTSCLES ?>" />
				<input class="btnReset" name="btnRechRes" id="btnRechRes" type="button" value="&nbsp;" onclick="envoiDemandeIdQuestionnaire('questionnaire_selectionner_recherche_initialiser', '<?php echo $quest->get("id_questionnaire") ?>')" />
				<input class="btnSubmit" name="btnRechSub" id="btnRechSub" type="submit" value="<?php echo TXT_CHERCHER ?>" />
		</form>
	</div>
