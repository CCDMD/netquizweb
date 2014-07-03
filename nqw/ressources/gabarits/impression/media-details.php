
				<h3><?php echo TXT_MEDIA ?>&nbsp;<?php echo TXT_PREFIX_MEDIA . $element->get("id_media")?>&nbsp;-&nbsp;<?php echo $element->get("titre") ?></h3>

				<p><span class="champTitre"><?php echo TXT_DATE_DE_CREATION ?></span>
					<span class="champValeur"><?php echo $element->getImpression("date_creation",1) ?></span>
					<span class="champTitre padGa25"><?php echo TXT_DATE_DE_MODIFICATION ?></span>
					<span class="champValeur"><?php echo $element->getImpression("date_modification",1) ?></span></p>
				
				<p><span class="champTitre"><?php echo TXT_SOURCE ?> : </span>
					<span class="champValeur"><?php echo ucfirst($element->get("source")) ?></span></p>
				
				<?php if ($element->get("source") == "fichier") { ?>
					
					<p><span class="champTitre"><?php echo TXT_FICHIER ?></span>
						<span class="champValeur"><?php echo $element->getImpression("fichier_usager") ?></span></p>
				
					<?php if ($element->get("type") == "image") { ?>
						
						<p><span class="champTitre"><?php echo TXT_APERCU ?></span><br /><br />
							<img src="media.php?demande=media_afficher&media_id_media=<?php echo $element->get("id_media") ?>" <?php if ($element->get("media_largeur") > "300") { ?> width="300" <?php }?> alt=""/></p>
						
					<?php } ?>
					
				<?php } ?>
				
				<?php if ($element->get("source") == "web") { ?>
					
					<p><span class="champTitre"><?php echo TXT_FICHIER ?></span>
						<span class="champValeur"><?php echo $element->getImpression("url",1) ?></span></p>
					
					<?php if ($element->get("type") == "image") { ?>
						
						<p><span class="champTitre"><?php echo TXT_APERCU ?> : </span>
							<span class="champValeur"><?php echo html_entity_decode($element->get("url")) ?></span></p>
						
					<?php } ?>
					
				<?php } ?>
				
				<p><span class="champTitre"><?php echo TXT_DESCRIPTION ?></span>
					<span class="champValeur"><?php echo $element->getImpression("description") ?></span></p>
				
				<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
					<span class="champValeur"><?php echo $element->getImpression("remarque") ?></span></p>
			
