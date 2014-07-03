	<div id="barreNav">
		<form name="frmRech" id="frmRech" action="media.php" method="get">
		        <input type="hidden" name="demande" value="media_selectionner_recherche" />
		        <input type="hidden" name="mode" value="fenetre" />
		        <input type="hidden" name="filtre_type_media" value="<?php echo $pageInfos['filtre_type_media'] ?>" />
				<input type="text" name="chaine" id="rechMots" size="50" maxlength="150"  value="<?php echo $chaineRechMediaSel ?>" placeholder="<?php echo TXT_INSCRIRE_MOTSCLES ?>" />
				<input class="btnReset" name="btnRechRes" id="btnRechRes" type="button" value="&nbsp;" onclick="reinitialiserRecherche()" />
				<input class="btnSubmit" name="btnRechSub" id="btnRechSub" type="submit" value="<?php echo TXT_CHERCHER ?>" />
		</form>
	</div>
