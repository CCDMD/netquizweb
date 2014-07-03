
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
					<span class="champValeur"><?php echo $item->getImpression("enonce")?></span></p>

<?php
				// Parcourir les classeurs
				for ($i = 1; $i <= NB_MAX_CLASSEURS; $i++) {
					if ($item->get("classeur_" . $i . "_statut") == 1) {
?>			
						<hr>

						<!--  Classeur début -->
						<p><span class="champTitre2"><?php echo TXT_CLASSEUR . " " . $i ?></span>
						
<?php 					if ($item->get("type_elements1") == "texte") {
?>							
							<!-- Onglet du classeur - texte -->
							<span class="champValeur"><?php echo $item->getImpression("classeur_" . $i . "_titre") ?></span></p>
<?php 					} else { 
?>
							<!-- Onglet du classeur - image -->
							<br /><img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $item->get("classeur_" . $i . "_titre") ?>" alt="<?php echo TXT_PREFIX_MEDIA . $item->get("classeur_" . $i . "_titre") ?>" />
								
<?php 					} ?>

						<!-- Rétro positive du classeur -->
						<p><span class="champTitre2"><?php echo TXT_RETROACTION_POSITIVE ?></span>
						<span class="champValeur"><?php echo $item->getImpression("classeur_" . $i . "_retroaction") ?></span></p>	
						
						<!-- Rétro négative du classeur -->
						<p><span class="champTitre2"><?php echo TXT_RETROACTION_NEGATIVE ?></span>
						<span class="champValeur"><?php echo $item->getImpression("classeur_" . $i . "_retroaction_negative") ?></span></p>
						
						<!--  Classeur fin -->
						
<?php 			
						// Parcourir les éléments
						for ($j = 1; $j < NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++) {
							if ($item->get("classeur_" . $i . "_element_" . $j . "_statut") == 1) {
?>

								<!--  Élément début -->
								<p><span class="champTitre2"><?php echo TXT_ELEMENT . " " . $j ?></span>
								
								
<?php							if ($item->get("type_elements2") == "texte") {
?>							
									<!-- Élément - texte -->
									<span class="champValeur">	<?php echo $item->getImpression("classeur_" . $i . "_element_" . $j . "_texte") ?></span></p>
<?php							} else { 
?>
									<!-- Élément - image -->
									<br /><img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $item->get("classeur_" . $i . "_element_" . $j . "_texte") ?>" alt="<?php echo TXT_PREFIX_MEDIA . $item->get("classeur_" . $i . "_element_" . $j . "_texte") ?>" />
<?php 							} ?> 								
								
								
								<!--  Élément fin -->
								
<?php
								// Parcourir les rétros
								for ($k = 1; $k < NB_MAX_CLASSEURS; $k++) {

									if ($item->get("classeur_" . $k . "_statut") == 1) {

?>
									<!--  Rétro début -->
									<p><span class="champTitre2"><?php echo TXT_RETROACTION . " " . $k ?></span>
									<span class="champValeur">
									
<?php 								if ($item->get("type_elements1") == "texte") {
?>							
										<!-- Préfix de la rétro - texte -->
										<span class="champValeur"><?php echo $item->getImpression("classeur_" . $k . "_titre") ?></span></p>
<?php 								} else { 
?>
										<!-- Préfix de la rétro - image -->
										<br /><img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $item->get("classeur_" . $k . "_titre") ?>" alt="<?php echo TXT_PREFIX_MEDIA . $item->get("classeur_" . $k . "_titre") ?>" />
<?php 								} ?> 
									
									
									<?php echo $item->getImpression("classeur_" . $i . "_element_" . $j . "_retro_" . $k) ?></span></p>
									<!--  Rétro fin -->

<?php 
									}
								}
							}
						}
					}	
				}
?>						
									
					
				<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
				<span class="champValeur"><?php echo $item->getImpression("remarque")?></span></p>			
				
