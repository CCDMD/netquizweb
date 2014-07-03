  <terme>
		<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
		<id_projet><?php echo $terme->getXML("id_projet")?></id_projet>
		<id_terme><?php echo $terme->getXML("id_terme")?></id_terme>
		<terme><?php echo $terme->getXML("terme")?></terme>
		<variantes><?php echo $terme->getXML("liste_variantes")?></variantes>
		<type_definition><?php echo $terme->getXML("type_definition")?></type_definition>
		<texte><?php echo $terme->getXML("texte")?></texte>
		<url><?php echo $terme->getXML("url")?></url>
		<media_image><?php echo $terme->getXML("media_image")?></media_image>
		<media_son><?php echo $terme->getXML("media_son")?></media_son>
		<media_video><?php echo $terme->getXML("media_video")?></media_video>
		<remarque><?php echo $terme->getXML("remarque")?></remarque>
		<date_creation><?php echo $terme->getXML("date_creation")?></date_creation>
		<date_modification><?php echo $terme->getXML("date_modification")?></date_modification>
  </terme>

