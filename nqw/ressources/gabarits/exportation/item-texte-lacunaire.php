
  <item>
	<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
	<id_item><?php echo $item->getXML("id_item")?></id_item>
	<id_projet><?php echo $item->getXML("id_projet")?></id_projet>
	<titre><?php echo $item->getXML("titre")?></titre>
	<enonce><?php echo $item->getXML("enonce")?></enonce>
	<type_item>11</type_item>		
	<info_comp1_titre><?php echo $item->getXML("info_comp1_titre")?></info_comp1_titre>
	<info_comp1_texte><?php echo $item->getXML("info_comp1_texte")?></info_comp1_texte>
	<info_comp2_titre><?php echo $item->getXML("info_comp2_titre")?></info_comp2_titre>
	<info_comp2_texte><?php echo $item->getXML("info_comp2_texte")?></info_comp2_texte>
	<media_titre><?php echo $item->getXML("media_titre")?></media_titre>
	<media_texte><?php echo $item->getXML("media_texte")?></media_texte>
	<media_image><?php echo $item->getXML("media_image")?></media_image>
	<media_son><?php echo $item->getXML("media_son")?></media_son>
	<media_video><?php echo $item->getXML("media_video")?></media_video>
	<id_categorie><?php echo $item->getXML("id_categorie")?></id_categorie>
	<suivi><?php echo $item->getXML("suivi")?></suivi>
	<ponderation><?php echo $item->getXML("ponderation")?></ponderation>
	<afficher_solution><?php echo $item->getXML("afficher_solution")?></afficher_solution>
	<points_retranches><?php echo $item->getXML("points_retranches")?></points_retranches>
	<majmin><?php echo $item->getXML("majmin")?></majmin>
	<ponctuation><?php echo $item->getXML("ponctuation")?></ponctuation>
	<type_elements1><?php echo $item->getXML("type_elements1")?></type_elements1>
	<type_elements2><?php echo $item->getXML("type_elements2")?></type_elements2>
	<demarrer_media><?php echo $item->getXML("demarrer_media")?></demarrer_media>
	<reponse_bonne_message><?php echo $item->getXML("reponse_bonne_message")?></reponse_bonne_message>
	<reponse_mauvaise_message><?php echo $item->getXML("reponse_mauvaise_message")?></reponse_mauvaise_message>
	<reponse_incomplete_message><?php echo $item->getXML("reponse_incomplete_message")?></reponse_incomplete_message>
	<reponse_bonne_media><?php echo $item->getXML("reponse_bonne_media")?></reponse_bonne_media>
	<reponse_mauvaise_media><?php echo $item->getXML("reponse_mauvaise_media")?></reponse_mauvaise_media>
	<reponse_incomplete_media><?php echo $item->getXML("reponse_incomplete_media")?></reponse_incomplete_media>
	<remarque><?php echo $item->getXML("remarque")?></remarque>
	<date_modification><?php echo $item->getXML("date_modification")?></date_modification>
	<date_creation><?php echo $item->getXML("date_creation")?></date_creation>
	<solution><?php echo $item->getXML("solution_xml")?></solution>
	<type_champs><?php echo $item->getXML("type_champs")?></type_champs>
<?php
	$idx = 1;
	$listeLacunes = array_reverse($item->listeLacunes);
	foreach ($listeLacunes as $lacune) {
?>
	<lacune>
	
	<?php 
		for ($j = 1; $j < NB_MAX_CHOIX_REPONSES; $j++) {

			$cle = "lacune_" . $lacune->get("idx_lacune") . "_reponse_" . $j;

			$element = $item->getXML($cle . "_element");
			$retro = $item->getXML($cle . "_retroaction");
			$reponse = $item->getXML($cle . "_reponse");
			
			// Vérifier que la réponse est valide
			if ($element != "" || $retro != "") { ?>
		<reponse>
				<element><?php echo $element ?></element>
				<retroaction><?php echo $retro ?></retroaction>
				<bonne_reponse><?php echo $reponse ?></bonne_reponse>
		</reponse>
	<?php
			} 
		}
	?>
					
		<retroaction><?php echo $item->getXML("lacune_" . $lacune->get("idx_lacune") . "_retro") ?></retroaction>		
	</lacune>			
<?php 
	}
?>	

  </item>
	