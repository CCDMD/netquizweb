						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_MES_PROJETS?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierProjetSelectionne(); }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuTerminerCollaboration" class="inactif"><a href="#" onclick="if (isLienActif(this)) { terminerCollaborationProjetSelectionne(); }"><?php echo TXT_ME_RETIRER_DE_LA_LISTE_DES_COLLABORATEURS ?></a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { mettreProjetsCorbeille(); }"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
							</ul>
						</div>