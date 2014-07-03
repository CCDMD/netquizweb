
  <item>
	<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
	<id_item><?php echo $item->getXML("id_item")?></id_item>
	<id_projet><?php echo $item->getXML("id_projet")?></id_projet>
	<titre><?php echo $item->getXML("titre")?></titre>
	<enonce><?php echo $item->getXML("enonce")?></enonce>
	<type_item>7</type_item>		
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
	<solution><?php echo $item->getXML("solution")?></solution>

<?php
	// Afficher la liste des couleurs
	for ($i = 1; $i <= NB_MAX_COULEURS; $i++) {

	if ($item->get("couleur_" . $i . "_statut") == "1") {

		// Afficher la liste des couleurs
		?>
		<couleur_marquage>
		 	<couleur><?php echo $item->getXML("couleur_" . $i . "_couleur") ?></couleur>
		 	<titre><?php echo $item->getXML("couleur_" . $i . "_titre") ?></titre>
		 	<retroaction><?php echo $item->getXML("couleur_" . $i . "_retroaction")?></retroaction>
		 	<retroaction_negative><?php echo $item->getXML("couleur_" . $i . "_retroaction_negative")?></retroaction_negative>
		 	<retroaction_incomplete><?php echo $item->getXML("couleur_" . $i . "_retroaction_incomplete")?></retroaction_incomplete>
		</couleur_marquage>
<?php 		} 	
		} 

	// Traiter chacune des marques et rétros
	$idx = 0;
	foreach ($item->listeMarques as $marque) {
		$idx++;
	
		// Afficher une marque 
?>
		<marque>
			<id_marque><?php echo $marque->getXML("id_marque")?></id_marque>
			<texte><?php echo $marque->getXML("texte")?></texte>
			<couleur><?php echo $marque->getXML("couleur")?></couleur>
			<position_debut><?php echo $marque->getXML("position_debut")?></position_debut>
			<position_fin><?php echo $marque->getXML("position_fin")?></position_fin>
<?php 
				// Liste des rétros pour cette marque
				foreach ($marque->listeRetros as $retro) {
?>
				<retroaction>
					<couleur><?php echo $retro->getXML("couleur") ?></couleur>
					<texte><?php echo $retro->getXML("retro") ?></texte>
				</retroaction>		
<?php 			} ?>
		</marque>
<?php } ?>

	<date_modification><?php echo $item->getXML("date_modification")?></date_modification>
	<date_creation><?php echo $item->getXML("date_creation")?></date_creation>

  </item>
	