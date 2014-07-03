						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $langue->get("titre_menu")?></p>
							<ul>
								<li><a href="bibliotheque.php?demande=langue_dupliquer&langue_id_langue=<?php echo $langue->get("id_langue")?>"><?php echo TXT_DUPLIQUER ?></a></li>
								<li><a href="bibliotheque.php?demande=langue_exporter&langue_id_langue=<?php echo $langue->get("id_langue") ?>&demandeRetour=langue_modifier"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li><a href="#" onclick="soumettreDemandeApercu('langue_imprimer')"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li><a href="#" onclick="soumettreDemande('langue_corbeille')"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
								<li class="section"></li>
								<li><a href="bibliotheque.php?demande=langue_ajouter"><?php echo TXT_AJOUTER_UNE_NOUVELLE_LANGUE ?></a></li>
							</ul>
						</div>