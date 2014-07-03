						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_TOUS_LES_UTILISATEURS ?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierUtilisateurSelectionne() }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuApprouver" class="inactif"><a href="#" onclick="if (isLienActif(this)) { approuverUtilisateurs() }"><?php echo TXT_APPROUVER_LA_DEMANDE ?></a></li>
								<li id="menuCourriel"  class="inactif"><a href="#" onclick="if (isLienActif(this)) { envoiCourrielUtilisateurs() }"><?php echo TXT_ENVOI_COURRIEL ?></a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('utilisateur_corbeille'); }"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
							</ul>
						</div>