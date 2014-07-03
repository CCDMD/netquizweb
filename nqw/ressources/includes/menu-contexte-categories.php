						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $categorie->get("titre_menu")?></p>
							<ul>
								<li><a href="bibliotheque.php?demande=categorie_dupliquer&categorie_id_categorie=<?php echo $categorie->get("id_categorie")?>"><?php echo TXT_DUPLIQUER ?></a></li>
								<li><a href="bibliotheque.php?demande=categorie_exporter&categorie_id_categorie=<?php echo $categorie->get("id_categorie") ?>&demandeRetour=categorie_modifier"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li><a href="#" onclick="soumettreDemandeApercu('categorie_imprimer')"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li><a href="#" onclick="soumettreDemande('categorie_corbeille')"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
								<li class="section"></li>
								<li><a href="bibliotheque.php?demande=categorie_ajouter">Ajouter une nouvelle categorie</a></li>
							</ul>
						</div>