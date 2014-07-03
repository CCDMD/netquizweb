						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_MES_ITEMS ?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierItemSelectionne(); }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuDupliquer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('item_dupliquer'); }"><?php echo TXT_DUPLIQUER ?></a></li>
								<li id="menuSuivi" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('item_suivi_activer'); }"><?php echo TXT_ACTIVER_SUIVI ?><img class="icActiveSuivi" src="../images/ic-star-gris-11px.png" alt="<?php echo TXT_ACTIVER_SUIVI ?>" /></a></li>
								<li id="menuApercu" class="inactif"><a href="#" onclick="if (isLienActif(this)) { apercuWebItemListe(); }"><?php echo TXT_VOIR_APERCU_WEB ?></a></li>
								<li id="menuAjouter" class="inactif"><a href="#" onclick="if (isLienActif(this)) { selectionnerQuestionnaire(); }"><?php echo TXT_AJOUTER_A_UN_QUESTIONNAIRE ?></a></li>
								<li id="menuExporter" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('item_exporter'); }"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li id="menuImprimer" class="inactif"><a href="#"  onclick="if (isLienActif(this)) { soumettreDemandeApercu('item_imprimer_liste');$('.selectionElement').prop('checked', false); }"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('item_corbeille_liste'); }"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
							</ul>
						</div>