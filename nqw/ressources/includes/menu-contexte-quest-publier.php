						<div class="menuDeroul">
							<p class="menuTitre"><?php echo $quest->get('titre_menu') ?></p>
							<ul>
								<li id="questionnaireVoir"><a href="questionnaires.php?demande=questionnaire_publier_voir&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>" target="voirQuestionnaire"><?php echo TXT_VOIR_LE_QUESTIONNAIRE_PUBLIE ?></a></li>
								<li><a href="questionnaires.php?demande=questionnaire_publier_valider&questionnaire_id_questionnaire=<?php echo $quest->get("id_questionnaire") ?>&item_id_item=<?php echo $item->get("id_item") ?>&demandeRetour=<?php echo $demandeRetour ?>"><?php echo TXT_PUBLIER_LE_QUESTIONNAIRE ?></a></li>
								<li id="questionnaireDesactiver"><a href="#" onclick="desactiverPublication('<?php echo $quest->get("id_questionnaire") ?>', '<?php echo $item->get("id_item") ?>', '<?php echo $demandeRetour ?>', '<?php echo TXT_AVERTISSEMENT_DESACTIVER_PUBLICATON ?>')"><?php echo TXT_DESACTIVER ?></a></li>
							</ul>
						</div>