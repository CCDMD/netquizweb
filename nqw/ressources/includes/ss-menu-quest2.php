	<div id="ssMenu2">
		<ul>
		
		<?php 
		$indexElement = 0;
		$questionnaireCourant = 0;
		$section = 0;
		$sectionOuverte = 0;
		
		foreach ($menu as $menuItem) {

			// ----------------------------------------------------------------------------------------------------			
			// Vérifier si on doit fermer une section
			// ----------------------------------------------------------------------------------------------------
			if ($menuItem->getIdSection() != $section) {
				
				if ($section != "") {
					print "</ul>\n</li>\n";
					
					// Fermer une section
					$sectionOuverte--;
					
					// Réinitialiser section
					$section = 0;
				}
			}
			
			// ----------------------------------------------------------------------------------------------------			
			// Afficher un élément de type questionnaire
			// ----------------------------------------------------------------------------------------------------  
			if ($menuItem->getType() == "questionnaire") {

				// Vérifier si on doit fermer un questionnaire avant d'en démarrer un nouveau
				if ($questionnaireCourant > 0) { 
		?>
		
						<li id="list_<?php echo $indexElement++ ?>" class="ssm-fin no-nest ui-state-disabled"><div id="ssm<?php echo $indexElement ?>" <?php if ($item->get("id_item") == "fin_questionnaire") { ?>class="actif"<?php } ?> ><a href="questionnaires.php?demande=fin_modifier&questionnaire_id_questionnaire=<?php echo $questionnaireCourant ?>"><?php echo TXT_FIN_DU_QUESTIONNAIRE ?></a></div></li>
					</ul>
				</li>
		
				
		<?php 
				} 
				// Indiquer qu'un questionnaire doit être fermé
				$questionnaireCourant = $menuItem->getId();	
		?>
				
				<li id="list_<?php $item->get("id_item") ?>" class="ssm-questionnaire ui-state-disabled">
					<div id="ssm<?php echo $indexElement++ ?>" <?php if ($item->get("id_item") == "" && $quest->get("id_questionnaire") == $menuItem->getId()) { ?>class="actif" <?php } ?>>
						<div class="tools menuContexte">
							<img src="../images/ic-tools.png" alt="" />
							<?php include '../ressources/includes/menu-contexte-quest.php' ?>
						</div>
						<a href="questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=<?php echo $questionnaireCourant ?>"><?php echo $menuItem->getLibelle() ?> (<?php echo $menuItem->getNbSousItem() ?> <?php echo strtolower(TXT_ITEM); if ($menuItem->getNbSousItem() > 1) echo 's' ?>)</a>
					</div>
				</li>
					<li id="list_<?php echo $indexElement++ ?>" class="ssm-questionnaire ui-state-disabled">
				
					<ul id="ssMenu2Items" class="sortable">
					  
						<li id="list_<?php echo $indexElement++ ?>" class="ssm-accueil no-nest ui-state-disabled"><div id="ssm<?php echo $indexElement++ ?>" <?php if ($item->get("id_item") == "accueil") { ?>class="actif"<?php } ?>><a href="questionnaires.php?demande=accueil_modifier&questionnaire_id_questionnaire=<?php echo $questionnaireCourant ?>"><?php echo TXT_PAGE_ACCUEIL ?></a></div></li>
		
				
		<?php
			// ----------------------------------------------------------------------------------------------------
			// Afficher un élément de type 2 - Choix multiples
			// ---------------------------------------------------------------------------------------------------- 
			} elseif ($menuItem->getType() == "item_1" ||
					  $menuItem->getType() == "item_2" ||
					  $menuItem->getType() == "item_3" ||
					  $menuItem->getType() == "item_4" ||
					  $menuItem->getType() == "item_5" ||
					  $menuItem->getType() == "item_6" ||
					  $menuItem->getType() == "item_7" ||
					  $menuItem->getType() == "item_8" ||
					  $menuItem->getType() == "item_9" ||
					  $menuItem->getType() == "item_10" ||
					  $menuItem->getType() == "item_11" ||
					  $menuItem->getType() == "item_12" ||
					  $menuItem->getType() == "item_13" ||
					  $menuItem->getType() == "item_14" 
			) {
		?>
			   
				<li id="list_<?php echo $menuItem->getId() ?>" class="ssm-<?php echo $menuItem->getTypeTxt() ?> no-nest"><div id="ssm<?php echo $indexElement++ ?>" <?php if ($idItem == $menuItem->getId()  ) { ?>class="actif" <?php } ?>><a href="questionnaires.php?demande=item_modifier&questionnaire_id_questionnaire=<?php echo $questionnaireCourant ?>&item_id_item=<?php echo $menuItem->getId() ?>"><?php echo $menuItem->getLibelle() ?></a></div></li>
			
			
		<?php
			// ----------------------------------------------------------------------------------------------------
			// Afficher un élément de type 15 - Section
			// ---------------------------------------------------------------------------------------------------- 
		
			} elseif ($menuItem->getType() == "item_15") {
				
				// Noter qu'une section est ouverte
				$sectionOuverte++;
				$section = $menuItem->getId();
		?>
			
				<li id="list_<?php echo $menuItem->getId() ?>" class="ssm-section"><div id="ssm<?php echo $indexElement++ ?>" <?php if ($idItem == $menuItem->getId()  ) { ?>class="actif" <?php } ?>><a href="questionnaires.php?demande=item_modifier&questionnaire_id_questionnaire=<?php echo $questionnaireCourant ?>&item_id_item=<?php echo $menuItem->getId() ?>"><?php echo $menuItem->getLibelle() ?></a></div>
					<ul>
					

		
		<?php 
			} 
		}

			// ----------------------------------------------------------------------------------------------------
			// Vérifier si on doit fermer une section
			// ----------------------------------------------------------------------------------------------------
			if ($sectionOuverte > 0) {
				print "</ul>\n</li>\n";
			}
		
			// ----------------------------------------------------------------------------------------------------
			// Vérifier si on doit fermer un questionnaire
			// ----------------------------------------------------------------------------------------------------
			if ($questionnaireCourant > 0) { 
		?>

						<li id="list_<?php echo $indexElement++ ?>" class="ssm-fin no-nest ui-state-disabled"><div id="ssm<?php echo $indexElement++ ?>" <?php if ($item->get("id_item") == "fin_questionnaire") { ?>class="actif"<?php } ?> ><a href="questionnaires.php?demande=fin_modifier&questionnaire_id_questionnaire=<?php echo $questionnaireCourant ?>"><?php echo TXT_FIN_DU_QUESTIONNAIRE ?></a></div></li>
					</ul>
				</li>
		
				
		<?php 
				} 
		?>		
		
		</ul>
		
	</div>			