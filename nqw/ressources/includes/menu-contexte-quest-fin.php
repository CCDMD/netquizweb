						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $quest->get("titre_menu") ?> : <?php echo $item->get("titre") ?></p>
							<ul>
								<li><a href="questionnaires.php?demande=questionnaire_apercu&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&demandeRetour=fin_modifier"><?php echo TXT_VOIR_APERCU_WEB ?></a></li>
								<li><a href="#" onclick="afficherApercu('questionnaires.php?demande=questionnaire_fin_imprimer&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>');"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li class="section"></li>
								<li><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=15"><?php echo TXT_AJOUTER_UNE_SECTION ?></a></li>
								<li><a href="#" onclick="selectionnerItems()"><?php echo TXT_AJOUTER_DES_ITEMS_DE_LA_BIBLIOTHEQUE ?></a></li>
								<li class="items2cols"><a href="#"><?php echo TXT_AJOUTER_UN_NOUVEL_ITEM ?></a>
									<ul class="flGa">
										<li class="ssm-associations"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=1"><?php echo TXT_ASSOCIATIONS ?></a></li>						
										<li class="ssm-choix-multiples"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=2"><?php echo TXT_CHOIX_MULTIPLES ?></a></li>
										<li class="ssm-classement"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=3"><?php echo TXT_CLASSEMENT ?></a></li>
										<li class="ssm-damier"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=4"><?php echo TXT_DAMIER ?></a></li>
										<li class="ssm-developpement"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=5"><?php echo TXT_DEVELOPPEMENT ?></a></li>
										<li class="ssm-dictee"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=6"><?php echo TXT_DICTEE ?></a></li>
										<li class="ssm-marquage"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=7"><?php echo TXT_MARQUAGE ?></a></li>
									</ul>
									<ul class="flDr">
										<li class="ssm-mise-ordre"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=8"><?php echo TXT_MISE_EN_ORDRE ?></a></li>
										<li class="ssm-reponse-breve"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=9"><?php echo TXT_REPONSE_BREVE ?></a></li>
										<li class="ssm-reponses-multiples"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=10"><?php echo TXT_REPONSES_MULTIPLES ?></a></li>
										<li class="ssm-texte-lacunaire"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=11"><?php echo TXT_TEXTE_LACUNAIRE ?></a></li>
										<li class="ssm-vrai-faux"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=12"><?php echo TXT_VRAI_OU_FAUX ?></a></li>
										<li class="ssm-zones-identifier"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=13"><?php echo TXT_ZONES_A_IDENTIFIER ?></a></li>
										<li class="ssm-page"><a href="questionnaires.php?demande=item_ajouter&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire")?>&item_type_item=14"><?php echo TXT_PAGE ?></a></li>
									</ul>
								</li>
								<li class="clear"></li>
							</ul>
						</div>
						

