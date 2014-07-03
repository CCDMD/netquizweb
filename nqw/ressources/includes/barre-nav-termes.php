	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="bibliotheque.php" method="post">
		<input type="hidden" name="demande" value="terme_modifier" />
			<div class="txt"><?php echo TXT_TERMES ?></div>
			<input class="btnSubmit <?php if ($terme->get("page_precedente") == $terme->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $terme->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="terme_page" size="4" maxlength="4"  value="<?php echo $terme->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $terme->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($terme->get("page_suivante") == $terme->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $terme->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>	