	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="bibliotheque.php" method="post">
		<input type="hidden" name="demande" value="item_modifier" />
			<div class="txt"><?php echo TXT_ITEM ?></div>
			<input class="btnSubmit <?php if ($item->get("page_precedente") == $item->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $item->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="item_page" size="4" maxlength="4"  value="<?php echo $item->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $item->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($item->get("page_suivante") == $item->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $item->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>	