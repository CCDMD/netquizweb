
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
				
				<h4><?php echo TXT_CHOIX_DE_REPONSES ?></h4>
				
				<?php
						if ($item->get("reponse_total") == "" || $item->get("reponse_total") == 0) {
							echo IMPRESSION_HTML_AUCUNE_VALEUR;
						} else {
					
							echo IMPRESSION_HTML_PREFIX_VALEUR_DEUX_LIGNES;
							for ($i =1; $i <= $item->get("reponse_total"); $i++ ) {
								
								// Ajouter l'information pour chaque choix de réponse
								echo "<h5>" . TXT_CHOIX . " $i</h5>\n";
								
								
								// Élément
								if ($item->get("type_elements1") == "texte") {
									echo "<p><span class='champTitre'>" . TXT_ELEMENT . "</span><span class='champValeur'>" . $item->getImpression("reponse_" . $i . "_element") .  "</span></p>\n";
								} else {

									// Charger le média pour obtenir le titre et les infos
									$img = $item->get("reponse_" . $i . "_element");
									
									echo "<p><span class='champTitre'>" . TXT_ELEMENT . "</span><br />\n";
									
									?>
									
									<img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="<?php echo TXT_PREFIX_MEDIA . $img ?>" />
									
									<?php 
									
									echo "<br /><span class='champValeur'>". TXT_MEDIA . " " . TXT_PREFIX_MEDIA . $img .  "</span></p>";
								}
								
								// Élément associé
								if ($item->get("type_elements2") == "texte") {
									echo "<p><span class='champTitre'>" . TXT_ELEMENT_ASSOCIE . "</span><span class='champValeur'>" . $item->getImpression("reponse_" . $i . "_element_associe") .  "</span></p>\n";
								} else {

									// Charger le média pour obtenir le titre et les infos
									$img = $item->get("reponse_" . $i . "_element_associe");
									
									echo "<p><span class='champTitre'>" . TXT_ELEMENT_ASSOCIE . "</span><br />\n";
									
									?>
									
									<img class="itemMediaImg" src="media.php?demande=media_afficher&media_id_media=<?php echo $img ?>" alt="<?php echo TXT_PREFIX_MEDIA . $img ?>" />
									
									<?php 
									
									echo "<br /><span class='champValeur'>". TXT_MEDIA . " " . TXT_PREFIX_MEDIA . $img .  "</span></p>";
								}								
																
								echo "<p><span class='champTitre'>" . TXT_RETROACTION_POSITIVE . "</span><span class='champValeur'>" . $item->getImpression("reponse_" . $i . "_retroaction") . "</span></p>\n";
								echo "<p><span class='champTitre'>" . TXT_RETROACTION_NEGATIVE . "</span><span class='champValeur'>" . $item->getImpression("reponse_" . $i . "_retroaction_negative") . "</span></p>\n";
								echo "<br />\n";
							}
							
							echo IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;
						}
				?>
			
				<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
				<span class="champValeur"><?php echo $item->getImpression("remarque")?></span></p>	
