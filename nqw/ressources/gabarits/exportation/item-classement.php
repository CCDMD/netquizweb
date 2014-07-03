
  <item>
	<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
	<id_item><?php echo $item->getXML("id_item")?></id_item>
	<id_projet><?php echo $item->getXML("id_projet")?></id_projet>
	<titre><?php echo $item->getXML("titre")?></titre>
	<enonce><?php echo $item->getXML("enonce")?></enonce>
	<type_item>3</type_item>		
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
	<ordre_presentation><?php echo $item->getXML("ordre_presentation")?></ordre_presentation>
	<type_etiquettes><?php echo $item->getXML("type_etiquettes")?></type_etiquettes>
	<afficher_solution><?php echo $item->getXML("afficher_solution")?></afficher_solution>
	<orientation_elements><?php echo $item->getXML("orientation_elements")?></orientation_elements>
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
<?php for ( $i = 1; $i <= NB_MAX_CLASSEURS; $i++ ) {

	if ($item->get("classeur_" . $i . "_statut") == 1 ) {  ?>	
		<classeur>
			<titre><?php echo $item->getXML("classeur_" . $i . "_titre")?></titre>
			<retroaction><?php echo $item->getXML("classeur_" . $i . "_retroaction")?></retroaction>
			<retroaction_negative><?php echo $item->getXML("classeur_" . $i . "_retroaction_negative")?></retroaction_negative>
			<retroaction_incomplete><?php echo $item->getXML("classeur_" . $i . "_retroaction_incomplete")?></retroaction_incomplete>
<?php 		for ( $j = 1; $j <= NB_MAX_ELEMENTS_PAR_CLASSEURS; $j++ ) { 
				if ($item->get("classeur_" . $i . "_element_" . $j . "_statut") == 1 ) {  ?>
					<element>
						<texte><?php echo $item->getXML("classeur_" . $i . "_element_" . $j . "_texte") ?></texte>
<?php 				for ( $k = 1; $k <= NB_MAX_CLASSEURS; $k++ ) { ?>				
						<retroaction>
							<texte><?php echo $item->getXML("classeur_" . $i . "_element_" . $j . "_retro_" . $k) ?></texte>
						</retroaction>
<?php				} ?>
					</element>
<?php 		}
 		}  ?>

		</classeur>
<?php 
	} 
}
?>
  </item>
	