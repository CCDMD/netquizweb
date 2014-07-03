						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_COLLABORATEURS_INVITES_A_CE_PROJET ?></p>
							<ul>
								<li id="menuInvitesCourriel" class="inactif"><a href="#" onclick="if (isLienActif(this)) { envoiCourrielCollaborateursInvites(this); }"><?php echo TXT_ENVOI_COURRIEL ?></a></li>
								<li id="menuInvitesSupprimer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { supprimerInvitationCollaborateur(this); }"><?php echo TXT_SUPPRIMER_INVITATION ?></a></li>
							</ul>
						</div>