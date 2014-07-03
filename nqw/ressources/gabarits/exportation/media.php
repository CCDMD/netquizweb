  <media>
		<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
		<id_projet><?php echo $media->getXML("id_projet")?></id_projet>
		<id_media><?php echo $media->getXML("id_media")?></id_media>
		<titre><?php echo $media->getXML("titre")?></titre>
		<remarque><?php echo $media->getXML("remarque")?></remarque>
		<description><?php echo $media->getXML("description")?></description>
		<type><?php echo $media->getXML("type")?></type>
		<source><?php echo $media->getXML("source")?></source>
		<fichier><?php echo $media->getXML("fichier")?></fichier>
		<url><?php echo $media->getXML("url")?></url>
		<suivi><?php echo $media->getXML("suivi")?></suivi>
		<date_creation><?php echo $media->getXML("date_creation")?></date_creation>
		<date_modification><?php echo $media->getXML("date_modification")?></date_modification>
  </media>

