	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="admin.php" method="get">
		<input type="hidden" name="demande" value="projet_modifier" />
			<div class="txt"><?php echo TXT_PROJET ?></div>
			<input class="btnSubmit <?php if ($projet->get("page_precedente") == $projet->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $projet->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="projet_page" size="4" maxlength="4"  value="<?php echo $projet->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $projet->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($projet->get("page_suivante") == $projet->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $projet->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>