	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="questionnaires.php" method="post">
		<input type="hidden" name="demande" value="questionnaire_modifier" />
			<div class="txt"><?php echo TXT_QUESTIONNAIRE ?></div>
			<input class="btnSubmit <?php if ($quest->get("page_precedente") == $quest->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $quest->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="questionnaire_page" size="4" maxlength="4"  value="<?php echo $quest->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $quest->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($quest->get("page_suivante") == $quest->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $quest->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>