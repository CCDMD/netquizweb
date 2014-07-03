						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $terme->get("titre_menu")?></p>
							<ul>
								<li><a href="bibliotheque.php?demande=terme_dupliquer&terme_id_terme=<?php echo $terme->get("id_terme")?>"><?php echo TXT_DUPLIQUER ?></a></li>
								<li><a href="bibliotheque.php?demande=terme_exporter&terme_id_terme=<?php echo $terme->get("id_terme") ?>&demandeRetour=terme_liste"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li><a href="#" onclick="supprimerTerme('<?php echo TXT_VOULEZ_VOUS_SUPPRIMER_CE_TERME ?>', '<?php echo $terme->get("id_terme")?>')"><?php echo TXT_SUPPRIMER ?></a></li>
								<li class="section"></li>
								<li><a href="bibliotheque.php?demande=terme_ajouter"><?php echo TXT_AJOUTER_UN_NOUVEAU_TERME ?></a></li>
							</ul>
						</div>