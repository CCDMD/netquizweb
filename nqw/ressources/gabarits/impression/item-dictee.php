
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

				<p><span class="champTitre"><?php echo TXT_SOLUTION_DICTEE ?></span>
					<span class="champValeur"><?php echo $item->getImpression("solution")?></span></p>

				<p><span class="champTitre"><?php echo TXT_RETROACTION_POSITIVE ?></span>
					<span class="champValeur"><?php echo $item->getImpression("retroaction_positive")?></span></p>

				<p><span class="champTitre"><?php echo TXT_RETROACTION_NEGATIVE ?></span>
					<span class="champValeur"><?php echo $item->getImpression("retroaction_negative")?></span></p>
					
				<p><span class="champTitre"><?php echo TXT_POINTS_RETRANCHES_MOT_MAL_ORTHOGRAPHIES ?></span>
					<span class="champValeur"><?php echo $item->getImpression("points_retranches_pub")?></span></p>					
				
				<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
				<span class="champValeur"><?php echo $item->getImpression("remarque")?></span></p>