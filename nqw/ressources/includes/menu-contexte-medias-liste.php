						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_MES_MEDIAS ?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierMediaSelectionne(); }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuSuivi" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('media_suivi_activer'); }"><?php echo TXT_ACTIVER_SUIVI ?><img class="icActiveSuivi" src="../images/ic-star-gris-11px.png" alt="<?php echo TXT_ACTIVER_SUIVI ?>" /></a></li>
								<li id="menuImprimer" class="inactif"><a href="#"  onclick="if (isLienActif(this)) { soumettreDemandeApercu('media_imprimer_liste');$('.selectionElement').prop('checked', false); }"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('media_corbeille_liste'); }"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
							</ul>
						</div>						