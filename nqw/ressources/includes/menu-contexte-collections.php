						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $collection->get("titre_menu")?></p>
							<ul>
								<li><a href="bibliotheque.php?demande=collection_dupliquer&collection_id_collection=<?php echo $collection->get("id_collection")?>"><?php echo TXT_DUPLIQUER ?></a></li>
								<li><a href="bibliotheque.php?demande=collection_exporter&collection_id_collection=<?php echo $collection->get("id_collection") ?>&demandeRetour=collection_modifier"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li><a href="#" onclick="soumettreDemandeApercu('collection_imprimer')"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li><a href="#" onclick="soumettreDemande('collection_corbeille')"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
								<li class="section"></li>
								<li><a href="bibliotheque.php?demande=collection_ajouter"><?php echo TXT_AJOUTER_UNE_NOUVELLE_COLLECTION ?></a></li>
							</ul>
						</div>