						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $media->get("titre_menu") ?></p>
							<ul>
								<li><a href="#" onclick="activerSuivi()"><?php echo TXT_ACTIVER_SUIVI ?><img class="icActiveSuivi" src="../images/ic-star-gris-11px.png" alt="<?php echo TXT_ACTIVER_SUIVI ?>" /></a></li>
								<li><a href="#" onclick="soumettreDemandeApercu('media_imprimer')"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li><a href="#" onclick="soumettreDemande('media_corbeille')"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
								<li class="section"></li>
								<li><a href="media.php?demande=media_ajouter"><?php echo TXT_AJOUTER_UN_NOUVEAU_MEDIA ?></a></li>								
								
							</ul>
						</div>