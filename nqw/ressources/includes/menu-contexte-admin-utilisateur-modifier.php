						<div class="menuDeroul">
							<ul class="sansTitre">
								<li><a href="mailto:<?php echo $usr->get("courriel") ?>"><?php echo TXT_ENVOYER_UN_COURRIEL ?></a></li>
								<li><a href="admin.php?demande=utilisateur_corbeille&usager_id_usager=<?php echo $usr->get("id_usager") ?>"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
							</ul>
						</div>
