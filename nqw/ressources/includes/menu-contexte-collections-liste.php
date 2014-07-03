						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_MES_COLLECTIONS ?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierCollectionSelectionnee(); }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuDupliquer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('collection_dupliquer'); }"><?php echo TXT_DUPLIQUER ?></a></li>
								<li id="menuExporter" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('collection_exporter'); }"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li id="menuImprimer" class="inactif"><a href="#"  onclick="if (isLienActif(this)) { soumettreDemandeApercu('collection_imprimer_liste');$('.selectionElement').prop('checked', false); }"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('collection_corbeille_liste'); }"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>								
							</ul>
						</div>