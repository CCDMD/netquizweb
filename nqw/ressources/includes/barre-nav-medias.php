	<div id="barreNav">
		<form name="frmNav" id="frmNav" action="media.php" method="post">
		<input type="hidden" name="demande" value="media_modifier" />
			<div class="txt"><?php echo TXT_MEDIA ?></div>
			<input class="btnSubmit <?php if ($media->get("page_precedente") == $media->get("page_courante")) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" onclick="changerPage('<?php echo $media->get("page_precedente") ?>')" name="btnPrev" value="" />
			<input class="noPage" type="text" name="media_page" size="4" maxlength="4"  value="<?php echo $media->get("page_courante") ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $media->get("pages_total") ?></div>
			<input class="btnSubmit <?php if ($media->get("page_suivante") == $media->get("page_courante")) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" onclick="changerPage('<?php echo $media->get("page_suivante") ?>')" name="btnNext" value="" />
		</form>
	</div>	