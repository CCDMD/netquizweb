						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_MES_TERMES ?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierTermeSelectionne(); }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuDupliquer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('terme_dupliquer'); }"><?php echo TXT_DUPLIQUER ?></a></li>
								<li id="menuExporter" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('terme_exporter'); }"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { supprimerListeTermes(); }"><?php echo TXT_SUPPRIMER ?></a></li>								
							</ul>
						</div>