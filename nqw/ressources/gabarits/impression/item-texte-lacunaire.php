
				<h3>I<?php echo $item->get("id_item")?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $item->getTypeItemTxt()?> - <?php echo $item->get("titre") ?></h3>
				
				<p><span class="champTitre"><?php echo TXT_DATE_DE_CREATION ?></span>
					<span class="champValeur"><?php echo $item->getImpression("date_creation",1) ?></span>
					<span class="champTitre padGa25"><?php echo TXT_DATE_DE_MODIFICATION ?></span>
					<span class="champValeur"><?php echo $item->getImpression("date_modification",1) ?></span></p>
				
				<p><span class="champTitre"><?php echo TXT_INFORMATION_COMPLEMENTAIRE . " 1" ?></span>
				   <span class="champValeur"><b><?php echo $item->getImpression("info_comp1_titre")?></b></span>
				   <?php if ($item->get("info_comp1_texte") != "" ) { ?>
				   		<span class="champValeur"><?php echo $item->getImpression("info_comp1_texte")?></span>
				   <?php }?>
				</p>
					
				<p><span class="champTitre"><?php echo TXT_INFORMATION_COMPLEMENTAIRE . " 2" ?></span>
				   <span class="champValeur"><b><?php echo $item->getImpression("info_comp2_titre")?></b></span>
				   <?php if ($item->get("info_comp2_texte") != "" ) { ?>
				   		<span class="champValeur"><?php echo $item->getImpression("info_comp2_texte")?></span>
				   <?php }?>
				</p>

				<p><span class="champTitre"><?php echo TXT_MEDIA_EN_ENTETE ?></span>
				   <?php if ($item->get("media_titre") != "") {?>
				   		<br /><span class="champValeur"><b><?php echo html_entity_decode($item->get("media_titre"))?></b></span>
				   <?php } ?>
				   <?php if ($item->get("media_texte") != "") {?>
  		   		   		<br /><span class="champValeur"><?php echo html_entity_decode($item->get("media_texte"))?></span>
  		   		   	<?php } ?>
  		   		   	<?php if ($item->get("media_titre") == "" && $item->get("media_texte") == "") { ?>
  		   		   		<br /><br /><span class="champTitre2"><?php echo TXT_TEXTE?> : -</span> 		 
  		   		   	<?php } ?>
				</p>
				
				<p><span class="champTitre2"><?php echo TXT_IMAGE ?></span>
					<span class="champValeur"><?php echo $item->getImpression("media_image_txt",1) ?></span></p>
				
				<p><span class="champTitre2"><?php echo TXT_SON ?></span>
					<span class="champValeur"><?php echo $item->getImpression("media_son_txt",1) ?></span></p>
				
				<p><span class="champTitre2"><?php echo TXT_VIDEO ?></span>
					<span class="champValeur"><?php echo $item->getImpression("media_video_txt",1) ?></span></p>
				
				<p><span class="champTitre"><?php echo TXT_ENONCE ?></span>
					<span class="champValeur"><?php echo $item->getImpression("enonce") ?></span></p>
				
				<p><span class="champTitre"><?php echo TXT_TEXTE_LACUNAIRE ?></span>
					<span class="champValeur"><?php echo $item->getImpression("solution") ?></span></p>
					
				<p><span class="champTitre"><?php echo TXT_TYPE_DE_LACUNE ?> : </span>
					<span class="champValeur"><?php echo $item->getTypeLacuneTxt() ?></span></p>					
					
			<?php

				$idx = 1;
				$listeLacunes = array_reverse($item->listeLacunes);
				foreach ($listeLacunes as $lacune) {
			?>
					
					<p><span class="champTitre"><?php echo TXT_LACUNE . " " . $idx ?></span></p>
					
			<?php
					for ($j = 1; $j <= NB_MAX_CHOIX_REPONSES; $j++) {
							
						$cle = "lacune_" . $lacune->get("idx_lacune") . "_reponse_" . $j;

						// Obtenir les valeurs
						$element = $item->getImpression($cle . "_element");
						$retro = $item->getImpression($cle . "_retroaction");
						$reponse = $item->get($cle . "_reponse");
						
						// Déterminer si c'est la bonne réponse
						$bonnereponse = '';
						if ($reponse == 1) {
							$bonnereponse = "(" . TXT_BONNE_REPONSE . ")";
						}						
							
						if ($item->get($cle . "_element") != "" || $item->get($cle . "_retroaction") != "") {
			?>
					
					
							<p><b><?php echo TXT_CHOIX_DE_REPONSE . " " . $j . " " . $bonnereponse ?></b></p>
							
							<p><span><?php echo TXT_ELEMENT ?></span>
							<span class="champValeur"><?php echo $element ?></span></p>
							
							<p><span><?php echo TXT_RETROACTION ?></span>
							<span class="champValeur"><?php echo $retro ?></span></p>
			<?php 
						}
					}	
			
					$idx++;
		
					// Rétro pour la lacune
					if ($item->get("lacune_" . $lacune->get("idx_lacune") . "_retro") != "" ) {
			?>
			
						<p><span class="champTitre"><?php echo TXT_RETROACTIONS_POUR_TOUTES_REPONSES_NON_PREVUES ?> : </span>
						<span class="champValeur"><?php echo $item->getImpression("lacune_" . $lacune->get("idx_lacune") . "_retro") ?></span></p>
			
			<?php 
					}
				}
				
			?>
								
			<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
			<span class="champValeur"><?php echo $item->getImpression("remarque")?></span></p>