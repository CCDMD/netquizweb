	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="bibliotheque.php" method="post">
		<input type="hidden" name="demande" value="categorie_modifier" />
			<div class="txt"><?php echo TXT_CATEGORIE ?></div>
			<input class="btnSubmit <?php if ($categorie->get("page_precedente") == $categorie->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $categorie->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="categorie_page" size="4" maxlength="4"  value="<?php echo $categorie->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $categorie->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($categorie->get("page_suivante") == $categorie->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $categorie->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>	