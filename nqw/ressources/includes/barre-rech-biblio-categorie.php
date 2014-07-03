	<div id="barreNav">
		<form name="frmRech" id="frmRech" action="bibliotheque.php" method="get">
		        <input type="hidden" name="demande" value="categorie_recherche" />
				<input type="text" name="chaine" id="rechMots" size="50" maxlength="150"  value="<?php echo $chaineRechCategorie ?>" placeholder="<?php echo TXT_INSCRIRE_MOTSCLES ?>" />
				<input class="btnReset" name="btnRechRes" id="btnRechRes" type="button" value="&nbsp;" onclick="envoiDemandeBiblio('categorie_recherche_initialiser')" />
				<input class="btnSubmit" name="btnRechSub" id="btnRechSub" type="submit" value="<?php echo TXT_CHERCHER ?>" />
		</form>
	</div>
