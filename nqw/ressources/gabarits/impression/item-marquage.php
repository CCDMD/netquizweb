
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

				<p><span class="champTitre"><?php echo TXT_POINTS_RETRANCHES_MOT_MAL_ORTHOGRAPHIES ?></span>
					<span class="champValeur"><?php echo $item->getImpression("points_retranches")?></span></p>					
		
				<p><hr /></p>
		
				<p><span class="champTitre"><?php echo TXT_COULEURS ?></span></p>


				<?php 	
						$couleurTitre = array();				
				
						// Analyser les couleurs (obtenir le statut)
						$item->analyserCouleurs();
						
						// Cas spécial pour mauvaise réponse
						$couleurTitre[COULEUR_MAUVAISE_REPONSE] = TXT_MAUVAISE_REPONSE;
						
						for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {
	
							// Obtenir la couleur
							$couleur = $item->get("couleur_" . $i . "_couleur");
							
							// Prendre note du nom de la couleur
							$couleurTitre[$couleur] = $item->get("couleur_" . $i . "_titre");
	
							if ($item->get("couleur_" . $i . "_statut") == "1") { 
						
								// Afficher la liste des couleurs		
				?>
								
								<p> 
									<span class="carre" style="background: #<?php echo $couleur ?>"></span>
									<span class='champTitre'><?php echo $item->get("couleur_" . $i . "_titre") ?></span>
								</p>
								<p>
									<span class='champTitre'><?php echo TXT_RETROACTION ?></span>
									<span class="champValeur"><?php echo $item->getImpression("couleur_" . $i . "_retroaction")?></span>
								</p>
								<p>
									<span class='champTitre'><?php echo TXT_RETROACTION_NEGATIVE ?></span>
									<span class="champValeur"><?php echo $item->getImpression("couleur_" . $i . "_retroaction_negative")?></span>
								</p>
								<p>
									<span class='champTitre'><?php echo TXT_RETROACTION_POUR_REPONSE_INCOMPLETE ?></span>
									<span class="champValeur"><?php echo $item->getImpression("couleur_" . $i . "_retroaction_incomplete")?></span>
								</p>
					
				<?php 		} 	
						} 
				?>		
					
										
				
				<p><hr /></p>	

				<p><span class="champTitre"><?php echo TXT_TEXTE_AVEC_MARQUES ?></span>
				<span class="champValeur"><?php echo $item->getImpression("texte")?></span></p>
										
				<p><hr /></p>	
					
				<p><span class="champTitre"><?php echo TXT_MARQUES ?></span>
										
				<?php	
						// Obtenir les marques courantes + rétros
						$item->analyserMarques();

						// Traiter chacune des marques et rétros
						$idx = 0;
						foreach ($item->listeMarques as $marque) {
							$idx++;
						
							// Afficher une marque 
				?>
							
							<p> <span class="champTitre"><?php echo TXT_MARQUE . $idx . " - " . $marque->get('texte') ?></span>
								<span class="carre" style="background: #<?php echo $marque->get('couleur') ?>"></span>
							</p> 
							
							<p><span class="champTitre"><?php echo TXT_RETROACTION?></span></p>
							
				<?php 
								// Liste des rétros pour cette marque
								foreach ($marque->listeRetros as $retro) {
				?>

									<p> 
										<span class="carre" style="background: #<?php echo $retro->get("couleur") ?>"></span><?php echo $couleurTitre[$retro->get("couleur")] ?> :
										<span class="champValeur"><?php echo $retro->get('retro') ?></span>
									</p>
							
				<?php 
								}							
						} 
				?>
					
				</p>					
					
				<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
				<span class="champValeur"><?php echo $item->getImpression("remarque")?></span></p>				
				
