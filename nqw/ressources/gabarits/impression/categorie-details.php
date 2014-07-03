			<h1><?php echo TXT_CATEGORIE ?>&nbsp;<?php echo TXT_PREFIX_CATEGORIE . $element->get("id_categorie")?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $element->get("titre") ?></h1>

			<p><span class="champTitre"><?php echo TXT_DATE_DE_CREATION ?></span>
				<span class="champValeur"><?php echo $element->getImpression("date_creation",1) ?></span>
				<span class="champTitre padGa25"><?php echo TXT_DATE_DE_MODIFICATION ?></span>
				<span class="champValeur"><?php echo $element->getImpression("date_modification",1) ?></span></p>

			<p><span class="champTitre"><?php echo TXT_REMARQUE ?></span>
				<span class="champValeur"><?php echo $element->getImpression("remarque") ?></span></p>