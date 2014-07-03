  <categorie>
		<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
		<id_projet><?php echo $categorie->getXML("id_projet")?></id_projet>
		<id_categorie><?php echo $categorie->getXML("id_categorie")?></id_categorie>
		<titre><?php echo $categorie->getXML("titre")?></titre>
		<remarque><?php echo $categorie->getXML("remarque")?></remarque>
		<date_creation><?php echo $categorie->getXML("date_creation")?></date_creation>
		<date_modification><?php echo $categorie->getXML("date_modification")?></date_modification>
  </categorie>

