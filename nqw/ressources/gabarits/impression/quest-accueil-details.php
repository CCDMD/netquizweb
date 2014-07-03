			<h2><?php echo TXT_ACCUEIL ?></h2>
			
			<p><span class="champTitre"><?php echo TXT_MEDIA_ACCUEIL_TITRE ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("media_titre")?></span></p>
				
			<p><span class="champTitre"><?php echo TXT_MEDIA_ACCUEIL_TEXTE ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("media_texte")?></span></p>				
	
			<p><span class="champTitre"><?php echo TXT_IMAGE ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("media_image_txt",1)?></span></p>
			
			<p><span class="champTitre"><?php echo TXT_SON ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("media_son_txt",1) ?></span></p>
			
			<p><span class="champTitre"><?php echo TXT_VIDEO ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("media_video_txt",1) ?></span></p>
	
			<p><span class="champTitre"><?php echo TXT_TITRE_QUESTIONNAIRE_PAGE_ACCUEIL ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("titre_long")?></span></p>
	
			<p><span class="champTitre"><?php echo TXT_MOT_DE_BIENVENUE ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("mot_bienvenue")?></span></p>
	
			<p><span class="champTitre"><?php echo TXT_NOTE ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("note")?></span></p>
	
			<p><span class="champTitre"><?php echo TXT_GENERIQUE ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("generique")?></span></p>