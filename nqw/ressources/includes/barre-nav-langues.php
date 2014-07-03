	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="bibliotheque.php" method="post">
		<input type="hidden" name="demande" value="langue_modifier" />
			<div class="txt"><?php echo TXT_LANGUE ?></div>
			<input class="btnSubmit <?php if ($langue->get("page_precedente") == $langue->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $langue->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="langue_page" size="4" maxlength="4"  value="<?php echo $langue->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $langue->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($langue->get("page_suivante") == $langue->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $langue->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>	