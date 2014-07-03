						<div class="menuDeroul">
							<p class="menuTitre"><?php echo TXT_MES_QUESTIONNAIRES?></p>
							<ul>
								<li id="menuModifier" class="inactif"><a href="#" onclick="if (isLienActif(this)) { modifierQuestionnaireSelectionne(); }"><?php echo TXT_MODIFIER ?></a></li>
								<li id="menuDupliquer" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('questionnaire_dupliquer'); }"><?php echo TXT_DUPLIQUER ?></a></li>
								<li id="menuSuivi" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('questionnaire_suivi_activer'); }"><?php echo TXT_ACTIVER_SUIVI ?><img class="icActiveSuivi" src="../images/ic-star-gris-11px.png" alt="<?php echo TXT_ACTIVER_SUIVI ?>" /></a></li>
								<li id="menuApercu" class="inactif"><a href="#" onclick="if (isLienActif(this)) { apercuQuestionnaireSelectionne(); }"><?php echo TXT_VOIR_APERCU_WEB ?></a></li>
								<li id="menuExporter" class="inactif"><a href="#" onclick="if (isLienActif(this)) { exporterQuestionnaireSelectionne(); }"><?php echo TXT_EXPORTER_XML ?></a></li>
								<li id="menuTelecharger" class="inactif"><a href="#" onclick="if (isLienActif(this)) { telechargerQuestionnaireSelectionne(); }"><?php echo TXT_TELECHARGER_QUESTIONNAIRE_WEB ?></a></li>
								<li id="menuImprimer" class="inactif"><a href="#"  onclick="if (isLienActif(this)) { imprimerQuestionnaireSelectionne(); }"><?php echo TXT_IMPRIMER ?>...</a></li>
								<li id="menuCorbeille" class="inactif"><a href="#" onclick="if (isLienActif(this)) { soumettreDemande('questionnaire_corbeille'); }"><?php echo TXT_METTRE_A_LA_CORBEILLE ?></a></li>
							</ul>
						</div>