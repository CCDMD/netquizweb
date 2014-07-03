                        <div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_COLLABORATEURS_INVITES_A_CE_PROJET ?></p>
							<ul>
								<li id="menuActuelsCourriel"  class="inactif"><a href="#" onclick="if (isLienActif(this)) { envoiCourrielCollaborateursActuels(this); }"><?php echo TXT_ENVOI_COURRIEL ?></a></li>
								<li id="menuActuelsResponsable"  class="inactif"><a href="#" onclick="if (isLienActif(this)) { remplacerResponsable(this); }"><?php echo TXT_NOMMER_RESPONSABLE ?></a></li>
								<li id="menuActuelsSupprimer"  class="inactif"><a href="#" onclick="if (isLienActif(this)) { retirerCollaborateursActuels(this); }"><?php echo TXT_RETIRER_ACCESS ?></a></li>
							</ul>
						</div>
