<div class="boxStd">
	<div class="boxTitre"><p>Choisir des items de la biblioth&egrave;que - Nom du questionnaire </p></div>
		<form id="frms" name="frms" action="">
		<div class="boxContenu">
			<?php include '../ressources/includes/barre-rech.php' ?>
			<div class="boxPrincipal">
				<div class="filAriane"><h2><img src="../images/ic-items.png" alt="<?php echo TXT_MES_ITEMS ?>" /><?php echo TXT_MES_ITEMS ?></h2></div>
				<table class="tblListe">
					<tr class="tblNav">
						<td colspan="9">
							<div class="flGa">
							  <select name="itemTypes">
								<option value="Tous">Afficher tous les types</option>
								<option value="Associations">Associations</option>
								<option value="ChoixMultiples">Choix multiples</option>
								<option value="Classement">Classement</option>
								<option value="Damier">Damier</option>
								<option value="Developpement">D&eacute;veloppement</option>
								<option value="Dictee">Dict&eacute;e</option>
								<option value="Marquage">Marquage</option>
								<option value="Ordre">Mise en ordre</option>
								<option value="ReponseBreve">R&eacute;ponse br&egrave;ve</option>
								<option value="ReponsesMultiples">R&eacute;ponses multiples</option>
								<option value="Lacunaire">Texte lacunaire</option>
								<option value="VraiFaux">Vrai ou faux</option>
								<option value="ZonesIdentifier">Zones &agrave; identifier</option>
								<option value="Page">Page</option>
							  </select>
</div>
							<div class="flDr">
								<?php include '../ressources/includes/table-nav-haut.php' ?>
							</div>
						</td>
					</tr>
					<tr>
						<th class="cCheck"><input class="noBord" type="checkbox" name="checkbox" value="checkbox" onclick="checkedAll(this.form.checkbox);" /></th>
						<th class="cCode"><a href="#">Code</a></th>
						<th class="c3"><a href="#">Titre</a></th>
						<th class="c4"><a href="#">Type</a></th>
						<th class="c5"><a href="#">Cat&eacute;gorie</a></th>
						<th class="c6"><a href="#">Remarque</a></th>
						<th class="c7"><a href="#">Date de modification</a></th>
						<th class="c8"><a href="#"><img src="../images/ic-star-gris.png" alt="" /></a></th>
						<th class="c9 last"><a href="#"><img src="../images/ic-link-2.png" alt="" /></a></th>
					</tr>
					<tr>
						<td><input class="noBord" type="checkbox" name="checkbox" value="checkbox" /></td>
						<td>I1</td>
						<td class="alGa"><a href="bibliotheque.php?demande=item_modifier&id=1">Succ&egrave;s du Box Office</a></td>
						<td>Associations</td>
						<td>Lorem</td>
						<td class="alGa">Lorem</td>
						<td>2009-11-19 / 10h30</td>
						<td><img src="../images/ic-star-jaune.png" alt="" /></td>
						<td class="last"><img src="../images/ic-link.png" alt="" /></td>
					</tr>
					<tr>
						<td><input class="noBord" type="checkbox" name="checkbox" value="checkbox" /></td>
						<td>I2</td>
						<td class="alGa"><a href="bibliotheque.php?demande=item_modifier&id=2">Succ&egrave;s du Box Office</a></td>
						<td>Choix multipes</td>
						<td>Lorem</td>
						<td class="alGa">Lorem</td>
						<td>2009-11-19 / 10h30</td>
						<td><img src="../images/ic-star-jaune.png" alt="" /></td>
						<td class="last"><img src="../images/ic-link.png" alt="" /></td>
					</tr>
					<tr>
						<td><input class="noBord" type="checkbox" name="checkbox" value="checkbox" /></td>
						<td>I3</td>
						<td class="alGa"><a href="bibliotheque.php?demande=item_modifier&id=3">Succ&egrave;s du Box Office</a></td>
						<td>Classement</td>
						<td>Lorem</td>
						<td class="alGa">Lorem</td>
						<td>2009-11-19 / 10h30</td>
						<td><img src="../images/ic-star-jaune.png" alt="" /></td>
						<td class="last"><img src="../images/ic-link.png" alt="" /></td>
					</tr>
					<tr class="lgLast tblNav">
						<td colspan="9" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="bottom">
			<input class="btnReset" name="btnReset" id="btnReset1" type="reset" value="<?php echo TXT_ANNULER ?>"  />
			<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_AJOUTER ?>" />
		</div>
	</form>		  
</div>

