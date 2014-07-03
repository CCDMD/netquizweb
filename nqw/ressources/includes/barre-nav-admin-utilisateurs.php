		<form name="frmNav" id="frmNav" action="admin.php" method="get">
	<div id="barreNav">
		<input type="hidden" name="demande" value="utilisateur_modifier" />
			<div class="txt"><?php echo TXT_TOUS_LES_UTILISATEURS ?></div>
			<input class="btnSubmit <?php if ($usr->get("page_precedente") == $usr->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $usr->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="usager_page" size="4" maxlength="4"  value="<?php echo $usr->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $usr->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($usr->get("page_suivante") == $usr->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $usr->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>