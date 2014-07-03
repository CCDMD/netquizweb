

									<tr class="tblNav">
									<td class="alCe">
										<div class="menuContexte">
											<img src="../images/ic-tools.png" alt="" />
											<div class="menuDeroul">
												<p class="menuTitre"><?php echo TXT_CORBEILLE ?></p>
												<ul>
													<li id="actionRecuperer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('corbeille_recuperer'); }"><?php echo TXT_RECUPERER_ELEMENT ?></a></li>
													<li id="actionSupprimer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { supprimerElementsCorbeille(); }"><?php echo TXT_SUPPRIMER_DEFINITIVEMENT ?></a></li>
												</ul>
											</div>
										</div>
									</td>
									<td colspan="3" class="alDr"><?php include '../ressources/includes/table-nav-haut.php' ?></td>
									</tr>
									<tr>
										<th class="cCheck"><input class="noBord" id="selectall" type="checkbox" name="checkbox" value="checkbox" onclick="verifierSelection()"/></th>
										<th class="cCode"><a href="#" class="<?php echo $corbeille->get('tri_id_element') ?>" onclick="changerTriCorbeille('id_element')"><?php echo TXT_CODE ?></a></th>
										<th class="cType"><a href="#" class="<?php echo $corbeille->get('tri_type') ?>" onclick="changerTriCorbeille('type')"><?php echo TXT_TYPE ?></a></th>
										<th class="c4 last"><a href="#" class="<?php echo $corbeille->get('tri_titre') ?>" onclick="changerTriCorbeille('titre')"><?php echo TXT_TITRE?></a></th>
									</tr>
									
									<?php foreach($listeCorbeille as $element){ ?> 
									
										<tr>
										<td class="cCheck"><input class="noBord selectionElement" type="checkbox" name="elements_selection_<?php echo $element->get("id_prefix") . $element->get("id_element")?>" value="<?php echo $element->get("id_prefix")?>" /></td>
										<td><?php echo $element->get('id_prefix')?></td>
										<td><?php echo $element->get('type') ?></td>
										<td class="last"><?php echo $element->get('titre') ?></td>
										</tr>

									<?php }?>

									<tr class="tblNav">
									<td colspan="4" class="alDr"><?php include '../ressources/includes/table-nav-bas.php' ?></td>
									</tr>

