	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="bibliotheque.php" method="post">
		<input type="hidden" name="demande" value="collection_modifier" />
			<div class="txt"><?php echo TXT_COLLECTION ?></div>
			<input class="btnSubmit <?php if ($collection->get("page_precedente") == $collection->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $collection->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="collection_page" size="4" maxlength="4"  value="<?php echo $collection->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $collection->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($collection->get("page_suivante") == $collection->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $collection->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>	