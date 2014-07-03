  <questionnaire>
	<url_domaine><?php echo URL_DOMAINE ?></url_domaine>
	<id_questionnaire><?php echo $quest->getXML("id_questionnaire") ?></id_questionnaire>
	<id_projet><?php echo $quest->getXML("id_projet") ?></id_projet>
	<titre><?php echo$quest->getXML("titre") ?></titre>
	<titre_long><?php echo $quest->getXML("titre") ?></titre_long>
	<suivi><?php echo $quest->getXML("suivi") ?></suivi>
	<generation_question_type><?php echo $quest->getXML("generation_question_type") ?></generation_question_type>
	<generation_question_nb><?php echo $quest->getXML("generation_question_nb") ?></generation_question_nb>
	<temps_reponse_calculer><?php echo $quest->getXML("temps_reponse_calculer") ?></temps_reponse_calculer>
	<essais_repondre_type><?php echo $quest->getXML("essais_repondre_type") ?></essais_repondre_type>
	<essais_repondre_nb><?php echo $quest->getXML("essais_repondre_nb") ?></essais_repondre_nb>
	<affichage_resultats_type><?php echo $quest->getXML("affichage_resultats_type") ?></affichage_resultats_type>
	<demarrage_media_type><?php echo $quest->getXML("demarrage_media_type") ?></demarrage_media_type>
	<id_langue_questionnaire><?php echo $quest->getXML("id_langue_questionnaire") ?></id_langue_questionnaire>
	<id_collection><?php echo $quest->getXML("id_collection") ?></id_collection>
	<theme><?php echo $quest->getXML("theme") ?></theme>
	<mot_bienvenue><?php echo $quest->getXML("mot_bienvenue") ?></mot_bienvenue>
	<note><?php echo $quest->getXML("note") ?></note>
	<generique><?php echo $quest->getXML("generique") ?></generique>
	<media_titre><?php echo $quest->getXML("media_titre") ?></media_titre>
	<media_texte><?php echo $quest->getXML("media_texte") ?></media_texte>
	<media_image><?php echo $quest->getXML("media_image") ?></media_image>
	<media_son><?php echo $quest->getXML("media_son") ?></media_son>
	<media_video><?php echo $quest->getXML("media_video") ?></media_video>
	<texte_fin><?php echo $quest->getXML("texte_fin") ?></texte_fin>
	<nb_items><?php echo $quest->getXML("nb_items") ?></nb_items>
	<remarque><?php echo $quest->getXML("remarque") ?></remarque>
	<date_creation><?php echo $quest->getXML("date_creation") ?></date_creation>
	<date_modification><?php echo $quest->getXML("date_modification") ?></date_modification>
	